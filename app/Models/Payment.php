<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'gateway',
        'app_trans_id',
        'zp_trans_id',
        'amount',
        'currency',
        'status',
        'meta',
        'paid_at',
    ];

    protected $casts = [
        'meta'    => 'array',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function logs()
    {
        return $this->hasMany(PaymentLog::class);
    }
}
