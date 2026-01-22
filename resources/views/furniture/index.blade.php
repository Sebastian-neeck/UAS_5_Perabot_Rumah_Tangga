@extends('layouts.app')

@section('header_title', 'Daftar Perabot')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5"></i>
            <input type="text" 
                   id="searchInput"
                   placeholder="Cari nama perabot..." 
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all"
                   onkeyup="filterTable()">
        </div>
        <div class="flex gap-3">
            <a href="{{ route('furniture.create') }}" 
               class="px-4 py-2.5 bg-amber-500 text-white rounded-xl font-semibold flex items-center gap-2 hover:bg-amber-600 shadow-lg shadow-amber-500/20">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Produk
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="m-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-200">
        <div class="flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="m-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-200">
        <div class="flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left" id="furnitureTable">
            <thead class="bg-slate-50/50 text-slate-500 text-sm uppercase">
                <tr>
                    <th class="px-6 py-4 font-semibold">Gambar</th>
                    <th class="px-6 py-4 font-semibold">Produk</th>
                    <th class="px-6 py-4 font-semibold">Kategori</th>
                    <th class="px-6 py-4 font-semibold">Harga</th>
                    <th class="px-6 py-4 font-semibold">Stok</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($furniture as $item)
                <tr class="hover:bg-slate-50/50 transition-colors" data-name="{{ strtolower($item->name) }}" data-category="{{ strtolower($item->category->name) }}">
                    <!-- Gambar Produk -->
                    <td class="px-6 py-4">
                        <div class="w-16 h-16 rounded-xl bg-slate-100 overflow-hidden border border-slate-200">
                            @php
                                // Handle images dengan benar
                                $images = $item->images;
                                $firstImage = null;
                                
                                if (is_array($images) && !empty($images)) {
                                    $firstImage = $images[0];
                                } elseif (is_string($images) && !empty($images)) {
                                    $decoded = json_decode($images, true);
                                    if (is_array($decoded) && !empty($decoded)) {
                                        $firstImage = $decoded[0];
                                    }
                                }
                                
                                // Jika ada gambar lokal
                                if ($firstImage && !str_starts_with($firstImage, 'http')) {
                                    $imageUrl = asset('storage/furniture/' . $firstImage);
                                } 
                                // Jika URL Unsplash
                                elseif ($firstImage && str_starts_with($firstImage, 'http')) {
                                    $imageUrl = $firstImage;
                                }
                                // Fallback ke Unsplash
                                else {
                                    $imageUrl = 'https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?w=200&auto=format&fit=crop';
                                }
                            @endphp
                            <img src="{{ $imageUrl }}" 
                                 alt="{{ $item->name }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?w=200'">
                        </div>
                    </td>
                    
                    <!-- Nama Produk -->
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-bold text-slate-800">{{ $item->name }}</p>
                            <p class="text-xs text-slate-400 mt-1">SKU: {{ $item->sku ?? 'FUR-' . str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </td>
                    
                    <!-- Kategori -->
                    <td class="px-6 py-4 text-slate-600">
                        <span class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-full text-xs font-bold">
                            {{ $item->category->name }}
                        </span>
                    </td>
                    
                    <!-- Harga -->
                    <td class="px-6 py-4 font-bold text-slate-800">
                        Rp {{ number_format($item->price, 0, ',', '.') }}
                    </td>
                    
                    <!-- Stok -->
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ 
                            $item->stock > 10 ? 'bg-green-100 text-green-600' : 
                            ($item->stock > 0 ? 'bg-amber-100 text-amber-600' : 'bg-red-100 text-red-600') 
                        }}">
                            {{ $item->stock }} Unit
                        </span>
                    </td>
                    
                    <!-- Status -->
                    <td class="px-6 py-4">
                        @if($item->status == 'available')
                        <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-bold">Tersedia</span>
                        @elseif($item->status == 'low_stock')
                        <span class="px-3 py-1 bg-amber-100 text-amber-600 rounded-full text-xs font-bold">Stok Menipis</span>
                        @else
                        <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold">Habis</span>
                        @endif
                    </td>
                    
                    <!-- Aksi -->
                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-2">
                            
                            
                            <!-- Edit -->
                            <a href="{{ route('furniture.edit', $item) }}" 
                               class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-all"
                               title="Edit Produk">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            
                            <!-- Delete -->
                            <form action="{{ route('furniture.destroy', $item) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Hapus produk {{ $item->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-all"
                                        title="Hapus Produk">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                
                @if($furniture->isEmpty())
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 rounded-full flex items-center justify-center">
                            <i data-lucide="package" class="w-8 h-8 text-slate-400"></i>
                        </div>
                        <p class="text-slate-500 font-medium">Belum ada produk</p>
                        <p class="text-sm text-slate-400 mt-1">Tambahkan produk pertama Anda</p>
                        <a href="{{ route('furniture.create') }}" 
                           class="inline-block mt-4 px-4 py-2 bg-amber-500 text-white rounded-xl font-semibold text-sm hover:bg-amber-600">
                            <i data-lucide="plus-circle" class="w-4 h-4 inline mr-1"></i>
                            Tambah Produk
                        </a>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    @if($furniture->hasPages())
    <div class="p-6 border-t border-slate-100">
        {{ $furniture->links() }}
    </div>
    @endif
</div>

<!-- Image Preview Modal -->
<div id="imagePreviewModal" class="fixed inset-0 bg-black/70 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-slate-200">
            <h3 class="font-bold text-slate-800">Preview Gambar</h3>
            <button onclick="closePreview()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-6">
            <img id="previewImage" src="" alt="Preview" class="w-full h-auto max-h-[70vh] object-contain">
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
    
    // Filter table by search
    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('furnitureTable');
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const name = row.getAttribute('data-name') || '';
            const category = row.getAttribute('data-category') || '';
            
            if (name.includes(filter) || category.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }
    
    // Open image preview
    function previewImage(src) {
        document.getElementById('previewImage').src = src;
        document.getElementById('imagePreviewModal').classList.remove('hidden');
        document.getElementById('imagePreviewModal').classList.add('flex');
    }
    
    // Close preview
    function closePreview() {
        document.getElementById('imagePreviewModal').classList.add('hidden');
        document.getElementById('imagePreviewModal').classList.remove('flex');
    }
    
    // Add click event to all product images
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('td img').forEach(img => {
            img.style.cursor = 'pointer';
            img.addEventListener('click', function() {
                previewImage(this.src);
            });
        });
    });
</script>
@endsection