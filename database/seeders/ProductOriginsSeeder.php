<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductOriginsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $origins = [
            'Nhật Bản',
            'Úc',
            'Trung Quốc',
            'Hàn Quốc',
            'Châu Âu',
            'Indonesia',
            'Malaysia',
            'Khác',
        ];

        foreach ($origins as $origin) {
            DB::table('product_origins')->updateOrInsert(
                ['name' => $origin],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
