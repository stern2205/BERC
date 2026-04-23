<?php

namespace App\Http\Controllers;

use App\Models\ResearchApplications;
use App\Models\ResearchApplicationRevision;
use App\Models\RevisionResponse;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Reviewer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ChairController extends Controller
{
    // this shows the add staff page where the chair can manage their staff accounts, including creating new accounts for reviewers and secretarial staff, and viewing the status of existing staff members. The function retrieves all users except standard Researchers, eager loads their reviewer profiles and latest login logs for status display, formats the data for the frontend, and passes it to the view.
    public function showAddStaff()
    {
        $user = auth()->user();
        // Fetch users (excluding standard Researchers)
        // Eager load the reviewer profile and the MOST RECENT login log
        $users = User::with(['reviewer', 'loginLogs' => function($query) {
                $query->orderBy('logged_in_at', 'desc'); // Ensure we get the latest first
            }])
            ->where('role', '!=', 'Researcher')
            ->get();

        $staffData = $users->map(function($user) {
            // --- 1. RESOLVE ROLE & EXPERTISE ---

            // A. Map the raw database role to the exact Title Case string the UI expects
            $rawRole = strtolower($user->role ?: 'staff');
            $roleMapping = [
                'chair'       => 'Chair',
                'cochair'     => 'Co-Chair',
                'co-chair'    => 'Co-Chair',
                'secretariat' => 'Secretariat',
                'secstaff'    => 'Secretarial Staff',
                'extconsultant' => 'External Consultant'
            ];

            // If it exists in our map, use the pretty version. Otherwise, just capitalize the word.
            $role = $roleMapping[$rawRole] ?? ucwords($rawRole);
            $expertise = 'General';

            // B. Override with Reviewer specifics if they exist
            if ($user->reviewer) {
                // This is already saved properly (e.g., 'Panel Expert') due to your DB constraint
                $role = $user->reviewer->type;

                $panel = $user->reviewer->panel ? $user->reviewer->panel . ' - ' : '';
                $expertise = $panel . $user->reviewer->specialization;
                if (empty(trim($expertise))) {
                    $expertise = 'General';
                }
            }

            // --- 2. RESOLVE STATUS & LATEST LOG FROM login_logs ---
            $latestLog = $user->loginLogs->first(); // Gets the most recent log due to our orderBy clause

            $status = 'Inactive';
            $logType = 'No Activity';
            $logTimestamp = 'N/A';
            $logDateKey = '';

            if ($latestLog) {
                // If logged_out_at is NULL, the user is currently online/active
                if (is_null($latestLog->logged_out_at)) {
                    $status = 'Active';
                    $logType = 'Logged In';
                    $timestamp = Carbon::parse($latestLog->logged_in_at);
                }
                // If logged_out_at has a timestamp, the user is offline/inactive
                else {
                    $status = 'Inactive';
                    $logType = 'Logged Out';
                    $timestamp = Carbon::parse($latestLog->logged_out_at);
                }

                // Format for the Alpine.js UI
                $logTimestamp = $timestamp->format('h:i A | m/d/y');
                $logDateKey = $timestamp->format('Y-m-d');
            }

            // --- 3. RETURN MAPPED ARRAY ---
            return [
                'id'           => $user->id,
                'employeeId'   => 'STF-' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                'role'         => $role, // This is now perfectly formatted (e.g., "Secretariat")
                'name'         => $user->name,
                'email'        => $user->email,
                'profile_image' => $user->profile_image, // This is the raw DB path; the frontend will handle URL generation
                'phone'        => 'N/A',
                'expertise'    => $expertise,
                'status'       => $status,
                'logType'      => $logType,
                'logTimestamp' => $logTimestamp,
                'logDateKey'   => $logDateKey,
            ];
        });

        return view('chair.pages.add-staff', compact('user', 'staffData'));
    }

    //this function saves the staff to the database when the chair creates a new staff account in the add staff page. It validates the incoming data, creates a new user record, and if the role is a reviewer type, it also creates a corresponding reviewer profile. The function uses transactions to ensure data integrity and returns appropriate JSON responses for success or failure.
    public function storeStaff(Request $request)
    {
        // 1. Validate incoming data
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'role'      => 'required|string',
            'expertise' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 2. Identify if the role belongs to the Reviewers table
            $reviewerRoles = ['Panel Expert', 'Layperson', 'External Consultant'];
            $isReviewer = in_array($request->role, $reviewerRoles);

            // 3. Map the User table base role
            if ($isReviewer) {
                $baseRole = 'reviewer';
            } elseif ($request->role === 'Secretarial Staff') {
                $baseRole = 'secstaff';
            } else {
                // Converts 'Chair' -> 'chair', 'Co-Chair' -> 'co-chair', 'Secretariat' -> 'secretariat'
                $baseRole = strtolower($request->role);
            }

            // 4. Create the Core User Record
            $user = User::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => Hash::make('BatStateU123!'), // Default password
                'role'              => $baseRole, // Saved as lowercase mapping
                'is_email_verified' => true, // Assuming internal staff are auto-verified
                'is_first_login' => true, // Force password change on first login
            ]);

            // 5. Create Reviewer Profile if applicable
            if ($isReviewer) {
                $panel = null;
                $specialization = $request->expertise;

                // Parse the frontend string (e.g., "Panel I - Engineering | Testing")
                if (strpos($request->expertise, ' | ') !== false) {
                    $parts = explode(' | ', $request->expertise, 2);
                    $panel = trim($parts[0]);
                    $specialization = trim($parts[1]);
                } elseif (str_starts_with($request->expertise, 'Panel')) {
                    $panel = trim($request->expertise);
                    $specialization = null;
                }

                Reviewer::create([
                    'user_id'              => $user->id,
                    'name'                 => $user->name,
                    // Keeps exact case ('Panel Expert') to satisfy your DB CHECK constraint
                    'type'                 => $request->role,
                    'panel'                => $panel,
                    'specialization'       => $specialization,
                    'avg_review_time_days' => 0,
                    'is_active'            => true,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Staff account created successfully.',
                // Returning the generated ID so the frontend can assign it immediately
                'new_id'  => $user->id
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create account: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DESTROY: Delete a Staff Account
     */
    public function destroyStaff($id)
    {
        try {
            $user = User::findOrFail($id);

            // Security check: Prevent admins from deleting their own active session
            if (auth()->id() === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own logged-in account.'
                ], 403);
            }

            // Because your schema uses ON DELETE CASCADE for reviewers and login_logs,
            // deleting the User automatically wipes their reviewer profile and logs perfectly.
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Account permanently deleted.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account: ' . $e->getMessage()
            ], 500);
        }
    }

    //this shows the frontend calendar page for the chair where they can see the schedule of upcoming meetings and important dates related to their protocols. The function simply retrieves the authenticated user and passes it to the view, where the calendar will be rendered using Alpine.js and populated with relevant data.
    public function showChairCalendar(Request $request){
        $user = auth()->user();
        return view("chair.pages.calendar",compact("user"));
    }

    //This shows the for approval page of the chair where they can see the list of applications that require their approval, along with all the necessary details to make an informed decision, and the ability to open a modal to draft the decision letter. The function fetches all relevant applications, preloads related data for efficiency, formats it for the frontend, and passes it to the view.
    public function showDecisionApproval(Request $request)
    {
        $user = auth()->user();

        // 1. Fetch pending applications with assessment forms and items
        $rawApps = ResearchApplications::with([
                'assignedReviewers',
                'supplementaryDocuments',
                'informedConsent',
                'logs',
                'assessmentForm.items', // Load assessment items
            ])
            ->where(function($query) {
                $query->whereNotNull('external_consultant')
                    ->orWhere('status', 'exempted_awaiting_chair_approval')
                    ->orWhere('status', 'awaiting_approval');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $protocolCodes = $rawApps->pluck('protocol_code')->toArray();

        // Pre-fetch Decision Letters
        $decisionLetters = DB::table('decision_letters')
            ->whereIn('protocol_code', $protocolCodes)
            ->get()
            ->keyBy('protocol_code');

        // Pre-fetch Exemption Certificates
        $exemptionCertificates = DB::table('exemption_certificates')
            ->whereIn('protocol_code', $protocolCodes)
            ->get()
            ->keyBy('protocol_code');

        // Pre-fetch ICF Assessment Items (since it's a flat DB table lookup via protocol_code)
        $icfAssessments = DB::table('icf_assessments')
            ->whereIn('protocol_code', $protocolCodes)
            ->get()
            ->keyBy('protocol_code');

        // Helper for secure URLs
        $getSecureUrl = function($dbPath) {
            if (!$dbPath) return null;
            $parts = explode('/', trim($dbPath, '/'));
            if (count($parts) >= 3) {
                $filename = implode('/', array_slice($parts, 2));
                return route('view.document', [
                    'protocol_code' => $parts[1],
                    'filename'      => $filename
                ]);
            }
            return null;
        };

        // 2. Format the data for Alpine.js
        $proposals = $rawApps->map(function ($app) use ($getSecureUrl, $decisionLetters, $icfAssessments, $exemptionCertificates) {

            // --- FETCH ACTION REQUIRED ITEMS ---

            // A. Assessment Form Items
            $assessmentRows = [];
            if ($app->assessmentForm && $app->assessmentForm->items) {
                $assessmentRows = $app->assessmentForm->items
                    ->filter(function($item) {
                        return $item->synthesized_comments_action_required == 1;
                    })
                    ->map(function($item) {
                        return [
                            'id' => $item->id,
                            'points' => 'Item ' . ($item->question_number ?? $item->id),
                            'synthesizedComments' => $item->synthesized_comments,
                            'synthesizedCommentsActionRequired' => true
                        ];
                    })->values()->toArray();
            }

            // B. ICF Assessment Items
            $consentRows = [];
            $icfHeader = $icfAssessments->get($app->protocol_code);
            if ($icfHeader) {
                $consentRows = DB::table('icf_assessment_items')
                    ->where('icf_assessment_id', $icfHeader->id)
                    ->where('synthesized_comments_action_required', 1)
                    ->get()
                    ->map(function($item) {
                        return [
                            'id' => $item->id,
                            'points' => 'ICF Item ' . ($item->question_number ?? $item->id),
                            'synthesizedComments' => $item->synthesized_comments,
                            'synthesizedCommentsActionRequired' => true
                        ];
                    })->toArray();
            }

            // --- DOCUMENT MAPPING ---
            $systemDocs = [
                ['key' => 'appform', 'label' => 'Application Form.pdf', 'url' => route('researcher.application.print', ['id' => $app->id])],
                ['key' => 'assessment', 'label' => 'Assessment Form.pdf', 'url' => route('researcher.assessment.print', ['id' => $app->id])],
            ];

            $uploadedDocs = [
                ['key' => 'letter', 'label' => 'Letter Request.pdf', 'url' => $getSecureUrl($app->doc_letter_request)],
                ['key' => 'endorsement', 'label' => 'Endorsement Letter.pdf', 'url' => $getSecureUrl($app->doc_endorsement_letter)],
                ['key' => 'proposal', 'label' => 'Study Protocol.pdf', 'url' => $getSecureUrl($app->doc_full_proposal)],
                ['key' => 'consent_en', 'label' => 'Informed Consent (EN).pdf', 'url' => $getSecureUrl($app->doc_informed_consent_english)],
            ];

            $suppDocs = $app->supplementaryDocuments->map(function($supp, $index) use ($getSecureUrl) {
                return [
                    'key' => 'supp_' . $index,
                    'label' => basename($supp->file_path),
                    'url' => $getSecureUrl($supp->file_path)
                ];
            })->toArray();

            $dl = $decisionLetters->get($app->protocol_code);
            $ec = $exemptionCertificates->get($app->protocol_code);
            $isExempt = $app->status === 'exempted_awaiting_chair_approval';

            return [
                'id' => $app->protocol_code,
                'title' => $app->research_title,
                'proponent' => $app->name_of_researcher,
                'status' => $app->status,
                'classification' => $app->review_classification ?: 'Pending',
                'abstract' => $app->brief_description ?: 'No abstract provided.',
                'external_consultant' => $app->external_consultant,

                // Required for the Action Required Table in Frontend
                'assessmentRows' => $assessmentRows,
                'consentRows' => $consentRows,
                'reviewers' => $app->assignedReviewers ? $app->assignedReviewers->pluck('name')->toArray() : [],

                'decision_letter' => $dl ? (array) $dl : null,
                'exemption_certificate' => $ec ? (array) $ec : null,

                // If it is exempted, check if the certificate exists. Otherwise, check if the decision letter exists.
                'decisionSaved' => $isExempt ? ($ec ? true : false) : ($dl ? true : false),

                'documents' => [
                    'basic' => array_values(array_filter(array_merge($systemDocs, $uploadedDocs), fn($d) => !empty($d['url']))),
                    'supplementary' => array_values(array_filter($suppDocs, fn($d) => !empty($d['url'])))
                ],

                // Formatting metadata
                'pendingReason' => $isExempt ? 'Chair Approval (Exempted)' : 'Pending Reviewers (Ext. Consultant Req.)',
                'classifiedDate' => $app->updated_at->format('Y-m-d'),
            ];
        });

        $systemExternalConsultants = User::where('role', 'External Consultant')->pluck('name')->toArray();

        return view("chair.pages.pipeline.approval", compact("user", "proposals", "systemExternalConsultants"));
    }

    // This function saves the draft of the decision letter details entered by the chair in the decision letter modal. It validates the incoming data, prepares the payload for insertion/updating, and uses updateOrInsert to either create a new record or update an existing one in the decision_letters table. The function also handles exceptions and returns appropriate JSON responses for success or failure.
    // This function is called when the chair clicks "Save Draft" in the decision letter modal on the approval page. It validates the input, prepares the data, and saves it to the database using updateOrInsert, allowing for both creating a new draft and updating an existing one seamlessly. The function ensures that all necessary fields are handled correctly and provides feedback on the operation's success or failure.
    // This is part of the for approval page of the chair when they are drafting the decision letter. It allows them to save their progress as a draft, which is crucial for longer letters that may require multiple editing sessions. The function validates the input, prepares the data for storage, and uses updateOrInsert to handle both new drafts and updates to existing drafts efficiently. It also provides feedback on whether the save operation was successful or if there were any errors.
    public function saveDecisionLetter(Request $request)
    {
        // 1. Validate the incoming data
        $request->validate([
            'protocol_code'   => 'required|exists:research_applications,protocol_code',
            'decision_status' => 'required|string',
            'letter_date'     => 'required|date',
            'documents'       => 'nullable|array', // Validates the dynamic list of document strings
        ]);

        DB::beginTransaction();
        try {
            // 2. Prepare the payload
            $payload = [
                'decision_status' => $request->decision_status,
                'letter_date'     => $request->letter_date,
                'proponent'       => $request->proponent,
                'designation'     => $request->designation,
                'institution'     => $request->institution,
                'address'         => $request->address,
                'title'           => $request->title,
                'subject'         => $request->subject,
                'dear_name'       => $request->dear_name,
                'support_date'    => $request->support_date ?: null,
                // Convert the array of strings into valid JSON for the database
                'documents'       => json_encode($request->documents ?? []),
                'findings'        => $request->findings,
                'recommendations' => $request->recommendations,
                'instructions'    => $request->instructions,
                'updated_at'      => now(),
            ];

            // 3. Add created_at if it's a brand new letter
            $exists = DB::table('decision_letters')->where('protocol_code', $request->protocol_code)->exists();
            if (!$exists) {
                $payload['created_at'] = now();
            }

            // 4. Save to Database
            DB::table('decision_letters')->updateOrInsert(
                ['protocol_code' => $request->protocol_code],
                $payload
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Decision letter details saved successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save decision letter: ' . $e->getMessage()
            ], 500);
        }
    }

    // This function is specifically for finalizing Exempted protocols, which is a unique workflow that generates an Exemption Certificate instead of a Decision Letter. It updates the application status to "approved", saves the certificate details, creates an official log entry, and manages protocol routing logs to reflect the document flow accurately. This is separate from the general finalizeProtocol function because Exempted protocols have distinct handling requirements and do not follow the same decision letter process as regular protocols.
    // This function is in the for approval page of the chair when they finalize an exempted protocol after review. It updates the application status to "approved", saves the exemption certificate details, creates an official log entry, and manages protocol routing logs to ensure the document flow is accurately recorded. This is separate from the general finalizeProtocol function because Exempted protocols have distinct handling requirements and do not follow the same decision letter process as regular protocols.
    public function finalizeExemptedProtocol(Request $request)
    {
        $request->validate([
            'protocol_code'    => 'required|exists:research_applications,protocol_code',
            'certificate_text' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $application = ResearchApplications::where('protocol_code', $request->protocol_code)->firstOrFail();
            $currentUser = auth()->user();
            $newStatus = 'approved';

            // 1. Update Application Status
            $application->update([
                'status' => $newStatus,
                'updated_at' => now(),
            ]);

            // 2. Save Exemption Certificate Data
            DB::table('decision_letters')->updateOrInsert(
                ['protocol_code' => $request->protocol_code],
                [
                    'decision_status' => 'Exempted Approved',
                    'letter_date'     => now()->toDateString(),
                    'findings'        => $request->certificate_text,
                    'updated_at'      => now()
                ]
            );

            // 3. Official Audit Log
            $application->logs()->create([
                'protocol_code' => $application->protocol_code,
                'user_id'       => $currentUser->id,
                'status'        => $newStatus,
                'comment'       => "Chair reviewed and finalized the Exemption Certificate.",
            ]);

            // --- PROTOCOL ROUTING LOGIC ---

            // A. RECEIVE: Close the step where the Secretariat forwarded the Exempted protocol to the Chair
            DB::table('protocol_routing_logs')
                ->where('protocol_code', $request->protocol_code)
                ->where('document_nature', 'Exempted - Forwarded to Chair')
                ->whereNull('to_name')
                ->orderBy('id', 'desc')
                ->first()?->update([
                    'to_name'    => $currentUser->name,
                    'to_user_id' => $currentUser->id,
                    'updated_at' => now()
                ]);

            // B. ROUTE: Final Exemption Certificate sent to the Researcher
            DB::table('protocol_routing_logs')->insert([
                'protocol_code'   => $request->protocol_code,
                'document_nature' => 'Exemption Certificate',
                'from_name'       => $currentUser->name,
                'from_user_id'    => $currentUser->id,
                'to_name'         => $application->name_of_researcher,
                'to_user_id'      => $application->user_id,
                'remarks'         => 'Exemption certificate issued to proponent.',
                'created_at'      => now(),
                'updated_at'      => now()
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Exempted protocol finalized.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // This is the main function that handles both Exempted and Full Board finalization, based on the "type" field in the request. It updates the application status, saves either the decision letter or exemption certificate, creates an official log entry, and manages protocol routing logs to ensure the document flow is accurately recorded.
    // This is part of the for approval page of the chair when they finalize a protocol after review. It handles both exempted protocols (which generate a certificate) and regular protocols (which generate a decision letter), using the "type" field to differentiate the logic paths. The function ensures that all database updates are wrapped in a transaction for data integrity, and it provides detailed logging for audit purposes.
    public function finalizeProtocol(Request $request)
    {
        // 1. Validation includes the new fields from your JavaScript payload
        $request->validate([
            'protocol_code'    => 'required|exists:research_applications,protocol_code',
            'type'             => 'required|in:exempted,full_board',
            'decision_status'  => 'required|in:approved,minor_revision,major_revision,rejected',
            'certificate_text' => 'nullable|string', // Captures the edited HTML from Alpine
            'principal_investigator' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $application = ResearchApplications::where('protocol_code', $request->protocol_code)->firstOrFail();
            $currentUser = auth()->user();

            // 2. Determine the official status based on the review type
            if ($request->type === 'exempted') {
                $newStatus = 'approved';
                $readableDecision = 'EXEMPTED FROM REVIEW';

                // Save the finalized certificate data using the HTML input
                DB::table('exemption_certificates')
                    ->where('protocol_code', $application->protocol_code)
                    ->update([
                        'chairperson_name'  => $currentUser->name,
                        'date_issued'       => now()->toDateString(),

                        // Looks for the HTML input first. If empty, falls back to the database.
                        'investigator_name' => $request->principal_investigator ?? ($application->primary_researcher ?? $application->name_of_researcher),

                        'study_title'       => $application->research_title,
                        'berc_code'         => $application->protocol_code,
                        'updated_at'        => now()
                    ]);

            } else {
                // Map Decision Status for the main Application table (Your existing logic)
                $newStatus = match ($request->decision_status) {
                    'minor_revision' => 'resubmit',
                    'major_revision' => 'resubmit',
                    'rejected'         => 'rejected',
                    default          => 'approved',
                };
                $readableDecision = strtoupper(str_replace('_', ' ', $request->decision_status));
            }

            // 3. Update Application Status
            $application->update([
                'status'     => $newStatus,
                'updated_at' => now(),
            ]);

            // 4. Official Audit Log
            $application->logs()->create([
                'protocol_code' => $application->protocol_code,
                'user_id'       => $currentUser->id,
                'status'        => $newStatus,
                'comment'       => "Chair validated and finalized the document. Official Decision: " . $readableDecision . ".",
            ]);

            // --- PROTOCOL ROUTING LOGIC ---

            // A. RECEIVE DRAFT: Close the loop on the Secretariat's draft entry
            DB::table('protocol_routing_logs')
                ->where('protocol_code', $request->protocol_code)
                ->where(function($query) {
                    // Check for either the regular draft OR the exempted forward log
                    $query->where('document_nature', 'Draft Decision Letter')
                          ->orWhere('document_nature', 'Exempted - Forwarded to Chair');
                })
                ->whereNull('to_name')
                ->orderBy('id', 'desc')
                ->limit(1)
                ->update([
                    'to_name'    => $currentUser->name,
                    'to_user_id' => $currentUser->id,
                    'updated_at' => now()
                ]);

            // B. ROUTE FINAL: Final Document record sent to the Researcher
            $documentNature = $request->type === 'exempted'
                ? 'Final Certificate of Exemption'
                : 'Final Decision Letter (' . $readableDecision . ')';

            $remarks = $request->type === 'exempted'
                ? 'Certificate of Exemption issued to proponent.'
                : 'Final decision letter issued to proponent.';

            DB::table('protocol_routing_logs')->insert([
                'protocol_code'   => $request->protocol_code,
                'document_nature' => $documentNature,
                'from_name'       => $currentUser->name,
                'from_user_id'    => $currentUser->id,
                'to_name'         => $application->name_of_researcher,
                'to_user_id'      => $application->user_id,
                'remarks'         => $remarks,
                'created_at'      => now(),
                'updated_at'      => now()
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Document successfully finalized and routed.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    //function to save the assigned consultant to the database and assign them to the protocol immediately with "Accepted" status, bypassing "Pending"
    //this function is in the for approval page of the chair when they assign an external consultant to a protocol that requires one. It creates the consultant as a user, creates their reviewer profile, and assigns them to the protocol with "Accepted" status in one go. It also clears the external_consultant field in the research_applications table to remove it from the chair's queue.
    public function assignConsultant(Request $request)
    {
        $request->validate([
            'protocol_code' => 'required|exists:research_applications,protocol_code',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'nullable|string|max:50',
            'expertise'     => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $application = ResearchApplications::where('protocol_code', $request->protocol_code)->firstOrFail();

            // 1. Create the User Account with a default password
            $defaultPassword = 'BatStateU123!'; // Set your desired default password here
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($defaultPassword),
                'role'     => 'External Consultant',
            ]);

            // 2. Create the Reviewer Profile
            $reviewer = Reviewer::create([
                'user_id'        => $user->id,
                'name'           => $request->name,
                'type'           => 'External Consultant',
                'specialization' => $request->expertise,
                'is_active'      => true,
            ]);

            // 3. Assign directly to the Protocol (Instantly Accepted!)
            DB::table('application_reviewer')->insert([
                'protocol_code' => $request->protocol_code,
                'reviewer_id'   => $reviewer->id,
                'status'        => 'Accepted', // <-- Bypasses "Pending"
                'date_assigned' => now(),
                'date_accepted' => now(),      // <-- Stamps the acceptance time
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // 4. Clear the External Consultant Appeal to remove it from the Chair's Queue
            $application->update([
                'external_consultant' => null
            ]);

            // 5. --- SYNC WITH ASSESSMENT FORMS & ICF ASSESSMENTS ---
            $assignedReviewerIds = DB::table('application_reviewer')
                ->where('protocol_code', $request->protocol_code)
                ->orderBy('id')
                ->pluck('reviewer_id')
                ->toArray();

            // Setup common reviewer mapping payload
            $reviewerMapping = [
                'reviewer_id'   => $application->user_id, // Applicant ID
                'reviewer_1_id' => $assignedReviewerIds[0] ?? null,
                'reviewer_2_id' => $assignedReviewerIds[1] ?? null,
                'reviewer_3_id' => $assignedReviewerIds[2] ?? null,
                'updated_at'    => now()
            ];

            // 5a. Sync Assessment Forms
            $formExists = DB::table('assessment_forms')->where('protocol_code', $request->protocol_code)->exists();
            $formPayload = $reviewerMapping;

            if (!$formExists) {
                $formPayload['status'] = 'evaluating';
                $formPayload['created_at'] = now();
            }

            DB::table('assessment_forms')->updateOrInsert(
                ['protocol_code' => $request->protocol_code],
                $formPayload
            );

            // 5b. Sync ICF Assessments
            $icfExists = DB::table('icf_assessments')->where('protocol_code', $request->protocol_code)->exists();
            $icfPayload = $reviewerMapping;

            if (!$icfExists) {
                $icfPayload['status'] = 'evaluating';
                $icfPayload['created_at'] = now();
            }

            DB::table('icf_assessments')->updateOrInsert(
                ['protocol_code' => $request->protocol_code],
                $icfPayload
            );

            /// 6. --- AUTO-TRANSITION CHECK ---
            $totalAssigned = count($assignedReviewerIds);
            $acceptedCount = DB::table('application_reviewer')
                ->where('protocol_code', $request->protocol_code)
                ->where('status', 'Accepted')
                ->count();

            if ($totalAssigned > 0 && $acceptedCount === $totalAssigned) {
                $application->update(['status' => 'under_review']);

                // Use DB::table instead of the model relationship to prevent relationship errors
                DB::table('research_application_logs')->insert([
                    'protocol_code' => $application->protocol_code,
                    'user_id'       => auth()->id(),
                    'status'        => 'under_review',
                    'comment'       => 'System: External Consultant created and instantly accepted. Protocol transitioned to under review.',
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'External Consultant created, accepted, and assigned to the assessment forms.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create consultant: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper function to format file sizes in a human-readable way
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

    //Shows the history page for the chair, which includes all the past applications with their revisions and documents. This is a complex page that requires efficient querying and formatting to ensure good performance and a user-friendly interface.
    public function showChairHistory(Request $request)
    {
        $user = auth()->user();

        // 1. Capture request parameters for Pagination, Search, and Filters
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 15, 20])) {
            $perPage = 10;
        }
        $search = $request->input('search', '');
        $statusFilter = $request->input('status', 'all');

        // 2. Fast Aggregate Counts for the UI Tabs (Runs quickly before pagination)
        $counts = [
            'all' => ResearchApplications::count(),
            'completed' => ResearchApplications::whereNotIn('status', ['Disapproved', 'Rejected'])->count(),
            'rejected' => ResearchApplications::whereIn('status', ['Disapproved', 'Rejected'])->count(),
        ];

        // 3. Build the Main Query
        $query = ResearchApplications::with('supplementaryDocuments')
            ->orderBy('created_at', 'desc');

        // Apply Server-Side Search
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('protocol_code', 'LIKE', "%{$search}%")
                  ->orWhere('research_title', 'LIKE', "%{$search}%");
            });
        }

        // Apply Server-Side Status Filter
        if ($statusFilter !== 'all') {
            if ($statusFilter === 'completed') {
                $query->whereNotIn('status', ['Disapproved', 'Rejected']);
            } elseif ($statusFilter === 'rejected') {
                $query->whereIn('status', ['Disapproved', 'Rejected']);
            }
        }

        // 4. Execute Server-Side Pagination
        $applications = $query->paginate($perPage)->appends($request->query());

        // 5. FETCH ALL REVISIONS FOR THESE APPLICATIONS
        // We pluck the protocol codes ONLY for the 10-20 items currently on this page
        $protocolCodes = collect($applications->items())->pluck('protocol_code');

        $exemptionCertificates = DB::table('exemption_certificates')
            ->whereIn('protocol_code', $protocolCodes)
            ->pluck('protocol_code')
            ->toArray();

        $allRevisions = ResearchApplicationRevision::with('documents')
            ->whereIn('protocol_code', $protocolCodes)
            ->orderBy('revision_number', 'asc')
            ->get()
            ->groupBy('protocol_code');

        // 6. FORMAT MAIN APPLICATIONS
        // Use collect()->map to build the precise array structure JS expects
        $formattedApps = collect($applications->items())->map(function($app) use ($allRevisions, $exemptionCertificates) {
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
                $icfDoneField = "reviwer_{$i}_done";

                // ─── PROCESS GENERAL ASSESSMENT FORM ───
                if ($assessment && !empty($assessment->$idField)) {
                    $reviewerData = DB::table('reviewers')
                        ->join('users', 'reviewers.user_id', '=', 'users.id')
                        ->where('reviewers.id', $assessment->$idField)
                        ->select('users.name')
                        ->first();

                    $reviewerName = $reviewerData ? $reviewerData->name : "Reviewer $i";
                    $isDone = in_array($assessment->$doneField, ['resolved', '1', 'true', 'Completed']);

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

            // Check if the protocol is exempted
            if (in_array($app->protocol_code, $exemptionCertificates)) {
                $docs[] = [
                    'name' => 'Certificate of Exemption (System Generated)',
                    'file' => route('protocol.print-exemption', ['protocol_code' => $app->protocol_code]),
                    'size' => 'Auto',
                    'type' => 'pdf'
                ];
            }

            // Otherwise, check for the standard decision letter
            elseif ($app->decisionLetter) {
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
                                ? "{$baseLabel} - {$doc->description}"
                                : $baseLabel;
                $addDoc($doc->file_path, $displayTitle);
            }

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
        })->values()->toArray(); // THIS IS CRITICAL: Formats it properly for Javascript!

        // 7. BUILD REVISION HISTORY WITH DOCUMENTS
        $decisionLetters = DB::table('revision_decision_letters')
            ->whereIn('protocol_code', $protocolCodes)
            ->get()
            ->groupBy(function($item) {
                return $item->protocol_code . '-' . $item->version_number;
            });

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

        // 8. Prepare arrays and pass new variables to the view
        $formattedRevisions = [];

        return view('chair.pages.history', compact(
            'user',
            'applications', // Passes the paginator object for the HTML links
            'formattedApps', // Properly mapped array with 'title', 'record', etc.
            'formattedRevisions',
            'revisionHistory',
            'counts',
            'perPage',
            'search',
            'statusFilter'
        ));
    }

    //Function to show the decisionletter and the revision points for the chair to make a decision on resubmissions
    public function showResubmissionDecision()
    {
        $user = auth()->user();

        // 1. Fetch revisions awaiting the chair's approval
        $revisions = ResearchApplicationRevision::where('status', 'awaiting_chair_approval')
            ->orderBy('updated_at', 'desc')
            ->get();

        $protocolsData = [];

        foreach ($revisions as $rev) {
            $app = ResearchApplications::where('protocol_code', $rev->protocol_code)->first();

            // Get the Revision Responses (for the Synthesized Form / Points for revision)
            $responses = RevisionResponse::where('protocol_code', $rev->protocol_code)
                ->where('revision_number', $rev->revision_number)
                ->orderBy('id', 'asc')
                ->get();

            $revisionRows = $responses->map(function($r) {
                return [
                    'id'                  => $r->id,
                    'item'                => $r->item,
                    'berc_recommendation' => $r->berc_recommendation,
                    'researcher_response' => $r->researcher_response,
                    'section_and_page'    => $r->section_and_page,
                    'synthesized_comments'=> $r->synthesized_comments ?? '<i class="text-gray-400">No synthesis provided.</i>',
                    'action'              => $r->synthesized_comments_action ?? 'pending'
                ];
            });

            // 2. Get the Drafted Decision Letter from the Secretariat
            // We match protocol_code and version_number to ensure it's the correct one
            $decisionLetter = DB::table('revision_decision_letters')
                ->where('protocol_code', $rev->protocol_code)
                ->where('version_number', (string)$rev->revision_number) // Cast to string if column is varchar
                ->first();

            $protocolsData[] = [
                'id'             => $rev->protocol_code,
                'version'        => 'V' . $rev->revision_number,
                'title'          => $app->research_title ?? 'Unknown Title',
                'proponent'      => $app->name_of_researcher ?? 'Unknown Proponent',
                'dateSubmitted'  => Carbon::parse($rev->updated_at)->format('Y-m-d'),
                'revisionRows'   => $revisionRows,

                // Pass the drafted letter data to the frontend
                'letterData'     => $decisionLetter ? [
                    'decision_status' => $decisionLetter->decision_status,
                    'date'            => $decisionLetter->letter_date,
                    'proponent'       => $decisionLetter->proponent,
                    'designation'     => $decisionLetter->designation,
                    'institution'     => $decisionLetter->institution,
                    'address'         => $decisionLetter->address,
                    'title'           => $decisionLetter->title,
                    'subject'         => $decisionLetter->subject,
                    'dearName'        => $decisionLetter->dear_name,
                    'supportDate'     => $decisionLetter->support_date,
                    'documents'       => $decisionLetter->documents ? json_decode($decisionLetter->documents) : [],
                    'paragraph1'      => $decisionLetter->findings, // Mapped to findings
                    'paragraph2'      => $decisionLetter->instructions // Mapped to instructions
                ] : null
            ];
        }

        return view('chair.pages.pipeline.revisiondecision', compact('user', 'protocolsData'));
    }

    // API Endpoint to handle Save Draft & Final Validation for the Chair's Decision Letter
    public function saveOrValidateDecision(Request $request)
    {
        $user = auth()->user(); // The Chair

        $validated = $request->validate([
            'action_type'     => 'required|in:draft,finalize',
            'protocol_code'   => 'required|string',
            'revision_number' => 'required|integer',
            'decision_status' => 'required|string',
            'letter_data'     => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $protocolCode = $validated['protocol_code'];
            $letterData = $validated['letter_data'];
            $version = (int) $validated['revision_number'];
            $decision = $validated['decision_status']; // approved, rejected, minor_revision, major_revision
            $isFinalizing = $validated['action_type'] === 'finalize';

            // 🚀 ENFORCE MAXIMUM REVISIONS: Auto-reject if V3 is not approved
            $maxRevisionsExceeded = false;
            if ($version >= 3 && $decision !== 'approved') {
                $decision = 'rejected';
                $maxRevisionsExceeded = true;
            }

            // 1. Update the Decision Letter
            DB::table('revision_decision_letters')
                ->where('protocol_code', $protocolCode)
                ->where('version_number', (string)$version) // Ensure we target the right version
                ->update([
                    'decision_status' => $decision,
                    'letter_date'     => $letterData['date'],
                    'proponent'       => $letterData['proponent'],
                    'designation'     => $letterData['designation'],
                    'institution'     => $letterData['institution'],
                    'address'         => $letterData['address'],
                    'title'           => $letterData['title'],
                    'subject'         => $letterData['subject'],
                    'dear_name'       => $letterData['dearName'],
                    'support_date'    => $letterData['supportDate'] ?? null,
                    'documents'       => isset($letterData['documents']) ? json_encode(array_filter($letterData['documents'])) : null,
                    'findings'        => $letterData['paragraph1'] ?? null,
                    'instructions'    => $letterData['paragraph2'] ?? null,
                    'approval_status' => $isFinalizing ? 'approved' : 'draft',
                    'updated_at'      => now(),
                ]);

            // 2. Handle Workflow Status Updates
            if ($isFinalizing) {
                // Update the REVISION status
                ResearchApplicationRevision::where('protocol_code', $protocolCode)
                    ->where('revision_number', $version)
                    ->update(['status' => $decision]);

                // Update MAIN APPLICATION status if terminal (or if max revisions reached)
                if ($decision === 'approved') {
                    ResearchApplications::where('protocol_code', $protocolCode)->update(['status' => 'approved']);
                } elseif ($decision === 'rejected') {
                    ResearchApplications::where('protocol_code', $protocolCode)->update(['status' => 'rejected']);
                }

                // 3. ─── UPDATE PREVIOUS ROUTING LOG (Secretariat to Chair) ───
                DB::table('protocol_routing_logs')
                    ->where('protocol_code', $protocolCode)
                    ->where('document_nature', "Draft Decision Letter (Version {$version})")
                    ->whereNull('to_user_id')
                    ->update([
                        'to_name'    => $user->name,
                        'to_user_id' => $user->id,
                        'updated_at' => now()
                    ]);

                // 4. ─── GET PROTOCOL OWNER (Researcher) ───
                $application = ResearchApplications::where('protocol_code', $protocolCode)->first();
                $proponent = User::find($application->user_id);

                // 5. ─── LOG NEW INSTANCE (Chair to Researcher) ───
                DB::table('protocol_routing_logs')->insert([
                    'protocol_code'   => $protocolCode,
                    'document_nature' => "Final Decision Letter (Version {$version})",
                    'from_name'       => $user->name,
                    'from_user_id'    => $user->id,
                    'to_name'         => $proponent->name ?? 'Researcher',
                    'to_user_id'      => $application->user_id,
                    'remarks'         => "Chair finalized the decision (" . str_replace('_', ' ', $decision) . ") and routed it back to the researcher.",
                    'created_at'      => now(),
                    'updated_at'      => now()
                ]);
            }

            DB::commit();

            // Customize the success message if it was auto-rejected
            $successMsg = $isFinalizing ? 'Decision Finalized successfully.' : 'Draft saved successfully.';
            if ($maxRevisionsExceeded) {
                $successMsg = 'Maximum revisions exceeded. Protocol automatically rejected.';
            }

            return response()->json([
                'success' => true,
                'message' => $successMsg
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Chair Decision Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
