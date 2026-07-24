<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerLanguage extends Model
{
    use HasFactory;
    protected $fillable = [
        'seller_id',
        'language',
        'level',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
