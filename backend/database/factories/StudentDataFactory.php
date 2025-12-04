<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentData>
 */
class StudentDataFactory extends Factory
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
            'address' => fake()->streetAddress() . ", " . fake()->city() . ", " . fake()->postcode(),
            'personal_email' => fake()->safeEmail(),
            'study_field' => fake()->randomElement(["AI22m", "AI22b"]),
        ];
    }
}
