<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnModel extends Model
{
    /** @use HasFactory<\Database\Factories\ReturnModelFactory> */
    use HasFactory;

    protected $table = 'returns';
    protected $fillable = [
        'order_id',
        'user_id',
        'reason',
        'proof_image',
        'status',
        'refund_method',
        'refund_account_number',
        'refund_amount',
        'approved_by',
        'decided_at',
        'evidence_urls',
        'action_type',
    ];

    protected $casts = [
        'evidence_urls' => 'array',
        'decided_at'    => 'datetime',
        'status'        => 'integer',
        'refund_amount' => 'decimal:2',
    ];

    const PENDING = 0;
    const APPROVED = 1;
    const REJECTED = 2;
    const REFUNDING = 3;
    const COMPLETED = 4;
    const WAITING_CUSTOMER_CONFIRM = 5;

    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusLabelAttribute()
    {
        return [
            self::PENDING                  => 'pending',
            self::APPROVED                 => 'approved',
            self::REJECTED                 => 'rejected',
            self::REFUNDING                => 'refunding',
            self::COMPLETED                => 'completed',
            self::WAITING_CUSTOMER_CONFIRM => 'waiting_customer_confirm',
        ][$this->status] ?? 'pending';
    }


    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
}
