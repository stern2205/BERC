<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevisionDocument extends Model
{
    protected $guarded = [];

    public function revision()
    {
        return $this->belongsTo(ResearchApplicationRevision::class, 'revision_id');
    }
}
