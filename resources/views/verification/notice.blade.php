@extends('layouts.app')

@section('title', 'Verifikasi Akun | ' . config('app.name', 'instanweb'))

@section('content')
    @php
        $emailVerified = $user->hasVerifiedEmail();
        $phoneVerified = $user->hasVerifiedPhone();
        $hasPhone = filled($user->phone_number);
    @endphp

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <x-ui.breadcrumb :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Verifikasi Akun'],
        ]" />

        <x-ui.page-header class="mt-4" eyebrow="Keamanan akun" title="Verifikasi Email & Nomor Telepon"
            description="Kami mengirim kode unik untuk memastikan email dan nomor telepon yang Anda gunakan benar-benar milik Anda.">
            <x-ui.button :href="route('home')" variant="outline">
                <x-ui.icon name="arrow-left" class="h-4 w-4" /> Kembali
            </x-ui.button>
        </x-ui.page-header>

        @if ($emailVerified && $phoneVerified)
            <div class="mt-6">
                <x-ui.alert variant="success" title="Akun terverifikasi">
                    Email dan nomor telepon Anda sudah terverifikasi. Anda dapat bertransaksi dengan aman.
                </x-ui.alert>
            </div>
        @endif

        <div class="mt-6 grid gap-5 lg:grid-cols-2">
            {{-- Email --}}
            <section class="rounded-panel border border-line bg-surface p-6 shadow-sm sm:p-7" aria-labelledby="verifikasi-email">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-text">Langkah 1</p>
                        <h2 id="verifikasi-email" class="mt-1 text-lg font-bold text-fg">Verifikasi email</h2>
                        <p class="mt-1 text-sm text-fg-muted">Kode dikirim ke alamat email terdaftar.</p>
                    </div>
                    <span
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-card {{ $emailVerified ? 'bg-success-soft text-success' : 'bg-surface-soft text-brand-text' }}">
                        <x-ui.icon :name="$emailVerified ? 'check-circle' : 'mail'" class="h-5 w-5" />
                    </span>
                </div>

                <div class="mt-5 rounded-card border border-line bg-surface-muted p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-fg-muted">Email</p>
                    <p class="mt-1 break-all text-sm font-semibold text-fg">{{ $user->email }}</p>
                    <div class="mt-2">
                        @if ($emailVerified)
                            <x-ui.badge variant="success">
                                <x-ui.icon name="check" class="h-3.5 w-3.5" /> Terverifikasi
                            </x-ui.badge>
                        @else
                            <x-ui.badge variant="warning">
                                <x-ui.icon name="alert-circle" class="h-3.5 w-3.5" /> Belum terverifikasi
                            </x-ui.badge>
                        @endif
                    </div>
                </div>

                @unless ($emailVerified)
                    <form method="POST" action="{{ route('verification.email.send') }}" class="mt-5">
                        @csrf
                        <x-ui.button type="submit" variant="outline" block>
                            <x-ui.icon name="mail" class="h-4 w-4" /> Kirim kode ke email
                        </x-ui.button>
                    </form>

                    <form method="POST" action="{{ route('verification.email.confirm') }}" class="mt-4 space-y-3">
                        @csrf

                        <x-ui.field label="Kode email" name="email_code" required hint="6 digit dari email Anda.">
                            <input type="text" id="email_code" name="email_code" value="{{ old('email_code') }}"
                                inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code"
                                placeholder="000000"
                                @error('email_code') aria-invalid="true" aria-describedby="email_code-error" @enderror
                                class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-center text-lg font-semibold tracking-[0.3em] text-fg placeholder:text-fg-muted">
                        </x-ui.field>

                        @error('verification')
                            <p class="text-xs font-medium text-danger" role="alert">{{ $message }}</p>
                        @enderror

                        <x-ui.button type="submit" block>
                            <x-ui.icon name="check" class="h-4 w-4" /> Verifikasi email
                        </x-ui.button>
                    </form>
                @endunless
            </section>

            {{-- Telepon --}}
            <section class="rounded-panel border border-line bg-surface p-6 shadow-sm sm:p-7" aria-labelledby="verifikasi-telepon">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-text">Langkah 2</p>
                        <h2 id="verifikasi-telepon" class="mt-1 text-lg font-bold text-fg">Verifikasi nomor telepon</h2>
                        <p class="mt-1 text-sm text-fg-muted">Kode dikirim melalui SMS ke nomor terdaftar.</p>
                    </div>
                    <span
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-card {{ $phoneVerified ? 'bg-success-soft text-success' : 'bg-surface-soft text-info' }}">
                        <x-ui.icon :name="$phoneVerified ? 'check-circle' : 'phone'" class="h-5 w-5" />
                    </span>
                </div>

                <form method="POST" action="{{ route('verification.phone.update') }}" class="mt-5 space-y-3">
                    @csrf
                    <x-ui.field label="Nomor telepon" name="phone_number" hint="Contoh: 08123456789. Gunakan nomor aktif.">
                        <input type="tel" id="phone_number" name="phone_number"
                            value="{{ old('phone_number', $user->phone_number) }}" inputmode="tel" autocomplete="tel"
                            placeholder="08123456789"
                            @error('phone_number') aria-invalid="true" aria-describedby="phone_number-error" @enderror
                            class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                    </x-ui.field>

                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            @if ($hasPhone && $phoneVerified)
                                <x-ui.badge variant="success">
                                    <x-ui.icon name="check" class="h-3.5 w-3.5" /> Terverifikasi
                                </x-ui.badge>
                            @elseif ($hasPhone)
                                <x-ui.badge variant="warning">
                                    <x-ui.icon name="alert-circle" class="h-3.5 w-3.5" /> Belum terverifikasi
                                </x-ui.badge>
                            @else
                                <x-ui.badge variant="info">
                                    <x-ui.icon name="phone" class="h-3.5 w-3.5" /> Belum diisi
                                </x-ui.badge>
                            @endif
                        </div>

                        <x-ui.button type="submit" variant="outline" size="sm">
                            Simpan nomor
                        </x-ui.button>
                    </div>
                </form>

                @if ($hasPhone && ! $phoneVerified)
                    <form method="POST" action="{{ route('verification.phone.send') }}" class="mt-4">
                        @csrf
                        <x-ui.button type="submit" variant="outline" block>
                            <x-ui.icon name="phone" class="h-4 w-4" /> Kirim kode SMS
                        </x-ui.button>
                    </form>

                    <form method="POST" action="{{ route('verification.phone.confirm') }}" class="mt-4 space-y-3">
                        @csrf

                        <x-ui.field label="Kode SMS" name="phone_code" required hint="6 digit dari SMS Anda.">
                            <input type="text" id="phone_code" name="phone_code" value="{{ old('phone_code') }}"
                                inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code"
                                placeholder="000000"
                                @error('phone_code') aria-invalid="true" aria-describedby="phone_code-error" @enderror
                                class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-center text-lg font-semibold tracking-[0.3em] text-fg placeholder:text-fg-muted">
                        </x-ui.field>

                        <x-ui.button type="submit" block>
                            <x-ui.icon name="check" class="h-4 w-4" /> Verifikasi nomor
                        </x-ui.button>
                    </form>
                @endif

                @if (! $hasPhone)
                    <p class="mt-4 rounded-card border border-info/25 bg-info-soft px-3.5 py-3 text-xs leading-relaxed text-info">
                        Simpan nomor telepon terlebih dahulu untuk menerima kode verifikasi melalui SMS.
                    </p>
                @endif
            </section>
        </div>
    </div>
@endsection
