<?php
// database/migrations/2024_01_01_000006_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_code')->unique();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->enum('method', ['bank_transfer', 'e_wallet']);
            $table->string('bank_name')->nullable();
            $table->string('wallet_type')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('status', [
                'pending',
                'waiting_payment',
                'pending_verification',
                'paid',
                'failed',
                'expired'
            ])->default('waiting_payment');
            $table->string('payment_proof')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('payment_code');
            $table->index('status');
            $table->index('expired_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};