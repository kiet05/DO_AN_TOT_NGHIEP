<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');                 // Câu hỏi
            $table->text('answer');                    // Câu trả lời
            $table->string('category')->nullable();    // Nhóm: Đặt hàng, Thanh toán...
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // Sắp xếp
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
