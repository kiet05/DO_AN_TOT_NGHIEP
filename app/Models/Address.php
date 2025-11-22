<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'receiver_name',
        'receiver_phone',
        'receiver_city',
        'receiver_district',
        'receiver_address_detail',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy địa chỉ mặc định của user
     */
    public static function getDefaultForUser($userId)
    {
        return static::where('user_id', $userId)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Đặt địa chỉ này làm mặc định
     */
    public function setAsDefault()
    {
        // Bỏ mặc định của các địa chỉ khác
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Đặt địa chỉ này làm mặc định
        $this->update(['is_default' => true]);
    }
}
