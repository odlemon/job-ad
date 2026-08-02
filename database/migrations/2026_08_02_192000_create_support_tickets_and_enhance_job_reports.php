<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('subject');
                $table->text('message');
                $table->string('status', 30)->default('open'); // open, in_progress, resolved, closed
                $table->string('priority', 20)->default('medium'); // low, medium, high
                $table->string('channel', 30)->default('ticket'); // ticket, live_chat, email
                $table->text('admin_response')->nullable();
                $table->unsignedBigInteger('assigned_to')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
                $table->index(['status', 'priority']);
                $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (Schema::hasTable('job_reports')) {
            Schema::table('job_reports', function (Blueprint $table) {
                if (! Schema::hasColumn('job_reports', 'category')) {
                    $table->string('category', 40)->nullable()->after('job_advertisement_id');
                }
                if (! Schema::hasColumn('job_reports', 'details')) {
                    $table->text('details')->nullable()->after('reason');
                }
                if (! Schema::hasColumn('job_reports', 'status')) {
                    $table->string('status', 30)->default('pending')->after('details');
                }
                if (! Schema::hasColumn('job_reports', 'admin_notes')) {
                    $table->text('admin_notes')->nullable()->after('status');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');

        if (Schema::hasTable('job_reports')) {
            Schema::table('job_reports', function (Blueprint $table) {
                foreach (['category', 'details', 'status', 'admin_notes'] as $col) {
                    if (Schema::hasColumn('job_reports', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
