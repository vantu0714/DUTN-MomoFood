<?php

namespace App\Providers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\Type;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrap();

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
        Relation::morphMap([
            'product' => Product::class,
            'variant' => ProductVariant::class,
        ]);

        if (DB::getDriverName() === 'mysql') {

            // Nếu tinyinteger chưa tồn tại, mới addType (tránh lỗi trùng key)
            if (!Type::hasType('tinyinteger')) {
                Type::addType('tinyinteger', BooleanType::class);
            }

            $platform = DB::getDoctrineSchemaManager()->getDatabasePlatform();

            // Ánh xạ tinyint và tinyinteger
            $platform->registerDoctrineTypeMapping('tinyint', 'boolean');
            $platform->registerDoctrineTypeMapping('tinyinteger', 'tinyinteger');

            // Enum nếu có
            $platform->registerDoctrineTypeMapping('enum', 'string');
        }
    }
}
