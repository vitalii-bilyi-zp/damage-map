<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ObjectCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'category_' . substr($this->faker->unique()->uuid(), 0, 8),
        ];
    }
}
