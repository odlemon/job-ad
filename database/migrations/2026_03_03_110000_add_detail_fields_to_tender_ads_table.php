<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tender_ads', function (Blueprint $table) {
            // Overview tab
            $table->text('summary')->nullable()->after('description');
            $table->text('scope_of_work')->nullable()->after('summary');
            $table->json('requirements')->nullable()->after('scope_of_work');
            $table->string('tender_type', 32)->nullable()->after('reference_number'); // RFQ, RFP, EOI, ITB, etc.
            $table->string('sector')->nullable()->after('entity_name');
            $table->string('procuring_entity')->nullable()->after('sector');
            $table->string('country_region')->nullable()->after('procuring_entity');
            $table->decimal('budget_min', 14, 2)->nullable()->after('amount');
            $table->decimal('budget_max', 14, 2)->nullable()->after('budget_min');

            // Submission Details tab
            $table->string('submission_method')->nullable()->after('currency');
            $table->json('required_documents')->nullable()->after('submission_method');
            $table->json('eligibility_criteria')->nullable()->after('required_documents');

            // Attachments tab
            $table->json('attachments')->nullable()->after('eligibility_criteria');

            // Important Dates tab
            $table->date('published_date')->nullable()->after('attachments');
            $table->date('clarification_deadline')->nullable()->after('published_date');
            $table->date('submission_deadline')->nullable()->after('clarification_deadline');
        });
    }

    public function down(): void
    {
        Schema::table('tender_ads', function (Blueprint $table) {
            $table->dropColumn([
                'summary', 'scope_of_work', 'requirements', 'tender_type',
                'sector', 'procuring_entity', 'country_region', 'budget_min', 'budget_max',
                'submission_method', 'required_documents', 'eligibility_criteria',
                'attachments', 'published_date', 'clarification_deadline', 'submission_deadline',
            ]);
        });
    }
};
