<?php

use App\Models\JobSeeker;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Move existing cv_file_path from job_seekers into job_seeker_documents.
     */
    public function up(): void
    {
        $seekers = JobSeeker::whereNotNull('cv_file_path')->where('cv_file_path', '!=', '')->get(['seeker_id', 'cv_file_path']);
        foreach ($seekers as $seeker) {
            DB::table('job_seeker_documents')->insert([
                'seeker_id' => $seeker->seeker_id,
                'name' => 'Resume',
                'file_path' => $seeker->cv_file_path,
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to remove rows; table drop is handled by create migration's down()
    }
};
