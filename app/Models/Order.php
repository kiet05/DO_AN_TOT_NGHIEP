<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;

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
        'note',
        'cancel_reason',
        'return_reason',
        'return_image_path',   // 👈 thêm dòng này

    ];

    protected $casts = [
        'status_changed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | HẰNG SỐ TRẠNG THÁI ĐƠN HÀNG
    |--------------------------------------------------------------------------
    */
    public const STATUS_PENDING   = 'pending';    // Chờ xử lý
    public const STATUS_CONFIRMED = 'confirmed';  // Chờ xác nhận
    public const STATUS_PREPARING = 'preparing';  // Chờ lấy hàng / Chuẩn bị
    public const STATUS_SHIPPING  = 'shipping';   // Đang giao
    public const STATUS_SHIPPED = 'shipped';  // Đã giao
    public const STATUS_RETURNED  = 'returned';   // Trả hàng
    public const STATUS_RETURN_PENDING  = 'return_pending';   // chờ Trả hàng
    public const STATUS_CANCELLED = 'cancelled';  // Đã hủy

    /**
     * Danh sách trạng thái + label tiếng Việt
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING   => 'Chờ xử lý',
            self::STATUS_CONFIRMED => 'Chờ xác nhận',
            self::STATUS_PREPARING => 'Chờ chuẩn bị',
            self::STATUS_SHIPPING  => 'Đang giao',
            self::STATUS_SHIPPED => 'Đã giao',
            self::STATUS_RETURNED  => 'Trả hàng',
            self::STATUS_RETURN_PENDING  => 'Chờ hoàn hàng',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

    /**
     * Các trạng thái mà KHÁCH được phép tự hủy đơn
     * (đơn chưa giao cho shipper)
     */
    public static function customerCancelableStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_PREPARING,
        ];
    }

    /**
     * Các trạng thái mà KHÁCH có thể yêu cầu trả hàng
     */
    public static function customerReturnableStatuses(): array
    {
        return [
            self::STATUS_SHIPPED,
        ];
    }

    /**
     * KH có thể hủy đơn không?
     */

    /**
     * KH có thể yêu cầu trả hàng không?
     */
    public function canBeReturnedByCustomer(): bool
    {
        $status = $this->normalizeStatus($this->order_status);

        return in_array($status, self::customerReturnableStatuses(), true);
    }

    /*
    |--------------------------------------------------------------------------
    | QUAN HỆ
    |--------------------------------------------------------------------------
    */

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

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR HIỂN THỊ THANH TOÁN / TRẠNG THÁI
    |--------------------------------------------------------------------------
    */

    public function getPaymentStatusLabelAttribute()
    {
        return match ($this->payment_status) {
            'unpaid'   => 'Chưa thanh toán',
            'pending'  => 'Đang chờ thanh toán',
            'paid'     => 'Đã thanh toán',
            'failed'   => 'Thanh toán thất bại',
            'canceled' => 'Đã hủy thanh toán',
            default    => $this->payment_status,
        };
    }

    /**
     * Label tiếng Việt cho order_status
     */
    public function getStatusLabelAttribute(): string
    {
        $key = $this->normalizeStatus($this->order_status);
        $map = self::statusOptions();

        return $map[$key] ?? ucfirst((string) $key);
    }

    /**
     * Chuẩn hoá status về tên chuẩn
     * (map dữ liệu cũ sang bộ status mới)
     */
    private function normalizeStatus(?string $status): string
    {
        $status = strtolower((string) $status);

        $aliases = [
            'canceled'   => self::STATUS_CANCELLED,   // kiểu Mỹ -> kiểu Anh
            'processing' => self::STATUS_PREPARING,   // cũ: processing
            'shipped'    => self::STATUS_SHIPPED,   // cũ: shipped
        ];

        return $aliases[$status] ?? $status;
    }
    // Chuẩn hoá trạng thái hiện tại của đơn
    public function canonicalStatus(): string
    {
        $status = $this->order_status ?? '';

        $aliases = [
            'success'  => 'completed',
            'canceled' => 'cancelled',
        ];

        return $aliases[$status] ?? $status;
    }

    /**
     * KH được phép hủy khi đơn còn ở: pending / confirmed / processing
     */
    public function canBeCancelledByCustomer(): bool
    {
        $canon = $this->canonicalStatus();

        return in_array($canon, ['pending', 'confirmed', 'processing'], true);
    }

    /**
     * KH được phép bấm "Đã nhận hàng" khi đơn đang giao
     */
    public function canBeConfirmedReceivedByCustomer(): bool
    {
        $canon = $this->canonicalStatus();

        return in_array($canon, ['shipping'], true);
    }

    /**
     * KH được phép gửi yêu cầu trả hàng / hoàn tiền
     * khi đơn đã giao
     */
    public function canRequestReturnByCustomer(): bool
    {
        $canon = $this->canonicalStatus();

        return in_array($canon, ['shipped', 'completed'], true);
    }

    /**
     * KH được phép "Mua lại" khi đơn đã hủy
     */
    public function canBeReorderedByCustomer(): bool
    {
        $canon = $this->canonicalStatus();

        return $canon === 'cancelled';
    }
}
