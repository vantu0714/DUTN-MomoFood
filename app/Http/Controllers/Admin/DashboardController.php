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

        // Định nghĩa các hằng số status để dễ quản lý
        $CANCELLED_STATUSES = [6, 10, 11]; // Trạng thái hủy/không xác nhận
        $VALID_VNPAY_STATUSES = [1, 2, 3, 4, 5, 7, 9, 12]; // VNPAY hợp lệ
        $VALID_COD_STATUSES = [4, 5, 7, 9, 12]; // COD hợp lệ


        // Tổng số đơn hàng (THÊM LẠI DÒNG NÀY)
        $totalOrders = (clone $baseOrderQuery)->count();
        // Định nghĩa các hằng số status
        $CANCELLED_STATUSES = [6, 10, 11];
        $VALID_VNPAY_STATUSES = [1, 2, 3, 4, 5, 7, 9, 12];
        $VALID_COD_STATUSES = [4, 5, 7, 9, 12];

        // Định nghĩa các hằng số status
        $CANCELLED_STATUSES = [6, 10, 11];
        $VALID_VNPAY_STATUSES = [1, 2, 3, 4, 5, 7, 9, 12];
        $VALID_COD_STATUSES = [4, 5, 7, 9, 12];

        // Bước 1: Tính tổng doanh thu từ TẤT CẢ đơn hàng
        $allOrdersRevenue = DB::table('orders')
            ->whereIn('id', (clone $baseOrderQuery)->pluck('id'))
            ->sum('total_price') ?? 0;

        // Bước 2: Tính tổng tiền các đơn VNPay bị hủy/không xác nhận (CHỈ TRỪ 1 LẦN)
        $cancelledVNPayAmount = DB::table('orders')
            ->whereIn('id', (clone $baseOrderQuery)->pluck('id'))
            ->where('payment_method', 'vnpay')
            ->whereIn('status', [6, 10, 11]) // hủy/không xác nhận
            ->sum('total_price') ?? 0;

        // Bước 3: Tính tổng tiền các đơn COD bị hủy (KHÔNG TÍNH vì chưa thu tiền)
        $cancelledCODAmount = DB::table('orders')
            ->whereIn('id', (clone $baseOrderQuery)->pluck('id'))
            ->where('payment_method', 'cod')
            ->whereIn('status', [6, 10, 11])
            ->sum('total_price') ?? 0;

        // Bước 4: Tính tổng tiền hoàn hàng
        $returnedAmount = DB::table('order_return_items as ori')
            ->join('order_details as od', 'ori.order_detail_id', '=', 'od.id')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->whereIn('od.order_id', (clone $baseOrderQuery)->pluck('id'))
            ->where('ori.status', 'approved')
            ->whereIn('o.status', [4, 5, 7, 9, 12]) // chỉ đơn đã hoàn thành/giao thành công
            ->sum(DB::raw('ori.quantity * od.price')) ?? 0;

        // Bước 5: Doanh thu cuối = Tổng tất cả - Tiền đơn VNPay hủy - Tiền hoàn hàng
        $totalRevenue = $allOrdersRevenue - $cancelledVNPayAmount - $returnedAmount;
        // Tổng số sản phẩm đã bán (THÊM PHẦN NÀY)
        $totalProductsSold = OrderDetail::join('orders', 'orders.id', '=', 'order_details.order_id')
            ->whereIn('order_details.order_id', (clone $baseOrderQuery)->pluck('id'))
            ->whereNotIn('orders.status', [6, 10, 11]) // không tính đơn hủy
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('orders.payment_method', 'vnpay')
                        ->whereIn('orders.status', [1, 2, 3, 4, 5, 7, 9, 12]);
                })->orWhere(function ($q) {
                    $q->where('orders.payment_method', 'cod')
                        ->whereIn('orders.status', [4, 5, 7, 9, 12]);
                });
            })
            ->sum('order_details.quantity');

        // Debug: Hiển thị các giá trị để kiểm tra
        echo "Tổng tất cả: " . number_format($allOrdersRevenue) . "<br>";
        echo "Tiền VNPay hủy: " . number_format($cancelledVNPayAmount) . "<br>";
        echo "Tiền COD hủy: " . number_format($cancelledCODAmount) . "<br>";
        echo "Tiền hoàn hàng: " . number_format($returnedAmount) . "<br>";
        echo "Doanh thu cuối: " . number_format($totalRevenue) . "<br>";

        // Tổng số đơn hàng
        $totalOrders = (clone $baseOrderQuery)->count();

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
            ->withCount([
                'orders' => function ($q) use ($completedOrderIds) {
                    $q->whereIn('id', $completedOrderIds);
                }
            ])
            ->withSum([
                'orders' => function ($q) use ($completedOrderIds) {
                    $q->whereIn('id', $completedOrderIds);
                }
            ], 'total_price')
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
            'orderStatusCount',
        ));
    }
}
