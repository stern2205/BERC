<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviewer extends Model
{
    protected $fillable = [
        'user_id', 'name', 'type', 'panel', 'specialization', 'avg_review_time_days', 'is_active'
    ];

    // Get the main User account tied to this reviewer
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer()
    {
        return $this->hasOne(Reviewer::class, 'user_id', 'id');
    }

    // Get all applications assigned to this reviewer
    public function assignedApplications()
    {
        return $this->belongsToMany(ResearchApplications::class, 'application_reviewer', 'reviewer_id', 'research_application_id')
                    ->withPivot(['status', 'date_assigned', 'date_accepted', 'date_declined', 'date_expired', 'declined_reason'])
                    ->withTimestamps();
    }
}
