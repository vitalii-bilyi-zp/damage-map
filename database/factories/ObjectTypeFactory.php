<?php

namespace Database\Factories;

use App\Models\ObjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObjectTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'               => 'type_' . substr($this->faker->unique()->uuid(), 0, 8),
            'object_category_id' => ObjectCategory::factory(),
        ];
    }
}
