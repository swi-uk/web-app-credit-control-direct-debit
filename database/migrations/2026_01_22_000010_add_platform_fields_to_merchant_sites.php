<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchant_sites', function (Blueprint $table) {
            $table->enum('platform', ['woocommerce', 'shopify', 'custom', 'api'])->default('woocommerce');
            $table->json('capabilities')->nullable();
            $table->json('settings_json')->nullable();
            $table->index('platform');
        });
    }

    public function down(): void
    {
        Schema::table('merchant_sites', function (Blueprint $table) {
            $table->dropIndex(['platform']);
            $table->dropColumn(['platform', 'capabilities', 'settings_json']);
        });
    }
};
