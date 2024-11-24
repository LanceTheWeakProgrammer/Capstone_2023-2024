<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user creation
        User::create([
            'username' => 'admin',
            'password' => Hash::make('P@ssword'),
            'role' => 'admin',
            'is_active' => true,
            'status' => 'active',
            'is_verified' => true,
            'logged_in_at' => now(), 
        ]);

        // // Regular user creation
        // $user = User::create([
        //     'email' => 'user@example.com',
        //     'password' => Hash::make('P@ssword'),
        //     'role' => 'user',
        //     'is_active' => true,
        //     'status' => 'active',
        //     'is_verified' => true,
        //     'logged_in_at' => now(),
        // ]);

        // // Create the user profile for the regular user
        // DB::table('user_profiles')->insert([
        //     'user_id' => $user->id,
        //     'full_name' => 'Suzie Harper',
        //     'phone_number' => '987654321',
        //     'address' => '123 Example St',
        //     'city' => 'Sample City',
        //     'state' => 'Example State',
        //     'country' => 'Sample Country',
        //     'zip_code' => '12345',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // // Technician user creation
        // $technician = User::create([
        //     'account_number' => '101234567',
        //     'password' => Hash::make('P@ssword'),
        //     'role' => 'technician',
        //     'is_active' => true,
        //     'status' => 'active',
        //     'is_verified' => true,
        //     'logged_in_at' => now(),
        // ]);

        // // Create technician profile
        // $technicianProfile = DB::table('technician_profiles')->insertGetId([
        //     'user_id' => $technician->id,
        //     'full_name' => 'John Technician',
        //     'phone_number' => '123456789',
        //     'year_experience' => '5',
        //     'avail_status' => 1,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // // Insert vehicle mastery for the technician
        // DB::table('vehicle_mastery')->insert([
        //     ['technician_id' => $technicianProfile, 'vehicle_type_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['technician_id' => $technicianProfile, 'vehicle_type_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        //     ['technician_id' => $technicianProfile, 'vehicle_type_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        //     ['technician_id' => $technicianProfile, 'vehicle_type_id' => 4, 'created_at' => now(), 'updated_at' => now()],
        // ]);

        // // Insert services offered by the technician
        // DB::table('service_offered')->insert([
        //     ['technician_id' => $technicianProfile, 'service_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['technician_id' => $technicianProfile, 'service_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        //     ['technician_id' => $technicianProfile, 'service_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        //     ['technician_id' => $technicianProfile, 'service_id' => 4, 'created_at' => now(), 'updated_at' => now()],
        // ]);
    }
}
