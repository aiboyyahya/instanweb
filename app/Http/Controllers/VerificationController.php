<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ContactVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class VerificationController extends Controller
{
    public function __construct(protected ContactVerificationService $verification)
    {
    }

    public function notice()
    {
        /** @var User $user */
        $user = Auth::user();

        return view('verification.notice', compact('user'));
    }

    public function updatePhone(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^[0-9+()\-\s]+$/'],
        ], [
            'phone_number.regex' => 'Gunakan format nomor telepon yang valid.',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $phone = $this->normalizePhone($validated['phone_number']);

        if ($phone === '') {
            throw ValidationException::withMessages([
                'phone_number' => 'Nomor telepon tidak valid.',
            ]);
        }

        $changed = $user->phone_number !== $validated['phone_number'];

        $user->forceFill([
            'phone_number' => $validated['phone_number'],
            'phone' => $validated['phone_number'],
            'phone_verified_at' => $changed ? null : $user->phone_verified_at,
        ])->save();

        if ($changed) {
            $user->forceFill([
                'phone_verification_code' => null,
                'phone_verification_expires_at' => null,
                'phone_verification_sent_at' => null,
            ])->save();
        }

        return back()->with('success', $changed
            ? 'Nomor telepon disimpan. Kirim kode untuk memverifikasi nomor baru.'
            : 'Nomor telepon tidak berubah.');
    }

    public function sendEmail(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Email Anda sudah terverifikasi.');
        }

        try {
            $this->verification->sendEmailCode($user);

            return back()->with('success', 'Kode verifikasi telah dikirim ke ' . $this->maskEmail($user->email) . '.');
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Kode gagal dikirim. Periksa konfigurasi email lalu coba lagi.');
        }
    }

    public function confirmEmail(Request $request)
    {
        $validated = $request->validate([
            'email_code' => ['required', 'digits:6'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        try {
            $this->verification->verifyEmailCode($user, $validated['email_code']);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return redirect()
            ->route('verification.notice')
            ->with('success', 'Email berhasil diverifikasi.');
    }

    public function sendPhone(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (blank($user->phone_number)) {
            return back()->with('error', 'Simpan nomor telepon terlebih dahulu.');
        }

        if ($user->hasVerifiedPhone()) {
            return back()->with('success', 'Nomor telepon Anda sudah terverifikasi.');
        }

        try {
            $this->verification->sendPhoneCode($user);

            return back()->with('success', 'Kode verifikasi telah dikirim via SMS ke nomor terdaftar.');
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Kode SMS gagal dikirim. Periksa konfigurasi gateway SMS lalu coba lagi.');
        }
    }

    public function confirmPhone(Request $request)
    {
        $validated = $request->validate([
            'phone_code' => ['required', 'digits:6'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        try {
            $this->verification->verifyPhoneCode($user, $validated['phone_code']);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return redirect()
            ->route('verification.notice')
            ->with('success', 'Nomor telepon berhasil diverifikasi.');
    }

    protected function normalizePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone) ?? '';
    }

    protected function maskEmail(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');

        $visible = mb_substr($name, 0, 2);

        return $visible . str_repeat('*', max(mb_strlen($name) - 2, 1)) . '@' . $domain;
    }
}
