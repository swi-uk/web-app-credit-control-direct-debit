<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchant_sites', function (Blueprint $table) {
            $table->text('previous_webhook_secret')->nullable();
            $table->dateTime('webhook_secret_rotated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('merchant_sites', function (Blueprint $table) {
            $table->dropColumn(['previous_webhook_secret', 'webhook_secret_rotated_at']);
        });
    }
};
