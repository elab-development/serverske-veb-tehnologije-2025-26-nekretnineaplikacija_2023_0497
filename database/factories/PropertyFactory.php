<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'title'       => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'price'       => $this->faker->randomFloat(2, 50000, 500000),
            'location'    => $this->faker->city(),
            'type'        => $this->faker->randomElement(['apartment', 'house', 'land', 'commercial']),
            'bedrooms'    => $this->faker->numberBetween(1, 5),
            'bathrooms'   => $this->faker->numberBetween(1, 3),
            'area_sqm'    => $this->faker->randomFloat(2, 30, 300),
            'status'      => $this->faker->randomElement(['available', 'sold', 'rented']),
        ];
    }
}