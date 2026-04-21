<?php

namespace Database\Factories;

use App\Models\ObjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObjectTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'               => $this->faker->unique()->word() . ' type',
            'object_category_id' => ObjectCategory::factory(),
        ];
    }
}
