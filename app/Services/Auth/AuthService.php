<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Contracts\JobSeekerRepositoryInterface;
use App\Repositories\Contracts\EmployerRepositoryInterface;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(
        private JobSeekerRepositoryInterface $jobSeekerRepository,
        private EmployerRepositoryInterface $employerRepository,
        private CompanyRepositoryInterface $companyRepository
    ) {
    }

    /**
     * Register a new job seeker user.
     */
    public function registerJobSeeker(array $userData, array $jobSeekerData): User
    {
        // Create user
        $user = User::create([
            'name' => trim(($jobSeekerData['first_name'] ?? '') . ' ' . ($jobSeekerData['last_name'] ?? '')),
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'user_type' => 'job_seeker',
            'phone' => $userData['phone'] ?? null,
            'is_active' => true,
            'is_verified' => false,
        ]);

        // Create job seeker profile
        $jobSeekerData['user_id'] = $user->id;
        $this->jobSeekerRepository->create($jobSeekerData);

        // Auto-login after registration
        Auth::login($user);

        return $user->load('jobSeeker');
    }

    /**
     * Login user and update last login.
     */
    public function login(array $credentials): ?User
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Update last login
            $user->update(['last_login' => now()]);
            
            // Load appropriate profile based on user type
            if ($user->user_type === 'job_seeker') {
                return $user->load('jobSeeker');
            } elseif ($user->user_type === 'employer') {
                return $user->load('employer');
            }
            
            return $user;
        }

        return null;
    }

    /**
     * Logout current user.
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * Get authenticated user with profile.
     */
    public function getAuthenticatedUser(): ?User
    {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }

        // Load appropriate profile based on user type
        if ($user->user_type === 'job_seeker') {
            return $user->load('jobSeeker');
        } elseif ($user->user_type === 'employer') {
            return $user->load('employer');
        }

        return $user;
    }

    /**
     * Change password for authenticated user.
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (! Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $user->update(['password' => Hash::make($newPassword)]);

        return true;
    }

    /**
     * Soft-delete / deactivate account.
     */
    public function deleteAccount(User $user): void
    {
        $user->tokens()->delete();
        $user->update([
            'is_active' => false,
            'email' => $user->email.'__deleted_'.$user->id.'_'.time(),
            'password' => Hash::make(Str::random(32)),
        ]);
    }

    /**
     * Register a new employer user.
     */
    public function registerEmployer(array $userData, array $employerData): User
    {
        // Create user — use first_name + last_name for the user's display name
        $firstName = $employerData['first_name'] ?? '';
        $lastName = $employerData['last_name'] ?? '';
        $fullName = trim("{$firstName} {$lastName}");

        $user = User::create([
            'name' => $fullName ?: ($employerData['company_name'] ?? $userData['email']),
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'user_type' => 'employer',
            'phone' => $userData['phone'] ?? null,
            'is_active' => true,
            'is_verified' => false,
        ]);

        // Create company record
        $company = $this->companyRepository->create([
            'name' => $employerData['company_name'],
            'slug' => Str::slug($employerData['company_name']),
            'website' => $employerData['website'] ?? null,
            'email' => $userData['email'],
            'phone' => $userData['phone'] ?? null,
            'industry' => $employerData['industry'] ?? null,
            'size' => $employerData['company_size'] ?? null,
            'is_active' => true,
        ]);

        // Create employer profile and link to company
        $this->employerRepository->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'company_name' => $employerData['company_name'],
            'industry' => $employerData['industry'] ?? null,
            'company_size' => $employerData['company_size'] ?? null,
            'website' => $employerData['website'] ?? null,
            'business_certificate_path' => $employerData['business_certificate_path'] ?? null,
        ]);

        // Auto-login after registration
        Auth::login($user);

        return $user->load('employer');
    }
}
