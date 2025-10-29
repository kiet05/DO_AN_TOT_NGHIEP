<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\SoftDeletes;
=======
>>>>>>> origin/feature/orders
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

<<<<<<< HEAD
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

=======
>>>>>>> origin/feature/orders
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
