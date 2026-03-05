<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class MailRecipientResolver
{
    /**
     * Resolve recipients by a permission name.
     *
     * @return array<int, string> Unique, non-empty email addresses.
     */
    public function resolveByPermission(string $permission, ?string $context = null): array
    {
        $permission = trim($permission);

        if ($permission === '') {
            Log::warning('MailRecipientResolver called with empty permission', [
                'context' => $context,
            ]);

            return $this->fallback($permission, $context);
        }

        $emails = User::query()
            ->where(function ($query) use ($permission) {
                $query->where('role', 'super_admin')
                    ->orWhereHas('permissions', function ($permissionQuery) use ($permission) {
                        $permissionQuery->where('name', $permission);
                    });
            })
            ->pluck('email')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (!empty($emails)) {
            return array_values($emails);
        }

        return $this->fallback($permission, $context);
    }

    /**
     * Resolve recipients by a logical module key defined in config/mail_permissions.php.
     *
     * @return array<int, string>
     */
    public function resolveByModule(string $moduleKey, ?string $context = null): array
    {
        $moduleKey = trim($moduleKey);
        $permission = config('mail_permissions.' . $moduleKey);

        if (empty($permission)) {
            Log::warning('MailRecipientResolver missing permission mapping for module', [
                'module' => $moduleKey,
                'context' => $context,
            ]);

            // Try fallback directly when mapping is missing.
            return $this->fallback($moduleKey, $context);
        }

        return $this->resolveByPermission($permission, $context ?? $moduleKey);
    }

    /**
     * Fallback to configured admin email if available.
     *
     * @return array<int, string>
     */
    protected function fallback(string $permissionOrModule, ?string $context = null): array
    {
        $adminEmail = (string) config('mail.admin_email');
        $adminEmail = trim($adminEmail);

        if ($adminEmail !== '') {
            Log::info('MailRecipientResolver using fallback admin email', [
                'target' => $permissionOrModule,
                'context' => $context,
                'admin_email' => $adminEmail,
            ]);

            return [$adminEmail];
        }

        Log::warning('No recipients found and no fallback admin email configured', [
            'target' => $permissionOrModule,
            'context' => $context,
        ]);

        return [];
    }
}

