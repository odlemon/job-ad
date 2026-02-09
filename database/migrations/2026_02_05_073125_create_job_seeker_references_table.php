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
        Schema::create('job_seeker_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seeker_id')->constrained('job_seekers', 'seeker_id')->onDelete('cascade');
            $table->string('reference_name');
            $table->string('title');
            $table->string('company');
            $table->enum('relationship', ['former_manager', 'former_colleague', 'former_client', 'academic', 'other'])->default('other');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_seeker_references');
    }
};
