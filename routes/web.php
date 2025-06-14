<?php

use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\InfoController;
use App\Http\Controllers\clients\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\clients\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\PromotionController;

use App\Http\Controllers\Clients\ShopController;
use App\Http\Controllers\Clients\NewsController;
use App\Http\Controllers\Clients\ContactsController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('home');
// });

Route::get('/', [HomeController::class, 'index'])->name('home.index');

//shop
Route::get('/cua-hang', [ShopController::class, 'index'])->name('shop.index');
//tin tức
Route::get('/tin-tuc', [NewsController::class, 'index'])->name('news.index');
//lien he
Route::get('/lien-he', [ContactsController::class, 'index'])->name('contacts.index');


Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    });
    Route::get(('/info'), [InfoController::class, 'info'])->name('info');
});

//users
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

// categories
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');

    Route::get('/create', [CategoryController::class, 'create'])->name('create');
    Route::get('/{id}/show', [CategoryController::class, 'show'])->name('show');
    Route::post('/store', [CategoryController::class, 'store'])->name('store');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/{category}/destroy', [CategoryController::class, 'destroy'])->name('destroy');
});
//product
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
//comments
Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
Route::get('/comments/{id}', [CommentController::class, 'show'])->name('comments.show');
Route::get('/comments/{id}/edit', [CommentController::class, 'edit'])->name('comments.edit');
Route::put('/comments/{id}', [CommentController::class, 'update'])->name('comments.update');
Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');



//Auth
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// orders
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/create', [OrderController::class, 'create'])->name('create');
    Route::post('/store', [OrderController::class, 'store'])->name('store');
    Route::get('/{id}/show', [OrderController::class, 'show'])->name('show');
    Route::get('/create', [OrderController::class, 'create'])->name('create');
});

// promotions
Route::prefix('promotions')->name('promotions.')->group(function () {
    Route::get('/', [PromotionController::class, 'index'])->name('index');
    Route::get('/create', [PromotionController::class, 'create'])->name('create');
    Route::post('/store', [PromotionController::class, 'store'])->name('store');
    Route::get('/{id}show', [PromotionController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [PromotionController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PromotionController::class, 'update'])->name('update');
    Route::delete('/{id}', [PromotionController::class, 'destroy'])->name('destroy');
});


//product_variants
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('product-variants')->name('product_variants.')->group(function () {
        Route::get('/', [ProductVariantController::class, 'index'])->name('index');
        Route::get('/create', [ProductVariantController::class, 'create'])->name('create');
        Route::post('/store', [ProductVariantController::class, 'store'])->name('store');
        Route::get('/{product_variant}/edit', [ProductVariantController::class, 'edit'])->name('edit');
        Route::put('/{product_variant}', [ProductVariantController::class, 'update'])->name('update');
        Route::delete('/{product_variant}/destroy', [ProductVariantController::class, 'destroy'])->name('destroy');
    });

});
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');


//Clients
Route::middleware(['auth', 'client'])->group(function () {
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/info', [AuthController::class, 'info'])->name('info');
        Route::get('/edit', [AuthController::class, 'showEditProfile'])->name('edit');
        Route::post('/edit', [AuthController::class, 'editProfile'])->name('update');
    });
});
