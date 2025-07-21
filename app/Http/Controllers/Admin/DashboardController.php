<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterType = $request->filter_type;

        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $month = $request->month;
        $year = $request->year ?? now()->year;

        // Query cơ bản cho đơn hàng đã hoàn thành
        $orderQuery = Order::query()->where('status', 3);

        // Áp dụng bộ lọc nếu người dùng đã chọn
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

        $orderIds = $orderQuery->pluck('id');

        $totalSold = OrderDetail::whereIn('order_id', $orderIds)->sum('quantity');

        // Sản phẩm bán chạy
        $orderIds = Order::where('status', 3)->pluck('id');

        $bestSellingProducts = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->whereIn('order_details.order_id', $orderIds)
            ->select(
                'products.id as product_id',
                'products.product_name',
                'products.image',
                DB::raw('SUM(order_details.quantity) as total_quantity'),
                DB::raw('MAX(order_details.price) as latest_price') // giá gần đây nhất để hiển thị
            )
            ->groupBy('products.id', 'products.product_name', 'products.image')
            ->orderByDesc('total_quantity')
            ->take(10)
            ->get();


        $filteredOrders = clone $orderQuery;

        $monthlyData = $filteredOrders
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

        // Khách hàng mua nhiều nhất
        $topCustomers = User::whereHas('orders', function ($q) {
            $q->where('status', 3);
        })
            ->withCount(['orders' => function ($q) {
                $q->where('status', 3);
            }])
            ->withSum(['orders' => function ($q) {
                $q->where('status', 3);
            }], 'total_price')
            ->orderByDesc('orders_count')
            ->take(5)
            ->get();

        // Tính tổng lợi nhuận
        // Tính tổng lợi nhuận
        $profits = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->leftJoin('products as p', 'p.id', '=', 'od.product_id')
            ->leftJoin('product_variants as pv', 'pv.id', '=', 'od.product_variant_id')
            ->select(
                DB::raw("COALESCE(p.product_name, 'Không xác định') as product_name"),
                DB::raw("SUM(od.quantity) as total_sold"),
                DB::raw("SUM(od.quantity * od.price) as total_revenue"),
                DB::raw("
            SUM(
                od.quantity * 
                CASE 
                    WHEN od.product_variant_id IS NULL THEN p.original_price 
                    ELSE pv.price 
                END
            ) as total_cost
        "),
                DB::raw("
            SUM(
                od.quantity * (
                    od.price - 
                    CASE 
                        WHEN od.product_variant_id IS NULL THEN p.original_price 
                        ELSE pv.price 
                    END
                )
            ) as total_profit
        ")
            )
            ->where('o.status', 3)
            ->groupBy('od.product_id', 'od.product_variant_id')
            ->get();

        // ⚠️ Bắt buộc phải có dòng này trước khi dùng compact
        $totalCost = $profits->sum('total_cost');
        $totalProfit = $profits->sum('total_profit');


        $totalStock = Product::sum('quantity_in_stock');

        return view('admin.dashboard', compact(
            'profits',
            'totalRevenue',
            'totalOrders',
            'totalSold',
            'bestSellingProducts',
            'chartLabels',
            'chartData',
            'filterType',
            'fromDate',
            'toDate',
            'month',
            'year',
            'topCustomers',
            'totalCost',
            'totalProfit' // cái này bạn đã có rồi
        ));
    }
}
