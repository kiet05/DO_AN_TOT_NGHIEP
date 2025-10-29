<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

<<<<<<< HEAD
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
=======
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('thumbnail', 255)->nullable();
            $table->text('content');
            $table->string('category', 100)->nullable(); // Giả sử là string, không FK
            $table->tinyInteger('status')->default(1);
>>>>>>> origin/feature/orders
            $table->timestamps();
        });
    }

<<<<<<< HEAD
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
=======
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
>>>>>>> origin/feature/orders
