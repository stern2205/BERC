<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\ResearchApplications;
use App\Models\ResearchApplicationRevision;
use App\Models\RevisionResponse;
use App\Models\Reviewer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //shows dashboard for each role and the KPIs and analytics data necessary
    public function index()
    {
        $user = auth()->user();
        $role = strtolower(str_replace('-', '', $user->role));

        if ($role === 'chair') {
            $now = Carbon::now();

            // 1. Calculate the Top KPI Cards
            $kpi = [
                'newToday' => ResearchApplications::whereDate('created_at', $now->toDateString())->count(),
                'totalMonth' => ResearchApplications::whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->count(),
                'notScreened' => ResearchApplications::whereIn('status', ['awaiting_chair_approval', 'exempted_awaiting_chair_approval'])->count(),
                'totalApps' => ResearchApplications::count(),
            ];

            // 2. Fetch data for Chart Analytics
            $applications = ResearchApplications::withCount('revisions')->get();

            $oldestApp = ResearchApplications::select('created_at')->orderBy('created_at', 'asc')->first();
            $startYear = $oldestApp ? $oldestApp->created_at->year : $now->year;
            $years = range($startYear, $now->year);

            $chartData = [
                'status' => ['byYear' => ['all' => [0, 0, 0, 0, 0]]],
                'appMonth' => ['all' => array_fill(0, 12, 0)],
                'appYear' => [
                    'years' => $years,
                    'totals' => array_fill(0, count($years), 0)
                ],
                'typeStudy' => ['byYear' => ['all' => array_fill(0, 8, 0)]],
                'avgRevYear' => [
                    'years' => $years,
                    'averages' => array_fill(0, count($years), 0)
                ]
            ];

            // Pre-fill zero arrays for the filtered years
            foreach ($years as $year) {
                $chartData['status']['byYear'][$year] = [0, 0, 0, 0, 0];
                $chartData['appMonth'][$year] = array_fill(0, 12, 0);
                $chartData['typeStudy']['byYear'][$year] = array_fill(0, 8, 0);
            }

            $revTracker = array_fill_keys($years, ['apps' => 0, 'revisions' => 0]);

            foreach ($applications as $app) {
                $appYear = $app->created_at->year;
                $monthIdx = $app->created_at->month - 1;

                $statusIdx = match (true) {
                    in_array($app->status, ['submitted', 'incomplete_documents', 'documents_checking', 'documents_complete', 'awaiting_reviewer_approval']) => 0,
                    in_array($app->status, ['under_review', 'review_finished', 'processing_assessment', 'assessment_processed', 'drafting_decision', 'awaiting_chair_approval', 'exempted_awaiting_chair_approval', 'in_meeting']) => 1,
                    in_array($app->status, ['approved', 'completed']) => 2,
                    in_array($app->status, ['minor_revision', 'major_revision', 'resubmit', 'returned_for_revision', 'Revision Required']) => 3,
                    in_array($app->status, ['rejected', 'disapproved']) => 4,
                    default => 0,
                };

                $typeId = (int) $app->type_of_research;
                $typeIdx = ($typeId >= 1 && $typeId <= 7) ? $typeId - 1 : 7;

                $chartData['status']['byYear']['all'][$statusIdx]++;
                $chartData['appMonth']['all'][$monthIdx]++;
                $chartData['typeStudy']['byYear']['all'][$typeIdx]++;

                if (in_array($appYear, $years)) {
                    $chartData['status']['byYear'][$appYear][$statusIdx]++;
                    $chartData['appMonth'][$appYear][$monthIdx]++;
                    $chartData['typeStudy']['byYear'][$appYear][$typeIdx]++;

                    $yearIndex = array_search($appYear, $years);
                    $chartData['appYear']['totals'][$yearIndex]++;

                    $revTracker[$appYear]['apps']++;
                    $revTracker[$appYear]['revisions'] += $app->revisions_count;
                }
            }

            foreach ($years as $index => $year) {
                $totalApps = $revTracker[$year]['apps'];
                $totalRevs = $revTracker[$year]['revisions'];
                $chartData['avgRevYear']['averages'][$index] = $totalApps > 0 ? round($totalRevs / $totalApps, 1) : 0;
            }

            // 3. Reviewer Performance Analytics
            $reviewers = DB::table('reviewers')->where('is_active', true)->get();
            $performanceData = [];

            foreach ($reviewers as $rev) {
                $declinedCount = DB::table('application_reviewer')
                    ->where('reviewer_id', $rev->id)
                    ->where('status', 'Rejected')
                    ->count();

                $assessedCount = DB::table('application_reviewer')
                    ->where('reviewer_id', $rev->id)
                    ->where('status', 'Accepted')
                    ->count();

                // Calculate active months (minimum 1 to avoid division by zero)
                $createdAt = Carbon::parse($rev->created_at);
                $monthsActive = max(1, $createdAt->diffInMonths($now));

                // Treat 1 month as 2 cut-offs (15th and end of month standard)
                $cutoffsActive = max(1, $monthsActive * 2);
                $avgAssessedPerCutoff = round($assessedCount / $cutoffsActive, 1);

                $performanceData[] = [
                    'name'         => $rev->name,
                    'type'         => $rev->type,
                    'avg_time'     => $rev->avg_review_time_days,
                    'declined'     => $declinedCount,
                    'avg_assessed' => $avgAssessedPerCutoff
                ];
            }

            // Sort by most active reviewers first for a cleaner chart
            usort($performanceData, fn($a, $b) => $b['avg_assessed'] <=> $a['avg_assessed']);

            return view('chair.dashboard', compact('user', 'kpi', 'chartData', 'performanceData'));
        }

        if ($role === 'secstaff') {
            $now = Carbon::now();

            // ── Existing KPI logic (unchanged) ──
            $newToday = ResearchApplications::whereDate('created_at', Carbon::today())->count();

            $totalMonth = ResearchApplications::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            $notScreened = ResearchApplications::where('status', 'submitted')->count();

            $totalAllTime = ResearchApplications::count();

            // ── Analytics logic copied from secretariat ──
            $applications = ResearchApplications::withCount('revisions')->get();

            $oldestApp = ResearchApplications::select('created_at')->orderBy('created_at', 'asc')->first();
            $startYear = $oldestApp ? $oldestApp->created_at->year : $now->year;
            $years = range($startYear, $now->year);

            $chartData = [
                'status' => ['byYear' => ['all' => [0, 0, 0, 0, 0]]],
                'appMonth' => ['all' => array_fill(0, 12, 0)],
                'appYear' => [
                    'years' => $years,
                    'totals' => array_fill(0, count($years), 0)
                ],
                'typeStudy' => ['byYear' => ['all' => array_fill(0, 8, 0)]],
                'avgRevYear' => [
                    'years' => $years,
                    'averages' => array_fill(0, count($years), 0)
                ]
            ];

            foreach ($years as $year) {
                $chartData['status']['byYear'][$year] = [0, 0, 0, 0, 0];
                $chartData['appMonth'][$year] = array_fill(0, 12, 0);
                $chartData['typeStudy']['byYear'][$year] = array_fill(0, 8, 0);
            }

            $revTracker = array_fill_keys($years, ['apps' => 0, 'revisions' => 0]);

            foreach ($applications as $app) {
                $appYear = $app->created_at->year;
                $monthIdx = $app->created_at->month - 1;

                $statusIdx = match (true) {
                    in_array($app->status, ['submitted', 'incomplete_documents', 'documents_checking', 'documents_complete', 'awaiting_reviewer_approval']) => 0, // Pending
                    in_array($app->status, ['under_review', 'review_finished', 'processing_assessment', 'assessment_processed', 'drafting_decision', 'awaiting_chair_approval', 'exempted_awaiting_chair_approval', 'in_meeting']) => 1, // For Review
                    in_array($app->status, ['approved', 'completed']) => 2, // Approved
                    in_array($app->status, ['minor_revision', 'major_revision', 'resubmit', 'returned_for_revision', 'Revision Required']) => 3, // For Revision
                    in_array($app->status, ['rejected', 'disapproved']) => 4, // Rejected
                    default => 0,
                };

                $typeId = (int) $app->type_of_research;
                $typeIdx = ($typeId >= 1 && $typeId <= 7) ? $typeId - 1 : 7;

                // All-time
                $chartData['status']['byYear']['all'][$statusIdx]++;
                $chartData['appMonth']['all'][$monthIdx]++;
                $chartData['typeStudy']['byYear']['all'][$typeIdx]++;

                // Per-year
                if (in_array($appYear, $years)) {
                    $chartData['status']['byYear'][$appYear][$statusIdx]++;
                    $chartData['appMonth'][$appYear][$monthIdx]++;
                    $chartData['typeStudy']['byYear'][$appYear][$typeIdx]++;

                    $yearIndex = array_search($appYear, $years);
                    $chartData['appYear']['totals'][$yearIndex]++;

                    $revTracker[$appYear]['apps']++;
                    $revTracker[$appYear]['revisions'] += $app->revisions_count;
                }
            }

            foreach ($years as $index => $year) {
                $totalApps = $revTracker[$year]['apps'];
                $totalRevs = $revTracker[$year]['revisions'];
                $chartData['avgRevYear']['averages'][$index] = $totalApps > 0
                    ? round($totalRevs / $totalApps, 1)
                    : 0;
            }

            return view('secstaff.dashboard', compact(
                'newToday',
                'totalMonth',
                'notScreened',
                'totalAllTime',
                'chartData',
                'user'
            ));
        }

        if ($role === 'secretariat') {
            $now = Carbon::now();

            // 1. Secretariat Specific KPIs
            $kpi = [
                'newToday' => ResearchApplications::whereDate('created_at', $now->toDateString())->count(),
                'totalMonth' => ResearchApplications::whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->count(),
                'totalApps' => ResearchApplications::count(),

                // --- Secretariat Tasks: Original Applications ---
                'reviewClassification' => ResearchApplications::where('status', 'documents_checking')->count(),
                'assessmentForms' => ResearchApplications::whereIn('status', ['under_review', 'review_finished'])->count(),
                'decisionLetter' => ResearchApplications::whereIn('status', ['drafting_decision', 'Drafting Decision', 'assessment_processed', 'Assessment Processed'])->count(),

                // --- Secretariat Tasks: Revisions ---
                'resubmissionValidation' => ResearchApplicationRevision::where('status', 'submitted')->count(),
                'resubmissionForms' => ResearchApplicationRevision::where('status', 'review_finished')->count(),
                'revisionDecisionLetter' => ResearchApplicationRevision::where('status', 'assessment_processed')->count(),
            ];

            // 2. Fetch data for Chart Analytics
            $applications = ResearchApplications::withCount('revisions')->get();

            // --- Dynamic Years Logic ---
            $oldestApp = ResearchApplications::select('created_at')->orderBy('created_at', 'asc')->first();
            $startYear = $oldestApp ? $oldestApp->created_at->year : $now->year;
            $years = range($startYear, $now->year);

            $chartData = [
                'status' => ['byYear' => ['all' => [0, 0, 0, 0, 0]]],
                'appMonth' => ['all' => array_fill(0, 12, 0)],
                'appYear' => [
                    'years' => $years,
                    'totals' => array_fill(0, count($years), 0)
                ],
                'typeStudy' => ['byYear' => ['all' => array_fill(0, 8, 0)]],
                'avgRevYear' => [
                    'years' => $years,
                    'averages' => array_fill(0, count($years), 0)
                ]
            ];

            // Pre-fill zero arrays for the filtered years
            foreach ($years as $year) {
                $chartData['status']['byYear'][$year] = [0, 0, 0, 0, 0];
                $chartData['appMonth'][$year] = array_fill(0, 12, 0);
                $chartData['typeStudy']['byYear'][$year] = array_fill(0, 8, 0);
            }

            // Tracking arrays for the Averages calculation
            $revTracker = array_fill_keys($years, ['apps' => 0, 'revisions' => 0]);

            // Process the Applications
            foreach ($applications as $app) {
                $appYear = $app->created_at->year;
                $monthIdx = $app->created_at->month - 1;

                // --- Status Mapping ---
                $statusIdx = match (true) {
                    in_array($app->status, ['submitted', 'incomplete_documents', 'documents_checking', 'documents_complete', 'awaiting_reviewer_approval']) => 0, // Pending
                    in_array($app->status, ['under_review', 'review_finished', 'processing_assessment', 'assessment_processed', 'drafting_decision', 'awaiting_chair_approval', 'exempted_awaiting_chair_approval', 'in_meeting']) => 1, // For Review
                    in_array($app->status, ['approved', 'completed']) => 2, // Approved
                    in_array($app->status, ['minor_revision', 'major_revision', 'resubmit', 'returned_for_revision', 'Revision Required']) => 3, // For Revision
                    in_array($app->status, ['rejected', 'disapproved']) => 4, // Rejected
                    default => 0,
                };

                // --- Type of Study Mapping ---
                $typeId = (int) $app->type_of_research;
                $typeIdx = ($typeId >= 1 && $typeId <= 7) ? $typeId - 1 : 7; // Defaults to 'Others' at index 7

                // --- Apply to "All Time" ---
                $chartData['status']['byYear']['all'][$statusIdx]++;
                $chartData['appMonth']['all'][$monthIdx]++;
                $chartData['typeStudy']['byYear']['all'][$typeIdx]++;

                // --- Apply to Specific Year ---
                if (in_array($appYear, $years)) {
                    $chartData['status']['byYear'][$appYear][$statusIdx]++;
                    $chartData['appMonth'][$appYear][$monthIdx]++;
                    $chartData['typeStudy']['byYear'][$appYear][$typeIdx]++;

                    $yearIndex = array_search($appYear, $years);
                    $chartData['appYear']['totals'][$yearIndex]++;

                    // Track data for average revisions
                    $revTracker[$appYear]['apps']++;
                    $revTracker[$appYear]['revisions'] += $app->revisions_count;
                }
            }

            // 3. Finalize Average Revisions Math
            foreach ($years as $index => $year) {
                $totalApps = $revTracker[$year]['apps'];
                $totalRevs = $revTracker[$year]['revisions'];
                $chartData['avgRevYear']['averages'][$index] = $totalApps > 0 ? round($totalRevs / $totalApps, 1) : 0;
            }

            return view('secretariat.dashboard', compact('user', 'kpi', 'chartData'));
        }

        if (in_array($user->role, ['reviewer', 'extconsultant', 'External Consultant'])) {
            $user = auth()->user();

            // 1. Get the Reviewer profile for the logged-in user
            $reviewer = Reviewer::where('user_id', $user->id)->first();

            // Default KPI values
            $kpi = [
                'invitations' => 0,
                'assessments' => 0,
                'resubmissions' => 0,
            ];

            $activeProtocolCodes = [];

            if ($reviewer) {
                $reviewerId = $reviewer->id;

                // --- KPI: Pending Invitations ---
                $kpi['invitations'] = DB::table('application_reviewer')
                    ->where('reviewer_id', $reviewerId)
                    ->where('status', 'Pending')
                    ->count();

                // --- Find Pending Original Assessments for this Reviewer ---
                // Checks if their specific slot is NOT marked as 'Done'
                $pendingAssessmentProtocols = DB::table('assessment_forms')
                    ->where(function($query) use ($reviewerId) {
                        $query->where(function($q) use ($reviewerId) {
                            $q->where('reviewer_1_id', $reviewerId)
                            ->where(function($inner) {
                                $inner->whereNull('reviewer_1_done')->orWhere('reviewer_1_done', '!=', 'Done');
                            });
                        })->orWhere(function($q) use ($reviewerId) {
                            $q->where('reviewer_2_id', $reviewerId)
                            ->where(function($inner) {
                                $inner->whereNull('reviewer_2_done')->orWhere('reviewer_2_done', '!=', 'Done');
                            });
                        })->orWhere(function($q) use ($reviewerId) {
                            $q->where('reviewer_3_id', $reviewerId)
                            ->where(function($inner) {
                                $inner->whereNull('reviewer_3_done')->orWhere('reviewer_3_done', '!=', 'Done');
                            });
                        });
                    })->pluck('protocol_code')->toArray();

                // --- Find Pending Resubmissions for this Reviewer ---
                // Checks if their specific slot boolean is false
                $pendingRevisionProtocols = DB::table('revision_responses')
                    ->join('research_application_revisions', function ($join) {
                        $join->on('revision_responses.protocol_code', '=', 'research_application_revisions.protocol_code')
                            ->on('revision_responses.revision_number', '=', 'research_application_revisions.revision_number');
                    })
                    ->where('research_application_revisions.status', 'under_review')
                    ->where(function($query) use ($reviewerId) {
                        $query->where(function($q1) use ($reviewerId) {
                            $q1->where('revision_responses.reviewer1_id', $reviewerId)->where('revision_responses.reviewer1_done', false);
                        })->orWhere(function($q2) use ($reviewerId) {
                            $q2->where('revision_responses.reviewer2_id', $reviewerId)->where('revision_responses.reviewer2_done', false);
                        })->orWhere(function($q3) use ($reviewerId) {
                            $q3->where('revision_responses.reviewer3_id', $reviewerId)->where('revision_responses.reviewer3_done', false);
                        });
                    })->pluck('revision_responses.protocol_code')->toArray();

                // --- Calculate KPIs ---
                // We intersect with the protocols they've actually Accepted to be perfectly accurate
                $acceptedProtocols = DB::table('application_reviewer')
                    ->where('reviewer_id', $reviewerId)
                    ->where('status', 'Accepted')
                    ->pluck('protocol_code')->toArray();

                $kpi['assessments'] = count(array_intersect($pendingAssessmentProtocols, $acceptedProtocols));
                $kpi['resubmissions'] = count(array_intersect($pendingRevisionProtocols, $acceptedProtocols));

                // Combine all pending work into one master array of Active Protocols
                $activeProtocolCodes = array_unique(array_merge($pendingAssessmentProtocols, $pendingRevisionProtocols));
            }

            // 2. Fetch ALL assignments by joining application_reviewer -> reviewers -> users
            $assignments = DB::table('application_reviewer')
                ->join('reviewers', 'application_reviewer.reviewer_id', '=', 'reviewers.id')
                ->join('research_applications', 'application_reviewer.protocol_code', '=', 'research_applications.protocol_code')
                ->select(
                    'application_reviewer.*',
                    'research_applications.research_title',
                    'research_applications.review_classification',
                    'research_applications.name_of_researcher'
                )
                ->where('reviewers.user_id', $user->id)
                ->orderBy('application_reviewer.date_assigned', 'desc')
                ->get();

            // 3. Categorize for the dashboard cards
            $pendingInvitations = $assignments->where('status', 'Pending');

            // Active Reviews: Status must be Accepted AND the protocol must be in our pending work array
            $activeReviews = $assignments->filter(function($assignment) use ($activeProtocolCodes) {
                return $assignment->status === 'Accepted' && in_array($assignment->protocol_code, $activeProtocolCodes);
            });

            // History Reviews: Includes Declined, Expired, OR Completed (Accepted but they have finished the work)
            $historyReviews = $assignments->filter(function($assignment) use ($activeProtocolCodes) {
                if (in_array($assignment->status, ['Declined', 'Expired'])) {
                    return true;
                }
                // If it was Accepted but is no longer "Active", it means they finished reviewing it
                if ($assignment->status === 'Accepted' && !in_array($assignment->protocol_code, $activeProtocolCodes)) {
                    return true;
                }
                return false;
            });

            return view('reviewer.dashboard', compact(
                'user',
                'kpi',
                'assignments',
                'pendingInvitations',
                'activeReviews',
                'historyReviews'
            ));
        }

        return view('researcher.dashboard', compact('user'));
    }
}
