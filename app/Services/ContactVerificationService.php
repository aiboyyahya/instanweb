<?php

namespace App\Services;

use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class ContactVerificationService
{
    public const CODE_TTL_MINUTES = 10;

    public const RESEND_COOLDOWN_SECONDS = 60;

    public function __construct(protected SmsService $sms) {}

    public function sendEmailCode(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $this->ensureEmailDeliveryIsConfigured();
        $this->ensureCanResend($user->email_verification_sent_at);

        $code = $this->generateCode();

        $user->forceFill([
            'email_verification_code' => Hash::make($code),
            'email_verification_expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES),
            'email_verification_sent_at' => now(),
        ])->save();

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($user->name, $code));
        } catch (Throwable $exception) {
            $user->forceFill([
                'email_verification_code' => null,
                'email_verification_expires_at' => null,
                'email_verification_sent_at' => null,
            ])->save();

            throw $exception;
        }
    }

    public function sendPhoneCode(User $user): void
    {
        if ($user->hasVerifiedPhone()) {
            return;
        }

        if (blank($user->phone_number)) {
            throw ValidationException::withMessages([
                'phone_number' => 'Isi nomor telepon terlebih dahulu.',
            ]);
        }

        $this->ensureCanResend($user->phone_verification_sent_at);

        $code = $this->generateCode();

        $user->forceFill([
            'phone_verification_code' => Hash::make($code),
            'phone_verification_expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES),
            'phone_verification_sent_at' => now(),
        ])->save();

        try {
            $this->sms->send(
                $user->phone_number,
                'Kode verifikasi nomor '.config('app.name', 'instanweb').': '.$code
                    .'. Berlaku '.self::CODE_TTL_MINUTES.' menit. Jangan bagikan kode ini.'
            );
        } catch (Throwable $exception) {
            $user->forceFill([
                'phone_verification_code' => null,
                'phone_verification_expires_at' => null,
                'phone_verification_sent_at' => null,
            ])->save();

            throw $exception;
        }
    }

    public function verifyEmailCode(User $user, string $code): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $this->assertCodeIsValid(
            storedCode: $user->email_verification_code,
            expiresAt: $user->email_verification_expires_at,
            submittedCode: $code,
            field: 'email_code',
        );

        $user->forceFill([
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
            'email_verification_sent_at' => null,
        ])->save();
    }

    public function verifyPhoneCode(User $user, string $code): void
    {
        if ($user->hasVerifiedPhone()) {
            return;
        }

        $this->assertCodeIsValid(
            storedCode: $user->phone_verification_code,
            expiresAt: $user->phone_verification_expires_at,
            submittedCode: $code,
            field: 'phone_code',
        );

        $user->markPhoneAsVerified();
    }

    protected function assertCodeIsValid(?string $storedCode, $expiresAt, string $submittedCode, string $field): void
    {
        if (blank($storedCode) || ! $expiresAt) {
            throw ValidationException::withMessages([
                $field => 'Belum ada kode aktif. Kirim kode verifikasi terlebih dahulu.',
            ]);
        }

        if ($expiresAt->isPast()) {
            throw ValidationException::withMessages([
                $field => 'Kode verifikasi sudah kedaluwarsa. Silakan kirim kode baru.',
            ]);
        }

        if (! Hash::check($submittedCode, $storedCode)) {
            throw ValidationException::withMessages([
                $field => 'Kode verifikasi tidak sesuai.',
            ]);
        }
    }

    /**
     * Cegah kode verifikasi "terkirim" secara palsu di produksi ketika
     * MAIL_MAILER masih log/array (kode hanya masuk ke file log).
     */
    protected function ensureEmailDeliveryIsConfigured(): void
    {
        if (! app()->isProduction()) {
            return;
        }

        $mailer = (string) config('mail.default');

        if (in_array($mailer, ['log', 'array'], true)) {
            throw new RuntimeException(
                'MAIL_MAILER masih "'.$mailer.'" di produksi. Atur SMTP Gmail (MAIL_MAILER=smtp, MAIL_HOST=smtp.gmail.com, MAIL_PORT=587, MAIL_USERNAME, MAIL_PASSWORD berupa App Password) lalu jalankan php artisan config:clear.'
            );
        }
    }

    protected function ensureCanResend($sentAt): void
    {
        if (! $sentAt) {
            return;
        }

        $secondsLeft = self::RESEND_COOLDOWN_SECONDS - (int) $sentAt->diffInSeconds(now());

        if ($secondsLeft > 0) {
            throw ValidationException::withMessages([
                'verification' => 'Tunggu '.$secondsLeft.' detik sebelum mengirim kode lagi.',
            ]);
        }
    }

    protected function generateCode(): string
    {
        return (string) random_int(100000, 999999);
    }
}
