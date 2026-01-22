<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->nullable()->constrained('merchants');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->string('event_type');
            $table->text('message')->nullable();
            $table->json('payload_json')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_events');
    }
};
