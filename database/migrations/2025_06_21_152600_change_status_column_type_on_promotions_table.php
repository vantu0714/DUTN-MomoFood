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
        //
        Schema::table('promotions', function (Blueprint $table) {
            // Nếu cần, có thể dùng raw SQL để xoá ENUM
            $table->boolean('status')->default(1)->change(); // true: hoạt động, false: không hoạt động
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('promotions', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('inactive')->change();
        });
    }
};
