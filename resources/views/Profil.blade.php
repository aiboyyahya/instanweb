@extends('layouts.app')

@section('title', 'Profil Saya | ' . (store()->store_name ?? 'instanweb'))

@section('content')
    @php
        $isOwner = $user->role === 'admin';
        $hasStore = $store && $store->exists;
        $profileNavStore = store_context();
        $profileHomeUrl = $profileNavStore ? route('store.show', $profileNavStore) : route('home');
        $roleLabel = $isOwner ? 'Pemilik Toko' : 'Pembeli';
        $initial = strtoupper(substr($user->name, 0, 1));
        $storeLogo = $store && $store->logo ? asset('storage/' . $store->logo) : null;
        $needsEmailVerification = !$user->hasVerifiedEmail();
        $needsPhoneVerification = filled($user->phone_number) && !$user->hasVerifiedPhone();
        $banners = [
            1 => $store->banner_1 ?? null,
            2 => $store->banner_2 ?? null,
            3 => $store->banner_3 ?? null,
        ];
    @endphp

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <x-ui.breadcrumb :items="[['label' => 'Beranda', 'url' => $profileHomeUrl], ['label' => 'Profil Saya']]" />

        {{-- Ringkasan akun --}}
        <section
            class="iw-fade-in relative mt-4 overflow-hidden rounded-panel border border-line bg-inverse text-fg-inverse shadow-sm"
            aria-labelledby="ringkasan-akun">
            <div aria-hidden="true"
                class="pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full bg-brand-sky/20 blur-3xl">
            </div>
            <div aria-hidden="true"
                class="pointer-events-none absolute -bottom-24 left-1/4 h-56 w-56 rounded-full bg-brand/20 blur-3xl">
            </div>

            <div class="relative flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                <div class="flex items-center gap-4">
                    <div class="relative shrink-0">
                        @if ($user->profile_photo)
                            <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Foto profil {{ $user->name }}"
                                class="h-20 w-20 rounded-2xl border-2 border-white/20 object-cover shadow-lg">
                        @else
                            <span
                                class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/10 text-2xl font-extrabold ring-1 ring-white/20">
                                {{ $initial }}
                            </span>
                        @endif
                        <span
                            class="absolute -bottom-1.5 -right-1.5 flex h-7 w-7 items-center justify-center rounded-full border-2 border-inverse {{ $isOwner ? 'bg-cta' : 'bg-brand-sky' }} text-white">
                            <x-ui.icon :name="$isOwner ? 'shield' : 'shopping-bag'" class="h-3.5 w-3.5" />
                        </span>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-white/60">Akun saya</p>
                        <h1 id="ringkasan-akun" class="mt-1 truncate text-2xl font-bold tracking-tight sm:text-3xl">
                            {{ $user->name }}
                        </h1>
                        <p class="mt-1 truncate text-sm text-white/60">{{ $user->email }}</p>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-white/10 px-2.5 py-1 text-xs font-semibold">
                                <span class="h-1.5 w-1.5 rounded-full bg-brand-sky" aria-hidden="true"></span>
                                {{ $roleLabel }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/5 px-2.5 py-1 text-xs text-white/70">
                                Bergabung {{ $user->created_at?->translatedFormat('M Y') }}
                            </span>
                            @if ($needsEmailVerification || $needsPhoneVerification)
                                <a href="{{ route('verification.notice') }}"
                                    class="inline-flex items-center gap-1.5 rounded-full border border-warning/40 bg-warning/15 px-2.5 py-1 text-xs font-semibold text-warning transition hover:bg-warning/25">
                                    <x-ui.icon name="shield" class="h-3.5 w-3.5" />
                                    Verifikasi akun
                                </a>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full border border-success/40 bg-success/15 px-2.5 py-1 text-xs font-semibold text-success">
                                    <x-ui.icon name="check-circle" class="h-3.5 w-3.5" />
                                    Terverifikasi
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 sm:justify-end">
                    @if ($isOwner && $hasStore)
                        <a href="{{ route('store.show', $store) }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-field border border-white/25 bg-white/10 px-4 text-sm font-semibold text-white transition hover:bg-white/20">
                            <x-ui.icon name="external" class="h-4 w-4" /> Lihat Toko
                        </a>
                        <a href="{{ url('/admin') }}"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-field bg-white px-4 text-sm font-semibold text-inverse transition hover:bg-white/90">
                            <x-ui.icon name="shield" class="h-4 w-4" /> Panel Toko
                        </a>
                    @endif
                    <a href="{{ route('orders') }}"
                        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-field border border-white/25 bg-white/10 px-4 text-sm font-semibold text-white transition hover:bg-white/20">
                        <x-ui.icon name="package" class="h-4 w-4" /> Pesanan Saya
                    </a>
                </div>
            </div>
        </section>

        {{-- Data pribadi --}}
        <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="mt-6"
            data-profile-form>
            @csrf
            @method('PUT')

            <div class="grid gap-6 lg:grid-cols-[300px_minmax(0,1fr)] lg:items-start">
                {{-- Foto profil --}}
                <section class="rounded-panel border border-line bg-surface p-6 shadow-sm lg:sticky lg:top-24"
                    aria-labelledby="foto-profil">
                    <h2 id="foto-profil" class="text-xs font-semibold uppercase tracking-[0.14em] text-fg-muted">Foto Profil
                    </h2>

                    <div class="mt-5 flex flex-col items-center text-center">
                        <div class="relative">
                            @if ($user->profile_photo)
                                <img id="profile-preview" src="{{ asset('storage/' . $user->profile_photo) }}"
                                    alt="Foto profil {{ $user->name }}"
                                    class="h-32 w-32 rounded-full border-4 border-surface object-cover shadow-md ring-1 ring-line">
                            @else
                                <span id="profile-initial"
                                    class="flex h-32 w-32 items-center justify-center rounded-full bg-surface-soft text-4xl font-bold text-fg-secondary ring-1 ring-line">
                                    {{ $initial }}
                                </span>
                                <img id="profile-preview" src="" alt="Pratinjau foto profil"
                                    class="hidden h-32 w-32 rounded-full border-4 border-surface object-cover shadow-md ring-1 ring-line">
                            @endif
                        </div>

                        <p class="mt-4 text-lg font-semibold text-fg">{{ $user->name }}</p>
                        <p class="text-sm text-fg-muted">{{ $user->email }}</p>

                        <div class="mt-3">
                            <x-ui.badge :variant="$isOwner ? 'accent' : 'info'">
                                <x-ui.icon :name="$isOwner ? 'shield' : 'shopping-bag'" class="h-3.5 w-3.5" />
                                {{ $roleLabel }}
                            </x-ui.badge>
                        </div>

                        <div class="mt-5 w-full">
                            <label for="profile_photo"
                                class="inline-flex min-h-11 w-full cursor-pointer items-center justify-center gap-2 rounded-field border border-line-strong bg-surface px-4 text-sm font-semibold text-fg transition hover:bg-surface-soft">
                                <x-ui.icon name="plus" class="h-4 w-4" />
                                Pilih foto baru
                            </label>
                            <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="sr-only"
                                data-profile-input>
                            <p class="mt-2 text-xs leading-relaxed text-fg-muted">
                                Format JPG, PNG, atau GIF. Maksimal 10 MB.
                            </p>
                            @error('profile_photo')
                                <p class="mt-2 flex items-start gap-1.5 text-xs font-medium text-danger" role="alert">
                                    <x-ui.icon name="alert-circle" class="mt-0.5 h-4 w-4" />
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 space-y-2 border-t border-line pt-5">
                        <a href="{{ route('orders') }}"
                            class="flex items-center gap-2.5 rounded-field px-3 py-2.5 text-sm font-medium text-fg-secondary transition hover:bg-surface-soft hover:text-fg">
                            <x-ui.icon name="package" class="h-4 w-4" /> Riwayat pesanan
                        </a>
                        <a href="{{ route('cart') }}"
                            class="flex items-center gap-2.5 rounded-field px-3 py-2.5 text-sm font-medium text-fg-secondary transition hover:bg-surface-soft hover:text-fg">
                            <x-ui.icon name="shopping-bag" class="h-4 w-4" /> Keranjang belanja
                        </a>
                    </div>
                </section>

                <div class="space-y-6">
                    {{-- Informasi pribadi --}}
                    <section class="rounded-panel border border-line bg-surface p-6 shadow-sm sm:p-8"
                        aria-labelledby="informasi-pribadi">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 id="informasi-pribadi" class="text-lg font-bold text-fg">Informasi Pribadi</h2>
                                <p class="mt-1 text-sm text-fg-muted">Data ini dipakai untuk kebutuhan akun dan pengiriman.
                                </p>
                            </div>
                            <span
                                class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-card bg-surface-soft text-brand-text sm:flex">
                                <x-ui.icon name="user" class="h-5 w-5" />
                            </span>
                        </div>

                        <div class="mt-6 grid gap-5 sm:grid-cols-2">
                            <x-ui.field label="Nama lengkap" name="name" required>
                                <input type="text" id="name" name="name"
                                    value="{{ old('name', $user->name) }}" required autocomplete="name"
                                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                                    class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg">
                            </x-ui.field>

                            <x-ui.field label="Email" name="email" required>
                                <input type="email" id="email" name="email"
                                    value="{{ old('email', $user->email) }}" required autocomplete="email"
                                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                                    class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg">
                            </x-ui.field>

                            <x-ui.field label="Nomor telepon" name="phone_number"
                                hint="Hanya angka, contoh: 08123456789.">
                                <input type="text" id="phone_number" name="phone_number"
                                    value="{{ old('phone_number', $user->phone_number) }}" inputmode="numeric"
                                    pattern="[0-9]*" autocomplete="tel" placeholder="08123456789"
                                    class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">

                                @if (filled($user->phone_number))
                                    <div class="pt-1.5">
                                        @if ($user->hasVerifiedPhone())
                                            <x-ui.badge variant="success">
                                                <x-ui.icon name="check" class="h-3 w-3" /> Nomor terverifikasi
                                            </x-ui.badge>
                                        @else
                                            <a href="{{ route('verification.notice') }}">
                                                <x-ui.badge variant="warning">
                                                    <x-ui.icon name="alert-circle" class="h-3 w-3" /> Verifikasi nomor
                                                </x-ui.badge>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </x-ui.field>

                            <x-ui.field label="Alamat" name="address">
                                <input type="text" id="address" name="address"
                                    value="{{ old('address', $user->address) }}" autocomplete="street-address"
                                    placeholder="Jalan, nomor, kota"
                                    class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                            </x-ui.field>
                        </div>
                    </section>

                    {{-- Password --}}
                    <section class="rounded-panel border border-line bg-surface p-6 shadow-sm sm:p-8"
                        aria-labelledby="ubah-password">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 id="ubah-password" class="text-lg font-bold text-fg">Ubah Password</h2>
                                <p class="mt-1 text-sm text-fg-muted">
                                    <span id="password-status-locked" class="inline-flex items-center gap-1.5 text-warning">
                                        <x-ui.icon name="shield" class="h-3.5 w-3.5" />
                                        Konfirmasi password lama untuk mengaktifkan
                                    </span>
                                    <span id="password-status-unlocked" class="hidden inline-flex items-center gap-1.5 text-success">
                                        <x-ui.icon name="check-circle" class="h-3.5 w-3.5" />
                                        Password lama valid — kosongkan untuk tidak mengubah
                                    </span>
                                </p>
                            </div>
                            <span
                                class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-card bg-surface-soft text-info sm:flex">
                                <x-ui.icon name="shield" class="h-5 w-5" />
                            </span>
                        </div>

                        <div class="mt-6 grid gap-5 sm:grid-cols-2">
                            <x-ui.field label="Password lama" name="current_password">
                                <div class="relative rounded-field border border-line-strong bg-surface overflow-hidden">
                                    <input type="password" id="current_password" name="current_password"
                                        autocomplete="current-password"
                                        placeholder="Masukkan password saat ini"
                                        @error('current_password') aria-invalid="true" aria-describedby="current_password-error" @enderror
                                        class="h-12 w-full bg-transparent px-3.5 text-sm text-fg placeholder:text-fg-muted pr-11">
                                    <button type="button" id="toggle-current-password-btn"
                                        class="absolute inset-y-0 right-0 z-10 flex cursor-pointer items-center justify-center px-3.5 text-fg-muted transition hover:text-brand-text focus:outline-none"
                                        aria-label="Tampilkan password" aria-pressed="false">
                                        <span data-icon-show>
                                            <x-ui.icon name="eye" class="h-5 w-5" />
                                        </span>
                                        <span data-icon-hide class="hidden">
                                            <x-ui.icon name="eye-off" class="h-5 w-5" />
                                        </span>
                                    </button>
                                </div>
                                <button type="button" id="btn-verify-password"
                                    class="mt-2 inline-flex min-h-9 items-center gap-2 rounded-field border border-line-strong bg-surface px-3 text-xs font-semibold text-fg-secondary transition hover:border-cta hover:text-cta disabled:pointer-events-none disabled:opacity-50">
                                    <x-ui.icon name="shield" class="h-3.5 w-3.5" />
                                    Konfirmasi
                                </button>
                                <p id="verify-message" class="mt-1.5 text-xs" role="status" aria-live="polite"></p>
                            </x-ui.field>

                            <x-ui.field label="Password baru" name="password" hint="Minimal 8 karakter.">
                                <div class="relative rounded-field border border-line-strong bg-surface overflow-hidden opacity-50 pointer-events-none"
                                    id="new-password-wrapper">
                                    <div class="absolute inset-0 z-20" aria-hidden="true"></div>
                                    <input type="password" id="password" name="password" autocomplete="new-password"
                                        disabled
                                        @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                                        class="h-12 w-full bg-transparent px-3.5 text-sm text-fg placeholder:text-fg-muted pr-11">
                                    <button type="button" id="toggle-profile-password-btn" disabled
                                        class="absolute inset-y-0 right-0 z-10 flex cursor-not-allowed items-center justify-center px-3.5 text-fg-muted">
                                        <span data-icon-show>
                                            <x-ui.icon name="eye" class="h-5 w-5" />
                                        </span>
                                        <span data-icon-hide class="hidden">
                                            <x-ui.icon name="eye-off" class="h-5 w-5" />
                                        </span>
                                    </button>
                                </div>
                            </x-ui.field>

                            <x-ui.field label="Konfirmasi password" name="password_confirmation">
                                <div class="relative rounded-field border border-line-strong bg-surface overflow-hidden opacity-50 pointer-events-none"
                                    id="confirm-password-wrapper">
                                    <div class="absolute inset-0 z-20" aria-hidden="true"></div>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        autocomplete="new-password" disabled
                                        @error('password_confirmation') aria-invalid="true" aria-describedby="password_confirmation-error" @enderror
                                        class="h-12 w-full bg-transparent px-3.5 text-sm text-fg placeholder:text-fg-muted pr-11">
                                    <button type="button" id="toggle-profile-confirm-password-btn" disabled
                                        class="absolute inset-y-0 right-0 z-10 flex cursor-not-allowed items-center justify-center px-3.5 text-fg-muted">
                                        <span data-icon-show>
                                            <x-ui.icon name="eye" class="h-5 w-5" />
                                        </span>
                                        <span data-icon-hide class="hidden">
                                            <x-ui.icon name="eye-off" class="h-5 w-5" />
                                        </span>
                                    </button>
                                </div>
                            </x-ui.field>
                        </div>
                    </section>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <x-ui.button :href="$profileHomeUrl" variant="outline">Batal</x-ui.button>
                        <x-ui.button type="submit">
                            <x-ui.icon name="check" class="h-4 w-4" /> Simpan Perubahan
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Pengaturan toko / CTA buat toko --}}
        <section class="mt-8" aria-labelledby="pengaturan-toko">
            @if ($isOwner)
                <form action="{{ route('profil.store.update') }}" method="POST" enctype="multipart/form-data"
                    class="overflow-hidden rounded-panel border border-line bg-surface shadow-sm">
                    @csrf
                    @method('PUT')

                    <div
                        class="flex flex-col gap-4 border-b border-line bg-surface-muted p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                        <div class="flex items-start gap-4">
                            <span
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-card bg-surface text-brand-text shadow-sm">
                                <x-ui.icon name="shield" class="h-5 w-5" />
                            </span>
                            <div>
                                <h2 id="pengaturan-toko" class="text-lg font-bold text-fg">Pengaturan Toko</h2>
                                <p class="mt-1 max-w-xl text-sm leading-relaxed text-fg-muted">
                                    Kelola identitas toko, kanal kontak, marketplace, dan asal pengiriman.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            @if ($hasStore)
                                <x-ui.badge variant="success">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                    Toko aktif
                                </x-ui.badge>
                                <a href="{{ url('/admin/pengaturan') }}"
                                    class="inline-flex min-h-11 items-center gap-2 rounded-field border border-line-strong bg-surface px-4 text-sm font-semibold text-fg transition hover:bg-surface-soft">
                                    <x-ui.icon name="external" class="h-4 w-4" /> Buka di Panel
                                </a>
                            @else
                                <x-ui.badge variant="warning">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                    Belum dibuat
                                </x-ui.badge>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-6 p-6 sm:p-8">
                        {{-- Identitas --}}
                        <div>
                            <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-fg-muted">Identitas Toko</h3>

                            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                                <x-ui.field label="Nama toko" name="store_name" required class="sm:col-span-2">
                                    <input type="text" id="store_name" name="store_name"
                                        value="{{ old('store_name', $store->store_name) }}" required
                                        placeholder="Contoh: Toko Kopi Nusantara"
                                        @error('store_name') aria-invalid="true" aria-describedby="store_name-error" @enderror
                                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                                </x-ui.field>

                                <x-ui.field label="Alamat toko" name="address" inputId="store_address"
                                    class="sm:col-span-2">
                                    <input type="text" id="store_address" name="address"
                                        value="{{ old('address', $store->address) }}" autocomplete="street-address"
                                        placeholder="Alamat lengkap toko"
                                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                                </x-ui.field>

                                <x-ui.field label="Deskripsi toko" name="description" inputId="store_description"
                                    class="sm:col-span-2">
                                    <textarea id="store_description" name="description" rows="3"
                                        placeholder="Ceritakan singkat produk dan nilai toko Anda."
                                        class="w-full rounded-field border border-line-strong bg-surface px-3.5 py-3 text-sm text-fg placeholder:text-fg-muted">{{ old('description', $store->description) }}</textarea>
                                </x-ui.field>

                                <div class="sm:col-span-2">
                                    <p class="block text-sm font-medium text-fg-secondary">Logo toko</p>
                                    <div
                                        class="mt-2 flex flex-col gap-4 rounded-card border border-line bg-surface-muted p-4 sm:flex-row sm:items-center">
                                        <div
                                            class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-line bg-surface">
                                            <img id="store-logo-preview" src="{{ $storeLogo ?? '' }}" alt="Logo toko"
                                                class="{{ $storeLogo ? '' : 'hidden' }} h-full w-full object-cover">
                                            <x-ui.icon name="sparkles"
                                                class="h-7 w-7 text-fg-muted {{ $storeLogo ? 'hidden' : '' }}"
                                                data-store-logo-placeholder />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <label for="store_logo"
                                                class="inline-flex min-h-10 cursor-pointer items-center justify-center gap-2 rounded-field border border-line-strong bg-surface px-4 text-sm font-semibold text-fg transition hover:bg-surface-soft">
                                                <x-ui.icon name="plus" class="h-4 w-4" /> Ganti logo
                                            </label>
                                            <input type="file" name="logo" id="store_logo" accept="image/*"
                                                class="sr-only" data-store-logo-input>
                                            <p class="mt-2 text-xs leading-relaxed text-fg-muted">JPG, PNG, GIF, atau WEBP.
                                                Maksimal 10 MB.</p>
                                            @error('logo')
                                                <p class="mt-1 text-xs font-medium text-danger" role="alert">
                                                    {{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <div class="flex flex-wrap items-end justify-between gap-2">
                                        <div>
                                            <p class="block text-sm font-medium text-fg-secondary">Banner toko</p>
                                            <p class="mt-1 text-xs leading-relaxed text-fg-muted">
                                                Maksimal 3 banner untuk hero carousel storefront. Jika kosong, banner bawaan
                                                digunakan.
                                            </p>
                                        </div>
                                        <x-ui.badge variant="info">{{ count(array_filter($banners)) }}/3
                                            terpasang</x-ui.badge>
                                    </div>

                                    <div class="mt-3 grid gap-4 sm:grid-cols-3">
                                        @foreach ($banners as $index => $bannerPath)
                                            @php
                                                $bannerUrl = $bannerPath ? asset('storage/' . $bannerPath) : null;
                                                $bannerInputId = 'store_banner_' . $index;
                                            @endphp

                                            <div class="rounded-card border border-line bg-surface-muted p-3">
                                                <p class="text-xs font-semibold text-fg-secondary">Banner
                                                    {{ $index }}</p>

                                                <div
                                                    class="mt-2 flex aspect-video items-center justify-center overflow-hidden rounded-lg border border-line bg-surface">
                                                    <img id="store-banner-preview-{{ $index }}"
                                                        src="{{ $bannerUrl ?? '' }}"
                                                        alt="Pratinjau banner {{ $index }}"
                                                        class="{{ $bannerUrl ? '' : 'hidden' }} h-full w-full object-cover">
                                                    <x-ui.icon name="sparkles"
                                                        class="h-6 w-6 text-fg-muted {{ $bannerUrl ? 'hidden' : '' }}"
                                                        data-store-banner-placeholder="{{ $index }}" />
                                                </div>

                                                <label for="{{ $bannerInputId }}"
                                                    class="mt-3 inline-flex min-h-9 w-full cursor-pointer items-center justify-center gap-1.5 rounded-field border border-line-strong bg-surface px-3 text-xs font-semibold text-fg transition hover:bg-surface-soft">
                                                    <x-ui.icon name="plus" class="h-3.5 w-3.5" />
                                                    Pilih banner
                                                </label>
                                                <input type="file" name="banner_{{ $index }}"
                                                    id="{{ $bannerInputId }}" accept="image/*" class="sr-only"
                                                    data-store-banner-input data-banner-index="{{ $index }}">

                                                @if ($bannerPath)
                                                    <label class="mt-2 flex items-center gap-2 text-xs text-fg-muted">
                                                        <input type="checkbox" name="remove_banner_{{ $index }}"
                                                            value="1"
                                                            class="h-4 w-4 rounded border-line-strong text-danger focus:ring-danger">
                                                        Hapus banner ini
                                                    </label>
                                                @endif

                                                @error('banner_' . $index)
                                                    <p class="mt-1 text-xs font-medium text-danger" role="alert">
                                                        {{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-line pt-6">
                            <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-fg-muted">Kontak & Sosial
                                Media</h3>

                            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                                <x-ui.field label="WhatsApp" name="whatsapp" inputId="store_whatsapp"
                                    hint="Contoh: 08123456789.">
                                    <input type="tel" id="store_whatsapp" name="whatsapp"
                                        value="{{ old('whatsapp', $store->whatsapp) }}" placeholder="08123456789"
                                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                                </x-ui.field>

                                <x-ui.field label="Instagram" name="instagram" inputId="store_instagram">
                                    <input type="text" id="store_instagram" name="instagram"
                                        value="{{ old('instagram', $store->instagram) }}" placeholder="@namatoko"
                                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                                </x-ui.field>

                                <x-ui.field label="TikTok" name="tiktok" inputId="store_tiktok">
                                    <input type="text" id="store_tiktok" name="tiktok"
                                        value="{{ old('tiktok', $store->tiktok) }}" placeholder="@namatoko"
                                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                                </x-ui.field>

                                <x-ui.field label="Facebook" name="facebook" inputId="store_facebook">
                                    <input type="text" id="store_facebook" name="facebook"
                                        value="{{ old('facebook', $store->facebook) }}" placeholder="Tautan halaman"
                                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                                </x-ui.field>

                                <x-ui.field label="Toko Shopee" name="shopee" inputId="store_shopee">
                                    <input type="text" id="store_shopee" name="shopee"
                                        value="{{ old('shopee', $store->shopee) }}" placeholder="Tautan toko Shopee"
                                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                                </x-ui.field>

                                <x-ui.field label="Toko Tokopedia" name="tokopedia" inputId="store_tokopedia">
                                    <input type="text" id="store_tokopedia" name="tokopedia"
                                        value="{{ old('tokopedia', $store->tokopedia) }}"
                                        placeholder="Tautan toko Tokopedia"
                                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                                </x-ui.field>
                            </div>
                        </div>

                        {{-- Pengiriman --}}
                        <div class="border-t border-line pt-6">
                            <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-fg-muted">Asal Pengiriman
                            </h3>

                            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                                <x-ui.field label="Origin City ID" name="origin_city_id" inputId="store_origin_city_id"
                                    hint="ID kota dari RajaOngkir.">
                                    <input type="text" id="store_origin_city_id" name="origin_city_id"
                                        value="{{ old('origin_city_id', $store->origin_city_id) }}" inputmode="numeric"
                                        placeholder="Contoh: 469"
                                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                                </x-ui.field>

                                <x-ui.field label="Origin District ID" name="origin_district_id"
                                    inputId="store_origin_district_id" hint="Opsional, lebih spesifik.">
                                    <input type="text" id="store_origin_district_id" name="origin_district_id"
                                        value="{{ old('origin_district_id', $store->origin_district_id) }}"
                                        inputmode="numeric" placeholder="Contoh: 4691"
                                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                                </x-ui.field>
                            </div>
                        </div>

                        <div
                            class="flex flex-col-reverse gap-3 border-t border-line pt-6 sm:flex-row sm:items-center sm:justify-end">
                            <x-ui.button :href="$profileHomeUrl" variant="outline">Batal</x-ui.button>
                            <x-ui.button type="submit">
                                <x-ui.icon name="check" class="h-4 w-4" /> Simpan Pengaturan Toko
                            </x-ui.button>
                        </div>
                    </div>
                </form>
            @else
                {{-- CTA pemilik toko baru --}}
                <div class="overflow-hidden rounded-panel border border-line bg-surface shadow-sm">
                    <div class="grid lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)]">
                        <div class="p-6 sm:p-8">
                            <span
                                class="inline-flex items-center gap-2 rounded-full border border-brand/25 bg-surface-soft px-3 py-1 text-xs font-semibold text-brand-text">
                                <x-ui.icon name="sparkles" class="h-3.5 w-3.5" /> Peluang UMKM
                            </span>

                            <h2 id="pengaturan-toko" class="mt-4 text-2xl font-bold tracking-tight text-fg">
                                Punya usaha? Buat toko Anda sekarang.
                            </h2>
                            <p class="mt-2 max-w-xl text-sm leading-relaxed text-fg-secondary">
                                Tingkatkan akun Anda menjadi pemilik toko untuk menampilkan profil usaha, mengelola
                                produk, dan menerima pesanan langsung dari storefront.
                            </p>

                            <ul class="mt-5 grid gap-3 text-sm text-fg-secondary sm:grid-cols-2">
                                <li class="flex items-start gap-2.5">
                                    <span
                                        class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-success-soft text-success">
                                        <x-ui.icon name="check" class="h-3.5 w-3.5" />
                                    </span>
                                    Identitas toko & logo
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span
                                        class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-success-soft text-success">
                                        <x-ui.icon name="check" class="h-3.5 w-3.5" />
                                    </span>
                                    Kontak & marketplace
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span
                                        class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-success-soft text-success">
                                        <x-ui.icon name="check" class="h-3.5 w-3.5" />
                                    </span>
                                    Web pribadi
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span
                                        class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-success-soft text-success">
                                        <x-ui.icon name="check" class="h-3.5 w-3.5" />
                                    </span>
                                    Panel pengelolaan toko
                                </li>
                            </ul>

                            <div class="mt-6 flex flex-wrap gap-3">
                                <x-ui.button :href="route('register.store.form')" size="lg">
                                    <x-ui.icon name="shield" class="h-4 w-4" /> Buat Toko Sekarang
                                </x-ui.button>
                                {{-- <x-ui.button :href="route('kontak')" variant="outline">Tanya Pengelola</x-ui.button> --}}
                            </div>
                        </div>

                        <div
                            class="relative flex items-center justify-center overflow-hidden bg-inverse p-8 text-fg-inverse">
                            <div aria-hidden="true"
                                class="pointer-events-none absolute -right-12 -top-12 h-40 w-40 rounded-full bg-brand/25 blur-2xl">
                            </div>
                            <div aria-hidden="true"
                                class="pointer-events-none absolute -bottom-16 -left-10 h-44 w-44 rounded-full bg-brand-sky/20 blur-2xl">
                            </div>

                            <div class="relative w-full max-w-xs space-y-3">
                                <div class="rounded-card border border-white/15 bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-wide text-white/60">Langkah 1</p>
                                    <p class="mt-1 text-sm font-semibold">Lengkapi data toko</p>
                                    <p class="mt-1 text-xs leading-relaxed text-white/60">Nama, logo, deskripsi, dan alamat
                                        usaha.</p>
                                </div>
                                <div class="ml-6 rounded-card border border-white/15 bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-wide text-white/60">Langkah 2</p>
                                    <p class="mt-1 text-sm font-semibold">Atur kontak & pengiriman</p>
                                    <p class="mt-1 text-xs leading-relaxed text-white/60">Hubungkan WhatsApp, marketplace,
                                        dan origin kirim.</p>
                                </div>
                                <div class="ml-12 rounded-card border border-white/15 bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-wide text-white/60">Langkah 3</p>
                                    <p class="mt-1 text-sm font-semibold">Mulai berjualan</p>
                                    <p class="mt-1 text-xs leading-relaxed text-white/60">Kelola produk dan pesanan dari
                                        panel toko.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pratinjau foto profil
            const input = document.querySelector('[data-profile-input]');
            const preview = document.getElementById('profile-preview');
            const initial = document.getElementById('profile-initial');

            if (input && preview) {
                input.addEventListener('change', function(event) {
                    const file = event.target.files && event.target.files[0];

                    if (!file || !file.type.startsWith('image/')) {
                        return;
                    }

                    const reader = new FileReader();

                    reader.onload = function(loadEvent) {
                        preview.src = loadEvent.target.result;
                        preview.classList.remove('hidden');
                        initial?.classList.add('hidden');
                    };

                    reader.readAsDataURL(file);
                });
            }

            // Pratinjau logo toko
            const storeLogoInput = document.querySelector('[data-store-logo-input]');
            const storeLogoPreview = document.getElementById('store-logo-preview');
            const storeLogoPlaceholder = document.querySelector('[data-store-logo-placeholder]');

            if (storeLogoInput && storeLogoPreview) {
                storeLogoInput.addEventListener('change', function(event) {
                    const file = event.target.files && event.target.files[0];

                    if (!file || !file.type.startsWith('image/')) {
                        return;
                    }

                    const reader = new FileReader();

                    reader.onload = function(loadEvent) {
                        storeLogoPreview.src = loadEvent.target.result;
                        storeLogoPreview.classList.remove('hidden');
                        storeLogoPlaceholder?.classList.add('hidden');
                    };

                    reader.readAsDataURL(file);
                });
            }

            // Pratinjau banner toko (maksimal 3)
            document.querySelectorAll('[data-store-banner-input]').forEach(function(input) {
                input.addEventListener('change', function(event) {
                    const file = event.target.files && event.target.files[0];

                    if (!file || !file.type.startsWith('image/')) {
                        return;
                    }

                    const index = input.dataset.bannerIndex;
                    const preview = document.getElementById('store-banner-preview-' + index);
                    const placeholder = document.querySelector('[data-store-banner-placeholder="' +
                        index + '"]');

                    const reader = new FileReader();

                    reader.onload = function(loadEvent) {
                        if (!preview) {
                            return;
                        }

                        preview.src = loadEvent.target.result;
                        preview.classList.remove('hidden');
                        placeholder?.classList.add('hidden');
                    };

                    reader.readAsDataURL(file);
                });
            });

            // Nomor telepon hanya angka
            const phoneInput = document.getElementById('phone_number');

            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }

            // Tombol mata password profil
            function setupToggle(buttonId, inputId) {
                const password = document.getElementById(inputId);
                const button = document.getElementById(buttonId);

                if (!password || !button) return;

                const iconShow = button.querySelector('[data-icon-show]');
                const iconHide = button.querySelector('[data-icon-hide]');

                button.addEventListener('click', function () {
                    const visible = password.type === 'password';

                    password.type = visible ? 'text' : 'password';

                    iconShow.classList.toggle('hidden', visible);
                    iconHide.classList.toggle('hidden', !visible);

                    button.setAttribute('aria-pressed', String(visible));
                    button.setAttribute(
                        'aria-label',
                        visible ? 'Sembunyikan password' : 'Tampilkan password'
                    );
                });
            }

            setupToggle('toggle-current-password-btn', 'current_password');

            // Verifikasi password lama → buka kunci field password baru
            const btnVerify = document.getElementById('btn-verify-password');
            const currentInput = document.getElementById('current_password');
            const newPasswordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const newWrapper = document.getElementById('new-password-wrapper');
            const confirmWrapper = document.getElementById('confirm-password-wrapper');
            const statusLocked = document.getElementById('password-status-locked');
            const statusUnlocked = document.getElementById('password-status-unlocked');
            const verifyMsg = document.getElementById('verify-message');

            const toggleBtnNew = document.getElementById('toggle-profile-password-btn');
            const toggleBtnConfirm = document.getElementById('toggle-profile-confirm-password-btn');

            let isPasswordVerified = false;

            btnVerify?.addEventListener('click', async function () {
                const value = currentInput.value.trim();

                if (!value) {
                    verifyMsg.textContent = 'Masukkan password lama terlebih dahulu.';
                    verifyMsg.className = 'mt-1.5 text-xs text-danger';
                    return;
                }

                btnVerify.disabled = true;
                btnVerify.textContent = 'Memverifikasi...';
                verifyMsg.textContent = '';

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                    const response = await fetch('{{ route('profil.verify.password') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ current_password: value }),
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        isPasswordVerified = true;

                        // Buka kunci
                        newPasswordInput.disabled = false;
                        confirmInput.disabled = false;
                        toggleBtnNew.disabled = false;
                        toggleBtnConfirm.disabled = false;
                        newWrapper.classList.remove('opacity-50', 'pointer-events-none');
                        confirmWrapper.classList.remove('opacity-50', 'pointer-events-none');
                        newWrapper.querySelector('[aria-hidden="true"]')?.remove();
                        confirmWrapper.querySelector('[aria-hidden="true"]')?.remove();

                        // Status
                        statusLocked.classList.add('hidden');
                        statusUnlocked.classList.remove('hidden');
                        verifyMsg.textContent = 'Password lama valid.';
                        verifyMsg.className = 'mt-1.5 text-xs text-success';
                    } else {
                        isPasswordVerified = false;
                        verifyMsg.textContent = data.message || 'Password lama salah.';
                        verifyMsg.className = 'mt-1.5 text-xs text-danger';
                        currentInput.setCustomValidity('Password lama tidak sesuai.');
                        setTimeout(() => currentInput.setCustomValidity(''), 3000);
                    }
                } catch (e) {
                    verifyMsg.textContent = 'Terjadi kesalahan. Coba lagi.';
                    verifyMsg.className = 'mt-1.5 text-xs text-danger';
                } finally {
                    btnVerify.disabled = false;
                    btnVerify.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg> Konfirmasi';
                }
            });

            // Reset kunci jika user menghapus isi password lama
            currentInput?.addEventListener('input', function () {
                if (isPasswordVerified && !this.value.trim()) {
                    isPasswordVerified = false;
                    newPasswordInput.disabled = true;
                    confirmInput.disabled = true;
                    toggleBtnNew.disabled = true;
                    toggleBtnConfirm.disabled = true;
                    newWrapper.classList.add('opacity-50', 'pointer-events-none');
                    confirmWrapper.classList.add('opacity-50', 'pointer-events-none');
                    // Tambah kembali overlay penghalang
                    if (!newWrapper.querySelector('[aria-hidden="true"]')) {
                        newWrapper.insertAdjacentHTML('beforeend', '<div class="absolute inset-0 z-20" aria-hidden="true"></div>');
                    }
                    if (!confirmWrapper.querySelector('[aria-hidden="true"]')) {
                        confirmWrapper.insertAdjacentHTML('beforeend', '<div class="absolute inset-0 z-20" aria-hidden="true"></div>');
                    }
                    statusLocked.classList.remove('hidden');
                    statusUnlocked.classList.add('hidden');
                    verifyMsg.textContent = '';
                }
            });

            setupToggle('toggle-profile-password-btn', 'password');
            setupToggle('toggle-profile-confirm-password-btn', 'password_confirmation');
        });
        
    </script>
@endpush
