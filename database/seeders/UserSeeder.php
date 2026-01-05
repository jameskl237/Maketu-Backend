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
            $email = "supplier$i@example.com";
            $user = User::where('email', $email)->first();
            
            if ($user) {
                // Mettre à jour le mot de passe si l'utilisateur existe déjà
                $user->update([
                    'password' => Hash::make('password'),
                ]);
            } else {
                // Créer un nouvel utilisateur
                User::create([
                    'name' => "Fournisseur $i",
                    'email' => $email,
                    'role' => 'supplier',
                    'password' => Hash::make('password'),
                    'username' => "supplier$i",
                    'phone' => '237695988879',
                    'address' => "Adresse $i",
                ]);
            }
        }
    }
}

