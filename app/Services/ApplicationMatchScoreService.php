<?php

namespace App\Services;

use App\Models\JobApplication;
use Illuminate\Support\Collection;

class ApplicationMatchScoreService
{
    /**
     * Calculate match score (0-100) based on job requirements vs job seeker skills.
     * Extracts skill-like terms from the job's requirements text and compares
     * against the seeker's listed skills (exact or partial match).
     */
    public function calculate(JobApplication $application): ?int
    {
        $job = $application->jobAdvertisement;
        $seeker = $application->jobSeeker;

        if (!$job || !$seeker) {
            return null;
        }

        $requiredTerms = $this->extractSkillTermsFromText($job->requirements);
        $seekerSkills = $this->getSeekerSkillNames($seeker);

        if ($requiredTerms->isEmpty()) {
            return null;
        }

        $matched = 0;
        foreach ($requiredTerms as $term) {
            if ($this->termMatchesAnySkill($term, $seekerSkills)) {
                $matched++;
            }
        }

        $percentage = (int) round(($matched / $requiredTerms->count()) * 100);
        return min(100, max(0, $percentage));
    }

    /**
     * Extract skill-like terms from requirements/description text.
     */
    protected function extractSkillTermsFromText(?string $text): Collection
    {
        if (empty(trim($text ?? ''))) {
            return collect();
        }

        $normalized = preg_replace('/\s+/', ' ', $text);
        $normalized = str_replace([' and ', ' or ', ' • ', ' - '], ',', $normalized);
        $chunks = preg_split('/[,;\n]/', $normalized, -1, PREG_SPLIT_NO_EMPTY);

        return collect($chunks)
            ->map(fn (string $s) => trim($s))
            ->filter(function (string $s) {
                $len = strlen($s);
                if ($len < 2 || $len > 80) {
                    return false;
                }
                if (preg_match('/^\d+\s*(years?|yrs?)?\s*(experience|exp)?\.?$/i', $s)) {
                    return false;
                }
                if (preg_match('/^\d+%?$/', $s)) {
                    return false;
                }
                return true;
            })
            ->map(fn (string $s) => strtolower($s))
            ->unique()
            ->values();
    }

    /**
     * Get normalized list of job seeker skill names.
     */
    protected function getSeekerSkillNames($seeker): Collection
    {
        if (!$seeker) {
            return collect();
        }

        $skills = $seeker->relationLoaded('skills') ? $seeker->skills : $seeker->skills()->get();
        return collect($skills)->map(function ($s) {
            $name = is_array($s) ? ($s['skill_name'] ?? $s['name'] ?? '') : ($s->skill_name ?? '');
            return strtolower(trim((string) $name));
        })->filter()->unique()->values();
    }

    /**
     * Check if a required term matches any of the seeker's skills (exact or partial).
     */
    protected function termMatchesAnySkill(string $term, Collection $seekerSkills): bool
    {
        foreach ($seekerSkills as $skill) {
            if ($term === $skill) {
                return true;
            }
            if (str_contains($skill, $term) || str_contains($term, $skill)) {
                return true;
            }
            if ($this->normalizedEquals($term, $skill)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Normalize for comparison (e.g. "node.js" vs "nodejs").
     */
    protected function normalizedEquals(string $a, string $b): bool
    {
        $a = str_replace(['.', '-', ' ', '_'], '', $a);
        $b = str_replace(['.', '-', ' ', '_'], '', $b);
        return $a === $b;
    }

    
}
