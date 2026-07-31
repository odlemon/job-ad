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
        Schema::create('job_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_advertisements')->onDelete('cascade');
            $table->foreignId('seeker_id')->constrained('job_seekers', 'seeker_id')->onDelete('cascade');
            $table->string('platform', 64)->nullable()->comment('linkedin, twitter, facebook, etc.');
            $table->timestamp('shared_at')->useCurrent();
            $table->timestamps();

            $table->index(['job_id', 'seeker_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_shares');
    }
};
