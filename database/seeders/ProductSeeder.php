<?php

namespace Database\Seeders;

// database/seeders/ProductSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $shops = Shop::all();
        $categories = Category::all();

        foreach ($shops as $shop) {
            for ($i = 1; $i <= 5; $i++) {
                Product::create([
                    'code' => $this->generateUniqueCode(),
                    'name' => "Produit $i de {$shop->name}",
                    'description' => 'Produit de qualité.',
                    'long_description' => 'Ceci est une description longue du produit.',
                    'price' => rand(1000, 50000),
                    'promotion_price' => rand(800, 40000),
                    'in_stock' => rand(0, 1),
                    'quantity' => rand(1, 20),
                    'origin' => ['local', 'imported'][rand(0, 1)],
                    'category_id' => $categories->random()->id,
                    'shop_id' => $shop->id,
                    'user_id' => $shop->user_id,
                ]);
            }
        }
    }

    private function generateUniqueCode()
    {
        do {
            $code = Str::upper(Str::random(10));
        } while (Product::where('code', $code)->exists());

        return $code;
    }
}
