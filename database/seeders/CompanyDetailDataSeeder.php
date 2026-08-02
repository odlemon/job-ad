<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyReview;
use App\Models\JobSeeker;
use Illuminate\Database\Seeder;

/**
 * Ensures every active company has full public-profile data for all detail tabs.
 */
class CompanyDetailDataSeeder extends Seeder
{
    public function run(): void
    {
        $benefitPalette = [
            ['title' => 'Competitive Salary', 'description' => 'Industry-leading compensation packages with performance bonuses', 'tone' => 'blue'],
            ['title' => 'Health Insurance', 'description' => 'Comprehensive medical, dental, and vision coverage', 'tone' => 'green'],
            ['title' => 'Learning & Development', 'description' => 'Training programs, workshops, and educational assistance', 'tone' => 'purple'],
            ['title' => 'Work-Life Balance', 'description' => 'Flexible working hours and remote work options', 'tone' => 'orange'],
            ['title' => 'Paid Time Off', 'description' => 'Generous vacation days, sick leave, and public holidays', 'tone' => 'cyan'],
            ['title' => 'Employee Wellness', 'description' => 'Gym memberships, wellness programs, and mental health support', 'tone' => 'pink'],
        ];

        $valueDefaults = [
            ['title' => 'Integrity', 'description' => 'We conduct business with honesty, transparency, and ethical principles', 'tone' => 'blue'],
            ['title' => 'Excellence', 'description' => 'We strive for excellence in everything we do and continuously improve', 'tone' => 'green'],
            ['title' => 'Innovation', 'description' => 'We embrace creativity and innovation to stay ahead of the curve', 'tone' => 'purple'],
        ];

        $galleryPool = [
            'https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/3184338/pexels-photo-3184338.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/3183197/pexels-photo-3183197.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/1181406/pexels-photo-1181406.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/1181396/pexels-photo-1181396.jpeg?auto=compress&cs=tinysrgb&w=800',
        ];

        $coverPool = [
            'https://images.pexels.com/photos/1833399/pexels-photo-1833399.jpeg?auto=compress&cs=tinysrgb&w=1260',
            'https://images.pexels.com/photos/264636/pexels-photo-264636.jpeg?auto=compress&cs=tinysrgb&w=1260',
            'https://images.pexels.com/photos/2294361/pexels-photo-2294361.jpeg?auto=compress&cs=tinysrgb&w=1260',
            'https://images.pexels.com/photos/3184325/pexels-photo-3184325.jpeg?auto=compress&cs=tinysrgb&w=1260',
        ];

        $seekerIds = JobSeeker::query()->limit(5)->pluck('seeker_id')->all();
        if (empty($seekerIds)) {
            // Reviews need a seeker_id; skip review insert if none exist
            $seekerIds = [];
        }

        Company::query()->where('is_active', true)->orderBy('id')->each(function (Company $company, $index) use (
            $benefitPalette, $valueDefaults, $galleryPool, $coverPool, $seekerIds
        ) {
            $name = $company->name;

            if (empty($company->description)) {
                $company->description = "{$name} is a growing organisation committed to quality, people, and long-term impact. We hire curious professionals and give them room to build meaningful careers.";
            }
            if (empty($company->workplace_description)) {
                $company->workplace_description = "Discover what makes {$name} a great place to work. We're committed to creating an environment where our team members can thrive, grow, and make meaningful contributions.";
            }
            if (empty($company->working_hours)) {
                $company->working_hours = '9:00 AM - 6:00 PM';
            }
            if (empty($company->email)) {
                $company->email = 'careers@'.\Illuminate\Support\Str::slug($name).'.example';
            }
            if (empty($company->phone)) {
                $company->phone = '+248 4'.str_pad((string) (100000 + $company->id), 6, '0', STR_PAD_LEFT);
            }
            if (empty($company->website)) {
                $company->website = 'www.'.\Illuminate\Support\Str::slug($name).'.com';
            }
            if (empty($company->location)) {
                $company->location = 'Victoria, Mahe';
            }
            if (empty($company->size)) {
                $company->size = ['11-50', '51-200', '201-500', '501-1000'][$company->id % 4];
            }
            if (empty($company->industry)) {
                $company->industry = 'Professional Services';
            }
            if (empty($company->founded_year)) {
                $company->founded_year = 2005 + ($company->id % 18);
            }
            if (empty($company->registration_number)) {
                $company->registration_number = '20'.(10 + ($company->id % 15)).str_pad((string) $company->id, 8, '0', STR_PAD_LEFT).'-K';
            }
            if (empty($company->cover_image)) {
                $company->cover_image = $coverPool[$index % count($coverPool)];
            }
            $gallery = $company->gallery_images;
            if (is_string($gallery)) {
                $gallery = json_decode($gallery, true) ?: [];
            }
            if (! is_array($gallery) || count($gallery) < 3) {
                $offset = $index % max(1, count($galleryPool) - 2);
                $company->gallery_images = array_slice($galleryPool, $offset, 4);
            }

            $benefits = $company->benefits;
            if (is_string($benefits)) {
                $benefits = json_decode($benefits, true) ?: [];
            }
            if (! is_array($benefits) || count($benefits) < 4) {
                $company->benefits = $benefitPalette;
            }

            $values = $company->company_values;
            if (is_string($values)) {
                $values = json_decode($values, true) ?: [];
            }
            if (! is_array($values) || count($values) < 3) {
                $company->company_values = $valueDefaults;
            }

            $faqs = $company->faqs;
            if (is_string($faqs)) {
                $faqs = json_decode($faqs, true) ?: [];
            }
            if (! is_array($faqs) || count($faqs) < 3) {
                $company->faqs = [
                    [
                        'question' => "What makes {$name} different from other companies in the industry?",
                        'answer' => 'We focus on quality, sustainability, and our people. We maintain high standards, invest in training, and create a welcoming environment where team members feel valued.',
                    ],
                    [
                        'question' => 'Do you offer career development opportunities?',
                        'answer' => 'Yes. We invest in training programs, certifications, and clear career progression paths. Many of our leads started in entry-level roles.',
                    ],
                    [
                        'question' => 'What are your working hours like?',
                        'answer' => $company->working_hours
                            ? "Typical hours are {$company->working_hours}. Flexible arrangements may be available depending on the role."
                            : 'We offer flexible scheduling for many roles. Specific hours depend on the position and location.',
                    ],
                    [
                        'question' => 'What benefits do employees receive?',
                        'answer' => 'Employees enjoy competitive pay, health cover where applicable, paid time off, and ongoing learning support. Full details are listed under Our Workplace.',
                    ],
                ];
            }
            if (empty($company->linkedin)) {
                $company->linkedin = 'https://www.linkedin.com/company/'.\Illuminate\Support\Str::slug($name);
            }
            if (empty($company->facebook)) {
                $company->facebook = 'https://www.facebook.com/'.\Illuminate\Support\Str::slug($name);
            }
            if (empty($company->instagram)) {
                $company->instagram = 'https://www.instagram.com/'.\Illuminate\Support\Str::slug($name);
            }
            if (! $company->verified_at && $company->id % 2 === 1) {
                $company->verified_at = now()->subMonths(3);
            }

            // Culture bullets stored as JSON list for Team Culture section
            if (empty($company->culture_benefits) || (is_string($company->culture_benefits) && ! str_starts_with(trim($company->culture_benefits), '['))) {
                $company->setAttribute('culture_benefits', json_encode([
                    ['title' => 'Collaborative Environment', 'description' => "We work together, share knowledge, and support each other's growth"],
                    ['title' => 'Innovation-Driven', 'description' => 'We encourage creative thinking and welcome new ideas from everyone'],
                    ['title' => 'Inclusive & Diverse', 'description' => 'We celebrate diversity and create opportunities for all backgrounds'],
                    ['title' => 'Results-Oriented', 'description' => 'We focus on delivering impactful outcomes while maintaining quality standards'],
                ]));
            }

            $company->save();

            // Seed a few reviews if company has none
            $existing = CompanyReview::query()->where('company_id', $company->id)->count();
            if ($existing === 0 && ! empty($seekerIds)) {
                $samples = [
                    ['rating' => 5, 'role' => 'Team Member', 'good' => 'Supportive colleagues and clear goals.', 'bad' => 'Peak seasons can get busy.'],
                    ['rating' => 4, 'role' => 'Supervisor', 'good' => 'Good progression and training.', 'bad' => 'Communication across teams could improve.'],
                    ['rating' => 3, 'role' => 'Associate', 'good' => 'Friendly culture and fair managers.', 'bad' => 'Pay progression is slower than expected.'],
                ];
                foreach ($samples as $i => $sample) {
                    CompanyReview::query()->create([
                        'company_id' => $company->id,
                        'seeker_id' => $seekerIds[$i % count($seekerIds)],
                        'rating' => $sample['rating'],
                        'work_life_balance' => 3.5 + ($i * 0.2),
                        'benefits_perks' => 3.4 + ($i * 0.1),
                        'work_environment_culture' => 3.8,
                        'career_growth_development' => 3.6,
                        'management_leadership' => 3.5,
                        'employee_support_wellbeing' => 3.7,
                        'role' => $sample['role'],
                        'location' => $company->location,
                        'employment_status' => 'Current employee',
                        'good_things' => $sample['good'],
                        'challenges' => $sample['bad'],
                        'helpful_count' => rand(0, 12),
                    ]);
                }
            }

            $this->command?->info("Enriched company detail data: {$company->name}");
        });
    }
}
