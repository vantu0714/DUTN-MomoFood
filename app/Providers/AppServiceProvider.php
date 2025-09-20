<?php

namespace App\Providers;

use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
       Paginator::useBootstrapFive();

        // Tự động chia sẻ biến $cartCount đến tất cả các view
        View::composer('*', function ($view) {
            $cartCount = 0;

            if (Auth::check()) {
                $cartCount = CartItem::whereHas('cart', function ($query) {
                    $query->where('user_id', Auth::id());
                })->count(); // Đếm số sản phẩm khác nhau
            }

            $view->with('cartCount', $cartCount);
        });

        //Nếu không ở trang thanh toán thì xóa mã giảm giá khỏi session
        if (!Request::is('orders') && !Request::is('orders/*')) {
            Session::forget(['promotion', 'discount', 'promotion_code']);
        }
    }
}
