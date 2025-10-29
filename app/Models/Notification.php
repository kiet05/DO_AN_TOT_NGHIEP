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
>>>>>>> origin/feature/orders
}
