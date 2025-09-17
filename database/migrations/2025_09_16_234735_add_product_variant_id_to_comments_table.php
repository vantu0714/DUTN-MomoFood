<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('product_variant_id')->nullable()->after('product_id');

            // Nếu có bảng product_variants thì thêm ràng buộc khóa ngoại
            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });
    }
};
