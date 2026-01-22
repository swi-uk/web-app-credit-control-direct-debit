<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bacs_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->enum('type', ['ARUDD', 'ADDACS']);
            $table->enum('source', ['upload', 'local']);
            $table->string('original_filename');
            $table->string('file_path');
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bacs_reports');
    }
};
