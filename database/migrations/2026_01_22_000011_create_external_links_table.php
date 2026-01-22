<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_site_id')->constrained('merchant_sites');
            $table->enum('entity_type', ['customer', 'order', 'payment', 'mandate']);
            $table->unsignedBigInteger('entity_id');
            $table->string('external_type', 50);
            $table->string('external_id', 255);
            $table->string('external_key', 255)->nullable();
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->index(['merchant_site_id', 'entity_type', 'entity_id']);
            $table->index(['merchant_site_id', 'external_type', 'external_id']);
            $table->unique(['merchant_site_id', 'external_type', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_links');
    }
};
