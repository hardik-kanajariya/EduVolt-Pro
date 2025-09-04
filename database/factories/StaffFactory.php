<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
{
    protected $model = Staff::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments = ['Administration', 'Accounts', 'Library', 'Laboratory', 'Security', 'Maintenance', 'Transport'];
        $positions = ['Clerk', 'Accountant', 'Librarian', 'Lab Assistant', 'Security Guard', 'Janitor', 'Driver'];

        return [
            'user_id' => User::factory(),
            'school_id' => School::factory(),
            'employee_id' => 'STF' . fake()->unique()->numberBetween(1000, 9999),
            'department' => fake()->randomElement($departments),
            'position' => fake()->randomElement($positions),
            'join_date' => fake()->dateTimeBetween('-10 years', 'now'),
            'salary' => fake()->numberBetween(15000, 50000),
            'employment_type' => fake()->randomElement(['full_time', 'part_time', 'contract']),
            'responsibilities' => [fake()->sentence(), fake()->sentence()],
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }

    /**
     * Indicate that the staff member is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Create staff for a specific department.
     */
    public function department(string $department): static
    {
        return $this->state(fn(array $attributes) => [
            'department' => $department,
        ]);
    }

    /**
     * Create a staff member with a user that has staff role.
     */
    public function withStaffUser(): static
    {
        return $this->state(function (array $attributes) {
            $user = User::factory()->create([
                'name' => fake()->name(),
                'email' => fake()->unique()->email(),
                'status' => true,
            ]);

            // Assign appropriate role based on department
            $role = match ($attributes['department'] ?? 'Administration') {
                'Library' => 'librarian',
                'Accounts' => 'accountant',
                default => 'staff'
            };

            $user->assignRole($role);

            return [
                'user_id' => $user->id,
            ];
        });
    }
}
