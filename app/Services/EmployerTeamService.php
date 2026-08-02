<?php

namespace App\Services;

use App\Models\CompanyTeamMember;
use App\Models\Employer;
use App\Models\User;
use App\Support\TeamPermissions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EmployerTeamService
{
    public function ensureOwnerMembership(Employer $employer): ?CompanyTeamMember
    {
        if (!$employer->company_id || !$employer->user_id) {
            return null;
        }

        $user = $employer->user;
        $existing = CompanyTeamMember::where('company_id', $employer->company_id)
            ->where(function ($q) use ($employer, $user) {
                $q->where('user_id', $employer->user_id);
                if ($user?->email) {
                    $q->orWhere('email', $user->email);
                }
            })
            ->first();

        if ($existing) {
            if (!$existing->user_id) {
                $existing->user_id = $employer->user_id;
            }
            if ($existing->status === 'pending') {
                $existing->status = 'active';
                $existing->joined_at = $existing->joined_at ?: now();
                $existing->invite_token = null;
            }
            $existing->last_active_at = now();
            $existing->save();

            return $existing;
        }

        // First member for company becomes admin (owner)
        $hasAdmin = CompanyTeamMember::where('company_id', $employer->company_id)
            ->where('role', 'admin')
            ->exists();

        return CompanyTeamMember::create([
            'company_id' => $employer->company_id,
            'user_id' => $employer->user_id,
            'invited_by' => null,
            'name' => $user->name ?? $employer->company_name,
            'email' => $user->email,
            'role' => $hasAdmin ? 'manager' : 'admin',
            'status' => 'active',
            'joined_at' => now(),
            'last_active_at' => now(),
            'jobs_posted' => 0,
        ]);
    }

    public function currentMember(): ?CompanyTeamMember
    {
        $user = Auth::user();
        if (!$user || $user->user_type !== 'employer') {
            return null;
        }

        $employer = $user->employer;
        if (!$employer?->company_id) {
            return null;
        }

        $this->ensureOwnerMembership($employer);

        return CompanyTeamMember::where('company_id', $employer->company_id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();
    }

    public function userCan(string $capability): bool
    {
        $member = $this->currentMember();

        return $member ? $member->can($capability) : false;
    }

    public function touchActivity(?User $user = null): void
    {
        $user = $user ?: Auth::user();
        if (!$user) {
            return;
        }

        CompanyTeamMember::where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['last_active_at' => now()]);
    }

    public function incrementJobsPosted(User $user): void
    {
        CompanyTeamMember::where('user_id', $user->id)
            ->where('status', 'active')
            ->increment('jobs_posted');
    }

    public function createInvite(int $companyId, int $inviterId, string $name, string $email, string $role): CompanyTeamMember
    {
        return CompanyTeamMember::create([
            'company_id' => $companyId,
            'user_id' => null,
            'invited_by' => $inviterId,
            'name' => $name,
            'email' => strtolower(trim($email)),
            'role' => $role,
            'status' => 'pending',
            'invite_token' => Str::random(48),
            'invited_at' => now(),
            'jobs_posted' => 0,
        ]);
    }
}
