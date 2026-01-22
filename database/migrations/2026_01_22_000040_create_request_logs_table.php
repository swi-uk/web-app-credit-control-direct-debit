<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('correlation_id', 64);
            $table->string('method', 10);
            $table->string('path', 255);
            $table->unsignedSmallInteger('status_code');
            $table->unsignedBigInteger('merchant_site_id')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->integer('duration_ms')->nullable();
            $table->timestamps();

            $table->index(['merchant_site_id', 'status_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_logs');
    }
};
