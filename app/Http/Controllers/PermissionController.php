<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display list of users for permission management (Super Admin only).
     */
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can manage permissions.');
        }

        // List all users except super_admin (super admin has all permissions by default)
        $users = User::where('role', '!=', 'super_admin')
            ->orderBy('name')
            ->get();

        return view('dashboard.permissions.index', compact('users'));
    }

    /**
     * Show form to edit permissions for a specific user.
     */
    public function editUser(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can manage permissions.');
        }

        if ($user->isSuperAdmin()) {
            abort(403, 'Super Admin has all permissions and cannot be edited.');
        }

        $permissions = Permission::orderBy('group')->orderBy('display_name')->get();
        $userPermissionIds = $user->permissions()->pluck('permissions.id')->toArray();

        return view('dashboard.permissions.edit-user', compact('user', 'permissions', 'userPermissionIds'));
    }

    /**
     * Update permissions for a specific user.
     */
    public function updateUser(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can manage permissions.');
        }

        if ($user->isSuperAdmin()) {
            abort(403, 'Super Admin has all permissions and cannot be edited.');
        }

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissionIds = $request->permissions ?? [];
        $user->permissions()->sync($permissionIds);

        return redirect()
            ->route('permissions.index')
            ->with('success', "Permissions updated successfully for {$user->name}.");
    }
}
