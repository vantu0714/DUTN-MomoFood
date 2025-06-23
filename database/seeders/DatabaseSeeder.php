<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Bước 1: Tạo Role trước
        $this->call(RoleSeeder::class);

        // Bước 2: Tạo User sau khi có role

        // Bước 3: Các seed khác
        \App\Models\Category::factory(5)->create();
        $this->call(ProductSeeder::class);
        $this->call(OrderSeeder::class);
        $this->call([
            AttributeSeeder::class,
            AttributeValueSeeder::class,
            SizeAttributeSeeder::class,
        ]);
    }
}
