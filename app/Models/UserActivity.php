<?php
// app/Models/UserActivity.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = ['user_id','causer_id','action','ip','user_agent','payload'];
    protected $casts = ['payload' => 'array'];
}
