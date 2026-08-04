<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Wallet;
use App\Services\NotificationService;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */


    protected $fillable = [
        'name',
        'username',
        'phone',
        'role',
        'status',
        'is_verified',
        'is_banned',
        'role_id',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roleData()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function rolePermissions()
    {
        return Permission::query()
            ->join('role_permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role_id', $this->role_id)
            ->select('permissions.*');
    }

    public function buyer()
    {
        return $this->hasOne(Buyer::class);
    }

    public function seller()
    {
        return $this->hasOne(Seller::class);
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    protected static function booted()
    {
        static::created(function ($user) {

            Wallet::firstOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'balance' => 0,
                    'pending_balance' => 0,
                    'withdrawn_balance' => 0,
                ]
            );

            // Admin ko notification nahi
            if (in_array($user->role, ['buyer', 'seller'])) {

                NotificationService::send(
                    $user->id,
                    'Wallet Created',
                    'Your wallet has been created successfully.',
                    'wallet',
                    route($user->role . '.wallet.index'),
                );
            }
        });
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function withdrawRequests()
    {
        return $this->hasMany(WithdrawRequest::class);
    }

    public function getDisplayNameAttribute()
    {
        if ($this->role == 'buyer' && $this->buyer) {
            return $this->buyer->full_name;
        }

        if ($this->role == 'seller' && $this->seller) {
            return $this->seller->full_name;
        }

        return $this->username;
    }

    public function getDisplayImageAttribute()
    {
        if ($this->role == 'buyer' && $this->buyer && $this->buyer->profile_image) {
            return asset('storage/' . $this->buyer->profile_image);
        }

        if ($this->role == 'seller' && $this->seller && $this->seller->profile_image) {
            return asset('storage/' . $this->seller->profile_image);
        }

        return asset('admin/assets/images/avatar-4.jpg');
    }
}
