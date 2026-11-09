# Upload Gambar di Server Produksi

Dokumen ini menjelaskan penyebab umum kegagalan unggah gambar Filament
(`The data.image.<uuid> failed to upload`) dan cara memverifikasinya di
produksi.

## Gejala

- Unggah gambar dari panel admin gagal dengan pesan
  `The data.image.<uuid> failed to upload.`
- Halaman lain (login, CRUD tanpa file, checkout) tetap berjalan normal.
- File kecil kadang berhasil, file dari kamera ponsel selalu gagal.

## Penyebab utama

### 1. Request di balik reverse proxy tidak dipercaya (401)

Produksi berjalan di balik reverse proxy (Railway/Heroku/Nginx). Bila
`TrustProxies` tidak dikonfigurasi:

- Laravel mengira request masih `http://`, padahal browser memakai `https://`.
- `AppServiceProvider` memaksa URL bertanda tangan memakai `https`.
- Saat Livewire mengunggah file ke URL bertanda tangan tersebut, tanda
  tangannya tidak cocok dengan request yang terbaca `http` → **401**.
- FilePond menampilkan pesan gagal unggah.

Perbaikan: `bootstrap/app.php` kini memanggil `trustProxies()` otomatis saat
`APP_ENV=production` (atau mengikuti `TRUSTED_PROXIES`).

### 2. Batas ukuran upload PHP lebih kecil dari foto ponsel (413)

Default PHP sering hanya `upload_max_filesize=2M`. Foto kamera biasanya
3–8 MB sehingga ditolak sebelum divalidasi aplikasi.

Perbaikan:

- `public/.user.ini` menaikkan batas ke 12M/16M untuk PHP-FPM/CGI.
- Semua field gambar memakai `maxSize(10240)` (10 MB) dan
  `imageResizeTargetWidth(...)`, sehingga browser memperkecil gambar
  (1200–1920 px) **sebelum** dikirim ke server.
- Field ikon kategori dibatasi 5 MB dan diperkecil ke 512 px.

## Checklist server produksi

1. Pastikan variabel lingkungan berikut ada (Railway → Variables):

   ```env
   APP_ENV=production
   APP_URL=https://domain-anda
   ```

   `TRUSTED_PROXIES` boleh dikosongkan karena otomatis `*` di produksi.

2. Pastikan batas PHP benar. Cek lewat shell server:

   ```bash
   php -i | grep -E "upload_max_filesize|post_max_size"
   ```

   Target minimal:

   ```ini
   upload_max_filesize = 12M
   post_max_size = 16M
   ```

   Jika `.user.ini` tidak terbaca (mis. server memakai `php artisan serve`),
   samakan nilainya pada `php.ini`/konfigurasi Nginx (`client_max_body_size 16m;`).

3. Pastikan folder penyimpanan dapat ditulis dan symlink tersedia:

   ```bash
   php artisan storage:link
   php artisan optimize:clear
   ```

   Folder yang perlu writable: `storage/`, `storage/app/private`,
   `storage/app/public`, `bootstrap/cache`.

4. Pastikan Nginx/edge proxy mengizinkan body upload:

   ```nginx
   client_max_body_size 16m;
   ```

## Cara memverifikasi setelah deploy

1. Buka DevTools → Network, lalu unggah gambar.
2. Perhatikan request `POST /livewire/upload-file?...`:
   - **401** → masalah signature/trusted proxy.
   - **413** → batas ukuran PHP/proxy masih terlalu kecil.
   - **419** → sesi/CSRF kedaluwarsa (muat ulang halaman).
   - **422** → file ditolak validasi (format/ukuran).
   - **200** → unggah berhasil; jika simpan form gagal, periksa izin
     `storage/app/public`.
3. Cek `storage/logs/laravel.log` untuk detail error server.

## Catatan

- `storage:link` hanya diperlukan agar file bisa diakses lewat `/storage/...`.
  Symlink tidak memengaruhi proses unggah itu sendiri.
- Jangan menurunkan `maxSize` di bawah ukuran foto asli jika ingin
  pemanfaatan resize sisi browser; validasi ukuran FilePond berjalan pada
  file asli sebelum transformasi.
