<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'instagram')) {
                $table->string('instagram')->nullable()->after('twitter');
            }
            if (! Schema::hasColumn('companies', 'benefits')) {
                $table->json('benefits')->nullable()->after('culture_benefits');
            }
            if (! Schema::hasColumn('companies', 'company_values')) {
                $table->json('company_values')->nullable()->after('benefits');
            }
        });

        if (Schema::hasTable('job_categories') && ! Schema::hasColumn('job_categories', 'icon')) {
            Schema::table('job_categories', function (Blueprint $table) {
                $table->string('icon', 16)->nullable()->after('name');
            });
        }

        if (! Schema::hasTable('course_enrollments')) {
            Schema::create('course_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'course_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');

        if (Schema::hasTable('job_categories') && Schema::hasColumn('job_categories', 'icon')) {
            Schema::table('job_categories', function (Blueprint $table) {
                $table->dropColumn('icon');
            });
        }

        Schema::table('companies', function (Blueprint $table) {
            $cols = [];
            foreach (['instagram', 'benefits', 'company_values'] as $col) {
                if (Schema::hasColumn('companies', $col)) {
                    $cols[] = $col;
                }
            }
            if ($cols) {
                $table->dropColumn($cols);
            }
        });
    }
};
