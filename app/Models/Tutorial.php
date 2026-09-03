<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'video_url',
        'thumbnail',
        'duration',
        'roles',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'roles' => 'array',
        'status' => 'boolean',
    ];
}
