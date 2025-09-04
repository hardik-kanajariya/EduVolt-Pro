<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $qualifications = ['B.Ed', 'M.Ed', 'B.A', 'M.A', 'B.Sc', 'M.Sc', 'B.Com', 'M.Com', 'Ph.D'];
        $specializations = ['Mathematics', 'English', 'Science', 'Social Studies', 'Computer Science', 'Physical Education'];

        return [
            'user_id' => User::factory(),
            'school_id' => School::factory(),
            'employee_id' => 'TCH' . fake()->unique()->numberBetween(1000, 9999),
            'qualification' => fake()->randomElement($qualifications),
            'experience_years' => fake()->numberBetween(1, 30),
            'join_date' => fake()->dateTimeBetween('-10 years', 'now'),
            'salary' => fake()->numberBetween(25000, 80000),
            'employment_type' => fake()->randomElement(['full_time', 'part_time', 'contract']),
            'specialization' => fake()->randomElement($specializations),
            'certifications' => [
                fake()->words(3, true),
                fake()->words(2, true),
            ],
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }

    /**
     * Indicate that the teacher is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the teacher is full-time.
     */
    public function fullTime(): static
    {
        return $this->state(fn(array $attributes) => [
            'employment_type' => 'full_time',
            'status' => 'active',
        ]);
    }

    /**
     * Create a teacher with a user that has teacher role.
     */
    public function withTeacherUser(): static
    {
        return $this->state(function (array $attributes) {
            $user = User::factory()->create([
                'name' => fake()->name(),
                'email' => fake()->unique()->email(),
                'status' => true,
            ]);

            // Assign teacher role
            $user->assignRole('teacher');

            return [
                'user_id' => $user->id,
            ];
        });
    }
}
