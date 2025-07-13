<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement("ALTER TABLE products CHANGE quantity quantity_in_stock INT UNSIGNED DEFAULT 0");
    }

    public function down()
    {
        DB::statement("ALTER TABLE products CHANGE quantity_in_stock quantity INT UNSIGNED DEFAULT 0");
    }
};
