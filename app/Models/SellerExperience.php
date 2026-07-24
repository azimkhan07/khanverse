<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerExperience extends Model
{
    use HasFactory;
    protected $fillable = [
        'seller_id',
        'company_name',
        'designation',
        'start_date',
        'end_date',
        'currently_working',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'currently_working' => 'boolean',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
