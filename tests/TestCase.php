<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Insert a Spatie role bypassing mass-assignment so the custom
     * NOT NULL `display_name` column is always populated.
     */
    protected function createRole(string $name, string $displayName = ''): void
    {
        DB::table('roles')->insert([
            'name'         => $name,
            'display_name' => $displayName ?: ucwords(str_replace('_', ' ', $name)),
            'guard_name'   => 'api',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Clear Spatie's in-memory permission cache
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
