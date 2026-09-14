<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'order_number', 'project_id', 'buyer_id', 'seller_id', 'service_id', 'amount', 'platform_fee', 'status', 'payment_status', 'payment_method', 'transaction_id', 'paid_at', 'gateway_id', 'requirements', 'requirements_title', 'requirements_type', 'requirements_docs', 'delivery_date', 'decline_reason', 'created_at', 'updated_at'];

    protected $casts = [
        'amount' => 'float',
        'platform_fee' => 'float',
        'paid_at' => 'datetime',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }
}
