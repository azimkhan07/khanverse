<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class ProjectHosting extends Model
{
    use HasFactory;

    /**
     * Fields stored encrypted at rest. Host + username deliberately stay
     * plaintext so the seller can identify the server before unlocking the key.
     */
    protected $encryptable = ['provider', 'domain', 'port', 'protocol', 'password'];

    protected $fillable = [
        'project_id',
        'order_id',
        'buyer_id',
        'hosting_type',
        'provider',
        'domain',
        'host',
        'port',
        'protocol',
        'username',
        'password',
        'notes',
        'is_used',
        'used_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable, true) && $value !== null && $value !== '') {
            $value = Crypt::encryptString((string) $value);
        }

        return parent::setAttribute($key, $value);
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encryptable, true) && $value !== null && $value !== '') {
            try {
                return Crypt::decryptString((string) $value);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                return $value;
            }
        }

        return $value;
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }
}