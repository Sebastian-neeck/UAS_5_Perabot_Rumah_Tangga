<?php
// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_code',
        'order_id',
        'method',
        'bank_name',
        'wallet_type',
        'amount',
        'status',
        'payment_proof',
        'paid_at',
        'expired_at',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime'
    ];

    protected $appends = [
        'formatted_amount',
        'method_text',
        'status_text',
        'status_color',
        'is_expired',
        'time_left',
        'payment_proof_url'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Method text
    public function getMethodTextAttribute()
    {
        $methods = [
            'bank_transfer' => 'Transfer Bank',
            'e_wallet' => 'E-Wallet'
        ];
        return $methods[$this->method] ?? $this->method;
    }

    // Bank/Wallet name
    public function getProviderNameAttribute()
    {
        if ($this->method === 'bank_transfer') {
            return $this->bank_name;
        }
        return $this->wallet_type;
    }

    // Status text
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Menunggu',
            'waiting_payment' => 'Menunggu Pembayaran',
            'pending_verification' => 'Menunggu Verifikasi',
            'paid' => 'Dibayar',
            'failed' => 'Gagal',
            'expired' => 'Kadaluarsa'
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    // Status color
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'amber',
            'waiting_payment' => 'amber',
            'pending_verification' => 'blue',
            'paid' => 'green',
            'failed' => 'red',
            'expired' => 'red'
        ];
        
        return $colors[$this->status] ?? 'gray';
    }

    // Format amount
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Cek apakah expired
    public function getIsExpiredAttribute()
    {
        return $this->expired_at && $this->expired_at->isPast() && 
               in_array($this->status, ['pending', 'waiting_payment']);
    }

    // Waktu tersisa
    public function getTimeLeftAttribute()
    {
        if (!$this->expired_at || $this->is_expired) {
            return '00:00:00';
        }
        
        $diff = $this->expired_at->diff(now());
        return sprintf('%02d:%02d:%02d', $diff->h, $diff->i, $diff->s);
    }

    // Payment proof URL
    public function getPaymentProofUrlAttribute()
    {
        if (!$this->payment_proof) {
            return null;
        }
        
        if (str_starts_with($this->payment_proof, 'http')) {
            return $this->payment_proof;
        }
        
        return asset('storage/' . $this->payment_proof);
    }

    // Mark as paid
    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);
        
        // Update order status
        if ($this->order) {
            $this->order->update([
                'payment_status' => 'paid',
                'status' => 'processing'
            ]);
        }
    }

    // Mark as failed
    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'notes' => $reason
        ]);
        
        if ($this->order) {
            $this->order->update([
                'payment_status' => 'failed'
            ]);
        }
    }

    // Mark as expired
    public function markAsExpired()
    {
        $this->update([
            'status' => 'expired'
        ]);
        
        if ($this->order) {
            $this->order->update([
                'payment_status' => 'expired',
                'status' => 'cancelled'
            ]);
            
            // Restore stock
            $this->order->restoreStock();
        }
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($payment) {
            if (empty($payment->payment_code)) {
                $payment->payment_code = 'PAY-' . date('Ymd') . '-' . strtoupper(uniqid());
            }
            
            if (empty($payment->status)) {
                $payment->status = 'waiting_payment';
            }
            
            if (empty($payment->expired_at)) {
                $payment->expired_at = Carbon::now()->addHours(24);
            }
        });
    }
}