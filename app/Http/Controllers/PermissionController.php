<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Display permissions management page
     */
    public function index()
    {
        // Check if user is super admin
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can manage permissions.');
        }

        $permissions = Permission::orderBy('group')->orderBy('display_name')->get();

        // Get permissions grouped by role
        $rolePermissions = DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->select('role_permissions.role', 'permissions.id as permission_id', 'permissions.name')
            ->get()
            ->groupBy('role');

        return view('dashboard.permissions.index', compact('permissions', 'rolePermissions'));
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions(Request $request)
    {
        // Check if user is super admin
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can manage permissions.');
        }

        $request->validate([
            'role' => 'required|in:admin,manager',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = $request->role;
        $permissionIds = $request->permissions ?? [];

        // Delete existing permissions for this role
        DB::table('role_permissions')->where('role', $role)->delete();

        // Insert new permissions
        if (!empty($permissionIds)) {
            $data = [];
            foreach ($permissionIds as $permissionId) {
                $data[] = [
                    'role' => $role,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('role_permissions')->insert($data);
        }

        return redirect()
            ->route('permissions.index')
            ->with('success', "Permissions updated successfully for {$role} role.")
            ->with('openRole', $role);
    }
}
