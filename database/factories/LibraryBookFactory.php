<?php

namespace Database\Factories;

use App\Models\LibraryBook;
use App\Models\BookCategory;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class LibraryBookFactory extends Factory
{
    protected $model = LibraryBook::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'isbn' => $this->faker->isbn13(),
            'publisher' => $this->faker->company(),
            'publication_year' => $this->faker->year(),
            'pages' => $this->faker->numberBetween(100, 800),
            'language' => 'English',
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->randomElement(['A1', 'A2', 'B1', 'B2', 'C1', 'C2']),
            'total_copies' => $this->faker->numberBetween(1, 10),
            'available_copies' => function (array $attributes) {
                return $attributes['total_copies'];
            },
            'school_id' => School::factory(),
            'book_category_id' => BookCategory::factory(),
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn(array $attributes) => [
            'available_copies' => 0,
        ]);
    }

    public function singleCopy(): static
    {
        return $this->state(fn(array $attributes) => [
            'total_copies' => 1,
            'available_copies' => 1,
        ]);
    }
}
