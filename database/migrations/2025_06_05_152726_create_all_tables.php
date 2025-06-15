<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('password');
            $table->foreignId('role_id')->constrained('roles');
            $table->string('avatar')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->boolean('status');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code');
            $table->string('product_name');
            $table->string('image')->nullable();
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

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity_in_stock');
            $table->string('sku')->nullable();
            $table->boolean('status')->default(1);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('image_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->string('image_product');
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('promotion_name');
            $table->string('discount_type');
            $table->decimal('discount_value', 10, 2);
            $table->decimal('max_discount_value', 10, 2)->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('recipient_address');
            $table->string('promotion')->nullable();
            $table->decimal('shipping_fee', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('payment_method');
            $table->string('payment_status');
            $table->string('status');
            $table->string('note')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('product_variant_id')->constrained('product_variants');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('order_detail_id')->constrained('order_details');
            $table->tinyInteger('rating');
            $table->text('comment');
            $table->timestamps();
        });

        Schema::create('cart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('cart');
            $table->foreignId('product_variant_id')->constrained('product_variants');
            $table->integer('quantity');
            $table->decimal('original_price', 10, 2);
            $table->decimal('discounted_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });

        Schema::create('vn_pay_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->decimal('amount', 10, 2);
            $table->string('trans_id');
            $table->string('status');
            $table->string('payment_method');
            $table->json('response_data')->nullable();
            $table->timestamps();
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('product_id')->constrained('products');
            $table->text('content');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('news');
        Schema::dropIfExists('vn_pay_transactions');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('cart');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('image_products');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};


