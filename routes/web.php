<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Furniture;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderItem;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FurnitureController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController; // TAMBAH INI

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== PUBLIC ROUTES ====================

// Home & Catalog
Route::get('/', [HomeController::class, 'index'])->name('user.catalog');

// Auth Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.post');
    Route::post('/register', 'register')->name('register.post');
    Route::post('/logout', 'logout')->name('logout');
});

// Cart Routes (available for both guest and authenticated users)
Route::controller(CartController::class)->prefix('cart')->group(function () {
    Route::get('/', 'index')->name('cart.index');
    Route::post('/add/{id}', 'add')->name('cart.add');
    Route::post('/remove/{id}', 'remove')->name('cart.remove');
    Route::post('/update/{id}', 'update')->name('cart.update');
    Route::post('/clear', 'clear')->name('cart.clear');
    Route::get('/count', 'count')->name('cart.count');
});

// Guest Checkout - redirect to login
Route::get('/checkout', function () {
    return redirect()->route('login')
        ->with('error', 'Silakan login terlebih dahulu untuk checkout.');
})->name('guest.checkout');

// ==================== AUTHENTICATED USER ROUTES ====================
Route::middleware(['auth'])->group(function () {
    
    // User Profile
    Route::controller(UserController::class)->prefix('profile')->group(function () {
        Route::get('/', 'profile')->name('profile');
        Route::put('/update', 'updateProfile')->name('profile.update');
    });
    
    // User Orders
    Route::controller(OrderController::class)->prefix('my-orders')->group(function () {
        Route::get('/', 'userOrders')->name('user.orders');
        Route::get('/{orderCode}', 'userOrderDetail')->name('user.order.detail');
    });
    
    // User Payments
    Route::get('/my-payments', [PaymentController::class, 'myPayments'])->name('payments.my');
    
    // Checkout Routes
    Route::controller(OrderController::class)->group(function () {
        Route::get('/checkout', 'checkout')->name('user.checkout');
        Route::post('/checkout/process', 'processCheckout')->name('checkout.process');
        Route::get('/checkout/success/{orderCode}', 'checkoutSuccess')->name('checkout.success');
        
        // Order Management
        Route::post('/order/{orderCode}/cancel', 'cancelOrder')->name('order.cancel');
    });
    
    // LEGACY: Payment upload via order code (untuk compatibility dengan view lama)
    Route::post('/payment/upload/{orderCode}', [OrderController::class, 'uploadPaymentProof'])->name('payment.upload');
    
    // Quick Actions
    Route::post('/clear-cart-and-logout', function () {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->delete();
        }
        session()->forget('cart');
        
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Berhasil keluar dan keranjang dikosongkan.');
    })->name('clear.cart.logout');
});

// ==================== PAYMENT ROUTES (Public & Auth) ====================
// Public Payment Pages
Route::get('/payment/{paymentCode}', [PaymentController::class, 'showPaymentPage'])->name('payment.page');
Route::get('/payment/{paymentCode}/status', [PaymentController::class, 'getStatus'])->name('payment.status');
Route::get('/payment/{paymentCode}/invoice', [PaymentController::class, 'generateInvoice'])->name('payment.invoice');

// Auth Payment Actions
Route::middleware(['auth'])->group(function () {
    Route::post('/payment/{paymentCode}/upload-proof', [PaymentController::class, 'uploadProof'])->name('payment.upload-proof');
});

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // Dashboard
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/dashboard/stats', 'getStats')->name('dashboard.stats');
    });
    
    // Furniture Management
    Route::controller(FurnitureController::class)->prefix('furniture')->group(function () {
        Route::get('/', 'index')->name('furniture.index');
        Route::get('/create', 'create')->name('furniture.create');
        Route::post('/', 'store')->name('furniture.store');
        Route::get('/{furniture}/edit', 'edit')->name('furniture.edit');
        Route::put('/{furniture}', 'update')->name('furniture.update');
        Route::delete('/{furniture}', 'destroy')->name('furniture.destroy');
        Route::post('/upload-image', 'uploadImage')->name('furniture.upload-image');
        Route::post('/{furniture}/update-stock', 'updateStock')->name('furniture.update-stock');
    });
    
    // Category Management
    Route::controller(CategoryController::class)->prefix('categories')->group(function () {
        Route::get('/', 'index')->name('categories.index');
        Route::post('/', 'store')->name('categories.store');
        Route::put('/{category}', 'update')->name('categories.update');
        Route::delete('/{category}', 'destroy')->name('categories.destroy');
    });
    
    // Order Management
    Route::controller(OrderController::class)->prefix('orders')->group(function () {
        Route::get('/', 'index')->name('orders.index');
        Route::get('/{order}', 'show')->name('orders.show');
        Route::put('/{order}/status', 'updateStatus')->name('orders.update.status');
        Route::put('/{order}/payment-status', 'updatePaymentStatus')->name('orders.update.payment.status');
        Route::post('/{order}/verify-payment', 'verifyPayment')->name('orders.verify.payment');
        Route::post('/{order}/reject-payment', 'rejectPayment')->name('orders.reject.payment');
    });
    
    // Payment Management
    Route::controller(PaymentController::class)->prefix('payments')->group(function () {
        Route::get('/', 'index')->name('payments.index');
        Route::get('/create', 'create')->name('payments.create');
        Route::post('/', 'store')->name('payments.store');
        Route::get('/{payment}', 'show')->name('payments.show');
        Route::get('/{payment}/edit', 'edit')->name('payments.edit');
        Route::put('/{payment}', 'update')->name('payments.update');
        Route::delete('/{payment}', 'destroy')->name('payments.destroy');
        Route::post('/{payment}/verify', 'verifyPayment')->name('payments.verify');
        Route::get('/{payment}/download-proof', 'downloadProof')->name('payments.download-proof');
        Route::get('/instructions/{method}/{provider?}', 'getInstructions')->name('payments.instructions');
    });
    
    // User Management
    Route::get('/users', function () {
        $users = User::where('role', 'user')
            ->withCount('orders')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.users', compact('users'));
    })->name('users.index');
    
    // Reports
    Route::get('/reports', function () {
        $startDate = request()->input('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = request()->input('end_date', now()->format('Y-m-d'));
        
        $orders = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $totalSales = $orders->where('status', 'delivered')->sum('total_amount');
        $totalOrders = $orders->count();
        $pendingOrders = $orders->where('status', 'pending')->count();
        $completedOrders = $orders->where('status', 'delivered')->count();
        
        return view('admin.reports', compact(
            'orders',
            'totalSales',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'startDate',
            'endDate'
        ));
    })->name('reports');
    
    // Export Data
    Route::get('/export/orders', function () {
        $orders = Order::with(['user', 'items.furniture'])
            ->whereBetween('created_at', [request('start_date'), request('end_date')])
            ->get();
            
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'Order Code',
                'Customer',
                'Email',
                'Total Amount',
                'Status',
                'Payment Method',
                'Payment Status',
                'Date',
                'Items Count'
            ]);
            
            // Data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_code,
                    $order->customer_name,
                    $order->user->email,
                    $order->total_amount,
                    $order->status,
                    $order->payment_method,
                    $order->payment_status,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->items->count()
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    })->name('export.orders');
    
    // System Settings
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');
    
    // Statistics API
    Route::get('/stats', function () {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');
        $thisMonth = now()->format('Y-m');
        
        return response()->json([
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'today_revenue' => Order::where('status', 'delivered')->whereDate('created_at', $today)->sum('total_amount'),
            'yesterday_orders' => Order::whereDate('created_at', $yesterday)->count(),
            'yesterday_revenue' => Order::where('status', 'delivered')->whereDate('created_at', $yesterday)->sum('total_amount'),
            'monthly_revenue' => Order::where('status', 'delivered')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
            'total_users' => User::count(),
            'active_users' => User::whereHas('orders', function ($q) {
                $q->where('created_at', '>=', now()->subMonth());
            })->count(),
            'low_stock_count' => Furniture::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock_count' => Furniture::where('stock', 0)->count(),
            'pending_payments' => Order::where('payment_status', 'pending_verification')->count()
        ]);
    })->name('stats');
});

// ==================== UTILITY & DEBUG ROUTES ====================
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/test-cart', function () {
        echo "<h1>Session Cart Data</h1>";
        echo "<pre>";
        print_r(session('cart'));
        echo "</pre>";
        
        echo "<h1>Database Cart</h1>";
        $cart = Cart::with('furniture')->where('user_id', Auth::id())->get();
        echo "<pre>";
        print_r($cart->toArray());
        echo "</pre>";
        
        echo "<h1>Cart Total</h1>";
        echo "Count: " . $cart->count() . "<br>";
        echo "Subtotal: " . $cart->sum('subtotal');
    })->name('test.cart');
    
    Route::get('/test-images', function () {
        $furniture = Furniture::limit(3)->get();
        foreach ($furniture as $item) {
            echo "<h2>" . $item->name . "</h2>";
            echo "<pre>";
            var_dump($item->images);
            echo "</pre>";
            echo "Type: " . gettype($item->images) . "<br>";
            echo "Is array: " . (is_array($item->images) ? 'Yes' : 'No') . "<br>";
            echo "First image: " . $item->first_image . "<br>";
            echo "<hr>";
        }
    })->name('test.images');
    
    Route::get('/test-orders', function () {
        $orders = Order::with(['user', 'items.furniture'])->limit(3)->get();
        foreach ($orders as $order) {
            echo "<h2>Order: " . $order->order_code . "</h2>";
            echo "Total: " . $order->formatted_total . "<br>";
            echo "Subtotal: " . $order->formatted_subtotal . "<br>";
            echo "Shipping: " . $order->formatted_shipping . "<br>";
            echo "Tax: " . $order->formatted_tax . "<br>";
            echo "Calculated Total: " . number_format($order->calculated_total, 0, ',', '.') . "<br>";
            echo "Status: " . $order->status_text . "<br>";
            echo "Payment: " . $order->payment_method_text . "<br>";
            echo "<hr>";
        }
    })->name('test.orders');
    
    // Cron job route (development only)
    Route::get('/cron/check-expired-payments', [PaymentController::class, 'checkExpiredPayments'])
         ->name('cron.expired-payments');
});

// ==================== MIGRATION ROUTES (Development Only) ====================
Route::get('/migrate-fresh', function () {
    if (app()->environment('local') && Auth::check() && Auth::user()->isAdmin()) {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh --seed');
        return 'Database migrated and seeded successfully!';
    }
    return abort(403);
});

// ==================== FALLBACK ROUTE ====================
Route::fallback(function () {
    return redirect()->route('user.catalog')
        ->with('error', 'Halaman yang Anda cari tidak ditemukan.');
});