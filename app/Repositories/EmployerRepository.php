<?php

namespace App\Repositories;

use App\Models\Employer;
use App\Repositories\Contracts\EmployerRepositoryInterface;

class EmployerRepository implements EmployerRepositoryInterface
{
    public function find(int $id): ?Employer
    {
        return Employer::with(['user', 'company'])->find($id);
    }

    public function findByUserId(int $userId): ?Employer
    {
        return Employer::with(['user', 'company'])
            ->where('user_id', $userId)
            ->first();
    }

    public function create(array $data): Employer
    {
        return Employer::create($data);
    }

    public function update(Employer $employer, array $data): Employer
    {
        $employer->update($data);
        return $employer->fresh(['user', 'company']);
    }

    public function delete(Employer $employer): bool
    {
        return $employer->delete();
    }
}
