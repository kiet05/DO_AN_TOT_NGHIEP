<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('voucher_usages', function (Blueprint $table) {
        $table->decimal('discount_amount', 15, 2)->default(0)->after('user_id');
    });
}

public function down()
{
    Schema::table('voucher_usages', function (Blueprint $table) {
        $table->dropColumn('discount_amount');
    });
}
};
