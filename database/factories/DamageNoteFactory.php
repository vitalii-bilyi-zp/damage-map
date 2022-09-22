<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ObjectType;
use App\Models\Community;
use App\Models\DamageNote;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DamageNote>
 */
class DamageNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'object_type_id' => ObjectType::inRandomOrder()->first()->id,
            'community_id' => Community::inRandomOrder()->first()->id,
            'city' => $this->faker->city(),
            'street' => $this->faker->streetName(),
            'building_number' => $this->faker->buildingNumber(),
            'damage_type' => array_keys(DamageNote::DAMAGE_TYPES_MAPPING)[rand(0, 2)],
            'restoration_cost' => $this->faker->numberBetween($min = 15000, $max = 25000000),
            'date' => '2022-02-24'
        ];
    }
}
