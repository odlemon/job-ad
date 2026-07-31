<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_advertisements', function (Blueprint $table) {
            $table->string('island')->nullable()->after('location');
            $table->string('district')->nullable()->after('island');
            $table->string('work_environment')->nullable()->after('is_remote');
            $table->string('education_level')->nullable()->after('experience_level');
        });
    }

    public function down(): void
    {
        Schema::table('job_advertisements', function (Blueprint $table) {
            $table->dropColumn(['island', 'district', 'work_environment', 'education_level']);
        });
    }
};
