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
        Schema::create('employers', function (Blueprint $table) {
            $table->id('employer_id');
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
            $table->string('company_name');
            $table->string('company_logo')->nullable();
            $table->text('company_description')->nullable();
            $table->string('industry')->nullable();
            $table->string('company_size')->nullable(); // e.g., '1-10', '11-50', '51-200', etc.
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->integer('coin_balance')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employers');
    }
};
