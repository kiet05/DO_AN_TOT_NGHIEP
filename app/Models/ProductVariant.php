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

    public function getAttributeSummaryAttribute(): ?string
    {
        // nạp thêm attributes + attribute (bảng attributes)
        $this->loadMissing('attributes.attribute');

        // KHÔNG dùng $this->attributes (vì đó là mảng field nội bộ của Model)
        $values = $this->getRelation('attributes');

        if ($values->isEmpty()) {
            return null;
        }

        return $values->map(function ($val) {
            // bảng attributes có cột name (Size, Màu sắc,...)
            $attrName = $val->attribute->name ?? null;
            $value    = $val->value ?? '';

            $label = match (strtolower((string) $attrName)) {
                'size'     => 'Kích cỡ',
                'color'    => 'Màu sắc',
                'material' => 'Chất liệu', 
                default    => $attrName, // nếu sau này thêm thuộc tính khác thì giữ nguyên
            };

            return $attrName ? "{$attrName}: {$value}" : $value;
        })->join(' | ');
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
