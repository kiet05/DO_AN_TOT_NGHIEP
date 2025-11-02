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
        // Chạy an toàn: nếu chưa có bảng mới tạo
        if (!Schema::hasTable('user_otps')) {
            Schema::create('user_otps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->string('otp_code', 6);      // mã OTP 6 số
                $table->timestamp('expires_at');    // hạn dùng OTP
                $table->timestamp('used_at')->nullable(); // thời điểm dùng (nếu đã dùng)
                $table->timestamps();

                $table->index(['user_id', 'otp_code']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_otps');
    }
};



