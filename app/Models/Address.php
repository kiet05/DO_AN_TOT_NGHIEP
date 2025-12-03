<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';

    protected $fillable = [
        'user_id',
        'receiver_name',
        'receiver_phone',
        'receiver_address_detail',
        'receiver_district',
        'receiver_city',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /*-------------------------------------------------
     | QUAN HỆ
     *------------------------------------------------*/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /*-------------------------------------------------
     | SCOPES & HÀM TIỆN ÍCH
     *------------------------------------------------*/
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public static function getDefaultForUser($userId)
    {
        return static::forUser($userId)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Đặt địa chỉ này làm mặc định, đồng thời bỏ mặc định các địa chỉ khác.
     */
    public function setAsDefault(): void
    {
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->is_default = true;
        $this->save();
    }

    /*-------------------------------------------------
     | ALIAS FIELD CHO CHECKOUTCONTROLLER
     | (để không cần sửa CheckoutController)
     *------------------------------------------------*/

    // receiver_phone <-> phone
    public function getReceiverPhoneAttribute()
    {
        return $this->phone;
    }

    public function setReceiverPhoneAttribute($value)
    {
        $this->attributes['phone'] = $value;
    }

    // receiver_address_detail <-> address_line
    public function getReceiverAddressDetailAttribute()
    {
        return $this->address_line;
    }

    public function setReceiverAddressDetailAttribute($value)
    {
        $this->attributes['address_line'] = $value;
    }

    // receiver_city <-> province
    public function getReceiverCityAttribute()
    {
        return $this->province;
    }

    public function setReceiverCityAttribute($value)
    {
        $this->attributes['province'] = $value;
    }

    // receiver_district <-> district
    public function getReceiverDistrictAttribute()
    {
        return $this->district;
    }

    public function setReceiverDistrictAttribute($value)
    {
        $this->attributes['district'] = $value;
    }
}
