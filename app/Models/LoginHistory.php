<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'browser',
        'device',
        'platform',
        'login_at',
        'logout_at',
        'is_successful',
    ];

    protected $casts = [
        'login_at'     => 'datetime',
        'logout_at'    => 'datetime',
        'is_successful'=> 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
