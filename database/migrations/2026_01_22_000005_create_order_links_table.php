<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_site_id')->constrained('merchant_sites');
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('woo_order_id');
            $table->string('woo_order_key');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);
            $table->string('redirect_token_hash', 64)->unique();
            $table->text('return_success_url');
            $table->text('return_cancel_url');
            $table->enum('status', ['pending', 'completed', 'expired']);
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_links');
    }
};
