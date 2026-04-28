<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ResearchApplications;
use App\Models\User;
use App\Models\ProtocolRoutingLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ResearchApplicationStatusController extends Controller
{
    // Handles status changes of a research application
    public function changeStatus(Request $request, $protocol_code)
    {
        // Fetch the application by protocol code
        $application = ResearchApplications::where('protocol_code', $protocol_code)->firstOrFail();

        // Validate incoming request data
        $request->validate([
            'status' => 'required|string',
            'comment' => 'nullable|string|max:255',
            'classification' => 'nullable|string',
            'reviewers' => 'nullable|array',
            'external_consultant_reason' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // Basic variables
            $currentStatus = $request->status;
            $hasConsultantRequest = !empty($request->external_consultant_reason);
            $currentUser = auth()->user();

            // --------------------------------------------------
            // 1. Update main application record
            // --------------------------------------------------
            $application->update([
                'status' => $currentStatus,
                'review_classification' => $request->classification,
                'external_consultant' => $request->external_consultant_reason,
            ]);

            // --------------------------------------------------
            // 2. Save status change to logs (history tracking)
            // --------------------------------------------------
            $application->logs()->create([
                'protocol_code' => $application->protocol_code,
                'user_id'       => $currentUser->id,
                'status'        => $currentStatus,
                'comment'       => $request->comment,
            ]);

            // --------------------------------------------------
            // 3. Auto-create exemption certificate if applicable
            // --------------------------------------------------
            if ($currentStatus === 'exempted_awaiting_chair_approval') {
                DB::table('exemption_certificates')->updateOrInsert(
                    ['protocol_code' => $application->protocol_code],
                    [
                        'date_issued'       => now()->toDateString(),
                        'investigator_name' => $request->principal_investigator,
                        'study_title'       => $application->research_title,
                        'berc_code'         => $application->protocol_code,
                        'chairperson_name'  => 'Pending Chair Approval',
                        'created_at'        => now(),
                        'updated_at'        => now()
                    ]
                );
            }

            // --------------------------------------------------
            // 4. ROUTING LOGIC (tracks workflow movement)
            // --------------------------------------------------

            // A. Close previous pending routing log
            $pendingLog = ProtocolRoutingLog::where('protocol_code', $protocol_code)
                ->whereNull('to_name') // still waiting for receiver
                ->orderBy('id', 'desc')
                ->first();

            if ($pendingLog) {
                $pendingLog->update([
                    'to_name'    => $currentUser->name,
                    'to_user_id' => $currentUser->id,
                ]);
            }

            // B. Map status to readable workflow label
            $natureMap = [
                'documents_checking' => 'Documents Checked',
                'incomplete_documents' => 'Incomplete Notice',
                'awaiting_reviewer_approval' => 'For Review (Protocol & Docs)',
                'exempted_awaiting_chair_approval' => 'Exempted - Forwarded to Chair',
                'drafting_decision' => 'Draft Decision Letter',
                'awaiting_approval' => 'For Chair Approval',
                'approved' => 'Approved Decision Letter',
                'resubmit' => 'Resubmit Decision Letter',
                'rejected' => 'Rejected Decision Letter',
            ];

            $documentNature = $natureMap[$currentStatus] ?? 'Status Updated: ' . $currentStatus;

            // C. Create new routing log (except when assigning reviewers)
            if (!($currentStatus === 'awaiting_reviewer_approval' && !empty($request->reviewers))) {
                ProtocolRoutingLog::create([
                    'protocol_code'   => $protocol_code,
                    'document_nature' => $documentNature,
                    'from_name'       => $currentUser->name,
                    'from_user_id'    => $currentUser->id,
                    'to_name'         => null, // next handler not yet assigned
                    'to_user_id'      => null,
                    'remarks'         => $request->comment,
                ]);
            }

            // --------------------------------------------------
            // 5. REVIEWER ASSIGNMENT HANDLING
            // --------------------------------------------------
            if ($request->has('reviewers')) {

                // Get existing reviewer records for this protocol
                $existingReviewers = DB::table('application_reviewer')
                    ->where('protocol_code', $protocol_code)
                    ->get()
                    ->keyBy('reviewer_id');

                DB::table('application_reviewer')
                    ->where('protocol_code', $protocol_code)
                    ->whereIn('status', ['Expired', 'Declined'])
                    ->update([
                        'status' => 'Rejected',
                        'updated_at' => now(),
                    ]);

                if (!empty($request->reviewers)) {
                    $reviewerData = [];
                    $acceptedCount = 0;
                    $totalAssigned = count($request->reviewers);

                    foreach ($request->reviewers as $rev) {
                        $status = $rev['status'] ?? 'Pending';
                        $reviewerId = $rev['reviewer_id'];
                        $existing = $existingReviewers->get($reviewerId);

                        if ($status === 'Accepted') {
                            $acceptedCount++;
                        }

                        DB::table('application_reviewer')->updateOrInsert(
                            [
                                'protocol_code' => $protocol_code,
                                'reviewer_id'   => $reviewerId,
                            ],
                            [
                                'status'        => $status,
                                'date_assigned' => $existing ? $existing->date_assigned : now(),
                                'date_accepted' => $existing
                                    ? $existing->date_accepted
                                    : ($status === 'Accepted' ? now() : null),
                                'created_at'    => $existing ? $existing->created_at : now(),
                                'updated_at'    => now(),
                            ]
                        );

                        if ($currentStatus === 'awaiting_reviewer_approval' && !$existing) {
                            ProtocolRoutingLog::create([
                                'protocol_code'   => $protocol_code,
                                'document_nature' => 'For Review (Protocol & Docs)',
                                'from_name'       => $currentUser->name,
                                'from_user_id'    => $currentUser->id,
                                'to_name'         => null,
                                'to_user_id'      => null,
                                'remarks'         => 'Reviewer Assignment Slot',
                            ]);
                        }
                    }

                    // --------------------------------------------------
                    // 6. AUTO TRANSITION TO "UNDER REVIEW"
                    // --------------------------------------------------
                    if ($totalAssigned > 0 && $acceptedCount === $totalAssigned && !$hasConsultantRequest) {

                        $currentStatus = 'under_review';

                        // Update application status
                        $application->update(['status' => $currentStatus]);

                        // Log automatic transition
                        $application->logs()->create([
                            'protocol_code' => $application->protocol_code,
                            'user_id'       => $currentUser->id,
                            'status'        => $currentStatus,
                            'comment'       => 'System: All reviewers accepted. Transitioned to under review.',
                        ]);

                        // Setup assessment form
                        $acceptedReviewerIds = DB::table('application_reviewer')
                            ->where('protocol_code', $protocol_code)
                            ->where('status', 'Accepted')
                            ->orderBy('id')
                            ->pluck('reviewer_id')
                            ->toArray();

                        $formExists = DB::table('assessment_forms')
                            ->where('protocol_code', $protocol_code)
                            ->exists();

                        $formPayload = [
                            'reviewer_id'   => $application->user_id,
                            'reviewer_1_id' => $acceptedReviewerIds[0] ?? null,
                            'reviewer_2_id' => $acceptedReviewerIds[1] ?? null,
                            'reviewer_3_id' => $acceptedReviewerIds[2] ?? null,
                            'status'        => 'evaluating',
                            'updated_at'    => now()
                        ];

                        if (!$formExists) {
                            $formPayload['created_at'] = now();
                        }

                        DB::table('assessment_forms')->updateOrInsert(
                            ['protocol_code' => $protocol_code],
                            $formPayload
                        );
                    }
                }
            }

            DB::commit();

            // --------------------------------------------------
            // 7. Return updated reviewer list
            // --------------------------------------------------
            $updatedReviewers = DB::table('application_reviewer')
                ->join('reviewers', 'application_reviewer.reviewer_id', '=', 'reviewers.id')
                ->where('protocol_code', $protocol_code)
                ->select(
                    'reviewers.name',
                    'application_reviewer.status',
                    'application_reviewer.date_assigned as dateAssigned'
                )
                ->get();

            return response()->json([
                'success' => true,
                'reviewers' => $updatedReviewers
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function expireAcceptedReviewerForReassignment(Request $request, $protocol_code)
    {
        $request->validate([
            'reviewer_id' => 'required|integer|exists:reviewers,id',
        ]);

        DB::beginTransaction();

        try {
            $application = ResearchApplications::where('protocol_code', $protocol_code)->firstOrFail();

            $daysAllowed = $application->review_classification === 'Expedited'
                ? 10
                : ($application->review_classification === 'Full Board' ? 20 : null);

            if (!$daysAllowed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Expedited and Full Board protocols use review deadlines.'
                ], 422);
            }

            $reviewerRow = DB::table('application_reviewer')
                ->where('protocol_code', $protocol_code)
                ->where('reviewer_id', $request->reviewer_id)
                ->where('status', 'Accepted')
                ->first();

            if (!$reviewerRow) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reviewer is not currently accepted for this protocol.'
                ], 404);
            }

            $acceptedAt = $reviewerRow->date_accepted ?? $reviewerRow->date_assigned;
            $deadline = \Carbon\Carbon::parse($acceptedAt)->addDays($daysAllowed);

            if (now()->lessThan($deadline)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reviewer deadline has not passed yet.'
                ], 422);
            }

            DB::table('application_reviewer')
                ->where('protocol_code', $protocol_code)
                ->where('reviewer_id', $request->reviewer_id)
                ->update([
                    'status' => 'Rejected',
                    'updated_at' => now(),
                ]);

            $this->removeReviewerFromAssessmentTables($protocol_code, $request->reviewer_id);

            $application->update([
                'status' => 'awaiting_reviewer_approval',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reviewer marked as rejected and removed from assessment slots.'
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function removeReviewerFromAssessmentTables(string $protocolCode, int $reviewerId): void
    {
        $assessment = DB::table('assessment_forms')
            ->where('protocol_code', $protocolCode)
            ->first();

        if ($assessment) {
            $updates = ['updated_at' => now()];

            foreach ([1, 2, 3] as $slot) {
                $reviewerCol = "reviewer_{$slot}_id";
                $doneCol = "reviewer_{$slot}_done";

                if ((int) $assessment->$reviewerCol === (int) $reviewerId) {
                    $updates[$reviewerCol] = null;
                    $updates[$doneCol] = null;
                }
            }

            DB::table('assessment_forms')
                ->where('protocol_code', $protocolCode)
                ->update($updates);
        }

        $icf = DB::table('icf_assessments')
            ->where('protocol_code', $protocolCode)
            ->first();

        if ($icf) {
            $updates = ['updated_at' => now()];

            foreach ([1, 2, 3] as $slot) {
                $reviewerCol = "reviewer_{$slot}_id";

                if ((int) $icf->$reviewerCol === (int) $reviewerId) {
                    $updates[$reviewerCol] = null;
                }
            }

            DB::table('icf_assessments')
                ->where('protocol_code', $protocolCode)
                ->update($updates);
        }
    }
}
