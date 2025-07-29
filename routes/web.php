<?php

use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\InfoController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Clients\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Clients\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComboItemController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Clients\CartClientController;
use App\Http\Controllers\Clients\ShopController;
use App\Http\Controllers\Clients\NewsController;
use App\Http\Controllers\Clients\ContactsController;
use App\Http\Controllers\Clients\OrderController as ClientsOrderController;
use App\Http\Controllers\Clients\ProductDetailController;
use App\Http\Controllers\VNPayController;
use App\Http\Controllers\clients\CommentController as ClientCommentController;
use App\Http\Controllers\Clients\GioithieuController;
use App\Http\Controllers\RecipientController;
use App\Http\Controllers\ThongKeController;

// ==================== PUBLIC ROUTES ====================

// Home & Main Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('clients.search');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/search-ajax', [HomeController::class, 'searchAjax'])->name('clients.search.ajax');
Route::get('/shop/category/{id}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/product/{id}', [ProductDetailController::class, 'show'])->name('product-detail.show');
Route::get('/tin-tuc', [NewsController::class, 'index'])->name('news.index');
Route::get('/lien-he', [ContactsController::class, 'index'])->name('contacts.index');
Route::get('/gioi-thieu', [GioithieuController::class, 'index'])->name('gioithieu.index');

// Authentication
Route::controller(AuthController::class)->group(function () {
    // Login/Logout
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');

    // Socialite
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

    // Registration
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');

    // Password Reset
    Route::get('/forgot-password', 'showForgotPassword')->name('password.request');
    Route::post('/forgot-password', 'sendResetRedirect')->name('password.email');
    Route::get('/reset-password', 'showResetForm')->name('password.reset');
    Route::post('/reset-password', 'resetPassword')->name('password.update');
    Route::get('/clear-reset-session', function () {
        session()->forget('reset_email');
        return response()->json(['status' => 'ok']);
    })->name('reset.session.clear');
});

// Comments
Route::post('/comments', [ClientCommentController::class, 'store'])->name('comments.store');

//vn-pay
Route::post('/vnpay/payment', [VNPayController::class, 'create'])->name('vnpay.payment');
Route::get('/vnpay-return', [VNPayController::class, 'vnpayReturn'])->name('vnpay.return');

Route::post('carts/add', [CartClientController::class, 'addToCart'])->name('carts.add')->middleware('auth');

// ==================== CLIENT AUTHENTICATED ROUTES ====================
Route::middleware(['auth', 'client'])->group(function () {
    // Profile Management
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/info', [AuthController::class, 'info'])->name('info');
        Route::get('/edit', [AuthController::class, 'showEditProfile'])->name('edit');
        Route::post('/edit', [AuthController::class, 'editProfile'])->name('update');
        Route::get('/changepassword', [AuthController::class, 'showChangePassword'])->name('changepassword');
        Route::post('/changepassword', [AuthController::class, 'updatePassword'])->name('updatepassword');

        // Orders
        Route::get('/orders', [ClientsOrderController::class, 'orderList'])->name('orders');
        Route::post('/create-payment', [ClientsOrderController::class, 'createPayment'])->name('create-payment');
        Route::get('/order/{id}', [ClientsOrderController::class, 'orderDetail'])->name('orderdetail');
        Route::post('/orders/{id}/cancel', [ClientsOrderController::class, 'cancel'])->name('ordercancel');
    });

    // Cart Management
    Route::prefix('carts')->group(function () {
        Route::get('/', [CartClientController::class, 'index'])->name('carts.index');
        Route::post('/update/{id}', [CartClientController::class, 'updateQuantity'])->name('carts.updateQuantity');
        Route::post('/update-ajax', [CartClientController::class, 'updateAjax'])->name('carts.updateAjax');
        Route::get('/remove/{id}', [CartClientController::class, 'removeFromCart'])->name('carts.remove');
        Route::post('/clear', [CartClientController::class, 'clearCart'])->name('carts.clear');
        Route::post('/remove-selected', [CartClientController::class, 'removeSelected'])->name('carts.removeSelected');
    });

    // Checkout
    Route::get('/order', [ClientsOrderController::class, 'index'])->name('clients.order');
    Route::post('/store', [ClientsOrderController::class, 'store'])->name('order.store');
    Route::get('/success/{id}', [ClientsOrderController::class, 'success'])->name('order.success');
    Route::post('/apply-coupon', [ClientsOrderController::class, 'applyCoupon'])->name('order.applyCoupon');
    Route::get('/remove-coupon', [ClientsOrderController::class, 'removeCoupon'])->name('order.removeCoupon');

    Route::post('/recipients', [RecipientController::class, 'store'])->name('recipients.store');
    // Route chọn địa chỉ
    Route::post('/recipients/select', [RecipientController::class, 'select'])->name('recipients.select');



    //tt nguoi nhan
    // Route::post('/store', [RecipientController::class, 'store'])->name('recipients.store');

});

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/info', [InfoController::class, 'info'])->name('info');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

    // Category Management
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::get('/{id}/show', [CategoryController::class, 'show'])->name('show');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}/destroy', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Product Management
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

    // Product Variants
    Route::prefix('product-variants')->name('product_variants.')->group(function () {

        Route::get('/cancel', [ProductVariantController::class, 'cancel'])->name('cancel');
        Route::get('/multi-create', [ProductVariantController::class, 'createMultiple'])->name('createMultiple');
        Route::post('/multi-store', [ProductVariantController::class, 'storeMultiple'])->name('storeMultiple');

        Route::get('/', [ProductVariantController::class, 'index'])->name('index');
        Route::get('/create', [ProductVariantController::class, 'create'])->name('create');
        Route::post('/store', [ProductVariantController::class, 'store'])->name('store');
        Route::get('/{product_variant}/edit', [ProductVariantController::class, 'edit'])->name('edit');
        Route::put('/{product_variant}', [ProductVariantController::class, 'update'])->name('update');
        Route::delete('/{product_variant}/destroy', [ProductVariantController::class, 'destroy'])->name('destroy');
        Route::get('/{product_variant}', [ProductVariantController::class, 'show'])->name('show');
    });
    // Order Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/store', [OrderController::class, 'store'])->name('store');
        Route::get('/{id}/show', [OrderController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
        Route::put('/{id}', [OrderController::class, 'update'])->name('update');
        Route::patch('{order}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::put('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });

    // Promotion Management
    Route::prefix('promotions')->name('promotions.')->group(function () {
        Route::get('/', [PromotionController::class, 'index'])->name('index');
        Route::get('/create', [PromotionController::class, 'create'])->name('create');
        Route::post('/store', [PromotionController::class, 'store'])->name('store');
        Route::get('/{id}show', [PromotionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PromotionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PromotionController::class, 'update'])->name('update');
        Route::delete('/{id}', [PromotionController::class, 'destroy'])->name('destroy');
    });

    // Comment Management
    Route::resource('comments', CommentController::class)->only('index');
    Route::get('comments/{product}', [CommentController::class, 'show'])->name('comments.show');
    Route::put('comments/{comment}/toggle-status', [CommentController::class, 'toggleStatus'])->name('comments.toggle');




    // thống kê
    Route::get('thongke', [ThongKeController::class, 'index'])->name('thongke');

    // Combo Management
    Route::resource('combo_items', ComboItemController::class)->except(['show', 'edit', 'update']);
    Route::delete('/combo-items/delete-combo/{comboId}', [ComboItemController::class, 'destroyCombo'])
        ->name('combo_items.delete_combo');
});

Route::get('/filter-category', [HomeController::class, 'filterByCategory'])->name('home.filter.category');
