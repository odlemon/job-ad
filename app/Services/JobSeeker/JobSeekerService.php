<?php

namespace App\Services\JobSeeker;

use App\Models\JobSeeker;
use App\Models\JobSeekerDocument;
use App\Repositories\Contracts\JobSeekerRepositoryInterface;
use App\Services\RemoteUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class JobSeekerService
{
    public function __construct(
        private JobSeekerRepositoryInterface $repository,
        private RemoteUploadService $uploadService
    ) {
    }

    /**
     * Get job seeker by user ID.
     */
    public function getByUserId(int $userId): ?JobSeeker
    {
        return $this->repository->findByUserId($userId);
    }

    /**
     * Get job seeker by ID.
     */
    public function getById(int $id): ?JobSeeker
    {
        return $this->repository->find($id);
    }

    /**
     * Update job seeker profile.
     */
    public function updateProfile(JobSeeker $jobSeeker, array $data): JobSeeker
    {
        return $this->repository->update($jobSeeker, $data);
    }

    /**
     * Upload CV file to remote server.
     */
    public function uploadCv(JobSeeker $jobSeeker, UploadedFile $file): JobSeeker
    {
        try {
            // Upload to remote server
            $uploadResult = $this->uploadService->uploadSingleFile($file, 'cv');

            // Update job seeker with remote file URL
            return $this->repository->update($jobSeeker, [
                'cv_file_path' => $uploadResult['downloadURL'],
                'cv_uploaded_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('CV upload failed', [
                'job_seeker_id' => $jobSeeker->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete CV file.
     * Note: We don't delete from remote server, just remove the reference
     */
    public function deleteCv(JobSeeker $jobSeeker): JobSeeker
    {
        // Update job seeker (remove CV reference)
        // Note: File remains on remote server for potential recovery
        return $this->repository->update($jobSeeker, [
            'cv_file_path' => null,
            'cv_uploaded_at' => null,
        ]);
    }

    /**
     * Upload profile photo to remote server.
     */
    public function uploadProfilePhoto(JobSeeker $jobSeeker, UploadedFile $file): JobSeeker
    {
        try {
            // Upload to remote server
            $uploadResult = $this->uploadService->uploadSingleFile($file, 'profile-photos');

            // Update job seeker with remote file URL
            return $this->repository->update($jobSeeker, [
                'profile_photo' => $uploadResult['downloadURL'],
            ]);
        } catch (\Exception $e) {
            Log::error('Profile photo upload failed', [
                'job_seeker_id' => $jobSeeker->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete profile photo.
     * Note: We don't delete from remote server, just remove the reference
     */
    public function deleteProfilePhoto(JobSeeker $jobSeeker): JobSeeker
    {
        // Update job seeker (remove profile photo reference)
        // Note: File remains on remote server for potential recovery
        return $this->repository->update($jobSeeker, [
            'profile_photo' => null,
        ]);
    }

    /**
     * Delete job seeker profile.
     */
    public function deleteProfile(JobSeeker $jobSeeker): bool
    {
        // Note: Files remain on remote server
        // In production, you might want to implement a cleanup job
        return $this->repository->delete($jobSeeker);
    }

    /**
     * Add a document (with user-provided name) for the job seeker.
     */
    public function addDocument(JobSeeker $jobSeeker, string $name, UploadedFile $file, bool $isPrimary = false): JobSeekerDocument
    {
        $uploadResult = $this->uploadService->uploadSingleFile($file, 'documents');
        if ($isPrimary) {
            JobSeekerDocument::where('seeker_id', $jobSeeker->seeker_id)->update(['is_primary' => false]);
            $this->repository->update($jobSeeker, [
                'cv_file_path' => $uploadResult['downloadURL'],
                'cv_uploaded_at' => now(),
            ]);
        }
        return JobSeekerDocument::create([
            'seeker_id' => $jobSeeker->seeker_id,
            'name' => $name,
            'file_path' => $uploadResult['downloadURL'],
            'is_primary' => $isPrimary,
        ]);
    }

    /**
     * Delete a document. If it was primary, clear cv_file_path on job seeker.
     */
    public function deleteDocument(JobSeeker $jobSeeker, JobSeekerDocument $document): void
    {
        $wasPrimary = $document->is_primary;
        $document->delete();
        if ($wasPrimary) {
            $newPrimary = JobSeekerDocument::where('seeker_id', $jobSeeker->seeker_id)->where('is_primary', true)->first()
                ?? JobSeekerDocument::where('seeker_id', $jobSeeker->seeker_id)->first();
            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
                $this->repository->update($jobSeeker, [
                    'cv_file_path' => $newPrimary->file_path,
                    'cv_uploaded_at' => now(),
                ]);
            } else {
                $this->repository->update($jobSeeker, [
                    'cv_file_path' => null,
                    'cv_uploaded_at' => null,
                ]);
            }
        }
    }

    /**
     * Set a document as the primary resume (used for job applications).
     */
    public function setPrimaryDocument(JobSeeker $jobSeeker, JobSeekerDocument $document): JobSeeker
    {
        JobSeekerDocument::where('seeker_id', $jobSeeker->seeker_id)->update(['is_primary' => false]);
        $document->update(['is_primary' => true]);
        return $this->repository->update($jobSeeker, [
            'cv_file_path' => $document->file_path,
            'cv_uploaded_at' => now(),
        ]);
    }
}
