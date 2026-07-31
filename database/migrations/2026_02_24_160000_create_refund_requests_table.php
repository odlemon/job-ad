<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_id', 64)->unique()->comment('e.g. REF-2026-001');
            $table->unsignedBigInteger('employer_id')->nullable()->comment('employers.employer_id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->decimal('amount', 14, 2);
            $table->string('currency', 3)->default('SCR');
            $table->unsignedInteger('coins_equivalent')->nullable();
            $table->string('payment_method', 32)->nullable()->comment('card, mobile_money, bank');
            $table->string('type', 32)->index()->comment('job, advertisement, coins, tender');
            $table->string('status', 24)->default('pending')->index()->comment('pending, processing, approved, completed, rejected');
            $table->text('reason')->nullable()->comment('employer reason for refund');
            $table->text('admin_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};
