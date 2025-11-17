<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'pickup_location',
        'status',
        'notes',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
