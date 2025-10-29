<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\SoftDeletes;
=======
>>>>>>> origin/feature/orders
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    /** @use HasFactory<\Database\Factories\AttributeFactory> */
    use HasFactory;

<<<<<<< HEAD
    protected $fillable = [
        'name',
        'description',
    ];

=======
>>>>>>> origin/feature/orders
    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
