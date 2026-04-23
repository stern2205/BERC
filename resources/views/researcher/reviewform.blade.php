<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>APPLICATION FOR ETHICS REVIEW OF A NEW PROTOCOL</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/reviewform.css') }}" />
  </head>

    <style>
        /* Styles the native "Choose File" button */
        .custom-file-input {
            font-size: 11px;
            color: #4b5563;
            width: 100%;
            cursor: pointer;
        }
        .custom-file-input::file-selector-button {
            background: #1e3a8a;
            color: white;
            border: none;
            padding: 6px 12px; /* Smaller than manuscript */
            border-radius: 6px;
            font-family: 'Montserrat', sans-serif;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            margin-right: 12px;
            transition: background 0.2s ease;
        }
        .custom-file-input::file-selector-button:hover {
            background: #172554;
        }

        @media print {
            /* Hide everything in the body by default */
            body * {
            visibility: hidden;
            }

            /* Make the success modal and all its children visible */
            #success-modal, #success-modal * {
            visibility: visible;
            }

            /* Reposition the modal to the top left of the printed page and remove the dark background */
            #success-modal {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: white !important;
            display: flex !important;
            align-items: flex-start !important; /* align to top instead of center for printing */
            padding-top: 40px;
            }

            /* Remove the shadow and borders from the inner white box for a clean print */
            #success-modal > div {
            box-shadow: none !important;
            border: none !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 20px !important;
            }

            /* Hide the buttons inside the modal so they don't show up on the paper */
            #success-modal button {
            display: none !important;
            }
        }
    </style>

  <body>
    <div class="container">
      <div class="header">
        <h1>APPLICATION FOR ETHICS REVIEW OF A NEW PROTOCOL</h1>
      </div>

      <!-- Progress bar: 8 steps (Assessment removed, merged into Protocol Review) -->
      <div class="progress-bar">
        <!-- Step 1: Consent -->
        <div class="step active" data-step="1">
          <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></div>
          <div class="step-label">Consent</div>
        </div>
        <!-- Step 2: General Info -->
        <div class="step" data-step="2">
          <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></div>
          <div class="step-label">General Info</div>
        </div>
        <!-- Step 3: Brief Description -->
        <div class="step" data-step="3">
          <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg></div>
          <div class="step-label">Brief Description</div>
        </div>
        <!-- Step 4: Documents -->
        <div class="step" data-step="4">
          <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg></div>
          <div class="step-label">Documents</div>
        </div>
        <!-- Step 5: Protocol Review (merged with Assessment) -->
        <div class="step" data-step="5">
          <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></div>
          <div class="step-label">Protocol Review</div>
        </div>
        <!-- Step 6: ICF Evaluation -->
        <div class="step" data-step="6" id="icf-step">
          <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2"></path></svg></div>
          <div class="step-label">ICF Evaluation</div>
        </div>
        <!-- Step 7: Payment -->
        <div class="step" data-step="7">
          <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg></div>
          <div class="step-label">Payment</div>
        </div>
        <!-- Step 8: Submit -->
        <div class="step" data-step="8">
          <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"></path></svg></div>
          <div class="step-label">Submit</div>
        </div>
      </div>

      <form id="sisForm" class="form-content" enctype="multipart/form-data">
        @csrf

        <!-- Section 1: Consent -->
        <div class="form-section" id="section-1">
          <h2 class="section-title">Data Privacy Consent</h2>
          <div style="text-align: center; padding: 40px; line-height: 1.8;">
            <p style="margin-bottom: 20px; color: #374151; font-size: 14px; font-weight: 500;">I hereby give my consent for the collection, processing, and storage of my personal information in accordance with the Data Privacy Act of 2012.</p>
            <label style="display: flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; margin-top: 30px;">
              <input type="checkbox" id="consentCheck" required style="width: 18px; height: 18px; cursor: pointer; accent-color: #1e3a8a;" />
              <span style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: #1e3a8a;">I agree to the terms and conditions</span>
            </label>
          </div>
        </div>

        <!-- Section 2: General Information -->
        <div class="form-section hidden" id="section-2">
          <h2 class="section-title">General Information</h2>
          <div class="subsection-heading">Study Details</div>
          <div class="form-group"><label class="form-label">Title of the Study</label><input type="text" class="form-input" name="research_title"required /></div>
          <div class="form-group">
            <label class="form-label">Type of Research</label>
            <select name="type_of_research" class="form-input" required>
                <option value="" disabled selected>Select type of research</option>
                <option value="1">1. Faculty Research</option>
                <option value="2">2. Graduate School Research</option>
                <option value="3">3. Undergraduate Research</option>
                <option value="4">4. Integrated School Student Research</option>
                <option value="5">5. External Research</option>
            </select>
        </div>
          <div class="form-group"><label class="form-label">Study Site</label><input type="text" class="form-input" name="study_site" required /></div>
          <div class="form-group"><label class="form-label">Name of Researcher</label><input type="text" class="form-input" name="name_of_researcher" value="{{ $user->name}}" required /></div>
          <div class="subsection-heading" style="margin-top: 20px;">Contact Information</div>
          <div class="form-group"><label class="form-label">Tel No:</label><input type="tel" class="form-input" name="tel_no" /></div>
          <div class="form-group"><label class="form-label">Mobile Number</label><input type="tel" class="form-input" name="mobile_no" required /></div>
          <div class="form-group"><label class="form-label">Fax No:</label><input type="tel" class="form-input" name="fax_no" /></div>
          <div class="form-group"><label class="form-label">Email:</label><input type="email" class="form-input" name="email" value="{{ $user->email }}" required /></div>
          <div class="form-group">
            <label class="form-label">Co-Researcher(s) (if any)</label>

            <div>
                <div id="co-researcher-list">
                    <div class="input-row">
                        <input type="text" class="form-input" name="co_researchers[]" placeholder="Enter name" />
                    </div>
                </div>

                <button type="button" id="add-researcher-btn" class="btn-add">
                    + Add Co-Researcher
                </button>
            </div>
        </div>
          <div class="form-group"><label class="form-label">Institution</label><input type="text" class="form-input" name="institution" required /></div>
          <div class="form-group"><label class="form-label">Address of Institution</label><input type="text" class="form-input" name="institution_address" required /></div>
          <div class="subsection-heading" style="margin-top: 20px;">Type of Study</div>
          <div style="margin-left: 200px;">
            <label class="check-label">
                <input type="radio" name="type_of_study" value="Clinical Trial (Sponsored)" />
                <span>Clinical Trial (Sponsored)</span>
            </label>
            <label class="check-label">
                <input type="radio" name="type_of_study" value="Clinical Trials (Researcher-initiated)" />
                <span>Clinical Trials (Researcher-initiated)</span>
            </label>
            <label class="check-label">
                <input type="radio" name="type_of_study" value="Health Operations Research (Health Programs and Policies)" />
                <span>Health Operations Research (Health Programs and Policies)</span>
            </label>
            <label class="check-label">
                <input type="radio" name="type_of_study" value="Social-Behavioral Research" />
                <span>Social-Behavioral Research</span>
            </label>
            <label class="check-label">
                <input type="radio" name="type_of_study" value="Public Health-Epidemiologic Research" />
                <span>Public Health-Epidemiologic Research</span>
            </label>
            <label class="check-label">
                <input type="radio" name="type_of_study" value="Biomedical research (Retrospective / Prospective and diagnostic studies)" />
                <span>Biomedical research (Retrospective / Prospective and diagnostic studies)</span>
            </label>
            <label class="check-label">
                <input type="radio" name="type_of_study" value="Stem Cell Research" />
                <span>Stem Cell Research</span>
            </label>
            <label class="check-label">
                <input type="radio" name="type_of_study" value="Others" id="study-others-check" onchange="toggleOthersInput('study-others-check','study-others-input')" />
                <span>Others</span>
            </label>

            <div id="study-others-input" style="display:none; margin-left: 26px; margin-top: 6px; margin-bottom: 4px;">
                <input type="text" name="type_of_study_others" class="form-input" placeholder="Please specify type of study..." style="max-width: 400px;" />
            </div>
        </div>

        <div class="subsection-heading" style="margin-top: 20px;">Site Configuration</div>
        <div style="margin-left: 200px; margin-top: 16px; display: flex; gap: 24px; align-items: center;">
            <label class="check-label"><input type="radio" name="type_of_study1" value="Multicenter (International)" /><span>Multicenter (International)</span></label>
            <label class="check-label"><input type="radio" name="type_of_study1" value="Multicenter (National)" /><span>Multicenter (National)</span></label>
            <label class="check-label"><input type="radio" name="type_of_study1" value="Single Site" /><span>Single Site</span></label>
          </div>
          <div class="subsection-heading" style="margin-top: 20px;">Source of Funding</div>
          <div style="margin-left: 200px;">
            <label class="check-label"><input type="radio" name="source_of_funding" value="Self-funded" /><span>Self-funded</span></label>
            <label class="check-label"><input type="radio" name="source_of_funding" value="Government-Funded" /><span>Government-Funded</span></label>
            <label class="check-label"><input type="radio" name="source_of_funding" value="Scholarship Research Grant" /><span>Scholarship Research Grant</span></label>
            <label class="check-label"><input type="radio" name="source_of_funding" value="Sponsored by a Pharmaceutical Company" /><span>Sponsored by a Pharmaceutical Company</span></label>
            <label class="check-label"><input type="radio" name="source_of_funding" value="Institution-Funded" /><span>Institution-Funded</span></label>
            <label class="check-label">
                <input type="radio" name="source_of_funding" value="Others" id="funding-others-check" onchange="toggleOthersInput('funding-others-check','funding-others-input')" />
                <span>Others</span>
            </label>

            <div id="funding-others-input" style="display:none; margin-left: 26px; margin-top: 6px; margin-bottom: 4px;">
                <input type="text" name="source_of_funding_others" class="form-input" placeholder="Please specify funding source..." style="max-width: 400px;" />
            </div>
        </div>
            <div class="subsection-heading" style="margin-top: 20px;">Duration &amp; Participants</div>
            <div class="form-group">
                <label class="form-label">Duration of the Study</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size:11px; color:#6b7280; margin-bottom:5px; display:block; font-weight:600;">Start date:</label>
                        <input type="date"
                            id="study_start_date"
                            name="study_start_date"
                            class="form-input"
                            required
                            onchange="
                                document.getElementById('study_end_date').min = this.value;
                                if(document.getElementById('study_end_date').value < this.value) {
                                    document.getElementById('study_end_date').value = '';
                                }"
                        />
                    </div>
                    <div>
                        <label style="font-size:11px; color:#6b7280; margin-bottom:5px; display:block; font-weight:600;">End date:</label>
                        <input type="date"
                            id="study_end_date"
                            name="study_end_date"
                            class="form-input"
                            required
                        />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">No. of Study Participants</label>
                <input type="number" name="study_participants" class="form-input" placeholder="Enter number" required />
            </div>
            <div class="subsection-heading" style="margin-top: 20px;">Technical Review</div>
            <div class="form-group">
                <label class="form-label">Has the Research undergone a Technical Review?</label>
                <div style="display: flex; gap: 20px; align-items: center;">
                    <label class="check-label"><input type="radio" name="technical_review" value="1" /><span>Yes (please attach technical review results)</span></label>
                    <label class="check-label"><input type="radio" name="technical_review" value="0" /><span>No</span></label>
                </div>
            </div>
            <div class="subsection-heading" style="margin-top: 20px;">Previous Submissions</div>
            <div class="form-group">
                <label class="form-label">Has the research been submitted to another BERC?</label>
                <div style="display: flex; gap: 20px; align-items: center;">
                    <label class="check-label"><input type="radio" name="has_been_submitted_to_another_berc" value="1" /><span>Yes</span></label>
                    <label class="check-label"><input type="radio" name="has_been_submitted_to_another_berc" value="0" /><span>No</span></label>
                </div>
            </div>
        </div>

        <!-- Section 3: Brief Description -->
        <div class="form-section hidden" id="section-3">
          <h2 class="section-title">Brief Description of the Study</h2>
          <div class="form-group" style="grid-template-columns: 1fr;">
            <label class="form-label" style="margin-bottom: 10px;">Brief Description of the Study</label>
            <textarea class="form-input" name="brief_description" placeholder="Provide a brief description of the study" rows="15" required style="width: 100%; resize: vertical;"></textarea>
          </div>
        </div>

        <div class="form-section hidden" id="section-4">
            <h2 class="section-title">Checklist of Documents</h2>
            <div class="subsection-heading">3. Checklist of Documents</div>
            <div style="max-width: 560px;">

                <p style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 14px; color: #1e3a8a;">Basic Requirements (REQUIRED)</p>

                <div class="document-group" style="margin-bottom: 16px;">
                    <label class="check-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" required onchange="toggleSpecificUpload(this)" />
                        <span style="font-size: 13px; font-weight: 600; color: #374151;">Letter request for review</span>
                    </label>
                    <div class="upload-bin" id="letter-container" style="display: none; margin-left: 24px; margin-top: 8px;">
                        <div class="doc-row" style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; background: #f8faff; border: 1px dashed #bfdbfe; border-radius: 8px; padding: 8px 12px;">
                            <div style="flex: 1; display: flex; align-items: center;">
                                <input type="file" required name="doc_letter_request[]" accept=".pdf,.doc,.docx" class="custom-file-input" />
                            </div>
                        </div>
                        <button type="button" onclick="addDocumentRow('letter-container', 'doc_letter_request[]')" style="background: #eff6ff; color: #1e3a8a; border: none; border-radius: 6px; padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: background 0.2s;">
                            + Add Another Letter
                        </button>
                    </div>
                </div>

                <div class="document-group" style="margin-bottom: 16px;">
                    <label class="check-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" required onchange="toggleSpecificUpload(this)" />
                        <span style="font-size: 13px; font-weight: 600; color: #374151;">Endorsement/Referral Letter</span>
                    </label>
                    <div class="upload-bin" id="endorsement-container" style="display: none; margin-left: 24px; margin-top: 8px;">
                        <div class="doc-row" style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; background: #f8faff; border: 1px dashed #bfdbfe; border-radius: 8px; padding: 8px 12px;">
                            <div style="flex: 1; display: flex; align-items: center;">
                                <input type="file" required name="doc_endorsement_letter[]" accept=".pdf,.doc,.docx" class="custom-file-input" />
                            </div>
                        </div>
                        <button type="button" onclick="addDocumentRow('endorsement-container', 'doc_endorsement_letter[]')" style="background: #eff6ff; color: #1e3a8a; border: none; border-radius: 6px; padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: background 0.2s;">
                            + Add Another Endorsement
                        </button>
                    </div>
                </div>

                <div class="document-group" style="margin-bottom: 16px;">
                    <label class="check-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" required onchange="toggleSpecificUpload(this)" />
                        <span style="font-size: 13px; font-weight: 600; color: #374151;">Full proposal/study protocol</span>
                    </label>
                    <div class="upload-bin" id="proposal-container" style="display: none; margin-left: 24px; margin-top: 8px;">
                        <div class="doc-row" style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; background: #f8faff; border: 1px dashed #bfdbfe; border-radius: 8px; padding: 8px 12px;">
                            <div style="flex: 1; display: flex; align-items: center;">
                                <input type="file" required name="doc_full_proposal[]" accept=".pdf,.doc,.docx" class="custom-file-input" />
                            </div>
                        </div>
                        <button type="button" onclick="addDocumentRow('proposal-container', 'doc_full_proposal[]')" style="background: #eff6ff; color: #1e3a8a; border: none; border-radius: 6px; padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: background 0.2s;">
                            + Add Another Document
                        </button>
                    </div>
                </div>

                <div class="document-group" style="margin-bottom: 16px;">
                    <label class="check-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" required onchange="toggleSpecificUpload(this)" />
                        <span style="font-size: 13px; font-weight: 600; color: #374151;">Technical Review Approval</span>
                    </label>
                    <div class="upload-bin" id="technical-container" style="display: none; margin-left: 24px; margin-top: 8px;">
                        <div class="doc-row" style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; background: #f8faff; border: 1px dashed #bfdbfe; border-radius: 8px; padding: 8px 12px;">
                            <div style="flex: 1; display: flex; align-items: center;">
                                <input type="file" required name="doc_technical_review_approval[]" accept=".pdf,.doc,.docx" class="custom-file-input" />
                            </div>
                        </div>
                        <button type="button" onclick="addDocumentRow('technical-container', 'doc_technical_review_approval[]')" style="background: #eff6ff; color: #1e3a8a; border: none; border-radius: 6px; padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: background 0.2s;">
                            + Add Another Approval
                        </button>
                    </div>
                </div>

                <div class="document-group" style="margin-bottom: 16px;">
                    <label class="check-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" required onchange="toggleSpecificUpload(this)" />
                        <span style="font-size: 13px; font-weight: 600; color: #374151;">Curriculum Vitae</span>
                    </label>
                    <div class="upload-bin" id="cv-container" style="display: none; margin-left: 24px; margin-top: 8px;">
                        <div class="doc-row" style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 8px; background: #f8faff; border: 1px dashed #bfdbfe; border-radius: 8px; padding: 10px 12px;">
                            <input type="text" name="doc_curriculum_vitae_desc[]" placeholder="Name of Researcher (e.g., Dr. Juan Dela Cruz)" style="width: 100%; padding: 8px 10px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'" />
                            <div style="flex: 1; display: flex; align-items: center;">
                                <input type="file" required name="doc_curriculum_vitae[]" accept=".pdf,.doc,.docx" class="custom-file-input" />
                            </div>
                        </div>
                        <button type="button" onclick="addDocumentRow('cv-container', 'doc_curriculum_vitae[]', 'doc_curriculum_vitae_desc[]', 'Name of Researcher (e.g., Dr. Juan Dela Cruz)')" style="background: #eff6ff; color: #1e3a8a; border: none; border-radius: 6px; padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: background 0.2s;">
                            + Add Another CV
                        </button>
                    </div>
                </div>

                <div style="border: 2px solid #e5e7eb; border-radius: 10px; padding: 20px; margin-bottom: 16px;">
                    <p style="font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 14px;">
                        Is it necessary to seek the informed consent of the participants?
                    </p>

                    <div style="display: flex; gap: 24px; margin-bottom: 14px;">
                        <label class="check-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="icf_necessity" value="unable" onchange="handleICFToggle(this.value)" required />
                            <span>Unable to Assess</span>
                        </label>
                        <label class="check-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="icf_necessity" value="yes" onchange="handleICFToggle(this.value)" />
                            <span>Yes</span>
                        </label>
                        <label class="check-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="icf_necessity" value="no" onchange="handleICFToggle(this.value)" />
                            <span>No</span>
                        </label>
                    </div>

                    <div id="no-explanation-section" style="display: none;">
                        <p style="font-size: 12px; font-weight: 600; color: #1e3a8a; text-decoration: underline; margin-bottom: 6px;">If NO, please explain:</p>
                        <textarea name="icf_necessity_explanation" class="form-input" rows="3" style="resize: vertical; width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 10px;" placeholder="Explain why consent is not necessary..."></textarea>
                    </div>

                    <div id="yes-upload-section" style="display: none; margin-top: 16px; padding-top: 16px; border-top: 1px solid #f1f5f9;">
                        <div class="document-group" style="margin-bottom: 24px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                <span style="font-size: 13px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.05em;">Informed Consent Form Upload</span>
                            </div>

                            <div class="upload-bin" id="icf-container">
                                <div class="doc-row" style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 8px; background: #f8faff; border: 1px dashed #bfdbfe; border-radius: 8px; padding: 10px 12px;">
                                    <input type="text" name="doc_informed_consent_lang[]" placeholder="Specify Document Nature" style="width: 100%; padding: 8px 10px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'" />
                                    <div style="flex: 1; display: flex; align-items: center;">
                                        <input type="file" name="doc_informed_consent[]" accept=".pdf,.doc,.docx" class="custom-file-input" />
                                    </div>
                                </div>

                                <button type="button" onclick="addDocumentRow('icf-container', 'doc_informed_consent[]', 'doc_informed_consent_lang[]', 'Specify Document Nature')" style="background: #eff6ff; color: #1e3a8a; border: none; border-radius: 6px; padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: background 0.2s;">
                                    + Add Another Language Version
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <p style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 14px; margin-top: 24px; color: #1e3a8a;">Supplementary Documents (If Applicable)</p>

                <div class="document-group" style="margin-bottom: 16px;">
                    <label class="check-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" onchange="toggleSpecificUpload(this)" />
                        <span style="font-size: 13px; font-weight: 600; color: #374151;">Questionnaire/s</span>
                    </label>
                    <div class="upload-bin" id="questionnaire-container" style="display: none; margin-left: 24px; margin-top: 8px;">
                        <div class="doc-row" style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 8px; background: #f8faff; border: 1px dashed #bfdbfe; border-radius: 8px; padding: 10px 12px;">
                            <input type="text" name="doc_questionnaire_desc[]" placeholder="Name of Questionnaire (e.g., Demographics Survey)" style="width: 100%; padding: 8px 10px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'" />
                            <div style="flex: 1; display: flex; align-items: center;">
                                <input type="file" name="doc_questionnaire[]" accept=".pdf,.doc,.docx" class="custom-file-input" />
                            </div>
                        </div>
                        <button type="button" onclick="addDocumentRow('questionnaire-container', 'doc_questionnaire[]', 'doc_questionnaire_desc[]', 'Name of Questionnaire (e.g., Demographics Survey)')" style="background: #eff6ff; color: #1e3a8a; border: none; border-radius: 6px; padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: background 0.2s;">
                            + Add Another Questionnaire
                        </button>
                    </div>
                </div>

                <div class="document-group" style="margin-bottom: 16px;">
                    <label class="check-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" onchange="toggleSpecificUpload(this)" />
                        <span style="font-size: 13px; font-weight: 600; color: #374151;">Data Collection Form/s</span>
                    </label>
                    <div class="upload-bin" id="data-collection-container" style="display: none; margin-left: 24px; margin-top: 8px;">
                        <div class="doc-row" style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 8px; background: #f8faff; border: 1px dashed #bfdbfe; border-radius: 8px; padding: 10px 12px;">
                            <input type="text" name="doc_data_collection_desc[]" placeholder="Name of Data Collection Form (e.g., Interview Guide)" style="width: 100%; padding: 8px 10px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'" />
                            <div style="flex: 1; display: flex; align-items: center;">
                                <input type="file" name="doc_data_collection[]" accept=".pdf,.doc,.docx" class="custom-file-input" />
                            </div>
                        </div>
                        <button type="button" onclick="addDocumentRow('data-collection-container', 'doc_data_collection[]', 'doc_data_collection_desc[]', 'Name of Data Collection Form (e.g., Interview Guide)')" style="background: #eff6ff; color: #1e3a8a; border: none; border-radius: 6px; padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: background 0.2s;">
                            + Add Another Form
                        </button>
                    </div>
                </div>

                <div class="document-group" style="margin-bottom: 16px;">
                    <label class="check-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" onchange="toggleSpecificUpload(this)" />
                        <span style="font-size: 13px; font-weight: 600; color: #374151;">Product Brochure/s</span>
                    </label>
                    <div class="upload-bin" id="product-brochure-container" style="display: none; margin-left: 24px; margin-top: 8px;">
                        <div class="doc-row" style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 8px; background: #f8faff; border: 1px dashed #bfdbfe; border-radius: 8px; padding: 10px 12px;">
                            <input type="text" name="doc_product_brochure_desc[]" placeholder="Name of Product Brochure" style="width: 100%; padding: 8px 10px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'" />
                            <div style="flex: 1; display: flex; align-items: center;">
                                <input type="file" name="doc_product_brochure[]" accept=".pdf,.doc,.docx" class="custom-file-input" />
                            </div>
                        </div>
                        <button type="button" onclick="addDocumentRow('product-brochure-container', 'doc_product_brochure[]', 'doc_product_brochure_desc[]', 'Name of Product Brochure')" style="background: #eff6ff; color: #1e3a8a; border: none; border-radius: 6px; padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: background 0.2s;">
                            + Add Another Brochure
                        </button>
                    </div>
                </div>

                <div class="document-group" style="margin-bottom: 16px;">
                    <label class="check-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" onchange="toggleSpecificUpload(this)" />
                        <span style="font-size: 13px; font-weight: 600; color: #374151;">Philippine FDA Marketing Authorization or Import License/s</span>
                    </label>
                    <div class="upload-bin" id="philippine-fda-container" style="display: none; margin-left: 24px; margin-top: 8px;">
                        <div class="doc-row" style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 8px; background: #f8faff; border: 1px dashed #bfdbfe; border-radius: 8px; padding: 10px 12px;">
                            <input type="text" name="doc_philippine_fda_desc[]" placeholder="Name of Authorization/License (e.g., FDA Clearance)" style="width: 100%; padding: 8px 10px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'" />
                            <div style="flex: 1; display: flex; align-items: center;">
                                <input type="file" name="doc_philippine_fda[]" accept=".pdf,.doc,.docx" class="custom-file-input" />
                            </div>
                        </div>
                        <button type="button" onclick="addDocumentRow('philippine-fda-container', 'doc_philippine_fda[]', 'doc_philippine_fda_desc[]', 'Name of Authorization/License (e.g., FDA Clearance)')" style="background: #eff6ff; color: #1e3a8a; border: none; border-radius: 6px; padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: background 0.2s;">
                            + Add Another Permit
                        </button>
                    </div>
                </div>

                <div class="document-group" style="margin-bottom: 16px;">
                    <label class="check-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" onchange="toggleSpecificUpload(this)" />
                        <span style="font-size: 13px; font-weight: 600; color: #374151;">Permit/s for Special Populations</span>
                    </label>
                    <div class="upload-bin" id="special-pop-container" style="display: none; margin-left: 24px; margin-top: 8px;">
                        <div class="doc-row" style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 8px; background: #f8faff; border: 1px dashed #bfdbfe; border-radius: 8px; padding: 10px 12px;">
                            <input type="text" name="doc_special_populations_desc[]" placeholder="Name of Permit (e.g., NCIP Clearance)" style="width: 100%; padding: 8px 10px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'" />
                            <div style="flex: 1; display: flex; align-items: center;">
                                <input type="file" name="doc_special_populations[]" accept=".pdf,.doc,.docx" class="custom-file-input" />
                            </div>
                        </div>
                        <button type="button" onclick="addDocumentRow('special-pop-container', 'doc_special_populations[]', 'doc_special_populations_desc[]', 'Name of Permit (e.g., NCIP Clearance)')" style="background: #eff6ff; color: #1e3a8a; border: none; border-radius: 6px; padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: background 0.2s;">
                            + Add Another Permit
                        </button>
                    </div>
                </div>

                <div class="document-group" style="margin-bottom: 16px;">
                    <label class="check-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" onchange="toggleSpecificUpload(this)" />
                        <span style="font-size: 13px; font-weight: 600; color: #374151;">Others (please specify)</span>
                    </label>
                    <div class="upload-bin" id="others-container" style="display: none; margin-left: 24px; margin-top: 8px;">
                        <div class="doc-row" style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 8px; background: #f8faff; border: 1px dashed #bfdbfe; border-radius: 8px; padding: 10px 12px;">
                            <input type="text" name="doc_others_desc[]" placeholder="Document Name (e.g., MOU with LGU)" style="width: 100%; padding: 8px 10px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'" />
                            <div style="flex: 1; display: flex; align-items: center;">
                                <input type="file" name="doc_others[]" accept=".pdf,.doc,.docx" class="custom-file-input" />
                            </div>
                        </div>
                        <button type="button" onclick="addDocumentRow('others-container', 'doc_others[]', 'doc_others_desc[]', 'Document Name (e.g., MOU with LGU)')" style="background: #eff6ff; color: #1e3a8a; border: none; border-radius: 6px; padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: background 0.2s;">
                            + Add Another Document
                        </button>
                    </div>
                </div>

                <div style="margin-top: 20px; margin-bottom: 14px;">
                    <p style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 10px; color: #1e3a8a;">Researcher's Manuscript</p>
                    <div style="border: 2px dashed #bfdbfe; border-radius: 10px; padding: 16px 20px; background: #f8faff; display: flex; align-items: center; gap: 16px;">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.5" style="flex-shrink: 0;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                        <div style="flex: 1;">
                            <p style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 11px; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 2px;">Upload Manuscript</p>
                            <p style="font-size: 11px; color: #6b7280;">PDF, DOC, DOCX — Max 20MB</p>
                        </div>
                        <label style="cursor: pointer; flex-shrink: 0;">
                            <input type="file" id="manuscript-upload" name="doc_manuscript[]" accept=".pdf,.doc,.docx" style="display: none;" required onchange="handleManuscriptUpload(this)" />
                            <span style="background: #1e3a8a; color: white; padding: 8px 16px; border-radius: 7px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; cursor: pointer; white-space: nowrap;">Choose File</span>
                        </label>
                    </div>

                    <div id="manuscript-preview" style="display: none; margin-top: 10px; border: 2px solid #e5e7eb; border-radius: 10px; padding: 12px 16px; background: white;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 38px; height: 38px; background: #eff6ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            </div>
                            <div style="flex: 1;">
                                <p id="manuscript-filename" style="font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 2px 0;">-</p>
                                <p id="manuscript-filesize" style="font-size: 11px; color: #6b7280; margin: 0;">-</p>
                            </div>
                            <button type="button" onclick="removeManuscript()" style="background: #fee2e2; color: #dc2626; border: none; padding: 5px 12px; border-radius: 6px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer;">Remove</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Protocol Review + Assessment MERGED -->
        <div class="form-section hidden" id="section-5">
          <h2 class="section-title">Protocol Review &amp; Assessment</h2>

          <!-- PART 1: Scientific Design (was section 5) -->
          <div style="border: 2px solid #e5e7eb; border-radius: 10px; overflow: hidden; margin-bottom: 28px;">
            <table style="width: 100%; border-collapse: collapse; font-family: 'Inter', sans-serif; font-size: 12px;">
              <thead>
                <tr style="background: white; color: #111827;">
                  <th style="padding: 14px 16px; text-align: left; font-family: 'Montserrat', sans-serif; font-size: 12px; font-weight: 800; border: 1px solid #e5e7eb; width: 38%;">ASSESSMENT POINTS</th>
                  <th colspan="3" style="padding: 14px 16px; text-align: center; font-family: 'Montserrat', sans-serif; font-size: 11px; font-weight: 700; border: 1px solid #e5e7eb; color: #374151;">To be filled out by the PI</th>
                </tr>
                <tr style="background: white; color: #374151;">
                  <th style="padding: 10px 16px; border: 1px solid #e5e7eb;"></th>
                  <th colspan="3" style="padding: 10px 16px; text-align: left; font-family: 'Inter', sans-serif; font-size: 11px; font-weight: 600; border: 1px solid #e5e7eb;">Indicate if the study protocol contains the specified assessment point</th>
                </tr>
                <tr style="background: #e5e7eb;">
                  <th style="padding: 8px 16px; border: 1px solid #d1d5db;"></th>
                  <th style="padding: 8px 16px; text-align: center; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; border: 1px solid #d1d5db; width: 12%;">YES</th>
                  <th style="padding: 8px 16px; text-align: center; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; border: 1px solid #d1d5db; width: 12%;">NO</th>
                  <th style="padding: 8px 16px; text-align: center; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; border: 1px solid #d1d5db; width: 18%;">Line &amp; Page<br><span style="font-weight: 500; font-size: 9px; opacity: 0.75;">Where Found</span></th>
                </tr>
              </thead>
              <tbody>
                <tr style="background: #e5e7eb;"><td colspan="4" style="padding: 10px 16px; border: 1px solid #d1d5db; font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; color: #1e3a8a;">1. &nbsp; Scientific Design</td></tr>
                <!-- SECTION 1: Scientific Design -->
                <tr style="background: #e5e7eb;">
                </tr>
                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.1. Objectives</strong><br><span style="color: #6b7280;">Review of viability of expected output</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.1]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.1]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.1]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Page/Line" />
                </td>
                </tr>

                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.2. Literature review</strong><br><span style="color: #6b7280;">Review of results of previous animal/human studies showing known risks and benefits of intervention, including known adverse drug effects, in case of drug trials</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.2]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.2]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.2]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Pg. #/ Ln #" />
                </td>
                </tr>

                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.3. Research design</strong><br><span style="color: #6b7280;">Review of appropriateness of design in view of objectives</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.3]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.3]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.3]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Pg. #/ Ln #" />
                </td>
                </tr>

                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.4. Sampling design</strong><br><span style="color: #6b7280;">Review of appropriateness of sampling methods and techniques</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.4]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.4]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.4]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Pg. #/ Ln #" />
                </td>
                </tr>

                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.5. Sample size</strong><br><span style="color: #6b7280;">Review of justification of sample size</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.5]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.5]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.5]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Pg. #/ Ln #" />
                </td>
                </tr>

                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.6. Statistical analysis plan (SAP)</strong><br><span style="color: #6b7280;">Review of appropriateness of statistical methods to be used and how participant data will be summarized</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.6]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.6]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.6]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Pg. #/ Ln #" />
                </td>
                </tr>

                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.7. Data analysis plan</strong><br><span style="color: #6b7280;">Review of appropriateness of statistical and non-statistical methods of data analysis</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.7]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.7]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.7]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Pg. #/ Ln #" />
                </td>
                </tr>

                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.8. Inclusion criteria</strong><br><span style="color: #6b7280;">Review of precision of criteria both for scientific merit and safety concerns; and of equitable selection</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.8]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.8]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.8]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Pg. #/ Ln #" />
                </td>
                </tr>

                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.9. Exclusion criteria</strong><br><span style="color: #6b7280;">Review of precision of criteria both for scientific merit and safety concerns; and of justified selection</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.9]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.9]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.9]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Pg. #/ Ln #" />
                </td>
                </tr>

                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.10. Exclusion criteria</strong><br><span style="color: #6b7280;">Review of criteria precision both for scientific merit and safety concerns</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.10]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.10]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.10]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Pg. #/ Ln #" />
                </td>
                </tr>

                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.11. Refusal to participate or discontinuance</strong><br>
                    <span style="color: #6b7280;">Refusal to participate or discontinuance at any time will involve penalty or loss of benefits to which the subject is entitled?</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.11]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.11]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.11]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Page/Line" />
                </td>
            </tr>

            <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.12. Statement that it involves research</strong>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.12]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.12]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.12]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Page/Line" />
                </td>
            </tr>

            <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.13. Approximate number of participants in the study</strong>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.13]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.13]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.13]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Page/Line" />
                </td>
            </tr>

            <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.14. Expected benefits</strong><br>
                    <span style="color: #6b7280;">Expected benefits to the community or to society, or contributions to scientific knowledge</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.14]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.14]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.14]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Page/Line" />
                </td>
            </tr>

            <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.15. Post-study access</strong><br>
                    <span style="color: #6b7280;">Description of post-study access to the study product or intervention that have been proven safe and effective</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.15]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.15]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.15]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Page/Line" />
                </td>
            </tr>

            <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.16. Anticipated payment</strong><br>
                    <span style="color: #6b7280;">Anticipated payment, if any, to the participant in the course of the study whether money or other forms of material goods, and if so, the kind and amount</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.16]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.16]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.16]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Page/Line" />
                </td>
            </tr>

            <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.17. Anticipated expenses</strong><br>
                    <span style="color: #6b7280;">Anticipated expenses, if any, to the participant in the course of the study</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.17]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.17]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.17]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Page/Line" />
                </td>
            </tr>

            <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>1.18. Direct access to medical records</strong><br>
                    <span style="color: #6b7280;">Statement that the study monitor(s), auditor(s), the BERC, and regulatory authorities will be granted direct access to participant's medical records for purposes ONLY of verification of clinical trial procedures and data</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.18]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[1.18]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[1.18]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Page/Line" />
                </td>
            </tr>

                <!-- SECTION 2: Conduct of Study -->
                <tr style="background: #e5e7eb;">
                <td colspan="4" style="padding: 10px 16px; border: 1px solid #d1d5db; font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; color: #1e3a8a;">
                    2. &nbsp; Conduct of Study
                </td>
                </tr>

                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>2.1. Specimen handling</strong><br><span style="color: #6b7280;">Review of specimen storage, access, disposal, and terms of use</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[2.1]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[2.1]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[2.1]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                </td>
                </tr>

                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>2.2. PI qualifications</strong><br><span style="color: #6b7280;">Review of CV and relevant certifications to ascertain capability to manage study related risks</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[2.2]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[2.2]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[2.2]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                </td>
                </tr>

                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>2.3. Suitability of site</strong><br><span style="color: #6b7280;">Review of adequacy of qualified staff and infrastructures</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[2.3]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[2.3]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[2.3]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                </td>
                </tr>

                <tr>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                    <strong>2.4. Duration</strong><br><span style="color: #6b7280;">Review of length/extent of human participant involvement in the study</span>
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[2.4]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="text-align: center; border: 1px solid #e5e7eb;">
                    <input type="radio" name="remarks[2.4]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                </td>
                <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                    <input type="text" name="line_pages[2.4]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                </td>
                </tr>

                <!-- SECTION 3: Ethical Considerations -->
                <tr style="background: #e5e7eb;">
                <td colspan="4" style="padding: 10px 16px; border: 1px solid #d1d5db; font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; color: #1e3a8a;">
                    3. &nbsp; Ethical Considerations
                </td>
                </tr>

                <tr>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                        <strong>3.1. Conflict of interest</strong><br>
                        <span style="color: #6b7280;">Review of management of conflict arising from financial, familial, or proprietary considerations of the PI, sponsor, or the study site</span>
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.1]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.1]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                        <input type="text" name="line_pages[3.1]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                    </td>
                </tr>

                <tr>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                        <strong>3.2. Privacy and confidentiality</strong><br>
                        <span style="color: #6b7280;">Review of measures or guarantees to protect privacy and confidentiality of participant information as indicated by data collection methods including data protection plans</span>
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.2]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.2]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                        <input type="text" name="line_pages[3.2]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                    </td>
                </tr>

                <tr>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                        <strong>3.3. Informed consent process</strong><br>
                        <span style="color: #6b7280;">Review of application of the principle of respect for persons, who may solicit consent, how and when it will be done; who may give consent especially in case of special populations like minors and those who are not legally competent to give consent, or indigenous people which require additional clearances</span>
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.3]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.3]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                        <input type="text" name="line_pages[3.3]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                    </td>
                </tr>

                <tr>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                        <strong>3.4. Vulnerable study populations</strong><br>
                        <span style="color: #6b7280;">Review of involvement of vulnerable study populations and impact on informed consent. Vulnerable groups include children, the elderly, ethnic and racial minority groups, the homeless, prisoners, people with incurable disease, people who are politically powerless, or junior members of a hierarchical group.</span>
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.4]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.4]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                        <input type="text" name="line_pages[3.4]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                    </td>
                </tr>

                <tr>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                        <strong>3.5. Recruitment methods</strong><br>
                        <span style="color: #6b7280;">Review of manner of recruitment including appropriateness of identified recruiting parties</span>
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.5]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.5]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                        <input type="text" name="line_pages[3.5]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                    </td>
                </tr>

                <tr>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                        <strong>3.6. Assent requirements</strong><br>
                        <span style="color: #6b7280;">Review of feasibility of obtaining assent vis à vis incompetence to consent; Review of applicability of the assent age brackets in children (0-under 7: No assent; 7-under 12: Verbal Assent; 12-under 15: Simplified Assent Form; 15-under 18: Co-sign informed consent form)</span>
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.6]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.6]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                        <input type="text" name="line_pages[3.6]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                    </td>
                </tr>

                <tr>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                        <strong>3.7. Risks and mitigation</strong><br>
                        <span style="color: #6b7280;">Review of level of risk and measures to mitigate these risks (including physical, psychological, social, economic), including plans for adverse event management; Review of justification for allowable use of placebo as detailed in the Declaration of Helsinki</span>
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.7]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.7]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                        <input type="text" name="line_pages[3.7]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                    </td>
                </tr>

                <tr>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                        <strong>3.8. Benefits</strong><br>
                        <span style="color: #6b7280;">Review of potential direct benefit to participants; the potential to yield generalizable knowledge about the participants' condition/problem; non-material compensation to participant (health education or other creative benefits), where no clear, direct benefit from the project will be received by the participant</span>
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.8]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.8]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                        <input type="text" name="line_pages[3.8]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                    </td>
                </tr>

                <tr>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                        <strong>3.9. Financial compensation</strong><br>
                        <span style="color: #6b7280;">Review of amount and method of compensations, financial incentives, or reimbursement of study-related expenses</span>
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.9]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.9]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                        <input type="text" name="line_pages[3.9]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                    </td>
                </tr>

                <tr>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                        <strong>3.10. Community impact</strong><br>
                        <span style="color: #6b7280;">Review of impact of the research on the community where the research occurs and/or to whom findings can be linked; including issues like stigma or draining of local capacity; sensitivity to cultural traditions, and involvement of the community in decisions about the conduct of study.</span>
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.10]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.10]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                        <input type="text" name="line_pages[3.10]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                    </td>
                </tr>

                <tr>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; font-size: 13px; color: #374151; vertical-align: top;">
                        <strong>3.11. Collaborative studies</strong><br>
                        <span style="color: #6b7280;">Review in terms of collaborative study especially in case of multi-country/multi-institutional studies, including intellectual property rights, publication rights, information and responsibility sharing, transparency, and capacity building</span>
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.11]" value="Yes" required style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="text-align: center; border: 1px solid #e5e7eb;">
                        <input type="radio" name="remarks[3.11]" value="No" style="accent-color:#1e3a8a; width:15px;height:15px;" />
                    </td>
                    <td style="padding: 12px 16px; border: 1px solid #e5e7eb; vertical-align: top;">
                        <input type="text" name="line_pages[3.11]" style="border:none;border-bottom:1px solid #e5e7eb;width:100%;outline:none;font-size:12px;" placeholder="Page/Line" />
                    </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Section 6: ICF Evaluation -->
        <div class="form-section hidden" id="section-6">
          <h2 class="section-title">Informed Consent Form / Assent Form Evaluation Sheet</h2>
          <div class="subsection-heading">Essential Elements</div>
          <div style="border: 2px solid #e5e7eb; border-radius: 10px; overflow: hidden; margin-bottom: 24px;">
            <table style="width: 100%; border-collapse: collapse; font-family: 'Inter', sans-serif; font-size: 12px;">
              <thead>
                <tr style="background: #1e3a8a; color: white;">
                  <th style="padding: 12px 16px; text-align: left; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; width: 45%; border-right: 1px solid #1e40af;">Essential Elements<br><span style="font-weight: 500; font-size: 9px; opacity: 0.85;">(as applicable to the study)</span></th>
                  <th style="padding: 12px 16px; text-align: center; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; border-right: 1px solid #1e40af; width: 18%;">ICF Has Element<br><span style="font-weight: 500; font-size: 9px; opacity: 0.85;">YES</span></th>
                  <th style="padding: 12px 16px; text-align: center; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; border-right: 1px solid #1e40af; width: 18%;">ICF Has Element<br><span style="font-weight: 500; font-size: 9px; opacity: 0.85;">NO</span></th>
                  <th style="padding: 12px 16px; text-align: center; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; width: 19%;">Line &amp; Page<br><span style="font-weight: 500; font-size: 9px; opacity: 0.85;">Where Found</span></th>
                </tr>
              </thead>
              <tbody id="icf-table-body"></tbody>
            </table>
          </div>
        </div>

        <!-- Section 7: Payment -->
        <div class="form-section hidden" id="section-7">
          <h2 class="section-title">Payment</h2>
          <div class="instructions-box">
            <p><strong>Payment Instructions:</strong> Please send your payment to any of the e-wallet numbers listed below, then upload a photo or screenshot of your payment receipt to complete your submission.</p>
          </div>

          <div class="subsection-heading">Online Payment Options</div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 28px;" id="payment-cards-grid">

            @foreach($paymentMethods as $method)
            <div class="payment-card {{ !$method->is_active ? 'unavailable' : '' }}" style="border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; background: white; position: relative; transition: all 0.3s ease;">

              <div style="width: 52px; height: 52px; background: {{ $method->bg_color }}; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; transition: all 0.3s ease;">
                  @if($method->logo_path)
                      <img src="{{ asset($method->logo_path) }}" alt="{{ $method->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                  @else
                      <span style="color: white; font-weight: 800; font-size: 18px;">{{ $method->icon_label }}</span>
                  @endif
              </div>

              <div style="flex:1;">
                <p style="font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 13px; color: {{ $method->bg_color }}; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.04em; transition: color 0.3s;">
                    {{ $method->name }}
                </p>
                <p style="font-family: 'Inter', sans-serif; font-size: 18px; font-weight: 700; color: #111827; letter-spacing: 0.05em;">
                    {{ $method->account_number }}
                </p>
                <p style="font-family: 'Inter', sans-serif; font-size: 11px; color: #6b7280; margin-top: 2px;">
                    Account Name: {{ $method->account_name }}
                </p>
              </div>

              @if(!$method->is_active)
              <div style="position: absolute; top: 10px; right: 10px; background: #f3f4f6; color: #9ca3af; font-family: 'Montserrat', sans-serif; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; padding: 3px 8px; border-radius: 20px; border: 1px solid #e5e7eb;">
                  Not Available
              </div>
              @endif

            </div>
            @endforeach

          </div>


          <div class="subsection-heading">Upload Proof of Payment</div>
          <div style="border: 2px dashed #bfdbfe; border-radius: 10px; padding: 16px 20px; background: #f8faff; margin-bottom: 20px; display: flex; align-items: center; gap: 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.5" style="flex-shrink: 0;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            <div style="flex: 1;">
                <p style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 11px; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 2px;">Upload Payment Receipt</p>
                <p style="font-size: 11px; color: #6b7280;">Screenshot or photo of your e-wallet or bank transaction confirmation · JPG, PNG, PDF — Max 10MB</p>
            </div>
            <label style="cursor: pointer; flex-shrink: 0;">
                <input type="file" name="proof_of_payment_file" id="receipt-upload" accept="image/*,.pdf" style="display: none;" onchange="handleReceiptUpload(this)" />
                <span style="background: #1e3a8a; color: white; padding: 8px 18px; border-radius: 7px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; cursor: pointer; white-space: nowrap;">Choose File</span>
            </label>
          </div>

          <div id="receipt-preview" style="display: none; border: 2px solid #e5e7eb; border-radius: 12px; padding: 16px; background: white;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; background: #eff6ff; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg></div>
                <div style="flex: 1;"><p id="receipt-filename" style="font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 2px;">-</p><p id="receipt-filesize" style="font-size: 11px; color: #6b7280;">-</p></div>
                <button type="button" onclick="removeReceipt()" style="background: #fee2e2; color: #dc2626; border: none; padding: 6px 14px; border-radius: 6px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer;">Remove</button>
            </div>
            <img id="receipt-img-preview" src="" alt="Receipt" style="display: none; width: 100%; max-height: 300px; object-fit: contain; border-radius: 8px; margin-top: 14px; border: 1px solid #e5e7eb;" />
          </div>

          <div class="subsection-heading">Upload E-Signature</div>
          <div style="border: 2px dashed #bfdbfe; border-radius: 10px; padding: 16px 20px; background: #f8faff; margin-bottom: 20px; display: flex; align-items: center; gap: 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.5" style="flex-shrink: 0;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            <div style="flex: 1;">
                <p style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 11px; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 2px;">Upload Your Signature</p>
                <p style="font-size: 11px; color: #6b7280;">Clear photo or scanned image of your signature on a white background · JPG, PNG — Max 5MB</p>
            </div>
            <label style="cursor: pointer; flex-shrink: 0;">
                <input type="file" name="e_signature" id="signature-upload" accept="image/png, image/jpeg, image/jpg" style="display: none;" onchange="handleSignatureUpload(this)" required />
                <span style="background: #1e3a8a; color: white; padding: 8px 18px; border-radius: 7px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; cursor: pointer; white-space: nowrap;">Choose File</span>
            </label>
          </div>

          <div id="signature-preview" style="display: none; border: 2px solid #e5e7eb; border-radius: 12px; padding: 16px; background: white; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; background: #eff6ff; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <div style="flex: 1;">
                    <p id="signature-filename" style="font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 2px;">-</p>
                    <p id="signature-filesize" style="font-size: 11px; color: #6b7280;">-</p>
                </div>
                <button type="button" onclick="removeSignature()" style="background: #fee2e2; color: #dc2626; border: none; padding: 6px 14px; border-radius: 6px; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer;">Remove</button>
            </div>
            <div style="background: #f9fafb; border: 1px dashed #d1d5db; border-radius: 8px; margin-top: 14px; padding: 10px; display: flex; justify-content: center;">
                <img id="signature-img-preview" src="" alt="Signature Preview" style="display: none; max-width: 100%; max-height: 120px; object-fit: contain;" />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Amount Paid</label>
            <input type="number"
                name="amount_paid"
                class="form-input"
                step="0.01"
                max="10000"
                placeholder="Enter amount"
                required
                oninput="if(this.value > 10000) this.value = 10000;" />
          </div>

          <div class="form-group">
              <label class="form-label">Payment Method Used</label>
              <select class="form-input" id="payment-method" name="payment_method" required>
                  <option value="">-- Select --</option>

                  {{-- Only show ACTIVE methods in the dropdown --}}
                  @foreach($paymentMethods as $method)
                      @if($method->is_active)
                          <option value="{{ $method->name }}">{{ $method->name }}</option>
                      @endif
                  @endforeach

                  <option value="Other">Other</option>
              </select>
          </div>

            <div class="form-group">
                <label class="form-label">Reference / Transaction No.</label>
                <input type="text"
                    class="form-input"
                    id="payment-ref"
                    name="reference_number"
                    placeholder="Enter transaction reference number"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                    required />
            </div>
        </div>

        <!-- Section 8: Submit -->
        <div class="form-section hidden" id="section-8">
          <h2 class="section-title">Review and Submit</h2>
          <div class="summary-header-card">
            <h3>Application Summary</h3>
            <p style="opacity: 0.85; font-size: 13px; font-weight: 500;">Please review your information carefully before submitting</p>
          </div>
          <div class="summary-section">
            <h4>📋 Study Information</h4>
            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 14px;">
              <div><p style="margin: 0; color: #6b7280; font-size: 11px; font-family: 'Montserrat', sans-serif; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Title of Study</p><p id="summary-title" style="margin: 4px 0 0 0; color: #111827; font-size: 14px; font-weight: 600;">-</p></div>
              <div><p style="margin: 0; color: #6b7280; font-size: 11px; font-family: 'Montserrat', sans-serif; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Researcher Name</p><p id="summary-researcher" style="margin: 4px 0 0 0; color: #111827; font-size: 14px; font-weight: 600;">-</p></div>
              <div><p style="margin: 0; color: #6b7280; font-size: 11px; font-family: 'Montserrat', sans-serif; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Email</p><p id="summary-email" style="margin: 4px 0 0 0; color: #111827; font-size: 14px; font-weight: 600;">-</p></div>
              <div><p style="margin: 0; color: #6b7280; font-size: 11px; font-family: 'Montserrat', sans-serif; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Institution</p><p id="summary-institution" style="margin: 4px 0 0 0; color: #111827; font-size: 14px; font-weight: 600;">-</p></div>
              <div><p style="margin: 0; color: #6b7280; font-size: 11px; font-family: 'Montserrat', sans-serif; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Study Site</p><p id="summary-site" style="margin: 4px 0 0 0; color: #111827; font-size: 14px; font-weight: 600;">-</p></div>
              <div><p style="margin: 0; color: #6b7280; font-size: 11px; font-family: 'Montserrat', sans-serif; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Number of Participants</p><p id="summary-participants" style="margin: 4px 0 0 0; color: #111827; font-size: 14px; font-weight: 600;">-</p></div>
            </div>
          </div>
          <div class="summary-section">
            <h4>💳 Payment Details</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
              <div><p style="margin: 0; color: #6b7280; font-size: 11px; font-family: 'Montserrat', sans-serif; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Payment Method</p><p id="summary-payment-method" style="margin: 4px 0 0 0; color: #111827; font-size: 14px; font-weight: 600;">-</p></div>
              <div><p style="margin: 0; color: #6b7280; font-size: 11px; font-family: 'Montserrat', sans-serif; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Reference No.</p><p id="summary-payment-ref" style="margin: 4px 0 0 0; color: #111827; font-size: 14px; font-weight: 600;">-</p></div>
            </div>
            <div id="summary-receipt-preview" style="display: none; margin-top: 14px; padding-top: 14px; border-top: 1px solid #f3f4f6;">
              <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 11px; font-family: 'Montserrat', sans-serif; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Payment Receipt</p>
              <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: #f9fafb; border-radius: 8px; border: 2px solid #f3f4f6;">
                <span style="color: #1e3a8a; font-size: 20px;">🧾</span>
                <span id="summary-receipt-name" style="color: #111827; font-size: 13px; font-weight: 500;">-</span>
              </div>
            </div>
          </div>
          <div class="summary-section">
            <div id="summary-documents" style="color: #6b7280; font-size: 13px;"><p>No documents uploaded</p></div>
          </div>
          <div class="confirm-box" style="margin-bottom: 20px;">
            <label style="display: flex; align-items: flex-start; cursor: pointer; gap: 12px;">
              <input type="checkbox" id="confirm-checkbox" style="margin-top: 3px; width: 17px; height: 17px; cursor: pointer; accent-color: #1e3a8a;" />
              <span style="color: #374151; line-height: 1.6; font-size: 14px; font-weight: 500;">
                I confirm that all the information provided above is accurate and complete to the best of my knowledge. I understand that providing false information may result in the rejection of my application.
              </span>
            </label>
          </div>

          <div style="text-align: center; margin-bottom: 30px;">
            <button type="button" id="final-submit-btn" class="btn btn-primary" style="padding: 12px 30px; font-size: 16px;" disabled>Submit Application</button>
          </div>
        </div> <div class="button-group">
          <button type="button" class="btn btn-secondary" id="prevBtn" disabled>← Previous</button>
          <button type="button" class="btn btn-primary" id="nextBtn">Next →</button>
          </div>
      </form>

      <div id="success-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">

        <div style="background: white; border-radius: 12px; padding: 40px; max-width: 480px; width: 90%; max-height: 90vh; overflow-y: auto; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3); border: 2px solid #f3f4f6;">

            <div style="width: 72px; height: 72px; background: #1e3a8a; border-radius: 50%; margin: 0 auto 22px; display: flex; align-items: center; justify-content: center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>

            <h3 style="margin: 0 0 12px 0; color: #111827; font-family: 'Montserrat', sans-serif; font-size: 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.02em;">Application Submitted!</h3>

            <div style="background: #eff6ff; border: 2px solid #bfdbfe; border-radius: 8px; padding: 18px; margin: 22px 0; text-align: left;">
                <p style="margin: 0 0 10px 0; color: #1e3a8a; font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em;">Application Details</p>

                <div style="display: grid; gap: 7px;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #bfdbfe; padding-bottom: 6px; margin-bottom: 2px;">
                        <span style="color: #1e40af; font-size: 12px; font-weight: 500;">Application ID:</span>
                        <span id="app-id" style="color: #1e3a8a; font-weight: 900; font-size: 12px; font-family: monospace;">-</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #1e40af; font-size: 12px; font-weight: 500;">Applicant Name:</span>
                        <span id="app-name" style="color: #1e3a8a; font-weight: 800; font-size: 12px; text-align: right;">-</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #1e40af; font-size: 12px; font-weight: 500;">Study Title:</span>
                        <span id="app-title" style="color: #1e3a8a; font-weight: 800; font-size: 11px; text-align: right; max-width: 60%; line-height: 1.3;">-</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 4px; padding-top: 6px; border-top: 1px dashed #bfdbfe;">
                        <span style="color: #1e40af; font-size: 12px; font-weight: 500;">Payment Method:</span>
                        <span id="receipt-payment-method" style="color: #1e3a8a; font-weight: 800; font-size: 12px;">-</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #1e40af; font-size: 12px; font-weight: 500;">Reference No:</span>
                        <span id="receipt-payment-ref" style="color: #1e3a8a; font-weight: 800; font-size: 12px; font-family: monospace;">-</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 4px; padding-top: 6px; border-top: 1px dashed #bfdbfe;">
                        <span style="color: #1e40af; font-size: 12px; font-weight: 500;">Submitted On:</span>
                        <span id="submit-date" style="color: #1e3a8a; font-weight: 800; font-size: 12px;">-</span>
                    </div>
                </div>
            </div>

            <p style="margin: 16px 0; color: #6b7280; line-height: 1.7; font-size: 14px;">A confirmation email has been sent to <strong id="confirm-email" style="color: #111827;">your email</strong> with your application details and reference number.</p>

            <div style="background: #f9fafb; border: 2px solid #f3f4f6; border-radius: 8px; padding: 16px; margin: 18px 0;">
                <p style="margin: 0 0 6px 0; color: #1e3a8a; font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em;">📊 Track Your Application</p>
                <p style="margin: 0; color: #374151; font-size: 12px; line-height: 1.6;">You can monitor the status of your application in the <strong>Application Tracker</strong> section of your dashboard for real-time updates.</p>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 26px;">
                <button type="button" onclick="window.print()" style="flex: 1; padding: 11px; background: white; border: 2px solid #1e3a8a; color: #1e3a8a; border-radius: 8px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.07em; cursor: pointer;">Print Receipt</button>
                <button type="button" onclick="goToDashboard()" style="flex: 1; padding: 11px; background: #1e3a8a; border: none; color: white; border-radius: 8px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.07em; cursor: pointer;">Go to Dashboard</button>
            </div>
        </div>
     </div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 8;
        const completedSteps = new Set();

        // 1. Correctly define all DOM elements here
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");
        const finalSubmitBtn = document.getElementById("final-submit-btn");
        const consentCheck = document.getElementById("consentCheck"); // Step 1 consent
        const finalConfirmCheckbox = document.getElementById("confirm-checkbox"); // Step 8 consent
        const form = document.getElementById("sisForm");

        function showSection(step) {
            document.querySelectorAll(".form-section").forEach(s => s.classList.add("hidden"));
            document.getElementById(`section-${step}`).classList.remove("hidden");

            document.querySelectorAll(".step").forEach((stepEl, index) => {
                stepEl.classList.remove("active");
                completedSteps.has(index + 1) ? stepEl.classList.add("completed") : stepEl.classList.remove("completed");
                if (index + 1 === step) stepEl.classList.add("active");
            });

            prevBtn.disabled = step === 1;

            // Hide next button on the final step
            if (step === totalSteps) {
                nextBtn.classList.add("hidden");
            } else {
                nextBtn.classList.remove("hidden");
            }

            nextBtn.disabled = step === 1 ? !consentCheck.checked : false;
            currentStep = step;
            window.scrollTo({ top: 0, behavior: "smooth" });
        }

        document.querySelectorAll(".step").forEach((stepEl, index) => {
            const stepNumber = index + 1;
            if (stepNumber < totalSteps) {
                stepEl.style.cursor = "pointer";
                stepEl.addEventListener("click", () => showSection(stepNumber));
                stepEl.addEventListener("mouseenter", function() { if (!this.classList.contains("active")) this.style.opacity = "0.7"; });
                stepEl.addEventListener("mouseleave", function() { this.style.opacity = "1"; });
            } else {
                stepEl.style.cursor = "not-allowed";
                stepEl.style.opacity = "0.6";
            }
        });

        let icfEnabled = false;

        document.getElementById('add-researcher-btn')?.addEventListener('click', function() {
            const container = document.getElementById('co-researcher-list');
            const row = document.createElement('div');
            row.className = 'input-row';

            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-input';
            input.name = 'co_researchers[]';
            input.placeholder = 'Enter name';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn-remove';
            removeBtn.innerHTML = '✕';
            removeBtn.title = 'Remove Co-Researcher';

            removeBtn.onclick = function() { row.remove(); };

            row.appendChild(input);
            row.appendChild(removeBtn);
            container.appendChild(row);
        });

        function toggleICFStep(checked) {
            icfEnabled = checked;
            const icfStep = document.getElementById('icf-step');
            if(icfStep) {
                icfStep.style.opacity = checked ? '1' : '0.3';
                icfStep.style.pointerEvents = checked ? 'auto' : 'none';
                icfStep.title = checked ? '' : 'Informed Consent Form not selected';
            }
        }

        function getNextStep(step) {
            if (step === 5 && !icfEnabled) return 7; // skip ICF section
            return step + 1;
        }

        function getPrevStep(step) {
            if (step === 7 && !icfEnabled) return 5; // skip ICF section going back
            return step - 1;
        }

        consentCheck?.addEventListener("change", function() {
            if (currentStep === 1) nextBtn.disabled = !this.checked;
        });

        nextBtn?.addEventListener("click", function(event) {
            if (currentStep < totalSteps) {
                const currentSection = document.getElementById(`section-${currentStep}`);

                if (currentStep === 5 || currentStep === 6) {
                    if (!isProtocolReviewComplete()) {
                        alert("Please complete all Protocol Review & Assessment fields before proceeding.");
                        return;
                    }
                }

                // --- STEP 4 SPECIFIC VALIDATION ---
                if (currentStep === 4) {
                    let totalFiles = 0;
                    const fileInputs = currentSection.querySelectorAll('input[type="file"]');

                    // 1. Count how many files are actually selected
                    fileInputs.forEach(input => {
                        if (input.files && input.files.length > 0) {
                            totalFiles += input.files.length;
                        }
                    });

                    // 2. Error: Too many files
                    if (totalFiles > 16) {
                        alert(`Limit Exceeded: You have ${totalFiles} documents selected. Please remove ${totalFiles - 16} to stay within the 16-document limit.`);
                        return; // Stop and stay on Step 4
                    }

                    // 3. Error: Missing required documents
                    const requiredDocs = [
                        { name: 'doc_letter_request[]', label: 'Letter request for review' },
                        { name: 'doc_endorsement_letter[]', label: 'Endorsement/Referral Letter' },
                        { name: 'doc_full_proposal[]', label: 'Full proposal/study protocol' },
                        { name: 'doc_technical_review_approval[]', label: 'Technical Review Approval' },
                        { name: 'doc_curriculum_vitae[]', label: 'Curriculum Vitae' }
                    ];

                    let missingDocs = [];

                    requiredDocs.forEach(doc => {
                        // Find all file inputs that match this specific document name
                        const inputs = currentSection.querySelectorAll(`input[name="${doc.name}"]`);
                        let hasFile = false;

                        // Check if at least one of those inputs has a file selected
                        inputs.forEach(input => {
                            if (input.files && input.files.length > 0) {
                                hasFile = true;
                            }
                        });

                        // If no files were found for this category, add it to the missing list
                        if (!hasFile) {
                            missingDocs.push(doc.label);
                        }
                    });

                    // If there are any missing documents, alert the user and stop progression
                    if (missingDocs.length > 0) {
                        alert("Please upload the following required documents before proceeding:\n\n- " + missingDocs.join("\n- "));
                        return; // Stop and stay on Step 4
                    }
                }

                // --- GENERAL VALIDATION (For all other steps) ---
                const requiredInputs = currentSection.querySelectorAll("[required]");
                let isValid = true;

                requiredInputs.forEach(input => {
                    if (!input.value) {
                        isValid = false;
                        input.style.borderColor = "#dc2626";
                    } else {
                        input.style.borderColor = "#e5e7eb";
                    }
                });

                if (!isValid) {
                    alert("Please fill in all required fields.");
                    return;
                }

                // If everything above passes, move to next section
                completedSteps.add(currentStep);
                showSection(getNextStep(currentStep));
            }
        });

        prevBtn?.addEventListener("click", function() {
            if (currentStep > 1) showSection(getPrevStep(currentStep));
        });

        function isProtocolReviewComplete() {
            const section = document.getElementById("section-5");

            // 1. Check ALL radio groups (remarks)
            const remarkGroups = {};

            section.querySelectorAll('input[type="radio"][name^="remarks"]').forEach(radio => {
                if (!remarkGroups[radio.name]) {
                    remarkGroups[radio.name] = false;
                }
                if (radio.checked) {
                    remarkGroups[radio.name] = true;
                }
            });

            // If any group is unanswered → block
            for (let key in remarkGroups) {
                if (!remarkGroups[key]) return false;
            }

            // 2. Check all line/page inputs
            const lineInputs = section.querySelectorAll('input[name^="line_pages"]');
            for (let input of lineInputs) {
                if (input.value.trim() === "") {
                    return false;
                }
            }

            return true;
        }



        showSection(1);
        toggleICFStep(false);

        const uploadedFiles = [];
        function formatFileSize(bytes) {
            if (bytes === 0) return "0 Bytes";
            const k = 1024, sizes = ["Bytes","KB","MB","GB"];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i];
        }

        function toggleOthersInput(checkboxId, inputDivId) {
            const el = document.getElementById(checkboxId);
            const div = document.getElementById(inputDivId);
            if (!el || !div) return;

            const checked = el.checked;
            div.style.display = checked ? 'block' : 'none';

            if (!checked) {
                const input = div.querySelector('input[type="text"]');
                if (input) {
                    input.value = '';
                    // Reset the radio value back to 'Others' if unchecked
                    el.value = 'Others';
                }
            }
        }

        document.addEventListener('input', function(e) {
            // SYNC TYPED TEXT TO RADIO VALUE
            // This ensures that 'type_of_study' equals the typed text on submission
            if (e.target.name === 'type_of_study_others' || e.target.name === 'source_of_funding_others') {
                const radioName = e.target.name.replace('_others', '');
                const radioOthers = document.querySelector(`input[name="${radioName}"][value]:checked`);

                // If the 'Others' radio is checked, set its value to the typed text
                if (radioOthers && radioOthers.id.includes('others')) {
                    radioOthers.value = e.target.value;
                }
            }
        });

        document.addEventListener('change', function(e) {
            if (e.target.type === 'radio') {
                // Handle "Type of Study" resets
                if (e.target.name === 'type_of_study' && e.target.id !== 'study-others-check') {
                    resetOthersField('study-others-input', 'study-others-check');
                }
                // Handle "Source of Funding" resets
                if (e.target.name === 'source_of_funding' && e.target.id !== 'funding-others-check') {
                    resetOthersField('funding-others-input', 'funding-others-check');
                }
            }
        });

        // Helper function to keep code clean
        function resetOthersField(divId, radioId) {
            const div = document.getElementById(divId);
            if (div) {
                div.style.display = 'none';
                const inp = div.querySelector('input[type="text"]');
                if (inp) inp.value = '';
            }
            const radio = document.getElementById(radioId);
            if (radio) radio.value = 'Others'; // Reset radio value to default
        }

        function toggleSpecificUpload(checkbox) {
            const parent = checkbox.closest('.document-group');
            const bin = parent.querySelector('.upload-bin');
            if(!bin) return;

            const fileInput = bin.querySelector('input[type="file"]');
            const textInput = bin.querySelector('input[type="text"]');
            const innerCheckboxes = bin.querySelectorAll('input[type="checkbox"]');

            if (checkbox.checked) {
                bin.style.display = 'block';
                if (fileInput) {
                    if (fileInput.name !== 'doc_informed_consent' && fileInput.name !== 'doc_others') {
                        fileInput.required = true;
                    } else {
                        fileInput.required = false;
                    }
                }
                if (textInput) textInput.required = true;
            } else {
                bin.style.display = 'none';
                if (fileInput) { fileInput.required = false; fileInput.value = ""; }
                if (textInput) { textInput.required = false; textInput.value = ""; }
                if (innerCheckboxes) innerCheckboxes.forEach(cb => cb.checked = false);
            }
        }

        // NEW: Function to add a new Name+File row
        function addDocRow(containerId, inputNameBase) {
            const container = document.getElementById(containerId);

            // Create the div wrapper
            const newRow = document.createElement('div');
            newRow.className = 'doc-row';
            newRow.style.marginBottom = '8px';
            newRow.style.borderLeft = '2px solid #ddd';
            newRow.style.paddingLeft = '10px';
            newRow.style.marginTop = '8px';

            // Determine placeholder based on type
            const placeholderMap = [
                { key: 'special', text: "Name of Permit" },
                { key: 'cv', text: "Name of Researcher (e.g., Dr. Juan Dela Cruz)" },
                { key: 'questionnaire', text: "Name of Questionnaire (e.g., Informed Consent Form)" },
                { key: 'data-collection', text: "Name of Data Collection Form (e.g., Survey Instrument)" },
                { key: 'product-brochure', text: "Name of Product Brochure" },
                { key: 'philippine-fda', text: "Name of Authorization/License (e.g., FDA Clearance)" }
            ];

            const matchedItem = placeholderMap.find(item => inputNameBase.includes(item.key));
            const placeholder = matchedItem ? matchedItem.text : "Document Name";

            // Inject HTML
            newRow.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                    <input type="text"
                        name="${inputNameBase}_desc[]"
                        placeholder="${placeholder}"
                        style="width: 85%; padding: 6px; font-size: 12px; border: 1px solid #ccc; border-radius: 4px;" />
                    <button type="button" onclick="this.closest('.doc-row').remove()" style="color:red; border:none; background:none; cursor:pointer; font-size:14px;">&times;</button>
                </div>
                <input type="file"
                    name="${inputNameBase}[]"
                    accept=".pdf,.doc,.docx"
                    style="font-size: 12px;" />
            `;

            // Insert before the "Add" button (which is the last child)
            container.insertBefore(newRow, container.lastElementChild);
        }

        function toggleSubUpload(checkbox) {
            // Finds the specific file input container for this language
            const inputDiv = checkbox.closest('.sub-document-group').querySelector('.sub-upload-input');
            inputDiv.style.display = checkbox.checked ? 'block' : 'none';

            // Clear the file if unchecked to prevent accidental uploads
            if (!checkbox.checked) {
                inputDiv.querySelector('input[type="file"]').value = "";
            }
        }

        let receiptFile = null;
        function handleReceiptUpload(input) {
            const file = input.files[0];
            if (!file) return;
            if (file.size > 10 * 1024 * 1024) { alert('File too large. Max 10MB.'); return; }
            receiptFile = file;
            document.getElementById('receipt-filename').textContent = file.name;
            document.getElementById('receipt-filesize').textContent = formatFileSize(file.size);
            document.getElementById('receipt-preview').style.display = 'block';
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.getElementById('receipt-img-preview');
                    img.src = e.target.result;
                    img.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('receipt-img-preview').style.display = 'none';
            }
        }

        function handleICFToggle(val) {
            const noSection = document.getElementById('no-explanation-section');
            const yesSection = document.getElementById('yes-upload-section');
            const infoSection = document.getElementById('sufficient-info-section');

            if (val === 'yes') {
                if (yesSection) yesSection.style.display = 'block';
                if (infoSection) infoSection.style.display = 'block';
                if (noSection) noSection.style.display = 'none';

                if (typeof toggleICFStep === "function") toggleICFStep(true);

            } else if (val === 'no') {
                if (yesSection) yesSection.style.display = 'none';
                if (infoSection) infoSection.style.display = 'none';
                if (noSection) noSection.style.display = 'block';

                if (typeof toggleICFStep === "function") toggleICFStep(false);

            } else {
                if (yesSection) yesSection.style.display = 'none';
                if (infoSection) infoSection.style.display = 'none';
                if (noSection) noSection.style.display = 'none';

                if (typeof toggleICFStep === "function") toggleICFStep(false);
            }
        }

        function removeReceipt() {
            receiptFile = null;
            const uploadInput = document.getElementById('receipt-upload');
            if(uploadInput) uploadInput.value = '';
            document.getElementById('receipt-preview').style.display = 'none';
            document.getElementById('receipt-img-preview').src = '';
        }

        function handleSignatureUpload(input) {
            const file = input.files[0];
            if (file) {
                // Set filename and size
                document.getElementById('signature-filename').textContent = file.name;

                // Convert size to KB or MB
                const sizeKB = file.size / 1024;
                if (sizeKB > 1024) {
                    document.getElementById('signature-filesize').textContent = (sizeKB / 1024).toFixed(2) + ' MB';
                } else {
                    document.getElementById('signature-filesize').textContent = sizeKB.toFixed(2) + ' KB';
                }

                // Read the image and display it
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('signature-img-preview').src = e.target.result;
                    document.getElementById('signature-img-preview').style.display = 'block';
                    document.getElementById('signature-preview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        function removeSignature() {
            // Clear the file input
            document.getElementById('signature-upload').value = '';

            // Hide the preview card
            document.getElementById('signature-preview').style.display = 'none';

            // Clear the image source
            document.getElementById('signature-img-preview').src = '';
            document.getElementById('signature-img-preview').style.display = 'none';
        }

        let manuscriptFile = null;
        function handleManuscriptUpload(input) {
            const file = input.files[0];
            if (!file) return;
            if (file.size > 20 * 1024 * 1024) { alert('File too large. Max 20MB.'); input.value = ''; return; }
            manuscriptFile = file;
            document.getElementById('manuscript-filename').textContent = file.name;
            document.getElementById('manuscript-filesize').textContent = formatFileSize(file.size);
            document.getElementById('manuscript-preview').style.display = 'block';
        }

        function removeManuscript() {
            manuscriptFile = null;
            const uploadInput = document.getElementById('manuscript-upload');
            if(uploadInput) uploadInput.value = '';
            document.getElementById('manuscript-preview').style.display = 'none';
        }

        function populateSummary() {
            // 1. TEXT FIELDS (Using name attributes for safety)
            const getText = (name) => {
                const el = document.querySelector(`input[name="${name}"]`);
                return el ? el.value : '-';
            };

            document.getElementById("summary-title").textContent = getText('research_title') || "-";
            document.getElementById("summary-researcher").textContent = getText('name_of_researcher') || "-";
            document.getElementById("summary-email").textContent = getText('email') || "-";
            document.getElementById("summary-institution").textContent = getText('institution') || "-";
            document.getElementById("summary-site").textContent = getText('study_site') || "-";
            document.getElementById("summary-participants").textContent = getText('study_participants') || "-";

            // 2. PAYMENT DETAILS
            const payMethod = document.querySelector('select[name="payment_method"]');
            document.getElementById('summary-payment-method').textContent = payMethod ? (payMethod.options[payMethod.selectedIndex]?.text || '-') : '-';
            document.getElementById('summary-payment-ref').textContent = getText('reference_number') || "-";

            // 3. PAYMENT RECEIPT PREVIEW
            const receiptInput = document.querySelector('input[name="proof_of_payment_file"]');
            const receiptPreviewEl = document.getElementById('summary-receipt-preview');
            const receiptNameEl = document.getElementById('summary-receipt-name');

            if (receiptInput && receiptInput.files[0]) {
                const file = receiptInput.files[0];

                // 🪄 Create the temporary local URL
                const fileURL = URL.createObjectURL(file);

                if (receiptNameEl) {
                    // Inject a clickable link instead of just plain text
                    receiptNameEl.innerHTML = `
                        <a href="${fileURL}" target="_blank" style="color:#2563eb; font-weight:600; text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" title="Click to view receipt">
                            ${file.name} <span style="font-size:10px; margin-left:4px;">↗</span>
                        </a>
                    `;
                }

                if(receiptPreviewEl) receiptPreviewEl.style.display = 'block';
            } else if (receiptPreviewEl) {
                receiptPreviewEl.style.display = 'none';
            }

            // 4. DOCUMENTS SUMMARY
            const docContainer = document.getElementById("summary-documents");
            let summaryHTML = '';
            let hasDocs = false;

            // Helper to create the HTML row (Now with Viewable Links!)
            const createDocRow = (label, file) => {
                const ext = file.name.split('.').pop().toUpperCase();
                const size = formatFileSize(file.size);

                // 🪄 Create a temporary local URL so the browser can preview the file
                const fileURL = URL.createObjectURL(file);

                return `
                    <div style="display:flex; align-items:center; gap:10px; padding:8px; background:#f9fafb; border-radius:6px; border:2px solid #f3f4f6;">
                        <span style="color:#1e3a8a; font-family:'Montserrat',sans-serif; font-weight:900; font-size:10px; background:#eff6ff; padding:4px 8px; border-radius:4px; min-width: 35px; text-align:center;">${ext}</span>

                        <div style="flex:1; overflow:hidden;">
                            <div style="color:#374151; font-size:11px; font-weight:700; text-transform:uppercase;">${label}</div>
                            <a href="${fileURL}" target="_blank" style="color:#2563eb; font-size:13px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; text-decoration:none; display:block;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" title="Click to view file">
                                ${file.name}
                            </a>
                        </div>

                        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:2px;">
                            <span style="color:#6b7280; font-size:11px; white-space:nowrap;">${size}</span>
                            <a href="${fileURL}" target="_blank" style="font-size:10px; font-weight:800; color:#10b981; text-decoration:none; text-transform:uppercase; letter-spacing:0.5px;">View ↗</a>
                        </div>
                    </div>`;
            };

            // Master list of all document inputs and their default fallback labels
            const documentCategories = [
                { name: 'doc_manuscript', defaultLabel: 'Manuscript' },
                { name: 'doc_letter_request', defaultLabel: 'Letter of Request' },
                { name: 'doc_endorsement_letter', defaultLabel: 'Endorsement Letter' },
                { name: 'doc_full_proposal', defaultLabel: 'Full Proposal' },
                { name: 'doc_informed_consent', defaultLabel: 'Informed Consent Form', descSuffix: '_lang' },
                { name: 'doc_technical_review_approval', defaultLabel: 'Technical Review Approval' },
                { name: 'doc_curriculum_vitae', defaultLabel: 'Curriculum Vitae' },
                { name: 'doc_questionnaire', defaultLabel: 'Questionnaire' },
                { name: 'doc_data_collection', defaultLabel: 'Data Collection' },
                { name: 'doc_product_brochure', defaultLabel: 'Product Brochure' },
                { name: 'doc_phillipine_fda', defaultLabel: 'Philippine FDA' },
                { name: 'doc_special_populations', defaultLabel: 'Special Permit' },
                { name: 'doc_others', defaultLabel: 'Other Document' }
            ];

            // Dynamically process every category looking for arrays
            documentCategories.forEach(category => {
                // Notice the [] added to the selector to match array inputs
                const fileInputs = document.querySelectorAll(`input[name="${category.name}[]"]`);
                const descInputs = document.querySelectorAll(`input[name="${category.name}_desc[]"]`);

                fileInputs.forEach((input, index) => {
                    if (input.files.length > 0) {
                        // Use user description if provided, otherwise use the default category label
                        let finalDesc = category.defaultLabel;
                        if (descInputs[index] && descInputs[index].value.trim() !== '') {
                            finalDesc = descInputs[index].value.trim();
                        }

                        // Loop through all files in this specific input
                        Array.from(input.files).forEach(file => {
                            summaryHTML += createDocRow(finalDesc, file);
                            hasDocs = true;
                        });
                    }
                });
            });

            // Inject the generated HTML into your summary container
            if (docContainer) {
                docContainer.innerHTML = hasDocs ? summaryHTML : '<div style="font-size:12px; color:#6b7280; font-style:italic; text-align:center; padding: 10px;">No documents uploaded.</div>';
            }

            // Render Logic
            if (docContainer) {
                if (hasDocs) {
                    docContainer.innerHTML = `<div style="display: grid; gap: 8px;">${summaryHTML}</div>`;
                } else {
                    docContainer.innerHTML = '<p style="color:#6b7280; font-size:13px;">No documents uploaded</p>';
                }
            }
        }

        // 2. Enable final button when checkbox is checked
        finalConfirmCheckbox?.addEventListener("change", function() {
            if(finalSubmitBtn) finalSubmitBtn.disabled = !this.checked;
        });

        // 3. The Final Submit AJAX Call
        if (finalSubmitBtn) {
            finalSubmitBtn.addEventListener("click", async function (e) {
                e.preventDefault();

                if (!finalConfirmCheckbox || !finalConfirmCheckbox.checked) {
                    return alert("Please confirm that all information is correct before submitting.");
                }

                const originalText = this.innerText;
                this.innerText = "Processing...";
                this.disabled = true;

                try {
                    const formData = new FormData(form);
                    const token = document.querySelector('input[name="_token"]')?.value;

                    const response = await fetch('/review-form', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token
                        },
                        body: formData
                    });

                    const responseData = await response.json();

                    if (response.ok && responseData.success) {
                        // 1. Grab data from the Summary UI
                        const studyTitle = document.getElementById('summary-title').innerText;
                        const researcherEmail = document.getElementById('summary-email').innerText;
                        const paymentMethod = document.getElementById('summary-payment-method').innerText;
                        const paymentRef = document.getElementById('summary-payment-ref').innerText;

                        // 2. Format the date
                        const today = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

                        // 3. Inject backend data & summary data into the Modal
                        document.getElementById("app-id").textContent = responseData.protocol_code;
                        document.getElementById("app-name").textContent = responseData.name_of_researcher || document.getElementById('summary-researcher').innerText;
                        document.getElementById("app-title").textContent = studyTitle;
                        document.getElementById("receipt-payment-method").textContent = paymentMethod;
                        document.getElementById("receipt-payment-ref").textContent = paymentRef;
                        document.getElementById("submit-date").textContent = today;
                        document.getElementById("confirm-email").textContent = researcherEmail;

                        // 4. Display the modal
                        document.getElementById("success-modal").style.display = "flex";
                        window.scrollTo({ top: 0, behavior: "smooth" });
                    } else {
                        let errorMsg = responseData.message || "Unknown error occurred.";
                        if (responseData.errors) {
                            errorMsg = "Validation failed:\n" + Object.values(responseData.errors).flat().join('\n');
                        }
                        alert(errorMsg);

                        this.innerText = originalText;
                        this.disabled = false;
                    }
                } catch (error) {
                    console.error("Critical Failure:", error);
                    alert("Connection error or Server timeout.");
                    this.innerText = originalText;
                    this.disabled = false;
                }
            });
        }

        function addDocumentRow(containerId, fileInputName, textInputName = null, textPlaceholder = '') {
            const container = document.getElementById(containerId);

            const rowDiv = document.createElement('div');
            rowDiv.className = 'doc-row';
            rowDiv.style.display = 'flex';

            // Adjust layout depending on if there's a text input
            if (textInputName) {
                rowDiv.style.flexDirection = 'column';
                rowDiv.style.gap = '6px';
            } else {
                rowDiv.style.alignItems = 'center';
                rowDiv.style.gap = '8px';
            }

            rowDiv.style.marginBottom = '8px';
            rowDiv.style.background = '#f8faff';
            rowDiv.style.border = '1px dashed #bfdbfe';
            rowDiv.style.borderRadius = '8px';
            rowDiv.style.padding = textInputName ? '10px 12px' : '8px 12px';

            let htmlContent = '';

            if (textInputName) {
                htmlContent += `
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <input type="text" name="${textInputName}" placeholder="${textPlaceholder}" style="flex: 1; padding: 8px 10px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#1e3a8a'" onblur="this.style.borderColor='#d1d5db'" />
                        <button type="button" onclick="this.closest('.doc-row').remove()" style="background: #fee2e2; color: #dc2626; border: none; width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s;" title="Remove this document">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <input type="file" name="${fileInputName}" accept=".pdf,.doc,.docx" class="custom-file-input" />
                    </div>
                `;
            } else {
                htmlContent += `
                    <div style="flex: 1; display: flex; align-items: center;">
                        <input type="file" name="${fileInputName}" accept=".pdf,.doc,.docx" class="custom-file-input" />
                    </div>
                    <button type="button" onclick="this.closest('.doc-row').remove()" style="background: #fee2e2; color: #dc2626; border: none; width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s;" title="Remove this document">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                `;
            }

            rowDiv.innerHTML = htmlContent;

            const button = container.querySelector('button');
            container.insertBefore(rowDiv, button);
        }

        function validateDocumentCount(event) {
            let totalFiles = 0;

            // Select all file inputs specifically within Section 4
            const fileInputs = document.querySelectorAll('#section-4 input[type="file"]');

            // Loop through each input and add to the total if a file is selected
            fileInputs.forEach(input => {
                if (input.files && input.files.length > 0) {
                    totalFiles += input.files.length;
                }
            });

            // Check if it exceeds the limit
            if (totalFiles > 16) {
                alert(`You have attached ${totalFiles} documents. The maximum allowed is 16. Please remove ${totalFiles - 16} document(s) before proceeding.`);

                // Stop the button from going to the next step or submitting the form
                if (event) {
                    event.preventDefault();
                }
                return false;
            }

            return true; // Allow proceeding if 16 or fewer
        }

        function goToDashboard() { window.location.href = "/dashboard"; }

        const originalShowSection = showSection;
        showSection = function(step) { if (step === 8) populateSummary(); originalShowSection(step); };

        // Dynamically build the ICF Table
        const icfElements = [
            "Purpose of the study?",
            "Expected duration of participation?",
            "Procedures to be carried out?",
            "Discomforts and inconveniences?",
            "Risks (including possible discrimination)?",
            "Random assignment to the trial treatments?",
            "Benefits to the participants?",
            "Alternative treatments procedures?",
            "Compensation and / or medical treatments in case of injury?",
            "Who to contact for pertinent questions and or for assistance in a research-related injury?",
            "Refusal to participate or discontinuance at any time will Involve penalty or loss of benefits to which the subject is entitled?",
            "Statement that it involves research",
            "Approximate number of participants in the study",
            "Expected benefits to the community or to society, or contributions to scientific knowledge",
            "Description of post-study access to the study product or intervention that have been proven safe and effective",
            "Anticipated payment, if any, to the participant in the course of the study; whether money or other forms of material goods, and if so, the kind and amount",
            "Anticipated expenses, if any, to the participant in the course of the study",
            "Statement that the study monitor(s), auditor(s), the BERC, and regulatory authorities will be granted direct access to participant's medical records for purposes ONLY of verification of clinical trial procedures and data",
            "Statement describing extent of participant's right to access his/her records (or lack thereof vis à vis pending request for approval of non or partial disclosure)",
            "Description of policy regarding the use of genetic tests and familial genetic information, and the precautions in place to prevent disclosure of results to immediate family relative or to others without consent of the participant",
            "Possible direct or secondary use of participant's medical records and biological specimens taken in the course of clinical care or in the course of this study",
            "Plans to destroy collected biological specimen at the end of the study; if not, details about storage (duration, type of storage facility, location, access information) and possible future use; affirming participant's right to refuse future use, refuse storage, or have the materials destroyed",
            "Plans to develop commercial products from biological specimens and whether the participant will receive monetary or other benefit from such development.",
            "Statement that the BERC (specify) has approved the study, and may be reached through the following contact for information regarding rights of study participants, including grievances and complaints: BERC Chairperson"
        ];

        const tbody = document.getElementById('icf-table-body');

        if (tbody) {
            icfElements.forEach((element, index) => {
                const questionNum = `4.${index + 1}`;
                const row = document.createElement('tr');
                row.style.borderBottom = '1px solid #e5e7eb';
                row.innerHTML = `
                <td style="padding:10px 14px;border:1px solid #e5e7eb;font-size:13px;color:#374151;font-weight:500;vertical-align:top;">
                    <strong>${questionNum}</strong> ${element}
                </td>
                <td style="padding:10px 14px;border:1px solid #e5e7eb;text-align:center;vertical-align:top;">
                    <input type="radio" name="icf_remarks[${questionNum}]" value="Yes" required style="accent-color:#1e3a8a;width:15px;height:15px;" />
                </td>
                <td style="padding:10px 14px;border:1px solid #e5e7eb;text-align:center;vertical-align:top;">
                    <input type="radio" name="icf_remarks[${questionNum}]" value="No" style="accent-color:#1e3a8a;width:15px;height:15px;" />
                </td>
                <td style="padding:10px 14px;border:1px solid #e5e7eb;vertical-align:top;">
                    <input type="text" name="icf_line_pages[${questionNum}]" style="border:none;border-bottom:1px solid #e5e7eb;border-radius:0;padding:2px 4px;font-size:12px;width:100%;outline:none;" placeholder="Page/Line" />
                </td>
                `;
                tbody.appendChild(row);
            });
        }
    </script>
  </body>
</html>
