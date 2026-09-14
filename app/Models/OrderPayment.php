<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gateway_id',
        'gateway',
        'method',
        'transaction_id',
        'amount',
        'status',
        'payload',
    ];

    protected $casts = [
        'amount' => 'float',
        'payload' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}