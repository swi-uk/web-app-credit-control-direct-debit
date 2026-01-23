<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->foreignId('customer_id')->constrained('customers');
            $table->unsignedInteger('score');
            $table->enum('band', ['low', 'medium', 'high', 'critical']);
            $table->json('factors_json')->nullable();
            $table->dateTime('calculated_at');
            $table->timestamps();

            $table->index(['merchant_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};
