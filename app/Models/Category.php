<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

<<<<<<< HEAD
    protected $fillable = [
        'name',
        'parent_id',
        'status',
    ];

=======
>>>>>>> origin/feature/orders
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
<<<<<<< HEAD

    public function products()
    {
        return $this->hasMany(Product::class);
    }
=======
>>>>>>> origin/feature/orders
}
