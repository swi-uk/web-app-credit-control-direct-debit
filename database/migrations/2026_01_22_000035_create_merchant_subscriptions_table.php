<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->foreignId('plan_id')->constrained('plans');
            $table->enum('status', ['active', 'trial', 'past_due', 'cancelled'])->default('active');
            $table->dateTime('trial_ends_at')->nullable();
            $table->dateTime('current_period_start');
            $table->dateTime('current_period_end');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_subscriptions');
    }
};
