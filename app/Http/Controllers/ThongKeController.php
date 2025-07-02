<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ThongKeController extends Controller
{
    public function index(Request $request)
    {
        // Tổng số sản phẩm
        $tongSanPham = Product::count();

        // Tổng số người dùng
        $tongNguoiDung = User::count();

        // Tổng số đơn hàng
        $tongDonHang = Order::count();

        // Doanh thu theo bộ lọc
        $query = Order::where('status', 3); // 3 = đơn đã giao

        if ($request->filled('ngay')) {
            $query->whereDate('created_at', $request->ngay);
        } else {
            if ($request->filled('thang')) {
                $query->whereMonth('created_at', $request->thang);
            }
            if ($request->filled('nam')) {
                $query->whereYear('created_at', $request->nam);
            }
        }

        $tongDoanhThu = $query->sum('total_price');

        // Sản phẩm bán chạy
        // Tạm bỏ where status để test xem có dữ liệu không
        $sanPhamBanChay = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('orders.status', 3) // Chỉ đơn đã giao
            ->select(
                'products.product_name',
                DB::raw('SUM(order_details.quantity) as so_luong_ban')
            )
            ->groupBy('products.id', 'products.product_name')
            ->orderByDesc('so_luong_ban')
            ->take(5)
            ->get();
        // Biểu đồ doanh thu theo tháng trong năm
        $year = $request->filled('nam') ? $request->nam : now()->year;
        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = "Tháng $i";
            $doanhThu = Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $i)
                ->where('status', 3)
                ->sum('total_price');

            $data[] = $doanhThu;
        }

        return view('admin.thongke.index', [
            'tongSanPham' => $tongSanPham,
            'tongNguoiDung' => $tongNguoiDung,
            'tongDonHang' => $tongDonHang,
            'tongDoanhThu' => $tongDoanhThu,
            'sanPhamBanChay' => $sanPhamBanChay,
            'ngay' => $request->ngay,
            'thang' => $request->thang,
            'nam' => $request->nam,
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
