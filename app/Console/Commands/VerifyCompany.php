<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class VerifyCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:verify 
                            {company? : The company ID or name to verify}
                            {--all : Verify all companies}
                            {--unverify : Unverify the company}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify or unverify a company by ID or name';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $company = $this->argument('company');
        $all = $this->option('all');
        $unverify = $this->option('unverify');

        if ($all) {
            return $this->verifyAll($unverify);
        }

        if (!$company) {
            $this->error('Please provide a company ID or name, or use --all to verify all companies.');
            $this->info('Usage: php artisan company:verify <company_id_or_name>');
            $this->info('       php artisan company:verify --all');
            $this->info('       php artisan company:verify <company_id_or_name> --unverify');
            return 1;
        }

        // Try to find company by ID first, then by name
        $companyModel = Company::find($company);
        
        if (!$companyModel) {
            $companyModel = Company::where('name', 'like', "%{$company}%")->first();
        }

        if (!$companyModel) {
            $this->error("Company not found: {$company}");
            return 1;
        }

        if ($unverify) {
            $companyModel->verified_at = null;
            $companyModel->save();
            $this->info("Company '{$companyModel->name}' (ID: {$companyModel->id}) has been unverified.");
        } else {
            $companyModel->verified_at = now();
            $companyModel->save();
            $this->info("Company '{$companyModel->name}' (ID: {$companyModel->id}) has been verified.");
        }

        return 0;
    }

    /**
     * Verify or unverify all companies
     */
    private function verifyAll(bool $unverify): int
    {
        $companies = Company::all();
        $count = 0;

        foreach ($companies as $company) {
            if ($unverify) {
                $company->verified_at = null;
            } else {
                $company->verified_at = now();
            }
            $company->save();
            $count++;
        }

        $action = $unverify ? 'unverified' : 'verified';
        $this->info("Successfully {$action} {$count} companies.");

        return 0;
    }
}
