<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $now = Carbon::now();

        $categories = [
            [
                'category_name' => 'Đồ ăn vặt truyền thống Việt Nam',
                'description' => 'Tổng hợp các món ăn vặt dân dã, đậm đà hương vị quê hương như bánh tráng, bánh gai, kẹo lạc, ô mai, v.v.',
                'status' => 1
            ],
            [
                'category_name' => 'Snack đóng gói',
                'description' => 'Các loại snack tiện lợi, giòn tan như khoai tây chiên, bắp rang, rong biển, phù hợp mọi lứa tuổi.',
                'status' => 1
            ],
            [
                'category_name' => 'Đồ uống giải khát',
                'description' => 'Nước ép trái cây, trà sữa, nước ngọt, nước detox giúp bạn giải nhiệt và tăng cường năng lượng.',
                'status' => 1
            ],
            [
                'category_name' => 'Trái cây',
                'description' => 'Các loại trái cây sấy, mứt trái cây, trái cây tươi phục vụ nhu cầu ăn vặt lành mạnh.',
                'status' => 1
            ],
            [
                'category_name' => 'Đồ ăn nhanh',
                'description' => 'Gà rán, hamburger, xúc xích, mì ăn liền,... dành cho những bữa ăn nhanh, tiện lợi.',
                'status' => 1
            ],
            [
                'category_name' => 'Đồ ăn vặt Hàn Quốc & Nhật Bản',
                'description' => 'Món ăn vặt nổi tiếng từ Hàn Quốc và Nhật Bản như tokbokki, bánh mochi, rong biển, bánh gạo cay.',
                'status' => 1
            ],
            [
                'category_name' => 'Hạt & đồ khô',
                'description' => 'Các loại hạt dinh dưỡng, hạt rang, khô gà, khô bò,... ngon miệng và tốt cho sức khỏe.',
                'status' => 1
            ],
        ];
        

        foreach ($categories as &$category) {
            $category['created_at'] = $now;
            $category['updated_at'] = $now;
        }

        DB::table('categories')->insert($categories);
    }
}
