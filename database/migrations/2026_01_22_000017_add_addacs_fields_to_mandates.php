<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mandates', function (Blueprint $table) {
            $table->string('addacs_code', 50)->nullable();
            $table->string('addacs_description', 255)->nullable();
            $table->dateTime('reported_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('mandates', function (Blueprint $table) {
            $table->dropColumn(['addacs_code', 'addacs_description', 'reported_at']);
        });
    }
};
