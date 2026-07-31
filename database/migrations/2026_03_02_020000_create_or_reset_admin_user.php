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
        // Try to find an existing admin by user_type.
        $admin = DB::table('users')
            ->where('user_type', 'admin')
            ->orderBy('id')
            ->first();

        if ($admin) {
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

            return;
        }

        // If no admin user_type exists yet, either update by email or create a new one.
        $byEmail = DB::table('users')
            ->where('email', 'admin@jobs.com')
            ->orderBy('id')
            ->first();

        if ($byEmail) {
            DB::table('users')
                ->where('id', $byEmail->id)
                ->update([
                    'user_type' => 'admin',
                    'name' => 'Admin',
                    'password' => bcrypt('admin123'),
                    'is_active' => true,
                    'is_verified' => true,
                    'email_verified_at' => now(),
                ]);

            return;
        }

        // Otherwise, insert a fresh admin row.
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@jobs.com',
            'user_type' => 'admin',
            'password' => bcrypt('admin123'),
            'is_active' => true,
            'is_verified' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do not delete the admin automatically; no down action.
    }
};

