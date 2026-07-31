<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id', 32)->unique(); // e.g. NOT-2026-001
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('method', 32)->default('email'); // email, in_app
            $table->string('audience', 64)->default('all_employers'); // all_employers, all_job_seekers, all
            $table->string('category', 32)->nullable(); // update, alert, promotion
            $table->string('status', 32)->default('draft'); // draft, scheduled, sent
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
