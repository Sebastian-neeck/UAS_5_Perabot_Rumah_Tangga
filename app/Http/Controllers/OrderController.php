<?php
// app/Http\Controllers\OrderController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Cart;
use App\Models\Furniture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Show checkout page
     */
    public function checkout()
    {
        $user = Auth::user();
        $cartItems = Cart::with('furniture')
            ->where('user_id', Auth::id())
            ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja kosong!');
        }
        
        // Check stock
        foreach ($cartItems as $cartItem) {
            if ($cartItem->furniture->stock < $cartItem->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', 'Stok ' . $cartItem->furniture->name . ' tidak mencukupi! Stok tersedia: ' . $cartItem->furniture->stock);
            }
        }
        
        // Calculate totals
        $subtotal = $cartItems->sum('subtotal');
        $shipping = 150000;
        $tax = $subtotal * 0.11;
        $total = $subtotal + $shipping + $tax;
        
        return view('user.checkout', compact(
            'user', 
            'cartItems', 
            'subtotal', 
            'shipping', 
            'tax', 
            'total'
        ));
    }
    
    /**
     * Process checkout
     */
    public function processCheckout(Request $request)
    {
        // Validate request
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|in:bank_transfer,e_wallet',
            'bank_name' => 'required_if:payment_method,bank_transfer',
            'wallet_type' => 'required_if:payment_method,e_wallet',
            'notes' => 'nullable|string'
        ]);
        
        $user = Auth::user();
        $cartItems = Cart::with('furniture')
            ->where('user_id', Auth::id())
            ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja kosong!');
        }
        
        // Check stock
        foreach ($cartItems as $cartItem) {
            if ($cartItem->furniture->stock < $cartItem->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', 'Stok ' . $cartItem->furniture->name . ' tidak mencukupi! Stok tersedia: ' . $cartItem->furniture->stock);
            }
        }
        
        // Calculate totals
        $subtotal = $cartItems->sum('subtotal');
        $shipping = 150000;
        $tax = $subtotal * 0.11;
        $totalAmount = $subtotal + $shipping + $tax;
        
        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'order_code' => 'ORD' . date('Ymd') . strtoupper(uniqid()),
            'total_amount' => $totalAmount,
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping,
            'tax' => $tax,
            'status' => 'pending',
            'shipping_address' => $request->shipping_address,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'payment_method' => $request->payment_method,
            'bank_name' => $request->bank_name,
            'wallet_type' => $request->wallet_type,
            'payment_status' => 'waiting_payment',
            'notes' => $request->notes
        ]);
        
        // Create order items and update stock
        foreach ($cartItems as $cartItem) {
            $order->items()->create([
                'furniture_id' => $cartItem->furniture_id,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->furniture->price,
                'subtotal' => $cartItem->subtotal
            ]);
            
            // Update stock
            $furniture = Furniture::find($cartItem->furniture_id);
            if ($furniture) {
                $furniture->stock -= $cartItem->quantity;
                
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
        
        // Create payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => $request->payment_method,
            'bank_name' => $request->bank_name,
            'wallet_type' => $request->wallet_type,
            'amount' => $totalAmount,
            'status' => 'waiting_payment',
            'expired_at' => Carbon::now()->addHours(24)
        ]);
        
        // Clear cart
        Cart::where('user_id', Auth::id())->delete();
        
        // Clear session cart too
        if (session()->has('cart')) {
            session()->forget('cart');
        }
        
        return redirect()->route('checkout.success', $order->order_code)
            ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
    }
    
    /**
     * Show checkout success page
     */
    public function checkoutSuccess($orderCode)
    {
        $order = Order::where('order_code', $orderCode)
            ->with(['items.furniture', 'payment'])
            ->firstOrFail();
            
        if ($order->user_id !== Auth::id()) {
            return redirect()->route('user.catalog')
                ->with('error', 'Anda tidak memiliki akses ke pesanan ini.');
        }
        
        // Pastikan payment record ada (backward compatibility)
        if (!$order->payment) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => $order->payment_method,
                'bank_name' => $order->bank_name,
                'wallet_type' => $order->wallet_type,
                'amount' => $order->total_amount,
                'status' => 'waiting_payment',
                'expired_at' => Carbon::now()->addHours(24)
            ]);
            
            // Reload order with payment
            $order->load('payment');
        }
        
        return view('user.checkout-success', compact('order'));
    }
    
    /**
     * Upload payment proof - LEGACY METHOD untuk compatibility
     */
    public function uploadPaymentProof(Request $request, $orderCode)
    {
        $order = Order::where('order_code', $orderCode)->firstOrFail();
        
        if ($order->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
        
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        // Store payment proof
        $path = $request->file('payment_proof')->store('payment-proofs', 'public');
        
        // Update or create payment record
        $payment = $order->payment;
        if ($payment) {
            $payment->update([
                'payment_proof' => $path,
                'status' => 'pending_verification'
            ]);
        } else {
            // Create payment if doesn't exist
            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => $order->payment_method,
                'bank_name' => $order->bank_name,
                'wallet_type' => $order->wallet_type,
                'amount' => $order->total_amount,
                'status' => 'pending_verification',
                'payment_proof' => $path,
                'expired_at' => Carbon::now()->addHours(24)
            ]);
        }
        
        // Update order payment status
        $order->update([
            'payment_status' => 'pending_verification'
        ]);
        
        return redirect()->back()
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
    }
    
    /**
     * Display user orders
     */
    public function userOrders()
    {
        $orders = Order::with('items.furniture')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('user.orders', compact('orders'));
    }
    
    /**
     * Display single user order
     */
    public function userOrderDetail($orderCode)
    {
        $order = Order::with(['items.furniture', 'payment'])
            ->where('order_code', $orderCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        return view('user.order-details', compact('order'));
    }
    
    /**
     * Cancel order
     */
    public function cancelOrder($orderCode)
    {
        $order = Order::where('order_code', $orderCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        if (!$order->can_cancel) {
            return redirect()->back()
                ->with('error', 'Pesanan tidak dapat dibatalkan.');
        }
        
        $order->update([
            'status' => 'cancelled',
            'payment_status' => 'cancelled'
        ]);
        
        // Restore stock
        $order->restoreStock();
        
        return redirect()->back()
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }
    
    /**
     * ADMIN: List orders - VERSI UPDATE FINAL
     */
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();
        
        // Filters
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }
        
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $orders = $query->paginate(15);
        
        // Hitung statistik - SESUAI DENGAN VIEW orders/index.blade.php
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $shippedOrders = Order::where('status', 'shipped')->count();
        $completedOrders = Order::where('status', 'delivered')->count();
        
        // Untuk kebutuhan tambahan (opsional)
        $processingOrders = Order::where('status', 'processing')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        $revenue = Order::where('status', 'delivered')->sum('total_amount');
        
        return view('orders.index', compact(
            'orders',
            'totalOrders',
            'pendingOrders',
            'shippedOrders',
            'completedOrders'
        ));
    }
    
    /**
     * ADMIN: Show order details
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.furniture', 'payment']);
        return view('orders.show', compact('order'));
    }
    
    /**
     * ADMIN: Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);
        
        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);
        
        // If cancelled, restore stock
        if ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
            $order->restoreStock();
        }
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Status pesanan berhasil diperbarui!');
    }
    
    /**
     * ADMIN: Update payment status
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,waiting_payment,pending_verification,paid,failed,expired,cancelled'
        ]);
        
        $order->update(['payment_status' => $request->payment_status]);
        
        // If paid, update order status too
        if ($request->payment_status === 'paid') {
            $order->update(['status' => 'processing']);
            
            // Update payment record if exists
            if ($order->payment) {
                $order->payment->update([
                    'status' => 'paid',
                    'paid_at' => now()
                ]);
            }
        }
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Status pembayaran berhasil diperbarui!');
    }
    
    /**
     * ADMIN: Verify payment
     */
    public function verifyPayment(Order $order)
    {
        if (!$order->payment) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }
        
        $order->payment->markAsPaid();
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Pembayaran berhasil diverifikasi! Pesanan diproses.');
    }
    
    /**
     * ADMIN: Reject payment
     */
    public function rejectPayment(Request $request, Order $order)
    {
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);
        
        if (!$order->payment) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }
        
        $order->payment->markAsFailed($request->rejection_reason);
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Pembayaran ditolak. Alasan telah dikirim ke pelanggan.');
    }
    
    /**
     * ADMIN: Get stats for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
            'pending_payments' => Order::where('payment_status', 'pending_verification')->count(),
            'today_orders' => Order::whereDate('created_at', Carbon::today())->count(),
            'today_revenue' => Order::where('status', 'delivered')
                ->whereDate('created_at', Carbon::today())
                ->sum('total_amount'),
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}