<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shop>
 */
use App\Models\User;

class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'description' => $this->faker->paragraph,
            'user_id' => User::factory(),
            'city' => $this->faker->city,
            'district' => $this->faker->streetName,
            'phone' => '237' . $this->faker->numerify('##########'),
        ];
    }
}
