<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'description', 'stock', 'product_number']; // 其他可填充的属性

    protected $attributes = [
        'description' => '商品描述',
        'stock' => 5,
    ];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'shop_cart');
    }

    public function classification()
    {
        return $this->hasMany(Classification::class);
    }
}
