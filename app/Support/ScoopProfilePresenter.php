<?php

namespace App\Support;

use App\Models\JobSeeker;
use App\Models\User;

class ScoopProfilePresenter
{
    public static function profile(JobSeeker $jobSeeker, ?User $user = null): array
    {
        $user = $user ?: $jobSeeker->user;

        // Counts are enough for strength; avoid hydrating every related model.
        $jobSeeker->loadCount([
            'categoryPreferences',
            'experiences',
            'educations',
            'skills',
            'languages',
            'certifications',
            'references',
            'documents',
            'socialLinks',
        ]);

        // Only load discovery names (small relation).
        $jobSeeker->loadMissing(['categoryPreferences.category:id,name']);

        // Match mobile Profile Completion drawer sections (14).
        $profileSections = [
            'personal_info' => filled($jobSeeker->first_name) && filled($jobSeeker->last_name),
            'social_links' => (int) $jobSeeker->social_links_count > 0,
            'about' => filled($jobSeeker->bio),
            'preferences' => ! empty($jobSeeker->job_preferences),
            'salary' => $jobSeeker->expected_salary_min !== null || $jobSeeker->expected_salary_max !== null,
            'documents' => (int) $jobSeeker->documents_count > 0,
            'discovery' => (int) $jobSeeker->category_preferences_count > 0,
            'experience' => (int) $jobSeeker->experiences_count > 0,
            'education' => (int) $jobSeeker->educations_count > 0,
            'skills' => (int) $jobSeeker->skills_count > 0,
            'languages' => (int) $jobSeeker->languages_count > 0,
            'hobbies' => ! empty($jobSeeker->hobbies),
            'certifications' => (int) $jobSeeker->certifications_count > 0,
            'references' => (int) $jobSeeker->references_count > 0,
        ];

        $completed = count(array_filter($profileSections));
        $total = count($profileSections);
        $percent = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        $joined = $jobSeeker->created_at
            ? 'Joined Since '.$jobSeeker->created_at->format('F Y')
            : null;

        $discovery = $jobSeeker->categoryPreferences
            ->map(fn ($pref) => $pref->category?->name)
            ->filter()
            ->values()
            ->all();

        return [
            'first_name' => $jobSeeker->first_name,
            'last_name' => $jobSeeker->last_name,
            'email' => $user?->email,
            'phone' => $jobSeeker->phone ?? $user?->phone,
            'location' => $jobSeeker->location,
            'gender' => $jobSeeker->gender,
            'date_of_birth' => optional($jobSeeker->date_of_birth)?->format('Y-m-d'),
            'employment_status' => $jobSeeker->employment_status,
            'highest_education' => $jobSeeker->highest_education,
            'driving_license' => (bool) $jobSeeker->driving_license,
            'profile_photo' => $jobSeeker->profile_photo,
            'bio' => $jobSeeker->bio,
            'job_preferences' => $jobSeeker->job_preferences ?? [],
            'job_discovery_categories' => $discovery,
            'expected_salary_min' => $jobSeeker->expected_salary_min,
            'expected_salary_max' => $jobSeeker->expected_salary_max,
            'joined_label' => $joined,
            'profile_strength_percent' => $percent,
            'profile_sections_completed' => $completed,
            'profile_sections_total' => $total,
            'profile_sections' => $profileSections,
        ];
    }
}
