<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedInteger('coins_amount')->nullable()->after('amount')->comment('coins purchased when category=coins');
            $table->foreignId('coin_package_id')->nullable()->after('company_id')->constrained('coin_packages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['coin_package_id']);
            $table->dropColumn(['coins_amount', 'coin_package_id']);
        });
    }
};
