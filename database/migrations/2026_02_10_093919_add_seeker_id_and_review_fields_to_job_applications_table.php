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
        Schema::table('job_applications', function (Blueprint $table) {
            // Check if seeker_id exists before adding
            if (!Schema::hasColumn('job_applications', 'seeker_id')) {
                $table->string('seeker_id')->nullable()->after('user_id');
                $table->foreign('seeker_id')->references('seeker_id')->on('job_seekers')->onDelete('cascade');
                $table->index('seeker_id');
            }
            
            // Add reviewed_by if it doesn't exist
            if (!Schema::hasColumn('job_applications', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')->constrained('users')->onDelete('set null');
            }
            
            // Add employer_notes if it doesn't exist
            if (!Schema::hasColumn('job_applications', 'employer_notes')) {
                $table->text('employer_notes')->nullable()->after('notes');
            }
            
            // Add composite index if seeker_id exists
            if (Schema::hasColumn('job_applications', 'seeker_id')) {
                $table->index(['seeker_id', 'status']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropForeign(['seeker_id']);
            $table->dropForeign(['reviewed_by']);
            $table->dropIndex(['seeker_id', 'status']);
            $table->dropIndex(['seeker_id']);
            $table->dropColumn(['seeker_id', 'reviewed_by', 'employer_notes']);
        });
    }
};
