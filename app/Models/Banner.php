<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    // thêm image_url nếu DB dùng trường đó
    protected $fillable = ['title', 'image', 'image', 'link', 'position', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Accessor: $banner->url trả về path public (storage) hoặc null
     */
    public function getUrlAttribute()
    {
        // nếu dùng image_url làm path relative trong disk public (vd: 'banners/name.jpg')
        $path = $this->image ?? $this->image ?? null;

        if (!$path) {
            return null;
        }

        // Storage::disk('public')->url(...) trả về '/storage/...' theo default
        return Storage::disk('public')->url($path);
    }
}
