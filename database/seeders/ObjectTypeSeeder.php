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
            // first category
            ['name' => 'Багатоповерховий будинок', 'objectCategoryId' => $firstCategory['id']],
            ['name' => 'Приватний будинок', 'objectCategoryId' => $firstCategory['id']],
            ['name' => 'Гуртожиток', 'objectCategoryId' => $firstCategory['id']],
            // second category
            ['name' => 'Адміністративна будівля', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Бізнес-центр', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Господарча споруда', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Готель / ресторан', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Магазин', 'objectCategoryId' => $secondCategory['id']],
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
