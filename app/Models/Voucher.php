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
        return $this->belongsToMany(Product::class, 'voucher_product');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'voucher_category');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_at && $this->end_at->isPast();
    }
    /** @use HasFactory<\Database\Factories\VoucherFactory> */
    use HasFactory;
}
