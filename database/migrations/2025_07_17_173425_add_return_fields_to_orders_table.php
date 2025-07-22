<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReturnFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('updated_at');
            $table->boolean('return_requested')->default(false)->after('completed_at');
            $table->boolean('return_approved')->nullable()->after('return_requested');
            $table->text('return_reason')->nullable()->after('return_approved');
            $table->text('return_rejection_reason')->nullable()->after('return_reason');
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
            $table->dropColumn([
                'completed_at',
                'return_requested',
                'return_approved',
                'return_reason',
                'return_rejection_reason'
            ]);
        });
    }
}
