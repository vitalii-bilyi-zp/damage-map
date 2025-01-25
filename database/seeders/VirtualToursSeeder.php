<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VirtualTour;

class VirtualToursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tours = [
            ['title' => 'Млин Германа Нібура', 'description' => 'м. Запоріжжя, вул. Сергія Серікова, 30'],
            ['title' => 'Вокзал «Олександрівськ»', 'description' => 'м. Запоріжжя, вул. Костянтина Великого, 9'],
            ['title' => 'Будинок жіночої гімназії', 'description' => 'м. Оріхів вул. Шевченка, 16'],
            ['title' => 'Будинок чоловічого реального училища', 'description' => 'м. Оріхів вул. Шевченка, 16'],
            ['title' => 'Будинок торгових рядів', 'description' => 'м. Оріхів вул. Покровська, 39'],
            ['title' => 'Гуляйпільський краєзнавчий музей', 'description' => 'м. Гуляйполе вул. Соборна, 75'],
            ['title' => 'Паровий млин «Надія»', 'description' => 'м. Гуляйполе вул. Соборна, 115'],
        ];

        foreach ($tours as $key => $value) {
            VirtualTour::updateOrCreate($value);
        }
    }
}
