<?php

namespace App\Services\JobSeeker;

use App\Models\JobSeeker;
use App\Models\JobSeekerCategoryPreference;
use Illuminate\Database\Eloquent\Collection;

class CategoryPreferenceService
{
    public function getBySeeker(JobSeeker $jobSeeker): Collection
    {
        return $jobSeeker->categoryPreferences()->with('category')->get();
    }

    public function sync(JobSeeker $jobSeeker, array $categoryIds): void
    {
        // Delete existing preferences
        $jobSeeker->categoryPreferences()->delete();

        // Create new preferences (max 6)
        $categoryIds = array_slice($categoryIds, 0, 6);
        foreach ($categoryIds as $categoryId) {
            JobSeekerCategoryPreference::create([
                'seeker_id' => $jobSeeker->seeker_id,
                'category_id' => $categoryId,
            ]);
        }
    }

    public function add(JobSeeker $jobSeeker, int $categoryId): JobSeekerCategoryPreference
    {
        // Check if already exists
        $existing = JobSeekerCategoryPreference::where('seeker_id', $jobSeeker->seeker_id)
            ->where('category_id', $categoryId)
            ->first();

        if ($existing) {
            return $existing;
        }

        // Check max limit (6)
        $count = $jobSeeker->categoryPreferences()->count();
        if ($count >= 6) {
            throw new \Exception('Maximum 6 categories allowed');
        }

        return JobSeekerCategoryPreference::create([
            'seeker_id' => $jobSeeker->seeker_id,
            'category_id' => $categoryId,
        ]);
    }

    public function remove(JobSeeker $jobSeeker, int $categoryId): bool
    {
        $preference = JobSeekerCategoryPreference::where('seeker_id', $jobSeeker->seeker_id)
            ->where('category_id', $categoryId)
            ->firstOrFail();

        return $preference->delete();
    }
}
