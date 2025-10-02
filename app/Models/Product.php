<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const CATEGORIES = [
        'Electronics',
        'Fashion',
        'Home & Garden',
        'Sports & Outdoors',
        'Books',
        'Health & Beauty',
        'Automotive',
        'Food & Beverages',
        'Toys & Games',
        'Office Supplies'
    ];

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public static function getCategories()
    {
        return self::CATEGORIES;
    }
}
