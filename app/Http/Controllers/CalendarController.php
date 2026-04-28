<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Maps the database status to the frontend 'type' and 'location' tags.
     */
    private function getLocationAndType($status)
    {
        $status = strtolower($status);
        $secretariatStatuses = ['submitted', 'incomplete_documents', 'documents_checking', 'documents_complete', 'review_finished', 'assessment_processed', 'drafting_decision', 'processing_assessment'];
        $reviewerStatuses = ['awaiting_reviewer_approval', 'under_review', 'under review'];
        $chairStatuses = ['exempted_awaiting_chair_approval', 'awaiting_approval', 'awaiting_chair_approval_decision', 'awaiting_chair_approval'];
        $applicantStatuses = ['resubmit', 'returned_for_revision', 'minor_revision', 'major_revision'];

        if (in_array($status, $secretariatStatuses)) {
            return ['location' => 'secretariat', 'type' => 'secretariat'];
        } elseif (in_array($status, $reviewerStatuses)) {
            return ['location' => 'reviewer', 'type' => 'reviewer'];
        } elseif (in_array($status, $chairStatuses)) {
            return ['location' => 'chair', 'type' => 'chair'];
        } elseif (in_array($status, $applicantStatuses)) {
            return ['location' => 'applicant', 'type' => 'applicant'];
        }

        return ['location' => 'secretariat', 'type' => 'secretariat'];
    }

    /**
     * Helper to build a comprehensive profile for the UI Modal.
     */
    private function getFullProtocolContext($protocol)
    {
        $status = strtolower($protocol->status);
        $protocolCode = $protocol->protocol_code;

        // 1. Logic for Workflow Actions
        $workflow = [];
        if (in_array($status, ['incomplete_documents', 'minor_revision', 'major_revision', 'resubmit', 'returned_for_revision'])) {
            $workflow[] = ['actor' => 'Applicant', 'action' => 'Action Required: Revisions', 'color' => 'orange'];
        }

        if (in_array($status, ['submitted', 'documents_checking', 'documents_complete', 'review_finished', 'assessment_processed', 'drafting_decision', 'processing_assessment'])) {
            $workflow[] = ['actor' => 'Secretariat', 'action' => 'Administrative Processing', 'color' => 'blue'];
        }

        if (in_array($status, ['exempted_awaiting_chair_approval', 'awaiting_approval', 'awaiting_chair_approval_decision', 'awaiting_chair_approval'])) {
            $workflow[] = ['actor' => 'Chair', 'action' => 'Pending Final Signature', 'color' => 'teal'];
        }

        // 2. Detailed Reviewer Progress
        $reviewerProgress = [];
        $reviewersList = DB::table('application_reviewer')
            ->join('reviewers', 'application_reviewer.reviewer_id', '=', 'reviewers.id')
            ->join('users', 'reviewers.user_id', '=', 'users.id')
            ->select('users.name', 'application_reviewer.status as invite_status', 'reviewers.id as rev_id')
            ->where('application_reviewer.protocol_code', $protocolCode)
            ->get();

        $assessment = DB::table('assessment_forms')->where('protocol_code', $protocolCode)->first();

        foreach ($reviewersList as $rev) {
            $state = 'Pending Invitation';
            $color = 'orange';
            $isDone = false;

            if ($rev->invite_status === 'Accepted') {
                $state = 'Invitation Accepted';
                $color = 'blue';

                if ($assessment) {
                    if ($assessment->reviewer_1_id == $rev->rev_id && $assessment->reviewer_1_done) $isDone = true;
                    if ($assessment->reviewer_2_id == $rev->rev_id && $assessment->reviewer_2_done) $isDone = true;
                    if ($assessment->reviewer_3_id == $rev->rev_id && $assessment->reviewer_3_done) $isDone = true;
                }
            } elseif ($rev->invite_status === 'Declined' || $rev->invite_status === 'Expired') {
                $state = $rev->invite_status;
                $color = 'red';
            }

            // Only add them to the Workflow Details list if they are NOT done
            if (!$isDone) {
                $reviewerProgress[] = ['actor' => 'Reviewer: ' . $rev->name, 'action' => $state, 'color' => $color];
            }
        }

        return [
            'workflow' => array_merge($workflow, $reviewerProgress),
            'metadata' => [
                'institution' => $protocol->institution ?? 'N/A',
                'study_type' => strtoupper(str_replace('_', ' ', $protocol->type_of_research ?? 'N/A')),
                'classification' => strtoupper($protocol->review_classification ?? 'EXPEDITED'),
                'submitted_on' => Carbon::parse($protocol->created_at)->format('M d, Y'),
                'last_update' => Carbon::parse($protocol->updated_at)->format('M d, Y'),
            ],
            'contact' => [
                'email' => $protocol->email ?? 'N/A',
                'phone' => $protocol->mobile_no ?? $protocol->tel_no ?? 'N/A'
            ]
        ];
    }

    // calendar controller for fetching all events to be displayed in the calendar view, including fixed events like meetings and cut-off dates, as well as dynamic events based on the status changes of research applications and their revisions. It categorizes events by their relevance to different user roles (secretariat, chair, reviewer, applicant) and compiles detailed context for each protocol to be used in the frontend modals.
    // this is an api call that the calendar frontend will call to get all events for a given month and year, and it returns a structured JSON response that the frontend can use to populate the calendar with event markers and details.
    // this is used on both chair and secretariat calendar page
    public function getEvents(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('n'));
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $events = [];
        $terminalStatuses = ['approved', 'completed', 'accepted', 'rejected'];

        // --- 1. FIXED EVENTS ---
        $meetingDate = Carbon::create($year, $month, 1)->nthOfMonth(2, Carbon::WEDNESDAY);
        if ($meetingDate->month == $month) {
            $events[$meetingDate->day][] = ['type' => 'meeting', 'appId' => null, 'label' => 'BERC Review Meeting', 'applicant' => null, 'location' => null, 'details' => [], 'notes' => 'Full Board protocols discussion.'];
        }
        if ($startDate->daysInMonth >= 15) {
            $events[15][] = ['type' => 'cutoff', 'appId' => null, 'label' => 'Application Cut-off Date', 'applicant' => null, 'location' => null, 'details' => [], 'notes' => 'Docs must be complete for meeting inclusion.'];
        }

        // --- 2A. INITIAL SUBMISSIONS ---
        $initialApplications = DB::table('research_applications')
            ->whereNotIn('status', $terminalStatuses)
            ->get();

        foreach ($initialApplications as $app) {
            $updatedDate = Carbon::parse($app->updated_at);
            $classification = strtolower($app->review_classification ?? 'expedited');
            $fullDetails = $this->getFullProtocolContext($app);
            $locType = $this->getLocationAndType($app->status);

            // A. Status Marker (Only if it happened this month)
            if ($updatedDate->between($startDate, $endDate)) {
                $events[$updatedDate->day][] = [
                    'type' => $locType['type'], 'appId' => $app->protocol_code, 'label' => $app->research_title,
                    'applicant' => $app->name_of_researcher, 'location' => $locType['location'],
                    'notes' => "Current status: " . strtoupper(str_replace('_', ' ', $app->status)),
                    'details' => $fullDetails
                ];
            }

            // B. Administrative Deadlines (Chair & Secretariat)
            $appDeadlineDays = 0;
            $appDeadlineLabel = '';
            $appDeadlineLoc = 'secretariat';
            $appDeadlineType = 'deadline';

            switch ($app->status) {
                case 'submitted':
                case 'incomplete_documents':
                    $appDeadlineDays = 1; $appDeadlineLabel = "SCREENING DEADLINE"; break;
                case 'documents_checking':
                case 'documents_complete':
                    $appDeadlineDays = 1; $appDeadlineLabel = "ASSIGNMENT DEADLINE"; break;
                case 'review_finished':
                case 'assessment_processed':
                case 'drafting_decision':
                    $appDeadlineDays = 1; $appDeadlineLabel = "DECISION DRAFTING DEADLINE"; break;
                case 'exempted_awaiting_chair_approval':
                case 'awaiting_approval':
                case 'awaiting_chair_approval_decision':
                case 'awaiting_chair_approval':
                    $appDeadlineDays = 1; $appDeadlineLabel = "CHAIR APPROVAL DEADLINE"; $appDeadlineLoc = 'chair'; break;
            }

            if ($appDeadlineDays > 0) {
                $appDeadlineDate = $updatedDate->copy()->addDays($appDeadlineDays);
                if ($appDeadlineDate->between($startDate, $endDate)) {
                    $events[$appDeadlineDate->day][] = [
                        'type' => $appDeadlineType, 'appId' => $app->protocol_code, 'label' => $appDeadlineLabel,
                        'applicant' => $app->name_of_researcher, 'location' => $appDeadlineLoc,
                        'notes' => "Administrative Action Required", 'details' => $fullDetails
                    ];
                }
            }

            // C. Reviewer Deadlines
            $reviewers = DB::table('application_reviewer')
                ->join('reviewers', 'application_reviewer.reviewer_id', '=', 'reviewers.id')
                ->join('users', 'reviewers.user_id', '=', 'users.id')
                ->select('users.name', 'application_reviewer.status', 'application_reviewer.date_assigned', 'application_reviewer.date_accepted', 'reviewers.id as rev_id')
                ->where('protocol_code', $app->protocol_code)
                ->get();

            $assessment = DB::table('assessment_forms')->where('protocol_code', $app->protocol_code)->first();

            foreach ($reviewers as $rev) {
                // Hide if reviewer is done
                if ($rev->status === 'Accepted' && $assessment) {
                    $isDone = false;
                    if ($assessment->reviewer_1_id == $rev->rev_id && $assessment->reviewer_1_done) $isDone = true;
                    if ($assessment->reviewer_2_id == $rev->rev_id && $assessment->reviewer_2_done) $isDone = true;
                    if ($assessment->reviewer_3_id == $rev->rev_id && $assessment->reviewer_3_done) $isDone = true;
                    if ($isDone) continue;
                }

                $deadlineDate = null;
                if ($rev->status === 'Pending' && $rev->date_assigned) {
                    $deadlineDate = Carbon::parse($rev->date_assigned)->addDay();
                    $lbl = "INVITATION DEADLINE: " . $rev->name;
                } elseif ($rev->status === 'Accepted' && $rev->date_accepted) {
                    $days = ($classification === 'full board' || $classification === 'full_board') ? 20 : 10;
                    $deadlineDate = Carbon::parse($rev->date_accepted)->addDays($days);
                    $lbl = "ASSESSMENT DEADLINE: " . $rev->name;
                }

                if ($deadlineDate && $deadlineDate->between($startDate, $endDate)) {
                    $events[$deadlineDate->day][] = [
                        'type' => 'deadline', 'appId' => $app->protocol_code, 'label' => $lbl,
                        'applicant' => $app->name_of_researcher, 'location' => 'reviewer',
                        'notes' => "Action required by reviewer.", 'details' => $fullDetails
                    ];
                }
            }
        }

        // --- 2B. RESUBMISSIONS (Latest Active Revision) ---
        $latestRevisionsSubquery = DB::table('research_application_revisions')
            ->select('protocol_code', DB::raw('MAX(revision_number) as max_rev'))
            ->groupBy('protocol_code');

        $revisions = DB::table('research_application_revisions as rev')
            ->joinSub($latestRevisionsSubquery, 'latest', function ($join) {
                $join->on('rev.protocol_code', '=', 'latest.protocol_code')
                     ->on('rev.revision_number', '=', 'latest.max_rev');
            })
            ->join('research_applications as app', 'rev.protocol_code', '=', 'app.protocol_code')
            ->select('app.*', 'rev.status as rev_status', 'rev.revision_number', 'rev.updated_at as rev_updated_at')
            ->whereNotIn('rev.status', $terminalStatuses)
            ->get();

        foreach ($revisions as $rev) {
            $rev->status = $rev->rev_status;
            $updatedDate = Carbon::parse($rev->rev_updated_at);
            $classification = strtolower($rev->review_classification ?? 'expedited');
            $fullDetails = $this->getFullProtocolContext($rev);
            $locType = $this->getLocationAndType($rev->status);

            // A. Revision Status Marker
            if ($updatedDate->between($startDate, $endDate)) {
                $events[$updatedDate->day][] = [
                    'type' => $locType['type'], 'appId' => $rev->protocol_code, 'label' => "[RESUB] " . $rev->research_title,
                    'applicant' => $rev->name_of_researcher, 'location' => $locType['location'],
                    'notes' => "Revision #{$rev->revision_number}: " . strtoupper($rev->status),
                    'details' => $fullDetails
                ];
            }

            // B. Revision Administrative Deadlines
            $revDeadlineDays = 0;
            $revDeadlineLabel = '';
            $revDeadlineLoc = 'secretariat';

            switch ($rev->status) {
                case 'submitted':
                    $revDeadlineDays = 1; $revDeadlineLabel = "REV SCREENING DEADLINE"; break;
                case 'review_finished':
                case 'processing_assessment':
                case 'assessment_processed':
                case 'drafting_decision':
                    $revDeadlineDays = 2; $revDeadlineLabel = "REV DRAFTING DEADLINE"; break;
                case 'awaiting_chair_approval':
                    $revDeadlineDays = 1; $revDeadlineLabel = "CHAIR APPROVAL DEADLINE"; $revDeadlineLoc = 'chair'; break;
            }

            if ($revDeadlineDays > 0) {
                $revDeadlineDate = $updatedDate->copy()->addDays($revDeadlineDays);
                if ($revDeadlineDate->between($startDate, $endDate)) {
                    $events[$revDeadlineDate->day][] = [
                        'type' => 'deadline', 'appId' => $rev->protocol_code, 'label' => $revDeadlineLabel,
                        'applicant' => $rev->name_of_researcher, 'location' => $revDeadlineLoc,
                        'notes' => "Revision Administrative Action", 'details' => $fullDetails
                    ];
                }
            }

            // C. Revision Reviewer Deadlines
            $revReviewers = DB::table('application_reviewer')
                ->join('reviewers', 'application_reviewer.reviewer_id', '=', 'reviewers.id')
                ->join('users', 'reviewers.user_id', '=', 'users.id')
                ->select('users.name', 'application_reviewer.status', 'application_reviewer.date_assigned', 'application_reviewer.date_accepted', 'reviewers.id as rev_id')
                ->where('protocol_code', $rev->protocol_code)
                ->get();

            $assessment = DB::table('assessment_forms')->where('protocol_code', $rev->protocol_code)->first();

            foreach ($revReviewers as $r) {
                if ($r->status === 'Accepted' && $assessment) {
                    $isDone = false;
                    if ($assessment->reviewer_1_id == $r->rev_id && $assessment->reviewer_1_done) $isDone = true;
                    if ($assessment->reviewer_2_id == $r->rev_id && $assessment->reviewer_2_done) $isDone = true;
                    if ($assessment->reviewer_3_id == $r->rev_id && $assessment->reviewer_3_done) $isDone = true;
                    if ($isDone) continue;
                }

                $deadlineDate = null;
                if ($r->status === 'Pending' && $r->date_assigned) {
                    $deadlineDate = Carbon::parse($r->date_assigned)->addDay();
                    $lbl = "REV INVITE DEADLINE: " . $r->name;
                } elseif ($r->status === 'Accepted' && $r->date_accepted) {
                    $days = ($classification === 'full board' || $classification === 'full_board') ? 20 : 10;
                    $deadlineDate = Carbon::parse($r->date_accepted)->addDays($days);
                    $lbl = "REV ASSESSMENT DEADLINE: " . $r->name;
                }

                if ($deadlineDate && $deadlineDate->between($startDate, $endDate)) {
                    $events[$deadlineDate->day][] = [
                        'type' => 'deadline', 'appId' => $rev->protocol_code, 'label' => $lbl,
                        'applicant' => $rev->name_of_researcher, 'location' => 'reviewer',
                        'notes' => "Revision deadline check.", 'details' => $fullDetails
                    ];
                }
            }
        }

        return response()->json($events);
    }

    // This is a separate endpoint to fetch only the events relevant to the currently logged-in reviewer, so that we can display a personalized calendar view for reviewers that only shows their assigned protocols and deadlines, without overwhelming them with irrelevant events. It uses similar logic to the main getEvents function but filters down to just the reviewer's own assignments and deadlines.
    // this is used on the reviewer calendar page
    // this is an api call that the reviewer calendar frontend will call to get all events relevant to the logged-in reviewer for a given month and year, and it returns a structured JSON response similar to getEvents but filtered for the reviewer's own protocols and deadlines.
    public function getReviewerEvents(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([]);
        }

        $reviewerProfile = DB::table('reviewers')
            ->where('user_id', $user->id)
            ->first();

        if (!$reviewerProfile) {
            return response()->json([]);
        }

        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $events = [];
        $terminalStatuses = ['approved', 'completed', 'accepted', 'rejected'];

        // --- 1. FIXED EVENTS ---
        $meetingDate = Carbon::create($year, $month, 1)->nthOfMonth(2, Carbon::WEDNESDAY);
        if ($meetingDate && $meetingDate->month == $month) {
            $events[$meetingDate->day][] = [
                'type' => 'meeting',
                'appId' => null,
                'label' => 'BERC Review Meeting',
                'applicant' => null,
                'location' => null,
                'details' => [],
                'notes' => 'Full Board protocols discussion.'
            ];
        }

        if ($startDate->daysInMonth >= 15) {
            $events[15][] = [
                'type' => 'cutoff',
                'appId' => null,
                'label' => 'Application Cut-off Date',
                'applicant' => null,
                'location' => null,
                'details' => [],
                'notes' => 'Docs must be complete for meeting inclusion.'
            ];
        }

        // --- 2A. INITIAL SUBMISSIONS: ONLY THIS REVIEWER'S ASSIGNED PROTOCOLS ---
        $assignedProtocols = DB::table('application_reviewer')
            ->join('research_applications as app', 'application_reviewer.protocol_code', '=', 'app.protocol_code')
            ->select(
                'app.*',
                'application_reviewer.status as invite_status',
                'application_reviewer.date_assigned',
                'application_reviewer.date_accepted',
                'application_reviewer.reviewer_id'
            )
            ->where('application_reviewer.reviewer_id', $reviewerProfile->id)
            ->whereNotIn('app.status', $terminalStatuses)
            ->distinct()
            ->get();

        foreach ($assignedProtocols as $app) {
            $classification = strtolower(trim($app->review_classification ?? 'expedited'));
            $inviteStatus = strtolower(trim($app->invite_status ?? ''));
            $fullDetails = $this->getFullProtocolContext($app);

            $assessment = DB::table('assessment_forms')
                ->where('protocol_code', $app->protocol_code)
                ->first();

            // Hide if this reviewer already finished
            $isDone = false;
            if ($inviteStatus === 'accepted' && $assessment) {
                if ((int) $assessment->reviewer_1_id === (int) $reviewerProfile->id && $assessment->reviewer_1_done) $isDone = true;
                if ((int) $assessment->reviewer_2_id === (int) $reviewerProfile->id && $assessment->reviewer_2_done) $isDone = true;
                if ((int) $assessment->reviewer_3_id === (int) $reviewerProfile->id && $assessment->reviewer_3_done) $isDone = true;
            }

            if ($isDone) {
                continue;
            }

            // Optional status marker for reviewer's own protocols
            $updatedDate = Carbon::parse($app->updated_at);
            if ($updatedDate->gte($startDate) && $updatedDate->lte($endDate)) {
                $events[$updatedDate->day][] = [
                    'type' => ($inviteStatus === 'accepted' ? 'reviewer' : 'assigned'),
                    'appId' => $app->protocol_code,
                    'label' => $app->research_title,
                    'applicant' => $app->name_of_researcher,
                    'location' => ($inviteStatus === 'accepted' ? 'reviewer' : 'assigned'),
                    'notes' => 'Current status: ' . strtoupper(str_replace('_', ' ', $app->status)),
                    'details' => $fullDetails
                ];
            }

            // Reviewer's own deadline only
            $deadlineDate = null;
            $label = null;

            if ($inviteStatus === 'pending' && !empty($app->date_assigned)) {
                $deadlineDate = Carbon::parse($app->date_assigned)->addDay();
                $label = 'YOUR INVITATION DEADLINE';
            } elseif ($inviteStatus === 'accepted' && !empty($app->date_accepted)) {
                $days = in_array($classification, ['full board', 'full_board']) ? 20 : 10;
                $deadlineDate = Carbon::parse($app->date_accepted)->addDays($days);
                $label = 'YOUR ASSESSMENT DEADLINE';
            }

            if ($deadlineDate && $deadlineDate->gte($startDate) && $deadlineDate->lte($endDate)) {
                $events[$deadlineDate->day][] = [
                    'type' => 'deadline',
                    'appId' => $app->protocol_code,
                    'label' => $label,
                    'applicant' => $app->name_of_researcher,
                    'location' => 'reviewer',
                    'notes' => $app->research_title,
                    'details' => $fullDetails
                ];
            }
        }

        // --- 2B. RESUBMISSIONS: ONLY THIS REVIEWER'S ASSIGNED PROTOCOLS ---
        $latestRevisionsSubquery = DB::table('research_application_revisions')
            ->select('protocol_code', DB::raw('MAX(revision_number) as max_rev'))
            ->groupBy('protocol_code');

        $assignedRevisions = DB::table('application_reviewer')
            ->join('research_applications as app', 'application_reviewer.protocol_code', '=', 'app.protocol_code')
            ->join('research_application_revisions as rev', 'application_reviewer.protocol_code', '=', 'rev.protocol_code')
            ->joinSub($latestRevisionsSubquery, 'latest', function ($join) {
                $join->on('rev.protocol_code', '=', 'latest.protocol_code')
                    ->on('rev.revision_number', '=', 'latest.max_rev');
            })
            ->select(
                'app.*',
                'rev.status as rev_status',
                'rev.revision_number',
                'rev.updated_at as rev_updated_at',
                'application_reviewer.status as invite_status',
                'application_reviewer.date_assigned',
                'application_reviewer.date_accepted',
                'application_reviewer.reviewer_id'
            )
            ->where('application_reviewer.reviewer_id', $reviewerProfile->id)
            ->whereNotIn('rev.status', $terminalStatuses)
            ->distinct()
            ->get();

        foreach ($assignedRevisions as $rev) {
            $rev->status = $rev->rev_status;

            $classification = strtolower(trim($rev->review_classification ?? 'expedited'));
            $inviteStatus = strtolower(trim($rev->invite_status ?? ''));
            $fullDetails = $this->getFullProtocolContext($rev);

            $assessment = DB::table('assessment_forms')
                ->where('protocol_code', $rev->protocol_code)
                ->first();

            // Hide if this reviewer already finished
            $isDone = false;
            if ($inviteStatus === 'accepted' && $assessment) {
                if ((int) $assessment->reviewer_1_id === (int) $reviewerProfile->id && $assessment->reviewer_1_done) $isDone = true;
                if ((int) $assessment->reviewer_2_id === (int) $reviewerProfile->id && $assessment->reviewer_2_done) $isDone = true;
                if ((int) $assessment->reviewer_3_id === (int) $reviewerProfile->id && $assessment->reviewer_3_done) $isDone = true;
            }

            if ($isDone) {
                continue;
            }

            // Optional revision status marker
            $updatedDate = Carbon::parse($rev->rev_updated_at);
            if ($updatedDate->gte($startDate) && $updatedDate->lte($endDate)) {
                $events[$updatedDate->day][] = [
                    'type' => ($inviteStatus === 'accepted' ? 'reviewer' : 'assigned'),
                    'appId' => $rev->protocol_code,
                    'label' => '[RESUBMISSION] ' . $rev->research_title,
                    'applicant' => $rev->name_of_researcher,
                    'location' => ($inviteStatus === 'accepted' ? 'reviewer' : 'assigned'),
                    'notes' => 'Revision #' . $rev->revision_number . ': ' . strtoupper(str_replace('_', ' ', $rev->status)),
                    'details' => $fullDetails
                ];
            }

            // Reviewer's own revision deadline only
            $deadlineDate = null;
            $label = null;

            if ($inviteStatus === 'pending' && !empty($rev->date_assigned)) {
                $deadlineDate = Carbon::parse($rev->date_assigned)->addDay();
                $label = 'YOUR REVISION INVITATION DEADLINE';
            } elseif ($inviteStatus === 'accepted' && !empty($rev->date_accepted)) {
                $days = in_array($classification, ['full board', 'full_board']) ? 20 : 10;
                $deadlineDate = Carbon::parse($rev->date_accepted)->addDays($days);
                $label = 'YOUR REVISION ASSESSMENT DEADLINE';
            }

            if ($deadlineDate && $deadlineDate->gte($startDate) && $deadlineDate->lte($endDate)) {
                $events[$deadlineDate->day][] = [
                    'type' => 'deadline',
                    'appId' => $rev->protocol_code,
                    'label' => $label,
                    'applicant' => $rev->name_of_researcher,
                    'location' => 'reviewer',
                    'notes' => '[RESUBMISSION] ' . $rev->research_title,
                    'details' => $fullDetails
                ];
            }
        }

        return response()->json($events);
    }
}
