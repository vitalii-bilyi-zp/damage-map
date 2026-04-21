<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class DistrictFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'      => $this->faker->city() . ' district',
            'region_id' => Region::factory(),
        ];
    }
}
