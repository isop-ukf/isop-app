<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'address' => fake()->streetAddress() . ", " . fake()->city() . ", " . fake()->postcode(),
            'ico' => fake()->numberBetween(111111, 999999),
            'contact' => 0,
            'hiring' => fake()->boolean(),
        ];
    }
}
