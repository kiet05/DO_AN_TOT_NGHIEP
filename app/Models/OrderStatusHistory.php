<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderStatusHistory extends Model
{
    use HasFactory;

    // Cho phép fill toàn bộ field, để dùng $order->statusHistories()->create([...])
    protected $guarded = [];

    // Nếu muốn rõ ràng hơn có thể dùng fillable:
    // protected $fillable = ['order_id', 'status', 'note', 'changed_from', 'changed_by'];

    /**
     * Đơn hàng liên quan
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Nhân viên / user đổi trạng thái (nếu bạn có cột changed_by)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
