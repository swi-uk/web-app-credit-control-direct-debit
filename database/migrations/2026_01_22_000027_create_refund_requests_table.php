<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('payment_id')->nullable()->constrained('payments');
            $table->string('external_order_id', 255)->nullable();
            $table->decimal('amount_requested', 10, 2)->nullable();
            $table->text('reason');
            $table->enum('status', ['requested', 'approved', 'denied', 'processed'])->default('requested');
            $table->text('admin_note')->nullable();
            $table->dateTime('decided_at')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();

            $table->index(['merchant_id', 'status']);
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};
