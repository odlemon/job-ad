<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id', 64)->unique()->nullable()->comment('e.g. TXN-001234');
            $table->string('category', 32)->index()->comment('job_ads, tender_ads, website_ads, course_ads, coins, lpo');
            $table->string('payer_name')->nullable()->comment('Company or payer display name');
            $table->string('description')->nullable()->comment('e.g. Featured Job Posting - Senior Developer');
            $table->string('payment_method', 32)->nullable()->comment('credit_card, bank_transfer, lpo, coin');
            $table->decimal('amount', 14, 2);
            $table->string('currency', 3)->default('SCR');
            $table->string('status', 24)->default('pending')->index()->comment('completed, pending, failed');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->timestamps();

            $table->index(['category', 'status']);
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
