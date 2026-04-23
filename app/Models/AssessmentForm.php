<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentForm extends Model
{
    protected $fillable = ['protocol_code', 'reviewer_id', 'status', 'is_consent_necessary', 'no_consent_explanation'];

    // Links the form to its many question items
    public function items()
    {
        return $this->hasMany(AssessmentFormItem::class);
    }

    public function application()
    {
        return $this->belongsTo(ResearchApplications::class, 'protocol_code', 'protocol_code');
    }
}
