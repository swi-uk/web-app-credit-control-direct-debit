<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments');
            $table->string('event_type');
            $table->decimal('amount', 12, 2);
            $table->dateTime('occurred_at');
            $table->json('metadata_json')->nullable();
            $table->timestamps();

            $table->index(['payment_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_events');
    }
};
