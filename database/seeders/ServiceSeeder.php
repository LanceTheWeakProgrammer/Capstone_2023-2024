<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('service')->insert([
            [
                'name' => 'Oil Change',
                'icon' => 'images/services/ICO-2108923.svg',
                'fee' => 50.00,
                'description' => 'Comprehensive oil change service to keep your engine running smoothly.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tire Rotation',
                'icon' => 'images/services/ICO-2108923.svg',
                'fee' => 40.00,
                'description' => 'Ensure even tire wear and extend the life of your tires with our rotation service.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Brake Inspection',
                'icon' => 'images/services/ICO-2108923.svg',
                'fee' => 60.00,
                'description' => 'Detailed brake inspection to ensure your safety on the road.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Battery Replacement',
                'icon' => 'images/services/ICO-2108923.svg',
                'fee' => 120.00,
                'description' => 'Quick and reliable battery replacement to keep your vehicle powered.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wheel Alignment',
                'icon' => 'images/services/ICO-2108923.svg',
                'fee' => 80.00,
                'description' => 'Precision wheel alignment service to ensure optimal handling and tire wear.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Transmission Service',
                'icon' => 'images/services/ICO-2108923.svg',
                'fee' => 250.00,
                'description' => 'Complete transmission service to maintain smooth and efficient gear shifts.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AC Service',
                'icon' => 'images/services/ICO-2108923.svg',
                'fee' => 100.00,
                'description' => 'Keep your car cool with our comprehensive air conditioning service.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Engine Diagnostics',
                'icon' => 'engine-diagnostics-icon.png',
                'fee' => 90.00,
                'description' => 'Advanced engine diagnostics to identify and fix issues quickly.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fuel System Cleaning',
                'icon' => 'images/services/ICO-2108923.svg',
                'fee' => 110.00,
                'description' => 'Thorough fuel system cleaning to improve performance and fuel efficiency.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Detailing Service',
                'icon' => 'images/services/ICO-2108923.svg',
                'fee' => 150.00,
                'description' => 'Professional detailing service to make your car look brand new inside and out.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
