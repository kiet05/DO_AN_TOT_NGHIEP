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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('receiver_name', 100);
            $table->string('receiver_phone', 20);
            $table->text('receiver_address');
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2);
            $table->decimal('final_amount', 15, 2);
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('set null');
            $table->string('payment_method', 50);
            $table->string('payment_status', 50);
            $table->string('order_status', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};