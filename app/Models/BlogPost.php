<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'author',
        'excerpt',
        'content',
        'cover_image',
        'published_at',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'status',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'status'       => 'boolean',
    ];

    protected $appends = ['cover_image_url'];

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image ? url('storage/' . $this->cover_image) : null;
    }
}