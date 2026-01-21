<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin() 
                ? redirect()->route('dashboard') 
                : redirect()->route('user.catalog');
        }
        
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Transfer session cart to database
            if (session()->has('cart')) {
                $cart = session()->get('cart');
                foreach ($cart as $id => $item) {
                    $existing = Cart::where('user_id', Auth::id())
                        ->where('furniture_id', $id)
                        ->first();
                    
                    if ($existing) {
                        $existing->quantity += $item['quantity'];
                        $existing->save();
                    } else {
                        Cart::create([
                            'user_id' => Auth::id(),
                            'furniture_id' => $id,
                            'quantity' => $item['quantity']
                        ]);
                    }
                }
                session()->forget('cart');
            }
            
            return Auth::user()->isAdmin()
                ? redirect()->route('dashboard')
                    ->with('success', 'Selamat datang, Admin ' . Auth::user()->name . '!')
                : redirect()->route('user.catalog')
                    ->with('success', 'Berhasil masuk! Selamat datang, ' . Auth::user()->name . '!');
        }
        
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null
        ]);
        
        Auth::login($user);
        
        // Transfer session cart
        if (session()->has('cart')) {
            $cart = session()->get('cart');
            foreach ($cart as $id => $item) {
                Cart::create([
                    'user_id' => $user->id,
                    'furniture_id' => $id,
                    'quantity' => $item['quantity']
                ]);
            }
            session()->forget('cart');
        }
        
        return redirect()->route('user.catalog')
            ->with('success', 'Akun berhasil dibuat! Selamat bergabung, ' . $user->name . '!');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Anda telah berhasil keluar.');
    }
}