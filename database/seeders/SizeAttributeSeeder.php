<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;

class SizeAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = ['100g', '300g', '500g', '1kg'];

        // Tìm thuộc tính 'Size' nếu có, đổi tên thành 'Khối lượng'
        $sizeAttr = Attribute::where('name', 'Size')->first();

        if ($sizeAttr) {
            $sizeAttr->name = 'Khối lượng';
            $sizeAttr->save();
        } else {
            $sizeAttr = Attribute::firstOrCreate(['name' => 'Khối lượng']);
        }

        foreach ($sizes as $size) {
            AttributeValue::firstOrCreate([
                'attribute_id' => $sizeAttr->id,
                'value' => $size,
            ]);
        }
    }
}
