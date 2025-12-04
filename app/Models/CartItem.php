<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    /** @use HasFactory<\Database\Factories\CartItemFactory> */
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_variant_id',
        'quantity',
        'price_at_time',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_at_time' => 'integer',
        'subtotal' => 'integer',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Lấy thông tin sản phẩm thông qua variant
     */
    public function getProductAttribute()
    {
        return $this->productVariant->product ?? null;
    }

    /**
     * Kiểm tra sản phẩm còn hàng không
     */
    public function isOutOfStock()
    {
        return $this->productVariant->quantity < $this->quantity;
    }

    /**
     * Tính lại subtotal và làm tròn thành số nguyên
     */
    public function calculateSubtotal()
    {
        $this->subtotal = round($this->quantity * $this->price_at_time);
        $this->save();
        return $this->subtotal;
    }
}
