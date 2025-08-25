<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class StatsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Fake dữ liệu doanh thu theo tháng
        foreach (range(1, 12) as $month) {
            DB::table('revenues')->insert([
                'month' => $month,
                'year' => 2025,
                'revenue' => $faker->numberBetween(80000000, 200000000),
                'orders' => $faker->numberBetween(200, 500),
                'new_customers' => $faker->numberBetween(50, 150),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Fake dữ liệu sản phẩm bán chạy
        $products = ['Trà sữa trân châu', 'Khô gà lá chanh', 'Bánh tráng trộn', 'Nước ép trái cây', 'Snack rong biển'];
        foreach ($products as $product) {
            DB::table('product_stats')->insert([
                'product_name' => $product,
                'quantity_sold' => $faker->numberBetween(500, 1500),
                'revenue' => $faker->numberBetween(20000000, 60000000),
                'percentage' => $faker->randomFloat(2, 5, 30), // % bán ra
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
