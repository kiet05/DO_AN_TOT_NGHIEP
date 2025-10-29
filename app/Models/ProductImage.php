<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\SoftDeletes;
=======
>>>>>>> origin/feature/orders
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    /** @use HasFactory<\Database\Factories\ProductImageFactory> */
    use HasFactory;

<<<<<<< HEAD
    protected $fillable = [
        'product_id',
        'image_url',
    ];

=======
>>>>>>> origin/feature/orders
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
