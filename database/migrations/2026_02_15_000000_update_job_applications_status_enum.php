<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        
        // Map old statuses to new ones first (works for both SQLite and MySQL)
        DB::statement("UPDATE job_applications SET status = 'applied' WHERE status = 'pending'");
        DB::statement("UPDATE job_applications SET status = 'in_review' WHERE status = 'reviewing'");
        DB::statement("UPDATE job_applications SET status = 'interview' WHERE status = 'shortlisted'");
        DB::statement("UPDATE job_applications SET status = 'offered' WHERE status = 'hired'");
        // 'rejected' stays the same
        
        // For MySQL, update the ENUM definition
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('applied', 'in_review', 'interview', 'offered', 'rejected') DEFAULT 'applied'");
        }
        // For SQLite, ENUMs are stored as TEXT, so no schema change needed
        // The values have already been updated above
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        
        // Map new statuses back to old ones
        DB::statement("UPDATE job_applications SET status = 'pending' WHERE status = 'applied'");
        DB::statement("UPDATE job_applications SET status = 'reviewing' WHERE status = 'in_review'");
        DB::statement("UPDATE job_applications SET status = 'shortlisted' WHERE status = 'interview'");
        DB::statement("UPDATE job_applications SET status = 'hired' WHERE status = 'offered'");
        
        // For MySQL, restore the old ENUM definition
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('pending', 'reviewing', 'shortlisted', 'rejected', 'hired') DEFAULT 'pending'");
        }
        // For SQLite, no schema change needed
    }
};
