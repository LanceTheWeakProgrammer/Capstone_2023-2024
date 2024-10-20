<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $types = [
            'Sedan', 'SUV', 'Truck', 'Electric', 'Hybrid', 'Sports Car', 'Luxury', 'Off-Road', 'Van'
        ];

        $IDs = [];
        foreach ($types as $type) {
            $IDs[$type] = DB::table('vehicle_types')->insertGetId([
                'type' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $vehicles = [
            // Sedans
            ['make' => 'Toyota', 'model' => 'Camry', 'type' => 'Sedan'],
            ['make' => 'Honda', 'model' => 'Civic', 'type' => 'Sedan'],
            ['make' => 'Nissan', 'model' => 'Altima', 'type' => 'Sedan'],
            ['make' => 'Ford', 'model' => 'Fusion', 'type' => 'Sedan'],
            ['make' => 'Chevrolet', 'model' => 'Malibu', 'type' => 'Sedan'],
            ['make' => 'Hyundai', 'model' => 'Sonata', 'type' => 'Sedan'],
            ['make' => 'Kia', 'model' => 'Optima', 'type' => 'Sedan'],
            ['make' => 'Mazda', 'model' => 'Mazda3', 'type' => 'Sedan'],
            ['make' => 'Volkswagen', 'model' => 'Passat', 'type' => 'Sedan'],
            ['make' => 'Subaru', 'model' => 'Legacy', 'type' => 'Sedan'],

            // SUVs
            ['make' => 'Toyota', 'model' => 'RAV4', 'type' => 'SUV'],
            ['make' => 'Honda', 'model' => 'CR-V', 'type' => 'SUV'],
            ['make' => 'Ford', 'model' => 'Explorer', 'type' => 'SUV'],
            ['make' => 'Chevrolet', 'model' => 'Tahoe', 'type' => 'SUV'],
            ['make' => 'Nissan', 'model' => 'Rogue', 'type' => 'SUV'],
            ['make' => 'Jeep', 'model' => 'Grand Cherokee', 'type' => 'SUV'],
            ['make' => 'BMW', 'model' => 'X5', 'type' => 'SUV'],
            ['make' => 'Mercedes-Benz', 'model' => 'GLC', 'type' => 'SUV'],
            ['make' => 'Hyundai', 'model' => 'Santa Fe', 'type' => 'SUV'],
            ['make' => 'Kia', 'model' => 'Sorento', 'type' => 'SUV'],

            // Trucks
            ['make' => 'Ford', 'model' => 'F-150', 'type' => 'Truck'],
            ['make' => 'Chevrolet', 'model' => 'Silverado', 'type' => 'Truck'],
            ['make' => 'Ram', 'model' => '1500', 'type' => 'Truck'],
            ['make' => 'Toyota', 'model' => 'Tacoma', 'type' => 'Truck'],
            ['make' => 'Nissan', 'model' => 'Frontier', 'type' => 'Truck'],
            ['make' => 'GMC', 'model' => 'Sierra', 'type' => 'Truck'],
            ['make' => 'Ford', 'model' => 'Ranger', 'type' => 'Truck'],
            ['make' => 'Chevrolet', 'model' => 'Colorado', 'type' => 'Truck'],
            ['make' => 'Honda', 'model' => 'Ridgeline', 'type' => 'Truck'],
            ['make' => 'Ram', 'model' => '2500', 'type' => 'Truck'],

            // Electric
            ['make' => 'Tesla', 'model' => 'Model S', 'type' => 'Electric'],
            ['make' => 'Tesla', 'model' => 'Model 3', 'type' => 'Electric'],
            ['make' => 'Nissan', 'model' => 'Leaf', 'type' => 'Electric'],
            ['make' => 'Chevrolet', 'model' => 'Bolt EV', 'type' => 'Electric'],
            ['make' => 'BMW', 'model' => 'i3', 'type' => 'Electric'],
            ['make' => 'Audi', 'model' => 'e-tron', 'type' => 'Electric'],
            ['make' => 'Hyundai', 'model' => 'Kona Electric', 'type' => 'Electric'],
            ['make' => 'Kia', 'model' => 'Soul EV', 'type' => 'Electric'],
            ['make' => 'Jaguar', 'model' => 'I-Pace', 'type' => 'Electric'],
            ['make' => 'Porsche', 'model' => 'Taycan', 'type' => 'Electric'],

            // Hybrid
            ['make' => 'Toyota', 'model' => 'Prius', 'type' => 'Hybrid'],
            ['make' => 'Honda', 'model' => 'Accord Hybrid', 'type' => 'Hybrid'],
            ['make' => 'Ford', 'model' => 'Fusion Hybrid', 'type' => 'Hybrid'],
            ['make' => 'Hyundai', 'model' => 'Ioniq Hybrid', 'type' => 'Hybrid'],
            ['make' => 'Kia', 'model' => 'Niro Hybrid', 'type' => 'Hybrid'],
            ['make' => 'Lexus', 'model' => 'RX Hybrid', 'type' => 'Hybrid'],
            ['make' => 'Toyota', 'model' => 'RAV4 Hybrid', 'type' => 'Hybrid'],
            ['make' => 'Honda', 'model' => 'Insight', 'type' => 'Hybrid'],
            ['make' => 'Chevrolet', 'model' => 'Volt', 'type' => 'Hybrid'],
            ['make' => 'BMW', 'model' => '330e', 'type' => 'Hybrid'],

            // Sports Car
            ['make' => 'Porsche', 'model' => '911', 'type' => 'Sports Car'],
            ['make' => 'Ford', 'model' => 'Mustang', 'type' => 'Sports Car'],
            ['make' => 'Chevrolet', 'model' => 'Camaro', 'type' => 'Sports Car'],
            ['make' => 'Nissan', 'model' => 'GT-R', 'type' => 'Sports Car'],
            ['make' => 'Dodge', 'model' => 'Challenger', 'type' => 'Sports Car'],
            ['make' => 'BMW', 'model' => 'M4', 'type' => 'Sports Car'],
            ['make' => 'Audi', 'model' => 'R8', 'type' => 'Sports Car'],
            ['make' => 'Jaguar', 'model' => 'F-Type', 'type' => 'Sports Car'],
            ['make' => 'Mercedes-Benz', 'model' => 'AMG GT', 'type' => 'Sports Car'],
            ['make' => 'Toyota', 'model' => 'Supra', 'type' => 'Sports Car'],

            // Luxury
            ['make' => 'BMW', 'model' => '7 Series', 'type' => 'Luxury'],
            ['make' => 'Mercedes-Benz', 'model' => 'S-Class', 'type' => 'Luxury'],
            ['make' => 'Audi', 'model' => 'A8', 'type' => 'Luxury'],
            ['make' => 'Lexus', 'model' => 'LS', 'type' => 'Luxury'],
            ['make' => 'Jaguar', 'model' => 'XJ', 'type' => 'Luxury'],
            ['make' => 'Cadillac', 'model' => 'CT6', 'type' => 'Luxury'],
            ['make' => 'Genesis', 'model' => 'G90', 'type' => 'Luxury'],
            ['make' => 'Volvo', 'model' => 'S90', 'type' => 'Luxury'],
            ['make' => 'Porsche', 'model' => 'Panamera', 'type' => 'Luxury'],
            ['make' => 'Maserati', 'model' => 'Quattroporte', 'type' => 'Luxury'],

            // Off-Road
            ['make' => 'Jeep', 'model' => 'Wrangler', 'type' => 'Off-Road'],
            ['make' => 'Toyota', 'model' => '4Runner', 'type' => 'Off-Road'],
            ['make' => 'Land Rover', 'model' => 'Defender', 'type' => 'Off-Road'],
            ['make' => 'Ford', 'model' => 'Bronco', 'type' => 'Off-Road'],
            ['make' => 'Chevrolet', 'model' => 'Colorado ZR2', 'type' => 'Off-Road'],
            ['make' => 'Nissan', 'model' => 'Xterra', 'type' => 'Off-Road'],
            ['make' => 'Ram', 'model' => 'Rebel', 'type' => 'Off-Road'],
            ['make' => 'Mercedes-Benz', 'model' => 'G-Class', 'type' => 'Off-Road'],
            ['make' => 'Toyota', 'model' => 'Land Cruiser', 'type' => 'Off-Road'],
            ['make' => 'Jeep', 'model' => 'Gladiator', 'type' => 'Off-Road'],

            // Van
            ['make' => 'Honda', 'model' => 'Odyssey', 'type' => 'Van'],
            ['make' => 'Toyota', 'model' => 'Sienna', 'type' => 'Van'],
            ['make' => 'Chrysler', 'model' => 'Pacifica', 'type' => 'Van'],
            ['make' => 'Kia', 'model' => 'Sedona', 'type' => 'Van'],
            ['make' => 'Ford', 'model' => 'Transit', 'type' => 'Van'],
            ['make' => 'Mercedes-Benz', 'model' => 'Sprinter', 'type' => 'Van'],
            ['make' => 'Ram', 'model' => 'ProMaster', 'type' => 'Van'],
            ['make' => 'Nissan', 'model' => 'NV200', 'type' => 'Van'],
            ['make' => 'Chevrolet', 'model' => 'Express', 'type' => 'Van'],
            ['make' => 'GMC', 'model' => 'Savana', 'type' => 'Van']
        ];

        foreach ($vehicles as $vehicle) {
            DB::table('vehicle_details')->insert([
                'make' => $vehicle['make'],
                'model' => $vehicle['model'],
                'vehicle_type_id' => $IDs[$vehicle['type']],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
