<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    /** @use HasFactory<\Database\Factories\ReturnItemFactory> */
    use HasFactory;

    public function return()
    {
        return $this->belongsTo(ReturnModel::class, 'return_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
}
