<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'category_name',
        'slug',
        'icon',
    ];


    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->category_name);
            }
        });
    }


    /**
     * Toko pemilik kategori ini.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
