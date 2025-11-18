<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'user_id',
        'customer_id',
        'product_id',
        'product_variant_id',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'quantity',
        'price',
        'discount',
        'subtotal',
        'shipping_fee',
        'total_price',
        'final_amount',
        'voucher_id',
        'payment_method',
        'payment_status',
        'order_status',
        'total',
        'note',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
