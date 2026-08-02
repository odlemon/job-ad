<?php

namespace App\Support;

class TeamPermissions
{
    public const ROLES = ['admin', 'manager', 'recruiter', 'viewer'];

    public const PERMISSIONS = [
        'admin' => [
            'Full access',
            'Manage team',
            'Billing',
            'Company settings',
        ],
        'manager' => [
            'Post jobs',
            'Review applicants',
            'Manage campaigns',
            'View analytics',
        ],
        'recruiter' => [
            'Post jobs',
            'Review applicants',
            'Schedule interviews',
        ],
        'viewer' => [
            'View jobs',
            'View applicants',
        ],
    ];

    public const CAPABILITIES = [
        'admin' => ['*'],
        'manager' => [
            'view_jobs',
            'post_jobs',
            'view_applicants',
            'review_applicants',
            'manage_campaigns',
            'view_analytics',
            'schedule_interviews',
        ],
        'recruiter' => [
            'view_jobs',
            'post_jobs',
            'view_applicants',
            'review_applicants',
            'schedule_interviews',
        ],
        'viewer' => [
            'view_jobs',
            'view_applicants',
        ],
    ];

    public const BADGE_CLASSES = [
        'admin' => 'tm-badge-admin',
        'manager' => 'tm-badge-manager',
        'recruiter' => 'tm-badge-recruiter',
        'viewer' => 'tm-badge-viewer',
    ];

    public static function can(?string $role, string $capability): bool
    {
        if (!$role) {
            return false;
        }

        $caps = self::CAPABILITIES[$role] ?? [];

        return in_array('*', $caps, true) || in_array($capability, $caps, true);
    }

    public static function canAssignRole(string $actorRole, string $targetRole): bool
    {
        if ($actorRole === 'admin') {
            return in_array($targetRole, self::ROLES, true);
        }

        // Managers can invite/edit only recruiter/viewer
        if ($actorRole === 'manager') {
            return in_array($targetRole, ['recruiter', 'viewer'], true);
        }

        return false;
    }

    public static function roleRank(string $role): int
    {
        return match ($role) {
            'admin' => 4,
            'manager' => 3,
            'recruiter' => 2,
            'viewer' => 1,
            default => 0,
        };
    }
}
