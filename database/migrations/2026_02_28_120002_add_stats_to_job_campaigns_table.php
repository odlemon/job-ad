<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('job_campaigns', 'views_count')) {
                $table->unsignedInteger('views_count')->default(0);
            }
            if (!Schema::hasColumn('job_campaigns', 'clicks_count')) {
                $table->unsignedInteger('clicks_count')->default(0);
            }
            if (!Schema::hasColumn('job_campaigns', 'shares_count')) {
                $table->unsignedInteger('shares_count')->default(0);
            }
            if (!Schema::hasColumn('job_campaigns', 'saved_count')) {
                $table->unsignedInteger('saved_count')->default(0);
            }
            if (!Schema::hasColumn('job_campaigns', 'messages_count')) {
                $table->unsignedInteger('messages_count')->default(0);
            }
            if (!Schema::hasColumn('job_campaigns', 'invitation_sent_count')) {
                $table->unsignedInteger('invitation_sent_count')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_campaigns', function (Blueprint $table) {
            $cols = ['views_count', 'clicks_count', 'shares_count', 'saved_count', 'messages_count', 'invitation_sent_count'];
            if (Schema::hasColumn('job_campaigns', 'views_count')) {
                $table->dropColumn($cols);
            }
        });
    }
};
