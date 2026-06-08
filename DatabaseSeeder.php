<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin user
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@vehicle.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '01700000000',
        ]);

        // Create a sample owner
        User::create([
            'name'     => 'John Owner',
            'email'    => 'owner@vehicle.com',
            'password' => Hash::make('password'),
            'role'     => 'owner',
            'phone'    => '01711111111',
        ]);

        // Create a workshop user
        User::create([
            'name'     => 'Workshop Manager',
            'email'    => 'workshop@vehicle.com',
            'password' => Hash::make('password'),
            'role'     => 'workshop',
            'phone'    => '01722222222',
        ]);

        // Run SQL setup (triggers + views)
        $this->call(SqlSetupSeeder::class);
    }
}
