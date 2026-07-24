<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerDocument extends Model
{
    use HasFactory;
    protected $fillable = [
        'seller_id',
        'document_type',
        'document_number',
        'document_file',
        'status',
        'remarks',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
