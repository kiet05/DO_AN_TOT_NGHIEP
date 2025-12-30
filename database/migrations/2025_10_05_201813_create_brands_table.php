<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('brands', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('logo')->nullable();
    $table->text('description')->nullable();
    $table->tinyInteger('status')->default(1); // 1: active, 0: inactive
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};