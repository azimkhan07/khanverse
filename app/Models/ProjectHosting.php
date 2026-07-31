<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectHosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'order_id',
        'buyer_id',
        'hosting_type',
        'provider',
        'domain',
        'host',
        'port',
        'protocol',
        'username',
        'password',
        'is_used',
        'used_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }
}
