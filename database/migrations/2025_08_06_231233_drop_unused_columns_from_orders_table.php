<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_started_at', 'estimated_delivery_time']);
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('shipping_started_at')->nullable()->after('status');
            $table->timestamp('estimated_delivery_time')->nullable()->after('shipping_started_at');
        });
    }
};
