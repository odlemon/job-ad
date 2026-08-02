<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('career_tool_documents')) {
            return;
        }

        Schema::create('career_tool_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seeker_id');
            $table->string('type', 40); // resume, cover_letter, assessment, other
            $table->string('name');
            $table->longText('content');
            $table->json('meta')->nullable();
            $table->unsignedInteger('size_bytes')->default(0);
            $table->timestamps();

            $table->index(['seeker_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_tool_documents');
    }
};
