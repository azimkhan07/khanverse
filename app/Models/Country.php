<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'iso2',
        'iso3',
        'phone_code',
        'currency',
        'currency_symbol',
        'timezone',
        'emoji',
        'status',
    ];

    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function buyerProfiles()
    {
        return $this->hasMany(BuyerProfile::class);
    }
}
