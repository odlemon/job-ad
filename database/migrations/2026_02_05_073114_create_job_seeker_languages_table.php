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
        Schema::create('job_seeker_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seeker_id')->constrained('job_seekers', 'seeker_id')->onDelete('cascade');
            $table->string('language');
            $table->enum('proficiency_level', ['basic', 'conversational', 'fluent', 'native'])->default('conversational');
            $table->timestamps();
            $table->unique(['seeker_id', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_seeker_languages');
    }
};
