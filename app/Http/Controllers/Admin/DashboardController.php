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

        $stats = OrderDetail::join('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('product_variants', 'product_variants.id', '=', 'order_details.product_variant_id')
            ->leftJoin('order_return_items as ri', function ($join) {
                $join->on('ri.order_detail_id', '=', 'order_details.id')
                    ->where('ri.status', 'approved');
            })
            ->whereIn('order_details.order_id', (clone $baseOrderQuery)->pluck('id'))
            ->selectRaw("
        SUM(
            CASE
                WHEN orders.payment_method = 'vnpay' AND orders.status IN (1,2,3,4,7,9)
                    THEN order_details.price * order_details.quantity
                WHEN orders.payment_method = 'cod' AND orders.status IN (4,7,9)
                    THEN order_details.price * order_details.quantity
                ELSE 0
            END
        ) as product_revenue,

        SUM(
            CASE
                WHEN (orders.payment_method = 'vnpay' AND orders.status IN (1,2,3,4,7,9))
                  OR (orders.payment_method = 'cod' AND orders.status IN (4,7,9))
                    THEN orders.shipping_fee
                ELSE 0
            END
        ) as shipping_revenue,

        COALESCE(SUM(ri.quantity * order_details.price), 0) as return_revenue
    ")
            ->first();

        $totalRevenue = ($stats->product_revenue + $stats->shipping_revenue) - $stats->return_revenue;



        $totalProductsSold = OrderDetail::whereIn('order_id', (clone $baseOrderQuery)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('status', 1)->where('payment_method', 'vnpay');
                })->orWhere(function ($q) {
                    $q->where('status', 9)->where('payment_method', 'cod');
                })->orWhere('status', 4);
            })->pluck('id'))
            ->sum('quantity');


        // Đơn hàng hoàn thành
        $completedOrderCount = (clone $baseOrderQuery)->where('status', 4)->count();

        // Đơn hàng đã hủy
        $cancelledOrderCount = (clone $baseOrderQuery)->where('status', 6)->count();

        // Lợi nhuận từ đơn hoàn thành
        $completedOrderIds = (clone $baseOrderQuery)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('status', 1)->where('payment_method', 'vnpay');
                })->orWhere(function ($q) {
                    $q->where('status', 9)->where('payment_method', 'cod');
                })->orWhere('status', 4);
            })
            ->pluck('id');


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

        // Biểu đồ doanh thu + số đơn hàng theo tháng hoặc năm
        $chartLabels = [];
        $chartDataRevenue = []; // Doanh thu
        $chartDataOrders = [];  // Số đơn hàng

        if ($filterType === 'year' && $year) {
            // Chỉ 1 cột cho cả năm
            $yearlyRevenue = (clone $baseOrderQuery)
                ->where('status', 4)
                ->sum('total_price');

            $yearlyOrders = (clone $baseOrderQuery)
                ->where('status', 4)
                ->count();

            $chartLabels[] = 'Năm ' . $year;
            $chartDataRevenue[] = $yearlyRevenue;
            $chartDataOrders[] = $yearlyOrders;
        } else {
            // Theo tháng
            $monthlyRevenue = (clone $baseOrderQuery)
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('status', 1)->where('payment_method', 'vnpay');
                    })->orWhere(function ($q) {
                        $q->where('status', 9)->where('payment_method', 'cod');
                    })->orWhere('status', 4);
                })
                ->selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $monthlyOrders = (clone $baseOrderQuery)
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('status', 1)->where('payment_method', 'vnpay');
                    })->orWhere(function ($q) {
                        $q->where('status', 9)->where('payment_method', 'cod');
                    })->orWhere('status', 4);
                })
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total_orders')
                ->groupBy('month')
                ->pluck('total_orders', 'month')
                ->toArray();


            for ($i = 1; $i <= 12; $i++) {
                $chartLabels[] = 'Tháng ' . $i;
                $chartDataRevenue[] = $monthlyRevenue[$i] ?? 0;
                $chartDataOrders[] = $monthlyOrders[$i] ?? 0;
            }
        }

        // Sản phẩm bán chạy
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
                DB::raw("COALESCE(pv.image, p.image) as image"), // ưu tiên ảnh biến thể
                'pv.sku as variant_name',
                DB::raw("GROUP_CONCAT(DISTINCT CONCAT(a.name, ': ', av.value) ORDER BY a.name SEPARATOR ', ') as variant_attributes"),
                DB::raw('SUM(od.quantity) as total_quantity'),
                DB::raw('MAX(od.price) as latest_price')
            )
            // ->groupBy('p.id', 'p.product_name', 'pv.id', 'pv.sku', 'pv.image') // thêm pv.id, pv.image để tránh lỗi SQL
            ->groupBy('p.id', 'p.product_name', 'pv.id', 'pv.sku', 'pv.image', 'p.image')

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
        // Thống kê đơn hàng theo trạng thái (ví dụ: 1=Chờ xử lý, 2=Đang giao, 4=Hoàn thành, 6=Đã hủy)
        $orderStatusCount = [
            'chưa xác nhận ' => (clone $baseOrderQuery)->where('status', 1)->count(),
            'Đang giao' => (clone $baseOrderQuery)->where('status', 3)->count(),
            'Hoàn thành' => (clone $baseOrderQuery)->where('status', 4)->count(),
            'Đã hủy' => (clone $baseOrderQuery)->where('status', 6)->count(),
        ];


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
            'chartDataRevenue',
            'chartDataOrders',
            'filterType',
            'fromDate',
            'toDate',
            'month',
            'year',
            'outOfStockProducts',
            'outOfStockVariants',
            'totalOutOfStock',
            'orderStatusCount'
        ));
    }
}
