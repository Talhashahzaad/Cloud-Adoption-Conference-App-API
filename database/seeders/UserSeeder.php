<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        User::updateOrCreate(
            ['email' => 'superadmin@conference.com'],
            [
                'role_id' => $superAdminRole->id,
                'name' => 'Super Admin',
                'phone' => '9999999999',
                'company_name' => 'Conference System',
                'designation' => 'Super Admin',
                'city' => 'Noida',
                'state' => 'UP',
                'area_of_interest' => 'Management',
                'linkedin_url' => '',
                'remarks' => 'Development User',
                'password' => 'Password@123',
                'status' => 'approved',
                'email_verified_at' => now(),
            ]
        );
    }
}
