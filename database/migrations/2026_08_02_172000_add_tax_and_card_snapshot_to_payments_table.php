<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('tax_amount', 14, 2)->nullable()->after('amount');
            $table->string('card_brand', 32)->nullable()->after('payment_method');
            $table->string('card_last4', 4)->nullable()->after('card_brand');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'card_brand', 'card_last4']);
        });
    }
};
