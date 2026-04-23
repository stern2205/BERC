<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplementaryDocument extends Model
{
    use HasFactory;

    // Explicitly define table name since it's custom
    protected $table = 'supplementary_documents';

    protected $fillable = [
        'protocol_code',
        'type',
        'description',
        'file_path',
    ];

    public function application()
    {
        return $this->belongsTo(ResearchApplications::class, 'protocol_code', 'protocol_code');
    }
}
