<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandPartner extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'url',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? url('storage/' . $this->logo) : null;
    }
}
