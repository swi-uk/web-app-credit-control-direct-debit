<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchant_site_secrets', function (Blueprint $table) {
            $table->dropColumn(['sftp_private_key', 'sftp_password', 'api_token']);
            $table->string('key');
            $table->text('encrypted_value');
            $table->unique(['merchant_site_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::table('merchant_site_secrets', function (Blueprint $table) {
            $table->dropUnique(['merchant_site_id', 'key']);
            $table->dropColumn(['key', 'encrypted_value']);
            $table->text('sftp_private_key')->nullable();
            $table->text('sftp_password')->nullable();
            $table->text('api_token')->nullable();
        });
    }
};
