<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',        'slug',
        'referral_code',
        'address',
        'latitude',
        'longitude',
        'logo',
        'banner_1',
        'banner_2',
        'banner_3',
        'description',
        'instagram',
        'tiktok',
        'whatsapp',
        'facebook',
        'shopee',
        'tokopedia',
        'midtrans_client_key',
        'midtrans_server_key',
        'is_production',
        'origin_city_id',
        'origin_district_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Store $store) {
            if (blank($store->slug)) {
                $store->slug = static::uniqueSlug($store->store_name);
            }
        });

        static::saving(function (Store $store) {
            if (blank($store->slug)) {
                $store->slug = static::uniqueSlug($store->store_name, $store->id);
            }

            // Generate referral code otomatis bila belum ada.
            if (blank($store->referral_code)) {
                do {
                    $code = \Illuminate\Support\Str::random(8);
                } while (static::where('referral_code', $code)->exists());
                $store->referral_code = $code;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Pemilik akun dari toko ini.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Produk yang dijual oleh toko ini.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Kategori katalog milik toko ini.
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Artikel yang diterbitkan oleh toko ini.
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Daftar banner aktif, maksimal tiga.
     *
     * @return array<int, string>
     */
    public function bannerPaths(): array
    {
        return collect([$this->banner_1, $this->banner_2, $this->banner_3])
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Tautan referral singkat toko ini.
     */
    public function getReferralUrlAttribute(): ?string
    {
        if (blank($this->referral_code)) {
            return null;
        }

        return route('store.referral', $this->referral_code);
    }

    /**
     * Generate kode referral unik bila belum ada.
     */
    public static function ensureReferralCode(self $store): void
    {
        if (blank($store->referral_code)) {
            do {
                $code = Str::random(8);
            } while (static::where('referral_code', $code)->exists());

            $store->referral_code = $code;
            $store->save();
        }
    }

    public static function uniqueSlug(?string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug((string) ($name ?: 'toko'));
        $base = $base !== '' ? $base : 'toko';
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
}
