<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['key','title','content','published'];

    // Tìm theo key
    public function scopeKey($q, string $key) {
        return $q->where('key', $key);
    }
}
