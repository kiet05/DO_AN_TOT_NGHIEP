<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSetting extends Model
{
    protected $fillable = [
        'logo',
        'hotline',
        'email',
        'address',
        'facebook',
        'instagram',
        'zalo',
        'tiktok',
        'youtube',
        'twitter',
    ];

    /**
     * Get the logo URL
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}