<?php
/**
 * View Beranda: Operator Program Studi & Jurusan (Level 3, 4)
 * Dashboard Operasional Resmi SIATEK — Bento Grid Asimetris & Antrean Verifikasi.
 */
declare(strict_types=1);

$userProdi = function_exists('get_prodi_user') ? get_prodi_user() : trim((string)($_SESSION['prodi'] ?? ''));
$namaProdiUser = '';
if ($userProdi !== '') {
    $qPr = query("SELECT nama FROM prodi WHERE kode = '" . bersihkan($userProdi) . "' LIMIT 1");
    if ($qPr && ($rP = mysqli_fetch_assoc($qPr))) {
        $namaProdiUser = $rP['nama'];
    }
} else {
    // Fallback jika Super Admin mempratinjau peran Operator
    $qPrSample = query("SELECT kode, nama FROM prodi ORDER BY kode LIMIT 1");
    if ($qPrSample && ($rps = mysqli_fetch_assoc($qPrSample))) {
        $userProdi = $rps['kode'];
        $namaProdiUser = $rps['nama'];
    }
}
if ($namaProdiUser === '') {
    $namaProdiUser = 'Teknik Elektro & Komputer';
}

$filterProdiMhs = ($userProdi !== '') ? "AND m.prodi = '" . bersihkan($userProdi) . "'" : "";

// 1. Total Mahasiswa Aktif di Prodi Ini
$qMhsAktifProdi = query("SELECT COUNT(*) AS c FROM mahasiswa WHERE status='Aktif' " . ($userProdi !== '' ? "AND prodi='" . bersihkan($userProdi) . "'" : ""));
$mhsAktifProdi = (int)mysqli_fetch_assoc($qMhsAktifProdi)['c'];

// 2. Antrean Berkas Masuk Menunggu Verifikasi
$berkasSkripsiList = [];
$qBerkasSkripsi = query("
    SELECT ts.id, ts.nim, ts.id_persyaratan, ts.file, ts.status, m.nama AS nama_mhs, m.prodi, 'Skripsi' AS tipe,
           pbs.nama_berkas AS nama_persyaratan
    FROM temp_skripsi ts
    JOIN mahasiswa m ON ts.nim = m.nim
    LEFT JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id
    WHERE ts.status = 'menunggu' $filterProdiMhs
    ORDER BY ts.id DESC LIMIT 6
");
if ($qBerkasSkripsi) {
    while ($r = mysqli_fetch_assoc($qBerkasSkripsi)) { $berkasSkripsiList[] = $r; }
}

$berkasKpList = [];
$qBerkasKp = query("
    SELECT tk.id, tk.nim, tk.id_persyaratan, tk.file, tk.status, m.nama AS nama_mhs, m.prodi, 'KP' AS tipe,
           pbk.nama_berkas AS nama_persyaratan
    FROM temp_kp tk
    JOIN mahasiswa m ON tk.nim = m.nim
    LEFT JOIN persyaratan_berkas_kp pbk ON tk.id_persyaratan = pbk.id
    WHERE tk.status = 'menunggu' $filterProdiMhs
    ORDER BY tk.id DESC LIMIT 6
");
if ($qBerkasKp) {
    while ($r = mysqli_fetch_assoc($qBerkasKp)) { $berkasKpList[] = $r; }
}

$semuaBerkasAntrean = array_merge($berkasSkripsiList, $berkasKpList);
usort($semuaBerkasAntrean, fn($a, $b) => (int)$b['id'] <=> (int)$a['id']);
$antreanVerifikasiTampil = array_slice($semuaBerkasAntrean, 0, 7);
$totalPendingBerkas = count($semuaBerkasAntrean);

// 4. Skripsi & KP Berjalan di Prodi Ini
$qSkripsiProdi = query("
    SELECT COUNT(*) AS c FROM skripsi s
    JOIN mahasiswa m ON s.nim = m.nim
    WHERE s.status='aktif' $filterProdiMhs
");
$skripsiProdiAktif = (int)mysqli_fetch_assoc($qSkripsiProdi)['c'];

$qKpProdi = query("
    SELECT COUNT(*) AS c FROM kp k
    JOIN mahasiswa m ON k.nim = m.nim
    WHERE (k.status='Aktif' OR k.status='aktif') $filterProdiMhs
");
$kpProdiAktif = (int)mysqli_fetch_assoc($qKpProdi)['c'];

// 5. Antrean Mahasiswa Siap Ujian (SOP Step 10 Hasil, Step 18 Tutup, atau Seminar KP)
$activeBatchId = 0;
$qActiveBatch = query("SELECT id FROM batch_ujian WHERE (status IS NULL OR status = '' OR status = 'buka') ORDER BY id DESC LIMIT 1");
if ($qActiveBatch && ($rAb = mysqli_fetch_assoc($qActiveBatch))) {
    $activeBatchId = (int)$rAb['id'];
}
$antreanUjianList = function_exists('ambil_antrian_siap_ujian') ? ambil_antrian_siap_ujian($activeBatchId, $userProdi) : [];
$totalAntreanUjian = count($antreanUjianList);

// Berita
$beritaItems = [];
$qBerita = query("SELECT judul, deskripsi, tanggal FROM berita WHERE judul <> '' ORDER BY tanggal DESC LIMIT 3");
if ($qBerita) {
    while ($b = mysqli_fetch_assoc($qBerita)) { $beritaItems[] = $b; }
}
?>

<!-- ============ PAGE HEADER (KONSISTEN SIATEK) ============ -->
<section class="flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3.5">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600 shadow-2xs">
            <i class="fas fa-desktop"></i>
        </div>
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-800">
                Selamat Datang, <?= htmlspecialchars($nmUser) ?>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Operator Administrasi &bull; <span class="font-semibold text-orange-600"><?= htmlspecialchars($namaProdiUser) ?></span> &bull; <span class="font-medium text-slate-700"><?= htmlspecialchars($today_label) ?></span>
            </p>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="/redesain-siatek/administrator/mhs-verifikasi"
           class="inline-flex items-center gap-2 rounded-lg bg-[#f97316] hover:bg-[#ea580c] px-3.5 py-2 text-xs font-semibold text-white shadow-sm shadow-orange-500/25 transition">
            <i class="fas fa-clipboard-check"></i> Verifikasi Berkas
            <?php if ($totalPendingBerkas > 0): ?>
                <span class="rounded-full bg-white/20 px-1.5 py-0.2 text-[10px] font-bold"><?= $totalPendingBerkas ?></span>
            <?php endif; ?>
        </a>
        <a href="/redesain-siatek/administrator/mhs-skripsi"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
            <i class="fas fa-book-open text-[#1a365d]"></i> Monitoring Skripsi
        </a>
        <a href="/redesain-siatek/administrator/mhs-ujian"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
            <i class="fas fa-calendar-check text-[#1a365d]"></i> Kelola Ujian
        </a>
    </div>
</section>

<!-- ============ 4 KARTU METRIK OPERASIONAL DENGAN 1 KARTU JANGKAR NAVY ============ -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- 1. KARTU JANGKAR OPERASIONAL (NAVY ANCHOR CARD - BERKAS MASUK) -->
    <div class="rounded-xl bg-[#1a365d] text-white p-5 shadow-md shadow-slate-900/10 flex flex-col justify-between relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-white/5 pointer-events-none"></div>
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-widest text-orange-300 flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                    Berkas Masuk
                </span>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 text-orange-300 text-xs">
                    <i class="fas fa-clipboard-check"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl sm:text-4xl font-bold font-mono tracking-tight text-white"><?= f($totalPendingBerkas) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between text-xs">
            <span class="text-slate-300">Menunggu validasi operator</span>
            <a href="/redesain-siatek/administrator/mhs-verifikasi" class="font-semibold text-orange-300 hover:text-orange-200 transition">
                Periksa &rarr;
            </a>
        </div>
    </div>

    <!-- 2. Antrean Mahasiswa Siap Ujian -->
    <a href="/redesain-siatek/administrator/mhs-ujian" 
       class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-orange-300 transition block group flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Antrean Ujian</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs group-hover:bg-[#1a365d] group-hover:text-white transition">
                    <i class="fas fa-user-clock"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($totalAntreanUjian) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Mahasiswa siap dijadwalkan</span>
            <span class="font-semibold text-slate-700 group-hover:underline">Buka &rarr;</span>
        </div>
    </a>

    <!-- 3. Skripsi Prodi Berjalan -->
    <a href="/redesain-siatek/administrator/mhs-skripsi" 
       class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-orange-300 transition block group flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Skripsi Prodi</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 text-orange-600 text-xs group-hover:bg-[#f97316] group-hover:text-white transition">
                    <i class="fas fa-book-open"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($skripsiProdiAktif) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Mahasiswa aktif skripsi</span>
            <span class="font-semibold text-orange-600 group-hover:underline">Buka &rarr;</span>
        </div>
    </a>

    <!-- 4. KP Prodi Berjalan -->
    <a href="/redesain-siatek/administrator/mhs-kp" 
       class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-orange-300 transition block group flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">KP Prodi</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs group-hover:bg-[#1a365d] group-hover:text-white transition">
                    <i class="fas fa-briefcase"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($kpProdiAktif) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Mahasiswa aktif magang</span>
            <span class="font-semibold text-slate-700 group-hover:underline">Buka &rarr;</span>
        </div>
    </a>
</section>

<!-- ============ BENTO GRID ASIMETRIS (2/3 FEED ANTREAN VS 1/3 RINGKASAN STATUS) ============ -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

    <!-- ===== KOLOM KIRI (8 KOLOM / 66%): ANTREAN BERKAS & PUSAT EKSEKUSI ===== -->
    <div class="lg:col-span-8 space-y-5">

        <!-- 1. Antrean Berkas Mahasiswa Menunggu Verifikasi (Feed Utama Operator) -->
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 bg-slate-50/70">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Antrean Berkas Mahasiswa Menunggu Verifikasi</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Unggahan berkas persyaratan Skripsi &amp; KP terbaru dari mahasiswa prodi</p>
                </div>
                <a href="/redesain-siatek/administrator/mhs-verifikasi" class="inline-flex items-center gap-1.5 rounded-lg bg-[#1a365d] hover:bg-[#234670] text-white px-3 py-1.5 text-xs font-semibold shadow-2xs transition">
                    Buka Modul Verifikasi &rarr;
                </a>
            </div>

            <?php if (count($antreanVerifikasiTampil) === 0): ?>
                <div class="flex flex-col items-center py-10 text-center">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400 text-sm"><i class="fas fa-check"></i></span>
                    <p class="mt-2 text-xs font-semibold text-slate-700">Semua berkas telah diverifikasi</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Tidak ada berkas tertunda pada antrean program studi Anda saat ini.</p>
                </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs text-slate-500 font-semibold uppercase tracking-wider bg-slate-50/40">
                            <th class="py-3 px-5">Mahasiswa</th>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4">Persyaratan Dokumen</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        <?php foreach ($antreanVerifikasiTampil as $b):
                            $isSkripsi = ($b['tipe'] === 'Skripsi');
                            $namaMhs = $b['nama_mhs'] ?: $b['nim'];
                            $namaSyarat = $b['nama_persyaratan'] ?: 'Berkas Persyaratan #' . $b['id_persyaratan'];
                        ?>
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-700">
                                        <?= strtoupper(substr($namaMhs, 0, 1)) ?>
                                    </span>
                                    <div>
                                        <p class="font-semibold text-slate-900"><?= htmlspecialchars($namaMhs) ?></p>
                                        <p class="font-mono text-xs text-slate-400"><?= htmlspecialchars($b['nim']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center rounded px-2.5 py-0.5 text-[11px] font-semibold <?= $isSkripsi ? 'bg-orange-50 text-orange-700 border border-orange-200/60' : 'bg-slate-100 text-slate-700' ?>">
                                    <?= $b['tipe'] ?>
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="text-xs text-slate-700 font-medium"><?= htmlspecialchars($namaSyarat) ?></p>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center text-xs text-amber-700 bg-amber-50 border border-amber-200/60 px-2 py-0.5 rounded font-medium">
                                    Menunggu Validasi
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <a href="/redesain-siatek/administrator/mhs-verifikasi" 
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700 transition">
                                    <i class="fas fa-magnifying-glass text-slate-400"></i> Periksa
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 bg-slate-50/50 px-5 py-2.5 text-center">
                <a href="/redesain-siatek/administrator/mhs-verifikasi" class="text-xs font-semibold text-slate-600 hover:text-orange-600 transition">
                    Lihat semua berkas di modul verifikasi &rarr;
                </a>
            </div>
            <?php endif; ?>
        </section>

        <!-- 2. Pusat Eksekusi Operasional Prodi -->
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="text-base font-semibold text-slate-800">Pusat Eksekusi Operasional Prodi</h2>
                <p class="text-xs text-slate-500 mt-0.5">Akses cepat tugas rutin administrasi akademik harian</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="/redesain-siatek/administrator/mhs-verifikasi" class="flex items-start gap-3 p-3.5 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-orange-200 transition group">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-orange-600 text-sm"><i class="fas fa-clipboard-check"></i></span>
                    <div>
                        <h4 class="text-sm font-semibold text-slate-800 group-hover:text-orange-600 transition">Verifikasi Berkas</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Validasi berkas persyaratan KP &amp; Skripsi.</p>
                    </div>
                </a>

                <a href="/redesain-siatek/administrator/mhs-skripsi" class="flex items-start gap-3 p-3.5 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-orange-200 transition group">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-sm"><i class="fas fa-book-open"></i></span>
                    <div>
                        <h4 class="text-sm font-semibold text-slate-800 group-hover:text-orange-600 transition">Monitoring Skripsi</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Pantau progres &amp; tahapan skripsi mahasiswa.</p>
                    </div>
                </a>

                <a href="/redesain-siatek/administrator/mhs-ujian" class="flex items-start gap-3 p-3.5 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-orange-200 transition group">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-sm"><i class="fas fa-user-clock"></i></span>
                    <div>
                        <h4 class="text-sm font-semibold text-slate-800 group-hover:text-orange-600 transition">Penjadwalan Ujian</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola antrean ujian seminar &amp; sidang prodi.</p>
                    </div>
                </a>

                <a href="/redesain-siatek/administrator/pengguna-mahasiswa" class="flex items-start gap-3 p-3.5 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-orange-200 transition group">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-sm"><i class="fas fa-users-gear"></i></span>
                    <div>
                        <h4 class="text-sm font-semibold text-slate-800 group-hover:text-orange-600 transition">Data Mahasiswa</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Reset password akun &amp; status aktif.</p>
                    </div>
                </a>
            </div>
        </section>

    </div>

    <!-- ===== KOLOM KANAN (4 KOLOM / 34%): RINGKASAN STATUS PRODI ===== -->
    <div class="lg:col-span-4 space-y-5">

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-800">Ringkasan Operasional Prodi</h3>
                <p class="text-xs text-orange-600 font-semibold mt-0.5"><?= htmlspecialchars($namaProdiUser) ?></p>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50/80 border border-slate-100">
                    <div class="flex items-center gap-2.5 text-xs font-semibold text-slate-700">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white border border-slate-200 text-[#1a365d]">
                            <i class="fas fa-users text-xs"></i>
                        </span>
                        <span>Mahasiswa Aktif</span>
                    </div>
                    <span class="text-sm font-bold font-mono text-slate-900"><?= f($mhsAktifProdi) ?></span>
                </div>

                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50/80 border border-slate-100">
                    <div class="flex items-center gap-2.5 text-xs font-semibold text-slate-700">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white border border-slate-200 text-orange-600">
                            <i class="fas fa-book-open text-xs"></i>
                        </span>
                        <span>Skripsi Berjalan</span>
                    </div>
                    <span class="text-sm font-bold font-mono text-slate-900"><?= f($skripsiProdiAktif) ?></span>
                </div>

                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50/80 border border-slate-100">
                    <div class="flex items-center gap-2.5 text-xs font-semibold text-slate-700">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white border border-slate-200 text-slate-600">
                            <i class="fas fa-briefcase text-xs"></i>
                        </span>
                        <span>KP Berjalan</span>
                    </div>
                    <span class="text-sm font-bold font-mono text-slate-900"><?= f($kpProdiAktif) ?></span>
                </div>

                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50/80 border border-slate-100">
                    <div class="flex items-center gap-2.5 text-xs font-semibold text-slate-700">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white border border-slate-200 text-orange-600">
                            <i class="fas fa-clipboard-check text-xs"></i>
                        </span>
                        <span>Antrean Verifikasi</span>
                    </div>
                    <span class="text-sm font-bold font-mono text-orange-600"><?= f($totalPendingBerkas) ?></span>
                </div>

                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50/80 border border-slate-100">
                    <div class="flex items-center gap-2.5 text-xs font-semibold text-slate-700">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white border border-slate-200 text-[#1a365d]">
                            <i class="fas fa-user-clock text-xs"></i>
                        </span>
                        <span>Antrean Siap Ujian</span>
                    </div>
                    <span class="text-sm font-bold font-mono text-slate-900"><?= f($totalAntreanUjian) ?></span>
                </div>
            </div>
        </section>

    </div>
</div>

<!-- ============ BERITA JURUSAN ============ -->
<section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm mt-5">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-base font-semibold text-slate-800">Berita &amp; Informasi Jurusan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pengumuman resmi untuk staf dan mahasiswa</p>
        </div>
        <a href="/redesain-siatek/administrator/jurusan-berita" class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition">Semua Berita &rarr;</a>
    </div>
    <?php if (count($beritaItems) === 0): ?>
        <p class="py-8 text-center text-xs text-slate-400">Belum ada berita jurusan terbaru.</p>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
        <?php foreach ($beritaItems as $b):
            $tanggalParsed = strtotime($b['tanggal']);
            $tanggalTxt = $tanggalParsed ? date('d M Y', $tanggalParsed) : '';
        ?>
        <a href="/redesain-siatek/administrator/jurusan-berita" class="group flex flex-col justify-between p-4 rounded-lg border border-slate-200/80 bg-white hover:border-orange-200 hover:shadow-2xs transition">
            <div>
                <span class="inline-flex items-center text-[11px] font-medium text-slate-400 mb-2">
                    <i class="far fa-clock mr-1 text-slate-400"></i><?= htmlspecialchars($tanggalTxt) ?>
                </span>
                <h3 class="text-sm font-semibold text-slate-800 group-hover:text-orange-600 transition line-clamp-2 leading-snug">
                    <?= htmlspecialchars($b['judul']) ?>
                </h3>
                <?php if (!empty($b['deskripsi'])): ?>
                <p class="mt-1.5 text-xs text-slate-500 line-clamp-2 leading-relaxed">
                    <?= htmlspecialchars($b['deskripsi']) ?>
                </p>
                <?php endif; ?>
            </div>
            <span class="mt-3 text-xs font-semibold text-orange-600 flex items-center gap-1 group-hover:gap-1.5 transition-all">
                Baca selengkapnya &rarr;
            </span>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
