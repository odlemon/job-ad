<?php

namespace App\Repositories\Contracts;

use App\Models\JobSeeker;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface JobSeekerRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?JobSeeker;
    public function findByUserId(int $userId): ?JobSeeker;
    public function create(array $data): JobSeeker;
    public function update(JobSeeker $jobSeeker, array $data): JobSeeker;
    public function delete(JobSeeker $jobSeeker): bool;
}
