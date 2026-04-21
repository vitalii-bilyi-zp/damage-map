<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RepairType;

class RepairTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'Поточний ремонт',                    'code' => 'current_repair'],
            ['name' => 'Капітальний ремонт',                 'code' => 'capital_repair'],
            ['name' => 'Реставрація',                        'code' => 'restoration'],
            ['name' => 'Знесення з новим будівництвом',      'code' => 'demolition_rebuild'],
            ['name' => 'Консервація',                        'code' => 'conservation'],
        ];

        foreach ($types as $data) {
            RepairType::updateOrCreate(
                ['code' => $data['code']],
                ['name' => $data['name'], 'code' => $data['code']]
            );
        }
    }
}
