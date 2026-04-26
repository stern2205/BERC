<?php

namespace App\Http\Controllers;

use App\Models\ResearchApplications;
use App\Models\Reviewer;
use App\Models\ResearchApplicationRevision;
use App\Models\RevisionResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReviewerController extends Controller
{
    //this function shows the list of pending review invitations for the logged-in reviewer. It first retrieves the authenticated user and their corresponding reviewer record. Then, it performs a database query to join the research applications with the application_reviewer pivot table to fetch only those applications that are assigned to the reviewer and have a status of "Pending". The selected data includes all columns from the research applications table as well as specific columns from the pivot table related to the reviewer's assignment status and dates. Finally, it returns a view that displays these pending invitations to the reviewer.
    public function showInvitations(Request $request)
    {
        $user = auth()->user();
        $reviewer = Reviewer::where('user_id', $user->id)->first();

        if (!$reviewer) {
            $research_applications = collect();
        } else {
            // Join to get Pending invitations and their assignment logs
            $research_applications = ResearchApplications::query()
                ->join('application_reviewer', 'research_applications.protocol_code', '=', 'application_reviewer.protocol_code')
                ->where('application_reviewer.reviewer_id', $reviewer->id)
                ->where('application_reviewer.status', 'Pending')
                // Select all application columns, PLUS the specific log columns you need
                ->select(
                    'research_applications.*',
                    'application_reviewer.status as reviewer_action_status',
                    'application_reviewer.date_assigned',
                    'application_reviewer.date_expired'
                )
                ->get();
        }

        return view("reviewer.pipeline.invitation", compact("user", "research_applications"));
    }

    //this function handles the reviewer's response to an invitation to review a research application. It validates the incoming request to ensure that the action is either "accept" or "decline", and if declining, that a reason is provided. It then checks if the reviewer has a pending invitation for the specified protocol code. If accepting, it updates the pivot table to mark the assignment as accepted, claims the corresponding protocol routing log entry, and slots the reviewer into the existing assessment form and ICF assessment if they exist. It also checks if all assigned reviewers have accepted and if no external consultant is pending, in which case it transitions the application's status to "under_review". If declining, it updates the pivot table to mark the assignment as declined along with the reason. Finally, it returns a JSON response indicating the success of the operation.
    public function respondToInvitation(Request $request, $protocol_code)
    {
        $request->validate([
            'action' => 'required|in:accept,decline',
            'decline_reason' => 'nullable|required_if:action,decline|string|max:1500'
        ]);

        $user = auth()->user();

        // Find the reviewer record linked to the logged-in user
        $reviewer = Reviewer::where('user_id', $user->id)->firstOrFail();

        // Verify the assignment exists and is currently Pending
        $query = DB::table('application_reviewer')
            ->where('reviewer_id', $reviewer->id)
            ->where('protocol_code', $protocol_code);

        $currentAssignment = (clone $query)->first();

        if (!$currentAssignment || $currentAssignment->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Invitation not found or already responded to.'
            ], 400);
        }

        if ($request->action === 'accept') {
            DB::transaction(function () use ($reviewer, $protocol_code, $user) {

                // 1. Update the current reviewer's pivot record to 'Accepted'
                DB::table('application_reviewer')
                    ->where('reviewer_id', $reviewer->id)
                    ->where('protocol_code', $protocol_code)
                    ->update([
                        'status' => 'Accepted',
                        'date_accepted' => now(),
                        'updated_at' => now()
                    ]);

                // --- NEW: UPDATE PROTOCOL ROUTING LOG ---
                // Find the first blank slot created by the Secretariat and "claim" it
                DB::table('protocol_routing_logs')
                    ->where('protocol_code', $protocol_code)
                    ->where('document_nature', 'For Review (Protocol & Docs)')
                    ->whereNull('to_name')
                    ->orderBy('id', 'asc')
                    ->limit(1)
                    ->update([
                        'to_name'    => $user->name,
                        'to_user_id' => $user->id,
                        'updated_at' => now()
                    ]);
                // ----------------------------------------

                // 2. SLOT THE REVIEWER INTO THE EXISTING FORM
                $form = DB::table('assessment_forms')->where('protocol_code', $protocol_code)->first();

                if ($form) {
                    $updateData = ['updated_at' => now()];

                    $alreadySlotted = in_array($reviewer->id, [
                        $form->reviewer_1_id,
                        $form->reviewer_2_id,
                        $form->reviewer_3_id
                    ]);

                    if (!$alreadySlotted) {
                        if (is_null($form->reviewer_1_id)) {
                            $updateData['reviewer_1_id'] = $reviewer->id;
                        } elseif (is_null($form->reviewer_2_id)) {
                            $updateData['reviewer_2_id'] = $reviewer->id;
                        } elseif (is_null($form->reviewer_3_id)) {
                            $updateData['reviewer_3_id'] = $reviewer->id;
                        }

                        if (count($updateData) > 1) {
                            DB::table('assessment_forms')
                                ->where('id', $form->id)
                                ->update($updateData);
                        }
                    }
                }

                $icfForm = DB::table('icf_assessments')->where('protocol_code', $protocol_code)->first();

                if ($icfForm) {
                    $icfUpdateData = ['updated_at' => now()];

                    $alreadySlottedIcf = in_array($reviewer->id, [
                        $icfForm->reviewer_1_id,
                        $icfForm->reviewer_2_id,
                        $icfForm->reviewer_3_id
                    ]);

                    if (!$alreadySlottedIcf) {
                        if (is_null($icfForm->reviewer_1_id)) {
                            $icfUpdateData['reviewer_1_id'] = $reviewer->id;
                        } elseif (is_null($icfForm->reviewer_2_id)) {
                            $icfUpdateData['reviewer_2_id'] = $reviewer->id;
                        } elseif (is_null($icfForm->reviewer_3_id)) {
                            $icfUpdateData['reviewer_3_id'] = $reviewer->id;
                        }

                        if (count($icfUpdateData) > 1) {
                            DB::table('icf_assessments')
                                ->where('id', $icfForm->id)
                                ->update($icfUpdateData);
                        }
                    }
                }

                // 3. CHECK: Are all assigned reviewers now "Accepted"?
                $totalAssigned = DB::table('application_reviewer')
                    ->where('protocol_code', $protocol_code)
                    ->count();

                $totalAccepted = DB::table('application_reviewer')
                    ->where('protocol_code', $protocol_code)
                    ->where('status', 'Accepted')
                    ->count();

                // --- NEW: CHECK EXTERNAL CONSULTANT STATUS ---
                $application = DB::table('research_applications')
                    ->where('protocol_code', $protocol_code)
                    ->select('external_consultant')
                    ->first();

                // If it's not null and not an empty string, an external consultant is pending
                $isExternalConsultantPending = $application && !empty($application->external_consultant);

                // 4. If everyone has accepted AND no external consultant is pending, move status to 'under_review'
                if ($totalAssigned > 0 && $totalAssigned === $totalAccepted && !$isExternalConsultantPending) {
                    $newStatus = 'under_review';

                    DB::table('research_applications')
                        ->where('protocol_code', $protocol_code)
                        ->update([
                            'status' => $newStatus,
                            'updated_at' => now()
                        ]);

                    DB::table('research_application_logs')->insert([
                        'protocol_code' => $protocol_code,
                        'user_id'       => $user->id,
                        'status'        => $newStatus,
                        'comment'       => 'System: All assigned reviewers have accepted. Transitioned to under review.',
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'You have successfully accepted the review assignment.'
            ]);
        }

        if ($request->action === 'decline') {
            $query->update([
                'status' => 'Declined',
                'date_declined' => Carbon::now(),
                'declined_reason' => $request->decline_reason,
                'updated_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'You have declined the review assignment.'
            ]);
        }
    }

    public function showAssessment(Request $request)
    {
        $user = auth()->user();
        $reviewer = Reviewer::where('user_id', $user->id)->first();

        if (!$reviewer) {
            $research_applications = collect();
        } else {
            $research_applications = ResearchApplications::query()
                ->join('application_reviewer', 'research_applications.protocol_code', '=', 'application_reviewer.protocol_code')
                ->join('assessment_forms', 'research_applications.protocol_code', '=', 'assessment_forms.protocol_code')
                ->where('application_reviewer.reviewer_id', $reviewer->id)
                ->where('application_reviewer.status', 'Accepted')
                ->where(function($query) use ($reviewer) {
                    $query->where(function($q) use ($reviewer) {
                        $q->where('assessment_forms.reviewer_1_id', $reviewer->id)
                        ->where(function($inner) {
                            $inner->whereNull('assessment_forms.reviewer_1_done')
                                    ->orWhere('assessment_forms.reviewer_1_done', '!=', 'Done');
                        });
                    })
                    ->orWhere(function($q) use ($reviewer) {
                        $q->where('assessment_forms.reviewer_2_id', $reviewer->id)
                        ->where(function($inner) {
                            $inner->whereNull('assessment_forms.reviewer_2_done')
                                    ->orWhere('assessment_forms.reviewer_2_done', '!=', 'Done');
                        });
                    })
                    ->orWhere(function($q) use ($reviewer) {
                        $q->where('assessment_forms.reviewer_3_id', $reviewer->id)
                        ->where(function($inner) {
                            $inner->whereNull('assessment_forms.reviewer_3_done')
                                    ->orWhere('assessment_forms.reviewer_3_done', '!=', 'Done');
                        });
                    });
                })
                ->select(
                    'research_applications.protocol_code',
                    'research_applications.research_title',
                    'research_applications.name_of_researcher as primary_researcher',
                    'research_applications.review_classification as classification',
                    'application_reviewer.status as reviewer_action_status',
                    'application_reviewer.date_assigned',
                    'application_reviewer.date_accepted'
                )
                ->get()
                ->map(function ($app) use ($reviewer) {
                    // 1. Handle General Assessment Form
                    $form = DB::table('assessment_forms')->where('protocol_code', $app->protocol_code)->first();

                    $app->assessmentRows = $form
                        ? DB::table('assessment_form_items')->where('assessment_form_id', $form->id)->get()
                        : [];

                    $app->reviewer_slot = null;
                    if ($form) {
                        if ($form->reviewer_1_id == $reviewer->id) $app->reviewer_slot = 'reviewer_1_comments';
                        elseif ($form->reviewer_2_id == $reviewer->id) $app->reviewer_slot = 'reviewer_2_comments';
                        elseif ($form->reviewer_3_id == $reviewer->id) $app->reviewer_slot = 'reviewer_3_comments';
                    }

                    // 2. Handle Informed Consent (ICF) Assessment
                    $icfForm = DB::table('icf_assessments')->where('protocol_code', $app->protocol_code)->first();

                    // Check if this specific reviewer is assigned to the ICF for this protocol
                    $app->has_icf_assignment = false;
                    $app->icf_reviewer_slot = null;

                    if ($icfForm) {
                        // Check slots 1, 2, and 3 in the icf_assessments table
                        if ($icfForm->reviewer_1_id == $reviewer->id) {
                            $app->has_icf_assignment = true;
                            $app->icf_reviewer_slot = 'reviewer_1_comments';
                        } elseif ($icfForm->reviewer_2_id == $reviewer->id) {
                            $app->has_icf_assignment = true;
                            $app->icf_reviewer_slot = 'reviewer_2_comments';
                        } elseif ($icfForm->reviewer_3_id == $reviewer->id) {
                            $app->has_icf_assignment = true;
                            $app->icf_reviewer_slot = 'reviewer_3_comments';
                        }
                    }

                    // Load rows for ICF if form exists
                    $app->consentRows = $icfForm
                        ? DB::table('icf_assessment_items')->where('icf_assessment_id', $icfForm->id)->get()
                        : [];

                    return $app;
                });
        }

        return view("reviewer.pipeline.assessment", compact("user", "research_applications"));
    }

    //this function is a subfunction that handles the assessment form and submits it to the database
    //it validates the incoming request to ensure that the assessment rows are provided and that if consent rows are included, they are in the correct format. It then identifies the reviewer and updates the corresponding assessment form entries based on which reviewer slot they occupy. The function also logs the submission of the review assessment in the protocol routing logs and calculates the average review time for the reviewer by matching their assignment times with their completion times. Finally, it returns a JSON response indicating the success of the operation.
    public function submitValidation(Request $request, $protocol_code)
    {
        $request->validate([
            'assessment_rows' => 'required|array',
            'consent_rows' => 'nullable|array',
        ]);

        $user = auth()->user();
        $reviewer = Reviewer::where('user_id', $user->id)->firstOrFail();
        $reviewerId = $reviewer->id;

        DB::beginTransaction();

        try {
            // 1. Handle General Protocol Assessment Form
            $form = DB::table('assessment_forms')->where('protocol_code', $protocol_code)->first();
            if (!$form) throw new \Exception("Assessment form record not found.");

            $commentColumn = null;
            $actionColumn = null;
            $doneColumn = null;

            if ($form->reviewer_1_id == $reviewerId) {
                $commentColumn = 'reviewer_1_comments';
                $actionColumn = 'reviewer_1_action_required';
                $doneColumn = 'reviewer_1_done';
            } elseif ($form->reviewer_2_id == $reviewerId) {
                $commentColumn = 'reviewer_2_comments';
                $actionColumn = 'reviewer_2_action_required';
                $doneColumn = 'reviewer_2_done';
            } elseif ($form->reviewer_3_id == $reviewerId) {
                $commentColumn = 'reviewer_3_comments';
                $actionColumn = 'reviewer_3_action_required';
                $doneColumn = 'reviewer_3_done';
            }

            if (!$commentColumn) throw new \Exception("You are not assigned to a protocol assessment slot.");

            // Mark Protocol Form as Done
            DB::table('assessment_forms')->where('id', $form->id)->update([
                $doneColumn => 'Done',
                'updated_at' => now()
            ]);

            // Save General Assessment Items
            foreach ($request->assessment_rows as $row) {
                DB::table('assessment_form_items')->updateOrInsert(
                    ['assessment_form_id' => $form->id, 'question_number' => $row['question_number']],
                    [
                        'remark' => $row['remark'],
                        'line_page' => $row['line_page'],
                        $commentColumn => $row['reviewer_comments'],
                        $actionColumn => $row['action_required'] ?? false,
                        'updated_at' => now(),
                    ]
                );
            }

            // 2. Handle Informed Consent (ICF) Assessment (If applicable)
            if ($request->has('consent_rows') && !empty($request->consent_rows)) {
                $icfForm = DB::table('icf_assessments')->where('protocol_code', $protocol_code)->first();

                if ($icfForm) {
                    $icfCommentCol = null;
                    $icfActionCol = null;
                    $icfDoneCol = null;

                    // Find the reviewer's specific slot in the ICF table
                    if ($icfForm->reviewer_1_id == $reviewerId) {
                        $icfCommentCol = 'reviewer_1_comments';
                        $icfActionCol = 'reviewer_1_action_required';
                        $icfDoneCol = 'reviwer_1_done'; // Note: matches your SQL spelling 'reviwer'
                    } elseif ($icfForm->reviewer_2_id == $reviewerId) {
                        $icfCommentCol = 'reviewer_2_comments';
                        $icfActionCol = 'reviewer_2_action_required';
                        $icfDoneCol = 'reviwer_2_done';
                    } elseif ($icfForm->reviewer_3_id == $reviewerId) {
                        $icfCommentCol = 'reviewer_3_comments';
                        $icfActionCol = 'reviewer_3_action_required';
                        $icfDoneCol = 'reviwer_3_done';
                    }

                    if ($icfDoneCol) {
                        // Mark ICF Form as Done
                        DB::table('icf_assessments')->where('id', $icfForm->id)->update([
                            $icfDoneCol => 'Done',
                            'updated_at' => now()
                        ]);

                        // Save ICF Assessment Items
                        foreach ($request->consent_rows as $row) {
                            DB::table('icf_assessment_items')->updateOrInsert(
                                ['icf_assessment_id' => $icfForm->id, 'question_number' => $row['question_number']],
                                [
                                    'remark' => $row['remark'],
                                    'line_page' => $row['line_page'],
                                    $icfCommentCol => $row['reviewer_comments'],
                                    $icfActionCol => $row['action_required'] ?? false,
                                    'updated_at' => now(),
                                ]
                            );
                        }
                    }
                }
            }

            // 3. Log Routing
            DB::table('protocol_routing_logs')->insert([
                'protocol_code' => $protocol_code,
                'document_nature' => 'Completed Review Assessment',
                'from_name' => $user->name,
                'from_user_id' => $user->id,
                'to_name' => null,
                'remarks' => 'Reviewer assessment submitted.',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // --- 3.5 Calculate and Update Average Review Time ---
            // Fetch all assignments for this specific user
            $assignments = DB::table('protocol_routing_logs')
                ->where('to_user_id', $user->id)
                ->where('document_nature', 'For Review (Protocol & Docs)')
                ->select('protocol_code', 'updated_at')
                ->get()
                ->keyBy('protocol_code');

            // Fetch all completed review submissions for this specific user
            $completions = DB::table('protocol_routing_logs')
                ->where('from_user_id', $user->id)
                ->where('document_nature', 'Completed Review Assessment')
                ->select('protocol_code', 'created_at')
                ->get();

            $totalDays = 0;
            $validReviewCount = 0;

            // Loop through their completions and match them to their assignment times
            foreach ($completions as $completion) {
                if ($assignments->has($completion->protocol_code)) {
                    // updated_at of the assignment log is when they accepted it
                    $assignedTime = Carbon::parse($assignments->get($completion->protocol_code)->updated_at);

                    // created_at of the completion log is when they submitted it
                    $completedTime = Carbon::parse($completion->created_at);

                    // Calculate precise days difference and round to nearest whole number
                    $days = (int) ceil($assignedTime->floatDiffInDays($completedTime));

                    $totalDays += $days;
                    $validReviewCount++;
                }
            }

            // If they have valid reviews, calculate the average and update the reviewers table
            if ($validReviewCount > 0) {
                $averageDays = (int) round($totalDays / $validReviewCount);
                DB::table('reviewers')
                    ->where('id', $reviewerId)
                    ->update([
                        'avg_review_time_days' => $averageDays,
                        'updated_at' => now()
                    ]);
            }

            // 4. Check Global Completion
            $finalForm = DB::table('assessment_forms')->where('id', $form->id)->first();
            $isProtocolDone = true;
            if ($finalForm->reviewer_1_id && $finalForm->reviewer_1_done !== 'Done') $isProtocolDone = false;
            if ($finalForm->reviewer_2_id && $finalForm->reviewer_2_done !== 'Done') $isProtocolDone = false;
            if ($finalForm->reviewer_3_id && $finalForm->reviewer_3_done !== 'Done') $isProtocolDone = false;

            // Also check ICF completion if an ICF form exists
            $finalIcf = DB::table('icf_assessments')->where('protocol_code', $protocol_code)->first();
            $isIcfDone = true;
            if ($finalIcf) {
                if ($finalIcf->reviewer_1_id && $finalIcf->reviwer_1_done !== 'Done') $isIcfDone = false;
                if ($finalIcf->reviewer_2_id && $finalIcf->reviwer_2_done !== 'Done') $isIcfDone = false;
                if ($finalIcf->reviewer_3_id && $finalIcf->reviwer_3_done !== 'Done') $isIcfDone = false;
            }

            if ($isProtocolDone && $isIcfDone) {
                DB::table('research_applications')
                    ->where('protocol_code', $protocol_code)
                    ->update(['status' => 'review_finished', 'updated_at' => now()]);

                DB::table('research_application_logs')->insert([
                    'protocol_code' => $protocol_code,
                    'user_id' => $user->id,
                    'status' => 'review_finished',
                    'comment' => 'System: All assigned reviewers and ICF evaluators have submitted their assessments.',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Clean up the private temp draft file for this specific user
            $draftFilePath = storage_path("app/temp/draft_" . auth()->id() . "_{$protocol_code}.json");
            if (File::exists($draftFilePath)) {
                File::delete($draftFilePath);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Assessment saved. ' . (($isProtocolDone && $isIcfDone) ? 'All reviews finished.' : 'Waiting for others.')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    //this function shows the revision page of the reviewer and fetches all the protocols under their review
    public function showRevisionPage(Request $request)
    {
        $user = auth()->user();

        // 1. Get the Reviewer profile for the logged-in user
        $reviewerProfile = DB::table('reviewers')
            ->where('user_id', $user->id)
            ->first();

        if (!$reviewerProfile) {
            return back()->with('error', 'You do not have a reviewer profile assigned.');
        }

        $reviewerId = $reviewerProfile->id;

        // 2. Fetch unique protocol tasks where this reviewerId is assigned AND status is 'under_review'
        $revisionTasks = RevisionResponse::select(
                'revision_responses.protocol_code',
                'revision_responses.revision_number',
                DB::raw('MAX(revision_responses.created_at) as date_submitted')
            )
            ->join('research_application_revisions', function ($join) {
                $join->on('revision_responses.protocol_code', '=', 'research_application_revisions.protocol_code')
                     ->on('revision_responses.revision_number', '=', 'research_application_revisions.revision_number');
            })
            ->where('research_application_revisions.status', 'under_review')
            ->where(function($query) use ($reviewerId) {
                $query->where('revision_responses.reviewer1_id', $reviewerId)
                      ->orWhere('revision_responses.reviewer2_id', $reviewerId)
                      ->orWhere('revision_responses.reviewer3_id', $reviewerId);
            })
            ->groupBy('revision_responses.protocol_code', 'revision_responses.revision_number')
            ->get();

        // 3. Map and Format data
        $formattedRevisions = $revisionTasks->map(function($task) use ($reviewerId) {

            $app = ResearchApplications::where('protocol_code', $task->protocol_code)->first();

            // 4. Fetch the actual line-items (Revision Responses) for this protocol
            $responseRows = RevisionResponse::where('protocol_code', $task->protocol_code)
                ->where('revision_number', $task->revision_number)
                ->orderByRaw("
                    CAST(split_part(item, '.', 1) AS INTEGER),
                    CAST(split_part(item, '.', 2) AS INTEGER)
                ")
                ->get();

            $isDone = true; // Assume done until we find an unresolved row

            // Map the rows to extract the specific text and check if THIS reviewer resolved it
            $mappedRows = $responseRows->map(function($row) use ($reviewerId, &$isDone) {
                $resolved = false;

                // Check which slot this reviewer occupies, and read that specific boolean
                if ($row->reviewer1_id == $reviewerId) {
                    $resolved = $row->reviewer1_done;
                } elseif ($row->reviewer2_id == $reviewerId) {
                    $resolved = $row->reviewer2_done;
                } elseif ($row->reviewer3_id == $reviewerId) {
                    $resolved = $row->reviewer3_done;
                }

                // If even one row is not resolved, the whole protocol is not "Done"
                if (!$resolved) {
                    $isDone = false;
                }

                return [
                    'id'                  => $row->id,
                    'section_and_page'    => $row->section_and_page,
                    'item'                => $row->item,
                    'berc_recommendation' => $row->berc_recommendation,
                    'researcher_response' => $row->researcher_response,
                    'resolved'            => (bool)$resolved
                ];
            });

            // Edge case: If there are no rows, it shouldn't be marked as "done"
            if ($responseRows->isEmpty()) {
                $isDone = false;
            }

            return [
                'id'            => $task->protocol_code,
                'version'       => 'V' . $task->revision_number,
                'title'         => $app->research_title ?? 'Unknown Title',
                'proponent'     => $app->name_of_researcher ?? 'Unknown Proponent',
                'revisionType'  => $app->review_classification ?? 'Minor',
                'dateSubmitted' => Carbon::parse($task->date_submitted)->format('Y-m-d'),
                'dateValidated' => $isDone ? now()->format('Y-m-d') : null,
                'chairStatus'   => 'Pending',
                'is_done'       => $isDone,
                'rows'          => $mappedRows // Inject the response rows directly into the array!
            ];
        });

        // 5. Split for the two-tab UI
        $revisions = $formattedRevisions->where('is_done', false)->values();
        $validatedRevisions = $formattedRevisions->where('is_done', true)->values();

        return view('reviewer.pipeline.revision', compact('revisions', 'validatedRevisions'));
    }

    //this function handles the submission of the reviewer's validation of the revisions. It validates the incoming request to ensure that the protocol code, revision number, and rows of data are provided in the correct format. It then identifies the reviewer and updates only the specific slots in the revision responses that belong to this reviewer. After updating the responses, it checks if all reviewers have completed their validations for this revision. If all reviewers are done, it updates the parent revision tracker status to "review_finished". The function also logs the submission of the reviewer's comments in the protocol routing logs and cleans up any temporary draft files. Finally, it returns a JSON response indicating the success or failure of the operation.
    public function validateRevisions(Request $request)
    {
        // 1. Validate the incoming payload
        $request->validate([
            'protocol_code'   => 'required|string',
            'revision_number' => 'required|integer',
            'rows'            => 'required|array',
            'rows.*.id'       => 'required|integer|exists:revision_responses,id',
            'rows.*.action'   => 'nullable|string|in:resolved,action_required',
            'rows.*.remarks'  => 'nullable|string',
        ]);

        $user = auth()->user();

        // 2. Identify the Reviewer
        $reviewerProfile = DB::table('reviewers')->where('user_id', $user->id)->first();

        if (!$reviewerProfile) {
            return response()->json(['message' => 'Reviewer profile not found.'], 403);
        }

        $reviewerId = $reviewerProfile->id;

        DB::beginTransaction();

        try {
            // 3. Loop through each row submitted from the frontend
            foreach ($request->rows as $rowData) {
                $response = RevisionResponse::find($rowData['id']);

                if (!$response) continue;

                // 4. Update ONLY the slot belonging to this specific reviewer
                if ($response->reviewer1_id == $reviewerId) {
                    $response->reviewer1_action = $rowData['action'];
                    $response->reviewer1_remarks = $rowData['remarks'];
                    $response->reviewer1_done = !empty($rowData['action']);
                }
                elseif ($response->reviewer2_id == $reviewerId) {
                    $response->reviewer2_action = $rowData['action'];
                    $response->reviewer2_remarks = $rowData['remarks'];
                    $response->reviewer2_done = !empty($rowData['action']);
                }
                elseif ($response->reviewer3_id == $reviewerId) {
                    $response->reviewer3_action = $rowData['action'];
                    $response->reviewer3_remarks = $rowData['remarks'];
                    $response->reviewer3_done = !empty($rowData['action']);
                }

                $response->save();
            }

            // 5. CHECK IF ALL REVIEWERS ARE DONE FOR THIS REVISION
            $allRowsForThisRevision = RevisionResponse::where('protocol_code', $request->protocol_code)
                ->where('revision_number', $request->revision_number)
                ->get();

            $allReviewersFinished = true;

            foreach ($allRowsForThisRevision as $row) {
                // For each row, check if an assigned reviewer exists AND if they are NOT done
                if (($row->reviewer1_id && !$row->reviewer1_done) ||
                    ($row->reviewer2_id && !$row->reviewer2_done) ||
                    ($row->reviewer3_id && !$row->reviewer3_done)) {

                    // If even one assigned reviewer on one row isn't done, the whole revision isn't finished
                    $allReviewersFinished = false;
                    break; // Stop checking, we already know it's not finished
                }
            }

            // 6. UPDATE PARENT REVISION TRACKER IF EVERYONE IS DONE
            if ($allReviewersFinished) {
                ResearchApplicationRevision::where('protocol_code', $request->protocol_code)
                    ->where('revision_number', $request->revision_number)
                    ->update(['status' => 'review_finished']);
            }

            // 7. ─── LOG TO PROTOCOL ROUTING LOGS ───
            DB::table('protocol_routing_logs')->insert([
                'protocol_code'   => $request->protocol_code,
                'document_nature' => 'Reviewer Comments (Version ' . $request->revision_number . ')',
                'from_name'       => $user->name,
                'from_user_id'    => $user->id,
                'to_name'         => null, // Blank for now
                'to_user_id'      => null, // Blank for now
                'remarks'         => 'Reviewer submitted comments for the revised protocol.',
                'created_at'      => now(),
                'updated_at'      => now()
            ]);

            // Clean up the private temp draft file
            $draftFilePath = storage_path("app/temp/reviewer_resub_draft_" . auth()->id() . "_{$request->protocol_code}_v{$request->revision_number}.json");
            if (File::exists($draftFilePath)) {
                File::delete($draftFilePath);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Validation saved successfully.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error saving validation: ' . $e->getMessage()
            ], 500);
        }
    }

    //this function shows the calendar view for the reviewer
    public function showReviewerCalendar(Request $request){
        $user = auth()->user();
        return view("reviewer.calendar",compact("user"));
    }

    //this function saves the reviewer's draft of their assessment form to a temporary JSON file in the storage/app/temp directory. The filename is unique to the reviewer and protocol code, ensuring that each reviewer has their own private draft. When the reviewer returns to the assessment page, the getDraft function can be called to retrieve and populate the form with their saved data. This allows reviewers to save their progress without submitting incomplete assessments to the database.
    public function saveDraft(Request $request, $protocol_code)
    {
        $userId = auth()->id();
        $tempDir = storage_path('app/temp');

        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $filePath = $tempDir . "/draft_{$userId}_{$protocol_code}.json";

        // Save the JSON payload to the temp folder
        File::put($filePath, json_encode($request->all()));

        return response()->json(['success' => true]);
    }

    //this function retrieves the saved draft for the reviewer and protocol code if it exists. It checks the storage/app/temp directory for a JSON file that matches the unique filename pattern for the reviewer and protocol. If the file exists, it reads the content and returns it as a JSON response to populate the assessment form. If the file does not exist, it returns a 404 response, indicating that there is no saved draft available.
    public function getDraft($protocol_code)
    {
        $userId = auth()->id();
        $filePath = storage_path("app/temp/draft_{$userId}_{$protocol_code}.json");

        if (File::exists($filePath)) {
            $content = File::get($filePath);
            return response()->json(json_decode($content, true));
        }

        return response()->json(null, 404);
    }

    // Similar to the general draft functions, but specifically for saving drafts of the reviewer's revision validation comments. This allows reviewers to save their progress on validating revisions without submitting incomplete comments to the database. The filename includes the revision number to allow for multiple drafts across different revisions of the same protocol.
    public function saveResubmissionDraft(Request $request, $protocol_code, $revision_number)
    {
        $userId = auth()->id();
        $tempDir = storage_path('app/temp');

        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        // Distinct filename for reviewer resubmission drafts using the version number
        $filePath = $tempDir . "/reviewer_resub_draft_{$userId}_{$protocol_code}_v{$revision_number}.json";

        File::put($filePath, json_encode($request->all()));

        return response()->json(['success' => true]);
    }

    // This function retrieves the saved draft for the reviewer's revision validation comments based on the protocol code and revision number. It checks for a JSON file in the storage/app/temp directory that matches the unique filename pattern for the reviewer, protocol, and revision version. If the file exists, it reads the content and returns it as a JSON response to populate the revision validation form. If the file does not exist, it returns a 404 response, indicating that there is no saved draft available for that specific revision.
    public function getResubmissionDraft($protocol_code, $revision_number)
    {
        $userId = auth()->id();
        $filePath = storage_path("app/temp/reviewer_resub_draft_{$userId}_{$protocol_code}_v{$revision_number}.json");

        if (File::exists($filePath)) {
            $content = File::get($filePath);
            return response()->json(json_decode($content, true));
        }

        return response()->json(null, 404);
    }
}
