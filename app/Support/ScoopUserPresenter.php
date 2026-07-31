<?php

namespace App\Support;

use App\Models\JobSeeker;
use App\Models\User;

class ScoopUserPresenter
{
    /**
     * @param  bool  $includeFullProfile  When false (default for /auth/me), skip heavy relation loads.
     */
    public static function user(User $user, bool $includeFullProfile = false): array
    {
        $jobSeeker = $user->relationLoaded('jobSeeker')
            ? $user->jobSeeker
            : $user->jobSeeker()->first();

        $firstName = $jobSeeker?->first_name;
        $lastName = $jobSeeker?->last_name;

        if ((! $firstName || ! $lastName) && $user->name) {
            $parts = preg_split('/\s+/', trim($user->name), 2) ?: [];
            $firstName = $firstName ?: ($parts[0] ?? null);
            $lastName = $lastName ?: ($parts[1] ?? null);
        }

        $displayName = trim(($firstName ?? '').' '.($lastName ?? ''));
        if ($displayName === '') {
            $displayName = $user->name;
        }

        $payload = [
            'id' => $user->id,
            'name' => $displayName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $user->email,
            'phone' => $user->phone ?? $jobSeeker?->phone,
            'user_type' => $user->user_type,
            'email_verified_at' => optional($user->email_verified_at)?->toIso8601String(),
            'created_at' => optional($user->created_at)?->toIso8601String(),
        ];

        if ($jobSeeker) {
            $payload['job_seeker'] = $includeFullProfile
                ? ScoopProfilePresenter::profile($jobSeeker, $user)
                : [
                    'first_name' => $jobSeeker->first_name,
                    'last_name' => $jobSeeker->last_name,
                    'email' => $user->email,
                    'phone' => $jobSeeker->phone ?? $user->phone,
                    'location' => $jobSeeker->location,
                    'profile_photo' => $jobSeeker->profile_photo,
                ];
        }

        return $payload;
    }
}
