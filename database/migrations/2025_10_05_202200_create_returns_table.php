<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->text('reason');
        $table->string('refund_account_number')->nullable()->after('refund_amount')->comment('Số tài khoản nhận tiền hoàn');

            $table->enum('action_type', [
                'refund_full',
                'refund_partial',
                'exchange_product',
                'exchange_variant',
            ])->default('refund_full');

            $table->string('proof_image', 255)->nullable();
            $table->json('evidence_urls')->nullable();

            // 0=pending, 1=approved, 2=rejected, 3=refunding, 4=completed
            $table->tinyInteger('status')->default(0)->index();

            // thông tin hoàn tiền
            $table->enum('refund_method', ['manual', 'wallet'])->nullable();
            $table->decimal('refund_amount', 12, 2)->default(0);

            // người duyệt và thời điểm quyết định
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['order_id', 'user_id']);
            $table->index('approved_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
