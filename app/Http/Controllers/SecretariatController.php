<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ResearchApplications;
use App\Models\ResearchApplicationLog;
use App\Models\ResearchApplicationRevision;
use App\Models\RevisionResponse;
use App\Models\AssessmentFormItem;
use App\Models\InformedConsentItem;
use App\Models\Reviewer;
use App\Models\DecisionLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SecretariatController extends Controller
{
    //this shows the review classification page of the secretariat and fetches all the protocols that are currently in the documents checking, awaiting reviewer approval, exempted awaiting chair approval, or under review status. It also calculates the current review cycle based on the second Wednesday of the month and fetches the reviewers along with their current workload of assigned reviews. Finally, it passes all this data to the view for rendering.
    //this function has a submodule in ResearchApplicationStatusController that handles the classification of the protocols and updates their status accordingly based on the secretariat's input.
    public function showProtocolEvaluation(Request $request)
    {
        $user = auth()->user();
        $protocol_code = $request->query('protocol_code');

        // 1. Fetch Proposals
        $proposals = ResearchApplications::with(['logs.user', 'assignedReviewers'])
            ->whereIn('status', [
                'documents_checking',
                'awaiting_reviewer_approval',
                'exempted_awaiting_chair_approval',
                'under_review'
            ])
            ->get()
            ->map(function($p) {
                $logEntry = $p->logs->where('status', 'documents_checking')->first();

                return [
                    'id' => $p->protocol_code,
                    'title' => $p->research_title,
                    'proponent' => $p->name_of_researcher,
                    'date' => $p->created_at->toIso8601String(),
                    'classifiedDate' => $p->updated_at->toIso8601String(),
                    'receiver' => $logEntry && $logEntry->user ? $logEntry->user->name : 'Unassigned',
                    'status' => $p->status,
                    'classification' => $p->review_classification,

                    'assignedReviewers' => $p->assignedReviewers->map(function($r) {
                        $assignedDate = Carbon::parse($r->pivot->date_assigned);
                        $expiredDate = $r->pivot->date_expired
                            ? Carbon::parse($r->pivot->date_expired)->toIso8601String()
                            : $assignedDate->copy()->addHours(24)->toIso8601String();

                        return [
                            'id' => $r->id,
                            'name' => $r->name,
                            'status' => $r->pivot->status ?? 'Pending',
                            'dateAssigned' => $assignedDate->toIso8601String(),
                            'dateExpired' => $expiredDate,
                            'dateAccepted' => $r->pivot->date_accepted ? Carbon::parse($r->pivot->date_accepted)->toIso8601String() : null,
                            'dateDeclined' => $r->pivot->date_declined ? Carbon::parse($r->pivot->date_declined)->toIso8601String() : null,
                            'declinedReason' => $r->pivot->declined_reason ?? null,
                        ];
                    })->toArray()
                ];
            });

        // 2. --- CALCULATE REVIEW CYCLE ---
        $now = Carbon::now();
        $secondWedThisMonth = Carbon::parse('second wednesday of ' . $now->format('F Y'))->startOfDay();

        if ($now->lessThan($secondWedThisMonth)) {
            $cycleStart = Carbon::parse('second wednesday of ' . $now->copy()->subMonth()->format('F Y'))->startOfDay();
        } else {
            $cycleStart = $secondWedThisMonth;
        }

        // 3. --- FETCH ASSIGNMENTS (ROLLOVER UNFINISHED + CURRENT CYCLE FINISHED) ---
        $cycleAssignments = DB::table('application_reviewer')
            ->join('research_applications', 'application_reviewer.protocol_code', '=', 'research_applications.protocol_code')
            ->select('application_reviewer.reviewer_id', 'application_reviewer.protocol_code', 'research_applications.research_title')
            ->where(function ($query) use ($cycleStart) {

                // Rule 1: ALWAYS count active/unfinished reviews, regardless of what month they were assigned
                $query->whereIn('application_reviewer.status', ['Pending', 'Accepted'])

                      // Rule 2: Count ANY review assigned during the current cycle,
                      // meaning "review_finished" protocols will still consume their quota for this month.
                      ->orWhere(function ($q) use ($cycleStart) {
                          $q->where('application_reviewer.date_assigned', '>=', $cycleStart)
                            // But do NOT count them if the reviewer declined or expired.
                            ->whereNotIn('application_reviewer.status', ['Declined', 'Expired']);
                      });
            })
            ->get()
            ->groupBy('reviewer_id');

        // 4. Fetch Reviewers and append their current cycle load
        $reviewers = Reviewer::where('type', '!=', 'External Consultant')
            ->where('is_active', true)
            ->get()
            ->map(function($rev) use ($cycleAssignments) {
                // Map the results into an array of objects containing id and title
                $evaluations = $cycleAssignments->has($rev->id)
                    ? $cycleAssignments->get($rev->id)->map(function($assignment) {
                        return [
                            'protocol_code' => $assignment->protocol_code,
                            'title' => $assignment->research_title
                        ];
                    })->toArray()
                    : [];

                return array_merge($rev->toArray(), [
                    'evaluations' => $evaluations
                ]);
            });

        $consultants = Reviewer::where('type', 'External Consultant')
            ->where('is_active', true)
            ->get()
            ->map(function($rev) use ($cycleAssignments) {
                $evaluations = $cycleAssignments->has($rev->id)
                    ? $cycleAssignments->get($rev->id)->map(function($assignment) {
                        return [
                            'protocol_code' => $assignment->protocol_code,
                            'title' => $assignment->research_title
                        ];
                    })->toArray()
                    : [];

                return array_merge($rev->toArray(), [
                    'evaluations' => $evaluations
                ]);
            });

        // 5. Pass everything to the view
        return view('secretariat.pipeline.evaluation', compact(
            'protocol_code',
            'user',
            'proposals',
            'reviewers',
            'consultants'
        ));
    }

    //this function shows the assessment form evaluation page of the secretariat
    //this fetches all the protocols that are currently under review or have finished their review but not yet classified. It also fetches the associated assessment form items, informed consent items, and assigned reviewers for each protocol. The function then processes the data to prepare it for display in the view, including formatting reviewer comments and normalizing status values. Finally, it passes all the necessary data to the view for rendering.
    //this fetches all the data of the assessment form of a specific protocol
    public function showAssessmentFormEvaluation(Request $request)
    {
        $user = auth()->user();

        $questionLabels = [
            '1.1' => 'Objectives – Review of viability of expected output',
            '1.2' => 'Literature review – Review of results of previous animal/human studies showing known risks and benefits of intervention, including known adverse drug effects, in case of drug trials',
            '1.3' => 'Research design – Review of appropriateness of design in view of objectives',
            '1.4' => 'Sampling design – Review of appropriateness of sampling methods and techniques',
            '1.5' => 'Sample size – Review of justification of sample size',
            '1.6' => 'Statistical analysis plan (SAP) – Review of appropriateness of statistical methods to be used and how participant data will be summarized',
            '1.7' => 'Data analysis plan – Review of appropriateness of statistical and non-statistical methods of data analysis',
            '1.8' => 'Inclusion criteria – Review of precision of criteria both for scientific merit and safety concerns; and of equitable selection',
            '1.9' => 'Exclusion criteria – Review of precision of criteria both for scientific merit and safety concerns; and of justified selection',
            '1.10' => 'Exclusion criteria – Review of criteria precision both for scientific merit and safety concerns',
            '1.11' => 'Refusal to participate or discontinuance at any time will involve penalty or loss of benefits to which the subject is entitled',
            '1.12' => 'Statement that the study involves research',
            '1.13' => 'Approximate number of participants in the study',
            '1.14' => 'Expected benefits to the community or to society, or contributions to scientific knowledge',
            '1.15' => 'Description of post-study access to the study product or intervention that have been proven safe and effective',
            '1.16' => 'Anticipated payment, if any, to the participant in the course of the study whether money or other forms of material goods, and if so, the kind and amount',
            '1.17' => 'Anticipated expenses, if any, to the participant in the course of the study',
            '1.18' => 'Statement that the study monitor(s), auditor(s), the BERC, and regulatory authorities will be granted direct access to participant’s medical records for purposes ONLY of verification of clinical trial procedures and data',

            '2.1' => 'Specimen handling – Review of specimen storage, access, disposal, and terms of use',
            '2.2' => 'Principal Investigator qualifications – Review of CV and relevant certifications to ascertain capability to manage study related risks',
            '2.3' => 'Suitability of site – Review of adequacy of qualified staff and infrastructures',
            '2.4' => 'Duration – Review of length/extent of human participant involvement in the study',

            '3.1' => 'Conflict of interest – Review of management of conflict arising from financial, familial, or proprietary considerations of the Principal Investigator, sponsor, or the study site',
            '3.2' => 'Privacy and confidentiality – Review of measures or guarantees to protect privacy and confidentiality of participant information as indicated by data collection methods including data protection plans',
            '3.3' => 'Informed consent process – Review of application of the principle of respect for persons, who may solicit consent, how and when it will be done; who may give consent especially in case of special populations like minors and those who are not legally competent to give consent, or indigenous people which require additional clearances',
            '3.4' => 'Vulnerable study populations – Review of involvement of vulnerable study populations and impact on informed consent. Vulnerable groups include children, the elderly, ethnic and racial minority groups, the homeless, prisoners, people with incurable disease, people who are politically powerless, or junior members of a hierarchical group',
            '3.5' => 'Recruitment methods – Review of manner of recruitment including appropriateness of identified recruiting parties',
            '3.6' => 'Assent requirements – Review of feasibility of obtaining assent vis à vis incompetence to consent; Review of applicability of the assent age brackets in children (0-under 7: No assent; 7-under 12: Verbal Assent; 12-under 15: Simplified Assent Form; 15-under 18: Co-sign informed consent form)',
            '3.7' => 'Risks and mitigation – Review of level of risk and measures to mitigate these risks (including physical, psychological, social, economic), including plans for adverse event management; Review of justification for allowable use of placebo as detailed in the Declaration of Helsinki',
            '3.8' => 'Benefits – Review of potential direct benefit to participants; the potential to yield generalizable knowledge about the participant’s condition/problem; non-material compensation to participant (health education or other creative benefits), where no clear, direct benefit from the project will be received by the participant',
            '3.9' => 'Financial compensation – Review of amount and method of compensations, financial incentives, or reimbursement of study-related expenses',
            '3.10' => 'Community impact – Review of impact of the research on the community where the research occurs and/or to whom findings can be linked; including issues like stigma or draining of local capacity; sensitivity to cultural traditions, and involvement of the community in decisions about the conduct of study',
            '3.11' => 'Collaborative studies – Review in terms of collaborative study especially in case of multi-country/multi-institutional studies, including intellectual property rights, publication rights, information and responsibility sharing, transparency, and capacity building',

            '4.1' => 'Purpose of the study',
            '4.2' => 'Expected duration of participation',
            '4.3' => 'Procedures to be carried out',
            '4.4' => 'Discomforts and inconveniences',
            '4.5' => 'Risks (including possible discrimination)',
            '4.6' => 'Random assignment to the trial treatments',
            '4.7' => 'Benefits to the participants',
            '4.8' => 'Alternative treatments procedures',
            '4.9' => 'Compensation and/or medical treatments in case of injury',
            '4.10' => 'Who to contact for pertinent questions and/or for assistance in a research-related injury',
            '4.11' => 'Refusal to participate or discontinuance at any time will involve penalty or loss of benefits to which the subject is entitled',
            '4.12' => 'Statement that the study involves research',
            '4.13' => 'Approximate number of participants in the study',
            '4.14' => 'Expected benefits to the community or to society, or contributions to scientific knowledge',
            '4.15' => 'Description of post-study access to the study product or intervention that have been proven safe and effective',
            '4.16' => 'Anticipated payment, if any, to the participant in the course of the study whether money or other forms of material goods, and if so, the kind and amount',
            '4.17' => 'Anticipated expenses, if any, to the participant in the course of the study',
            '4.18' => 'Statement that the study monitor(s), auditor(s), the BERC, and regulatory authorities will be granted direct access to participant’s medical records for purposes ONLY of verification of clinical trial procedures and data',
            '4.19' => 'Statement describing extent of participant’s right to access his/her records (or lack thereof vis à vis pending request for approval of non or partial disclosure)',
            '4.20' => 'Description of policy regarding the use of genetic tests and familial genetic information, and the precautions in place to prevent disclosure of results to immediate family relative or to others without consent of the participant',
            '4.21' => 'Possible direct or secondary use of participant’s medical records and biological specimens taken in the course of clinical care or in the course of this study',
            '4.22' => 'Plans to destroy collected biological specimen at the end of the study; if not, details about storage (duration, type of storage facility, location, access information) and possible future use; affirming participant’s right to refuse future use, refuse storage, or have the materials destroyed',
            '4.23' => 'Plans to develop commercial products from biological specimens and whether the participant will receive monetary or other benefit from such development',
            '4.24' => 'Statement that the BERC has approved the study and may be reached for information regarding participant rights, grievances, and complaints'
        ];

        $sectionTitles = [
            1 => 'Scientific Design',
            2 => 'Conduct of Study',
            3 => 'Ethical Consideration',
            4 => 'Informed Consent'
        ];

        $protocols = ResearchApplications::whereIn('status', ['under_review', 'review_finished'])
            ->with(['assessmentForm.items', 'informedConsent.items', 'assignedReviewers'])
            ->get();

        $conjoinComments = function ($item, $reviewerNames) {
            $comments = [];

            for ($i = 1; $i <= 3; $i++) {
                $commentCol = "reviewer_{$i}_comments";
                $actionCol  = "reviewer_{$i}_action_required";

                $revName = e($reviewerNames[$i] ?? "Reviewer {$i}");

                if (!empty($item->$commentCol)) {
                    $safeText = nl2br(e($item->$commentCol));

                    if ($item->$actionCol) {
                        $comments[] = "
                            <div class='mb-1 text-[10px] font-black text-gray-800'>{$revName}:</div>
                            <div class='text-red-600 font-medium leading-relaxed'>
                                <span class='font-black uppercase tracking-wider'>Action Required:</span> {$safeText}
                            </div>";
                    } else {
                        $comments[] = "
                            <div class='mb-1 text-[10px] font-black text-gray-800'>{$revName}:</div>
                            <div class='text-gray-700 font-medium leading-relaxed'>{$safeText}</div>";
                    }
                }
            }

            return empty($comments)
                ? '<div class="italic text-gray-400">No comments provided yet.</div>'
                : implode("<div class='my-2.5 border-t border-dashed border-gray-200'></div>", $comments);
        };

        $parseYesNo = function ($remark, $checkType) {
            if (empty($remark)) return false;
            $val = strtolower(trim($remark));
            if ($checkType === 'yes') return in_array($val, ['yes', 'y', '1', 'true']);
            if ($checkType === 'no') return in_array($val, ['no', 'n', '0', 'false']);
            return false;
        };

        $protocolsData = $protocols->map(function ($protocol) use ($conjoinComments, $parseYesNo, $questionLabels, $sectionTitles) {
            $aForm = $protocol->assessmentForm;
            $iForm = $protocol->informedConsent;

            $protocolCode = $protocol->protocol_code ?? $protocol->id;

            $assignedReviewers = $protocol->assignedReviewers
                ? $protocol->assignedReviewers->keyBy('id')
                : collect();

            $reviewerNames = [
                1 => 'Reviewer 1',
                2 => 'Reviewer 2',
                3 => 'Reviewer 3',
            ];

            $applicationReviewers = DB::table('application_reviewer')
                ->where('protocol_code', $protocolCode)
                ->get()
                ->keyBy('reviewer_id');

            $normalizeStatus = function ($status) {
                $s = strtolower(trim((string) $status));
                return match ($s) {
                    'accepted' => 'Accepted',
                    'declined' => 'Declined',
                    'expired'  => 'Expired',
                    'pending'  => 'Pending',
                    default    => 'Pending',
                };
            };

            $isDone = function ($val) {
                return in_array(strtolower(trim((string) $val)), [
                    'done', 'yes', 'completed', '1', 'true'
                ], true);
            };

            if ($aForm) {
                if ($aForm->reviewer_1_id) {
                    $reviewerNames[1] = $assignedReviewers->get($aForm->reviewer_1_id)->name ?? 'Reviewer 1';
                }
                if ($aForm->reviewer_2_id) {
                    $reviewerNames[2] = $assignedReviewers->get($aForm->reviewer_2_id)->name ?? 'Reviewer 2';
                }
                if ($aForm->reviewer_3_id) {
                    $reviewerNames[3] = $assignedReviewers->get($aForm->reviewer_3_id)->name ?? 'Reviewer 3';
                }
            }

            $reviewers = [];

            foreach ($applicationReviewers as $reviewerId => $appRev) {
                $assigned = $assignedReviewers->get($reviewerId);
                $name = $assigned->name ?? "Reviewer {$reviewerId}";

                $doneValue = null;

                if ($aForm) {
                    if ((int) $aForm->reviewer_1_id === (int) $reviewerId) {
                        $doneValue = $aForm->reviewer_1_done;
                    } elseif ((int) $aForm->reviewer_2_id === (int) $reviewerId) {
                        $doneValue = $aForm->reviewer_2_done;
                    } elseif ((int) $aForm->reviewer_3_id === (int) $reviewerId) {
                        $doneValue = $aForm->reviewer_3_done;
                    }
                }

                $reviewers[] = [
                    'id' => (int) $reviewerId,
                    'name' => $name,
                    'invitationStatus' => $normalizeStatus($appRev->status ?? 'Pending'),
                    'formSubmitted' => $isDone($doneValue),

                    'dateAssigned' => !empty($appRev->date_assigned)
                        ? Carbon::parse($appRev->date_assigned)->toIso8601String()
                        : null,

                    'dateAccepted' => !empty($appRev->date_accepted)
                        ? Carbon::parse($appRev->date_accepted)->toIso8601String()
                        : null,

                    'dateDeclined' => !empty($appRev->date_declined)
                        ? Carbon::parse($appRev->date_declined)->toIso8601String()
                        : null,

                    'dateExpired' => !empty($appRev->date_expired)
                        ? Carbon::parse($appRev->date_expired)->toIso8601String()
                        : null,

                    'declinedReason' => $appRev->declined_reason ?? null,
                ];
            }

            usort($reviewers, fn($a, $b) => $a['id'] <=> $b['id']);

            return [
                'id' => $protocolCode,
                'title' => $protocol->research_title ?? 'Untitled Protocol',
                'proponent' => $protocol->name_of_researcher ?? 'N/A',
                'dateSubmitted' => $protocol->created_at ? $protocol->created_at->toIso8601String() : null,
                'classification' => $protocol->review_classification ?? 'N/A',
                'reviewers' => $reviewers,
                'hasInformedConsent' => !is_null($iForm),

                'assessmentRows' => $aForm ? $aForm->items
                    ->sortBy('question_number', SORT_NATURAL)
                    ->values()
                    ->map(fn($item) => [
                        'id' => $item->id,
                        'points' => $item->question_number,
                        'label' => $questionLabels[$item->question_number] ?? 'Unknown Label',
                        'section' => $sectionTitles[(int) explode('.', $item->question_number)[0]] ?? 'Unknown Section',
                        'yes' => $parseYesNo($item->remark, 'yes'),
                        'no' => $parseYesNo($item->remark, 'no'),
                        'linePage' => $item->line_page ?: 'N/A',
                        'conjoinedComments' => $conjoinComments($item, $reviewerNames),
                        'synthesizedComments' => $item->synthesized_comments ?? '',
                        'synthesizedCommentsActionRequired' => (bool) ($item->synthesized_comments_action_required ?? false),
                    ])->toArray() : [],

                'consentRows' => $iForm ? $iForm->items
                    ->sortBy('question_number', SORT_NATURAL)
                    ->values()
                    ->map(fn($item) => [
                        'id' => $item->id,
                        'points' => $item->question_number,
                        'label' => $questionLabels[$item->question_number] ?? 'Unknown Label',
                        'section' => $sectionTitles[(int) explode('.', $item->question_number)[0]] ?? 'Unknown Section',
                        'yes' => $parseYesNo($item->remark, 'yes'),
                        'no' => $parseYesNo($item->remark, 'no'),
                        'linePage' => $item->line_page ?: 'N/A',
                        'conjoinedComments' => $conjoinComments($item, $reviewerNames),
                        'synthesizedComments' => $item->synthesized_comments ?? '',
                        'synthesizedCommentsActionRequired' => (bool) ($item->synthesized_comments_action_required ?? false),
                    ])->toArray() : [],
            ];
        })->toArray();

        return view('secretariat.pipeline.assessment', compact('user', 'protocolsData'));
    }

    //submodule of the assessment form evaluation page, this function handles the saving of the synthesized comments and action required flags for both the assessment form items and informed consent items. It also updates the protocol status to "Drafting Decision" and logs this action in the research application logs and protocol routing logs. The function ensures that all database operations are performed within a transaction to maintain data integrity, and it returns a JSON response indicating the success or failure of the operation.
    //this function saves the synthesized comments from the secretariat after reviewing the assessment form and informed consent form. It validates the incoming request data, updates the relevant assessment form items and informed consent items with the synthesized comments and action required flags, and then advances the protocol status to "Drafting Decision". It also logs this action in the research application logs and updates the protocol routing logs accordingly. Finally, it returns a JSON response indicating success or failure of the operation.
    public function saveSynthesis(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'protocol_code' => 'required|string',
            'full_board_comments' => 'nullable|string',
            'assessment_items' => 'array',
            'assessment_items.*.id' => 'required|integer',
            'assessment_items.*.synthesized_comments' => 'nullable|string',
            'assessment_items.*.action_required' => 'boolean',
            'icf_items' => 'array',
            'icf_items.*.id' => 'required|integer',
            'icf_items.*.synthesized_comments' => 'nullable|string',
            'icf_items.*.action_required' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $protocolCode = $validated['protocol_code'];

            $application = ResearchApplications::where('protocol_code', $protocolCode)
                ->lockForUpdate()
                ->firstOrFail();

            // Keep your current save behavior intact
            if (!empty($validated['assessment_items'])) {
                foreach ($validated['assessment_items'] as $itemData) {
                    AssessmentFormItem::where('id', $itemData['id'])->update([
                        'synthesized_comments' => $itemData['synthesized_comments'] ?? null,
                        'synthesized_comments_action_required' => $itemData['action_required'] ?? false,
                    ]);
                }
            }

            if (!empty($validated['icf_items'])) {
                foreach ($validated['icf_items'] as $itemData) {
                    InformedConsentItem::where('id', $itemData['id'])->update([
                        'synthesized_comments' => $itemData['synthesized_comments'] ?? null,
                        'synthesized_comments_action_required' => $itemData['action_required'] ?? false,
                    ]);
                }
            }

            $application->update([
                'status' => ResearchApplications::STATUS_DRAFTING_DECISION
            ]);

            ResearchApplicationLog::create([
                'protocol_code' => $protocolCode,
                'user_id' => $user->id,
                'status' => ResearchApplications::STATUS_DRAFTING_DECISION,
                'comment' => 'Secretariat completed assessment validation and synthesized comments.',
            ]);

            DB::table('protocol_routing_logs')
                ->where('protocol_code', $protocolCode)
                ->where('document_nature', 'Completed Review Assessment')
                ->whereNull('to_name')
                ->update([
                    'to_name'    => $user->name,
                    'to_user_id' => $user->id,
                    'updated_at' => now()
                ]);

            DB::table('protocol_routing_logs')->insert([
                'protocol_code'   => $protocolCode,
                'document_nature' => 'Synthesized Assessment Form',
                'from_name'       => $user->name,
                'from_user_id'    => $user->id,
                'to_name'         => $application->name_of_researcher,
                'to_user_id'      => $application->user_id,
                'remarks'         => 'Synthesis completed and forwarded to proponent.',
                'created_at'      => now(),
                'updated_at'      => now()
            ]);

            DB::commit();

            // Delete autosaved temp draft after successful final submission
            $draftFilePath = storage_path("app/temp/secretariat_synthesis_draft_" . auth()->id() . "_{$protocolCode}.json");
            if (File::exists($draftFilePath)) {
                File::delete($draftFilePath);
            }


            return response()->json([
                'status' => 'success',
                'message' => 'Synthesized comments saved successfully. Protocol advanced to decision drafting.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save synthesis: ' . $e->getMessage()
            ], 500);
        }
    }

    //this function shows the decision letter page of the secretariat, it fetches all the protocols that are currently in the drafting decision, assessment processed, awaiting approval, or awaiting chair approval decision status. It also retrieves the associated assessment form items, informed consent items, and assigned reviewers for each protocol. The function processes this data to prepare it for display in the view, including formatting reviewer comments and normalizing status values. Finally, it passes all the necessary data to the view for rendering.
    public function showDecisionLetter(Request $request)
    {
        $user = auth()->user();

        $applications = ResearchApplications::withoutGlobalScopes()
            ->with(['assessmentForm', 'assignedReviewers'])
            ->whereIn('status', [
                'drafting_decision', 'Drafting Decision',
                'assessment_processed', 'Assessment Processed',
                'awaiting_approval', 'Awaiting Approval',
                'awaiting_chair_approval_decision', 'Awaiting Chair Approval Decision'
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($app) {
                $aForm = $app->assessmentForm;
                $assignedReviewers = $app->assignedReviewers
                    ? $app->assignedReviewers->keyBy('id')
                    : collect();

                $reviewers = [];
                $assessmentRows = [];
                $consentRows = [];

                if ($aForm) {
                    if ($aForm->reviewer_1_id) {
                        $reviewers[] = [
                            'id' => (string) $aForm->reviewer_1_id,
                            'name' => $assignedReviewers->get($aForm->reviewer_1_id)->name ?? ('Reviewer ' . $aForm->reviewer_1_id),
                        ];
                    }

                    if ($aForm->reviewer_2_id) {
                        $reviewers[] = [
                            'id' => (string) $aForm->reviewer_2_id,
                            'name' => $assignedReviewers->get($aForm->reviewer_2_id)->name ?? ('Reviewer ' . $aForm->reviewer_2_id),
                        ];
                    }

                    if ($aForm->reviewer_3_id) {
                        $reviewers[] = [
                            'id' => (string) $aForm->reviewer_3_id,
                            'name' => $assignedReviewers->get($aForm->reviewer_3_id)->name ?? ('Reviewer ' . $aForm->reviewer_3_id),
                        ];
                    }

                    $assessmentRows = DB::table('assessment_form_items')
                        ->where('assessment_form_id', $aForm->id)
                        ->get()
                        ->filter(function ($item) {
                            $action = $item->synthesized_comments_action_required;
                            return $action === true || $action === 1 || $action === '1' || $action === 'true' || $action === 't';
                        })
                        ->map(function ($item) {
                            return [
                                'id' => 'assess_' . $item->id,
                                'points' => 'Item ' . ($item->question_number ?? $item->id),
                                'synthesizedComments' => $item->synthesized_comments ?? '',
                                'synthesizedCommentsActionRequired' => true,
                            ];
                        })
                        ->values()
                        ->toArray();
                }

                $icfForm = DB::table('icf_assessments')
                    ->where('protocol_code', $app->protocol_code)
                    ->first();

                if ($icfForm) {
                    $consentRows = DB::table('icf_assessment_items')
                        ->where('icf_assessment_id', $icfForm->id)
                        ->get()
                        ->filter(function ($item) {
                            $action = $item->synthesized_comments_action_required;
                            return $action === true || $action === 1 || $action === '1' || $action === 'true' || $action === 't';
                        })
                        ->map(function ($item) {
                            return [
                                'id' => 'icf_' . $item->id,
                                'points' => 'ICF Item ' . ($item->question_number ?? $item->id),
                                'synthesizedComments' => $item->synthesized_comments ?? '',
                                'synthesizedCommentsActionRequired' => true,
                            ];
                        })
                        ->values()
                        ->toArray();
                }

                $basicDocs = DB::table('basic_requirements')
                    ->where('protocol_code', $app->protocol_code)
                    ->get()
                    ->map(function ($doc) {
                        return [
                            'id' => 'basic_' . $doc->id,
                            'type' => 'basic',
                            'category' => $doc->type,
                            'name' => ucwords(str_replace('_', ' ', $doc->type)),
                            'description' => $doc->description,
                            'file' => $doc->file_path,
                            'url' => asset('storage/' . $doc->file_path),
                            'uploaded_at' => $doc->created_at,
                        ];
                    });

                $suppDocs = DB::table('supplementary_documents')
                    ->where('protocol_code', $app->protocol_code)
                    ->get()
                    ->map(function ($doc) {
                        return [
                            'id' => 'supp_' . $doc->id,
                            'type' => 'supplementary',
                            'category' => $doc->type ?? 'supplementary',
                            'name' => ucwords(str_replace('_', ' ', $doc->type ?? 'supplementary')),
                            'description' => $doc->description ?? null,
                            'file' => $doc->file_path,
                            'url' => asset('storage/' . $doc->file_path),
                            'uploaded_at' => $doc->created_at,
                        ];
                    });

                $docs = collect()
                    ->merge($basicDocs)
                    ->merge($suppDocs)
                    ->groupBy('category')
                    ->map(function ($items, $category) {
                        return [
                            'category' => $category,
                            'label' => ucwords(str_replace('_', ' ', $category)),
                            'files' => $items->values(),
                        ];
                    })
                    ->values()
                    ->toArray();

                return [
                    'id' => $app->protocol_code,
                    'db_id' => $app->id,
                    'title' => $app->research_title ?? 'Untitled Protocol',
                    'proponent' => $app->name_of_researcher ?? 'N/A',
                    'classification' => $app->review_classification ?? 'N/A',
                    'institution' => $app->institution ?? 'N/A',
                    'address' => $app->institution_address ?? 'N/A',
                    'dateSubmitted' => $app->created_at ? Carbon::parse($app->created_at)->toIso8601String() : null,
                    'status' => str_replace(' ', '_', strtolower($app->status)),
                    'reviewers' => $reviewers,
                    'assessmentRows' => $assessmentRows,
                    'consentRows' => $consentRows,
                    'docs' => $docs,
                ];
            });

        $drafting = $applications->filter(function ($item) {
            return in_array($item['status'], ['drafting_decision', 'assessment_processed']);
        })->values()->toArray();

        $awaiting = $applications->filter(function ($item) {
            return in_array($item['status'], ['awaiting_approval', 'awaiting_chair_approval_decision']);
        })->values()->toArray();

        $initialData = [
            'drafting' => $drafting,
            'awaiting' => $awaiting,
        ];

        return view('secretariat.pipeline.decision', compact('user', 'initialData'));
    }

    //this function submits the decision letter drafted by the secretariat, it validates the incoming request data, creates or updates the decision letter record, updates the main protocol status to "Awaiting Approval", and logs this action in both the research application logs and protocol routing logs. The function ensures that all database operations are performed within a transaction to maintain data integrity, and it returns a JSON response indicating the success or failure of the operation.
    public function saveLetter(Request $request)
    {
        // Updated validation to accept the new split resubmit statuses
        $validated = $request->validate([
            'protocol_code' => 'required|string|exists:research_applications,protocol_code',
            'decision_status' => 'required|in:approved,minor_revision,major_revision,rejected',
            'letter_data.date' => 'required|date',
            'letter_data.proponent' => 'nullable|string',
            'letter_data.designation' => 'nullable|string',
            'letter_data.institution' => 'nullable|string',
            'letter_data.address' => 'nullable|string',
            'letter_data.title' => 'required|string',
            'letter_data.subject' => 'nullable|string',
            'letter_data.dearName' => 'nullable|string',
            'letter_data.supportDate' => 'nullable|date',
            'letter_data.documents' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $protocolCode = $validated['protocol_code'];
            $data = $validated['letter_data'];
            $decisionStatus = $validated['decision_status'];
            $currentUser = auth()->user();

            // 1. Create/Update the Decision Letter Record
            DecisionLetter::updateOrCreate(
                ['protocol_code' => $protocolCode],
                [
                    'decision_status' => $decisionStatus,
                    'letter_date' => $data['date'],
                    'proponent' => $data['proponent'],
                    'designation' => $data['designation'],
                    'institution' => $data['institution'],
                    'address' => $data['address'],
                    'title' => $data['title'],
                    'subject' => $data['subject'],
                    'dear_name' => $data['dearName'],
                    'support_date' => $data['supportDate'],
                    'documents' => isset($data['documents']) ? json_encode($data['documents']) : null,
                ]
            );

            // 2. Update the Main Protocol Status
            ResearchApplications::withoutGlobalScopes()
                ->where('protocol_code', $protocolCode)
                ->update(['status' => 'awaiting_approval']);

            // 3. Create Standard Application History Log
            ResearchApplicationLog::create([
                'protocol_code' => $protocolCode,
                'user_id' => $currentUser->id,
                'status' => 'awaiting_approval',
                'comment' => "Secretariat drafted Decision Letter (Status: " . strtoupper(str_replace('_', ' ', $decisionStatus)) . ") and routed to Chair."
            ]);

            // --- PROTOCOL ROUTING LOG ---
            DB::table('protocol_routing_logs')->insert([
                'protocol_code'   => $protocolCode,
                'document_nature' => 'Draft Decision Letter',
                'from_name'       => $currentUser->name,
                'from_user_id'    => $currentUser->id,
                'to_name'         => null,
                'to_user_id'      => null,
                'remarks'         => 'Drafted and routed to REC Chair for final approval.',
                'created_at'      => now(),
                'updated_at'      => now()
            ]);

            DB::commit();

            return response()->json(['message' => 'Decision letter saved successfully'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to save letter: ' . $e->getMessage()], 500);
        }
    }

    //this function is a helper to properly display the application details to alpine.js, it fetches the application based on the provided protocol code, retrieves the associated payment information, basic requirements, and supplementary documents. It processes the document paths to generate proper URLs for viewing, and organizes the data into a structured format before returning it as a JSON response. The function also includes error handling to log any exceptions that occur during the data retrieval process.
    public function getApplicationDetails($protocol_code)
    {
        try {
            // 1. Fetch the application
            $app = ResearchApplications::with(['payment'])
                ->where('protocol_code', $protocol_code)
                ->firstOrFail();

            // 2. Fetch Requirements
            $basicDocs = DB::table('basic_requirements')
                ->where('protocol_code', $protocol_code)
                ->orderBy('created_at', 'desc')
                ->get();

            $suppDocs = DB::table('supplementary_documents')
                ->where('protocol_code', $protocol_code)
                ->orderBy('created_at', 'desc')
                ->get();

            // 3. Helper to safely generate URLs (Prevents PHP 500 crashes on null paths)
            $processDoc = function($doc) use ($protocol_code) {
                $path = $doc->file_path ?? '';
                $cleanPath = str_replace('documents/'.$protocol_code.'/', '', $path);
                return [
                    'id' => $doc->id,
                    'url' => $cleanPath !== '' ? route('view.document', ['protocol_code' => $protocol_code, 'filename' => $cleanPath]) : null,
                    'description' => $doc->description ?? 'View File'
                ];
            };

            // 4. Organize documents
            $documentGroups = [];

            foreach ($basicDocs as $doc) {
                if (!isset($documentGroups[$doc->type])) $documentGroups[$doc->type] = [];
                $documentGroups[$doc->type][] = $processDoc($doc);
            }

            foreach ($suppDocs as $doc) {
                if (!isset($documentGroups[$doc->type])) $documentGroups[$doc->type] = [];
                $documentGroups[$doc->type][] = $processDoc($doc);
            }

            // 5. Safe Payment Processing
            $paymentData = null;
            if ($app->payment) {
                $proofUrl = null;
                if (!empty($app->payment->proof_of_payment_path)) {
                    $cleanProofPath = str_replace('documents/'.$protocol_code.'/', '', $app->payment->proof_of_payment_path);
                    $proofUrl = route('view.document', ['protocol_code' => $protocol_code, 'filename' => $cleanProofPath]);
                }

                $paymentData = [
                    'payment_method' => $app->payment->payment_method,
                    'reference_number' => $app->payment->reference_number,
                    'proof_url' => $proofUrl
                ];
            }

            // 6. Return Data to Alpine.js
            return response()->json([
                'id' => $app->id,
                'protocol_code' => $app->protocol_code,
                'research_title' => $app->research_title,
                'primary_researcher' => $app->primary_researcher ?? $app->name_of_researcher,
                'payment' => $paymentData,
                'documents' => $documentGroups
            ]);

        } catch (\Exception $e) {
            // If anything fails, it will log the exact error to storage/logs/laravel.log instead of just returning 500
            Log::error("Application Fetch Error: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error', 'details' => $e->getMessage()], 500);
        }
    }

    //this function shows the validation page for the resubmissionss in the secretariat pipeline, it fetches all the research application revisions that are currently in the "submitted" status, retrieves the associated research application and revision responses for each revision, and prepares the data for display in the view. The function also includes a dictionary to map question numbers to their corresponding labels for better readability in the view.
    public function showRevisionValidation()
    {
        $user = auth()->user();

        // Labels dictionary
        $questionLabels = [
            '1.1' => 'Viability of expected output',
            '1.2' => 'Previous animal/human studies',
            '1.3' => 'Appropriateness of design',
            '1.4' => 'Sampling methods',
            '1.5' => 'Sample size justification',
            '1.6' => 'Statistical methods',
            '1.7' => 'Data analysis methods',
            '1.8' => 'Merit and safety criteria',
            '1.9' => 'Justified exclusion',
            '1.10' => 'Criteria precision',
            '1.11' => 'Penalty/Benefit statement',
            '1.12' => 'Research statement',
            '1.13' => 'Number of participants',
            '1.14' => 'Community benefits',
            '1.15' => 'Post-study access',
            '1.16' => 'Anticipated payment',
            '1.17' => 'Anticipated expenses',
            '1.18' => 'Medical record access',

            '2.1' => 'Specimen storage & disposal',
            '2.2' => 'Researcher CV/Capability',
            '2.3' => 'Staff & Infrastructure',
            '2.4' => 'Extent of involvement',

            '3.1' => 'Conflict of Interest',
            '3.2' => 'Privacy & Confidentiality',
            '3.3' => 'Consent Principle',
            '3.4' => 'Vulnerable Populations',
            '3.5' => 'Recruitment Manner',
            '3.6' => 'Assent for Minors',
            '3.7' => 'Risk Mitigation',
            '3.8' => 'Direct Benefits',
            '3.9' => 'Reimbursements',
            '3.10' => 'Community Impact',
            '3.11' => 'Collaborative Terms',

            '4.1' => 'Purpose',
            '4.2' => 'Duration',
            '4.3' => 'Procedures',
            '4.4' => 'Discomforts',
            '4.5' => 'Risks',
            '4.6' => 'Randomization',
            '4.7' => 'Benefits',
            '4.8' => 'Alternatives',
            '4.9' => 'Compensation',
            '4.10' => 'Contact Persons',
            '4.11' => 'Voluntary Nature',
            '4.12' => 'Research Nature',
            '4.13' => 'Participant Count',
            '4.14' => 'Community Benefit',
            '4.15' => 'Post-study Access',
            '4.16' => 'Payments',
            '4.17' => 'Expenses',
            '4.18' => 'Medical Record Access',
            '4.19' => 'Right to Access',
            '4.20' => 'Genetic Policy',
            '4.21' => 'Secondary Use',
            '4.22' => 'Storage/Destruction',
            '4.23' => 'Commercialization',
            '4.24' => 'BERC Approval & Contact',
        ];

        $revisions = ResearchApplicationRevision::where('status', 'submitted')
            ->orderBy('updated_at', 'desc')
            ->get();

        $protocolsData = [];

        foreach ($revisions as $rev) {
            $app = ResearchApplications::where('protocol_code', $rev->protocol_code)->first();

            $responses = RevisionResponse::where('protocol_code', $rev->protocol_code)
                ->where('revision_number', $rev->revision_number)
                ->orderBy('id', 'asc')
                ->get();

            $revisionRows = $responses->map(function ($r) use ($questionLabels) {
                $itemLabel = $questionLabels[$r->item] ?? null;

                return [
                    'id'                  => $r->id,
                    'item'                => $r->item,
                    'item_label'          => $itemLabel,
                    'item_display'        => $itemLabel ? $r->item . ' - ' . $itemLabel : $r->item,
                    'section_and_page'    => $r->section_and_page,
                    'berc_recommendation' => $r->berc_recommendation,
                    'researcher_response' => $r->researcher_response,
                ];
            });

            $protocolsData[] = [
                'id'             => $rev->protocol_code,
                'protocol_code'  => $rev->protocol_code,
                'version'        => 'V' . $rev->revision_number,
                'revision_number'=> $rev->revision_number,
                'title'          => $app->research_title ?? 'Unknown Title',
                'proponent'      => $app->name_of_researcher ?? 'Unknown Proponent',
                'classification' => $app->review_classification ?? 'Minor',
                'dateSubmitted'  => Carbon::parse($rev->updated_at)->format('Y-m-d'),
                'revisionRows'   => $revisionRows,
            ];
        }

        return view('secretariat.pipeline.revisionvalidation', compact('user', 'protocolsData'));
    }

    //this is the subfunction that saves or finalizes the secretariat decision of the validation of the resubmitted revision, it validates the incoming request data, finds the active submitted revision for the specified protocol, updates the revision status based on the Secretariat's decision (approve or reject), saves any optional comments from the Secretariat, and returns a JSON response indicating the outcome of the operation.
    public function completeRevisionValidation(Request $request)
    {
        // Require the protocol code and action, but make the comment optional
        $request->validate([
            'protocol_code'       => 'required|string',
            'action'              => 'required|in:approve,reject',
            'secretariat_comment' => 'nullable|string|max:1000'
        ]);

        // Find the active submitted revision for this protocol
        $revision = ResearchApplicationRevision::where('protocol_code', $request->protocol_code)
            ->where('status', 'submitted')
            ->latest('revision_number')
            ->first();

        if (!$revision) {
            return response()->json([
                'message' => 'No submitted revision found for this protocol.'
            ], 404);
        }

        // Branch logic based on the Secretariat's decision
        if ($request->action === 'reject') {
            $revision->status = 'incorrect';
            $message = 'Resubmission rejected and returned to the researcher.';
        } else {
            $revision->status = 'under_review';
            $message = 'Validation complete. Protocol is now under review.';
        }

        // Save the optional comment if provided
        if ($request->has('secretariat_comment')) {
            $revision->secretariat_comment = $request->secretariat_comment;
        }

        $revision->save();

        return response()->json([
            'message' => $message
        ], 200);
    }

    //this shows the resubmission forms and comments of the reviewers
    public function showRevisionComments()
    {
        $user = auth()->user();

        $questionLabels = [
            '1.1' => 'Viability of expected output', '1.2' => 'Previous animal/human studies', '1.3' => 'Appropriateness of design',
            '1.4' => 'Sampling methods', '1.5' => 'Sample size justification', '1.6' => 'Statistical methods',
            '1.7' => 'Data analysis methods', '1.8' => 'Merit and safety criteria', '1.9' => 'Justified exclusion',
            '1.10' => 'Criteria precision', '1.11' => 'Penalty/Benefit statement', '1.12' => 'Research statement',
            '1.13' => 'Number of participants', '1.14' => 'Community benefits', '1.15' => 'Post-study access',
            '1.16' => 'Anticipated payment', '1.17' => 'Anticipated expenses', '1.18' => 'Medical record access',
            '2.1' => 'Specimen storage & disposal', '2.2' => 'Researcher CV/Capability', '2.3' => 'Staff & Infrastructure', '2.4' => 'Extent of involvement',
            '3.1' => 'Conflict of Interest', '3.2' => 'Privacy & Confidentiality', '3.3' => 'Consent Principle',
            '3.4' => 'Vulnerable Populations', '3.5' => 'Recruitment Manner', '3.6' => 'Assent for Minors',
            '3.7' => 'Risk Mitigation', '3.8' => 'Direct Benefits', '3.9' => 'Reimbursements',
            '3.10' => 'Community Impact', '3.11' => 'Collaborative Terms',
            '4.1' => 'Purpose', '4.2' => 'Duration', '4.3' => 'Procedures', '4.4' => 'Discomforts',
            '4.5' => 'Risks', '4.6' => 'Randomization', '4.7' => 'Benefits', '4.8' => 'Alternatives',
            '4.9' => 'Compensation', '4.10' => 'Contact Persons', '4.11' => 'Voluntary Nature',
            '4.12' => 'Research Nature', '4.13' => 'Participant Count', '4.14' => 'Community Benefit',
            '4.15' => 'Post-study Access', '4.16' => 'Payments', '4.17' => 'Expenses',
            '4.18' => 'Medical Record Access', '4.19' => 'Right to Access', '4.20' => 'Genetic Policy',
            '4.21' => 'Secondary Use', '4.22' => 'Storage/Destruction', '4.23' => 'Commercialization',
            '4.24' => 'BERC Approval & Contact'
        ];

        $sectionTitles = [
            1 => 'Scientific Design',
            2 => 'Conduct of Study',
            3 => 'Ethical Consideration',
            4 => 'Informed Consent'
        ];

        $revisions = ResearchApplicationRevision::where('status', 'review_finished')
            ->orderBy('updated_at', 'desc')
            ->get();

        $protocolsData = [];

        foreach ($revisions as $rev) {
            $app = ResearchApplications::where('protocol_code', $rev->protocol_code)->first();

            $responses = RevisionResponse::where('protocol_code', $rev->protocol_code)
                ->where('revision_number', $rev->revision_number)
                ->orderBy('id', 'asc')
                ->get();

            $revisionRows = $responses->map(function ($r) use ($questionLabels, $sectionTitles) {
                $combinedRemarks = "";

                if (!empty($r->reviewer1_remarks)) {
                    $status = $r->reviewer1_action === 'resolved'
                        ? '<span style="color:green">✓</span>'
                        : '<span style="color:red">⚠</span>';
                    $combinedRemarks .= "<div class='mb-2'><b>Rev 1 {$status}:</b> {$r->reviewer1_remarks}</div>";
                }

                if (!empty($r->reviewer2_remarks)) {
                    $status = $r->reviewer2_action === 'resolved'
                        ? '<span style="color:green">✓</span>'
                        : '<span style="color:red">⚠</span>';
                    $combinedRemarks .= "<div class='mb-2'><b>Rev 2 {$status}:</b> {$r->reviewer2_remarks}</div>";
                }

                if (!empty($r->reviewer3_remarks)) {
                    $status = $r->reviewer3_action === 'resolved'
                        ? '<span style="color:green">✓</span>'
                        : '<span style="color:red">⚠</span>';
                    $combinedRemarks .= "<div><b>Rev 3 {$status}:</b> {$r->reviewer3_remarks}</div>";
                }

                $itemCode = $r->item; // e.g. "1.3"
                $sectionPage = $r->section_and_page; // e.g. "1.3 (Page 5)"
                $mainSection = (int) strtok($itemCode, '.');

                $itemLabel = $questionLabels[$itemCode] ?? 'Unlabeled Item';

                // Combine item + label
                $itemDisplay = "{$itemCode} - {$itemLabel}";

                return [
                    'id'                  => $r->id,
                    'berc_recommendation' => $r->berc_recommendation,
                    'researcher_response' => $r->researcher_response,

                    // ✅ NEW combined field
                    'item_display'        => $itemDisplay,

                    // keep if you still need page info separately
                    'section_and_page'    => $sectionPage,

                    'section_title'       => $sectionTitles[$mainSection] ?? '',
                    'reviewers_remarks'   => $combinedRemarks ?: '<i class="text-gray-400">No remarks provided.</i>',
                    'synthesizedComments' => $r->synthesized_comments ?? '',
                    'action'              => $r->synthesized_comments_action ?? ''
                ];
            });

            $protocolsData[] = [
                'id'             => $rev->protocol_code,
                'version'        => 'V' . $rev->revision_number,
                'title'          => $app->research_title ?? 'Unknown Title',
                'proponent'      => $app->name_of_researcher ?? 'Unknown Proponent',
                'classification' => $app->review_classification ?? 'Minor',
                'dateSubmitted'  => Carbon::parse($rev->updated_at)->format('Y-m-d'),
                'revisionRows'   => $revisionRows
            ];
        }

        return view('secretariat.pipeline.revisioncomments', compact('user', 'protocolsData'));
    }

    //this function validates and saves the synthesized comments and actions of the secretariat for each revision response item after the reviewers have completed their assessment. It updates the status of the overall revision to "assessment_processed", logs the action in the protocol routing logs, and cleans up any temporary draft files related to this resubmission version. The function ensures that all database operations are performed within a transaction to maintain data integrity, and it returns a JSON response indicating the success or failure of the operation.
    public function saveResubmissionSynthesis(Request $request)
    {
        $user = auth()->user();

        // 1. Validate the incoming payload and assign it to $validated
        $validated = $request->validate([
            'protocol_code' => 'required|string',
            'revision_number' => 'required|integer',
            'rows' => 'required|array',
            'rows.*.id' => 'required|integer|exists:revision_responses,id',
            'rows.*.synthesized_comments' => 'nullable|string',
            'rows.*.synthesized_comments_action' => 'nullable|string|in:resolved,action_required',
        ]);

        // Safely extract variables
        $protocolCode = $validated['protocol_code'];
        $revisionNumber = $validated['revision_number'];

        DB::beginTransaction();

        try {
            // 2. Update individual Revision Response rows
            foreach ($validated['rows'] as $rowData) {
                // Find the specific existing record
                $response = RevisionResponse::find($rowData['id']);

                if ($response) {
                    // Update the synthesized fields
                    $response->synthesized_comments = $rowData['synthesized_comments'];
                    $response->synthesized_comments_action = $rowData['synthesized_comments_action'];
                    $response->save();
                }
            }

            // 3. Update the overall Revision Status
            ResearchApplicationRevision::where('protocol_code', $protocolCode)
                ->where('revision_number', $revisionNumber)
                ->update([
                    'status' => 'assessment_processed'
                ]);

            // 4. ─── UPDATE PREVIOUS REVIEWER LOGS ───
            // Close the loop on the reviewers' submission by setting the "to" fields to the Secretariat
            DB::table('protocol_routing_logs')
                ->where('protocol_code', $protocolCode)
                ->where('document_nature', 'Reviewer Comments (Version ' . $revisionNumber . ')')
                ->whereNull('to_user_id') // Only update the pending ones
                ->update([
                    'to_name'    => $user->name,
                    'to_user_id' => $user->id,
                    'updated_at' => now()
                ]);

            // 5. ─── LOG NEW ACTION TO PROTOCOL ROUTING LOGS ───
            DB::table('protocol_routing_logs')->insert([
                'protocol_code'   => $protocolCode,
                'document_nature' => 'Synthesized Comments (Version ' . $revisionNumber . ')',
                'from_name'       => $user->name,
                'from_user_id'    => $user->id,
                'to_name'         => null, // Blank for now (Waiting for Decision Letter drafting)
                'to_user_id'      => null, // Blank for now
                'remarks'         => 'Secretariat synthesized reviewer comments for the revised protocol.',
                'created_at'      => now(),
                'updated_at'      => now()
            ]);

            // Clean up the private temp draft file for this specific resubmission version
            $draftFilePath = storage_path("app/temp/secretariat_resub_draft_" . auth()->id() . "_{$request->protocol_code}_v{$request->revision_number}.json");
            if (File::exists($draftFilePath)) {
                File::delete($draftFilePath);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Synthesis saved successfully.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error saving synthesis: ' . $e->getMessage()
            ], 500);
        }
    }

    //this function shows the decision page for resubmissions in the secretariat pipeline, it fetches all the research application revisions that have completed the synthesis process (status = 'assessment_processed'), retrieves the associated research application and revision responses for each revision, and prepares the data for display in the view. The function also combines the reviewers' remarks into a single field for easier display and includes a mapping of question numbers to their corresponding labels for better readability in the view.
    public function showResubmissionDecision()
    {
        $user = auth()->user();

        // 1. Fetch revisions where synthesis is complete (status = 'assessment_processed')
        $revisions = ResearchApplicationRevision::where('status', 'assessment_processed')
            ->orderBy('updated_at', 'desc')
            ->get();

        $protocolsData = [];

        foreach ($revisions as $rev) {
            $app = ResearchApplications::where('protocol_code', $rev->protocol_code)->first();

            $responses = RevisionResponse::where('protocol_code', $rev->protocol_code)
                ->where('revision_number', $rev->revision_number)
                ->orderBy('id', 'asc')
                ->get();

            // 2. Map the responses for read-only viewing
            $revisionRows = $responses->map(function($r) {
                $combinedRemarks = "";
                if (!empty($r->reviewer1_remarks)) {
                    $status = $r->reviewer1_action === 'resolved' ? '<span style="color:green">✓</span>' : '<span style="color:red">⚠</span>';
                    $combinedRemarks .= "<div class='mb-2'><b>Rev 1 {$status}:</b> {$r->reviewer1_remarks}</div>";
                }
                if (!empty($r->reviewer2_remarks)) {
                    $status = $r->reviewer2_action === 'resolved' ? '<span style="color:green">✓</span>' : '<span style="color:red">⚠</span>';
                    $combinedRemarks .= "<div class='mb-2'><b>Rev 2 {$status}:</b> {$r->reviewer2_remarks}</div>";
                }
                if (!empty($r->reviewer3_remarks)) {
                    $status = $r->reviewer3_action === 'resolved' ? '<span style="color:green">✓</span>' : '<span style="color:red">⚠</span>';
                    $combinedRemarks .= "<div><b>Rev 3 {$status}:</b> {$r->reviewer3_remarks}</div>";
                }

                return [
                    'id'                  => $r->id,
                    'item'                => $r->item, // ADDED ITEM BINDING
                    'berc_recommendation' => $r->berc_recommendation,
                    'researcher_response' => $r->researcher_response,
                    'section_and_page'    => $r->section_and_page,
                    'reviewers_remarks'   => $combinedRemarks ?: '<i class="text-gray-400">No remarks provided.</i>',
                    'synthesized_comments'=> $r->synthesized_comments ?? '<i class="text-gray-400">No synthesis provided.</i>',
                    'action'              => $r->synthesized_comments_action ?? 'pending'
                ];
            });

            $protocolsData[] = [
                'id'             => $rev->protocol_code,
                'version'        => 'V' . $rev->revision_number,
                'title'          => $app->research_title ?? 'Unknown Title',
                'proponent'      => $app->name_of_researcher ?? 'Unknown Proponent',
                'institution'    => $app->institution ?? 'Unknown',
                'institution_address'   => $app->institution_address ?? 'Unknown',
                'dateSubmitted'  => Carbon::parse($rev->updated_at)->format('Y-m-d'),
                'revisionRows'   => $revisionRows
            ];
        }

        return view('secretariat.pipeline.revisiondecision', compact('user', 'protocolsData'));
    }

    //this function saves the decision of the secretariat on the resubmitted revision, it validates the incoming request data, creates a new decision letter record with the provided information, updates the status of the main revision tracker to "awaiting_chair_approval", logs the action in the protocol routing logs, and returns a JSON response indicating the success or failure of the operation. The function ensures that all database operations are performed within a transaction to maintain data integrity.
    public function saveResubmissionDecision(Request $request)
    {
        $user = auth()->user();

        // 1. Validate the incoming payload
        $validated = $request->validate([
            'protocol_code' => 'required|string|exists:research_application_revisions,protocol_code',
            'revision_number' => 'required|integer',
            'decision_status' => 'required|string|in:approved,minor_revision,major_revision,rejected',

            // Letter Data mapping
            'letter_data.date' => 'required|date',
            'letter_data.proponent' => 'nullable|string',
            'letter_data.designation' => 'nullable|string',
            'letter_data.institution' => 'nullable|string',
            'letter_data.address' => 'nullable|string',
            'letter_data.title' => 'nullable|string',
            'letter_data.subject' => 'nullable|string',
            'letter_data.dearName' => 'nullable|string',
            'letter_data.supportDate' => 'nullable|date',
            'letter_data.documents' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $protocolCode = $validated['protocol_code'];
            $letterData = $validated['letter_data'];
            $version = $validated['revision_number'];

            // 2. Create the Decision Letter (Insert Only)
            DB::table('revision_decision_letters')->insert([
                'protocol_code'   => $protocolCode,
                'decision_status' => $validated['decision_status'],
                'letter_date'     => $letterData['date'],
                'proponent'       => $letterData['proponent'],
                'designation'     => $letterData['designation'],
                'institution'     => $letterData['institution'],
                'address'         => $letterData['address'],
                'title'           => $letterData['title'],
                'subject'         => $letterData['subject'],
                'dear_name'       => $letterData['dearName'],
                'support_date'    => $letterData['supportDate'] ?? null,
                'version_number'  => $version,
                'documents'       => isset($letterData['documents']) ? json_encode(array_filter($letterData['documents'])) : null,
                'approval_status' => 'draft',
                'updated_at'      => now(),
                'created_at'      => now()
            ]);

            // 3. Update the Main Revision Tracker Status
            ResearchApplicationRevision::where('protocol_code', $protocolCode)
                ->where('revision_number', $version)
                ->update([
                    'status' => 'awaiting_chair_approval'
                ]);

            // 4. ─── UPDATE PREVIOUS SYNTHESIS LOG ───
            // Update the "Synthesized Comments" instance to set "to" to the current user
            DB::table('protocol_routing_logs')
                ->where('protocol_code', $protocolCode)
                ->where('document_nature', "Synthesized Comments (Version {$version})")
                ->whereNull('to_user_id')
                ->update([
                    'to_name'    => $user->name,
                    'to_user_id' => $user->id,
                    'updated_at' => now()
                ]);

            // 5. ─── LOG NEW INSTANCE: Decision Letter ───
            // Leave "to" blank as it is routed for Chair approval/signature
            DB::table('protocol_routing_logs')->insert([
                'protocol_code'   => $protocolCode,
                'document_nature' => "Draft Decision Letter (Version {$version})",
                'from_name'       => $user->name,
                'from_user_id'    => $user->id,
                'to_name'         => null,
                'to_user_id'      => null,
                'remarks'         => 'Secretariat drafted the revision decision letter and routed it to the Chair.',
                'created_at'      => now(),
                'updated_at'      => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Decision letter drafted and routed to the Chair successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Secretariat Resubmission Decision Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save decision letter: ' . $e->getMessage()
            ], 500);
        }
    }

    //this function saves the secretariat's draft of the synthesis of the original application
    public function saveDraft(Request $request, $protocol_code)
    {
        $userId = auth()->id();
        $tempDir = storage_path('app/temp');

        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        // Distinct filename for secretariat drafts
        $filePath = $tempDir . "/secretariat_draft_{$userId}_{$protocol_code}.json";

        File::put($filePath, json_encode($request->all()));

        return response()->json(['success' => true]);
    }

    //this function retrieves the saved draft of the synthesis of the original application if it exists
    public function getDraft($protocol_code)
    {
        $userId = auth()->id();
        $filePath = storage_path("app/temp/secretariat_draft_{$userId}_{$protocol_code}.json");

        if (File::exists($filePath)) {
            $content = File::get($filePath);
            return response()->json(json_decode($content, true));
        }

        return response()->json(null, 404);
    }

    //this function saves the draft of a resubmission for the secretariat
    public function saveResubmissionDraft(Request $request, $protocol_code, $revision_number)
    {
        $userId = auth()->id();
        $tempDir = storage_path('app/temp');

        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        // Distinct filename for RESUBMISSION drafts using the version number
        $filePath = $tempDir . "/secretariat_resub_draft_{$userId}_{$protocol_code}_v{$revision_number}.json";

        File::put($filePath, json_encode($request->all()));

        return response()->json(['success' => true]);
    }

    //this function retrieves the saved draft of the synthesis of the resubmission if it exists
    public function getResubmissionDraft($protocol_code, $revision_number)
    {
        $userId = auth()->id();
        $filePath = storage_path("app/temp/secretariat_resub_draft_{$userId}_{$protocol_code}_v{$revision_number}.json");

        if (File::exists($filePath)) {
            $content = File::get($filePath);
            return response()->json(json_decode($content, true));
        }

        return response()->json(null, 404);
    }

    //this function shows the calendar view for the secretariat, it retrieves the authenticated user's information and passes it to the calendar view for display.
    public function showCalendar(Request $request){
        $user = auth()->user();
        return view('secretariat.calendar', compact('user'));
    }

    //this is a helper function that formats file sizes into human-readable strings, it takes a file size in bytes as input and returns a formatted string with the appropriate unit (bytes, KB, MB, GB) based on the size of the input. The function uses conditional statements to determine the correct unit and formats the number to two decimal places for larger units.
    function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }

    //this function shows the history of research applications in the secretariat pipeline, it retrieves the authenticated user's information, applies pagination, search, and status filters to the research applications, gathers statistics for the filter dropdown, fetches all revisions and decision letters for the paginated applications, formats the main applications with their associated documents and assessments, and passes the data to the history view for display.
    public function showHistory(Request $request)
    {
        $user = auth()->user();

        // 1. Set up pagination and filters from the request
        $perPage = $request->input('per_page', 10);
        $searchTerm = $request->input('search', '');
        $statusFilter = $request->input('status', 'all');

        // 2. Start the query builder
        $query = ResearchApplications::with('supplementaryDocuments');

        // 3. Apply Search Filter
        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('protocol_code', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('research_title', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('name_of_researcher', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // 4. Apply Status Filter
        if ($statusFilter !== 'all') {
            if ($statusFilter === 'completed') {
                $query->whereIn('status', ['approved', 'completed']);
            } elseif ($statusFilter === 'rejected') {
                $query->whereIn('status', ['disapproved', 'rejected']);
            }
        }

        // 5. Execute Pagination
        $applications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // 6. Gather statistics for the filter dropdown (ignoring the current filter/search)
        $counts = [
            'all' => ResearchApplications::count(),
            'completed' => ResearchApplications::whereIn('status', ['approved', 'completed'])->count(),
            'rejected' => ResearchApplications::whereIn('status', ['disapproved', 'rejected'])->count(),
        ];

        // 7. FETCH ALL REVISIONS FOR THESE PAGINATED APPLICATIONS
        $protocolCodes = collect($applications->items())->pluck('protocol_code');

        $allRevisions = ResearchApplicationRevision::with('documents')
            ->whereIn('protocol_code', $protocolCodes)
            ->orderBy('revision_number', 'asc')
            ->get()
            ->groupBy('protocol_code');

        // 1. Fetch all revision decision letters upfront to optimize performance
        $decisionLetters = DB::table('revision_decision_letters')
            ->whereIn('protocol_code', $protocolCodes)
            ->get()
            ->groupBy(function($item) {
                return $item->protocol_code . '-' . $item->version_number;
            });

        // 8. FORMAT MAIN APPLICATIONS ($formattedApps)
        $formattedApps = collect($applications->items())->map(function($app) use ($allRevisions) {
            $docs = [];

            $docs[] = [
                'name' => 'Incoming Communications Logbook',
                'file' => route('incoming.logbook.print', ['protocol_code' => $app->protocol_code]),
                'size' => 'Auto',
                'type' => 'pdf'
            ];

            $docs[] = [
                'name' => 'Outgoing Communications Logbook',
                'file' => route('outgoing.logbook.print', ['protocol_code' => $app->protocol_code]),
                'size' => 'Auto',
                'type' => 'pdf'
            ];

            $docs[] = [
                'name' => 'Application Form (System Generated)',
                'file' => route('researcher.application.print', ['id' => $app->id]),
                'size' => 'Auto',
                'type' => 'pdf'
            ];

            $assessment = DB::table('assessment_forms')
                ->where('protocol_code', $app->protocol_code)
                ->first();

            $icfAssessment = DB::table('icf_assessments')
                ->where('protocol_code', $app->protocol_code)
                ->first();

            for ($i = 1; $i <= 3; $i++) {
                $idField   = "reviewer_{$i}_id";
                $doneField = "reviewer_{$i}_done";
                $icfDoneField = "reviwer_{$i}_done"; // Using your schema's typo

                // ─── PROCESS GENERAL ASSESSMENT FORM ───
                if ($assessment && !empty($assessment->$idField)) {
                    $reviewerData = DB::table('reviewers')
                        ->join('users', 'reviewers.user_id', '=', 'users.id')
                        ->where('reviewers.id', $assessment->$idField)
                        ->select('users.name')
                        ->first();

                    $reviewerName = $reviewerData ? $reviewerData->name : "Reviewer $i";

                    $docs[] = [
                        'name' => "Assessment Form - $reviewerName ",
                        'file' => route('assessment.individual.print', [
                            'id'          => $app->id,
                            'reviewer_id' => $assessment->$idField
                        ]),
                        'size' => 'Auto',
                        'type' => 'pdf'
                    ];
                }

                // ─── PROCESS ICF ASSESSMENT FORM ───
                if ($icfAssessment && !empty($icfAssessment->$idField)) {
                    $reviewerData = DB::table('reviewers')
                        ->join('users', 'reviewers.user_id', '=', 'users.id')
                        ->where('reviewers.id', $icfAssessment->$idField)
                        ->select('users.name')
                        ->first();

                    $reviewerName = $reviewerData ? $reviewerData->name : "Reviewer $i";

                    $docs[] = [
                        'name' => "ICF Assessment - $reviewerName ",
                        'file' => route('icf.individual.print', [
                            'id'          => $app->id,
                            'reviewer_id' => $icfAssessment->$idField
                        ]),
                        'size' => 'Auto',
                        'type' => 'pdf'
                    ];
                }
            };

            if ($app->decisionLetter) {
                $docs[] = [
                    'name' => 'Decision Letter (System Generated)',
                    'file' => route('decision.pdf', ['protocol_code' => $app->protocol_code]),
                    'size' => 'Auto',
                    'type' => 'pdf'
                ];
            }

            $addDoc = function($dbPath, $name) use (&$docs, $app) {
                if (!$dbPath) return;

                $parts = explode('/', trim($dbPath, '/'));
                if (count($parts) < 3) return;

                $secureUrl = route('view.document', [
                    'protocol_code' => $parts[1],
                    'filename'      => implode('/', array_slice($parts, 2))
                ]);

                $fullPath = storage_path("app/" . $dbPath);
                $formattedSize = File::exists($fullPath)
                    ? $this->formatSize(File::size($fullPath))
                    : 'Unknown';

                $docs[] = [
                    'name' => $name,
                    'file' => $secureUrl,
                    'size' => $formattedSize,
                    'type' => strtolower(pathinfo($dbPath, PATHINFO_EXTENSION))
                ];
            };

            $documentDictionary = [
                'letter_request'            => 'Letter of Request',
                'endorsement_letter'        => 'Endorsement Letter',
                'full_proposal'             => 'Full Proposal',
                'technical_review_approval' => 'Technical Review Approval',
                'informed_consent'          => 'Informed Consent Form',
                'manuscript'                => 'Manuscript',
                'curriculum_vitae'          => 'Curriculum Vitae',
                'questionnaire'             => 'Questionnaire / Data Gathering Tool',
                'data_collection'           => 'Data Collection Procedures',
                'product_brochure'          => 'Product Brochure',
                'philippine_fda'            => 'Philippine FDA Approval',
                'special_populations'       => 'Special Populations',
                'others'                    => 'Other Document'
            ];

            $basicDocs = DB::table('basic_requirements')
                ->where('protocol_code', $app->protocol_code)
                ->get();

            foreach ($basicDocs as $doc) {
                $baseLabel = $documentDictionary[$doc->type] ?? ucwords(str_replace('_', ' ', $doc->type));
                $displayTitle = !empty($doc->description) && $doc->description !== $baseLabel
                                ? "{$baseLabel} - {$doc->description}" : $baseLabel;
                $addDoc($doc->file_path, $displayTitle);
            }

            $suppDocs = DB::table('supplementary_documents')
                ->where('protocol_code', $app->protocol_code)
                ->get();

            foreach ($suppDocs as $doc) {
                $baseLabel = $documentDictionary[$doc->type] ?? ucwords(str_replace('_', ' ', $doc->type));
                $displayTitle = !empty($doc->description) && $doc->description !== $baseLabel
                                ? "{$baseLabel} - {$doc->description}" : $baseLabel;
                $addDoc($doc->file_path, $displayTitle);
            }

            if ($app->documents) {
                foreach($app->documents as $doc) {
                    $secureUrl = '#';
                    $formattedSize = 'Unknown';

                    if ($doc->file_path) {
                        $parts = explode('/', trim($doc->file_path, '/'));
                        if (count($parts) >= 3) {
                            $secureUrl = route('view.document', [
                                'protocol_code' => $parts[1],
                                'filename'      => implode('/', array_slice($parts, 2))
                            ]);
                        }

                        $fullPath = storage_path("app/" . $doc->file_path);
                        if (File::exists($fullPath)) {
                            $bytes = File::size($fullPath);
                            $formattedSize = $bytes > 0 ? round($bytes / 1024, 2) . ' KB' : 'Unknown';
                            if ($bytes >= 1048576) $formattedSize = round($bytes / 1048576, 2) . ' MB';
                        }
                    }

                    $docs[] = [
                        'name' => $doc->description ?? 'Document',
                        'file' => $secureUrl,
                        'size' => $formattedSize,
                        'type' => strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION))
                    ];
                }
            }

            $statusText = match(strtolower($app->status)) {
                'approved' => 'Approved',
                'disapproved', 'rejected' => 'Disapproved',
                default => ucwords(str_replace('_', ' ', $app->status))
            };

            return [
                'id' => $app->id,
                'record' => $app->protocol_code,
                'title' => $app->research_title,
                'status' => $statusText,
                'step' => 7,
                'date' => $app->created_at->format('F j, Y'),
                'reviewer' => $app->reviewer1_assigned ?? 'Secretariat',
                'studyType' => match ((int) $app->type_of_research) {
                    1 => 'Faculty Research', 2 => 'Graduate School Research', 3 => 'Undergraduate Research',
                    4 => 'Integrated School Student Research', 5 => 'External Research', default => 'Not Specified',
                },
                'comments' => $app->revision_remarks ?? '',
                'docs' => $docs,
            ];
        });

        // 9. BUILD REVISION HISTORY WITH DOCUMENTS ($revisionHistory)
        $revisionHistory = ResearchApplicationRevision::with('documents')
            ->whereIn('protocol_code', $protocolCodes)
            ->orderBy('revision_number', 'asc')
            ->get()
            ->map(function($rev) use ($decisionLetters) {
                $statusText = match(strtolower($rev->status)) {
                    'minor_revision', 'major_revision', 'resubmit' => 'Revision Required',
                    'approved' => 'Approved',
                    'rejected', 'disapproved' => 'Rejected',
                    default => 'Under Review'
                };

                $revDocs = [];

                $revDocs[] = [
                    'name' => 'Resubmission Form (System Generated)',
                    'file' => route('researcher.resubmission.print', ['id' => $rev->id]),
                    'size' => 'Auto',
                    'type' => 'pdf'
                ];

                $decisionKey = $rev->protocol_code . '-' . $rev->revision_number;
                if ($decisionLetters->has($decisionKey)) {
                    $revDocs[] = [
                        'name' => 'Decision Letter (System Generated)',
                        'file' => route('revision_decision.print', [
                            'protocol_code' => $rev->protocol_code,
                            'version' => $rev->revision_number
                        ]),
                        'size' => 'Auto',
                        'type' => 'pdf'
                    ];
                }

                if ($rev->documents) {
                    foreach($rev->documents as $doc) {
                        $secureUrl = '#';
                        $formattedSize = 'Unknown';

                        if ($doc->file_path) {
                            $parts = explode('/', trim($doc->file_path, '/'));
                            if (count($parts) >= 3) {
                                $secureUrl = route('view.document', [
                                    'protocol_code' => $parts[1],
                                    'filename'      => implode('/', array_slice($parts, 2))
                                ]);
                            }

                            $fullPath = storage_path("app/" . $doc->file_path);
                            if (File::exists($fullPath)) {
                                $bytes = File::size($fullPath);
                                $formattedSize = $bytes > 0 ? round($bytes / 1024, 2) . ' KB' : 'Unknown';
                                if ($bytes >= 1048576) $formattedSize = round($bytes / 1048576, 2) . ' MB';
                            }
                        }

                        $revDocs[] = [
                            'name' => $doc->description ?? 'Revision Document',
                            'file' => $secureUrl,
                            'size' => $formattedSize,
                            'type' => strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION))
                        ];
                    }
                }

                return [
                    'id' => 'RESUB-' . str_pad($rev->id, 3, '0', STR_PAD_LEFT),
                    'record' => $rev->protocol_code,
                    'revision_number' => $rev->revision_number,
                    'status' => $statusText,
                    'date' => $rev->created_at->format('M d, Y'),
                    'docs' => $revDocs
                ];
            });

        $formattedRevisions = [];

        // Pass $applications instead of raw array so Blade can render the pagination links
        return view('secretariat.history', compact('user', 'applications', 'formattedApps', 'formattedRevisions', 'revisionHistory', 'counts'));
    }
}
