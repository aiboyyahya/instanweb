<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Store;

class ProfilController extends Controller
{

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


        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'password' => 'nullable|min:6|confirmed',
        ]);


        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone_number = $validated['phone_number'] ?? $user->phone_number;
        $user->address = $validated['address'] ?? $user->address;

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

        return redirect()->route('profil')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Perbarui pengaturan toko langsung dari halaman profil.
     */
    public function updateStore(Request $request)
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'admin') {
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
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'shopee' => ['nullable', 'string', 'max:255'],
            'tokopedia' => ['nullable', 'string', 'max:255'],
            'origin_city_id' => ['nullable', 'string', 'max:50'],
            'origin_district_id' => ['nullable', 'string', 'max:50'],
        ]);

        $store->fill($validated);

        if ($request->hasFile('logo')) {
            if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                Storage::disk('public')->delete($store->logo);
            }

            $store->logo = $request->file('logo')->store('logos', 'public');
        }

        if (! $store->user_id) {
            $store->user_id = $user->id;
        }

        $store->save();

        cache()->forget('store_data');

        return redirect()->route('profil')->with('success', 'Pengaturan toko berhasil diperbarui!');
    }
}
