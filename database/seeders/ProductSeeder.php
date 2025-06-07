<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        for ($i = 1; $i <= 50; $i++) {
            Product::create([
                'product_code' => 'P' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'product_name' => 'Sản phẩm ' . $i,
                'image' => 'https://via.placeholder.com/300x300?text=Product+' . $i,
                'description' => 'Mô tả sản phẩm ' . $i,
                'ingredients' => 'Thành phần sản phẩm ' . $i,
                'expiration_date' => now()->addMonths(rand(1, 12)),
                'original_price' => rand(10000, 100000),
                'discounted_price' => rand(5000, 90000),
                'status' => 1,
                'view' => rand(0, 1000),
                'is_show_home' => rand(0, 1),
                'category_id' => rand(1, 5), // đảm bảo bạn đã có ít nhất 5 category
            ]);
        }
    }
}
