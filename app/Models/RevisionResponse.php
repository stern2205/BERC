<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevisionResponse extends Model
{
    protected $fillable = [
        'protocol_code',
        'revision_number',
        'berc_recommendation',
        'researcher_response',
        'section_and_page',
        'item',
        // NEW FIELDS: Mass assignment now allowed
        'reviewer1_id',
        'reviewer2_id',
        'reviewer3_id',
        'reviewer1_done',
        'reviewer2_done',
        'reviewer3_done',
        'reviewer1_action',
        'reviewer2_action',
        'reviewer3_action',
        'synthesized_comments',
        'synthesized_comments_action',
        'secretariat_comment'
    ];

    /**
     * Relationship: The parent revision tracker
     */
    public function revision()
    {
        return $this->belongsTo(ResearchApplicationRevision::class, 'protocol_code', 'protocol_code')
                    ->where('revision_number', $this->revision_number);
    }

    /**
     * Optional: Helper relationships to get Reviewer details
     */
    public function reviewer1() { return $this->belongsTo(Reviewer::class, 'reviewer1_id'); }
    public function reviewer2() { return $this->belongsTo(Reviewer::class, 'reviewer2_id'); }
    public function reviewer3() { return $this->belongsTo(Reviewer::class, 'reviewer3_id'); }
}
