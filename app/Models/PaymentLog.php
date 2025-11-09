<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = ['payment_id', 'type', 'message', 'payload'];
    protected $casts = ['payload' => 'array'];
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
