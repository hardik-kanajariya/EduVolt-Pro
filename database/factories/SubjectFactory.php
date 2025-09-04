<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subjects = [
            'Mathematics',
            'English',
            'Science',
            'Social Studies',
            'Hindi',
            'Sanskrit',
            'Physics',
            'Chemistry',
            'Biology',
            'Computer Science',
            'Physical Education',
            'Art & Craft',
            'Music',
            'History',
            'Geography',
            'Economics',
            'Political Science'
        ];

        return [
            'school_id' => School::factory(),
            'name' => fake()->randomElement($subjects),
            'code' => strtoupper(fake()->lexify('???')),
            'description' => fake()->sentence(),
            'credits' => fake()->numberBetween(1, 6),
            'type' => fake()->randomElement(['core', 'elective', 'optional']),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }

    /**
     * Indicate that the subject is core.
     */
    public function core(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'core',
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the subject is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
        ]);
    }
}
