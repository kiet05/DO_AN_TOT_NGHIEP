<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'quantity',
        'image_url', 
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attributes', 'product_variant_id', 'attribute_value_id');
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attributes');
    }

    public function sizes()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attributes')
            ->where('attribute_values.type', 'size'); // filter trên attribute_values
    }

    public function colors()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attributes')
            ->where('attribute_values.type', 'color'); // filter trên attribute_values
    }

    public function materials()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attributes')
            ->where('attribute_values.type', 'material'); // filter trên attribute_values
    }
}
