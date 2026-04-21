<?php

namespace Database\Factories;

use App\Models\DamageNote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DamageNoteRequest>
 */
class DamageNoteRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name'        => null,
            'email'            => null,
            'phone'            => null,
            'damage_note_id'   => DamageNote::factory(),
            'creator_id'       => User::factory(),
            'approver_id'      => null,
            'approver_comment' => null,
            'approved_at'      => null,
            'declined_at'      => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'approver_id' => User::factory(),
            'approved_at' => Carbon::now(),
            'declined_at' => null,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'approved_at' => null,
            'declined_at' => null,
        ]);
    }
}
