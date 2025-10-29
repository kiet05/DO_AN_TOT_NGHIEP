<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\SoftDeletes;
class Banner extends Model
{
use SoftDeletes;

    protected $fillable = ['title','image','status']; 
   protected $casts = [
        'status' => 'boolean',
    ];
=======

class Banner extends Model
{
    /** @use HasFactory<\Database\Factories\BannerFactory> */
    use HasFactory;
>>>>>>> origin/feature/orders
}
