<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->string('refund_proof_image')->nullable()
                ->comment('Ảnh chứng minh đã hoàn tiền');

            $table->string('approved_by_name')->nullable()
                ->comment('Tên người duyệt hoàn tiền (nhập tay)');
        });
    }

    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn([
                'refund_proof_image',
                'approved_by_name',
            ]);
        });
    }
};
