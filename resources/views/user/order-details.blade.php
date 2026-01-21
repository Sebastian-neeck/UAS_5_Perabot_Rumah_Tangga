<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #{{ $order->order_code }} - FurniStock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .status-badge {
            @apply px-3 py-1 rounded-full text-xs font-bold;
        }
        .status-pending { @apply bg-amber-100 text-amber-600; }
        .status-processing { @apply bg-blue-100 text-blue-600; }
        .status-shipped { @apply bg-purple-100 text-purple-600; }
        .status-delivered { @apply bg-green-100 text-green-600; }
        .status-cancelled { @apply bg-red-100 text-red-600; }
        
        .payment-badge {
            @apply px-2 py-1 rounded text-xs font-bold;
        }
        .payment-waiting { @apply bg-amber-100 text-amber-600; }
        .payment-pending { @apply bg-blue-100 text-blue-600; }
        .payment-paid { @apply bg-green-100 text-green-600; }
        .payment-failed { @apply bg-red-100 text-red-600; }
        .payment-expired { @apply bg-gray-100 text-gray-600; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <!-- Navigation -->
    <nav class="bg-white border-b border-slate-100 py-6 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <a href="{{ route('user.orders') }}" class="flex items-center gap-2 group">
                <i data-lucide="arrow-left" class="w-5 h-5 text-slate-400 group-hover:text-amber-600"></i>
                <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900">Kembali ke Pesanan Saya</span>
            </a>
            <div class="flex items-center gap-2">
                <div class="bg-amber-500 p-1 rounded-lg">
                    <i data-lucide="armchair" class="w-4 h-4 text-slate-900"></i>
                </div>
                <span class="font-black tracking-tight text-slate-900">Furni<span class="text-amber-500">Stock</span></span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('cart.index') }}" class="p-2 hover:bg-slate-100 rounded-full relative">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-slate-600"></i>
                    @auth
                        @php
                            $cartCount = \App\Models\Cart::where('user_id', Auth::id())->sum('quantity');
                        @endphp
                        @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-amber-500 text-xs text-slate-900 font-bold rounded-full flex items-center justify-center">
                            {{ $cartCount }}
                        </span>
                        @endif
                    @endauth
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('user.orders') }}" class="text-sm font-bold text-slate-900 hover:text-amber-600">
                        Pesanan Saya
                    </a>
                    <a href="{{ route('profile') }}" class="text-sm font-bold text-slate-900 hover:text-amber-600">
                        {{ Auth::user()->name }}
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="max-w-7xl mx-auto px-6 mt-6">
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center">
            <i data-lucide="check-circle" class="w-6 h-6 mr-3 flex-shrink-0"></i>
            <div>
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="max-w-7xl mx-auto px-6 mt-6">
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl flex items-center">
            <i data-lucide="alert-circle" class="w-6 h-6 mr-3 flex-shrink-0"></i>
            <div>
                <p class="font-semibold">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <main class="max-w-7xl mx-auto px-6 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 mb-2">Detail Pesanan</h1>
                    <div class="flex items-center gap-4">
                        <p class="text-slate-500">Order ID: {{ $order->order_code }}</p>
                        <span class="status-badge {{ $order->status_badge }}">
                            {{ $order->status_text }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    @if($order->can_cancel)
                    <form action="{{ route('order.cancel', $order->order_code) }}" 
                          method="POST" 
                          onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 bg-red-100 text-red-600 text-sm font-bold rounded-lg hover:bg-red-200 transition-colors">
                            Batalkan Pesanan
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Order Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Order Timeline -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h2 class="text-xl font-bold text-slate-800 mb-6">Status Pesanan</h2>
                    
                    <div class="relative">
                        <!-- Progress Line -->
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-slate-200"></div>
                        
                        @php
                            $steps = [
                                'order_placed' => [
                                    'icon' => 'shopping-bag',
                                    'title' => 'Pesanan Dibuat',
                                    'active' => true,
                                    'date' => $order->created_at->format('d M Y, H:i'),
                                    'description' => 'Pesanan Anda telah diterima'
                                ],
                                'payment' => [
                                    'icon' => 'credit-card',
                                    'title' => 'Pembayaran',
                                    'active' => in_array($order->payment_status, ['pending_verification', 'paid']),
                                    'description' => $order->payment_status_text
                                ],
                                'processing' => [
                                    'icon' => 'package',
                                    'title' => 'Diproses',
                                    'active' => in_array($order->status, ['processing', 'shipped', 'delivered'])
                                ],
                                'shipping' => [
                                    'icon' => 'truck',
                                    'title' => 'Pengiriman',
                                    'active' => in_array($order->status, ['shipped', 'delivered'])
                                ],
                                'delivered' => [
                                    'icon' => 'check-circle',
                                    'title' => 'Selesai',
                                    'active' => $order->status === 'delivered'
                                ]
                            ];
                        @endphp
                        
                        <div class="space-y-8">
                            @foreach($steps as $step)
                            <div class="relative flex items-start gap-4">
                                <div class="relative z-10">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center 
                                        {{ $step['active'] ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-400' }}">
                                        <i data-lucide="{{ $step['icon'] }}" class="w-4 h-4"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2">
                                        <div>
                                            <h3 class="font-bold text-slate-800">{{ $step['title'] }}</h3>
                                            @if(isset($step['date']))
                                            <p class="text-sm text-slate-500">{{ $step['date'] }}</p>
                                            @endif
                                            @if(isset($step['description']))
                                            <p class="text-sm text-slate-500">{{ $step['description'] }}</p>
                                            @endif
                                        </div>
                                        @if($step['active'])
                                        <span class="text-sm font-bold text-green-600">
                                            <i data-lucide="check" class="w-4 h-4 inline-block mr-1"></i>
                                            Selesai
                                        </span>
                                        @else
                                        <span class="text-sm text-slate-400">Menunggu</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h2 class="text-xl font-bold text-slate-800 mb-6">Produk yang Dipesan</h2>
                    
                    <div class="space-y-6">
                        @foreach($order->items as $item)
                        @php
                            $product = $item->furniture;
                            $images = $product->images ?? [];
                            $imageUrl = null;
                            
                            if (is_array($images) && !empty($images)) {
                                $firstImage = $images[0];
                                $imageUrl = str_starts_with($firstImage, 'http') ? 
                                           $firstImage : 
                                           asset('storage/furniture/' . $firstImage);
                            }
                        @endphp
                        <div class="flex items-start gap-4 p-4 border border-slate-100 rounded-xl hover:bg-slate-50 transition-colors">
                            <div class="w-20 h-20 bg-slate-100 rounded-lg overflow-hidden shrink-0">
                                <img src="{{ $imageUrl ?? 'https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?w=400' }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div>
                                        <h3 class="font-bold text-slate-800">{{ $product->name }}</h3>
                                        <p class="text-sm text-slate-500 mt-1">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                        <p class="text-sm text-slate-500 mt-1">SKU: {{ $product->sku ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-slate-800">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                        <p class="text-sm text-slate-500">Qty: {{ $item->quantity }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                        <p class="font-bold text-lg text-slate-800 mt-2">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h2 class="text-xl font-bold text-slate-800 mb-6">Informasi Pengiriman</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-bold text-slate-700 mb-3">Alamat Pengiriman</h3>
                            <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                                <p class="text-slate-800 font-medium">{{ $order->customer_name }}</p>
                                <p class="text-slate-600">{{ $order->customer_phone }}</p>
                                <p class="text-slate-600 mt-3">{{ $order->shipping_address }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-bold text-slate-700 mb-3">Informasi Pengiriman</h3>
                            <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                                <p class="text-slate-800 font-medium">Standard Delivery</p>
                                <p class="text-slate-600">Estimasi: 3-5 hari kerja</p>
                                <p class="text-slate-600">Biaya: Rp 150.000</p>
                                @if($order->tracking_number)
                                <div class="mt-4">
                                    <p class="text-sm font-bold text-slate-700 mb-1">No. Resi</p>
                                    <p class="font-mono text-lg text-slate-800">{{ $order->tracking_number }}</p>
                                    <a href="#" class="text-sm font-bold text-blue-600 hover:text-blue-700 mt-2 inline-block">
                                        Lacak Pengiriman
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                @if($order->payment)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h2 class="text-xl font-bold text-slate-800 mb-6">Informasi Pembayaran</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-bold text-slate-700 mb-3">Detail Pembayaran</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-slate-500">Metode Pembayaran</p>
                                    <p class="font-bold text-slate-800">{{ $order->payment_method_text }}</p>
                                </div>
                                @if($order->bank_name)
                                <div>
                                    <p class="text-sm text-slate-500">Bank Tujuan</p>
                                    <p class="font-bold text-slate-800">{{ $order->bank_name }}</p>
                                </div>
                                @endif
                                @if($order->wallet_type)
                                <div>
                                    <p class="text-sm text-slate-500">Jenis E-Wallet</p>
                                    <p class="font-bold text-slate-800">{{ $order->wallet_type }}</p>
                                </div>
                                @endif
                                <div>
                                    <p class="text-sm text-slate-500">Status Pembayaran</p>
                                    <span class="payment-badge {{ $order->payment_status_badge }}">
                                        {{ $order->payment_status_text }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        @if($order->payment->payment_proof)
                        <div>
                            <h3 class="font-bold text-slate-700 mb-3">Bukti Pembayaran</h3>
                            <div class="border-2 border-dashed border-slate-200 rounded-xl overflow-hidden bg-slate-50/50 p-4">
                                <img src="{{ asset('storage/' . $order->payment->payment_proof) }}" 
                                     alt="Bukti Pembayaran" 
                                     class="w-full h-auto rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                     onclick="openImageModal('{{ asset('storage/' . $order->payment->payment_proof) }}')">
                                <p class="text-xs text-center text-slate-500 mt-2">Klik untuk memperbesar</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-slate-800 mb-6">Ringkasan Pesanan</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Subtotal Produk</span>
                            <span class="font-medium text-slate-800">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-slate-600">Biaya Pengiriman</span>
                            <span class="font-medium text-slate-800">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-slate-600">Pajak (11%)</span>
                            <span class="font-medium text-slate-800">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="border-t border-slate-200 pt-4">
                            <div class="flex justify-between">
                                <span class="text-lg font-bold text-slate-800">Total Pembayaran</span>
                                <span class="text-2xl font-black text-slate-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Information -->
                    <div class="mt-8 pt-8 border-t border-slate-200">
                        <h3 class="font-bold text-slate-700 mb-4">Informasi Pesanan</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-slate-500">Order ID</p>
                                <p class="font-medium text-slate-800">{{ $order->order_code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Tanggal Pesanan</p>
                                <p class="font-medium text-slate-800">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Status Pesanan</p>
                                <span class="status-badge {{ $order->status_badge }}">
                                    {{ $order->status_text }}
                                </span>
                            </div>
                            @if($order->payment)
                            <div>
                                <p class="text-sm text-slate-500">Kode Pembayaran</p>
                                <p class="font-medium text-slate-800">{{ $order->payment->payment_code }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Payment Actions -->
                    @if($order->status === 'pending' && $order->payment_status === 'waiting_payment' && $order->payment)
                    <div class="mt-8">
                        <a href="{{ route('payment.page', $order->payment->payment_code) }}" 
                           class="block w-full bg-amber-500 text-slate-900 text-center py-3 rounded-xl font-bold hover:bg-amber-400 transition-all mb-3">
                            Bayar Sekarang
                        </a>
                        <p class="text-xs text-slate-500 text-center">
                            Batas pembayaran: {{ $order->payment->expired_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    @endif
                    
                    <!-- Customer Service -->
                    
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-2">
                    <div class="bg-amber-500 p-1.5 rounded-lg">
                        <i data-lucide="armchair" class="w-5 h-5 text-slate-900"></i>
                    </div>
                    <span class="text-xl font-black text-slate-900">Furni<span class="text-amber-500">Stock</span></span>
                </div>
                
                <div class="text-center text-sm">
                    <p>Jl. Perabotan No. 123, Jakarta Selatan</p>
                    <p class="mt-1">Email: hello@furnistock.com • Telp: (021) 1234-5678</p>
                </div>
                
                <div class="text-center text-sm">
                    <p class="text-slate-300 font-medium mb-2">Jam Operasional</p>
                    <p>Senin - Jumat: 09:00 - 18:00</p>
                    <p>Sabtu: 09:00 - 15:00</p>
                </div>
            </div>
            
            <div class="mt-8 pt-8 border-t border-slate-800 text-center text-xs font-bold uppercase tracking-widest opacity-50">
                &copy; {{ date('Y') }} FurniStock Indonesia. All Rights Reserved.
            </div>
        </div>
    </footer>

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

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
        
        // Image Modal Functions
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
        
        // Auto refresh status every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
        
        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
        
        // Close modal when clicking outside
        document.getElementById('imageModal')?.addEventListener('click', function(e) {
            if (e.target.id === 'imageModal') {
                closeImageModal();
            }
        });
    </script>
</body>
</html>