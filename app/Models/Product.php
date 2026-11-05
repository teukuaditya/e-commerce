<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'brand', 'title', 'description', 'price', 'size', 'stock', 'image', 'weight',
    ];

    // Relasi dengan kategori
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Relasi dengan review
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Relasi dengan cart (dipakai guard hapus produk)
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Ringkasan rating: [ 'avg' => float|null, 'count' => int, 'stars' => int ]
     * helper untuk dipakai di view detail produk / kartu produk.
     */
    public function ratingSummary(): array
    {
        $summary = [
            'avg' => null,
            'count' => (int) $this->reviews()->count(),
            'stars' => null,
        ];

        if ($summary['count'] > 0) {
            $summary['avg'] = (float) round($this->reviews()->avg('rating'), 1);
            $summary['stars'] = (int) round($summary['avg']);
        }

        return $summary;
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
