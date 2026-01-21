<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('furniture')->orderBy('name')->get();
        return view('furniture.categories', compact('categories'));
    }
    
    public function store(Request $request)
    {
        Log::info('Category store request received', $request->all());
        
        try {
            // Validasi sederhana dulu
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);
            
            Log::info('Validation passed', $validated);
            
            // Cek duplikat manual
            $existing = Category::where('name', $request->name)->first();
            if ($existing) {
                Log::warning('Category already exists', ['name' => $request->name]);
                return redirect()->route('categories.index')
                    ->with('error', 'Kategori "' . $request->name . '" sudah ada!');
            }
            
            // Buat kategori
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'slug' => \Str::slug($request->name)
            ]);
            
            Log::info('Category created successfully', ['id' => $category->id]);
            
            return redirect()->route('categories.index')
                ->with('success', 'Kategori "' . $category->name . '" berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            Log::error('Category store error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('categories.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function update(Request $request, Category $category)
    {
        Log::info('Category update request', [
            'id' => $category->id,
            'data' => $request->all()
        ]);
        
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);
            
            // Cek nama duplikat (kecuali untuk kategori ini)
            $existing = Category::where('name', $request->name)
                ->where('id', '!=', $category->id)
                ->first();
                
            if ($existing) {
                return redirect()->route('categories.index')
                    ->with('error', 'Nama kategori sudah digunakan!');
            }
            
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'slug' => \Str::slug($request->name)
            ]);
            
            Log::info('Category updated', ['id' => $category->id]);
            
            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil diperbarui!');
                
        } catch (\Exception $e) {
            Log::error('Category update error', [
                'message' => $e->getMessage()
            ]);
            
            return redirect()->route('categories.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function destroy(Category $category)
    {
        Log::info('Category delete request', ['id' => $category->id]);
        
        try {
            if ($category->furniture()->count() > 0) {
                return redirect()->route('categories.index')
                    ->with('error', 'Tidak dapat menghapus kategori yang memiliki produk!');
            }
            
            $categoryName = $category->name;
            $category->delete();
            
            Log::info('Category deleted', ['name' => $categoryName]);
            
            return redirect()->route('categories.index')
                ->with('success', 'Kategori "' . $categoryName . '" berhasil dihapus!');
                
        } catch (\Exception $e) {
            Log::error('Category delete error', [
                'message' => $e->getMessage()
            ]);
            
            return redirect()->route('categories.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}