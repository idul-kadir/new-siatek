<?php
/**
 * Halaman Beranda — Dashboard utama (Tailwind, mengikuti template.html).
 *
 * Query DB tetap dari versi Bootstrap (data mahasiswa, dosen, skripsi, kp,
 * surat_penunjukkan, pengumuman, agenda, dsb). Konten visual dirender
 * dengan class Tailwind agar match dengan template.html.
 */

require_once __DIR__ . '/../../koneksi.php';

/* ================================================================
   QUERY SEMUA DATA YANG DIPERLUKAN
   ================================================================ */

// --- KPI: Prodi & Mahasiswa (aktif, 7 tahun terakhir) ---
$qProdi = mysqli_query($koneksi, "SELECT * FROM prodi ORDER BY nama");
$prodiList = [];
$totalMhs = 0;
$aktifSince = date('Y') - 7;
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
$qTTDPending = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM surat_penunjukkan WHERE ttd='' OR ttd IS NULL");
$ttdPendingTotal = (int)mysqli_fetch_assoc($qTTDPending)['c'];

// Feed: 5 surat terbaru yang masih menunggu TTD
$qTTD = mysqli_query($koneksi, "SELECT sp.*, m.nama FROM surat_penunjukkan sp LEFT JOIN mahasiswa m ON sp.nim=m.nim WHERE sp.ttd='' OR sp.ttd IS NULL ORDER BY sp.id DESC LIMIT 5");
$ttdItems = [];
while ($t = mysqli_fetch_assoc($qTTD)) {
    $ttdItems[] = $t;
}

// --- Chart 1: Angkatan Mahasiswa (7 tahun terakhir, per prodi) ---
// Range tahun penuh 7 tahun terakhir (ascending) — walau belum ada data di DB
$thnSekarang = (int)date('Y');
$recentYears = [];
for ($y = $thnSekarang - 6; $y <= $thnSekarang; $y++) {
    $recentYears[] = (string)$y;
}

$qAngkatan = mysqli_query($koneksi, "SELECT angkatan, COUNT(*) AS c FROM mahasiswa WHERE status='Aktif' AND angkatan >= " . ($thnSekarang - 6) . " GROUP BY angkatan");
$angkatanTotal = [];
while ($a = mysqli_fetch_assoc($qAngkatan)) {
    $angkatanTotal[(int)$a['angkatan']] = (int)$a['c'];
}

$angkatanPerProdi = [];
foreach ($prodiList as $pr) {
    $q = mysqli_query($koneksi, "SELECT angkatan, COUNT(*) AS c FROM mahasiswa WHERE prodi='" . mysqli_real_escape_string($koneksi, $pr['kode']) . "' AND status='Aktif' AND angkatan >= " . ($thnSekarang - 6) . " GROUP BY angkatan");
    while ($a = mysqli_fetch_assoc($q)) {
        $angkatanPerProdi[$pr['kode']][(int)$a['angkatan']] = (int)$a['c'];
    }
}
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

// --- Chart 2 doughnut: Distribusi konsentrasi (mahasiswa per prodi) ---
$concentrationLabels = [];
$concentrationValues = [];
foreach ($prodiList as $pr) {
    $concentrationLabels[] = $pr['nama'];
    $concentrationValues[] = (int)$pr['mahasiswa'];
}
$concentrationTotal = array_sum($concentrationValues);

// --- Agenda (upcoming) — dipakai untuk jadwal lab ---
$nowTs = time();
$qAgenda = mysqli_query($koneksi, "SELECT a.*, d.nama AS penulis FROM agenda a LEFT JOIN dosen d ON a.penulis=d.nip WHERE a.tanggal > $nowTs ORDER BY a.tanggal ASC LIMIT 4");
$agendaItems = [];
while ($g = mysqli_fetch_assoc($qAgenda)) {
    $g['tanggal_fmt'] = date('d M Y', $g['tanggal']);
    $agendaItems[] = $g;
}

/* ================================================================
   HELPER
   ================================================================ */
function timeAgo($ts) {
    $diff = time() - (int)$ts;
    if ($diff < 60) return 'baru saja';
    if ($diff < 3600) return floor($diff / 60) . ' menit lalu';
    if ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
    return floor($diff / 86400) . ' hari lalu';
}

$hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$today_label = $hari[(int)date('w')] . ', ' . (int)date('d') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');

/* Palet warna kartu KPI (colorful) */
$kpiPalettes = [
    [
        'icon' => 'fa-users',       'icon_bg' => 'bg-white/20', 'icon_text' => 'text-white',
        'card_bg' => 'bg-gradient-to-br from-blue-500 to-blue-700',
        'value' => 'text-white', 'label' => 'text-white/80', 'badge_bg' => 'bg-white/20', 'badge_text' => 'text-white',
    ],
    [
        'icon' => 'fa-user-tie',    'icon_bg' => 'bg-white/20', 'icon_text' => 'text-white',
        'card_bg' => 'bg-gradient-to-br from-emerald-500 to-emerald-700',
        'value' => 'text-white', 'label' => 'text-white/80', 'badge_bg' => 'bg-white/20', 'badge_text' => 'text-white',
    ],
    [
        'icon' => 'fa-book',        'icon_bg' => 'bg-white/20', 'icon_text' => 'text-white',
        'card_bg' => 'bg-gradient-to-br from-orange-500 to-orange-600',
        'value' => 'text-white', 'label' => 'text-white/80', 'badge_bg' => 'bg-white/20', 'badge_text' => 'text-white',
    ],
    [
        'icon' => 'fa-briefcase',   'icon_bg' => 'bg-white/20', 'icon_text' => 'text-white',
        'card_bg' => 'bg-gradient-to-br from-purple-500 to-purple-700',
        'value' => 'text-white', 'label' => 'text-white/80', 'badge_bg' => 'bg-white/20', 'badge_text' => 'text-white',
    ],
];
?>

<main class="flex-1 overflow-y-auto bg-slate-50">

    <div class="p-8 space-y-6">

        <!-- ============ Banner Welcome ============ -->
        <div class="bg-[#1a365d] p-6 rounded-xl shadow-sm flex flex-col justify-center text-white relative overflow-hidden min-h-[120px]">
            <div class="relative z-10">
                <h3 class="text-xl font-semibold mb-1">Selamat Datang di SIATEK, Admin</h3>
                <p class="text-slate-300 text-sm font-normal">
                    Pantau aktivitas administrasi, akademik, dan laboratorium departemen hari ini.
                </p>
            </div>
            <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-white/5 rounded-full"></div>
            <div class="absolute -right-24 -top-24 w-64 h-64 bg-white/5 rounded-full"></div>
        </div>

        <!-- ============ Quick Stats (4 KPI) ============ -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <?php
            $kpis = [
                ['Total Mahasiswa',   $totalMhs,      'Aktif',    0],
                ['Dosen &amp; Staff', $dosenAktif,    'Terdaftar', 1],
                ['Skripsi Aktif',     $skripsiAktif,  'Berjalan',  2],
                ['KP Aktif',          $kpAktif,       'Berjalan',  3],
            ];
            foreach ($kpis as $i => [$label, $val, $badge, $pi]):
                $pal = $kpiPalettes[$pi];
            ?>
            <div class="<?= $pal['card_bg'] ?> p-5 rounded-xl card-shadow card-hover flex flex-col justify-between h-32 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full"></div>
                <div class="flex justify-between items-start relative z-10">
                    <div class="w-10 h-10 <?= $pal['icon_bg'] ?> rounded-lg flex items-center justify-center <?= $pal['icon_text'] ?>">
                        <i class="fas <?= $pal['icon'] ?> text-sm"></i>
                    </div>
                    <span class="<?= $pal['badge_bg'] ?> <?= $pal['badge_text'] ?> px-2 py-0.5 rounded-full text-xs font-medium"><?= $badge ?></span>
                </div>
                <div class="relative z-10">
                    <p class="<?= $pal['label'] ?> text-xs font-normal"><?= $label ?></p>
                    <h3 class="text-2xl font-bold <?= $pal['value'] ?> mt-0.5"><?= number_format($val) ?></h3>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- ============ Charts ============ -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Main Chart -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl card-shadow">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-semibold text-slate-900 text-base">Tren Pendaftaran Mahasiswa Baru</h3>
                        <p class="text-xs text-slate-500 mt-1">Data 7 tahun terakhir per prodi</p>
                    </div>
                </div>
                <div style="height: 260px">
                    <canvas id="enrollmentChart"></canvas>
                </div>
            </div>

            <!-- Side Chart -->
            <div class="bg-white p-6 rounded-xl card-shadow">
                <div class="mb-6">
                    <h3 class="font-semibold text-slate-900 text-base">Distribusi Konsentrasi</h3>
                    <p class="text-xs text-slate-500 mt-1">Berdasarkan program studi</p>
                </div>
                <div style="height: 260px">
                    <canvas id="concentrationChart"></canvas>
                </div>
            </div>
        </div>

        <!-- ============ Table & Schedule ============ -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <!-- Tabel Pengajuan Judul Skripsi / TTD -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl card-shadow overflow-hidden flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="font-semibold text-slate-900 text-base">Surat Menunggu Tanda Tangan</h3>
                        <p class="text-xs text-slate-500 mt-1">Mahasiswa yang mengajukan dokumen resmi</p>
                    </div>
                    <a href="index.php?page=jurusan-surat-penunjukkan" class="text-xs text-[#1a365d] font-medium hover:text-[#f97316] transition">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-slate-500 text-xs border-b border-slate-200">
                                <th class="py-3 px-2 font-medium uppercase tracking-wider">NIM</th>
                                <th class="py-3 px-2 font-medium uppercase tracking-wider">Mahasiswa</th>
                                <th class="py-3 px-2 font-medium uppercase tracking-wider">Keterangan</th>
                                <th class="py-3 px-2 font-medium uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-slate-700 font-normal">
                            <?php if (count($ttdItems) === 0): ?>
                                <tr>
                                    <td colspan="4" class="py-6 px-2 text-center text-slate-400 text-xs">Tidak ada surat yang menunggu tanda tangan.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ttdItems as $t):
                                    $inisial = strtoupper(substr($t['nama'] ?: ($t['nim'] ?? '?'), 0, 2));
                                    $warnaBg = ['1a365d','10b981','f97316','dc2626','6366f1','7c3aed'];
                                    $bg = $warnaBg[crc32($t['nim'] ?? $inisial) % count($warnaBg)];
                                ?>
                                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                    <td class="py-3 px-2 text-slate-500"><?= htmlspecialchars($t['nim']) ?></td>
                                    <td class="py-3 px-2">
                                        <div class="flex items-center space-x-3">
                                            <img src="https://ui-avatars.com/api/?background=<?= $bg ?>&color=fff&name=<?= urlencode($inisial) ?>&size=32"
                                                 class="w-7 h-7 rounded-full" alt="Avatar">
                                            <span class="text-slate-900"><?= htmlspecialchars($t['nama'] ?: $t['nim']) ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-2 text-slate-600"><?= htmlspecialchars($t['keterangan'] ?: 'Surat Penunjukkan') ?></td>
                                    <td class="py-3 px-2">
                                        <span class="bg-orange-50 text-[#f97316] px-2.5 py-1 rounded-full text-xs font-medium">Pending</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Jadwal Lab Hari Ini -->
            <div class="bg-white p-6 rounded-xl card-shadow">
                <div class="mb-5">
                    <h3 class="font-semibold text-slate-900 text-base">Agenda Mendatang</h3>
                    <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($today_label) ?></p>
                </div>
                <div class="space-y-5">
                    <?php if (count($agendaItems) === 0): ?>
                        <p class="text-xs text-slate-400 text-center py-4">Tidak ada agenda mendatang.</p>
                    <?php else: ?>
                        <?php
                        $totalA = count($agendaItems);
                        foreach ($agendaItems as $i => $g):
                            $pct = max(25, 100 - ($i * (100 / max(1, $totalA))));
                            $colorClass = $i === 0 ? '[#f97316]' : ($i === 1 ? '[#1a365d]' : 'green-600');
                            $bgClass = $i === 0 ? 'bg-[#f97316]' : ($i === 1 ? 'bg-[#1a365d]' : 'bg-green-500');
                        ?>
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-sm text-slate-900 truncate"><?= htmlspecialchars($g['agenda']) ?></span>
                                <span class="text-xs font-medium text-<?= $colorClass ?>"><?= round($pct) ?>%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                <div class="<?= $bgClass ?> h-1.5 rounded-full" style="width: <?= round($pct) ?>%"></div>
                            </div>
                            <p class="text-xs text-slate-500 mt-1.5 font-normal">
                                <?= htmlspecialchars($g['tanggal_fmt']) ?> · oleh <?= htmlspecialchars($g['penulis'] ?: '—') ?>
                            </p>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Data untuk Chart.js -->
<script>
window.__berandaData = {
    /* Chart 1: Enrollment per prodi (line chart) */
    enrollment: {
        labels: <?= json_encode($recentYears) ?>,
        datasetLabels: <?= json_encode($chartDatasetLabels) ?>,
        values: <?= json_encode($chartDatasetValues) ?>
    },
    /* Chart 2: Distribusi konsentrasi (doughnut) */
    concentration: {
        labels: <?= json_encode($concentrationLabels) ?>,
        values: <?= json_encode($concentrationValues) ?>
    }
};
</script>
