<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
<<<<<<< HEAD
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'status',
    ];
=======
    /** @use HasFactory<\Database\Factories\BannerFactory> */
    use HasFactory;
>>>>>>> 6e27f9aa04493d2bfa9f40b2fca490bdbb0905cb
}
