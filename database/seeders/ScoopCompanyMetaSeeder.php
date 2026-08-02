<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JobCategory;
use Illuminate\Database\Seeder;

class ScoopCompanyMetaSeeder extends Seeder
{
    public function run(): void
    {
        $defaultFaqs = [
            [
                'question' => 'Do you offer career development opportunities?',
                'answer' => 'Yes. We invest in training, certifications, and clear progression paths so team members can grow into new roles.',
            ],
            [
                'question' => 'What are typical working hours?',
                'answer' => 'Core hours are published on our profile. Many roles offer flexible start times depending on the team.',
            ],
            [
                'question' => 'What benefits do employees receive?',
                'answer' => 'Benefits vary by role and are listed under Our Workplace. Common offerings include health cover, learning budgets, and paid time off.',
            ],
        ];

        $samples = [
            [
                'match' => ['slug' => 'tech-solutions-ltd'],
                'fallback_name' => 'Tech Solutions Ltd',
                'data' => [
                    'workplace_description' => 'Open-plan offices in Victoria with quiet focus rooms, a rooftop terrace, and hybrid work two days a week.',
                    'culture_benefits' => 'Learning stipend, flexible hours, and quarterly team offsites across the islands.',
                    'working_hours' => 'Mon–Fri 08:00–16:30 (flexible start)',
                    'founded_year' => 2014,
                    'registration_number' => '201401234567',
                    'verified_at' => now()->subMonths(6),
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
                    'faqs' => $defaultFaqs,
                ],
            ],
            [
                'match' => ['slug' => 'digital-innovations'],
                'fallback_name' => 'Digital Innovations',
                'data' => [
                    'workplace_description' => 'Beachside studio in Beau Vallon — collaborative desks, design lab, and client lounge.',
                    'culture_benefits' => 'Creative Fridays, wellness allowance, and paid volunteer days.',
                    'working_hours' => 'Mon–Fri 09:00–17:00',
                    'founded_year' => 2018,
                    'registration_number' => '201809876543',
                    'verified_at' => now()->subMonths(2),
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
                        ['title' => 'Transparency', 'description' => 'Open salary bands and clear growth paths.'],
                    ],
                    'faqs' => $defaultFaqs,
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

        // Enrich any remaining active companies with FAQs / registration if empty
        Company::query()->where('is_active', true)->each(function (Company $company) use ($defaultFaqs) {
            $dirty = false;
            if (empty($company->faqs)) {
                $company->faqs = array_map(function ($faq) use ($company) {
                    return [
                        'question' => str_replace('the company', $company->name, $faq['question']),
                        'answer' => $faq['answer'],
                    ];
                }, $defaultFaqs);
                $dirty = true;
            }
            if (empty($company->registration_number)) {
                $company->registration_number = 'REG-'.str_pad((string) $company->id, 8, '0', STR_PAD_LEFT);
                $dirty = true;
            }
            if (empty($company->company_values)) {
                $company->company_values = [
                    ['title' => 'People first', 'description' => 'We hire for character and grow for skill.'],
                    ['title' => 'Customer focus', 'description' => 'Outcomes for clients drive our daily work.'],
                ];
                $dirty = true;
            }
            if (empty($company->benefits)) {
                $company->benefits = [
                    ['title' => 'Competitive pay', 'description' => 'Market-aligned compensation packages.'],
                    ['title' => 'Paid leave', 'description' => 'Annual leave plus public holidays.'],
                ];
                $dirty = true;
            }
            if ($dirty) {
                $company->save();
            }
        });

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
