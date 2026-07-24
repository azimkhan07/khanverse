<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerCertificate extends Model
{
    use HasFactory;
    protected $fillable = [
        'seller_id',
        'title',
        'organization',
        'issue_date',
        'certificate_file',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
