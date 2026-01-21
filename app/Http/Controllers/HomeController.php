<?php

namespace App\Http\Controllers;

use App\Models\Furniture;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display catalog/homepage
     */
    public function index(Request $request)
    {
        $query = Furniture::with('category')
            ->where('status', '!=', 'out_of_stock');
        
        // Search filter
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('category', function($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }
        
        // Category filter
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }
        
        $furniture = $query->latest()->paginate(12);
        $categories = Category::withCount('furniture')->get();
        
        return view('user.catalog', compact('furniture', 'categories'));
    }
}