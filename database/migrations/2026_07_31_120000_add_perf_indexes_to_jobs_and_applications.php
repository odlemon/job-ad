<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('job_advertisements', function (Blueprint $table) {
                $table->index(['status', 'published_at'], 'job_ads_status_published_at_index');
            });
        } catch (\Throwable) {
        }

        try {
            Schema::table('job_advertisements', function (Blueprint $table) {
                $table->index(['category_id', 'status'], 'job_ads_category_status_index');
            });
        } catch (\Throwable) {
        }

        try {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->index(['seeker_id', 'job_advertisement_id'], 'job_apps_seeker_job_index');
            });
        } catch (\Throwable) {
        }
    }

    public function down(): void
    {
        try {
            Schema::table('job_advertisements', function (Blueprint $table) {
                $table->dropIndex('job_ads_status_published_at_index');
            });
        } catch (\Throwable) {
        }

        try {
            Schema::table('job_advertisements', function (Blueprint $table) {
                $table->dropIndex('job_ads_category_status_index');
            });
        } catch (\Throwable) {
        }

        try {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->dropIndex('job_apps_seeker_job_index');
            });
        } catch (\Throwable) {
        }
    }
};
