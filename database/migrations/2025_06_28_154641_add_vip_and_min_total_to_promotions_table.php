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
        Schema::table('promotions', function (Blueprint $table) {
            $table->boolean('vip_only')->default(false)->after('status'); // Chỉ dành cho VIP
            $table->decimal('min_total_spent', 15, 2)->nullable()->after('max_discount_value'); // Mức tối thiểu áp dụng
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            //
        });
    }
};
