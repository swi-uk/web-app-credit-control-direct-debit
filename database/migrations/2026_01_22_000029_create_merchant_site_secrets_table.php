<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_site_secrets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_site_id')->constrained('merchant_sites');
            $table->text('sftp_private_key')->nullable();
            $table->text('sftp_password')->nullable();
            $table->text('api_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_site_secrets');
    }
};
