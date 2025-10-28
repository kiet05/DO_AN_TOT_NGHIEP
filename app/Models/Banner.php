<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Banner extends Model
{
use SoftDeletes;

    protected $fillable = ['title','image','status']; 
   protected $casts = [
        'status' => 'boolean',
    ];
}
