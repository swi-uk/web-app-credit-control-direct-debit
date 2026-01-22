<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->string('email');
            $table->string('role')->default('billing');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['merchant_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_contacts');
    }
};
