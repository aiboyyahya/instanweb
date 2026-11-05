<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'title',
        'slug',
        'description',
        'image',
        'thumbnail',
        'category_id',
        'user_id',
    ];

    protected static function booted(): void
    {
        static::saving(function (Article $article) {
            if (blank($article->slug)) {
                $article->slug = static::uniqueSlug($article->title, $article->id);
            }
        });
    }

    /**
     * Slug unik global agar tautan artikel lama tetap dapat diakses.
     */
    public static function uniqueSlug(?string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug((string) ($title ?: 'artikel'));
        $base = $base !== '' ? $base : 'artikel';
        $slug = $base;
        $suffix = 2;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Toko pemilik artikel ini.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
