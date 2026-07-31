<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_advertisement_id')->constrained('job_advertisements')->onDelete('cascade');
            $table->foreignId('campaign_type_id')->constrained('campaign_types')->onDelete('cascade');
            $table->unsignedSmallInteger('duration_days');
            $table->string('status')->default('pending'); // pending, active, expired
            $table->string('payment_method')->nullable(); // coin, card, lpo
            $table->timestamp('launched_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['job_advertisement_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_campaigns');
    }
};
