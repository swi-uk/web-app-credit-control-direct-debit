<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bacs_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bacs_report_id')->constrained('bacs_reports');
            $table->string('record_type', 20);
            $table->string('reference', 255)->nullable();
            $table->string('external_order_id', 255)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('code', 50)->nullable();
            $table->string('description', 255)->nullable();
            $table->json('raw_json');
            $table->enum('matched_entity_type', ['payment', 'mandate', 'none'])->default('none');
            $table->unsignedBigInteger('matched_entity_id')->nullable();
            $table->string('item_hash', 64);
            $table->timestamps();

            $table->index(['bacs_report_id', 'item_hash']);
            $table->unique(['bacs_report_id', 'item_hash']);
            $table->index(['matched_entity_type', 'matched_entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bacs_report_items');
    }
};
