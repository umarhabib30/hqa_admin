<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'group' => 'Dashboard', 'description' => 'Access to main dashboard'],

            // HomePage
            ['name' => 'homepage.view', 'display_name' => 'View Homepage Content', 'group' => 'HomePage', 'description' => 'Access to homepage management'],
            ['name' => 'homepage.modal', 'display_name' => 'Manage Home Modals', 'group' => 'HomePage', 'description' => 'Create, edit, delete home modals'],
            ['name' => 'homepage.memories', 'display_name' => 'Manage Home Memories', 'group' => 'HomePage', 'description' => 'Manage home page memories'],
            ['name' => 'homepage.top_achievers', 'display_name' => 'Manage Top Achievers', 'group' => 'HomePage', 'description' => 'Manage top achievers section'],
            ['name' => 'homepage.news', 'display_name' => 'Manage News', 'group' => 'HomePage', 'description' => 'Create and manage news articles'],
            ['name' => 'homepage.videos', 'display_name' => 'Manage Videos', 'group' => 'HomePage', 'description' => 'Manage video content'],
            ['name' => 'homepage.socials', 'display_name' => 'Manage Social Links', 'group' => 'HomePage', 'description' => 'Manage social media links'],

            // Donation
            ['name' => 'donation.view', 'display_name' => 'View Donation Section', 'group' => 'Donation', 'description' => 'Access to donation management'],
            ['name' => 'donation.achievements', 'display_name' => 'Manage Achievements', 'group' => 'Donation', 'description' => 'Manage donation achievements'],
            ['name' => 'donation.fundraise', 'display_name' => 'Manage Fundraisers', 'group' => 'Donation', 'description' => 'Manage fundraising goals'],
            ['name' => 'donation.booking', 'display_name' => 'Manage Donation Bookings', 'group' => 'Donation', 'description' => 'Manage donation event bookings'],
            ['name' => 'donation.images', 'display_name' => 'Manage Donation Images', 'group' => 'Donation', 'description' => 'Manage donation gallery'],

            // PTO
            ['name' => 'pto.view', 'display_name' => 'View PTO Section', 'group' => 'PTO', 'description' => 'Access to PTO management'],
            ['name' => 'pto.events', 'display_name' => 'Manage PTO Events', 'group' => 'PTO', 'description' => 'Create and manage PTO events'],
            ['name' => 'pto.subscribe', 'display_name' => 'Manage PTO Subscriptions', 'group' => 'PTO', 'description' => 'View PTO email subscriptions'],
            ['name' => 'pto.images', 'display_name' => 'Manage PTO Images', 'group' => 'PTO', 'description' => 'Manage PTO gallery'],
            ['name' => 'pto.letter_guide', 'display_name' => 'Manage Letter Guides', 'group' => 'PTO', 'description' => 'Manage downloadable letter guides'],
            ['name' => 'pto.easy_join', 'display_name' => 'Manage Easy Joins', 'group' => 'PTO', 'description' => 'Manage PTO easy join registrations'],
            ['name' => 'pto.fee', 'display_name' => 'Manage Fee Person Prices', 'group' => 'PTO', 'description' => 'Manage fee person pricing'],

            // Calendar
            ['name' => 'calendar.view', 'display_name' => 'View Calendar', 'group' => 'Calendar', 'description' => 'Access to calendar'],
            ['name' => 'calendar.manage', 'display_name' => 'Manage Calendar Events', 'group' => 'Calendar', 'description' => 'Create, edit, delete calendar events'],

            // Career/Jobs
            ['name' => 'career.view', 'display_name' => 'View Career Section', 'group' => 'Career', 'description' => 'Access to career management'],
            ['name' => 'career.job_posts', 'display_name' => 'Manage Job Posts', 'group' => 'Career', 'description' => 'Create and manage job postings'],
            ['name' => 'career.job_applications', 'display_name' => 'View Job Applications', 'group' => 'Career', 'description' => 'View and manage job applications'],

            // Alumni
            ['name' => 'alumni.view', 'display_name' => 'View Alumni Section', 'group' => 'Alumni', 'description' => 'Access to alumni management'],
            ['name' => 'alumni.huston', 'display_name' => 'Manage Alumni Huston', 'group' => 'Alumni', 'description' => 'Manage alumni Huston information'],
            ['name' => 'alumni.events', 'display_name' => 'Manage Alumni Events', 'group' => 'Alumni', 'description' => 'Create and manage alumni events'],
            ['name' => 'alumni.posts', 'display_name' => 'Manage Alumni Posts', 'group' => 'Alumni', 'description' => 'Manage alumni posts'],
            ['name' => 'alumni.images', 'display_name' => 'Manage Alumni Images', 'group' => 'Alumni', 'description' => 'Manage alumni gallery'],
            ['name' => 'alumni.forms', 'display_name' => 'View Alumni Forms', 'group' => 'Alumni', 'description' => 'View submitted alumni forms'],
            ['name' => 'alumni.mails', 'display_name' => 'Manage Alumni Mails', 'group' => 'Alumni', 'description' => 'Manage alumni email subscriptions'],

            // Users (Admin + Manager)
            ['name' => 'managers.view', 'display_name' => 'View Users', 'group' => 'Users', 'description' => 'Access to users list (admins + managers)'],
            ['name' => 'managers.create', 'display_name' => 'Create Users', 'group' => 'Users', 'description' => 'Create new admin/manager accounts'],
            ['name' => 'managers.edit', 'display_name' => 'Edit Users', 'group' => 'Users', 'description' => 'Edit admin/manager information'],
            ['name' => 'managers.delete', 'display_name' => 'Delete Users', 'group' => 'Users', 'description' => 'Delete admin/manager accounts'],
            ['name' => 'managers.reset_password', 'display_name' => 'Reset User Passwords', 'group' => 'Users', 'description' => 'Reset passwords for admins/managers'],

            // Sponsor Packages
            ['name' => 'sponsor_packages.view', 'display_name' => 'View Sponsor Packages', 'group' => 'Sponsor Packages', 'description' => 'Access to sponsor packages'],
            ['name' => 'sponsor_packages.create', 'display_name' => 'Create Sponsor Packages', 'group' => 'Sponsor Packages', 'description' => 'Create new sponsor packages'],
            ['name' => 'sponsor_packages.edit', 'display_name' => 'Edit Sponsor Packages', 'group' => 'Sponsor Packages', 'description' => 'Edit sponsor packages'],
            ['name' => 'sponsor_packages.delete', 'display_name' => 'Delete Sponsor Packages', 'group' => 'Sponsor Packages', 'description' => 'Delete sponsor packages'],

            // Coupons
            ['name' => 'coupons.view', 'display_name' => 'View Coupons', 'group' => 'Coupons', 'description' => 'Access to coupons list'],
            ['name' => 'coupons.create', 'display_name' => 'Create Coupons', 'group' => 'Coupons', 'description' => 'Create new coupon packages'],
            ['name' => 'coupons.edit', 'display_name' => 'Edit Coupons', 'group' => 'Coupons', 'description' => 'Edit coupon packages'],
            ['name' => 'coupons.delete', 'display_name' => 'Delete Coupons', 'group' => 'Coupons', 'description' => 'Delete coupon packages'],
            ['name' => 'coupons.view_codes', 'display_name' => 'View Coupon Codes', 'group' => 'Coupons', 'description' => 'View and copy coupon codes'],

            // Permissions (Super Admin Only)
            ['name' => 'permissions.manage', 'display_name' => 'Manage Permissions', 'group' => 'Permissions', 'description' => 'Manage role permissions (Super Admin only)'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
