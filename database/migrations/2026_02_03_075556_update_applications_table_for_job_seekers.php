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
            // Add seeker_id column
            $table->foreignId('seeker_id')->nullable()->after('job_advertisement_id')
                ->constrained('job_seekers', 'seeker_id')->onDelete('cascade');
            
            // Add reviewed_by column (for employer user who reviewed)
            $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')
                ->constrained('users')->onDelete('set null');
            
            // Add employer_notes column
            $table->text('employer_notes')->nullable()->after('notes');
            
            // Update indexes
            $table->index('seeker_id');
            $table->index('reviewed_by');
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
            $table->dropColumn(['seeker_id', 'reviewed_by', 'employer_notes']);
        });
    }
};
