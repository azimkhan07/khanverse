<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'order_id',
        'seller_id',
        'buyer_id',
        'title',
        'description',
        'report_file',
        'delivery_notes',
        'version',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }
}
