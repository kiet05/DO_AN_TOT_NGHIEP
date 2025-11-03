<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            // Tiêu đề banner
            $table->string('title');

            // Đường dẫn hình ảnh
            $table->string('image_url');

            // Liên kết banner (nếu có)
            $table->string('link')->nullable();

            // Vị trí hiển thị (trên, giữa, dưới)
            $table->enum('position', ['top', 'middle', 'bottom'])->default('top');

            // Trạng thái hoạt động
            $table->boolean('is_active')->default(true);

            // Ngày bắt đầu và kết thúc hiển thị
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();

            // Cột mềm để xóa tạm
            $table->softDeletes();

            // Thời gian tạo & cập nhật
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
