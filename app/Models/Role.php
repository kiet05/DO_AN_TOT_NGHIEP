<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
<<<<<<< HEAD

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Liên kết 1-n: Role có nhiều User.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
=======
>>>>>>> origin/feature/orders
}
