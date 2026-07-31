<?php

namespace App\Support;

/**
 * Normalize Scoop / web registration payloads so UI labels and date formats
 * map onto the canonical API enum values before Laravel validation runs.
 */
class RegisterInputNormalizer
{
    public static function normalize(array $input): array
    {
        if (isset($input['date_of_birth'])) {
            $input['date_of_birth'] = self::normalizeDateOfBirth($input['date_of_birth']);
        }

        if (isset($input['gender'])) {
            $input['gender'] = self::normalizeGender($input['gender']);
        }

        if (isset($input['employment_status'])) {
            $input['employment_status'] = self::normalizeEmployment($input['employment_status']);
        }

        if (isset($input['highest_education'])) {
            $input['highest_education'] = self::normalizeEducation($input['highest_education']);
        }

        if (isset($input['job_preferences']) && is_array($input['job_preferences'])) {
            $input['job_preferences'] = array_values(array_filter(array_map(
                [self::class, 'normalizeJobPreference'],
                $input['job_preferences']
            )));
        }

        return $input;
    }

    public static function normalizeDateOfBirth(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $raw = trim((string) $value);
        $cleaned = preg_replace('/\s+/', '', $raw) ?? $raw;

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $cleaned)) {
            return $cleaned;
        }

        // DD/MM/YYYY or DD-MM-YYYY (Scoop UI)
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $cleaned, $m)) {
            return sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]);
        }

        return $raw;
    }

    public static function normalizeGender(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $key = self::key($value);

        $map = [
            'male' => 'male',
            'female' => 'female',
            'non_binary' => 'non_binary',
            'nonbinary' => 'non_binary',
            'other' => 'other',
            'prefer_not_to_say' => 'prefer_not_to_say',
            'prefernottosay' => 'prefer_not_to_say',
        ];

        return $map[$key] ?? strtolower(str_replace([' ', '-'], '_', trim((string) $value)));
    }

    public static function normalizeEmployment(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $key = self::key($value);

        $map = [
            'currently_employed' => 'currently_employed',
            'employed_full_time' => 'currently_employed',
            'employedfulltime' => 'currently_employed',
            'employed_part_time' => 'employed_part_time',
            'employedparttime' => 'employed_part_time',
            'self_employed' => 'self_employed',
            'selfemployed' => 'self_employed',
            'unemployed' => 'unemployed',
            'student' => 'student',
            'retired' => 'retired',
            'prefer_not_to_say' => 'prefer_not_to_say',
            'prefernottosay' => 'prefer_not_to_say',
        ];

        return $map[$key] ?? strtolower(str_replace([' ', '-'], '_', trim((string) $value)));
    }

    public static function normalizeEducation(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $key = self::key($value);

        $map = [
            'high_school' => 'high_school',
            'highschool' => 'high_school',
            'certificate_diploma' => 'certificate_diploma',
            'certificatediploma' => 'certificate_diploma',
            'diploma' => 'diploma',
            'associate' => 'associate',
            'associate_degree' => 'associate',
            'bachelor' => 'bachelor',
            'bachelors_degree' => 'bachelor',
            'bachelorsdegree' => 'bachelor',
            'master' => 'master',
            'masters_degree' => 'master',
            'mastersdegree' => 'master',
            'doctorate' => 'doctorate',
            'doctorate_phd' => 'doctorate',
            'professional' => 'professional',
            'other' => 'other',
            'none' => 'none',
            'prefer_not_to_say' => 'prefer_not_to_say',
            'prefernottosay' => 'prefer_not_to_say',
        ];

        return $map[$key] ?? trim((string) $value);
    }

    public static function normalizeJobPreference(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $key = self::key($value);

        $map = [
            'full_time' => 'full-time',
            'fulltime' => 'full-time',
            'part_time' => 'part-time',
            'parttime' => 'part-time',
            'contract' => 'contract',
            'temporary' => 'temporary',
            'internship' => 'internship',
            'freelance' => 'freelance',
        ];

        return $map[$key] ?? strtolower(str_replace('_', '-', trim((string) $value)));
    }

    private static function key(mixed $value): string
    {
        $s = strtolower(trim((string) $value));
        $s = str_replace(["'", "'"], '', $s);

        return preg_replace('/[\s\-]+/', '_', $s) ?? $s;
    }
}
