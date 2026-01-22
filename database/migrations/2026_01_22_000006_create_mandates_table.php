<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mandates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('reference');
            $table->text('account_holder_name');
            $table->text('sort_code');
            $table->text('account_number');
            $table->json('bank_address_json')->nullable();
            $table->timestamp('consent_timestamp');
            $table->string('consent_ip', 45);
            $table->string('consent_user_agent', 2000);
            $table->string('status');
            $table->timestamps();

            $table->unique(['merchant_id', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mandates');
    }
};
