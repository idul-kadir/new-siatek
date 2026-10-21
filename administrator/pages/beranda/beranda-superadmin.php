<?php
/**
 * View Beranda: Super Administrator (Level 9)
 * Pusat Komando & Pengawasan Sistem SIATEK
 * Fokus: Manajemen Pengguna Lintas Peran, Kesehatan Platform & Database, dan Tata Kelola Sistem.
 */
declare(strict_types=1);

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
?>

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
