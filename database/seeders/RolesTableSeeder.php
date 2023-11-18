<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::updateOrCreate(['name' => 'super_admin', 'display_name' => 'Технічний адміністратор', 'guard_name' => 'api']);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'display_name' => 'Адміністратор', 'guard_name' => 'api']);
        $analystRole = Role::firstOrCreate(['name' => 'analyst', 'display_name' => 'Аналітик', 'guard_name' => 'api']);

        $this->setAdminPermissions($adminRole);
        $this->setAnalystPermissions($analystRole);
    }

    private function setAdminPermissions($role)
    {
        $permissionsList = [
            'users.view',
            'users.update',
        ];
        $role->syncPermissions(Permission::whereIn('name', $permissionsList)->get());
    }

    private function setAnalystPermissions($role)
    {
        $permissionsList = [
            'users.view',
            'users.update',
        ];
        $role->syncPermissions(Permission::whereIn('name', $permissionsList)->get());
    }
}
