<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'protocol_code',
        'payment_method',
        'amount_paid',
        'reference_number',
        'proof_of_payment_path',
        'status'
    ];

    public function application()
    {
        return $this->belongsTo(ResearchApplications::class, 'protocol_code', 'protocol_code');
    }
}
