<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurniStock - Koleksi Perabot Terbaik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .text-outline {
            -webkit-text-stroke: 2px #f59e0b;
            -webkit-text-fill-color: transparent;
        }
        .category-filter.active {
            background-color: #f59e0b;
            color: white;
            border-color: #f59e0b;
        }
        .notification {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>
</head>
<body class="bg-white text-slate-900">
    <!-- Notification Container -->
    <div id="notificationContainer"></div>

    <!-- User Navigation -->
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('user.catalog') }}" class="flex items-center gap-2">
                    <div class="bg-amber-500 p-1.5 rounded-lg">
                        <i data-lucide="armchair" class="w-5 h-5 text-slate-900"></i>
                    </div>
                    <span class="text-xl font-black tracking-tight text-slate-900">Furni<span class="text-amber-500">Stock</span></span>
                </a>
            </div>

            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="{{ route('user.catalog') }}" class="text-amber-600">Beranda</a>
                <a href="#products" class="hover:text-amber-600 transition-colors">Katalog</a>
                @auth
                    <!-- TAMBAHKAN LINK INI -->
                    <a href="{{ route('user.orders') }}" class="hover:text-amber-600 transition-colors">Pesanan Saya</a>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('dashboard') }}" class="hover:text-amber-600 transition-colors">Dashboard</a>
                    @endif
                @endauth
            </div>

            <div class="flex items-center gap-4">
                <!-- Search Form -->
                <form action="{{ route('user.catalog') }}" method="GET" class="relative hidden md:block">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               placeholder="Cari produk..." 
                               value="{{ request('search') }}"
                               class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 w-64">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                        
                        <!-- Clear search button jika ada search term -->
                        @if(request('search'))
                        <a href="{{ route('user.catalog') }}" 
                           class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition-colors">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                        @endif
                    </div>
                </form>
                
                <!-- Cart Icon with Counter -->
                <a href="{{ route('cart.index') }}" class="p-2 hover:bg-slate-100 rounded-full transition-colors relative">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-slate-600"></i>
                    @php
                        $cartCount = 0;
                        if(auth()->check()) {
                            $cartCount = \App\Models\Cart::where('user_id', auth()->id())->count();
                        } elseif(session()->has('cart')) {
                            $cartCount = count(session('cart'));
                        }
                    @endphp
                    @if($cartCount > 0)
                    <span class="absolute top-1 right-1 w-4 h-4 bg-amber-500 text-[10px] font-bold text-slate-900 flex items-center justify-center rounded-full border-2 border-white">
                        {{ $cartCount }}
                    </span>
                    @endif
                </a>
                
                <div class="h-6 w-px bg-slate-200 mx-2"></div>
                
                @auth
                <div class="flex items-center gap-4">
                    <!-- TAMBAHKAN LINK PESANAN SAYA DISINI JUGA -->
                    <a href="{{ route('user.orders') }}" 
                       class="hidden md:block text-sm font-bold text-slate-900 hover:text-amber-600 transition-colors">
                        Pesanan Saya
                    </a>
                    
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-bold text-slate-900">{{ Auth::user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-slate-500 hover:text-amber-600">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="text-sm font-bold text-slate-900 hover:text-amber-600">Masuk</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="max-w-7xl mx-auto px-6 mt-6">
        <div class="p-4 bg-green-50 text-green-700 rounded-xl border border-green-200">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="max-w-7xl mx-auto px-6 mt-6">
        <div class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-200">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Hero Section -->
    <header class="relative bg-slate-50 py-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="relative z-10">
                <span class="text-amber-600 font-bold text-sm tracking-widest uppercase mb-4 block">Koleksi 2026</span>
                <h1 class="text-5xl lg:text-7xl font-black text-slate-900 leading-tight mb-6">
                    Kenyamanan <br> Modern Untuk <br> <span class="text-amber-500 text-outline">Rumah Anda.</span>
                </h1>
                <p class="text-slate-500 text-lg mb-8 max-w-md">
                    Temukan pilihan perabot kayu jati dan desain minimalis yang dirancang khusus untuk estetika hunian Anda.
                </p>
                <a href="#products" class="inline-block bg-slate-900 text-white px-10 py-4 rounded-full font-bold hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/20">
                    Jelajahi Sekarang
                </a>
            </div>
            <div class="relative">
                <div class="aspect-square bg-amber-100 rounded-[3rem] overflow-hidden rotate-3 shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1592078615290-033ee584e267?w=800" alt="Hero Chair" class="w-full h-full object-cover -rotate-3 scale-110">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-2xl shadow-xl flex items-center gap-4">
                    <div class="bg-emerald-100 p-3 rounded-full">
                        <i data-lucide="check" class="w-6 h-6 text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900 italic">"Kualitas Terbaik"</p>
                        <p class="text-xs text-slate-400 font-medium">100% Kayu Jati Solid</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Decorative Shapes -->
        <div class="absolute top-20 right-0 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl"></div>
    </header>

    <!-- Category Filter -->
    <section class="max-w-7xl mx-auto px-6 py-10">
        <div class="flex items-center gap-4 overflow-x-auto no-scrollbar pb-4">
            <!-- Semua Kategori button dengan link -->
            <a href="{{ route('user.catalog') }}?{{ request('search') ? 'search=' . request('search') . '&' : '' }}category=all" 
               class="category-filter px-6 py-3 rounded-full font-bold text-sm whitespace-nowrap transition-colors {{ !request('category') || request('category') == 'all' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white border border-slate-200 text-slate-700 hover:border-amber-300 hover:text-amber-600' }}">
                Semua Kategori
            </a>
            
            @foreach($categories as $category)
            <a href="{{ route('user.catalog') }}?{{ request('search') ? 'search=' . request('search') . '&' : '' }}category={{ $category->id }}" 
               class="category-filter px-6 py-3 rounded-full font-medium text-sm whitespace-nowrap transition-colors {{ request('category') == $category->id ? 'bg-amber-500 text-white border-amber-500' : 'bg-white border border-slate-200 text-slate-700 hover:border-amber-300 hover:text-amber-600' }}">
                <div class="flex items-center gap-2">
                    @if($category->icon)
                    <i data-lucide="{{ $category->icon }}" class="w-4 h-4"></i>
                    @endif
                    <span>{{ $category->name }}</span>
                    <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">
                        {{ $category->furniture_count }}
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </section>

    <!-- Product Grid -->
    <section id="products" class="max-w-7xl mx-auto px-6 py-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <h2 class="text-4xl font-black text-slate-900 mb-2 tracking-tight">
                    @if(request('search'))
                        Hasil Pencarian
                    @else
                        Koleksi Produk Terlaris
                    @endif
                </h2>
                <p class="text-slate-500">
                    @if(request('search'))
                        Menampilkan hasil untuk "{{ request('search') }}"
                    @else
                        Pilihan terbaik untuk melengkapi estetika rumah Anda.
                    @endif
                </p>
            </div>
            
            <!-- Showing results count -->
            @if(request('search') || (request('category') && request('category') !== 'all'))
            <div class="text-sm text-slate-500">
                Ditemukan {{ $furniture->total() }} produk
                @if(request('search') && request('category') && request('category') !== 'all')
                    untuk "<span class="font-bold">{{ request('search') }}</span>" dalam kategori 
                    <span class="font-bold">{{ $categories->find(request('category'))->name ?? '' }}</span>
                @elseif(request('search'))
                    untuk "<span class="font-bold">{{ request('search') }}</span>"
                @elseif(request('category') && request('category') !== 'all')
                    dalam kategori <span class="font-bold">{{ $categories->find(request('category'))->name ?? '' }}</span>
                @endif
                
                <!-- Clear filters button -->
                @if(request('search') || (request('category') && request('category') !== 'all'))
                <a href="{{ route('user.catalog') }}" 
                   class="ml-4 text-amber-600 hover:text-amber-700 font-medium">
                    Hapus Filter
                </a>
                @endif
            </div>
            @endif
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-12" id="productsGrid">
            @forelse($furniture as $product)
            <div class="group product-card" data-category="{{ $product->category_id }}">
                <div class="relative aspect-[3/4] rounded-[2.5rem] overflow-hidden mb-6 bg-slate-100 shadow-inner">
                    <!-- Product Image - PAKAI ACCESSOR DARI MODEL -->
                    <img src="{{ $product->first_image }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                         onerror="this.src='https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?w=400&auto=format&fit=crop'">
                    
                    <!-- Stock Badge -->
                    @if($product->status == 'low_stock')
                        <div class="absolute top-4 left-4 bg-amber-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                            Stok Menipis
                        </div>
                    @elseif($product->status == 'out_of_stock')
                        <div class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                            Habis
                        </div>
                    @endif
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <!-- Add to Cart Button -->
                    <div class="absolute bottom-6 left-0 right-0 px-6 translate-y-12 group-hover:translate-y-0 transition-transform duration-500">
                        <button type="button" 
                                onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->price }}')"
                                class="add-to-cart-btn w-full bg-white text-slate-900 py-3 rounded-2xl font-bold text-sm shadow-xl flex items-center justify-center gap-2 hover:bg-amber-50 transition-colors {{ $product->status == 'out_of_stock' ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $product->status == 'out_of_stock' ? 'disabled' : '' }}
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                data-product-price="{{ $product->price }}">
                            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                            {{ $product->status == 'out_of_stock' ? 'Stok Habis' : 'Tambah ke Keranjang' }}
                        </button>
                    </div>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 mb-1 block">
                        {{ $product->category->name }}
                    </span>
                    <h4 class="font-bold text-slate-900 text-lg mb-1 line-clamp-1">{{ $product->name }}</h4>
                    <p class="text-slate-500 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-xs text-slate-400">
                            Stok: <span class="{{ $product->stock <= 5 ? 'text-amber-500 font-bold' : 'text-slate-500' }}">
                                {{ $product->stock }} unit
                            </span>
                        </p>
                        <span class="text-xs font-medium px-2 py-1 rounded-full 
                            {{ $product->status == 'available' ? 'bg-green-100 text-green-600' : 
                               ($product->status == 'low_stock' ? 'bg-amber-100 text-amber-600' : 'bg-red-100 text-red-600') }}">
                            {{ $product->status == 'available' ? 'Tersedia' : 
                               ($product->status == 'low_stock' ? 'Stok Menipis' : 'Habis') }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <!-- Empty state akan ditangani di bawah -->
            @endforelse
        </div>

        <!-- Pagination -->
        @if($furniture->hasPages())
        <div class="mt-16 flex justify-center">
            <div class="flex items-center gap-2 bg-white p-2 rounded-2xl border border-slate-100 shadow-sm">
                {{ $furniture->withQueryString()->onEachSide(1)->links('pagination::simple-tailwind') }}
            </div>
        </div>
        @endif
        
        <!-- Empty State -->
        @if($furniture->isEmpty())
        <div class="text-center py-20">
            <div class="w-24 h-24 mx-auto mb-6 bg-slate-100 rounded-full flex items-center justify-center">
                <i data-lucide="package-open" class="w-12 h-12 text-slate-400"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Tidak ada produk ditemukan</h3>
            <p class="text-slate-500 mb-8">
                @if(request('search'))
                    Tidak ditemukan produk dengan kata kunci "{{ request('search') }}"
                @elseif(request('category') && request('category') !== 'all')
                    Belum ada produk dalam kategori "{{ $categories->find(request('category'))->name ?? 'ini' }}"
                @else
                    Silakan coba lagi nanti.
                @endif
            </p>
            <a href="{{ route('user.catalog') }}" 
               class="inline-block bg-amber-500 text-slate-900 px-8 py-3 rounded-xl font-bold hover:bg-amber-400 transition-all">
                Lihat Semua Produk
            </a>
        </div>
        @endif
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-2">
                    <div class="bg-amber-500 p-1.5 rounded-lg">
                        <i data-lucide="armchair" class="w-5 h-5 text-slate-900"></i>
                    </div>
                    <span class="text-xl font-black text-white">Furni<span class="text-amber-500">Stock</span></span>
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
        
        // Search form handling
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.querySelector('nav form');
            const searchInput = searchForm?.querySelector('input[name="search"]');
            
            // Enter key submission
            searchInput?.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    
                    // Keep category filter if exists
                    const urlParams = new URLSearchParams(window.location.search);
                    const category = urlParams.get('category');
                    
                    if (category) {
                        searchForm.action = `{{ route('user.catalog') }}?category=${category}`;
                    }
                    
                    searchForm.submit();
                }
            });
            
            // Mobile search toggle
            const searchIcon = document.querySelector('nav i[lucide="search"]');
            if (window.innerWidth < 768) {
                searchIcon?.parentElement?.addEventListener('click', function(e) {
                    e.preventDefault();
                    const searchForm = document.querySelector('nav form');
                    if (searchForm) {
                        searchForm.classList.toggle('hidden');
                        if (!searchForm.classList.contains('hidden')) {
                            searchForm.querySelector('input').focus();
                        }
                    }
                });
            }
        });
        
        // Category Filter Functionality
        document.querySelectorAll('.category-filter').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                window.location.href = url.toString();
            });
        });
        
        // Add to Cart with Notification
        async function addToCart(productId, productName, productPrice) {
            const button = document.querySelector(`[data-product-id="${productId}"]`);
            const originalText = button.innerHTML;
            
            // Show loading state
            button.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Menambahkan...';
            button.disabled = true;
            
            try {
                // Send AJAX request
                const response = await fetch(`/cart/add/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ quantity: 1 })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Show success notification
                    showNotification(productName, productPrice);
                    
                    // Update cart counter
                    updateCartCounter();
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Gagal menambahkan ke keranjang');
                }
            } catch (error) {
                // Show error notification
                showErrorNotification(error.message);
                
                // Reset button immediately
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }
        
        // Show success notification
        function showNotification(productName, productPrice) {
            const container = document.getElementById('notificationContainer');
            
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.innerHTML = `
                <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 p-6 max-w-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-800 mb-1">Berhasil Ditambahkan!</h4>
                            <p class="text-sm text-slate-600 mb-2">${productName}</p>
                            <p class="text-sm font-bold text-amber-600 mb-4">Rp ${formatPrice(productPrice)}</p>
                            <div class="flex gap-3">
                                <a href="{{ route('cart.index') }}" 
                                   class="flex-1 bg-amber-500 text-white text-sm font-bold py-2 rounded-xl text-center hover:bg-amber-600 transition-colors">
                                    Lihat Keranjang
                                </a>
                                <button onclick="this.closest('.notification').remove()" 
                                        class="px-4 py-2 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition-colors">
                                    Lanjut Belanja
                                </button>
                            </div>
                        </div>
                        <button onclick="this.closest('.notification').remove()" 
                                class="text-slate-400 hover:text-slate-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, 5000);
            
            // Re-initialize icons in notification
            setTimeout(() => lucide.createIcons(), 100);
        }
        
        // Show error notification
        function showErrorNotification(message) {
            const container = document.getElementById('notificationContainer');
            
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.innerHTML = `
                <div class="bg-white rounded-2xl shadow-2xl border border-red-200 p-6 max-w-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center">
                            <i data-lucide="alert-circle" class="w-6 h-6 text-red-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-800 mb-1">Gagal Menambahkan</h4>
                            <p class="text-sm text-slate-600 mb-4">${message}</p>
                            <button onclick="this.closest('.notification').remove()" 
                                    class="w-full px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-200 transition-colors">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
            
            // Re-initialize icons in notification
            setTimeout(() => lucide.createIcons(), 100);
        }
        
        // Update cart counter
        async function updateCartCounter() {
            try {
                // Fetch current cart count from API or calculate
                const response = await fetch('/cart/count');
                const data = await response.json();
                
                const cartCount = document.querySelector('nav [href*="cart"] span');
                const cartLink = document.querySelector('nav [href*="cart"]');
                
                if (data.count > 0) {
                    if (cartCount) {
                        cartCount.textContent = data.count;
                    } else if (cartLink) {
                        const counter = document.createElement('span');
                        counter.className = 'absolute top-1 right-1 w-4 h-4 bg-amber-500 text-[10px] font-bold text-slate-900 flex items-center justify-center rounded-full border-2 border-white';
                        counter.textContent = data.count;
                        cartLink.appendChild(counter);
                    }
                } else if (cartCount) {
                    cartCount.remove();
                }
            } catch (error) {
                console.error('Failed to update cart counter:', error);
                // Fallback: increment by 1
                const cartCount = document.querySelector('nav [href*="cart"] span');
                if (cartCount) {
                    const newCount = parseInt(cartCount.textContent) + 1;
                    cartCount.textContent = newCount;
                } else {
                    const cartLink = document.querySelector('nav [href*="cart"]');
                    if (cartLink) {
                        const counter = document.createElement('span');
                        counter.className = 'absolute top-1 right-1 w-4 h-4 bg-amber-500 text-[10px] font-bold text-slate-900 flex items-center justify-center rounded-full border-2 border-white';
                        counter.textContent = '1';
                        cartLink.appendChild(counter);
                    }
                }
            }
        }
        
        // Format price
        function formatPrice(price) {
            return parseInt(price).toLocaleString('id-ID');
        }
    </script>
</body>
</html>