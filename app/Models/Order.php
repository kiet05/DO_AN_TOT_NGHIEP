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
        'customer_id',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'shipping_fee',
        'total_price',
        'final_amount',
        'voucher_id',
        'payment_method_id',
        'payment_method',
        'payment_status',
        'order_status',
        'status',
    ];

    // Mỗi đơn hàng thuộc về 1 user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
    public function getPaymentStatusLabelAttribute()
{
    return match ($this->payment_status) {
        'unpaid'  => 'Chưa thanh toán',
        'pending' => 'Đang chờ thanh toán',
        'paid'    => 'Đã thanh toán',
        'failed'   => 'Thanh toán thất bại',
        'canceled' => 'Đã hủy thanh toán',
        default   => $this->payment_status,
    };
}

   public function getStatusLabelAttribute(): string
    {
        // Chuẩn hoá lại status (đổi success -> completed, canceled -> cancelled...)
        $key = $this->normalizeStatus($this->order_status);

        $map = [
            'pending'    => 'Chờ xử lý',
            'confirmed'  => 'Xác nhận',
            'processing' => 'Chuẩn bị',
            'shipping'   => 'Đang giao',
            'shipped'    => 'Đã giao',
            'completed'  => 'Hoàn thành',
            'cancelled'  => 'Hủy',
            'returned'   => 'Hoàn hàng',
        ];

        return $map[$key] ?? ucfirst($key);
    }

    /**
     * Chuẩn hoá status về tên chuẩn
     */
    private function normalizeStatus(string $status): string
    {
        $aliases = [
            'success'  => 'completed',  // dữ liệu cũ
            'canceled' => 'cancelled',  // kiểu Mỹ -> kiểu Anh
        ];

        return $aliases[$status] ?? $status;
    }

}
