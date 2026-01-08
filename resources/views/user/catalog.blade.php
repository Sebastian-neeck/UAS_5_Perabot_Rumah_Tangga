<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurniStock - Koleksi Perabot Terbaik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-white text-slate-900">
    <!-- User Navigation -->
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="bg-amber-500 p-1.5 rounded-lg">
                    <i data-lucide="armchair" class="w-5 h-5 text-slate-900"></i>
                </div>
                <span class="text-xl font-black tracking-tight text-slate-900">Furni<span class="text-amber-500">Stock</span></span>
            </div>

            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#" class="text-amber-600">Beranda</a>
                <a href="#" class="hover:text-amber-600 transition-colors">Katalog</a>
                <a href="#" class="hover:text-amber-600 transition-colors">Tentang Kami</a>
                <a href="#" class="hover:text-amber-600 transition-colors">Kontak</a>
            </div>

            <div class="flex items-center gap-4">
                <button class="p-2 hover:bg-slate-100 rounded-full transition-colors relative">
                    <i data-lucide="search" class="w-5 h-5 text-slate-600"></i>
                </button>
                <button class="p-2 hover:bg-slate-100 rounded-full transition-colors relative">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-slate-600"></i>
                    <span class="absolute top-1 right-1 w-4 h-4 bg-amber-500 text-[10px] font-bold text-slate-900 flex items-center justify-center rounded-full border-2 border-white">2</span>
                </button>
                <div class="h-6 w-px bg-slate-200 mx-2"></div>
                <a href="{{ route('login') }}" class="text-sm font-bold text-slate-900 hover:text-amber-600">Masuk</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative bg-slate-50 py-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="relative z-10">
                <span class="text-amber-600 font-bold text-sm tracking-widest uppercase mb-4 block">Koleksi 2024</span>
                <h1 class="text-5xl lg:text-7xl font-black text-slate-900 leading-tight mb-6">
                    Kenyamanan <br> Modern Untuk <br> <span class="text-amber-500 text-outline">Rumah Anda.</span>
                </h1>
                <p class="text-slate-500 text-lg mb-8 max-w-md">
                    Temukan pilihan perabot kayu jati dan desain minimalis yang dirancang khusus untuk estetika hunian Anda.
                </p>
                <button class="bg-slate-900 text-white px-10 py-4 rounded-full font-bold hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/20">
                    Jelajahi Sekarang
                </button>
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
    <section class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-black text-slate-900">Kategori Populer</h2>
            <div class="flex gap-2">
                <button class="w-10 h-10 border border-slate-200 rounded-full flex items-center justify-center hover:bg-amber-500 hover:border-amber-500 transition-all">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </button>
                <button class="w-10 h-10 border border-slate-200 rounded-full flex items-center justify-center hover:bg-amber-500 hover:border-amber-500 transition-all">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @php
                $categories = [
                    ['name' => 'Ruang Tamu', 'icon' => 'sofa', 'count' => '120'],
                    ['name' => 'Meja Makan', 'icon' => 'utensils', 'count' => '45'],
                    ['name' => 'Tempat Tidur', 'icon' => 'bed', 'count' => '82'],
                    ['name' => 'Kursi Kerja', 'icon' => 'briefcase', 'count' => '30'],
                    ['name' => 'Lemari Pakaian', 'icon' => 'archive', 'count' => '15'],
                    ['name' => 'Dekorasi', 'icon' => 'palette', 'count' => '200'],
                ];
            @endphp
            @foreach($categories as $cat)
            <div class="group bg-slate-50 p-6 rounded-3xl text-center hover:bg-white hover:shadow-xl transition-all cursor-pointer border border-transparent hover:border-amber-100">
                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-amber-500 transition-colors shadow-sm">
                    <i data-lucide="{{ $cat['icon'] }}" class="w-6 h-6 text-slate-600 group-hover:text-slate-900"></i>
                </div>
                <p class="font-bold text-slate-800 text-sm mb-1">{{ $cat['name'] }}</p>
                <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $cat['count'] }} Produk</p>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Product Grid -->
    <section class="max-w-7xl mx-auto px-6 py-20">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <h2 class="text-4xl font-black text-slate-900 mb-2 tracking-tight">Koleksi Produk Terlaris</h2>
                <p class="text-slate-500">Pilihan terbaik untuk melengkapi estetika rumah Anda.</p>
            </div>
            <div class="flex gap-4 overflow-x-auto pb-2 no-scrollbar">
                <button class="whitespace-nowrap px-6 py-2.5 bg-slate-900 text-white rounded-full text-sm font-bold">Semua</button>
                <button class="whitespace-nowrap px-6 py-2.5 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-full text-sm font-bold transition-all">Terbaru</button>
                <button class="whitespace-nowrap px-6 py-2.5 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-full text-sm font-bold transition-all">Paling Populer</button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-12">
            @php
                $products = [
                    ['name' => 'Kursi Jati Scandinav', 'price' => '1.250.000', 'img' => 'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=400', 'cat' => 'Ruang Tamu'],
                    // FIXED: Updated Image URL for Meja Makan
                    ['name' => 'Meja Makan Minimalis', 'price' => '4.800.000', 'img' => 'https://images.unsplash.com/photo-1617806118233-18e1de247200?w=400', 'cat' => 'Ruang Makan'],
                    ['name' => 'Sofa Beludru Premium', 'price' => '8.900.000', 'img' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400', 'cat' => 'Ruang Tamu'],
                    ['name' => 'Lampu Hias Modern', 'price' => '750.000', 'img' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400', 'cat' => 'Dekorasi'],
                    ['name' => 'Kursi Kerja Ergonomis', 'price' => '2.100.000', 'img' => 'https://images.unsplash.com/photo-1580480055273-228ff5388ef8?w=400', 'cat' => 'Ruang Kerja'],
                    ['name' => 'Rak Buku Jati Tua', 'price' => '3.450.000', 'img' => 'https://images.unsplash.com/photo-1594620302200-9a762244a156?w=400', 'cat' => 'Penyimpanan'],
                    ['name' => 'Side Table Minimalis', 'price' => '950.000', 'img' => 'https://images.unsplash.com/photo-1532372320572-cda25653a26d?w=400', 'cat' => 'Ruang Tamu'],
                    ['name' => 'Meja Rias Klasik', 'price' => '5.200.000', 'img' => 'https://images.unsplash.com/photo-1618220179428-22790b461013?w=400', 'cat' => 'Kamar Tidur'],
                ];
            @endphp

            @foreach($products as $product)
            <div class="group">
                <div class="relative aspect-[3/4] rounded-[2.5rem] overflow-hidden mb-6 bg-slate-100 shadow-inner">
                    <img src="{{ $product['img'] }}" alt="{{ $product['name'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <button class="absolute top-4 right-4 w-10 h-10 bg-white/80 backdrop-blur-md rounded-full flex items-center justify-center text-slate-900 hover:bg-amber-500 transition-colors shadow-lg">
                        <i data-lucide="heart" class="w-4 h-4"></i>
                    </button>
                    <div class="absolute bottom-6 left-0 right-0 px-6 translate-y-12 group-hover:translate-y-0 transition-transform duration-500">
                        <button class="w-full bg-white text-slate-900 py-3 rounded-2xl font-bold text-sm shadow-xl flex items-center justify-center gap-2">
                            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                            Tambah ke Keranjang
                        </button>
                    </div>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 mb-1 block">{{ $product['cat'] }}</span>
                    <h4 class="font-bold text-slate-900 text-lg mb-1">{{ $product['name'] }}</h4>
                    <p class="text-slate-500 font-bold">Rp {{ $product['price'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-20 mt-20">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="md:col-span-1">
                <div class="flex items-center gap-2 mb-6">
                    <div class="bg-amber-500 p-1.5 rounded-lg text-slate-900">
                        <i data-lucide="armchair" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xl font-black tracking-tight text-white">Furni<span class="text-amber-500">Stock</span></span>
                </div>
                <p class="text-sm leading-relaxed mb-6">
                    Membawa kehangatan jati asli dan kenyamanan desain modern ke setiap sudut rumah Anda sejak 2024.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-amber-500 hover:text-slate-900 transition-all">
                        <i data-lucide="instagram" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-amber-500 hover:text-slate-900 transition-all">
                        <i data-lucide="facebook" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
            <div>
                <h5 class="text-white font-bold mb-6">Produk</h5>
                <ul class="space-y-4 text-sm font-medium">
                    <li><a href="#" class="hover:text-amber-500 transition-colors">Ruang Tamu</a></li>
                    <li><a href="#" class="hover:text-amber-500 transition-colors">Kamar Tidur</a></li>
                    <li><a href="#" class="hover:text-amber-500 transition-colors">Dapur</a></li>
                    <li><a href="#" class="hover:text-amber-500 transition-colors">Kantor</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white font-bold mb-6">Dukungan</h5>
                <ul class="space-y-4 text-sm font-medium">
                    <li><a href="#" class="hover:text-amber-500 transition-colors">Pusat Bantuan</a></li>
                    <li><a href="#" class="hover:text-amber-500 transition-colors">Cara Order</a></li>
                    <li><a href="#" class="hover:text-amber-500 transition-colors">Kebijakan Pengembalian</a></li>
                    <li><a href="#" class="hover:text-amber-500 transition-colors">Cek Status Pesanan</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white font-bold mb-6">Newsletter</h5>
                <p class="text-sm mb-4">Dapatkan info koleksi terbaru dan promo menarik.</p>
                <div class="flex gap-2">
                    <input type="text" placeholder="Email Anda" class="flex-1 bg-slate-800 border-none rounded-xl px-4 text-sm focus:ring-2 focus:ring-amber-500">
                    <button class="bg-amber-500 text-slate-900 px-4 py-2 rounded-xl font-bold text-xs">Join</button>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 mt-20 pt-8 border-t border-slate-800 text-center text-xs font-bold uppercase tracking-widest opacity-50">
            &copy; 2024 FurniStock Indonesia. All Rights Reserved.
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>