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
        $thirdCategory = ObjectCategory::firstWhere('name', 'Природний фонд');

        $types = [
            // first category
            ['name' => 'Багатоповерховий будинок', 'objectCategoryId' => $firstCategory['id']],
            ['name' => 'Приватний будинок', 'objectCategoryId' => $firstCategory['id']],
            ['name' => 'Гуртожиток', 'objectCategoryId' => $firstCategory['id']],
            ['name' => 'Інше', 'objectCategoryId' => $firstCategory['id']],
            // second category
            ['name' => 'Заклад дошкільної освіти', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Заклад загальної середньої освіти', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Заклад професійно-технічної освіти', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Заклад вищої освіти', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Заклад системи охорони здоров\'я', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Заклад культури', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Об\'єкт критичної інфраструктури', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Адміністративна будівля', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Бізнес-центр', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Господарча споруда', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Готель / ресторан', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Магазин', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'ТРЦ', 'objectCategoryId' => $secondCategory['id']],
            ['name' => 'Інше', 'objectCategoryId' => $secondCategory['id']],
            // third category
            ['name' => 'Ліс', 'objectCategoryId' => $thirdCategory['id']],
            ['name' => 'Поле', 'objectCategoryId' => $thirdCategory['id']],
            ['name' => 'Водойма', 'objectCategoryId' => $thirdCategory['id']],
            ['name' => 'Інше', 'objectCategoryId' => $thirdCategory['id']],
        ];

        foreach ($types as $value) {
            ObjectType::updateOrCreate([
                'name' => $value['name'],
                'object_category_id' => $value['objectCategoryId']
            ]);
        }
    }
}
