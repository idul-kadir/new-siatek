<?php
/**
 * Halaman Beranda — Dashboard utama dengan KPI cards, charts, feeds, dll.
 *
 * Semua data diambil langsung dari database (tabel prodi, mahasiswa, dosen,
 * skripsi, kp, pengumuman, agenda, surat_penunjukkan, arsip_*, broadcast).
 */

require_once __DIR__ . '/../../koneksi.php';

/* ================================================================
   QUERY SEMUA DATA YANG DIPERLUKAN
   ================================================================ */

// --- KPI: Prodi & Mahasiswa (hanya yang aktif, maksimal 7 tahun / angkatan >= 2019) ---
$qProdi = mysqli_query($koneksi, "SELECT * FROM prodi ORDER BY nama");
$prodiList = [];
$totalMhs = 0;
$aktifSince = date('Y') - 7; // 7 tahun terakhir
while ($p = mysqli_fetch_assoc($qProdi)) {
    $q = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM mahasiswa WHERE prodi='" . mysqli_real_escape_string($koneksi, $p['kode']) . "' AND angkatan >= $aktifSince AND status='Aktif'");
    $r = mysqli_fetch_assoc($q);
    $p['mahasiswa'] = (int)$r['c'];
    $prodiList[] = $p;
    $totalMhs += (int)$r['c'];
}
$qDosenAktif = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM dosen WHERE status='aktif'");
$dosenAktif = (int)mysqli_fetch_assoc($qDosenAktif)['c'];

// --- Skripsi/KP Status ---
$qSkripsiAktif = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM skripsi WHERE status='aktif'");
$skripsiAktif = (int)mysqli_fetch_assoc($qSkripsiAktif)['c'];
$qKpAktif = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM kp WHERE status='aktif'");
$kpAktif = (int)mysqli_fetch_assoc($qKpAktif)['c'];

// --- Surat Penunjukkan (TTD) ---
// KPI "Menunggu TTD" = total surat penunjukkan yang kolom ttd-nya masih kosong
$qTTDPending = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM surat_penunjukkan WHERE ttd='' OR ttd IS NULL");
$ttdPendingTotal = (int)mysqli_fetch_assoc($qTTDPending)['c'];

// Feed: tampilkan 5 surat terbaru yang masih menunggu TTD
$qTTD = mysqli_query($koneksi, "SELECT sp.*, m.nama FROM surat_penunjukkan sp LEFT JOIN mahasiswa m ON sp.nim=m.nim WHERE sp.ttd='' OR sp.ttd IS NULL ORDER BY sp.id DESC LIMIT 5");
$ttdItems = [];
while ($t = mysqli_fetch_assoc($qTTD)) {
    $ttdItems[] = $t;
}

// --- Pengumuman (latest) ---
$qAnn = mysqli_query($koneksi, "SELECT p.*, d.nama AS penulis FROM pengumuman p LEFT JOIN dosen d ON p.nip=d.nip ORDER BY p.id DESC LIMIT 5");
$annItems = [];
while ($a = mysqli_fetch_assoc($qAnn)) {
    $a['tanggal_fmt'] = @date('d M Y', $a['tanggal']);
    $a['status_short'] = strlen($a['status']) > 120 ? substr($a['status'], 0, 120) . '...' : $a['status'];
    $annItems[] = $a;
}

// --- Agenda (upcoming) ---
$nowTs = time();
$qAgenda = mysqli_query($koneksi, "SELECT a.*, d.nama AS penulis FROM agenda a LEFT JOIN dosen d ON a.penulis=d.nip WHERE a.tanggal > $nowTs ORDER BY a.tanggal ASC LIMIT 10");
$agendaItems = [];
while ($g = mysqli_fetch_assoc($qAgenda)) {
    $g['tanggal_fmt'] = @date('d M Y', $g['tanggal']);
    $g['bulan_indo'] = date('F', $g['tanggal']);
    $g['hari_indo'] = date('l', $g['tanggal']);
    $agendaItems[] = $g;
}

// --- Chart 1: Angkatan Mahasiswa (5 tahun terakhir, per prodi) ---
// hanya yang aktif dan angkatan <= 7 tahun terakhir
$aktifSince = date('Y') - 7;
$angkatanTotal = [];
$qAngkatan = mysqli_query($koneksi, "SELECT angkatan, COUNT(*) AS c FROM mahasiswa WHERE status='Aktif' AND angkatan >= $aktifSince GROUP BY angkatan ORDER BY angkatan DESC");
while ($a = mysqli_fetch_assoc($qAngkatan)) {
    $angkatanTotal[(int)$a['angkatan']] = (int)$a['c'];
}
$angkatanPerProdi = [];
foreach ($prodiList as $pr) {
    $q = mysqli_query($koneksi, "SELECT angkatan, COUNT(*) AS c FROM mahasiswa WHERE prodi='" . mysqli_real_escape_string($koneksi, $pr['kode']) . "' AND status='Aktif' AND angkatan >= $aktifSince GROUP BY angkatan ORDER BY angkatan DESC");
    while ($a = mysqli_fetch_assoc($q)) {
        $angkatanPerProdi[$pr['kode']][(int)$a['angkatan']] = (int)$a['c'];
    }
}
// Ambil 5 tahun terakhir
$recentYears = [];
$keys = array_keys($angkatanTotal);
for ($i = 0; $i < 5 && $i < count($keys); $i++) {
    $recentYears[] = (string)$keys[$i];
}
// Siapkan data per prodi untuk chart
$chartDatasetLabels = [];
$chartDatasetValues = [];
foreach ($prodiList as $pr) {
    $chartDatasetLabels[] = $pr['nama'];
    $vals = [];
    foreach ($recentYears as $yr) {
        $vals[] = isset($angkatanPerProdi[$pr['kode']][(int)$yr]) ? $angkatanPerProdi[$pr['kode']][(int)$yr] : 0;
    }
    $chartDatasetValues[] = $vals;
}

// --- Chart 2: Ringkasan Aktif (Skripsi/KP per fase) ---
// Mahasiswa skripsi bergerak: proposal → hasil → tutup.
// Jika sudah di tutup, artinya sudah lewat hasil dan proposal.
// Jika sudah di hasil (tapi belum tutup), artinya sedang di tahap hasil.
// Disinkronkan dengan tabel skripsi: hanya mahasiswa dengan skripsi.status='aktif'
// dan angkatan <= 7 tahun terakhir yang dihitung (sesuai definisi "aktif").
$aktifSince = date('Y') - 7;

$qTutup = mysqli_query($koneksi, "SELECT COUNT(DISTINCT t.nim) AS c
                                   FROM tutup t
                                   INNER JOIN skripsi s ON t.nim=s.nim
                                   INNER JOIN mahasiswa m ON t.nim=m.nim
                                   WHERE s.status='aktif' AND m.angkatan >= $aktifSince");
$tutupCount = (int)mysqli_fetch_assoc($qTutup)['c'];

$qHasil = mysqli_query($koneksi, "SELECT COUNT(DISTINCT h.nim) AS c
                                   FROM hasil h
                                   INNER JOIN skripsi s ON h.nim=s.nim
                                   INNER JOIN mahasiswa m ON h.nim=m.nim
                                   LEFT JOIN tutup t ON h.nim=t.nim
                                   WHERE s.status='aktif' AND m.angkatan >= $aktifSince
                                   AND t.nim IS NULL");
$hasilCount = (int)mysqli_fetch_assoc($qHasil)['c'];

$qProp = mysqli_query($koneksi, "SELECT COUNT(DISTINCT p.nim) AS c
                                   FROM proposal p
                                   INNER JOIN skripsi s ON p.nim=s.nim
                                   INNER JOIN mahasiswa m ON p.nim=m.nim
                                   LEFT JOIN hasil h ON p.nim=h.nim
                                   LEFT JOIN tutup t ON p.nim=t.nim
                                   WHERE s.status='aktif' AND m.angkatan >= $aktifSince
                                   AND h.nim IS NULL AND t.nim IS NULL");
$propCount = (int)mysqli_fetch_assoc($qProp)['c'];
$ringkesData = [
    'Kerja Praktek' => $kpAktif,
    'Proposal Skripsi' => $propCount,
    'Hasil Penelitian' => $hasilCount,
    'Tutup' => $tutupCount,
];
$ringkesTotal = array_sum($ringkesData);

// --- Chart 3: Sinkronisasi Sister DIHAPUS ---
// --- Chart 4: Beban Kerja Operasional DIHAPUS ---

/* ================================================================
   HELPER: Format bulan Indonesia
   ================================================================ */
function bulanIndo($ts) {
    $bulans = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    return $bulans[date('n', $ts) - 1] . ' ' . date('Y', $ts);
}

?>

<main class="content-area">

    <!-- ===== KPI Summary Cards ===== -->
    <section class="mb-4">
        <div class="kpi-row">
            <?php foreach ($prodiList as $idx => $pr): ?>
            <a href="#" class="kpi-card card-kpi <?= ['card-kpi--info','card-kpi--primary','card-kpi--success'][$idx % 3] ?>">
                <div class="card-kpi-icon"><i class="bi bi-diagram-3-fill"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value"><?= number_format($pr['mahasiswa']) ?></span>
                    <span class="card-kpi-label"><?= htmlspecialchars($pr['nama']) ?></span>
                </div>
            </a>
            <?php endforeach; ?>

            <a href="#" class="kpi-card card-kpi card-kpi--warning">
                <div class="card-kpi-icon"><i class="bi bi-person-workspace"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value"><?= number_format($dosenAktif) ?></span>
                    <span class="card-kpi-label">Dosen Terdaftar</span>
                </div>
            </a>

            <a href="#" class="kpi-card card-kpi card-kpi--info">
                <div class="card-kpi-icon"><i class="bi bi-mortarboard-fill"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value"><?= number_format($skripsiAktif) ?></span>
                    <span class="card-kpi-label">Skripsi Aktif</span>
                </div>
            </a>

            <a href="#" class="kpi-card card-kpi card-kpi--primary">
                <div class="card-kpi-icon"><i class="bi bi-briefcase-fill"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value"><?= number_format($kpAktif) ?></span>
                    <span class="card-kpi-label">KP Aktif</span>
                </div>
            </a>

            <a href="#" class="kpi-card card-kpi card-kpi--danger">
                <div class="card-kpi-icon"><i class="bi bi-pen-fill"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value"><?= number_format($ttdPendingTotal) ?></span>
                    <span class="card-kpi-label">Menunggu TTD</span>
                </div>
                <span class="card-kpi-badge badge-flash">Penting</span>
            </a>
        </div>
    </section>

    <!-- ===== Charts Row 1x2 ===== -->
    <section class="mb-4">
        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h6 class="chart-card-title"><i class="bi bi-bar-chart-fill me-1"></i>Angkatan Mahasiswa</h6>
                        <small class="text-muted">Angkatan mahasiswa 5 tahun terakhir</small>
                    </div>
                    <div class="chart-card-body">
                        <canvas id="chartAngkatan"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h6 class="chart-card-title"><i class="bi bi-pie-chart-fill me-1"></i>Ringkasan Aktif</h6>
                        <small class="text-muted">Distribusi KP &amp; Skripsi per fase saat ini</small>
                    </div>
                    <div class="chart-card-body chart-card-body--center">
                        <canvas id="chartSkripsi"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Tanda Tangan Digital (empty log section replaced) ===== -->
    <section class="mb-4">
        <div class="row g-3">
            <!-- Tanda Tangan Digital -->
            <div class="col-12 col-xl-4">
                <div class="feed-card">
                    <div class="feed-card-header">
                        <h6 class="feed-card-title"><i class="bi bi-pen-fill me-1"></i>Tanda Tangan Digital</h6>
                        <a href="#" class="feed-card-action">Lihat Semua <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="feed-card-body">
                        <?php if (count($ttdItems) > 0): ?>
                            <?php foreach ($ttdItems as $t): ?>
                            <div class="broadcast-item mb-3">
                                <div class="d-flex justify-content-between mb-1 gap-2">
                                    <small class="fw-semibold"><?= htmlspecialchars($t['nama'] ?: $t['nim']) ?> <span class="text-muted ms-1" style="font-size:.7rem; font-weight:400;"><?= htmlspecialchars($t['no_surat']) ?></span></small>
                                    <button class="btn btn-sm btn-signature" data-bs-toggle="modal" data-bs-target="#signModal">TTD</button>
                                </div>
                                <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;"><?= htmlspecialchars($t['keterangan'] ?: 'Surat Penunjukkan') ?> — NIM <?= htmlspecialchars($t['nim']) ?></p>
                            </div>
                            <?php if ($t != end($ttdItems)): ?><hr><?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-3" style="font-size:.85rem;">Belum ada surat menunggu tanda tangan.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Log Aktivitas Skripsi -->
            <div class="col-12 col-xl-4">
                <div class="feed-card">
                    <div class="feed-card-header">
                        <h6 class="feed-card-title"><i class="bi bi-journal-text me-1"></i>Log Aktivitas Skripsi</h6>
                        <a href="#" class="feed-card-action">Semua <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="feed-card-body">
                        <p class="text-muted text-center py-3" style="font-size:.85rem;">Belum ada aktivitas terbaru.</p>
                    </div>
                </div>
            </div>

            <!-- Log Kerja Praktek -->
            <div class="col-12 col-xl-4">
                <div class="feed-card">
                    <div class="feed-card-header">
                        <h6 class="feed-card-title"><i class="bi bi-briefcase-fill me-1"></i>Log Kerja Praktek</h6>
                        <a href="#" class="feed-card-action">Semua <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="feed-card-body">
                        <p class="text-muted text-center py-3" style="font-size:.85rem;">Belum ada aktivitas terbaru.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Pengumuman & Agenda ===== -->
    <section class="mb-4">
        <div class="row g-3">
            <!-- Pengumuman -->
            <div class="col-12 col-lg-5">
                <div class="feed-card feed-card--collapsible" id="announcementWidget">
                    <div class="feed-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="feed-card-title mb-0"><i class="bi bi-megaphone-fill me-1"></i>Pengumuman</h6>
                            <button class="btn btn-sm btn-link text-muted p-0" title="Edit Pengumuman">
                                <i class="bi bi-gear"></i>
                            </button>
                        </div>
                        <button class="btn btn-sm btn-link text-muted p-0 ms-auto" id="announcementToggle" title="Sembunyikan/Tampilkan">
                            <i class="bi bi-chevron-up"></i>
                        </button>
                    </div>
                    <div class="feed-card-body" id="announcementBody">
                        <?php if (count($annItems) > 0): ?>
                            <?php foreach ($annItems as $i => $a): ?>
                            <div class="announcement-item mb-3">
                                <?php if ($i === 0): ?><span class="badge bg-primary mb-1" style="font-size:.65rem;">Baru</span><?php endif; ?>
                                <p class="mb-0 mt-1" style="font-size:.82rem;"><strong><?= nl2br(htmlspecialchars($a['status_short'])) ?></strong></p>
                                <small class="text-muted" style="font-size:.72rem;">Diumumkan, <?= $a['tanggal_fmt'] ?></small>
                            </div>
                            <?php if ($i < count($annItems) - 1): ?><hr><?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-3" style="font-size:.85rem;">Tidak ada pengumuman.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Agenda / Kalender Mini -->
            <div class="col-12 col-lg-7">
                <div class="feed-card">
                    <div class="feed-card-header">
                        <h6 class="feed-card-title"><i class="bi bi-calendar-week me-1"></i>Agenda</h6>
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            <button class="btn btn-sm btn-link text-dark p-0" id="calPrev"><i class="bi bi-chevron-left"></i></button>
                            <span class="fw-semibold" style="font-size:.85rem;" id="calMonthYear"><?= bulanIndo(time()) ?></span>
                            <button class="btn btn-sm btn-link text-dark p-0" id="calNext"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="feed-card-body">
                        <div class="calendar-grid" id="calendarGrid">
                            <!-- Diisi oleh JS -->
                        </div>
                        <div class="mt-3">
                            <small class="text-muted d-block mb-2"><strong>Agenda Mendatang:</strong></small>
                            <?php if (count($agendaItems) > 0): ?>
                                <?php foreach ($agendaItems as $i => $g): ?>
                                <div class="agenda-item d-flex align-items-center gap-2 mb-2">
                                    <span class="agenda-dot agenda-dot--exam"></span>
                                    <small style="font-size:.78rem;"><strong><?= date('d M', $g['tanggal']) ?></strong> — <?= htmlspecialchars($g['agenda']) ?></small>
                                </div>
                                <?php if ($i >= 4): break; endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <small class="text-muted d-block" style="font-size:.78rem;">Tidak ada agenda mendatang.</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- ===== Chart.js Inline Data (dari PHP) ===== -->
<script>
// Data chart dari database — dashboard.js akan membaca ini via window.__berandaData
window.__berandaData = {
    /* Chart 1: Angkatan */
    angkatan: {
        labels: <?= json_encode($recentYears) ?>,
        datasetLabels: <?= json_encode($chartDatasetLabels) ?>,
        values: <?= json_encode($chartDatasetValues) ?>
    },
    /* Chart 2: Ringkasan Aktif */
    ringkasan: {
        labels: <?= json_encode(array_keys($ringkesData)) ?>,
        values: <?= json_encode(array_values($ringkesData)) ?>,
        total: <?= $ringkesTotal ?>
    }
};
</script>
