<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\JobSeeker;
use App\Models\TrainingProvider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ScoopJobSeekerSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'scoop.seeker@example.com'],
            [
                'name' => 'Alex Thompson',
                'password' => Hash::make('password123'),
                'user_type' => 'job_seeker',
                'phone' => '+2480000000',
                'is_active' => true,
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        JobSeeker::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => 'Alex',
                'last_name' => 'Thompson',
                'gender' => 'male',
                'date_of_birth' => '1995-01-15',
                'employment_status' => 'currently_employed',
                'highest_education' => 'bachelor',
                'location' => 'Mahe',
                'phone' => '+2480000000',
                'bio' => 'Scoop test job seeker profile',
                'job_preferences' => ['full-time', 'on-site', 'mid'],
                'expected_salary_min' => 9000,
                'expected_salary_max' => 15000,
                'hobbies' => ['Diving', 'Reading'],
            ]
        );

        TrainingProvider::updateOrCreate(
            ['name' => 'Scoop Training Hub'],
            [
                'subtitle' => 'Featured training partner',
                'courses_available' => 3,
                'tagline' => 'Learn skills that get you hired',
                'is_featured' => true,
            ]
        );

        Course::updateOrCreate(
            ['title' => 'Hospitality Essentials'],
            [
                'badge' => 'Popular',
                'badges' => ['Popular', 'Beginner'],
                'level' => 'Beginner',
                'duration' => '4 weeks',
                'format' => 'Online',
                'price' => 'Free',
                'provider' => 'Scoop Training Hub',
                'instructor' => 'Jane Doe',
                'seats' => 30,
                'start_date' => now()->addWeeks(2)->toDateString(),
                'phone' => '+2481111111',
                'email' => 'training@example.com',
                'overview' => 'Intro course for hospitality careers in Seychelles.',
                'is_active' => true,
            ]
        );

        Course::updateOrCreate(
            ['title' => 'Digital Marketing Basics'],
            [
                'badge' => 'New',
                'badges' => ['New'],
                'level' => 'Intermediate',
                'duration' => '6 weeks',
                'format' => 'Hybrid',
                'price' => 'SCR 1500',
                'provider' => 'Scoop Training Hub',
                'instructor' => 'John Smith',
                'seats' => 20,
                'start_date' => now()->addMonth()->toDateString(),
                'overview' => 'Practical digital marketing for job seekers.',
                'is_active' => true,
            ]
        );
    }
}
