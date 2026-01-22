<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bureau_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_site_id')->constrained('merchant_sites');
            $table->enum('entity_type', ['mandate', 'payment']);
            $table->unsignedBigInteger('entity_id');
            $table->enum('request_type', ['submit', 'cancel', 'amend', 'refund']);
            $table->string('idempotency_key');
            $table->json('request_json');
            $table->json('response_json')->nullable();
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->unique(['merchant_site_id', 'idempotency_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bureau_requests');
    }
};
