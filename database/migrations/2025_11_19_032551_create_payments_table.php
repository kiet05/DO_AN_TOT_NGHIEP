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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Đơn hàng liên quan
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            // Cổng thanh toán: cod, vnpay, momo...
            $table->string('gateway', 50);

            // Mã giao dịch của app / cổng thanh toán (nếu có)
            $table->string('app_trans_id', 100)->nullable();
            $table->string('zp_trans_id', 100)->nullable();

            // Số tiền
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('VND');

            // Trạng thái thanh toán: pending, paid, failed...
            $table->string('status', 50);

            // Dữ liệu bổ sung (JSON cho VNPay, VNPay response…)
            $table->json('meta')->nullable();

            // Thời điểm thanh toán xong (nếu có)
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
