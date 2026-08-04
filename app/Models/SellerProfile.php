<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'phone',
        'gender',
        'dob',
        'country_id',
        'state_id',
        'city_id',
        'postal_code',
        'address',
        'bio',
        'email_notifications',
        'push_notifications',
        'sms_notifications',
        'profile_visibility',
        'show_email',
        'show_phone',
    ];

    protected $casts = [
        'dob' => 'date',
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'show_email' => 'boolean',
        'show_phone' => 'boolean',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
