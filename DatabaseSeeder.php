<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin user (firstOrCreate = safe to run multiple times)
        User::firstOrCreate(
            ['email' => 'admin@vehicle.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('123456789'),
                'role'     => 'admin',
                'phone'    => '01700000000',
            ]
        );

        // Create a sample owner
        User::firstOrCreate(
            ['email' => 'owner@vehicle.com'],
            [
                'name'     => 'John Owner',
                'password' => Hash::make('password'),
                'role'     => 'owner',
                'phone'    => '01711111111',
            ]
        );

        // Create a workshop user
        User::firstOrCreate(
            ['email' => 'workshop@vehicle.com'],
            [
                'name'     => 'Workshop Manager',
                'password' => Hash::make('password'),
                'role'     => 'workshop',
                'phone'    => '01722222222',
            ]
        );

        // Run SQL setup (view creation)
        $this->call(SqlSetupSeeder::class);
    }
}
