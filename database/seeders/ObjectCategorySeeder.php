<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ObjectCategory;

class ObjectCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'Житловий фонд',
            'Нежитловий фонд',
            'Природний фонд'
        ];

        foreach ($categories as $value) {
            ObjectCategory::updateOrCreate(
                ['name' => $value],
            );
        }
    }
}
