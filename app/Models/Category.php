<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'status',
        'category_type',
        'form_fields'
    ];

    protected $casts = [
        'status' => 'boolean',
        'form_fields' => 'array',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function serviceTypes()
    {
        return $this->hasMany(ServiceType::class);
    }
}
