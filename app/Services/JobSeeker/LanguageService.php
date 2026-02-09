<?php

namespace App\Services\JobSeeker;

use App\Models\JobSeeker;
use App\Models\JobSeekerLanguage;
use Illuminate\Database\Eloquent\Collection;

class LanguageService
{
    public function getBySeeker(JobSeeker $jobSeeker): Collection
    {
        return $jobSeeker->languages()->orderBy('language')->get();
    }

    public function create(JobSeeker $jobSeeker, array $data): JobSeekerLanguage
    {
        $data['seeker_id'] = $jobSeeker->seeker_id;
        return JobSeekerLanguage::create($data);
    }

    public function update(JobSeekerLanguage $language, array $data): JobSeekerLanguage
    {
        $language->update($data);
        return $language->fresh();
    }

    public function delete(JobSeekerLanguage $language): bool
    {
        return $language->delete();
    }
}
