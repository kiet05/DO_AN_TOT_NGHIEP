<?php
// database/migrations/xxxx_add_deleted_at_to_banners_table.php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void {
        Schema::table('banners', function (Blueprint $table) {
            if (!Schema::hasColumn('banners', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }
    public function down(): void {
        Schema::table('banners', function (Blueprint $table) {
            if (Schema::hasColumn('banners', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
