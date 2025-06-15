<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;

class AttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy ID các thuộc tính
        $vi = Attribute::where('name', 'Vị')->first()->id;
        $khoiLuong = Attribute::where('name', 'Khối lượng')->first()->id;
        $doCay = Attribute::where('name', 'Độ cay')->first()->id;
        $baoBi = Attribute::where('name', 'Bao bì')->first()->id;

        $values = [
            // Vị
            ['attribute_id' => $vi, 'value' => 'Cay'],
            ['attribute_id' => $vi, 'value' => 'Ngọt'],
            ['attribute_id' => $vi, 'value' => 'Mặn'],

            // Khối lượng
            ['attribute_id' => $khoiLuong, 'value' => '100g'],
            ['attribute_id' => $khoiLuong, 'value' => '250g'],
            ['attribute_id' => $khoiLuong, 'value' => '500g'],

            // Độ cay
            ['attribute_id' => $doCay, 'value' => 'Không cay'],
            ['attribute_id' => $doCay, 'value' => 'Vừa cay'],
            ['attribute_id' => $doCay, 'value' => 'Rất cay'],

            // Bao bì
            ['attribute_id' => $baoBi, 'value' => 'Gói giấy'],
            ['attribute_id' => $baoBi, 'value' => 'Hộp nhựa'],
        ];

        AttributeValue::insert($values);
    }
}
