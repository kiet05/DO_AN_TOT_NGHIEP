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
    if (!Schema::hasTable('banners')) {
        Schema::create('banners', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->string('link')->nullable();
            $table->enum('position', ['top','middle','bottom'])->default('top');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }
}

public function down(): void
{
    Schema::dropIfExists('banners');
}

};
