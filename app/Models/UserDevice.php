<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'device_name', 'browser', 'platform', 'ip_address',
        'user_agent', 'is_current_device', 'last_activity',
    ];

    protected $casts = [
        'is_current_device' => 'boolean',
        'last_activity' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
