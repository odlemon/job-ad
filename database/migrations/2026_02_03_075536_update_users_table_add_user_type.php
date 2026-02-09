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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['job_seeker', 'employer', 'admin', 'institution', 'advertiser'])->nullable()->after('email');
            $table->string('phone')->nullable()->after('user_type');
            $table->boolean('is_active')->default(true)->after('phone');
            $table->boolean('is_verified')->default(false)->after('is_active');
            $table->timestamp('last_login')->nullable()->after('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_type', 'phone', 'is_active', 'is_verified', 'last_login']);
        });
    }
};
