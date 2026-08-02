<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employers', function (Blueprint $table) {
            $table->string('billing_card_brand', 32)->nullable()->after('coin_balance');
            $table->string('billing_card_last4', 4)->nullable()->after('billing_card_brand');
            $table->string('billing_card_exp', 7)->nullable()->after('billing_card_last4'); // MM/YYYY
        });
    }

    public function down(): void
    {
        Schema::table('employers', function (Blueprint $table) {
            $table->dropColumn(['billing_card_brand', 'billing_card_last4', 'billing_card_exp']);
        });
    }
};
