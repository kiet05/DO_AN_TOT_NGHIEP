<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // 🔥 Quan trọng
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
//use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use  HasFactory, Notifiable , TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'address',
        'role_id',
        'status',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
    ];
    
    public function initials()
    {
    $name = $this->name ?? '';
    $words = explode(' ', trim($name));
    $initials = '';

    foreach ($words as $w) {
        $initials .= strtoupper(mb_substr($w, 0, 1));
    }

    return $initials ?: 'U'; // U = default nếu không có tên
    }


    /**
     * Liên kết ngược: User thuộc về Role.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
