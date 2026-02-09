<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed default role_permissions so new users get default permissions by role.
     * Run after PermissionSeeder.
     */
    public function run(): void
    {
        $allIds = DB::table('permissions')->pluck('id')->toArray();
        $excludeForRole = DB::table('permissions')->where('name', 'permissions.manage')->pluck('id')->toArray();
        $defaultIds = array_values(array_diff($allIds, $excludeForRole));

        foreach (['admin', 'manager'] as $role) {
            foreach ($defaultIds as $permissionId) {
                DB::table('role_permissions')->updateOrInsert(
                    ['role' => $role, 'permission_id' => $permissionId],
                    ['role' => $role, 'permission_id' => $permissionId]
                );
            }
        }
    }
}
