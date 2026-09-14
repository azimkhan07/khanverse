<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerCategoryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'category_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}