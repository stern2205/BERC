<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon_label',
        'account_number',
        'account_name',
        'logo_path',
        'bg_color',
        'is_active',
    ];

    // Automatically cast the boolean so it behaves cleanly in your code
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
