<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('original_price', 15, 2)->nullable()->change();
            $table->decimal('discounted_price', 15, 2)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('original_price', 15, 2)->change();
            $table->decimal('discounted_price', 15, 2)->change();
        });
    }
};
