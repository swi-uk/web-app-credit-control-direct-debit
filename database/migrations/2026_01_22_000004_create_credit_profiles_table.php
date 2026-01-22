<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->unique();
            $table->decimal('limit_amount', 12, 2)->default(0);
            $table->decimal('current_exposure_amount', 12, 2)->default(0);
            $table->unsignedInteger('days_max')->default(14);
            $table->unsignedInteger('days_default')->default(14);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_profiles');
    }
};
