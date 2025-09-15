<?php

// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Fournisseur $i",
                'email' => "supplier$i@example.com",
                'role' => 'supplier',
                'password' => Hash::make('password'),
                'username' => "supplier$i",
                'phone' => '6' . rand(50000000, 99999999),
                'address' => "Adresse $i",
            ]);
        }
    }
}

