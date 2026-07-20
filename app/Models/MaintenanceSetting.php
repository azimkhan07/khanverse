<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'message',
        'image',
        'button_text',
        'button_url',
        'status',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'status' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
}
