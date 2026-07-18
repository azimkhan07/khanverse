<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'description',
        'banner_image',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'status',
    ];
}
