<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FurniStock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <!-- Mini Navigation -->
    <nav class="bg-white border-b border-slate-100 py-6">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <a href="{{ route('user.catalog') }}" class="flex items-center gap-2 group">
                <i data-lucide="arrow-left" class="w-5 h-5 text-slate-400 group-hover:text-amber-600 transition-colors"></i>
                <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900">Kembali Belanja</span>
            </a>
            <div class="flex items-center gap-2">
                <div class="bg-amber-500 p-1 rounded-lg">
                    <i data-lucide="armchair" class="w-4 h-4 text-slate-900"></i>
                </div>
                <span class="font-black tracking-tight text-slate-900">Furni<span class="text-amber-500">Stock</span></span>
            </div>
            <div class="w-24"></div> <!-- Spacer -->
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-12">
        <h1 class="text-3xl font-black text-slate-900 mb-8">Pemesanan Anda</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Left Column: Shipping & Payment -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Shipping Address -->
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-amber-100 p-2 rounded-xl text-amber-600">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800">Informasi Pengiriman</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" placeholder="Masukkan nama penerima" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nomor Telepon</label>
                            <input type="tel" placeholder="Contoh: 0812xxxx" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Lengkap</label>
                            <textarea rows="3" placeholder="Nama jalan, nomor rumah, kelurahan/kecamatan" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-amber-500 transition-all"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-blue-100 p-2 rounded-xl text-blue-600">
                            <i data-lucide="credit-card" class="w-5 h-5"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800">Metode Pembayaran</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative flex flex-col p-4 bg-slate-50 rounded-2xl border-2 border-transparent cursor-pointer hover:bg-white hover:border-amber-500 transition-all group">
                            <input type="radio" name="payment" class="absolute top-4 right-4 text-amber-500 focus:ring-amber-500">
                            <i data-lucide="landmark" class="w-6 h-6 text-slate-400 group-hover:text-amber-500 mb-3"></i>
                            <span class="text-sm font-bold text-slate-800">Transfer Bank</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold mt-1">Verifikasi Manual</span>
                        </label>
                        <label class="relative flex flex-col p-4 bg-slate-50 rounded-2xl border-2 border-transparent cursor-pointer hover:bg-white hover:border-amber-500 transition-all group">
                            <input type="radio" name="payment" class="absolute top-4 right-4 text-amber-500 focus:ring-amber-500">
                            <i data-lucide="wallet" class="w-6 h-6 text-slate-400 group-hover:text-amber-500 mb-3"></i>
                            <span class="text-sm font-bold text-slate-800">E-Wallet</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold mt-1">OVO, Dana, QRIS</span>
                        </label>
                        <label class="relative flex flex-col p-4 bg-slate-50 rounded-2xl border-2 border-transparent cursor-pointer hover:bg-white hover:border-amber-500 transition-all group">
                            <input type="radio" name="payment" class="absolute top-4 right-4 text-amber-500 focus:ring-amber-500">
                            <i data-lucide="truck" class="w-6 h-6 text-slate-400 group-hover:text-amber-500 mb-3"></i>
                            <span class="text-sm font-bold text-slate-800">Bayar di Tempat</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold mt-1">COD</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 sticky top-32">
                    <h2 class="text-xl font-bold text-slate-800 mb-6">Ringkasan Pesanan</h2>
                    
                    <!-- Item List -->
                    <div class="space-y-6 mb-8">
                        <div class="flex gap-4">
                            <div class="w-16 h-16 bg-slate-100 rounded-2xl overflow-hidden shrink-0">
                                <img src="https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=200" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-slate-800 truncate">Kursi Jati Scandinav</h4>
                                <p class="text-xs text-slate-500">Qty: 1</p>
                                <p class="text-sm font-bold text-amber-600 mt-1">Rp 1.250.000</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-16 h-16 bg-slate-100 rounded-2xl overflow-hidden shrink-0">
                                <img src="https://images.unsplash.com/photo-1532372320572-cda25653a26d?w=200" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-slate-800 truncate">Side Table Minimalis</h4>
                                <p class="text-xs text-slate-500">Qty: 2</p>
                                <p class="text-sm font-bold text-amber-600 mt-1">Rp 1.900.000</p>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-slate-100 py-6 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Subtotal</span>
                            <span class="font-bold text-slate-800">Rp 3.150.000</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Ongkos Kirim</span>
                            <span class="font-bold text-slate-800">Rp 150.000</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Pajak (PPN 11%)</span>
                            <span class="font-bold text-slate-800">Rp 346.500</span>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="border-t border-slate-100 pt-6 mb-8">
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Bayar</p>
                                <p class="text-2xl font-black text-slate-900 leading-none mt-1">Rp 3.646.500</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <button class="w-full bg-amber-500 text-slate-900 py-4 rounded-2xl font-black text-sm hover:bg-amber-400 transition-all shadow-xl shadow-amber-500/20 flex items-center justify-center gap-2">
                        Konfirmasi & Bayar
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                    
                    <p class="text-center text-[10px] text-slate-400 mt-6 flex items-center justify-center gap-2 italic uppercase font-bold tracking-widest">
                        <i data-lucide="shield-check" class="w-3 h-3"></i>
                        Transaksi Aman & Terenkripsi
                    </p>
                </div>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>