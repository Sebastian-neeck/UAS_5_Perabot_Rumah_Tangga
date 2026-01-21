<?php
// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'total_amount',
        'status',
        'shipping_address',
        'customer_name',
        'customer_phone',
        'payment_method',
        'payment_status',
        'bank_name',
        'wallet_type',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2'
    ];

    protected $appends = [
        'formatted_total',
        'payment_method_text',
        'status_text',
        'status_badge',
        'payment_status_text',   // Tambah ini
        'payment_status_badge',  // Tambah ini nanti
        'status_color',
        'subtotal',
        'shipping_cost',
        'tax',
        'payment_expired_at',
        'can_cancel'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // TAMBAHKAN RELATIONSHIP INI!
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Hitung subtotal dari items
    public function getSubtotalAttribute()
    {
        return $this->items->sum('subtotal');
    }

    // Ongkir flat Rp 150.000
    public function getShippingCostAttribute()
    {
        return 150000;
    }

    // Pajak 11% dari subtotal
    public function getTaxAttribute()
    {
        return $this->subtotal * 0.11;
    }

    // Total yang konsisten (harus sama dengan total_amount)
    public function getCalculatedTotalAttribute()
    {
        return $this->subtotal + $this->shipping_cost + $this->tax;
    }

    // Format total untuk display
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    // Format subtotal untuk display
    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    // Format shipping untuk display
    public function getFormattedShippingAttribute()
    {
        return 'Rp ' . number_format($this->shipping_cost, 0, ',', '.');
    }

    // Format tax untuk display
    public function getFormattedTaxAttribute()
    {
        return 'Rp ' . number_format($this->tax, 0, ',', '.');
    }

    // Payment method text
    public function getPaymentMethodTextAttribute()
    {
        $methods = [
            'bank_transfer' => 'Transfer Bank',
            'e_wallet' => 'E-Wallet',
            'cod' => 'Bayar di Tempat (COD)'
        ];
        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    // Payment status text
    public function getPaymentStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Menunggu Pembayaran',
            'waiting_payment' => 'Menunggu Pembayaran',
            'pending_verification' => 'Menunggu Verifikasi',
            'paid' => 'Dibayar',
            'failed' => 'Gagal',
            'expired' => 'Kadaluarsa'
        ];
        return $statuses[$this->payment_status] ?? $this->payment_status;
    }

    // Status text
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Menunggu Pembayaran',
            'processing' => 'Sedang Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    // Status badge untuk UI
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-amber-100 text-amber-600',
            'processing' => 'bg-blue-100 text-blue-600',
            'shipped' => 'bg-indigo-100 text-indigo-600',
            'delivered' => 'bg-green-100 text-green-600',
            'cancelled' => 'bg-red-100 text-red-600'
        ];
        
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-600';
    }

    // Status color untuk UI
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'amber',
            'processing' => 'blue',
            'shipped' => 'indigo',
            'delivered' => 'green',
            'cancelled' => 'red'
        ];
        
        return $colors[$this->status] ?? 'gray';
    }

    // Payment status color
    public function getPaymentStatusColorAttribute()
    {
        $colors = [
            'pending' => 'amber',
            'waiting_payment' => 'amber',
            'pending_verification' => 'blue',
            'paid' => 'green',
            'failed' => 'red',
            'expired' => 'red'
        ];
        
        return $colors[$this->payment_status] ?? 'gray';
    }

    // Cek apakah order bisa dibatalkan
    public function getCanCancelAttribute()
    {
        return in_array($this->status, ['pending', 'processing']) && 
               in_array($this->payment_status, ['pending', 'waiting_payment']);
    }

    // Cek apakah sudah expired (24 jam dari created_at)
    public function getIsExpiredAttribute()
    {
        return $this->created_at->addHours(24)->isPast() && 
               in_array($this->payment_status, ['pending', 'waiting_payment']);
    }

    // Tanggal expired pembayaran
    public function getPaymentExpiredAtAttribute()
    {
        return $this->created_at->addHours(24);
    }

    // Format tanggal expired
    public function getFormattedExpiredAtAttribute()
    {
        return $this->payment_expired_at->format('d/m/Y H:i');
    }

    // Waktu tersisa untuk pembayaran (format: 23:59:59)
    public function getPaymentTimeLeftAttribute()
    {
        if ($this->is_expired) {
            return '00:00:00';
        }
        
        $diff = $this->payment_expired_at->diff(now());
        return sprintf('%02d:%02d:%02d', $diff->h, $diff->i, $diff->s);
    }

    // Cek apakah order punya payment proof
    public function hasPaymentProof()
    {
        return $this->payment && $this->payment->payment_proof;
    }

    // Update stock ketika order dibatalkan
    public function restoreStock()
    {
        foreach ($this->items as $item) {
            if ($item->furniture) {
                $furniture = $item->furniture;
                $furniture->stock += $item->quantity;
                
                if ($furniture->stock <= 0) {
                    $furniture->status = 'out_of_stock';
                } elseif ($furniture->stock <= 5) {
                    $furniture->status = 'low_stock';
                } else {
                    $furniture->status = 'available';
                }
                
                $furniture->save();
            }
        }
    }

    // Validasi total_amount sebelum save
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_code)) {
                $order->order_code = 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
            }
            
            // Set default values
            if (empty($order->status)) {
                $order->status = 'pending';
            }
            
            if (empty($order->payment_status)) {
                $order->payment_status = 'waiting_payment';
            }
        });
        
        static::saving(function ($order) {
            // Jika ada items, hitung ulang total_amount untuk konsistensi
            if ($order->items->isNotEmpty()) {
                $subtotal = $order->items->sum('subtotal');
                $shipping = 150000;
                $tax = $subtotal * 0.11;
                $order->total_amount = $subtotal + $shipping + $tax;
            }
        });
        
        // Auto cancel jika expired
        static::updated(function ($order) {
            if ($order->is_expired && 
                in_array($order->status, ['pending']) && 
                in_array($order->payment_status, ['pending', 'waiting_payment'])) {
                
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'expired'
                ]);
                
                // Restore stock
                $order->restoreStock();
            }
        });
    }

    // Scope untuk filter
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }
    
    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }
    
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }
    
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
    
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
    
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }
    
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }
}