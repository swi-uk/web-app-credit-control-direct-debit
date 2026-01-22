<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['merchant_id', 'external_woocommerce_user_id']);
            $table->dropColumn('external_woocommerce_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('external_woocommerce_user_id')->nullable();
            $table->index(['merchant_id', 'external_woocommerce_user_id']);
        });
    }
};
