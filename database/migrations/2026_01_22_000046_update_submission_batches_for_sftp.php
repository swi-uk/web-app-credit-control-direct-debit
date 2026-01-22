<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_batches', function (Blueprint $table) {
            $table->dropColumn('merchant_id');
            $table->string('format_version')->default('v1');
            $table->string('file_sha256', 64)->nullable();
            $table->unsignedInteger('record_count')->default(0);
            $table->dateTime('generated_at')->nullable();
            $table->dateTime('uploaded_at')->nullable();
            $table->text('last_error')->nullable();
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE submission_batches MODIFY status ENUM('pending','generated','uploaded','failed') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE submission_batches MODIFY status ENUM('pending','generated','sent','acknowledged','failed') DEFAULT 'pending'");
        }

        Schema::table('submission_batches', function (Blueprint $table) {
            $table->unsignedBigInteger('merchant_id')->nullable();
            $table->dropColumn([
                'format_version',
                'file_sha256',
                'record_count',
                'generated_at',
                'uploaded_at',
                'last_error',
            ]);
        });
    }
};
