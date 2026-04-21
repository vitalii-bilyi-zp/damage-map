<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RepairTypeFactory extends Factory
{
    public function definition(): array
    {
        $uid = $this->faker->unique()->uuid();

        return [
            'name' => 'repair_' . substr($uid, 0, 8),
            'code' => 'test_' . substr($uid, 0, 8),
        ];
    }
}
