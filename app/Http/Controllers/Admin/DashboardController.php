<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;
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

        // Query lọc theo trạng thái đơn hàng
        $baseOrderQuery = Order::query();
        if ($filterType === 'date' && $fromDate && $toDate) {
            $baseOrderQuery->whereBetween('created_at', ["$fromDate 00:00:00", "$toDate 23:59:59"]);
        } elseif ($filterType === 'month' && $month && $year) {
            $baseOrderQuery->whereMonth('created_at', $month)->whereYear('created_at', $year);
        } elseif ($filterType === 'year' && $year) {
            $baseOrderQuery->whereYear('created_at', $year);
        }

        // Tổng đơn hàng và doanh thu từ tất cả đơn
        $totalOrders = (clone $baseOrderQuery)->count();
        $totalRevenue = (clone $baseOrderQuery)->where('status', 4)->sum('total_price');
        $totalProductsSold = OrderDetail::whereIn('order_id', (clone $baseOrderQuery)->where('status', 4)->pluck('id'))->sum('quantity');

        // Đơn hàng hoàn thành
        $completedOrderCount = (clone $baseOrderQuery)->where('status', 4)->count();

        // Đơn hàng đã hủy
        $cancelledOrderCount = (clone $baseOrderQuery)->where('status', 6)->count();

        // Lợi nhuận từ đơn hoàn thành
        $completedOrderIds = (clone $baseOrderQuery)->where('status', 4)->pluck('id');

        $profits = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->leftJoin('products as p', 'p.id', '=', 'od.product_id')
            ->leftJoin('product_variants as pv', 'pv.id', '=', 'od.product_variant_id')
            ->whereIn('od.order_id', $completedOrderIds)
            ->selectRaw("
                SUM(od.quantity * od.price) as total_revenue,
                SUM(od.quantity * 
                    CASE 
                        WHEN od.product_variant_id IS NULL THEN p.original_price
                        ELSE pv.price
                    END
                ) as total_cost,
                SUM(od.quantity * (
                    od.price -
                    CASE 
                        WHEN od.product_variant_id IS NULL THEN p.original_price
                        ELSE pv.price
                    END
                )) as total_profit
            ")
            ->first();

        $completedTotalProfit = $profits->total_profit ?? 0;

        // Biểu đồ doanh thu theo tháng
        $chartLabels = [];
        $chartData = [];

        if ($filterType === 'year' && $year) {
            // Hiển thị duy nhất 1 cột cho cả năm
            $yearlyRevenue = (clone $baseOrderQuery)
                ->where('status', 4)
                ->sum('total_price');

            $chartLabels[] = 'Năm ' . $year;
            $chartData[] = $yearlyRevenue;
        } else {
            // Biểu đồ doanh thu theo tháng (mặc định)
            $monthlyRevenue = (clone $baseOrderQuery)
                ->where('status', 4)
                ->selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            for ($i = 1; $i <= 12; $i++) {
                $chartLabels[] = 'Tháng ' . $i;
                $chartData[] = $monthlyRevenue[$i] ?? 0;
            }
        }


        // Sản phẩm bán chạy (tính theo biến thể nếu có)
        $bestSellingProducts = DB::table('order_details as od')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->leftJoin('product_variants as pv', 'pv.id', '=', 'od.product_variant_id')
            ->leftJoin('product_variant_values as pvav', 'pvav.product_variant_id', '=', 'pv.id')
            ->leftJoin('attribute_values as av', 'av.id', '=', 'pvav.attribute_value_id')
            ->leftJoin('attributes as a', 'a.id', '=', 'av.attribute_id')
            ->whereIn('od.order_id', $completedOrderIds)
            ->select(
                'p.id as product_id',
                'p.product_name',
                'p.image',
                'pv.sku as variant_name',
                DB::raw("GROUP_CONCAT(DISTINCT CONCAT(a.name, ': ', av.value) ORDER BY a.name SEPARATOR ', ') as variant_attributes"),
                DB::raw('SUM(od.quantity) as total_quantity'),
                DB::raw('MAX(od.price) as latest_price')
            )
            ->groupBy('p.id', 'p.product_name', 'p.image', 'pv.sku')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // Tổng tồn kho
        $totalStock = Product::sum('quantity_in_stock');


        // Sản phẩm đã hết hàng
        // Sản phẩm cha hết hàng (không có biến thể hoặc tổng hết hàng)
        // Sản phẩm cha hết hàng (không có biến thể hoặc tổng hết hàng)
        $outOfStockProducts = Product::with('category')->where('quantity_in_stock', 0);

        // Lọc theo ngày / tháng / năm nếu có
        if ($filterType === 'date' && $fromDate && $toDate) {
            $outOfStockProducts->whereBetween('created_at', ["$fromDate 00:00:00", "$toDate 23:59:59"]);
        } elseif ($filterType === 'month' && $month && $year) {
            $outOfStockProducts->whereYear('created_at', $year)
                ->whereMonth('created_at', $month);
        } elseif ($filterType === 'year' && $year) {
            $outOfStockProducts->whereYear('created_at', $year);
        }

        $outOfStockProducts = $outOfStockProducts->get();


        // Biến thể sản phẩm hết hàng
        $outOfStockVariants = ProductVariant::with('product.category')->where('quantity_in_stock', 0);

        // Lọc theo ngày / tháng / năm nếu có
        if ($filterType === 'date' && $fromDate && $toDate) {
            $outOfStockVariants->whereBetween('created_at', ["$fromDate 00:00:00", "$toDate 23:59:59"]);
        } elseif ($filterType === 'month' && $month && $year) {
            $outOfStockVariants->whereYear('created_at', $year)
                ->whereMonth('created_at', $month);
        } elseif ($filterType === 'year' && $year) {
            $outOfStockVariants->whereYear('created_at', $year);
        }

        $outOfStockVariants = $outOfStockVariants->get();

        // Tổng số sản phẩm hết hàng
        $totalOutOfStock = $outOfStockProducts->count() + $outOfStockVariants->count();

        // Khách hàng mua nhiều nhất
        $topCustomers = User::whereHas('orders', function ($q) use ($completedOrderIds) {
            $q->whereIn('id', $completedOrderIds);
        })
            ->withCount(['orders' => function ($q) use ($completedOrderIds) {
                $q->whereIn('id', $completedOrderIds);
            }])
            ->withSum(['orders' => function ($q) use ($completedOrderIds) {
                $q->whereIn('id', $completedOrderIds);
            }], 'total_price')
            ->orderByDesc('orders_sum_total_price')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'totalProductsSold',
            'completedOrderCount',
            'cancelledOrderCount',
            'completedTotalProfit',
            'bestSellingProducts',
            'topCustomers',
            'chartLabels',
            'chartData',
            'filterType',
            'fromDate',
            'toDate',
            'month',
            'year',
            'outOfStockProducts',
            'outOfStockVariants',
            'totalOutOfStock'
        ));
    }
}
