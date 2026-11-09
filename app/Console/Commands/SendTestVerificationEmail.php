<?php

namespace App\Console\Commands;

use App\Mail\VerificationCodeMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendTestVerificationEmail extends Command
{
    protected $signature = 'verification:test-email {email : Alamat email tujuan}';

    protected $description = 'Kirim email uji verifikasi memakai konfigurasi MAIL_* yang aktif';

    public function handle(): int
    {
        $email = (string) $this->argument('email');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Alamat email tidak valid.');

            return self::FAILURE;
        }

        $mailer = (string) config('mail.default');

        if (app()->isProduction() && in_array($mailer, ['log', 'array'], true)) {
            $this->error('MAIL_MAILER masih "'.$mailer.'". Atur SMTP Gmail terlebih dahulu (MAIL_MAILER=smtp, MAIL_HOST=smtp.gmail.com, MAIL_PORT=587, MAIL_SCHEME=smtp, MAIL_USERNAME, MAIL_PASSWORD=App Password).');

            return self::FAILURE;
        }

        $this->components->info("Mengirim email uji ke {$email} via mailer [{$mailer}] ...");

        try {
            Mail::to($email)->send(new VerificationCodeMail('Pengujian', '123456'));
        } catch (Throwable $exception) {
            report($exception);

            $this->error('Gagal mengirim email: '.$exception->getMessage());
            $this->line('Periksa MAIL_HOST, MAIL_PORT, MAIL_SCHEME, MAIL_USERNAME, dan MAIL_PASSWORD (App Password Gmail, bukan password akun).');

            return self::FAILURE;
        }

        $this->components->info("Email uji terkirim. Cek inbox/spam {$email}.");

        return self::SUCCESS;
    }
}
