<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ResearchApplications;
use App\Models\ResearchApplicationRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class SecStaffController extends Controller
{
    //this function shows the list of submitted applications for the secretariat staff, it retrieves the authenticated user's information, fetches the submitted applications with their associated logs, extracts the most recent comment from the logs for each application, and passes the data to the applications view for display.
    public function showSecStaffApplications()
    {
        $user = Auth::user();

        // 1. Get the baseline applications
        $applications = $this->getSubmittedApplications();

        // 2. Eager load the 'logs' relationship to prevent database performance issues
        $applications->load('logs');

        // 3. Map through the applications to grab the most recent comment
        $applications->transform(function ($app) {
            // Find the newest log entry for this application that actually has a comment
            $latestLog = $app->logs->whereNotNull('comment')->sortByDesc('created_at')->first();

            // Attach it as a new property
            $app->latest_comment = $latestLog ? $latestLog->comment : null;

            return $app;
        });

        return view('secstaff.applications', compact('user', 'applications'));
    }

    //this is a helper function that retrieves the application data for a given protocol code when secretarial staff clicks a protocol to show it's information inside the modal
    public function getApplicationData($protocol_code)
    {
        $app = ResearchApplications::with(['payment'])
            ->where('protocol_code', $protocol_code)
            ->firstOrFail();

        // Fetch Basic Requirements
        $basicDocs = DB::table('basic_requirements')
            ->where('protocol_code', $protocol_code)
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch Supplementary Documents
        $suppDocs = DB::table('supplementary_documents')
            ->where('protocol_code', $protocol_code)
            ->orderBy('created_at', 'desc')
            ->get();

        // Organize documents by Type to easily check if a category exists
        $documentGroups = [];
        foreach ($basicDocs as $doc) {
            if (!isset($documentGroups[$doc->type])) {
                $documentGroups[$doc->type] = [];
            }
            $documentGroups[$doc->type][] = [
                'id' => $doc->id,
                'url' => route('view.document', ['protocol_code' => $protocol_code, 'filename' => str_replace('documents/'.$protocol_code.'/', '', $doc->file_path)]),
                'description' => $doc->description
            ];
        }
        foreach ($suppDocs as $doc) {
            if (!isset($documentGroups[$doc->type])) {
                $documentGroups[$doc->type] = [];
            }
            $documentGroups[$doc->type][] = [
                'id' => $doc->id,
                'url' => route('view.document', ['protocol_code' => $protocol_code, 'filename' => str_replace('documents/'.$protocol_code.'/', '', $doc->file_path)]),
                'description' => $doc->description
            ];
        }

        return response()->json([
            'protocol_code' => $app->protocol_code,
            'research_title' => $app->research_title,
            'name_of_researcher' => $app->name_of_researcher,
            'payment' => $app->payment ? [
                'payment_method' => $app->payment->payment_method,
                'reference_number' => $app->payment->reference_number,
                'proof_url' => $app->payment->proof_of_payment_path ? route('view.document', ['protocol_code' => $protocol_code, 'filename' => str_replace('documents/'.$protocol_code.'/', '', $app->payment->proof_of_payment_path)]) : null
            ] : null,
            'documents' => $documentGroups // Now sending the organized groups
        ]);
    }

    //this function shows the calendar view for the secretarial staff, it retrieves the authenticated user's information and passes it to the calendar view for display.
    public function showSecStaffCalendar()
    {
        $user = Auth::user();
        return view('secstaff.calendar', compact('user'));
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

        $applications = ResearchApplications::with('supplementaryDocuments')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. FETCH ALL REVISIONS FOR THESE APPLICATIONS (For the Version Hub Modal)
        // We pluck the protocol codes from the filtered list to optimize the query
        $protocolCodes = $applications->pluck('protocol_code');

        $allRevisions = ResearchApplicationRevision::with('documents')
            ->whereIn('protocol_code', $protocolCodes)
            ->orderBy('revision_number', 'asc')
            ->get()
            ->groupBy('protocol_code');

        // 3. FORMAT MAIN APPLICATIONS ($formattedApps)
        $formattedApps = $applications->map(function($app) use ($allRevisions) {
            $docs = [];

            $docs[] = [
                'name' => 'Incoming Communications Logbook',
                // We use the route name defined above and pass the protocol_code
                'file' => route('incoming.logbook.print', ['protocol_code' => $app->protocol_code]),
                'size' => 'Auto', // Since it's generated on the fly
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

            // 1. Fetch the assessment records using first() to ensure we get objects, not builders
            $assessment = DB::table('assessment_forms')
                ->where('protocol_code', $app->protocol_code)
                ->first();

            $icfAssessment = DB::table('icf_assessments')
                ->where('protocol_code', $app->protocol_code)
                ->first();

            // 2. Loop through the 3 possible reviewer slots
            for ($i = 1; $i <= 3; $i++) {
                $idField   = "reviewer_{$i}_id";
                $doneField = "reviewer_{$i}_done";

                $icfDoneField = "reviwer_{$i}_done";

                // ─── PROCESS GENERAL ASSESSMENT FORM ───
                if ($assessment && !empty($assessment->$idField)) {
                // 1. Query the 'reviewers' table and join 'users' to get the actual name
                $reviewerData = DB::table('reviewers')
                    ->join('users', 'reviewers.user_id', '=', 'users.id')
                    ->where('reviewers.id', $assessment->$idField) // Match form slot to reviewers.id
                    ->select('users.name')
                    ->first();

                // 2. Set the name, falling back to a generic label if not found
                $reviewerName = $reviewerData ? $reviewerData->name : "Reviewer $i";

                $isDone = in_array($assessment->$doneField, ['resolved', '1', 'true', 'Completed']);

                $docs[] = [
                    'name' => "Assessment Form - $reviewerName ",
                    'file' => route('assessment.individual.print', [
                        'id'          => $app->id,
                        'reviewer_id' => $assessment->$idField // Pass the ID from the form
                    ]),
                    'size' => 'Auto',
                    'type' => 'pdf'
                ];
            }

                // ─── PROCESS ICF ASSESSMENT FORM ───
                if ($icfAssessment && !empty($icfAssessment->$idField)) {
                    // 1. Join reviewers with users to get the name from the user_id link
                    $reviewerData = DB::table('reviewers')
                        ->join('users', 'reviewers.user_id', '=', 'users.id')
                        ->where('reviewers.id', $icfAssessment->$idField) // Match form slot to reviewers.id
                        ->select('users.name')
                        ->first();

                    // 2. Set the name, falling back to a generic label if the join fails
                    $reviewerName = $reviewerData ? $reviewerData->name : "Reviewer $i";

                    // 3. Check status using the specific typo column 'reviwer_X_done' from your schema
                    $icfDoneField = "reviwer_{$i}_done";
                    $isIcfDone = in_array($icfAssessment->$icfDoneField, ['resolved', '1', 'true', 'Completed']);

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

            /**
             * SECURE STORAGE HELPER
             */
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

            // --- DYNAMIC DOCUMENTS LOOPS ---
            $documentDictionary = [
                // Basic Requirements
                'letter_request'            => 'Letter of Request',
                'endorsement_letter'        => 'Endorsement Letter',
                'full_proposal'             => 'Full Proposal',
                'technical_review_approval' => 'Technical Review Approval',
                'informed_consent'          => 'Informed Consent Form',

                // Supplementary Documents
                'manuscript'                => 'Manuscript',
                'curriculum_vitae'          => 'Curriculum Vitae',
                'questionnaire'             => 'Questionnaire / Data Gathering Tool',
                'data_collection'           => 'Data Collection Procedures',
                'product_brochure'          => 'Product Brochure',
                'philippine_fda'            => 'Philippine FDA Approval',
                'special_populations'       => 'Special Populations',
                'others'                    => 'Other Document'
            ];

            // 1. Fetch and attach Basic Requirements
            $basicDocs = DB::table('basic_requirements')
                ->where('protocol_code', $app->protocol_code)
                ->get();

            foreach ($basicDocs as $doc) {
                $baseLabel = $documentDictionary[$doc->type] ?? ucwords(str_replace('_', ' ', $doc->type));

                $displayTitle = !empty($doc->description) && $doc->description !== $baseLabel
                                ? "{$baseLabel} - {$doc->description}"
                                : $baseLabel;

                $addDoc($doc->file_path, $displayTitle);
            }

            // 2. Fetch and attach Supplementary Documents
            $suppDocs = DB::table('supplementary_documents')
                ->where('protocol_code', $app->protocol_code)
                ->get();

            foreach ($suppDocs as $doc) {
                $baseLabel = $documentDictionary[$doc->type] ?? ucwords(str_replace('_', ' ', $doc->type));

                $displayTitle = !empty($doc->description) && $doc->description !== $baseLabel
                                ? "{$baseLabel} - {$doc->description}"
                                : $baseLabel;

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

            // Standardize the visual text for terminal states
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
                'step' => 7, // History items always show a fully completed progress bar
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

        // 1. Fetch all revision decision letters for the relevant protocols upfront to optimize performance
        $decisionLetters = DB::table('revision_decision_letters')
            ->whereIn('protocol_code', $protocolCodes)
            ->get()
            ->groupBy(function($item) {
                // Group by a unique key: protocol_code + version_number
                return $item->protocol_code . '-' . $item->version_number;
            });

        // 2. BUILD REVISION HISTORY WITH DOCUMENTS ($revisionHistory)
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

                // Process Revision Documents
                $revDocs = [];

                $revDocs[] = [
                    'name' => 'Resubmission Form (System Generated)',
                    'file' => route('researcher.resubmission.print', ['id' => $rev->id]),
                    'size' => 'Auto',
                    'type' => 'pdf'
                ];

                // Check if a decision letter exists for this specific protocol AND version
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
                    'docs' => $revDocs // Send the documents to the JavaScript
                ];
            });

        $formattedRevisions = [];

        return view('secstaff.history', compact('user', 'formattedApps', 'formattedRevisions', 'revisionHistory'));
    }

    //this function shows the payment settings view for the secretariat staff, it retrieves the authenticated user's information and passes it to the payment settings view for display.
    public function showPaymentSettings()
    {
        $user = Auth::user();
        return view('secstaff.payment_settings', compact('user'));
    }

    //this is a helper function that retrieves the application data for a given protocol code when secretarial staff clicks a protocol to show it's information inside the modal
    private function getSubmittedApplications()
    {
        return ResearchApplications::where('status', 'submitted')
            ->with(['user', 'payment'])
            ->latest()
            ->get();
    }
}
