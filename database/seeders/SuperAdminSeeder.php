<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'role' => 'superadmin',
            'name' => 'Mr. Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('superadmin@123'),
            'status' => 'active',
            'phone' => '1234567890',
        ]);

        $user->assignRole('super_admin');
    }
}
