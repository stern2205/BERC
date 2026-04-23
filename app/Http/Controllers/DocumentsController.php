<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ResearchApplications;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DocumentsController extends Controller
{
    // helper function to consistently display the same documents all throughout all pages
    // this is called on all modals that requires fetching documents, such as the decision letter view, revision decision letter view, and the document viewer modal itself. It fetches all documents related to a protocol code from both basic_requirements and supplementary_documents tables, organizes them into groups by their type, and returns a structured JSON response that the frontend can use to display the documents in a consistent format across all pages.
    // this is an api call that the document viewer modals will call to get all documents for a given protocol code, and it returns a structured JSON response that includes the application details, payment information, and grouped documents with secure URLs for viewing.
    public function show($protocol_code)
    {
        try {
            // 1. Fetch the application with relations
            $application = ResearchApplications::with(['payment', 'user', 'logs.user'])
                ->where('protocol_code', $protocol_code)
                ->firstOrFail();

            // 2. Fetch Requirements from the NEW tables
            $basicDocs = DB::table('basic_requirements')
                ->where('protocol_code', $protocol_code)
                ->orderBy('created_at', 'desc')
                ->get();

            $suppDocs = DB::table('supplementary_documents')
                ->where('protocol_code', $protocol_code)
                ->orderBy('created_at', 'desc')
                ->get();

            // 3. HELPER: Unified Secure URL Generator
            $getSecureUrl = function($dbPath) use ($protocol_code) {
                if (empty($dbPath)) return null;

                // Clean the path to just get the filename/subfolder
                $cleanPath = str_replace('documents/'.$protocol_code.'/', '', $dbPath);

                return $cleanPath !== '' ? route('view.document', [
                    'protocol_code' => $protocol_code,
                    'filename' => $cleanPath
                ]) : null;
            };

            // 4. Organize documents into the Groups our Alpine JS expects
            $documentGroups = [];

            $processDoc = function($doc) use ($getSecureUrl) {
                return [
                    'id' => $doc->id,
                    'url' => $getSecureUrl($doc->file_path),
                    'description' => $doc->description ?? 'View File'
                ];
            };

            foreach ($basicDocs as $doc) {
                if (!isset($documentGroups[$doc->type])) $documentGroups[$doc->type] = [];
                $documentGroups[$doc->type][] = $processDoc($doc);
            }

            foreach ($suppDocs as $doc) {
                if (!isset($documentGroups[$doc->type])) $documentGroups[$doc->type] = [];
                $documentGroups[$doc->type][] = $processDoc($doc);
            }

            // 5. Format Payment securely
            $paymentData = null;
            if ($application->payment) {
                $paymentData = [
                    'payment_method' => $application->payment->payment_method,
                    'reference_number' => $application->payment->reference_number,
                    'proof_url' => $getSecureUrl($application->payment->proof_of_payment_path)
                ];
            }

            // 6. Return Universal JSON format
            return response()->json([
                'id' => $application->id,
                'protocol_code' => $application->protocol_code,
                'research_title' => $application->research_title,
                'primary_researcher' => $application->primary_researcher ?? $application->name_of_researcher,
                'payment' => $paymentData,
                'documents' => $documentGroups // Sends grouped versions!
            ]);

        } catch (\Exception $e) {
            Log::error("Unified Document Fetch Error: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error', 'details' => $e->getMessage()], 500);
        }
    }

    // this is a function that renders the decision letter iso form into a printable pdf
    public function viewDecisionPdf($protocol_code)
    {
        $application = ResearchApplications::where('protocol_code', $protocol_code)->firstOrFail();

        $decisionLetter = DB::table('decision_letters')
                            ->where('protocol_code', $protocol_code)
                            ->first();

        if (!$decisionLetter) {
            abort(404, 'Decision letter has not been drafted yet.');
        }

        // --- 1. DECODE & MAP DOCUMENT NAMES ---
        $decoded = json_decode($decisionLetter->documents, true);
        $rawDocuments = [];

        if (is_array($decoded)) {
            $rawDocuments = $decoded;
        } elseif (is_string($decisionLetter->documents) && !empty($decisionLetter->documents)) {
            $rawDocuments = explode(',', $decisionLetter->documents);
        }

        $documentDictionary = [
            'LETTER' => 'Letter of Request',
            'ENDORSEMENT' => 'Endorsement Letter',
            'PROPOSAL' => 'Full Research Proposal',
            'QUESTIONNAIRE' => 'Data Gathering Tool / Questionnaire',
            'DATACOLLECTION' => 'Data Collection Procedures',
            'MANUSCRIPT' => 'Manuscript',
            'TECHNICALREVIEWAPPROVAL' => 'Technical Review Approval',
            'CV' => 'Curriculum Vitae',
            'CONSENT EN' => 'Informed Consent Form (English)',
            'CONSENT PH' => 'Informed Consent Form (Filipino)',
            'BROCHURE' => 'Product Brochure',
            'FDA' => 'Philippine FDA Approval'
        ];

        $documents = [];
        foreach ($rawDocuments as $doc) {
            $cleanDoc = trim(trim($doc, '"\'[]\\')); // Strip any weird JSON remnants
            if (!empty($cleanDoc)) {
                $documents[] = $documentDictionary[$cleanDoc] ?? $cleanDoc;
            }
        }

        // Define the labels for all possible items
        $assessmentLabels = [
            // Section 1: Scientific Design
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

            // Section 2: Conduct of Study
            '2.1' => 'Specimen handling – Review of specimen storage, access, disposal, and terms of use',
            '2.2' => 'Principal Investigator qualifications – Review of CV and relevant certifications to ascertain capability to manage study related risks',
            '2.3' => 'Suitability of site – Review of adequacy of qualified staff and infrastructures',
            '2.4' => 'Duration – Review of length/extent of human participant involvement in the study',

            // Section 3: Ethical Considerations
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

            // Section 4: Informed Consent
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

        // Fetch and map Assessment Items
        $assessmentActionItems = DB::table('assessment_form_items')
            ->join('assessment_forms', 'assessment_form_items.assessment_form_id', '=', 'assessment_forms.id')
            ->where('assessment_forms.protocol_code', $protocol_code)
            ->where('assessment_form_items.synthesized_comments_action_required', 1)
            ->get()
            ->map(function($item) use ($assessmentLabels) {
                $num = (string)$item->question_number;
                $section = (int)explode('.', $num)[0];

                $sectionNames = [1 => '1. Scientific Design', 2 => '2. Conduct of Study', 3 => '3. Ethical Consideration'];

                return (object)[
                    'raw_number' => $num, // Kept for sorting
                    'section' => $sectionNames[$section] ?? 'Other',
                    'points' => "Item {$num}: " . ($assessmentLabels[$num] ?? 'Requirement'),
                    'synthesizedComments' => $item->synthesized_comments
                ];
            });

        // Fetch and map ICF Items
        $icfActionItems = DB::table('icf_assessment_items')
            ->join('icf_assessments', 'icf_assessment_items.icf_assessment_id', '=', 'icf_assessments.id')
            ->where('icf_assessments.protocol_code', $protocol_code)
            ->where('icf_assessment_items.synthesized_comments_action_required', 1)
            ->get()
            ->map(function($item) use ($assessmentLabels) {
                $num = (string)$item->question_number;
                return (object)[
                    'raw_number' => $num, // Kept for sorting
                    'section' => '4. Informed Consent',
                    'points' => "Item {$num}: " . ($assessmentLabels[$num] ?? 'Requirement'),
                    'synthesizedComments' => $item->synthesized_comments
                ];
            });

        // --- 2. MERGE AND STRICTLY SORT ---
        $actionItems = $assessmentActionItems->concat($icfActionItems)->sort(function($a, $b) {
            $aParts = explode('.', $a->raw_number);
            $bParts = explode('.', $b->raw_number);

            // If sections match (e.g. 1.10 and 1.2), sort by the sub-number mathematically
            if ((int)$aParts[0] === (int)$bParts[0]) {
                return (int)($aParts[1] ?? 0) <=> (int)($bParts[1] ?? 0);
            }
            // Otherwise, sort by section
            return (int)$aParts[0] <=> (int)$bParts[0];
        })->values();

        $finalDecisionLog = DB::table('research_application_logs')
                            ->where('protocol_code', $protocol_code)
                            ->whereIn('status', ['approved', 'resubmit', 'rejected'])
                            ->orderBy('created_at', 'desc')
                            ->first();

        $chairUser = $finalDecisionLog ? \App\Models\User::find($finalDecisionLog->user_id) : null;

        return view('forms.decisionletter', compact(
            'application',
            'decisionLetter',
            'documents',
            'actionItems',
            'chairUser'
        ));
    }

    //this is a function that renders the revision decision letter iso form into a printable pdf, similar to the main decision letter view but with some differences in the data it fetches and displays, such as showing the specific version of the revision decision letter, and including any additional comments or requirements that are specific to the revision process. It also uses the same document mapping logic to ensure that any documents associated with the revision decision are displayed with user-friendly names in the view.
    public function viewRevisionDecisionPdf($protocol_code, $version)
    {
        // 1. Get Main Application
        $application = ResearchApplications::where('protocol_code', $protocol_code)->firstOrFail();

        // 2. Fetch the specific Revision Decision Letter
        $decisionLetter = DB::table('revision_decision_letters')
            ->where('protocol_code', $protocol_code)
            ->where('version_number', (string) $version)
            ->first();

        if (!$decisionLetter) {
            abort(404, "Decision letter for Version {$version} has not been drafted yet.");
        }

        // --- 3. DECODE & MAP DOCUMENT NAMES ---
        $decoded = json_decode($decisionLetter->documents, true);
        $rawDocuments = [];

        if (is_array($decoded)) {
            $rawDocuments = $decoded;
        } elseif (is_string($decisionLetter->documents) && !empty($decisionLetter->documents)) {
            $rawDocuments = explode(',', $decisionLetter->documents);
        }

        $documentDictionary = [
            'LETTER' => 'Letter of Request',
            'ENDORSEMENT' => 'Endorsement Letter',
            'PROPOSAL' => 'Full Research Proposal',
            'QUESTIONNAIRE' => 'Data Gathering Tool / Questionnaire',
            'DATACOLLECTION' => 'Data Collection Procedures',
            'MANUSCRIPT' => 'Manuscript',
            'TECHNICALREVIEWAPPROVAL' => 'Technical Review Approval',
            'CV' => 'Curriculum Vitae',
            'CONSENT EN' => 'Informed Consent Form (English)',
            'CONSENT PH' => 'Informed Consent Form (Filipino)',
            'BROCHURE' => 'Product Brochure',
            'FDA' => 'Philippine FDA Approval'
        ];

        $documents = [];
        foreach ($rawDocuments as $doc) {
            $cleanDoc = trim(trim($doc, '"\'[]\\')); // Strip any weird JSON remnants
            if (!empty($cleanDoc)) {
                $documents[] = $documentDictionary[$cleanDoc] ?? $cleanDoc;
            }
        }

        // 4. Dictionaries to match your view's expected format
        $assessmentLabels = [
            // Section 1: Scientific Design
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

            // Section 2: Conduct of Study
            '2.1' => 'Specimen handling – Review of specimen storage, access, disposal, and terms of use',
            '2.2' => 'Principal Investigator qualifications – Review of CV and relevant certifications to ascertain capability to manage study related risks',
            '2.3' => 'Suitability of site – Review of adequacy of qualified staff and infrastructures',
            '2.4' => 'Duration – Review of length/extent of human participant involvement in the study',

            // Section 3: Ethical Considerations
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

            // Section 4: Informed Consent
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

        $sectionNames = [
            1 => '1. Scientific Design',
            2 => '2. Conduct of Study',
            3 => '3. Ethical Consideration',
            4 => '4. Informed Consent'
        ];

        // 5. --- FETCH & STRICTLY SORT ACTION ITEMS ---
        $actionItems = DB::table('revision_responses')
            ->where('protocol_code', $protocol_code)
            ->where('revision_number', $version)
            ->where(function($query) {
                $query->where('synthesized_comments_action', 'true')
                    ->orWhere('synthesized_comments_action', '1')
                    ->orWhere('synthesized_comments_action', 'action_required');
            })
            ->get()
            ->map(function($itemRow) use ($assessmentLabels, $sectionNames) {
                $num = (string) $itemRow->item; // e.g., '1.1' or '4.5'
                $sectionId = (int) explode('.', $num)[0];

                return (object)[
                    'raw_number' => $num, // Kept for sorting
                    'section' => $sectionNames[$sectionId] ?? 'Other',
                    'points' => "Item {$num}: " . ($assessmentLabels[$num] ?? 'Requirement'),
                    'synthesizedComments' => $itemRow->synthesized_comments
                ];
            })
            ->sort(function($a, $b) {
                $aParts = explode('.', $a->raw_number);
                $bParts = explode('.', $b->raw_number);

                // If sections match, sort by the sub-number mathematically
                if ((int)$aParts[0] === (int)$bParts[0]) {
                    return (int)($aParts[1] ?? 0) <=> (int)($bParts[1] ?? 0);
                }
                // Otherwise, sort by section
                return (int)$aParts[0] <=> (int)$bParts[0];
            })->values();

        // 6. ─── VERSIONED CHAIR SIGNATURE LOGIC ───
        $routingLog = DB::table('protocol_routing_logs')
            ->where('protocol_code', $protocol_code)
            ->where('document_nature', "Final Decision Letter (Version {$version})")
            ->first();

        $chairUser = $routingLog ? \App\Models\User::find($routingLog->from_user_id) : null;

        return view('forms.decisionletter', compact(
            'application',
            'decisionLetter',
            'documents',
            'actionItems',
            'chairUser',
            'assessmentLabels',
            'version'
        ));
    }

    //this is a function that renders the individual assessment form each separate reviewer. this is only shown to the berc staff, which is accessed in the history
    public function printIndividualAssessmentFormPDF($id, $reviewer_id)
    {
        // 1. Load application and relationships
        $application = ResearchApplications::with([
            'supplementaryDocuments',
            'logs.user',
            'assessmentForm.items'
        ])->findOrFail($id);

        $assessmentForm = $application->assessmentForm;

        if (!$assessmentForm) {
            return abort(404, "Assessment Form not found.");
        }

        // 2. Identify the slot (1, 2, or 3) for this reviewer_id
        $slot = null;
        if ($assessmentForm->reviewer_1_id == $reviewer_id) $slot = 1;
        elseif ($assessmentForm->reviewer_2_id == $reviewer_id) $slot = 2;
        elseif ($assessmentForm->reviewer_3_id == $reviewer_id) $slot = 3;

        if (!$slot) {
            return abort(403, "Reviewer not assigned to this protocol.");
        }

        $latestLog = $application->logs->sortByDesc('created_at')->first();

        // 3. Process items using the specific Reviewer's columns
        $items = $assessmentForm->items->map(function($item) use ($slot) {
            $commentField = "reviewer_{$slot}_comments";
            $actionField  = "reviewer_{$slot}_action_required";

            $baseComment = $item->$commentField ?? '';

            if ($item->$actionField) {
                $item->final_comment = "ACTION REQUIRED: " . $baseComment;
            } else {
                $item->final_comment = $baseComment;
            }

            return $item;
        })->keyBy('question_number');

        // 4. Return the same view with same variable names
        return view('forms.individualassessmentform', compact('application', 'assessmentForm', 'latestLog', 'items'));
    }

    //this is a function that renders the individual informed consent form each separate reviewer. this is only shown to the berc staff, which is accessed in the history
    //this is only shown to the berc staff, which is accessed in the history
    public function printIndividualInformedConsentPDF($id, $reviewer_id)
    {
        // 1. Load application and relationships
        $application = ResearchApplications::with([
            'supplementaryDocuments',
            'logs.user',
            'informedConsent.items'
        ])->findOrFail($id);

        $consentForm = $application->informedConsent;

        if (!$consentForm) {
            return abort(404, "Informed Consent Form not found.");
        }

        // 2. Identify the slot (1, 2, or 3)
        $slot = null;
        if ($consentForm->reviewer_1_id == $reviewer_id) $slot = 1;
        elseif ($consentForm->reviewer_2_id == $reviewer_id) $slot = 2;
        elseif ($consentForm->reviewer_3_id == $reviewer_id) $slot = 3;

        if (!$slot) {
            return abort(403, "Reviewer not assigned to this protocol.");
        }

        $latestLog = $application->logs->sortByDesc('created_at')->first();

        // 3. Process items using specific Reviewer's columns
        $items = $consentForm->items->map(function($item) use ($slot) {
            $commentField = "reviewer_{$slot}_comments";
            $actionField  = "reviewer_{$slot}_action_required";

            $baseComment = $item->$commentField ?? '';

            if ($item->$actionField) {
                $item->final_comment = "ACTION REQUIRED: " . $baseComment;
            } else {
                $item->final_comment = $baseComment;
            }

            return $item;
        })->keyBy('question_number');

        // 4. Return existing view
        return view('forms.informedconsent', compact('application', 'consentForm', 'latestLog', 'items'));
    }

    //this function renders the logbook of communications for outgoing correspondences, which typically involve communications from BERC staff to external parties such as researchers or proponents. It fetches the relevant routing logs for the specified protocol code, filters them to include only those where the recipient is an external party, and then maps the data to a format suitable for display in the Blade view. The function also checks for the presence of electronic signatures for both the sender (BERC staff) and the recipient (external party) to indicate whether the communication was signed by either party.
    //this can only be seen by the berc staff which is accessible in the history page
    public function printOutgoingLogbook($protocol_code)
    {
        // 1. Fetch routing logs with signature data
        $routingLogs = DB::table('protocol_routing_logs')
            ->leftJoin('users as sender', 'protocol_routing_logs.from_user_id', '=', 'sender.id')
            ->leftJoin('users as recipient', 'protocol_routing_logs.to_user_id', '=', 'recipient.id')
            ->where('protocol_routing_logs.protocol_code', $protocol_code)
            // Filtering for outgoing: usually sent to researchers or external parties
            ->whereIn('recipient.role', ['researcher', 'extconsultant', 'Researcher', 'ExtConsultant', 'proponent'])
            ->select(
                'protocol_routing_logs.*',
                'sender.e_signature as sender_sig',
                'recipient.e_signature as recipient_sig'
            )
            ->orderBy('protocol_routing_logs.id', 'asc')
            ->get();

        // 2. Map the entries for the Blade
        $logbookEntries = $routingLogs->map(function ($log) {
            return [
                'date'              => \Carbon\Carbon::parse($log->created_at)->format('M d, Y / h:i A'),
                'nature'            => $log->document_nature,

                // Signatory is the person who sent/signed it (BERC Staff)
                'signatory'         => $log->from_name,
                'signatory_id'      => $log->from_user_id,
                'has_signatory_sig' => !empty($log->sender_sig),

                'addressee'         => $log->to_name,

                // Received By is the external person (Recipient)
                'received_by'       => $log->to_name,
                'received_by_id'    => $log->to_user_id,
                'has_received_sig'  => !empty($log->recipient_sig),

                // Delivered By is the BERC staff handing it out
                'delivered_by'      => $log->from_name,
                'delivered_id'      => $log->from_user_id,
                'has_delivered_sig' => !empty($log->sender_sig),

                'remarks'           => $log->remarks
            ];
        });

        return view('forms.logbook-outgoing-communications', [
            'logbookEntries' => $logbookEntries,
            'protocol_code'  => $protocol_code
        ]);
    }

    //this function renders the logbook of communications for incoming correspondences, which typically involve communications from external parties such as researchers or proponents to BERC staff. It fetches the relevant routing logs for the specified protocol code, filters them to include only those where the recipient is a BERC staff member, and then maps the data to a format suitable for display in the Blade view. The function also checks for the presence of electronic signatures for both the sender (external party) and the recipient (BERC staff) to indicate whether the communication was signed by either party.
    //this can only be seen by the berc staff which is accessible in the history page
    public function printIncomingLogbook($protocol_code)
    {
        $internalRoles = ['chair', 'secstaff', 'extconsultant', 'secretariat', 'reviewer', 'cochair'];

        $routingLogs = DB::table('protocol_routing_logs')
            ->leftJoin('users as recipient', 'protocol_routing_logs.to_user_id', '=', 'recipient.id')
            ->leftJoin('users as sender', 'protocol_routing_logs.from_user_id', '=', 'sender.id')
            ->where('protocol_routing_logs.protocol_code', $protocol_code)
            // We filter for incoming by checking if the recipient is BERC staff
            ->whereIn('recipient.role', $internalRoles)
            ->select(
                'protocol_routing_logs.*',
                'recipient.e_signature as recipient_sig', // Check if receiver has sig
                'sender.e_signature as sender_sig'        // Check if sender has sig
            )
            ->orderBy('protocol_routing_logs.id', 'asc')
            ->get();

        // Inside your printIncomingLogbook map function:
        $logbookEntries = $routingLogs->map(function ($log) {
            return [
                'date'             => \Carbon\Carbon::parse($log->created_at)->format('M d, Y / h:i A'),
                'nature'           => $log->document_nature,
                'signatory'        => $log->from_name,
                'signatory_id'     => $log->from_user_id,
                'has_sender_sig'   => !empty($log->sender_sig),

                'received_by'      => $log->to_name,
                'received_by_id'   => $log->to_user_id,
                'has_received_sig' => !empty($log->recipient_sig),

                'delivered_by'     => $log->from_name,
                'delivered_id'     => $log->from_user_id, // Same ID as sender
                'has_delivered_sig'=> !empty($log->sender_sig), // Same signature status

                'addressee'        => $log->to_name,
                'remarks'          => $log->remarks
            ];
        });

        return view('forms.logbook-incoming-communications', [
            'logbookEntries' => $logbookEntries,
            'protocol_code'  => $protocol_code
        ]);
    }

    //this function fetches the documents associated with a specific revision of a protocol
    //this is called on the application status, history to both the reseaerchers and the berc staff
    public function getRevisionDocuments($protocol_code, $revision_number)
    {
        try {
            // 1. Find the specific revision ID
            $revision = DB::table('research_application_revisions')
                ->where('protocol_code', $protocol_code)
                ->where('revision_number', $revision_number)
                ->first();

            if (!$revision) {
                return response()->json(['documents' => []]);
            }

            // 2. Fetch the documents ONLY for this specific revision
            $documents = DB::table('revision_documents')
                ->where('revision_id', $revision->id)
                ->get();

            // 3. Helper to safely generate URLs
            $getSecureUrl = function($dbPath) use ($protocol_code) {
                if (empty($dbPath)) return null;
                $cleanPath = str_replace('documents/'.$protocol_code.'/', '', $dbPath);
                return $cleanPath !== '' ? route('view.document', [
                    'protocol_code' => $protocol_code,
                    'filename' => $cleanPath
                ]) : null;
            };

            // 4. Organize documents by type
            $documentGroups = [];
            foreach ($documents as $doc) {
                if (!isset($documentGroups[$doc->type])) {
                    $documentGroups[$doc->type] = [];
                }
                $documentGroups[$doc->type][] = [
                    'id' => $doc->id,
                    'url' => $getSecureUrl($doc->file_path),
                    'description' => $doc->description
                ];
            }

            return response()->json(['documents' => $documentGroups]);

        } catch (\Exception $e) {
            Log::error("Revision Docs Error: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    //helper function to download all documents associated with a protocol as a ZIP file, including the main application documents, supplementary documents, payment proofs, and revision documents. The function organizes the files in the ZIP with user-friendly names and handles potential naming conflicts by appending numbers to duplicate names. It also includes error handling for missing files and issues with the ZipArchive process.
    public function downloadAllAsZip($protocol_code)
    {
        try {
            if (!extension_loaded('zip')) {
                throw new \Exception("The PHP ZipArchive extension is not installed/enabled on your server.");
            }

            $zip = new ZipArchive;
            $fileName = $protocol_code . '_Documents.zip';

            $tempDir = storage_path('app/temp');
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }
            $tempPath = $tempDir . '/' . $fileName;

            $openResult = $zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            if ($openResult !== TRUE) {
                throw new \Exception("ZipArchive failed to open. Error code: " . $openResult);
            }

            $usedNames = [];
            $filesAddedCount = 0;

            /**
             * Helper: Converts strings to Title Case excluding small words.
             */
            $toTitleCase = function($string) {
                $smallWords = ['a', 'an', 'the', 'and', 'but', 'or', 'for', 'nor', 'at', 'by', 'from', 'in', 'into', 'of', 'off', 'on', 'onto', 'out', 'over', 'up', 'with', 'to', 'as'];

                $words = explode(' ', strtolower(str_replace(['_', '-'], ' ', $string)));

                foreach ($words as $i => $word) {
                    if ($i === 0 || !in_array($word, $smallWords)) {
                        $words[$i] = ucfirst($word);
                    }
                }
                return implode(' ', $words);
            };

            /**
             * Helper: Formats the full filename logic.
             */
            $formatName = function($type, $description, $path) use (&$usedNames, $toTitleCase) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $cleanType = $toTitleCase($type);

                if (!empty($description) && strtolower(trim($description)) !== 'view file') {
                    $descRaw = preg_replace('/\.[a-zA-Z0-9]+$/', '', $description);
                    $cleanDesc = $toTitleCase($descRaw);
                    $baseName = "{$cleanType} - {$cleanDesc}";
                } else {
                    $baseName = $cleanType;
                }

                $baseName = preg_replace('/[<>:"\/\\|?*]/', '', $baseName);
                $fullName = "{$baseName}.{$ext}";

                $counter = 1;
                while (in_array($fullName, $usedNames)) {
                    $fullName = "{$baseName} ({$counter}).{$ext}";
                    $counter++;
                }
                $usedNames[] = $fullName;

                return $fullName;
            };

            // 1. BASE & SUPPLEMENTARY DOCUMENTS
            $basicDocs = DB::table('basic_requirements')->where('protocol_code', $protocol_code)->get();
            $suppDocs = DB::table('supplementary_documents')->where('protocol_code', $protocol_code)->get();

            foreach([...$basicDocs, ...$suppDocs] as $doc) {
                if (!empty($doc->file_path)) {
                    $absolutePath = storage_path("app/" . ltrim($doc->file_path, '/'));
                    if (File::exists($absolutePath)) {
                        $localName = $formatName($doc->type, $doc->description, $absolutePath);

                        // 👇 NEW LOGIC: Check if the file is a resubmission based on its path
                        if (str_contains(strtolower($doc->file_path), 'resubmit')) {
                            $zip->addFile($absolutePath, 'resubmit/' . $localName);
                        } else {
                            $zip->addFile($absolutePath, $localName); // Original goes to root
                        }

                        $filesAddedCount++;
                    }
                }
            }

            // 2. PAYMENT FOLDER
            $app = ResearchApplications::with('payment')->where('protocol_code', $protocol_code)->first();
            if($app && $app->payment && !empty($app->payment->proof_of_payment_path)) {
                $paymentPath = storage_path("app/" . ltrim($app->payment->proof_of_payment_path, '/'));
                if(File::exists($paymentPath)) {
                    $ext = pathinfo($paymentPath, PATHINFO_EXTENSION);
                    $zip->addFile($paymentPath, 'payment/Proof of Payment.' . $ext);
                    $filesAddedCount++;
                }
            }

            // 3. REVISION DOCUMENTS (v1, v2, v3...)
            $revisions = DB::table('research_application_revisions')->where('protocol_code', $protocol_code)->get();

            foreach($revisions as $rev) {
                $revDocs = DB::table('revision_documents')->where('revision_id', $rev->id)->get();

                // 👇 NEW LOGIC: Version folders are now fully independent separate folders
                $folderName = 'v' . $rev->revision_number . '/';

                $usedNames = []; // Reset duplication tracker for each subfolder

                foreach($revDocs as $rDoc) {
                    if (!empty($rDoc->file_path)) {
                        $absolutePath = storage_path("app/" . ltrim($rDoc->file_path, '/'));
                        if (File::exists($absolutePath)) {
                            $localName = $formatName($rDoc->type, $rDoc->description, $absolutePath);
                            $zip->addFile($absolutePath, $folderName . $localName);
                            $filesAddedCount++;
                        }
                    }
                }
            }

            if ($filesAddedCount === 0) {
                $zip->close();
                if (File::exists($tempPath)) { File::delete($tempPath); }
                return response()->json(['error' => 'No files found.'], 404);
            }

            $zip->close();
            return response()->download($tempPath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error("ZIP Creation Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //this function renders the Certificate of Exemption for a given protocol code. It first fetches the certificate data from the database, then retrieves the routing log to identify who issued the certificate. The function attempts to find the chairperson's user record based on the routing log's from_user_id, and if that fails, it falls back to finding the chairperson by name. Finally, it loads the Blade view for the certificate and passes both the certificate data and the chairperson's information to it. This allows the view to display the certificate details along with the name and signature of the chairperson who
    public function printExemptionCertificate($protocol_code)
    {
        // 1. Fetch the certificate data
        $certificate = DB::table('exemption_certificates')
            ->where('protocol_code', $protocol_code)
            ->first();

        if (!$certificate) {
            abort(404, 'Certificate of Exemption has not been generated for this protocol yet.');
        }

        // 2. Fetch the routing log to find exactly WHO issued this certificate
        $log = DB::table('protocol_routing_logs')
            ->where('protocol_code', $protocol_code)
            ->where('document_nature', 'Final Certificate Of Exemption')
            ->first();

        $chairperson = null;


        if ($log && $log->from_user_id) {
            // Find the user by their ID from the routing log
            $chairperson = \App\Models\User::find($log->from_user_id);
            dd($chairperson);
        } else {
            // Fallback: If the log is somehow missing, try finding them by the printed name on the certificate
            $chairperson = \App\Models\User::where('name', $certificate->chairperson_name)->first();
        }

        // 3. Load the Blade view and pass both the certificate and the chairperson's data
        return view('forms.certificateexemption', compact('certificate', 'chairperson'));
    }
}
