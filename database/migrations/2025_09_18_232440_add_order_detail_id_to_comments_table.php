<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('order_detail_id')->nullable()->after('order_id');

            $table->foreign('order_detail_id')
                ->references('id')->on('order_details')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['order_detail_id']);
            $table->dropColumn('order_detail_id');
        });
    }
};
