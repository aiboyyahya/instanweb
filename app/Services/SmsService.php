<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class SmsService
{
    /**
     * Kirim SMS verifikasi melalui driver yang dikonfigurasi.
     *
     * Driver "log" dipakai pada pengembangan lokal. Untuk produksi, gunakan
     * driver "http" dan sesuaikan endpoint gateway SMS pada .env.
     */
    public function send(string $phoneNumber, string $message): void
    {
        $phoneNumber = $this->formatPhoneNumber($phoneNumber);
        $driver = (string) config('services.sms.driver', 'log');

        match ($driver) {
            'log' => Log::info('SMS verification code (log driver)', [
                'phone' => $this->maskPhoneNumber($phoneNumber),
                'message' => $message,
            ]),
            'http' => $this->sendViaHttp($phoneNumber, $message),
            default => throw new RuntimeException('Driver SMS tidak dikenal: ' . $driver),
        };
    }

    protected function sendViaHttp(string $phoneNumber, string $message): void
    {
        $endpoint = config('services.sms.endpoint');

        if (blank($endpoint)) {
            throw new RuntimeException('Endpoint SMS belum dikonfigurasi.');
        }

        $request = Http::timeout((int) config('services.sms.timeout', 10))->acceptJson();

        $token = config('services.sms.token');
        $header = config('services.sms.auth_header', 'Authorization');
        $scheme = config('services.sms.auth_scheme', 'Bearer');

        if ($token) {
            $request = $request->withHeaders([
                $header => $scheme ? trim($scheme . ' ' . $token) : $token,
            ]);
        }

        try {
            $response = $request->post($endpoint, [
                'to' => $phoneNumber,
                'target' => $phoneNumber,
                'message' => $message,
                'sender' => config('services.sms.sender'),
            ]);

            if (! $response->successful()) {
                throw new RuntimeException('Gateway SMS menolak permintaan: ' . $response->status());
            }
        } catch (Throwable $exception) {
            Log::error('Gagal mengirim SMS verifikasi.', [
                'phone' => $this->maskPhoneNumber($phoneNumber),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (str_starts_with($phone, '8')) {
            return '62' . $phone;
        }

        return $phone;
    }

    protected function maskPhoneNumber(string $phone): string
    {
        if (strlen($phone) <= 6) {
            return $phone;
        }

        return substr($phone, 0, 4) . str_repeat('*', max(strlen($phone) - 8, 1)) . substr($phone, -4);
    }
}
