@extends('layouts.app')

@section('header_title', 'Detail Pesanan')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-[2rem] p-8 text-white shadow-2xl">
        <div class="relative z-10">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('orders.index') }}" class="text-slate-300 hover:text-white transition-colors">
                    <i data-lucide="arrow-left" class="w-6 h-6"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-extrabold mb-1">Pesanan #{{ $order->order_code }}</h1>
                    <p class="text-slate-300">
                        {{ $order->created_at->translatedFormat('l, d F Y') }} • 
                        <span class="font-medium">{{ $order->customer_name }}</span>
                    </p>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-4 mt-6">
                <div class="bg-slate-800/50 backdrop-blur-sm px-4 py-2.5 rounded-xl border border-slate-700">
                    <p class="text-xs text-slate-400 mb-1">Total Pesanan</p>
                    <p class="text-xl font-bold">{{ $order->formatted_total }}</p>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-sm px-4 py-2.5 rounded-xl border border-slate-700">
                    <p class="text-xs text-slate-400 mb-1">Status</p>
                    <span class="px-3 py-1 rounded-full text-sm font-bold {{ $order->status_badge }}">
                        {{ $order->status_text }}
                    </span>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-sm px-4 py-2.5 rounded-xl border border-slate-700">
                    <p class="text-xs text-slate-400 mb-1">Pembayaran</p>
                    <span class="px-3 py-1 rounded-full text-sm font-bold {{ $order->payment_status_badge ?? 'bg-slate-600 text-white' }}">
                        {{ $order->payment_status_text ?? $order->payment_status }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center">
        <i data-lucide="check-circle" class="w-6 h-6 mr-3 flex-shrink-0"></i>
        <div>
            <p class="font-semibold">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl flex items-center">
        <i data-lucide="alert-circle" class="w-6 h-6 mr-3 flex-shrink-0"></i>
        <div>
            <p class="font-semibold">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Order Details -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Customer Information Card -->
            <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800">Informasi Pelanggan</h3>
                    <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
                        <i data-lucide="user" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Nama Lengkap</p>
                            <p class="text-lg font-semibold text-slate-800">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">No. Telepon</p>
                            <p class="text-lg font-semibold text-slate-800">{{ $order->customer_phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Email</p>
                            <p class="text-lg font-semibold text-slate-800">{{ $order->user->email ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-3">Alamat Pengiriman</p>
                            <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                                <p class="text-slate-800 leading-relaxed">{{ $order->shipping_address }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items Card -->
            <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800">Detail Pesanan</h3>
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                        <i data-lucide="package" class="w-5 h-5 text-amber-600"></i>
                    </div>
                </div>
                
                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="w-full">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-bold text-slate-700 uppercase tracking-wider">Produk</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-slate-700 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-slate-700 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-slate-700 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($order->items as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-16 rounded-lg bg-slate-100 overflow-hidden flex-shrink-0">
                                            @if($item->furniture && $item->furniture->first_image)
                                            <img src="{{ $item->furniture->first_image }}" 
                                                 alt="{{ $item->furniture->name }}"
                                                 class="w-full h-full object-cover">
                                            @else
                                            <div class="w-full h-full bg-slate-200 flex items-center justify-center">
                                                <i data-lucide="package" class="w-6 h-6 text-slate-400"></i>
                                            </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $item->furniture->name ?? 'Produk' }}</p>
                                            <p class="text-sm text-slate-500">{{ $item->furniture->category->name ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-800">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center justify-center w-10 h-10 bg-slate-100 text-slate-800 font-bold rounded-lg">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-lg text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column - Actions & Summary -->
        <div class="space-y-8">
            <!-- Payment Information Card -->
            <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800">Informasi Pembayaran</h3>
                    <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Metode Pembayaran</p>
                        <p class="text-lg font-semibold text-slate-800">
                            {{ $order->payment_method == 'bank_transfer' ? 'Transfer Bank' : 'E-Wallet' }}
                        </p>
                    </div>
                    
                    @if($order->bank_name)
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Bank Tujuan</p>
                        <p class="text-lg font-semibold text-slate-800">{{ $order->bank_name }}</p>
                    </div>
                    @endif
                    
                    @if($order->wallet_type)
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Jenis E-Wallet</p>
                        <p class="text-lg font-semibold text-slate-800">{{ $order->wallet_type }}</p>
                    </div>
                    @endif
                    
                    @if($order->notes)
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Catatan</p>
                        <div class="bg-slate-50 p-4 rounded-xl">
                            <p class="text-slate-800">{{ $order->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800">Ringkasan Pembayaran</h3>
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                        <i data-lucide="receipt" class="w-5 h-5 text-blue-600"></i>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-600">Biaya Pengiriman</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-600">Pajak (11%)</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-slate-200 pt-4 mt-2">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-slate-800">Total Pembayaran</span>
                            <span class="text-2xl font-black text-slate-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Proof Card -->
            @if($order->payment && $order->payment->payment_proof)
            <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800">Bukti Pembayaran</h3>
                    <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                        <i data-lucide="image" class="w-5 h-5 text-purple-600"></i>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <!-- Payment Proof Image -->
                    <div class="border-3 border-dashed border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50 p-4">
                        <img src="{{ asset('storage/' . $order->payment->payment_proof) }}" 
                             alt="Bukti Pembayaran" 
                             class="w-full h-auto rounded-xl cursor-pointer hover:opacity-90 transition-opacity"
                             onclick="openImageModal('{{ asset('storage/' . $order->payment->payment_proof) }}')">
                    </div>
                    
                    <!-- Verification Actions -->
                    @if($order->payment_status == 'pending_verification')
                    <div class="space-y-3">
                        <h4 class="font-bold text-slate-800">Verifikasi Pembayaran</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <form action="{{ route('orders.verify.payment', $order) }}" method="POST" 
                                  onsubmit="return confirm('Verifikasi pembayaran ini?')">
                                @csrf
                                <button type="submit" 
                                        class="w-full px-4 py-3.5 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                                    Verifikasi
                                </button>
                            </form>
                            
                            <button type="button" 
                                    onclick="openRejectModal()"
                                    class="w-full px-4 py-3.5 bg-rose-500 text-white font-bold rounded-xl hover:bg-rose-600 transition-all flex items-center justify-center gap-2">
                                <i data-lucide="x-circle" class="w-5 h-5"></i>
                                Tolak
                            </button>
                        </div>
                    </div>
                    @elseif($order->payment_status == 'paid')
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl">
                        <div class="flex items-center">
                            <i data-lucide="shield-check" class="w-5 h-5 mr-3"></i>
                            <div>
                                <p class="font-bold">Pembayaran Sudah Diverifikasi</p>
                                <p class="text-sm mt-1">
                                    Diverifikasi pada {{ $order->payment->paid_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Quick Actions Card -->
            <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800">Aksi Cepat</h3>
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                        <i data-lucide="zap" class="w-5 h-5 text-amber-600"></i>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <!-- Update Status Form -->
                    <form action="{{ route('orders.update.status', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Update Status Pesanan</label>
                            <div class="flex gap-2">
                                <select name="status" 
                                        class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <button type="submit" 
                                        class="px-5 py-2.5 bg-indigo-500 text-white font-bold rounded-xl hover:bg-indigo-600 transition-all">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Quick Action Buttons -->
                    <div class="grid grid-cols-2 gap-3">
                        @if($order->status == 'processing' && $order->payment_status == 'paid')
                        <form action="{{ route('orders.update.status', $order) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="shipped">
                            <button type="submit" 
                                    class="w-full px-3 py-2.5 bg-blue-500 text-white text-sm font-bold rounded-lg hover:bg-blue-600 transition-all flex items-center justify-center gap-2">
                                <i data-lucide="truck" class="w-4 h-4"></i>
                                Kirim
                            </button>
                        </form>
                        @endif
                        
                        @if($order->status == 'shipped')
                        <form action="{{ route('orders.update.status', $order) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="delivered">
                            <button type="submit" 
                                    class="w-full px-3 py-2.5 bg-emerald-500 text-white text-sm font-bold rounded-lg hover:bg-emerald-600 transition-all flex items-center justify-center gap-2">
                                <i data-lucide="package-check" class="w-4 h-4"></i>
                                Selesai
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black/90 z-50 hidden items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-[90vh]">
        <button onclick="closeImageModal()" 
                class="absolute -top-12 right-0 text-white hover:text-slate-300 p-2">
            <i data-lucide="x" class="w-8 h-8"></i>
        </button>
        <img id="modalImage" src="" alt="" class="w-full h-auto rounded-2xl shadow-2xl">
    </div>
</div>

<!-- Reject Payment Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-slate-800">Tolak Pembayaran</h3>
            <button onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form action="{{ route('orders.reject.payment', $order) }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <p class="text-slate-600 mb-3">Berikan alasan penolakan yang jelas:</p>
                    <textarea name="rejection_reason" 
                              rows="4"
                              class="w-full px-4 py-3.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-300"
                              placeholder="Contoh: Bukti pembayaran tidak jelas, nominal tidak sesuai, dll."
                              required></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()"
                            class="flex-1 px-4 py-3.5 border border-slate-300 text-slate-700 font-bold rounded-xl hover:bg-slate-50 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-3.5 bg-rose-500 text-white font-bold rounded-xl hover:bg-rose-600 transition-all">
                        Tolak Pembayaran
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Modal Functions
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modals on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
        closeRejectModal();
    }
});

// Close modals when clicking outside
document.getElementById('imageModal')?.addEventListener('click', function(e) {
    if (e.target.id === 'imageModal') {
        closeImageModal();
    }
});

document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target.id === 'rejectModal') {
        closeRejectModal();
    }
});

// Initialize Lucide icons
lucide.createIcons();
</script>

<style>
/* Smooth animations */
#imageModal, #rejectModal {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Custom scrollbar for tables */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endsection
