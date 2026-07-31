<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('email_otps')) {
            Schema::create('email_otps', function (Blueprint $table) {
                $table->id();
                $table->string('email')->index();
                $table->string('code', 6);
                $table->string('purpose')->default('verify'); // verify | reset
                $table->timestamp('expires_at');
                $table->timestamp('consumed_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('job_seeker_settings')) {
            Schema::create('job_seeker_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('seeker_id')->unique();
                $table->boolean('app_notifications')->default(true);
                $table->boolean('email_notifications')->default(true);
                $table->boolean('job_alerts')->default(true);
                $table->boolean('application_updates')->default(true);
                $table->boolean('marketing_emails')->default(false);
                $table->timestamps();

                $table->foreign('seeker_id')
                    ->references('seeker_id')
                    ->on('job_seekers')
                    ->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('job_seeker_social_links')) {
            Schema::create('job_seeker_social_links', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('seeker_id')->index();
                $table->string('platform');
                $table->string('url');
                $table->timestamps();

                $table->foreign('seeker_id')
                    ->references('seeker_id')
                    ->on('job_seekers')
                    ->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('job_reports')) {
            Schema::create('job_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('job_advertisement_id');
                $table->text('reason')->nullable();
                $table->timestamps();

                $table->foreign('job_advertisement_id')
                    ->references('id')
                    ->on('job_advertisements')
                    ->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('tender_clarifications')) {
            Schema::create('tender_clarifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tender_ad_id');
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->text('message');
                $table->timestamps();

                $table->foreign('tender_ad_id')
                    ->references('id')
                    ->on('tender_ads')
                    ->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('tender_reports')) {
            Schema::create('tender_reports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tender_ad_id');
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->text('reason')->nullable();
                $table->timestamps();

                $table->foreign('tender_ad_id')
                    ->references('id')
                    ->on('tender_ads')
                    ->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('badge')->nullable();
                $table->json('badges')->nullable();
                $table->string('level')->nullable();
                $table->string('duration')->nullable();
                $table->string('format')->nullable();
                $table->string('price')->nullable();
                $table->string('image_url')->nullable();
                $table->string('provider')->nullable();
                $table->string('instructor')->nullable();
                $table->unsignedInteger('seats')->nullable();
                $table->date('start_date')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('overview')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('training_providers')) {
            Schema::create('training_providers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('subtitle')->nullable();
                $table->unsignedInteger('courses_available')->default(0);
                $table->string('tagline')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('job_seekers') && ! Schema::hasColumn('job_seekers', 'notification_settings')) {
            Schema::table('job_seekers', function (Blueprint $table) {
                // no-op placeholder kept for clarity
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('training_providers');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('tender_reports');
        Schema::dropIfExists('tender_clarifications');
        Schema::dropIfExists('job_reports');
        Schema::dropIfExists('job_seeker_social_links');
        Schema::dropIfExists('job_seeker_settings');
        Schema::dropIfExists('email_otps');
    }
};
