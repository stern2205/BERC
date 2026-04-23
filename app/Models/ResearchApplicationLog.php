<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchApplicationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocol_code', // Linking via code, not ID
        'user_id',       // Who made the change
        'status',        // The new status
        'comment'        // Optional notes
    ];

    /**
     * Relationship back to the Application
     */
    public function application()
    {
        return $this->belongsTo(ResearchApplications::class, 'protocol_code', 'protocol_code');
    }

    /**
     * Relationship to the User (so we can see who did it)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
