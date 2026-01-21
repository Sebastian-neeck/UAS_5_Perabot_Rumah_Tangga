<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - FurniStock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <!-- Navigation -->
    <nav class="bg-white border-b border-slate-100 py-6 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <a href="{{ route('user.catalog') }}" class="flex items-center gap-2 group">
                <i data-lucide="arrow-left" class="w-5 h-5 text-slate-400 group-hover:text-amber-600 transition-colors"></i>
                <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900">Lanjutkan Belanja</span>
            </a>
            <div class="flex items-center gap-2">
                <div class="bg-amber-500 p-1 rounded-lg">
                    <i data-lucide="armchair" class="w-4 h-4 text-slate-900"></i>
                </div>
                <span class="font-black tracking-tight text-slate-900">Furni<span class="text-amber-500">Stock</span></span>
            </div>
            <div class="flex items-center gap-4">
                @auth
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-600">Halo, {{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-slate-400 hover:text-amber-600">
                            (Keluar)
                        </button>
                    </form>
                </div>
                @else
                <a href="{{ route('login') }}" class="text-sm font-bold text-slate-900 hover:text-amber-600">Masuk</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 mb-2">Keranjang Belanja</h1>
                <p class="text-slate-500">Periksa dan kelola produk yang akan Anda beli</p>
            </div>
            @if(count($cartItems) > 0)
            <form action="{{ route('cart.clear') }}" method="POST" class="inline" 
                  onsubmit="return confirm('Kosongkan seluruh keranjang belanja?')">
                @csrf
                <button type="submit" 
                        class="flex items-center gap-2 text-sm text-slate-400 hover:text-red-600">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Kosongkan Keranjang
                </button>
            </form>
            @endif
        </div>
        
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-200 flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif
        
        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-200 flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @if(count($cartItems) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex flex-col sm:flex-row gap-6">
                        <!-- Product Image -->
                        <div class="w-full sm:w-32 h-32 bg-slate-100 rounded-2xl overflow-hidden shrink-0">
                            @if(is_object($item->furniture ?? $item['furniture'] ?? null))
                                @php
                                    $product = $item->furniture ?? $item['furniture'];
                                    // Handle images dengan benar
                                    $images = $product->images;
                                    $imageUrl = null;
                                    
                                    // Jika images adalah array
                                    if (is_array($images) && !empty($images)) {
                                        $firstImage = $images[0];
                                        // Jika URL lengkap (Unsplash)
                                        if (str_starts_with($firstImage, 'http')) {
                                            $imageUrl = $firstImage;
                                        } else {
                                            // Jika nama file lokal
                                            $imageUrl = asset('storage/furniture/' . $firstImage);
                                        }
                                    }
                                    // Jika images string JSON (fallback)
                                    elseif (is_string($images) && !empty($images)) {
                                        $decoded = json_decode($images, true);
                                        if (is_array($decoded) && !empty($decoded)) {
                                            $firstImage = $decoded[0];
                                            if (str_starts_with($firstImage, 'http')) {
                                                $imageUrl = $firstImage;
                                            } else {
                                                $imageUrl = asset('storage/furniture/' . $firstImage);
                                            }
                                        }
                                    }
                                    
                                    // Fallback image
                                    $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?w=400';
                                @endphp
                                <img src="{{ $imageUrl }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.src='https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?w=400'">
                            @elseif(isset($item['image']))
                                @php
                                    $imageUrl = $item['image'];
                                    if (!str_starts_with($imageUrl, 'http')) {
                                        $imageUrl = asset('storage/furniture/' . $imageUrl);
                                    }
                                @endphp
                                <img src="{{ $imageUrl }}" 
                                     alt="{{ $item['name'] }}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.src='https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?w=400'">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-200">
                                    <i data-lucide="package" class="w-8 h-8 text-slate-400"></i>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Product Details -->
                        <div class="flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg text-slate-800 mb-1">
                                        @if(is_object($item->furniture ?? $item['furniture'] ?? null))
                                            {{ $item->furniture->name ?? $item['furniture']->name }}
                                        @else
                                            {{ $item['name'] ?? 'Produk' }}
                                        @endif
                                    </h3>
                                    <p class="text-sm text-slate-500 mb-2">
                                        @if(is_object($item->furniture ?? $item['furniture'] ?? null))
                                            Kategori: {{ $item->furniture->category->name ?? $item['furniture']->category->name }}
                                        @endif
                                    </p>
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-full">
                                            SKU: {{ is_object($item->furniture ?? $item['furniture'] ?? null) ? 
                                                  ($item->furniture->sku ?? $item['furniture']->sku) : 
                                                  ($item['sku'] ?? 'N/A') }}
                                        </span>
                                        <span class="px-2 py-1 {{ 
                                            (is_object($item->furniture ?? $item['furniture'] ?? null) ? 
                                            ($item->furniture->stock ?? $item['furniture']->stock) : 
                                            ($item['stock'] ?? 0)) > 5 ? 'bg-green-100 text-green-600' : 
                                            'bg-amber-100 text-amber-600' }} rounded-full">
                                            Stok: {{ is_object($item->furniture ?? $item['furniture'] ?? null) ? 
                                                  ($item->furniture->stock ?? $item['furniture']->stock) : 
                                                  ($item['stock'] ?? 0) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Delete Button -->
                                @php
                                    // Get product ID untuk form
                                    if (is_object($item->furniture ?? $item['furniture'] ?? null)) {
                                        $productId = $item->furniture->id ?? $item['furniture']->id;
                                    } else {
                                        $productId = $item['product_id'] ?? array_keys($cartItems)[$loop->index];
                                    }
                                @endphp
                                <form action="{{ route('cart.remove', $productId) }}" 
                                      method="POST"
                                      onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                                    @csrf
                                    <button type="submit" 
                                            class="text-slate-400 hover:text-red-500 transition-colors p-2 hover:bg-red-50 rounded-lg">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-6">
                                <!-- Quantity Controls -->
                                <div class="flex items-center gap-4">
                                    <form action="{{ route('cart.update', $productId) }}" 
                                          method="POST" 
                                          class="flex items-center gap-2"
                                          id="quantityForm{{ $loop->index }}">
                                        @csrf
                                        <button type="button" 
                                                onclick="updateQuantity({{ $loop->index }}, -1)"
                                                class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                                id="minusBtn{{ $loop->index }}"
                                                {{ ($item->quantity ?? $item['quantity']) <= 1 ? 'disabled' : '' }}>
                                            <i data-lucide="minus" class="w-3 h-3"></i>
                                        </button>
                                        <input type="number" 
                                               name="quantity" 
                                               value="{{ $item->quantity ?? $item['quantity'] }}" 
                                               min="1" 
                                               max="{{ is_object($item->furniture ?? $item['furniture'] ?? null) ? 
                                                     ($item->furniture->stock ?? $item['furniture']->stock) : 
                                                     ($item['stock'] ?? 99) }}"
                                               class="w-16 text-center border border-slate-200 rounded-lg bg-white py-1"
                                               id="quantityInput{{ $loop->index }}"
                                               onchange="validateQuantity({{ $loop->index }})"
                                               onblur="submitQuantityForm({{ $loop->index }})">
                                        <button type="button" 
                                                onclick="updateQuantity({{ $loop->index }}, 1)"
                                                class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                                id="plusBtn{{ $loop->index }}"
                                                {{ ($item->quantity ?? $item['quantity']) >= (is_object($item->furniture ?? $item['furniture'] ?? null) ? 
                                                    ($item->furniture->stock ?? $item['furniture']->stock) : 
                                                    ($item['stock'] ?? 99)) ? 'disabled' : '' }}>
                                            <i data-lucide="plus" class="w-3 h-3"></i>
                                        </button>
                                    </form>
                                    
                                    <!-- Update Button (mobile) -->
                                    <button type="button"
                                            onclick="submitQuantityForm({{ $loop->index }})"
                                            class="sm:hidden px-4 py-2 bg-amber-500 text-white text-sm font-bold rounded-lg hover:bg-amber-600">
                                        Update
                                    </button>
                                </div>
                                
                                <!-- Price -->
                                <div class="text-right">
                                    <p class="text-sm text-slate-500">Harga Satuan</p>
                                    <p class="text-xl font-bold text-amber-600">
                                        Rp {{ number_format(
                                            is_object($item->furniture ?? $item['furniture'] ?? null) ? 
                                            ($item->furniture->price ?? $item['furniture']->price) : 
                                            $item['price'], 0, ',', '.') }}
                                    </p>
                                    <p class="text-lg font-bold text-slate-800 mt-1">
                                        Subtotal: Rp {{ number_format(
                                            is_object($item) ? $item->subtotal : 
                                            (($item['price'] ?? 0) * ($item['quantity'] ?? 0)), 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 lg:p-8 rounded-2xl border border-slate-100 shadow-sm sticky top-32">
                    <h2 class="text-xl font-bold text-slate-800 mb-6">Ringkasan Pesanan</h2>
                    
                    <!-- Order Details -->
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Subtotal ({{ count($cartItems) }} item)</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Ongkos Kirim</span>
                            <span class="font-bold text-slate-800">Rp 150.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Pajak (PPN 11%)</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($total * 0.11, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <!-- Divider -->
                    <div class="border-t border-slate-100 pt-6 mb-6">
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-sm font-bold text-slate-600">Total Bayar</p>
                                <p class="text-3xl font-black text-slate-900 leading-none mt-1">
                                    Rp {{ number_format($total + 150000 + ($total * 0.11), 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-slate-400 mt-1">Sudah termasuk semua biaya</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    @auth
                    <a href="{{ route('user.checkout') }}" 
                       class="block w-full bg-amber-500 text-slate-900 py-4 rounded-2xl font-black text-sm text-center hover:bg-amber-400 transition-all shadow-lg shadow-amber-500/20 mb-4 hover:shadow-xl">
                        Lanjutkan ke Checkout
                    </a>
                    @else
                    <div class="space-y-4">
                        <a href="{{ route('login') }}" 
                           class="block w-full bg-amber-500 text-slate-900 py-4 rounded-2xl font-black text-sm text-center hover:bg-amber-400 transition-all shadow-lg shadow-amber-500/20">
                            Login untuk Checkout
                        </a>
                        <p class="text-center text-sm text-slate-500">
                            Atau <a href="{{ route('user.catalog') }}" class="text-amber-600 font-bold hover:text-amber-700">lanjutkan belanja sebagai tamu</a>
                        </p>
                    </div>
                    @endauth
                    
                    <!-- Security Badge -->
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <p class="text-center text-xs text-slate-400 flex items-center justify-center gap-2">
                            <i data-lucide="shield-check" class="w-3 h-3"></i>
                            Transaksi Aman & Terenkripsi
                        </p>
                        <p class="text-center text-xs text-slate-400 mt-2">
                            <i data-lucide="truck" class="w-3 h-3 inline mr-1"></i>
                            Gratis retur 30 hari
                        </p>
                    </div>
                    
                    <!-- Continue Shopping -->
                    <div class="mt-6">
                        <a href="{{ route('user.catalog') }}" 
                           class="block w-full border border-slate-200 text-slate-600 py-3 rounded-xl text-sm text-center hover:bg-slate-50 transition-all">
                            <i data-lucide="plus-circle" class="w-4 h-4 inline mr-1"></i>
                            Tambah Produk Lainnya
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Empty Cart -->
        <div class="bg-white p-8 md:p-12 rounded-2xl border border-slate-100 shadow-sm text-center max-w-2xl mx-auto">
            <div class="w-24 h-24 mx-auto mb-6 bg-slate-100 rounded-full flex items-center justify-center">
                <i data-lucide="shopping-cart" class="w-12 h-12 text-slate-400"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Keranjang Belanja Kosong</h3>
            <p class="text-slate-500 mb-8 max-w-md mx-auto">
                Belum ada produk di keranjang belanja Anda. Mulai tambahkan produk favorit Anda sekarang!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('user.catalog') }}" 
                   class="inline-block bg-amber-500 text-slate-900 px-8 py-3 rounded-xl font-bold hover:bg-amber-400 transition-all shadow-lg shadow-amber-500/20">
                    Jelajahi Katalog
                </a>
                <a href="{{ route('user.catalog') }}?category=1" 
                   class="inline-block border border-slate-300 text-slate-700 px-8 py-3 rounded-xl font-bold hover:bg-slate-50 transition-all">
                    Lihat Produk Terlaris
                </a>
            </div>
        </div>
        @endif
    </main>

    <!-- Footer (optional) -->
    <footer class="bg-white border-t border-slate-100 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-sm text-slate-500">
                &copy; {{ date('Y') }} FurniStock. All rights reserved.
                <span class="block sm:inline mt-2 sm:mt-0">Jl. Perabotan No. 123, Jakarta | Telp: (021) 1234-5678</span>
            </p>
        </div>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Cart quantity management functions
        function updateQuantity(index, change) {
            const input = document.getElementById('quantityInput' + index);
            const minusBtn = document.getElementById('minusBtn' + index);
            const plusBtn = document.getElementById('plusBtn' + index);
            
            let currentValue = parseInt(input.value) || 1;
            let maxValue = parseInt(input.max) || 99;
            
            // Calculate new value
            let newValue = currentValue + change;
            
            // Validate bounds
            if (newValue < 1) newValue = 1;
            if (newValue > maxValue) newValue = maxValue;
            
            // Update input value
            input.value = newValue;
            
            // Update button states
            minusBtn.disabled = newValue <= 1;
            plusBtn.disabled = newValue >= maxValue;
            
            // Auto-submit if change is from button click (not for Enter key)
            if (change !== 0) {
                submitQuantityForm(index);
            }
        }
        
        function validateQuantity(index) {
            const input = document.getElementById('quantityInput' + index);
            const minusBtn = document.getElementById('minusBtn' + index);
            const plusBtn = document.getElementById('plusBtn' + index);
            
            let value = parseInt(input.value) || 1;
            let maxValue = parseInt(input.max) || 99;
            
            // Validate bounds
            if (value < 1) value = 1;
            if (value > maxValue) value = maxValue;
            
            input.value = value;
            
            // Update button states
            minusBtn.disabled = value <= 1;
            plusBtn.disabled = value >= maxValue;
        }
        
        function submitQuantityForm(index) {
            const form = document.getElementById('quantityForm' + index);
            const input = document.getElementById('quantityInput' + index);
            
            // Validate before submit
            validateQuantity(index);
            
            // Submit the form
            form.submit();
        }
        
        // Auto-update on Enter key
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const form = this.closest('form');
                    if (form) {
                        form.submit();
                    }
                }
            });
        });
        
        // Show loading state on form submit
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin inline"></i> Memproses...';
                }
            });
        });
        
        // Initialize button states on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Find all quantity inputs and set their button states
            document.querySelectorAll('input[name="quantity"]').forEach((input, index) => {
                validateQuantity(index);
            });
        });
    </script>
</body>
</html>