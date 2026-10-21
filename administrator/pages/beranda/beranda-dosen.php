<?php
/**
 * View Beranda: Dosen Pengajar & Pembimbing (Level 1)
 * Dashboard Akademik Resmi SIATEK — Bento Grid Asimetris & Live Schedule.
 */
declare(strict_types=1);

// Identifikasi Dosen
$nipDosen = bersihkan((string)($_SESSION['nip'] ?? $_SESSION['user_id'] ?? ''));
$idDosen  = bersihkan((string)($_SESSION['id'] ?? ''));

// Ambil profil lengkap dosen
$qDosenMe = query("SELECT * FROM dosen WHERE (nip != '' AND nip = '$nipDosen') OR (id != '' AND id = '$idDosen') LIMIT 1");
$dosenMe = $qDosenMe ? mysqli_fetch_assoc($qDosenMe) : [];

// Fallback jika Super Admin mempratinjau peran Dosen tanpa NIP spesifik
if (empty($dosenMe)) {
    $qFallback = query("SELECT * FROM dosen WHERE posisi='dosen' AND status='aktif' ORDER BY id ASC LIMIT 1");
    if ($qFallback && ($rf = mysqli_fetch_assoc($qFallback))) {
        $dosenMe = $rf;
    }
}

if (!empty($dosenMe['id'])) {
    $idDosen = $dosenMe['id'];
}
if (!empty($dosenMe['nip'])) {
    $nipDosen = $dosenMe['nip'];
}
$nidnDosen = $dosenMe['nidn'] ?? '-';
$jabatanFung = $dosenMe['jabatan_fungsional'] ?? 'Tenaga Pengajar';
$pangkatGol  = $dosenMe['pangkat_golongan'] ?? '';
$prodiDosen = $dosenMe['prodi'] ?? '';
$statusDosen = trim((string)($dosenMe['status'] ?? 'aktif'));
$isDosenAktif = (strtolower($statusDosen) === 'aktif');
$namaDosenTampil = trim((string)($dosenMe['nama'] ?? $nmUser));
$initialsDosen = mb_substr(preg_replace('/[^\p{L}]/u', '', explode(',', $namaDosenTampil)[0]), 0, 2);
if ($initialsDosen === '') $initialsDosen = 'DS';

// Foto profil dosen
$fotoProfil = trim((string)($dosenMe['profil'] ?? ''));
$srcFotoDosen = ($fotoProfil !== '' && function_exists('url_file')) ? url_file($fotoProfil) : '';

// Bidang keahlian dosen
$bidangKeahlianArr = array_values(array_filter(array_map('trim', explode(',', (string)($dosenMe['keahlian'] ?? ''))), 'strlen'));

// Nama prodi dosen
$namaProdiDosen = $prodiDosen;
if (!empty($prodiDosen)) {
    $qPr = query("SELECT nama FROM prodi WHERE kode = '$prodiDosen' OR nama = '$prodiDosen' LIMIT 1");
    if ($qPr && ($rp = mysqli_fetch_assoc($qPr))) {
        $namaProdiDosen = $rp['nama'];
    }
}

// Pendidikan terakhir dosen
$eduTerakhir = '';
if (!empty($nipDosen)) {
    $qEdu = query("SELECT jenjang FROM riwayat_pendidikan WHERE nip = '$nipDosen' ORDER BY tahun DESC, id DESC");
    $rankEdu = ['TK'=>0,'SD'=>1,'SMP'=>2,'SMA/SMK'=>3,'D1'=>4,'D2'=>5,'D3'=>6,'D4'=>7,'S1'=>8,'S2'=>9,'S3'=>10];
    if ($qEdu) {
        while ($re = mysqli_fetch_assoc($qEdu)) {
            $j = trim((string)$re['jenjang']);
            if ($eduTerakhir === '' || ($rankEdu[$j] ?? -1) >= ($rankEdu[$eduTerakhir] ?? -1)) {
                $eduTerakhir = $j;
            }
        }
    }
}

$tahunBerjalan = (int)date('Y');

// 1. Akumulasi Bimbingan Skripsi (Tahun Berjalan dari Surat Penunjukkan)
$qBimSkripsi = query("
    SELECT COUNT(*) AS c
    FROM surat_penunjukkan sp
    WHERE sp.keterangan = 'PEMBIMBING SKRIPSI'
      AND (YEAR(sp.tanggal) = $tahunBerjalan OR sp.tanggal LIKE '$tahunBerjalan%')
      AND (sp.pembimbing1 = '$idDosen' OR sp.pembimbing1 = '$nipDosen'
        OR sp.pembimbing2 = '$idDosen' OR sp.pembimbing2 = '$nipDosen')
");
$totalBimSkripsi = $qBimSkripsi ? (int)mysqli_fetch_assoc($qBimSkripsi)['c'] : 0;

// 2. Akumulasi Bimbingan Kerja Praktek (KP) (Tahun Berjalan dari Surat Penunjukkan)
$qBimKp = query("
    SELECT COUNT(*) AS c
    FROM surat_penunjukkan sp
    WHERE sp.keterangan = 'PEMBIMBING KP'
      AND (YEAR(sp.tanggal) = $tahunBerjalan OR sp.tanggal LIKE '$tahunBerjalan%')
      AND (sp.pembimbing1 = '$idDosen' OR sp.pembimbing1 = '$nipDosen'
        OR sp.pembimbing2 = '$idDosen' OR sp.pembimbing2 = '$nipDosen')
");
$totalBimKp = $qBimKp ? (int)mysqli_fetch_assoc($qBimKp)['c'] : 0;

// 3. Jadwal Mengajar (Senin - Sabtu & Semester)
$daftarHari = [
    'SENIN'  => 'Senin',
    'SELASA' => 'Selasa',
    'RABU'   => 'Rabu',
    'KAMIS'  => 'Kamis',
    'JUMAT'  => 'Jumat',
    'SABTU'  => 'Sabtu'
];

$hariMap = [
    'Sunday' => 'MINGGU', 'Monday' => 'SENIN', 'Tuesday' => 'SELASA',
    'Wednesday' => 'RABU', 'Thursday' => 'KAMIS', 'Friday' => 'JUMAT', 'Saturday' => 'SABTU'
];
$hariIni = $hariMap[date('l')] ?? 'SENIN';
$hariAktifDefault = ($hariIni === 'MINGGU') ? 'SENIN' : $hariIni;

$qJadwalSemua = query("
    SELECT jk.*, mk.nama AS nama_matkul, mk.kode AS kode_mk, mk.sks
    FROM jadwal_kuliah jk
    LEFT JOIN matakuliah mk ON jk.id_matakuliah = mk.id
    WHERE jk.pengajar1 = '$idDosen' OR jk.pengajar2 = '$idDosen'
       OR jk.pengajar1 = '$nipDosen' OR jk.pengajar2 = '$nipDosen'
    ORDER BY FIELD(jk.hari, 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'), jk.jam ASC
");

$jadwalPerHari = [
    'SENIN'  => [],
    'SELASA' => [],
    'RABU'   => [],
    'KAMIS'  => [],
    'JUMAT'  => [],
    'SABTU'  => []
];
$totalSksMengajar = 0;
$totalKelasAjar = 0;

if ($qJadwalSemua) {
    while ($j = mysqli_fetch_assoc($qJadwalSemua)) {
        $totalSksMengajar += (int)($j['sks'] ?? 0);
        $totalKelasAjar++;
        $hKey = strtoupper(trim((string)$j['hari']));
        if (isset($jadwalPerHari[$hKey])) {
            $jadwalPerHari[$hKey][] = $j;
        }
    }
}
$jadwalHariIni = $jadwalPerHari[$hariIni] ?? [];

// 4. Akumulasi Tugas Penguji Sidang (Tahun Berjalan dari Surat Penunjukkan)
$qPenguji = query("
    SELECT COUNT(*) AS c
    FROM surat_penunjukkan sp
    WHERE (sp.keterangan LIKE '%PENGUJI%' OR sp.keterangan IN ('PENGUJI HASIL', 'PENGUJI TUTUP', 'PENGUJI KP'))
      AND (YEAR(sp.tanggal) = $tahunBerjalan OR sp.tanggal LIKE '$tahunBerjalan%')
      AND (sp.penguji1 = '$idDosen' OR sp.penguji1 = '$nipDosen'
        OR sp.penguji2 = '$idDosen' OR sp.penguji2 = '$nipDosen'
        OR sp.penguji3 = '$idDosen' OR sp.penguji3 = '$nipDosen'
        OR sp.penguji4 = '$idDosen' OR sp.penguji4 = '$nipDosen'
        OR sp.penguji5 = '$idDosen' OR sp.penguji5 = '$nipDosen')
");
$totalTugasPenguji = $qPenguji ? (int)mysqli_fetch_assoc($qPenguji)['c'] : 0;

// Berita
$beritaItems = [];
$qBerita = query("SELECT judul, deskripsi, tanggal FROM berita WHERE judul <> '' ORDER BY tanggal DESC LIMIT 3");
if ($qBerita) {
    while ($b = mysqli_fetch_assoc($qBerita)) { $beritaItems[] = $b; }
}

// Menit saat ini untuk deteksi jadwal aktif
$nowMinutes = (int)date('H') * 60 + (int)date('i');
?>

<!-- ============ PAGE HEADER (KONSISTEN SIATEK) ============ -->
<section class="flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3.5">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600 shadow-2xs">
            <i class="fas fa-chalkboard-user"></i>
        </div>
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-800">
                Selamat Datang, <?= htmlspecialchars($nmUser) ?>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                NIP: <span class="font-mono text-slate-700"><?= htmlspecialchars($nipDosen ?: '-') ?></span>
                <?php if ($nidnDosen !== '-' && $nidnDosen !== ''): ?>
                    &bull; NIDN: <span class="font-mono text-slate-700"><?= htmlspecialchars($nidnDosen) ?></span>
                <?php endif; ?>
                &bull; <span class="font-medium text-orange-600"><?= htmlspecialchars($jabatanFung) ?></span>
                &bull; <span class="font-medium text-slate-700"><?= htmlspecialchars($today_label) ?></span>
            </p>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="/redesain-siatek/administrator/bimbingan-skripsi"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-orange-300 px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
            <i class="fas fa-book-open text-orange-500"></i> Bimbingan Skripsi (<?= $totalBimSkripsi ?>)
        </a>
        <a href="/redesain-siatek/administrator/bimbingan-kp"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-orange-300 px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
            <i class="fas fa-briefcase text-orange-500"></i> Bimbingan KP (<?= $totalBimKp ?>)
        </a>
        <a href="/redesain-siatek/administrator/jadwal"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-orange-300 px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
            <i class="fas fa-calendar-check text-[#1a365d]"></i> Jadwal Kuliah
        </a>
    </div>
</section>

<!-- ============ 4 KARTU METRIK AKADEMIK DENGAN 1 KARTU JANGKAR NAVY ============ -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- 1. Bimbingan Skripsi -->
    <a href="/redesain-siatek/administrator/bimbingan-skripsi" 
       class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-orange-300 transition block group flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Bimbingan Skripsi</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 text-orange-600 text-xs group-hover:bg-[#f97316] group-hover:text-white transition">
                    <i class="fas fa-book-open"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($totalBimSkripsi) ?> <span class="text-xs font-normal text-slate-400">mhs</span></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Pembimbing 1 &amp; 2</span>
            <span class="font-semibold text-orange-600 group-hover:underline">Buka &rarr;</span>
        </div>
    </a>

    <!-- 2. Bimbingan Kerja Praktek -->
    <a href="/redesain-siatek/administrator/bimbingan-kp" 
       class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-orange-300 transition block group flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Bimbingan KP</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs group-hover:bg-[#1a365d] group-hover:text-white transition">
                    <i class="fas fa-briefcase"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($totalBimKp) ?> <span class="text-xs font-normal text-slate-400">mhs</span></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Praktik industri aktif</span>
            <span class="font-semibold text-slate-700 group-hover:underline">Buka &rarr;</span>
        </div>
    </a>

    <!-- 3. Tugas Penguji Sidang -->
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tugas Penguji</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs">
                    <i class="fas fa-clipboard-check"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($totalTugasPenguji) ?> <span class="text-xs font-normal text-slate-400">sidang</span></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Dewan penguji ujian</span>
            <span class="font-semibold text-slate-700">Terdaftar</span>
        </div>
    </div>

    <!-- 4. KARTU JANGKAR EKSEKUTIF (NAVY ANCHOR CARD - BEBAN MENGAJAR) -->
    <div class="rounded-xl bg-[#1a365d] text-white p-5 shadow-md shadow-slate-900/10 flex flex-col justify-between relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-white/5 pointer-events-none"></div>
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-widest text-orange-300 flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                    Beban Mengajar
                </span>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 text-orange-300 text-xs">
                    <i class="fas fa-calendar-days"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl sm:text-4xl font-bold font-mono tracking-tight text-white">
                <?= f($totalSksMengajar) ?> <span class="text-base font-normal text-slate-300">SKS</span>
            </p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between text-xs">
            <span class="text-slate-300"><?= $totalKelasAjar ?> kelas sem. &bull; <?= count($jadwalHariIni) ?> hari ini</span>
            <a href="/redesain-siatek/administrator/jadwal" class="font-semibold text-orange-300 hover:text-orange-200 transition">
                Jadwal &rarr;
            </a>
        </div>
    </div>
</section>

<!-- ============ BENTO GRID ASIMETRIS (2/3 KANVAS AKADEMIK VS 1/3 TIMELINE) ============ -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

    <!-- ===== KOLOM KIRI (8 KOLOM / 66%): JADWAL KULIAH HARI INI & MAHASISWA BIMBINGAN ===== -->
    <div class="lg:col-span-8 space-y-5">

        <!-- 1. Jadwal Perkuliahan Interaktif (Senin - Sabtu, Default Hari Ini) -->
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 bg-slate-50/70">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">
                        Jadwal Mengajar Dosen
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Agenda perkuliahan semester aktif (Senin – Sabtu)</p>
                </div>
                <a href="/redesain-siatek/administrator/jadwal" class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition">
                    Jadwal Lengkap &rarr;
                </a>
            </div>

            <!-- Tab Bar Hari (Senin - Sabtu) -->
            <div class="flex items-center gap-1.5 px-5 py-2.5 bg-slate-50/90 border-b border-slate-100 overflow-x-auto">
                <?php foreach ($daftarHari as $kHari => $lHari): 
                    $jmlKls = count($jadwalPerHari[$kHari]);
                    $isDefActive = ($kHari === $hariAktifDefault);
                ?>
                <button type="button" 
                        onclick="switchTabJadwal('<?= $kHari ?>')"
                        id="tab-btn-<?= $kHari ?>"
                        class="tab-jadwal-btn shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition <?= $isDefActive ? 'bg-[#1a365d] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' ?>">
                    <span><?= $lHari ?></span>
                    <span class="inline-flex items-center justify-center min-w-4 h-4 px-1 rounded-full text-[10px] font-bold <?= $isDefActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' ?> tab-count-badge">
                        <?= $jmlKls ?>
                    </span>
                </button>
                <?php endforeach; ?>
            </div>

            <!-- Tab Content Pane (Per Hari) -->
            <?php foreach ($daftarHari as $kHari => $lHari): 
                $isToday = ($kHari === $hariIni);
                $isDefActive = ($kHari === $hariAktifDefault);
                $listKelas = $jadwalPerHari[$kHari];
            ?>
            <div id="tab-pane-<?= $kHari ?>" class="tab-jadwal-pane <?= $isDefActive ? '' : 'hidden' ?>">
                <?php if (count($listKelas) === 0): ?>
                    <div class="px-5 py-8 text-center">
                        <div class="w-10 h-10 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-sm mb-2">
                            <i class="far fa-calendar-xmark"></i>
                        </div>
                        <p class="text-xs font-medium text-slate-500">Tidak ada jadwal perkuliahan pada hari <?= htmlspecialchars($lHari) ?>.</p>
                        <?php if ($isToday): ?>
                            <p class="text-[11px] text-slate-400 mt-1">Hari ini Anda tidak memiliki agenda tatap muka di kelas.</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($listKelas as $jh):
                            $jamStr = trim((string)($jh['jam'] ?? ''));
                            $isLiveNow = false;
                            if ($isToday && preg_match('/(\d{1,2})[:.](\d{2})\s*[-–—]\s*(\d{1,2})[:.](\d{2})/', $jamStr, $mJam)) {
                                $startM = (int)$mJam[1] * 60 + (int)$mJam[2];
                                $endM   = (int)$mJam[3] * 60 + (int)$mJam[4];
                                if ($nowMinutes >= $startM && $nowMinutes <= $endM) {
                                    $isLiveNow = true;
                                }
                            }
                        ?>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 px-5 hover:bg-slate-50/80 transition gap-3">
                            <div class="flex items-start gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#1a365d] font-bold text-white text-xs">
                                    <?= htmlspecialchars($jh['kelas'] ?? 'A') ?>
                                </span>
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($jh['nama_matkul'] ?: 'Mata Kuliah') ?></h4>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-slate-500">
                                        <span><i class="far fa-clock mr-1 text-slate-400"></i><?= htmlspecialchars($jh['jam'] ?: '-') ?></span>
                                        <span>&bull;</span>
                                        <span><i class="fas fa-location-dot mr-1 text-slate-400"></i><?= htmlspecialchars($jh['ruangan'] ?: 'Ruang Kelas') ?></span>
                                        <span>&bull;</span>
                                        <span>Kelas <?= htmlspecialchars($jh['kelas']) ?> &bull; <?= (int)$jh['sks'] ?> SKS</span>
                                    </div>
                                </div>
                            </div>
                            <?php if ($isLiveNow): ?>
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2.5 py-0.5 rounded-full shrink-0">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span>
                                    Sedang Berlangsung
                                </span>
                            <?php elseif ($isToday): ?>
                                <span class="inline-flex items-center text-xs font-semibold text-slate-700 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-lg shrink-0">
                                    Terjadwal Hari Ini
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center text-xs font-medium text-slate-500 bg-slate-50 border border-slate-200/60 px-2.5 py-1 rounded-lg shrink-0">
                                    Hari <?= htmlspecialchars($lHari) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </section>

        <!-- 2. Portofolio Tridharma & Kinerja Dosen -->
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="text-base font-semibold text-slate-800">Portofolio Tridharma &amp; Kinerja Dosen</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pintasan pemutakhiran data pangkalan dan pelaporan BKD / SKP</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <a href="/redesain-siatek/administrator/pd-pendidikan" class="flex flex-col items-center justify-center p-3.5 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-orange-200 transition text-center group">
                    <span class="w-9 h-9 rounded-lg bg-slate-100 text-[#1a365d] flex items-center justify-center text-sm group-hover:bg-orange-100 group-hover:text-orange-600 transition"><i class="fas fa-chalkboard-user"></i></span>
                    <span class="mt-2 text-xs font-semibold text-slate-800">Pendidikan</span>
                    <span class="text-[11px] text-slate-400">Pengajaran &amp; RPS</span>
                </a>
                <a href="/redesain-siatek/administrator/pd-penelitian" class="flex flex-col items-center justify-center p-3.5 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-orange-200 transition text-center group">
                    <span class="w-9 h-9 rounded-lg bg-slate-100 text-[#1a365d] flex items-center justify-center text-sm group-hover:bg-orange-100 group-hover:text-orange-600 transition"><i class="fas fa-microscope"></i></span>
                    <span class="mt-2 text-xs font-semibold text-slate-800">Penelitian</span>
                    <span class="text-[11px] text-slate-400">Publikasi &amp; Jurnal</span>
                </a>
                <a href="/redesain-siatek/administrator/pd-pengabdian" class="flex flex-col items-center justify-center p-3.5 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-orange-200 transition text-center group">
                    <span class="w-9 h-9 rounded-lg bg-slate-100 text-[#1a365d] flex items-center justify-center text-sm group-hover:bg-orange-100 group-hover:text-orange-600 transition"><i class="fas fa-hand-holding-heart"></i></span>
                    <span class="mt-2 text-xs font-semibold text-slate-800">Pengabdian</span>
                    <span class="text-[11px] text-slate-400">Kegiatan PKM</span>
                </a>
                <a href="/redesain-siatek/administrator/pd-penunjang" class="flex flex-col items-center justify-center p-3.5 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-orange-200 transition text-center group">
                    <span class="w-9 h-9 rounded-lg bg-slate-100 text-[#1a365d] flex items-center justify-center text-sm group-hover:bg-orange-100 group-hover:text-orange-600 transition"><i class="fas fa-award"></i></span>
                    <span class="mt-2 text-xs font-semibold text-slate-800">Penunjang</span>
                    <span class="text-[11px] text-slate-400">Panitia &amp; Sertifikasi</span>
                </a>
                <a href="/redesain-siatek/administrator/pd-skp" class="flex flex-col items-center justify-center p-3.5 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-orange-200 transition text-center group col-span-2 sm:col-span-1">
                    <span class="w-9 h-9 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-sm"><i class="fas fa-file-contract"></i></span>
                    <span class="mt-2 text-xs font-semibold text-slate-800">SKP Dosen</span>
                    <span class="text-[11px] text-slate-400">Sasaran Kinerja</span>
                </a>
            </div>
        </section>

    </div>

    <!-- ===== KOLOM KANAN (4 KOLOM / 34%): PROFIL & IDENTITAS AKADEMIK DOSEN ===== -->
    <div class="lg:col-span-4 space-y-5">

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-3.5 bg-slate-50/70">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Profil &amp; Identitas Dosen</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Identitas kepegawaian &amp; kepakaran</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold <?= $isDosenAktif ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/80' : 'bg-slate-100 text-slate-600' ?>">
                    <?php if ($isDosenAktif): ?>
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                        </span>
                    <?php endif; ?>
                    <?= $isDosenAktif ? 'Aktif Mengajar' : 'Non-Aktif' ?>
                </span>
            </div>

            <div class="p-5 space-y-4">
                <!-- Avatar & Ringkasan Nama -->
                <div class="flex items-center gap-3.5 pb-4 border-t-0 border-b border-slate-100">
                    <?php if (!empty($srcFotoDosen)): ?>
                        <img src="<?= htmlspecialchars($srcFotoDosen) ?>" alt="Foto Dosen" class="h-13 w-13 rounded-xl object-cover ring-2 ring-slate-100 shadow-xs shrink-0">
                    <?php else: ?>
                        <div class="flex h-13 w-13 shrink-0 items-center justify-center rounded-xl bg-[#1a365d] text-white font-bold text-base shadow-xs ring-2 ring-slate-100">
                            <?= htmlspecialchars($initialsDosen) ?>
                        </div>
                    <?php endif; ?>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-sm font-bold text-slate-900 leading-snug truncate" title="<?= htmlspecialchars($namaDosenTampil) ?>">
                            <?= htmlspecialchars($namaDosenTampil) ?>
                        </h4>
                        <p class="text-xs text-slate-500 mt-0.5 font-mono truncate">
                            NIP: <?= htmlspecialchars($nipDosen ?: '-') ?>
                        </p>
                        <?php if ($nidnDosen !== '-' && $nidnDosen !== ''): ?>
                            <p class="text-xs text-slate-500 font-mono truncate">
                                NIDN: <?= htmlspecialchars($nidnDosen) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Detail Informasi Akademik (Key-Value) -->
                <div class="space-y-3 text-xs">
                    <!-- Homebase Prodi -->
                    <div class="flex items-start gap-2.5">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs">
                            <i class="fas fa-building-columns"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Homebase Prodi</span>
                            <span class="font-semibold text-slate-800 leading-tight block mt-0.5"><?= htmlspecialchars($namaProdiDosen ?: 'Jurusan Teknik Elektro') ?></span>
                        </div>
                    </div>

                    <!-- Jabatan Fungsional & Golongan -->
                    <div class="flex items-start gap-2.5">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-orange-600 text-xs">
                            <i class="fas fa-award"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Jabatan &amp; Golongan</span>
                            <span class="font-semibold text-slate-800 leading-tight block mt-0.5">
                                <?= htmlspecialchars($jabatanFung) ?>
                                <?php if (!empty($pangkatGol)): ?>
                                    <span class="text-slate-400 font-normal">&bull; <?= htmlspecialchars($pangkatGol) ?></span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Pendidikan Terakhir -->
                    <?php if (!empty($eduTerakhir)): ?>
                    <div class="flex items-start gap-2.5">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600 text-xs">
                            <i class="fas fa-graduation-cap"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Pendidikan Terakhir</span>
                            <span class="font-semibold text-slate-800 leading-tight block mt-0.5">
                                Jenjang <?= htmlspecialchars($eduTerakhir) ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Bidang Kepakaran / Fokus Riset -->
                    <div class="flex items-start gap-2.5">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-600 text-xs">
                            <i class="fas fa-microscope"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Bidang Kepakaran</span>
                            <?php if (count($bidangKeahlianArr) > 0): ?>
                                <div class="flex flex-wrap gap-1.5 mt-1">
                                    <?php foreach ($bidangKeahlianArr as $bk): ?>
                                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700">
                                            <?= htmlspecialchars($bk) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-slate-400 italic text-[11px] block mt-0.5">Belum diatur</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Footer Tautan Cepat -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-400">Data Pangkalan SIATEK</span>
                    <a href="/redesain-siatek/administrator/biodata" class="font-semibold text-orange-600 hover:text-orange-700 transition flex items-center gap-1 group">
                        Biodata Lengkap <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>
            </div>
        </section>

    </div>
</div>

<!-- ============ BERITA JURUSAN ============ -->
<section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm mt-5">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-base font-semibold text-slate-800">Pengumuman &amp; Berita Jurusan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Informasi resmi akademik</p>
        </div>
        <a href="/redesain-siatek/administrator/jurusan-berita" class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition">Semua Berita &rarr;</a>
    </div>
    <?php if (count($beritaItems) === 0): ?>
        <p class="py-8 text-center text-xs text-slate-400">Belum ada pengumuman jurusan terbaru.</p>
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

<!-- ============ SCRIPT INTERAKTIF TAB JADWAL DOSEN ============ -->
<script>
function switchTabJadwal(hari) {
    // Sembunyikan semua pane
    document.querySelectorAll('.tab-jadwal-pane').forEach(function(pane) {
        pane.classList.add('hidden');
    });

    // Tampilkan pane hari yang dipilih
    var activePane = document.getElementById('tab-pane-' + hari);
    if (activePane) {
        activePane.classList.remove('hidden');
    }

    // Reset seluruh style tombol tab
    document.querySelectorAll('.tab-jadwal-btn').forEach(function(btn) {
        btn.classList.remove('bg-[#1a365d]', 'text-white', 'shadow-xs');
        btn.classList.add('bg-white', 'text-slate-600', 'border', 'border-slate-200');

        var countBadge = btn.querySelector('.tab-count-badge');
        if (countBadge) {
            countBadge.classList.remove('bg-white/20', 'text-white');
            countBadge.classList.add('bg-slate-100', 'text-slate-500');
        }
    });

    // Aktifkan style tombol tab terpilih
    var activeBtn = document.getElementById('tab-btn-' + hari);
    if (activeBtn) {
        activeBtn.classList.remove('bg-white', 'text-slate-600', 'border', 'border-slate-200');
        activeBtn.classList.add('bg-[#1a365d]', 'text-white', 'shadow-xs');

        var activeCountBadge = activeBtn.querySelector('.tab-count-badge');
        if (activeCountBadge) {
            activeCountBadge.classList.remove('bg-slate-100', 'text-slate-500');
            activeCountBadge.classList.add('bg-white/20', 'text-white');
        }
    }
}
</script>
