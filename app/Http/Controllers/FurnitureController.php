<?php

namespace App\Http\Controllers;

use App\Models\Furniture;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        // Validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:available,low_stock,out_of_stock',
            'images' => 'nullable|array',
            'images.*' => 'string'
        ]);
        
        // Handle images
        $images = [];
        if ($request->has('images') && is_array($request->images)) {
            $images = $request->images;
        }
        
        // Create furniture
        $furniture = Furniture::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'status' => $validated['status'],
            'images' => $images
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
     */
    public function update(Request $request, Furniture $furniture)
    {
        // Validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:available,low_stock,out_of_stock',
            'images' => 'nullable|array',
            'images.*' => 'string'
        ]);
        
        // Handle images
        if ($request->has('images') && is_array($request->images)) {
            $validated['images'] = $request->images;
        } else {
            // Keep existing images
            $validated['images'] = $furniture->images;
        }
        
        // Update furniture
        $furniture->update($validated);
        
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
        $furniture->delete();
        
        return redirect()->route('furniture.index')
            ->with('success', 'Produk "' . $name . '" berhasil dihapus!');
    }

    /**
     * Upload image via AJAX
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            
            // Generate unique filename
            $filename = 'furniture-' . time() . '-' . uniqid() . '.' . $request->image->extension();
            
            // Store image
            $path = $request->image->storeAs('public/furniture', $filename);
            
            return response()->json([
                'success' => true,
                'filename' => $filename,
                'url' => asset('storage/furniture/' . $filename)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload gambar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update stock
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
            'message' => 'Stok berhasil diperbarui!'
        ]);
    }
}