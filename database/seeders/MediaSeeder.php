<?php

namespace Database\Seeders;

// database/seeders/MediaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Media;
use App\Models\Product;

class MediaSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();

        foreach ($products as $product) {
            // 1 image principale
            Media::create([
                'url' => 'products/default.jpg',
                'type' => 'image',
                'is_principal' => true,
                'product_id' => $product->id,
            ]);

            // + 1-2 images supplémentaires
            for ($i = 1; $i <= rand(1, 2); $i++) {
                Media::create([
                    'url' => 'products/image' . rand(1, 3) . '.jpg',
                    'type' => 'image',
                    'is_principal' => false,
                    'product_id' => $product->id,
                ]);
            }

            // + 0-1 vidéos
            if (rand(0, 1)) {
                Media::create([
                    'url' => 'products/video' . rand(1, 2) . '.mp4',
                    'type' => 'video',
                    'is_principal' => false,
                    'product_id' => $product->id,
                ]);
            }
        }
    }
}
