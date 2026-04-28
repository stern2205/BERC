<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResearcherController;
use App\Http\Controllers\ReviewForm;
use App\Http\Controllers\SecStaffController;
use App\Http\Controllers\SecretariatController;
use App\Http\Controllers\ResearchApplicationStatusController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\ReviewerController;
use App\Http\Controllers\ChairController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SignatureController;
use App\Mail\VerifCode;


/*
|--------------------------------------------------------------------------
| MODULE 1: PUBLIC ROUTES (GUEST USERS ONLY)
|--------------------------------------------------------------------------
| This group contains routes that can be accessed before login.
|
| Main user flow here:
| Landing Page
| → Login
| → Signup
| → Email Verification
| → Forgot Password / Reset Password
|
| Connected mostly to:
| - AuthController
|
| Middleware used:
| - prevent-back-history
|   This helps prevent users from going back to protected pages after logout.
*/
Route::middleware('prevent-back-history')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 1.1: LANDING PAGE
    |--------------------------------------------------------------------------
    | Main public entry page of the system.
    */
    Route::get('/', function () {
        return view('landing');
    })->name('landing');

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 1.2: LOGIN FLOW
    |--------------------------------------------------------------------------
    | Flow:
    | showLoginForm() → login()
    |
    | Connected to:
    | - AuthController::showLoginForm
    | - AuthController::login
    */
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 1.3: SIGNUP + ACCOUNT VERIFICATION FLOW
    |--------------------------------------------------------------------------
    | Flow:
    | showSignupForm()
    | → signup()
    | → showVerificationPage()
    | → verifyCode()
    |
    | Connected to:
    | - AuthController signup logic
    | - email verification code sending
    */
    Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup.form');
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup.submit');
    Route::get('/acctv', [AuthController::class, 'showVerificationPage'])->name('acctv.form');
    Route::post('/acctv', [AuthController::class, 'verifyCode'])->name('acctv.verify');

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 1.4: FORGOT PASSWORD / RESET PASSWORD FLOW
    |--------------------------------------------------------------------------
    | Full flow:
    | showForgotPasswordForm()
    | → sendResetCode()
    | → showVerifyForm()
    | → verifyResetCode()
    | → showResetPasswordForm()
    | → resetPassword()
    |
    | Connected to:
    | - AuthController password reset logic
    | - email verification code sending
    */
    Route::get('/forget', [AuthController::class, 'showForgotPasswordForm'])->name('forget.form');

    // Main named forgot-password route used by reset workflow
    Route::get('/forget-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.forget');
    Route::post('/forget-password', [AuthController::class, 'sendResetCode'])->name('password.sendCode');

    // Verification code step
    Route::get('/reset-verify', [AuthController::class, 'showVerifyForm'])->name('password.verifyForm');
    Route::post('/reset-verify', [AuthController::class, 'verifyResetCode'])->name('password.verifyCode');

    // New password step
    Route::get('/new-password', [AuthController::class, 'showResetPasswordForm'])->name('password.resetForm');
    Route::put('/new-password', [AuthController::class, 'resetPassword'])->name('password.reset.update');
});


/*
|--------------------------------------------------------------------------
| MODULE 2: AUTHENTICATED ROUTES (ALL LOGGED-IN USERS)
|--------------------------------------------------------------------------
| This group contains all routes that require authentication.
|
| Core system flow happens inside this group.
|
| Main system workflow:
| Researcher
| → Secretariat Staff / Secretariat
| → Reviewer
| → Chair
| → Final outputs (documents, decisions, reports)
|
| Middleware used:
| - auth
| - prevent-back-history
*/
Route::middleware(['auth', 'prevent-back-history'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 2.1: SHARED CORE USER FEATURES
    |--------------------------------------------------------------------------
    | Routes that are common or globally accessible to authenticated users.
    |
    | Connected to:
    | - dashboard
    | - profile management
      - settings page
    | - password updates
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/settings', [AuthController::class, 'showSettings'])->name('settings');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Shared profile / account updates
    Route::put('/user/password', [AuthController::class, 'updatePassword'])->name('password.update');
    Route::patch('/profile/image', [AuthController::class, 'updateProfileImage'])->name('profile.update_image');

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 2.2: DOCUMENT API (GENERAL)
    |--------------------------------------------------------------------------
    | Loads the documents of a protocol, usually through fetch/AJAX calls
    | from modals and pages.
    |
    | Connected to:
    | - DocumentsController::show
    | - secretariat pages
    | - decision pages
    | - researcher pages
    */
    Route::get('/documents/api/{protocol_code}', [DocumentsController::class, 'show'])
        ->name('api.documents.show')
        ->middleware('auth');

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 2.3: SECURE DOCUMENT VIEWER
    |--------------------------------------------------------------------------
    | This route streams a file directly from storage/app/documents instead of
    | exposing it publicly.
    |
    | Connected to:
    | - frontend file previews
    | - any document preview button/link using /view-document/...
    |
    | Security note:
    | This helps avoid direct public storage exposure.
    */
    Route::get('/view-document/{protocol_code}/{filename}', function ($protocol_code, $filename) {
        $fullPath = storage_path("app/documents/{$protocol_code}/{$filename}");

        if (!File::exists($fullPath)) {
            abort(404, "File not found at: " . $fullPath);
        }

        $contents = File::get($fullPath);
        $mimeType = File::mimeType($fullPath);

        return response($contents)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . basename($filename) . '"');
    })->where('filename', '.*')->name('view.document');


    /*
    |--------------------------------------------------------------------------
    | MODULE 3: RESEARCHER ROUTES
    |--------------------------------------------------------------------------
    | This module covers the Researcher side of the system.
    |
    | Main flow:
    | Submit application
    | → monitor status
    | → print forms
    | → submit resubmissions if revisions are needed
    |
    | Connected to:
    | - ReviewForm controller (actual submission logic)
    | - ResearcherController (UI and researcher actions)
    */

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 3.1: INITIAL APPLICATION SUBMISSION
    |--------------------------------------------------------------------------
    | Researcher opens the form and submits a new application.
    */
    Route::get('/review-form', [ResearcherController::class, 'showReviewForm'])->name('review.form');
    Route::post('/review-form', [ReviewForm::class, 'submit'])->name('research.submit');

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 3.2: APPLICATION TRACKING / HISTORY
    |--------------------------------------------------------------------------
    | Researcher checks current application status and completed reviews.
    */
    Route::get('/application-status', [ResearcherController::class, 'showApplicationStatus'])->name('application.status');
    Route::get('/completed-reviews', [ResearcherController::class, 'showApplicationHistory'])->name('application.history');
    Route::post('/complete-tutorial', [ResearcherController::class, 'completeTutorial'])->name('tutorial.complete');

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 3.3: RESUBMISSION FLOW
    |--------------------------------------------------------------------------
    | Used when the protocol needs revisions and the researcher must submit
    | updated documents / answers.
    |
    | Flow:
    | showResubmissionForm()
    | → submitResubmissionForm()
    |
    */
    Route::get('/resubmission-form/{protocol_code}', [ResearcherController::class, 'showResubmissionForm'])->name('resubmission.form');
    Route::post('/resubmission/store', [ResearcherController::class, 'submitResubmissionForm'])->name('resubmission.store');
/*
    |--------------------------------------------------------------------------
    | SUBMODULE 3.4: Rejected Protocol Secretarial Staff Flow
    |--------------------------------------------------------------------------
    | Used when the protocol is rejected by secretarial staff and the researcher must
    | submit updated documents / answers.
    |
    */
    Route::post('/researcher/application/{protocol_code}/resubmit', [ResearcherController::class, 'resubmitDocuments'])->name('researcher.application.resubmit');

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 3.5: RESEARCHER PDF PRINTING
    |--------------------------------------------------------------------------
    | Allows the researcher to print/download copies of their forms.
    |
    | Connected to:
    | - ResearcherController PDF methods
    | - Application Form and Resubmission Form is accessible to the staff as well
    */
    Route::get('/application-form/{id}/print', [ResearcherController::class, 'printApplicationForm'])->name('researcher.application.print');
    Route::get('/assessment-form/{id}/print', [ResearcherController::class, 'printAssessmentFormPDF'])->name('researcher.assessment.print');
    Route::get('/informed-consent/{id}/print', [ResearcherController::class, 'printInformedConsentFormPDF'])->name('researcher.informedconsent.print');
    Route::get('/resubmission/{id}/print', [ResearcherController::class, 'printResubmissionFormPdf'])->name('researcher.resubmission.print');


    /*
    |--------------------------------------------------------------------------
    | MODULE 4: SECRETARIAT STAFF ROUTES
    |--------------------------------------------------------------------------
    | This module is for secretariat staff support functions.
    |
    | Main purpose:
    | - monitor applications
    | - view application data
    | - history
    | - calendar
    | - payment settings page
    |
    | Connected to:
    | - SecStaffController
    */
    Route::get('/secstaff-applications', [SecStaffController::class, 'showSecstaffApplications'])->name('secstaff.applications');
    Route::get('/secstaff/applications/{protocol_code}', [SecStaffController::class, 'getApplicationData']);
    Route::get('/secstaff/history', [SecStaffController::class,'showHistory'])->name('secstaff.history');
    Route::get('/secstaff/calendar', [SecStaffController::class, 'showSecStaffCalendar'])->name('secstaff.calendar');
    Route::get('/secstaff/payment-settings', [SecStaffController::class, 'showPaymentSettings'])->name('secstaff.payment_settings');

    /*
    |--------------------------------------------------------------------------
    | MODULE 5: ADMIN / PAYMENT METHOD ROUTES
    |--------------------------------------------------------------------------
    | This module handles payment method configuration.
    |
    | Connected to:
    | - PaymentMethodController
    | - payment settings pages
    */
    Route::prefix('admin/payment-methods')->group(function () {
        Route::get('/', [PaymentMethodController::class, 'index']);
        Route::post('/', [PaymentMethodController::class, 'store']);
        Route::post('/{id}', [PaymentMethodController::class, 'update']);
        Route::delete('/{id}', [PaymentMethodController::class, 'destroy']);
    });


    /*
    |--------------------------------------------------------------------------
    | MODULE 6: SECRETARIAT ROUTES
    |--------------------------------------------------------------------------
    | This is one of the main workflow-processing modules.
    |
    | Main responsibilities:
    | - protocol evaluation
    | - assessment form evaluation
    | - decision letter creation
    | - revision comments / revision decisions
    | - revision validation
    | - calendar and history
    |
    | Connected heavily to:
    | - Reviewer module
    | - Chair module
    | - Documents module
    | - API endpoints below
    */

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 6.1: SECRETARIAT PAGE ROUTES
    |--------------------------------------------------------------------------
    | These routes load the actual Secretariat pages.
    */
    Route::get('/secretariat/protocol-evaluation', [SecretariatController::class, 'showProtocolEvaluation'])->name('secretariat.evaluation');
    Route::get('/secretariat/assessment', [SecretariatController::class, 'showAssessmentFormEvaluation'])->name('secretariat.assessment');
    Route::get('/secretariat/decision-letter', [SecretariatController::class, 'showDecisionLetter'])->name('secretariat.decision');
    Route::get('/secretariat/resubmission-page', [SecretariatController::class, 'showRevisionComments'])->name('secretariat.revision_forms');
    Route::get('/secretariat/revision/decision', [SecretariatController::class, 'showResubmissionDecision'])->name('secretariat.revision.decision');
    Route::get('/secretariat/reviewer-comments', [SecretariatController::class, 'showRevisionValidation'])->name('secretariat.revision_validation');
    Route::get('/secretariat/calendar', [SecretariatController::class, 'showCalendar'])->name('secretariat.calendar');
    Route::get('/secretariat/history', [SecretariatController::class, 'showHistory'])->name('secretariat.reports');

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 6.2: SECRETARIAT API ENDPOINTS
    |--------------------------------------------------------------------------
    | These are backend endpoints commonly called by JavaScript/fetch from
    | Secretariat pages. They save drafts, validations, synthesis, and
    | decision outputs.
    |
    | Turnover note:
    | If a Secretariat modal opens but save/submit buttons do nothing,
    | check these routes together with the frontend fetch URL and the
    | corresponding SecretariatController method.
    */

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 6.3: ORIGINAL PROTOCOLS API ENDPOINTS
    |--------------------------------------------------------------------------
    | These are backend endpoints for handling original protocol evaluations, including
    | saving the review classification and assigned reviewers, as well as handling
    | the saving and retrieval of synthesis and decision letter drafts for the original
    | application workflow.
    */

    //Saves the review classification and reviewers assigned for the protocol evaluation step
    Route::post('/research/status/{protocol_code}', [ResearchApplicationStatusController::class, 'changeStatus'])
        ->name('research.status.update');

    // Save synthesis for initial/original application workflow
    Route::post('/api/secretariat/synthesis/save', [SecretariatController::class, 'saveSynthesis'])
        ->name('secretariat.synthesis.save');

    // Save original decision letter
    Route::post('/api/secretariat/decision-letter/save', [SecretariatController::class, 'saveLetter'])
        ->name('secretariat.decision_letters.save');

    // Draft handling for original synthesis
    Route::get('/api/secretariat/synthesis/{protocol_code}/draft', [SecretariatController::class, 'getDraft']);
    Route::post('/api/secretariat/synthesis/{protocol_code}/draft', [SecretariatController::class, 'saveDraft']);

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 6.4: REVISION-related API ENDPOINTS
    |--------------------------------------------------------------------------
    | These are backend endpoints for handling revision-related actions,
    | including validating the resubmission form of its compliance with the revision requirements,
    | as well as saving and retrieving drafts for the resubmission synthesis and resubmission decision
    | outputs.
    */

    // Completes revision validation step and pushes workflow forward
    Route::post('/api/secretariat/revision/validate', [SecretariatController::class, 'completeRevisionValidation'])
        ->name('secretariat.revision.validate');

    // Save synthesis for resubmission workflow
    Route::post('/api/secretariat/resubmission/synthesis/save', [SecretariatController::class, 'saveResubmissionSynthesis'])
        ->name('secretariat.resubmission.synthesis.save');

    // Save resubmission decision letter
    Route::post('/api/secretariat/resubmission/decision/save', [SecretariatController::class, 'saveResubmissionDecision'])
        ->name('secretariat.resubmission.decision.save');

    // Get application details and documents for protocol-specific Secretariat views/modals
    Route::get('/secretariat/applications/{protocol_code}', [SecretariatController::class, 'getApplicationDetails']);

    // Draft handling for resubmission synthesis
    Route::get('/api/secretariat/resubmission/{protocol_code}/v{revision_number}/draft', [SecretariatController::class, 'getResubmissionDraft']);
    Route::post('/api/secretariat/resubmission/{protocol_code}/v{revision_number}/draft', [SecretariatController::class, 'saveResubmissionDraft']);

    Route::post('/secretariat/reassignment/{protocol_code}/expire-reviewer',
        [ResearchApplicationStatusController::class, 'expireAcceptedReviewerForReassignment']
    )->name('secretariat.reassignment.expire-reviewer');

    /*
    |--------------------------------------------------------------------------
    | MODULE 6: REVIEWER ROUTES
    |--------------------------------------------------------------------------
    | This module handles the Reviewer side of the workflow.
    |
    | Main responsibilities:
    | - invitation responses
    | - original assessments
    | - resubmission review
    | - reviewer calendar
    | - saving drafts / validations
    |
    | Connected to:
    | - ReviewerController
    | - Secretariat workflow
    | - Chair workflow
    */

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 6.1: REVIEWER PAGE ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/invitations', [ReviewerController::class, 'showInvitations'])->name('reviewer.invitations');
    Route::get('/assessment', [ReviewerController::class, 'showAssessment'])->name('reviewer.assessment');
    Route::get('/resubmission', [ReviewerController::class, 'showRevisionPage'])->name('reviewer.resubmissions');
    Route::get('/reviewer/calendar', [ReviewerController::class, 'showReviewerCalendar'])->name('reviewer.calendar');

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 6.2: REVIEWER ACTION ROUTES
    |--------------------------------------------------------------------------
    | These routes are triggered by reviewer actions on the frontend.
    */
    // Respond to reviewer invitation (accept/decline)
    Route::post('/reviewer/protocol/{protocol_code}/respond', [ReviewerController::class, 'respondToInvitation'])->name('reviewer.protocol.respond');

    // Validate the assessment form of the original application of the protocol code
    Route::post('/reviewer/assessment/{protocol_code}/validate', [ReviewerController::class, 'submitValidation'])->name('reviewer.assessment.validate');

    // Validate the resubmission review form of the protocol code and revision number
    Route::post('/reviewer/validate-revisions', [ReviewerController::class, 'validateRevisions'])->name('reviewer.validate-revisions');

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 6.3: REVIEWER DRAFT ROUTES
    |--------------------------------------------------------------------------
    | These endpoints save and retrieve draft data for both original and
    | resubmission review forms.
    */

    // Draft handling for original assessment form
    Route::get('/reviewer/assessment/{protocol_code}/draft', [ReviewerController::class, 'getDraft']);
    Route::post('/reviewer/assessment/{protocol_code}/draft', [ReviewerController::class, 'saveDraft']);

    // Draft handling for resubmission review form (includes revision number for version tracking)
    Route::get('/reviewer/assessment/{protocol_code}/v{revision_number}/draft', [ReviewerController::class, 'getResubmissionDraft']);
    Route::post('/reviewer/assessment/{protocol_code}/v{revision_number}/draft', [ReviewerController::class, 'saveResubmissionDraft']);


    /*
    |--------------------------------------------------------------------------
    | MODULE 7: CHAIR ROUTES
    |--------------------------------------------------------------------------
    | This module handles the Chair side of the system.
    |
    | Main responsibilities:
    | - final approval of decision letters
    | - resubmission decision handling
    | - consultant assignment
    | - protocol finalization
    | - staff management
    |
    | Connected to:
    | - Secretariat outputs
    | - final workflow status changes
    */

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 7.1: CHAIR-ONLY RESTRICTED ROUTES
    |--------------------------------------------------------------------------
    | This inner group is explicitly protected by role:chair middleware.
    */
    Route::middleware('role:chair')->group(function () {
        Route::get('/chair/create-account', [DashboardController::class, 'createAccountForm'])->name('chair.create');
        Route::post('/chair/create-account', [DashboardController::class, 'storeAccount'])->name('chair.store');
    });

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 7.2: CHAIR PAGE ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/chair/history', [ChairController::class, 'showChairHistory'])->name('chair.history');
    Route::get('/calendar', [ChairController::class, 'showChairCalendar'])->name('chair.calendar');
    Route::get('/pipeline/approval', [ChairController::class, 'showDecisionApproval'])->name('chair.approval');
    Route::get('/pipeline/revision/decision', [ChairController::class, 'showResubmissionDecision'])->name('chair.revision.decision');
    Route::get('/add-staff', [ChairController::class, 'showAddStaff'])->name('chair.add-staff');

    /*
    |--------------------------------------------------------------------------
    | SUBMODULE 7.3: CHAIR ACTION ROUTES
    |--------------------------------------------------------------------------
    | These routes are used when the Chair performs final actions.
    */

    // Saving the draft of the decision letter when chair changes the decision contents
    Route::post('/chair/decision-letter/save', [ChairController::class, 'saveDecisionLetter'])->name('chair.decision.save');

    // Assigns the external consultant to the protocol after chair selects the consultant and clicks assign, this route updates the protocol record with the assigned consultant and sends notification emails if needed.
    Route::post('/external-consultant/assign', [ChairController::class, 'assignConsultant'])->name('chair.consultant.assign');

    // Finalizes the protocol after chair gives the final approval, this saves the decision letter details
    Route::post('/chair/protocol/finalize', [ChairController::class, 'finalizeProtocol'])->name('chair.protocol.finalize');

    // Finalizes the decision for the resubmission after chair gives the final approval, this saves the resubmission decision details and updates the protocol status accordingly
    Route::post('/api/chair/resubmission/decision/save', [ChairController::class, 'saveOrValidateDecision'])->name('chair.resubmission.decision.save');

    // Staff management routes for the chair to add or remove user accounts, these routes are protected by role:chair middleware to ensure only the chair can perform these actions.
    Route::post('/staff/store', [ChairController::class, 'storeStaff'])->name('staff.store');
    Route::delete('/staff/{id}', [ChairController::class, 'destroyStaff'])->name('staff.destroy');

    /*
    |--------------------------------------------------------------------------
    | MODULE 9: GENERAL DOCUMENTS, PDF PRINTING, DOWNLOADS, CALENDAR
    |--------------------------------------------------------------------------
    | This module centralizes shared document and output routes.
    |
    | Main uses:
    | - printing PDFs
    | - viewing decision outputs
    | - logbook printing
    | - individual reviewer form printing
    | - revision document fetching
    | - ZIP download
    | - calendar events
    |
    | Connected to:
    | - DocumentsController
    | - CalendarController
    | - various frontend print/download buttons
    */

    // Duplicate documents API route also exists above; keep route names consistent when maintaining
    Route::get('/documents/api/{protocol_code}', [DocumentsController::class, 'show'])->name('api.documents.show');

    // Original decision letter PDF
    Route::get('/decision-letter/pdf/{protocol_code}', [DocumentsController::class, 'viewDecisionPdf'])->name('decision.pdf');

    // Resubmission decision PDF
    Route::get('/decision-letter/revision/{protocol_code}/{version}', [DocumentsController::class, 'viewRevisionDecisionPdf'])->name('revision_decision.print');

    // Routing logbook printing
    Route::get('/outgoinglogbook/print/{protocol_code}', [DocumentsController::class, 'printOutgoingLogbook'])->name('outgoing.logbook.print');
    Route::get('/incominglogbook/print/{protocol_code}', [DocumentsController::class, 'printIncomingLogbook'])->name('incoming.logbook.print');

    // Individual reviewer assessment / ICF printing
    Route::get('/assessment/{id}/reviewer/{reviewer_id}', [DocumentsController::class, 'printIndividualAssessmentFormPDF'])->name('assessment.individual.print');
    Route::get('/icf/{id}/reviewer/{reviewer_id}', [DocumentsController::class, 'printIndividualInformedConsentPDF'])->name('icf.individual.print');

    // Revision document loading API
    Route::get('/documents/api/revision/{protocol_code}/{revision_number}', [DocumentsController::class, 'getRevisionDocuments'])->middleware('auth');

    // Exemption certificate printing
    Route::get('/protocol/{protocol_code}/print-exemption', [DocumentsController::class, 'printExemptionCertificate'])->name('protocol.print-exemption');

    // Download all files of a protocol as ZIP
    Route::get('/documents/api/download-zip/{protocol_code}', [DocumentsController::class, 'downloadAllAsZip']);

    // Calendar event APIs
    Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');
    Route::get('/reviewer/calendar/events', [CalendarController::class, 'getReviewerEvents'])->name('reviewer.calendar.events');


    /*
    |--------------------------------------------------------------------------
    | MODULE 10: DIGITAL SIGNATURE ROUTES
    |--------------------------------------------------------------------------
    | Handles user signature upload, deletion, and secure viewing.
    |
    | Connected to:
    | - SignatureController
    | - user profile
    | - secure signature streaming
    | - logbooks / document signatures
    */

    // Upload a new signature
    Route::patch('/profile/signature', [SignatureController::class, 'upload'])->name('profile.update_signature');

    // Remove the current signature
    Route::delete('/profile/signature', [SignatureController::class, 'remove'])->name('profile.remove_signature');

    // Securely view a signature, either for the owner or authorized system use
    Route::get('/signature/user/{id}', [SignatureController::class, 'showSpecific'])->name('signature.view_specific');

});
