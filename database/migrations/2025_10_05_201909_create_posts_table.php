<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('content');
            $table->string('slug')->unique();
            $table->string('thumbnail', 255)->nullable(); // Hình đại diện
            $table->string('category', 100)->nullable();
            $table->boolean('is_published')->default(false);
            $table->tinyInteger('status')->default(1);
            $table->string('image')->nullable(); // đường dẫn trong storage
            $table->timestamp('published_at')->nullable(); // chỉ giữ 1 dòng này
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
