<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSettlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'project_id', 'seller_id', 'buyer_id',
        'order_number', 'invoice_number',
        'order_amount', 'platform_fee', 'seller_amount', 'platform_pct',
        'status', 'transaction_id', 'paid_at', 'admin_note',
    ];

    protected $casts = [
        'order_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'seller_amount' => 'decimal:2',
        'platform_pct' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function seller() { return $this->belongsTo(Seller::class); }
    public function buyer() { return $this->belongsTo(Buyer::class); }
}