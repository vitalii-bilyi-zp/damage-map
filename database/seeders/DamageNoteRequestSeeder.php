<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DamageNote;
use App\Models\DamageNoteRequest;
use Carbon\Carbon;

class DamageNoteRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $startDay = Carbon::today()->startOfMonth()->subMonth();
        $endDay = Carbon::today()->endOfDay();

        while ($startDay->lessThan($endDay)) {
            DamageNote::factory()->count(rand(3, 10))
                ->create([
                    'date' => $startDay->toDateString(),
                ])
                ->each(function ($damageNote) {
                    $isApproved = rand(0, 1);

                    if ($isApproved) {
                        DamageNoteRequest::factory()->create([
                            'damage_note_id' => $damageNote->id,
                        ]);
                    } else {
                        DamageNoteRequest::factory()->create([
                            'damage_note_id' => $damageNote->id,
                            'approver_id' => null,
                            'approved_at' => null,
                        ]);
                    }
                });

            $startDay->addDay();
        }
    }
}
