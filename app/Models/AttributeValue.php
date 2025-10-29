<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\SoftDeletes;
=======
>>>>>>> origin/feature/orders
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    /** @use HasFactory<\Database\Factories\AttributeValueFactory> */
    use HasFactory;

<<<<<<< HEAD
    protected $fillable = [
        'attribute_id',
        'value',
    ];

=======
>>>>>>> origin/feature/orders
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
<<<<<<< HEAD

    public function variants()
{
    return $this->belongsToMany(ProductVariant::class, 'product_variant_attributes', 'attribute_value_id', 'variant_id');
}
=======
>>>>>>> origin/feature/orders
}
