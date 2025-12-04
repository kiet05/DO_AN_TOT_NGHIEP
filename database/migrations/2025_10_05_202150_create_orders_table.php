<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Liên kết user (người đặt hàng)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Liên kết khách hàng (nếu có)
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');

            // Thông tin người nhận
            $table->string('receiver_name', 100);
            $table->string('receiver_phone', 20);
            $table->text('receiver_address');

            // Ghi chú đơn hàng
            $table->text('note')->nullable();

            // Thông tin thanh toán
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->decimal('final_amount', 15, 2)->default(0);
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('set null');
            $table->string('payment_method', 50);
            $table->string('payment_status', 50);
            $table->string('order_status', 50);

            // Lý do hủy, trả hàng
            $table->text('cancel_reason')->nullable();
            $table->text('return_reason')->nullable();
            $table->string('return_image_path')->nullable();

            // Thời điểm thay đổi trạng thái
            $table->timestamp('status_changed_at')->nullable();

            // Trạng thái chung
            $table->string('status', 50)->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
