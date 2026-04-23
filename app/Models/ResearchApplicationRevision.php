<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchApplicationRevision extends Model
{
    protected $guarded = [];

    public function application()
    {
        return $this->belongsTo(ResearchApplications::class, 'protocol_code', 'protocol_code');
    }

    // A revision can have many documents
    public function documents()
    {
        return $this->hasMany(RevisionDocument::class, 'revision_id');
    }
}
