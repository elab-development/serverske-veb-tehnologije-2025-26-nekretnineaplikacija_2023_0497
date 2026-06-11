<?php
namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InquiryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'property_id' => Property::factory(),
            'message'     => $this->faker->paragraph(),
            'status'      => $this->faker->randomElement(['pending', 'answered', 'closed']),
        ];
    }
}