@extends('layouts.app')

@section('header_title', 'Daftar Perabot')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5"></i>
            <input type="text" placeholder="Cari nama perabot..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
        </div>
        <div class="flex gap-3">
            <button class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-semibold flex items-center gap-2 hover:bg-slate-200">
                <i data-lucide="filter" class="w-4 h-4"></i> Filter
            </button>
            <button class="px-4 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold flex items-center gap-2 hover:bg-indigo-700 shadow-lg shadow-indigo-100">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Produk Baru
            </button>
        </div>
    </div>

    <table class="w-full text-left">
        <thead class="bg-slate-50/50 text-slate-500 text-sm uppercase">
            <tr>
                <th class="px-6 py-4 font-semibold">Produk</th>
                <th class="px-6 py-4 font-semibold">Kategori</th>
                <th class="px-6 py-4 font-semibold">Harga</th>
                <th class="px-6 py-4 font-semibold">Stok</th>
                <th class="px-6 py-4 font-semibold text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-4">
                        <img src="https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=200" class="w-12 h-12 rounded-lg object-cover" alt="Sofa">
                        <div>
                            <p class="font-bold text-slate-800">Sofa Velvet Emerald</p>
                            <p class="text-xs text-slate-400">ID: PRB-001</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-slate-600">Ruang Tamu</td>
                <td class="px-6 py-4 font-bold text-slate-800">Rp 4.500.000</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-bold">12 Unit</span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex justify-center gap-2">
                        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all"><i data-lucide="eye" class="w-4 h-4"></i></button>
                        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-all"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection