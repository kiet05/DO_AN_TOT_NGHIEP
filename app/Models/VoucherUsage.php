<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherUsage extends Model
{
    protected $fillable = [
        'voucher_id',
        'order_id',
        'user_id',
        'discount_amount',
        'used_at'
    ];

    protected $casts = [
        'used_at' => 'datetime'
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
