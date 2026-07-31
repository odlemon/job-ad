<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->unsignedInteger('coins_price');
            $table->unsignedInteger('scr_price');
            $table->unsignedSmallInteger('duration_days');
            $table->unsignedInteger('est_reach_min');
            $table->unsignedInteger('est_reach_max');
            $table->json('features')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_types');
    }
};
