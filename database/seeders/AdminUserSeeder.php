<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@job.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@job.com',
                'password' => Hash::make('job123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
