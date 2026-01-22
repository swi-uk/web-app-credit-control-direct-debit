<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_items', function (Blueprint $table) {
            $table->string('external_ref')->nullable();
            $table->text('last_error')->nullable()->change();
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE submission_items MODIFY status ENUM('queued','included','uploaded','failed') DEFAULT 'queued'");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE submission_items MODIFY status ENUM('pending','included','sent','failed') DEFAULT 'pending'");
        }

        Schema::table('submission_items', function (Blueprint $table) {
            $table->dropColumn('external_ref');
        });
    }
};
