<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Group order matching the sidebar (top to bottom).
     */
    private static function sidebarGroupOrder(): array
    {
        return [
            'Dashboard',
            'HomePage',
            'Fundrasier',  // same as sidebar section name
            'PTO',
            'Career',
            'Alumni',
            'Calendar',
            'Sponsor Packages',
            'Contact Sponsor',
            'Users',
            'Coupons',
            'Permissions',
        ];
    }

    /**
     * Permission name order within each group (matching sidebar).
     */
    private static function sidebarPermissionOrder(): array
    {
        return [
            'Dashboard' => ['dashboard.view'],
            'HomePage' => ['homepage.view', 'homepage.modal', 'homepage.memories', 'homepage.top_achievers', 'homepage.news', 'homepage.videos', 'homepage.socials'],
            'Fundrasier' => ['donation.achievements', 'donation.fundraise', 'donation.booking', 'donation.images', 'donation.view', 'coupons.view'],
            'PTO' => ['pto.events', 'pto.fee', 'pto.subscribe', 'pto.attendees', 'pto.letter_guide', 'pto.images', 'pto.easy_join', 'pto.view'],
            'Career' => ['career.view', 'career.job_posts', 'career.job_applications'],
            'Alumni' => ['alumni.huston', 'alumni.events', 'alumni.posts', 'alumni.fee', 'alumni.attendees', 'alumni.forms', 'alumni.mails', 'alumni.images', 'alumni.view'],
            'Calendar' => ['calendar.view', 'calendar.manage'],
            'Sponsor Packages' => ['sponsor_packages.view', 'sponsor_packages.create', 'sponsor_packages.edit', 'sponsor_packages.delete'],
            'Contact Sponsor' => ['contact_sponsor.view'],
            'Users' => ['managers.view', 'managers.create', 'managers.edit', 'managers.delete', 'managers.reset_password'],
            'Coupons' => ['coupons.create', 'coupons.edit', 'coupons.delete', 'coupons.view_codes'],
            'Permissions' => ['permissions.manage'],
        ];
    }

    /**
     * Return permissions ordered to match the sidebar (group order, then permission order within group).
     */
    private static function permissionsInSidebarOrder()
    {
        $all = Permission::all()->keyBy('name');
        $groupOrder = self::sidebarGroupOrder();
        $permissionOrder = self::sidebarPermissionOrder();
        $ordered = collect();

        foreach ($groupOrder as $group) {
            $names = $permissionOrder[$group] ?? [];
            foreach ($names as $name) {
                if ($all->has($name)) {
                    $ordered->push($all->get($name));
                }
            }
            // Any permission in this group not in the list (e.g. new ones) append at end of group
            $inGroup = $all->filter(fn ($p) => $p->group === $group);
            foreach ($inGroup as $p) {
                if (!$ordered->contains('id', $p->id)) {
                    $ordered->push($p);
                }
            }
        }

        // Groups or permissions not in the map (e.g. future additions)
        foreach ($all as $p) {
            if (!$ordered->contains('id', $p->id)) {
                $ordered->push($p);
            }
        }

        return $ordered;
    }
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

        $permissions = self::permissionsInSidebarOrder();
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
