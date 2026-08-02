<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class SeedCompanyMedia extends Command
{
    protected $signature = 'companies:seed-media
                            {--force : Replace existing logo/cover/gallery even if set}
                            {--skip-reviews : Do not seed company reviews}';

    protected $description = 'Download real logos, banners, and gallery photos for every company; seed reviews';

    /**
     * @var list<array{logo:string,cover:string,gallery:list<string>}>
     */
    private array $packs = [
        // Tech
        [
            'logo' => 'https://images.pexels.com/photos/1181244/pexels-photo-1181244.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/1181354/pexels-photo-1181354.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'gallery' => [
                'https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/3184338/pexels-photo-3184338.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/1181675/pexels-photo-1181675.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/3861969/pexels-photo-3861969.jpeg?auto=compress&cs=tinysrgb&w=1000',
            ],
        ],
        // Digital
        [
            'logo' => 'https://images.pexels.com/photos/3861969/pexels-photo-3861969.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'gallery' => [
                'https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/3183197/pexels-photo-3183197.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/1181406/pexels-photo-1181406.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/1181396/pexels-photo-1181396.jpeg?auto=compress&cs=tinysrgb&w=1000',
            ],
        ],
        // Resort
        [
            'logo' => 'https://images.pexels.com/photos/258154/pexels-photo-258154.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/338504/pexels-photo-338504.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'gallery' => [
                'https://images.pexels.com/photos/261102/pexels-photo-261102.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/189296/pexels-photo-189296.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/2034335/pexels-photo-2034335.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/1450353/pexels-photo-1450353.jpeg?auto=compress&cs=tinysrgb&w=1000',
            ],
        ],
        // Trading / shipping
        [
            'logo' => 'https://images.pexels.com/photos/906494/pexels-photo-906494.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/1427107/pexels-photo-1427107.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'gallery' => [
                'https://images.pexels.com/photos/906494/pexels-photo-906494.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/2226458/pexels-photo-2226458.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/4483610/pexels-photo-4483610.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/1267338/pexels-photo-1267338.jpeg?auto=compress&cs=tinysrgb&w=1000',
            ],
        ],
        // Bank
        [
            'logo' => 'https://images.pexels.com/photos/259027/pexels-photo-259027.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/534216/pexels-photo-534216.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'gallery' => [
                'https://images.pexels.com/photos/50987/money-card-business-credit-card-50987.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/4386370/pexels-photo-4386370.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/210574/pexels-photo-210574.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/7821486/pexels-photo-7821486.jpeg?auto=compress&cs=tinysrgb&w=1000',
            ],
        ],
        // Restaurant
        [
            'logo' => 'https://images.pexels.com/photos/262978/pexels-photo-262978.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/67468/pexels-photo-67468.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'gallery' => [
                'https://images.pexels.com/photos/941861/pexels-photo-941861.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/262047/pexels-photo-262047.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/1267320/pexels-photo-1267320.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/1581384/pexels-photo-1581384.jpeg?auto=compress&cs=tinysrgb&w=1000',
            ],
        ],
        // Medical
        [
            'logo' => 'https://images.pexels.com/photos/40568/medical-appointment-doctor-healthcare-40568.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/236380/pexels-photo-236380.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'gallery' => [
                'https://images.pexels.com/photos/263402/pexels-photo-263402.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/3376790/pexels-photo-3376790.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/4386466/pexels-photo-4386466.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/3845810/pexels-photo-3845810.jpeg?auto=compress&cs=tinysrgb&w=1000',
            ],
        ],
        // Education
        [
            'logo' => 'https://images.pexels.com/photos/256541/pexels-photo-256541.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&fit=crop',
            'cover' => 'https://images.pexels.com/photos/207692/pexels-photo-207692.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'gallery' => [
                'https://images.pexels.com/photos/256490/pexels-photo-256490.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/159844/cellular-education-classroom-159844.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/289737/pexels-photo-289737.jpeg?auto=compress&cs=tinysrgb&w=1000',
                'https://images.pexels.com/photos/1370296/pexels-photo-1370296.jpeg?auto=compress&cs=tinysrgb&w=1000',
            ],
        ],
    ];

    /** @var list<array<string, mixed>> */
    private array $reviewTemplates = [
        [
            'rating' => 5,
            'role' => 'Senior Specialist',
            'employment_status' => '2+ years in the role, current employee',
            'good_things' => 'Supportive leadership, clear goals, and real investment in training. The team culture is collaborative and welcoming.',
            'challenges' => 'Peak seasons can get busy; planning ahead helps.',
            'work_life_balance' => 4.5,
            'benefits_perks' => 4.2,
            'work_environment_culture' => 4.6,
            'career_growth_development' => 4.3,
            'management_leadership' => 4.4,
            'employee_support_wellbeing' => 4.1,
        ],
        [
            'rating' => 4,
            'role' => 'Team Lead',
            'employment_status' => '1–2 years in the role, current employee',
            'good_things' => 'Good progression paths and modern tools. Managers listen and act on feedback.',
            'challenges' => 'Cross-team communication could be tighter on larger projects.',
            'work_life_balance' => 4.0,
            'benefits_perks' => 3.8,
            'work_environment_culture' => 4.2,
            'career_growth_development' => 4.0,
            'management_leadership' => 3.9,
            'employee_support_wellbeing' => 3.8,
        ],
        [
            'rating' => 4,
            'role' => 'Associate',
            'employment_status' => 'Less than 1 year in the role, current employee',
            'good_things' => 'Friendly onboarding and helpful colleagues. You learn quickly on real work.',
            'challenges' => 'Documentation of internal processes could be more complete.',
            'work_life_balance' => 3.9,
            'benefits_perks' => 3.6,
            'work_environment_culture' => 4.1,
            'career_growth_development' => 3.7,
            'management_leadership' => 3.8,
            'employee_support_wellbeing' => 3.9,
        ],
        [
            'rating' => 3,
            'role' => 'Coordinator',
            'employment_status' => '1–2 years in the role, former employee',
            'good_things' => 'Solid workplace culture and fair day-to-day management.',
            'challenges' => 'Pay progression felt slower than expected for the workload.',
            'work_life_balance' => 3.4,
            'benefits_perks' => 3.2,
            'work_environment_culture' => 3.7,
            'career_growth_development' => 3.3,
            'management_leadership' => 3.4,
            'employee_support_wellbeing' => 3.5,
        ],
        [
            'rating' => 5,
            'role' => 'Operations Manager',
            'employment_status' => '2+ years in the role, current employee',
            'good_things' => 'Strong standards, respectful culture, and meaningful work. Great place to build a career.',
            'challenges' => 'Resource planning during busy periods needs more buffer.',
            'work_life_balance' => 4.2,
            'benefits_perks' => 4.0,
            'work_environment_culture' => 4.5,
            'career_growth_development' => 4.1,
            'management_leadership' => 4.2,
            'employee_support_wellbeing' => 4.0,
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
            $path = $uploadRoot.DIRECTORY_SEPARATOR.$dir;
            if (! is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        $companies = DB::table('companies')->orderBy('id')->get(['id', 'name', 'logo', 'cover_image', 'gallery_images', 'location']);
        $this->info('Seeding media + photos for '.$companies->count().' companies...');

        $ok = 0;
        foreach ($companies as $index => $company) {
            $pack = $this->packs[$index % count($this->packs)];
            $updates = [];

            $needsLogo = $force || blank($company->logo) || str_contains((string) $company->logo, 'demo-company-');
            $needsCover = $force || blank($company->cover_image);
            $gallery = $this->decodeGallery($company->gallery_images);
            $needsGallery = $force || count($gallery) < 3;

            if ($needsLogo) {
                $rel = "company-logos/company-{$company->id}-logo.jpg";
                if ($this->downloadTo($pack['logo'], $this->abs($uploadRoot, $rel))) {
                    $updates['logo'] = $mediaBase.'/'.$rel;
                } else {
                    $this->warn("Failed logo for #{$company->id}");
                }
            }

            if ($needsCover) {
                $rel = "company-gallery/company-{$company->id}-banner.jpg";
                if ($this->downloadTo($pack['cover'], $this->abs($uploadRoot, $rel))) {
                    $updates['cover_image'] = $mediaBase.'/'.$rel;
                } else {
                    $this->warn("Failed cover for #{$company->id}");
                }
            }

            if ($needsGallery) {
                $urls = [];
                foreach ($pack['gallery'] as $gIndex => $src) {
                    $rel = "company-gallery/company-{$company->id}-photo-".($gIndex + 1).'.jpg';
                    if ($this->downloadTo($src, $this->abs($uploadRoot, $rel))) {
                        $urls[] = $mediaBase.'/'.$rel;
                    }
                }
                if (count($urls) >= 3) {
                    $updates['gallery_images'] = json_encode($urls);
                } else {
                    $this->warn("Failed gallery for #{$company->id} (got ".count($urls).')');
                }
            }

            if ($updates !== []) {
                DB::table('companies')->where('id', $company->id)->update($updates);
                $this->line('✓ #'.$company->id.' '.$company->name.': '.implode(', ', array_keys($updates)));
                $ok++;
            } else {
                $this->line("- #{$company->id} {$company->name}: media OK");
            }
        }

        $this->info("Media updated for {$ok} companies.");

        if (! $this->option('skip-reviews') && Schema::hasTable('company_reviews')) {
            $this->seedReviews($companies);
        }

        return self::SUCCESS;
    }

    private function seedReviews($companies): void
    {
        $seekerIds = Schema::hasTable('job_seekers')
            ? DB::table('job_seekers')->orderBy('seeker_id')->limit(10)->pluck('seeker_id')->all()
            : [];

        $created = 0;
        foreach ($companies as $cIndex => $company) {
            $existing = DB::table('company_reviews')->where('company_id', $company->id)->count();
            if ($existing >= 3) {
                $this->line("- reviews #{$company->id}: already has {$existing}");
                continue;
            }

            // Rotate templates so each company gets a varied set
            $templates = array_values(array_merge(
                array_slice($this->reviewTemplates, $cIndex % count($this->reviewTemplates)),
                array_slice($this->reviewTemplates, 0, $cIndex % count($this->reviewTemplates))
            ));
            $templates = array_slice($templates, 0, 4);

            foreach ($templates as $i => $tpl) {
                $seekerId = $seekerIds !== [] ? $seekerIds[($cIndex + $i) % count($seekerIds)] : null;
                DB::table('company_reviews')->insert([
                    'company_id' => $company->id,
                    'seeker_id' => $seekerId,
                    'rating' => $tpl['rating'],
                    'work_life_balance' => $tpl['work_life_balance'],
                    'benefits_perks' => $tpl['benefits_perks'],
                    'work_environment_culture' => $tpl['work_environment_culture'],
                    'career_growth_development' => $tpl['career_growth_development'],
                    'management_leadership' => $tpl['management_leadership'],
                    'employee_support_wellbeing' => $tpl['employee_support_wellbeing'],
                    'role' => $tpl['role'],
                    'location' => $company->location ?: 'Victoria, Mahe',
                    'employment_status' => $tpl['employment_status'],
                    'good_things' => $tpl['good_things'],
                    'challenges' => $tpl['challenges'],
                    'helpful_count' => random_int(1, 18),
                    'created_at' => now()->subDays(random_int(5, 120)),
                    'updated_at' => now(),
                ]);
                $created++;
            }
            $this->line("✓ reviews #{$company->id} {$company->name}: +".count($templates));
        }

        $this->info("Created {$created} reviews.");
    }

    private function decodeGallery(mixed $raw): array
    {
        if (is_array($raw)) {
            return array_values(array_filter($raw));
        }
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);

            return is_array($decoded) ? array_values(array_filter($decoded)) : [];
        }

        return [];
    }

    private function abs(string $root, string $rel): string
    {
        return $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $rel);
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
