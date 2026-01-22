<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('bureau_external_ref')->nullable();
        });
        Schema::table('mandates', function (Blueprint $table) {
            $table->string('bureau_external_ref')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('bureau_external_ref');
        });
        Schema::table('mandates', function (Blueprint $table) {
            $table->dropColumn('bureau_external_ref');
        });
    }
};
