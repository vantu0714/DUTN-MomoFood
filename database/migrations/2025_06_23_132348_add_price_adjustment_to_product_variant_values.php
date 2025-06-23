<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceAdjustmentToProductVariantValues extends Migration
{
    public function up()
    {
        Schema::table('product_variant_values', function (Blueprint $table) {
            $table->decimal('price_adjustment', 10, 2)->default(0)->after('attribute_value_id');
        });
    }

    public function down()
    {
        Schema::table('product_variant_values', function (Blueprint $table) {
            $table->dropColumn('price_adjustment');
        });
    }
}

