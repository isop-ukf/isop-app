<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InternshipStatusData>
 */
class InternshipStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'internship_id' => 0,
            'status' => fake()->randomElement(["SUBMITTED", "CONFIRMED", "DENIED", "DEFENDED", "NOT_DEFENDED"]),
            'changed' => fake()->dateTime(),
            'note' => null,
            'modified_by' => 0,
        ];
    }
}
