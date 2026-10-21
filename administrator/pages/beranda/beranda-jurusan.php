<?php
/**
 * View Beranda: Pengelola Jurusan (Level 6 - Kajur / Sekjur)
 * Dashboard Eksekutif Jurusan Teknik Elektro & Komputer
 * Monitoring Makro 3 Program Studi, Jadwal Terpadu & Agenda Jurusan.
 */
declare(strict_types=1);

// --- Mahasiswa aktif per prodi ---
$qProdi = query("SELECT * FROM prodi ORDER BY nama");
$prodiList = [];
$totalMhs = 0;
$aktifSince = date('Y') - 7;
while ($p = mysqli_fetch_assoc($qProdi)) {
    $q = query("SELECT COUNT(*) AS c FROM mahasiswa
                WHERE prodi='" . bersihkan($p['kode']) . "'
                  AND angkatan >= " . (int)$aktifSince . " AND status='Aktif'");
    $p['mahasiswa'] = (int)mysqli_fetch_assoc($q)['c'];
    $prodiList[] = $p;
    $totalMhs += (int)$p['mahasiswa'];
}

$mhsTE = 0; $mhsTK = 0; $mhsPTE = 0;
$labelTE = 'S1 Teknik Elektro';
$labelTK = 'S1 Teknik Komputer';
$labelPTE = 'S1 Rekayasa Elektro';

foreach ($prodiList as $pr) {
    if (stripos($pr['nama'], 'Komputer') !== false) {
        $mhsTK += (int)$pr['mahasiswa'];
        $labelTK = $pr['nama'];
    } elseif (stripos($pr['nama'], 'Vokasional') !== false || stripos($pr['nama'], 'Pendidikan') !== false || stripos($pr['nama'], 'Rekayasa') !== false) {
        $mhsPTE += (int)$pr['mahasiswa'];
        $labelPTE = $pr['nama'];
    } else {
        $mhsTE += (int)$pr['mahasiswa'];
        $labelTE = $pr['nama'];
    }
}

$pctTE  = $totalMhs > 0 ? round(($mhsTE / $totalMhs) * 100) : 0;
$pctTK  = $totalMhs > 0 ? round(($mhsTK / $totalMhs) * 100) : 0;
$pctPTE = $totalMhs > 0 ? max(0, 100 - $pctTE - $pctTK) : 0;

$qMhsTahunIni = query("SELECT COUNT(*) AS c FROM mahasiswa WHERE angkatan=" . (int)date('Y'));
$mhsBaruTahunIni = (int)mysqli_fetch_assoc($qMhsTahunIni)['c'];

$qDosenAktif = query("SELECT COUNT(*) AS c FROM dosen WHERE status='aktif'");
$dosenAktif = (int)mysqli_fetch_assoc($qDosenAktif)['c'];

$qSkripsiAktif = query("SELECT COUNT(*) AS c FROM skripsi WHERE status='aktif'");
$skripsiAktif = (int)mysqli_fetch_assoc($qSkripsiAktif)['c'];
$qKpAktif = query("SELECT COUNT(*) AS c FROM kp WHERE status='aktif'");
$kpAktif = (int)mysqli_fetch_assoc($qKpAktif)['c'];

$qTTDPending = query("SELECT COUNT(*) AS c FROM surat_penunjukkan WHERE ttd='' OR ttd IS NULL");
$ttdPendingTotal = (int)mysqli_fetch_assoc($qTTDPending)['c'];
$qTTD = query("SELECT sp.*, m.nama FROM surat_penunjukkan sp
               LEFT JOIN mahasiswa m ON sp.nim=m.nim
               WHERE sp.ttd='' OR sp.ttd IS NULL ORDER BY sp.id DESC LIMIT 5");
$ttdItems = [];
while ($t = mysqli_fetch_assoc($qTTD)) { $ttdItems[] = $t; }

$qBerkasPending = query("
    SELECT ts.id, ts.nim, ts.file, ts.status, m.nama AS nama_mhs, m.prodi, 'Skripsi' AS tipe, pbs.nama_berkas
    FROM temp_skripsi ts
    JOIN mahasiswa m ON ts.nim = m.nim
    LEFT JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id
    WHERE ts.status = 'menunggu'
    UNION ALL
    SELECT tk.id, tk.nim, tk.file, tk.status, m.nama AS nama_mhs, m.prodi, 'KP' AS tipe, pbk.nama_berkas
    FROM temp_kp tk
    JOIN mahasiswa m ON tk.nim = m.nim
    LEFT JOIN persyaratan_berkas_kp pbk ON tk.id_persyaratan = pbk.id
    WHERE tk.status = 'menunggu'
    ORDER BY id DESC LIMIT 5
");
$berkasPendingList = [];
if ($qBerkasPending) {
    while ($bp = mysqli_fetch_assoc($qBerkasPending)) { $berkasPendingList[] = $bp; }
}

$qTotalBerkasPending = query("
    SELECT (
        (SELECT COUNT(*) FROM temp_skripsi WHERE status='menunggu') +
        (SELECT COUNT(*) FROM temp_kp WHERE status='menunggu')
    ) AS c
");
$totalBerkasPending = $qTotalBerkasPending ? (int)mysqli_fetch_assoc($qTotalBerkasPending)['c'] : 0;

// Jadwal Kuliah Jurusan Lengkap
$hariMap = [
    'Sunday' => 'MINGGU', 'Monday' => 'SENIN', 'Tuesday' => 'SELASA',
    'Wednesday' => 'RABU', 'Thursday' => 'KAMIS', 'Friday' => 'JUMAT', 'Saturday' => 'SABTU'
];
$hariIni = $hariMap[date('l')] ?? 'SENIN';
$hariList = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
$nowMinutes = (int)date('H') * 60 + (int)date('i');

// Ambil jadwal lengkap seluruh hari dari database agar dinamis mengikuti hari & jam
$qJadwalSemuaAdmin = query("
    SELECT jk.id, jk.hari, jk.jam, jk.kelas, jk.ruangan,
           rk.nama AS nama_ruangan,
           mk.nama AS nama_matkul, mk.kode AS kode_matkul, mk.sks,
           d.nama AS nama_dosen
    FROM jadwal_kuliah jk
    LEFT JOIN matakuliah mk ON jk.id_matakuliah = mk.id
    LEFT JOIN dosen d ON (jk.pengajar1 = d.id OR jk.pengajar1 = d.nip)
    LEFT JOIN ruang_kuliah rk ON jk.ruangan = rk.id
    ORDER BY jk.jam ASC
");

$jadwalSemuaPerHari = [
    'SENIN' => [], 'SELASA' => [], 'RABU' => [], 'KAMIS' => [], 'JUMAT' => [], 'SABTU' => []
];

if ($qJadwalSemuaAdmin) {
    while ($jh = mysqli_fetch_assoc($qJadwalSemuaAdmin)) {
        $hUpper = strtoupper(trim((string)$jh['hari']));
        if (!isset($jadwalSemuaPerHari[$hUpper])) {
            $jadwalSemuaPerHari[$hUpper] = [];
        }

        $jamStr = trim((string)($jh['jam'] ?? ''));
        $startM = null;
        $endM   = null;
        if (preg_match('/(\d{1,2})[:.](\d{2})\s*[-–—]\s*(\d{1,2})[:.](\d{2})/', $jamStr, $mJam)) {
            $startM = (int)$mJam[1] * 60 + (int)$mJam[2];
            $endM   = (int)$mJam[3] * 60 + (int)$mJam[4];
        }

        $statusWaktu = 'mendatang';
        if ($hUpper === $hariIni && $startM !== null && $endM !== null) {
            if ($nowMinutes >= $startM && $nowMinutes <= $endM) {
                $statusWaktu = 'berlangsung';
            } elseif ($nowMinutes > $endM) {
                $statusWaktu = 'selesai';
            } else {
                $statusWaktu = 'akan_datang';
            }
        }

        $jh['start_m']        = $startM;
        $jh['end_m']          = $endM;
        $jh['status_waktu']   = $statusWaktu;
        $jh['ruangan_tampil'] = !empty($jh['nama_ruangan']) ? $jh['nama_ruangan'] : ($jh['ruangan'] ?: 'Ruang Kuliah');

        $jadwalSemuaPerHari[$hUpper][] = $jh;
    }
}

// Urutkan jadwal hari ini secara dinamis (Sedang Berlangsung -> Akan Datang -> Selesai)
if (isset($jadwalSemuaPerHari[$hariIni])) {
    usort($jadwalSemuaPerHari[$hariIni], function($a, $b) {
        $prio = ['berlangsung' => 1, 'akan_datang' => 2, 'selesai' => 3, 'mendatang' => 4];
        $pA = $prio[$a['status_waktu']] ?? 5;
        $pB = $prio[$b['status_waktu']] ?? 5;
        if ($pA !== $pB) {
            return $pA <=> $pB;
        }
        return ($a['start_m'] ?? 0) <=> ($b['start_m'] ?? 0);
    });
}

// Hitung rekap sesi hari ini
$jadwalHariIniAdmin = $jadwalSemuaPerHari[$hariIni] ?? [];
$countBerlangsung = 0;
$countAkanDatang  = 0;
$countSelesai     = 0;
foreach ($jadwalHariIniAdmin as $jItem) {
    if ($jItem['status_waktu'] === 'berlangsung') $countBerlangsung++;
    elseif ($jItem['status_waktu'] === 'akan_datang') $countAkanDatang++;
    elseif ($jItem['status_waktu'] === 'selesai') $countSelesai++;
}
$totalHariIni = count($jadwalHariIniAdmin);

// Tren mahasiswa baru 5 tahun
$years = [];
$mhsPerYear = [];
for ($y = date('Y') - 4; $y <= date('Y'); $y++) {
    $years[] = (string)$y;
    $q = query("SELECT COUNT(*) AS c FROM mahasiswa WHERE angkatan=$y");
    $mhsPerYear[] = (int)mysqli_fetch_assoc($q)['c'];
}

// Distribusi per prodi
$concLabels = [];
$concValues = [];
$concColors = [];
foreach ($prodiList as $pr) {
    $namaRingkas = preg_replace('/^S1\s+/i', '', $pr['nama']);
    $namaRingkas = str_ireplace('Pendidikan Vokasional ', '', $namaRingkas);
    $namaRingkas = trim($namaRingkas) ?: $pr['nama'];
    $concLabels[] = $namaRingkas;
    $concValues[] = (int)$pr['mahasiswa'];
    $concColors[] = beranda_pick_color($pr['nama']);
}

// Batch Ujian & Sidang (Dihitung Dinamis Sesuai Status & Tanggal Berjalan)
$agendaTimeline = [];
$totalBatchBuka = 0;
$nowTs = time();

$qBatch = query("
    SELECT b.id, b.tanggal_buka, b.tanggal_tutup, b.pembuat, b.status,
           d.nama AS nama_pembuat,
           (SELECT COUNT(*) FROM peserta_batch pb WHERE pb.id_batch = b.id) AS total_peserta
    FROM batch_ujian b
    LEFT JOIN dosen d ON (b.pembuat = d.id OR b.pembuat = d.nip)
    ORDER BY b.id DESC LIMIT 3
");

if ($qBatch) {
    while ($b = mysqli_fetch_assoc($qBatch)) {
        $idBatch      = (int)$b['id'];
        $totalPeserta = (int)($b['total_peserta'] ?? 0);
        $pembuatNama  = !empty($b['nama_pembuat']) ? $b['nama_pembuat'] : (!empty($b['pembuat']) ? $b['pembuat'] : 'Admin Akademik');

        $tglBuka  = $b['tanggal_buka'];
        $tglTutup = $b['tanggal_tutup'];
        $bukaTs   = strtotime($tglBuka . ' 00:00:00');
        $tutupTs  = strtotime($tglTutup . ' 23:59:59');

        $statusDb = strtolower(trim((string)($b['status'] ?? 'buka')));
        $isManuallyClosed = in_array($statusDb, ['tutup', 'selesai', 'closed'], true);
        $isAktif = (!$isManuallyClosed && $tutupTs && $nowTs <= $tutupTs && $nowTs >= $bukaTs);

        if ($isAktif) {
            $totalBatchBuka++;
            $statusLabel = 'Dibuka';
            $statusColor = 'text-emerald-700 bg-emerald-50 border-emerald-200';
            $ringColor   = 'ring-emerald-500';
            $dotColor    = 'bg-emerald-500';
        } elseif (!$isManuallyClosed && $bukaTs && $nowTs < $bukaTs) {
            $statusLabel = 'Mendatang';
            $statusColor = 'text-blue-700 bg-blue-50 border-blue-200';
            $ringColor   = 'ring-blue-500';
            $dotColor    = 'bg-blue-500';
        } else {
            $statusLabel = 'Ditutup';
            $statusColor = 'text-slate-500 bg-slate-100 border-slate-200';
            $ringColor   = 'ring-slate-300';
            $dotColor    = 'bg-slate-400';
        }

        $tglBukaFmt  = function_exists('tgl_indo') ? tgl_indo($tglBuka)  : $tglBuka;
        $tglTutupFmt = function_exists('tgl_indo') ? tgl_indo($tglTutup) : $tglTutup;

        $agendaTimeline[] = [
            'tipe'         => 'batch',
            'is_aktif'     => $isAktif,
            'judul'        => 'Batch Ujian Mahasiswa #' . $idBatch,
            'deskripsi'    => $totalPeserta . ' Mahasiswa Terdaftar &bull; ' . htmlspecialchars($pembuatNama),
            'tanggal_fmt'  => $tglBukaFmt . ' s/d ' . $tglTutupFmt,
            'status'       => $statusLabel,
            'status_color' => $statusColor,
            'ring_color'   => $ringColor,
            'dot_color'    => $dotColor,
            'link'         => '/redesain-siatek/administrator/mhs-ujian'
        ];
    }
}

$qSidang = query("
    SELECT s.nim, m.nama AS nama_mhs, s.tanggal_hasil AS tgl, s.jam_hasil AS jam, s.ruangan_hasil AS ruangan, 'Seminar Hasil' AS jenis
    FROM skripsi s JOIN mahasiswa m ON s.nim = m.nim
    WHERE s.tanggal_hasil IS NOT NULL AND s.tanggal_hasil > '2000-01-01'
    UNION ALL
    SELECT s.nim, m.nama AS nama_mhs, s.tanggal_tutup AS tgl, s.jam_tutup AS jam, s.ruangan_tutup AS ruangan, 'Sidang Tutup' AS jenis
    FROM skripsi s JOIN mahasiswa m ON s.nim = m.nim
    WHERE s.tanggal_tutup IS NOT NULL AND s.tanggal_tutup > '2000-01-01'
    ORDER BY tgl DESC LIMIT 4
");
if ($qSidang) {
    while ($sd = mysqli_fetch_assoc($qSidang)) {
        $tglSdFmt = function_exists('tgl_indo') ? tgl_indo($sd['tgl']) : $sd['tgl'];
        $isSidangPast = strtotime($sd['tgl'] . ' 23:59:59') < $nowTs;
        $statusSidang = $isSidangPast ? 'Selesai' : 'Terjadwal';
        $statusColor = $isSidangPast ? 'text-slate-500 bg-slate-100 border-slate-200' : 'text-[#1a365d] bg-blue-50 border-blue-200';
        $ringColor = $isSidangPast ? 'ring-slate-300' : 'ring-[#1a365d]';
        $dotColor = $isSidangPast ? 'bg-slate-400' : 'bg-[#1a365d]';

        $agendaTimeline[] = [
            'tipe'         => 'sidang',
            'is_aktif'     => !$isSidangPast,
            'judul'        => $sd['jenis'] . ' — ' . ($sd['nama_mhs'] ?: $sd['nim']),
            'deskripsi'    => ($sd['ruangan'] ?: 'Ruang Ujian') . (!empty($sd['jam']) ? ' &bull; Pukul ' . $sd['jam'] : ''),
            'tanggal_fmt'  => $tglSdFmt,
            'status'       => $statusSidang,
            'status_color' => $statusColor,
            'ring_color'   => $ringColor,
            'dot_color'    => $dotColor,
            'link'         => '/redesain-siatek/administrator/mhs-ujian'
        ];
    }
}

// Berita
$beritaItems = [];
$qBerita = query("SELECT judul, deskripsi, tanggal FROM berita WHERE judul <> '' ORDER BY tanggal DESC LIMIT 3");
if ($qBerita) {
    while ($b = mysqli_fetch_assoc($qBerita)) { $beritaItems[] = $b; }
}

// Distribusi per angkatan
$perAngkatan = [];
$tahunSekarang = (int)date('Y');
$qAngkatan = query("
    SELECT angkatan FROM (
        SELECT DISTINCT angkatan FROM mahasiswa WHERE angkatan > 0
        UNION
        SELECT DISTINCT CAST(angkatan AS UNSIGNED) AS angkatan FROM alumni WHERE angkatan > 0
    ) AS semua_angkatan
    ORDER BY angkatan DESC
");
if ($qAngkatan) {
    while ($a = mysqli_fetch_assoc($qAngkatan)) {
        $ang = (int)$a['angkatan'];
        if ($ang <= 0) continue;

        $qKpA = query("SELECT COUNT(DISTINCT k.nim) AS c
                       FROM kp k JOIN mahasiswa m ON k.nim=m.nim
                       WHERE k.status='aktif' AND m.angkatan=$ang");
        $kpAktifAng = $qKpA ? (int)mysqli_fetch_assoc($qKpA)['c'] : 0;

        $qSkA = query("SELECT COUNT(DISTINCT s.nim) AS c
                       FROM skripsi s JOIN mahasiswa m ON s.nim=m.nim
                       WHERE s.status='aktif' AND m.angkatan=$ang");
        $skAktifAng = $qSkA ? (int)mysqli_fetch_assoc($qSkA)['c'] : 0;

        // Ambil data alumni riil dari tabel alumni (dan mahasiswa status Alumni jika ada)
        $qAlmAng = query("
            SELECT COUNT(DISTINCT nim) AS c FROM (
                SELECT nim FROM alumni WHERE angkatan = '$ang' OR angkatan = $ang
                UNION
                SELECT a.nim FROM alumni a JOIN mahasiswa m ON a.nim = m.nim WHERE m.angkatan = $ang
                UNION
                SELECT nim FROM mahasiswa WHERE angkatan = $ang AND LOWER(status) = 'alumni'
            ) AS gabungan_alumni
        ");
        $alumniAng = $qAlmAng ? (int)mysqli_fetch_assoc($qAlmAng)['c'] : 0;

        // Total populasi angkatan: mahasiswa terdaftar + alumni terdaftar
        $qTotAng = query("
            SELECT COUNT(DISTINCT nim) AS c FROM (
                SELECT nim FROM mahasiswa WHERE angkatan = $ang
                UNION
                SELECT nim FROM alumni WHERE angkatan = '$ang' OR angkatan = $ang
            ) AS gabungan_populasi
        ");
        $totalAng = $qTotAng ? (int)mysqli_fetch_assoc($qTotAng)['c'] : 0;
        if ($totalAng <= 0 && $alumniAng > 0) {
            $totalAng = $alumniAng;
        }

        $perAngkatan[] = [
            'angkatan' => $ang,
            'total'    => $totalAng,
            'kp'       => $kpAktifAng,
            'skripsi'  => $skAktifAng,
            'alumni'   => $alumniAng,
        ];
    }
}

$minang = ((int)date('n') >= 8) ? $tahunSekarang - 6 : $tahunSekarang - 7;
$maxang = ((int)date('n') >= 8) ? $tahunSekarang     : $tahunSekarang - 1;
$perAngkatanTampil = [];
foreach ($perAngkatan as $pa) {
    if ($pa['angkatan'] >= $minang && $pa['angkatan'] <= $maxang) {
        $perAngkatanTampil[] = $pa;
    }
}
usort($perAngkatanTampil, fn($x, $y) => $x['angkatan'] <=> $y['angkatan']);

// Waktu saat ini dalam menit untuk deteksi jadwal aktif riil
$nowMinutes = (int)date('H') * 60 + (int)date('i');
?>

<!-- ============ PAGE HEADER (KONSISTEN SIATEK) ============ -->
<section class="flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3.5">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600 shadow-2xs">
            <i class="fas fa-shield-halved"></i>
        </div>
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-800">
                Selamat Datang, <?= htmlspecialchars($nmUser) ?>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                <?= htmlspecialchars($roleTeks) ?> &bull; Jurusan Teknik Elektro &amp; Komputer &bull; <span class="font-medium text-slate-700"><?= htmlspecialchars($today_label) ?></span>
            </p>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="/redesain-siatek/administrator/jurusan-surat-penunjukkan"
           class="inline-flex items-center gap-2 rounded-lg bg-[#f97316] hover:bg-[#ea580c] px-3.5 py-2 text-xs font-semibold text-white shadow-sm shadow-orange-500/25 transition">
            <i class="fas fa-file-signature"></i> TTD Surat
            <?php if ($ttdPendingTotal > 0): ?>
                <span class="rounded-full bg-white/20 px-1.5 py-0.2 text-[10px] font-bold"><?= $ttdPendingTotal ?></span>
            <?php endif; ?>
        </a>
        <a href="/redesain-siatek/administrator/mhs-verifikasi"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
            <i class="fas fa-clipboard-check text-orange-500"></i> Verifikasi Berkas (<?= $totalBerkasPending ?>)
        </a>
        <a href="/redesain-siatek/administrator/mhs-ujian"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
            <i class="fas fa-calendar-check text-[#1a365d]"></i> Kelola Ujian
        </a>
    </div>
</section>

<!-- ============ 4 KARTU METRIK UTAMA DENGAN 1 KARTU JANGKAR NAVY ============ -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- 1. Total Mahasiswa Aktif (dengan Micro Sparkline) -->
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Mahasiswa Aktif</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs">
                    <i class="fas fa-users"></i>
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between gap-2">
                <p class="text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($totalMhs) ?></p>
                <!-- Mini Sparkline SVG -->
                <?php if (count($mhsPerYear) > 1): ?>
                <div class="shrink-0" title="Tren intake 5 tahun">
                    <svg width="68" height="24" viewBox="0 0 120 36" class="overflow-visible stroke-[#1a365d] fill-none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="<?= spark_path($mhsPerYear, 120, 36) ?>" />
                    </svg>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Intake tahun <?= date('Y') ?></span>
            <span class="font-bold text-orange-600">+<?= f($mhsBaruTahunIni) ?> baru</span>
        </div>
    </div>

    <!-- 2. Dosen Pengajar -->
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Dosen Pengajar</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs">
                    <i class="fas fa-chalkboard-user"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($dosenAktif) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Rasio dosen : mahasiswa</span>
            <span class="font-semibold text-slate-700">1 : <?= $dosenAktif > 0 ? round($totalMhs / $dosenAktif) : 0 ?></span>
        </div>
    </div>

    <!-- 3. Skripsi & Kerja Praktek -->
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Bimbingan Aktif</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 text-orange-600 text-xs">
                    <i class="fas fa-book-open"></i>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($skripsiAktif) ?></span>
                <span class="text-xs font-semibold text-slate-500">Skripsi</span>
                <span class="text-slate-300">&bull;</span>
                <span class="text-xl font-bold font-mono text-slate-700"><?= f($kpAktif) ?></span>
                <span class="text-xs font-semibold text-slate-500">KP</span>
            </div>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Total mahasiswa dibimbing</span>
            <span class="font-semibold text-slate-700"><?= f($skripsiAktif + $kpAktif) ?> orang</span>
        </div>
    </div>

    <!-- 4. KARTU JANGKAR EKSEKUTIF (NAVY ANCHOR CARD - TITIK FOKUS UTAMA) -->
    <div class="rounded-xl bg-[#1a365d] text-white p-5 shadow-md shadow-slate-900/10 flex flex-col justify-between relative overflow-hidden">
        <!-- Aksen visual halus penambah kedalaman -->
        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-white/5 pointer-events-none"></div>
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-widest text-orange-300 flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                    Menunggu Tindakan
                </span>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 text-orange-300 text-xs">
                    <i class="fas fa-clock"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl sm:text-4xl font-bold font-mono tracking-tight text-white">
                <?= f($ttdPendingTotal + $totalBerkasPending) ?>
            </p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between text-xs">
            <span class="text-slate-300"><?= f($ttdPendingTotal) ?> TTD &bull; <?= f($totalBerkasPending) ?> Berkas</span>
            <a href="/redesain-siatek/administrator/jurusan-surat-penunjukkan" class="font-semibold text-orange-300 hover:text-orange-200 transition">
                Proses &rarr;
            </a>
        </div>
    </div>
</section>

<!-- ============ BENTO GRID ASIMETRIS (2/3 KANVAS ANALITIK VS 1/3 COMMAND STREAM) ============ -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

    <!-- ===== KOLOM KIRI (8 KOLOM / 66%): ANALITIK, PROGRES & JADWAL AKTIF ===== -->
    <div class="lg:col-span-8 space-y-5">

        <!-- 1. Distribusi Mahasiswa Program Studi (Terpadu & Proporsional) -->
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Distribusi Mahasiswa Program Studi</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Proporsi mahasiswa aktif sarjana per program studi</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                    <i class="fas fa-building-columns text-slate-400"></i> Total <?= f($totalMhs) ?> Mahasiswa
                </span>
            </div>

            <!-- Combined Progress Bar Tri-Warna Resmi -->
            <div class="w-full h-3 overflow-hidden rounded-full bg-slate-100 flex mb-4">
                <div class="h-full bg-[#1a365d] transition-all" style="width: <?= $pctTE ?>%" title="<?= htmlspecialchars($labelTE) ?>: <?= $pctTE ?>%"></div>
                <div class="h-full bg-[#f97316] transition-all" style="width: <?= $pctTK ?>%" title="<?= htmlspecialchars($labelTK) ?>: <?= $pctTK ?>%"></div>
                <div class="h-full bg-[#94a3b8] transition-all" style="width: <?= $pctPTE ?>%" title="<?= htmlspecialchars($labelPTE) ?>: <?= $pctPTE ?>%"></div>
            </div>

            <!-- 3 Kartu Rincian Prodi -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 bg-slate-50/60">
                    <span class="flex h-3 w-3 rounded-full bg-[#1a365d] shrink-0"></span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-semibold text-slate-800 truncate" title="<?= htmlspecialchars($labelTE) ?>"><?= htmlspecialchars($labelTE) ?></h4>
                            <span class="font-bold font-mono text-slate-900 text-sm"><?= f($mhsTE) ?></span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5"><?= $pctTE ?>% dari total jurusan</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 bg-slate-50/60">
                    <span class="flex h-3 w-3 rounded-full bg-[#f97316] shrink-0"></span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-semibold text-slate-800 truncate" title="<?= htmlspecialchars($labelTK) ?>"><?= htmlspecialchars($labelTK) ?></h4>
                            <span class="font-bold font-mono text-slate-900 text-sm"><?= f($mhsTK) ?></span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5"><?= $pctTK ?>% dari total jurusan</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 bg-slate-50/60">
                    <span class="flex h-3 w-3 rounded-full bg-[#94a3b8] shrink-0"></span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-semibold text-slate-800 truncate" title="<?= htmlspecialchars($labelPTE) ?>"><?= htmlspecialchars($labelPTE) ?></h4>
                            <span class="font-bold font-mono text-slate-900 text-sm"><?= f($mhsPTE) ?></span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5"><?= $pctPTE ?>% dari total jurusan</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. Tabel Distribusi Angkatan -->
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 bg-slate-50/70">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Distribusi Mahasiswa per Angkatan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Progres Kerja Praktek, Skripsi, dan Alumni 7 tahun terakhir</p>
                </div>
                <?php
                $totalMasa = 0;
                foreach ($perAngkatanTampil as $pa) { $totalMasa += $pa['kp'] + $pa['skripsi']; }
                ?>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 shadow-2xs">
                    <i class="fas fa-graduation-cap text-[#f97316]"></i> <?= f($totalMasa) ?> Mahasiswa Berprogres
                </span>
            </div>
            <?php if (count($perAngkatanTampil) === 0): ?>
                <p class="py-10 text-center text-xs text-slate-400">Belum ada data mahasiswa per angkatan.</p>
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
                        <?php foreach ($perAngkatanTampil as $pa):
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

        <!-- 3. Dua Grafik Analitik (Chart.js) -->
        <section class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Tren Pendaftaran Mahasiswa</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Intake 5 tahun terakhir</p>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">Tahunan</span>
                </div>
                <div style="height:200px"><canvas id="enrollmentChart"></canvas></div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-sm font-bold text-slate-800">Proporsi Program Studi</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Berdasarkan mahasiswa aktif</p>
                </div>
                <div style="height:200px"><canvas id="concentrationChart"></canvas></div>
            </div>
        </section>

        <!-- 4. Jadwal Perkuliahan Jurusan (Dinamis: Tab Hari, Live Status Berdasarkan Jam Real-time) -->
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <!-- Header Section -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-3.5 bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#1a365d] text-white shadow-2xs">
                        <i class="far fa-calendar-alt text-sm"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-slate-800">
                                Jadwal Perkuliahan Jurusan
                            </h3>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-800 border border-orange-200">
                                <?= htmlspecialchars($hariIni) ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Sesi perkuliahan aktif terpadu &bull; Sinkronisasi jam real-time</p>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-mono font-semibold text-slate-700 shadow-2xs">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="jadwalLiveClock"><?= date('H:i') ?> WITA</span>
                    </div>
                    <a href="/redesain-siatek/administrator/jadwal" class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition flex items-center gap-1">
                        <span>Jadwal Lengkap</span> &rarr;
                    </a>
                </div>
            </div>

            <!-- Tab Navigasi Hari (Senin s.d. Sabtu) -->
            <div class="flex items-center gap-1 px-3 py-2 bg-slate-100/70 border-b border-slate-200 overflow-x-auto text-xs">
                <?php foreach ($hariList as $h): 
                    $isToday = ($h === $hariIni);
                    $countH = count($jadwalSemuaPerHari[$h] ?? []);
                ?>
                    <button type="button" 
                            class="tab-jadwal-btn px-3 py-1.5 rounded-lg font-semibold transition flex items-center gap-1.5 shrink-0 <?= $isToday ? 'bg-[#1a365d] text-white shadow-2xs active-tab' : 'bg-white/60 text-slate-600 hover:text-slate-900 hover:bg-white border border-transparent' ?>"
                            data-target="panel-jadwal-<?= strtolower($h) ?>"
                            data-is-today="<?= $isToday ? '1' : '0' ?>">
                        <span><?= ucfirst(strtolower($h)) ?></span>
                        <?php if ($isToday): ?>
                            <span class="px-1.5 py-0.2 rounded-full bg-orange-500 text-white text-[9px] font-extrabold uppercase">Hari Ini</span>
                        <?php else: ?>
                            <span class="text-[10px] opacity-70">(<?= $countH ?>)</span>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Sub-filter Status Real-Time (Khusus Hari Ini) -->
            <div id="subFilterHariIni" class="flex flex-wrap items-center justify-between gap-2 px-5 py-2.5 bg-slate-50/60 border-b border-slate-100 text-xs">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button type="button" class="btn-filter-status px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#1a365d] text-white shadow-2xs transition" data-status="semua">
                        Semua (<?= $totalHariIni ?>)
                    </button>
                    <?php if ($countBerlangsung > 0): ?>
                        <button type="button" class="btn-filter-status px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 border border-emerald-200 text-emerald-800 hover:bg-emerald-100 transition flex items-center gap-1" data-status="berlangsung">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Sedang Berlangsung (<?= $countBerlangsung ?>)
                        </button>
                    <?php endif; ?>
                    <?php if ($countAkanDatang > 0): ?>
                        <button type="button" class="btn-filter-status px-2.5 py-1 rounded-md text-[11px] font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 transition" data-status="akan_datang">
                            Akan Datang (<?= $countAkanDatang ?>)
                        </button>
                    <?php endif; ?>
                    <?php if ($countSelesai > 0): ?>
                        <button type="button" class="btn-filter-status px-2.5 py-1 rounded-md text-[11px] font-semibold bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition" data-status="selesai">
                            Selesai (<?= $countSelesai ?>)
                        </button>
                    <?php endif; ?>
                </div>
                <div class="text-[11px] text-slate-400 font-medium">
                    <?php if ($countBerlangsung > 0): ?>
                        <span class="text-emerald-700 font-semibold"><i class="fas fa-circle text-[8px] mr-1 text-emerald-500 animate-ping"></i> <?= $countBerlangsung ?> kelas aktif saat ini</span>
                    <?php elseif ($countAkanDatang > 0): ?>
                        <span><?= $countAkanDatang ?> sesi berikutnya hari ini</span>
                    <?php else: ?>
                        <span>Seluruh sesi hari ini telah selesai</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Wadah Seluruh Panel Hari -->
            <div class="divide-y divide-slate-100 max-h-[540px] overflow-y-auto">
                <?php foreach ($hariList as $h):
                    $isHariIni = ($h === $hariIni);
                    $listJadwalH = $jadwalSemuaPerHari[$h] ?? [];
                ?>
                <div id="panel-jadwal-<?= strtolower($h) ?>" class="panel-jadwal-hari divide-y divide-slate-100 <?= $isHariIni ? '' : 'hidden' ?>">
                    <?php if (count($listJadwalH) === 0): ?>
                        <div class="px-5 py-10 text-center">
                            <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-2">
                                <i class="far fa-calendar-xmark text-base"></i>
                            </div>
                            <p class="text-xs font-semibold text-slate-600">Tidak ada jadwal perkuliahan pada hari <?= ucfirst(strtolower($h)) ?>.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Silakan pilih hari lain atau kelola master jadwal jurusan.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($listJadwalH as $jh):
                            $statusWaktu = $jh['status_waktu'] ?? 'mendatang';
                            $isLive = ($statusWaktu === 'berlangsung');
                            $isPast = ($statusWaktu === 'selesai');
                            $isUpcoming = ($statusWaktu === 'akan_datang');

                            // Row container styles
                            $rowBg = 'bg-white hover:bg-slate-50/80';
                            $borderLeft = 'border-l-4 border-l-transparent';
                            if ($isLive) {
                                $rowBg = 'bg-emerald-50/30 hover:bg-emerald-50/50';
                                $borderLeft = 'border-l-4 border-l-emerald-500';
                            } elseif ($isPast && $isHariIni) {
                                $rowBg = 'bg-slate-50/40 hover:bg-slate-50 opacity-75';
                            }
                        ?>
                        <div class="p-3.5 px-5 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 baris-jadwal <?= $rowBg ?> <?= $borderLeft ?>"
                             data-status="<?= $statusWaktu ?>">
                            
                            <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                <!-- Kolom Jam & Waktu Kuliah -->
                                <div class="w-28 shrink-0">
                                    <div class="font-mono text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                        <i class="far fa-clock <?= $isLive ? 'text-emerald-600' : 'text-slate-400' ?> text-xs"></i>
                                        <span><?= htmlspecialchars($jh['jam'] ?: '-') ?></span>
                                    </div>
                                    <div class="mt-1">
                                        <?php if ($isLive): ?>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-800 bg-emerald-100 border border-emerald-300 px-2 py-0.5 rounded-full">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                                Live Sekarang
                                            </span>
                                        <?php elseif ($isUpcoming): ?>
                                            <span class="inline-flex items-center text-[10px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full">
                                                Akan Datang
                                            </span>
                                        <?php elseif ($isPast && $isHariIni): ?>
                                            <span class="inline-flex items-center text-[10px] font-medium text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                                                Selesai
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center text-[10px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
                                                Terjadwal
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Detail Matakuliah, Kelas & Dosen -->
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <!-- Badge Kelas (Terpisah tegas dengan warna navy kontras) -->
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold bg-[#1a365d] text-white shadow-2xs tracking-wider shrink-0">
                                            KELAS <?= htmlspecialchars($jh['kelas'] ?? 'A') ?>
                                        </span>
                                        <!-- Nama Matakuliah -->
                                        <h4 class="text-sm font-bold text-slate-900 truncate">
                                            <?= htmlspecialchars($jh['nama_matkul'] ?: 'Mata Kuliah') ?>
                                        </h4>
                                        <?php if (!empty($jh['kode_matkul'])): ?>
                                            <span class="text-[11px] font-mono font-medium text-slate-400">
                                                (<?= htmlspecialchars($jh['kode_matkul']) ?>)
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Informasi Ruangan & Pengajar -->
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-slate-500">
                                        <span class="flex items-center gap-1 font-semibold text-orange-600">
                                            <i class="fas fa-location-dot text-[11px]"></i>
                                            <span><?= htmlspecialchars($jh['ruangan_tampil']) ?></span>
                                        </span>
                                        <?php if (!empty($jh['nama_dosen'])): ?>
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="flex items-center gap-1 truncate text-slate-700 font-medium">
                                                <i class="fas fa-user-tie text-[11px] text-slate-400"></i>
                                                <?= htmlspecialchars($jh['nama_dosen']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: SKS & Status -->
                            <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center shrink-0 border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-100">
                                <span class="text-xs font-bold font-mono text-slate-700 bg-slate-100 border border-slate-200/80 px-2.5 py-1 rounded-md">
                                    <?= (int)$jh['sks'] ?> SKS
                                </span>
                                <?php if ($isLive): ?>
                                    <span class="text-[10px] font-bold text-emerald-600 mt-1 flex items-center gap-1">
                                        <i class="fas fa-chalkboard-user"></i> Kuliah Aktif
                                    </span>
                                <?php endif; ?>
                            </div>

                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

    </div>

    <!-- ===== KOLOM KANAN (4 KOLOM / 34%): COMMAND STREAM & ANTREAN AKSI PRIORITAS ===== -->
    <div class="lg:col-span-4 space-y-5">

        <!-- 1. Antrean Tanda Tangan Dokumen -->
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-3.5 bg-slate-50/70">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Antrean Tanda Tangan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Surat penunjukkan pembimbing & penguji</p>
                </div>
                <span class="inline-flex items-center gap-1 rounded-md bg-orange-50 border border-orange-200/60 px-2.5 py-0.5 text-xs font-semibold text-orange-700">
                    <?= f($ttdPendingTotal) ?> Menunggu
                </span>
            </div>
            <?php if (count($ttdItems) === 0): ?>
                <div class="flex flex-col items-center py-7 text-center">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-400 text-xs">
                        <i class="fas fa-check"></i>
                    </span>
                    <p class="mt-2 text-xs text-slate-500">Tidak ada dokumen menunggu tanda tangan saat ini.</p>
                </div>
            <?php else: ?>
            <ul class="divide-y divide-slate-100">
                <?php foreach ($ttdItems as $t):
                    $namaTampil = $t['nama'] ?: ($t['nim'] ?? '?');
                    $inisial = strtoupper(mb_substr($namaTampil, 0, 2));
                ?>
                <li class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50/80 transition">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-orange-700 text-xs font-bold">
                        <?= htmlspecialchars($inisial) ?>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-bold text-slate-800"><?= htmlspecialchars($namaTampil) ?></p>
                        <p class="font-mono text-[11px] text-slate-400"><?= htmlspecialchars($t['nim']) ?></p>
                    </div>
                    <a href="/redesain-siatek/administrator/jurusan-surat-penunjukkan" 
                       class="inline-flex items-center gap-1 rounded-lg bg-[#f97316] hover:bg-[#ea580c] px-2.5 py-1 text-xs font-semibold text-white shadow-2xs transition shrink-0">
                        Proses
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <a href="/redesain-siatek/administrator/jurusan-surat-penunjukkan" class="block border-t border-slate-100 bg-slate-50/50 px-4 py-2.5 text-center text-xs font-semibold text-slate-600 hover:text-orange-600 transition">
                Buka Semua Surat Penunjukkan &rarr;
            </a>
            <?php endif; ?>
        </section>

        <!-- 2. Antrean Verifikasi Berkas Lintas Prodi -->
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-3.5 bg-slate-50/70">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Verifikasi Berkas Masuk</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Persyaratan Skripsi &amp; KP terbaru</p>
                </div>
                <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 border border-slate-200 px-2.5 py-0.5 text-xs font-semibold text-slate-700">
                    <?= f($totalBerkasPending) ?> Berkas
                </span>
            </div>
            <?php if (count($berkasPendingList) === 0): ?>
                <div class="flex flex-col items-center py-7 text-center">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-400 text-xs">
                        <i class="fas fa-check"></i>
                    </span>
                    <p class="mt-2 text-xs text-slate-500">Semua berkas persyaratan mahasiswa telah diverifikasi.</p>
                </div>
            <?php else: ?>
            <ul class="divide-y divide-slate-100">
                <?php foreach ($berkasPendingList as $bp):
                    $isSkripsi = ($bp['tipe'] === 'Skripsi');
                    $namaMhs = $bp['nama_mhs'] ?: $bp['nim'];
                    $namaBerkas = $bp['nama_berkas'] ?: 'Persyaratan ' . $bp['tipe'];
                ?>
                <li class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50/80 transition">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold <?= $isSkripsi ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-700' ?>">
                        <?= $isSkripsi ? 'SK' : 'KP' ?>
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <p class="truncate text-xs font-bold text-slate-800"><?= htmlspecialchars($namaMhs) ?></p>
                        </div>
                        <p class="text-[11px] text-slate-500 truncate"><?= htmlspecialchars($namaBerkas) ?></p>
                    </div>
                    <a href="/redesain-siatek/administrator/mhs-verifikasi" 
                       class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700 transition shrink-0">
                        Periksa
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <a href="/redesain-siatek/administrator/mhs-verifikasi" class="block border-t border-slate-100 bg-slate-50/50 px-4 py-2.5 text-center text-xs font-semibold text-slate-600 hover:text-orange-600 transition">
                Buka Modul Verifikasi Berkas &rarr;
            </a>
            <?php endif; ?>
        </section>

        <!-- 3. TIMELINE VERTIKAL JADWAL UJIAN & BATCH -->
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-3.5 bg-slate-50/70">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-slate-800">Timeline Ujian &amp; Batch</h3>
                        <?php if ($totalBatchBuka > 0): ?>
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <?= $totalBatchBuka ?> Batch Terbuka
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                Tidak ada batch terbuka
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Sidang terjadwal &amp; status periode pendaftaran</p>
                </div>
                <a href="/redesain-siatek/administrator/mhs-ujian" class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition">
                    Kelola &rarr;
                </a>
            </div>
            <div class="p-4">
                <?php if (count($agendaTimeline) === 0): ?>
                    <p class="py-6 text-center text-xs text-slate-400">Tidak ada jadwal ujian atau batch saat ini.</p>
                <?php else: ?>
                <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    <?php foreach ($agendaTimeline as $item): 
                        $isBatch = ($item['tipe'] === 'batch');
                        $ringClass = $item['ring_color'] ?? ($isBatch ? 'ring-[#f97316]' : 'ring-[#1a365d]');
                        $dotClass = $item['dot_color'] ?? ($isBatch ? 'bg-[#f97316]' : 'bg-[#1a365d]');
                        $badgeStyle = $item['status_color'] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    ?>
                    <div class="relative">
                        <!-- Node Titik Stepper -->
                        <span class="absolute -left-6 top-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-white ring-2 <?= $ringClass ?>">
                            <span class="h-1.5 w-1.5 rounded-full <?= $dotClass ?>"></span>
                        </span>
                        <div class="p-3 rounded-lg border border-slate-100 bg-slate-50/60 hover:bg-white hover:border-slate-200 transition">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border <?= $badgeStyle ?>">
                                    <?= htmlspecialchars($item['status']) ?>
                                </span>
                                <span class="text-[11px] text-slate-500 font-medium">
                                    <i class="far fa-calendar mr-1 text-slate-400"></i><?= htmlspecialchars($item['tanggal_fmt']) ?>
                                </span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-800 mt-1.5">
                                <a href="<?= htmlspecialchars($item['link']) ?>" class="hover:text-orange-600 transition">
                                    <?= htmlspecialchars($item['judul']) ?>
                                </a>
                            </h4>
                            <p class="text-[11px] text-slate-500 mt-0.5"><?= $item['deskripsi'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

    </div>
</div>

<!-- ============ BERITA & INFORMASI JURUSAN ============ -->
<section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm mt-5">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-base font-semibold text-slate-800">Berita &amp; Informasi Jurusan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Publikasi dan edaran resmi terkini</p>
        </div>
        <a href="/redesain-siatek/administrator/jurusan-berita" class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition">
            Semua Berita &rarr;
        </a>
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

<!-- Data untuk Chart.js & Script Interaktivitas Jadwal Dinamis -->
<script>
window.__berandaData = {
    enrollment: {
        labels: <?= json_encode($years) ?>,
        values: <?= json_encode($mhsPerYear) ?>
    },
    concentration: {
        labels: <?= json_encode($concLabels) ?>,
        values: <?= json_encode($concValues) ?>,
        colors: <?= json_encode($concColors) ?>
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // 1. Live Digital Clock (WITA)
    const clockEl = document.getElementById('jadwalLiveClock');
    if (clockEl) {
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${h}:${m}:${s} WITA`;
        }
        updateClock();
        setInterval(updateClock, 1000);
    }

    // 2. Tab Hari Switcher (Senin s.d. Sabtu)
    const tabBtns = document.querySelectorAll('.tab-jadwal-btn');
    const panels = document.querySelectorAll('.panel-jadwal-hari');
    const subFilter = document.getElementById('subFilterHariIni');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const isToday = this.getAttribute('data-is-today') === '1';

            // Reset tab styles
            tabBtns.forEach(b => {
                b.classList.remove('bg-[#1a365d]', 'text-white', 'shadow-2xs', 'active-tab');
                b.classList.add('bg-white/60', 'text-slate-600', 'border-transparent');
            });
            this.classList.remove('bg-white/60', 'text-slate-600', 'border-transparent');
            this.classList.add('bg-[#1a365d]', 'text-white', 'shadow-2xs', 'active-tab');

            // Ganti panel hari
            panels.forEach(p => p.classList.add('hidden'));
            const targetPanel = document.getElementById(targetId);
            if (targetPanel) {
                targetPanel.classList.remove('hidden');
                targetPanel.querySelectorAll('.baris-jadwal').forEach(row => row.classList.remove('hidden'));
            }

            // Tampilkan sub-filter hanya untuk hari ini
            if (subFilter) {
                if (isToday) {
                    subFilter.classList.remove('hidden');
                } else {
                    subFilter.classList.add('hidden');
                }
            }
        });
    });

    // 3. Sub-filter Status Real-Time (Semua, Sedang Berlangsung, Akan Datang, Selesai)
    const filterBtns = document.querySelectorAll('.btn-filter-status');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const status = this.getAttribute('data-status');

            // Reset styling tombol filter
            filterBtns.forEach(b => {
                b.classList.remove('bg-[#1a365d]', 'text-white', 'shadow-2xs');
                b.classList.add('bg-white', 'text-slate-700');
            });
            this.classList.remove('bg-white', 'text-slate-700', 'text-slate-500', 'text-slate-600');
            this.classList.add('bg-[#1a365d]', 'text-white', 'shadow-2xs');

            // Filter baris kelas di panel aktif
            const activePanel = document.querySelector('.panel-jadwal-hari:not(.hidden)');
            if (activePanel) {
                activePanel.querySelectorAll('.baris-jadwal').forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    if (status === 'semua' || rowStatus === status) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            }
        });
    });
});
</script>
