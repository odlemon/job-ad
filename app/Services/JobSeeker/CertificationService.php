<?php

namespace App\Services\JobSeeker;

use App\Models\JobSeeker;
use App\Models\JobSeekerCertification;
use App\Services\RemoteUploadService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

class CertificationService
{
    public function __construct(
        private RemoteUploadService $uploadService
    ) {
    }

    public function getBySeeker(JobSeeker $jobSeeker): Collection
    {
        return $jobSeeker->certifications()->orderBy('issue_date', 'desc')->get();
    }

    public function create(JobSeeker $jobSeeker, array $data, ?UploadedFile $certificateFile = null): JobSeekerCertification
    {
        $data['seeker_id'] = $jobSeeker->seeker_id;

        if ($certificateFile) {
            $uploadResult = $this->uploadService->uploadSingleFile($certificateFile, 'certifications');
            $data['certificate_file_path'] = $uploadResult['downloadURL'];
        }

        return JobSeekerCertification::create($data);
    }

    public function update(JobSeekerCertification $certification, array $data, ?UploadedFile $certificateFile = null): JobSeekerCertification
    {
        if ($certificateFile) {
            $uploadResult = $this->uploadService->uploadSingleFile($certificateFile, 'certifications');
            $data['certificate_file_path'] = $uploadResult['downloadURL'];
        }

        $certification->update($data);
        return $certification->fresh();
    }

    public function delete(JobSeekerCertification $certification): bool
    {
        return $certification->delete();
    }
}
