<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Giả sử user có cột role_id = 1 là admin
        if (auth()->check() && auth()->user()->role_id == 1) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập trang này');
    }
}
