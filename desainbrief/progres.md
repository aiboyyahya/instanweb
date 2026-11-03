# PROGRES DESAIN — InstanWeb

| Aspek | Nilai |
|---|---|
| Tanggal | 1 November 2026 |
| Sumber desain | `uiux-brief-advance.md` v2.0 |
| Sumber perintah | `figma-prompts.md` (41 prompt) |
| Alat | Figma AI — First Draft |
| Target implementasi | Laravel 12 + Blade + Tailwind CSS 4 + Filament |
| Status | Baru dimulai |

**Legenda status**

| Tanda | Arti |
|---|---|
| ⬜ | belum dikerjakan |
| 🟨 | sedang dikerjakan |
| ✅ | selesai & sudah dirapikan |
| ⛔ | terblokir / menunggu keputusan |

---

## 1. Ringkasan pekerjaan

| Kelompok | Gen (prompt khusus) | Konversi | Jumlah frame |
|---|---|---|---|
| Customer mobile | 16 | 0 | 16 |
| Customer desktop | 7 | 9 | 16 |
| Admin desktop | 12 | 0 | 12 |
| Admin mobile | 6 | 6 | 12 |
| **Total** | **41** | **15** | **56** |

**Estimasi waktu**

| Aktivitas | Volume | Per item | Total |
|---|---|---|---|
| Generate + rapikan + restyle | 41 | 4 menit | ± 2 jam 45 menit |
| Konversi | 15 | 2 menit | ± 30 menit |
| QA visual & konsistensi | 1 | — | ± 60 menit |
| **Total** | | | **± 4 jam 15 menit** |

> Angka ini realistis bila restyle gaya dilakukan **langsung di tiap layar** selagi panel Actions masih terbuka. Kalau panel ditutup dulu dan gaya diperbaiki di akhir, waktunya bisa dua kali lipat.

---

## 2. Urutan langkah

### FASE 0 — Persiapan (±15 menit)

- [ ] ⬜ Buat file Figma baru: **InstanWeb — Design v2**
- [ ] ⬜ Buat 2 halaman: `Customer` dan `Admin`
- [ ] ⬜ Buat 2 kolom bantu di setiap halaman: `Mobile 390` dan `Desktop 1440`
- [ ] ⬜ Buka `figma-prompts.md` di samping Figma (split screen)
- [ ] ⬜ Baca ulang "Cara pakai" di bagian atas `figma-prompts.md`

**Selesai bila:** file Figma punya 2 halaman dan 4 area kolom, prompt sudah terbuka.

---

### FASE 1 — Pilot C-03 (±20 menit) ⭐ jangan dilewati

Layar ini menetapkan warna, tombol, radius, dan ritme. Semua layar lain mengikuti hasilnya.

- [ ] ⬜ Generate **C-03 mobile** (BAGIAN 1)
- [ ] ⬜ Periksa lebar frame = **390**
- [ ] ⬜ Restyle selagi panel Actions terbuka (§4 di bawah)
- [ ] ⬜ Generate **C-03 desktop** (BAGIAN 2)
- [ ] ⬜ Periksa lebar frame = **1440**, panel beli sticky di kanan
- [ ] ⬜ Restyle dengan kontrol yang sama
- [ ] ⬜ Catat keputusan final di §8 (log)

**Selesai bila:** dua frame C-03 punya warna, radius, dan tipografi identik, dan kamu bisa menyebut 5 nilai token tanpa membuka dokumen (CTA `#C2410C`, bg `#F8FAFC`, border `#E2E8F0`, radius 12, font Inter).

---

### FASE 2 — Customer mobile (15 layar, ±60 menit)

Urutan: C-01 → C-02 → C-04 → C-05 → C-06 → C-07 → C-11 → C-12 → C-08 → C-09 → C-10 → C-16 → C-13 → C-14 → C-15

- [ ] ⬜ C-01 Beranda
- [ ] ⬜ C-02 Katalog
- [ ] ⬜ C-04 Keranjang
- [ ] ⬜ C-05 Checkout
- [ ] ⬜ C-06 Pembayaran
- [ ] ⬜ C-07 Pesanan Berhasil
- [ ] ⬜ C-11 Login
- [ ] ⬜ C-12 Registrasi
- [ ] ⬜ C-08 Riwayat Pesanan
- [ ] ⬜ C-09 Detail Pesanan
- [ ] ⬜ C-10 Profil
- [ ] ⬜ C-16 Kirim Ulasan
- [ ] ⬜ C-13 Artikel
- [ ] ⬜ C-14 Detail Artikel
- [ ] ⬜ C-15 Kontak

**Selesai bila:** 16 frame mobile ada, semua lebar 390, header & footer identik, tidak ada warna di luar token.

---

### FASE 3 — Customer desktop (15 layar tersisa, ±55 menit)

**Gen (7):** C-01, C-02, C-03 (sudah di Fase 1), C-04, C-05, C-06, C-07
**Konversi (9):** C-08, C-09, C-10, C-11, C-12, C-13, C-14, C-15, C-16

- [ ] ⬜ C-01 desktop — gen
- [ ] ⬜ C-02 desktop — gen
- [ ] ⬜ C-04 desktop — gen
- [ ] ⬜ C-05 desktop — gen
- [ ] ⬜ C-06 desktop — gen
- [ ] ⬜ C-07 desktop — gen
- [ ] ⬜ C-08 → C-16 desktop — konversi (9×)

**Selesai bila:** grid desktop 4 kolom, container 1280, header dengan pencarian inline, panel ringkasan sticky pada C-03/C-04/C-05.

---

### FASE 4 — Admin desktop (12 layar, ±45 menit)

Urutan: A-01 → A-07 → A-08 → A-02 → A-03 → A-11 → A-00 → A-04 → A-05 → A-06 → A-09 → A-10

- [ ] ⬜ A-01 Dashboard
- [ ] ⬜ A-07 Daftar Transaksi
- [ ] ⬜ A-08 Form Transaksi
- [ ] ⬜ A-02 Daftar Produk
- [ ] ⬜ A-03 Form Produk
- [ ] ⬜ A-11 Pengaturan Toko
- [ ] ⬜ A-00 Login Admin
- [ ] ⬜ A-04 Detail Produk + Galeri
- [ ] ⬜ A-05 Daftar Kategori
- [ ] ⬜ A-06 Form Kategori
- [ ] ⬜ A-09 Daftar Artikel
- [ ] ⬜ A-10 Form Artikel

**Selesai bila:** sidebar 240 px konsisten, tabel punya filter bar, dua status (pesanan vs pembayaran) beda bentuk.

---

### FASE 5 — Admin mobile (12 layar, ±35 menit)

**Gen (6):** A-01, A-02, A-03, A-07, A-08, A-11
**Konversi (6):** A-00, A-04, A-05, A-06, A-09, A-10

- [ ] ⬜ A-01 mobile — gen
- [ ] ⬜ A-02 mobile — gen
- [ ] ⬜ A-03 mobile — gen
- [ ] ⬜ A-07 mobile — gen
- [ ] ⬜ A-08 mobile — gen
- [ ] ⬜ A-11 mobile — gen
- [ ] ⬜ A-00, A-04, A-05, A-06, A-09, A-10 mobile — konversi (6×)

**Selesai bila:** tidak ada sidebar di 390, tabel jadi kartu, tombol simpan sticky di bawah.

---

### FASE 6 — QA visual (±60 menit)

- [ ] ⬜ Semua 56 frame bisa dibuka tanpa error
- [ ] ⬜ Warna hanya dari token (cek dengan color picker 5 sampel acak)
- [ ] ⬜ Radius hanya 6/8/12/16/24/999
- [ ] ⬜ Spasi kelipatan 4
- [ ] ⬜ Satu tombol primer per layar
- [ ] ⬜ Badge status identik di semua layar
- [ ] ⬜ Tidak ada teks Inggris tersisa
- [ ] ⬜ Tidak ada wishlist/kupon/varian/COD/chat
- [ ] ⬜ Harga customer = harga jual saja
- [ ] ⬜ Jalankan §8 checklist

---

### FASE 7 — Handoff ke Tailwind (±90 menit, teknis)

Bukan pekerjaan Figma. Lihat §5 dan §6.

- [ ] ⬜ Terapkan `@theme` di `resources/css/app.css`
- [ ] ⬜ Buat komponen Blade dasar (tombol, kartu, badge, input)
- [ ] ⬜ Terapkan tema Filament
- [ ] ⬜ Konversi layar P0 lebih dulu

---

## 3. Peta layar: gen atau konversi

### 3.1 Customer

| ID | Layar | Mobile 390 | Desktop 1440 | Prioritas |
|---|---|---|---|---|
| C-01 | Beranda | Gen · B1 | Gen · B2 | P0 |
| C-02 | Katalog Produk | Gen · B1 | Gen · B2 | P0 |
| C-03 | Detail Produk | Gen · B1 | Gen · B2 | P0 ⭐ |
| C-04 | Keranjang | Gen · B1 | Gen · B2 | P0 |
| C-05 | Checkout | Gen · B1 | Gen · B2 | P0 |
| C-06 | Pembayaran | Gen · B1 | Gen · B2 | P0 |
| C-07 | Pesanan Berhasil | Gen · B1 | Gen · B2 | P0 |
| C-08 | Riwayat Pesanan | Gen · B1 | **Konversi** | P1 |
| C-09 | Detail Pesanan | Gen · B1 | **Konversi** | P1 |
| C-10 | Profil | Gen · B1 | **Konversi** | P1 |
| C-11 | Login | Gen · B1 | **Konversi** | P0 |
| C-12 | Registrasi | Gen · B1 | **Konversi** | P0 |
| C-13 | Artikel | Gen · B1 | **Konversi** | P2 |
| C-14 | Detail Artikel | Gen · B1 | **Konversi** | P2 |
| C-15 | Kontak | Gen · B1 | **Konversi** | P2 |
| C-16 | Kirim Ulasan | Gen · B1 | **Konversi** | P1 |

B1 = `figma-prompts.md` BAGIAN 1 · B2 = BAGIAN 2

### 3.2 Admin

| ID | Layar | Desktop 1440 | Mobile 390 | Prioritas |
|---|---|---|---|---|
| A-00 | Login Admin | Gen · B3 | **Konversi** | P1 |
| A-01 | Dashboard | Gen · B3 | Gen · B4 | P0 |
| A-02 | Daftar Produk | Gen · B3 | Gen · B4 | P1 |
| A-03 | Form Produk | Gen · B3 | Gen · B4 | P1 |
| A-04 | Detail Produk + Galeri | Gen · B3 | **Konversi** | P1 |
| A-05 | Daftar Kategori | Gen · B3 | **Konversi** | P1 |
| A-06 | Form Kategori | Gen · B3 | **Konversi** | P1 |
| A-07 | Daftar Transaksi | Gen · B3 | Gen · B4 | P0 |
| A-08 | Form Transaksi | Gen · B3 | Gen · B4 | P0 |
| A-09 | Daftar Artikel | Gen · B3 | **Konversi** | P2 |
| A-10 | Form Artikel | Gen · B3 | **Konversi** | P2 |
| A-11 | Pengaturan Toko | Gen · B3 | Gen · B4 | P1 |

B3 = BAGIAN 3 · B4 = BAGIAN 4

### 3.3 Perintah konversi

**Desktop customer** (duplikat frame mobile → Make changes):

```text
Convert this design to a desktop website layout at 1440px wide. Keep the same
content, copy and colors. Use a 1280px centered container with 24px gutters and
a sticky header with inline search.
```

**Mobile admin** (duplikat frame desktop → Make changes):

```text
Convert this design to a mobile web layout at 390px wide, single column.
Replace the sidebar with a hamburger menu in the top bar, turn tables into
stacked cards showing only the most important fields, and add a sticky bottom
bar with the primary action.
```

---

## 4. Checklist restyle (lakukan SEBELUM menutup panel Actions)

Panel Actions ditutup = kontrol warna, radius, spacing, dan tipografi hilang. Urutannya:

1. ⬜ **Color** → CTA `#C2410C`, latar halaman `#F8FAFC`, border `#E2E8F0`
2. ⬜ **Border radius** → `12` (kartu & tombol), `8` (input), `999` (pill)
3. ⬜ **Spacing** → naikkan sampai terasa lega; target padding kartu 16 mobile / 24 desktop
4. ⬜ **Typography** → Inter; judul halaman 26/36, judul section 20/28, body 16
5. ⬜ **Cek 3 hal:** tombol primer hanya satu, badge status konsisten, harga memakai angka rata
6. ⬜ Baru tutup panel

Kalau terlanjur ditutup: klik frame → set `W` manual → ganti warna per elemen. Jauh lebih lambat.

---

## 5. Yang perlu diubah/ditambah agar siap Tailwind CSS

Ini daftar celah antara **desain** dan **kode**. Kerjakan dari atas.

| # | Kekurangan saat ini | Akibat bila dibiarkan | Tindakan | Prioritas |
|---|---|---|---|---|
| 1 | Token di §4.9 baru berupa JSON, belum ada padanan `@theme` Tailwind v4 | Developer menebak nama variabel → muncul hex liar | Tempel blok `@theme` di §6 ke `resources/css/app.css` | **P0** |
| 2 | Nama token dokumen (`text-primary`, `surface`) berbeda dari nama utility (`text-ink-900`) | Salah pakai warna, kontras turun | Pakai tabel pemetaan §6.3 sebagai acuan tunggal | **P0** |
| 3 | Komponen §5 baru daftar, belum ada nama komponen Blade | Tiap halaman bikin markup sendiri → tidak konsisten | Buat 12 komponen Blade inti (§7) | **P0** |
| 4 | State (loading/kosong/error) belum jadi partial | Tiap layar menulis state sendiri, copy berbeda | Buat `<x-state.empty>`, `<x-state.error>`, `<x-skeleton.*>` | **P0** |
| 5 | Tema Filament belum dipetakan ke token | Panel admin tetap ungu default | Buat `resources/css/filament/admin/theme.css` + `->viteTheme()` | **P0** |
| 6 | Font Inter belum ditentukan cara memuatnya | FOIT/CLS, atau font tidak muncul | Self-host `@fontsource/inter` atau `<link>` preconnect | P1 |
| 7 | Set ikon belum dipilih | Campur Lucide + emoji + SVG manual | Tetapkan Lucide; pasang `blade-ui-kit/blade-icons` atau inline SVG | P1 |
| 8 | Styling form belum diputuskan | Input terlihat beda antara Blade dan Filament | Pakai `@tailwindcss/forms` **atau** base style sendiri — pilih satu | P1 |
| 9 | Belum ada aturan eksplisit: dark mode **tidak** dipakai | Browser auto-dark merusak kontras | Set `color-scheme: light` di `html` | P1 |
| 10 | Tabel admin → kartu belum ditentukan caranya | Rawan butuh JS tambahan | Dua markup: `hidden md:block` (tabel) + `md:hidden` (kartu) | P1 |
| 11 | Sticky bar belum memakai safe-area | Tombol tertutup gesture bar iOS | `pb-[max(1rem,env(safe-area-inset-bottom))]` | P1 |
| 12 | `prefers-reduced-motion` baru disebut, belum ada CSS-nya | Animasi tetap jalan bagi yang sensitif | Tambah blok di `@layer base` | P2 |
| 13 | Belum ada aturan `tabular-nums` di komponen harga | Kolom harga tidak rata | Terapkan `tabular-nums` di semua elemen uang | P2 |
| 14 | Format Rupiah belum ada helper | Format berbeda antar halaman | Helper `format_rupiah()` di sisi aplikasi (bukan CSS) | P2 |

> Catatan: nomor 5, 8, dan 10 bergantung pada versi Filament/Tailwind yang benar-benar terpasang. **Perlu dicek di repo** sebelum menetapkan sintaks final — jangan mengarang API.

### 5.1 Keputusan desain yang harus dikunci agar ramah Tailwind

Tujuh hal ini murah di Figma, mahal di kode kalau salah:

1. **Grid produk selalu 2/3/4** → `grid-cols-2 md:grid-cols-3 xl:grid-cols-4`. Jangan pernah 5 kolom.
2. **Timeline memakai flex, bukan posisi absolut** → aman berubah horizontal/vertikal.
3. **Panel ringkasan memakai `sticky top-6`**, bukan `fixed` → tidak perlu hitung offset JS.
4. **Tidak ada lebar tetap** kecuali sidebar admin (240 px) dan sidebar ringkasan (384 px).
5. **Semua jarak kelipatan 4** → langsung jadi `p-4`, `gap-6`, `mt-10`.
6. **Tidak ada warna transparan bertumpuk** di atas foto; pakai overlay solid `rgba(11,17,32,.45)`.
7. **Badge status = satu komponen dengan 5 nilai**, bukan 5 desain berbeda.

---

## 6. Fondasi Tailwind CSS 4

### 6.1 `resources/css/app.css`

```css
@import "tailwindcss";

@theme {
  /* ---------- Tipografi ---------- */
  --font-sans: "Inter", ui-sans-serif, system-ui, -apple-system, "Segoe UI",
    Roboto, sans-serif;

  --text-display: clamp(2rem, 1.4rem + 3vw, 3rem);
  --text-display--line-height: 1.05;
  --text-display--letter-spacing: -0.02em;
  --text-display--font-weight: 800;

  --text-h1: clamp(1.625rem, 1.2rem + 2vw, 2.25rem);
  --text-h1--line-height: 1.15;
  --text-h1--letter-spacing: -0.015em;
  --text-h1--font-weight: 700;

  --text-h2: clamp(1.25rem, 1.05rem + 1vw, 1.75rem);
  --text-h2--line-height: 1.2;
  --text-h2--letter-spacing: -0.01em;
  --text-h2--font-weight: 700;

  --text-h3: clamp(1.0625rem, 1rem + 0.4vw, 1.25rem);
  --text-h3--line-height: 1.3;
  --text-h3--font-weight: 600;

  --text-body-lg: clamp(1rem, 0.95rem + 0.3vw, 1.125rem);
  --text-body-lg--line-height: 1.6;

  --text-body: 1rem;
  --text-body--line-height: 1.6;

  --text-sm: 0.875rem;
  --text-sm--line-height: 1.5;

  --text-caption: 0.75rem;
  --text-caption--line-height: 1.4;
  --text-caption--letter-spacing: 0.02em;

  --text-eyebrow: 0.75rem;
  --text-eyebrow--line-height: 1.2;
  --text-eyebrow--letter-spacing: 0.08em;
  --text-eyebrow--font-weight: 700;

  --text-price-lg: clamp(1.5rem, 1.3rem + 0.8vw, 1.75rem);
  --text-price-lg--line-height: 1.1;
  --text-price-lg--font-weight: 700;

  --text-price: clamp(1.0625rem, 1rem + 0.3vw, 1.125rem);
  --text-price--line-height: 1.2;
  --text-price--font-weight: 700;

  --text-price-sm: 0.875rem;
  --text-price-sm--line-height: 1.3;
  --text-price-sm--font-weight: 600;

  /* ---------- Warna: ink (navy) ---------- */
  --color-ink-950: #060a14;
  --color-ink-900: #0b1120;
  --color-ink-800: #111c2f;
  --color-ink-700: #1e2a44;
  --color-ink-600: #334155;
  --color-ink-500: #475569;
  --color-ink-400: #64748b;
  --color-ink-300: #94a3b8;
  --color-ink-200: #cbd5e1;
  --color-ink-100: #e2e8f0;
  --color-ink-50: #f8fafc;

  /* ---------- Warna: aksen ---------- */
  --color-sky-50: #f0f9ff;
  --color-sky-400: #38bdf8;
  --color-sky-500: #0284c7;
  --color-sky-600: #0369a1;
  --color-sky-700: #075985;

  --color-brand-50: #fff7ed;
  --color-brand-400: #f59e0b;
  --color-brand-500: #ea741c;
  --color-brand-600: #c2410c;
  --color-brand-700: #9a3412;

  /* ---------- Warna: semantik ---------- */
  --color-success: #15803d;
  --color-success-soft: #f0fdf4;
  --color-warning: #b45309;
  --color-warning-soft: #fffbeb;
  --color-danger: #b91c1c;
  --color-danger-soft: #fef2f2;
  --color-info: #0369a1;
  --color-info-soft: #f0f9ff;
  --color-violet: #6d28d9;
  --color-violet-soft: #f5f3ff;
  --color-neutral: #475569;
  --color-neutral-soft: #f8fafc;

  /* ---------- Warna: peran ---------- */
  --color-surface: #ffffff;
  --color-surface-muted: #f8fafc;
  --color-surface-inverse: #0b1120;
  --color-line: #e2e8f0;
  --color-line-strong: #cbd5e1;
  --color-link: #0369a1;
  --color-focus: #0284c7;
  --color-overlay: rgb(11 17 32 / 0.48);

  /* ---------- Radius ---------- */
  --radius-xs: 6px;
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;

  /* ---------- Elevasi ---------- */
  --shadow-xs: 0 1px 2px rgb(11 17 32 / 0.05);
  --shadow-sm: 0 1px 3px rgb(11 17 32 / 0.08), 0 1px 2px rgb(11 17 32 / 0.04);
  --shadow-md: 0 4px 12px rgb(11 17 32 / 0.08);
  --shadow-lg: 0 12px 32px rgb(11 17 32 / 0.12);
  --shadow-focus: 0 0 0 3px rgb(2 132 199 / 0.28);

  /* ---------- Kontainer ---------- */
  --container-page: 80rem;     /* 1280px */
  --container-content: 48rem;  /* 768px  */
  --container-sidebar: 24rem;  /* 384px  */

  /* ---------- Motion ---------- */
  --ease-standard: cubic-bezier(0.2, 0.8, 0.2, 1);
}

@layer base {
  html {
    color-scheme: light; /* cegah auto-dark browser */
    -webkit-text-size-adjust: 100%;
  }

  body {
    @apply bg-surface-muted font-sans text-ink-900 antialiased;
  }

  :focus-visible {
    outline: 2px solid var(--color-focus);
    outline-offset: 2px;
  }

  /* Semua elemen uang wajib rata */
  [data-money] {
    font-variant-numeric: tabular-nums;
  }

  @media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
      animation-duration: 0.01ms !important;
      transition-duration: 0.01ms !important;
      scroll-behavior: auto !important;
    }
  }
}
```

**Catatan penting**

- Breakpoint Tailwind default **sudah cocok** dengan §4.4 (sm 640, md 768, lg 1024, xl 1280, 2xl 1536). Tidak perlu diubah.
- Spacing default Tailwind v4 = kelipatan 4 px, sudah cocok dengan §4.3. `p-4` = 16 px, `gap-6` = 24 px.
- Override `--radius-*` mengubah arti `rounded-md` di seluruh proyek — lakukan sekali di awal, bukan di tengah.
- `--color-violet` menambah utility baru, tidak menimpa skala `violet-500` bawaan.

### 6.2 Utility yang sering dipakai

| Kebutuhan | Kelas |
|---|---|
| Kontainer halaman | `mx-auto w-full max-w-page px-4 sm:px-6 lg:px-8 xl:px-10` |
| Kartu | `rounded-lg border border-line bg-surface p-4 md:p-5 lg:p-6` |
| Tombol primer | `inline-flex h-12 items-center justify-center rounded-md bg-brand-600 px-6 text-base font-semibold text-white hover:bg-brand-700` |
| Tombol sekunder | `inline-flex h-12 items-center justify-center rounded-md border border-line-strong bg-surface px-6 text-base font-semibold text-ink-900` |
| Input | `h-12 w-full rounded-sm border border-line-strong bg-surface px-4 text-base text-ink-900 placeholder:text-ink-400` |
| Badge status | `inline-flex items-center gap-1 rounded-full px-3 py-1 text-caption font-semibold` |
| Grid produk | `grid grid-cols-2 gap-4 md:grid-cols-3 md:gap-5 xl:grid-cols-4 xl:gap-6` |
| Judul halaman | `text-h1 text-ink-900` |
| Metadata | `text-sm text-ink-400` |
| Harga | `text-price text-ink-900 tabular-nums` |
| Sticky bar mobile | `sticky bottom-0 z-40 border-t border-line bg-surface p-4 pb-[max(1rem,env(safe-area-inset-bottom))]` |

### 6.3 Pemetaan token dokumen → utility

| Dokumen §4 | Utility Tailwind | Contoh |
|---|---|---|
| `text-primary` | `text-ink-900` | Judul, teks utama |
| `text-secondary` | `text-ink-500` | Paragraf |
| `text-muted` | `text-ink-400` | Metadata, placeholder |
| `text-link` | `text-link` | Tautan di latar terang |
| `text-on-cta` | `text-white` | Hanya di atas `bg-brand-600` |
| `surface` | `bg-surface` | Kartu |
| `surface-muted` | `bg-surface-muted` | Latar halaman |
| `surface-inverse` | `bg-surface-inverse` | Header/footer gelap |
| `border` | `border-line` | Garis standar |
| `border-strong` | `border-line-strong` | Batas input |
| `focus-ring` | `outline-focus` / `var(--color-focus)` | Ring fokus |
| `radius-md` | `rounded-md` | Tombol, kartu |
| `shadow-sm` | `shadow-sm` | Header sticky |
| `motion-base` | `duration-[220ms] ease-standard` | Drawer, modal |

---

## 7. Komponen Blade yang perlu dibuat

| # | Komponen | Props inti | Dipakai di |
|---|---|---|---|
| 1 | `<x-btn>` | `variant` (primary/secondary/ghost/danger), `size`, `href`, `disabled` | Semua |
| 2 | `<x-card>` | `padding`, `as` | Semua |
| 3 | `<x-input>` | `label`, `error`, `hint`, `type` | C-05, C-10, C-11, C-12, A-03 |
| 4 | `<x-select>` | `label`, `error`, `options` | C-02, C-05, A-08 |
| 5 | `<x-textarea>` | `label`, `counter`, `maxlength` | C-05, C-16, A-03 |
| 6 | `<x-badge-status>` | `type` (order/payment), `value` | C-08, C-09, A-07, A-08 |
| 7 | `<x-product-card>` | `product` | C-01, C-02 |
| 8 | `<x-empty-state>` | `icon`, `title`, `description`, `action` | Semua daftar |
| 9 | `<x-error-state>` | `message`, `retryUrl` | Semua daftar |
| 10 | `<x-skeleton>` | `variant` (card/list/text) | Semua loading |
| 11 | `<x-toast>` | `type`, `message` | Global |
| 12 | `<x-modal>` | `name`, `title`, `size` | C-08, C-09, A-* |

**Aturan:** komponen memakai utility class langsung (bukan `@apply` berlebihan). Satu komponen = satu file di `resources/views/components/`.

---

## 8. Checklist QA akhir

**Konsistensi**

- [ ] ⬜ Tidak ada hex di luar token
- [ ] ⬜ Tidak ada ukuran teks di luar skala
- [ ] ⬜ Tidak ada jarak bukan kelipatan 4
- [ ] ⬜ Satu tombol primer per layar
- [ ] ⬜ Badge status identik di 8 layar yang memakainya

**Cakupan**

- [ ] ⬜ 16 layar customer × 2 ukuran
- [ ] ⬜ 12 layar admin × 2 ukuran
- [ ] ⬜ Tidak ada layar tambahan
- [ ] ⬜ Tidak ada fitur terlarang (wishlist, kupon, varian, COD, chat)

**Transaksi**

- [ ] ⬜ Total = subtotal + ongkir, tidak dobel
- [ ] ⬜ Resi hanya setelah "Dikirim"
- [ ] ⬜ Dua status beda bentuk
- [ ] ⬜ Harga beli tidak muncul di customer

**Responsif**

- [ ] ⬜ Tidak ada scroll horizontal di 360
- [ ] ⬜ Grid 2/3/4 kolom
- [ ] ⬜ Admin tanpa sidebar di mobile
- [ ] ⬜ Tombol utama terjangkau ibu jari

---

## 9. Log progres

| Tanggal | Layar | Aksi | Status | Catatan |
|---|---|---|---|---|
| 1 Nov 2026 | — | Dokumen progres dibuat | ✅ | — |
| | C-03 mobile | | ⬜ | |
| | C-03 desktop | | ⬜ | |

---

## 10. Yang masih menunggu keputusan

Lima pertanyaan di `uiux-brief-advance.md` §14.2 belum dijawab dan memengaruhi desain:

1. Strip kategori di Beranda — dipakai atau tidak?
2. Posisi bar pencarian di mobile — di header atau di bawah hero?
3. Indikator langkah checkout — ditampilkan atau tidak?
4. Undo hapus keranjang — perlu atau cukup konfirmasi?
5. Tombol ekspor di admin — perlu atau tidak?

Selama belum dijawab, kerjakan dengan asumsi default: **tanpa strip kategori, pencarian di header, indikator langkah tampil, undo tanpa konfirmasi, tanpa tombol ekspor.**
