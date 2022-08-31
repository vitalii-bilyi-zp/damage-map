<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::firstOrCreate(
            ['name' => 'admin', 'email' => 'admin@example.com'],
            [
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
            ]
        );
    }
}
