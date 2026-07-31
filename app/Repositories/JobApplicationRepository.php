<?php

namespace App\Repositories;

use App\Models\JobApplication;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class JobApplicationRepository implements JobApplicationRepositoryInterface
{
    public function all(): Collection
    {
        return JobApplication::with(['jobAdvertisement', 'user', 'jobSeeker'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return JobApplication::with(['jobAdvertisement', 'user', 'jobSeeker'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function find(int $id): ?JobApplication
    {
        return JobApplication::with(['jobAdvertisement', 'user', 'jobSeeker', 'reviewer'])->find($id);
    }

    public function create(array $data): JobApplication
    {
        return JobApplication::create($data);
    }

    public function update(JobApplication $application, array $data): JobApplication
    {
        $application->update($data);
        return $application->fresh(['jobAdvertisement', 'user', 'jobSeeker', 'reviewer']);
    }

    public function delete(JobApplication $application): bool
    {
        return $application->delete();
    }

    public function getByJobAdvertisement(int $jobId): Collection
    {
        return JobApplication::with(['user', 'jobSeeker'])
            ->where('job_advertisement_id', $jobId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return JobApplication::with(['jobAdvertisement', 'user', 'jobSeeker'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getBySeekerId(int $seekerId): Collection
    {
        return JobApplication::with(['jobAdvertisement', 'jobAdvertisement.company', 'jobAdvertisement.category'])
            ->where('seeker_id', $seekerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function existsForSeekerAndJob(int $seekerId, int $jobAdvertisementId): bool
    {
        return JobApplication::query()
            ->where('seeker_id', $seekerId)
            ->where('job_advertisement_id', $jobAdvertisementId)
            ->exists();
    }

    public function paginateBySeekerId(int $seekerId, int $perPage = 15, ?array $statuses = null): LengthAwarePaginator
    {
        $query = JobApplication::with(['jobAdvertisement', 'jobAdvertisement.company', 'jobAdvertisement.category'])
            ->where('seeker_id', $seekerId)
            ->orderBy('created_at', 'desc');

        if ($statuses) {
            $query->whereIn('status', $statuses);
        }

        return $query->paginate($perPage);
    }

    public function getByCompanyId(int $companyId): Collection
    {
        return JobApplication::with(['jobAdvertisement', 'jobSeeker', 'jobAdvertisement.company'])
            ->whereHas('jobAdvertisement', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRecentByCompanyId(int $companyId, int $limit = 5): Collection
    {
        return JobApplication::with(['jobAdvertisement', 'jobSeeker', 'jobAdvertisement.company'])
            ->whereHas('jobAdvertisement', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getByStatusAndCompanyId(string $status, int $companyId): Collection
    {
        return JobApplication::with(['jobAdvertisement', 'jobSeeker', 'jobAdvertisement.company'])
            ->where('status', $status)
            ->whereHas('jobAdvertisement', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function countByCompanyId(int $companyId): int
    {
        return JobApplication::whereHas('jobAdvertisement', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->count();
    }

    public function getByUserId(int $userId): Collection
    {
        return JobApplication::with(['jobAdvertisement', 'jobAdvertisement.company', 'jobAdvertisement.category'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function paginateByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return JobApplication::with(['jobAdvertisement', 'jobAdvertisement.company', 'jobAdvertisement.category'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByUserIdPaginated(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return JobApplication::with(['jobAdvertisement', 'jobAdvertisement.company', 'jobAdvertisement.category'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
