<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchApplications extends Model
{
    use HasFactory;

    protected $table = 'research_applications';

    // =========================================================================
    // 1. WORKFLOW STATUS CONSTANTS (The Map)
    // =========================================================================

    // Phase 1: Initial Processing
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_DOCUMENTS_CHECKING = 'documents_checking';
    const STATUS_CLASSIFICATION_REVIEW = 'classification_review';

    // Phase 2: Reviewer Assignment
    const STATUS_ASSIGNING_REVIEWERS = 'assigning_reviewers';
    // GATE 1: Stops here until Chair approves
    const STATUS_WAITING_CHAIR_REVIEWER_APPROVAL = 'waiting_chair_reviewer_approval';

    // Phase 3: The Meeting
    const STATUS_IN_MEETING = 'in_meeting';
    const STATUS_DRAFTING_DECISION = 'drafting_decision';

    // Phase 4: Final Decision
    // GATE 2: Stops here until Chair signs off
    const STATUS_WAITING_CHAIR_DECISION_APPROVAL = 'waiting_chair_decision_approval';

    // Phase 5: Result
    const STATUS_RETURNED_FOR_REVISION = 'returned_for_revision';
    const STATUS_COMPLETED = 'completed';

    // =========================================================================

    protected $fillable = [
        'user_id',
        'status', // <--- ADD THIS (Crucial for saving the status)
        'year',
        'type_of_research',
        'protocol_code',
        'research_title',
        'name_of_researcher',
        'co_researchers',
        'study_site',
        'tel_no',
        'mobile_no',
        'fax_no',
        'email',
        'institution',
        'institution_address',
        'type_of_study',
        'type_of_study1',
        'source_of_funding',
        'study_start_date',
        'study_end_date',
        'study_participants',
        'technical_review',
        'tracking_number',
        'has_been_submitted_to_another_berc',
        'brief_description',
        'review_classification',
        'reviewer1_assigned',
        'reviewer2_assigned',
        'external_consultant',
        'version',
        'e_signature', // <--- ADD THIS (For storing the path to the e-signature image)

        // --- 1:1 Document Columns ---
        'doc_letter_request',
        'doc_endorsement_letter',
        'doc_full_proposal',
        'doc_informed_consent_english',
        'doc_informed_consent_filipino',
        'doc_manuscript',
        'doc_technical_review_approval',
    ];

    protected $casts = [
        'co_researchers' => 'array',
    ];

    public function assessmentForm()
    {
        return $this->hasOne(AssessmentForm::class, 'protocol_code', 'protocol_code');
    }

    // Add this method to your ResearchApplications class
    public function informedConsent()
    {
        // 1st arg: The model you just shared (InformedConsent)
        // 2nd arg: Foreign key on 'icf_assessments' table
        // 3rd arg: Local key on 'research_applications' table
        return $this->hasOne(InformedConsent::class, 'protocol_code', 'protocol_code');
    }

    public function decisionLetter()
    {
        return $this->hasOne(DecisionLetter::class, 'protocol_code', 'protocol_code');
    }

    /**
     * Relationship to the Payment model
     * Linking via 'protocol_code'
     */
    public function payment()
    {
        // 1st argument: The related model
        // 2nd argument: Foreign key on the 'payments' table ('protocol_code')
        // 3rd argument: Local key on the 'research_applications' table ('protocol_code')
        return $this->hasOne(Payment::class, 'protocol_code', 'protocol_code');
    }

    /**
     * Relationship to the supplementary documents
     */

    /**
     * Relationship: An application has many basic requirements.
     */
    public function basicRequirements()
    {
        return $this->hasMany(BasicRequirement::class, 'protocol_code', 'protocol_code');
    }

    public function supplementaryDocuments()
    {
        return $this->hasMany(SupplementaryDocument::class, 'protocol_code', 'protocol_code');
    }

    /**
     * Relationship to the Status History Logs
     * Linking via 'protocol_code' instead of ID
     */
    public function logs()
    {
        return $this->hasMany(ResearchApplicationLog::class, 'protocol_code', 'protocol_code');
    }

    /**
     * Relationship to the User model (Researcher)
     */
    public function user()
    {
        // This assumes your table has a 'user_id' column
        return $this->belongsTo(User::class);
    }

    // Get all reviewers assigned to this specific application
    public function assignedReviewers()
    {
        return $this->belongsToMany(
            Reviewer::class,
            'application_reviewer',
            'protocol_code',
            'reviewer_id',
            'protocol_code',
            'id'
        )
        ->withPivot([
            'status',
            'date_assigned',
            'date_accepted',
            'date_declined',
            'date_expired',
            'declined_reason',
        ])
        ->withTimestamps();
    }

    public function revisions()
    {
        return $this->hasMany(ResearchApplicationRevision::class, 'protocol_code', 'protocol_code');
    }

    public function latestRevision()
    {
        return $this->hasOne(ResearchApplicationRevision::class, 'protocol_code', 'protocol_code')
                    ->latestOfMany('revision_number');
    }
}
