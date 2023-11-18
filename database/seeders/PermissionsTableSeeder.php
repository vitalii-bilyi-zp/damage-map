<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            ['name' => 'users.viewList', 'guard_name' => 'api'],
            ['name' => 'users.store', 'guard_name' => 'api'],
            ['name' => 'users.view', 'guard_name' => 'api'],
            ['name' => 'users.update', 'guard_name' => 'api'],
            ['name' => 'users.destroy', 'guard_name' => 'api'],
        ];

        foreach ($permissions as $key => $value) {
            Permission::updateOrCreate($value);
        }
    }
}
