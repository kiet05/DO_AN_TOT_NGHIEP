<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_user_activities_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();     // khách hàng bị tác động
            $table->unsignedBigInteger('causer_id')->nullable();   // admin thực hiện
            $table->string('action');                               // created customer / updated customer / locked...
            $table->string('ip')->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'causer_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('user_activities');
    }
};
