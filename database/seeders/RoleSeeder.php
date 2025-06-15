<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            ['id' => 1, 'name' => 'admin', 'description' => 'Quản trị viên'],
            ['id' => 2, 'name' => 'user', 'description' => 'Khách hàng'],
        ]);
    }
}
