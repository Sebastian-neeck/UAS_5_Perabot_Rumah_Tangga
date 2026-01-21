@extends('layouts.app')

@section('header_title', 'Ringkasan Inventaris')

@section('content')
<div class="space-y-8">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-[2rem] p-10 text-white shadow-2xl">
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div>
                <span class="inline-block px-4 py-1.5 bg-amber-500/20 text-amber-400 rounded-full text-xs font-bold uppercase tracking-wider mb-4 border border-amber-500/30">
                    Sistem Manajemen Inventaris
                </span>
                <h3 class="text-4xl font-extrabold mb-4 leading-tight">
                    Selamat Datang, <br>
                    <span class="text-amber-500">{{ Auth::user()->name }}</span>
                </h3>
                <p class="text-slate-400 text-lg mb-8 max-w-md">
                    Pantau ketersediaan stok perabot dalam satu dasbor terintegrasi.
                </p>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="group bg-white p-6 rounded-[1.5rem] border border-slate-100 hover:border-amber-200 hover:shadow-xl transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-amber-50 p-3 rounded-2xl text-amber-600">
                    <i data-lucide="package" class="w-6 h-6"></i>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Total Produk</p>
                <h4 class="text-2xl font-black text-slate-800 tracking-tight">{{ $totalProducts }}</h4>
            </div>
        </div>
        
        <div class="group bg-white p-6 rounded-[1.5rem] border border-slate-100 hover:border-blue-200 hover:shadow-xl transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-blue-50 p-3 rounded-2xl text-blue-600">
                    <i data-lucide="grid" class="w-6 h-6"></i>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Kategori Aktif</p>
                <h4 class="text-2xl font-black text-slate-800 tracking-tight">{{ $totalCategories }}</h4>
            </div>
        </div>
        
        <div class="group bg-white p-6 rounded-[1.5rem] border border-slate-100 hover:border-emerald-200 hover:shadow-xl transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-emerald-50 p-3 rounded-2xl text-emerald-600">
                    <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Total Pesanan</p>
                <h4 class="text-2xl font-black text-slate-800 tracking-tight">{{ $totalOrders }}</h4>
            </div>
        </div>
        
        <div class="group bg-white p-6 rounded-[1.5rem] border border-slate-100 hover:border-rose-200 hover:shadow-xl transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-rose-50 p-3 rounded-2xl text-rose-600">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Pesanan Pending</p>
                <h4 class="text-2xl font-black text-slate-800 tracking-tight">{{ $pendingOrders }}</h4>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Orders -->
        <div class="lg:col-span-2 bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8 flex justify-between items-center">
                <div>
                    <h4 class="text-xl font-bold text-slate-800 tracking-tight">Pesanan Terbaru</h4>
                    <p class="text-sm text-slate-500">Daftar transaksi yang perlu diproses</p>
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
                            <th class="px-8 py-4">Pelanggan</th>
                            <th class="px-8 py-4">Status</th>
                            <th class="px-8 py-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($recentOrders as $order)
                        <tr class="group hover:bg-slate-50/80 transition-colors">
                            <td class="px-8 py-5">
                                <span class="font-bold text-slate-800 block text-sm">{{ $order->order_code }}</span>
                                <span class="text-[10px] text-slate-400">{{ $order->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 font-bold text-xs">
                                        {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-700">{{ $order->customer_name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $order->status_badge }}">
                                    {{ $order->status_text }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right font-bold text-slate-800">
                                {{ $order->formatted_total }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="space-y-6">
            <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                <h4 class="font-bold text-slate-800 mb-6">Stok Menipis</h4>
                <div class="space-y-4">
                    @foreach($lowStockProducts as $product)
                    <div class="flex items-center gap-3 p-3 bg-rose-50/50 rounded-xl">
                        <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-slate-500">Stok: {{ $product->stock }} unit</p>
                        </div>
                        <a href="{{ route('furniture.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700">
                            Restock
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection