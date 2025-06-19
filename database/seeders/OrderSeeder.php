<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Tạo user mẫu nếu chưa có
        $userId = DB::table('users')->insertGetId([
            'name' => 'Nguyễn Văn A',
            'email' => 'a@example.com',
            'phone' => '0987654321',
            'address' => '123 Đường ABC, Quận 1, TP.HCM',
            'password' => bcrypt('123456'),
            'role_id' => 2, // giả sử role_id = 2 là user
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Tạo khuyến mãi
        $promotionCode = 'SUMMER10';
        DB::table('promotions')->insert([
            'promotion_name' => $promotionCode,
            'discount_type' => 'percent',
            'discount_value' => 10,
            'max_discount_value' => 30000,
            'start_date' => $now->copy()->subDays(5),
            'end_date' => $now->copy()->addDays(5),
            'description' => 'Giảm 10% tối đa 30k',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Tạo 2 product_variant mẫu
        $variant1 = DB::table('product_variants')->insertGetId([
            'product_id' => 1, // bạn cần có sản phẩm ID 1
            'price' => 120000,
            'quantity_in_stock' => 50,
            'sku' => 'SP001-A',
            'status' => 1,
            'image' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $variant2 = DB::table('product_variants')->insertGetId([
            'product_id' => 1, 
            'price' => 60000,
            'quantity_in_stock' => 30,
            'sku' => 'SP001-B',
            'status' => 1,
            'image' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Tạo đơn hàng
        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $userId,
            'recipient_name' => 'Nguyễn Văn A',
            'recipient_phone' => '0987654321',
            'recipient_address' => '123 Đường ABC, Quận 1, TP.HCM',
            'promotion' => $promotionCode,
            'shipping_fee' => 20000,
            'total_price' => 320000,
            'payment_method' => 'COD',
            'payment_status' => 'unpaid',
            'status' => 'pending',
            'note' => 'Giao giờ hành chính',
            'cancellation_reason' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Chi tiết đơn hàng
        DB::table('order_details')->insert([
            [
                'order_id' => $orderId,
                'product_variant_id' => $variant1,
                'quantity' => 2,
                'price' => 120000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'order_id' => $orderId,
                'product_variant_id' => $variant2,
                'quantity' => 1,
                'price' => 60000,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);

        // Giao dịch thanh toán mẫu (giả định dùng VNPAY)
        DB::table('vn_pay_transactions')->insert([
            'order_id' => $orderId,
            'amount' => 320000,
            'trans_id' => 'VNP20250612001',
            'status' => 'success',
            'payment_method' => 'VNPAY',
            'response_data' => json_encode(['code' => '00', 'message' => 'Thành công']),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
