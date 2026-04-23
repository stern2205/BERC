<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformedConsent extends Model
{
    // ADD THIS LINE: Replace 'icf_assessments' with your EXACT database table name
    protected $table = 'icf_assessments';

    protected $fillable = ['protocol_code', 'reviewer_id', 'status'. 'is_consent_necessary', 'no_consent_explanation'];

    public function items()
    {
        // Also double-check this foreign key!
        // If your database column is 'icf_assessment_id', change it here.
        return $this->hasMany(InformedConsentItem::class, 'icf_assessment_id');
    }

    public function applications()
    {
        // This should match the actual foreign key in your database
        return $this->belongsTo(ResearchApplications::class, 'protocol_code', 'protocol_code');
    }
}
