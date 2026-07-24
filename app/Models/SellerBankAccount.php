<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerBankAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'seller_id',
        'bank_name',
        'account_holder_name',
        'account_number',
        'ifsc_code',
        'upi_id',
        'branch_name',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
