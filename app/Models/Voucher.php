<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'name',
        'discount_type',
        'discount_value',
        'type',
        'value',
        'max_discount',
        'min_order_value',
        'apply_type',
        'usage_limit',
        'start_at',
        'end_at',
        'expired_at',
        'is_active',
    ];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
        'is_active' => 'boolean',
    ];
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'voucher_product', 'voucher_id', 'product_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'voucher_category', 'voucher_id', 'category_id');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_at && $this->end_at->isPast();
    }

    /**
     * Lấy số lần đã sử dụng voucher
     */
    public function getUsedCountAttribute(): int
    {
        return $this->usages()->count();
    }

    /**
     * Lấy số lần còn lại có thể sử dụng
     */
    public function getRemainingCountAttribute(): ?int
    {
        if (!$this->usage_limit) {
            return null; // Không giới hạn
        }
        return max(0, $this->usage_limit - $this->used_count);
    }

    /**
     * Kiểm tra voucher còn có thể sử dụng không
     */
    public function isUsable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->end_at && $this->end_at->isPast()) {
            return false;
        }

        if ($this->start_at && $this->start_at->isFuture()) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Kiểm tra user đã sử dụng voucher này chưa
     */
    public function isUsedByUser($userId): bool
    {
        return $this->usages()->where('user_id', $userId)->exists();
    }

    /** @use HasFactory<\Database\Factories\VoucherFactory> */
    use HasFactory;
}
