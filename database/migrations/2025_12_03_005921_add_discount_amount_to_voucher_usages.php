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
        $table->integer('discount_amount')->default(0);
    });
}

public function down()
{
    Schema::table('voucher_usages', function (Blueprint $table) {
        $table->dropColumn('discount_amount');
    });
}

};
