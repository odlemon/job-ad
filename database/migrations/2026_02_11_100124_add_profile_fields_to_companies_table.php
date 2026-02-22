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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('logo');
            $table->integer('founded_year')->nullable()->after('location');
            $table->string('linkedin')->nullable()->after('founded_year');
            $table->string('twitter')->nullable()->after('linkedin');
            $table->text('culture_benefits')->nullable()->after('description');
            $table->json('gallery_images')->nullable()->after('cover_image');
            $table->timestamp('verified_at')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'cover_image',
                'founded_year',
                'linkedin',
                'twitter',
                'culture_benefits',
                'gallery_images',
                'verified_at',
            ]);
        });
    }
};
