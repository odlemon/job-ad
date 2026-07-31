<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the first admin user (by user_type) to the desired credentials.
        $admin = DB::table('users')
            ->where('user_type', 'admin')
            ->orderBy('id')
            ->first();

        if (! $admin) {
            return;
        }

        DB::table('users')
            ->where('id', $admin->id)
            ->update([
                'email' => 'admin@jobs.com',
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
                'is_active' => true,
                'is_verified' => true,
                'email_verified_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank as we don't know the previous admin credentials.
    }
};

