<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicRequirement extends Model
{
    use HasFactory;

    // Explicitly define the table name just to be safe
    protected $table = 'basic_requirements';

    // Allow mass assignment for these columns
    protected $fillable = [
        'protocol_code',
        'type',
        'description',
        'file_path',
    ];

    /**
     * Relationship: A basic requirement belongs to a specific research application.
     */
    public function application()
    {
        return $this->belongsTo(ResearchApplications::class, 'protocol_code', 'protocol_code');
    }
}
