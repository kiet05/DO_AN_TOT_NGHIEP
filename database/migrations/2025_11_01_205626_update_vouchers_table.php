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
        Schema::table('vouchers', function (Blueprint $table) {
            if (!Schema::hasColumn('vouchers', 'code')) {
                $table->string('code')->unique()->after('id');
            }

            if (!Schema::hasColumn('vouchers', 'name')) {
                $table->string('name')->nullable()->after('code');
            }

            if (!Schema::hasColumn('vouchers', 'type')) {
                $table->enum('type', ['percent', 'fixed'])->default('percent');
            }

            if (!Schema::hasColumn('vouchers', 'value')) {
                $table->decimal('value', 10, 2)->unsigned()->default(0);
            }

            if (!Schema::hasColumn('vouchers', 'max_discount')) {
                $table->decimal('max_discount', 10, 2)->unsigned()->nullable();
            }

            if (!Schema::hasColumn('vouchers', 'min_order_value')) {
                $table->decimal('min_order_value', 10, 2)->unsigned()->nullable();
            }

            if (!Schema::hasColumn('vouchers', 'apply_type')) {
                $table->enum('apply_type', ['all', 'products', 'categories'])->default('all');
            }

            if (!Schema::hasColumn('vouchers', 'usage_limit')) {
                $table->unsignedInteger('usage_limit')->nullable();
            }

            if (!Schema::hasColumn('vouchers', 'used_count')) {
                $table->unsignedInteger('used_count')->default(0);
            }

            if (!Schema::hasColumn('vouchers', 'start_at')) {
                $table->dateTime('start_at')->nullable();
            }

            if (!Schema::hasColumn('vouchers', 'end_at')) {
                $table->dateTime('end_at')->nullable();
            }

            if (!Schema::hasColumn('vouchers', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'name',
                'type',
                'value',
                'max_discount',
                'min_order_value',
                'apply_type',
                'usage_limit',
                'used_count',
                'start_at',
                'end_at',
                'is_active'
            ]);
        });
    }
};
