<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            ['name' => 'Vị'],
            ['name' => 'Khối lượng'],
            ['name' => 'Độ cay'],
            ['name' => 'Bao bì'],
        ];

        Attribute::insert($attributes);
    }
}
