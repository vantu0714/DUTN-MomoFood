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
        Schema::table('products', function (Blueprint $table) {
            // Thêm cột origin_id liên kết với bảng product_origins
            $table->foreignId('origin_id')
                  ->nullable()
                  ->constrained('product_origins')
                  ->nullOnDelete()
                  ->after('ingredients'); // đặt vị trí sau cột ingredients
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Xoá ràng buộc và cột origin_id
            $table->dropForeign(['origin_id']);
            $table->dropColumn('origin_id');
        });
    }
};
