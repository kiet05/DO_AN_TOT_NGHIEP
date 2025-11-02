<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

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
        'password' => 'hashed',
    ];


    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('') ?: 'U';
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    
    
        // ✅ Thêm mới – Hỗ trợ tìm kiếm khách hàng
    public function scopeSearch($query, ?string $keyword)
    {
        $keyword = trim((string)$keyword);
        if ($keyword === '') return $query;

        return $query->where(function($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('email', 'like', "%{$keyword}%")
              ->orWhere('phone', 'like', "%{$keyword}%");
        });
    }

    // ✅ Thêm mới – Hỗ trợ toggle trạng thái nhanh (active/inactive)
    public function toggleStatus(): void
    {
        if (!isset($this->attributes['status'])) return;
        $this->status = $this->status ? 0 : 1;
        $this->save();
    }

}
