<?php

namespace Database\Factories;

use App\Models\BookIssue;
use App\Models\LibraryBook;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookIssueFactory extends Factory
{
    protected $model = BookIssue::class;

    public function definition(): array
    {
        $issuedAt = $this->faker->dateTimeBetween('-30 days', 'now');

        return [
            'library_book_id' => LibraryBook::factory(),
            'student_id' => Student::factory(),
            'issued_by' => User::factory(),
            'returned_to' => null,
            'issued_at' => $issuedAt,
            'due_date' => Carbon::instance($issuedAt)->addDays(14),
            'returned_at' => null,
            'renewal_count' => 0,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function returned(): static
    {
        return $this->state(fn(array $attributes) => [
            'returned_at' => $this->faker->dateTimeBetween($attributes['issued_at'], 'now'),
            'returned_to' => User::factory(),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn(array $attributes) => [
            'due_date' => $this->faker->dateTimeBetween('-10 days', '-1 day'),
        ]);
    }

    public function renewed(): static
    {
        return $this->state(fn(array $attributes) => [
            'renewal_count' => $this->faker->numberBetween(1, 3),
            'due_date' => Carbon::instance($attributes['issued_at'])->addDays(28), // Extended due to renewal
        ]);
    }
}
