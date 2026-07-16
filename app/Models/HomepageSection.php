<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_key',
        'title',
        'subtitle',
        'description',
        'button_text',
        'button_url',
        'image',
        'icon',
        'background_image',
        'extra_data',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'extra_data' => 'array',
        'status' => 'boolean',
    ];
}
