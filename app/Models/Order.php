<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'shipping_fee',
        'total_price',
        'final_amount',
        'product_variant_id',
        'payment_method',
        'payment_status',
        'order_status',
                'payment_id',

    ];

    // Mỗi đơn hàng thuộc về 1 user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
