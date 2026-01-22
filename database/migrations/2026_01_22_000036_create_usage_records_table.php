<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->dateTime('period_start');
            $table->dateTime('period_end');
            $table->enum('metric', ['mandates_active', 'debits_success', 'debits_failed', 'sms_sent', 'webhooks_sent']);
            $table->unsignedInteger('quantity');
            $table->timestamps();

            $table->index(['merchant_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_records');
    }
};
