<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $hidden = ['delivery_key'];

    protected $fillable = [
        'title', 'slug', 'description', 'budget', 'deadline', 'status',
        'buyer_id', 'seller_id', 'service_id', 'delivery_method',
        'delivery_key', 'delivery_key_hash', 'delivery_key_shown_at', 'delivery_key_verified_at',
    ];

    protected $casts = [
        'delivery_key_shown_at' => 'datetime',
        'delivery_key_verified_at' => 'datetime',
    ];

    /**
     * Generate (once) and return the delivery key for a hosting project.
     */
    public function ensureDeliveryKey(): string
    {
        if (! $this->delivery_key) {
            $this->delivery_key = self::newDeliveryKey();
            $this->delivery_key_hash = hash('sha256', $this->delivery_key);
            $this->delivery_key_shown_at = now();
            $this->save();
        }

        return $this->delivery_key;
    }

    /**
     * Replace the key with a fresh one (used by the "forgot key" flow).
     */
    public function rotateDeliveryKey(): string
    {
        $key = self::newDeliveryKey();
        $this->delivery_key = $key;
        $this->delivery_key_hash = hash('sha256', $key);
        $this->delivery_key_shown_at = now();
        $this->delivery_key_verified_at = null;
        $this->save();

        return $key;
    }

    private static function newDeliveryKey(): string
    {
        return strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
    }

    public function buyer() { return $this->belongsTo(Buyer::class, 'buyer_id'); }
    public function seller() { return $this->belongsTo(Seller::class, 'seller_id'); }
    public function service() { return $this->belongsTo(Service::class); }
    public function order() { return $this->hasOne(Order::class); }
    public function attachments() { return $this->hasMany(ProjectAttachment::class); }
    public function deliveries() { return $this->hasMany(ProjectDelivery::class); }
    public function review() { return $this->hasOne(Review::class); }
    public function hostings() { return $this->hasMany(ProjectHosting::class); }
}