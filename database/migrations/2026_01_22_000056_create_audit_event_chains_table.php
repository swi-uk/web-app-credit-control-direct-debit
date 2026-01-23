<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_event_chains', function (Blueprint $table) {
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->string('last_hash', 64)->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_event_chains');
    }
};
