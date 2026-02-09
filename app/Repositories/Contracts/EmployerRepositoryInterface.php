<?php

namespace App\Repositories\Contracts;

use App\Models\Employer;

interface EmployerRepositoryInterface
{
    public function find(int $id): ?Employer;
    public function findByUserId(int $userId): ?Employer;
    public function create(array $data): Employer;
    public function update(Employer $employer, array $data): Employer;
    public function delete(Employer $employer): bool;
}
