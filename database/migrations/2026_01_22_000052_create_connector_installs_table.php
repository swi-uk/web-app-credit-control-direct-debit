<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connector_installs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_site_id')->constrained('merchant_sites');
            $table->enum('connector', ['woocommerce', 'shopify', 'custom']);
            $table->enum('status', ['installed', 'revoked', 'error'])->default('installed');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->dateTime('installed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connector_installs');
    }
};
