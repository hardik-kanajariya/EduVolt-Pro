<?php

namespace Database\Factories;

use App\Models\LibraryFine;
use App\Models\BookIssue;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LibraryFineFactory extends Factory
{
    protected $model = LibraryFine::class;

    public function definition(): array
    {
        return [
            'book_issue_id' => BookIssue::factory(),
            'student_id' => Student::factory(),
            'amount' => $this->faker->randomFloat(2, 1, 50),
            'reason' => $this->faker->randomElement([
                'Overdue return',
                'Late return',
                'Damaged book',
                'Lost book'
            ]),
            'status' => 'unpaid',
            'fine_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'paid_at' => null,
            'paid_by' => null,
            'waived_at' => null,
            'waived_by' => null,
            'waiver_reason' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'paid',
            'paid_at' => $this->faker->dateTimeBetween($attributes['fine_date'], 'now'),
            'paid_by' => User::factory(),
        ]);
    }

    public function waived(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'waived',
            'waived_at' => $this->faker->dateTimeBetween($attributes['fine_date'], 'now'),
            'waived_by' => User::factory(),
            'waiver_reason' => $this->faker->sentence(),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn(array $attributes) => [
            'reason' => 'Overdue return',
            'amount' => $this->faker->randomFloat(2, 5, 25),
        ]);
    }
}
