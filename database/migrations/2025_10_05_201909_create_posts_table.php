<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Tiêu đề bài viết
            $table->text('content'); // Nội dung bài viết
            $table->string('slug')->unique(); // Slug cho URL thân thiện
            $table->string('image')->nullable(); // Hình ảnh đại diện (tùy chọn)
            $table->boolean('is_published')->default(false); // Trạng thái xuất bản
            $table->timestamp('published_at')->nullable(); // Thời gian xuất bản
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('posts');
    }
}