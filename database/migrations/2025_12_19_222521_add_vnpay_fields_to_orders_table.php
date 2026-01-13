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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('vnp_txn_ref')->nullable()->after('payment_status');
            $table->json('vnp_response')->nullable()->after('vnp_txn_ref');
            $table->string('vnp_transaction_no')->nullable()->after('vnp_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['vnp_txn_ref', 'vnp_response', 'vnp_transaction_no']);
        });
    }
};
