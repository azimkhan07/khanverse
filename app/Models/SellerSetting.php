<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'seller_id',
        'email_notification',
        'push_notification',
        'sms_notification',
        'profile_visibility',
        'dark_mode',
        'timezone',
        'language',
    ];

    protected $casts = [
        'email_notification' => 'boolean',
        'push_notification' => 'boolean',
        'sms_notification' => 'boolean',
        'dark_mode' => 'boolean',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
