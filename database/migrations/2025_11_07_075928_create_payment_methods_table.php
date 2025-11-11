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
        if (!Schema::hasTable('payment_methods')) {
            Schema::create('payment_methods', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100); // Tên phương thức: COD, VNPay
                $table->string('slug', 50)->unique(); // cod, vnpay
                $table->string('display_name', 100); // Tên hiển thị: Thanh toán khi nhận hàng, VNPay
                $table->text('description')->nullable();
                $table->string('icon')->nullable(); // Icon hoặc logo
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0); // Thứ tự hiển thị
                $table->json('config')->nullable(); // Cấu hình riêng (cho VNPay: merchant_id, secret_key, etc.)
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
