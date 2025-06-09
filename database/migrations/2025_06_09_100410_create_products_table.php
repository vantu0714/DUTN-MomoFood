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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code');
            $table->string('product_name');
            $table->string('image');
            $table->text('description')->nullable();
            $table->text('ingredients')->nullable();
            $table->date('expiration_date')->nullable();
            $table->decimal('original_price', 10, 2);
            $table->decimal('discounted_price', 10, 2);
            $table->boolean('status')->default(1);
            $table->integer('view')->default(0);
            $table->boolean('is_show_home')->default(0);
            $table->foreignId('category_id')->constrained('categories');
            $table->timestamps();            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
