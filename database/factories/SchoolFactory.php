<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    protected $model = School::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company() . ' School';

        return [
            'name' => $name,
            'code' => strtoupper(fake()->unique()->lexify('SCH???')),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->companyEmail(),
            'website' => 'https://' . fake()->domainName(),
            'principal_name' => fake()->name(),
            'established_date' => fake()->dateTimeBetween('-50 years', '-5 years')->format('Y-m-d'),
            'board_affiliation' => fake()->randomElement(['CBSE', 'ICSE', 'State Board', 'IB', 'Cambridge']),
            'status' => fake()->randomElement(['active', 'inactive']),
            'logo' => null,
            'description' => fake()->paragraph(),
        ];
    }

    /**
     * Indicate that the school is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the school is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
