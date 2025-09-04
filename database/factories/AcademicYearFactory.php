<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    protected $model = AcademicYear::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startYear = fake()->numberBetween(2020, 2025);
        $endYear = $startYear + 1;

        return [
            'school_id' => School::factory(),
            'name' => $startYear . '-' . $endYear,
            'start_date' => date('Y-m-d', mktime(0, 0, 0, 4, 1, $startYear)), // April 1st
            'end_date' => date('Y-m-d', mktime(0, 0, 0, 3, 31, $endYear)), // March 31st
            'is_current' => false,
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }

    /**
     * Indicate that the academic year is current.
     */
    public function current(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_current' => true,
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the academic year is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
        ]);
    }
}
