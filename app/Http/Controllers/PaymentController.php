<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments (admin only)
     */
    public function index(Request $request)
    {
        $query = Payment::with('order.user')->latest();
        
        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Method filter
        if ($request->has('method') && !empty($request->method)) {
            $query->where('method', $request->method);
        }
        
        // Search by payment code or order code
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('payment_code', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('order', function($q2) use ($searchTerm) {
                      $q2->where('order_code', 'like', '%' . $searchTerm . '%');
                  });
            });
        }
        
        $payments = $query->paginate(20);
        
        return view('payments.index', compact('payments'));
    }

    /**
     * Show payment details (admin)
     */
    public function show(Payment $payment)
    {
        $payment->load('order.orderItems.furniture', 'order.user');
        return view('payments.show', compact('payment'));
    }

    /**
     * Show form for creating payment (admin)
     */
    public function create()
    {
        $orders = Order::where('payment_status', '!=', 'paid')
                      ->where(function($q) {
                          $q->whereDoesntHave('payment')
                            ->orWhereHas('payment', function($q2) {
                                $q2->whereIn('status', ['failed', 'expired']);
                            });
                      })
                      ->latest()
                      ->get();
        
        return view('payments.create', compact('orders'));
    }

    /**
     * Store a newly created payment (admin)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'method' => 'required|in:bank_transfer,e_wallet',
            'bank_name' => 'required_if:method,bank_transfer|nullable|string',
            'wallet_type' => 'required_if:method,e_wallet|nullable|string',
            'amount' => 'required|numeric|min:0',
            'expired_at' => 'required|date|after:now',
            'notes' => 'nullable|string'
        ]);
        
        // Check if order already has active payment
        $existingPayment = Payment::where('order_id', $validated['order_id'])
                                 ->whereIn('status', ['waiting_payment', 'pending_verification', 'pending'])
                                 ->first();
        
        if ($existingPayment) {
            return redirect()->back()
                ->with('error', 'Order ini sudah memiliki payment yang aktif!')
                ->withInput();
        }
        
        // Payment code akan digenerate otomatis oleh model boot()
        $payment = Payment::create($validated);
        
        // Update order payment info
        $order = Order::find($validated['order_id']);
        $order->update([
            'payment_method' => $validated['method'],
            'payment_status' => 'waiting_payment',
            'bank_name' => $validated['bank_name'] ?? null,
            'wallet_type' => $validated['wallet_type'] ?? null
        ]);
        
        return redirect()->route('payments.index')
            ->with('success', 'Payment berhasil dibuat dengan kode: ' . $payment->payment_code);
    }

    /**
     * Show form for editing payment (admin)
     */
    public function edit(Payment $payment)
    {
        $orders = Order::all();
        return view('payments.edit', compact('payment', 'orders'));
    }

    /**
     * Update payment (admin)
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'method' => 'required|in:bank_transfer,e_wallet',
            'bank_name' => 'required_if:method,bank_transfer|nullable|string',
            'wallet_type' => 'required_if:method,e_wallet|nullable|string',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,waiting_payment,pending_verification,paid,failed,expired',
            'expired_at' => 'required|date',
            'notes' => 'nullable|string'
        ]);
        
        // If status changed to paid, use model method
        if ($validated['status'] == 'paid' && $payment->status != 'paid') {
            $payment->markAsPaid();
        } 
        // If status changed to failed
        elseif ($validated['status'] == 'failed' && $payment->status != 'failed') {
            $payment->markAsFailed($validated['notes'] ?? null);
        }
        // If status changed to expired
        elseif ($validated['status'] == 'expired' && $payment->status != 'expired') {
            $payment->markAsExpired();
        }
        else {
            // Update other fields
            $payment->update($validated);
            
            // Update order payment status
            $payment->order->update([
                'payment_status' => $validated['status'],
                'payment_method' => $validated['method'],
                'bank_name' => $validated['bank_name'] ?? null,
                'wallet_type' => $validated['wallet_type'] ?? null
            ]);
        }
        
        return redirect()->route('payments.index')
            ->with('success', 'Payment berhasil diperbarui!');
    }

    /**
     * Delete payment (admin)
     */
    public function destroy(Payment $payment)
    {
        // Check if payment can be deleted
        if (in_array($payment->status, ['paid', 'pending_verification'])) {
            return redirect()->route('payments.index')
                ->with('error', 'Tidak dapat menghapus payment dengan status ' . $payment->status_text);
        }
        
        $paymentCode = $payment->payment_code;
        $payment->delete();
        
        return redirect()->route('payments.index')
            ->with('success', 'Payment ' . $paymentCode . ' berhasil dihapus!');
    }

    /**
     * Show payment page for customer
     */
    public function showPaymentPage($paymentCode)
    {
        $payment = Payment::where('payment_code', $paymentCode)
                         ->with('order.orderItems.furniture')
                         ->firstOrFail();
        
        // Check if payment expired
        if ($payment->is_expired && in_array($payment->status, ['pending', 'waiting_payment'])) {
            $payment->markAsExpired();
        }
        
        // Check if user is authorized
        if (Auth::check() && $payment->order->user_id != Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke payment ini.');
        }
        
        return view('payments.payment-page', compact('payment'));
    }

    /**
     * Upload payment proof (customer)
     */
    public function uploadProof(Request $request, $paymentCode)
    {
        $payment = Payment::where('payment_code', $paymentCode)->firstOrFail();
        
        // Check authorization
        if (Auth::check() && $payment->order->user_id != Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke payment ini.');
        }
        
        // Check if payment can accept proof
        if (!in_array($payment->status, ['waiting_payment', 'pending'])) {
            return redirect()->back()
                ->with('error', 'Tidak dapat mengupload bukti untuk payment dengan status ' . $payment->status_text);
        }
        
        // Validate
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string|max:500'
        ]);
        
        // Upload proof
        if ($request->hasFile('payment_proof')) {
            // Delete old proof if exists
            if ($payment->payment_proof && Storage::exists('public/payment-proofs/' . $payment->payment_proof)) {
                Storage::delete('public/payment-proofs/' . $payment->payment_proof);
            }
            
            // Store new proof
            $filename = 'proof-' . time() . '-' . $paymentCode . '.' . $request->payment_proof->extension();
            $request->payment_proof->storeAs('public/payment-proofs', $filename);
            
            // Update payment
            $payment->update([
                'payment_proof' => $filename,
                'status' => 'pending_verification',
                'notes' => $request->notes ?: $payment->notes
            ]);
            
            // Update order
            $payment->order->update([
                'payment_status' => 'pending_verification'
            ]);
        }
        
        return redirect()->back()
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
    }

    /**
     * Verify payment (admin)
     */
    public function verifyPayment(Request $request, Payment $payment)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_notes' => 'nullable|string|max:500'
        ]);
        
        if ($request->action == 'approve') {
            $payment->markAsPaid();
            $message = 'Payment berhasil diverifikasi!';
        } else {
            $payment->markAsFailed($request->admin_notes);
            $message = 'Payment ditolak!';
        }
        
        return redirect()->route('payments.show', $payment)
            ->with('success', $message);
    }

    /**
     * Get payment status via AJAX
     */
    public function getStatus($paymentCode)
    {
        $payment = Payment::where('payment_code', $paymentCode)->firstOrFail();
        
        return response()->json([
            'status' => $payment->status,
            'status_text' => $payment->status_text,
            'payment_code' => $payment->payment_code,
            'amount' => $payment->amount,
            'formatted_amount' => $payment->formatted_amount,
            'method' => $payment->method,
            'method_text' => $payment->method_text,
            'bank_name' => $payment->bank_name,
            'wallet_type' => $payment->wallet_type,
            'provider_name' => $payment->provider_name,
            'expired_at' => $payment->expired_at ? $payment->expired_at->format('d M Y H:i') : null,
            'time_left' => $payment->time_left,
            'is_expired' => $payment->is_expired,
            'payment_proof_url' => $payment->payment_proof_url
        ]);
    }

    /**
     * Check expired payments (cron job)
     */
    public function checkExpiredPayments()
    {
        $expiredPayments = Payment::whereIn('status', ['pending', 'waiting_payment'])
                                 ->where('expired_at', '<', now())
                                 ->get();
        
        $count = 0;
        foreach ($expiredPayments as $payment) {
            $payment->markAsExpired();
            $count++;
        }
        
        return response()->json([
            'expired' => $count,
            'message' => 'Expired payments checked successfully'
        ]);
    }

    /**
     * Download payment proof (admin)
     */
    public function downloadProof(Payment $payment)
    {
        if (!$payment->payment_proof) {
            return redirect()->back()->with('error', 'Bukti pembayaran tidak ditemukan!');
        }
        
        $path = storage_path('app/public/payment-proofs/' . $payment->payment_proof);
        
        if (!Storage::exists('public/payment-proofs/' . $payment->payment_proof)) {
            return redirect()->back()->with('error', 'File bukti pembayaran tidak ditemukan!');
        }
        
        return response()->download($path, 'payment-proof-' . $payment->payment_code . '.' . pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * Get payment instructions based on method
     */
    public function getInstructions($method, $bankName = null, $walletType = null)
    {
        $instructions = [];
        
        if ($method == 'bank_transfer' && $bankName) {
            $instructions = $this->getBankInstructions($bankName);
        } elseif ($method == 'e_wallet' && $walletType) {
            $instructions = $this->getEWalletInstructions($walletType);
        }
        
        return response()->json($instructions);
    }
    
    private function getBankInstructions($bankName)
    {
        $instructions = [
            'BCA' => [
                'title' => 'Transfer Bank BCA',
                'steps' => [
                    'Masuk ke menu Transfer > Transfer ke Rekening BCA',
                    'Masukkan nomor rekening: 7145083982 (a.n. Muhammad Sebastian Perdana)',
                    'Masukkan jumlah sesuai tagihan',
                    'Konfirmasi transfer',
                    'Simpan bukti transfer',
                    'Upload bukti transfer di halaman ini'
                ],
                'account_number' => '7145083982',
                'account_name' => 'FurniStock Indonesia'
            ],
            'Mandiri' => [
                'title' => 'Transfer Bank Mandiri',
                'steps' => [
                    'Masuk ke menu Transfer > Antar Bank Mandiri',
                    'Masukkan nomor rekening: 0987654321 (a.n. Muhammad Sebastian Perdana)',
                    'Masukkan jumlah sesuai tagihan',
                    'Konfirmasi transfer',
                    'Simpan bukti transfer',
                    'Upload bukti transfer di halaman ini'
                ],
                'account_number' => '0987654321',
                'account_name' => 'FurniStock Indonesia'
            ],
            'BRI' => [
                'title' => 'Transfer Bank BRI',
                'steps' => [
                    'Masuk ke menu Transfer > Transfer BRI',
                    'Masukkan nomor rekening: 1122334455 (a.n. Muhammad Sebastian Perdana)',
                    'Masukkan jumlah sesuai tagihan',
                    'Konfirmasi transfer',
                    'Simpan bukti transfer',
                    'Upload bukti transfer di halaman ini'
                ],
                'account_number' => '1122334455',
                'account_name' => 'FurniStock Indonesia'
            ],
            'BNI' => [
                'title' => 'Transfer Bank BNI',
                'steps' => [
                    'Masuk ke menu Transfer > Transfer ke BNI',
                    'Masukkan nomor rekening: 6677889900 (a.n. Muhammad Sebastian Perdana)',
                    'Masukkan jumlah sesuai tagihan',
                    'Konfirmasi transfer',
                    'Simpan bukti transfer',
                    'Upload bukti transfer di halaman ini'
                ],
                'account_number' => '6677889900',
                'account_name' => 'FurniStock Indonesia'
            ]
        ];
        
        return $instructions[$bankName] ?? [
            'title' => 'Transfer Bank',
            'steps' => ['Silakan transfer ke rekening yang tertera pada invoice'],
            'account_number' => 'Silakan lihat invoice',
            'account_name' => 'FurniStock Indonesia'
        ];
    }
    
    private function getEWalletInstructions($walletType)
    {
        $instructions = [
            'OVO' => [
                'title' => 'Pembayaran OVO',
                'steps' => [
                    'Buka aplikasi OVO',
                    'Pilih "Pay" atau "Transfer"',
                    'Scan QR Code yang tersedia',
                    'Atau masukkan nomor telepon: 089676737200',
                    'Masukkan jumlah sesuai tagihan',
                    'Konfirmasi pembayaran',
                    'Simpan bukti pembayaran',
                    'Upload bukti pembayaran di halaman ini'
                ],
                'phone_number' => '089676737200',
                'qr_code' => asset('images/qr-code-ovo.png')
            ],
            'DANA' => [
                'title' => 'Pembayaran DANA',
                'steps' => [
                    'Buka aplikasi DANA',
                    'Pilih "Bayar" atau "Transfer"',
                    'Scan QR Code yang tersedia',
                    'Atau masukkan nomor telepon: 089676737200',
                    'Masukkan jumlah sesuai tagihan',
                    'Konfirmasi pembayaran',
                    'Simpan bukti pembayaran',
                    'Upload bukti pembayaran di halaman ini'
                ],
                'phone_number' => '089676737200',
                'qr_code' => asset('images/qr-code-dana.png')
            ],
            'GoPay' => [
                'title' => 'Pembayaran GoPay',
                'steps' => [
                    'Buka aplikasi Gojek',
                    'Pilih "GoPay"',
                    'Pilih "Bayar" atau "Transfer"',
                    'Scan QR Code yang tersedia',
                    'Atau masukkan nomor telepon: 081234567890',
                    'Masukkan jumlah sesuai tagihan',
                    'Konfirmasi pembayaran',
                    'Simpan bukti pembayaran',
                    'Upload bukti pembayaran di halaman ini'
                ],
                'phone_number' => '089676737200',
                'qr_code' => asset('images/qr-code-gopay.png')
            ]
        ];
        
        return $instructions[$walletType] ?? [
            'title' => 'Pembayaran E-Wallet',
            'steps' => ['Silakan lakukan pembayaran melalui e-wallet pilihan Anda'],
            'phone_number' => '089676737200',
            'qr_code' => asset('images/qr-code-default.png')
        ];
    }

    /**
     * Get user's payments
     */
    public function myPayments()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $payments = Payment::whereHas('order', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->with('order')
            ->latest()
            ->paginate(10);
        
        return view('payments.my-payments', compact('payments'));
    }

    /**
     * Generate payment invoice (PDF)
     */
    public function generateInvoice($paymentCode)
    {
        $payment = Payment::where('payment_code', $paymentCode)
                         ->with('order.orderItems.furniture', 'order.user')
                         ->firstOrFail();
        
        // Check authorization
        if (Auth::check() && $payment->order->user_id != Auth::id() && Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses ke invoice ini.');
        }
        
        // You can implement PDF generation here using DomPDF or similar
        // For now, return view
        return view('payments.invoice', compact('payment'));
    }
}