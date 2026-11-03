# 41 Perintah Siap Tempel — Figma AI (First Draft)

Pendamping `uiux-brief-advance.md`. Semua perintah sudah disesuaikan dengan aturan proyek: single-store, harga jual saja, tanpa kupon/varian/wishlist, copy Indonesia.

Isi: 16 layar customer (mobile) + 7 versi desktop customer + 12 layar admin (desktop) + 6 versi mobile admin.

---

## Cara pakai

1. Buka `figma.com` → **New design file**
2. Tekan **Ctrl + K** (Windows) / **Cmd + K** (Mac) → cari **First Draft**
3. **Tidak ada pemilih Mobile/Desktop.** Device ditentukan oleh kalimat pertama prompt — dan setiap blok di bawah **sudah memuat kalimat itu di baris pertama**, jadi tinggal tempel apa adanya
4. Klik **Make it** → tunggu 10–30 detik
5. Cek lebar frame di panel kanan: **390 = mobile**, **1440 = desktop**. Sekarang kedua ukuran dipakai di customer **dan** admin
6. Rapikan sesuai catatan di bawah tiap blok

> Kalau versi Figma Anda **menampilkan** tombol **Library** di dalam kotak prompt, pilih **Website + Basic design** untuk semua layar desktop (admin dan customer versi desktop). Kalau tombol itu **tidak ada**, abaikan — kalimat pembuka yang sudah disisipkan ke setiap prompt menggantikan fungsinya.

### Kalau device-nya salah

Jangan tutup panel **Actions** dulu. Selama panel terbuka, masih ada tombol **Make changes**.

Hasilnya mobile padahal seharusnya desktop:

```text
Convert this design to a desktop website layout, 1440px wide, with a 240px
left sidebar and a wide data table. Keep the same content and colors.
```

Hasilnya desktop padahal seharusnya mobile:

```text
Convert this design to a mobile web layout at 390px wide, single column.
Replace the sidebar with a hamburger menu, turn tables into stacked cards,
and add a sticky bottom bar with the primary action.
```

Kalau panel sudah ditutup, tombol itu hilang. Jalan terakhir: klik frame → ubah **W** menjadi `1440` atau `390` di panel kanan → rapikan manual.

**Mulai dari C-03**, bukan C-01. Layar itu yang menentukan warna, tombol, dan ritme. Kalau C-03 sudah pas, layar lain tinggal mengikuti.

---

## Baris gaya global (tempel di akhir perintah bila hasilnya melenceng)

```text
Style: light theme, background #F8FAFC, white cards with 1px #E2E8F0 border
and 12px radius, headings #0F172A, body text #475569 at 16px, primary button
#C2410C with white 16px semibold text and 12px radius, Inter font, spacing in
multiples of 4, touch targets at least 44px. Indonesian language only.
```

### Menyamakan warna & font setelah desain jadi

Figma **tidak bisa** memakai design system kita otomatis. Lakukan selagi panel **Actions** masih terbuka:

1. Klik **Make changes**
2. **Color** → pilih warna kustom → `#C2410C` untuk tombol utama
3. **Border radius** → geser ke `12`
4. **Spacing** → naikkan sampai terasa lega
5. **Typography** → klik `title`, `body`, `label` → pilih **Inter**

Kalau panel sudah ditutup, semua ini harus diganti manual per elemen.

---

# BAGIAN 1 — CUSTOMER (Mobile)

> Semua prompt di bawah sudah diawali `Mobile web page at 390x844 viewport...` — jangan dihapus. Baris itulah yang membuat Figma memilih library mobile.

---

## C-01 · Beranda — P0

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web homepage for "InstanWeb", an Indonesian single-store shop.
1) Sticky header: logo, hamburger icon, cart icon with badge "2".
2) Hero banner 4:3 with dark overlay, headline "Belanja Produk UMKM Lokal",
   short subtext, orange button "Lihat Produk".
3) Section header "Produk Terbaru" on the left, text link "Lihat semua" right.
4) Two-column product grid, 8 cards. Each card: square photo, product name
   over 2 lines, stars "4.8 (24 ulasan)", price "Rp 149.000".
Light theme, white cards, 12px radius, Inter font.
```

**Rapikan setelah muncul:**
- Pastikan harga hanya satu (harga jual), tidak ada harga coret.
- Hapus elemen karangan seperti badge "Promo" atau "Diskon".
- Jarak antar kartu 16px, padding kartu 12px.

---

## C-02 · Katalog Produk — P0

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web product catalog page for "InstanWeb", Indonesian.
1) Page title "Produk" with muted count "24 produk".
2) Full-width search input with magnifier icon, placeholder "Cari produk…".
3) Row of two equal controls: outline button "Filter" with sliders icon,
   dropdown "Terbaru".
4) Active filter chips: "Elektronik ×", "Harga terendah ×", text link "Reset".
5) Two-column product grid with 12 cards: square photo, name over 2 lines,
   stars "4.8 (24)", price "Rp 149.000".
6) Pagination centered: "‹ Sebelumnya", "1", "2", "3", "Berikutnya ›".
Light theme, 12px radius, 16px gutter.
```

**Rapikan setelah muncul:**
- Tombol "Filter" jangan berwarna orange (bukan aksi utama).
- Pastikan chip filter bisa terlihat jelas sebagai elemen aktif.
- Hapus banner promosi jika muncul.

---

## C-03 · Detail Produk — P0 ⭐ mulai dari sini

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web product detail page for "InstanWeb", Indonesian.
1) Header: back arrow, page title "Detail Produk", cart icon with badge "2".
2) Breadcrumb "Beranda / Produk / Tas Kanvas".
3) Square product photo with 4 thumbnails underneath, first thumbnail active
   with orange border.
4) Product name "Tas Kanvas Premium" at 20px bold, stars "4.8 (24 ulasan)".
5) Price "Rp 149.000" at 24px bold, muted label "Stok tersisa 8".
6) Quantity stepper: minus button, number "1", plus button.
7) Two stacked buttons: outline "Tambah ke Keranjang", solid orange
   "Beli Sekarang".
8) Small note with return icon "Bisa dikembalikan dalam 7 hari".
9) Section "Deskripsi" with 3 lines of text and "Selengkapnya".
10) Section "Ulasan" with rating summary and 3 review items: avatar, name,
    stars, date "12 Nov 2026", comment text.
11) Sticky bottom bar with price and orange button "Beli Sekarang".
Light theme, white cards, 12px radius, Inter font.
```

**Rapikan setelah muncul:**
- Ukuran harga harus paling menonjol setelah nama produk.
- Tombol outline dan solid harus jelas berbeda bobot.
- Hapus elemen wishlist/favorit (tidak ada di sistem).
- Sticky bar jangan menutupi section terakhir — beri jarak bawah 88px.

---

## C-04 · Keranjang — P0

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web shopping cart page, Indonesian.
1) Header: back arrow, title "Keranjang", muted text "3 produk".
2) Three cart item rows in one card: 72px square photo, product name over
   2 lines, price "Rp 149.000", quantity stepper "− 1 +", text link "Hapus"
   in muted red.
3) Summary card: rows "Subtotal" / "Rp 447.000", "Ongkir" /
   "Dihitung saat checkout", divider, bold row "Total" / "Rp 447.000".
4) Sticky bottom bar: total on the left, solid orange button
   "Lanjut ke Checkout" on the right.
Light theme, cards with 1px #E2E8F0 border, 12px radius, 16px padding.
```

**Rapikan setelah muncul:**
- Ongkir **jangan** ditampilkan sebagai angka di halaman ini.
- Pastikan total = subtotal (belum ada ongkir), jangan dobel.
- Tombol hapus harus kecil dan tidak menonjol.

---

## C-05 · Checkout — P0

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web checkout page, Indonesian.
1) Header: back arrow, title "Checkout".
2) Step indicator "Keranjang — Pengiriman — Pembayaran" with step 2 active.
3) Card "Data Penerima": field "Nama penerima", phone field "Nomor telepon"
   with +62 prefix.
4) Card "Alamat Pengiriman": textarea "Alamat lengkap"; three dropdowns
   "Provinsi", "Kota/Kabupaten" (disabled with hint "Pilih provinsi terlebih
   dahulu"), "Kecamatan" (disabled); small field "Kode pos".
5) Card "Pengiriman": three selectable courier cards stacked —
   "JNE REG · Rp 18.000 · 2-3 hari" (selected, orange border, check icon),
   "J&T EZ · Rp 16.000 · 3-4 hari", "SiCepat REG · Rp 17.000 · 2-3 hari".
6) Card "Catatan": textarea "Catatan untuk penjual (opsional)" with counter
   "0/200" bottom right.
7) Sticky bottom bar: "Total Rp 187.000" with caption "Termasuk ongkir
   Rp 18.000", orange button "Konfirmasi Pesanan".
Light theme, 16px card padding, 12px radius.
```

**Rapikan setelah muncul:**
- Tidak boleh ada pilihan metode pembayaran (hanya Midtrans).
- Tidak boleh ada daftar alamat tersimpan.
- Pastikan ongkir muncul sekali saja, dan total sudah termasuk ongkir.
- Dropdown kota/kecamatan harus terlihat nonaktif, bukan kosong biasa.

---

## C-06 · Pembayaran — P0

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web payment page, Indonesian.
1) Header: back arrow, title "Pembayaran".
2) Card at top: label "Kode Pesanan" with value "INV-2026-0012", large bold
   "Total Rp 187.000", amber badge "Belum Dibayar".
3) Status panel: spinner with text "Menyiapkan pembayaran…" above a full-width
   solid orange button "Bayar Sekarang".
4) Card "Rincian Pesanan": two item rows with photo, name, qty "1×", price;
   then rows "Ongkir Rp 18.000" and bold "Total Rp 187.000".
5) Card "Alamat Pengiriman": recipient name, phone, full address, courier
   "JNE REG".
6) Small muted note "Pembayaran diproses oleh Midtrans".
Light theme, white cards, 12px radius.
```

**Rapikan setelah muncul:**
- Jangan ada daftar QRIS/VA/e-wallet — itu muncul di modal Midtrans, bukan di sini.
- Tombol bayar hanya satu, full width.
- Hapus countdown timer jika muncul (tidak ada di sistem).

---

## C-07 · Pesanan Berhasil — P0

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web order success page, Indonesian.
1) Centered block: green circle with check icon, heading "Pesanan Berhasil
   Dibuat", text "Kode pesanan INV-2026-0012", small outline button
   "Salin Kode".
2) Two buttons side by side: solid orange "Lihat Pesanan", outline
   "Belanja Lagi".
3) Card "Ringkasan Pesanan": rows Tanggal, Alamat, Status; then two item rows
   with photo, name, qty, price; bold total.
4) Card "Beri Ulasan": grey disabled state with text "Ulasan dapat dikirim
   setelah pesanan selesai".
Light theme, centered content max-width 480px, 12px radius.
```

**Rapikan setelah muncul:**
- Ikon sukses harus hijau, bukan orange.
- Form ulasan tampil nonaktif (bukan hilang).
- Hapus confetti/animasi berlebihan.

---

## C-08 · Riwayat Pesanan — P1

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web order history page, Indonesian.
1) Header: back arrow, title "Pesanan Saya".
2) Horizontally scrollable tabs: "Semua", "Belum Bayar", "Dikemas",
   "Dikirim", "Selesai" — "Semua" active with underline and bold.
3) Three order cards stacked, each with: top row order code "INV-2026-0012"
   and date "12 Nov 2026", status pill "Dikirim" in violet with truck icon,
   small green text "Bayar: Lunas"; middle row two product thumbnails plus
   "+1 produk lain"; bottom row "Total Rp 187.000" and link "Lihat Detail".
4) One card shows action buttons "Batalkan Pesanan" (outline red) and
   "Beri Ulasan" (solid orange).
Light theme, 12px radius, 16px padding.
```

**Rapikan setelah muncul:**
- Dua status (pesanan vs pembayaran) harus beda bentuk: pill vs teks label.
- Jangan pakai warna yang sama untuk keduanya.
- Hapus tombol "Lacak" bila muncul.

---

## C-09 · Detail Pesanan — P1

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web order detail page, Indonesian.
1) Header: back arrow, title "Detail Pesanan".
2) Card: "INV-2026-0012", date "12 Nov 2026", status pill "Dikirim".
3) Vertical 4-step timeline: "Dibuat" done with timestamp "12 Nov 10:20",
   "Dikemas" done "12 Nov 14:05", "Dikirim" active "13 Nov 09:00",
   "Selesai" pending with muted dot. Each step: circle dot, vertical
   connector, title, timestamp.
4) Card "Nomor Resi": "JP1234567890" in monospace with copy icon on right.
5) Card "Alamat Pengiriman": recipient, phone, address, "JNE REG", "2-3 hari".
6) Card "Rincian Pesanan": two item rows, "Ongkir Rp 18.000", bold total.
7) Outline danger button "Batalkan Pesanan" at the bottom.
Light theme, white cards, 12px radius.
```

**Rapikan setelah muncul:**
- Timeline hanya 4 langkah. "Dibatalkan" bukan langkah kelima.
- Status tiap langkah harus ada teks, bukan hanya warna.
- Resi hanya muncul di langkah "Dikirim".

---

## C-10 · Profil — P1

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web profile page, Indonesian.
1) Header: back arrow, title "Profil Saya".
2) Identity block centered: circular avatar 96px with small camera badge,
   name "Budi Santoso", muted email.
3) Card "Data Diri": fields "Nama", "Email", "Nomor telepon", textarea
   "Alamat". Labels above inputs.
4) Card "Ubah Kata Sandi": fields "Kata sandi baru" and "Ulangi kata sandi"
   with eye toggle icons, strength meter with 4 segments and text "Sedang".
5) Sticky bottom bar with full-width solid orange button "Simpan Perubahan".
Light theme, 12px radius, 44px input height.
```

**Rapikan setelah muncul:**
- Label harus di atas field, bukan placeholder di dalam field.
- Jangan tampilkan "Hapus akun" (tidak ada di sistem).
- Avatar preview tanpa tombol berlebihan.

---

## C-11 · Login — P0

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web login page, Indonesian.
1) Small logo centered at top, text link "Kembali" top left.
2) Heading "Masuk", subtitle "Kelola pesanan dan belanja lebih cepat."
3) White button with Google logo "Masuk dengan Google", full width, 1px
   #E2E8F0 border.
4) Divider line with centered small text "atau".
5) Fields "Email" and "Kata sandi" with eye toggle, labels above.
6) Row: checkbox "Ingat saya" on the left.
7) Full-width solid orange button "Masuk".
8) Footer text "Belum punya akun? Daftar" with teal link.
Centered card max-width 448px, light theme, 12px radius.
```

**Rapikan setelah muncul:**
- Jangan ada tombol Apple/Facebook/X.
- Jangan ada tautan "Lupa password" (belum tersedia).
- Tombol Google harus punya teks, bukan logo saja.

---

## C-12 · Registrasi — P0

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web registration page, Indonesian.
1) Small logo centered, text link "Kembali" top left.
2) Heading "Buat Akun", subtitle "Daftar untuk mempermudah checkout."
3) White button with Google logo "Daftar dengan Google".
4) Divider with centered text "atau".
5) Fields stacked: "Nama lengkap", "Email", "Kata sandi" with hint
   "Minimal 8 karakter" and eye toggle, "Ulangi kata sandi".
6) Password strength: 4 segments, two filled, text "Sedang" beside.
7) Full-width solid orange button "Daftar".
8) Footer "Sudah punya akun? Masuk" with teal link.
Centered max-width 448px, light theme, 12px radius.
```

**Rapikan setelah muncul:**
- Kekuatan password wajib ada teksnya, jangan warna saja.
- Jangan tambahkan field nomor telepon di sini (bukan bagian registrasi).

---

## C-13 · Artikel — P2

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web article list page, Indonesian.
1) Header: logo, hamburger, cart icon with badge.
2) Page header "Artikel" with muted subtitle "Tips dan cerita dari toko
   kami".
3) Three article cards stacked: 16:9 image with 12px radius, small category
   label "Tips" in uppercase muted, title over 2 lines, author row with
   24px avatar, name, and date "12 Nov 2026".
4) Pagination centered at the bottom.
Light theme, 16px gap between cards.
```

**Rapikan setelah muncul:**
- Jangan ada kolom sidebar atau kategori bertingkat.
- Thumbnail dan gambar utama jangan tampil bersamaan di kartu.

---

## C-14 · Detail Artikel — P2

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web article detail page, Indonesian.
1) Header: back arrow, share icon on the right.
2) Breadcrumb "Beranda / Artikel / Tips".
3) Category label "Tips" uppercase, title "5 Tips Memilih Tas Kanvas" at
   28px bold, author row with avatar, name, date "12 Nov 2026".
4) 16:9 hero image with 12px radius.
5) Article body: 16px text with line height 1.6, max 68 characters per line,
   one H2 subheading, one bulleted list, one pull quote with left border.
6) Sticky bottom share bar: WhatsApp, Facebook, X icons and text
   "Salin Tautan".
Light theme, generous whitespace, 24px section gaps.
```

**Rapikan setelah muncul:**
- Hapus tombol share selain WhatsApp, Facebook, X, salin tautan.
- Jangan tampilkan sidebar "artikel terkait".

---

## C-15 · Kontak — P2

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web contact page for a small Indonesian shop.
1) Header: logo, hamburger, cart icon with badge.
2) Store block: square logo 96px with 16px radius, store name "InstanWeb"
   at 24px bold, short description, address text with pin icon.
3) Card "Hubungi Kami": WhatsApp row with green icon and number, then a
   full-width green button "Chat WhatsApp".
4) Card "Ikuti Kami": rows with icon plus label — Instagram, TikTok, Facebook,
   each row at least 56px tall with chevron on the right.
5) Card "Marketplace": rows for Shopee and Tokopedia with chevron.
6) Simple footer with copyright.
Light theme, 12px radius, 44px touch targets.
```

**Rapikan setelah muncul:**
- Ikon sosial harus berpasangan dengan teks, bukan ikon saja.
- Hapus kanal yang tidak ada data (mis. kolom kosong).

---

## C-16 · Kirim Ulasan — P1

```text
Mobile web page at 390x844 viewport, responsive website, not a native app.
Mobile web review form page, Indonesian.
1) Header: back arrow, title "Beri Ulasan".
2) Product summary row in a card: 72px photo, name "Tas Kanvas Premium",
   price "Rp 149.000".
3) Section "Beri rating": five large star buttons in a row, hint text
   "Pilih 1 sampai 5 bintang".
4) Textarea "Ceritakan pengalaman Anda (opsional)" with counter "0/1000"
   bottom right.
5) Upload area: dashed bordered box "Unggah Foto (opsional)" with plus icon
   and small hint "Maksimal 2 MB".
6) Full-width solid orange button "Kirim Ulasan" at the bottom.
Light theme, max-width 768px, generous spacing.
```

**Rapikan setelah muncul:**
- Bintang harus besar dan mudah ditekan (minimal 44×44).
- Jangan ada field tambahan seperti "judul ulasan".
- Tombol kirim tampil nonaktif bila belum memilih bintang.

---

# BAGIAN 2 — CUSTOMER VERSI DESKTOP (P0)

> Customer membuka **satu situs yang sama** dari HP maupun laptop. 7 layar di bawah punya layout desktop yang benar-benar berbeda. Frame harus **W = 1440**.

## C-01 · Beranda — Desktop  pd

```text
Desktop web page at 1440x1024 viewport, browser window, not a mobile app.
Desktop homepage for "InstanWeb", an Indonesian single-store shop.
1) Sticky header 72px: logo left, menu "Beranda / Produk / Artikel / Kontak",
   inline search field 360px, text link "Masuk", cart icon with badge "2".
2) Hero banner 1280x384 with 16:9 image, dark overlay, left-aligned headline
   "Belanja Produk UMKM Lokal" at 48px, short subtext, orange button
   "Lihat Produk".
3) Section header row: "Produk Terbaru" left, link "Lihat semua" right.
4) Four-column product grid, 8 cards, 24px gap.
5) Footer with 4 columns: Toko, Jelajahi, Bantuan, Hubungi.
Container 1280px centered, 24px gutters, white cards, 12px radius.
```

**Rapikan:** pastikan grid benar-benar 4 kolom, header memuat pencarian inline, hero tidak melebihi 384 px tinggi.

---

## C-02 · Katalog Produk — Desktop pd

```text
Desktop web page at 1440x1024 viewport, browser window, not a mobile app.
Desktop product catalog page, Indonesian.
1) Page header "Produk" with muted count "24 produk" aligned right.
2) Toolbar in one row: search input 320px, dropdown "Semua Kategori",
   dropdown "Terbaru", text link "Reset".
3) Four-column product grid, 12 cards, 24px column and row gap.
4) Numbered pagination centered: "‹ 1 2 3 ›" with active page filled orange.
Container 1280px.
```

---

## C-03 · Detail Produk — Desktop pd

```text
Desktop web page at 1440x1024 viewport, browser window, not a mobile app.
Desktop product detail page, Indonesian. Two columns: left 8/12, right 4/12.
Left column: large square product image, thumbnail strip of 4 below it,
then breadcrumb above the image.
Right column sticky at top 24px: product name "Tas Kanvas Premium" at 32px
bold, stars "4.8 (24 ulasan)", price "Rp 149.000" at 28px bold, muted
"Stok tersisa 8", quantity stepper, full-width solid orange button
"Beli Sekarang", outline button "Tambah ke Keranjang", small returns note.
Below both columns: section "Deskripsi" limited to 68 characters per line,
then section "Ulasan" with a 240px rating summary on the left and the review
list on the right.
Container 1280px, white cards, 12px radius.
```

**Rapikan:** panel kanan harus sticky; jangan biarkan tombol beli pindah ke bawah deskripsi.

---

## C-04 · Keranjang — Desktop pd

```text
Desktop web page at 1440x1024 viewport, browser window, not a mobile app.
Desktop shopping cart page, Indonesian. Two columns: left 8/12, right 4/12.
Left: one white card containing horizontal cart rows. Each row: 96px square
image, product name, muted price, quantity stepper, subtotal on the right,
text link "Hapus". Rows separated by 1px dividers.
Right: sticky summary card "Ringkasan" with rows Subtotal, Ongkir
"Dihitung saat checkout", divider, bold Total, full-width orange button
"Lanjut ke Checkout".
Container 1280px, 12px radius.
```

---

## C-05 · Checkout — Desktop pd

```text
Desktop web page at 1440x1024 viewport, browser window, not a mobile app.
Desktop checkout page, Indonesian. Step indicator "Keranjang — Pengiriman —
Pembayaran" with step 2 active above the content.
Two columns: form 640px left, summary 384px sticky right.
Left cards stacked: "Data Penerima" (name, phone); "Alamat Pengiriman"
(textarea, then three dropdowns in one row: Provinsi, Kota/Kabupaten, Kecamatan,
then Kode pos); "Pengiriman" (three courier radio cards in one row, first
selected with orange border); "Catatan" (textarea with counter 0/200).
Right card: compact item list, Subtotal, Ongkir, bold Total, full-width orange
button "Konfirmasi Pesanan".
Container 1280px, white cards, 12px radius.
```

**Rapikan:** tiga dropdown wilayah harus sejajar dalam satu baris, bukan menumpuk.

---

## C-06 · Pembayaran — Desktop pd

```text
Desktop web page at 1440x1024 viewport, browser window, not a mobile app.
Desktop payment page, Indonesian. Content centered at 720px.
Card 1: label "Kode Pesanan" with value "INV-2026-0012", large bold
"Total Rp 187.000", amber badge "Belum Dibayar", status row with spinner and
text "Menyiapkan pembayaran…", full-width orange button "Bayar Sekarang".
Card 2 "Rincian Pesanan": compact items table, rows Ongkir and bold Total.
Card 3 "Alamat Pengiriman": two columns — left recipient and address, right
courier "JNE REG" and service "2-3 hari".
Container 1280px, white cards, 12px radius.
```

---

## C-07 · Pesanan Berhasil — Desktop pd

```text
Desktop web page at 1440x1024 viewport, browser window, not a mobile app.
Desktop order success page, Indonesian. Content centered at 720px.
Header block: green circle check icon, heading "Pesanan Berhasil Dibuat",
order code "INV-2026-0012" with outline button "Salin Kode".
Two cards side by side: left "Ringkasan Pesanan" with dates, address and item
rows; right "Beri Ulasan" in disabled state with text "Ulasan dapat dikirim
setelah pesanan selesai".
Bottom row: solid orange "Lihat Pesanan" and outline "Belanja Lagi".
Container 1280px, 12px radius.
```

---

## Versi Desktop — Konversi (C-08 … C-16)

Sembilan layar ini layout desktop-nya mirip mobile (konten terpusat), jadi tidak perlu prompt baru:

1. Duplikat frame mobile: `Ctrl + D` / `Cmd + D`
2. Buka **First Draft → Make changes**
3. Tempel:

```text
Convert this design to a desktop website layout at 1440px wide. Keep the same
content, copy and colors. Use a 1280px centered container with 24px gutters and
a sticky header with inline search.
```

Berlaku untuk: **C-08, C-09, C-10, C-11, C-12, C-13, C-14, C-15, C-16.**

---

# BAGIAN 3 — ADMIN (Desktop)

> Semua prompt di bawah sudah diawali `Desktop web application at 1440x1024 viewport...` — **jangan dihapus**. Baris itulah satu-satunya yang mencegah Figma membuat tampilan admin dalam bentuk aplikasi HP. Kalau hasilnya tetap mobile, pakai **Make changes** selagi panel Actions masih terbuka (lihat "Kalau device-nya salah" di atas).

> Admin memakai Filament. Hasil Figma ini **referensi visual**, bukan untuk dijiplak 1:1.

---

## A-00 · Login Admin — P1

```text
Desktop web application at 1440x1024 viewport, browser window with a left 240px sidebar, not a mobile app.
Desktop web admin login page, Indonesian, 1440 wide.
Light gray background #F8FAFC, one centered white card 420px with 16px radius
and subtle shadow.
Card content: store logo centered, heading "Masuk ke Panel Admin", subtitle
"Gunakan akun Google yang terdaftar.", then a white button with Google logo
"Masuk dengan Google" full width, then muted text link "Kembali ke toko".
Minimal, no email or password fields.
```

**Rapikan setelah muncul:**
- Tidak boleh ada field email/password.
- Hanya satu tombol aksi.

---

## A-01 · Dashboard — P0

```text
Desktop web application at 1440x1024 viewport, browser window with a left 240px sidebar, not a mobile app.
Desktop admin dashboard, Indonesian, 1440 wide.
Left sidebar 240px, dark navy #0B1120, logo at top, menu groups:
"Produk" (Kategori, Produk), "Manajemen Order" (Transaksi),
"Manajemen Konten" (Artikel), "Pengaturan".
Main area with #F8FAFC background:
1) Page title "Dashboard".
2) Row of three stat cards: "Total Produk 128", "Kategori 12",
   "Pesanan 342". Label small muted on top, big number below, small icon right.
3) Two chart cards side by side: bar chart "Pesanan 7 Hari Terakhir" and line
   chart "Tren Pesanan", each with period selector "7 hari".
4) Table "10 Pesanan Terbaru" with columns Kode, Customer, Status, Total,
   Tanggal, and an "Lihat semua" link in the header.
White cards, 12px radius, orange accents only on active sidebar item.
```

**Rapikan setelah muncul:**
- Hapus kartu "Pendapatan"/"Omzet" jika muncul (tidak ada datanya).
- Grafik cukup satu warna seri, jangan warna-warni.

---

## A-02 · Daftar Produk — P1

```text
Desktop web application at 1440x1024 viewport, browser window with a left 240px sidebar, not a mobile app.
Desktop admin product list, Indonesian, 1440 wide, left sidebar dark navy.
1) Page header "Produk" with solid orange button "Tambah Produk" on the right.
2) Filter bar: search input "Cari produk…", dropdown "Semua Kategori",
   dropdown "Semua Status", chip "Stok menipis".
3) Data table with sticky header. Columns: checkbox, 48px image, "Nama"
   (product name bold with muted slug below), "Kategori", "Harga Jual",
   "Harga Beli" in muted smaller text, "Stok" with badge "Menipis" when ≤ 5,
   "Status" badge Aktif/Nonaktif, "Tanggal", row action menu.
4) Eight rows with realistic Indonesian products: Tas Kanvas Premium,
   Sepatu Kulit Coklat, Kemeja Batik Modern, Topi Rajut, Dompet Kulit,
   Gelang Manik, Ikat Pinggang, Jaket Denim.
5) Footer: "Menampilkan 1–8 dari 128" left, pagination right.
Row height 56px, white card, 12px radius.
```

**Rapikan setelah muncul:**
- Kolom Harga Jual harus lebih tebal daripada Harga Beli.
- Harga beli diberi label jelas "Harga beli (internal)".
- Jangan campur kolom status produk dengan status stok.

---

## A-03 · Form Produk — P1

```text
Desktop web application at 1440x1024 viewport, browser window with a left 240px sidebar, not a mobile app.
Desktop admin product form, Indonesian, 1440 wide, left sidebar.
Two-column layout, main 2/3 and side 1/3.
Main column card "Informasi Produk": fields "Nama Produk", "Slug" with
readonly style and hint "Dibuat otomatis dari nama", "Kategori" dropdown,
"Deskripsi" textarea with toolbar.
Side column card "Harga & Stok": currency fields "Harga Beli (internal)" and
"Harga Jual" with Rp prefix, helper line "Margin: Rp 49.000", number field
"Stok", toggle "Status Aktif", image uploader "Gambar Utama" with dashed
dropzone and preview.
Sticky bottom bar: text link "Batal" left, buttons "Simpan & Buat Lagi"
outline and "Simpan" solid orange right.
Light theme, inputs 44px tall, 12px radius.
```

**Rapikan setelah muncul:**
- Margin tampil sebagai teks bantuan, bukan field terpisah.
- Jangan ada field varian/ukuran/warna.
- Sticky bar jangan menutupi field terakhir.

---

## A-04 · Detail Produk + Galeri — P1

```text
Desktop web application at 1440x1024 viewport, browser window with a left 240px sidebar, not a mobile app.
Desktop admin product detail page, Indonesian, 1440 wide, left sidebar.
1) Header: product name "Tas Kanvas Premium" at 24px bold, green badge
   "Aktif", outline orange button "Edit Produk" on the right.
2) Card "Detail Produk": two-column label-value grid with rows Kategori,
   Slug, Harga Jual, Harga Beli, Stok, Berat, Dibuat, Diubah. Labels muted
   14px, values 16px dark.
3) Card "Galeri Gambar": grid of four 120px square thumbnails with delete
   icon on hover, plus one dashed tile "Tambah Gambar".
4) Divider, then outline danger button "Hapus Produk" at the bottom left.
White cards, 12px radius.
```

**Rapikan setelah muncul:**
- Tandai satu gambar sebagai "Utama".
- Tombol hapus jangan sejajar dengan tombol edit.

---

## A-05 · Daftar Kategori — P1

```text
Desktop web application at 1440x1024 viewport, browser window with a left 240px sidebar, not a mobile app.
Desktop admin category list, Indonesian, 1440 wide, left sidebar.
1) Page header "Kategori" with orange button "Tambah Kategori".
2) Search field "Cari kategori…".
3) Table columns: checkbox, "Ikon" 32px square, "Nama", "Slug" muted,
   "Jumlah Produk", "Tanggal Dibuat", row action menu.
4) Six rows: Elektronik, Fashion, Makanan, Kerajinan, Kecantikan, Olahraga.
5) Pagination at the bottom right.
Row height 56px, white card, sticky header.
```

**Rapikan setelah muncul:**
- Jangan tampilkan struktur bertingkat (subkategori tidak ada).
- Ikon tampil sebagai gambar kecil, bukan emoji.

---

## A-06 · Form Kategori — P1

```text
Desktop web application at 1440x1024 viewport, browser window with a left 240px sidebar, not a mobile app.
Desktop admin category form, Indonesian, centered max-width 720px.
1) Header with back arrow and title "Tambah Kategori".
2) Card with fields: "Nama Kategori" with required mark, "Slug" readonly with
   hint "Dibuat otomatis dari nama", "Ikon" uploader 96px square dashed box
   with preview.
3) Footer row: text link "Batal" left, solid orange "Simpan" right.
Light theme, inputs 44px, 12px radius, white card.
```

**Rapikan setelah muncul:**
- Slug harus jelas tidak dapat diedit.
- Jangan tambahkan field deskripsi kateogri (tidak ada di data).

---

## A-07 · Daftar Transaksi — P0 pd

```text
Desktop web application at 1440x1024 viewport, browser window with a left 240px sidebar, not a mobile app.
Desktop admin transaction list, Indonesian, 1440 wide, left sidebar.
1) Page header "Transaksi" with summary chips on the right: "Menunggu 8",
   "Dikemas 5", "Dikirim 12".
2) Filter bar: search "Cari kode atau nama", date range field, dropdown
   "Semua Status Pesanan", dropdown "Semua Status Bayar".
3) Dense data table with sticky header. Columns: "Kode" with date below in
   muted text, "Customer", "Kota", "Status Pesanan" as colored pill with icon,
   "Status Bayar" as plain text label "Bayar: Lunas", "Kurir", "Ongkir",
   "Total", "Resi", action menu.
4) Eight rows. Rows with status "Menunggu" have a 3px amber left border.
5) Footer: result count left, pagination right.
Row height 56px, white card, 12px radius.
```

**Rapikan setelah muncul:**
- Status pesanan = pill berikon; status bayar = teks. Jangan disamakan.
- Resi kosong tampil sebagai "—", bukan "null" atau kosong polos.
- Hapus tombol ekspor bila muncul.

---

## A-08 · Form Transaksi — P0

```text
Desktop web application at 1440x1024 viewport, browser window with a left 240px sidebar, not a mobile app.
Desktop admin transaction edit page, Indonesian, 1440 wide, left sidebar.
Two columns.
Left column: card "Pesanan INV-2026-0012" with read-only rows: customer name,
phone, full address, province/city/district, postal code, notes, payment
method. Then card "Item Pesanan" table with columns Produk, Harga, Qty,
Subtotal.
Right column: card "Status & Pengiriman": vertical radio group of five
statuses with icons — "Menunggu", "Dikemas", "Dikirim" (selected),
"Selesai", "Dibatalkan". Below it an amber info box "Nomor resi akan dibuat
otomatis saat status Dikirim". Then dropdown "Kurir", dropdown "Layanan",
field "Kode Pos", readonly field "Nomor Resi" with value "JP1234567890".
Sticky bottom bar with "Simpan" solid orange right.
```

**Rapikan setelah muncul:**
- Status hanya boleh berupa satu pilihan (radio), bukan banyak toggle.
- Peringatan resi otomatis harus terlihat, bukan tersembunyi.
- Data read-only harus terlihat jelas tidak dapat diedit.

---

## A-09 · Daftar Artikel — P2

```text
Desktop web application at 1440x1024 viewport, browser window with a left 240px sidebar, not a mobile app.
Desktop admin article list, Indonesian, 1440 wide, left sidebar.
1) Page header "Artikel" with orange button "Tulis Artikel".
2) Table columns: checkbox, "Thumbnail" 64×36 rounded, "Judul" bold with
   muted slug below, "Kategori", "Penulis" with 24px avatar and name,
   "Tanggal", action menu.
3) Five rows with Indonesian titles about UMKM tips.
4) Pagination bottom right.
Row height 56px, white card, sticky header.
```

**Rapikan setelah muncul:**
- Thumbnail dan gambar utama jangan tampil sebagai dua kolom.
- Jangan tambahkan kolom status publikasi bila tidak ada datanya.

---

## A-10 · Form Artikel — P2

```text
Desktop web application at 1440x1024 viewport, browser window with a left 240px sidebar, not a mobile app.
Desktop admin article editor, Indonesian, 1440 wide, left sidebar.
Two columns: editor main 2/3, sidebar 1/3.
Main column: large title field "Judul Artikel", then a rich text editor with
toolbar (H2, bold, italic, bullet list, link, image) and body text about UMKM
tips in Indonesian.
Sidebar card "Publikasi": dropdown "Kategori", image uploader "Gambar Utama",
image uploader "Thumbnail", then buttons "Pratinjau" outline and "Simpan"
solid orange.
Sticky bottom bar.
Light theme, white cards, 12px radius.
```

**Rapikan setelah muncul:**
- Hapus field "Status publikasi" bila tidak ada di sistem.
- Editor harus terasa lega, bukan sempit.

---

## A-11 · Pengaturan Toko — P1

```text
Desktop web application at 1440x1024 viewport, browser window with a left 240px sidebar, not a mobile app.
Desktop admin settings page, Indonesian, 1440 wide, left sidebar.
Stacked cards, each with a numbered section header:
1) "Info Toko": fields "Nama Toko", "Alamat", textarea "Deskripsi", image
   uploader "Logo".
2) "Sosial Media": fields Instagram, TikTok, WhatsApp, Facebook with small
   icons inside the inputs.
3) "Marketplace": fields Shopee, Tokopedia.
4) "Payment Gateway": "Midtrans Client Key", "Midtrans Server Key" shown as
   masked dots with eye toggle, toggle "Mode Produksi", amber warning banner
   "Midtrans belum dikonfigurasi — pembayaran tidak akan berfungsi".
5) "Lokasi Asal Kirim": fields "Origin City ID" and "Origin District ID".
Sticky bottom bar with solid orange "Simpan Pengaturan".
Light theme, white cards, 12px radius, 44px inputs.
```

**Rapikan setelah muncul:**
- Server key wajib tampil tersamar (titik-titik), bukan teks biasa.
- Banner peringatan harus jelas terbaca, bukan abu-abu samar.
- Jangan tampilkan nilai rahasia penuh setelah disimpan.

---

# BAGIAN 4 — ADMIN (Mobile)

> Admin juga dipakai dari HP. Enam layar di bawah punya layout mobile yang benar-benar berbeda. Frame harus **W = 390**.

## A-01 · Dashboard — Mobile

```text
Mobile web admin panel at 390x844 viewport, responsive website. No sidebar.
1) Top bar: hamburger icon left, title "Dashboard", small avatar right.
2) Three stat cards stacked full width: "Total Produk 128", "Kategori 12",
   "Pesanan 342" — label small muted on top, number 28px bold.
3) Chart card "Pesanan 7 Hari Terakhir" with bar chart full width.
4) Chart card "Tren Pesanan" with line chart full width.
5) "10 Pesanan Terbaru" as stacked cards: code and date, customer name,
   status pill, total.
Light theme, white cards, 12px radius, 16px page padding.
```

---

## A-02 · Daftar Produk — Mobile

```text
Mobile web admin product list at 390x844 viewport, responsive website. No sidebar.
1) Top bar: hamburger, title "Produk", plus icon button on the right.
2) Full-width search input "Cari produk…".
3) Horizontally scrollable filter chips: "Semua", "Aktif", "Stok menipis".
4) Product cards stacked, each: 56px square image left, product name over
   2 lines, muted category, bold "Rp 149.000", stock badge "Menipis" in amber,
   three-dot menu on the right.
5) Floating orange action button "Tambah Produk" bottom right.
Light theme, white cards, 12px radius.
```

**Rapikan:** harga jual harus paling menonjol; harga beli tidak ditampilkan di kartu.

---

## A-03 · Form Produk — Mobile

```text
Mobile web admin product form at 390x844 viewport, responsive website. No sidebar.
1) Top bar: back arrow, title "Tambah Produk", text link "Simpan" right.
2) Single column fields in this order: "Nama Produk", "Kategori" dropdown,
   "Harga Jual" with Rp prefix, helper text "Margin: Rp 49.000", "Stok",
   toggle "Status Aktif", image uploader with dashed box, "Deskripsi" textarea.
3) Sticky bottom bar with full-width solid orange button "Simpan".
Light theme, inputs 44px tall, 12px radius.
```

---

## A-07 · Daftar Transaksi — Mobile

```text
Mobile web admin transaction list at 390x844 viewport, responsive website. No sidebar.
1) Top bar: hamburger, title "Transaksi", filter icon.
2) Horizontally scrollable chips: "Menunggu 8", "Dikemas 5", "Dikirim 12".
3) Order cards stacked. Each card top row: code "INV-2026-0012" with date
   below, then status pill "Dikirim" violet with truck icon, and plain text
   label "Bayar: Lunas" in green. Middle: customer name and city. Bottom:
   bold "Rp 187.000" and chevron icon.
4) Cards with status "Menunggu" have a 3px amber left border.
Light theme, white cards, 12px radius.
```

**Rapikan:** status pesanan (pill) dan status bayar (teks) jangan disamakan bentuknya.

---

## A-08 · Form Transaksi — Mobile

```text
Mobile web admin transaction detail at 390x844 viewport, responsive website. No sidebar.
1) Top bar: back arrow, title "INV-2026-0012", text link "Simpan".
2) Card "Status Pesanan": vertical radio list of five statuses with icons —
   "Menunggu", "Dikemas", "Dikirim" selected, "Selesai", "Dibatalkan".
3) Amber info box "Nomor resi akan dibuat otomatis saat status Dikirim".
4) Card "Pengiriman": dropdown Kurir, dropdown Layanan, field Kode Pos,
   readonly field "Nomor Resi" with value "JP1234567890".
5) Card "Item Pesanan": compact rows with product name, qty, subtotal.
6) Card "Alamat Pengiriman": read-only text block.
7) Sticky bottom bar with full-width solid orange button "Simpan".
Light theme.
```

---

## A-11 · Pengaturan Toko — Mobile

```text
Mobile web admin settings at 390x844 viewport, responsive website. No sidebar.
1) Top bar: back arrow, title "Pengaturan".
2) Accordion sections, first one expanded: "Info Toko", "Sosial Media",
   "Marketplace", "Payment Gateway", "Lokasi Asal Kirim".
3) Expanded "Info Toko": fields "Nama Toko", "Alamat", "Deskripsi" textarea,
   "Logo" uploader.
4) Collapsed "Payment Gateway" row shows amber badge "Belum dikonfigurasi".
5) Sticky bottom bar with full-width solid orange button "Simpan Pengaturan".
Light theme, 12px radius.
```

**Rapikan:** server key tetap tersamar bila section Payment Gateway dibuka.

---

## Admin Mobile — Konversi (A-00, A-04, A-05, A-06, A-09, A-10)

Enam layar ini layout mobile-nya sederhana. Gunakan konversi:

1. Duplikat frame desktop: `Ctrl + D` / `Cmd + D`
2. **First Draft → Make changes**
3. Tempel:

```text
Convert this design to a mobile web layout at 390px wide, single column.
Replace the sidebar with a hamburger menu in the top bar, turn tables into
stacked cards showing only the most important fields, and add a sticky bottom
bar with the primary action.
```

Berlaku untuk: **A-00, A-04, A-05, A-06, A-09, A-10.**

---

# Ringkasan urutan pengerjaan

| Urutan | Layar | Versi yang dibuat | Alasan |
|---|---|---|---|
| 1 | C-03 Detail Produk | Mobile + Desktop | Menentukan warna, komponen, ritme |
| 2 | C-01 Beranda | Mobile + Desktop | Layar paling sering dilihat |
| 3 | C-02 Katalog | Mobile + Desktop | Melanjutkan pola kartu produk |
| 4 | C-04 → C-07 | Mobile + Desktop | Alur transaksi inti |
| 5 | C-11, C-12 | Mobile + konversi desktop | Autentikasi |
| 6 | C-08, C-09, C-10, C-16 | Mobile + konversi desktop | Retensi & ulasan |
| 7 | A-01, A-07, A-08 | Desktop + Mobile | Admin inti |
| 8 | A-02, A-03, A-11 | Desktop + Mobile | Admin operasional |
| 9 | A-00, A-04, A-05, A-06, A-09, A-10 | Desktop + konversi mobile | Sekunder |
| 10 | C-13, C-14, C-15 | Mobile + konversi desktop | Konten |

### Jumlah frame akhir

| Kelompok | Frame |
|---|---|
| Customer mobile (C-01…C-16) | 16 |
| Customer desktop — prompt khusus (C-01…C-07) | 7 |
| Customer desktop — konversi (C-08…C-16) | 9 |
| Admin desktop (A-00…A-11) | 12 |
| Admin mobile — prompt khusus (A-01, A-02, A-03, A-07, A-08, A-11) | 6 |
| Admin mobile — konversi (A-00, A-04, A-05, A-06, A-09, A-10) | 6 |
| **Total** | **56** |

> 56 frame terlihat banyak, tapi 15 di antaranya hasil konversi (2 menit per layar), bukan prompt baru. Prompt yang benar-benar ditulis: **41**.

---

# Kalau hasil Figma AI melenceng

| Masalah | Perbaikan |
|---|---|
| **Keluar mobile padahal butuh desktop** | Jangan tutup panel Actions. Klik **Make changes**, lalu: `Convert this design to a desktop website layout, 1440px wide, with a 240px left sidebar and a wide data table.` |
| **Keluar desktop padahal butuh mobile** | Sama: **Make changes** → `Convert this design to a mobile web layout, 390px wide, single column.` |
| **Frame sudah benar tapi isinya sempit/melebar** | Klik frame → panel kanan → set **W** = 390 (mobile) atau 1440 (desktop) → periksa padding |
| Teks jadi bahasa Inggris | Tambahkan di akhir: `All interface text must be Indonesian as written above.` |
| Warna tidak sesuai | Tempel baris gaya global di bagian atas dokumen ini |
| Terlalu banyak elemen | Tambahkan: `Remove all promotional banners, discount badges and upsell sections.` |
| Muncul wishlist/kupon/varian | Tambahkan: `Do not include wishlist, coupons, discount codes or product variants.` |
| Layout terlalu padat di mobile | Tambahkan: `Increase vertical spacing between sections to 24px and card padding to 16px.` |
| Tabel admin berantakan | Pecah jadi dua prompt: header + filter dulu, lalu tabel saja |
| **Konversi desktop masih 2 kolom** | `Make the product grid 4 columns wide with 24px gap inside a 1280px container.` |
| **Admin mobile masih ada sidebar** | `Remove the sidebar. Use a hamburger icon in the top bar that opens a drawer menu.` |
| **Tabel admin tetap tabel di mobile** | `Turn the table into stacked cards showing only: name, price, status and total.` |
