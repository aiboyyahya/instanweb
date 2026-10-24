# InstanWeb

InstanWeb adalah proyek marketplace untuk membantu UMKM memasarkan produk secara online dan menjangkau pelanggan dari berbagai daerah di Indonesia.

## Teknologi
- Laravel 12
- PHP 8.2+
- MySQL
- Filament 4
- Tailwind CSS dan Vite

## Menjalankan di komputer lokal

1. Buat database MySQL bernama `instanweb`.
2. Salin `.env.example` menjadi `.env`.
3. Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada `.env`.
4. Jalankan perintah:

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan storage:link
npm install
npm run build
php artisan serve
```

## Catatan penting
- File `.env`, database lokal, folder dependency, dan cache runtime tidak disertakan dalam arsip ini demi keamanan dan ukuran file.
- Gunakan salinan database untuk pengembangan. Jangan menjalankan migrasi pada database produksi sebelum backup.
- Versi ini adalah tahap rebranding dan penyesuaian pesan produk untuk InstanWeb. Struktur saat ini masih berorientasi pada satu toko; fitur marketplace multi-UMKM (akun penjual, toko per penjual, dan pemisahan pesanan/produk per penjual) memerlukan perubahan database dan alur backend lanjutan.
