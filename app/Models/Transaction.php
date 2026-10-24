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
        'notes',
        'payment_method',
        'snap_token',
        'payment_status',
        'province',
        'city',
        'district',
        'postal_code',
        'courier',
        'courier_service',
        'tracking_number',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
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
        $prefixes = [
            'jne' => 'JN',
            'tiki' => 'TK',
            'pos' => 'PS',
            'jnt' => 'JT',
        ];

        $prefix = $prefixes[$courier] ?? 'TR';
        $timestamp = now()->format('ymdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));

        return $prefix . $timestamp . $random;
    }
}
