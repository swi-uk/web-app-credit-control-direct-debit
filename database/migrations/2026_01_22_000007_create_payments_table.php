<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('mandate_id')->constrained('mandates');
            $table->string('woo_order_id');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);
            $table->date('due_date');
            $table->enum('status', [
                'scheduled',
                'submitted',
                'processing',
                'collected',
                'unpaid_returned',
                'retry_scheduled',
                'failed_final',
                'cancelled',
            ]);
            $table->unsignedInteger('retry_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
