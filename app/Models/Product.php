<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'brand', 'title', 'description', 'price', 'size', 'stock', 'image', 'weight'
    ];

    // Relasi dengan kategori
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Accessor untuk kolom 'size'
    public function getSizeAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    // Accessor untuk kolom 'image'
    public function getImageAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }
}
