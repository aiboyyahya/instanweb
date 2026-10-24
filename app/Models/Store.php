<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
