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
        // Add working_hours column if it does not exist
        if (!Schema::hasColumn('companies', 'working_hours')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->string('working_hours', 255)->nullable()->after('culture_benefits');
            });
        }

        // Add workplace_description column if it does not exist
        if (!Schema::hasColumn('companies', 'workplace_description')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->text('workplace_description')->nullable()->after('working_hours');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank to avoid destructive schema changes in production.
    }
};

