<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\SoftDeletes;
=======
>>>>>>> origin/feature/orders
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use HasFactory;

<<<<<<< HEAD
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'quantity',
        'status',
    ];

=======
>>>>>>> origin/feature/orders
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
<<<<<<< HEAD

    public function attributes()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attributes', 'product_variant_id', 'attribute_value_id');
    }
=======
>>>>>>> origin/feature/orders
}
