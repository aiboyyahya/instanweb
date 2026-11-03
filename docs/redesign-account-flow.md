# Catatan Redesign — Login, Registrasi Toko, dan Profil

Tanggal: 3 November 2026

## Ringkasan

Redesign difokuskan pada alur akun tanpa mengubah inti transaksi, katalog, pembayaran, pengiriman, dan panel admin yang sudah berjalan.

- Satu halaman login dengan pengalihan otomatis berdasarkan peran.
- Dua jalur registrasi: pembeli (`/register`) dan pemilik toko (`/register/toko`).
- Profil diperluas: data pribadi + foto profil, lalu pengaturan toko untuk pemilik toko atau CTA buat toko untuk pembeli.

## File dibuat

- `database/migrations/2026_11_03_000000_add_user_id_to_stores_table.php`
- `resources/views/auth/register-store.blade.php`
- `tests/Feature/AccountFlowTest.php`
- `docs/redesign-account-flow.md`

## File diubah

- `app/Http/Controllers/GoogleLoginController.php`
- `app/Http/Controllers/ProfilController.php`
- `app/Models/Store.php`
- `app/Models/User.php`
- `app/Filament/Pages/Pengaturan.php`
- `app/Providers/Filament/AdminPanelProvider.php`
- `routes/web.php`
- `resources/views/layouts/auth.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/Profil.blade.php`
- `resources/views/home.blade.php`
- `resources/views/selamatdatang.blade.php`
- `resources/views/components/ui/field.blade.php`
- `resources/css/app.css`
- `tests/Feature/AdminPagesTest.php`

## Perubahan visual dan UX

### Login
- Satu formulir login untuk semua peran.
- Informasi bahwa peran akun dikenali otomatis.
- Dua kartu tujuan registrasi: pembeli dan pemilik toko.
- Tetap menyediakan login Google dan "Ingat saya".

### Registrasi pembeli
- Pemilih jenis akun (pembeli aktif, pemilik toko sebagai tautan).
- Indikator kekuatan password sederhana.
- Validasi server tetap menjadi sumber kebenaran.

### Registrasi toko
- Formulir baru dengan bagian: akun, identitas toko, kontak & marketplace, dan asal pengiriman.
- Untuk pengguna yang sudah masuk, bagian akun disembunyikan dan akun ditingkatkan menjadi pemilik toko.
- Pratinjau logo toko sebelum unggah.
- Kolom mengikuti pengaturan toko di panel admin: nama, alamat, deskripsi, logo, WhatsApp, Instagram, TikTok, Facebook, Shopee, Tokopedia, origin city/district.

### Profil
- Kartu ringkasan akun dengan avatar, peran, dan tautan cepat.
- Form data pribadi + foto profil + ubah password tetap berfungsi seperti sebelumnya.
- Pemilik toko melihat formulir Pengaturan Toko langsung di profil.
- Pembeli melihat CTA "Buat Toko Sekarang" beserta langkah ringkas.

### Motion
- Fade-in pada halaman dan kartu autentikasi.
- `iw-slide-up`, `iw-pop`, dan `iw-lift` untuk umpan balik halus.
- Semua animasi mengikuti `prefers-reduced-motion`.

## Fitur lama yang dipertahankan

- Login email/password dan Google OAuth.
- Registrasi pembeli.
- Logout, keranjang, checkout, pesanan, ulasan.
- CRUD produk/kategori/transaksi/artikel di Filament.
- Pengaturan toko di panel admin.
- Validasi, middleware autentikasi, dan relasi database lama.

## Perubahan backend (aditif)

- Kolom `stores.user_id` nullable untuk menghubungkan toko dengan pemilik.
- Relasi `User::store()` dan `Store::owner()`.
- `User` mengimplementasikan `FilamentUser` agar hanya peran admin dapat membuka panel pada semua environment.
- `Pengaturan` Filament memprioritaskan toko milik pengguna yang sedang masuk.

## Testing

- `php artisan test`: 19 tests lulus (82 assertions).
- `npm run build`: berhasil.
- Cakupan baru: redirect login per peran, registrasi toko tamu, upgrade pembeli menjadi pemilik toko, CTA profil, pengaturan toko di profil, dan otorisasi pembeli.

## Kendala / batasan

- Arsitektur data masih single-store: storefront publik memakai toko pertama. Pendaftaran toko baru menambah data toko milik pengguna, tetapi belum ada isolasi produk per toko atau domain kustom.
- Produk belum memiliki `store_id`, sehingga panel admin masih mengelola katalog global.
- Peningkatan multi-toko penuh memerlukan keputusan arsitektur dan migrasi data tersendiri.
