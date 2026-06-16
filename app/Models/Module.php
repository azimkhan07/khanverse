<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'route',
        'view_path',
        'controller',
        'panel',
        'roles',
        'status',
    ];

    protected $casts = [
        'roles' => 'array',
        'status' => 'boolean',
    ];
}
