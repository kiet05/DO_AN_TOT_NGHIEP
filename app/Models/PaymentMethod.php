<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'display_name',
        'description',
        'icon',
        'is_active',
        'sort_order',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    /**
     * Quan hệ: PaymentMethod có nhiều Order
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope: Chỉ lấy các phương thức thanh toán đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
