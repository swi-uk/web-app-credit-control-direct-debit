<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('partner_merchants', function (Blueprint $table) {
            $table->foreignId('partner_id')->constrained('partners');
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->enum('commission_type', ['percentage', 'flat']);
            $table->decimal('commission_value', 10, 4);
            $table->timestamps();

            $table->unique(['partner_id', 'merchant_id']);
        });

        Schema::create('partner_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners');
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->dateTime('period_start');
            $table->dateTime('period_end');
            $table->enum('basis', ['debits_success', 'volume']);
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['calculated', 'approved', 'paid'])->default('calculated');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_commissions');
        Schema::dropIfExists('partner_merchants');
        Schema::dropIfExists('partners');
    }
};
