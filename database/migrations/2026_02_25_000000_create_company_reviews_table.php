<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('seeker_id')->nullable();
            $table->unsignedTinyInteger('rating'); // 1-5 overall
            $table->decimal('work_life_balance', 3, 1)->nullable();
            $table->decimal('benefits_perks', 3, 1)->nullable();
            $table->decimal('work_environment_culture', 3, 1)->nullable();
            $table->decimal('career_growth_development', 3, 1)->nullable();
            $table->decimal('management_leadership', 3, 1)->nullable();
            $table->decimal('employee_support_wellbeing', 3, 1)->nullable();
            $table->string('role')->nullable();
            $table->string('location')->nullable();
            $table->string('employment_status')->nullable(); // e.g. "Less than 1 year in the role, former employee"
            $table->text('good_things')->nullable();
            $table->text('challenges')->nullable();
            $table->unsignedInteger('helpful_count')->default(0);
            $table->timestamps();

            $table->index(['company_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_reviews');
    }
};
