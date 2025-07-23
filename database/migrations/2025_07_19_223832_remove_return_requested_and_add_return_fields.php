<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // 1. Xử lý trường return_requested cũ
            if (Schema::hasColumn('orders', 'return_requested')) {
                $table->dropColumn('return_requested');
            }

            // 2. Thêm các trường mới (chỉ nếu chưa tồn tại)
            if (!Schema::hasColumn('orders', 'return_approved')) {
                $table->boolean('return_approved')->nullable()->after('status');
            }

            if (!Schema::hasColumn('orders', 'return_reason')) {
                $table->text('return_reason')->nullable()->after('return_approved');
            }

            if (!Schema::hasColumn('orders', 'return_rejection_reason')) {
                $table->text('return_rejection_reason')->nullable()->after('return_reason');
            }

            if (!Schema::hasColumn('orders', 'return_requested_at')) {
                $table->timestamp('return_requested_at')->nullable()->after('return_rejection_reason');
            }

            if (!Schema::hasColumn('orders', 'return_processed_at')) {
                $table->timestamp('return_processed_at')->nullable()->after('return_requested_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // 1. Khôi phục trường cũ nếu cần
            if (!Schema::hasColumn('orders', 'return_requested')) {
                $table->tinyInteger('return_requested')->default(0)->after('status');
            }

            // 2. Xóa các trường mới (nếu tồn tại)
            $columnsToDrop = [
                'return_approved',
                'return_reason',
                'return_rejection_reason',
                'return_requested_at',
                'return_processed_at'
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
