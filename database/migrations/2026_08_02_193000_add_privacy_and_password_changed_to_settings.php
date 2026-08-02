<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_seeker_settings')) {
            Schema::table('job_seeker_settings', function (Blueprint $table) {
                if (! Schema::hasColumn('job_seeker_settings', 'show_activity_status')) {
                    $table->boolean('show_activity_status')->default(true)->after('two_factor_enabled');
                }
                if (! Schema::hasColumn('job_seeker_settings', 'allow_contact_by_recruiters')) {
                    $table->boolean('allow_contact_by_recruiters')->default(true)->after('show_activity_status');
                }
            });
        }

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'password_changed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('password_changed_at')->nullable()->after('password');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('job_seeker_settings')) {
            Schema::table('job_seeker_settings', function (Blueprint $table) {
                foreach (['show_activity_status', 'allow_contact_by_recruiters'] as $col) {
                    if (Schema::hasColumn('job_seeker_settings', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'password_changed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('password_changed_at');
            });
        }
    }
};
