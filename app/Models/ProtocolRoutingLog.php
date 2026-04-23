<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtocolRoutingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocol_code',
        'document_nature',
        'from_name',
        'to_name',
        'from_user_id',
        'to_user_id',
        'remarks'
    ];
}
