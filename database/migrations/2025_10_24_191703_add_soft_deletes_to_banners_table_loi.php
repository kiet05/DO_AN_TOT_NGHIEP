<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToBannersTable extends Migration
{
    public function up()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->softDeletes(); // Thêm cột deleted_at
        });
    }

    public function down()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Xóa cột deleted_at khi rollback
        });
    }
}