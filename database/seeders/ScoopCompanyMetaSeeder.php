<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JobCategory;
use Illuminate\Database\Seeder;

class ScoopCompanyMetaSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'match' => ['slug' => 'tech-solutions-ltd'],
                'fallback_name' => 'Tech Solutions Ltd',
                'data' => [
                    'workplace_description' => 'Open-plan offices in Victoria with quiet focus rooms, a rooftop terrace, and hybrid work two days a week.',
                    'culture_benefits' => 'Learning stipend, flexible hours, and quarterly team offsites across the islands.',
                    'working_hours' => 'Mon–Fri 08:00–16:30 (flexible start)',
                    'linkedin' => 'https://www.linkedin.com/company/tech-solutions-seychelles',
                    'facebook' => 'https://www.facebook.com/techsolutions.sc',
                    'twitter' => 'https://twitter.com/techsolutions_sc',
                    'instagram' => 'https://www.instagram.com/techsolutions.sc',
                    'benefits' => [
                        ['title' => 'Health cover', 'description' => 'Private medical for you and dependents', 'icon' => 'heart'],
                        ['title' => 'Learning budget', 'description' => 'SCR 5,000 / year for courses & conferences', 'icon' => 'book'],
                        ['title' => 'Hybrid work', 'description' => 'Up to 2 remote days per week', 'icon' => 'home'],
                    ],
                    'company_values' => [
                        ['title' => 'Craft', 'description' => 'We ship quality software and mentor juniors.'],
                        ['title' => 'Island pace, global standards', 'description' => 'Local roots with international engineering practices.'],
                    ],
                ],
            ],
            [
                'match' => ['slug' => 'digital-innovations'],
                'fallback_name' => 'Digital Innovations',
                'data' => [
                    'workplace_description' => 'Beachside studio in Beau Vallon — collaborative desks, design lab, and client lounge.',
                    'culture_benefits' => 'Creative Fridays, wellness allowance, and paid volunteer days.',
                    'working_hours' => 'Mon–Fri 09:00–17:00',
                    'linkedin' => 'https://www.linkedin.com/company/digital-innovations-sc',
                    'facebook' => null,
                    'twitter' => null,
                    'instagram' => 'https://www.instagram.com/digitalinnovations.sc',
                    'benefits' => [
                        ['title' => 'Wellness', 'description' => 'Gym or wellness stipend each quarter', 'icon' => 'sparkles'],
                        ['title' => 'Gear', 'description' => 'MacBook + dual monitors on day one', 'icon' => 'laptop'],
                    ],
                    'company_values' => [
                        ['title' => 'Design-led', 'description' => 'Every product starts with the user.'],
                        ['title' => 'Transparency', 'description' => 'Open salaries bands and clear growth paths.'],
                    ],
                ],
            ],
        ];

        foreach ($samples as $sample) {
            $company = Company::query()->where($sample['match'])->first()
                ?? Company::query()->where('name', 'like', '%'.$sample['fallback_name'].'%')->first()
                ?? Company::query()->where('is_active', true)->first();

            if (! $company) {
                continue;
            }

            $company->fill($sample['data']);
            $company->save();
            $this->command?->info("Scoop company meta seeded: {$company->name} (#{$company->id})");
        }

        $iconMap = [
            'Hospitality' => '🍽️',
            'Tourism' => '🏝️',
            'Technology' => '💻',
            'Software' => '💻',
            'Education' => '🎓',
            'Healthcare' => '🏥',
            'Construction' => '🏗️',
            'Administration' => '📄',
            'Finance' => '💰',
            'Sales' => '🛒',
            'Customer' => '🎧',
            'Transport' => '📦',
            'Logistics' => '📦',
            'Government' => '🏛️',
        ];

        JobCategory::query()->whereNull('icon')->orWhere('icon', '')->each(function (JobCategory $cat) use ($iconMap) {
            foreach ($iconMap as $needle => $emoji) {
                if (stripos($cat->name, $needle) !== false) {
                    $cat->icon = $emoji;
                    $cat->save();
                    break;
                }
            }
        });
    }
}
