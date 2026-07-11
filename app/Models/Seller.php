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
}
