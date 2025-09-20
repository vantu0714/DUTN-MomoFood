<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_cancellations', function (Blueprint $table) {
            if (!Schema::hasColumn('order_cancellations', 'refund_amount')) {
                $table->decimal('refund_amount', 15, 2)->default(0)->after('reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cancellations', function (Blueprint $table) {
            if (Schema::hasColumn('order_cancellations', 'refund_amount')) {
                $table->dropColumn('refund_amount');
            }
        });
    }
};
