<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

// Models used in this submission flow
use App\Models\ResearchApplications;
use App\Models\SupplementaryDocument;
use App\Models\AssessmentForm;
use App\Models\InformedConsent;
use App\Models\Payment;
use App\Models\ProtocolRoutingLog;

class ReviewForm extends Controller
{
    public function index()
    {
        // This can be used later for dashboard/display logic if needed
    }

    /**
     * =========================================================
     * MAIN MODULE: APPLICATION SUBMISSION
     * =========================================================
     * This is the main function that handles the whole submission flow.
     *
     * Connected sections inside this method:
     * 1. Validate all incoming form data
     * 2. Generate a unique protocol code
     * 3. Create the storage folder for this protocol
     * 4. Prepare the main application data
     * 5. Save the e-signature
     * 6. Create the main research application record
     * 7. Create the first routing log entry
     * 8. Save basic requirement documents
     * 9. Save supplementary documents
     * 10. Save payment details and proof of payment
     * 11. Save assessment remarks
     * 12. Save informed consent remarks
     *
     * Everything here runs inside one database transaction so that if
     * one part fails, the whole submission is rolled back.
     */
    public function submit(Request $request)
    {
        /**
         * ---------------------------------------------------------
         * SECTION 1: VALIDATION
         * ---------------------------------------------------------
         * First, we validate everything the researcher submitted.
         * This includes:
         * - basic applicant/study information
         * - uploaded documents
         * - payment details
         * - remarks and consent-related fields
         *
         * This section is connected to all the next sections because
         * the rest of the process assumes the data is already valid.
         */
        $validated = $request->validate([
            // Basic Info
            'type_of_research'   => 'required|string|max:50',
            'research_title'     => 'required|string|max:500',
            'name_of_researcher' => 'required|string|max:255',
            'co_researchers'     => 'nullable|array',
            'co_researchers.*'   => 'nullable|string|max:255',
            'study_site'         => 'required|string|max:255',
            'email'              => 'required|email|max:255',
            'institution'        => 'required|string|max:255',
            'institution_address'=> 'required|string|max:255',
            'tel_no'             => 'nullable|string|max:50',
            'mobile_no'          => 'nullable|string|max:50',
            'fax_no'             => 'nullable|string|max:50',

            // Study Details
            'type_of_study'            => 'nullable|string|max:100',
            'type_of_study1'           => 'nullable|string|max:100',
            'type_of_study_others'     => 'required_if:type_of_study,Others|nullable|string|max:255',
            'source_of_funding'        => 'nullable|string|max:100',
            'source_of_funding_others' => 'required_if:source_of_funding,Others|nullable|string|max:255',
            'study_start_date'         => 'nullable|date',
            'study_end_date'           => 'nullable|date|after_or_equal:study_start_date',
            'study_participants'       => 'nullable|integer|min:1',
            'technical_review'         => 'nullable|boolean',
            'tracking_number'          => 'nullable|string|max:100',
            'has_been_submitted_to_another_berc' => 'nullable|boolean',
            'brief_description'        => 'nullable|string',
            'e_signature'              => 'required|image|mimes:png,jpg,jpeg|max:2048',

            // Informed consent necessity
            'icf_necessity'             => 'required|in:unable,yes,no',
            'icf_necessity_explanation' => 'required_if:icf_necessity,no|nullable|string|max:2000',

            // Multiple File Uploads (Basic Requirements)
            'doc_letter_request'            => 'nullable|array',
            'doc_letter_request.*'          => 'file|mimes:pdf,doc,docx|max:10240',
            'doc_endorsement_letter'        => 'nullable|array',
            'doc_endorsement_letter.*'      => 'file|mimes:pdf,doc,docx|max:10240',
            'doc_full_proposal'             => 'nullable|array',
            'doc_full_proposal.*'           => 'file|mimes:pdf,doc,docx|max:10240',
            'doc_technical_review_approval' => 'nullable|array',
            'doc_technical_review_approval.*'=> 'file|mimes:pdf,doc,docx|max:10240',
            'doc_informed_consent'          => 'nullable|array',
            'doc_informed_consent.*'        => 'file|mimes:pdf,doc,docx|max:10240',
            'doc_informed_consent_lang'     => 'nullable|array',
            'doc_informed_consent_lang.*'   => 'nullable|string|max:255',

            // Multiple File Uploads (Supplementary Arrays)
            'doc_manuscript'                => 'nullable',
            'doc_questionnaire'             => 'nullable|array',
            'doc_questionnaire.*'           => 'file|mimes:pdf,doc,docx|max:10240',
            'doc_questionnaire_desc'        => 'nullable|array',
            'doc_questionnaire_desc.*'      => 'nullable|string|max:255',

            'doc_curriculum_vitae'          => 'nullable|array',
            'doc_curriculum_vitae.*'        => 'file|mimes:pdf,doc,docx|max:10240',
            'doc_curriculum_vitae_desc'     => 'nullable|array',
            'doc_curriculum_vitae_desc.*'   => 'nullable|string|max:255',

            'doc_data_collection'           => 'nullable|array',
            'doc_data_collection.*'         => 'file|mimes:pdf,doc,docx|max:10240',
            'doc_data_collection_desc'      => 'nullable|array',
            'doc_data_collection_desc.*'    => 'nullable|string|max:255',

            'doc_product_brochure'          => 'nullable|array',
            'doc_product_brochure.*'        => 'file|mimes:pdf,doc,docx|max:10240',
            'doc_product_brochure_desc'     => 'nullable|array',
            'doc_product_brochure_desc.*'   => 'nullable|string|max:255',

            'doc_philippine_fda'            => 'nullable|array',
            'doc_philippine_fda.*'          => 'file|mimes:pdf,doc,docx|max:10240',
            'doc_philippine_fda_desc'       => 'nullable|array',
            'doc_philippine_fda_desc.*'     => 'nullable|string|max:255',

            'doc_special_populations'       => 'nullable|array',
            'doc_special_populations.*'     => 'file|mimes:pdf,doc,docx|max:10240',
            'doc_special_populations_desc'  => 'nullable|array',
            'doc_special_populations_desc.*'=> 'nullable|string|max:255',

            'doc_others'                    => 'nullable|array',
            'doc_others.*'                  => 'file|mimes:pdf,doc,docx|max:10240',
            'doc_others_desc'               => 'nullable|array',
            'doc_others_desc.*'             => 'nullable|string|max:255',

            // Payment
            'payment_method'        => 'required|string',
            'amount_paid'           => 'required|numeric|min:0',
            'reference_number'      => 'required|string|unique:payments,reference_number',
            'proof_of_payment_file' => 'required|file|mimes:jpg,png,pdf|max:10240',

            // Remarks arrays
            'remarks'        => 'nullable|array',
            'line_pages'     => 'nullable|array',
            'icf_remarks'    => 'nullable|array',
            'icf_line_pages' => 'nullable|array',
        ]);

        try {
            return DB::transaction(function () use ($request) {

                /**
                 * ---------------------------------------------------------
                 * SECTION 2: GENERATE PROTOCOL CODE
                 * ---------------------------------------------------------
                 * Here we build the unique protocol code for the application.
                 * The format depends on:
                 * - current year
                 * - selected type of research
                 * - next running number
                 *
                 * This protocol code becomes the main identifier used by
                 * almost every connected section below:
                 * - folder creation
                 * - main application record
                 * - routing log
                 * - document records
                 * - payment record
                 * - assessment and ICF records
                 */
                $year = now()->year;
                $typeId = (int) $request->type_of_research;
                $typePrefix = str_pad($typeId, 2, '0', STR_PAD_LEFT);

                $lastRecord = ResearchApplications::where('year', $year)
                                ->where('protocol_code', 'LIKE', "{$year}-{$typePrefix}-%")
                                ->latest('id')
                                ->first();

                if ($lastRecord && $lastRecord->protocol_code) {
                    $parts = explode('-', $lastRecord->protocol_code);
                    $nextNumber = (int) end($parts) + 1;
                } else {
                    $nextNumber = 1;
                }

                $protocol_code = "{$year}-{$typePrefix}-" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                /**
                 * ---------------------------------------------------------
                 * SECTION 3: CREATE STORAGE FOLDER
                 * ---------------------------------------------------------
                 * Each protocol gets its own folder in storage.
                 * This folder will be used by the document-related sections
                 * later when saving uploaded files.
                 *
                 * Connected to:
                 * - signature saving
                 * - basic requirement uploads
                 * - supplementary uploads
                 * - payment proof upload
                 */
                $folderPath = 'documents/' . $protocol_code;
                $absolutePath = storage_path('app/' . $folderPath);

                if (!File::exists($absolutePath)) {
                    File::makeDirectory($absolutePath, 0755, true);
                }

                /**
                 * ---------------------------------------------------------
                 * SECTION 4: PREPARE MAIN APPLICATION DATA
                 * ---------------------------------------------------------
                 * Here we collect the fields that belong directly to the
                 * main research_applications table.
                 *
                 * This section is connected to SECTION 6 where the actual
                 * ResearchApplications record is created.
                 */
                $mainTableData = $request->only([
                    'type_of_research', 'research_title', 'name_of_researcher', 'study_site',
                    'email', 'institution', 'institution_address', 'tel_no', 'mobile_no', 'fax_no',
                    'type_of_study', 'type_of_study1', 'type_of_study_others', 'source_of_funding',
                    'source_of_funding_others', 'study_start_date', 'study_end_date', 'study_participants',
                    'technical_review', 'tracking_number', 'has_been_submitted_to_another_berc', 'brief_description'
                ]);

                $mainTableData['protocol_code']  = $protocol_code;
                $mainTableData['user_id']        = Auth::id();
                $mainTableData['year']           = $year;
                $mainTableData['status']         = 'submitted';
                $mainTableData['co_researchers'] = array_filter($request->input('co_researchers', []));

                /**
                 * ---------------------------------------------------------
                 * SECTION 5: SAVE E-SIGNATURE
                 * ---------------------------------------------------------
                 * The signature is stored separately, then its path is added
                 * into the main application data array.
                 *
                 * Connected to:
                 * - SECTION 4 (mainTableData)
                 * - SECTION 6 (application creation)
                 */
                if ($request->hasFile('e_signature')) {
                    $file = $request->file('e_signature');
                    $fileName = 'sig_' . $protocol_code . '_' . time() . '_' . Str::random(5) . '.' . $file->extension();
                    $path = $file->storeAs('signatures', $fileName, 'local');
                    $mainTableData['e_signature'] = $path;
                }

                /**
                 * ---------------------------------------------------------
                 * SECTION 6: CREATE MAIN APPLICATION RECORD
                 * ---------------------------------------------------------
                 * This is where the actual research application is inserted.
                 * The result of this section is the main record that the
                 * other related tables will point to using protocol_code.
                 */
                $research = ResearchApplications::create($mainTableData);

                /**
                 * ---------------------------------------------------------
                 * SECTION 7: CREATE INITIAL ROUTING LOG
                 * ---------------------------------------------------------
                 * As soon as the application is submitted, we also log the
                 * first routing entry so the workflow has a starting point.
                 *
                 * Connected to:
                 * - protocol workflow / routing module
                 * - future secretariat handling
                 */
                ProtocolRoutingLog::create([
                    'protocol_code'   => $protocol_code,
                    'document_nature' => 'Application Form',
                    'from_name'       => $request->name_of_researcher,
                    'to_name'         => null, // Secretariat or next handler fills this later
                    'from_user_id'    => Auth::id(),
                    'to_user_id'      => null,
                    'remarks'         => 'Initial Submission'
                ]);

                /**
                 * ---------------------------------------------------------
                 * SECTION 8: SAVE BASIC REQUIREMENT DOCUMENTS
                 * ---------------------------------------------------------
                 * This part handles the required/basic documents and stores
                 * them in the basic_requirements table.
                 *
                 * Connected to:
                 * - SECTION 3 (storage folder)
                 * - basic_requirements table
                 * - letter document list used later in other workflows
                 */
                $basicGroups = [
                    'doc_letter_request'            => 'letter_request',
                    'doc_endorsement_letter'        => 'endorsement_letter',
                    'doc_full_proposal'             => 'full_proposal',
                    'doc_technical_review_approval' => 'technical_review_approval',
                    'doc_curriculum_vitae'          => 'curriculum_vitae',
                    'doc_informed_consent'          => 'informed_consent',
                    'doc_manuscript'                => 'manuscript',
                ];

                foreach ($basicGroups as $inputName => $dbType) {
                    if ($request->hasFile($inputName)) {
                        $files = $request->file($inputName);

                        // For informed consent, the description comes from the selected language
                        if ($inputName === 'doc_informed_consent') {
                            $descriptions = $request->input('doc_informed_consent_lang', []);
                        } else {
                            $descriptions = $request->input($inputName . '_desc', []);
                        }

                        foreach ($files as $index => $file) {
                            $fallbackDesc = ucwords(str_replace('_', ' ', $dbType)) . ' ' . ($index + 1);
                            $descText = !empty($descriptions[$index]) ? $descriptions[$index] : $fallbackDesc;

                            $safeName = Str::slug($descText);
                            $fileName = sprintf(
                                '%s-%s_%s_%s_%d.%s',
                                str_replace('_', '-', $dbType),
                                $safeName,
                                $protocol_code,
                                time(),
                                $index,
                                $file->extension()
                            );

                            $file->move($absolutePath, $fileName);

                            DB::table('basic_requirements')->insert([
                                'protocol_code' => $protocol_code,
                                'type'          => $dbType,
                                'description'   => $descText,
                                'file_path'     => $folderPath . '/' . $fileName,
                                'created_at'    => now(),
                                'updated_at'    => now(),
                            ]);
                        }
                    }
                }

                /**
                 * ---------------------------------------------------------
                 * SECTION 9: SAVE SUPPLEMENTARY DOCUMENTS
                 * ---------------------------------------------------------
                 * This part handles optional/supporting documents and saves
                 * them to the supplementary_documents table.
                 *
                 * Connected to:
                 * - SECTION 3 (storage folder)
                 * - SupplementaryDocument model
                 */
                $supplementaryGroups = [
                    'doc_questionnaire'       => 'questionnaire',
                    'doc_data_collection'     => 'data_collection',
                    'doc_product_brochure'    => 'product_brochure',
                    'doc_philippine_fda'      => 'philippine_fda',
                    'doc_special_populations' => 'special_populations',
                    'doc_others'              => 'others'
                ];

                foreach ($supplementaryGroups as $inputName => $dbType) {
                    if ($request->hasFile($inputName)) {
                        $files = $request->file($inputName);
                        $descriptions = $request->input($inputName . '_desc', []);

                        foreach ($files as $index => $file) {
                            $fallbackDesc = ucwords(str_replace('_', ' ', $dbType)) . ' ' . ($index + 1);
                            $descText = !empty($descriptions[$index]) ? $descriptions[$index] : $fallbackDesc;

                            $safeName = Str::slug($descText);
                            $fileName = sprintf(
                                '%s-%s_%s_%s_%d.%s',
                                str_replace('_', '-', $dbType),
                                $safeName,
                                $protocol_code,
                                time(),
                                $index,
                                $file->extension()
                            );

                            $file->move($absolutePath, $fileName);

                            SupplementaryDocument::create([
                                'protocol_code' => $protocol_code,
                                'type'          => $dbType,
                                'description'   => $descText,
                                'file_path'     => $folderPath . '/' . $fileName,
                            ]);
                        }
                    }
                }

                /**
                 * ---------------------------------------------------------
                 * SECTION 10: SAVE PAYMENT DETAILS
                 * ---------------------------------------------------------
                 * This saves the payment record and proof of payment file.
                 *
                 * Connected to:
                 * - payment validation fields from SECTION 1
                 * - Payment model/table
                 * - protocol folder from SECTION 3
                 */
                if ($request->hasFile('proof_of_payment_file')) {
                    $payFile = $request->file('proof_of_payment_file');
                    $payFileName = 'receipt_' . $protocol_code . '.' . $payFile->extension();

                    $paymentPath = $absolutePath . '/payment';
                    if (!File::exists($paymentPath)) {
                        File::makeDirectory($paymentPath, 0755, true);
                    }

                    $payFile->move($paymentPath, $payFileName);

                    Payment::create([
                        'protocol_code'         => $protocol_code,
                        'payment_method'        => $request->payment_method,
                        'amount_paid'           => $request->amount_paid,
                        'reference_number'      => $request->reference_number,
                        'proof_of_payment_path' => $folderPath . '/payment/' . $payFileName,
                    ]);
                }

                /**
                 * ---------------------------------------------------------
                 * SECTION 11: SAVE ASSESSMENT REMARKS
                 * ---------------------------------------------------------
                 * This creates the assessment form header first, then saves
                 * each assessment item/remark under it.
                 *
                 * Connected to:
                 * - AssessmentForm model
                 * - assessment form items relationship
                 * - icf necessity fields from validation
                 */
                $explanation = ($request->icf_necessity === 'no') ? $request->icf_necessity_explanation : null;

                if ($request->has('remarks')) {
                    $assessment = AssessmentForm::create([
                        'protocol_code'            => $protocol_code,
                        'reviewer_id'              => Auth::id(),
                        'is_consent_necessary'     => $request->icf_necessity,
                        'no_consent_explanation'   => $explanation,
                        'status'                   => 'submitted'
                    ]);

                    foreach ($request->remarks as $qNum => $remark) {
                        $assessment->items()->create([
                            'question_number' => $qNum,
                            'remark'          => $remark,
                            'line_page'       => $request->line_pages[$qNum] ?? null
                        ]);
                    }
                }

                /**
                 * ---------------------------------------------------------
                 * SECTION 12: SAVE INFORMED CONSENT REMARKS
                 * ---------------------------------------------------------
                 * Similar to the assessment section above, but this time
                 * for the informed consent review module.
                 *
                 * Connected to:
                 * - InformedConsent model
                 * - informed consent items relationship
                 */
                if ($request->has('icf_remarks')) {
                    $icf = InformedConsent::create([
                        'protocol_code' => $protocol_code,
                        'reviewer_id'   => Auth::id(),
                        'status'        => 'submitted'
                    ]);

                    foreach ($request->icf_remarks as $qNum => $remark) {
                        $icf->items()->create([
                            'question_number' => $qNum,
                            'remark'          => $remark,
                            'line_page'       => $request->icf_line_pages[$qNum] ?? null
                        ]);
                    }
                }

                /**
                 * ---------------------------------------------------------
                 * SECTION 13: FINAL SUCCESS RESPONSE
                 * ---------------------------------------------------------
                 * If all connected sections above succeed, we return a
                 * success response together with the generated protocol code.
                 */
                return response()->json([
                    'success'       => true,
                    'protocol_code' => $protocol_code,
                    'id'            => $research->id,
                    'message'       => 'Application submitted successfully.'
                ]);
            });

        } catch (\Exception $e) {
            /**
             * ---------------------------------------------------------
             * ERROR HANDLING
             * ---------------------------------------------------------
             * If anything fails anywhere inside the transaction, the
             * whole submission is rolled back and the frontend gets
             * an error response.
             */
            return response()->json([
                'success' => false,
                'message' => 'Database Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
