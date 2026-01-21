<?php
// database/migrations/2024_01_01_000003_create_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 15, 2);
            
            // Order status
            $table->enum('status', [
                'pending', 
                'processing', 
                'shipped', 
                'delivered', 
                'cancelled'
            ])->default('pending');
            
            $table->text('shipping_address');
            $table->string('customer_name');
            $table->string('customer_phone');
            
            // Payment method - HAPUS 'cod' karena kita hanya pakai bank_transfer & e_wallet
            $table->enum('payment_method', [
                'bank_transfer', 
                'e_wallet'
                // 'cod' dihapus karena tidak digunakan
            ]);
            
            // Payment status - PERBAIKI INI!
            $table->enum('payment_status', [
                'pending', 
                'waiting_payment',    // TAMBAHKAN
                'pending_verification', // TAMBAHKAN
                'paid', 
                'failed',
                'expired',            // TAMBAHKAN
                'cancelled'           // TAMBAHKAN
            ])->default('pending');
            
            // Kolom baru untuk detail pembayaran
            $table->string('bank_name')->nullable();        // TAMBAHKAN: BCA, BRI, Mandiri, dll
            $table->string('wallet_type')->nullable();      // TAMBAHKAN: OVO, DANA, GoPay, dll
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Index untuk performa query
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};