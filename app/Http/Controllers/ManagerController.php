<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    /**
     * Display a listing of the managers.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Super admin can see all users, admin can see admin and manager, manager can see only managers
        if ($user->isSuperAdmin()) {
            $managers = User::latest()->get();
        } elseif ($user->isAdmin()) {
            $managers = User::whereIn('role', ['admin', 'manager'])->latest()->get();
        } else {
            $managers = User::where('role', 'manager')->latest()->get();
        }
        
        return view('dashboard.managers.index', compact('managers'));
    }

    /**
     * Show the form for creating a new manager.
     */
    public function create()
    {
        return view('dashboard.managers.create');
    }

    /**
     * Store a newly created manager in storage.
     */
    public function store(Request $request)
    {
        $roleOptions = auth()->user()->isSuperAdmin() 
            ? 'required|in:super_admin,admin,manager' 
            : 'required|in:admin,manager';
            
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => $roleOptions,
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role ?? 'manager',
            'password' => Hash::make($request->password),
        ]);

        // Copy default permissions from role to user_permissions (super admin can customize later)
        if (!$user->isSuperAdmin()) {
            $permissionIds = DB::table('role_permissions')
                ->where('role', $user->role)
                ->pluck('permission_id');
            $user->permissions()->sync($permissionIds);
        }

        return redirect()
            ->route('managers.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified manager.
     */
    public function show($id)
    {
        $manager = User::findOrFail($id);
        return view('dashboard.managers.show', compact('manager'));
    }

    /**
     * Show the form for editing the specified manager.
     */
    public function edit($id)
    {
        $manager = User::findOrFail($id);
        return view('dashboard.managers.edit', compact('manager'));
    }

    /**
     * Update the specified manager in storage.
     */
    public function update(Request $request, $id)
    {
        $manager = User::findOrFail($id);

        $roleOptions = auth()->user()->isSuperAdmin() 
            ? 'required|in:super_admin,admin,manager' 
            : 'required|in:admin,manager';
            
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => $roleOptions,
        ]);
        
        // Prevent non-super-admin from changing role to super_admin
        if (!auth()->user()->isSuperAdmin() && $request->role === 'super_admin') {
            return back()->withErrors(['role' => 'You cannot assign super admin role.'])->withInput();
        }

        $manager->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return redirect()
            ->route('managers.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified manager from storage.
     */
    public function destroy($id)
    {
        $manager = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($manager->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        // Prevent non-super-admin from deleting super_admin
        if (!$manager->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            // Allow admins to delete managers
        } elseif ($manager->role === 'super_admin') {
            return back()->with('error', 'You cannot delete a super admin account.');
        }

        $manager->delete();

        return redirect()
            ->route('managers.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Show the form for resetting password.
     */
    public function showResetPassword($id)
    {
        $manager = User::findOrFail($id);
        return view('dashboard.managers.reset-password', compact('manager'));
    }

    /**
     * Reset the password for the specified manager.
     */
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $manager = User::findOrFail($id);
        $manager->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('managers.index')
            ->with('success', 'Password reset successfully.');
    }
}
