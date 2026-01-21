<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - FurniStock</title>
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
            <a href="{{ route('user.catalog') }}" class="flex items-center gap-2 group">
                <i data-lucide="arrow-left" class="w-5 h-5 text-slate-400 group-hover:text-amber-600"></i>
                <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900">Kembali ke Beranda</span>
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
                </a>
                <a href="{{ route('profile') }}" class="text-sm font-bold text-slate-900 hover:text-amber-600">
                    {{ Auth::user()->name }}
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-slate-900 mb-2">Pesanan Saya</h1>
            <p class="text-slate-500">Kelola dan lacak pesanan Anda di sini</p>
        </div>

        <!-- Stats Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Pesanan</p>
                        <p class="text-2xl font-black text-slate-900">{{ $orders->total() }}</p>
                    </div>
                    <div class="bg-slate-100 p-3 rounded-full">
                        <i data-lucide="package" class="w-6 h-6 text-slate-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Dalam Proses</p>
                        <p class="text-2xl font-black text-blue-600">
                            {{ $orders->whereIn('status', ['pending', 'processing'])->count() }}
                        </p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i data-lucide="clock" class="w-6 h-6 text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Dikirim</p>
                        <p class="text-2xl font-black text-purple-600">
                            {{ $orders->where('status', 'shipped')->count() }}
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i data-lucide="truck" class="w-6 h-6 text-purple-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Selesai</p>
                        <p class="text-2xl font-black text-green-600">
                            {{ $orders->where('status', 'delivered')->count() }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <!-- Table Header -->
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-xl font-bold text-slate-800">Semua Pesanan</h2>
                <p class="text-sm text-slate-500 mt-1">Menampilkan {{ $orders->count() }} dari {{ $orders->total() }} pesanan</p>
            </div>

            @if($orders->isEmpty())
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-slate-100 rounded-full flex items-center justify-center">
                    <i data-lucide="package-open" class="w-12 h-12 text-slate-400"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Belum ada pesanan</h3>
                <p class="text-slate-500 mb-8">Mulai belanja dan buat pesanan pertama Anda!</p>
                <a href="{{ route('user.catalog') }}" 
                   class="inline-block bg-amber-500 text-slate-900 px-8 py-3 rounded-xl font-bold hover:bg-amber-400 transition-all">
                    Jelajahi Katalog
                </a>
            </div>
            @else
            <!-- Orders List -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left py-4 px-6 text-slate-600 font-bold">Order ID</th>
                            <th class="text-left py-4 px-6 text-slate-600 font-bold">Tanggal</th>
                            <th class="text-left py-4 px-6 text-slate-600 font-bold">Produk</th>
                            <th class="text-left py-4 px-6 text-slate-600 font-bold">Total</th>
                            <th class="text-left py-4 px-6 text-slate-600 font-bold">Status</th>
                            <th class="text-left py-4 px-6 text-slate-600 font-bold">Pembayaran</th>
                            <th class="text-left py-4 px-6 text-slate-600 font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-6">
                                <div>
                                    <p class="font-bold text-slate-800">{{ $order->order_code }}</p>
                                    <p class="text-xs text-slate-500 mt-1">#{{ $order->id }}</p>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-slate-800">{{ $order->created_at->format('d/m/Y') }}</p>
                                <p class="text-xs text-slate-500">{{ $order->created_at->format('H:i') }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    @if($order->items->isNotEmpty())
                                        @php
                                            $firstItem = $order->items->first();
                                            $product = $firstItem->furniture;
                                            $images = $product->images ?? [];
                                            $imageUrl = null;
                                            
                                            if (is_array($images) && !empty($images)) {
                                                $firstImage = $images[0];
                                                $imageUrl = str_starts_with($firstImage, 'http') ? 
                                                           $firstImage : 
                                                           asset('storage/furniture/' . $firstImage);
                                            }
                                        @endphp
                                        <div class="w-12 h-12 bg-slate-100 rounded-lg overflow-hidden shrink-0">
                                            <img src="{{ $imageUrl ?? 'https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?w=400' }}" 
                                                 alt="{{ $product->name }}"
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 text-sm">{{ $product->name }}</p>
                                            <p class="text-xs text-slate-500">
                                                {{ $order->items->count() }} item
                                                @if($order->items->count() > 1)
                                                +{{ $order->items->count() - 1 }} lainnya
                                                @endif
                                            </p>
                                        </div>
                                    @else
                                        <p class="text-slate-500 text-sm">Produk tidak ditemukan</p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p class="font-bold text-slate-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $statusClasses = [
                                        'pending' => 'status-pending',
                                        'processing' => 'status-processing',
                                        'shipped' => 'status-shipped',
                                        'delivered' => 'status-delivered',
                                        'cancelled' => 'status-cancelled'
                                    ];
                                    
                                    $statusLabels = [
                                        'pending' => 'Menunggu',
                                        'processing' => 'Diproses',
                                        'shipped' => 'Dikirim',
                                        'delivered' => 'Selesai',
                                        'cancelled' => 'Dibatalkan'
                                    ];
                                @endphp
                                <span class="status-badge {{ $statusClasses[$order->status] ?? 'status-pending' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $paymentClasses = [
                                        'pending' => 'payment-pending',
                                        'waiting_payment' => 'payment-waiting',
                                        'pending_verification' => 'payment-pending',
                                        'paid' => 'payment-paid',
                                        'failed' => 'payment-failed',
                                        'expired' => 'payment-expired',
                                        'cancelled' => 'payment-failed'
                                    ];
                                    
                                    $paymentLabels = [
                                        'pending' => 'Menunggu',
                                        'waiting_payment' => 'Belum Bayar',
                                        'pending_verification' => 'Verifikasi',
                                        'paid' => 'Lunas',
                                        'failed' => 'Gagal',
                                        'expired' => 'Kadaluarsa',
                                        'cancelled' => 'Dibatalkan'
                                    ];
                                @endphp
                                <span class="payment-badge {{ $paymentClasses[$order->payment_status] ?? 'payment-pending' }}">
                                    {{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('user.order.detail', $order->order_code) }}" 
                                       class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-200 transition-colors">
                                        Detail
                                    </a>
                                    
                                    @if($order->status === 'pending' && $order->payment_status === 'waiting_payment' && $order->payment)
                                    <a href="{{ route('payment.page', $order->payment->payment_code) }}" 
                                       class="px-4 py-2 bg-amber-500 text-slate-900 text-sm font-bold rounded-lg hover:bg-amber-400 transition-colors">
                                        Bayar
                                    </a>
                                    @endif
                                    
                                    @if($order->can_cancel)
                                    <form action="{{ route('order.cancel', $order->order_code) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                        @csrf
                                        <button type="submit" 
                                                class="px-4 py-2 bg-red-100 text-red-600 text-sm font-bold rounded-lg hover:bg-red-200 transition-colors">
                                            Batalkan
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Pagination -->
            @if($orders->hasPages())
            <div class="p-6 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-slate-500">
                        Menampilkan {{ $orders->firstItem() }} - {{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan
                    </div>
                    <div class="flex items-center gap-2">
                        {{ $orders->links('pagination::simple-tailwind') }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Help Section -->
       
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

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
        
        // Auto refresh status every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>