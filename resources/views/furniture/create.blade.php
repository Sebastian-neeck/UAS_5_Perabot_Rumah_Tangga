@extends('layouts.app')

@section('header_title', 'Tambah Produk Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tambah Produk Baru</h1>
            <p class="text-slate-500">Tambahkan produk furniture baru ke katalog</p>
        </div>
        <a href="{{ route('furniture.index') }}" 
           class="flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-slate-800">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>
    
    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
        <h3 class="font-bold text-red-700 mb-2">Terjadi kesalahan:</h3>
        <ul class="list-disc list-inside text-red-600">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <!-- PERBAIKAN: Tambah enctype="multipart/form-data" -->
    <form action="{{ route('furniture.store') }}" 
          method="POST" 
          enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-slate-200 p-8">
        @csrf
        
        <!-- Basic Info -->
        <div class="mb-8">
            <h2 class="text-lg font-bold text-slate-700 mb-4">Informasi Produk</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full p-3 border border-slate-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" 
                            class="w-full p-3 border border-slate-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                            required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Harga (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="price" 
                           value="{{ old('price') }}"
                           min="0"
                           step="1000"
                           class="w-full p-3 border border-slate-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                           required>
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Stok <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="stock" 
                           value="{{ old('stock', 0) }}"
                           min="0"
                           class="w-full p-3 border border-slate-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                           required>
                    @error('stock')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Deskripsi <span class="text-red-500">*</span>
                </label>
                <textarea name="description" 
                          rows="4"
                          class="w-full p-3 border border-slate-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                          required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- PERBAIKAN: Image Upload Section -->
        <div class="mb-8">
            <h2 class="text-lg font-bold text-slate-700 mb-4">
                Gambar Produk <span class="text-red-500">*</span>
            </h2>
            
            <!-- File input untuk multiple upload -->
            <div class="mb-4">
                <input type="file" 
                       name="images[]" 
                       id="images"
                       multiple
                       accept="image/*"
                       class="w-full p-3 border border-slate-200 rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100"
                       onchange="previewImages(event)"
                       required>
                <p class="text-sm text-slate-500 mt-2">
                    Upload minimal 1 gambar. Format: JPG, PNG, GIF (maks 2MB per file)
                </p>
                @error('images')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Preview Container -->
            <div id="imagePreviewContainer" 
                 class="grid grid-cols-2 md:grid-cols-4 gap-4 min-h-[150px] border-2 border-dashed border-slate-200 rounded-2xl p-6">
                <div class="flex flex-col items-center justify-center text-slate-400">
                    <i data-lucide="image" class="w-12 h-12 mb-2"></i>
                    <p class="text-sm">Belum ada gambar</p>
                    <p class="text-xs text-slate-400 mt-1">Upload gambar akan muncul di sini</p>
                </div>
            </div>
        </div>
        
        <!-- Status -->
        <div class="mb-8">
            <label class="block text-sm font-bold text-slate-700 mb-2">
                Status <span class="text-red-500">*</span>
            </label>
            <select name="status" 
                    class="w-full md:w-64 p-3 border border-slate-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                    required>
                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="low_stock" {{ old('status') == 'low_stock' ? 'selected' : '' }}>Stok Menipis</option>
                <option value="out_of_stock" {{ old('status') == 'out_of_stock' ? 'selected' : '' }}>Habis</option>
            </select>
            @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Submit Buttons -->
        <div class="flex gap-4">
            <button type="submit" 
                    class="px-8 py-3 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-700">
                <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                Simpan Produk
            </button>
            <a href="{{ route('furniture.index') }}" 
               class="px-8 py-3 border border-slate-300 text-slate-700 font-bold rounded-xl hover:bg-slate-50">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
    lucide.createIcons();
    
    // Preview uploaded images
    function previewImages(event) {
        const container = document.getElementById('imagePreviewContainer');
        const files = event.target.files;
        
        container.innerHTML = '';
        
        if (files.length === 0) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center text-slate-400">
                    <i data-lucide="image" class="w-12 h-12 mb-2"></i>
                    <p class="text-sm">Belum ada gambar</p>
                    <p class="text-xs text-slate-400 mt-1">Upload gambar akan muncul di sini</p>
                </div>
            `;
            return;
        }
        
        // Validasi jumlah file
        if (files.length > 10) {
            alert('Maksimal 10 gambar yang dapat diupload');
            event.target.value = '';
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center text-slate-400">
                    <i data-lucide="image" class="w-12 h-12 mb-2"></i>
                    <p class="text-sm">Belum ada gambar</p>
                    <p class="text-xs text-slate-400 mt-1">Upload gambar akan muncul di sini</p>
                </div>
            `;
            return;
        }
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Validasi ukuran file
            if (file.size > 2 * 1024 * 1024) {
                alert(`Gambar "${file.name}" melebihi 2MB`);
                continue;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const imgDiv = document.createElement('div');
                imgDiv.className = 'relative group';
                imgDiv.innerHTML = `
                    <div class="aspect-square rounded-xl overflow-hidden border border-slate-200">
                        <img src="${e.target.result}" 
                             alt="Preview ${i + 1}" 
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="text-xs text-white bg-black/50 px-2 py-1 rounded">
                                Gambar ${i + 1}
                            </span>
                        </div>
                    </div>
                `;
                container.appendChild(imgDiv);
            };
            
            reader.readAsDataURL(file);
        }
        
        // Reinitialize icons
        setTimeout(() => lucide.createIcons(), 100);
    }
</script>
@endsection