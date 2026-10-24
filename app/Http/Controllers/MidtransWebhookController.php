<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    protected $apiUrl;
    protected $token;

    public function __construct()
    {
        $this->apiUrl = env('WA_API_URL', 'https://api.fonnte.com/send');
        $this->token = env('WA_API_TOKEN');
    }

    public function handleWebhook(Request $request)
    {
        try {
            $payload = $request->all();

            Log::info('Midtrans Webhook Received', $payload);

            // Verify webhook signature using server key from database
            $store = Store::first();
            if (!$store || empty($store->midtrans_server_key)) {
                Log::error('Midtrans server key not configured in database');
                return response()->json(['status' => 'error', 'message' => 'Configuration error'], 500);
            }

            $serverKey = $store->midtrans_server_key;
            $signatureKey = $request->header('x-callback-signature') ?? '';

            // Create signature string
            $signatureString = $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $serverKey;

            // Generate expected signature
            $expectedSignature = hash('sha512', $signatureString);

            if ($signatureKey !== $expectedSignature) {
                Log::warning('Invalid webhook signature', [
                    'received' => $signatureKey,
                    'expected' => $expectedSignature
                ]);
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
            }

            $orderId = $payload['order_id'];
            $transactionStatus = $payload['transaction_status'];
            $paymentType = $payload['payment_type'];
            $fraudStatus = $payload['fraud_status'] ?? null;

            $transaction = Transaction::where('order_code', $orderId)->first();

            if (!$transaction) {
                Log::error('Transaction not found for order_id: ' . $orderId);
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            $updateData = [];
            $sendNotification = false;

            switch ($transactionStatus) {
                case 'capture':
                    if ($fraudStatus == 'challenge') {
                        $updateData['payment_status'] = 'pending';
                    } elseif ($fraudStatus == 'accept') {
                        $updateData['payment_status'] = 'settlement';
                        $updateData['status'] = 'packing';
                        $sendNotification = true;
                    }
                    break;
                case 'settlement':
                    $updateData['payment_status'] = 'settlement';
                    $updateData['status'] = 'packing';
                    $sendNotification = true;
                    break;
                case 'pending':
                    $updateData['payment_status'] = 'pending';
                    break;
                case 'deny':
                    $updateData['payment_status'] = 'deny';
                    break;
                case 'expire':
                    $updateData['payment_status'] = 'expire';
                    break;
                case 'cancel':
                    $updateData['payment_status'] = 'cancel';
                    break;
                default:
                    Log::warning('Unknown transaction status: ' . $transactionStatus);
                    break;
            }

            if (!empty($updateData)) {
                $transaction->update($updateData);
            }

            // Send WhatsApp notification if payment is successful
            if ($sendNotification) {
                app(\App\Http\Controllers\HomeController::class)->sendOrderConfirmation($transaction);
            }

            Log::info('Transaction updated', [
                'order_id' => $orderId,
                'payment_status' => $transaction->payment_status,
                'status' => $transaction->status
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Processing failed'], 500);
        }
    }
}
