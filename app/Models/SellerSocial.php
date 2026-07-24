<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerSocial extends Model
{
    use HasFactory;
    protected $fillable = [
        'seller_id',
        'facebook',
        'linkedin',
        'github',
        'twitter',
        'instagram',
        'youtube',
        'website',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
