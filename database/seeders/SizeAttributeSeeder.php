<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;

class SizeAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = ['S', 'M', 'L', 'XL',];

        // Tạo thuộc tính 'Size' nếu chưa có
        $sizeAttr = Attribute::firstOrCreate(['name' => 'Size']);

        foreach ($sizes as $size) {
            AttributeValue::firstOrCreate([
                'attribute_id' => $sizeAttr->id,
                'value' => $size,
            ]);
        }
    }
}
