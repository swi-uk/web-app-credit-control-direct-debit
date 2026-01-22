<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_links', function (Blueprint $table) {
            $table->string('external_order_type', 50)->default('order');
            $table->string('external_order_id', 255)->nullable();
            $table->string('external_order_key', 255)->nullable();
            $table->string('external_customer_id', 255)->nullable();
            $table->dropColumn(['woo_order_id', 'woo_order_key']);
        });
    }

    public function down(): void
    {
        Schema::table('order_links', function (Blueprint $table) {
            $table->string('woo_order_id');
            $table->string('woo_order_key');
            $table->dropColumn(['external_order_type', 'external_order_id', 'external_order_key', 'external_customer_id']);
        });
    }
};
