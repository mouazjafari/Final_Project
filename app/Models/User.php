<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'profile_image',
        'password',
    ];
    protected string $guard_name = 'api';

    protected function getDefaultGuardName(): string
    {
        return $this->guard_name;
    }
    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // دالة مساعدة لإنشاء المحفظة تلقائياً عند التسجيل
    public static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            // إنشاء محفظة تلقائياً للمستخدم الجديد
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0.00,
            ]);
        });
    }
}
