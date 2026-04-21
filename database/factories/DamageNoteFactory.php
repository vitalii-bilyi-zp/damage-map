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
    protected static array $objectTypeIds = [];
    protected static array $communityIds = [];
    protected static array $repairTypeIds = [];

    public function definition(): array
    {
        if (empty(self::$objectTypeIds)) {
            self::$objectTypeIds = ObjectType::query()->pluck('id')->all();
        }
        if (empty(self::$communityIds)) {
            self::$communityIds = Community::query()->pluck('id')->all();
        }
        if (empty(self::$repairTypeIds)) {
            self::$repairTypeIds = RepairType::query()->pluck('id')->all();
        }

        return [
            'date'             => '2022-02-24',
            'object_type_id'   => $this->faker->randomElement(self::$objectTypeIds),
            'community_id'     => $this->faker->randomElement(self::$communityIds),
            'city'             => $this->faker->city(),
            'street'           => $this->faker->streetName(),
            'building_number'  => $this->faker->buildingNumber(),
            'floors'           => $this->faker->numberBetween(1, 25),
            'area'             => round($this->faker->randomFloat(2, 40, 8000), 2),
            'damage_type'      => $this->faker->randomElement(array_keys(DamageNote::DAMAGE_TYPES_MAPPING)),
            'repair_type_id'   => $this->faker->randomElement(self::$repairTypeIds),
            'restoration_cost' => $this->faker->numberBetween(15000, 25000000),
            'heritage_status'  => $this->faker->randomElement(array_keys(DamageNote::HERITAGE_STATUSES_MAPPING)),
        ];
    }
}
