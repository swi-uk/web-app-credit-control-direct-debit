<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->json('billing_address_json')->nullable();
            $table->string('external_woocommerce_user_id')->nullable();
            $table->string('status')->default('active');
            $table->string('lock_reason')->nullable();
            $table->timestamps();

            $table->unique(['merchant_id', 'email']);
            $table->index(['merchant_id', 'external_woocommerce_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
