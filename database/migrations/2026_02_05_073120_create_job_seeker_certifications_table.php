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
        Schema::create('job_seeker_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seeker_id')->constrained('job_seekers', 'seeker_id')->onDelete('cascade');
            $table->string('certification_name');
            $table->string('issuing_organization');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('certificate_file_path')->nullable();
            $table->string('credential_id')->nullable();
            $table->string('credential_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_seeker_certifications');
    }
};
