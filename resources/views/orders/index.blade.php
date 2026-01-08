@extends('layouts.app')

@section('header_title', 'Data Pemesanan')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-2xl border border-slate-200">
        <p class="text-slate-500 text-sm font-medium">Total Pesanan</p>
        <h4 class="text-2xl font-bold text-slate-800 mt-1">1,284</h4>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 border-l-4 border-l-amber-400">
        <p class="text-slate-500 text-sm font-medium">Pending</p>
        <h4 class="text-2xl font-bold text-slate-800 mt-1">12</h4>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 border-l-4 border-l-indigo-400">
        <p class="text-slate-500 text-sm font-medium">Dikirim</p>
        <h4 class="text-2xl font-bold text-slate-800 mt-1">45</h4>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 border-l-4 border-l-green-400">
        <p class="text-slate-500 text-sm font-medium">Selesai</p>
        <h4 class="text-2xl font-bold text-slate-800 mt-1">1,227</h4>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 font-semibold">Order ID</th>
                    <th class="px-6 py-4 font-semibold">Pelanggan</th>
                    <th class="px-6 py-4 font-semibold">Item</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-indigo-600">#ORD-9921</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-slate-800 font-semibold text-sm">Budi Santoso</span>
                            <span class="text-xs text-slate-400">Jakarta Selatan</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">2x Kursi Kerja, 1x Lampu Baca</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-amber-100 text-amber-600 rounded-full text-[10px] font-bold uppercase">Menunggu Pembayaran</span>
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-800">Rp 2.150.000</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection