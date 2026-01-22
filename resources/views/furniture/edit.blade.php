@extends('layouts.app')

@section('header_title', 'Edit Produk')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Produk: {{ $furniture->name }}</h1>
            <p class="text-slate-500">Perbarui informasi produk furniture</p>
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
    <form action="{{ route('furniture.update', $furniture) }}" 
          method="POST" 
          enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-slate-200 p-8">
        @csrf
        @method('PUT')
        
        <!-- Basic Info -->
        <div class="mb-8">
            <h2 class="text-lg font-bold text-slate-700 mb-4">Informasi Produk</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Produk *</label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $furniture->name) }}"
                           class="w-full p-3 border border-slate-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Kategori *</label>
                    <select name="category_id" 
                            class="w-full p-3 border border-slate-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                            required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $furniture->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Harga (Rp) *</label>
                    <input type="number" 
                           name="price" 
                           value="{{ old('price', $furniture->price) }}"
                           min="0"
                           step="1000"
                           class="w-full p-3 border border-slate-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Stok *</label>
                    <input type="number" 
                           name="stock" 
                           value="{{ old('stock', $furniture->stock) }}"
                           min="0"
                           class="w-full p-3 border border-slate-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                           required>
                </div>
            </div>
            
            <div class="mt-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi *</label>
                <textarea name="description" 
                          rows="4"
                          class="w-full p-3 border border-slate-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                          required>{{ old('description', $furniture->description) }}</textarea>
            </div>
        </div>
        
        <!-- PERBAIKAN: Image Upload Section -->
        <div class="mb-8">
            <h2 class="text-lg font-bold text-slate-700 mb-4">Gambar Produk</h2>
            
            <!-- 1. Existing Images -->
            @if(!empty($furniture->images) && is_array($furniture->images) && count($furniture->images) > 0)
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-3">Gambar yang tersedia:</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="existingImagesContainer">
                    @foreach($furniture->images as $index => $image)
                    @php
                        // Get image URL menggunakan getter dari model
                        $imageUrls = $furniture->image_urls;
                        $imageUrl = $imageUrls[$index] ?? '#';
                        $isUrl = str_starts_with($image, 'http');
                    @endphp
                    <div class="relative group existing-image" data-image="{{ $image }}">
                        <div class="aspect-square rounded-xl overflow-hidden border border-slate-200">
                            <img src="{{ $imageUrl }}" 
                                 alt="Gambar {{ $index + 1 }}" 
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button type="button" 
                                        onclick="markImageForDeletion('{{ $image }}', this)"
                                        class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 truncate text-center">
                            {{ $isUrl ? 'Unsplash Image' : (strlen($image) > 15 ? substr($image, 0, 15) . '...' : $image) }}
                        </p>
                        <!-- Hidden input untuk existing images -->
                        <input type="hidden" name="existing_images[]" value="{{ $image }}">
                    </div>
                    @endforeach
                </div>
                <!-- Hidden input untuk gambar yang akan dihapus -->
                <input type="hidden" name="deleted_images" id="deletedImagesInput" value="[]">
            </div>
            @endif
            
            <!-- 2. New Image Upload -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Tambah Gambar Baru (Opsional)
                </label>
                <input type="file" 
                       name="images[]" 
                       id="newImages"
                       multiple
                       accept="image/*"
                       class="w-full p-3 border border-slate-200 rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100"
                       onchange="previewNewImages(event)">
                <p class="text-sm text-slate-500 mt-2">
                    Format: JPG, PNG, GIF, WEBP (maks 2MB per file)
                </p>
            </div>
            
            <!-- 3. New Images Preview -->
            <div id="newImagePreviewContainer" 
                 class="grid grid-cols-2 md:grid-cols-4 gap-4 min-h-[100px] mt-4">
                <!-- Preview akan muncul di sini -->
            </div>
        </div>
        
        <!-- Status -->
        <div class="mb-8">
            <label class="block text-sm font-bold text-slate-700 mb-2">Status *</label>
            <select name="status" 
                    class="w-full md:w-64 p-3 border border-slate-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                    required>
                <option value="available" {{ old('status', $furniture->status) == 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="low_stock" {{ old('status', $furniture->status) == 'low_stock' ? 'selected' : '' }}>Stok Menipis</option>
                <option value="out_of_stock" {{ old('status', $furniture->status) == 'out_of_stock' ? 'selected' : '' }}>Habis</option>
            </select>
        </div>
        
        <!-- Submit Buttons -->
        <div class="flex gap-4">
            <button type="submit" 
                    class="px-8 py-3 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-700">
                <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                Update Produk
            </button>
            <a href="{{ route('furniture.index') }}" 
               class="px-8 py-3 border border-slate-300 text-slate-700 font-bold rounded-xl hover:bg-slate-50">
                Batal
            </a>
        </div>
    </form>
    
    <!-- Delete Form -->
    <form id="deleteForm" action="{{ route('furniture.destroy', $furniture) }}" method="POST" class="mt-6">
        @csrf
        @method('DELETE')
        <button type="button"
                onclick="if(confirm('Yakin hapus produk {{ $furniture->name }}? Tindakan ini tidak dapat dibatalkan!')) this.form.submit();"
                class="px-6 py-2 border border-red-300 text-red-600 text-sm font-bold rounded-xl hover:bg-red-50">
            <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i>
            Hapus Produk
        </button>
    </form>
</div>

<script>
    lucide.createIcons();
    let deletedImages = [];
    
    // Mark image for deletion
    function markImageForDeletion(imageFilename, button) {
        if (confirm('Hapus gambar ini?')) {
            // Tambah ke array deleted images
            if (!deletedImages.includes(imageFilename)) {
                deletedImages.push(imageFilename);
                document.getElementById('deletedImagesInput').value = JSON.stringify(deletedImages);
            }
            
            // Tandai elemen sebagai akan dihapus
            const imageDiv = button.closest('.existing-image');
            imageDiv.classList.add('opacity-50', 'border-red-300');
            
            // Sembunyikan tombol hapus
            button.style.display = 'none';
            
            // Nonaktifkan hidden input untuk image ini
            const hiddenInput = imageDiv.querySelector('input[name="existing_images[]"]');
            hiddenInput.disabled = true;
        }
    }
    
    // Preview new uploaded images
    function previewNewImages(event) {
        const container = document.getElementById('newImagePreviewContainer');
        const files = event.target.files;
        
        container.innerHTML = '';
        
        if (files.length === 0) {
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
                    <div class="aspect-square rounded-lg overflow-hidden border border-slate-200">
                        <img src="${e.target.result}" 
                             alt="Preview ${i + 1}" 
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button type="button" 
                                    onclick="removeNewImage(this, ${i})"
                                    class="p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600">
                                <i data-lucide="x" class="w-3 h-3"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 truncate">${file.name}</p>
                    <p class="text-xs text-slate-400">${(file.size / 1024).toFixed(0)} KB</p>
                `;
                container.appendChild(imgDiv);
                lucide.createIcons();
            };
            
            reader.readAsDataURL(file);
        }
    }
    
    // Remove new image preview and from file input
    function removeNewImage(button, index) {
        if (confirm('Hapus gambar ini dari upload?')) {
            const imgDiv = button.closest('.relative');
            imgDiv.remove();
            
            // Hapus file dari input (DataTransfer approach)
            const input = document.getElementById('newImages');
            const dt = new DataTransfer();
            
            // Salin semua file kecuali yang dihapus
            for (let i = 0; i < input.files.length; i++) {
                if (i !== index) {
                    dt.items.add(input.files[i]);
                }
            }
            
            input.files = dt.files;
        }
    }
</script>
@endsection