<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\SoftDeletes;
=======
>>>>>>> origin/feature/orders
use Illuminate\Database\Eloquent\Model;

class ProductVariantAttribute extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantAttributeFactory> */
    use HasFactory;

<<<<<<< HEAD
    protected $fillable = [
        'product_variant_id',
        'attribute_value_id',
    ];

=======
>>>>>>> origin/feature/orders
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
