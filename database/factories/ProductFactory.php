<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category; // importe o model Category

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => $this->faker->word(),
            'price' => (float) $this->faker->numberBetween(200, 1000),
            'category_id' => Category::factory(),
        ];
    }
}
