<?php
/**
 * View Beranda: Pengelola Program Studi (Level 5 — Ketua Program Studi / Kaprodi)
 * Dasbor Otoritas Akademik Prodi SIATEK
 * Fokus: Penetapan Surat Penunjukkan (TTD), Monitoring Skripsi/KP Prodi, & Pemerataan Beban Bimbingan Dosen.
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

// Ambil Profil Program Studi
$prodiInfo = ['kode' => $userProdi, 'nama' => 'Program Studi', 'jenjang' => 'S1'];
$qPrInfo = query("SELECT kode, nama, jenjang FROM prodi WHERE kode = '" . bersihkan($userProdi) . "' LIMIT 1");
if ($qPrInfo && ($r = mysqli_fetch_assoc($qPrInfo))) {
    $prodiInfo = $r;
}
$namaProdiUser = $prodiInfo['nama'];
$cleanProdi = bersihkan($userProdi);

// 2. Metrik Kunci Otoritas Kaprodi
// A. Surat Penunjukkan Belum TTD (Tugas Pokok Kaprodi)
$qSPPending = query("
    SELECT COUNT(*) AS c 
    FROM surat_penunjukkan sp
    JOIN mahasiswa m ON sp.nim = m.nim
    WHERE (sp.ttd = '' OR sp.ttd IS NULL) AND m.prodi = '$cleanProdi'
");
$spPendingTotal = $qSPPending ? (int)mysqli_fetch_assoc($qSPPending)['c'] : 0;

// B. Mahasiswa Aktif Prodi
$qMhsAktif = query("SELECT COUNT(*) AS c FROM mahasiswa WHERE prodi = '$cleanProdi' AND status = 'Aktif'");
$mhsAktifProdi = $qMhsAktif ? (int)mysqli_fetch_assoc($qMhsAktif)['c'] : 0;

// C. Skripsi Berjalan di Prodi
$qSkripsiAktif = query("
    SELECT COUNT(*) AS c 
    FROM skripsi s
    JOIN mahasiswa m ON s.nim = m.nim
    WHERE s.status = 'aktif' AND m.prodi = '$cleanProdi'
");
$skripsiProdiAktif = $qSkripsiAktif ? (int)mysqli_fetch_assoc($qSkripsiAktif)['c'] : 0;

// D. Kerja Praktek Berjalan di Prodi
$qKpAktif = query("
    SELECT COUNT(*) AS c 
    FROM kp k
    JOIN mahasiswa m ON k.nim = m.nim
    WHERE (k.status = 'Aktif' OR k.status = 'aktif') AND m.prodi = '$cleanProdi'
");
$kpProdiAktif = $qKpAktif ? (int)mysqli_fetch_assoc($qKpAktif)['c'] : 0;

// 3. Antrean Surat Penunjukkan Siap TTD / Penetapan
$daftarSPPending = [];
$qListSP = query("
    SELECT sp.id, sp.nim, sp.keterangan, sp.tanggal, sp.pembimbing1, sp.pembimbing2,
           m.nama AS nama_mhs, m.angkatan
    FROM surat_penunjukkan sp
    JOIN mahasiswa m ON sp.nim = m.nim
    WHERE (sp.ttd = '' OR sp.ttd IS NULL) AND m.prodi = '$cleanProdi'
    ORDER BY sp.id DESC LIMIT 6
");
if ($qListSP) {
    while ($sp = mysqli_fetch_assoc($qListSP)) {
        $daftarSPPending[] = $sp;
    }
}

// 4. Mahasiswa Siap Ujian di Prodi (Antrean Sidang/Seminar)
$antreanUjianList = function_exists('ambil_antrian_siap_ujian') ? ambil_antrian_siap_ujian(0, $cleanProdi) : [];
$totalAntreanUjian = count($antreanUjianList);

// 5. Beban Bimbingan Dosen di Lingkungan Prodi (Pemerataan Pembimbingan)
$bebanDosenList = [];
$qBebanDosen = query("
    SELECT d.id, d.nip, d.nama,
           (
               SELECT COUNT(*) FROM skripsi s 
               JOIN mahasiswa m ON s.nim = m.nim 
               WHERE (s.pembimbing1 = d.id OR s.pembimbing1 = d.nip OR s.pembimbing2 = d.id OR s.pembimbing2 = d.nip)
                 AND s.status = 'aktif' AND m.prodi = '$cleanProdi'
           ) AS bimbingan_skripsi,
           (
               SELECT COUNT(*) FROM kp k 
               JOIN mahasiswa m ON k.nim = m.nim 
               WHERE (k.dosen = d.id OR k.dosen = d.nip)
                 AND (k.status = 'Aktif' OR k.status = 'aktif') AND m.prodi = '$cleanProdi'
           ) AS bimbingan_kp
    FROM dosen d
    WHERE d.status = 'aktif'
    ORDER BY (bimbingan_skripsi + bimbingan_kp) DESC LIMIT 5
");
if ($qBebanDosen) {
    while ($bd = mysqli_fetch_assoc($qBebanDosen)) {
        $bd['total_bimbingan'] = (int)$bd['bimbingan_skripsi'] + (int)$bd['bimbingan_kp'];
        $bebanDosenList[] = $bd;
    }
}

// 6. Berita & Informasi Jurusan
$beritaItems = [];
$qBerita = query("SELECT judul, deskripsi, tanggal FROM berita WHERE judul <> '' ORDER BY tanggal DESC LIMIT 3");
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
        <a href="/redesain-siatek/administrator/mhs-penunjukkan"
           class="inline-flex items-center gap-2 rounded-lg bg-[#f97316] hover:bg-[#ea580c] px-3.5 py-2 text-xs font-semibold text-white shadow-sm shadow-orange-500/25 transition">
            <i class="fas fa-file-signature"></i> Surat Penunjukkan
            <?php if ($spPendingTotal > 0): ?>
                <span class="rounded-full bg-white/20 px-1.5 py-0.2 text-[10px] font-bold"><?= $spPendingTotal ?></span>
            <?php endif; ?>
        </a>
        <a href="/redesain-siatek/administrator/mhs-skripsi"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
            <i class="fas fa-book-open text-[#1a365d]"></i> Monitoring Skripsi
        </a>
        <a href="/redesain-siatek/administrator/mhs-ujian"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
            <i class="fas fa-graduation-cap text-orange-500"></i> Kelayakan Ujian (<?= $totalAntreanUjian ?>)
        </a>
    </div>
</section>

<!-- ============ 4 KARTU METRIK UTAMA KAPRODI ============ -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- 1. KARTU JANGKAR NAVY: Surat Penunjukkan Menunggu TTD Kaprodi -->
    <a href="/redesain-siatek/administrator/mhs-penunjukkan"
       class="rounded-xl bg-[#1a365d] text-white p-5 shadow-md shadow-slate-900/10 flex flex-col justify-between relative overflow-hidden group hover:brightness-105 transition">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-white/5 pointer-events-none"></div>
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-widest text-orange-300 flex items-center gap-1.5">
                    <?php if ($spPendingTotal > 0): ?>
                        <span class="h-1.5 w-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                    <?php endif; ?>
                    Surat Penunjukkan
                </span>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 text-orange-300 text-xs">
                    <i class="fas fa-file-signature"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl sm:text-4xl font-bold font-mono tracking-tight text-white"><?= f($spPendingTotal) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between text-xs">
            <span class="text-slate-300">Menunggu TTD Kaprodi</span>
            <span class="font-semibold text-orange-300 group-hover:underline">Buka Berkas &rarr;</span>
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
            <span>Terdaftar di <?= htmlspecialchars($userProdi) ?></span>
            <span class="font-semibold text-slate-700 group-hover:underline">Kelola &rarr;</span>
        </div>
    </a>

    <!-- 3. Skripsi Berjalan Prodi -->
    <a href="/redesain-siatek/administrator/mhs-skripsi"
       class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-orange-300 transition block group flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Skripsi Berjalan</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 text-orange-600 text-xs group-hover:bg-[#f97316] group-hover:text-white transition">
                    <i class="fas fa-book-open"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($skripsiProdiAktif) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Bimbingan skripsi aktif</span>
            <span class="font-semibold text-orange-600 group-hover:underline">Pantau &rarr;</span>
        </div>
    </a>

    <!-- 4. KP Berjalan Prodi -->
    <a href="/redesain-siatek/administrator/mhs-kp"
       class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-orange-300 transition block group flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Kerja Praktek</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs group-hover:bg-[#1a365d] group-hover:text-white transition">
                    <i class="fas fa-briefcase"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold font-mono tracking-tight text-slate-900"><?= f($kpProdiAktif) ?></p>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Mahasiswa KP berjalan</span>
            <span class="font-semibold text-slate-700 group-hover:underline">Pantau &rarr;</span>
        </div>
    </a>
</section>

<!-- ============ BENTO WORKSPACE 8 : 4 ============ -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

    <!-- ===== KOLOM KIRI (8 KOLOM / 66%): WORKSPACE KEPUTUSAN KAPRODI ===== -->
    <div class="lg:col-span-8 space-y-5">

        <!-- 1. Antrean Penetapan & Tanda Tangan Surat Penunjukkan -->
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-3.5 bg-slate-50/70">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 text-orange-600 text-xs">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">
                            Antrean Surat Penunjukkan Menunggu TTD Kaprodi
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Penetapan resmi dosen pembimbing &amp; dewan penguji prodi</p>
                    </div>
                </div>
                <a href="/redesain-siatek/administrator/mhs-penunjukkan" class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition">
                    Kelola Semua &rarr;
                </a>
            </div>

            <?php if (count($daftarSPPending) === 0): ?>
                <div class="px-5 py-10 text-center">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 mb-2">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-700">Semua surat penunjukkan prodi telah ditandatangani.</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Tidak ada draf penetapan yang menunggu persetujuan Kaprodi saat ini.</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($daftarSPPending as $sp):
                        $tglFmt = !empty($sp['tanggal']) ? date('d M Y', strtotime($sp['tanggal'])) : '-';
                        $ket = !empty($sp['keterangan']) ? $sp['keterangan'] : 'Surat Penunjukkan Pembimbing/Penguji';
                    ?>
                    <div class="p-3.5 px-5 hover:bg-slate-50/80 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 text-orange-600 shrink-0 text-xs mt-0.5">
                                <i class="fas fa-file-contract"></i>
                            </span>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($sp['nama_mhs']) ?></h4>
                                    <span class="font-mono text-xs text-slate-400">(<?= htmlspecialchars($sp['nim']) ?>)</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-0.5 truncate"><?= htmlspecialchars($ket) ?></p>
                                <span class="text-[11px] text-slate-400 mt-0.5 inline-block">
                                    <i class="far fa-calendar-alt mr-1"></i> Diajukan: <?= htmlspecialchars($tglFmt) ?>
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 justify-end">
                            <a href="/redesain-siatek/administrator/mhs-penunjukkan"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#1a365d] hover:bg-[#234670] text-white text-xs font-semibold shadow-2xs transition">
                                <i class="fas fa-signature text-[11px]"></i> Tinjau &amp; TTD
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- 2. Antrean Mahasiswa Siap Ujian di Prodi (Kelayakan Sidang & Seminar) -->
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-3.5 bg-slate-50/70">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">
                            Antrean Mahasiswa Siap Ujian (<?= htmlspecialchars($userProdi) ?>)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Mahasiswa telah diverifikasi &amp; siap dialokasikan ke jadwal sidang/seminar</p>
                    </div>
                </div>
                <a href="/redesain-siatek/administrator/mhs-ujian" class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition">
                    Jadwalkan Ujian &rarr;
                </a>
            </div>

            <?php if ($totalAntreanUjian === 0): ?>
                <div class="px-5 py-8 text-center text-slate-400">
                    <i class="far fa-calendar-check text-2xl mb-1.5 inline-block opacity-40"></i>
                    <p class="text-xs font-medium text-slate-500">Tidak ada antrean mahasiswa siap ujian di prodi ini saat ini.</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-100 max-h-[300px] overflow-y-auto">
                    <?php foreach (array_slice($antreanUjianList, 0, 5) as $au): ?>
                    <div class="p-3.5 px-5 hover:bg-slate-50/80 transition flex items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="<?= $au['badge_class'] ?? 'bg-orange-100 text-orange-800' ?> px-2 py-0.5 rounded text-[10px] font-bold">
                                    <?= htmlspecialchars($au['tahapan_label'] ?? 'Ujian') ?>
                                </span>
                                <h4 class="text-xs font-semibold text-slate-900"><?= htmlspecialchars($au['nama_mhs']) ?></h4>
                                <span class="font-mono text-xs text-slate-400">(<?= htmlspecialchars($au['nim']) ?>)</span>
                            </div>
                            <?php if (!empty($au['penguji'])): ?>
                                <p class="text-[11px] text-slate-500 mt-1 truncate">
                                    <i class="fas fa-user-check mr-1 text-slate-400"></i>
                                    Penguji: <?= htmlspecialchars(implode(', ', array_slice($au['penguji'], 0, 2))) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <a href="/redesain-siatek/administrator/mhs-ujian" class="text-xs font-semibold text-orange-600 hover:text-orange-700 shrink-0">
                            Proses &rarr;
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </div>

    <!-- ===== KOLOM KANAN (4 KOLOM / 34%): BEBAN DOSEN & PINTASAN KAPRODI ===== -->
    <div class="lg:col-span-4 space-y-5">

        <!-- 1. Pemantauan Beban Bimbingan Dosen Prodi -->
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-800">Beban Bimbingan Dosen Prodi</h3>
                <p class="text-xs text-slate-500 mt-0.5">Monitoring kuota &amp; pemerataan mahasiswa bimbingan</p>
            </div>

            <?php if (count($bebanDosenList) === 0): ?>
                <p class="text-xs text-slate-400 py-4 text-center">Belum ada data bimbingan dosen di prodi ini.</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($bebanDosenList as $bd): ?>
                    <div class="p-3 rounded-lg bg-slate-50/80 border border-slate-100 flex items-center justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-semibold text-slate-800 truncate"><?= htmlspecialchars($bd['nama']) ?></h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                <?= $bd['bimbingan_skripsi'] ?> Skripsi &bull; <?= $bd['bimbingan_kp'] ?> KP
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-mono font-bold bg-white border border-slate-200 text-slate-800 shadow-2xs shrink-0">
                            <?= $bd['total_bimbingan'] ?> Mhs
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- 2. Hub Pintasan Otoritas Kaprodi -->
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Pusat Otoritas Kaprodi</h3>
            <div class="grid grid-cols-1 gap-2">
                <a href="/redesain-siatek/administrator/mhs-penunjukkan" class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-orange-200 hover:bg-orange-50/40 transition group">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 text-orange-600 text-xs shrink-0"><i class="fas fa-file-signature"></i></span>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-semibold text-slate-800 group-hover:text-orange-600 transition">Surat Penunjukkan SK</h4>
                        <p class="text-[11px] text-slate-400 truncate">Persetujuan &amp; TTD pembimbing/penguji.</p>
                    </div>
                </a>

                <a href="/redesain-siatek/administrator/mhs-skripsi" class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-orange-200 hover:bg-orange-50/40 transition group">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs shrink-0"><i class="fas fa-book-open"></i></span>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-semibold text-slate-800 group-hover:text-orange-600 transition">Monitoring Skripsi Prodi</h4>
                        <p class="text-[11px] text-slate-400 truncate">Status judul &amp; progres tahapan SOP.</p>
                    </div>
                </a>

                <a href="/redesain-siatek/administrator/bimbingan-skripsi" class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-orange-200 hover:bg-orange-50/40 transition group">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs shrink-0"><i class="fas fa-users"></i></span>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-semibold text-slate-800 group-hover:text-orange-600 transition">Rekap Bimbingan Prodi</h4>
                        <p class="text-[11px] text-slate-400 truncate">Pemerataan kuota bimbingan dosen.</p>
                    </div>
                </a>

                <a href="/redesain-siatek/administrator/mhs-ujian" class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-orange-200 hover:bg-orange-50/40 transition group">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-[#1a365d] text-xs shrink-0"><i class="fas fa-calendar-check"></i></span>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-semibold text-slate-800 group-hover:text-orange-600 transition">Jadwal Ujian Prodi</h4>
                        <p class="text-[11px] text-slate-400 truncate">Kelayakan peserta seminar &amp; sidang.</p>
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
