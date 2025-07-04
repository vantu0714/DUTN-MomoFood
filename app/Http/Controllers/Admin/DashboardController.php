<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterType = $request->filter_type ?? 'date';
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $month = $request->month;
        $year = $request->year ?? now()->year;

        // Query cơ bản
        $orderQuery = Order::where('status', 3);

        // Áp dụng bộ lọc
        if ($filterType === 'date' && $fromDate && $toDate) {
            $orderQuery->whereBetween('created_at', ["$fromDate 00:00:00", "$toDate 23:59:59"]);
        } elseif ($filterType === 'month' && $month && $year) {
            $orderQuery->whereMonth('created_at', $month)
                       ->whereYear('created_at', $year);
        } elseif ($filterType === 'year' && $year) {
            $orderQuery->whereYear('created_at', $year);
        }

        // Tổng quan
        $totalRevenue = $orderQuery->sum('total_price');
        $totalOrders = $orderQuery->count();

        $totalSold = OrderDetail::whereHas('order', function ($q) use ($orderQuery) {
            $q->mergeConstraintsFrom($orderQuery);
        })->sum('quantity');

        // Sản phẩm bán chạy
        $bestSellers = Product::withSum(['orderDetails as total_sold' => function ($q) use ($orderQuery) {
            $q->whereHas('order', function ($q2) use ($orderQuery) {
                $q2->mergeConstraintsFrom($orderQuery);
            });
        }], 'quantity')
        ->orderByDesc('total_sold')
        ->take(5)
        ->get();

        // Biểu đồ doanh thu theo tháng (không lọc để giữ toàn bộ 12 tháng)
        $monthlyData = Order::where('status', 3)
            ->selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $chartLabels = [];
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartLabels[] = 'Tháng ' . $i;
            $chartData[] = $monthlyData[$i] ?? 0;
        }

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalSold',
            'bestSellers',
            'filterType',
            'chartLabels',
            'chartData',
            'fromDate',
            'toDate',
            'month',
            'year'
        ));
    }
}
