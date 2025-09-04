<?php

namespace Database\Factories;

use App\Models\BookCategory;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookCategoryFactory extends Factory
{
    protected $model = BookCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Science Fiction',
                'Fantasy',
                'Mystery',
                'Romance',
                'Biography',
                'History',
                'Science',
                'Mathematics',
                'Literature',
                'Philosophy'
            ]),
            'description' => $this->faker->sentence(),
            'color' => $this->faker->randomElement([
                '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'
            ]),
            'sort_order' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
            'school_id' => School::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
