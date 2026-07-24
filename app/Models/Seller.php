<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'user_id',
        'full_name',
        'bio',
        'profile_image',
        'skills',
        'hourly_rate',
        'experience_level',
        'total_earning',
        'available_for_work',
        'country',
        'city',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function social()
    {
        return $this->hasOne(SellerSocial::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Documents
    |--------------------------------------------------------------------------
    */

    public function documents()
    {
        return $this->hasMany(SellerDocument::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Bank Account
    |--------------------------------------------------------------------------
    */

    public function bankAccount()
    {
        return $this->hasOne(SellerBankAccount::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Languages
    |--------------------------------------------------------------------------
    */

    public function languages()
    {
        return $this->hasMany(SellerLanguage::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Certificates
    |--------------------------------------------------------------------------
    */

    public function certificates()
    {
        return $this->hasMany(SellerCertificate::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Experiences
    |--------------------------------------------------------------------------
    */

    public function experiences()
    {
        return $this->hasMany(SellerExperience::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Portfolios
    |--------------------------------------------------------------------------
    */

    public function portfolios()
    {
        return $this->hasMany(SellerPortfolio::class);
    }

    // Settings

    public function setting()
    {
        return $this->hasOne(SellerSetting::class);
    }

    // Gig

    // public function gigs()
    // {
    //     return $this->hasMany(Gig::class);
    // }
}
