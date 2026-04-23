<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformedConsentItem extends Model
{
    // ADD THIS LINE: Explicitly define the table name
    protected $table = 'icf_assessment_items';

    protected $fillable = ['icf_assessment_id', 'question_number', 'remark', 'line_page'];

    public function assessment()
    {
        // Make sure the foreign key is explicitly stated here as well
        return $this->belongsTo(InformedConsent::class, 'icf_assessment_id');
    }
}
