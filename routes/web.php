<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\clients\HomeController;

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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/', function () {
    return view('welcome');
});

Route::get('/backend', function () {
    return view('backend.layouts.app');
});
