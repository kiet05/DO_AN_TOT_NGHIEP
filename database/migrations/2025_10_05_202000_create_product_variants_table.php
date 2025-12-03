<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            // Liên kết sản phẩm
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // Mã SKU
            $table->string('sku', 50)->unique();

            // Giá và số lượng
            $table->decimal('price', 15, 2);
            $table->unsignedInteger('quantity');

            // Ảnh sản phẩm
            $table->string('image_url', 255)->nullable();

            // Trạng thái
            $table->tinyInteger('status')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
