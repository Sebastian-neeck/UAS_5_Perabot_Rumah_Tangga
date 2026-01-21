@extends('layouts.app')

@section('header_title', 'Kategori Perabot')

@section('content')
<div class="space-y-6">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4">
        ✅ {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl mb-4">
        ❌ {{ session('error') }}
    </div>
    @endif

    <!-- Header & Add Form (SIMPLE - NO MODAL) -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Tambah Kategori Baru</h3>
        <form action="{{ route('categories.store') }}" method="POST" id="categoryForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <input type="text" 
                           name="name" 
                           required
                           placeholder="Nama Kategori"
                           class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('name') }}">
                </div>
                <div>
                    <button type="submit" 
                            class="w-full px-4 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                        Tambah Kategori
                    </button>
                </div>
            </div>
            <div class="mt-4">
                <textarea name="description" 
                          placeholder="Deskripsi (opsional)"
                          rows="2"
                          class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
            </div>
        </form>
    </div>

    <!-- Categories List -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($categories as $category)
        <div class="bg-white p-6 rounded-2xl border border-slate-200 hover:shadow-lg transition-shadow">
            <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center mb-4">
                <i data-lucide="folder" class="text-indigo-600 w-8 h-8"></i>
            </div>
            
            <h4 class="text-lg font-bold text-slate-800 mb-1">{{ $category->name }}</h4>
            <p class="text-sm text-slate-500 mb-4">
                {{ $category->description ?: 'Tidak ada deskripsi' }}
            </p>
            
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <span class="text-xs font-medium px-3 py-1 bg-green-100 text-green-600 rounded-full">
                    {{ $category->furniture_count }} Produk
                </span>
                <div class="flex gap-2">
                    <!-- Edit Form -->
                    <form action="{{ route('categories.update', $category) }}" method="POST" 
                          class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" value="{{ $category->name }}">
                        <input type="hidden" name="description" value="{{ $category->description }}">
                        <button type="submit" 
                                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                title="Edit">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </button>
                    </form>
                    
                    <!-- Delete Form -->
                    <form action="{{ route('categories.destroy', $category) }}" method="POST"
                          onsubmit="return confirm('Yakin hapus kategori {{ $category->name }}?')"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                title="Hapus"
                                {{ $category->furniture_count > 0 ? 'disabled' : '' }}>
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
// Debugging script
document.addEventListener('DOMContentLoaded', function() {
    console.log('Categories page loaded');
    
    // Log CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    console.log('CSRF Token from meta:', csrfToken);
    
    // Log form submission
    const form = document.getElementById('categoryForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitted');
            console.log('Form data:', {
                name: this.name.value,
                description: this.description.value,
                _token: this._token.value
            });
        });
    }
    
    // Add CSRF token to all forms
    document.querySelectorAll('form').forEach(form => {
        if (!form.querySelector('[name="_token"]')) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken;
            form.appendChild(tokenInput);
            console.log('Added CSRF token to form:', form);
        }
    });
});
</script>
@endsection