<?php
/**
 * View Beranda: Pengelola Program Studi (Level 5 — Ketua Program Studi / Kaprodi)
 * Dasbor Otoritas Akademik Prodi SIATEK
 * Fokus: Antrean Penunjukkan Dosen Pembimbing & Penguji, Monitoring Mahasiswa Aktif, Kelayakan Ujian, & Tren Kelulusan.
 */
declare(strict_types=1);

// 1. Identifikasi Program Studi yang Diampu Kaprodi
$userProdi = function_exists('get_prodi_user') ? get_prodi_user() : trim((string)($_SESSION['prodi'] ?? ''));

// Fallback untuk Super Admin jika sedang melakukan pratinjau prodi
if ($userProdi === '') {
    $qPrDefault = query("SELECT kode FROM prodi ORDER BY kode ASC LIMIT 1");
    if ($qPrDefault && ($rD = mysqli_fetch_assoc($qPrDefault))) {
        $userProdi = $rD['kode'];
    } else {
        $userProdi = 'PTE';
    }
}

// Ambil Profil Resmi Program Studi
$prodiInfo = ['kode' => $userProdi, 'nama' => 'Program Studi', 'jenjang' => 'S1'];
$qPrInfo = query("SELECT kode, nama, jenjang FROM prodi WHERE kode = '" . bersihkan($userProdi) . "' LIMIT 1");
if ($qPrInfo && ($r = mysqli_fetch_assoc($qPrInfo))) {
    $prodiInfo = $r;
}
$namaProdiUser = $prodiInfo['nama'];
$cleanProdi = bersihkan($userProdi);

// 2. Antrean Penunjukkan Dosen Pembimbing & Penguji (Tugas Pokok & Wewenang Utama Kaprodi)
$antreanPenunjukkanList = function_exists('ambil_antrian_penunjukkan') ? ambil_antrian_penunjukkan($cleanProdi) : [];
$totalAntreanPenunjukkan = count($antreanPenunjukkanList);

// 3. Metrik Kunci Otoritas Kaprodi
// A. Mahasiswa Aktif Prodi (Sesuai Regulasi S1: Maksimal 7 Angkatan Terakhir / 7 Tahun Berjalan)
$aktifSince = (int)date('Y') - 6; // Pada tahun 2026: Angkatan 2020 s.d. 2026
$qMhsAktif = query("
    SELECT COUNT(*) AS c 
    FROM mahasiswa 
    WHERE prodi = '$cleanProdi' 
      AND angkatan >= $aktifSince 
      AND (status = 'Aktif' OR status = 'aktif')
");
$mhsAktifProdi = $qMhsAktif ? (int)mysqli_fetch_assoc($qMhsAktif)['c'] : 0;

// B. Skripsi Berjalan di Prodi
$qSkripsiAktif = query("
    SELECT COUNT(*) AS c 
    FROM skripsi s
    JOIN mahasiswa m ON s.nim = m.nim
    WHERE (s.status = 'aktif' OR s.status = 'Aktif') AND m.prodi = '$cleanProdi'
");
$skripsiProdiAktif = $qSkripsiAktif ? (int)mysqli_fetch_assoc($qSkripsiAktif)['c'] : 0;

// C. Kerja Praktek Berjalan di Prodi
$qKpAktif = query("
    SELECT COUNT(*) AS c 
    FROM kp k
    JOIN mahasiswa m ON k.nim = m.nim
    WHERE (k.status = 'Aktif' OR k.status = 'aktif') AND m.prodi = '$cleanProdi'
");
$kpProdiAktif = $qKpAktif ? (int)mysqli_fetch_assoc($qKpAktif)['c'] : 0;

// D. Total Alumni / Kelulusan Prodi
$qAlumniProdi = query("
    SELECT COUNT(DISTINCT nim) AS c FROM (
        SELECT nim FROM alumni WHERE prodi = '$cleanProdi'
        UNION
        SELECT a.nim FROM alumni a JOIN mahasiswa m ON a.nim = m.nim WHERE m.prodi = '$cleanProdi'
        UNION
        SELECT nim FROM mahasiswa WHERE prodi = '$cleanProdi' AND LOWER(status) = 'alumni'
    ) AS gabungan_alumni_prodi
");
$totalAlumniProdi = $qAlumniProdi ? (int)mysqli_fetch_assoc($qAlumniProdi)['c'] : 0;

// E. Antrean Siap Ujian Prodi (Data Telemetri)
$totalAntreanUjian = function_exists('ambil_antrian_siap_ujian') ? count(ambil_antrian_siap_ujian(0, $cleanProdi)) : 0;

// 4. Mahasiswa Expired / Kadaluarsa Status Skripsi & KP Prodi
$kadaluarsaList = [];

// Skripsi: status Kadaluarsa / Expired atau status Aktif tanpa progres lebih dari 180 hari
$qSkrKadaluarsa = query("
    SELECT s.nim, s.judul, s.status, 'Skripsi' AS jenis, m.nama AS nama_mhs, m.angkatan,
           COALESCE(ps.waktu_terbaru, 0) AS last_time,
           ps.proses_terbaru AS latest_step
    FROM skripsi s
    JOIN mahasiswa m ON s.nim = m.nim
    LEFT JOIN (
        SELECT mahasiswa, MAX(waktu) AS waktu_terbaru, MAX(proses) AS proses_terbaru
        FROM progres_skripsi
        GROUP BY mahasiswa
    ) ps ON s.nim = ps.mahasiswa
    WHERE m.prodi = '$cleanProdi'
      AND (
          LOWER(s.status) = 'kadaluarsa' 
          OR LOWER(s.status) = 'expired'
          OR (
              (s.status = 'aktif' OR s.status = 'Aktif')
              AND ps.waktu_terbaru > 0 
              AND ps.waktu_terbaru < (UNIX_TIMESTAMP() - 15552000)
          )
      )
    ORDER BY s.nim DESC
");
if ($qSkrKadaluarsa) {
    while ($row = mysqli_fetch_assoc($qSkrKadaluarsa)) {
        $row['is_overdue'] = (strtolower((string)$row['status']) !== 'kadaluarsa');
        $kadaluarsaList[] = $row;
    }
}

// KP: status Kadaluarsa / Expired atau status Aktif tanpa progres lebih dari 180 hari
$qKpKadaluarsa = query("
    SELECT k.nim, k.judul, k.status, 'Kerja Praktek' AS jenis, m.nama AS nama_mhs, m.angkatan,
           COALESCE(pk.waktu_terbaru, 0) AS last_time,
           pk.proses_terbaru AS latest_step
    FROM kp k
    JOIN mahasiswa m ON k.nim = m.nim
    LEFT JOIN (
        SELECT mahasiswa, MAX(waktu) AS waktu_terbaru, MAX(proses) AS proses_terbaru
        FROM progres_kp
        GROUP BY mahasiswa
    ) pk ON k.nim = pk.mahasiswa
    WHERE m.prodi = '$cleanProdi'
      AND (
          LOWER(k.status) = 'kadaluarsa' 
          OR LOWER(k.status) = 'expired'
          OR (
              (k.status = 'aktif' OR k.status = 'Aktif')
              AND pk.waktu_terbaru > 0 
              AND pk.waktu_terbaru < (UNIX_TIMESTAMP() - 15552000)
          )
      )
    ORDER BY k.nim DESC
");
if ($qKpKadaluarsa) {
    while ($row = mysqli_fetch_assoc($qKpKadaluarsa)) {
        $row['is_overdue'] = (strtolower((string)$row['status']) !== 'kadaluarsa');
        $kadaluarsaList[] = $row;
    }
}
$totalKadaluarsa = count($kadaluarsaList);

// 5. Distribusi Mahasiswa Prodi per Angkatan (7 Tahun Berjalan)
$perAngkatanProdi = [];
for ($ang = (int)date('Y'); $ang >= $aktifSince; $ang--) {
    $qKpA = query("
        SELECT COUNT(DISTINCT k.nim) AS c
        FROM kp k JOIN mahasiswa m ON k.nim = m.nim
        WHERE (k.status = 'aktif' OR k.status = 'Aktif') AND m.prodi = '$cleanProdi' AND m.angkatan = $ang
    ");
    $kpAktifAng = $qKpA ? (int)mysqli_fetch_assoc($qKpA)['c'] : 0;

    $qSkA = query("
        SELECT COUNT(DISTINCT s.nim) AS c
        FROM skripsi s JOIN mahasiswa m ON s.nim = m.nim
        WHERE (s.status = 'aktif' OR s.status = 'Aktif') AND m.prodi = '$cleanProdi' AND m.angkatan = $ang
    ");
    $skAktifAng = $qSkA ? (int)mysqli_fetch_assoc($qSkA)['c'] : 0;

    $qAlmAng = query("
        SELECT COUNT(DISTINCT nim) AS c FROM (
            SELECT nim FROM alumni WHERE prodi = '$cleanProdi' AND (angkatan = '$ang' OR angkatan = $ang)
            UNION
            SELECT a.nim FROM alumni a JOIN mahasiswa m ON a.nim = m.nim WHERE m.prodi = '$cleanProdi' AND m.angkatan = $ang
            UNION
            SELECT nim FROM mahasiswa WHERE prodi = '$cleanProdi' AND angkatan = $ang AND LOWER(status) = 'alumni'
        ) AS gabungan_alumni_ang
    ");
    $alumniAng = $qAlmAng ? (int)mysqli_fetch_assoc($qAlmAng)['c'] : 0;

    $qTotAng = query("
        SELECT COUNT(DISTINCT nim) AS c FROM (
            SELECT nim FROM mahasiswa WHERE prodi = '$cleanProdi' AND angkatan = $ang
            UNION
            SELECT nim FROM alumni WHERE prodi = '$cleanProdi' AND (angkatan = '$ang' OR angkatan = $ang)
        ) AS gabungan_populasi_ang
    ");
    $totalAng = $qTotAng ? (int)mysqli_fetch_assoc($qTotAng)['c'] : 0;
    if ($totalAng <= 0 && $alumniAng > 0) {
        $totalAng = $alumniAng;
    }

    $perAngkatanProdi[] = [
        'angkatan' => $ang,
        'total'    => $totalAng,
        'kp'       => $kpAktifAng,
        'skripsi'  => $skAktifAng,
        'alumni'   => $alumniAng
    ];
}

// 6. Tren Intake & Kelulusan Mahasiswa Prodi 5 Tahun Terakhir (Chart.js)
$years = [];
$mhsPerYear = [];
$alumniPerYear = [];
for ($y = (int)date('Y') - 4; $y <= (int)date('Y'); $y++) {
    $years[] = (string)$y;
    $qM = query("SELECT COUNT(*) AS c FROM mahasiswa WHERE prodi = '$cleanProdi' AND angkatan = $y");
    $mhsPerYear[] = (int)mysqli_fetch_assoc($qM)['c'];

    $qA = query("
        SELECT COUNT(DISTINCT nim) AS c FROM (
            SELECT nim FROM alumni WHERE prodi = '$cleanProdi' AND (tahun_lulus = '$y' OR YEAR(tanggal_lulus) = $y)
            UNION
            SELECT a.nim FROM alumni a JOIN mahasiswa m ON a.nim = m.nim 
            WHERE m.prodi = '$cleanProdi' AND (a.tahun_lulus = '$y' OR YEAR(a.tanggal_lulus) = $y)
        ) AS alm_yr
    ");
    $alumniPerYear[] = (int)mysqli_fetch_assoc($qA)['c'];
}

// 7. Berita & Informasi Jurusan (Maksimal 5 Berita Terbaru)
$beritaItems = [];
$qBerita = query("SELECT judul, deskripsi, tanggal FROM berita WHERE judul <> '' ORDER BY tanggal DESC LIMIT 5");
if ($qBerita) {
    while ($b = mysqli_fetch_assoc($qBerita)) { $beritaItems[] = $b; }
}
?>

<!-- ============ PAGE HEADER KAPRODI ============ -->
<section class="flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3.5">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600 shadow-2xs">
            <i class="fas fa-user-tie"></i>
        </div>
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold tracking-tight text-slate-800">
                    Selamat Datang, <?= htmlspecialchars($nmUser) ?>
                </h1>
                <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 border border-orange-200/80 px-2.5 py-0.5 text-[11px] font-bold text-orange-800">
                    Kaprodi
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Ketua Program Studi &bull; <span class="font-semibold text-orange-600"><?= htmlspecialchars($namaProdiUser) ?></span> &bull; <span class="font-medium text-slate-700"><?= htmlspecialchars($today_label) ?></span>
            </p>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="/redesain-siatek/administrator/mhs-skripsi"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
            <i class="fas fa-book-open text-[#1a365d]"></i> Monitoring Skripsi
        </a>
        <a href="/redesain-siatek/administrator/mhs-kp"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
            <i class="fas fa-briefcase text-sky-600"></i> Monitoring KP
        </a>
        <a href="/redesain-siatek/administrator/pengguna-mahasiswa"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
            <i class="fas fa-users text-[#1a365d]"></i> Data Mahasiswa
        </a>
    </div>
</section>

<!-- ============ 4 KARTU METRIK UTAMA KAPRODI ============ -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- 1. KARTU JANGKAR NAVY: Antrean Penunjukkan Dosen (Titik Fokus Otoritas & Tindakan Darurat) -->
    <a href="/redesain-siatek/administrator/mhs-penunjukkan"
       class="rounded-xl bg-[#1a365d] text-white p-5 shadow-md shadow-slate-900/10 flex flex-col justify-between relative overflow-hidden group hover:brightness-105 transition">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-white/5 pointer-events-none"></div>
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-widest text-orange-300 flex items-center gap-2">
                    <?php if ($totalAntreanPenunjukkan > 0): ?>
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-300 opacity-75" style="animation-duration: 3s;"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-400"></span>
                        </span>
                    <?php endif; ?>
                    Antrean Penunjukkan
                </span>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 text-orange-300 text-xs">
                    <i class="fas fa-user-check"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl sm:text-4xl font-bold font-mono tracking-tight text-white"><?= f($totalAntreanPenunjukkan) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between text-xs">
            <span class="text-slate-300">Butuh Pembimbing/Penguji</span>
            <span class="font-semibold text-orange-300 group-hover:underline">Tentukan Dosen &rarr;</span>
        </div>
    </a>

    <!-- 2. Mahasiswa Aktif Program Studi -->
    <a href="/redesain-siatek/administrator/pengguna-mahasiswa"
       class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-orange-300 transition block group flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Mahasiswa Aktif</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs group-hover:bg-[#1a365d] group-hover:text-white transition">
                    <i class="fas fa-users"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($mhsAktifProdi) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Angkatan <?= $aktifSince ?>–<?= date('Y') ?> (7 Thn)</span>
            <span class="font-semibold text-slate-700 group-hover:underline">Kelola &rarr;</span>
        </div>
    </a>

    <!-- 3. Skripsi & Kerja Praktek Berjalan -->
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Bimbingan Aktif</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 text-orange-600 text-xs">
                    <i class="fas fa-book-open"></i>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($skripsiProdiAktif) ?></span>
                <span class="text-xs font-semibold text-slate-500">Skripsi</span>
                <span class="text-slate-300">&bull;</span>
                <span class="text-xl font-bold font-mono text-slate-700"><?= f($kpProdiAktif) ?></span>
                <span class="text-xs font-semibold text-slate-500">KP</span>
            </div>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Total mahasiswa dibimbing</span>
            <span class="font-semibold text-slate-700"><?= f($skripsiProdiAktif + $kpProdiAktif) ?> orang</span>
        </div>
    </div>

    <!-- 4. Total Alumni / Kelulusan Prodi -->
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Alumni / Kelulusan</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 text-xs">
                    <i class="fas fa-graduation-cap"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($totalAlumniProdi) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Total lulusan prodi terdata</span>
            <span class="font-semibold text-emerald-600"><?= htmlspecialchars($prodiInfo['jenjang'] ?? 'S1') ?> Terakreditasi</span>
        </div>
    </div>
</section>

<!-- ============ BENTO WORKSPACE 8 : 4 ============ -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

    <!-- ===== KOLOM KIRI (8 KOLOM / 66%): WORKSPACE KEPUTUSAN & TELEMETRI ===== -->
    <div class="lg:col-span-8 space-y-5">

        <!-- 1. Panel Mahasiswa Expired / Kadaluarsa Status Skripsi & KP -->
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-3.5 bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-600 text-white shadow-2xs">
                        <i class="fas fa-triangle-exclamation text-sm"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-slate-800">
                                Mahasiswa Expired / Kadaluarsa (Skripsi &amp; KP)
                            </h2>
                            <?php if ($totalKadaluarsa > 0): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                <?= $totalKadaluarsa ?> Mahasiswa
                            </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar mahasiswa yang masa berlaku skripsi/KP telah kadaluarsa atau stagnan melewati batas waktu</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="/redesain-siatek/administrator/mhs-skripsi" class="text-xs font-semibold text-rose-700 hover:text-rose-800 transition">
                        Kelola Skripsi &rarr;
                    </a>
                </div>
            </div>

            <?php if ($totalKadaluarsa === 0): ?>
                <div class="px-5 py-8 text-center text-slate-400">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 mb-2">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                    <p class="text-xs font-medium text-slate-700">Tidak ada mahasiswa dengan status kadaluarsa.</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Seluruh skripsi dan kerja praktek di program studi ini berjalan aktif dalam batas waktu normal.</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-100 max-h-[380px] overflow-y-auto">
                    <?php foreach (array_slice($kadaluarsaList, 0, 8) as $kd): 
                        $isSkr = ($kd['jenis'] === 'Skripsi');
                        $linkTarget = $isSkr ? '/redesain-siatek/administrator/mhs-skripsi' : '/redesain-siatek/administrator/mhs-kp';
                        $badgeBg = $isSkr ? 'bg-orange-50 text-orange-700 border-orange-200/60' : 'bg-sky-50 text-sky-700 border-sky-200/60';
                        $tglUpdate = !empty($kd['last_time']) ? date('d M Y', (int)$kd['last_time']) : '-';
                    ?>
                    <div class="p-3.5 px-5 hover:bg-rose-50/20 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold border <?= $badgeBg ?>">
                                    <?= htmlspecialchars($kd['jenis']) ?>
                                </span>
                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                    Kadaluarsa
                                </span>
                                <h4 class="text-xs font-bold text-slate-900"><?= htmlspecialchars($kd['nama_mhs']) ?></h4>
                                <span class="font-mono text-xs text-slate-400">(<?= htmlspecialchars($kd['nim']) ?>)</span>
                                <?php if (!empty($kd['angkatan'])): ?>
                                    <span class="text-[10px] text-slate-400 font-semibold">&bull; Angkatan <?= htmlspecialchars((string)$kd['angkatan']) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($kd['judul'])): ?>
                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-1 italic">
                                    &ldquo;<?= htmlspecialchars($kd['judul']) ?>&rdquo;
                                </p>
                            <?php endif; ?>
                            <div class="mt-1 flex items-center gap-2 text-[10px] text-slate-400">
                                <span><i class="far fa-clock mr-1 text-slate-400"></i>Aktivitas Terakhir: <?= htmlspecialchars($tglUpdate) ?></span>
                                <?php if (!empty($kd['latest_step'])): ?>
                                    <span>&bull;</span>
                                    <span>Tahap SOP: <?= (int)$kd['latest_step'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <a href="<?= $linkTarget ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 hover:text-slate-900 text-xs font-semibold shadow-2xs shrink-0 transition">
                            <span>Tinjau Status</span> &rarr;
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($totalKadaluarsa > 8): ?>
                <div class="px-5 py-2.5 bg-slate-50/60 border-t border-slate-100 text-center">
                    <span class="text-xs font-medium text-slate-500">
                        Menampilkan 8 dari total <?= $totalKadaluarsa ?> mahasiswa kadaluarsa di prodi ini.
                    </span>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>

        <!-- 2. Tabel Distribusi Mahasiswa Prodi per Angkatan (7 Tahun Terakhir) -->
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 bg-slate-50/70">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Distribusi Mahasiswa <?= htmlspecialchars($namaProdiUser) ?> per Angkatan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Progres Kerja Praktek, Skripsi, dan Alumni 7 tahun terakhir</p>
                </div>
                <?php
                $totalMasaProdi = 0;
                foreach ($perAngkatanProdi as $pa) { $totalMasaProdi += $pa['kp'] + $pa['skripsi']; }
                ?>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 shadow-2xs">
                    <i class="fas fa-graduation-cap text-[#f97316]"></i> <?= f($totalMasaProdi) ?> Mahasiswa Berprogres
                </span>
            </div>
            <?php if (count($perAngkatanProdi) === 0): ?>
                <p class="py-10 text-center text-xs text-slate-400">Belum ada data mahasiswa per angkatan di prodi ini.</p>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs text-slate-500 font-semibold uppercase tracking-wider bg-slate-50/40">
                            <th class="py-3 px-5">Tahun Angkatan</th>
                            <th class="py-3 px-4 text-right">Total Mhs</th>
                            <th class="py-3 px-4 text-right">Sedang KP</th>
                            <th class="py-3 px-4 text-right">Sedang Skripsi</th>
                            <th class="py-3 px-5 text-right">Kelulusan / Alumni</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        <?php foreach ($perAngkatanProdi as $pa):
                            $pctAlumni = $pa['total'] > 0 ? round(($pa['alumni'] / $pa['total']) * 100) : 0;
                        ?>
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-5 font-semibold text-slate-800">
                                <span class="inline-flex items-center gap-2">
                                    <span class="flex h-6 w-6 items-center justify-center rounded bg-slate-100 text-xs font-bold text-slate-700">
                                        <?= substr((string)$pa['angkatan'], 2, 2) ?>
                                    </span>
                                    Angkatan <?= $pa['angkatan'] ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-medium text-slate-700"><?= f($pa['total']) ?></td>
                            <td class="py-3 px-4 text-right">
                                <span class="inline-flex items-center font-mono font-semibold text-xs text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                                    <?= f($pa['kp']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <span class="inline-flex items-center font-mono font-semibold text-xs text-orange-700 bg-orange-50 border border-orange-200/60 px-2 py-0.5 rounded">
                                    <?= f($pa['skripsi']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-5 text-right">
                                <div class="flex items-center justify-end gap-2.5">
                                    <span class="text-xs font-mono font-semibold text-slate-600"><?= f($pa['alumni']) ?> mhs</span>
                                    <div class="w-16 h-2 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-[#1a365d] transition-all" style="width:<?= min(100, max($pctAlumni > 0 ? 4 : 0, $pctAlumni)) ?>%"></div>
                                    </div>
                                    <span class="text-xs font-mono font-bold text-slate-800 w-10 text-right"><?= $pctAlumni ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </section>

        <!-- 3. Dua Grafik Analitik Prodi (Twin Analisis Intake vs Kelulusan) -->
        <section class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Tren Pendaftaran Mahasiswa</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Intake 5 tahun terakhir (<?= htmlspecialchars($namaProdiUser) ?>)</p>
                    </div>
                    <span class="text-[11px] font-semibold text-orange-700 bg-orange-50 border border-orange-200/60 px-2 py-0.5 rounded">Mahasiswa Baru</span>
                </div>
                <div style="height:200px"><canvas id="enrollmentChart"></canvas></div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Tren Kelulusan / Alumni</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Lulusan 5 tahun terakhir (<?= htmlspecialchars($namaProdiUser) ?>)</p>
                    </div>
                    <span class="text-[11px] font-semibold text-sky-700 bg-sky-50 border border-sky-200/60 px-2 py-0.5 rounded">Alumni</span>
                </div>
                <div style="height:200px"><canvas id="alumniChart"></canvas></div>
            </div>
        </section>

    </div>

    <!-- ===== KOLOM KANAN (4 KOLOM / 34%): TELEMETRI, PINTASAN & PENGUMUMAN ===== -->
    <div class="lg:col-span-4 space-y-5">

        <!-- 1. Ringkasan Status Akademik Prodi -->
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-800">Telemetri Akademik Prodi</h3>
                <p class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($namaProdiUser) ?></p>
            </div>
            <div class="space-y-3">
                <!-- Antrean Siap Ujian (Data Telemetri Prodi) -->
                <?php if ($totalAntreanUjian > 0): ?>
                <div class="p-3 rounded-lg bg-amber-50/60 border border-amber-200/70 flex items-center justify-between">
                    <span class="text-xs text-slate-700 font-medium flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75" style="animation-duration: 3s;"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                        Antrean Siap Ujian
                    </span>
                    <span class="text-xs font-mono font-bold text-amber-700 bg-white border border-amber-200/60 px-2 py-0.5 rounded shadow-2xs">
                        <?= f($totalAntreanUjian) ?> mhs
                    </span>
                </div>
                <?php else: ?>
                <div class="p-3 rounded-lg bg-slate-50/80 border border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-600 font-medium">Antrean Siap Ujian</span>
                    <span class="text-xs font-mono font-bold text-slate-400">0 mhs</span>
                </div>
                <?php endif; ?>

                <div class="p-3 rounded-lg bg-slate-50/80 border border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-600 font-medium">Mahasiswa Aktif (7 Thn)</span>
                    <span class="text-xs font-mono font-bold text-slate-900"><?= f($mhsAktifProdi) ?> mhs</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50/80 border border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-600 font-medium">Mahasiswa Sedang Skripsi</span>
                    <span class="text-xs font-mono font-bold text-orange-600"><?= f($skripsiProdiAktif) ?> mhs</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50/80 border border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-600 font-medium">Mahasiswa Sedang KP</span>
                    <span class="text-xs font-mono font-bold text-sky-600"><?= f($kpProdiAktif) ?> mhs</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50/80 border border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-600 font-medium">Mahasiswa Kadaluarsa</span>
                    <span class="text-xs font-mono font-bold text-rose-600"><?= f($totalKadaluarsa) ?> mhs</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50/80 border border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-600 font-medium">Total Alumni Terdata</span>
                    <span class="text-xs font-mono font-bold text-emerald-600"><?= f($totalAlumniProdi) ?> orang</span>
                </div>
            </div>
        </section>

        <!-- 2. Hub Pintasan Otoritas Kaprodi -->
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Pusat Otoritas Kaprodi</h3>
            <div class="grid grid-cols-1 gap-2">
                <a href="/redesain-siatek/administrator/mhs-penunjukkan" class="flex items-center gap-3 p-3 rounded-lg border border-orange-200 bg-orange-50/30 hover:border-orange-400 hover:bg-orange-50/70 transition group">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 text-orange-600 text-xs shrink-0"><i class="fas fa-user-check"></i></span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-slate-800 group-hover:text-orange-600 transition">Penunjukkan Mahasiswa</h4>
                            <?php if ($totalAntreanPenunjukkan > 0): ?>
                                <span class="text-[10px] font-bold text-orange-700 bg-orange-200/80 px-1.5 py-0.2 rounded-full"><?= $totalAntreanPenunjukkan ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="text-[11px] text-slate-500 truncate">Penetapan dosen pembimbing &amp; penguji.</p>
                    </div>
                </a>

                <a href="/redesain-siatek/administrator/mhs-skripsi" class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-orange-200 hover:bg-orange-50/40 transition group">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs shrink-0"><i class="fas fa-book-open"></i></span>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-semibold text-slate-800 group-hover:text-orange-600 transition">Monitoring Skripsi Prodi</h4>
                        <p class="text-[11px] text-slate-400 truncate">Status judul &amp; progres tahapan SOP.</p>
                    </div>
                </a>

                <a href="/redesain-siatek/administrator/mhs-kp" class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-orange-200 hover:bg-orange-50/40 transition group">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs shrink-0"><i class="fas fa-briefcase"></i></span>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-semibold text-slate-800 group-hover:text-orange-600 transition">Monitoring Kerja Praktek</h4>
                        <p class="text-[11px] text-slate-400 truncate">Pelaksanaan &amp; seminar KP prodi.</p>
                    </div>
                </a>

                <a href="/redesain-siatek/administrator/pengguna-mahasiswa" class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-orange-200 hover:bg-orange-50/40 transition group">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs shrink-0"><i class="fas fa-users"></i></span>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-semibold text-slate-800 group-hover:text-orange-600 transition">Data Mahasiswa Prodi</h4>
                        <p class="text-[11px] text-slate-400 truncate">Database mahasiswa per angkatan.</p>
                    </div>
                </a>

                <a href="/redesain-siatek/administrator/bimbingan-skripsi" class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-orange-200 hover:bg-orange-50/40 transition group">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs shrink-0"><i class="fas fa-chalkboard-user"></i></span>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-semibold text-slate-800 group-hover:text-orange-600 transition">Rekap Bimbingan Dosen</h4>
                        <p class="text-[11px] text-slate-400 truncate">Pemerataan kuota bimbingan skripsi &amp; KP.</p>
                    </div>
                </a>
            </div>
        </section>

        <!-- 3. Berita Jurusan Terkini -->
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

<!-- Data untuk Chart.js -->
<script>
window.__berandaData = {
    enrollment: {
        labels: <?= json_encode($years) ?>,
        values: <?= json_encode($mhsPerYear) ?>
    },
    alumni: {
        labels: <?= json_encode($years) ?>,
        values: <?= json_encode($alumniPerYear) ?>
    }
};
</script>
