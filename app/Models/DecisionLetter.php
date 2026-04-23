<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DecisionLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocol_code',
        'decision_status',
        'letter_date',
        'proponent',
        'designation',
        'institution',
        'address',
        'title',
        'subject',
        'dear_name',
        'support_date',
        'documents',
        'findings',
        'recommendations',
        'instructions',
        'signature_path'
    ];

    protected $casts = [
        'documents' => 'array',
        'letter_date' => 'date',
        'support_date' => 'date',
    ];

    public function application()
    {
        return $this->belongsTo(ResearchApplications::class, 'protocol_code', 'protocol_code');
    }
}
