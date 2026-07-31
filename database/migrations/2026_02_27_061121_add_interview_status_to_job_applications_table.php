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
            $table->enum('interview_status', ['pending', 'accepted', 'declined'])
                ->nullable()
                ->after('interview_notes');
            $table->text('interview_response_reason')
                ->nullable()
                ->after('interview_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['interview_status', 'interview_response_reason']);
        });
    }
};
