<?php
// app/Models/Furniture.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Furniture extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'sku',
        'images',
        'category_id',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'images' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = [
        'display_image',
        'formatted_price',
        'stock_status',
        'first_image'
    ];

    // ============ RELATIONSHIPS ============
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    // ============ ACCESSORS & MUTATORS ============
    
    /**
     * Get display image (utama untuk frontend)
     */
    public function getDisplayImageAttribute()
    {
        $images = $this->images;
        
        // Jika kosong, return default
        if (empty($images)) {
            return 'https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?w=400&auto=format&fit=crop';
        }
        
        // Ambil gambar pertama
        $firstImage = is_array($images) ? $images[0] : $images;
        
        // Jika sudah URL lengkap (Unsplash)
        if (is_string($firstImage) && str_starts_with($firstImage, 'http')) {
            return $firstImage;
        }
        
        // Jika nama file lokal
        if (is_string($firstImage)) {
            // Cek apakah sudah ada path furniture/
            if (str_starts_with($firstImage, 'furniture/')) {
                return asset('storage/' . $firstImage);
            }
            
            return asset('storage/furniture/' . $firstImage);
        }
        
        // Fallback
        return 'https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?w=400&auto=format&fit=crop';
    }

    /**
     * Alias for compatibility (first_image)
     */
    public function getFirstImageAttribute()
    {
        return $this->display_image;
    }

    /**
     * Alias for image_url (compatibility)
     */
    public function getImageUrlAttribute()
    {
        return $this->display_image;
    }

    /**
     * Get all image URLs
     */
    public function getImageUrlsAttribute()
    {
        $images = $this->images;
        $urls = [];
        
        if (is_array($images)) {
            foreach ($images as $image) {
                if (str_starts_with($image, 'http')) {
                    $urls[] = $image;
                } else {
                    $urls[] = asset('storage/furniture/' . $image);
                }
            }
        } elseif (is_string($images)) {
            try {
                $decoded = json_decode($images, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $image) {
                        if (str_starts_with($image, 'http')) {
                            $urls[] = $image;
                        } else {
                            $urls[] = asset('storage/furniture/' . $image);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Do nothing
            }
        }
        
        return $urls;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get stock status based on quantity
     */
    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock <= 5) {
            return 'low_stock';
        }
        return 'available';
    }

    /**
     * Get stock status label
     */
    public function getStockStatusLabelAttribute()
    {
        $status = $this->stock_status;
        
        return match($status) {
            'available' => 'Tersedia',
            'low_stock' => 'Stok Menipis',
            'out_of_stock' => 'Habis',
            default => 'Unknown'
        };
    }

    /**
     * Check if product is available
     */
    public function getIsAvailableAttribute()
    {
        return $this->stock_status === 'available' || $this->stock_status === 'low_stock';
    }

    /**
     * Check if product is out of stock
     */
    public function getIsOutOfStockAttribute()
    {
        return $this->stock_status === 'out_of_stock';
    }

    /**
     * Custom accessor untuk images dengan handling berbagai format
     */
    protected function getImagesAttribute($value)
    {
        // Jika null, return empty array
        if (is_null($value)) {
            return [];
        }
        
        // Jika sudah array, return langsung
        if (is_array($value)) {
            return $value;
        }
        
        // Jika string, coba decode JSON
        if (is_string($value)) {
            try {
                // Bersihkan string
                $cleaned = trim($value, '"');
                $cleaned = stripslashes($cleaned);
                
                // Coba decode
                $decoded = json_decode($cleaned, true);
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
                
                // Jika tidak bisa decode, mungkin string biasa
                // Coba split by comma atau newline
                if (str_contains($cleaned, ',') || str_contains($cleaned, "\n")) {
                    $items = preg_split('/,|\n/', $cleaned);
                    $items = array_map('trim', $items);
                    $items = array_filter($items);
                    return array_values($items);
                }
                
                // Jika hanya satu item
                return [$cleaned];
                
            } catch (\Exception $e) {
                return [];
            }
        }
        
        // Fallback
        return [];
    }

    /**
     * Mutator untuk images - selalu simpan sebagai JSON array
     */
    protected function setImagesAttribute($value)
    {
        // Jika null, set empty array
        if (is_null($value)) {
            $this->attributes['images'] = json_encode([]);
            return;
        }
        
        // Jika sudah array, encode ke JSON
        if (is_array($value)) {
            $this->attributes['images'] = json_encode($value);
            return;
        }
        
        // Jika string, coba decode dulu
        if (is_string($value)) {
            try {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $this->attributes['images'] = json_encode($decoded);
                } else {
                    // Jika bukan JSON valid, anggap sebagai array dengan 1 item
                    $this->attributes['images'] = json_encode([$value]);
                }
            } catch (\Exception $e) {
                $this->attributes['images'] = json_encode([$value]);
            }
            return;
        }
        
        // Fallback
        $this->attributes['images'] = json_encode([]);
    }

    // ============ SCOPES ============
    
    /**
     * Scope untuk produk yang available (stok > 0)
     */
    public function scopeAvailable($query)
    {
        return $query->where('stock', '>', 0)
                     ->where('status', '!=', 'out_of_stock');
    }

    /**
     * Scope untuk produk low stock
     */
    public function scopeLowStock($query)
    {
        return $query->where('stock', '>', 0)
                     ->where('stock', '<=', 5)
                     ->where('status', '!=', 'out_of_stock');
    }

    /**
     * Scope untuk produk out of stock
     */
    public function scopeOutOfStock($query)
    {
        return $query->where(function($q) {
            $q->where('stock', '<=', 0)
              ->orWhere('status', 'out_of_stock');
        });
    }

    /**
     * Scope untuk search produk
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('name', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%')
              ->orWhere('sku', 'like', '%' . $searchTerm . '%');
        });
    }

    /**
     * Scope untuk filter by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        if ($categoryId && $categoryId !== 'all') {
            return $query->where('category_id', $categoryId);
        }
        return $query;
    }

    // ============ BUSINESS LOGIC ============
    
    /**
     * Kurangi stok produk
     */
    public function reduceStock($quantity)
    {
        if ($this->stock < $quantity) {
            throw new \Exception('Stok tidak mencukupi');
        }
        
        $this->stock -= $quantity;
        
        // Update status berdasarkan stok baru
        if ($this->stock <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->stock <= 5) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'available';
        }
        
        return $this->save();
    }

    /**
     * Tambah stok produk
     */
    public function increaseStock($quantity)
    {
        $this->stock += $quantity;
        
        // Update status berdasarkan stok baru
        if ($this->stock <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->stock <= 5) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'available';
        }
        
        return $this->save();
    }

    /**
     * Update stok dan status
     */
    public function updateStock($newStock)
    {
        $this->stock = $newStock;
        
        // Update status berdasarkan stok baru
        if ($this->stock <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->stock <= 5) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'available';
        }
        
        return $this->save();
    }

    /**
     * Cek apakah produk bisa ditambahkan ke cart
     */
    public function canAddToCart($quantity = 1)
    {
        return $this->is_available && $this->stock >= $quantity;
    }

    /**
     * Get available quantity for cart
     */
    public function getAvailableQuantity()
    {
        return min($this->stock, 99); // Maksimal 99 per item di cart
    }

    // ============ BOOT ============
    protected static function boot()
    {
        parent::boot();
        
        // Auto generate slug dan SKU
        static::creating(function ($furniture) {
            $furniture->slug = Str::slug($furniture->name);
            
            // Generate SKU jika belum ada
            if (empty($furniture->sku)) {
                $furniture->sku = 'FUR-' . strtoupper(uniqid());
            }
        });
        
        static::updating(function ($furniture) {
            $furniture->slug = Str::slug($furniture->name);
        });
    }
}