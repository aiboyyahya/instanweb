# Verifikasi Email (Gmail) & Nomor Telepon (SMS) — Produksi

Dokumen ini menjelaskan cara mengaktifkan verifikasi email lewat Gmail SMTP dan
verifikasi nomor telepon lewat SMS di server produksi, serta cara mengujinya.

## Ringkasan alur

1. Pengguna mendaftar → `email_verified_at` kosong.
2. Halaman **/verifikasi** mengirim kode 6 digit:
   - Email: `ContactVerificationService::sendEmailCode()` → `VerificationCodeMail`.
   - SMS: `ContactVerificationService::sendPhoneCode()` → `SmsService`.
3. Kode berlaku **10 menit**, maksimal kirim ulang tiap **60 detik**.
4. Di produksi, aplikasi **menolak** pengiriman jika:
   - `MAIL_MAILER=log` atau `array` (email hanya masuk log).
   - `SMS_DRIVER=log` (SMS hanya masuk log).
   Tujuannya agar kode tidak pernah dianggap terkirim secara palsu.

## 1. Email lewat Gmail SMTP

### Siapkan App Password Google

1. Aktifkan **2-Step Verification** pada akun Google.
2. Buka <https://myaccount.google.com/apppasswords>.
3. Buat App Password (16 karakter). Simpan nilainya.
4. Gunakan App Password tersebut sebagai `MAIL_PASSWORD` — **bukan** password login Gmail.

### Variabel server produksi

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_SCHEME=smtp
MAIL_USERNAME=akun@gmail.com
MAIL_PASSWORD=app-password-16-karakter
MAIL_FROM_ADDRESS=akun@gmail.com
MAIL_FROM_NAME="Nama Toko"
MAIL_TIMEOUT=15
```

Catatan:
- Port `587` memakai `MAIL_SCHEME=smtp` (STARTTLS). Untuk port `465` gunakan `MAIL_SCHEME=smtps`.
- `MAIL_FROM_ADDRESS` sebaiknya sama dengan `MAIL_USERNAME`, atau alias yang sudah
  diverifikasi di Gmail (**Settings → Accounts → Send mail as**). Jika tidak, Gmail
  menolak pengiriman.
- Jangan commit App Password ke repository. Isi lewat dashboard Railway
  (Variables) atau secret manager.

## 2. SMS verifikasi nomor telepon

Pilih salah satu driver. Nomor Indonesia dinormalisasi otomatis ke format
`62xxxxxxxxxx` (contoh `0812-3456-7890` → `6281234567890`).

### Opsi A — Twilio

```dotenv
SMS_DRIVER=twilio
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_FROM=+1415xxxxxxx
```

`TWILIO_FROM` dapat berupa nomor Twilio atau Sender ID alfanumerik yang sudah
disetujui untuk Indonesia.

### Opsi B — Vonage (Nexmo)

```dotenv
SMS_DRIVER=vonage
VONAGE_API_KEY=xxxxxxxx
VONAGE_API_SECRET=xxxxxxxxxxxxxxxx
VONAGE_FROM=Instanweb
```

### Opsi C — Gateway HTTP generik

Gateway harus menerima `POST` JSON:

```json
{ "to": "6281234567890", "target": "6281234567890", "message": "Kode ...", "sender": "NamaToko" }
```

```dotenv
SMS_DRIVER=http
SMS_ENDPOINT=https://gateway.example.com/send
SMS_TOKEN=token-rahasia
SMS_SENDER=NamaToko
SMS_AUTH_HEADER=Authorization
SMS_AUTH_SCHEME=Bearer
SMS_TIMEOUT=10
```

Respons non-2xx dianggap gagal dan dicatat ke log (nomor disamarkan).

## 3. Uji dari server produksi

Jalankan lewat shell/one-off command di server (Railway CLI, SSH, atau console):

```bash
php artisan config:clear

php artisan verification:test-email admin@domain.com
php artisan verification:test-sms 08123456789
```

Perintah akan menampilkan error spesifik (SMTP auth, kredensial gateway, dsb.)
dan mencatat detail ke `storage/logs/laravel.log`.

Uji end-to-end di aplikasi:

1. Daftar akun baru atau buka **/verifikasi**.
2. Klik **Kirim kode** pada kartu email → cek inbox/spam.
3. Masukkan 6 digit → status email terverifikasi.
4. Simpan nomor telepon → klik **Kirim kode SMS** → masukkan kode.

## 4. Troubleshooting

| Gejala | Penyebab umum | Solusi |
| --- | --- | --- |
| `Kode gagal dikirim` + log `MAIL_MAILER masih "log"` | Email belum dikonfigurasi | Set `MAIL_MAILER=smtp` + kredensial Gmail |
| `Username and Password not accepted` | Memakai password akun | Buat & pakai App Password Google |
| `Connection timed out` | Port diblokir / host salah | Coba `587` + `smtp`, atau `465` + `smtps` |
| Email masuk spam | Domain pengirim Gmail pribadi | Gunakan alias terverifikasi; untuk volume besar pertimbangkan domain + SPF/DKIM |
| `SMS_DRIVER masih "log"` | SMS belum dikonfigurasi | Set `twilio`, `vonage`, atau `http` + kredensial |
| Twilio error 21608/21211 | Nomor tujuan/nomor pengirim belum valid | Pastikan nomor aktif dan `TWILIO_FROM` benar |
| Vonage `status` bukan `0` | Kredensial salah / kredit habis | Cek `VONAGE_API_KEY`, `VONAGE_API_SECRET`, saldo |
| Perubahan env tidak terbaca | Config ter-cache | `php artisan config:clear` atau redeploy (script deploy sudah `optimize:clear`) |

## 5. Login Google

Email dari Google hanya ditandai terverifikasi jika Google mengembalikan
`verified_email=true`. Jika tidak, pengguna tetap harus memverifikasi lewat kode
email. Nomor telepon **tidak** diambil dari Google dan tetap diverifikasi via SMS.

## 6. Keamanan

- Kode disimpan sebagai hash (`Hash::make`), bukan teks biasa.
- Kode kedaluwarsa 10 menit dan dibatasi kirim ulang 60 detik.
- Nomor telepon disamarkan di log.
- Jangan pernah menaruh App Password / token SMS di repository atau dokumentasi publik.
