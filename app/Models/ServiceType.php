<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_service_type');
    }

    public function sellers()
    {
        return $this->belongsToMany(Seller::class, 'seller_service_type');
    }
}