<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bacs_reports', function (Blueprint $table) {
            $table->string('remote_id')->nullable();
            $table->string('file_hash', 64)->nullable();
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bacs_reports MODIFY source ENUM('upload','local','bureau')");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bacs_reports MODIFY source ENUM('upload','local')");
        }

        Schema::table('bacs_reports', function (Blueprint $table) {
            $table->dropColumn(['remote_id', 'file_hash']);
        });
    }
};
