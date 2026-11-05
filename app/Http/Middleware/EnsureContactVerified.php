<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureContactVerified
{
    /**
     * Pastikan email (dan nomor telepon bila diisi) sudah terverifikasi
     * sebelum pengguna mengakses alur transaksi.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login.form');
        }

        if (! $user->hasVerifiedEmail()) {
            return redirect()
                ->route('verification.notice')
                ->with('warning', 'Verifikasi email Anda terlebih dahulu untuk melanjutkan transaksi.');
        }

        if ($user->phone_number && ! $user->hasVerifiedPhone()) {
            return redirect()
                ->route('verification.notice')
                ->with('warning', 'Verifikasi nomor telepon Anda terlebih dahulu untuk melanjutkan transaksi.');
        }

        return $next($request);
    }
}
