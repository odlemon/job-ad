<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'registration_number')) {
                $table->string('registration_number')->nullable()->after('founded_year');
            }
            if (! Schema::hasColumn('companies', 'faqs')) {
                $table->json('faqs')->nullable()->after('company_values');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'faqs')) {
                $table->dropColumn('faqs');
            }
            if (Schema::hasColumn('companies', 'registration_number')) {
                $table->dropColumn('registration_number');
            }
        });
    }
};
