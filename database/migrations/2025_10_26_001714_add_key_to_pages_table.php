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
    Schema::table('pages', function (Blueprint $table) {
        if (!Schema::hasColumn('pages', 'key')) {
            $table->string('key')->unique()->after('id');
        }
    });
}

public function down(): void
{
    Schema::table('pages', function (Blueprint $table) {
        if (Schema::hasColumn('pages', 'key')) {
            $table->dropUnique('pages_key_unique');
            $table->dropColumn('key');
        }
    });
}

};
