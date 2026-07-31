<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coin_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('coins_amount');
            $table->decimal('price', 14, 2);
            $table->string('currency', 3)->default('SCR');
            $table->text('description')->nullable();
            $table->string('status', 24)->default('active')->index()->comment('active, inactive');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('icon', 64)->nullable()->comment('optional icon identifier');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coin_packages');
    }
};
