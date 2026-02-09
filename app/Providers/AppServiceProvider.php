<?php

namespace App\Providers;

use App\Repositories\CompanyRepository;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Contracts\JobAdvertisementRepositoryInterface;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use App\Repositories\Contracts\JobCategoryRepositoryInterface;
use App\Repositories\JobAdvertisementRepository;
use App\Repositories\JobApplicationRepository;
use App\Repositories\JobCategoryRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repository Interfaces with their Implementations
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(JobCategoryRepositoryInterface::class, JobCategoryRepository::class);
        $this->app->bind(JobAdvertisementRepositoryInterface::class, JobAdvertisementRepository::class);
        $this->app->bind(JobApplicationRepositoryInterface::class, JobApplicationRepository::class);
        $this->app->bind(\App\Repositories\Contracts\JobSeekerRepositoryInterface::class, \App\Repositories\JobSeekerRepository::class);
        $this->app->bind(\App\Repositories\Contracts\SavedJobRepositoryInterface::class, \App\Repositories\SavedJobRepository::class);
        $this->app->bind(\App\Repositories\Contracts\FollowedCompanyRepositoryInterface::class, \App\Repositories\FollowedCompanyRepository::class);
        $this->app->bind(\App\Repositories\Contracts\EmployerRepositoryInterface::class, \App\Repositories\EmployerRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Log all database queries in development (to debug slow queries)
        if (config('app.debug')) {
            \DB::listen(function ($query) {
                \Log::debug('Query: ' . $query->sql . ' [' . $query->time . 'ms]');
            });
        }
    }
}
