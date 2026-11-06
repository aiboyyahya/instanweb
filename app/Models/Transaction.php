<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_id',
        'phone_number',
        'address',
        'status',
        'total',
        'shipping_cost',
        'distance_km',
        'notes',
        'payment_method',
        'snap_token',
        'payment_status',
        'province',
        'city',
        'district',
        'latitude',
        'longitude',
        'postal_code',
        'courier',
        'courier_service',
        'tracking_number',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'distance_km' => 'float',
            'shipping_cost' => 'float',
            'total' => 'float',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Label metode pembayaran yang dipilih pelanggan.
     */
    public function paymentMethodLabel(): string
    {
        return match ($this->payment_method) {
            'transfer_bank' => 'Transfer Bank',
            'cod' => 'Bayar di Tempat (COD)',
            'qris' => 'QRIS / E-Wallet',
            'midtrans' => 'Midtrans',
            'manual' => 'Konfirmasi Manual',
            default => ucfirst((string) $this->payment_method),
        };
    }

    protected static function booted()
    {
        static::updating(function ($transaction) {
            // If status is being changed to 'sent' and tracking_number is empty, generate one
            if ($transaction->isDirty('status') && $transaction->status === 'sent' && empty($transaction->tracking_number)) {
                $courier = $transaction->courier ?? 'jne';
                $trackingNumber = static::generateTrackingNumber($courier);
                $transaction->tracking_number = $trackingNumber;

                // Log the generated tracking number
                \Illuminate\Support\Facades\Log::info('Generated tracking number for transaction', [
                    'transaction_id' => $transaction->id,
                    'order_code' => $transaction->order_code,
                    'tracking_number' => $trackingNumber,
                    'courier' => $courier,
                ]);
            }
        });
    }

    private static function generateTrackingNumber(string $courier): string
    {
        $normalized = strtolower(trim($courier));

        $prefixes = [
            'jne' => 'JN',
            'j&t' => 'JT',
            'sicepat' => 'SC',
            'anteraja' => 'AJ',
            'pos' => 'PS',
            'tiki' => 'TK',
            'cod / kurir toko' => 'CD',
            'cod' => 'CD',
        ];

        $prefix = $prefixes[$normalized] ?? 'TR';
        $timestamp = now()->format('ymdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));

        return $prefix . $timestamp . $random;
    }
}
