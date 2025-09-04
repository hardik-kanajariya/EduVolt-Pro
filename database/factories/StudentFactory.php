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
            'user_id' => User::factory(),
            'school_id' => School::factory(),
            'class_id' => SchoolClass::factory(),
            'admission_number' => 'STU' . fake()->unique()->numberBetween(1000, 9999),
            'roll_number' => fake()->numberBetween(1, 50),
            'admission_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'parent_name' => fake()->name(),
            'parent_phone' => fake()->phoneNumber(),
            'parent_email' => fake()->email(),
            'medical_info' => fake()->optional()->sentence(),
            'transport_route' => fake()->optional()->streetName(),
            'emergency_contacts' => [
                [
                    'name' => fake()->name(),
                    'relationship' => fake()->randomElement(['Father', 'Mother', 'Guardian']),
                    'phone' => fake()->phoneNumber(),
                ]
            ],
            'status' => fake()->randomElement(['active', 'inactive', 'transferred']),
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
