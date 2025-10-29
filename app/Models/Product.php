<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'base_price',
        'category_id',
        'brand_id',
        'image_main',
        'is_new',
        'is_on_sale',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Quan hệ với Brand
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // Quan hệ với các ảnh phụ (nếu có)
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Quan hệ với các biến thể sản phẩm (nếu có)
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
