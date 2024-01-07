<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\DamageNote;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DamageNote>
 */
class DamageNoteRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $userId = User::inRandomOrder()->first()->id;

        return [
            'full_name' => null,
            'email' => null,
            'phone' => null,
            'damage_note_id' => DamageNote::inRandomOrder()->first()->id,
            'creator_id' => $userId,
            'approver_id' => $userId,
            'approver_comment' => null,
            'approved_at' => Carbon::now(),
            'declined_at' => null
        ];
    }
}
