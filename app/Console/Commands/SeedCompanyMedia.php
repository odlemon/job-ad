<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class SeedCompanyMedia extends Command
{
    protected $signature = 'companies:seed-media {--force : Replace existing logo/cover even if set}';

    protected $description = 'Download real stock logos and banner images for every company and store them on the local media server';

    /**
     * Curated free Pexels images (direct JPEG URLs). Indexed per company id cycle.
     *
     * @var list<array{logo:string,cover:string}>
     */
    private array $packs = [
        // Tech / office
        [
            'logo' => 'https://images.pexels.com/photos/1181244/pexels-photo-1181244.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/1181354/pexels-photo-1181354.jpeg?auto=compress&cs=tinysrgb&w=1600',
        ],
        // Digital / modern workspace
        [
            'logo' => 'https://images.pexels.com/photos/3861969/pexels-photo-3861969.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=1600',
        ],
        // Resort / tropical
        [
            'logo' => 'https://images.pexels.com/photos/258154/pexels-photo-258154.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/338504/pexels-photo-338504.jpeg?auto=compress&cs=tinysrgb&w=1600',
        ],
        // Trading / shipping / warehouse
        [
            'logo' => 'https://images.pexels.com/photos/906494/pexels-photo-906494.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/1427107/pexels-photo-1427107.jpeg?auto=compress&cs=tinysrgb&w=1600',
        ],
        // Bank / finance
        [
            'logo' => 'https://images.pexels.com/photos/259027/pexels-photo-259027.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/534216/pexels-photo-534216.jpeg?auto=compress&cs=tinysrgb&w=1600',
        ],
        // Restaurant / food
        [
            'logo' => 'https://images.pexels.com/photos/262978/pexels-photo-262978.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/67468/pexels-photo-67468.jpeg?auto=compress&cs=tinysrgb&w=1600',
        ],
        // Medical
        [
            'logo' => 'https://images.pexels.com/photos/40568/medical-appointment-doctor-healthcare-40568.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/236380/pexels-photo-236380.jpeg?auto=compress&cs=tinysrgb&w=1600',
        ],
        // Education / academy
        [
            'logo' => 'https://images.pexels.com/photos/256541/pexels-photo-256541.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/207692/pexels-photo-207692.jpeg?auto=compress&cs=tinysrgb&w=1600',
        ],
        // Extra packs if more companies appear
        [
            'logo' => 'https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/380769/pexels-photo-380769.jpeg?auto=compress&cs=tinysrgb&w=1600',
        ],
        [
            'logo' => 'https://images.pexels.com/photos/3184338/pexels-photo-3184338.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/1170412/pexels-photo-1170412.jpeg?auto=compress&cs=tinysrgb&w=1600',
        ],
    ];

    public function handle(): int
    {
        if (! Schema::hasTable('companies')) {
            $this->error('companies table missing');

            return self::FAILURE;
        }

        $mediaBase = rtrim((string) config('services.media.base_url', 'http://127.0.0.1/uploads'), '/');
        $uploadRoot = (string) config('services.media.upload_dir', base_path('uploads'));
        $force = (bool) $this->option('force');

        foreach (['company-logos', 'company-gallery'] as $dir) {
            $path = $uploadRoot . DIRECTORY_SEPARATOR . $dir;
            if (! is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        $companies = DB::table('companies')->orderBy('id')->get(['id', 'name', 'logo', 'cover_image']);
        $this->info('Seeding media for '.$companies->count().' companies...');

        $ok = 0;
        foreach ($companies as $index => $company) {
            $pack = $this->packs[$index % count($this->packs)];
            $updates = [];

            $needsLogo = $force || blank($company->logo) || str_contains((string) $company->logo, 'demo-company-');
            $needsCover = $force || blank($company->cover_image);

            if ($needsLogo) {
                $rel = "company-logos/company-{$company->id}-logo.jpg";
                if ($this->downloadTo($pack['logo'], $uploadRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel))) {
                    $updates['logo'] = $mediaBase.'/'.$rel;
                } else {
                    $this->warn("Failed logo download for #{$company->id} {$company->name}");
                }
            }

            if ($needsCover) {
                $rel = "company-gallery/company-{$company->id}-banner.jpg";
                if ($this->downloadTo($pack['cover'], $uploadRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel))) {
                    $updates['cover_image'] = $mediaBase.'/'.$rel;
                } else {
                    $this->warn("Failed cover download for #{$company->id} {$company->name}");
                }
            }

            if ($updates !== []) {
                DB::table('companies')->where('id', $company->id)->update($updates);
                $this->line("✓ #{$company->id} {$company->name}: ".implode(', ', array_keys($updates)));
                $ok++;
            } else {
                $this->line("- #{$company->id} {$company->name}: already has media (use --force to replace)");
            }
        }

        $this->info("Updated {$ok} companies.");

        return self::SUCCESS;
    }

    private function downloadTo(string $url, string $absolute): bool
    {
        try {
            $response = Http::timeout(45)
                ->withHeaders([
                    'User-Agent' => 'ScoopMediaSeeder/1.0',
                    'Accept' => 'image/jpeg,image/*,*/*',
                ])
                ->get($url);

            if (! $response->successful()) {
                return false;
            }

            $body = $response->body();
            if (strlen($body) < 1000) {
                return false;
            }

            $dir = dirname($absolute);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($absolute, $body);

            return is_file($absolute) && filesize($absolute) > 1000;
        } catch (\Throwable $e) {
            $this->warn($e->getMessage());

            return false;
        }
    }
}
