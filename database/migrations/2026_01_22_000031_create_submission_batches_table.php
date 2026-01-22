<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->foreignId('merchant_site_id')->constrained('merchant_sites');
            $table->enum('type', ['mandate', 'payment']);
            $table->enum('status', ['pending', 'generated', 'sent', 'acknowledged', 'failed'])->default('pending');
            $table->string('file_path')->nullable();
            $table->string('external_ref')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_batches');
    }
};
