<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $table = 'user_activities'; // chỉnh lại đúng tên bảng nếu khác
    protected $guarded = [];
}
