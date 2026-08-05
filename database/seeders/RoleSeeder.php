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
        Role::create([
            'name' => 'Super Admin',
            'status' => 'active',
        ]);

        Role::create([
            'name' => 'Admin',
            'status' => 'active',
        ]);

        Role::create([
            'name' => 'User',
            'status' => 'active',
        ]);
    }
}
