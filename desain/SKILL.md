---
name: desain
description: Panduan konvensi desain UI proyek redesain-siatek. Baca skill ini saat user minta membuat/mengubah/memperbaiki tampilan (halaman, komponen, warna, layout) agar konsisten dengan halaman yang sudah ada. ACUAN UTAMA = halaman `pages/jurusan/arsip/arsip-jurusan.php` (dan saudara-saudaranya yang memakai konsep oranye). Jangan pernah improvisasi warna/layout — selalu samakan dengan acuan.
---

# Skill: Desain UI Konsisten (acuan = Arsip)

Rujukan visual & struktur: **`pages/jurusan/arsip/arsip-jurusan.php`** — inilah standar de-facto untuk seluruh halaman di proyek ini. Halaman lain (Kerja Sama `jurusan-kerjasama.php`, Kurikulum `jurusan-kurikulum.php`, Tridharma `jurusan-tridharma.php`) mengikuti konsep yang sama (oranye). GUNakan Arsip sebagai tolok ukur utama.

> ⚠️ JANGAN improvisasi warna. Kalau ragu, buka Arsip dan salin persis.
> ⚠️ JANGAN jadikan halaman `jurusan-surat.php` (dan turunannya) sebagai acuan WAJIB — Surat memakai akcent **ungu/violet** yang TIDAK mewakili konsep umum. Acuan warna yang benar = **oranye** (seperti Arsip).

---

## 1. Ruang Lingkup (SAMA dengan blueprint §11.0 — WAJIB)

- Tugas AI/assistant **HANYA FRONTEND** — HTML + CSS + JS + data dummy.
- Jangan membuat arsitektur PHP backend, helper PHP, atau CRUD.
- Data dummy **hardcoded langsung di HTML** (`<script type="application/json">` atau markup statis), bukan `$_SESSION`/`$_POST`/`$_GET`.
- Aksi tombol (simpan/edit/hapus) boleh dibangun frontend-only (JS) dan ditandai "dummy".
- File tetap berekstensi `.php` (di-route `index.php`) tapi isinya murni markup/JS tanpa logika PHP (kecuali loop array dummy sederhana seperti Arsip).

---

## 2. Stack & Palet Warna

- **Tailwind CSS via CDN** + **Font Awesome 6** + **Chart.js** (bila perlu grafik).
- Warna brand: **oranye `#f97316`**. Teks judul `slate-800`, subjudul `slate-500`. Kartu `bg-white border-slate-200`.
- Sidebar: navy `#1a365d`, item aktif `#11243d` + border-kiri oranye 4px (sudah di `index.php`, jangan ulang).

### Aturan poin warna — yang BOLEH / TIDAK BOLEH
| Sumber | Wajib? |
|---|---|
| Header ikon kotak | `bg-orange-100 text-orange-600` ✅ |
| Tombol aksi utama (header) | `bg-orange-500 … hover:bg-orange-600` ✅ |
| Toolbar focus (search/select) | `focus:border-orange-400` ✅ |
| Indeks baris tabel | `bg-orange-500 text-white` ✅ |
| Hover baris tabel | `hover:bg-orange-50` ✅ |
| Hover tab-btn / pg-btn | `border-color:#fdba74; color:#c2410c` ✅ |
| Badge filter/chip oranye | `bg-orange-100 … text-orange-700` ✅ |
| Tombol aksi baris | emerald(unduh) / sky(edit) / rose(hapus) ✅ |
| Header tabel | `bg-slate-900 text-white` ✅ (Arsip gelap!) |
| **Akcent ungu/violet/pink dll untuk header halaman** | ❌ JANGAN — itu bukan acuan |
| **Tombol aksi hitam (detail dst)** | ❌ JANGAN — Arsip tidak punya |

---

## 3. Komponen Wajib (biarkan persis seperti Arsip)

### 3.1 Page Header
```html
<section class="mb-5">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
        <i class="fas fa-…"></i>
      </div>
      <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-800">Judul</h1>
        <p class="text-xs text-slate-500">Subjudul</p>
      </div>
    </div>
    <!-- tombol aksi utama (kanan): -->
    <button class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600">
      <i class="fas fa-plus text-sm"></i><span class="tip">…</span>
    </button>
  </div>
</section>
```

### 3.2 Kartu Statistik (tile)
```html
<section class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
  <div class="tile-orange tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-orange-500/25">
    <div class="flex items-start justify-between gap-2">
      <div class="min-w-0">
        <p class="text-xs font-medium uppercase tracking-wide text-white/85">Label</p>
        <p class="mt-2 text-2xl font-bold tracking-tight">Nilai</p>
        <p class="mt-2 text-[11px] text-white/70">sub</p>
      </div>
      <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-…"></i></span>
    </div>
  </div>
</section>
```
- Variasi: `.tile-orange`, `.tile-sky`, `.tile-emerald`, `.tile-violet` (+ `shadow-{warna}/25`).
- `.tile-corak` = polkadot halus (dipasang selalu).

### 3.3 Toolbar (cari + filter)
```html
<section class="mb-4">
  <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
    <div class="relative flex-1 min-w-[200px]">
      <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
      <input type="search" id="fCari" placeholder="Cari…"
        class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
    </div>
    <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
      <option value="">Semua Tahun</option>
    </select>
    <button id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">
      <i class="fas fa-times mr-1"></i>Reset
    </button>
  </div>
</section>
```

### 3.4 Tabel (GELAP — persis Arsip)
```html
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
  <div class="max-h-[560px] overflow-y-auto">
    <table class="w-full text-left text-sm">
      <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
        <tr>
          <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">…</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <tr class="transition hover:bg-orange-50 bg-white" data-… >
          <td class="py-4 pl-5 pr-4">
            <div class="flex items-center gap-3">
              <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">1</span>
              <div class="min-w-0">
                <p class="font-semibold text-slate-800 leading-snug">Nama</p>
                <!-- badge format file: -->
                <span class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold bg-rose-500 text-white">
                  <i class="fas fa-file-export"></i>PDF
                </span>
              </div>
            </div>
          </td>
          <td class="px-4 py-4">…</td>
          <td class="px-5 py-4">
            <div class="flex items-center gap-1.5">
              <!-- aksi btn-circle: unduh=emerald, edit=sky, hapus=rose -->
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
```
- Zebra: bergantian `bg-white` / `bg-slate-50/60`.
- Header **gelap `bg-slate-900 text-white`** (JANGAN abu terang).

### 3.5 Tombol Aksi Baris (btn-circle) — PALET WAJIB
```html
<!-- Unduh -->
<a class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
  <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
</a>
<!-- Edit -->
<button class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
  <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
</button>
<!-- Hapus -->
<button class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600">
  <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
</button>
```
> ❌ Tidak ada tombol aksi hitam/slate-900 di Arsip. Kalau butuh aksi lain, gunakan palet di atas.

### 3.6 Empty State
```html
<div id="noHasil" class="hidden py-16 text-center">
  <i class="fas fa-archive text-4xl text-slate-300"></i>
  <p class="mt-3 font-medium text-slate-500">Tidak ada … ditemukan</p>
  <p class="text-xs text-slate-400">Coba ubah kata kunci atau filter.</p>
</div>
```

### 3.7 CSS inti (salin dari Arsip)
```css
.content-scroll { overflow-y: auto; min-height: 0; }
@keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
.reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
@media (prefers-reduced-motion: reduce) { .reveal { animation: none; } }
.btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
.btn-circle-lg { width: 2.5rem; height: 2.5rem; }
.btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
.btn-circle:hover .tip { opacity: 1; visibility: visible; }
.tile-orange  { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }
.tile-sky     { background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); }
.tile-emerald { background: linear-gradient(135deg, #047857 0%, #10b981 100%); }
.tile-violet  { background: linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%); }
.tile-corak { position: relative; overflow: hidden; }
.tile-corak::before { content: ""; position: absolute; inset: 0; pointer-events: none; background-image: radial-gradient(rgba(255,255,255,.22) 1px, transparent 1px); background-size: 12px 12px; opacity: .35; mix-blend-mode: overlay; }
.tile-corak > * { position: relative; }
.pg-btn { min-width: 2rem; height: 2rem; border-radius: .5rem; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: .75rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
.pg-btn:hover { border-color: #fdba74; color: #c2410c; }
.pg-btn:disabled { opacity: .45; cursor: not-allowed; }
.pg-btn.on { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }
```

> Catatan: kalau halaman memakai tab penuh (`tab-btn`), salin style `tab-btn` dari halaman Kerja Sama/Kurikulum (hover `#fdba74`/`#c2410c`, aktif navy `#1e3a5f`).

---

## 4. Pola Data Dummy (Frontend Only)

- Data dummy **hardcoded di halaman** (blok `<script type="application/json">` ATAU markup HTML statis, ATAU array PHP sederhana loop `foreach` seperti Arsip).
- Field mengikuti struktur tabel asli (bila ada).
- Filter live via JS dengan atribut `data-*` pada baris (tanpa reload); counter update via `textContent`.
- Aksi (simpan/edit/hapus) frontend-only + toast "… (dummy)". Menghubungkan ke server = pekerjaan pemilik.

---

## 5. Checklist Halaman Baru (WAJIB)

- [ ] Slug mengikuti konvensi, terdaftar di `index.php` ($routeMap), `.htaccess`, dan `components/sidebar.php`.
- [ ] File halaman bernama `<slug>.php` + guard `index.php` (403 redirect).
- [ ] Header ikon = `bg-orange-100 text-orange-600` + tombol aksi oranye.
- [ ] Kartu statistik = `tile-{warna} tile-corak`, ikon kanan `bg-white/20`.
- [ ] Toolbar search/select `focus:border-orange-400`.
- [ ] Tabel header **gelap `bg-slate-900 text-white`**, zebra, scroll `max-h-[560px]`, indeks oranye `bg-orange-500`.
- [ ] Aksi baris = btn-circle emerald/sky/rose (tanpa tombol hitam).
- [ ] `<main>` memakai `.content-scroll`.
- [ ] `php -l` bersih setelah perubahan.

---

## 6. Pitfall (jangan diulang)

- **JANGAN improvisasi warna** (mis. pink, ungu, hitam untuk header/tombol). Cek Arsip dulu.
- **JANGAN jadikan `jurusan-surat.php` sebagai acuan** — ia memakai ungu dan tidak mewakili konsep umum; acuan = oranye (Arsip).
- Jangan pakai header tabel abu terang (Surat/SKP) — Arsip pakai **gelap `bg-slate-900`**.
- Jangan menaruh contoh `<?php include ... ?>` di dalam komentar HTML file `.php` (PHP tetap mengeksekusinya).
