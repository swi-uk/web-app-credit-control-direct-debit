<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->string('site_id');
            $table->string('base_url');
            $table->string('api_key_hash', 64)->index();
            $table->text('webhook_secret');
            $table->timestamps();

            $table->unique(['merchant_id', 'site_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_sites');
    }
};
