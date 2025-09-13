<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'school_id' => \App\Models\School::factory(),
            'class_id' => \App\Models\SchoolClass::factory(),
            'admission_number' => fake()->unique()->numerify('STU######'),
            'roll_number' => fake()->numberBetween(1, 50),
            'admission_date' => fake()->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
            'parent_name' => fake()->name(),
            'parent_phone' => fake()->phoneNumber(),
            'parent_email' => fake()->email(),
            'emergency_contact' => fake()->phoneNumber(),
            'medical_conditions' => fake()->optional()->sentence(),
            'transport_required' => fake()->boolean(),
            'bus_route' => fake()->optional()->streetName(),
            'status' => fake()->randomElement(['active', 'inactive', 'transferred', 'graduated']),
        ];
    }

    /**
     * Indicate that the student is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Create a student with a user that has student role.
     */
    public function withStudentUser(): static
    {
        return $this->state(function (array $attributes) {
            $user = User::factory()->create([
                'name' => fake()->name(),
                'email' => fake()->unique()->email(),
                'status' => true,
            ]);

            // Assign student role
            $user->assignRole('student');

            return [
                'user_id' => $user->id,
            ];
        });
    }
}
