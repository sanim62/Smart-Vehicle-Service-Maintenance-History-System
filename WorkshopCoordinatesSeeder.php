<?php

namespace Database\Seeders;

use App\Models\Workshop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WorkshopCoordinatesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or update workshop owners & workshops
        $workshopUser1 = User::firstOrCreate(
            ['email' => 'workshop1@vehicle.com'],
            [
                'name' => 'Metro Auto Repair',
                'password' => Hash::make('password'),
                'role' => 'workshop',
                'phone' => '01711223344',
            ]
        );

        $w1 = Workshop::updateOrCreate(
            ['user_id' => $workshopUser1->id],
            [
                'name' => 'Metro Auto Repair Center',
                'owner_name' => 'Metro Auto Repair',
                'phone' => '01711223344',
                'email' => 'metro@auto.com',
                'address' => 'Mirpur 10 Circle, Dhaka',
                'city' => 'Dhaka',
                'latitude' => 23.8069,
                'longitude' => 90.3687,
                'service_categories' => json_encode(['oil_change', 'tire_service', 'brake_service']),
                'status' => 'active',
                'description' => 'Top rated mirpur auto repair and diagnostics center.',
            ]
        );

        $workshopUser2 = User::firstOrCreate(
            ['email' => 'workshop2@vehicle.com'],
            [
                'name' => 'Apex Engine Works',
                'password' => Hash::make('password'),
                'role' => 'workshop',
                'phone' => '01755667788',
            ]
        );

        $w2 = Workshop::updateOrCreate(
            ['user_id' => $workshopUser2->id],
            [
                'name' => 'Apex Engine Specialists',
                'owner_name' => 'Apex Engine Works',
                'phone' => '01755667788',
                'email' => 'apex@engines.com',
                'address' => 'Gulshan 2, Dhaka',
                'city' => 'Dhaka',
                'latitude' => 23.7925,
                'longitude' => 90.4178,
                'service_categories' => json_encode(['engine_repair', 'transmission', 'ac_service']),
                'status' => 'active',
                'description' => 'Premium engine overhaul and repair mechanics in Gulshan.',
            ]
        );

        $workshopUser3 = User::firstOrCreate(
            ['email' => 'workshop3@vehicle.com'],
            [
                'name' => 'Dhanmondi Care',
                'password' => Hash::make('password'),
                'role' => 'workshop',
                'phone' => '01799001122',
            ]
        );

        $w3 = Workshop::updateOrCreate(
            ['user_id' => $workshopUser3->id],
            [
                'name' => 'Dhanmondi Car Care & Paint',
                'owner_name' => 'Dhanmondi Care',
                'phone' => '01799001122',
                'email' => 'dhanmondi@care.com',
                'address' => 'Road 27, Dhanmondi, Dhaka',
                'city' => 'Dhaka',
                'latitude' => 23.7542,
                'longitude' => 90.3754,
                'service_categories' => json_encode(['body_work', 'general_service', 'electrical']),
                'status' => 'active',
                'description' => 'Fast general vehicle maintenance and custom car painting.',
            ]
        );
    }
}
