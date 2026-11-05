<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Store;
use App\Services\ContactVerificationService;
use App\Services\StoreBannerService;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProfilController extends Controller
{
    public function __construct(
        protected ContactVerificationService $verification,
        protected StoreBannerService $storeBanners,
    ) {
    }

    public function verifyPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password valid.',
        ]);
    }

    public function index()
    {

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login.form')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Pemilik toko melihat toko miliknya. Admin lama yang belum memiliki
        // relasi kepemilikan tetap dapat mengelola toko utama (single-store).
        // Jika belum ada toko sama sekali, admin tetap melihat formulir
        // pengaturan untuk membuat toko pertamanya.
        $store = $user->store
            ?: ($user->role === 'admin' ? (Store::first() ?? new Store()) : null);

        return view('Profil', compact('user', 'store'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();


        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone_number' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'current_password' => 'nullable|string',
                'password' => 'nullable|min:8|confirmed',
            ],
            [
                'password.min'=> 'Password minimal 8 karakter',
            ]
        );

        if (!empty($validated['password']) && !Hash::check($request->current_password, $user->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'current_password' => 'Password lama tidak sesuai.',
            ]);
        }


        $user->name = $validated['name'];

        $emailChanged = $validated['email'] !== $user->email;
        $phoneChanged = ($validated['phone_number'] ?? null) !== $user->phone_number;

        $user->email = $validated['email'];
        $user->phone_number = $validated['phone_number'] ?? $user->phone_number;
        $user->phone = $user->phone_number;
        $user->address = $validated['address'] ?? $user->address;

        if ($emailChanged) {
            $user->forceFill([
                'email_verified_at' => null,
                'email_verification_code' => null,
                'email_verification_expires_at' => null,
                'email_verification_sent_at' => null,
            ]);
        }

        if ($phoneChanged) {
            $user->forceFill([
                'phone_verified_at' => null,
                'phone_verification_code' => null,
                'phone_verification_expires_at' => null,
                'phone_verification_sent_at' => null,
            ]);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // Store new photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
        }


        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }


        $user->save();

        $verificationMessage = '';

        if ($emailChanged && !$user->hasVerifiedEmail()) {
            try {
                $this->verification->sendEmailCode($user);
                $verificationMessage = ' Kode verifikasi email telah dikirim.';
            } catch (ValidationException $exception) {
                // Cooldown pengiriman; pengguna tetap bisa meminta kode dari halaman verifikasi.
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        if ($phoneChanged && filled($user->phone_number) && !$user->hasVerifiedPhone()) {
            try {
                $this->verification->sendPhoneCode($user);
                $verificationMessage .= ' Kode verifikasi SMS telah dikirim.';
            } catch (ValidationException $exception) {
                //
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return redirect()->route('profil')->with('success', 'Profil berhasil diperbarui!' . $verificationMessage);
    }

    /**
     * Perbarui pengaturan toko langsung dari halaman profil.
     */
    public function updateStore(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return redirect()
                ->route('profil')
                ->with('error', 'Pengaturan toko hanya dapat diubah oleh pemilik toko.');
        }

        $store = $user->store ?: Store::first() ?: new Store();

        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'banner_1' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'banner_2' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'banner_3' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'remove_banner_1' => ['nullable', 'boolean'],
            'remove_banner_2' => ['nullable', 'boolean'],
            'remove_banner_3' => ['nullable', 'boolean'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'shopee' => ['nullable', 'string', 'max:255'],
            'tokopedia' => ['nullable', 'string', 'max:255'],
            'origin_city_id' => ['nullable', 'string', 'max:50'],
            'origin_district_id' => ['nullable', 'string', 'max:50'],
        ]);

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

        $store->fill($storeData);

        if ($request->hasFile('logo')) {
            if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                Storage::disk('public')->delete($store->logo);
            }

            $store->logo = $request->file('logo')->store('logos', 'public');
        }

        if (!$store->user_id) {
            $store->user_id = $user->id;
        }

        $store->save();

        $this->storeBanners->sync($store, $request);

        if (Store::count() === 1) {
            Product::query()
                ->whereNull('store_id')
                ->update(['store_id' => $store->id]);
        }

        cache()->forget('store_data');

        return redirect()->route('profil')->with('success', 'Pengaturan toko berhasil diperbarui!');
    }
}
