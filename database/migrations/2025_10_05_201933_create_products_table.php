<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('base_price', 15, 2);
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->string('image_main', 255)->nullable();
            $table->boolean('is_new')->default(false);
            $table->boolean('is_on_sale')->default(false);
            $table->tinyInteger('status')->default(1);

            $table->timestamps();

            // ✅ Thêm SoftDeletes
            $table->softDeletes(); // tạo cột deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
