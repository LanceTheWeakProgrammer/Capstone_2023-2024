<?php

namespace Database\Seeders;

use App\Models\User; 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create([
            'username' => 'admin',
            'password' => Hash::make('P@ssword'), 
            'role' => 'admin',
            'is_active' => true,
            'status' => 'active'
        ]);

    }
}
