<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Họ tên
            $table->string('email');                   // Email
            $table->string('phone', 20)->nullable();   // SĐT
            $table->string('subject')->nullable();     // Tiêu đề
            $table->text('message');                   // Nội dung
            $table->enum('status', ['new', 'read'])
                  ->default('new');                    // Trạng thái xử lý
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
