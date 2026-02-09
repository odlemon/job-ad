<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_seekers', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('location');
            $table->string('address')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('address');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->enum('employment_status', ['currently_employed', 'unemployed', 'student', 'self_employed', 'retired'])->nullable()->after('date_of_birth');
            $table->string('highest_education')->nullable()->after('employment_status');
            $table->boolean('driving_license')->default(false)->after('highest_education');
            $table->date('license_issued_date')->nullable()->after('driving_license');
            $table->json('job_preferences')->nullable()->after('license_issued_date'); // ['full_time', 'part_time', 'contract']
            $table->string('linkedin_url')->nullable()->after('job_preferences');
            $table->string('website_url')->nullable()->after('linkedin_url');
            $table->boolean('public_profile')->default(true)->after('website_url');
            $table->boolean('open_to_opportunities')->default(true)->after('public_profile');
            $table->json('hobbies')->nullable()->after('open_to_opportunities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_seekers', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'address', 'gender', 'date_of_birth', 'employment_status',
                'highest_education', 'driving_license', 'license_issued_date',
                'job_preferences', 'linkedin_url', 'website_url',
                'public_profile', 'open_to_opportunities', 'hobbies'
            ]);
        });
    }
};
