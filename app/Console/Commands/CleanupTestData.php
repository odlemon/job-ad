<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\JobAdvertisement;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete test data: jobs from Lysp and Google companies, specific users, and the companies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of test data...');

        DB::beginTransaction();

        try {
            // Step 1: Delete jobs from specified companies
            $this->info('Step 1: Deleting jobs from Lysp and Google companies...');
            $companies = Company::whereIn('name', ['Lysp', 'Google'])->get();
            
            if ($companies->isEmpty()) {
                $this->warn('No companies found with names "Lysp" or "Google"');
            } else {
                foreach ($companies as $company) {
                    $jobsCount = JobAdvertisement::where('company_id', $company->id)->count();
                    $this->info("Found {$jobsCount} jobs for company: {$company->name}");
                    
                    // Delete jobs (applications will cascade delete)
                    JobAdvertisement::where('company_id', $company->id)->forceDelete();
                    $this->info("Deleted {$jobsCount} jobs from {$company->name}");
                }
            }

            // Step 2: Delete specified users
            $this->info('Step 2: Deleting users...');
            $userEmails = ['veximagames@gmail.com', 'nyashakarata1@gmail.com'];
            $users = User::whereIn('email', $userEmails)->get();
            
            if ($users->isEmpty()) {
                $this->warn('No users found with the specified emails');
            } else {
                foreach ($users as $user) {
                    $this->info("Deleting user: {$user->email}");
                    
                    // Delete related job seeker if exists
                    if ($user->jobSeeker) {
                        $user->jobSeeker->forceDelete();
                        $this->info("  - Deleted job seeker profile");
                    }
                    
                    // Delete related employer if exists
                    if ($user->employer) {
                        $user->employer->forceDelete();
                        $this->info("  - Deleted employer profile");
                    }
                    
                    // Delete user
                    $user->forceDelete();
                    $this->info("  - Deleted user");
                }
            }

            // Step 3: Delete the companies
            $this->info('Step 3: Deleting companies...');
            $companies = Company::whereIn('name', ['Lysp', 'Google'])->get();
            
            if ($companies->isEmpty()) {
                $this->warn('No companies found to delete');
            } else {
                foreach ($companies as $company) {
                    $this->info("Deleting company: {$company->name}");
                    $company->forceDelete();
                    $this->info("  - Deleted company: {$company->name}");
                }
            }

            DB::commit();
            $this->info('Cleanup completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error during cleanup: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
