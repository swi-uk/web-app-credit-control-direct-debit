<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dateTime('advance_notice_sent_at')->nullable();
            $table->string('advance_notice_channel', 10)->nullable();
            $table->string('failure_code', 50)->nullable();
            $table->string('failure_description', 255)->nullable();
            $table->dateTime('reported_at')->nullable();
            $table->dateTime('next_retry_at')->nullable();

            $table->index(['status', 'next_retry_at']);
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['status', 'next_retry_at']);
            $table->dropIndex(['due_date']);
            $table->dropColumn([
                'advance_notice_sent_at',
                'advance_notice_channel',
                'failure_code',
                'failure_description',
                'reported_at',
                'next_retry_at',
            ]);
        });
    }
};
