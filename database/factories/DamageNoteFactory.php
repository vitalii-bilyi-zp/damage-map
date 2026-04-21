<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\DamageNote;
use App\Models\ObjectType;
use App\Models\RepairType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DamageNote>
 */
class DamageNoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'date'             => '2022-02-24',
            'object_type_id'   => ObjectType::factory(),
            'community_id'     => Community::factory(),
            'city'             => $this->faker->city(),
            'street'           => $this->faker->streetName(),
            'building_number'  => $this->faker->buildingNumber(),
            'floors'           => $this->faker->numberBetween(1, 25),
            'area'             => round($this->faker->randomFloat(2, 40, 8000), 2),
            'damage_type'      => $this->faker->randomElement(array_keys(DamageNote::DAMAGE_TYPES_MAPPING)),
            'repair_type_id'   => RepairType::factory(),
            'restoration_cost' => $this->faker->numberBetween(15000, 25000000),
        ];
    }
}
