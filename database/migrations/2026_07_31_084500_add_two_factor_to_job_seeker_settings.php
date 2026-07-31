<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_seeker_settings') && ! Schema::hasColumn('job_seeker_settings', 'two_factor_enabled')) {
            Schema::table('job_seeker_settings', function (Blueprint $table) {
                $table->boolean('two_factor_enabled')->default(false)->after('marketing_emails');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('job_seeker_settings') && Schema::hasColumn('job_seeker_settings', 'two_factor_enabled')) {
            Schema::table('job_seeker_settings', function (Blueprint $table) {
                $table->dropColumn('two_factor_enabled');
            });
        }
    }
};
