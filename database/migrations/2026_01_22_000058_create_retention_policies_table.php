<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retention_policies', function (Blueprint $table) {
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->unsignedInteger('mandates_retention_days')->default(3650);
            $table->unsignedInteger('payments_retention_days')->default(3650);
            $table->unsignedInteger('documents_retention_days')->default(3650);
            $table->unsignedInteger('pii_mask_after_days')->default(365);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retention_policies');
    }
};
