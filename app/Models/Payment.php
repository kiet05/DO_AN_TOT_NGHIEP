<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'gateway',
        'app_trans_id',
        'zp_trans_id',
        'amount',
        'status',
        'paid_at',
        'meta',
        'webhook_status',
        'webhook_received_at',
        'attempts',
        'last_queried_at',
        'note'
    ];

    protected $casts = [
        'meta' => 'array',
        'paid_at' => 'datetime',
        'webhook_received_at' => 'datetime',
        'last_queried_at' => 'datetime',
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
