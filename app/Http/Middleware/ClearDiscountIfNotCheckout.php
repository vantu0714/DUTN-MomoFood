<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClearDiscountIfNotCheckout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Nếu không phải trang thanh toán, thì xóa mã giảm giá
        $allowedRoutes = [
            'clients.order',
            'order.applyCoupon',
            'order.removeCoupon',
            'order.store',
            'order.success',
            'recipients.select',
        ];

        // Nếu route hiện tại KHÔNG thuộc nhóm được phép giữ session mã giảm giá thì xoá
        if (!in_array(optional($request->route())->getName(), $allowedRoutes)) {
            session()->forget(['promotion', 'promotion_code', 'discount']);
        }

        return $next($request);
    }
}
