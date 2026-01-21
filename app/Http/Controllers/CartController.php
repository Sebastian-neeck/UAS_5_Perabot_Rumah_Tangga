<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Furniture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display shopping cart
     */
    public function index()
    {
        $cartItems = [];
        $total = 0;
        
        if (Auth::check()) {
            $cartItems = Cart::with('furniture')
                ->where('user_id', Auth::id())
                ->get();
            $total = $cartItems->sum('subtotal');
        } else {
            $cartSession = session()->get('cart', []);
            
            foreach ($cartSession as $itemId => $item) {
                $product = Furniture::find($itemId);
                if ($product) {
                    $cartItems[] = (object)[
                        'furniture' => $product,
                        'quantity' => $item['quantity'],
                        'subtotal' => $product->price * $item['quantity']
                    ];
                    $total += $product->price * $item['quantity'];
                }
            }
        }
        
        return view('user.cart', compact('cartItems', 'total'));
    }

    /**
     * Add item to cart (AJAX)
     */
    public function add($id, Request $request)
    {
        try {
            $product = Furniture::findOrFail($id);
            $quantity = $request->input('quantity', 1);
            
            // Check stock
            if ($product->stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock
                ]);
            }
            
            if (Auth::check()) {
                $cartItem = Cart::where('user_id', Auth::id())
                    ->where('furniture_id', $id)
                    ->first();
                
                if ($cartItem) {
                    $cartItem->quantity += $quantity;
                    $cartItem->save();
                } else {
                    Cart::create([
                        'user_id' => Auth::id(),
                        'furniture_id' => $id,
                        'quantity' => $quantity
                    ]);
                }
                
                $cartCount = Cart::where('user_id', Auth::id())->count();
            } else {
                $cart = session()->get('cart', []);
                
                if (isset($cart[$id])) {
                    $cart[$id]['quantity'] += $quantity;
                } else {
                    // Handle images data
                    $images = $product->images;
                    $firstImage = 'default-furniture.jpg';
                    
                    if (is_array($images) && !empty($images)) {
                        $firstImage = $images[0];
                    } elseif (is_string($images) && !empty($images)) {
                        $decoded = json_decode($images, true);
                        if (is_array($decoded) && !empty($decoded)) {
                            $firstImage = $decoded[0];
                        }
                    }
                    
                    $cart[$id] = [
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $quantity,
                        'image' => $firstImage,
                        'stock' => $product->stock,
                        'category' => $product->category->name ?? 'Uncategorized'
                    ];
                }
                
                session()->put('cart', $cart);
                $cartCount = count($cart);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang!',
                'cart_count' => $cartCount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Cart add error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function remove($id, Request $request)
    {
        try {
            if (Auth::check()) {
                Cart::where('user_id', Auth::id())
                    ->where('furniture_id', $id)
                    ->delete();
            } else {
                $cart = session()->get('cart', []);
                unset($cart[$id]);
                session()->put('cart', $cart);
            }
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil dihapus dari keranjang!'
                ]);
            }
            
            return redirect()->route('cart.index')
                ->with('success', 'Produk berhasil dihapus dari keranjang!');
                
        } catch (\Exception $e) {
            \Log::error('Cart remove error: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    /**
     * Update cart quantity
     */
    public function update($id, Request $request)
    {
        try {
            $quantity = $request->input('quantity', 1);
            
            if ($quantity <= 0) {
                return $this->remove($id, $request);
            }
            
            $product = Furniture::find($id);
            if ($product && $quantity > $product->stock) {
                return redirect()->back()
                    ->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock);
            }
            
            if (Auth::check()) {
                Cart::where('user_id', Auth::id())
                    ->where('furniture_id', $id)
                    ->update(['quantity' => $quantity]);
            } else {
                $cart = session()->get('cart', []);
                if (isset($cart[$id])) {
                    $cart[$id]['quantity'] = $quantity;
                    session()->put('cart', $cart);
                }
            }
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Keranjang berhasil diperbarui!'
                ]);
            }
            
            return redirect()->route('cart.index')
                ->with('success', 'Keranjang berhasil diperbarui!');
                
        } catch (\Exception $e) {
            \Log::error('Cart update error: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal memperbarui keranjang: ' . $e->getMessage());
        }
    }

    /**
     * Get cart count (API)
     */
    public function count()
    {
        try {
            $count = 0;
            
            if (Auth::check()) {
                $count = Cart::where('user_id', Auth::id())->count();
            } elseif (session()->has('cart')) {
                $count = count(session('cart'));
            }
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Cart count error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'count' => 0,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        try {
            if (Auth::check()) {
                Cart::where('user_id', Auth::id())->delete();
            } else {
                session()->forget('cart');
            }
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Keranjang berhasil dikosongkan!'
                ]);
            }
            
            return redirect()->route('cart.index')
                ->with('success', 'Keranjang berhasil dikosongkan!');
                
        } catch (\Exception $e) {
            \Log::error('Cart clear error: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengosongkan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal mengosongkan keranjang: ' . $e->getMessage());
        }
    }
}