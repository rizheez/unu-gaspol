<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $casts = [
        'diumumkan_by_tahap' => 'array',
        'sk_tahap_by_tahap' => 'array',
        'status_stats' => 'array',
    ];
}
