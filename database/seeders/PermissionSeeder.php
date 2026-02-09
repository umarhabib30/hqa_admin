<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Defines all permissions; run RolePermissionSeeder after for default role assignments.
     */
    public function run(): void
    {
        $permissions = [
            // ─── Dashboard (sidebar: "Dashboard") ────────────────────────────────
            ['name' => 'dashboard.view', 'display_name' => 'Dashboard', 'group' => 'Dashboard', 'description' => 'Access to main dashboard'],

            // ─── HomePage (sidebar: "Home PopUp", "Home Alumni", etc.) ───────────
            ['name' => 'homepage.view', 'display_name' => 'HomePage', 'group' => 'HomePage', 'description' => 'Access to homepage management'],
            ['name' => 'homepage.modal', 'display_name' => 'Home PopUp', 'group' => 'HomePage', 'description' => 'Create, edit, delete home modals'],
            ['name' => 'homepage.memories', 'display_name' => 'Home Alumni', 'group' => 'HomePage', 'description' => 'Manage home page memories'],
            ['name' => 'homepage.top_achievers', 'display_name' => 'Home Top Achievers', 'group' => 'HomePage', 'description' => 'Manage top achievers section'],
            ['name' => 'homepage.news', 'display_name' => 'Home News Section', 'group' => 'HomePage', 'description' => 'Create and manage news articles'],
            ['name' => 'homepage.videos', 'display_name' => 'Home Videos Section', 'group' => 'HomePage', 'description' => 'Manage video content'],
            ['name' => 'homepage.socials', 'display_name' => 'Home Socials Section', 'group' => 'HomePage', 'description' => 'Manage social media links'],

            // ─── Fundrasier (sidebar section name; items: Achievements, Giving, etc.) ─
            ['name' => 'donation.view', 'display_name' => 'Giving', 'group' => 'Fundrasier', 'description' => 'Access to general donations (Giving)'],
            ['name' => 'donation.achievements', 'display_name' => 'Achievements', 'group' => 'Fundrasier', 'description' => 'Manage donation achievements'],
            ['name' => 'donation.fundraise', 'display_name' => 'FundRaise Goals', 'group' => 'Fundrasier', 'description' => 'Manage fundraising goals'],
            ['name' => 'donation.booking', 'display_name' => 'Donation Booking & Scan Check-in', 'group' => 'Fundrasier', 'description' => 'Donation booking and scan check-in'],
            ['name' => 'donation.images', 'display_name' => 'Donation Images', 'group' => 'Fundrasier', 'description' => 'Manage donation gallery'],
            ['name' => 'coupons.view', 'display_name' => 'Coupons', 'group' => 'Fundrasier', 'description' => 'Access coupons list and codes'],

            // ─── PTO (sidebar: "PTO Events", "PTO Fee Person", etc.) ─────────────
            ['name' => 'pto.view', 'display_name' => 'PTO', 'group' => 'PTO', 'description' => 'Access to PTO section'],
            ['name' => 'pto.events', 'display_name' => 'PTO Events', 'group' => 'PTO', 'description' => 'Create and manage PTO events'],
            ['name' => 'pto.subscribe', 'display_name' => 'PTO Subscribe Mails', 'group' => 'PTO', 'description' => 'View PTO email subscriptions'],
            ['name' => 'pto.attendees', 'display_name' => 'PTO Attendees', 'group' => 'PTO', 'description' => 'View and manage PTO event attendees'],
            ['name' => 'pto.images', 'display_name' => 'PTO Images', 'group' => 'PTO', 'description' => 'Manage PTO gallery'],
            ['name' => 'pto.letter_guide', 'display_name' => 'PTO Letter Guide Download', 'group' => 'PTO', 'description' => 'Manage downloadable letter guides'],
            ['name' => 'pto.easy_join', 'display_name' => 'PTO Easy Join', 'group' => 'PTO', 'description' => 'Manage PTO easy join registrations'],
            ['name' => 'pto.fee', 'display_name' => 'PTO Fee Person', 'group' => 'PTO', 'description' => 'Manage PTO fee person pricing'],

            // ─── Career (sidebar: "Teacher Job Post", "Job Applications") ────────
            ['name' => 'career.view', 'display_name' => 'Career', 'group' => 'Career', 'description' => 'Access to career section'],
            ['name' => 'career.job_posts', 'display_name' => 'Teacher Job Post', 'group' => 'Career', 'description' => 'Create and manage job postings'],
            ['name' => 'career.job_applications', 'display_name' => 'Job Applications', 'group' => 'Career', 'description' => 'View and manage job applications'],

            // ─── Alumni (sidebar: "Add Alumni", "Alumni Event", etc.) ────────────
            ['name' => 'alumni.view', 'display_name' => 'Alumni', 'group' => 'Alumni', 'description' => 'Access to alumni section'],
            ['name' => 'alumni.huston', 'display_name' => 'Add Alumni', 'group' => 'Alumni', 'description' => 'Manage alumni Huston information'],
            ['name' => 'alumni.events', 'display_name' => 'Alumni Event', 'group' => 'Alumni', 'description' => 'Create and manage alumni events'],
            ['name' => 'alumni.posts', 'display_name' => 'Alumni Posts', 'group' => 'Alumni', 'description' => 'Manage alumni posts'],
            ['name' => 'alumni.images', 'display_name' => 'Alumni Images', 'group' => 'Alumni', 'description' => 'Manage alumni gallery'],
            ['name' => 'alumni.fee', 'display_name' => 'Alumni Fee Per Person', 'group' => 'Alumni', 'description' => 'Manage alumni event fee pricing'],
            ['name' => 'alumni.attendees', 'display_name' => 'Alumni Event Attendees', 'group' => 'Alumni', 'description' => 'View and manage alumni event attendees'],
            ['name' => 'alumni.forms', 'display_name' => 'Alumni Form', 'group' => 'Alumni', 'description' => 'View submitted alumni forms'],
            ['name' => 'alumni.mails', 'display_name' => 'Alumni Mail', 'group' => 'Alumni', 'description' => 'Manage alumni email subscriptions'],

            // ─── Calendar (sidebar: "Calendar") ─────────────────────────────────
            ['name' => 'calendar.view', 'display_name' => 'Calendar (View)', 'group' => 'Calendar', 'description' => 'Access to calendar'],
            ['name' => 'calendar.manage', 'display_name' => 'Calendar', 'group' => 'Calendar', 'description' => 'Create, edit, delete calendar events'],

            // ─── Sponsor Packages (sidebar: "Sponsor Packages") ───────────────────
            ['name' => 'sponsor_packages.view', 'display_name' => 'Sponsor Packages', 'group' => 'Sponsor Packages', 'description' => 'Access to sponsor packages'],
            ['name' => 'sponsor_packages.create', 'display_name' => 'Sponsor Packages (Create)', 'group' => 'Sponsor Packages', 'description' => 'Create new sponsor packages'],
            ['name' => 'sponsor_packages.edit', 'display_name' => 'Sponsor Packages (Edit)', 'group' => 'Sponsor Packages', 'description' => 'Edit sponsor packages'],
            ['name' => 'sponsor_packages.delete', 'display_name' => 'Sponsor Packages (Delete)', 'group' => 'Sponsor Packages', 'description' => 'Delete sponsor packages'],

            // ─── Contact Sponsor (sidebar: "Contact Sponsor") ────────────────────
            ['name' => 'contact_sponsor.view', 'display_name' => 'Contact Sponsor', 'group' => 'Contact Sponsor', 'description' => 'Access contact sponsor inquiries'],

            // ─── Users (sidebar: "Users") ───────────────────────────────────────
            ['name' => 'managers.view', 'display_name' => 'Users', 'group' => 'Users', 'description' => 'Access to users list (admins + managers)'],
            ['name' => 'managers.create', 'display_name' => 'Users (Create)', 'group' => 'Users', 'description' => 'Create new admin/manager accounts'],
            ['name' => 'managers.edit', 'display_name' => 'Users (Edit)', 'group' => 'Users', 'description' => 'Edit admin/manager information'],
            ['name' => 'managers.delete', 'display_name' => 'Users (Delete)', 'group' => 'Users', 'description' => 'Delete admin/manager accounts'],
            ['name' => 'managers.reset_password', 'display_name' => 'Users (Reset Password)', 'group' => 'Users', 'description' => 'Reset passwords for admins/managers'],

            // ─── Coupons (extra actions; main "Coupons" link is under Fundrasier) ─
            ['name' => 'coupons.create', 'display_name' => 'Coupons (Create)', 'group' => 'Coupons', 'description' => 'Create new coupon packages'],
            ['name' => 'coupons.edit', 'display_name' => 'Coupons (Edit)', 'group' => 'Coupons', 'description' => 'Edit coupon packages'],
            ['name' => 'coupons.delete', 'display_name' => 'Coupons (Delete)', 'group' => 'Coupons', 'description' => 'Delete coupon packages'],
            ['name' => 'coupons.view_codes', 'display_name' => 'Coupons (View Codes)', 'group' => 'Coupons', 'description' => 'View and copy coupon codes'],

            // ─── Permissions (sidebar: "Permissions", Super Admin only) ──────────
            ['name' => 'permissions.manage', 'display_name' => 'Permissions', 'group' => 'Permissions', 'description' => 'Manage user permissions (Super Admin only)'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
