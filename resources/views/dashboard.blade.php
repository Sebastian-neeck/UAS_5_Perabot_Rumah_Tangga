@extends('layouts.app')

@section('header_title', 'Ringkasan Inventaris')

@section('content')
<div class="space-y-8">
    <!-- Hero Section: Modern Furniture Showcase -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-[2rem] p-10 text-white shadow-2xl">
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div>
                <span class="inline-block px-4 py-1.5 bg-amber-500/20 text-amber-400 rounded-full text-xs font-bold uppercase tracking-wider mb-4 border border-amber-500/30">
                    Sistem Manajemen Inventaris
                </span>
                <h3 class="text-4xl font-extrabold mb-4 leading-tight">
                    Optimalkan Koleksi <br>
                    <span class="text-amber-500">Perabot Terbaik</span> Anda.
                </h3>
                <p class="text-slate-400 text-lg mb-8 max-w-md">
                    Pantau ketersediaan stok jati, mahoni, dan koleksi modern dalam satu dasbor terintegrasi.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('furniture.index') }}" class="group bg-amber-500 text-slate-900 px-7 py-3.5 rounded-2xl font-bold text-sm hover:bg-amber-400 transition-all shadow-lg shadow-amber-500/25 flex items-center gap-2">
                        Kelola Stok
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <button class="bg-white/5 backdrop-blur-md text-white border border-white/10 px-7 py-3.5 rounded-2xl font-bold text-sm hover:bg-white/10 transition-all flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Laporan Bulanan
                    </button>
                </div>
            </div>
            
            <!-- Mini Chart / Visual Element -->
            <div class="hidden lg:flex justify-end relative">
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-3xl w-full max-w-xs shadow-inner">
                    <div class="flex justify-between items-end gap-2 h-32 mb-4">
                        <div class="w-full bg-amber-500/40 rounded-t-lg h-[40%] hover:bg-amber-500 transition-all cursor-pointer"></div>
                        <div class="w-full bg-amber-500/40 rounded-t-lg h-[65%] hover:bg-amber-500 transition-all cursor-pointer"></div>
                        <div class="w-full bg-amber-500/40 rounded-t-lg h-[50%] hover:bg-amber-500 transition-all cursor-pointer"></div>
                        <div class="w-full bg-amber-500/40 rounded-t-lg h-[90%] hover:bg-amber-500 transition-all cursor-pointer shadow-[0_0_20px_rgba(245,158,11,0.3)]"></div>
                        <div class="w-full bg-amber-500/40 rounded-t-lg h-[60%] hover:bg-amber-500 transition-all cursor-pointer"></div>
                    </div>
                    <div class="flex justify-between text-[10px] font-bold text-slate-500 uppercase">
                        <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span>
                    </div>
                    <p class="mt-4 text-xs font-medium text-slate-300 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        Tren Penjualan Meningkat 12%
                    </p>
                </div>
                <!-- Floating Icon -->
                <div class="absolute -bottom-6 -left-6 bg-amber-500 p-4 rounded-2xl shadow-xl rotate-12">
                    <i data-lucide="armchair" class="w-8 h-8 text-slate-900"></i>
                </div>
            </div>
        </div>
        <!-- Abstract Decoration -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-amber-500/10 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/2"></div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $stats = [
                ['label' => 'Total Produk', 'value' => '1,240', 'icon' => 'package', 'color' => 'amber', 'trend' => '+12%'],
                ['label' => 'Kategori Aktif', 'value' => '24', 'icon' => 'grid', 'color' => 'blue', 'trend' => 'Tetap'],
                ['label' => 'Pesanan Berjalan', 'value' => '84', 'icon' => 'shopping-cart', 'color' => 'emerald', 'trend' => '+5%'],
                ['label' => 'Stok Menipis', 'value' => '7', 'icon' => 'alert-triangle', 'color' => 'rose', 'trend' => '-2%'],
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="group bg-white p-6 rounded-[1.5rem] border border-slate-100 hover:border-{{ $stat['color'] }}-200 hover:shadow-xl hover:shadow-{{ $stat['color'] }}-500/5 transition-all duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-{{ $stat['color'] }}-50 p-3 rounded-2xl text-{{ $stat['color'] }}-600 group-hover:scale-110 transition-transform">
                    <i data-lucide="{{ $stat['icon'] }}" class="w-6 h-6"></i>
                </div>
                <span class="text-[10px] font-bold px-2 py-1 rounded-md {{ str_contains($stat['trend'], '+') ? 'bg-emerald-50 text-emerald-600' : (str_contains($stat['trend'], '-') ? 'bg-rose-50 text-rose-600' : 'bg-slate-50 text-slate-500') }}">
                    {{ $stat['trend'] }}
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                <h4 class="text-2xl font-black text-slate-800 tracking-tight">{{ $stat['value'] }}</h4>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Modern Table Section -->
        <div class="lg:col-span-2 bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8 flex justify-between items-center">
                <div>
                    <h4 class="text-xl font-bold text-slate-800 tracking-tight">Pesanan Perabot Terbaru</h4>
                    <p class="text-sm text-slate-500">Daftar transaksi yang perlu diproses hari ini</p>
                </div>
                <a href="{{ route('orders.index') }}" class="px-4 py-2 text-sm font-bold text-amber-600 bg-amber-50 rounded-xl hover:bg-amber-100 transition-colors">
                    Lihat Semua
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-400 text-[10px] uppercase font-bold tracking-[0.1em]">
                            <th class="px-8 py-4">Kode Transaksi</th>
                            <th class="px-8 py-4">Nama Pelanggan</th>
                            <th class="px-8 py-4">Status Alur</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr class="group hover:bg-slate-50/80 transition-colors">
                            <td class="px-8 py-5">
                                <span class="font-bold text-slate-800 block text-sm">#FURN-9920</span>
                                <span class="text-[10px] text-slate-400">Meja Makan Minimalis</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 font-bold text-xs">A</div>
                                    <span class="text-sm font-medium text-slate-700">Andini Putri</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm text-slate-600">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Selesai
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <button class="p-2 text-slate-400 hover:text-amber-500 transition-colors">
                                    <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="group hover:bg-slate-50/80 transition-colors">
                            <td class="px-8 py-5 font-bold text-slate-800 text-sm">#FURN-9919</td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs">B</div>
                                    <span class="text-sm font-medium text-slate-700">Budi Santoso</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm text-slate-600">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                    Dikirim
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <button class="p-2 text-slate-400 hover:text-amber-500 transition-colors">
                                    <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sidebar Activity/Alerts -->
        <div class="space-y-6">
            <!-- Analytics Card -->
            <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                <h4 class="font-bold text-slate-800 mb-6">Top Kategori</h4>
                <div class="space-y-5">
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-slate-600 font-medium">Furniture Ruang Tamu</span>
                            <span class="font-bold text-slate-800">65%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-slate-600 font-medium">Set Meja Makan</span>
                            <span class="font-bold text-slate-800">20%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: 20%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-slate-600 font-medium">Dekorasi Kamar</span>
                            <span class="font-bold text-slate-800">15%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: 15%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promotion / Action Card -->
            <div class="bg-amber-500 rounded-[2rem] p-8 text-slate-900 relative overflow-hidden group">
                <div class="relative z-10">
                    <h5 class="font-black text-xl mb-2">Butuh Bantuan?</h5>
                    <p class="text-sm font-medium opacity-80 mb-6 leading-relaxed">Hubungi support tim untuk kendala teknis stok.</p>
                    <button class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold text-xs hover:bg-slate-800 transition-all">
                        Hubungi Kami
                    </button>
                </div>
                <i data-lucide="headphones" class="absolute -bottom-4 -right-4 w-24 h-24 opacity-10 group-hover:scale-110 transition-transform"></i>
            </div>
        </div>
    </div>
</div>
@endsection