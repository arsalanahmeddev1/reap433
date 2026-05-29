<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@reap433.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
            ]
        );
        $admin->assignRole('admin');
    }
}
