<?php

namespace Database\Factories;

use App\Enums\InternshipStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InternshipStatusData>
 */
class InternshipStatusDataFactory extends Factory
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
            'status' => fake()->randomElement(InternshipStatus::all()),
            'changed' => fake()->dateTime(),
            'note' => null,
            'modified_by' => 0,
        ];
    }
}
