<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ObjectCategory;
use App\Models\ObjectType;

class ObjectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $firstCategory = ObjectCategory::firstWhere('name', 'Житловий фонд');
        $secondCategory = ObjectCategory::firstWhere('name', 'Нежитловий фонд');

        $types = [
            ['name' => 'багатоповерховий будинок', 'objectCategoryId' => $firstCategory['id']],
            ['name' => 'приватний будинок', 'objectCategoryId' => $firstCategory['id']],
            ['name' => 'гуртожиток', 'objectCategoryId' => $firstCategory['id']],

            ['name' => 'адміністративна будівля', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'бізнес-центр', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'господарча споруда', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'готель / ресторан', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'магазин', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'ТРЦ', 'objectCategoryId' => $secondCategory['id']]
        ];

        foreach ($types as $value) {
            ObjectType::updateOrCreate(
                ['name' => $value['name']],
                ['object_category_id' => $value['objectCategoryId']]
            );
        }
    }
}
