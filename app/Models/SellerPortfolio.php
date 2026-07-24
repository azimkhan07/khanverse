<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerPortfolio extends Model
{
    use HasFactory;
    protected $fillable = [
        'seller_id',
        'title',
        'description',
        'thumbnail',
        'project_url',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
