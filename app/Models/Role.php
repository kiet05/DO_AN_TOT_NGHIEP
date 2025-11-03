<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

   protected $fillable = ['name', 'slug', 'description'];


    /**
     * Liên kết 1-n: Role có nhiều User.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
