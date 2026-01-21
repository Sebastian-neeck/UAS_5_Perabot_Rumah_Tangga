<?php

namespace App\Http\Controllers;

use App\Models\Furniture;
use App\Models\Order;
use App\Models\User;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung statistik
        $totalProducts = Furniture::count();
        $totalCategories = Category::count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalRevenue = Order::where('status', 'delivered')->sum('total_amount');
        $totalUsers = User::where('role', 'user')->count();
        $lowStock = Furniture::where('stock', '<=', 5)->where('stock', '>', 0)->count();
        $outOfStock = Furniture::where('stock', 0)->count();

        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        $lowStockProducts = Furniture::where('stock', '<=', 5)
            ->where('stock', '>', 0)
            ->orderBy('stock')
            ->take(5)
            ->get();

        $topProducts = Furniture::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalOrders',
            'pendingOrders',
            'totalRevenue',
            'totalUsers',
            'lowStock',
            'outOfStock',
            'recentOrders',
            'lowStockProducts',
            'topProducts'
        ));
    }
    
    public function getStats()
    {
        $stats = [
            'total_products' => Furniture::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'low_stock' => Furniture::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock' => Furniture::where('stock', 0)->count()
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}