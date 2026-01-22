<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_batch_id')->constrained('submission_batches');
            $table->enum('entity_type', ['mandate', 'payment']);
            $table->unsignedBigInteger('entity_id');
            $table->enum('status', ['pending', 'included', 'sent', 'failed'])->default('pending');
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_items');
    }
};
