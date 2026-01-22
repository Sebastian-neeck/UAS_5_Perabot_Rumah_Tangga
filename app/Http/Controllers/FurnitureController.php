<?php

namespace App\Http\Controllers;

use App\Models\Furniture;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FurnitureController extends Controller
{
    /**
     * Display a listing of the furniture.
     */
    public function index(Request $request)
    {
        $query = Furniture::with('category')->latest();
        
        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Category filter
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }
        
        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        $furniture = $query->paginate(10);
        $categories = Category::all();
        
        return view('furniture.index', compact('furniture', 'categories'));
    }

    /**
     * Show the form for creating a new furniture.
     */
    public function create()
    {
        $categories = Category::all();
        return view('furniture.create', compact('categories'));
    }

    /**
     * Store a newly created furniture in storage.
     */
    public function store(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:available,low_stock,out_of_stock',
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ], [
            'images.required' => 'Minimal upload 1 gambar',
            'images.min' => 'Minimal upload 1 gambar',
            'images.max' => 'Maksimal upload 10 gambar',
            'images.*.image' => 'File harus berupa gambar',
            'images.*.mimes' => 'Format gambar harus jpeg, png, jpg, gif, atau webp',
            'images.*.max' => 'Ukuran gambar maksimal 2MB'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Handle image uploads
        $imageFilenames = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('furniture', $filename, 'public');
                $imageFilenames[] = $filename;
            }
        }
        
        // Create furniture
        $furniture = Furniture::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'status' => $request->status,
            'images' => $imageFilenames
        ]);
        
        return redirect()->route('furniture.index')
            ->with('success', 'Produk "' . $furniture->name . '" berhasil ditambahkan!');
    }

    /**
     * Display the specified furniture.
     */
    public function show(Furniture $furniture)
    {
        return view('furniture.show', compact('furniture'));
    }

    /**
     * Show the form for editing the specified furniture.
     */
    public function edit(Furniture $furniture)
    {
        $categories = Category::all();
        return view('furniture.edit', compact('furniture', 'categories'));
    }

    /**
     * Update the specified furniture in storage.
     * FIXED VERSION - HANDLES SEEDER IMAGES (UNSPLASH) + LOCAL UPLOADS
     */
    public function update(Request $request, Furniture $furniture)
    {
        // Debug: UNCOMMENT untuk lihat apa yang terjadi
        /*
        dd([
            'REQUEST DATA' => $request->all(),
            'HAS FILES' => $request->hasFile('images'),
            'FILE COUNT' => $request->hasFile('images') ? count($request->file('images')) : 0,
            'EXISTING IMAGES FROM FORM' => $request->existing_images,
            'DELETED IMAGES JSON' => $request->deleted_images,
            'CURRENT FURNITURE IMAGES' => $furniture->images,
            'CURRENT IMAGE URLS' => $furniture->image_urls
        ]);
        */
        
        // Validasi
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:available,low_stock,out_of_stock',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'existing_images' => 'nullable|array',
            'deleted_images' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // ============ HANDLE IMAGES ============
        $imageFilenames = [];
        
        // 1. Start with existing images from form
        if ($request->has('existing_images') && is_array($request->existing_images)) {
            $imageFilenames = $request->existing_images;
        }
        
        // 2. Handle deleted images (baik dari seeder maupun upload)
        if ($request->filled('deleted_images')) {
            try {
                $deletedImages = json_decode($request->deleted_images, true);
                
                if (is_array($deletedImages)) {
                    foreach ($deletedImages as $deletedImage) {
                        // Hapus dari array imageFilenames
                        if (($key = array_search($deletedImage, $imageFilenames)) !== false) {
                            // PERBAIKAN: Hanya hapus file fisik jika bukan URL Unsplash
                            if ($this->isLocalFile($deletedImage)) {
                                Storage::delete('public/furniture/' . $deletedImage);
                            }
                            unset($imageFilenames[$key]);
                        }
                    }
                    $imageFilenames = array_values($imageFilenames); // Reset keys
                }
            } catch (\Exception $e) {
                // Skip jika error
            }
        }
        
        // 3. Handle new uploaded files
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('furniture', $filename, 'public');
                    $imageFilenames[] = $filename;
                }
            }
        }
        
        // ============ UPDATE FURNITURE ============
        $updateData = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'status' => $request->status,
            'images' => $imageFilenames
        ];
        
        $furniture->update($updateData);
        
        // Update slug jika nama berubah
        if ($furniture->wasChanged('name')) {
            $furniture->slug = \Illuminate\Support\Str::slug($request->name);
            $furniture->save();
        }
        
        return redirect()->route('furniture.index')
            ->with('success', 'Produk "' . $furniture->name . '" berhasil diperbarui!');
    }

    /**
     * Remove the specified furniture from storage.
     */
    public function destroy(Furniture $furniture)
    {
        // Check if furniture has orders
        if ($furniture->orderItems()->count() > 0) {
            return redirect()->route('furniture.index')
                ->with('error', 'Tidak dapat menghapus produk yang memiliki pesanan!');
        }
        
        $name = $furniture->name;
        
        // Delete associated images from storage
        if (!empty($furniture->images) && is_array($furniture->images)) {
            foreach ($furniture->images as $imageFilename) {
                // Hapus file fisik hanya jika file lokal
                if ($this->isLocalFile($imageFilename)) {
                    Storage::delete('public/furniture/' . $imageFilename);
                }
            }
        }
        
        $furniture->delete();
        
        return redirect()->route('furniture.index')
            ->with('success', 'Produk "' . $name . '" berhasil dihapus!');
    }

    /**
     * Helper method: Check if image is local file (not Unsplash URL)
     */
    private function isLocalFile($filename)
    {
        // Jika string kosong
        if (empty($filename) || !is_string($filename)) {
            return false;
        }
        
        // Jika adalah URL (Unsplash atau lainnya)
        if (str_starts_with($filename, 'http://') || 
            str_starts_with($filename, 'https://')) {
            return false;
        }
        
        // Jika mengandung ekstensi file gambar
        $imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
        foreach ($imageExtensions as $ext) {
            if (str_ends_with(strtolower($filename), $ext)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Upload single image via AJAX
     */
    public function uploadImage(Request $request)
    {
        try {
            // Jika upload file
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
                ]);
                
                $filename = 'furniture-' . time() . '-' . uniqid() . '.' . $request->image->extension();
                $path = $request->image->storeAs('public/furniture', $filename);
                
                return response()->json([
                    'success' => true,
                    'filename' => $filename,
                    'url' => asset('storage/furniture/' . $filename)
                ]);
            }
            // Jika URL (Unsplash)
            elseif ($request->has('url')) {
                $request->validate([
                    'url' => 'required|url'
                ]);
                
                return response()->json([
                    'success' => true,
                    'filename' => $request->url,
                    'url' => $request->url
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file atau URL yang diupload'
            ], 400);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload gambar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update stock via AJAX
     */
    public function updateStock(Request $request, Furniture $furniture)
    {
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);
        
        $furniture->update([
            'stock' => $request->stock,
            'status' => $request->stock <= 0 ? 'out_of_stock' : 
                       ($request->stock <= 5 ? 'low_stock' : 'available')
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil diperbarui!',
            'stock' => $furniture->stock,
            'status' => $furniture->status,
            'status_label' => $furniture->stock_status_label
        ]);
    }

    /**
     * Get furniture images for edit form via AJAX
     */
    public function getImages(Furniture $furniture)
    {
        return response()->json([
            'success' => true,
            'images' => $furniture->images ?? [],
            'image_urls' => $furniture->image_urls ?? []
        ]);
    }

    /**
     * Delete specific image via AJAX
     */
    public function deleteImage(Furniture $furniture, Request $request)
    {
        $request->validate([
            'image_url' => 'required|string'
        ]);
        
        $imageUrl = $request->image_url;
        $images = $furniture->images ?? [];
        
        if (($key = array_search($imageUrl, $images)) !== false) {
            // Hapus file fisik hanya jika file lokal
            if ($this->isLocalFile($imageUrl)) {
                $filename = basename($imageUrl);
                Storage::delete('public/furniture/' . $filename);
            }
            
            // Hapus dari array
            unset($images[$key]);
            $images = array_values($images);
            
            // Update furniture
            $furniture->update(['images' => $images]);
            
            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil dihapus!'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Gambar tidak ditemukan!'
        ], 404);
    }
    
    /**
     * Export furniture data to CSV
     */
    public function exportCsv()
    {
        $furniture = Furniture::with('category')->get();
        
        $filename = 'furniture_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($furniture) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID', 'Nama', 'Kategori', 'SKU', 'Harga', 'Stok', 'Status',
                'Deskripsi', 'Jumlah Gambar', 'Dibuat', 'Diupdate'
            ]);
            
            // Data
            foreach ($furniture as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->name,
                    $item->category->name ?? '-',
                    $item->sku,
                    $item->price,
                    $item->stock,
                    $item->status,
                    substr(strip_tags($item->description), 0, 100),
                    count($item->images ?? []),
                    $item->created_at->format('Y-m-d H:i:s'),
                    $item->updated_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}