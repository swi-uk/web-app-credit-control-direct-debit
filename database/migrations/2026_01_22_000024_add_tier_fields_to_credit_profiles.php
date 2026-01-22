<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credit_profiles', function (Blueprint $table) {
            $table->foreignId('credit_tier_id')->nullable()->constrained('credit_tiers');
            $table->boolean('manual_tier_override')->default(false);
            $table->boolean('manual_limit_override')->default(false);
            $table->boolean('manual_days_override')->default(false);
            $table->dateTime('tier_assigned_at')->nullable();
            $table->unsignedInteger('successful_collections')->default(0);
            $table->unsignedInteger('bounces_60d')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('credit_profiles', function (Blueprint $table) {
            $table->dropForeign(['credit_tier_id']);
            $table->dropColumn([
                'credit_tier_id',
                'manual_tier_override',
                'manual_limit_override',
                'manual_days_override',
                'tier_assigned_at',
                'successful_collections',
                'bounces_60d',
            ]);
        });
    }
};
