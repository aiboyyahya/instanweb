<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'address',
        'logo',
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

    /**
     * Pemilik akun dari toko ini.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
