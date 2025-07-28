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
    {  // Xóa toàn bộ dữ liệu cũ trong bảng và reset ID tự tăng
        DB::table('product_origins')->delete();
        $origins = [
            'Nhật Bản',
            'Úc',
            'Trung Quốc',
            'Hàn Quốc',
            'Châu Âu',
            'Indonesia',
            'Malaysia',
            'Việt Nam',
        ];

        foreach ($origins as $origin) {
            DB::table('product_origins')->updateOrInsert(
                ['name' => $origin],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
