<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_tier_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->foreignId('tier_id')->constrained('credit_tiers');
            $table->unsignedInteger('min_successful_collections')->default(0);
            $table->unsignedInteger('max_bounces_60d')->default(999);
            $table->unsignedInteger('min_account_age_days')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_tier_rules');
    }
};
