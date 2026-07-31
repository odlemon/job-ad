<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyReview;
use Illuminate\Database\Seeder;

class CompanyReviewSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::query()->where('name', 'Lysps')->first()
            ?? Company::query()->where('is_active', true)->first();
        if (!$company) {
            $this->command->warn('No company found. Skipping company review seed.');
            return;
        }

        if (CompanyReview::query()->where('company_id', $company->id)->exists()) {
            $this->command->info('Reviews for ' . $company->name . ' already exist. Skipping.');
            return;
        }

        $reviews = [
            [
                'rating' => 4,
                'work_life_balance' => 4.0,
                'benefits_perks' => 3.5,
                'work_environment_culture' => 4.2,
                'career_growth_development' => 4.0,
                'management_leadership' => 3.8,
                'employee_support_wellbeing' => 3.5,
                'role' => 'Software Developer',
                'location' => 'Victoria, Mahé',
                'employment_status' => '1–2 years in the role, current employee',
                'good_things' => 'Flexible hours and remote options. Team is collaborative and the tech stack is modern. Management is open to new ideas.',
                'challenges' => 'Sometimes scope changes last-minute. Would benefit from more formal career progression paths.',
            ],
            [
                'rating' => 5,
                'work_life_balance' => 4.5,
                'benefits_perks' => 4.0,
                'work_environment_culture' => 4.5,
                'career_growth_development' => 4.2,
                'management_leadership' => 4.0,
                'employee_support_wellbeing' => 4.0,
                'role' => 'Project Manager',
                'location' => 'Victoria, Mahé',
                'employment_status' => '2+ years in the role, current employee',
                'good_things' => 'Clear goals, supportive leadership, and good work-life balance. Company invests in training and tools.',
                'challenges' => 'Resource allocation can be tight during peak periods.',
            ],
            [
                'rating' => 3,
                'work_life_balance' => 3.0,
                'benefits_perks' => 3.0,
                'work_environment_culture' => 3.5,
                'career_growth_development' => 3.2,
                'management_leadership' => 3.0,
                'employee_support_wellbeing' => 3.3,
                'role' => 'Junior Developer',
                'location' => 'Victoria, Mahé',
                'employment_status' => 'Less than 1 year in the role, former employee',
                'good_things' => 'Good learning environment and friendly colleagues. Exposure to real projects from day one.',
                'challenges' => 'Onboarding could be more structured. Salary was below market for the role at the time.',
            ],
            [
                'rating' => 4,
                'work_life_balance' => 4.0,
                'benefits_perks' => 3.8,
                'work_environment_culture' => 4.0,
                'career_growth_development' => 3.8,
                'management_leadership' => 3.8,
                'employee_support_wellbeing' => 3.8,
                'role' => 'UX Designer',
                'location' => 'Victoria, Mahé',
                'employment_status' => '1–2 years in the role, current employee',
                'good_things' => 'Creative freedom and cross-functional collaboration. Design is valued and included early in the process.',
                'challenges' => 'Design system and documentation could be more consistent across projects.',
            ],
            [
                'rating' => 3,
                'work_life_balance' => 3.2,
                'benefits_perks' => 3.2,
                'work_environment_culture' => 3.8,
                'career_growth_development' => 3.5,
                'management_leadership' => 3.2,
                'employee_support_wellbeing' => 3.0,
                'role' => 'Delivery Driver',
                'location' => 'Penang Island, Penang',
                'employment_status' => 'Less than 1 year in the role, former employee',
                'good_things' => 'Can meet many people; good for social interaction and staying active.',
                'challenges' => 'Parcel volume did not always translate to good income; rates were low when considering petrol and time. Petrol costs could not be claimed and had to be borne personally.',
            ],
        ];

        foreach ($reviews as $data) {
            CompanyReview::query()->create(array_merge($data, ['company_id' => $company->id]));
        }

        $this->command->info('Seeded ' . count($reviews) . ' reviews for company ' . $company->name . '.');
    }
}
