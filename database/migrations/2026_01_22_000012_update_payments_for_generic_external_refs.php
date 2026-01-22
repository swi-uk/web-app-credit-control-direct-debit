<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('source_site_id')->nullable()->constrained('merchant_sites');
            $table->string('external_order_id', 255)->nullable();
            $table->string('external_order_key', 255)->nullable();
            $table->string('external_order_type', 50)->default('order');
            $table->dropColumn('woo_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['source_site_id']);
            $table->dropColumn(['source_site_id', 'external_order_id', 'external_order_key', 'external_order_type']);
            $table->string('woo_order_id');
        });
    }
};
