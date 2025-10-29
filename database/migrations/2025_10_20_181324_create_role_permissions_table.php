<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
<<<<<<< HEAD
        Schema::create('role_permission', function (Blueprint $table) {
=======
        Schema::create('role_permissions', function (Blueprint $table) {
>>>>>>> origin/feature/orders
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
<<<<<<< HEAD
        Schema::dropIfExists('role_permission');
    }
};
=======
        Schema::dropIfExists('role_permissions');
    }
};
>>>>>>> origin/feature/orders
