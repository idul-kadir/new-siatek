# CETAK BIRU REDESIGN SIATEK v3.0

**Sistem Asli:** SIATEK v3.0 (Sistem Informasi Akademik Teknik Elektro & Komputer)
**URL:** https://siatek.web.id
**Pemilik:** Ridwan Kadir (Laboran)
**Tanggal Audit:** 29 Juni 2026
**Tujuan:** Redesign UI/UX + cetak biru untuk pengembangan ulang

---

## 1. INVENTARIS FITUR LENGKAP

### 1.1 Menu Utama (Sidebar)

| # | Menu | Sub-menu / Isi |
|---|---|---|
| 1 | **Beranda** | Dashboard Pengumuman (timeline) + Agenda (kalender 2020-2050) |
| 2 | **Pengelolaan** | Data Mahasiswa/Alumni (5 tab) • Jurusan • Data Bimbingan • Data Pengguna • Broadcast |
| 3 | **Pangkalan Data** | Pendidikan & Pengajaran • Penelitian • Pengabdian • Data Penunjang • Arsip Lain • SKP |
| 4 | **Biodata** | Edit Username + Password |
| 5 | **Jadwal Kuliah** | Grid hari (Senin-Sabtu) × ruangan (8 lokasi) |
| 6 | **Sinkronisasi Sister** | Pull data Sister Dikti (Pendidikan, Penelitian, Pengabdian, Penunjang) |
| 7 | **Logout** | - |

### 1.2 Modul Pengelolaan — Data Mahasiswa/Alumni (5 tab)

1. **Skripsi** — Accordion list mahasiswa. Filter: Prodi (Semua/S1 Elektro/S1 Komputer/D3 Elektro) + Status (22 status).
2. **Kerja Praktek** — Sama struktur dengan Skripsi, tapi alur KP.
3. **Verifikasi Berkas** — Queue berkas yang perlu diverifikasi admin.
4. **Data Alumni** — List mahasiswa yang sudah lulus.
5. **Kegiatan** — Log aktivitas mahasiswa (seminar, sidang, dll).

### 1.3 Workflow Skripsi (22 status mentah → dikelompokkan 5 fase)

```
Fase 1: PROPOSAL
  - Verifikasi Pendaftaran
  - Mengunggah persyaratan (KRS, Transkip, CV, KTM)
  - Verifikasi Berkas Pendaftaran
  - Membuat Surat Penunjukan Pembimbing

Fase 2: PENELITIAN
  - Mengunggah Laporan Proposal & Lembar Bimbingan
  - Verifikasi Laporan Proposal & Lembar Bimbingan
  - Tahap Penelitian

Fase 3: UJIAN HASIL
  - Mengupload berkas pendaftaran Ujian Hasil (KRS + Persetujuan)
  - Verifikasi berkas pendaftaran
  - Membuat surat penunjukan penguji hasil
  - Siap Ujian Hasil
  - Mengupload Berita Acara & Lembar Penilaian hasil
  - Upload lembar koreksi
  - Verifikasi lembar koreksi hasil

Fase 4: UJIAN TUTUP
  - Cetak lembar persetujuan Pembimbing
  - Upload lembar persetujuan pembimbing
  - Verifikasi lembar pembimbing
  - Membuat surat penunjukkan penguji tutup
  - Siap Ujian Tutup
  - Mengupload lembar penilaian

Fase 5: YUDISIUM
  - Upload skripsi dan jurnal
  - Verifikasi SKRIPSI dan Jurnal
  - Mencetak Berita Acara Yudisium
```

### 1.4 Role Sistem

| Role | Hak Akses |
|---|---|
| **Admin** (akun Ridwan Kadir) | Full access semua modul, verifikasi, sinkronisasi |
| **Dosen** (asumsi) | Lihat bimbingan, verifikasi mahasiswa bimbingan, biodata sendiri |
| **Mahasiswa** (asumsi) | Biodata sendiri, upload berkas, lihat jadwal |

### 1.5 Master Data

- **Prodi:** S1 Teknik Elektro, S1 Teknik Komputer, D3 Teknik Elektro
- **Ruangan (8):** Lab. Elektronika & Komunikasi, Lab. Komputer 1, Lab. Komputer 2, Lab. Teknik Kendali, Lab. Tenaga Listrik, Lab. Unprotect, R.K 2.11, R.K 3.16
- **Pengumuman:** Sender + message (timeline)
- **Agenda:** Kalender event

---

## 2. DIAGNOSA MASALAH UX (Dari Snapshot UI)

### 2.1 Navigasi & Informasi
- ❌ **Sidebar tanpa icon** — hanya teks, sulit scan visual
- ❌ **Tidak ada breadcrumb** — user bingung posisi setelah klik submenu
- ❌ **Expand-collapse sidebar** — bukan route langsung, harus klik parent dulu
- ❌ **Tidak ada active state** yang konsisten — sulit tahu halaman aktif

### 2.2 Dashboard (Beranda)
- ❌ **Pengumuman feed flat** — tidak ada filter/search/pin
- ❌ **Kalender dropdown tahun 2020-2050** (31 tahun) — UX buruk, harus scroll panjang
- ❌ **2 kolom layout** fixed — tidak adaptif mobile
- ❌ **Avatar pengirim** broken image (placeholder "...")

### 2.3 Data Mahasiswa
- ❌ **Accordion tanpa pagination** — 30+ mahasiswa scroll panjang
- ❌ **Status 22-item di dropdown** — overwhelming, harus baca semua dulu
- ❌ **Tidak ada bulk action** — verifikasi satu-satu
- ❌ **Tidak ada export** Excel/CSV

### 2.4 Form & Input
- ❌ **Filter tahun 2011-2026** — terlalu panjang, perlu quick range
- ❌ **Search box generik** — tanpa placeholder yang jelas
- ❌ **Captcha** widget aneh (heading icon "—")
- ❌ **Edit Biodata** — field Username + Password saja, tanpa profil lain (foto, kontak, dll)

### 2.5 Jadwal Kuliah
- ❌ **6 tab sekaligus selected** (Senin-Sabtu) — redundan
- ❌ **Cell jadwal cuma nama matkul** — tidak ada jam, dosen, kelas
- ❌ **Tidak bisa klik cell** untuk detail
- ❌ **Layout 8 kolom ruangan** — overflow di layar kecil

### 2.6 Sinkronisasi Sister
- ❌ **Hanya accordion tanpa progress indicator** — tidak jelas status sync
- ❌ **Tidak ada log/history sync**

### 2.7 Performance & Modern
- ❌ **Stack lama** (kemungkinan PHP native + Bootstrap jQuery, dilihat dari markup accordion sederhana)
- ❌ **Tidak ada dark mode**
- ❌ **Tidak ada notifikasi real-time** (badge count, toast)
- ❌ **Tidak ada mobile responsive** yang baik

---

## 3. ARSITEKTUR REDESIGN

### 3.1 Stack Pilihan

**Opsi A: Full Rewrite Modern (Rekomendasi)**
```
Backend:  Laravel 11 + PHP 8.3
Frontend: Inertia.js + Vue 3 + TypeScript
UI Kit:   shadcn-vue + Tailwind CSS + Reka UI
DB:       MySQL/MariaDB 11
Auth:     Laravel Sanctum + 2FA (TOTP)
Realtime: Laravel Reverb (WebSocket)
File:     Laravel Storage + S3-compatible
Search:   Meilisearch
```

**Opsi B: Incremental Redesign**
```
Tetap:  PHP existing + MySQL
Tambah: Tailwind CSS + Alpine.js (replace jQuery/Bootstrap)
Modif:  Blade templates, tambah route API JSON untuk AJAX
```

**Opsi C: SPA Terpisah**
```
Backend:  Laravel API only (tanpa view)
Frontend: Next.js 14 + TypeScript
Deploy:   Docker Compose (nginx + php-fpm + next standalone)
```

**Rekomendasi:** Opsi A untuk skalabilitas + UX modern. Opsi B kalau bos idul mau cepat & murah.

### 3.2 Struktur Direktori (Opsi A)

```
siatek-v4/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── MahasiswaController.php
│   │   ├── SkripsiController.php
│   │   ├── JadwalController.php
│   │   ├── PengumumanController.php
│   │   └── SinkronisasiController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Mahasiswa.php
│   │   ├── Dosen.php
│   │   ├── Skripsi.php
│   │   ├── Jadwal.php
│   │   ├── Ruangan.php
│   │   └── Pengumuman.php
│   ├── Services/
│   │   ├── SkripsiWorkflowService.php
│   │   ├── SisterSyncService.php
│   │   └── NotifikasiService.php
│   └── Jobs/
│       ├── SyncSisterJob.php
│       └── SendBroadcastJob.php
├── resources/js/
│   ├── Pages/
│   │   ├── Dashboard.vue
│   │   ├── Mahasiswa/
│   │   │   ├── Index.vue
│   │   │   ├── Detail.vue
│   │   │   └── SkripsiTimeline.vue
│   │   ├── Jadwal/Grid.vue
│   │   ├── Sinkronisasi/Status.vue
│   │   └── Pengumuman/Index.vue
│   ├── Components/
│   │   ├── AppShell.vue
│   │   ├── Sidebar.vue
│   │   ├── DataTable.vue
│   │   ├── StatusChip.vue
│   │   ├── TimelineStepper.vue
│   │   └── CalendarPicker.vue
│   └── Composables/
│       ├── useAuth.ts
│       ├── useSkripsi.ts
│       └── useSister.ts
├── database/migrations/
│   ├── ...users
│   ├── ...mahasiswas
│   ├── ...skripsis
│   ├── ...jadwals
│   └── ...pengumumen
└── routes/
    ├── web.php
    └── api.php
```

### 3.3 Skema Database (Inti)

```sql
-- Users (multi-role)
users (id, username, email, password, role, fcm_token, created_at)

-- Mahasiswa (extend users)
mahasiswas (id, user_id, nim, nama, prodi, angkatan, status, foto, no_hp, email)

-- Dosen (extend users)
dosens (id, user_id, nip, nama, bidang, foto)

-- Skripsi (workflow)
skripsis (id, mahasiswa_id, judul, fase, status, pembimbing_id, penguji_ids, created_at, updated_at)
skripsi_files (id, skripsi_id, fase, jenis, filename, uploaded_by, verified_by, verified_at)
skripsi_logs (id, skripsi_id, fase_lama, fase_baru, actor_id, catatan, created_at)

-- Jadwal
jadwals (id, hari, jam_mulai, jam_selesai, matkul_id, dosen_id, ruangan_id, kelas, semester, tahun_ajaran)
ruangans (id, nama, kapasitas, jenis)
matkuls (id, kode, nama, sks, prodi, semester)

-- Pengumuman
pengumumen (id, judul, isi, sender_id, target_role, pinned, published_at)
broadcasts (id, pesan, target_filter, sent_count, created_at)

-- Sinkronisasi Sister
sister_sync_logs (id, kategori, status, records_synced, started_at, finished_at, error_log)
```

### 3.4 State Machine Skripsi (Backend)

```php
class SkripsiFase {
    const PROPOSAL = 'proposal';
    const PENELITIAN = 'penelitian';
    const UJIAN_HASIL = 'ujian_hasil';
    const UJIAN_TUTUP = 'ujian_tutup';
    const YUDISIUM = 'yudisium';
    const SELESAI = 'selesai';
}

class SkripsiTransition {
    const transitions = [
        'proposal' => ['penelitian', 'ditolak'],
        'penelitian' => ['ujian_hasil', 'proposal'], // bisa kembali
        'ujian_hasil' => ['ujian_tutup', 'penelitian'],
        'ujian_tutup' => ['yudisium', 'ujian_hasil'],
        'yudisium' => ['selesai'],
        'selesai' => [], // terminal
        'ditolak' => ['proposal'],
    ];
}
```

---

## 4. WIREFRAME REDESIGN

### 4.1 Layout Global (App Shell)

```
┌───────────────────────────────────────────────────────────────┐
│  [LOGO SIATEK]    [🔍 Search global...]    [🔔 3] [👤 Ridwan ▾]│  ← Topbar (60px)
├──────────┬────────────────────────────────────────────────────┤
│          │                                                    │
│ 🏠 Beranda│                                                    │
│ 📚 Akademik                                                    │
│   └ Jadwal│              <MAIN CONTENT AREA>                   │
│   └ KRS   │                                                    │
│   └ Nilai │                                                    │
│ 📝 Skripsi                                                     │
│   └ Antrean│                                                    │
│   └ Bimbingan                                                    │
│   └ Semua  │                                                    │
│ 📢 Pengumuman                                                   │
│   └ Compose                                                      │
│   └ History                                                      │
│ 🗄 Master  │                                                    │
│   └ Mhs    │                                                    │
│   └ Dosen  │                                                    │
│   └ Ruangan                                                      │
│ ⚙ Pengaturan                                                     │
│   └ User   │                                                    │
│   └ Role   │                                                    │
│   └ Sister │                                                    │
│          │                                                    │
│ 🌓 (theme) │                                                    │
└──────────┴────────────────────────────────────────────────────┘
```

### 4.2 Dashboard (Beranda Baru)

```
┌──────────────────────────────────────────────────────────────────┐
│  Selamat pagi, Pak Ridwan 👋                                       │
│  Senin, 29 Juni 2026                                              │
├──────────────────────────────────────────────────────────────────┤
│ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐  │
│ │1.124 │ │ 978  │ │ 745  │ │ 142  │ │  38  │ │  24  │ │  7   │  │
│ │TE    │ │TK    │ │PTE   │ │Dosen │ │Skripsi│ │ KP   │ │TTD  │  │
│ └──────┘ └──────┘ └──────┘ └──────┘ └──────┘ └──────┘ └──────┘  │
��──────────────────────────────────────────────────────────────────┤
│ ┌────────────────────────────┐ ┌─────────────────────────────┐   │
│ │ 📢 Pengumuman Terbaru       │ │ 📅 Jadwal Hari Ini           │   │
│ │ ────────────────────────  │ │ Senin, 29 Juni 2026         │   │
│ │ 🔴 [PIN] Sosialisasi SKP   │ │ ─────────────────────────   │   │
│ │    Yasin • 2 jam lalu      │ │ 08:00 Teknik Kendali Dasar  │   │
│ │ ────────────────────────  │ │       Lab. Kendali • Pak A  │   │
│ │ 📌 Undangan Dosen          │ │ 10:00 Statistik Teknik      │   │
│ │    Ridwan • kemarin        │ │       Lab. Komputer 1 • Bu B│   │
│ │ ────────────────────────  │ │ 13:00 Elektronika Daya      │   │
│ │ 📝 Jadwal Sem. Genap 2026  │ │       Lab. Elektronika      │   │
│ │    Yasin • 3 hari lalu     │ │ ...                          │   │
│ └────────────────────────────┘ └─────────────────────────────┘   │
└──────────────────────────────────────────────────────────────────┘
```

### 4.3 Halaman Skripsi (DataTable Modern)

```
┌──────────────────────────────────────────────────────────────────┐
│ 📝 Skripsi                            [+ Tambah] [⬇ Export]      │
├──────────────────────────────────────────────────────────────────┤
│  Filter:  [Semua Prodi ▾] [Semua Fase ▾] [Semua Status ▾]        │
│           [🔍 Cari nama / NIM...]                  [Reset Filter] │
├──────────────────────────────────────────────────────────────────┤
│ ☐ │ NIM      │ Nama              │ Prodi   │ Fase        │ Aksi  │
├──────────────────────────────────────────────────────────────────┤
│ ☐ │ 521422026│ Mariska Palakum    │ S1 TE   │ 🟡 Penelitian│ Detail│
│ ☐ │ 521422007│ Yusfiyah F Daud    │ S1 TE   │ 🟢 Proposal  │ Detail│
│ ☐ │ 521422025│ Salsabila Buka     │ S1 TE   │ 🔵 Ujian Hasil│ Detail│
│ ☐ │ 521422018│ Nur Uyun I Yusuf   │ S1 TE   │ ⚪ Ditolak   │ Detail│
│ ...                                                               │
├──────────────────────────────────────────────────────────────────┤
│         [◄ Prev]  1 2 3 ... 12  [Next ►]      25 rows/page ▾     │
└──────────────────────────────────────────────────────────────────┘
```

### 4.4 Detail Skripsi + Timeline

```
┌──────────────────────────────────────────────────────────────────┐
│ ← Kembali          Mariska Palakum - 521421026 - S1 Teknik Elektro│
├──────────────────────────────────────────────────────────────────┤
│  Judul: "Rancang Bangun Sistem Monitoring Kualitas Air..."         │
│  Pembimbing: Dr. Yasin Mohamad, ST., MT.                          │
│  Penguji: [3 orang]                                               │
├──────────────────────────────────────────────────────────────────┤
│  PROGRESS SKRIPSI                                                  │
│  ──────────────                                                   │
│  ✅ PROPOSAL              15 Mar 2026                              │
│     └ Berkas lengkap, Pembimbing ditunjuk                          │
│  ──────────────                                                   │
│  ✅ PENELITIAN            22 Apr 2026                              │
│     └ Laporan penelitian disetujui                                │
│  ──────────────                                                   │
│  🔄 UJIAN HASIL           Sedang berjalan                          │
│     └ Penguji: Bu A, Pak B, Pak C                                │
│     └ Sidang: 5 Jul 2026 (akan datang)                           │
│  ──────────────                                                   │
│  ⚪ UJIAN TUTUP                                                       │
│  ⚪ YUDISIUM                                                         │
├──────────────────────────────────────────────────────────────────┤
│  BERKAS                                                            │
│  📄 Proposal.pdf          ✅ Verified                               │
│  📄 Laporan Penelitian.pdf ✅ Verified                              │
│  📄 KRS.pdf               ✅ Verified                               │
│  📄 Persetujuan Pembimbing.pdf ⏳ Pending verifikasi               │
│  📝 Berita Acara (kosong - belum diupload)                         │
│                                                                  │
│  [Verifikasi] [Tolak] [Komentar]                                   │
└──────────────────────────────────────────────────────────────────┘
```

### 4.5 Jadwal Kuliah (Grid View)

```
┌──────────────────────────────────────────────────────────────────┐
│ 📅 Jadwal Kuliah          Semester: [Genap 2025/2026 ▾]  Prodi: [S1 TE ▾]│
├─────┬───────┬───────┬───────┬───────┬───────┬───────┬───────┬──────┤
│Jam  │Lab.Elk│Lab.K1 │Lab.K2 │Lab.Ken│Lab.Ten│Lab.Unp│R.2.11 │R.3.16│
├─────┼───────┼───────┼───────┼───────┼───────┼───────┼───────┼──────┤
│08:00│       │Statist│       │Kendali│       │       │       │      │
│     │       │Bu B   │       │Pak A  │       │       │       │      │
├─────┼───────┼───────┼───────┼───────┼───────┼───────┼───────┼──────┤
│10:00│Telekom│       │Struk  │       │Ilmu  │       │       │      │
│     │Dasar  │       │Data   │       │Bahan │       │       │      │
│     │Pak C  │       │Bu D   │       │Pak E │       │       │      │
├─────┼───────┼───────┼───────┼───────┼───────┼───────┼───────┼──────┤
│13:00│Elektro│       │       │       │       │       │Fisika2│Basis │
│     │Daya   │       │       │       │       │       │Bu F  │data  │
│     │Pak G  │       │       │       │       │       │       │Bu H  │
├─────┴───────┴───────┴───────┴───────┴───────┴───────┴───────┴──────┤
│ ← Week 1 | Week 2 | Week 3 | Week 4 | Week 5 ... →                  │
└──────────────────────────────────────────────────────────────────┘
```

### 4.6 Sinkronisasi Sister (Status + Progress)

```
┌──────────────────────────────────────────────────────────────────┐
│ 🔄 Sinkronisasi Sister Dikti        [Sync Semua] [Auto-sync: ON ⚙]│
├──────────────────────────────────────────────────────────────────┤
│  ┌────────────────────────────────────────────────────────────┐ │
│  │ 📚 Pendidikan & Pengajaran         [Sync]                  │ │
│  │ ─────────────────────────────────────────────────────────  │ │
│  │ Last sync: 28 Jun 2026 22:00 (success)                     │ │
│  │ Records: 247 fetched, 12 updated, 0 error                  │ │
│  │ [Lihat log] [Lihat data]                                   │ │
│  └────────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │ 🔬 Penelitian                     [Sync]                  │ │
│  │ ─────────────────────────────────────────────────────────  │ │
│  │ Last sync: 28 Jun 2026 22:05 (success)                     │ │
│  │ Records: 89 fetched, 3 updated, 0 error                    │ │
│  │ [Lihat log] [Lihat data]                                   │ │
│  └────────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │ 🤝 Pengabdian                       [Sync]                │ │
│  │ ─────────────────────────────────────────────────────────  │ │
│  │ Last sync: 28 Jun 2026 22:10 (success)                     │ │
│  │ Records: 156 fetched, 5 updated, 0 error                   │ │
│  │ [Lihat log] [Lihat data]                                   │ │
│  └────────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │ 🛠 Unsur Penunjang                  [Sync]                │ │
│  │ ─────────────────────────────────────────────────────────  │ │
│  │ Last sync: 28 Jun 2026 22:15 (success)                     │ │
│  ��� Records: 34 fetched, 0 updated, 0 error                    │ │
│  │ [Lihat log] [Lihat data]                                   │ │
│  └────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────┘
```

---

## 5. FITUR BARU YANG DIREKOMENDASIKAN

### 5.1 Wajib (MVP)
- ✅ **Dark mode** (toggle topbar)
- ✅ **Notifikasi real-time** (badge + toast + email)
- ✅ **Mobile responsive** (sidebar collapse jadi drawer)
- ✅ **Export Excel/PDF** di semua data list
- ✅ **Bulk action** (verifikasi banyak sekaligus)
- ✅ **Search global** (cari mahasiswa/dosen/jadwal dari topbar)
- ✅ **Filter chips** ganti dropdown panjang
- ✅ **Pagination** di semua list
- ✅ **Activity log** per aksi (audit trail)
- ✅ **Status color coding** (merah/kuning/hijau/biru/abu)

### 5.2 Penting (V1.1)
- 🔔 **Push notification** via FCM (Android) / Web Push
- 📊 **Dashboard analytics** (chart statistik: mhs aktif, distribusi fase, dll)
- 📅 **Kalender event** UI proper (FullCalendar.js)
- 📥 **Import Excel** untuk bulk insert
- 🔐 **2FA TOTP** untuk admin
- 💬 **In-app messaging** antar role
- 📝 **Notulensi sidang** digital (replace form fisik)

### 5.3 Nice-to-have (V2)
- 🤖 **AI assistant** untuk cek kesesuaian judul skripsi
- 📱 **Mobile app** (React Native / Flutter) untuk verifikasi via HP
- 🔗 **Integrasi Sister real-time** (webhook + auto-sync tiap jam)
- 📈 **Predictive analytics**: prediksi kelulusan, deteksi mhs "stuck"
- 🎓 **E-Portofolio** mahasiswa (linked ke Sister)
- 🗃 **Document versioning** (track revisi skripsi)
- 💳 **Payment gateway** untuk registrasi/sidang

---

## 6. ROADMAP IMPLEMENTASI

### Phase 1: Foundation (4 minggu)
- [ ] Setup Laravel 11 + Inertia + Vue 3 + shadcn-vue
- [ ] Skema database + migration
- [ ] Auth (login + captcha baru + 2FA)
- [ ] Sidebar + topbar + theme system
- [ ] Dashboard widgets
- [ ] Deploy dev server

### Phase 2: Core Modul (6 minggu)
- [ ] Modul Mahasiswa (CRUD + DataTable + detail)
- [ ] Modul Skripsi (workflow 5 fase + timeline)
- [ ] Modul Jadwal (grid + import Excel)
- [ ] Modul Pengumuman (compose + history)
- [ ] Modul Biodata (edit profil + foto)
- [ ] Export Excel/PDF

### Phase 3: Integrasi (3 minggu)
- [ ] Sinkronisasi Sister (job queue + retry + log)
- [ ] Notifikasi real-time (Reverb + FCM)
- [ ] Activity log + audit trail
- [ ] Bulk action + import Excel

### Phase 4: Polish (2 minggu)
- [ ] Dark mode + theme tuning
- [ ] Mobile responsive + drawer
- [ ] Loading skeleton + empty state
- [ ] Error boundary + 404 page
- [ ] UAT + bug fixing

### Phase 5: Launch (1 minggu)
- [ ] Data migration dari SIATEK v3
- [ ] Training user (dosen, laboran)
- [ ] Parallel run (v3 + v4 bersamaan 1 minggu)
- [ ] Cutover + monitoring

**Total: ~16 minggu (4 bulan)** untuk 1 developer full-time.

---

## 7. RISIKO & MITIGASI

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Data migration gagal | Tinggi | Dry-run + backup DB + parallel run 1 minggu |
| Sister API rate limit | Sedang | Queue + retry + cache |
| User resistensi perubahan | Sedang | Training + UI mirip familiar + feedback loop |
| Scope creep | Tinggi | Tahan Phase 5+ di roadmap terpisah |
| Single dev bottleneck | Tinggi | Tulis test + dokumentasi + delegation |

---

## 8. ESTIMASI BIAYA (Referensi, Bukan Quotation)

**Full rewrite (Opsi A):**
- Dev 4 bulan @ Rp 25jt/bulan = Rp 100jt
- Server 1 tahun = Rp 6jt
- Domain + SSL = Rp 500rb
- **Total: ~Rp 106.5jt**

**Incremental (Opsi B):**
- Dev 2 bulan @ Rp 25jt/bulan = Rp 50jt
- **Total: ~Rp 56.5jt**

*(Bos idul sebagai laboran bisa juga handle sendiri sebagian — biaya bisa ditekan)*

---

## 9. PERTANYAAN UNTUK BOS IDUL

Sebelum lanjut eksekusi, butuh klarifikasi:

1. **Scope:** Full rewrite (Opsi A) atau incremental (Opsi B)?
2. **Workflow skripsi:** Apakah 22 status asli perlu dipertahankan semua, atau boleh disederhanakan jadi 5 fase (seperti blueprint)?
3. **Multi-role:** Apakah akan ada 3 role (admin/dosen/mahasiswa) atau hanya admin dulu?
4. **Sister API:** Apakah sudah punya kredensial API Sister Dikti atau masih via form login manual?
5. **Data lama:** Apakah data di SIATEK v3 perlu dimigrasi ke v4, atau mulai dari nol?
6. **Branding:** Logo + warna khas Teknik Elektro & Komputer apa? (untuk theme default)

---

**Dokumen ini: `/tmp/siatek-inventory.md` + blueprint ini**
**Tersimpan di:** `/tmp/siatek-blueprint.md`

---

## 10. CHANGELOG

### 2026-07-16
- **BREAKING:** Hapus KPI card "WA Terkirim (Hari Ini)" dari dashboard — panel WA terkirim tidak lagi relevan
- **ADDED:** Section "Distribusi Mahasiswa per Prodi" di dashboard — menampilkan 3 prodi: S1 Teknik Elektro (1.124), S1 Teknik Komputer (978), D3 Teknik Elektro (745)
- **UPDATED:** KPI "Mahasiswa Aktif" total: 2.847 (dari 247)
- **UPDATED:** Chart "Angkatan Mahasiswa" sekarang mencakup 3 prodi (tambahan D3 Teknik Elektro)
- **UPDATED:** Master Data prodi: S1 Teknik Elektro, S1 Teknik Komputer, D3 Teknik Elektro
- **UPDATED:** Filter modul Skripsi/KP: tambahkan D3 Elektro
- **UPDATED:** Wireframe Dashboard (4.2) menampilkan KPI cards + distribusi per prodi