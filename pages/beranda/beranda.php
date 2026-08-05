<?php
/**
 * Halaman Beranda — Dasbor utama (Tailwind).
 *
 * LATAR TERANG dengan komposisi tegas & proporsional:
 *   - Hero band navy (panel, bukan latar halaman) sebagai momen "wow".
 *   - 4 kartu metrik ringkas + mini-sparkline data real.
 *   - Grafik, antrean dokumen + agenda (proporsi asimetris), berita.
 */

require_once __DIR__ . '/../../koneksi.php';

/* ================================================================
   DATA (semua opsional; fallback 0 jika tabel kosong)
   ================================================================ */

// --- Mahasiswa aktif per prodi (7 tahun terakhir) ---
$qProdi = mysqli_query($koneksi, "SELECT * FROM prodi ORDER BY nama");
$prodiList = [];
$totalMhs = 0;
$aktifSince = date('Y') - 7;
while ($p = mysqli_fetch_assoc($qProdi)) {
    $q = mysqli_query($koneksi,
        "SELECT COUNT(*) AS c FROM mahasiswa
         WHERE prodi='" . mysqli_real_escape_string($koneksi, $p['kode']) . "'
           AND angkatan >= " . (int)$aktifSince . " AND status='Aktif'");
    $p['mahasiswa'] = (int)mysqli_fetch_assoc($q)['c'];
    $prodiList[] = $p;
    $totalMhs += (int)$p['mahasiswa'];
}

// --- Mahasiswa baru tahun berjalan & total keseluruhan ---
$qMhsTahunIni = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM mahasiswa WHERE angkatan=" . (int)date('Y'));
$mhsBaruTahunIni = (int)mysqli_fetch_assoc($qMhsTahunIni)['c'];
$qMhsTotal = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM mahasiswa");
$mhsTotalSemua = (int)mysqli_fetch_assoc($qMhsTotal)['c'];

// --- Dosen aktif ---
$qDosenAktif = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM dosen WHERE status='aktif'");
$dosenAktif = (int)mysqli_fetch_assoc($qDosenAktif)['c'];

// --- Skripsi / KP aktif ---
$qSkripsiAktif = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM skripsi WHERE status='aktif'");
$skripsiAktif = (int)mysqli_fetch_assoc($qSkripsiAktif)['c'];
$qKpAktif = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM kp WHERE status='aktif'");
$kpAktif = (int)mysqli_fetch_assoc($qKpAktif)['c'];

// --- Surat penunjukkan belum ditandatangani ---
$qTTDPending = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM surat_penunjukkan WHERE ttd='' OR ttd IS NULL");
$ttdPendingTotal = (int)mysqli_fetch_assoc($qTTDPending)['c'];
$qTTD = mysqli_query($koneksi,
    "SELECT sp.*, m.nama FROM surat_penunjukkan sp
     LEFT JOIN mahasiswa m ON sp.nim=m.nim
     WHERE sp.ttd='' OR sp.ttd IS NULL ORDER BY sp.id DESC LIMIT 6");
$ttdItems = [];
while ($t = mysqli_fetch_assoc($qTTD)) { $ttdItems[] = $t; }

// --- Tren mahasiswa baru 5 tahun ---
$years = [];
$mhsPerYear = [];
for ($y = date('Y') - 4; $y <= date('Y'); $y++) {
    $years[] = (string)$y;
    $q = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM mahasiswa WHERE angkatan=$y");
    $mhsPerYear[] = (int)mysqli_fetch_assoc($q)['c'];
}

// --- Distribusi per prodi (label ringkas) ---
$concLabels = [];
$concValues = [];
foreach ($prodiList as $pr) {
    $namaRingkas = preg_replace('/^S1\s+/i', '', $pr['nama']);
    $namaRingkas = str_ireplace('Pendidikan Vokasional ', '', $namaRingkas);
    $namaRingkas = trim($namaRingkas);
    if ($namaRingkas === '') $namaRingkas = $pr['nama'];
    $concLabels[] = $namaRingkas;
    $concValues[] = (int)$pr['mahasiswa'];
}

// --- Agenda mendatang (tanggal unix) ---
$qAgenda = mysqli_query($koneksi,
    "SELECT a.*, d.nama AS penulis FROM agenda a
     LEFT JOIN dosen d ON a.penulis=d.nip
     WHERE a.tanggal > " . time() . " ORDER BY a.tanggal ASC LIMIT 4");
$agendaItems = [];
if ($qAgenda) {
    while ($g = mysqli_fetch_assoc($qAgenda)) { $agendaItems[] = $g; }
}
if (count($agendaItems) === 0) {
    $agendaItems = [
        ['tanggal' => time() + 86400,     'tanggal_fmt' => date('d M Y', time() + 86400),     'agenda' => 'Rapat Koordinasi Jurusan',   'penulis' => ''],
        ['tanggal' => time() + 3 * 86400, 'tanggal_fmt' => date('d M Y', time() + 3 * 86400), 'agenda' => 'Audit Mutu Internal Prodi', 'penulis' => ''],
        ['tanggal' => time() + 7 * 86400, 'tanggal_fmt' => date('d M Y', time() + 7 * 86400), 'agenda' => 'Sidang Proposal Skripsi',   'penulis' => ''],
    ];
}

// --- Berita jurusan terbaru ---
$beritaItems = [];
$qBerita = mysqli_query($koneksi,
    "SELECT judul, deskripsi, tanggal FROM berita WHERE judul <> '' ORDER BY tanggal DESC LIMIT 3");
if ($qBerita) {
    while ($b = mysqli_fetch_assoc($qBerita)) { $beritaItems[] = $b; }
}

/* ================================================================
   HELPERS
   ================================================================ */
$hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$today_label = $hari[(int)date('w')] . ', ' . (int)date('d') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');

// format ribuan
function f($n){ return number_format((int)$n, 0, ',', '.'); }

// bangun path poligon mini-sparkline dari array data real
function spark_path(array $vals, int $w = 120, int $h = 36): string {
    $n = count($vals);
    if ($n === 0) return '';
    $max = max(1, max($vals));
    $min = min($vals);
    $span = max(1, $max - $min);
    $pts = [];
    foreach ($vals as $i => $v) {
        $x = $n === 1 ? $w / 2 : ($i / ($n - 1)) * $w;
        $y = $h - (($v - $min) / $span) * ($h - 4) - 2;
        $pts[] = round($x, 1) . ',' . round($y, 1);
    }
    return implode(' ', $pts);
}
$spark = spark_path($mhsPerYear);
?>

<style>
    .tile-orange { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }
    .tile-sky    { background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); }
    .tile-emerald{ background: linear-gradient(135deg, #047857 0%, #10b981 100%); }
    .tile-rose   { background: linear-gradient(135deg, #be123c 0%, #fb7185 100%); }

    .lift { transition: transform .2s ease, box-shadow .2s ease; }
    .lift:hover { transform: translateY(-3px); box-shadow: 0 14px 28px -14px rgba(15,23,42,.22); }

    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
    @media (prefers-reduced-motion: reduce) { .reveal, .lift { animation: none; transition: none; } }

    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<main class="flex-1 overflow-y-auto bg-slate-100/80">
    <div class="w-full px-4 py-5 lg:px-6 space-y-5">

        <!-- ============ HERO BAND ============ -->
        <div class="reveal relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#0f1f3d] via-[#1a365d] to-[#234670] text-white shadow-xl shadow-[#1a365d]/30">
            <!-- dekorasi mesh -->
            <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="xMidYMid slice" viewBox="0 0 800 200" aria-hidden="true">
                <defs>
                    <radialGradient id="hs1" cx="90%" cy="0%" r="60%">
                        <stop offset="0%" stop-color="#f97316" stop-opacity="0.35"/>
                        <stop offset="100%" stop-color="#f97316" stop-opacity="0"/>
                    </radialGradient>
                    <radialGradient id="hs2" cx="0%" cy="100%" r="55%">
                        <stop offset="0%" stop-color="#0ea5e9" stop-opacity="0.35"/>
                        <stop offset="100%" stop-color="#0ea5e9" stop-opacity="0"/>
                    </radialGradient>
                </defs>
                <rect width="800" height="200" fill="url(#hs2)"/>
                <rect width="800" height="200" fill="url(#hs1)"/>
            </svg>

            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-6 px-6 py-7 lg:px-8">
                <!-- kiri: sapaan -->
                <div class="flex-1 min-w-0">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-orange-200 ring-1 ring-white/15">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-400"></span>
                        </span>
                        <?= htmlspecialchars($today_label) ?>
                    </span>
                    <h1 class="mt-3 text-xl lg:text-2xl font-bold tracking-tight">Selamat Datang, Admin</h1>
                    <p class="mt-1 max-w-xl text-sm text-slate-300">Pantau dan kelola operasional jurusan Teknik Elektro &amp; Komputer dari satu tempat.</p>
                    <div class="mt-4 flex flex-wrap gap-2.5">
                        <a href="index.php?page=jurusan-surat-penunjukkan"
                           class="inline-flex items-center gap-2 rounded-lg bg-[#f97316] px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-orange-500/30 transition hover:bg-[#ea580c] active:scale-[.98]">
                            <i class="fas fa-pen-to-square"></i> Tindak Lanjut Surat
                        </a>
                        <a href="index.php?page=jurusan-berita"
                           class="inline-flex items-center gap-2 rounded-lg bg-white/10 px-4 py-2 text-sm font-medium text-white ring-1 ring-white/20 transition hover:bg-white/15 active:scale-[.98]">
                            <i class="fas fa-bullhorn"></i> Berita Jurusan
                        </a>
                    </div>
                </div>
                <!-- kanan: spot statistik -->
                <div class="grid grid-cols-2 gap-3 sm:min-w-[260px]">
                    <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur-sm">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-300">Mahasiswa Aktif</p>
                        <p class="mt-1 text-2xl font-bold"><?= f($totalMhs) ?></p>
                        <p class="mt-0.5 text-[11px] text-orange-200">+<?= f($mhsBaruTahunIni) ?> th ini</p>
                    </div>
                    <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur-sm">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-300">Menunggu TTD</p>
                        <p class="mt-1 text-2xl font-bold"><?= f($ttdPendingTotal) ?></p>
                        <p class="mt-0.5 text-[11px] text-sky-200">dokumen proses</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============ METRIK UTAMA ============ -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3.5">
            <!-- Mahasiswa (dengan sparkline) -->
            <div class="reveal tile-orange lift rounded-xl p-4 text-white shadow-md shadow-orange-500/25">
                <div class="flex items-center justify-between">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-user-graduate"></i></span>
                    <span class="rounded-full bg-white/25 px-2 py-0.5 text-[10px] font-bold">+<?= f($mhsBaruTahunIni) ?></span>
                </div>
                <p class="mt-3 text-sm font-medium text-white/85">Mahasiswa Aktif</p>
                <p class="text-2xl font-bold tracking-tight"><?= f($totalMhs) ?></p>
                <svg class="mt-2 w-full" viewBox="0 0 120 36" preserveAspectRatio="none" style="height:26px" aria-hidden="true">
                    <polyline points="<?= $spark ?>" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.9"/>
                </svg>
            </div>

            <div class="reveal tile-sky lift rounded-xl p-4 text-white shadow-md shadow-sky-500/25" style="animation-delay:.05s">
                <div class="flex items-center justify-between">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-chalkboard-user"></i></span>
                    <span class="rounded-full bg-white/25 px-2 py-0.5 text-[10px] font-bold">terdaftar</span>
                </div>
                <p class="mt-3 text-sm font-medium text-white/85">Dosen Aktif</p>
                <p class="text-2xl font-bold tracking-tight"><?= f($dosenAktif) ?></p>
                <p class="mt-2 text-[11px] text-white/70">Tenaga pengajar &amp; pembimbing.</p>
            </div>

            <div class="reveal tile-emerald lift rounded-xl p-4 text-white shadow-md shadow-emerald-500/25" style="animation-delay:.10s">
                <div class="flex items-center justify-between">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-book-open"></i></span>
                    <span class="rounded-full bg-white/25 px-2 py-0.5 text-[10px] font-bold">bimbingan</span>
                </div>
                <p class="mt-3 text-sm font-medium text-white/85">Skripsi Berjalan</p>
                <p class="text-2xl font-bold tracking-tight"><?= f($skripsiAktif) ?></p>
                <p class="mt-2 text-[11px] text-white/70">Mahasiswa dalam bimbingan.</p>
            </div>

            <div class="reveal tile-rose lift rounded-xl p-4 text-white shadow-md shadow-rose-500/25" style="animation-delay:.15s">
                <div class="flex items-center justify-between">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-briefcase"></i></span>
                    <span class="rounded-full bg-white/25 px-2 py-0.5 text-[10px] font-bold">magang</span>
                </div>
                <p class="mt-3 text-sm font-medium text-white/85">Kerja Praktek</p>
                <p class="text-2xl font-bold tracking-tight"><?= f($kpAktif) ?></p>
                <p class="mt-2 text-[11px] text-white/70">Mahasiswa magang industri.</p>
            </div>
        </div>

        <!-- ============ GRAFIK ============ -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3.5">
            <div class="reveal lg:col-span-2 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Tren Mahasiswa Baru</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Pendaftaran 5 tahun terakhir</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-2.5 py-1 text-xs font-semibold text-orange-700">
                        <i class="fas fa-arrow-trend-up"></i> Tahunan
                    </span>
                </div>
                <div style="height:230px"><canvas id="enrollmentChart"></canvas></div>
            </div>
            <div class="reveal rounded-xl border border-slate-200 bg-white p-5 shadow-sm" style="animation-delay:.05s">
                <div class="mb-4">
                    <h2 class="text-base font-semibold text-slate-800">Distribusi Mahasiswa</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Per program studi</p>
                </div>
                <div style="height:230px"><canvas id="concentrationChart"></canvas></div>
            </div>
        </div>

        <!-- ============ DOKUMEN + AGENDA (asimetris) ============ -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-3.5">
            <!-- Antrean TTD (lebih lebar) -->
            <div class="reveal lg:col-span-3 rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-3.5">
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Antrean Tanda Tangan</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Dokumen menunggu persetujuan</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-500 px-3 py-1 text-xs font-bold text-white">
                        <i class="fas fa-clock"></i> <?= f($ttdPendingTotal) ?>
                    </span>
                </div>
                <?php if (count($ttdItems) === 0): ?>
                    <div class="flex flex-col items-center py-10 text-center">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-100 text-emerald-600"><i class="fas fa-check"></i></span>
                        <p class="mt-3 text-sm text-slate-500">Tidak ada dokumen yang menunggu tanda tangan.</p>
                    </div>
                <?php else: ?>
                <ul class="divide-y divide-slate-100">
                    <?php
                    $avatarColors = ['f97316','0ea5e9','10b981','f43f5e','8b5cf6','f59e0b'];
                    foreach ($ttdItems as $t):
                        $namaTampil = $t['nama'] ?: ($t['nim'] ?? '?');
                        $inisial = strtoupper(mb_substr($namaTampil, 0, 2));
                        $bg = $avatarColors[crc32($t['nim'] ?? $inisial) % count($avatarColors)];
                    ?>
                    <li class="flex items-center gap-3 px-5 py-3 transition hover:bg-orange-50/40">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white" style="background:<?= $bg ?>"><?= htmlspecialchars($inisial) ?></span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800"><?= htmlspecialchars($namaTampil) ?></p>
                            <p class="font-mono text-[11px] text-slate-400"><?= htmlspecialchars($t['nim']) ?></p>
                        </div>
                        <span class="hidden md:block max-w-[140px] truncate text-xs text-slate-500"><?= htmlspecialchars($t['keterangan'] ?: 'Surat Penunjukkan') ?></span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-2.5 py-1 text-xs font-semibold text-orange-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#f97316]"></span> Pending
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <a href="index.php?page=jurusan-surat-penunjukkan" class="block border-t border-slate-100 bg-slate-50/60 px-5 py-2.5 text-center text-xs font-semibold text-orange-600 hover:bg-orange-50 transition">
                    Buka semua surat penunjukkan →
                </a>
                <?php endif; ?>
            </div>

            <!-- Agenda (lebih sempit) -->
            <div class="reveal lg:col-span-2 rounded-xl border border-slate-200 bg-white p-5 shadow-sm" style="animation-delay:.05s">
                <div class="mb-4">
                    <h2 class="text-base font-semibold text-slate-800">Agenda Mendatang</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Jadwal departemen terdekat</p>
                </div>
                <ol class="relative space-y-0">
                    <?php
                    $pills = ['#f97316','#0ea5e9','#10b981','#8b5cf6'];
                    foreach ($agendaItems as $i => $g):
                        $p = $pills[$i % count($pills)];
                    ?>
                    <li class="relative flex gap-3 pb-4 last:pb-0">
                        <?php if ($i < count($agendaItems) - 1): ?>
                        <span class="absolute left-[11px] top-8 bottom-0 w-px bg-slate-200"></span>
                        <?php endif; ?>
                        <span class="relative mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full" style="background:<?= $p ?>; box-shadow:0 0 0 3px rgba(0,0,0,.04)">
                            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold leading-snug text-slate-800"><?= htmlspecialchars($g['agenda']) ?></p>
                            <p class="mt-0.5 text-xs text-slate-400"><i class="far fa-calendar mr-1"></i><?= htmlspecialchars($g['tanggal_fmt']) ?><?php if (!empty($g['penulis'])): ?> · <?= htmlspecialchars($g['penulis']) ?><?php endif; ?></p>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>

        <!-- ============ BERITA ============ -->
        <div class="reveal rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Berita &amp; Informasi Jurusan</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Publikasi terbaru</p>
                </div>
                <a href="index.php?page=jurusan-berita" class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition">Semua Berita →</a>
            </div>
            <?php if (count($beritaItems) === 0): ?>
                <p class="py-8 text-center text-sm text-slate-400">Belum ada berita jurusan.</p>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                <?php foreach ($beritaItems as $b):
                    $tanggalParsed = strtotime($b['tanggal']);
                    $tanggalTxt = $tanggalParsed ? date('d M Y', $tanggalParsed) : '';
                ?>
                <a href="index.php?page=jurusan-berita" class="group flex flex-col rounded-lg border border-slate-200 bg-slate-50/60 p-4 transition hover:border-orange-300 hover:bg-white hover:shadow-md hover:-translate-y-0.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-[#f97316] to-[#fb923c] text-white text-sm shadow-md shadow-orange-500/20"><i class="fas fa-file-lines"></i></span>
                    <h3 class="mt-3 text-sm font-semibold leading-snug text-slate-800 group-hover:text-orange-700 transition line-clamp-2"><?= htmlspecialchars($b['judul']) ?></h3>
                    <?php if (!empty($b['deskripsi'])): ?>
                    <p class="mt-1.5 text-xs leading-relaxed text-slate-500 line-clamp-2"><?= htmlspecialchars($b['deskripsi']) ?></p>
                    <?php endif; ?>
                    <p class="mt-3 text-[11px] font-medium text-slate-400"><i class="far fa-clock mr-1"></i><?= htmlspecialchars($tanggalTxt) ?></p>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<!-- Data untuk Chart.js -->
<script>
window.__berandaData = {
    enrollment: {
        labels: <?= json_encode($years) ?>,
        values: <?= json_encode($mhsPerYear) ?>
    },
    concentration: {
        labels: <?= json_encode($concLabels) ?>,
        values: <?= json_encode($concValues) ?>
    }
};
</script>
