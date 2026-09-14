<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'user_id',
        'full_name',
        'company_name',
        'profile_image',
        'country',
        'city',
        'verification_document',
        'verification_status',
        'verification_rejection_reason',
        'is_consultancy',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    public function profile()
    {
        return $this->hasOne(BuyerProfile::class);
    }
}
