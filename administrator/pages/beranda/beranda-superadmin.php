<?php
/**
 * View Beranda: Super Administrator (Level 9)
 * Pusat Komando & Pengawasan Sistem SIATEK
 * Fokus: Manajemen Pengguna Lintas Peran, Kesehatan Platform & Database, dan Tata Kelola Sistem.
 */
declare(strict_types=1);

if (!function_exists('f')) {
    function f($n) { return number_format((int)$n, 0, ',', '.'); }
}
if (!isset($today_label)) {
    $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $today_label = $hari[(int)date('w')] . ', ' . (int)date('d') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');
}
if (!isset($nmUser)) {
    $nmUser = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Super Administrator';
}

// 1. Metrik Pokok Sistem (100% Data Valid Terfilter Sesuai Status Aktif)
// A. Mahasiswa Aktif Riil (Status 'Aktif' dalam 7 angkatan terakhir)
$aktifSince = (int)date('Y') - 7;
$qMhsAktif = query("SELECT COUNT(*) AS c FROM mahasiswa WHERE status = 'Aktif' AND angkatan >= $aktifSince");
$totalMhsAktif = $qMhsAktif ? (int)mysqli_fetch_assoc($qMhsAktif)['c'] : 0;

// B. Dosen Aktif (Status 'aktif')
$qDosenAktif = query("SELECT COUNT(*) AS c FROM dosen WHERE status = 'aktif'");
$totalDosenAktif = $qDosenAktif ? (int)mysqli_fetch_assoc($qDosenAktif)['c'] : 0;

// C. Skripsi Berjalan (Status 'aktif')
$qSkripsiAktif = query("SELECT COUNT(*) AS c FROM skripsi WHERE status = 'aktif'");
$totalSkripsi = $qSkripsiAktif ? (int)mysqli_fetch_assoc($qSkripsiAktif)['c'] : 0;
$totalSkripsiAktif = $totalSkripsi;

// D. Kerja Praktek Berjalan (Status 'Aktif')
$qKpAktif = query("SELECT COUNT(*) AS c FROM kp WHERE (status = 'Aktif' OR status = 'aktif')");
$totalKp = $qKpAktif ? (int)mysqli_fetch_assoc($qKpAktif)['c'] : 0;
$totalKpAktif = $totalKp;

// E. Berkas Masuk Menunggu Validasi
$qBerkasPending = query("SELECT ((SELECT COUNT(*) FROM temp_skripsi WHERE status = 'menunggu') + (SELECT COUNT(*) FROM temp_kp WHERE status = 'menunggu')) AS c");
$totalBerkasPending = $qBerkasPending ? (int)mysqli_fetch_assoc($qBerkasPending)['c'] : 0;

// F. Total Dokumen Surat Penunjukkan
$qTotalSP = query("SELECT COUNT(*) AS c FROM surat_penunjukkan");
$totalSP = $qTotalSP ? (int)mysqli_fetch_assoc($qTotalSP)['c'] : 0;

// 2. Distribusi Akun Staf per Level Hak Akses (Tabel dosen)
$qLevelUser = query("SELECT level, COUNT(*) AS total FROM dosen GROUP BY level ORDER BY level DESC");
$distribusiLevel = [];
if ($qLevelUser) {
    while ($lu = mysqli_fetch_assoc($qLevelUser)) {
        $lvl = (int)$lu['level'];
        $label = match($lvl) {
            9 => 'Super Admin',
            6 => 'Pengelola Jurusan',
            5 => 'Ketua Program Studi (Kaprodi)',
            4 => 'Operator Jurusan',
            3 => 'Operator Program Studi',
            1 => 'Dosen Pengajar & Pembimbing',
            default => 'Staf / Pengguna Level ' . $lvl
        };
        $distribusiLevel[] = [
            'level' => $lvl,
            'label' => $label,
            'total' => (int)$lu['total']
        ];
    }
}

// 4. Inventaris Fasilitas & Master Data
$qRuang = query("SELECT COUNT(*) AS c FROM ruang_kuliah");
$totalRuang = $qRuang ? (int)mysqli_fetch_assoc($qRuang)['c'] : 0;

$qMatkul = query("SELECT COUNT(*) AS c FROM matakuliah");
$totalMatkul = $qMatkul ? (int)mysqli_fetch_assoc($qMatkul)['c'] : 0;

$qBatch = query("SELECT COUNT(*) AS c FROM batch_ujian");
$totalBatch = $qBatch ? (int)mysqli_fetch_assoc($qBatch)['c'] : 0;

// 5. Berita Jurusan
$beritaItems = [];
$qBerita = query("SELECT judul, deskripsi, tanggal FROM berita WHERE judul <> '' ORDER BY tanggal DESC LIMIT 3");
if ($qBerita) {
    while ($b = mysqli_fetch_assoc($qBerita)) { $beritaItems[] = $b; }
}

// 6. Hitung Total Data Manipulasi 6 Tabel Database
$qCntPKp = query("SELECT COUNT(*) AS c FROM progres_kp");
$cntPKp = $qCntPKp ? (int)mysqli_fetch_assoc($qCntPKp)['c'] : 0;

$qCntPSkr = query("SELECT COUNT(*) AS c FROM progres_skripsi");
$cntPSkr = $qCntPSkr ? (int)mysqli_fetch_assoc($qCntPSkr)['c'] : 0;

$qCntTKp = query("SELECT COUNT(*) AS c FROM temp_kp");
$cntTKp = $qCntTKp ? (int)mysqli_fetch_assoc($qCntTKp)['c'] : 0;

$qCntTSkr = query("SELECT COUNT(*) AS c FROM temp_skripsi");
$cntTSkr = $qCntTSkr ? (int)mysqli_fetch_assoc($qCntTSkr)['c'] : 0;

$qCntFmt = query("SELECT COUNT(*) AS c FROM format_nomor_surat");
$cntFmt = $qCntFmt ? (int)mysqli_fetch_assoc($qCntFmt)['c'] : 0;

$qCntNo = query("SELECT COUNT(*) AS c FROM nomor_surat");
$cntNo = $qCntNo ? (int)mysqli_fetch_assoc($qCntNo)['c'] : 0;
?>

<style>
  /* Standar Tombol Lingkaran Aksi SIATEK (.btn-circle) */
  .btn-circle {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 9999px;
    transition: all .15s ease;
  }
  .btn-circle .tip {
    position: absolute;
    bottom: 115%;
    left: 50%;
    transform: translateX(-50%);
    background: #0f172a;
    color: #fff;
    font-size: 11px;
    font-weight: 500;
    padding: 3px 8px;
    border-radius: 6px;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: opacity .15s ease;
    z-index: 40;
    pointer-events: none;
    box-shadow: 0 2px 6px rgba(15, 23, 42, .25);
  }
  .btn-circle:hover .tip {
    opacity: 1;
    visibility: visible;
  }
</style>

<!-- ============ PAGE HEADER SUPERADMIN ============ -->
<section class="flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3.5">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#1a365d] text-lg text-white shadow-sm">
            <i class="fas fa-screwdriver-wrench"></i>
        </div>
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold tracking-tight text-slate-800">
                    Pusat Kontrol Super Admin
                </h1>
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 border border-emerald-200 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Sistem Online
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Pengawasan Platform Terpadu &bull; Administrator: <strong class="text-slate-700"><?= htmlspecialchars($nmUser) ?></strong> &bull; <span class="font-medium text-slate-700"><?= htmlspecialchars($today_label) ?></span>
            </p>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="/redesain-siatek/administrator/pengguna-mahasiswa"
           class="inline-flex items-center gap-2 rounded-lg bg-[#f97316] hover:bg-[#ea580c] px-3.5 py-2 text-xs font-semibold text-white shadow-sm shadow-orange-500/25 transition">
            <i class="fas fa-users-gear"></i> Kelola Mahasiswa
        </a>
        <a href="/redesain-siatek/administrator/pengguna-dosen"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
            <i class="fas fa-user-tie text-[#1a365d]"></i> Kelola Dosen
        </a>
        <a href="/redesain-siatek/administrator/pengelolaan-broadcast"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
            <i class="fas fa-bullhorn text-orange-500"></i> Broadcast
        </a>
    </div>
</section>

<!-- ============ 4 KARTU METRIK SISTEM DENGAN 1 KARTU JANGKAR NAVY ============ -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- 1. KARTU JANGKAR NAVY: Mahasiswa Aktif Riil Jurusan -->
    <a href="/redesain-siatek/administrator/pengguna-mahasiswa"
       class="rounded-xl bg-[#1a365d] text-white p-5 shadow-md shadow-slate-900/10 flex flex-col justify-between relative overflow-hidden group hover:brightness-105 transition">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-white/5 pointer-events-none"></div>
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-widest text-orange-300">
                    Mahasiswa Aktif
                </span>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 text-orange-300 text-xs">
                    <i class="fas fa-users"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl sm:text-4xl font-bold font-mono tracking-tight text-white"><?= f($totalMhsAktif) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between text-xs">
            <span class="text-slate-300">3 Program Studi Jurusan</span>
            <span class="font-semibold text-orange-300 group-hover:underline">Kelola &rarr;</span>
        </div>
    </a>

    <!-- 2. Dosen Aktif Mengajar -->
    <a href="/redesain-siatek/administrator/pengguna-dosen"
       class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-orange-300 transition block group flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Dosen Aktif</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs group-hover:bg-[#1a365d] group-hover:text-white transition">
                    <i class="fas fa-chalkboard-user"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($totalDosenAktif) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Staf &amp; Pengajar Jurusan</span>
            <span class="font-semibold text-slate-700 group-hover:underline">Kelola &rarr;</span>
        </div>
    </a>

    <!-- 3. Skripsi Aktif Berjalan -->
    <a href="/redesain-siatek/administrator/mhs-skripsi"
       class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-orange-300 transition block group flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Skripsi Berjalan</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 text-orange-600 text-xs group-hover:bg-[#f97316] group-hover:text-white transition">
                    <i class="fas fa-book-open"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($totalSkripsiAktif) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Mahasiswa aktif bimbingan</span>
            <span class="font-semibold text-orange-600 group-hover:underline">Pantau &rarr;</span>
        </div>
    </a>

    <!-- 4. Antrean Berkas Menunggu Validasi -->
    <a href="/redesain-siatek/administrator/mhs-verifikasi"
       class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-orange-300 transition block group flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Berkas Masuk</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs group-hover:bg-[#1a365d] group-hover:text-white transition">
                    <i class="fas fa-clipboard-check"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($totalBerkasPending) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Menunggu verifikasi berkas</span>
            <span class="font-semibold text-slate-700 group-hover:underline">Periksa &rarr;</span>
        </div>
    </a>
</section>

<!-- ============ BENTO WORKSPACE 8 : 4 ============ -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

    <!-- ===== KOLOM KIRI (8 KOLOM / 66%): ADMINISTRASI PENGGUNA & KESEHATAN SISTEM ===== -->
    <div class="lg:col-span-8 space-y-5">

        <!-- 1. Distribusi Akun Berdasarkan Tingkat Hak Akses (User Level) -->
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-3.5 bg-slate-50/70">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#1a365d] text-white text-xs">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">
                            Distribusi Hak Akses Staf &amp; Operator
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Struktur kewenangan pengguna terdaftar di tabel sistem</p>
                    </div>
                </div>
                <span class="text-xs font-semibold text-slate-500"><?= count($distribusiLevel) ?> Tingkat Akses</span>
            </div>

            <div class="divide-y divide-slate-100">
                <?php foreach ($distribusiLevel as $dl): ?>
                <div class="p-3.5 px-5 hover:bg-slate-50/80 transition flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md font-mono text-xs font-bold bg-slate-100 text-slate-700">
                            <?= $dl['level'] ?>
                        </span>
                        <div>
                            <h4 class="text-xs font-bold text-slate-800"><?= htmlspecialchars($dl['label']) ?></h4>
                            <p class="text-[11px] text-slate-400">Level Wewenang <?= $dl['level'] ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-slate-800 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-md">
                            <?= $dl['total'] ?> Akun
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 2. Matriks Modul & Volume Data Inti Sistem -->
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-800">Integritas Basis Data &amp; Modul Akademik</h3>
                <p class="text-xs text-slate-500 mt-0.5">Statistik volume catatan data operasional yang tersimpan</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 text-center">
                    <span class="text-[11px] font-semibold text-slate-400 block uppercase">Skripsi Terdaftar</span>
                    <strong class="text-xl font-bold font-mono text-slate-800 mt-1 block"><?= f($totalSkripsi) ?></strong>
                </div>

                <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 text-center">
                    <span class="text-[11px] font-semibold text-slate-400 block uppercase">KP Terdaftar</span>
                    <strong class="text-xl font-bold font-mono text-slate-800 mt-1 block"><?= f($totalKp) ?></strong>
                </div>

                <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 text-center">
                    <span class="text-[11px] font-semibold text-slate-400 block uppercase">Surat SK Terbit</span>
                    <strong class="text-xl font-bold font-mono text-slate-800 mt-1 block"><?= f($totalSP) ?></strong>
                </div>

                <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 text-center">
                    <span class="text-[11px] font-semibold text-slate-400 block uppercase">Batch Ujian</span>
                    <strong class="text-xl font-bold font-mono text-slate-800 mt-1 block"><?= f($totalBatch) ?></strong>
                </div>
            </div>
        </section>

    </div>

    <!-- ===== KOLOM KANAN (4 KOLOM / 34%): PINTASAN MASTER & FASILITAS ===== -->
    <div class="lg:col-span-4 space-y-5">

        <!-- 1. Master Administrasi Sistem -->
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Tata Kelola Master Data</h3>
            <div class="space-y-2">
                <a href="/redesain-siatek/administrator/pengguna-mahasiswa" class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-orange-200 hover:bg-orange-50/40 transition group">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs shrink-0"><i class="fas fa-users-gear"></i></span>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-semibold text-slate-800 group-hover:text-orange-600 transition">Pengguna Mahasiswa</h4>
                        <p class="text-[11px] text-slate-400 truncate">Reset sandi, status aktif &amp; angkatan.</p>
                    </div>
                </a>

                <a href="/redesain-siatek/administrator/pengguna-dosen" class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-orange-200 hover:bg-orange-50/40 transition group">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs shrink-0"><i class="fas fa-user-tie"></i></span>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-semibold text-slate-800 group-hover:text-orange-600 transition">Pengguna Dosen</h4>
                        <p class="text-[11px] text-slate-400 truncate">NIP, NIDN &amp; penugasan pembimbing.</p>
                    </div>
                </a>

                <a href="/redesain-siatek/administrator/pengelolaan-broadcast" class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-orange-200 hover:bg-orange-50/40 transition group">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 text-orange-600 text-xs shrink-0"><i class="fas fa-bullhorn"></i></span>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-semibold text-slate-800 group-hover:text-orange-600 transition">Broadcast Sistem</h4>
                        <p class="text-[11px] text-slate-400 truncate">Kirim pengumuman massal.</p>
                    </div>
                </a>
            </div>
        </section>

        <!-- 2. Fasilitas Kampus & Kurikulum -->
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Fasilitas &amp; Kurikulum</h3>
            <div class="space-y-2.5">
                <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100 text-xs">
                    <span class="text-slate-600 flex items-center gap-2">
                        <i class="fas fa-door-open text-orange-500"></i> Ruang &amp; Laboratorium
                    </span>
                    <strong class="font-mono font-bold text-slate-800"><?= f($totalRuang) ?> Ruang</strong>
                </div>

                <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100 text-xs">
                    <span class="text-slate-600 flex items-center gap-2">
                        <i class="fas fa-book-bookmark text-[#1a365d]"></i> Matakuliah Terdata
                    </span>
                    <strong class="font-mono font-bold text-slate-800"><?= f($totalMatkul) ?> MK</strong>
                </div>
            </div>
        </section>

        <!-- 3. Berita & Informasi Jurusan -->
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-slate-800">Berita Jurusan</h3>
                <a href="/redesain-siatek/administrator/jurusan-berita" class="text-xs font-semibold text-orange-600 hover:underline">Semua &rarr;</a>
            </div>
            <?php if (count($beritaItems) === 0): ?>
                <p class="text-xs text-slate-400">Tidak ada pengumuman terbaru.</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($beritaItems as $b): ?>
                    <a href="/redesain-siatek/administrator/jurusan-berita" class="block group">
                        <h4 class="text-xs font-semibold text-slate-800 group-hover:text-orange-600 transition line-clamp-2"><?= htmlspecialchars($b['judul']) ?></h4>
                        <span class="text-[10px] text-slate-400 mt-1 inline-block"><?= htmlspecialchars($b['tanggal']) ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </div>
</div>

<!-- ========================================================================= -->
<!-- PANEL MANIPULASI TABEL DATABASE (KHUSUS SUPER ADMIN)                     -->
<!-- Tabel: progres_kp, progres_skripsi, temp_kp, temp_skripsi,                -->
<!--        format_nomor_surat, nomor_surat                                    -->
<!-- ========================================================================= -->
<section class="mt-8 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" id="section-db-manipulator">
    <!-- Header Panel Utama -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 bg-slate-900 px-6 py-4 text-white">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-500 text-white shadow-sm">
                <i class="fas fa-database text-lg"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold tracking-tight">Pusat Manipulasi Tabel Basis Data</h2>
                    <span class="inline-flex items-center rounded-full bg-orange-500/20 px-2.5 py-0.5 text-[10px] font-bold text-orange-300 border border-orange-500/30 uppercase tracking-wider">
                        Superadmin Tool
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">
                    Kelola, telusuri, perbaiki, tambah, dan hapus data langsung pada 6 tabel operasional inti SIATEK.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" id="btnRefreshTabelDb" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 px-3.5 py-2 text-xs font-semibold text-slate-200 border border-slate-700 transition">
                <i class="fas fa-rotate"></i> Refresh Data
            </button>
            <button type="button" id="btnTambahRecordDb" class="inline-flex items-center gap-1.5 rounded-lg bg-[#f97316] hover:bg-[#ea580c] px-4 py-2 text-xs font-bold text-white shadow-sm shadow-orange-500/30 transition">
                <i class="fas fa-plus"></i> Tambah Record
            </button>
        </div>
    </div>

    <!-- Tab Selector 6 Tabel -->
    <div class="flex border-b border-slate-200 bg-slate-50/80 px-6 pt-3 overflow-x-auto gap-2 text-xs">
        <button type="button" class="tab-db-btn active inline-flex items-center gap-2 rounded-t-xl border-t-2 border-orange-500 bg-white px-4 py-2.5 font-bold text-slate-800 shadow-xs transition" data-tabel="progres_kp">
            <i class="fas fa-bars-progress text-orange-600"></i>
            <span>progres_kp</span>
            <span class="badge-cnt-tbl rounded-full bg-slate-100 px-2 py-0.5 font-mono text-[10px] text-slate-700 font-bold" id="badgeCnt_progres_kp"><?= f($cntPKp) ?></span>
        </button>

        <button type="button" class="tab-db-btn inline-flex items-center gap-2 rounded-t-xl border-t-2 border-transparent bg-transparent hover:bg-white/60 px-4 py-2.5 font-semibold text-slate-500 transition" data-tabel="progres_skripsi">
            <i class="fas fa-diagram-project text-[#1a365d]"></i>
            <span>progres_skripsi</span>
            <span class="badge-cnt-tbl rounded-full bg-slate-200/80 px-2 py-0.5 font-mono text-[10px] text-slate-600" id="badgeCnt_progres_skripsi"><?= f($cntPSkr) ?></span>
        </button>

        <button type="button" class="tab-db-btn inline-flex items-center gap-2 rounded-t-xl border-t-2 border-transparent bg-transparent hover:bg-white/60 px-4 py-2.5 font-semibold text-slate-500 transition" data-tabel="temp_kp">
            <i class="fas fa-file-arrow-up text-amber-600"></i>
            <span>temp_kp</span>
            <span class="badge-cnt-tbl rounded-full bg-slate-200/80 px-2 py-0.5 font-mono text-[10px] text-slate-600" id="badgeCnt_temp_kp"><?= f($cntTKp) ?></span>
        </button>

        <button type="button" class="tab-db-btn inline-flex items-center gap-2 rounded-t-xl border-t-2 border-transparent bg-transparent hover:bg-white/60 px-4 py-2.5 font-semibold text-slate-500 transition" data-tabel="temp_skripsi">
            <i class="fas fa-file-circle-check text-sky-600"></i>
            <span>temp_skripsi</span>
            <span class="badge-cnt-tbl rounded-full bg-slate-200/80 px-2 py-0.5 font-mono text-[10px] text-slate-600" id="badgeCnt_temp_skripsi"><?= f($cntTSkr) ?></span>
        </button>

        <button type="button" class="tab-db-btn inline-flex items-center gap-2 rounded-t-xl border-t-2 border-transparent bg-transparent hover:bg-white/60 px-4 py-2.5 font-semibold text-slate-500 transition" data-tabel="format_nomor_surat">
            <i class="fas fa-hashtag text-indigo-600"></i>
            <span>format_nomor_surat</span>
            <span class="badge-cnt-tbl rounded-full bg-slate-200/80 px-2 py-0.5 font-mono text-[10px] text-slate-600" id="badgeCnt_format_nomor_surat"><?= f($cntFmt) ?></span>
        </button>

        <button type="button" class="tab-db-btn inline-flex items-center gap-2 rounded-t-xl border-t-2 border-transparent bg-transparent hover:bg-white/60 px-4 py-2.5 font-semibold text-slate-500 transition" data-tabel="nomor_surat">
            <i class="fas fa-envelope-open-text text-emerald-600"></i>
            <span>nomor_surat</span>
            <span class="badge-cnt-tbl rounded-full bg-slate-200/80 px-2 py-0.5 font-mono text-[10px] text-slate-600" id="badgeCnt_nomor_surat"><?= f($cntNo) ?></span>
        </button>
    </div>

    <!-- Filter & Toolbar Sub-Pencarian -->
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-white px-5 py-3.5 text-xs">
        <div class="flex flex-1 items-center gap-2 max-w-md">
            <div class="relative w-full">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="search" id="inputCariDb" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 pl-9 text-xs text-slate-800 placeholder-slate-400 outline-none focus:border-orange-400 focus:bg-white transition" placeholder="Cari data dalam tabel aktif...">
            </div>
            <button type="button" id="btnCariDb" class="rounded-lg bg-slate-800 hover:bg-slate-700 px-4 py-2 font-semibold text-white transition shrink-0">
                Cari
            </button>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1.5 text-slate-500">
                <span>Baris:</span>
                <select id="selectLimitDb" class="rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs font-semibold text-slate-700 outline-none focus:border-orange-400">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <span class="text-slate-300">&bull;</span>
            <span id="infoTotalRecordDb" class="font-mono font-semibold text-slate-600">Memuat data...</span>
        </div>
    </div>

    <!-- Container Tabel Data Sesuai Style Bimbingan Skripsi -->
    <div class="overflow-x-auto min-h-[320px] relative" id="containerTabelDb">
        <div id="loaderTabelDb" class="hidden absolute inset-0 bg-white/70 backdrop-blur-[1px] flex items-center justify-center z-20">
            <div class="flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white shadow-xl">
                <i class="fas fa-spinner fa-spin text-orange-400"></i> Memuat data tabel...
            </div>
        </div>

        <table class="w-full text-left text-sm" id="tabelDbManipulator">
            <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white" id="theadDb">
                <!-- Diisi dinamis via JS -->
            </thead>
            <tbody class="divide-y divide-slate-100" id="tbodyDb">
                <tr>
                    <td colspan="7" class="py-12 text-center text-slate-400">
                        <i class="fas fa-spinner fa-spin text-lg mb-2 block text-orange-500"></i> Mempersiapkan tabel...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 bg-slate-50/50 px-5 py-3.5 text-xs" id="footerPaginasiDb">
        <div class="text-slate-500" id="labelPaginasiDb">
            Menampilkan 0 - 0 dari 0 baris
        </div>
        <div class="flex items-center gap-1.5" id="navPaginasiDb">
            <!-- Tombol Prev, Next, Nomor Hal diisi dinamis via JS -->
        </div>
    </div>
</section>

<!-- ========================================================================= -->
<!-- MODAL MANIPULASI RECORD (TAMBAH / EDIT DINAMIS)                          -->
<!-- ========================================================================= -->
<div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden" id="modalDbManipulator" role="dialog" aria-modal="true">
    <div class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-200 transform transition-all">
        <!-- Header Modal -->
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-900 px-6 py-4 text-white">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-white text-xs">
                    <i class="fas fa-pen-to-square"></i>
                </span>
                <div>
                    <h3 class="text-sm font-bold tracking-tight" id="modalDbTitle">Manipulasi Record</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5" id="modalDbSubtitle">Tabel: progres_kp</p>
                </div>
            </div>
            <button type="button" class="text-slate-400 hover:text-white text-lg transition leading-none" id="btnCloseModalDb">
                &times;
            </button>
        </div>

        <!-- Form Body Modal -->
        <form id="formDbManipulator" class="p-6 space-y-4 text-xs">
            <input type="hidden" id="formDbTabel" name="tabel" value="">
            <input type="hidden" id="formDbMode" name="mode" value="tambah">
            <input type="hidden" id="formDbIdKey" name="id_key" value="">

            <div id="dynamicFormFields" class="space-y-4">
                <!-- Field input dirender dinamis sesuai tabel yang sedang aktif -->
            </div>

            <!-- Footer Tombol Modal -->
            <div class="mt-6 flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" class="rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-4 py-2 font-semibold text-slate-600 transition" id="btnBatalModalDb">
                    Batal
                </button>
                <button type="submit" id="btnSubmitModalDb" class="inline-flex items-center gap-1.5 rounded-lg bg-[#f97316] hover:bg-[#ea580c] px-4 py-2 font-bold text-white shadow-sm shadow-orange-500/30 transition">
                    <i class="fas fa-floppy-disk"></i> Simpan Record
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- TOAST NOTIFIKASI ELEGAN                                                  -->
<!-- ========================================================================= -->
<div id="toastDbContainer" class="fixed top-6 right-6 z-50 flex flex-col gap-2 pointer-events-none"></div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT CONTROLLER MANIPULASI TABEL DATABASE                          -->
<!-- ========================================================================= -->
<script>
(function($) {
    'use strict';

    var PROSES_DB_URL = '<?= base_url() ?>/administrator/pages/beranda/proses.php';
    var state = {
        tabel: 'progres_kp',
        page: 1,
        limit: 10,
        cari: '',
        master: null
    };

    // Helper Toast
    function toastDb(pesan, jenis) {
        jenis = jenis || 'success';
        var bgClass = jenis === 'success' ? 'bg-emerald-600' : (jenis === 'error' ? 'bg-rose-600' : 'bg-slate-800');
        var icon = jenis === 'success' ? 'fa-circle-check' : (jenis === 'error' ? 'fa-triangle-exclamation' : 'fa-info-circle');
        
        var $t = $(
            '<div class="pointer-events-auto flex items-center gap-2.5 rounded-xl ' + bgClass + ' px-4 py-3 text-xs font-semibold text-white shadow-xl transition-all duration-300 transform translate-x-12 opacity-0">' +
                '<i class="fas ' + icon + ' text-sm"></i>' +
                '<span>' + pesan + '</span>' +
            '</div>'
        );

        $('#toastDbContainer').append($t);
        setTimeout(function() {
            $t.removeClass('translate-x-12 opacity-0');
        }, 10);

        setTimeout(function() {
            $t.addClass('translate-x-12 opacity-0');
            setTimeout(function() { $t.remove(); }, 350);
        }, 3500);
    }

    // Format Tanggal Unix Timestamp
    function formatTimeUnix(ts) {
        if (!ts || isNaN(ts) || parseInt(ts) <= 0) return '-';
        var d = new Date(parseInt(ts) * 1000);
        var pad = function(n) { return n < 10 ? '0' + n : n; };
        return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
    }

    function formatTimeInput(ts) {
        if (!ts || isNaN(ts) || parseInt(ts) <= 0) {
            var now = new Date();
            var pad = function(n) { return n < 10 ? '0' + n : n; };
            return now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + 'T' + pad(now.getHours()) + ':' + pad(now.getMinutes());
        }
        var d = new Date(parseInt(ts) * 1000);
        var pad = function(n) { return n < 10 ? '0' + n : n; };
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()) + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes());
    }

    // 1. Ambil Master Lookup (SOP & Syarat Berkas)
    function muatMasterLookup(callback) {
        if (state.master) {
            if (callback) callback();
            return;
        }
        $.ajax({
            url: PROSES_DB_URL,
            type: 'POST',
            dataType: 'json',
            data: { action: 'ambil-master-lookup' },
            success: function(res) {
                if (res && res.status) {
                    state.master = res;
                    if (callback) callback();
                }
            }
        });
    }

    // 2. Ambil & Perbarui Badge Total Record 6 Tabel
    function muatRingkasanTabel() {
        $.ajax({
            url: PROSES_DB_URL,
            type: 'POST',
            dataType: 'json',
            data: { action: 'ambil-ringkasan-tabel' },
            success: function(res) {
                if (res && res.status && res.data) {
                    $.each(res.data, function(tbl, cnt) {
                        $('#badgeCnt_' + tbl).text(new Intl.NumberFormat('id-ID').format(cnt));
                    });
                }
            }
        });
    }

    // Helper escape HTML
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // 3. Render Kolom Header (THEAD) sesuai tabel & gaya konsisten SIATEK
    function renderThead(tbl) {
        var html = '<tr>';
        switch (tbl) {
            case 'progres_kp':
            case 'progres_skripsi':
                html += '<th class="py-3.5 px-5 font-semibold uppercase tracking-wider">Mahasiswa</th>';
                html += '<th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tahapan SOP</th>';
                html += '<th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Waktu Selesai</th>';
                html += '<th class="py-3.5 px-5 font-semibold uppercase tracking-wider text-center">Aksi</th>';
                break;
            case 'temp_kp':
            case 'temp_skripsi':
                html += '<th class="py-3.5 px-5 font-semibold uppercase tracking-wider">Mahasiswa</th>';
                html += '<th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Persyaratan Berkas</th>';
                html += '<th class="py-3.5 px-4 font-semibold uppercase tracking-wider text-center">Status Validasi</th>';
                html += '<th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Catatan / Keterangan</th>';
                html += '<th class="py-3.5 px-5 font-semibold uppercase tracking-wider text-center">Aksi</th>';
                break;
            case 'format_nomor_surat':
                html += '<th class="py-3.5 px-5 font-semibold uppercase tracking-wider w-28">ID</th>';
                html += '<th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Nama Surat / Kategori</th>';
                html += '<th class="py-3.5 px-6 font-semibold uppercase tracking-wider">Format Penomoran</th>';
                html += '<th class="py-3.5 px-5 font-semibold uppercase tracking-wider text-center w-28">Aksi</th>';
                break;
            case 'nomor_surat':
                html += '<th class="py-3.5 px-5 font-semibold uppercase tracking-wider w-28">ID</th>';
                html += '<th class="py-3.5 px-3 font-semibold uppercase tracking-wider text-center w-24">Urutan</th>';
                html += '<th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Nomor Terbit</th>';
                html += '<th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tanggal</th>';
                html += '<th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Keterangan</th>';
                html += '<th class="py-3.5 px-5 font-semibold uppercase tracking-wider text-center w-28">Aksi</th>';
                break;
        }
        html += '</tr>';
        $('#theadDb').html(html);
    }

    // 4. Render Baris Body (TBODY) seragam dengan konsep warna, tipografi, dan .btn-circle SIATEK
    function renderTbody(tbl, rows) {
        var colCount = (tbl.indexOf('temp') !== -1) ? 5 : (tbl === 'nomor_surat' ? 6 : 4);
        if (!rows || rows.length === 0) {
            $('#tbodyDb').html(
                '<tr>' +
                '  <td colspan="' + colCount + '" class="py-12 text-center">' +
                '    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">' +
                '      <i class="fas fa-database text-lg"></i>' +
                '    </div>' +
                '    <p class="text-sm font-semibold text-slate-700">Tidak ada data ditemukan</p>' +
                '    <p class="text-xs text-slate-400 mt-1">Belum ada rekaman pada tabel <strong class="text-slate-600">' + escapeHtml(tbl) + '</strong></p>' +
                '  </td>' +
                '</tr>'
            );
            return;
        }

        var startRow = (state.page - 1) * state.limit + 1;
        var html = '';
        $.each(rows, function(i, r) {
            var rowNo = startRow + i;
            var trClass = "transition hover:bg-orange-50/60 " + ((i % 2 === 0) ? "bg-white" : "bg-slate-50/50");
            html += '<tr class="' + trClass + '" data-row-json=\'' + JSON.stringify(r).replace(/'/g, "&apos;") + '\'>';
            switch (tbl) {
                case 'progres_kp':
                case 'progres_skripsi':
                    var compositeId = (r.mahasiswa || '') + '|' + (r.proses || '') + '|' + (r.waktu || '');
                    var namaMhs = r.nama_mhs ? escapeHtml(r.nama_mhs) : 'Mahasiswa Terdaftar';
                    var nimMhs = escapeHtml(r.mahasiswa || '-');
                    var prodiNm = r.nama_prodi || r.prodi || '';

                    html += '<td class="py-3.5 pl-5 pr-4 align-top">';
                    html +=   '<div class="flex items-start gap-3">';
                    html +=     '<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-xs font-bold text-white shadow-sm font-mono">' + rowNo + '</span>';
                    html +=     '<div class="min-w-0">';
                    html +=       '<p class="font-bold text-slate-800 leading-snug text-sm">' + namaMhs + '</p>';
                    html +=       '<div class="flex flex-wrap items-center gap-1.5 mt-0.5 text-xs text-slate-500">';
                    html +=         '<span class="font-mono font-semibold text-slate-700">' + nimMhs + '</span>';
                    if (prodiNm) {
                        html +=     '<span class="text-slate-300">&bull;</span>';
                        html +=     '<span class="text-slate-500 font-medium">' + escapeHtml(prodiNm) + '</span>';
                    }
                    html +=       '</div>';
                    html +=     '</div>';
                    html +=   '</div>';
                    html += '</td>';

                    html += '<td class="px-4 py-3.5 align-top">';
                    html +=   '<div class="flex flex-col gap-1">';
                    html +=     '<span class="inline-flex w-fit items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-800 border border-orange-200 font-mono">';
                    html +=       '<i class="fas fa-layer-group text-[10px]"></i> SOP ' + escapeHtml(r.proses);
                    html +=     '</span>';
                    html +=     '<span class="text-xs font-semibold text-slate-700 leading-snug line-clamp-2 mt-0.5" title="' + escapeHtml(r.nama_proses || '') + '">';
                    html +=       escapeHtml(r.nama_proses || ('Langkah SOP #' + r.proses));
                    html +=     '</span>';
                    html +=   '</div>';
                    html += '</td>';

                    html += '<td class="px-4 py-3.5 align-top text-xs font-mono font-semibold text-slate-600">';
                    html +=   '<div class="flex items-center gap-1.5">';
                    html +=     '<i class="far fa-clock text-slate-400 text-xs"></i>';
                    html +=     '<span>' + escapeHtml(r.waktu_format || formatTimeUnix(r.waktu)) + '</span>';
                    html +=   '</div>';
                    html += '</td>';

                    html += '<td class="py-3.5 px-5 align-top text-center whitespace-nowrap">';
                    html +=   '<div class="flex items-center justify-center gap-1.5">';
                    html +=     '<button type="button" class="btn-edit-row btn-circle bg-sky-500 text-white shadow-xs hover:bg-sky-600 hover:scale-105 transition" data-idkey="' + escapeHtml(compositeId) + '" title="Edit Record"><i class="fas fa-pencil-alt text-xs"></i><span class="tip">Edit Record</span></button>';
                    html +=     '<button type="button" class="btn-delete-row btn-circle bg-rose-500 text-white shadow-xs hover:bg-rose-600 hover:scale-105 transition" data-idkey="' + escapeHtml(compositeId) + '" title="Hapus Record"><i class="fas fa-trash-alt text-xs"></i><span class="tip">Hapus Record</span></button>';
                    html +=   '</div>';
                    html += '</td>';
                    break;

                case 'temp_kp':
                case 'temp_skripsi':
                    var stBadge = '';
                    var stRaw = String(r.status || 'menunggu').toLowerCase();
                    if (stRaw === 'diterima') {
                        stBadge = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200"><i class="fas fa-check-circle text-[10px]"></i> Diterima</span>';
                    } else if (stRaw === 'ditolak') {
                        stBadge = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200"><i class="fas fa-times-circle text-[10px]"></i> Ditolak</span>';
                    } else {
                        stBadge = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200"><i class="fas fa-clock text-[10px]"></i> Menunggu</span>';
                    }

                    var namaMhsTk = r.nama_mhs ? escapeHtml(r.nama_mhs) : 'Mahasiswa Terdaftar';
                    var nimMhsTk = escapeHtml(r.nim || '-');
                    var prodiNmTk = r.nama_prodi || r.prodi || '';

                    html += '<td class="py-3.5 pl-5 pr-4 align-top">';
                    html +=   '<div class="flex items-start gap-3">';
                    html +=     '<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-xs font-bold text-white shadow-sm font-mono">' + rowNo + '</span>';
                    html +=     '<div class="min-w-0">';
                    html +=       '<p class="font-bold text-slate-800 leading-snug text-sm">' + namaMhsTk + '</p>';
                    html +=       '<div class="flex flex-wrap items-center gap-1.5 mt-0.5 text-xs text-slate-500">';
                    html +=         '<span class="font-mono font-semibold text-slate-700">' + nimMhsTk + '</span>';
                    if (prodiNmTk) {
                        html +=     '<span class="text-slate-300">&bull;</span>';
                        html +=     '<span class="text-slate-500 font-medium">' + escapeHtml(prodiNmTk) + '</span>';
                    }
                    html +=         '<span class="text-slate-300">&bull;</span>';
                    html +=         '<span class="font-mono text-[10px] text-slate-500 bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded font-bold">ID #' + r.id + '</span>';
                    html +=       '</div>';
                    html +=     '</div>';
                    html +=   '</div>';
                    html += '</td>';

                    html += '<td class="px-4 py-3.5 align-top">';
                    html +=   '<p class="text-xs font-bold text-slate-800 leading-snug">' + escapeHtml(r.nama_berkas || ('Persyaratan #' + r.id_persyaratan)) + '</p>';
                    html +=   '<div class="flex items-center gap-2 mt-1 text-[11px] text-slate-400">';
                    html +=     '<span>Tahap SOP: <strong class="text-slate-700 font-mono font-bold">' + escapeHtml(r.syarat_progres || '-') + '</strong></span>';
                    if (r.file) {
                        html += '&bull; <span class="font-mono text-slate-500 font-medium"><i class="fas fa-paperclip mr-0.5"></i> File #' + escapeHtml(r.file) + '</span>';
                    }
                    html +=   '</div>';
                    html += '</td>';

                    html += '<td class="px-4 py-3.5 align-top text-center">' + stBadge + '</td>';

                    html += '<td class="px-4 py-3.5 align-top">';
                    html +=   '<p class="text-xs text-slate-600 line-clamp-2" title="' + escapeHtml(r.ket || '-') + '">' + escapeHtml(r.ket || '-') + '</p>';
                    html += '</td>';

                    html += '<td class="py-3.5 px-5 align-top text-center whitespace-nowrap">';
                    html +=   '<div class="flex items-center justify-center gap-1.5">';
                    html +=     '<button type="button" class="btn-edit-row btn-circle bg-sky-500 text-white shadow-xs hover:bg-sky-600 hover:scale-105 transition" data-idkey="' + r.id + '" title="Edit Record"><i class="fas fa-pencil-alt text-xs"></i><span class="tip">Edit Record</span></button>';
                    html +=     '<button type="button" class="btn-delete-row btn-circle bg-rose-500 text-white shadow-xs hover:bg-rose-600 hover:scale-105 transition" data-idkey="' + r.id + '" title="Hapus Record"><i class="fas fa-trash-alt text-xs"></i><span class="tip">Hapus Record</span></button>';
                    html +=   '</div>';
                    html += '</td>';
                    break;

                case 'format_nomor_surat':
                    html += '<td class="py-3.5 pl-5 pr-4 align-top">';
                    html +=   '<div class="flex items-start gap-3">';
                    html +=     '<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#1a365d] text-xs font-bold text-white shadow-sm font-mono">' + rowNo + '</span>';
                    html +=     '<div class="min-w-0">';
                    html +=       '<span class="font-mono font-bold text-slate-700 text-xs bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">ID #' + r.id + '</span>';
                    html +=     '</div>';
                    html +=   '</div>';
                    html += '</td>';

                    html += '<td class="px-4 py-3.5 align-top">';
                    html +=   '<p class="font-bold text-slate-800 text-sm leading-snug">' + escapeHtml(r.nama_surat) + '</p>';
                    html +=   '<span class="text-[11px] text-slate-400 mt-0.5 block">Format Penomoran Dokumen Resmi</span>';
                    html += '</td>';

                    html += '<td class="px-6 py-3.5 align-top">';
                    html +=   '<span class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 border border-indigo-200 px-3 py-1.5 font-mono text-xs font-bold text-indigo-700 shadow-2xs">';
                    html +=     '<i class="fas fa-hashtag text-[10px] text-indigo-400"></i> ' + escapeHtml(r.format_nomor);
                    html +=   '</span>';
                    html += '</td>';

                    html += '<td class="py-3.5 px-5 align-top text-center whitespace-nowrap">';
                    html +=   '<div class="flex items-center justify-center gap-1.5">';
                    html +=     '<button type="button" class="btn-edit-row btn-circle bg-sky-500 text-white shadow-xs hover:bg-sky-600 hover:scale-105 transition" data-idkey="' + r.id + '" title="Edit Record"><i class="fas fa-pencil-alt text-xs"></i><span class="tip">Edit Record</span></button>';
                    html +=     '<button type="button" class="btn-delete-row btn-circle bg-rose-500 text-white shadow-xs hover:bg-rose-600 hover:scale-105 transition" data-idkey="' + r.id + '" title="Hapus Record"><i class="fas fa-trash-alt text-xs"></i><span class="tip">Hapus Record</span></button>';
                    html +=   '</div>';
                    html += '</td>';
                    break;

                case 'nomor_surat':
                    html += '<td class="py-3.5 pl-5 pr-4 align-top">';
                    html +=   '<div class="flex items-start gap-3">';
                    html +=     '<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#1a365d] text-xs font-bold text-white shadow-sm font-mono">' + rowNo + '</span>';
                    html +=     '<div class="min-w-0">';
                    html +=       '<span class="font-mono font-bold text-slate-700 text-xs bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">ID #' + r.id + '</span>';
                    html +=     '</div>';
                    html +=   '</div>';
                    html += '</td>';

                    html += '<td class="px-3 py-3.5 align-top text-center">';
                    html +=   '<span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md font-mono font-bold text-xs bg-slate-100 text-slate-700 border border-slate-200">#' + escapeHtml(r.urutan || '-') + '</span>';
                    html += '</td>';

                    html += '<td class="px-4 py-3.5 align-top">';
                    html +=   '<span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 border border-emerald-200 px-3 py-1.5 font-mono text-xs font-bold text-emerald-800 shadow-2xs">';
                    html +=     '<i class="fas fa-envelope-open-text text-[10px] text-emerald-500"></i> ' + escapeHtml(r.nomor || '-');
                    html +=   '</span>';
                    html += '</td>';

                    html += '<td class="px-4 py-3.5 align-top text-slate-600 text-xs whitespace-nowrap font-medium">';
                    html +=   '<div class="flex items-center gap-1.5"><i class="far fa-calendar-alt text-slate-400 text-xs"></i> <span>' + escapeHtml(r.tanggal || '-') + '</span></div>';
                    html += '</td>';

                    html += '<td class="px-4 py-3.5 align-top">';
                    html +=   '<p class="text-xs text-slate-600 line-clamp-2" title="' + escapeHtml(r.keterangan || '-') + '">' + escapeHtml(r.keterangan || '-') + '</p>';
                    html += '</td>';

                    html += '<td class="py-3.5 px-5 align-top text-center whitespace-nowrap">';
                    html +=   '<div class="flex items-center justify-center gap-1.5">';
                    html +=     '<button type="button" class="btn-edit-row btn-circle bg-sky-500 text-white shadow-xs hover:bg-sky-600 hover:scale-105 transition" data-idkey="' + r.id + '" title="Edit Record"><i class="fas fa-pencil-alt text-xs"></i><span class="tip">Edit Record</span></button>';
                    html +=     '<button type="button" class="btn-delete-row btn-circle bg-rose-500 text-white shadow-xs hover:bg-rose-600 hover:scale-105 transition" data-idkey="' + r.id + '" title="Hapus Record"><i class="fas fa-trash-alt text-xs"></i><span class="tip">Hapus Record</span></button>';
                    html +=   '</div>';
                    html += '</td>';
                    break;
            }
            html += '</tr>';
        });

        $('#tbodyDb').html(html);
    }

    // 5. Render Paginasi
    function renderPaginasi(total, page, limit) {
        var totalPages = Math.ceil(total / limit) || 1;
        var startRow = total === 0 ? 0 : (page - 1) * limit + 1;
        var endRow = Math.min(page * limit, total);

        $('#labelPaginasiDb').html('Menampilkan <strong>' + startRow + ' - ' + endRow + '</strong> dari <strong>' + new Intl.NumberFormat('id-ID').format(total) + '</strong> baris');
        $('#infoTotalRecordDb').text(new Intl.NumberFormat('id-ID').format(total) + ' Catatan');

        var navHtml = '';
        navHtml += '<button type="button" class="btn-page-db rounded px-2.5 py-1 text-xs border border-slate-200 ' + (page <= 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-100') + '" data-page="' + (page - 1) + '" ' + (page <= 1 ? 'disabled' : '') + '>&laquo; Prev</button>';

        var startP = Math.max(1, page - 2);
        var endP = Math.min(totalPages, page + 2);
        for (var p = startP; p <= endP; p++) {
            var activeClass = p === page ? 'bg-orange-500 text-white font-bold border-orange-500' : 'bg-white hover:bg-slate-100 border-slate-200 text-slate-700';
            navHtml += '<button type="button" class="btn-page-db rounded px-2.5 py-1 text-xs border ' + activeClass + '" data-page="' + p + '">' + p + '</button>';
        }

        navHtml += '<button type="button" class="btn-page-db rounded px-2.5 py-1 text-xs border border-slate-200 ' + (page >= totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-100') + '" data-page="' + (page + 1) + '" ' + (page >= totalPages ? 'disabled' : '') + '>Next &raquo;</button>';

        $('#navPaginasiDb').html(navHtml);
    }

    // 6. Muat Data Tabel Utama via AJAX
    function muatDataTabelDb() {
        $('#loaderTabelDb').removeClass('hidden');
        renderThead(state.tabel);

        $.ajax({
            url: PROSES_DB_URL,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'ambil-data-tabel',
                tabel: state.tabel,
                cari: state.cari,
                page: state.page,
                limit: state.limit
            },
            success: function(res) {
                $('#loaderTabelDb').addClass('hidden');
                if (res && res.status) {
                    renderTbody(state.tabel, res.rows);
                    renderPaginasi(res.total, res.page, res.limit);
                } else {
                    toastDb(res ? res.pesan : 'Gagal memuat data', 'error');
                }
            },
            error: function(xhr) {
                $('#loaderTabelDb').addClass('hidden');
                toastDb('Terjadi kesalahan koneksi server (' + xhr.status + ')', 'error');
            }
        });
    }

    // 7. Bangun Field Formulir Modal Dinamis sesuai tabel
    function bangunFormModal(tbl, mode, row) {
        mode = mode || 'tambah';
        row = row || {};
        var html = '';

        $('#formDbTabel').val(tbl);
        $('#formDbMode').val(mode);
        
        var modalTitle = (mode === 'tambah' ? 'Tambah Record Baru' : 'Edit Record');
        $('#modalDbTitle').text(modalTitle);
        $('#modalDbSubtitle').text('Tabel Sasaran: ' + tbl);

        switch (tbl) {
            case 'progres_kp':
            case 'progres_skripsi':
                var sopList = tbl === 'progres_kp' ? (state.master ? state.master.sop_kp : []) : (state.master ? state.master.sop_skripsi : []);
                var curMhs = row.mahasiswa || '';
                var curProses = parseInt(row.proses || 1);
                var curWaktuInput = formatTimeInput(row.waktu);
                var compositeId = mode === 'edit' ? (row.mahasiswa + '|' + row.proses + '|' + row.waktu) : '';
                $('#formDbIdKey').val(compositeId);

                html += '<div>';
                html +=   '<label class="block font-bold text-slate-700 mb-1">NIM Mahasiswa <span class="text-rose-500">*</span></label>';
                html +=   '<input type="text" name="mahasiswa" value="' + curMhs + '" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono font-bold outline-none focus:border-orange-500" placeholder="Contoh: 531420001" required>';
                html +=   '<p class="text-[11px] text-slate-400 mt-1">Harus merupakan NIM valid yang terdaftar pada tabel mahasiswa.</p>';
                html += '</div>';

                html += '<div>';
                html +=   '<label class="block font-bold text-slate-700 mb-1">Tahap Proses (SOP ' + (tbl === 'progres_kp' ? 'KP' : 'Skripsi') + ') <span class="text-rose-500">*</span></label>';
                html +=   '<select name="proses" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-800 outline-none focus:border-orange-500" required>';
                if (sopList && sopList.length > 0) {
                    $.each(sopList, function(idx, sp) {
                        var sel = parseInt(sp.no) === curProses ? 'selected' : '';
                        html += '<option value="' + sp.no + '" ' + sel + '>SOP #' + sp.no + ' - ' + sp.keterangan + '</option>';
                    });
                } else {
                    for (var s = 1; s <= (tbl === 'progres_kp' ? 16 : 22); s++) {
                        var sel = s === curProses ? 'selected' : '';
                        html += '<option value="' + s + '" ' + sel + '>Langkah #' + s + '</option>';
                    }
                }
                html +=   '</select>';
                html += '</div>';

                html += '<div>';
                html +=   '<label class="block font-bold text-slate-700 mb-1">Waktu Penyelesaian / Timestamp</label>';
                html +=   '<input type="datetime-local" name="waktu" value="' + curWaktuInput + '" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono outline-none focus:border-orange-500">';
                html +=   '<p class="text-[11px] text-slate-400 mt-1">Biarkan default waktu sekarang, atau sesuaikan jika mencatat histori lama.</p>';
                html += '</div>';
                break;

            case 'temp_kp':
            case 'temp_skripsi':
                var syaratList = tbl === 'temp_kp' ? (state.master ? state.master.syarat_kp : []) : (state.master ? state.master.syarat_skripsi : []);
                var curNim = row.nim || '';
                var curSyarat = parseInt(row.id_persyaratan || 0);
                var curStatus = (row.status || 'menunggu').toLowerCase();
                var curKet = row.ket || '';
                var curFile = row.file || '';
                $('#formDbIdKey').val(row.id || '');

                html += '<div>';
                html +=   '<label class="block font-bold text-slate-700 mb-1">NIM Mahasiswa <span class="text-rose-500">*</span></label>';
                html +=   '<input type="text" name="nim" value="' + curNim + '" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono font-bold outline-none focus:border-orange-500" placeholder="NIM Mahasiswa..." required>';
                html += '</div>';

                html += '<div>';
                html +=   '<label class="block font-bold text-slate-700 mb-1">Jenis Persyaratan Berkas <span class="text-rose-500">*</span></label>';
                html +=   '<select name="id_persyaratan" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-800 outline-none focus:border-orange-500" required>';
                html +=     '<option value="">-- Pilih Persyaratan Berkas --</option>';
                if (syaratList && syaratList.length > 0) {
                    $.each(syaratList, function(idx, sy) {
                        var sel = parseInt(sy.id) === curSyarat ? 'selected' : '';
                        html += '<option value="' + sy.id + '" ' + sel + '>[Tahap ' + sy.progres + '] #' + sy.id + ' - ' + sy.nama_berkas + '</option>';
                    });
                }
                html +=   '</select>';
                html += '</div>';

                html += '<div class="grid grid-cols-2 gap-3">';
                html +=   '<div>';
                html +=     '<label class="block font-bold text-slate-700 mb-1">Status Berkas</label>';
                html +=     '<select name="status_berkas" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold outline-none focus:border-orange-500">';
                html +=       '<option value="menunggu" ' + (curStatus === 'menunggu' ? 'selected' : '') + '>MENUNGGU (Pending)</option>';
                html +=       '<option value="diterima" ' + (curStatus === 'diterima' ? 'selected' : '') + '>DITERIMA (Valid)</option>';
                html +=       '<option value="ditolak" ' + (curStatus === 'ditolak' ? 'selected' : '') + '>DITOLAK (Revisi)</option>';
                html +=     '</select>';
                html +=   '</div>';
                html +=   '<div>';
                html +=     '<label class="block font-bold text-slate-700 mb-1">File ID (Tabel files)</label>';
                html +=     '<input type="text" name="file_id" value="' + curFile + '" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono outline-none focus:border-orange-500" placeholder="ID file atau kosongkan...">';
                html +=   '</div>';
                html += '</div>';

                html += '<div>';
                html +=   '<label class="block font-bold text-slate-700 mb-1">Catatan / Keterangan Verifikasi</label>';
                html +=   '<textarea name="ket" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs outline-none focus:border-orange-500" placeholder="Alasan penolakan / instruksi perbaikan...">' + curKet + '</textarea>';
                html += '</div>';
                break;

            case 'format_nomor_surat':
                var curNamaSurat = row.nama_surat || '';
                var curFormat = row.format_nomor || '';
                $('#formDbIdKey').val(row.id || '');

                html += '<div>';
                html +=   '<label class="block font-bold text-slate-700 mb-1">Nama / Kategori Surat <span class="text-rose-500">*</span></label>';
                html +=   '<input type="text" name="nama_surat" value="' + curNamaSurat + '" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-800 outline-none focus:border-orange-500" placeholder="Contoh: surat permohonan kp" required>';
                html += '</div>';

                html += '<div>';
                html +=   '<label class="block font-bold text-slate-700 mb-1">Pola Format Penomoran <span class="text-rose-500">*</span></label>';
                html +=   '<input type="text" name="format_nomor" value="' + curFormat + '" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono font-bold text-indigo-700 outline-none focus:border-orange-500" placeholder="Contoh: {urutan}/UN47.B5/TU/{tahun}" required>';
                html +=   '<p class="text-[11px] text-slate-400 mt-1">Gunakan tag dinamis seperti <code>{urutan}</code>, <code>{tahun}</code>, atau <code>{bulan}</code>.</p>';
                html += '</div>';
                break;

            case 'nomor_surat':
                var curUrutan = row.urutan || '';
                var curNomor = row.nomor || '';
                var curTgl = row.tanggal || new Date().toISOString().split('T')[0];
                var curKeterangan = row.keterangan || '';
                $('#formDbIdKey').val(row.id || '');

                if (mode === 'tambah') {
                    html += '<div>';
                    html +=   '<label class="block font-bold text-slate-700 mb-1">ID Kustom (Opsional)</label>';
                    html +=   '<input type="text" name="id_custom" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono outline-none focus:border-orange-500" placeholder="Biarkan kosong untuk auto-generate timestamp ID">';
                    html += '</div>';
                }

                html += '<div class="grid grid-cols-2 gap-3">';
                html +=   '<div>';
                html +=     '<label class="block font-bold text-slate-700 mb-1">Urutan Numerik</label>';
                html +=     '<input type="number" name="urutan" value="' + curUrutan + '" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono font-bold outline-none focus:border-orange-500" placeholder="Contoh: 125">';
                html +=   '</div>';
                html +=   '<div>';
                html +=     '<label class="block font-bold text-slate-700 mb-1">Tanggal Surat</label>';
                html +=     '<input type="date" name="tanggal" value="' + curTgl + '" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono outline-none focus:border-orange-500" required>';
                html +=   '</div>';
                html += '</div>';

                html += '<div>';
                html +=   '<label class="block font-bold text-slate-700 mb-1">Teks Lengkap Nomor Surat <span class="text-rose-500">*</span></label>';
                html +=   '<input type="text" name="nomor" value="' + curNomor + '" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono font-bold text-emerald-800 outline-none focus:border-orange-500" placeholder="Contoh: 042/UN47.B5/TU/2026" required>';
                html += '</div>';

                html += '<div>';
                html +=   '<label class="block font-bold text-slate-700 mb-1">Keterangan / Keperluan Penerbitan</label>';
                html +=   '<textarea name="keterangan" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs outline-none focus:border-orange-500" placeholder="Tujuan surat, nama mahasiswa atau keterangan penugasan...">' + curKeterangan + '</textarea>';
                html += '</div>';
                break;
        }

        $('#dynamicFormFields').html(html);
        $('#modalDbManipulator').removeClass('hidden');
    }

    // 8. Event Listener: Tab Switcher
    $(document).on('click', '.tab-db-btn', function() {
        var $btn = $(this);
        var targetTbl = $btn.data('tabel');
        if (state.tabel === targetTbl) return;

        $('.tab-db-btn').removeClass('active border-orange-500 font-bold text-slate-800 bg-white shadow-xs')
                        .addClass('border-transparent font-semibold text-slate-500 bg-transparent');
        $btn.addClass('active border-orange-500 font-bold text-slate-800 bg-white shadow-xs')
            .removeClass('border-transparent font-semibold text-slate-500 bg-transparent');

        state.tabel = targetTbl;
        state.page = 1;
        state.cari = '';
        $('#inputCariDb').val('');

        muatDataTabelDb();
    });

    // 9. Event Listener: Search & Limit
    $('#btnCariDb').on('click', function() {
        state.cari = $.trim($('#inputCariDb').val());
        state.page = 1;
        muatDataTabelDb();
    });
    $('#inputCariDb').on('keyup', function(e) {
        if (e.key === 'Enter') {
            $('#btnCariDb').trigger('click');
        }
    });
    $('#selectLimitDb').on('change', function() {
        state.limit = parseInt($(this).val()) || 10;
        state.page = 1;
        muatDataTabelDb();
    });

    // 10. Event Listener: Refresh
    $('#btnRefreshTabelDb').on('click', function() {
        muatDataTabelDb();
        muatRingkasanTabel();
        toastDb('Tabel ' + state.tabel + ' disegarkan', 'info');
    });

    // 11. Event Listener: Pagination Buttons
    $(document).on('click', '.btn-page-db', function() {
        var p = parseInt($(this).data('page'));
        if (p && p !== state.page) {
            state.page = p;
            muatDataTabelDb();
        }
    });

    // 12. Event Listener: Modal Open & Close
    $('#btnTambahRecordDb').on('click', function() {
        muatMasterLookup(function() {
            bangunFormModal(state.tabel, 'tambah', {});
        });
    });

    $(document).on('click', '.btn-edit-row', function() {
        var $tr = $(this).closest('tr');
        var rowData = $tr.data('row-json');
        muatMasterLookup(function() {
            bangunFormModal(state.tabel, 'edit', rowData);
        });
    });

    $('#btnCloseModalDb, #btnBatalModalDb').on('click', function() {
        $('#modalDbManipulator').addClass('hidden');
    });

    $('#modalDbManipulator').on('click', function(e) {
        if ($(e.target).closest('.w-full.max-w-xl').length === 0) {
            $('#modalDbManipulator').addClass('hidden');
        }
    });

    // 13. Submit Simpan Record (Tambah / Edit)
    $('#formDbManipulator').on('submit', function(e) {
        e.preventDefault();
        var $btnSubmit = $('#btnSubmitModalDb');
        var origText = $btnSubmit.html();
        $btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        var formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'simpan-data-tabel' });

        $.ajax({
            url: PROSES_DB_URL,
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(res) {
                $btnSubmit.prop('disabled', false).html(origText);
                if (res && res.status) {
                    toastDb(res.pesan, 'success');
                    $('#modalDbManipulator').addClass('hidden');
                    muatDataTabelDb();
                    muatRingkasanTabel();
                } else {
                    toastDb(res ? res.pesan : 'Gagal menyimpan data', 'error');
                }
            },
            error: function(xhr) {
                $btnSubmit.prop('disabled', false).html(origText);
                toastDb('Terjadi kesalahan koneksi server saat menyimpan (' + xhr.status + ')', 'error');
            }
        });
    });

    // 14. Hapus Record
    $(document).on('click', '.btn-delete-row', function() {
        var idKey = $(this).data('idkey');
        if (!confirm('Apakah Anda yakin ingin menghapus record ini dari tabel ' + state.tabel + '? Operasi ini tidak dapat dibatalkan.')) {
            return;
        }

        $.ajax({
            url: PROSES_DB_URL,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'hapus-data-tabel',
                tabel: state.tabel,
                id_key: idKey
            },
            success: function(res) {
                if (res && res.status) {
                    toastDb(res.pesan, 'success');
                    muatDataTabelDb();
                    muatRingkasanTabel();
                } else {
                    toastDb(res ? res.pesan : 'Gagal menghapus baris', 'error');
                }
            },
            error: function(xhr) {
                toastDb('Kesalahan server saat menghapus data (' + xhr.status + ')', 'error');
            }
        });
    });

    // Inisialisasi Pertama Kali
    $(document).ready(function() {
        muatMasterLookup();
        muatDataTabelDb();
        muatRingkasanTabel();
    });

})(jQuery);
</script>
