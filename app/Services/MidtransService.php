<?php

namespace App\Services;

use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Support\Arr;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        $store = Store::first(); // Assuming single store, adjust if needed

        if (!$store || empty($store->midtrans_server_key) || empty($store->midtrans_client_key)) {
            throw new \RuntimeException('Midtrans keys belum dikonfigurasi di pengaturan toko.');
        }

        Config::$isProduction = (bool) $store->is_production;
        Config::$serverKey = $store->midtrans_server_key;
        Config::$clientKey = $store->midtrans_client_key;

        Config::$isSanitized = true;
        Config::$curlOptions = config('services.midtrans.options', []);
        if (! isset(Config::$curlOptions[CURLOPT_HTTPHEADER])) {
            Config::$curlOptions[CURLOPT_HTTPHEADER] = [];
        }
    }

    /**
     * Membuat transaksi Snap dan mengembalikan payload.
     */
    public function createTransaction(Transaction $transaction, array $customer): array
    {
        if (empty(Config::$serverKey)) {
            throw new \RuntimeException('Midtrans server key belum dikonfigurasi.');
        }

        $itemDetails = [];
        foreach ($transaction->items as $item) {
            $itemDetails[] = [
                'id' => (string) $item->product_id,
                'price' => (int) round($item->price),
                'quantity' => (int) $item->quantity,
                'name' => mb_substr($item->product->product_name, 0, 50),
            ];
        }

        // Add shipping cost as a separate line item if present
        if ($transaction->shipping_cost && $transaction->shipping_cost > 0) {
            $itemDetails[] = [
                'id' => 'shipping',
                'price' => (int) round($transaction->shipping_cost),
                'quantity' => 1,
                'name' => 'Biaya Pengiriman',
            ];
        }

        $transactionPayload = [
            'transaction_details' => [
                'order_id' => $transaction->order_code,
                'gross_amount' => (int) round($transaction->total),
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => Arr::get($customer, 'name'),
                'email' => Arr::get($customer, 'email'),
                'phone' => Arr::get($customer, 'phone'),
            ],
            'callbacks' => array_filter([
                'finish' => $this->resolveFinishUrl($transaction),
                'unfinish' => config('midtrans.unfinish_url'),
                'error' => config('midtrans.error_url'),
            ]),
        ];

        $result = Snap::createTransaction($transactionPayload);

        return is_array($result) ? $result : json_decode(json_encode($result), true);
    }

    protected function resolveFinishUrl(Transaction $transaction): string
    {
        $configured = config('midtrans.finish_url');

        return $configured ?: route('checkout.success', ['id' => $transaction->id]);
    }
}
