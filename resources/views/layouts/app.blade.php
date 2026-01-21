<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- INI YANG PERLU DITAMBAH! -->
    <title>FurniStock - Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="flex min-h-screen">
        <!-- Sidebar - Updated to Slate & Amber -->
        <aside class="w-72 bg-slate-900 text-slate-300 flex flex-col border-r border-slate-800">
            <div class="p-8 flex items-center gap-3">
                <div class="bg-amber-500 p-2 rounded-xl">
                    <i data-lucide="armchair" class="w-6 h-6 text-slate-900"></i>
                </div>
                <span class="text-xl font-black text-white tracking-tight">Furni<span class="text-amber-500">Stock</span></span>
            </div>

            <nav class="flex-1 px-4 space-y-2">
                <p class="px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-4">Menu Utama</p>
                
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('dashboard') ? 'bg-amber-500 text-slate-900 font-bold shadow-lg shadow-amber-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    Dashboard
                </a>

                <!-- Inventaris: Menggunakan ikon Package untuk produk fisik -->
                <a href="{{ route('furniture.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('furniture.*') ? 'bg-amber-500 text-slate-900 font-bold shadow-lg shadow-amber-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="package" class="w-5 h-5"></i>
                    Inventaris
                </a>

                <!-- Kategori: Menggunakan ikon Layers untuk pengelompokan -->
                <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('categories.*') ? 'bg-amber-500 text-slate-900 font-bold shadow-lg shadow-amber-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                    Kategori
                </a>

                <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('orders.*') ? 'bg-amber-500 text-slate-900 font-bold shadow-lg shadow-amber-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                    Pesanan
                </a>
            </nav>

            <div class="p-6">
                <div class="bg-slate-800/50 rounded-2xl p-4 border border-slate-700">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-slate-700 border border-slate-600 flex items-center justify-center font-bold text-amber-500">
                            AD
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-xs font-bold text-white truncate">Admin Toko</p>
                            <p class="text-[10px] text-slate-500 truncate">admin@furnistock.com</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2 bg-slate-700 hover:bg-rose-500/20 hover:text-rose-400 text-slate-400 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2">
                            <i data-lucide="log-out" class="w-3 h-3"></i>
                            Keluar Sistem
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-20 bg-white border-b border-slate-100 flex items-center justify-between px-8 shrink-0">
                <h2 class="text-xl font-bold text-slate-800">@yield('header_title', 'Dashboard')</h2>
                
                <div class="flex items-center gap-4">
                    <button class="p-2.5 text-slate-400 hover:bg-slate-50 rounded-xl transition-colors relative">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full border-2 border-white"></span>
                    </button>
                    <div class="h-8 w-px bg-slate-100"></div>
                    <div class="flex items-center gap-3">
                        <p class="text-sm font-semibold text-slate-700 hidden md:block">8 Jan 2024</p>
                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <section class="flex-1 overflow-y-auto p-8">
                @yield('content')
            </section>
        </main>
    </div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
        
        // Add global CSRF token for AJAX requests
        window.csrfToken = "{{ csrf_token() }}";
        
        // Log untuk debugging
        console.log('Layout loaded, CSRF token:', window.csrfToken);
    </script>
</body>
</html>