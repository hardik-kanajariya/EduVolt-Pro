<?php

namespace Database\Factories;

use App\Models\BookReservation;
use App\Models\LibraryBook;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookReservationFactory extends Factory
{
    protected $model = BookReservation::class;

    public function definition(): array
    {
        $reservedAt = $this->faker->dateTimeBetween('-7 days', 'now');
        
        return [
            'library_book_id' => LibraryBook::factory(),
            'student_id' => Student::factory(),
            'reserved_by' => User::factory(),
            'fulfilled_by' => null,
            'reserved_at' => $reservedAt,
            'expires_at' => Carbon::instance($reservedAt)->addDays(7),
            'fulfilled_at' => null,
            'status' => 'pending',
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function fulfilled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'fulfilled',
            'fulfilled_at' => $this->faker->dateTimeBetween($attributes['reserved_at'], 'now'),
            'fulfilled_by' => User::factory(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'expires_at' => $this->faker->dateTimeBetween('-3 days', '-1 day'),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
