<?php

// database/seeders/ShopSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;
use App\Models\User;

class ShopSeeder extends Seeder
{
    public function run()
    {
        $cities = ['Yaoundé', 'Douala', 'Bafoussam'];
        $districts = ['Mokolo', 'Bonamoussadi', 'Ngousso', 'Akwa', 'Melen'];

        $suppliers = User::where('role', 'supplier')->get();

        foreach ($suppliers as $supplier) {
            for ($i = 1; $i <= 10; $i++) {
                Shop::create([
                    'name' => "Boutique {$i} de {$supplier->name}",
                    'description' => "Boutique spécialisée à {$cities[array_rand($cities)]}",
                    'city' => $cities[array_rand($cities)],
                    'district' => $districts[array_rand($districts)],
                    'phone' => '237695988879',
                    'user_id' => $supplier->id,
                ]);
            }
        }
    }
}

