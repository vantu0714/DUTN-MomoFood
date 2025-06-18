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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('promotion_name');
            $table->enum('discount_type', ['fixed', 'percent']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('max_discount_value', 10, 2)->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->text('description')->nullable(); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
