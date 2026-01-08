<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes - FurniStock
|--------------------------------------------------------------------------
*/

// --- Bagian Pengguna (User Side) ---

// 1. Halaman Utama / Katalog
Route::get('/', function () {
    return view('user.catalog');
})->name('user.catalog');

// 2. Fitur Keranjang (Cart)
// Siapa pun bisa melihat keranjang (termasuk tamu)
Route::get('/cart', function () {
    return view('user.cart');
})->name('cart.index');

// Proses menambah barang ke keranjang
Route::post('/cart/add/{id}', function ($id) {
    // Logika simpan ke session agar tamu bisa belanja dulu
    return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
})->name('cart.add');

// 3. Halaman Checkout (PROSES VALIDASI LOGIN)
Route::get('/checkout', function () {
    /**
     * Logika Simulasi Middleware Auth:
     * Di Laravel sungguhan, kita menggunakan ->middleware('auth')
     * Di sini kita buat pengecekan manual agar Anda paham alurnya.
     */
    if (!Auth::check()) {
        // Jika user belum login, arahkan ke halaman login dengan pesan error
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk melakukan checkout.');
    }

    return view('user.checkout');
})->name('user.checkout');


// --- Bagian Auth (Login & Register Gabungan) ---

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    // Simulasi Login Sukses
    // Nantinya di sini Anda menggunakan Auth::attempt($credentials)
    return redirect()->route('user.catalog')->with('success', 'Berhasil masuk! Silakan lanjutkan belanja.');
})->name('login.post');

Route::post('/register', function (Request $request) {
    // Simulasi Registrasi Sukses
    return redirect()->route('user.catalog')->with('success', 'Akun berhasil dibuat! Silakan login.');
})->name('register.post');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/')->with('success', 'Anda telah keluar.');
})->name('logout');


// --- Bagian Admin (Admin Panel) ---
Route::prefix('admin')->group(function () {
    
    Route::get('/dashboard', function () {
        if (view()->exists('dashboard')) {
            return view('dashboard');
        }
        return "Halo, ini halaman Dashboard Admin";
    })->name('dashboard');

    Route::get('/furniture', function () {
        return "Halaman Inventaris";
    })->name('furniture.index');

    Route::get('/categories', function () {
        return "Halaman Kategori";
    })->name('categories.index');

    Route::get('/orders', function () {
        return "Halaman Pesanan Admin";
    })->name('orders.index');

});