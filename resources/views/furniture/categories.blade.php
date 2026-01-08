@extends('layouts.app')

@section('header_title', 'Kategori Perabot')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h3 class="text-2xl font-bold text-slate-800">Master Kategori</h3>
        <p class="text-slate-500">Kelola klasifikasi produk perabot Anda.</p>
    </div>
    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold flex items-center gap-2 transition-all shadow-lg shadow-indigo-200">
        <i data-lucide="plus" class="w-5 h-5"></i> Tambah Kategori
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Contoh Card Kategori -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 hover:shadow-xl transition-all group">
        <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <i data-lucide="sofa" class="text-indigo-600 w-8 h-8"></i>
        </div>
        <h4 class="text-lg font-bold text-slate-800 mb-1">Ruang Tamu</h4>
        <p class="text-sm text-slate-500 mb-4">Sofa, Meja Tamu, dan Dekorasi.</p>
        <div class="flex items-center justify-between pt-4 border-t border-slate-50">
            <span class="text-xs font-medium px-3 py-1 bg-green-100 text-green-600 rounded-full">24 Item</span>
            <div class="flex gap-2">
                <button class="p-2 text-slate-400 hover:text-indigo-600"><i data-lucide="edit-3" class="w-4 h-4"></i></button>
                <button class="p-2 text-slate-400 hover:text-red-600"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
            </div>
        </div>
    </div>
    
    <!-- Card Kosong untuk Menambah -->
    <div class="border-2 border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center p-6 hover:bg-slate-50 cursor-pointer transition-all">
        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mb-2">
            <i data-lucide="plus" class="text-slate-400"></i>
        </div>
        <span class="text-slate-500 font-medium">Tambah Baru</span>
    </div>
</div>
@endsection