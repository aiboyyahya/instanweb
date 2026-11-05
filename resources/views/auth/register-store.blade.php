@extends('layouts.auth')

@section('title', 'Daftar Toko')

@section('form_width', 'max-w-3xl')

@section('aside_title', 'Punya produk bernilai? Buka toko online Anda.')

@section('aside_description', 'Daftarkan akun pemilik toko, lengkapi identitas usaha, lalu kelola produk dan pesanan dari satu panel.')

@section('aside_points')
    <ul class="space-y-3 text-sm text-white/80">
        <li class="flex items-start gap-3">
            <x-ui.icon name="check-circle" class="mt-0.5 h-5 w-5 text-brand-sky" />
            <span>Satu akun untuk mengelola toko dan pesanan.</span>
        </li>
        <li class="flex items-start gap-3">
            <x-ui.icon name="check-circle" class="mt-0.5 h-5 w-5 text-brand-sky" />
            <span>Identitas toko, kontak, dan marketplace dalam satu tempat.</span>
        </li>
        <li class="flex items-start gap-3">
            <x-ui.icon name="check-circle" class="mt-0.5 h-5 w-5 text-brand-sky" />
            <span>Pengaturan toko bisa diperbarui kapan saja dari profil.</span>
        </li>
    </ul>
@endsection

@section('content')
    @php($isExistingUser = (bool) $currentUser)

    <div class="overflow-hidden rounded-panel border border-line bg-surface shadow-sm">
        <div class="h-1.5 w-full bg-gradient-to-r from-brand via-brand-hover to-brand-sky" aria-hidden="true"></div>

        <div class="p-6 sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-text">Pemilik toko</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-fg">
                        {{ $isExistingUser ? 'Buat toko Anda' : 'Daftar & buat toko' }}
                    </h1>
                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-fg-muted">
                        {{ $isExistingUser
                            ? 'Akun Anda akan ditingkatkan menjadi pemilik toko. Lengkapi informasi toko di bawah ini.'
                            : 'Isi data akun dan informasi toko. Setelah selesai, Anda langsung masuk ke panel pengelolaan toko. Kode verifikasi email dan SMS akan dikirim untuk mengamankan akun.' }}
                    </p>
                </div>

                <div class="flex items-center gap-2 rounded-full border border-line bg-surface-muted px-3 py-1.5 text-xs font-semibold text-fg-secondary">
                    <x-ui.icon name="shield" class="h-4 w-4 text-info" />
                    Akun pemilik toko
                </div>
            </div>

            @if ($isExistingUser)
                <div class="mt-5 flex items-start gap-3 rounded-card border border-info/25 bg-info-soft p-3.5 text-sm text-info">
                    <x-ui.icon name="user" class="mt-0.5 h-5 w-5" />
                    <p class="leading-relaxed">
                        Masuk sebagai <span class="font-semibold">{{ $currentUser->name }}</span>
                        ({{ $currentUser->email }}). Data akun tidak perlu diisi ulang.
                    </p>
                </div>
            @else
                <div class="mt-5 grid gap-2 sm:grid-cols-2" role="group" aria-label="Pilih jenis akun">
                    <a href="{{ route('register.form') }}"
                        class="iw-lift group flex items-center gap-3 rounded-card border border-line bg-surface-muted p-3 hover:border-cta/40 hover:bg-surface-soft">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface text-brand-text">
                            <x-ui.icon name="shopping-bag" class="h-4 w-4" />
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-fg">Hanya belanja</span>
                            <span class="block text-xs text-fg-muted">Daftar sebagai pembeli</span>
                        </span>
                        <x-ui.icon name="arrow-right"
                            class="ml-auto h-4 w-4 text-fg-muted transition group-hover:translate-x-0.5 group-hover:text-cta" />
                    </a>

                    <div class="flex items-center gap-3 rounded-card border-2 border-cta/50 bg-surface-soft p-3" aria-current="true">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface text-info">
                            <x-ui.icon name="shield" class="h-4 w-4" />
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-fg">Buka toko</span>
                            <span class="block text-xs text-fg-muted">Jual produk UMKM</span>
                        </span>
                        <x-ui.icon name="check-circle" class="ml-auto h-4 w-4 text-cta" />
                    </div>
                </div>
            @endif

            @if (session('error'))
                <x-ui.alert variant="danger" class="mt-5">{{ session('error') }}</x-ui.alert>
            @endif

            @if ($errors->any())
                <x-ui.alert variant="danger" class="mt-5">
                    Periksa kembali data yang Anda masukkan.
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5"
                data-store-register-form>
                @csrf

                @unless ($isExistingUser)
                    {{-- Langkah 1: Akun --}}
                    <section class="rounded-panel border border-line bg-surface-muted p-4 sm:p-5" aria-labelledby="akun-pemilik">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-cta text-sm font-bold text-on-cta"
                                aria-hidden="true">1</span>
                            <div>
                                <h2 id="akun-pemilik" class="text-base font-bold text-fg">Akun pemilik</h2>
                                <p class="text-xs text-fg-muted">Data untuk masuk ke panel toko.</p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <x-ui.field label="Nama lengkap" name="name" required class="sm:col-span-2">
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name"
                                    placeholder="Contoh: Siti Rahma"
                                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                                    class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                            </x-ui.field>

                            <x-ui.field label="Email" name="email" required>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    autocomplete="email" placeholder="nama@email.com"
                                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                                    class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                            </x-ui.field>

                            <x-ui.field label="Nomor telepon" name="phone_number"
                                hint="Opsional. Kode verifikasi SMS dikirim ke nomor ini.">
                                <input type="tel" id="phone_number" name="phone_number"
                                    value="{{ old('phone_number') }}" autocomplete="tel" inputmode="tel"
                                    placeholder="08123456789"
                                    @error('phone_number') aria-invalid="true" aria-describedby="phone_number-error" @enderror
                                    class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                            </x-ui.field>

                            <x-ui.field label="Password" name="password" required hint="Minimal 8 karakter.">
                                <div class="relative rounded-field border border-line-strong bg-surface overflow-hidden">
                                    <input type="password" id="password" name="password" required autocomplete="new-password"
                                        placeholder="Buat password"
                                        @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                                        class="h-12 w-full bg-transparent px-3.5 text-sm text-fg placeholder:text-fg-muted pr-11">
                                    <button type="button" id="toggle-store-password-btn"
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
                            </x-ui.field>

                            <x-ui.field label="Konfirmasi password" name="password_confirmation" required class="sm:col-span-2">
                                <div class="relative rounded-field border border-line-strong bg-surface overflow-hidden">
                                    <input type="password" id="password_confirmation" name="password_confirmation" required
                                        autocomplete="new-password" placeholder="Ulangi password"
                                        @error('password_confirmation') aria-invalid="true" aria-describedby="password_confirmation-error" @enderror
                                        class="h-12 w-full bg-transparent px-3.5 text-sm text-fg placeholder:text-fg-muted pr-11">
                                    <button type="button" id="toggle-store-confirm-password-btn"
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
                            </x-ui.field>
                        </div>
                    </section>
                @endunless

                {{-- Langkah 2: Identitas toko --}}
                <section class="rounded-panel border border-line bg-surface-muted p-4 sm:p-5" aria-labelledby="identitas-toko">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-cta text-sm font-bold text-on-cta"
                            aria-hidden="true">{{ $isExistingUser ? 1 : 2 }}</span>
                        <div>
                            <h2 id="identitas-toko" class="text-base font-bold text-fg">Identitas toko</h2>
                            <p class="text-xs text-fg-muted">Tampil di header, footer, dan halaman toko.</p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <x-ui.field label="Nama toko" name="store_name" required class="sm:col-span-2">
                            <input type="text" id="store_name" name="store_name" value="{{ old('store_name') }}" required
                                placeholder="Contoh: Toko Kopi Nusantara"
                                @error('store_name') aria-invalid="true" aria-describedby="store_name-error" @enderror
                                class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                        </x-ui.field>

                        <x-ui.field label="Alamat toko" name="address" class="sm:col-span-2">
                            <input type="text" id="address" name="address" value="{{ old('address') }}"
                                autocomplete="street-address" placeholder="Jalan, nomor, kota"
                                class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                        </x-ui.field>

                        <x-ui.field label="Deskripsi toko" name="description" class="sm:col-span-2">
                            <textarea id="description" name="description" rows="3"
                                placeholder="Ceritakan singkat produk dan nilai toko Anda."
                                class="w-full rounded-field border border-line-strong bg-surface px-3.5 py-3 text-sm text-fg placeholder:text-fg-muted">{{ old('description') }}</textarea>
                        </x-ui.field>

                        <div class="sm:col-span-2">
                            <p class="block text-sm font-medium text-fg-secondary">Logo toko</p>
                            <div class="mt-2 flex flex-col gap-4 rounded-card border border-line bg-surface p-4 sm:flex-row sm:items-center">
                                <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-line bg-surface-soft">
                                    <img data-store-logo-preview src="" alt="Pratinjau logo toko" class="hidden h-full w-full object-cover">
                                    <x-ui.icon name="sparkles" data-store-logo-placeholder class="h-7 w-7 text-fg-muted" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <label for="logo"
                                        class="inline-flex min-h-10 cursor-pointer items-center justify-center gap-2 rounded-field border border-line-strong bg-surface px-4 text-sm font-semibold text-fg transition hover:bg-surface-soft">
                                        <x-ui.icon name="plus" class="h-4 w-4" />
                                        Pilih logo
                                    </label>
                                    <input type="file" name="logo" id="logo" accept="image/*" class="sr-only"
                                        data-store-logo-input>
                                    <p class="mt-2 text-xs leading-relaxed text-fg-muted">JPG, PNG, GIF, atau WEBP. Maksimal 10 MB.</p>
                                    @error('logo')
                                        <p class="mt-1 text-xs font-medium text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Langkah 3: Kontak & marketplace --}}
                <section class="rounded-panel border border-line bg-surface-muted p-4 sm:p-5" aria-labelledby="kontak-toko">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-cta text-sm font-bold text-on-cta"
                            aria-hidden="true">{{ $isExistingUser ? 2 : 3 }}</span>
                        <div>
                            <h2 id="kontak-toko" class="text-base font-bold text-fg">Kontak & marketplace</h2>
                            <p class="text-xs text-fg-muted">Opsional. Membantu pelanggan menghubungi toko Anda.</p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <x-ui.field label="WhatsApp" name="whatsapp" hint="Contoh: 08123456789.">
                            <input type="tel" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}"
                                placeholder="08123456789"
                                class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                        </x-ui.field>

                        <x-ui.field label="Instagram" name="instagram">
                            <input type="text" id="instagram" name="instagram" value="{{ old('instagram') }}"
                                placeholder="@namatoko"
                                class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                        </x-ui.field>

                        <x-ui.field label="TikTok" name="tiktok">
                            <input type="text" id="tiktok" name="tiktok" value="{{ old('tiktok') }}"
                                placeholder="@namatoko"
                                class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                        </x-ui.field>

                        <x-ui.field label="Facebook" name="facebook">
                            <input type="text" id="facebook" name="facebook" value="{{ old('facebook') }}"
                                placeholder="Tautan atau nama halaman"
                                class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                        </x-ui.field>

                        <x-ui.field label="Toko Shopee" name="shopee">
                            <input type="text" id="shopee" name="shopee" value="{{ old('shopee') }}"
                                placeholder="Tautan toko Shopee"
                                class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                        </x-ui.field>

                        <x-ui.field label="Toko Tokopedia" name="tokopedia">
                            <input type="text" id="tokopedia" name="tokopedia" value="{{ old('tokopedia') }}"
                                placeholder="Tautan toko Tokopedia"
                                class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                        </x-ui.field>
                    </div>
                </section>

                {{-- Langkah 4: Asal pengiriman --}}
                <section class="rounded-panel border border-line bg-surface-muted p-4 sm:p-5" aria-labelledby="pengiriman-toko">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-cta text-sm font-bold text-on-cta"
                            aria-hidden="true">{{ $isExistingUser ? 3 : 4 }}</span>
                        <div>
                            <h2 id="pengiriman-toko" class="text-base font-bold text-fg">Asal pengiriman</h2>
                            <p class="text-xs text-fg-muted">Dipakai untuk menghitung ongkos kirim.</p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <x-ui.field label="Origin City ID" name="origin_city_id" hint="ID kota dari RajaOngkir.">
                            <input type="text" id="origin_city_id" name="origin_city_id" value="{{ old('origin_city_id') }}"
                                inputmode="numeric" placeholder="Contoh: 469"
                                class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                        </x-ui.field>

                        <x-ui.field label="Origin District ID" name="origin_district_id" hint="Opsional, lebih spesifik.">
                            <input type="text" id="origin_district_id" name="origin_district_id"
                                value="{{ old('origin_district_id') }}" inputmode="numeric" placeholder="Contoh: 4691"
                                class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                        </x-ui.field>
                    </div>
                </section>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ $isExistingUser ? route('profil') : route('login.form') }}"
                        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-field px-3 text-sm font-semibold text-fg-secondary transition hover:bg-surface-soft hover:text-fg">
                        <x-ui.icon name="arrow-left" class="h-4 w-4" />
                        {{ $isExistingUser ? 'Kembali ke profil' : 'Sudah punya akun? Masuk' }}
                    </a>

                    <x-ui.button type="submit" size="lg">
                        <x-ui.icon name="check" class="h-4 w-4" />
                        {{ $isExistingUser ? 'Buat Toko' : 'Daftar & Buat Toko' }}
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>

    <p class="mt-4 text-center text-xs leading-relaxed text-fg-muted">
        Dengan mendaftar, Anda menyetujui penggunaan data untuk keperluan akun, toko, dan pesanan.
    </p>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.querySelector('[data-store-logo-input]');
            const preview = document.querySelector('[data-store-logo-preview]');
            const placeholder = document.querySelector('[data-store-logo-placeholder]');

            if (input && preview) {
                input.addEventListener('change', function (event) {
                    const file = event.target.files && event.target.files[0];

                    if (!file || !file.type.startsWith('image/')) {
                        return;
                    }

                    const reader = new FileReader();

                    reader.onload = function (loadEvent) {
                        preview.src = loadEvent.target.result;
                        preview.classList.remove('hidden');
                        placeholder?.classList.add('hidden');
                    };

                    reader.readAsDataURL(file);
                });
            }

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

            setupToggle('toggle-store-password-btn', 'password');
            setupToggle('toggle-store-confirm-password-btn', 'password_confirmation');
        });
    </script>
@endpush
