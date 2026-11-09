<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class SmsService
{
    /**
     * Kirim SMS verifikasi melalui driver yang dikonfigurasi.
     *
     * Driver yang didukung: log, http, twilio, vonage.
     * "log" hanya untuk pengembangan lokal; di produksi driver ini ditolak
     * agar kode verifikasi tidak pernah "terkirim" secara palsu.
     */
    public function send(string $phoneNumber, string $message): void
    {
        $phoneNumber = $this->formatPhoneNumber($phoneNumber);
        $driver = (string) config('services.sms.driver', 'log');

        if ($driver === 'log' && app()->isProduction()) {
            throw new RuntimeException(
                'SMS_DRIVER masih "log" di produksi. Atur SMS_DRIVER=http, twilio, atau vonage beserta kredensialnya.'
            );
        }

        match ($driver) {
            'log' => $this->sendViaLog($phoneNumber, $message),
            'http' => $this->sendViaHttp($phoneNumber, $message),
            'twilio' => $this->sendViaTwilio($phoneNumber, $message),
            'vonage' => $this->sendViaVonage($phoneNumber, $message),
            default => throw new RuntimeException('Driver SMS tidak dikenal: '.$driver),
        };
    }

    protected function sendViaLog(string $phoneNumber, string $message): void
    {
        Log::info('SMS verification code (log driver)', [
            'phone' => $this->maskPhoneNumber($phoneNumber),
            'message' => $message,
        ]);
    }

    /**
     * Gateway SMS generik yang menerima JSON { to, target, message, sender }.
     */
    protected function sendViaHttp(string $phoneNumber, string $message): void
    {
        $endpoint = config('services.sms.endpoint');

        if (blank($endpoint)) {
            throw new RuntimeException('Endpoint SMS (SMS_ENDPOINT) belum dikonfigurasi.');
        }

        $request = $this->request()->acceptJson();

        if ($token = config('services.sms.token')) {
            $header = (string) config('services.sms.auth_header', 'Authorization');
            $scheme = (string) config('services.sms.auth_scheme', 'Bearer');

            $request = $request->withHeaders([
                $header => $scheme !== '' ? trim($scheme.' '.$token) : $token,
            ]);
        }

        $payload = [
            'to' => $phoneNumber,
            'target' => $phoneNumber,
            'message' => $message,
        ];

        if (filled($sender = config('services.sms.sender'))) {
            $payload['sender'] = $sender;
        }

        try {
            $response = $request->post($endpoint, $payload);

            if (! $response->successful()) {
                throw new RuntimeException(
                    'Gateway SMS menolak permintaan (HTTP '.$response->status().'): '.Str::limit($response->body(), 200)
                );
            }
        } catch (Throwable $exception) {
            $this->logFailure('http', $phoneNumber, $exception);

            throw $exception;
        }
    }

    /**
     * Twilio Programmable SMS.
     * Dokumentasi: https://www.twilio.com/docs/messaging/api/message-resource
     */
    protected function sendViaTwilio(string $phoneNumber, string $message): void
    {
        $sid = (string) config('services.sms.twilio.sid');
        $token = (string) config('services.sms.twilio.token');
        $from = (string) config('services.sms.twilio.from');

        if ($sid === '' || $token === '' || $from === '') {
            throw new RuntimeException(
                'Kredensial Twilio belum lengkap. Isi TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN, dan TWILIO_FROM.'
            );
        }

        try {
            $response = $this->request()
                ->withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'To' => '+'.$phoneNumber,
                    'From' => $from,
                    'Body' => $message,
                ]);

            if (! $response->successful()) {
                throw new RuntimeException(
                    'Twilio menolak permintaan (HTTP '.$response->status().'): '.Str::limit((string) $response->json('message'), 200)
                );
            }
        } catch (Throwable $exception) {
            $this->logFailure('twilio', $phoneNumber, $exception);

            throw $exception;
        }
    }

    /**
     * Vonage (dahulu Nexmo) SMS API.
     * Dokumentasi: https://developer.vonage.com/en/messaging/sms/overview
     */
    protected function sendViaVonage(string $phoneNumber, string $message): void
    {
        $key = (string) config('services.sms.vonage.key');
        $secret = (string) config('services.sms.vonage.secret');
        $from = (string) config('services.sms.vonage.from');

        if ($key === '' || $secret === '' || $from === '') {
            throw new RuntimeException(
                'Kredensial Vonage belum lengkap. Isi VONAGE_API_KEY, VONAGE_API_SECRET, dan VONAGE_FROM.'
            );
        }

        try {
            $response = $this->request()
                ->asForm()
                ->post('https://rest.nexmo.com/sms/json', [
                    'api_key' => $key,
                    'api_secret' => $secret,
                    'from' => $from,
                    'to' => $phoneNumber,
                    'text' => $message,
                ]);

            $status = (string) $response->json('messages.0.status', '-1');
            $error = (string) $response->json('messages.0.error-text', '');

            if (! $response->successful() || $status !== '0') {
                throw new RuntimeException(
                    'Vonage menolak permintaan (HTTP '.$response->status().', status '.$status.'): '.Str::limit($error, 200)
                );
            }
        } catch (Throwable $exception) {
            $this->logFailure('vonage', $phoneNumber, $exception);

            throw $exception;
        }
    }

    protected function request()
    {
        return Http::timeout((int) config('services.sms.timeout', 10));
    }

    protected function logFailure(string $driver, string $phoneNumber, Throwable $exception): void
    {
        Log::error('Gagal mengirim SMS verifikasi.', [
            'driver' => $driver,
            'phone' => $this->maskPhoneNumber($phoneNumber),
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Normalisasi nomor Indonesia ke format 62xxxxxxxxxx.
     */
    public function formatPhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            throw new InvalidArgumentException('Nomor telepon tidak valid.');
        }

        if (str_starts_with($digits, '620')) {
            $digits = '62'.substr($digits, 3);
        } elseif (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        if (strlen($digits) < 8 || strlen($digits) > 15) {
            throw new InvalidArgumentException('Nomor telepon tidak valid.');
        }

        return $digits;
    }

    protected function maskPhoneNumber(string $phone): string
    {
        if (strlen($phone) <= 6) {
            return $phone;
        }

        return substr($phone, 0, 4).str_repeat('*', max(strlen($phone) - 8, 1)).substr($phone, -4);
    }
}
