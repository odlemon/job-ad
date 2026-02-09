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
        Schema::create('job_advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('job_categories')->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            $table->string('employment_type')->nullable(); // e.g., 'full-time', 'part-time', 'contract', 'freelance'
            $table->string('experience_level')->nullable(); // e.g., 'entry', 'mid', 'senior', 'executive'
            $table->string('salary_min')->nullable();
            $table->string('salary_max')->nullable();
            $table->string('currency')->default('USD');
            $table->string('location')->nullable();
            $table->boolean('is_remote')->default(false);
            $table->date('application_deadline')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->enum('status', ['draft', 'published', 'closed', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('published_at');
            $table->index(['company_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_advertisements');
    }
};
