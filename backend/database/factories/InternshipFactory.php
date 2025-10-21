<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Internship>
 */
class InternshipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 0,
            'company_id' => 0,
            'start' => fake()->dateTime(),
            'end' => fake()->dateTime("+30 days"),
            'year_of_study' => fake()->randomElement([1, 2, 3, 4, 5]),
            'semester' => fake()->randomElement(["WINTER", "SUMMER"]),
            'position_description' => fake()->jobTitle(),
            'agreement' => null,
        ];
    }
}
