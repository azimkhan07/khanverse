<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'delivery_days',
        'delivery_method',
        'revisions',
        'thumbnail',
        'status',
        'seller_id',
        'category_id',
        'visit_start_time',
        'visit_end_time',
        'working_days',
    ];

    protected $casts = [
        'working_days' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function serviceTypes()
    {
        return $this->belongsToMany(ServiceType::class, 'service_service_type');
    }

    public function images()
    {
        return $this->hasMany(ServiceImage::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Order::class, 'service_id', 'order_id', 'id', 'id');
    }
}