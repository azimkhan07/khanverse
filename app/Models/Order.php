<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'project_id', 'buyer_id', 'seller_id', 'service_id', 'amount', 'platform_fee', 'status', 'requirements', 'delivery_date', 'created_at', 'updated_at'];

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
}
