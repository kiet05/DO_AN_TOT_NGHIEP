<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('discount_type', 20); // e.g., 'percentage' or 'fixed'
            $table->decimal('discount_value', 15, 2);
            $table->integer('usage_limit')->unsigned();
            $table->tinyInteger('status')->default(1);
            $table->dateTime('expired_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};