<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('monthly_price', 10, 2);
            $table->unsignedInteger('included_mandates')->default(0);
            $table->unsignedInteger('included_debits')->default(0);
            $table->unsignedInteger('included_sms')->default(0);
            $table->decimal('per_debit_fee', 10, 4)->default(0);
            $table->decimal('per_mandate_fee', 10, 4)->default(0);
            $table->decimal('per_sms_fee', 10, 4)->default(0);
            $table->json('features_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
