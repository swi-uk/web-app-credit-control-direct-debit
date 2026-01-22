<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bureau_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_site_id')->constrained('merchant_sites');
            $table->string('event_type');
            $table->string('external_ref');
            $table->enum('entity_type', ['mandate', 'payment', 'unknown'])->default('unknown');
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->dateTime('occurred_at')->nullable();
            $table->json('payload_json');
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['merchant_site_id', 'event_type', 'external_ref', 'occurred_at'], 'bureau_events_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bureau_events');
    }
};
