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
        Schema::create('job_seekers', function (Blueprint $table) {
            $table->id('seeker_id');
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('profile_photo')->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('cv_file_path')->nullable();
            $table->timestamp('cv_uploaded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_seekers');
    }
};
