<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->string('gateway', 50)->index();       
            $table->string('app_trans_id')->unique();     // yymmdd_order_rand
            $table->string('zp_trans_id')->nullable()->index();

            $table->unsignedBigInteger('amount');
            $table->string('currency', 10)->default('VND');

            $table->enum('status', ['pending','success','failed','canceled','refunded'])
                  ->default('pending')->index();

            $table->json('meta')->nullable();
            $table->timestamp('paid_at')->nullable()->index();

            $table->timestamps();
            $table->index('created_at');
        });

        // 2️⃣ payment_logs (đã thêm cột message)
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()
                  ->constrained('payments')->nullOnDelete();

            $table->string('type', 40); 
            $table->string('message')->nullable(); // 🟢 thêm dòng này
            $table->json('payload')->nullable();

            $table->timestamps();
            $table->index(['payment_id', 'type']);
        });

        // 3️⃣ orders: bổ sung cột liên quan thanh toán
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['unpaid','paid','failed','refunded'])
                      ->default('unpaid')->after('status');
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'payment_id')) {
                $table->foreignId('payment_id')->nullable()->after('payment_method')
                      ->constrained('payments')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_id')) {
                try { $table->dropConstrainedForeignId('payment_id'); } catch (\Throwable $e) {}
                $table->dropColumn('payment_id');
            }
            if (Schema::hasColumn('orders', 'payment_method')) $table->dropColumn('payment_method');
            if (Schema::hasColumn('orders', 'payment_status')) $table->dropColumn('payment_status');
        });

        Schema::dropIfExists('payment_logs');
        Schema::dropIfExists('payments');
    }
};
