<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchant_sites', function (Blueprint $table) {
            $table->enum('mode', ['test', 'live'])->default('test');
        });
    }

    public function down(): void
    {
        Schema::table('merchant_sites', function (Blueprint $table) {
            $table->dropColumn('mode');
        });
    }
};
