<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Comment;

class Post extends Model
{
    use HasFactory, SoftDeletes;

 protected $fillable = [
        'title',
        'content',
        'slug',
        'thumbnail',
        'category',
        'image',
        'status',
        'is_published',   
        'published_at',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    public function comments()
{
    return $this->hasMany(Comment::class)->latest();
}
}
