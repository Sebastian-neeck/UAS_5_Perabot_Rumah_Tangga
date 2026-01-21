<?php
// app/Models/Cart.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['session_id', 'user_id', 'furniture_id', 'quantity'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function furniture()
    {
        return $this->belongsTo(Furniture::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->furniture->price;
    }

    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}