<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ResearchApplications;
use App\Models\PaymentMethod;
use App\Models\ResearchApplication;
use App\Models\ResearchApplicationRevision;
use Illuminate\Support\Facades\Auth;
use App\Models\RevisionResponse;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ResearcherController extends Controller
{
    public function completeTutorial(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->update([
                'is_first_login' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tutorial marked as complete.'
            ]);
        }

        return response()->json(['success' => false], 401);
    }

    //this function shows the research review application for the researcher page
    //this function gets all the payment methods to display in the payment section of the review form, allowing researchers to select their preferred payment method when submitting their application or making payments related to their research review process.
    public function showReviewForm()
    {
        $user = auth()->user();
        $paymentMethods = PaymentMethod::orderBy('is_active', 'desc')
                                   ->orderBy('name', 'asc')
                                   ->get();
        return view('researcher.reviewform', compact('user', 'paymentMethods'));
    }

    //this function renders the application form as a PDF for a specific research application. It fetches the application data along with related logs and documents, processes the data to extract necessary information such as unique informed consent form languages and document details, and then passes this data to a Blade view that generates the PDF. The function also handles the retrieval of electronic signatures for display in the PDF if they exist.
    //this is called on endpoints such as the application status and history on all users except reviewers
    public function printApplicationForm($id)
    {
        // 1. Fetch application with logs
        $application = ResearchApplications::with(['logs.user'])->findOrFail($id);

        // 2. Fetch Basic Requirements (New Table)
        $basicReqs = DB::table('basic_requirements')
            ->where('protocol_code', $application->protocol_code)
            ->get();

        // 3. Fetch Supplementary Documents (Existing Table)
        $suppDocs = DB::table('supplementary_documents')
            ->where('protocol_code', $application->protocol_code)
            ->get();

        // --- NEW: UNIQUE ICF LANGUAGES ---
        // Filters for informed_consent, grabs the description (language),
        // makes them uniform case, and keeps only unique entries.
        $icfLanguages = $basicReqs->where('type', 'informed_consent')
            ->pluck('description')
            ->map(function($lang) {
                return trim(ucwords(strtolower($lang)));
            })
            ->unique()
            ->filter() // Removes null/empty if any
            ->values();

        // 4. Map Supplementary Documents (From the suppDocs collection)
        $curriculumVitae = $basicReqs->where('type', 'curriculum_vitae');
        $questionnaire   = $suppDocs->where('type', 'questionnaire');
        $dataCollection  = $suppDocs->where('type', 'data_collection');
        $productBrochure = $suppDocs->where('type', 'product_brochure');
        $philippineFda   = $suppDocs->where('type', 'philippine_fda');
        $specialPops     = $suppDocs->where('type', 'special_populations');
        $others          = $suppDocs->where('type', 'others');

        // 5. Log Logic
        $latestLog = $application->logs->sortByDesc('created_at')->first();
        $checkingLog = $application->logs
            ->whereIn('status', ['documents_complete', 'documents_checking', 'incomplete_documents'])
            ->sortByDesc('created_at')
            ->first();

        $checkingComment = $checkingLog ? $checkingLog->comment : null;
        $checkingUser = ($checkingLog && $checkingLog->user) ? $checkingLog->user : null;

        // 6. Signature Retrieval
        $signatureBase64 = null;
        if (!empty($application->e_signature)) {
            $pathsToTry = [
                storage_path('app/' . $application->e_signature),
                storage_path('app/private/' . $application->e_signature)
            ];

            foreach ($pathsToTry as $fullPath) {
                if (file_exists($fullPath)) {
                    $fileContents = file_get_contents($fullPath);
                    $mimeType = mime_content_type($fullPath);
                    $signatureBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
                    break;
                }
            }
        }

        return view('forms.applicationform', compact(
            'application',
            'latestLog',
            'checkingLog',
            'checkingComment',
            'checkingUser',
            'icfLanguages',    // List of unique languages (e.g., ["English", "Filipino"])
            'curriculumVitae',
            'questionnaire',
            'dataCollection',
            'productBrochure',
            'philippineFda',
            'specialPops',
            'others',
            'signatureBase64'
        ));
    }

    //this function renders the assessment form as a PDF for a specific research application. It fetches the application data along with related logs and the associated assessment form items. The function processes the assessment form items to check if any of them have the "Action Required" flag set, and if so, it prepends "ACTION REQUIRED: " to the synthesized comments for that item. Finally, it passes the application data, assessment form, latest log, and processed items to a Blade view that generates the PDF.
    //this function is called on the application status and history page of the researcher only
    public function printAssessmentFormPDF($id)
    {
        // 1. Eager load 'assessmentForm.items'
        $application = ResearchApplications::with([
            'supplementaryDocuments',
            'logs.user',
            'assessmentForm.items'
        ])->findOrFail($id);

        $assessmentForm = $application->assessmentForm;
        $latestLog = $application->logs->sortByDesc('created_at')->first();

        // 2. Process items and handle the Action Required flag
        $items = $assessmentForm ? $assessmentForm->items->map(function($item) {
            // Create a temporary property to hold the text for the PDF
            $baseComment = $item->synthesized_comments ?? '';

            if ($item->synthesized_comments_action_required) {
                // Prepend the flag to the comment
                $item->final_comment = "ACTION REQUIRED: " . $baseComment;
            } else {
                $item->final_comment = $baseComment;
            }

            return $item;
        })->keyBy('question_number') : collect();

        return view('forms.assessmentform', compact('application', 'assessmentForm', 'latestLog', 'items'));
    }

    //this function renders the informed consent form as a PDF for a specific research application. It fetches the application data along with related logs and the associated informed consent form items. The function processes the informed consent form items to check if any of them have the "Action Required" flag set, and if so, it prepends "ACTION REQUIRED: " to the synthesized comments for that item. Finally, it passes the application data, informed consent form, latest log, and processed items to a Blade view that generates the PDF.
    //this function is called on the application status and history page of the researcher only
    public function printInformedConsentFormPDF($id)
    {
        $application = ResearchApplications::with([
            'supplementaryDocuments',
            'logs.user',
            'informedConsent.items',
            'assessmentForm'
        ])->findOrFail($id);

        $consentForm = $application->informedConsent;
        $assessmentForm = $application->assessmentForm;

        if (!$consentForm) {
            return response()->make('<script>alert("No ICF found."); window.close();</script>');
        }

        $latestLog = $application->logs->sortByDesc('created_at')->first();

        $items = $consentForm->items->map(function($item) {
            $baseComment = $item->synthesized_comments ?? '';

            if ($item->synthesized_comments_action_required) {
                $item->final_comment = "ACTION REQUIRED: " . $baseComment;
            } else {
                $item->final_comment = $baseComment;
            }

            return $item;
        })->keyBy('question_number');

        return view('forms.informedconsent', compact(
            'application',
            'consentForm',
            'assessmentForm',
            'latestLog',
            'items'
        ));
    }

    //this function renders the resubmission form as a PDF for a specific research application revision. It fetches the revision data along with the original application using the protocol code as a bridge. The function also defines a mapping of question numbers to their full question labels, which is used to enhance the display of the revision responses. It then retrieves the specific responses for the given protocol code and revision number, normalizes the item identifiers, and attaches the full question labels to each response. Finally, it passes the application data, latest revision, and processed responses to a Blade view that generates the PDF.
    //this function is called on the application status and history page of all users except reviewers when they click the "View Resubmission Form" button for a specific revision entry
    public function printResubmissionFormPdf($id)
    {
        // 1. Find the specific Revision Tracker by its primary ID (passed from the URL)
        $revision = ResearchApplicationRevision::findOrFail($id);

        // 2. Find the original Application using the shared protocol_code bridge
        $application = ResearchApplications::where('protocol_code', $revision->protocol_code)->first();

        if (!$application) {
            return response()->make('<script>alert("Original Application not found for this revision."); window.close();</script>');
        }

        // 3. Full question labels
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

        // 4. Fetch the specific responses matching BOTH the protocol code AND the revision number
        $responses = RevisionResponse::where('protocol_code', $revision->protocol_code)
            ->where('revision_number', $revision->revision_number)
            ->get()
            ->map(function ($response) use ($questionLabels) {
                $normalizedItem = null;

                if (!empty($response->item)) {
                    preg_match('/\d+\.\d+/', (string) $response->item, $matches);
                    $normalizedItem = $matches[0] ?? trim((string) $response->item);
                }

                $response->normalized_item = $normalizedItem;
                $response->full_question = $normalizedItem && isset($questionLabels[$normalizedItem])
                    ? $questionLabels[$normalizedItem]
                    : 'Requirement';

                return $response;
            });

        // 5. Return the view with the securely fetched data
        return view('forms.resubmissionform', [
            'application' => $application,
            'latestRevision' => $revision,
            'responses' => $responses
        ]);
    }

    //helper function to format file sizes into human-readable strings (e.g., converting bytes to KB, MB, GB). This function checks the size in bytes and formats it accordingly, ensuring that file sizes are displayed in a user-friendly manner when listing documents in the application status and history pages.
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

    //function to show the application status page for the researcher, which lists all their research applications that are not in terminal statuses (approved, rejected, disapproved, completed). The function fetches the relevant applications along with their supplementary documents, informed consent forms, logs, and revisions. It then formats the applications to include both system-generated documents (like the application form and assessment form) and user-uploaded documents, ensuring that all file links are secure and that file sizes are displayed in a human-readable format. Finally, it passes the formatted applications to the Blade view responsible for rendering the application status page.
    public function showApplicationStatus()
    {
        $user = auth()->user();

        // ══════════════════════════════════════════════════════════
        // 1. FETCH & FORMAT MAIN APPLICATIONS
        // ══════════════════════════════════════════════════════════
        $terminalStatuses = [
            'approved', 'Approved',
            'rejected', 'Rejected',
            'disapproved', 'Disapproved',
            'completed', 'Completed'
        ];

        $rawApps = ResearchApplications::with(['supplementaryDocuments', 'informedConsent', 'logs', 'revisions'])
            ->where('user_id', $user->id)
            ->whereNotIn('status', $terminalStatuses) // <-- Added this line
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedApps = $rawApps->map(function ($app) {
            $docs = [];

            // System Generated Docs
            $docs[] = [
                'name' => 'Application Form (System Generated)',
                'file' => route('researcher.application.print', ['id' => $app->id]),
                'size' => 'Auto',
                'type' => 'pdf'
            ];

            $docs[] = [
                'name' => 'Assessment Form (System Generated)',
                'file' => route('researcher.assessment.print', ['id' => $app->id]),
                'size' => 'Auto',
                'type' => 'pdf'
            ];

            if ($app->informedConsent) {
                $docs[] = [
                    'name' => 'Informed Consent Form (System Generated)',
                    'file' => route('researcher.informedconsent.print', ['id' => $app->id]),
                    'size' => 'Auto',
                    'type' => 'pdf'
                ];
            }

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

            // 1. Define a readable dictionary for the database types
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

            // 2. Add Basic Requirements dynamically
            // (Using DB::table as a safe fallback in case the model relationship isn't set up yet)
            $basicDocs = DB::table('basic_requirements')
                ->where('protocol_code', $app->protocol_code)
                ->get();

            foreach ($basicDocs as $doc) {
                $baseLabel = $documentDictionary[$doc->type] ?? ucwords(str_replace('_', ' ', $doc->type));

                // If the user provided a custom description (like "English" for ICF), append it
                $displayTitle = !empty($doc->description) && $doc->description !== $baseLabel
                                ? "{$baseLabel} - {$doc->description}"
                                : $baseLabel;

                $addDoc($doc->file_path, $displayTitle);
            }

            // 3. Add Supplementary Documents dynamically
            foreach ($app->supplementaryDocuments as $sup) {
                $baseLabel = $documentDictionary[$sup->type] ?? ucwords(str_replace('_', ' ', $sup->type));

                // If the user provided a custom description (like a specific Doctor's name for CV), append it
                $displayTitle = !empty($sup->description) && $sup->description !== $baseLabel
                                ? "{$baseLabel} - {$sup->description}"
                                : $baseLabel;

                $addDoc($sup->file_path, $displayTitle);
            }

            // Logic and status handling...
            $currentStep = 2;
            $status = 'Under Review';
            if ($app->protocol_code) $currentStep = 2;

            $dbStatus = $app->status;
            $latestLog = $app->logs->whereNotNull('comment')->sortByDesc('created_at')->first();
            $commentText = $latestLog ? $latestLog->comment : null;

            // Get the latest revision status for the frontend button logic
            $latestRevision = $app->revisions->sortByDesc('revision_number')->first();
            $revisionStatus = $latestRevision ? $latestRevision->status : null;

            switch ($dbStatus) {
                case 'submitted':
                case 'incomplete_documents':
                    $currentStep = 2;
                    $status = ($dbStatus === 'submitted') ? 'Checking Documents' : 'Reupload Documents';
                    $stage = ($dbStatus === 'submitted') ? 'Awaiting Secretariat Screening' : 'Action Required: Incomplete Documents';
                    break;
                case 'documents_checking':
                case 'documents_complete':
                    $currentStep = 3;
                    $status = 'Awaiting Classification & Assignment';
                    $stage = 'Secretariat Verifying Completeness';
                    break;
                case 'exempted_awaiting_chair_approval':
                    $currentStep = 6;
                    $status = 'Awaiting Chair Approval';
                    $stage = 'Exempted Protocol: Sent to Chair for final sign-off';
                    break;
                case 'awaiting_reviewer_approval':
                    $currentStep = 3;
                    $status = 'Awaiting Reviewer Confirmation';
                    $stage = 'Reviewers have been assigned and must accept the invitation';
                    break;
                case 'under_review':
                    $currentStep = 4;
                    $status = 'Under Review';
                    $stage = 'Reviewers are currently evaluating the protocol';
                    break;
                case 'review_finished':
                    $currentStep = 5;
                    $status = 'Review Completed';
                    $stage = 'Evaluations submitted; awaiting meeting or results';
                    break;
                case 'assessment_processed':
                case 'drafting_decision':
                    $currentStep = 6;
                    $status = 'Finalizing Decision';
                    $stage = 'Decision drafting or waiting for Chair signature';
                    break;
                case 'awaiting_approval':
                case 'awaiting_chair_approval_decision':
                    $currentStep = 6;
                    $status = 'Awaiting Chair Approval';
                    $stage = 'Awaiting Chair Approval and Signature';
                    break;
                case 'approved':
                case 'completed':
                    $currentStep = 7;
                    $status = ($dbStatus === 'approved') ? 'Approved' : 'Completed';
                    $stage = 'Process Finished';
                    break;
                case 'resubmit':
                case 'returned_for_revision':
                    $currentStep = 8;
                    $status = 'Revision Required';
                    $stage = 'Check remarks for required changes';
                    break;
                default:
                    $currentStep = 2;
                    $status = 'Pending';
                    $stage = 'Initial Submission';
            }

            return [
                'id' => 'APP-' . str_pad($app->id, 3, '0', STR_PAD_LEFT),
                'protocol_code' => $app->protocol_code,
                'tab' => 'application',
                'title' => $app->research_title,
                'record' => $app->protocol_code ?? 'Pending',
                'status' => $status,
                'date' => $app->created_at->format('F j, Y'),
                'comments'=> $commentText,
                'reviewer' => $app->reviewer1_assigned ?? 'Secretariat',
                'studyType' => match ((int) $app->type_of_research) {
                    1 => 'Faculty Research', 2 => 'Graduate School Research', 3 => 'Undergraduate Research',
                    4 => 'Integrated School Student Research', 5 => 'External Research', default => 'Not Specified',
                },
                'stage' => $stage,
                'step' => $currentStep,
                'revisionRemarks' => null,
                'revision_status' => $revisionStatus,
                'docs' => $docs
            ];
        });

        // ══════════════════════════════════════════════════════════
        // 2. FETCH & FORMAT RESUBMISSIONS (LATEST VERSION ONLY)
        // ══════════════════════════════════════════════════════════
        $rawRevisions = ResearchApplicationRevision::with(['application', 'documents'])
            ->whereHas('application', function($q) use ($user) {
                $q->where('user_id', $user->id)
                ->whereNotIn('status', ['approved', 'rejected', 'completed']);
            })
            ->whereNotIn('status', ['approved', 'rejected', 'completed'])
            ->orderBy('protocol_code')
            ->orderBy('revision_number', 'desc')
            ->get()
            ->unique('protocol_code');

        $revisionHistory = ResearchApplicationRevision::whereHas('application', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('revision_number', 'asc')
            ->get()
            ->map(function($rev) {
                return [
                    'record' => $rev->protocol_code,
                    'revision_number' => $rev->revision_number,
                    'status' => $rev->status,
                    'date' => $rev->created_at->format('F j, Y'),
                    'id' => 'RESUB-' . str_pad($rev->id, 3, '0', STR_PAD_LEFT)
                ];
            });

        $decisionLetters = DB::table('decision_letters')
            ->whereIn('protocol_code', $rawRevisions->pluck('protocol_code'))
            ->get()
            ->groupBy('protocol_code');

        $formattedRevisions = $rawRevisions->map(function ($rev) use ($decisionLetters) {
            $step = 2;
            $statusText = 'Documents Checking';

            switch ($rev->status) {
                case 'submitted':
                case 'under review':
                    $step = 2;
                    $statusText = 'Waiting for Review';
                    break;
                case 'incorrect':
                    $step = 2;
                    $statusText = 'Incorrect';
                    break;
                case 'review_finished':
                case 'processing_assessment':
                    $step = 3;
                    $statusText = 'Processing Assessment Forms';
                    break;
                case 'assessment_processed':
                case 'drafting_decision':
                case 'awaiting_chair_approval':
                    $step = 4;
                    $statusText = 'Drafting Decision Letter';
                    break;
                case 'minor_revision':
                case 'major_revision':
                case 'resubmit':
                case 'returned_for_revision':
                    $step = 5;
                    $statusText = 'Revision Required';
                    break;
                default:
                    $step = 2;
                    $statusText = 'Pending Secretariat Review';
                    break;
            }

            $docs = [];

            $docs[] = [
                'name' => 'Resubmission Form (System Generated)',
                'file' => route('researcher.resubmission.print', ['id' => $rev->id]),
                'size' => 'Auto',
                'type' => 'pdf'
            ];

            if ($decisionLetters->has($rev->protocol_code)) {
                $docs[] = [
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
                            $formattedSize = $this->formatSize(File::size($fullPath));
                        }
                    }

                    $docs[] = [
                        'name' => $doc->description,
                        'file' => $secureUrl,
                        'size' => $formattedSize,
                        'type' => strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION))
                    ];
                }
            }

            return [
                'id' => 'RESUB-' . str_pad($rev->id, 3, '0', STR_PAD_LEFT),
                'sourceId' => 'APP-' . str_pad($rev->application->id, 3, '0', STR_PAD_LEFT),
                'title' => $rev->application->research_title,
                'record' => $rev->protocol_code,
                'status' => $statusText,
                'date' => $rev->created_at->format('F j, Y'),
                'reviewer' => $rev->application->reviewer1_assigned ?? 'Secretariat',
                'studyType' => match ((int) $rev->application->type_of_research) {
                    1 => 'Faculty Research', 2 => 'Graduate School Research', 3 => 'Undergraduate Research',
                    4 => 'Integrated School Student Research', 5 => 'External Research', default => 'Not Specified',
                },
                'step' => $step,
                'docs' => $docs,
                'revision_number' => $rev->revision_number
            ];
        });

        return view('researcher.application_status_list', compact('user', 'formattedApps', 'formattedRevisions', 'revisionHistory'));
    }

    //this function handles the resubmission of documents for a specific research application. It first verifies that the application belongs to the authenticated user and then processes the uploaded files for both basic requirements and supplementary documents. The function organizes the uploaded files into a structured directory based on the protocol code and ensures that each file is saved with a unique name to prevent overwriting. It also updates the application's status to "submitted" and logs the resubmission action in the research application logs. If any errors occur during the process, it rolls back the transaction and returns an error message.
    //this is connected to the "Resubmit Documents" button on the application status page, which is shown when the application is in "incomplete_documents" status or when a revision has been requested by the reviewers (major_revision, minor_revision, resubmit, returned_for_revision)
    public function resubmitDocuments(Request $request, $protocol_code)
    {
        $app = ResearchApplications::where('protocol_code', $protocol_code)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();

        $baseFolderPath = 'documents/' . $protocol_code;
        $resubmitPath = $baseFolderPath . '/resubmit';
        $absolutePath = storage_path('app/' . $resubmitPath);

        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }

        $basicGroups = [
            'doc_letter_request'            => 'letter_request',
            'doc_endorsement_letter'        => 'endorsement_letter',
            'doc_full_proposal'             => 'full_proposal',
            'doc_technical_review_approval' => 'technical_review_approval',
            'doc_informed_consent'          => 'informed_consent',
            'doc_manuscript'                => 'manuscript',
        ];

        $supplementaryGroups = [
            'doc_curriculum_vitae'    => 'curriculum_vitae',
            'doc_questionnaire'       => 'questionnaire',
            'doc_data_collection'     => 'data_collection',
            'doc_product_brochure'    => 'product_brochure',
            'doc_philippine_fda'      => 'philippine_fda',
            'doc_special_populations' => 'special_populations',
            'doc_others'              => 'others'
        ];

        DB::beginTransaction();
        try {
            // --- 2. PROCESS BASIC REQUIREMENTS ---
            foreach ($basicGroups as $inputName => $dbType) {
                if ($request->hasFile($inputName)) {
                    $files = $request->file($inputName);
                    // Handle single file or array of files
                    $files = is_array($files) ? $files : [$files];
                    $langs = ($inputName === 'doc_informed_consent') ? $request->input('doc_informed_consent_lang', []) : [];

                    foreach ($files as $index => $file) {
                        $descText = (!empty($langs[$index])) ? $langs[$index] : ucwords(str_replace('_', ' ', $dbType));
                        $descText .= " (Resubmitted)";

                        $fileName = 'resubmit_' . $dbType . '_' . time() . '_' . $index . '.' . $file->extension();
                        $file->move($absolutePath, $fileName);

                        DB::table('basic_requirements')->insert([
                            'protocol_code' => $protocol_code,
                            'type'          => $dbType,
                            'description'   => $descText,
                            'file_path'     => $resubmitPath . '/' . $fileName,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ]);
                    }
                }
            }

            // --- 3. PROCESS SUPPLEMENTARY DOCUMENTS (This was missing!) ---
            foreach ($supplementaryGroups as $inputName => $dbType) {
                if ($request->hasFile($inputName)) {
                    $files = $request->file($inputName);
                    $files = is_array($files) ? $files : [$files];
                    $descriptions = $request->input($inputName . '_desc', []);

                    foreach ($files as $index => $file) {
                        $descText = (!empty($descriptions[$index])) ? $descriptions[$index] : ucwords(str_replace('_', ' ', $dbType));
                        $descText .= " (Resubmitted)";

                        $fileName = 'resubmit_' . $dbType . '_' . time() . '_' . $index . '.' . $file->extension();
                        $file->move($absolutePath, $fileName);

                        // Using DB::table for consistency, or use your SupplementaryDocument model
                        DB::table('supplementary_documents')->insert([
                            'protocol_code' => $protocol_code,
                            'type'          => $dbType,
                            'description'   => $descText,
                            'file_path'     => $resubmitPath . '/' . $fileName,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ]);
                    }
                }
            }

            $app->status = 'submitted';
            $app->save();

            DB::table('research_application_logs')->insert([
                'protocol_code' => $protocol_code,
                'user_id'       => auth()->id(),
                'status'        => 'submitted',
                'comment'       => 'Researcher provided updated documents (Identified by resubmit_ prefix).',
                'created_at'    => now(),
                'updated_at'    => now()
            ]);

            DB::commit();
            return back()->with('success', 'Documents resubmitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    //this function shows the application history page of the researcher, which lists all their research applications that are in terminal statuses (approved, rejected, disapproved, completed). The function fetches the relevant applications along with their supplementary documents and revisions. It then formats the applications to include both system-generated documents (like the application form, assessment form, informed consent form, and decision letter) and user-uploaded documents, ensuring that all file links are secure and that file sizes are displayed in a human-readable format. Additionally, it fetches all revisions for these applications to be used in the Version Hub Modal on the frontend. Finally, it passes the formatted applications and their revisions to the Blade view responsible for rendering the application history page.
    public function showApplicationHistory()
    {
        $user = auth()->user();

        // 1. FETCH ONLY TERMINAL APPLICATIONS (Approved or Rejected)
        $terminalStatuses = ['Approved', 'Disapproved', 'approved', 'rejected', 'completed'];

        $applications = ResearchApplications::with('supplementaryDocuments')
            ->where('user_id', $user->id)
            ->whereIn('status', $terminalStatuses)
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
                'name' => 'Application Form (System Generated)',
                'file' => route('researcher.application.print', ['id' => $app->id]),
                'size' => 'Auto',
                'type' => 'pdf'
            ];

            $docs[] = [
                'name' => 'Assessment Form (System Generated)',
                'file' => route('researcher.assessment.print', ['id' => $app->id]),
                'size' => 'Auto',
                'type' => 'pdf'
            ];

            if ($app->informedConsent) {
                $docs[] = [
                    'name' => 'Informed Consent Form (System Generated)',
                    'file' => route('researcher.informedconsent.print', ['id' => $app->id]),
                    'size' => 'Auto',
                    'type' => 'pdf'
                ];
            }

            // Check if the protocol is exempted
            if ($app->review_classification === 'Exempted') {
                $docs[] = [
                    'name' => 'Certificate of Exemption (System Generated)',
                    'file' => route('protocol.print-exemption', ['protocol_code' => $app->protocol_code]),
                    'size' => 'Auto',
                    'type' => 'pdf' // Keep as pdf/html depending on your frontend icon logic
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

        return view('researcher.application_history', compact('user', 'formattedApps', 'formattedRevisions', 'revisionHistory'));
    }

    //this function shows the resubmission form for a specific research application. It first checks if the application belongs to the authenticated user and then verifies if the application's status allows for resubmission. If the application is eligible, it prepares the necessary data, including the versioning logic and labels for the form fields, and then returns the view for the resubmission form. If the application is not marked for resubmission, it redirects the user back to the dashboard with an error message.
    //this is connected to the Resubmission Application when a protocol requires revision or resubmission, it will automatically appear in the modal of that protocol
    public function showResubmissionForm(Request $request, $protocol_code)
    {
        $user = auth()->user();

        $application = ResearchApplications::where('user_id', $user->id)
                        ->where('protocol_code', $protocol_code)
                        ->firstOrFail();

        // --- 1. Versioning & Status Logic (Moved UP to check before kicking out) ---
        $existingRevisions = DB::table('research_application_revisions')
            ->where('protocol_code', $protocol_code)
            ->orderBy('revision_number', 'desc')
            ->get();

        $latestRevision = $existingRevisions->first();

        // Check the REVISION table for the 'incorrect' status, since it doesn't exist on the main application
        $isFixingIncorrect = ($latestRevision && strtolower($latestRevision->status) === 'incorrect');

        // --- 2. Guard Clause ---
        $allowedAppStatuses = ['resubmit', 'minor_revision', 'major_revision', 'returned_for_revision'];

        // Allow them in if the main app needs revision OR if their latest resubmission was marked incorrect
        if (!in_array(strtolower($application->status), $allowedAppStatuses) && !$isFixingIncorrect) {
            return redirect()->route('dashboard')->with('error', 'This application is not marked for resubmission.');
        }

        // Set Version Number based on whether it's a new revision or fixing an incorrect one
        if ($isFixingIncorrect) {
            $nextVersionNumber = $latestRevision->revision_number;
        } else {
            $nextVersionNumber = $existingRevisions->count() + 1;
        }

        $autoVersion = "V" . $nextVersionNumber . " (" . now()->format('Y-m-d') . ")";

        // --- 3. Labels Dictionary ---
        $questionLabels = [

            // 1. SCIENTIFIC DESIGN

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

            '1.11' => 'Refusal to participate or discontinuance – Refusal to participate or discontinuance at any time will involve penalty or loss of benefits to which the subject is entitled',

            '1.12' => 'Statement that the study involves research',

            '1.13' => 'Approximate number of participants in the study',

            '1.14' => 'Expected benefits – Expected benefits to the community or to society, or contributions to scientific knowledge',

            '1.15' => 'Post-study access – Description of post-study access to the study product or intervention that have been proven safe and effective',

            '1.16' => 'Anticipated payment – Anticipated payment, if any, to the participant in the course of the study whether money or other forms of material goods, and if so, the kind and amount',

            '1.17' => 'Anticipated expenses – Anticipated expenses, if any, to the participant in the course of the study',

            '1.18' => 'Direct access to medical records – Statement that the study monitor(s), auditor(s), the BERC, and regulatory authorities will be granted direct access to participant\'s medical records for purposes ONLY of verification of clinical trial procedures and data',


            // 2. CONDUCT OF STUDY

            '2.1' => 'Specimen handling – Review of specimen storage, access, disposal, and terms of use',

            '2.2' => 'PI qualifications – Review of CV and relevant certifications to ascertain capability to manage study related risks',

            '2.3' => 'Suitability of site – Review of adequacy of qualified staff and infrastructures',

            '2.4' => 'Duration – Review of length/extent of human participant involvement in the study',


            // 3. ETHICAL CONSIDERATIONS

            '3.1' => 'Conflict of interest – Review of management of conflict arising from financial, familial, or proprietary considerations of the PI, sponsor, or the study site',

            '3.2' => 'Privacy and confidentiality – Review of measures or guarantees to protect privacy and confidentiality of participant information as indicated by data collection methods including data protection plans',

            '3.3' => 'Informed consent process – Review of application of the principle of respect for persons, who may solicit consent, how and when it will be done; who may give consent especially in case of special populations like minors and those who are not legally competent to give consent, or indigenous people which require additional clearances',

            '3.4' => 'Vulnerable study populations – Review of involvement of vulnerable study populations and impact on informed consent. Vulnerable groups include children, the elderly, ethnic and racial minority groups, the homeless, prisoners, people with incurable disease, people who are politically powerless, or junior members of a hierarchical group',

            '3.5' => 'Recruitment methods – Review of manner of recruitment including appropriateness of identified recruiting parties',

            '3.6' => 'Assent requirements – Review of feasibility of obtaining assent vis à vis incompetence to consent; Review of applicability of the assent age brackets in children (0-under 7: No assent; 7-under 12: Verbal Assent; 12-under 15: Simplified Assent Form; 15-under 18: Co-sign informed consent form)',

            '3.7' => 'Risks and mitigation – Review of level of risk and measures to mitigate these risks (including physical, psychological, social, economic), including plans for adverse event management; Review of justification for allowable use of placebo as detailed in the Declaration of Helsinki',

            '3.8' => 'Benefits – Review of potential direct benefit to participants; the potential to yield generalizable knowledge about the participants\' condition/problem; non-material compensation to participant (health education or other creative benefits), where no clear, direct benefit from the project will be received by the participant',

            '3.9' => 'Financial compensation – Review of amount and method of compensations, financial incentives, or reimbursement of study-related expenses',

            '3.10' => 'Community impact – Review of impact of the research on the community where the research occurs and/or to whom findings can be linked; including issues like stigma or draining of local capacity; sensitivity to cultural traditions, and involvement of the community in decisions about the conduct of study',

            '3.11' => 'Collaborative studies – Review in terms of collaborative study especially in case of multi-country/multi-institutional studies, including intellectual property rights, publication rights, information and responsibility sharing, transparency, and capacity building',


            // 4. INFORMED CONSENT

            '4.1' => 'Purpose of the study',

            '4.2' => 'Expected duration of participation',

            '4.3' => 'Procedures to be carried out',

            '4.4' => 'Discomforts and inconveniences',

            '4.5' => 'Risks (including possible discrimination)',

            '4.6' => 'Random assignment to the trial treatments',

            '4.7' => 'Benefits to the participants',

            '4.8' => 'Alternative treatments procedures',

            '4.9' => 'Compensation and / or medical treatments in case of injury',

            '4.10' => 'Who to contact for pertinent questions and or for assistance in a research-related injury',

            '4.11' => 'Refusal to participate or discontinuance at any time will involve penalty or loss of benefits to which the subject is entitled',

            '4.12' => 'Statement that it involves research',

            '4.13' => 'Approximate number of participants in the study',

            '4.14' => 'Expected benefits to the community or to society, or contributions to scientific knowledge',

            '4.15' => 'Description of post-study access to the study product or intervention that have been proven safe and effective',

            '4.16' => 'Anticipated payment, if any, to the participant in the course of the study; whether money or other forms of material goods, and if so, the kind and amount',

            '4.17' => 'Anticipated expenses, if any, to the participant in the course of the study',

            '4.18' => 'Statement that the study monitor(s), auditor(s), the BERC, and regulatory authorities will be granted direct access to participant\'s medical records for purposes ONLY of verification of clinical trial procedures and data',

            '4.19' => 'Statement describing extent of participant\'s right to access his/her records (or lack thereof vis à vis pending request for approval of non or partial disclosure)',

            '4.20' => 'Description of policy regarding the use of genetic tests and familial genetic information, and the precautions in place to prevent disclosure of results to immediate family relative or to others without consent of the participant',

            '4.21' => 'Possible direct or secondary use of participant\'s medical records and biological specimens taken in the course of clinical care or in the course of this study',

            '4.22' => 'Plans to destroy collected biological specimen at the end of the study; if not, details about storage (duration, type of storage facility, location, access information) and possible future use; affirming participant\'s right to refuse future use, refuse storage, or have the materials destroyed',

            '4.23' => 'Plans to develop commercial products from biological specimens and whether the participant will receive monetary or other benefit from such development',

            '4.24' => 'Statement that the BERC has approved the study and may be reached for information regarding participant rights, grievances, and complaints'
        ];

        $sectionTitles = [
            1 => 'Scientific Design',
            2 => 'Conduct of Study',
            3 => 'Ethical Consideration',
            4 => 'Informed Consent'
        ];

        $rawData = collect();

        // ─── 4. DATA FETCHING SCENARIOS ───

        // SCENARIO A: Fixing an 'incorrect' submission
        // Pull their EXISTING answers and the Secretariat's rejection comment
        if ($isFixingIncorrect) {
            $rawData = DB::table('revision_responses')
                ->where('protocol_code', $protocol_code)
                ->where('revision_number', $latestRevision->revision_number)
                ->select(
                    'item as question_number',
                    'berc_recommendation as synthesized_comments',
                    'researcher_response',
                    'section_and_page',
                    'secretariat_comment'
                )->get();
        }
        // SCENARIO B: FIRST RESUBMISSION (V1)
        // Pull from initial assessment synthesize columns
        elseif ($existingRevisions->count() === 0) {
            $generalItems = DB::table('assessment_form_items')
                ->join('assessment_forms', 'assessment_form_items.assessment_form_id', '=', 'assessment_forms.id')
                ->where('assessment_forms.protocol_code', $protocol_code)
                ->where('assessment_form_items.synthesized_comments_action_required', true)
                ->select('assessment_form_items.question_number', 'assessment_form_items.synthesized_comments')
                ->get();

            $icfItems = DB::table('icf_assessment_items')
                ->join('icf_assessments', 'icf_assessment_items.icf_assessment_id', '=', 'icf_assessments.id')
                ->where('icf_assessments.protocol_code', $protocol_code)
                ->where('icf_assessment_items.synthesized_comments_action_required', true)
                ->select('icf_assessment_items.question_number', 'icf_assessment_items.synthesized_comments')
                ->get();

            $rawData = $generalItems->concat($icfItems);
        }
        // SCENARIO C: SUBSEQUENT RESUBMISSIONS (V2+)
        // Pull from the previous revision's response table where Reviewers demanded more action
        else {
            $lastRevisionNumber = $latestRevision->revision_number;

            $rawData = DB::table('revision_responses')
                ->where('protocol_code', $protocol_code)
                ->where('revision_number', $lastRevisionNumber)
                ->where('synthesized_comments_action', 'action_required')
                ->select('item as question_number', 'synthesized_comments')
                ->get();
        }

        // --- 5. Format for View ---
        $assessmentItems = $rawData
            ->sortBy('question_number', SORT_NATURAL)
            ->values()
            ->map(function($item) use ($questionLabels, $sectionTitles) {
                $num = $item->question_number;
                $secId = (int)explode('.', $num)[0];

                return (object) [
                    'section_name'         => $sectionTitles[$secId] ?? 'General Revisions',
                    'section_id'           => $secId,
                    'full_recommendation'  => "Item {$num} (" . ($questionLabels[$num] ?? 'Requirement') . ")\n\nComments: {$item->synthesized_comments}",
                    'question_number'      => $num,
                    'synthesized_comments' => $item->synthesized_comments,
                    'label_detail'         => $questionLabels[$num] ?? 'Requirement',

                    // Attach pre-filled data if fixing an incorrect revision
                    'existing_response'    => $item->researcher_response ?? '',
                    'existing_section'     => $item->section_and_page ?? '',
                    'secretariat_comment'  => $item->secretariat_comment ?? null
                ];
            });

        return view('researcher.resubmission_form', compact('user', 'application', 'assessmentItems', 'autoVersion', 'isFixingIncorrect'));
    }

    //function to submit the resubmission form. It validates the incoming data, determines whether the submission is a new revision or a fix for an 'incorrect' status, and then either updates the existing revision or creates a new one accordingly. The function also handles the associated RevisionResponse entries based on the recommendations and responses provided in the form. Finally, it commits the transaction and redirects the user back with a success message, or rolls back in case of any errors.
    public function submitResubmissionForm(Request $request)
    {
        // 1. Validate the incoming data
        $validated = $request->validate([
            'protocol_code' => 'required|exists:research_applications,protocol_code',
            'version' => 'required|string',
            'items' => 'required|array|min:1',
            'recommendations' => 'required|array|min:1',
            'responses' => 'required|array|min:1',
            'section_pages' => 'required|array|min:1',
            'revised_manuscript' => 'file|mimes:pdf,doc,docx|max:10240',
            'icf_languages' => 'nullable|array',
            'icf_languages.*' => 'nullable|string',
            'icf_files' => 'nullable|array',
            'icf_files.*' => 'file|mimes:pdf,doc,docx|max:10240',
            'other_descriptions' => 'nullable|array',
            'other_descriptions.*' => 'nullable|string',
            'other_files' => 'nullable|array',
            'other_files.*' => 'file|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $application = ResearchApplications::where('protocol_code', $request->protocol_code)->firstOrFail();
            $user = auth()->user();

            // 2. Determine Current Revision State
            $latestRevision = ResearchApplicationRevision::where('protocol_code', $application->protocol_code)
                ->orderBy('revision_number', 'desc')
                ->first();

            $isFixingIncorrect = ($latestRevision && strtolower($latestRevision->status) === 'incorrect');

            // 3. FETCH ORIGINAL REVIEWER IDs & USER INFO
            $assignedReviewers = DB::table('application_reviewer')
                ->join('reviewers', 'application_reviewer.reviewer_id', '=', 'reviewers.id')
                ->leftJoin('users', 'reviewers.user_id', '=', 'users.id')
                ->where('application_reviewer.protocol_code', $application->protocol_code)
                ->where('application_reviewer.status', 'Accepted')
                ->orderBy('application_reviewer.id', 'asc')
                ->select('reviewers.id as rev_id', 'users.id as user_id', 'users.name as user_name')
                ->get();

            $rev1 = $assignedReviewers->get(0)->rev_id ?? null;
            $rev2 = $assignedReviewers->get(1)->rev_id ?? null;
            $rev3 = $assignedReviewers->get(2)->rev_id ?? null;

            // 4. BRANCH LOGIC: Update vs Create
            if ($isFixingIncorrect) {
                $currentRevisionNumber = $latestRevision->revision_number;
                $revision = $latestRevision;

                // Update status back to 'submitted' so the Secretariat can re-validate it
                // Clear the old secretariat comment since they are submitting a fix
                $revision->update([
                    'status' => 'submitted',
                    'secretariat_comment' => null
                ]);

                // Update existing RevisionResponse rows instead of creating new ones
                foreach ($request->recommendations as $index => $recommendation) {
                    if (!empty($recommendation) && !empty($request->responses[$index])) {
                        RevisionResponse::where('protocol_code', $application->protocol_code)
                            ->where('revision_number', $currentRevisionNumber)
                            ->where('item', $request->items[$index] ?? null)
                            ->update([
                                'researcher_response' => $request->responses[$index],
                                'section_and_page' => $request->section_pages[$index] ?? 'N/A',
                            ]);
                    }
                }

                // NOTE: We no longer unconditionally delete all documents here!

            } else {
                // NORMAL FLOW: Create an entirely new revision (V1, V2, V3)
                $currentRevisionCount = $application->revisions()->count();
                $currentRevisionNumber = $currentRevisionCount + 1;

                if ($currentRevisionNumber > 3) {
                    return back()->with('error', 'Maximum of 3 revisions allowed.');
                }

                $revision = ResearchApplicationRevision::create([
                    'protocol_code' => $application->protocol_code,
                    'revision_number' => $currentRevisionNumber,
                    'status' => 'submitted',
                ]);

                // Insert brand new RevisionResponse rows
                foreach ($request->recommendations as $index => $recommendation) {
                    if (!empty($recommendation) && !empty($request->responses[$index])) {
                        RevisionResponse::create([
                            'protocol_code' => $application->protocol_code,
                            'revision_number' => $currentRevisionNumber,
                            'item' => $request->items[$index] ?? null,
                            'berc_recommendation' => $recommendation,
                            'researcher_response' => $request->responses[$index],
                            'section_and_page' => $request->section_pages[$index] ?? 'N/A',
                            'reviewer1_id' => $rev1,
                            'reviewer2_id' => $rev2,
                            'reviewer3_id' => $rev3,
                            'reviewer1_done' => false,
                            'reviewer2_done' => false,
                            'reviewer3_done' => false,
                        ]);
                    }
                }
            }

            // 5. ─── PROCESS FILE UPLOADS (Selective Deletion) ───
            $uploadDirectory = 'documents/' . $application->protocol_code . '/v' . $currentRevisionNumber;

            // Manuscript
            if ($request->hasFile('revised_manuscript')) {
                // Only wipe the old manuscript IF they are uploading a replacement
                if ($isFixingIncorrect) {
                    DB::table('revision_documents')->where('revision_id', $revision->id)->where('type', 'manuscript')->delete();
                }

                $manuscriptPath = $request->file('revised_manuscript')->store($uploadDirectory);
                DB::table('revision_documents')->insert([
                    'revision_id' => $revision->id,
                    'type' => 'manuscript',
                    'description' => 'Revised Manuscript',
                    'file_path' => $manuscriptPath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // ICFs
            if ($request->has('icf_languages') && $request->hasFile('icf_files')) {
                // Only wipe the old ICFs IF they are uploading new ones
                if ($isFixingIncorrect) {
                    DB::table('revision_documents')->where('revision_id', $revision->id)->where('type', 'informed_consent')->delete();
                }

                $languages = $request->icf_languages;
                $files = $request->file('icf_files');
                foreach ($files as $index => $file) {
                    if ($file->isValid() && !empty($languages[$index])) {
                        $icfPath = $file->store($uploadDirectory);
                        DB::table('revision_documents')->insert([
                            'revision_id' => $revision->id,
                            'type' => 'informed_consent',
                            'description' => 'ICF - ' . $languages[$index],
                            'file_path' => $icfPath,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Other Documents
            if ($request->has('other_descriptions') && $request->hasFile('other_files')) {
                // Only wipe the old 'other' docs IF they are uploading new ones
                if ($isFixingIncorrect) {
                    DB::table('revision_documents')->where('revision_id', $revision->id)->where('type', 'other')->delete();
                }

                $descriptions = $request->other_descriptions;
                $otherFiles = $request->file('other_files');

                foreach ($otherFiles as $index => $file) {
                    if ($file->isValid() && !empty($descriptions[$index])) {
                        $otherPath = $file->store($uploadDirectory);
                        DB::table('revision_documents')->insert([
                            'revision_id' => $revision->id,
                            'type' => 'other',
                            'description' => $descriptions[$index],
                            'file_path' => $otherPath,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // 6. Update Application Status
            $application->update(['status' => 'resubmit']);

            // 7. ─── LOG TO PROTOCOL ROUTING LOGS PER REVIEWER ───
            $logNature = $isFixingIncorrect
                ? 'Resubmission Form Corrected (Version ' . $currentRevisionNumber . ')'
                : 'Resubmission Form (Version ' . $currentRevisionNumber . ')';

            if ($assignedReviewers->isNotEmpty()) {
                foreach ($assignedReviewers as $reviewer) {
                    DB::table('protocol_routing_logs')->insert([
                        'protocol_code'   => $application->protocol_code,
                        'document_nature' => $logNature,
                        'from_name'       => $user->name,
                        'from_user_id'    => $user->id,
                        'to_name'         => $reviewer->user_name ?? 'Reviewer',
                        'to_user_id'      => $reviewer->user_id ?? $reviewer->rev_id,
                        'remarks'         => 'Researcher submitted a revised protocol.',
                        'created_at'      => now(),
                        'updated_at'      => now()
                    ]);
                }
            } else {
                // Fallback: If no reviewers are active, send to Secretariat
                DB::table('protocol_routing_logs')->insert([
                    'protocol_code'   => $application->protocol_code,
                    'document_nature' => $logNature,
                    'from_name'       => $user->name,
                    'from_user_id'    => $user->id,
                    'to_name'         => 'Secretariat',
                    'to_user_id'      => null,
                    'remarks'         => 'Researcher submitted a revised protocol.',
                    'created_at'      => now(),
                    'updated_at'      => now()
                ]);
            }

            DB::commit();
            return redirect()->route('application.status', ['submitted' => $application->protocol_code]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving resubmission: ' . $e->getMessage());
        }
    }
}
