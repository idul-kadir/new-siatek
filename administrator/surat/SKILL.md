---
name: surat
description: Panduan sistem pembuatan surat resmi A4 di proyek redesain-siatek. Baca skill ini saat user minta membuat/mengubah/mencetak/memeriksa surat (dokumen/...), tanpa perlu penjelasan ulang aturan-aturannya.
---

# Skill: Surat (template A4 resmi)

Rujukan visual: surat asli produksi di `https://siatek.web.id/adm/dokumen/cetak/{kategori}/{nim}` (mis. `surat-penunjukkan-pembimbing-skripsi/521421011`).

## Arsitektur (singkat)

- `surat/template-a4.php` → kertas A4 + switch whitelist. Dibuka lewat URL bersih:
  - `.htaccess`: `^dokumen/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)$` → `surat/template-a4.php?kategori=$1&nim=$2`
  - Url: `.../dokumen/surat-penunjukkan-pembimbing-skripsi/521421011`
- File konten = `surat/content-surat/<nama>.php` — **HTML polos, data dummy**, TANPA logika/skrip PHP untuk isi. Satu-satunya PHP yang boleh: include kop (kalau surat butuh kop).
- Menambah surat baru = 2 langkah:
  1. Buat `content-surat/surat-<kategori>.php` (HTML polos, struktur di bawah).
  2. Tambah `case '<kategori>': require __DIR__.'/content-surat/surat-<kategori>.php'; break;` di switch `template-a4.php` (default: "Dokumen yang Anda minta tidak ada.", kosong: "Jenis dokumen tidak dikenali.").
- `$nim` dari URL otomatis tersedia di file konten (untuk data real nanti).

## Aturan WAJIB user (jangan dilanggar)

1. **Simpel & aman**: pilih konten lewat switch whitelist — dilarang `include` file dari input URL mentah.
2. **Full template**: isi surat = HTML murni (dummy). **Data tidak boleh pakai script PHP**.
3. **Ukuran font**: **kecuali kop, SEMUA 12pt** (judul bold+underline, nomor, pembuka, tabel, penutup, TTD, tembusan).

## Font & layout (sudah disamakan dengan produksi)

- `body` → `font-size: 12pt`, `line-height: 1.35`, Times New Roman.
- Kop: `.org` = **12pt**, `.addr` = **10pt**; teks `.brand` `text-align:center; margin-left:35px`; logo `width:100px; position:absolute; left:35px`; `hr` = `border:1px solid #000`.
- `.judul` margin `18px 0 10px`; `.nomor` margin-top `8px`; `.pembuka` margin-top `28px`.
- `table.data`: kolom no `40px`, kolom label `130px`, kolom titik dua `25px`.
- Spacer antar-blok `height="20px"`; ruang TTD `.ttd-place` `100px` (cetak 95px); spacer sebelum tembusan `height="35px"`.
- `.sheet` = `width:210mm`, padding `12mm 20mm 14mm`, `@page { size: A4 portrait; margin: 0 }`.

## Struktur isi surat (copy dari acuan produksi)

1. Kop (kalau perlu): `<?php include __DIR__ . '/kop-surat.php'; ?>`
2. `<p class="judul"><b><u>JUDUL LENGKAP</u></b></p>`
3. `<p class="nomor">Nomor : 300/UN47.B5.5/TD.06/2026</p>`
4. `<p class="pembuka">Ketua Jurusan ... sebagai berikut :</p>`
5. `table.data` → baris pokok + NIP (rowspan 2), lalu blok "Mahasiswa yang dibimbing," (Nama / NIM / Judul — judul justify, bold).
6. `<p class="penutup">Kepada Bapak/Ibu ...</p>`
7. `table.ttd-wrap` kolom kanan (tanggal "Gorontalo, 09 Juni 2026" / "Ketua Jurusan" / svg tanda tangan dummy / `<b><u>` nama `</u></b>` / `NIP. ...`), kolom kiri tembusan:
   - `Tembusan disampaikan kepada Yth. :` / `1. ...` / `2. Mahasiswa yang bersangkutan` / `3. Arsip`
- TTD memakai **svg dummy** (bukan gambar eksternal — tanpa load sumber luar).

## File konten yang sudah ada

| Kategori (URL) | File | Isi utama |
|---|---|---|
| `surat-penunjukkan-penguji` (alias `surat-penunjukkan-hasil`) | `content-surat/surat-penunjukkan-penguji.php` | 5 penguji + NIP; mhs Alya Pratiwi Putri Junus / 521422062; No 141/UN47.B5.5/TD.06/2026; tgl 02 Maret 2026 |
| `surat-penunjukkan-pembimbing-skripsi` | `content-surat/surat-penunjukkan-pembimbing-skripsi.php` | Pembimbing 1 Amirudin Yunus Dako (NIP 197410032001121001) & 2 Iskandar Z. Nasibu (197011052001121001); mhs Mohamad Ryan Noor Sahidu / 521421011; No 300/UN47.B5.5/TD.06/2026; tgl 09 Juni 2026; tembusan "1. Dosen pembimbing I dan II" |

Kop dipakai oleh kedua surat di atas (include sendiri di masing-masing file konten). `konten-contoh-tanpa-kop.php` = contoh konten tanpa kop.

## Teks kop yang benar (persis produksi)

- Org: `KEMENTERIAN PENDIDIKAN,KEBUDAYAAN,` (tanpa spasi setelah koma) lalu baris RISET DAN TEKNOLOGI / UNIVERSITAS NEGERI GORONTALO / FAKULTAS TEKNIK / JURUSAN TEKNIK ELEKTRO DAN KOMPUTER.
- Alamat: `Jalan B.J.Habibie Desa Moutong Kecamatan Tilongkabila Kab.Bone Bolango` / `Telp. (0435) 821125, Fax. (0435) 821752 Gorontalo` / `Laman :http:// www.ft.ung.ac.id`.
- Logo: `src="/redesain-siatek/surat/img/logo.png"` — **path absolut**, karena `src` relatif akan 404 di URL bersih `/dokumen/...`.

## Verifikasi (Windows / PHP CLI)

- PHP: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`
- Lint: `php -l <file>`
- Render CLI: datang set `$_GET = array('kategori'=>..., 'nim'=>...)`, `ob_start()` + `include template-a4.php`, lalu cocokkan penanda (kop, judul, nomor, nama, NIM, TTD, tembusan). Pastikan `LEN` output wajar.

## Pitfall (jangan diulang)

- **Jangan pernah** menaruh contoh `<?php include ... ?>` di dalam komentar HTML file `.php` — PHP tetap mengeksekusinya → file kop dulu include-diri sendiri tanpa henti (hang CLI).
- Surat lama `surat/surat.html` (font 11pt) tidak dipakai lagi.
- Kalau mengganti nama proyek (base path), `src` logo absolut di `kop-surat.php` ikut diubah.