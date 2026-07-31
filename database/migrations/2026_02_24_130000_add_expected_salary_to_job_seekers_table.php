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
            $table->unsignedInteger('expected_salary_min')->nullable()->after('job_preferences');
            $table->unsignedInteger('expected_salary_max')->nullable()->after('expected_salary_min');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_seekers', function (Blueprint $table) {
            $table->dropColumn(['expected_salary_min', 'expected_salary_max']);
        });
    }
};

