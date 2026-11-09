<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\ContactVerificationService;
use App\Services\StoreBannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleLoginController extends Controller
{
    public function __construct(
        protected ContactVerificationService $verification,
        protected StoreBannerService $storeBanners,
    ) {}

    public function redirectToGoogle()
    {
        if (blank(config('services.google.client_id')) || blank(config('services.google.client_secret'))) {
            return redirect()
                ->route('login.form')
                ->with('error', 'Login Google belum dikonfigurasi. Isi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET pada file .env, lalu jalankan php artisan config:clear.');
        }

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('login.form')
                ->with('error', 'Login Google gagal. Silakan coba lagi atau masuk menggunakan email dan password.');
        }

        $user = User::where('email', $googleUser->email)->first();
        $emailVerifiedByGoogle = (bool) data_get($googleUser->user, 'verified_email', false);

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Hash::make(rand(100000, 999999)),
                'role' => 'user',
            ]);

            // Tandai terverifikasi hanya jika Google menyatakan email valid.
            if ($emailVerifiedByGoogle) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }
        } elseif (! $user->hasVerifiedEmail() && $emailVerifiedByGoogle) {
            // Google sudah memverifikasi email ini; tidak perlu kode OTP.
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user);

        if ($user->role === 'admin') {
            return redirect('/admin');
        }

        return redirect('/');
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/')->with('success', 'Berhasil logout.');
    }

    public function loginform()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin'
                ? redirect('/admin')
                : redirect()->route('home');
        }

        return view('auth.login', ['storename' => config('app.name', 'instanweb')]);
    }

    public function loginEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $intended = session()->pull('url.intended');
            $intendedPath = $intended ? parse_url($intended, PHP_URL_PATH) : null;
            $intendedIsAdmin = is_string($intendedPath) && str_starts_with($intendedPath, '/admin');

            // Satu pintu login: peran akun menentukan tujuan berikutnya.
            if ($user->role === 'admin') {
                return redirect()->to($intended && ! $intendedIsAdmin ? $intended : '/admin');
            }

            if ($intended && ! $intendedIsAdmin) {
                return redirect()->to($intended);
            }

            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function registerForm()
    {
        if (Auth::check()) {
            return redirect()->route('profil');
        }

        return view('auth.register');
    }

    public function registerEmail(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('profil');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|min:8|confirmed',
        ],
            [
                'password.min' => 'Password minimal 8 karakter',
            ]
        );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'phone' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        Auth::login($user);

        $this->sendVerificationCodes($user);

        return redirect('/')->with('success', 'Akun berhasil dibuat dan Anda telah masuk. Lengkapi verifikasi email untuk mulai bertransaksi.');
    }

    /**
     * Formulir pendaftaran pemilik toko.
     *
     * Tamu mengisi data akun + data toko. Pengguna yang sudah masuk cukup
     * melengkapi data toko; akunnya akan ditingkatkan menjadi pemilik toko.
     */
    public function storeRegisterForm(Request $request)
    {
        $user = $request->user();

        if ($user && $user->store) {
            return redirect()
                ->route('profil')
                ->with('error', 'Akun ini sudah memiliki toko.');
        }

        return view('auth.register-store', [
            'storename' => config('app.name', 'instanweb'),
            'currentUser' => $user,
        ]);
    }

    /**
     * Simpan pendaftaran toko baru.
     */
    public function storeRegister(Request $request)
    {
        $user = $request->user();

        if ($user && $user->store) {
            return redirect()
                ->route('profil')
                ->with('error', 'Akun ini sudah memiliki toko.');
        }

        $rules = $this->storeRules();

        if (! $user) {
            $rules = array_merge($rules, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
        }

        $validated = $request->validate($rules);

        $storeData = $request->only([
            'store_name',
            'address',
            'description',
            'whatsapp',
            'instagram',
            'tiktok',
            'facebook',
            'shopee',
            'tokopedia',
            'origin_city_id',
            'origin_district_id',
        ]);

        if ($request->hasFile('logo')) {
            $storeData['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $user = DB::transaction(function () use ($user, $validated, $storeData) {
            if (! $user) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone_number' => $validated['phone_number'] ?? null,
                    'phone' => $validated['phone_number'] ?? null,
                    'password' => Hash::make($validated['password']),
                    'role' => 'admin',
                ]);
            } else {
                $user->role = 'admin';

                if (! empty($validated['phone_number']) && $validated['phone_number'] !== $user->phone_number) {
                    $user->forceFill([
                        'phone_number' => $validated['phone_number'],
                        'phone' => $validated['phone_number'],
                        'phone_verified_at' => null,
                        'phone_verification_code' => null,
                        'phone_verification_expires_at' => null,
                        'phone_verification_sent_at' => null,
                    ]);
                }

                $user->save();
            }

            $store = $user->store()->create($storeData);

            // Produk lama yang belum memiliki toko menjadi milik toko pertama.
            if (Store::count() === 1) {
                Product::query()
                    ->whereNull('store_id')
                    ->update(['store_id' => $store->id]);
            }

            return $user;
        });

        $store = $user->store()->first();
        $this->storeBanners->sync($store, $request);
        $this->sendVerificationCodes($user);

        cache()->forget('store_data');

        if ($request->user()) {
            return redirect()
                ->route('profil')
                ->with('success', 'Toko berhasil dibuat. Pengaturan toko kini tersedia di profil Anda.');
        }

        Auth::login($user);

        return redirect('/admin')
            ->with('success', 'Toko berhasil dibuat. Selamat mengelola toko Anda!');
    }

    /**
     * Aturan validasi data toko yang dipakai bersama oleh pendaftaran toko
     * dan halaman pengaturan toko pada profil.
     */
    private function storeRules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'banner_1' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'banner_2' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'banner_3' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'shopee' => ['nullable', 'string', 'max:255'],
            'tokopedia' => ['nullable', 'string', 'max:255'],
            'origin_city_id' => ['nullable', 'string', 'max:50'],
            'origin_district_id' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Kirim kode verifikasi email/nomor tanpa menggagalkan pendaftaran bila
     * layanan pengiriman belum dikonfigurasi.
     */
    private function sendVerificationCodes(User $user): void
    {
        try {
            $this->verification->sendEmailCode($user);
        } catch (ValidationException $exception) {
            // Cooldown pengiriman; kode sebelumnya masih berlaku.
        } catch (Throwable $exception) {
            report($exception);
        }

        if (blank($user->phone_number) || $user->hasVerifiedPhone()) {
            return;
        }

        try {
            $this->verification->sendPhoneCode($user);
        } catch (ValidationException $exception) {
            //
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
