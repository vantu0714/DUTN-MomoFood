<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('reason')->nullable();
            $table->timestamp('cancelled_at');
            $table->timestamps();
        });

        Schema::create('order_cancellation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_cancellation_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_detail_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_cancellation_items');
        Schema::dropIfExists('order_cancellations');
    }
};
