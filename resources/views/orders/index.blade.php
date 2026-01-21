@extends('layouts.app')

@section('header_title', 'Data Pemesanan')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-2xl border border-slate-200">
        <p class="text-slate-500 text-sm font-medium">Total Pesanan</p>
        <h4 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalOrders }}</h4>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 border-l-4 border-l-amber-400">
        <p class="text-slate-500 text-sm font-medium">Pending</p>
        <h4 class="text-2xl font-bold text-slate-800 mt-1">{{ $pendingOrders }}</h4>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 border-l-4 border-l-indigo-400">
        <p class="text-slate-500 text-sm font-medium">Dikirim</p>
        <h4 class="text-2xl font-bold text-slate-800 mt-1">{{ $shippedOrders }}</h4>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 border-l-4 border-l-green-400">
        <p class="text-slate-500 text-sm font-medium">Selesai</p>
        <h4 class="text-2xl font-bold text-slate-800 mt-1">{{ $completedOrders }}</h4>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5"></i>
            <input type="text" id="searchInput" placeholder="Cari kode atau nama pelanggan..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
        </div>
        <div class="flex gap-3">
            <select id="statusFilter" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Sedang Diproses</option>
                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 font-semibold">Order ID</th>
                    <th class="px-6 py-4 font-semibold">Pelanggan</th>
                    <th class="px-6 py-4 font-semibold">Tanggal</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold">Total</th>
                    <th class="px-6 py-4 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($orders as $order)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-indigo-600">
                        <a href="{{ route('orders.show', $order) }}" class="hover:underline">
                            {{ $order->order_code }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-slate-800 font-semibold text-sm">{{ $order->customer_name }}</span>
                            <span class="text-xs text-slate-400">{{ $order->customer_phone }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $order->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $order->status_badge }}">
                            {{ $order->status_text }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-800">{{ $order->formatted_total }}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('orders.show', $order) }}" 
                               class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all"
                               title="Lihat Detail">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($orders->hasPages())
    <div class="p-6 border-t border-slate-100">
        {{ $orders->links() }}
    </div>
    @endif
</div>

<script>
// Simple filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    
    // Auto submit filter on change
    statusFilter.addEventListener('change', function() {
        const status = this.value;
        const url = new URL(window.location.href);
        
        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        
        window.location.href = url.toString();
    });
    
    // Search on Enter key
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            const search = this.value;
            const url = new URL(window.location.href);
            
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }
            
            window.location.href = url.toString();
        }
    });
    
    // Set search value from URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('search')) {
        searchInput.value = urlParams.get('search');
    }
});
</script>
@endsection