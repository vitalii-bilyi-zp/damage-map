<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DamageNote;
use Carbon\Carbon;

class DamageNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $startDay = Carbon::createFromFormat('Y-m-d', '2023-06-01')->startOfDay();
        $endDay = Carbon::now()->endOfDay();

        while ($startDay->lessThan($endDay)) {
            DamageNote::factory()->count(rand(3, 10))->create([
                'date' => $startDay->toDateString(),
            ]);

            $startDay->addDay();
        }
    }
}
