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
    
    <form action="{{ route('furniture.update', $furniture) }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-8">
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
        
        <!-- Image Upload Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-700">Gambar Produk</h2>
                <button type="button" 
                        onclick="openImageUpload()"
                        class="flex items-center gap-2 px-4 py-2 bg-amber-500 text-white text-sm font-bold rounded-xl hover:bg-amber-600">
                    <i data-lucide="upload" class="w-4 h-4"></i>
                    Upload Gambar Baru
                </button>
            </div>
            
            <!-- Image Preview Container -->
            <div id="imagePreviewContainer" 
                 class="grid grid-cols-2 md:grid-cols-4 gap-4 min-h-[200px] border-2 border-dashed border-slate-200 rounded-2xl p-6">
                <!-- Images will be loaded here -->
            </div>
            
            <!-- Hidden input untuk images -->
            <input type="hidden" name="images" id="imagesInput" value='@json($furniture->images)'>
            
            <p class="text-sm text-slate-500 mt-2">
                Klik gambar untuk menghapus. Upload gambar baru untuk menambah.
            </p>
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
                Update Produk
            </button>
            <a href="{{ route('furniture.index') }}" 
               class="px-8 py-3 border border-slate-300 text-slate-700 font-bold rounded-xl hover:bg-slate-50">
                Batal
            </a>
            <button type="button"
                    onclick="if(confirm('Hapus produk ini?')) document.getElementById('deleteForm').submit();"
                    class="px-8 py-3 border border-red-300 text-red-600 font-bold rounded-xl hover:bg-red-50">
                Hapus Produk
            </button>
        </div>
    </form>
    
    <!-- Delete Form (hidden) -->
    <form id="deleteForm" action="{{ route('furniture.destroy', $furniture) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

<!-- Image Upload Modal (same as create) -->
<div id="imageUploadModal" 
     class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-800">Upload Gambar Baru</h3>
            <button onclick="closeImageUpload()" 
                    class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <!-- Option 1: Upload from URL -->
            <div class="border border-slate-200 rounded-xl p-4">
                <h4 class="font-bold text-slate-700 mb-2">Gunakan URL Gambar</h4>
                <div class="flex gap-2">
                    <input type="text" 
                           id="imageUrlInput"
                           placeholder="https://images.unsplash.com/photo-..."
                           class="flex-1 p-2 border border-slate-200 rounded-lg">
                    <button onclick="addUrlImage()"
                            class="px-4 py-2 bg-amber-500 text-white rounded-lg font-bold text-sm hover:bg-amber-600">
                        Tambah
                    </button>
                </div>
            </div>
            
            <!-- Option 2: Upload from computer -->
            <div class="border border-slate-200 rounded-xl p-4">
                <h4 class="font-bold text-slate-700 mb-2">Upload dari Komputer</h4>
                <div id="uploadArea" 
                     class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center cursor-pointer hover:border-amber-500 hover:bg-amber-50/50 transition-all"
                     onclick="document.getElementById('fileInput').click()">
                    <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-400 mx-auto mb-3"></i>
                    <p class="font-bold text-slate-700 mb-1">Klik untuk pilih file</p>
                    <p class="text-sm text-slate-500">Format: JPG, PNG, GIF (maks 2MB)</p>
                    <input type="file" 
                           id="fileInput" 
                           accept="image/*" 
                           class="hidden" 
                           onchange="handleFileUpload(this)">
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex gap-3">
            <button onclick="closeImageUpload()" 
                    class="flex-1 border border-slate-300 text-slate-700 py-2 rounded-xl font-bold hover:bg-slate-50">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
    let uploadedImages = [];
    
    // Load existing images on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Parse existing images from hidden input
        const imagesInput = document.getElementById('imagesInput');
        try {
            uploadedImages = JSON.parse(imagesInput.value);
        } catch (e) {
            uploadedImages = [];
        }
        
        updateImagePreviews();
    });
    
    // Same functions as create.blade.php
    function openImageUpload() {
        document.getElementById('imageUploadModal').classList.remove('hidden');
        document.getElementById('imageUploadModal').classList.add('flex');
    }
    
    function closeImageUpload() {
        document.getElementById('imageUploadModal').classList.add('hidden');
        document.getElementById('imageUploadModal').classList.remove('flex');
        document.getElementById('imageUrlInput').value = '';
        document.getElementById('fileInput').value = '';
    }
    
    function addUrlImage() {
        const urlInput = document.getElementById('imageUrlInput');
        const url = urlInput.value.trim();
        
        if (!url) {
            alert('Masukkan URL gambar');
            return;
        }
        
        if (!url.startsWith('http')) {
            alert('URL harus diawali dengan http:// atau https://');
            return;
        }
        
        uploadedImages.push(url);
        updateImagePreviews();
        urlInput.value = '';
        closeImageUpload();
    }
    
    function handleFileUpload(input) {
        const file = input.files[0];
        if (!file) return;
        
        if (!file.type.startsWith('image/')) {
            alert('Hanya file gambar yang diizinkan!');
            return;
        }
        
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB!');
            return;
        }
        
        // Create object URL for preview
        const objectUrl = URL.createObjectURL(file);
        uploadedImages.push(objectUrl);
        updateImagePreviews();
        
        closeImageUpload();
        input.value = '';
    }
    
    function updateImagePreviews() {
        const container = document.getElementById('imagePreviewContainer');
        const imagesInput = document.getElementById('imagesInput');
        
        // Update hidden input
        imagesInput.value = JSON.stringify(uploadedImages);
        
        // Clear container
        container.innerHTML = '';
        
        if (uploadedImages.length === 0) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center text-slate-400 col-span-full">
                    <i data-lucide="image" class="w-12 h-12 mb-2"></i>
                    <p class="text-sm">Belum ada gambar</p>
                </div>
            `;
            return;
        }
        
        // Add each image
        uploadedImages.forEach((imageUrl, index) => {
            const imgDiv = document.createElement('div');
            imgDiv.className = 'relative group';
            imgDiv.innerHTML = `
                <div class="aspect-square rounded-xl overflow-hidden border border-slate-200">
                    <img src="${imageUrl}" 
                         alt="Preview ${index + 1}" 
                         class="w-full h-full object-cover cursor-pointer"
                         onclick="removeImage(${index})">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <div class="p-2 bg-red-500 text-white rounded-full">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-1 truncate text-center">Klik untuk hapus</p>
            `;
            container.appendChild(imgDiv);
        });
        
        setTimeout(() => lucide.createIcons(), 100);
    }
    
    function removeImage(index) {
        if (confirm('Hapus gambar ini?')) {
            uploadedImages.splice(index, 1);
            updateImagePreviews();
        }
    }
</script>
@endsection