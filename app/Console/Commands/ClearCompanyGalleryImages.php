<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class ClearCompanyGalleryImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:clear-gallery-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all gallery images for all companies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing gallery images for all companies...');
        
        $companies = Company::all();
        $count = 0;
        
        foreach ($companies as $company) {
            if ($company->gallery_images) {
                $company->gallery_images = null;
                $company->save();
                $count++;
                $this->line("Cleared gallery for: {$company->name} (ID: {$company->id})");
            }
        }
        
        $this->info("Successfully cleared gallery images for {$count} companies.");
        
        return Command::SUCCESS;
    }
}
