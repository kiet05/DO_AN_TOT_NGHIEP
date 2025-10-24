<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
<<<<<<< HEAD
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'status',
        'created_by',
    ];
=======
    /** @use HasFactory<\Database\Factories\NotificationFactory> */
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
>>>>>>> 6e27f9aa04493d2bfa9f40b2fca490bdbb0905cb
}
