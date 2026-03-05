<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\Models\User;
use App\Services\MailRecipientResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MailRecipientResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolve_by_permission_returns_users_with_permission_and_super_admins(): void
    {
        Config::set('mail.admin_email', 'fallback@example.com');

        $permission = Permission::create([
            'name' => 'donation.view',
            'display_name' => 'Donations',
            'group' => 'Fundrasier',
            'description' => 'Access to general donations',
        ]);

        $superAdmin = User::factory()->create([
            'email' => 'super@example.com',
            'role' => 'super_admin',
        ]);

        $permittedUser = User::factory()->create([
            'email' => 'donations@example.com',
            'role' => 'admin',
        ]);
        $permittedUser->permissions()->attach($permission->id);

        $resolver = app(MailRecipientResolver::class);

        $emails = $resolver->resolveByPermission('donation.view', 'unit-test');

        $this->assertEqualsCanonicalizing(
            ['super@example.com', 'donations@example.com'],
            $emails
        );
        $this->assertNotContains('fallback@example.com', $emails);
    }

    public function test_resolve_by_permission_falls_back_to_config_admin_email_when_no_users(): void
    {
        Config::set('mail.admin_email', 'fallback@example.com');

        $resolver = app(MailRecipientResolver::class);

        $emails = $resolver->resolveByPermission('nonexistent.permission', 'unit-test');

        $this->assertSame(['fallback@example.com'], $emails);
    }

    public function test_resolve_by_permission_returns_empty_array_when_no_users_and_no_fallback(): void
    {
        Config::set('mail.admin_email', null);

        $resolver = app(MailRecipientResolver::class);

        $emails = $resolver->resolveByPermission('nonexistent.permission', 'unit-test');

        $this->assertSame([], $emails);
    }
}

