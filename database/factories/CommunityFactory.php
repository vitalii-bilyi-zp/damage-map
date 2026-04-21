<?php

namespace Database\Factories;

use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommunityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => $this->faker->city() . ' community',
            'district_id' => District::factory(),
        ];
    }
}
