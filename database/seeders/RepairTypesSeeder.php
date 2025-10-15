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
        $categories = [
            'Поточний',
            'Капітальний',
            'Повна реконструкція'
        ];

        foreach ($categories as $value) {
            RepairType::updateOrCreate(
                ['name' => $value],
            );
        }
    }
}
