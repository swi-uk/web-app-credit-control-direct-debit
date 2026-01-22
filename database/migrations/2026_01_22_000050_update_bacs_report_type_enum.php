<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bacs_reports MODIFY type ENUM('ARUDD','ADDACS','AUDDIS')");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bacs_reports MODIFY type ENUM('ARUDD','ADDACS')");
        }
    }
};
