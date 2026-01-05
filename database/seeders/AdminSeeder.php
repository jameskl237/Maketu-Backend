<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists to avoid duplicates
        $existingAdmin = User::where('email', 'james@example.com')->first();

        if (!$existingAdmin) {
            User::create([
                'name' => 'Admin User',
                'username' => 'admin',
                'email' => 'james@example.com',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
                'phone' => '695988879',
                'address' => 'Admin Address',
            ]);

            $this->command->info('Admin user created successfully.');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}
