<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentFormItem extends Model
{
    protected $fillable = [
        'assessment_form_id',
        'question_number',
        'remark',
        'line_page',
        'reviewer_comments'
    ];

    public function form()
    {
        return $this->belongsTo(AssessmentForm::class, 'assessment_form_id');
    }
}
