<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_advertisements', function (Blueprint $table) {
            $table->boolean('hide_salary')->default(false)->after('currency');
            $table->dropColumn('application_deadline');
        });
    }

    public function down(): void
    {
        Schema::table('job_advertisements', function (Blueprint $table) {
            $table->dropColumn('hide_salary');
            $table->date('application_deadline')->nullable();
        });
    }
};
