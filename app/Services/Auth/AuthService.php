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
     * Register a new employer user.
     */
    public function registerEmployer(array $userData, array $employerData): User
    {
        // Create user
        $user = User::create([
            'name' => $employerData['company_name'] ?? $userData['email'],
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
            'description' => $employerData['company_description'] ?? null,
            'website' => $employerData['website'] ?? null,
            'email' => $userData['email'],
            'phone' => $userData['phone'] ?? null,
            'industry' => $employerData['industry'] ?? null,
            'size' => $employerData['company_size'] ?? null,
            'location' => $employerData['address'] ?? null,
            'is_active' => true,
        ]);

        // Create employer profile and link to company
        $employerData['user_id'] = $user->id;
        $employerData['company_id'] = $company->id;
        $this->employerRepository->create($employerData);

        // Auto-login after registration
        Auth::login($user);

        return $user->load('employer');
    }
}
