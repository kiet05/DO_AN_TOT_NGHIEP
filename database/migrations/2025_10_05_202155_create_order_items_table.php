<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // Liên kết đơn hàng
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            // Liên kết người dùng
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Liên kết khách hàng (nếu có)
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');

            // Liên kết sản phẩm
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // Liên kết biến thể sản phẩm (nếu có)
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');

            // Thông tin người nhận (nếu cần tách riêng từng sản phẩm)
            $table->string('receiver_name', 100)->nullable();
            $table->string('receiver_phone', 20)->nullable();
            $table->text('receiver_address')->nullable();

            // Giá, số lượng, giảm giá
            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);

            // Tổng giá của từng dòng sản phẩm
            $table->decimal('subtotal', 15, 2)->default(0);

            // Phí vận chuyển riêng từng sản phẩm (nếu có)
            $table->decimal('shipping_fee', 15, 2)->default(0);

            // Tổng giá sản phẩm (price * quantity - discount)
            $table->decimal('total_price', 15, 2)->default(0);

            // Số tiền cuối cùng sau áp dụng voucher, giảm giá, phí ship...
            $table->decimal('final_amount', 15, 2)->default(0);

            // Liên kết voucher (nếu có)
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('set null');

            // Phương thức thanh toán, trạng thái
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_status', 50)->nullable();

            // Trạng thái xử lý riêng của sản phẩm (ví dụ: đang giao, đã nhận, hoàn trả)
            $table->string('order_status', 50)->default('pending');

            // Tổng tiền đơn hàng con (nếu có tính tổng riêng)
            $table->decimal('total', 15, 2)->default(0);

            // Ghi chú sản phẩm
            $table->text('note')->nullable();

            // Trạng thái chung
            $table->string('status', 50)->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
