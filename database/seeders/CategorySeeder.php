<?php

// database/seeders/CategorySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Électronique',
            'Vêtements',
            'Alimentation',
            'Maison',
            'Sport',
            'Livres',
            'Beauté',
            'Jeux vidéo',
        ];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "Catégorie des produits $name",
                'image' => 'categories/default.jpg',
            ]);
        }
    }
}
