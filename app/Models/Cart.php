<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    /** @use HasFactory<\Database\Factories\CartFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total_price',
        'voucher_id',
        'discount_amount',
    ];

    protected $casts = [
        'discount_amount' => 'integer',
        'total_price' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Tính lại tổng tiền của giỏ hàng
     * Tính lại từ quantity * price_at_time và làm tròn thành số nguyên
     */
    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $itemSubtotal = round($item->quantity * $item->price_at_time);
            $total += $itemSubtotal;
        }
        $total = round($total);
        $this->update(['total_price' => $total]);
        return $total;
    }

}

