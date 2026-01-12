<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

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

    public function reviews()
    {
        return $this->hasMany(Review::class)
            ->where('status', 1)
            ->latest();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }
    // Product.php
    public function orders()
    {
        return $this->hasMany(OrderItem::class); // giả sử OrderItem lưu product_id
    }
}
