<?php

namespace App\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;
use Throwable;

class SendTestVerificationSms extends Command
{
    protected $signature = 'verification:test-sms {phone : Nomor tujuan, contoh 08123456789}';

    protected $description = 'Kirim SMS uji verifikasi memakai driver SMS yang aktif';

    public function handle(SmsService $sms): int
    {
        $phone = (string) $this->argument('phone');
        $driver = (string) config('services.sms.driver', 'log');

        $this->components->info("Mengirim SMS uji ke {$phone} via driver [{$driver}] ...");

        try {
            $sms->send($phone, 'Tes SMS '.config('app.name', 'instanweb').'. Kode uji: 123456');
        } catch (Throwable $exception) {
            report($exception);

            $this->error('Gagal mengirim SMS: '.$exception->getMessage());
            $this->line('Periksa SMS_DRIVER beserta kredensial gateway (SMS_ENDPOINT/SMS_TOKEN, TWILIO_*, atau VONAGE_*).');

            return self::FAILURE;
        }

        $this->components->info('SMS uji berhasil dikirim.');

        return self::SUCCESS;
    }
}
