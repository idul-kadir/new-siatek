<?php
/**
 * Halaman: Skripsi Mahasiswa (Data Dinamis)
 */
require_once __DIR__ . '/../../../fungsi.php';
mysqli_set_charset($GLOBALS['koneksi'], 'utf8mb4');
$nim = $_SESSION['nim'] ?? '';

/* --- Ambil Data Utama Mahasiswa & Skripsi --- */
$dMhs = ['nama' => '', 'prodi' => '', 'angkatan' => '', 'pa' => '', 'nip_pa' => ''];
$dSkr = ['judul' => '', 'pembimbing_1' => '-', 'nip_pemb_1' => '-', 'pembimbing_2' => '-', 'nip_pemb_2' => '-'];
$jadwal = ['proposal' => 'Belum dijadwalkan', 'hasil' => 'Belum dijadwalkan', 'tutup' => 'Belum dijadwalkan'];
$progresSaatIni = 'Menunggu data SOP';

// Ambil Mahasiswa
$qMhs = query("SELECT nama, prodi, angkatan, dosen_pa FROM mahasiswa WHERE nim = '$nim' LIMIT 1");
if ($qMhs && mysqli_num_rows($qMhs) > 0) {
    $row = mysqli_fetch_assoc($qMhs);
    $dMhs['nama']     = $row['nama'];
    $dMhs['angkatan'] = $row['angkatan'];
    
    // Ambil nama prodi lengkap dari tabel prodi berdasar kode/nama
    $kodeProdi = bersihkan($row['prodi']);
    $qProdi = query("SELECT nama FROM prodi WHERE kode = '$kodeProdi' OR nama = '$kodeProdi' LIMIT 1");
    if ($qProdi && mysqli_num_rows($qProdi) > 0) {
        $dMhs['prodi'] = mysqli_fetch_assoc($qProdi)['nama'];
    } else {
        $dMhs['prodi'] = $row['prodi'];
    }
    
    // Cari nama PA
    if (!empty($row['dosen_pa'])) {
        $nipPa = $row['dosen_pa'];
        $dMhs['nip_pa'] = $nipPa;
        $qPa = query("SELECT nama FROM dosen WHERE nip = '$nipPa' LIMIT 1");
        if ($qPa && mysqli_num_rows($qPa) > 0) {
            $dMhs['pa'] = mysqli_fetch_assoc($qPa)['nama'];
        } else {
            $dMhs['pa'] = $nipPa;
        }
    } else {
        $dMhs['pa'] = '-';
        $dMhs['nip_pa'] = '-';
    }
}

// Ambil Data Skripsi & Pembimbing
// Cek status pendaftaran di tabel skripsi
$statusSkripsiDb = null;
$qSkr = query("SELECT judul, penunjukkan_pembimbing, status FROM skripsi WHERE nim = '$nim' LIMIT 1");
if ($qSkr && mysqli_num_rows($qSkr) > 0) {
    $row = mysqli_fetch_assoc($qSkr);
    $statusSkripsiDb = strtolower(trim((string)$row['status']));
    $dSkr['judul'] = $row['judul'] ?: 'Belum mengajukan judul skripsi';
    
    // Ambil Pembimbing dari tabel surat_penunjukkan (untuk skripsi)
    if (!empty($row['penunjukkan_pembimbing'])) {
        $noSurat = $row['penunjukkan_pembimbing'];
        // Relasikan dengan nim, atau id, atau no_surat
        $qSurat = query("SELECT pembimbing1, pembimbing2 FROM surat_penunjukkan WHERE (id = '$noSurat' OR no_surat = '$noSurat' OR nim = '$nim') AND keterangan LIKE '%PEMBIMBING%' LIMIT 1");
        if ($qSurat && mysqli_num_rows($qSurat) > 0) {
            $surat = mysqli_fetch_assoc($qSurat);
            
            // Pembimbing 1
            if (!empty($surat['pembimbing1'])) {
                $dSkr['nip_pemb_1'] = $surat['pembimbing1'];
                $qP1 = query("SELECT nama FROM dosen WHERE nip = '{$surat['pembimbing1']}' OR id = '{$surat['pembimbing1']}' LIMIT 1");
                $dSkr['pembimbing_1'] = ($qP1 && mysqli_num_rows($qP1) > 0) ? mysqli_fetch_assoc($qP1)['nama'] : $surat['pembimbing1'];
            } else {
                $dSkr['pembimbing_1'] = 'Belum Ditentukan';
                $dSkr['nip_pemb_1']   = '-';
            }
            // Pembimbing 2
            if (!empty($surat['pembimbing2'])) {
                $dSkr['nip_pemb_2'] = $surat['pembimbing2'];
                $qP2 = query("SELECT nama FROM dosen WHERE nip = '{$surat['pembimbing2']}' OR id = '{$surat['pembimbing2']}' LIMIT 1");
                $dSkr['pembimbing_2'] = ($qP2 && mysqli_num_rows($qP2) > 0) ? mysqli_fetch_assoc($qP2)['nama'] : $surat['pembimbing2'];
            } else {
                $dSkr['pembimbing_2'] = 'Belum Ditentukan';
                $dSkr['nip_pemb_2']   = '-';
            }
        } else {
            $dSkr['pembimbing_1'] = 'Belum Ditentukan';
            $dSkr['nip_pemb_1']   = '-';
            $dSkr['pembimbing_2'] = 'Belum Ditentukan';
            $dSkr['nip_pemb_2']   = '-';
        }
    } else {
        $dSkr['pembimbing_1'] = 'Belum Ditentukan';
        $dSkr['nip_pemb_1']   = '-';
        $dSkr['pembimbing_2'] = 'Belum Ditentukan';
        $dSkr['nip_pemb_2']   = '-';
    }
} else {
    $dSkr['judul'] = 'Belum mengajukan judul skripsi';
}

// Ambil Jadwal Proposal, Hasil, Tutup (kolom tgl_ujian ada di hasil & tutup, proposal cek bila ada)
$tahapan = ['hasil' => 'tgl_ujian', 'tutup' => 'tgl_ujian'];
foreach ($tahapan as $tabel => $kolomTgl) {
    $qJadwal = query("SELECT $kolomTgl FROM $tabel WHERE nim = '$nim' LIMIT 1");
    if ($qJadwal && mysqli_num_rows($qJadwal) > 0) {
        $j = mysqli_fetch_assoc($qJadwal);
        if (!empty($j[$kolomTgl]) && $j[$kolomTgl] != '0000-00-00') {
            $jadwal[$tabel] = date('d M Y', strtotime($j[$kolomTgl]));
        }
    }
}

// Ambil Status Proses SOP dari tabel progres_skripsi via helper
$currentProgres = get_progres_skripsi($nim);
$lastProsesId = ($currentProgres !== '-') ? (int) $currentProgres : 0;

/* --- Susun Array Info Skripsi --- */
$infoSkripsi = [
    'nama'          => $dMhs['nama'] ?: 'Nama Tidak Ditemukan',
    'nim'           => $nim,
    'prodi'         => $dMhs['prodi'] ?: '-',
    'angkatan'      => $dMhs['angkatan'] ?: '-',
    'judul'         => $dSkr['judul'],
    'pembimbing_1'  => $dSkr['pembimbing_1'],
    'nip_pemb_1'    => $dSkr['nip_pemb_1'],
    'pembimbing_2'  => $dSkr['pembimbing_2'],
    'nip_pemb_2'    => $dSkr['nip_pemb_2'],
    'pa'            => $dMhs['pa'],
    'nip_pa'        => $dMhs['nip_pa'],
    'ujian_prop'    => $jadwal['proposal'],
    'ujian_hasil'   => $jadwal['hasil'],
    'ujian_tutup'   => $jadwal['tutup'],
];

/* --- Ambil data alur SOP Skripsi --- */
$steps = [];
$querySop = query("SELECT * FROM sop_skripsi ORDER BY CAST(no AS UNSIGNED) ASC");

if ($querySop && mysqli_num_rows($querySop) > 0) {
    while ($row = mysqli_fetch_assoc($querySop)) {
        $noSop = (int) $row['no'];

        // Logika status step berdasarkan tabel progres_skripsi:
        // Jika no_sop < lastProsesId => selesai
        // Jika no_sop == lastProsesId => aktif
        // Jika no_sop > lastProsesId => menunggu
        // (Jika lastProsesId == 0, maka step 1 aktif)

        if ($lastProsesId == 0) {
            $st = ($noSop == 1) ? 'aktif' : 'menunggu';
        } else {
            if ($noSop < $lastProsesId) {
                $st = 'selesai';
            } elseif ($noSop == $lastProsesId) {
                $st = 'aktif';
            } else {
                $st = 'menunggu';
            }
        }

        if ($st === 'aktif') {
            $progresSaatIni = $row['keterangan'];
        }

        $steps[] = [
            'id'       => $row['no'],
            'urutan'   => $noSop,
            'judul'    => $row['keterangan'],
            'desc'     => 'Eksekutor: ' . ucfirst(strtolower($row['eksekutor'])),
            'executor' => ucfirst(strtolower($row['eksekutor'])),
            'status'   => $st,
        ];
    }
}

$total    = count($steps);
$selesai  = count(array_filter($steps, fn($s) => $s['status'] === 'selesai'));
$progres  = $total > 0 ? round($selesai / $total * 100) : 0;

/* Ambil step aktif saat ini untuk ditampilkan di Lembar Kerja */
$currentStep = null;
foreach ($steps as $s) {
    if ($s['status'] === 'aktif') {
        $currentStep = $s;
        break;
    }
}
if (!$currentStep && !empty($steps)) {
    // Kalau semua selesai, ambil yang terakhir
    $currentStep = $steps[$total - 1];
    $progresSaatIni = 'Semua tahapan selesai';
}

/* Flag Kondisi Status Pendaftaran Skripsi Mahasiswa */
$hasSkripsiRecord  = ($statusSkripsiDb !== null);
$isSkripsiInactive = ($statusSkripsiDb === 'tidak aktif' || $statusSkripsiDb === 'nonaktif');
$hasProgres        = ($currentProgres !== '-');

/* --- Ambil Persyaratan Berkas sesuai progres saat ini --- */
$berkasDibutuhkan = [];
$berkasSudahDunggah = [];
$nim_clean = bersihkan($nim);
if ($hasSkripsiRecord && !$isSkripsiInactive && $lastProsesId > 0) {
    $qBerkas = query("SELECT id, nama_berkas, tabel, field FROM persyaratan_berkas_skripsi WHERE progres = '$lastProsesId' ORDER BY id ASC");
    if ($qBerkas && mysqli_num_rows($qBerkas) > 0) {
        while ($b = mysqli_fetch_assoc($qBerkas)) {
            $berkasDibutuhkan[] = $b;
        }
    }
    
    // Cek berkas yang sudah ada di temp_skripsi untuk progres ini
    $qSudah = query("SELECT id_persyaratan FROM temp_skripsi WHERE nim = '$nim_clean' AND id_persyaratan IN (SELECT id FROM persyaratan_berkas_skripsi WHERE progres = '$lastProsesId')");
    if ($qSudah && mysqli_num_rows($qSudah) > 0) {
        while ($t = mysqli_fetch_assoc($qSudah)) {
            $berkasSudahDunggah[] = (int) $t['id_persyaratan'];
        }
    }
}
?>
<style>
    .content-scroll { overflow-y: auto; height: calc(100vh - 64px); }

    /* Custom Scrollbar kecil untuk panel SOP */
    .sop-scroll { max-height: 500px; overflow-y: auto; padding-right: 8px; }
    .sop-scroll::-webkit-scrollbar { width: 4px; }
    .sop-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .sop-scroll::-webkit-scrollbar-track { background: transparent; }

    .step-node { position: relative; display: flex; align-items: flex-start; gap: .75rem; }
    .step-line { position: absolute; left: 15px; top: 32px; bottom: -12px; width: 2px; background: #e2e8f0; }
    .step-line.done { background: #22c55e; }

    .step-dot {
        width: 32px; height: 32px; border-radius: 9999px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: .7rem; font-weight: 700; z-index: 10; position: relative;
        border: 2px solid transparent;
    }
    .step-dot.done  { background: #dcfce7; color: #16a34a; border-color: #22c55e; }
    .step-dot.aktif { background: #fff7ed; color: #ea580c; border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.15); }
    .step-dot.wait  { background: #f8fafc; color: #94a3b8; border-color: #e2e8f0; }

    .step-item-card {
        flex: 1; border-radius: .5rem; border: 1px solid #e2e8f0; background: #fff;
        padding: .65rem .85rem; margin-bottom: .85rem; transition: all .15s ease;
    }
    .step-item-card.aktif { border-color: #f97316; background: #fffaf5; }
    .step-item-card.done  { border-color: #e2e8f0; background: #fafafa; }
    .step-item-card.wait  { opacity: .7; }

    .progres-bar-track { height: 7px; border-radius: 999px; background: #e2e8f0; overflow: hidden; }
    .progres-bar-fill  { height: 100%; border-radius: 999px; background: linear-gradient(90deg, #1a365d, #f97316); transition: width .5s ease; }

    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .45s cubic-bezier(.16,1,.3,1) forwards; }

    .spinner-slate { width: 1rem; height: 1rem; border: 2px solid rgba(15,23,42,.2); border-top-color: #1a365d; border-radius: 50%; animation: spin .7s linear infinite; display: inline-block; vertical-align: middle; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .is-loading { opacity: .7; cursor: not-allowed !important; pointer-events: none; }

    #skripsiToast {
        position: fixed; right: 20px; top: 20px; z-index: 2000;
        display: none; align-items: center; gap: .6rem; max-width: 360px;
        border-radius: .75rem; padding: .7rem 1rem; font-size: .82rem;
        box-shadow: 0 12px 24px rgba(15,23,42,.18); color: #fff;
    }
    #skripsiToast.show { display: flex; animation: riseIn .3s ease; }
    #skripsiToast.success { background: linear-gradient(135deg,#16a34a,#15803d); }
    #skripsiToast.error   { background: linear-gradient(135deg,#dc2626,#b91c1c); }
    #skripsiToast.info    { background: linear-gradient(135deg,#1a365d,#234670); }
</style>

<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-book"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Skripsi Mahasiswa</h1>
                    <p class="text-xs text-slate-500">Lembar Kerja Aktivitas Skripsi Sesuai Prosedur SOP</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">
                <i class="fas fa-list-check"></i><?= $selesai ?>/<?= $total ?> SOP Selesai
            </span>
        </div>
    </section>

    <!-- ===== Panel Informasi Skripsi ===== -->
    <section class="mb-6 reveal">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col xl:flex-row gap-6">
            
            <!-- Kiri: Identitas, Judul & Jadwal Ujian -->
            <div class="flex-1 space-y-5 border-b xl:border-b-0 xl:border-r border-slate-100 pb-5 xl:pb-0 xl:pr-6">
                <div>
                    <!-- Badge Identitas Mahasiswa -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-900 text-white px-3 py-1 text-xs font-bold shadow-sm">
                            <i class="fas fa-user-graduate text-orange-400"></i><?= htmlspecialchars($infoSkripsi['nama']) ?>
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 text-orange-700 px-3 py-1 text-xs font-bold">
                            <i class="fas fa-id-card"></i><?= htmlspecialchars($infoSkripsi['nim']) ?>
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-100 text-sky-700 px-3 py-1 text-xs font-bold">
                            <i class="fas fa-graduation-cap"></i><?= htmlspecialchars($infoSkripsi['prodi']) ?> (<?= htmlspecialchars($infoSkripsi['angkatan']) ?>)
                        </span>
                    </div>

                    <!-- Judul Skripsi -->
                    <div class="space-y-1">
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Judul Skripsi / Tugas Akhir</p>
                        <h2 class="text-lg font-extrabold text-slate-800 leading-snug">
                            <?= htmlspecialchars($infoSkripsi['judul']) ?>
                        </h2>
                    </div>
                </div>

                <!-- Jadwal Ujian Sejajar -->
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Jadwal Ujian Skripsi</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100">
                            <i class="fas fa-calendar-check text-orange-500"></i>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Proposal</p>
                                <p class="text-[11px] font-bold text-slate-700"><?= htmlspecialchars($infoSkripsi['ujian_prop']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100">
                            <i class="fas fa-calendar-check text-sky-500"></i>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Hasil</p>
                                <p class="text-[11px] font-bold text-slate-700"><?= htmlspecialchars($infoSkripsi['ujian_hasil']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100">
                            <i class="fas fa-calendar-check text-emerald-500"></i>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Tutup</p>
                                <p class="text-[11px] font-bold text-slate-700"><?= htmlspecialchars($infoSkripsi['ujian_tutup']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan: Dosen (PA, Pembimbing 1, Pembimbing 2 disusun 3 ke bawah) -->
            <div class="w-full xl:w-72 flex-shrink-0 space-y-4">
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 flex-shrink-0 text-[11px] font-bold">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Penasihat Akademik</p>
                            <p class="text-[11px] font-bold text-slate-800"><?= htmlspecialchars($infoSkripsi['pa']) ?></p>
                            <p class="text-[10px] text-slate-400">NIP. <?= htmlspecialchars($infoSkripsi['nip_pa']) ?></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 text-orange-600 flex-shrink-0 text-[11px] font-bold">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Pembimbing 1</p>
                            <p class="text-[11px] font-bold text-slate-800"><?= htmlspecialchars($infoSkripsi['pembimbing_1']) ?></p>
                            <p class="text-[10px] text-slate-400">NIP. <?= htmlspecialchars($infoSkripsi['nip_pemb_1']) ?></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-600 flex-shrink-0 text-[11px] font-bold">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Pembimbing 2</p>
                            <p class="text-[11px] font-bold text-slate-800"><?= htmlspecialchars($infoSkripsi['pembimbing_2']) ?></p>
                            <p class="text-[10px] text-slate-400">NIP. <?= htmlspecialchars($infoSkripsi['nip_pemb_2']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ===== Tata Letak 2 Kolom (Besar: Lembar Kerja SOP | Kecil: Progress Line) ===== -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start">

        <?php if (!$hasSkripsiRecord): ?>
        <!-- BELUM TERDAFTAR DI SKRIPSI -->
        <div class="md:col-span-12 reveal">
            <div class="rounded-xl border border-slate-200 bg-white p-8 md:p-10 text-center shadow-sm">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-100 text-3xl text-orange-500 mx-auto mb-4">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Mulai Skripsi/Tugas Akhir</h3>
                <p class="mt-2 text-sm text-slate-500 max-w-lg mx-auto">Anda belum terdaftar dalam aktivitas skripsi. Pastikan Anda telah memenuhi persyaratan utama di bawah ini sebelum mendaftar.</p>

                <!-- Box Persyaratan Checkbox -->
                <div class="mt-6 max-w-xl mx-auto rounded-xl border border-slate-200 bg-slate-50 p-5 text-left space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Persyaratan Wajib Pendaftaran:</h4>
                    
                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" id="chkSks" class="mt-0.5 rounded border-slate-300 text-orange-500 focus:ring-orange-500 w-4 h-4 flex-shrink-0">
                        <span class="text-xs text-slate-700 leading-normal">
                            <strong class="font-bold text-slate-800">SKS dicapai minimal 120 SKS</strong> (dibuktikan dengan dokumen Lampiran Transkrip Nilai).
                        </span>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" id="chkKrs" class="mt-0.5 rounded border-slate-300 text-orange-500 focus:ring-orange-500 w-4 h-4 flex-shrink-0">
                        <span class="text-xs text-slate-700 leading-normal">
                            <strong class="font-bold text-slate-800">Mengontrak Mata Kuliah Skripsi</strong> pada semester berjalan saat ini (dibuktikan dengan Kartu Rencana Studi / KRS).
                        </span>
                    </label>
                </div>
                
                <button type="button" id="btnDaftarSkripsi" disabled class="mt-6 inline-flex items-center justify-center gap-2 rounded-lg bg-slate-300 px-5 py-2.5 text-sm font-bold text-slate-500 cursor-not-allowed transition min-w-[210px] h-[42px]">
                    <i class="fas fa-rocket"></i>Daftar Skripsi Sekarang
                </button>
            </div>
        </div>
        <?php elseif ($isSkripsiInactive): ?>
        <!-- SUDAH TERDAFTAR TAPI STATUS TIDAK AKTIF -->
        <div class="md:col-span-12 reveal">
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-2xl text-amber-600 flex-shrink-0">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-bold text-amber-900">Pendaftaran Skripsi Anda Masih Non-Aktif</h3>
                        <p class="mt-1 text-sm text-amber-800 leading-relaxed">
                            Pendaftaran Anda tercatat dalam sistem namun aktivasi tahapan skripsinya masih menunggu konfirmasi.
                        </p>
                        <p class="mt-3 text-xs text-amber-700 font-medium border-t border-amber-200 pt-3">
                            <i class="fas fa-exclamation-triangle text-amber-500 mr-1"></i>
                            Harap segera menghadap <strong class="text-amber-900 font-bold">Operator Jurusan/Prodi</strong> untuk mengaktifkan pendaftaran skripsi dengan membawa dokumen 
                            <strong class="font-bold text-amber-900">Transkrip Nilai</strong> dan <strong class="font-bold text-amber-900">KRS</strong> semester berjalan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>

        <!-- ===== KOLOM BESAR (md:col-span-7 lg:col-span-8): Lembar Kerja Aktivitas SOP ===== -->
        <div class="md:col-span-7 lg:col-span-8 space-y-5 reveal">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <!-- Header Lembar Kerja -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-bold"><?= $currentStep['urutan'] ?></span>
                            <h3 class="text-base font-bold text-slate-800"><?= htmlspecialchars($currentStep['judul']) ?></h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Eksekutor SOP: <strong class="text-orange-600"><?= htmlspecialchars($currentStep['executor']) ?></strong></p>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">
                        <i class="fas fa-pen"></i>Aktif
                    </span>
                </div>

                <!-- Konten Aktivitas Formulir / Task SOP -->
                <div class="space-y-4">
                    <?php if (strtolower($currentStep['executor']) === 'mahasiswa'): ?>
                    <div class="rounded-lg bg-orange-50/60 border border-orange-100 p-4 text-xs text-slate-700 leading-relaxed">
                        <i class="fas fa-info-circle text-orange-500 mr-1.5"></i>
                        Silakan lengkapi berkas dan persyaratan berikut untuk menyelesaikan tahapan <strong><?= htmlspecialchars($currentStep['judul']) ?></strong>.
                    </div>
                    <?php endif; ?>

                    <?php if (strtolower($currentStep['executor']) !== 'mahasiswa'): ?>
                        <div class="p-8 text-center rounded-lg border border-dashed border-slate-200 bg-slate-50">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-200 text-slate-400 mb-3 text-xl">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <h4 class="text-sm font-bold text-slate-700">Menunggu Tindakan dari <?= htmlspecialchars($currentStep['executor']) ?></h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Anda tidak dapat melakukan tindakan pada tahapan ini. Harap menunggu hingga <?= htmlspecialchars($currentStep['executor']) ?> memproses tahapan ini.</p>
                        </div>
                    <?php else: ?>

                        <?php 
                        // ==============================================================
                        // SWITCH CASE TAHAPAN / STEP MAHASISWA
                        // ==============================================================
                        $nomorStepMhs = (int)$currentStep['urutan'];
                        switch ($nomorStepMhs):

                            case 1: 
                                // Step 1: Mengunggah Berkas Persyaratan Proposal
                            ?>
                                <form id="formSopKerja" method="post" enctype="multipart/form-data" class="space-y-4">
                                    <?php if (!empty($berkasDibutuhkan)): ?>
                                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-4">
                                            <?php foreach ($berkasDibutuhkan as $b): 
                                                $sudahAda = in_array((int)$b['id'], $berkasSudahDunggah);
                                                $urlFileLama = '';
                                                $statusBerkas = 'menunggu';
                                                $ketStatus = '';
                                                
                                                if ($sudahAda) {
                                                    $qLama = query("SELECT file, status, ket FROM temp_skripsi WHERE nim = '$nim_clean' AND id_persyaratan = '{$b['id']}' LIMIT 1");
                                                    if ($qLama && mysqli_num_rows($qLama) > 0) {
                                                        $dTemp = mysqli_fetch_assoc($qLama);
                                                        $urlFileLama = url_file($dTemp['file']);
                                                        $statusBerkas = strtolower(trim((string)$dTemp['status']));
                                                        $ketStatus = trim((string)$dTemp['ket']);
                                                    }
                                                }
                                            ?>
                                                <div>
                                                    <div class="flex items-center justify-between mb-1">
                                                        <label class="block text-xs font-bold text-slate-700">
                                                            <?= htmlspecialchars($b['nama_berkas']) ?> (PDF) <span class="text-rose-500">*</span>
                                                        </label>
                                                        <?php if ($sudahAda): ?>
                                                        <div class="flex items-center gap-3">
                                                            <?php if ($urlFileLama): ?>
                                                            <a href="<?= $urlFileLama ?>" target="_blank" class="text-[10px] font-bold text-orange-600 hover:underline"><i class="fas fa-eye mr-1"></i>Lihat File</a>
                                                            <?php endif; ?>
                                                            
                                                            <?php if ($statusBerkas === 'diterima'): ?>
                                                                <span class="status-berkas-badge text-[10px] font-bold text-emerald-600" data-id="<?= $b['id'] ?>"><i class="fas fa-check-circle mr-1"></i>Diterima</span>
                                                            <?php elseif ($statusBerkas === 'ditolak'): ?>
                                                                <span class="status-berkas-badge text-[10px] font-bold text-rose-600" data-id="<?= $b['id'] ?>"><i class="fas fa-times-circle mr-1"></i>Ditolak</span>
                                                            <?php else: ?>
                                                                <span class="status-berkas-badge text-[10px] font-bold text-amber-600" data-id="<?= $b['id'] ?>"><i class="fas fa-clock mr-1"></i>Menunggu Verifikasi</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php else: ?>
                                                        <span class="status-berkas-badge text-[10px] font-semibold text-slate-400" data-id="<?= $b['id'] ?>">Belum diunggah</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <input 
                                                        type="file" 
                                                        accept=".pdf" 
                                                        <?= $statusBerkas === 'diterima' ? 'disabled' : '' ?>
                                                        class="auto-upload-input w-full text-xs text-slate-500 border border-slate-200 rounded-lg p-2 bg-white focus:bg-white focus:outline-none transition <?= $statusBerkas === 'diterima' ? 'bg-emerald-50 border-emerald-200 cursor-not-allowed opacity-75' : ($statusBerkas === 'ditolak' ? 'bg-rose-50 border-rose-200' : ($sudahAda ? 'bg-amber-50 border-amber-200' : '')) ?>" 
                                                        data-id="<?= $b['id'] ?>"
                                                        data-field="<?= htmlspecialchars($b['field']) ?>"
                                                    >
                                                    
                                                    <?php if ($statusBerkas === 'ditolak' && $ketStatus !== ''): ?>
                                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">
                                                        <i class="fas fa-exclamation-circle mr-1"></i>Alasan penolakan: <strong><?= htmlspecialchars($ketStatus) ?></strong>. Silakan unggah ulang.
                                                    </p>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </form>
                            <?php 
                                break;

                            case 4: 
                                // Step 4: Mengunggah Laporan Proposal dan Lembar Bimbingan
                            ?>
                                <form id="formSopKerja" method="post" enctype="multipart/form-data" class="space-y-4">
                                    <div class="rounded-lg bg-sky-50 border border-sky-100 p-3 text-xs text-sky-800">
                                        <i class="fas fa-info-circle mr-1 text-sky-600"></i>Unggah naskah laporan proposal dan lembar bimbingan yang telah disetujui untuk melanjutkan ke tahap penelitian.
                                    </div>
                                    
                                    <?php if (!empty($berkasDibutuhkan)): ?>
                                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-4">
                                            <?php foreach ($berkasDibutuhkan as $b): 
                                                $sudahAda = in_array((int)$b['id'], $berkasSudahDunggah);
                                                $urlFileLama = '';
                                                $statusBerkas = 'menunggu';
                                                $ketStatus = '';
                                                
                                                if ($sudahAda) {
                                                    $qLama = query("SELECT file, status, ket FROM temp_skripsi WHERE nim = '$nim_clean' AND id_persyaratan = '{$b['id']}' LIMIT 1");
                                                    if ($qLama && mysqli_num_rows($qLama) > 0) {
                                                        $dTemp = mysqli_fetch_assoc($qLama);
                                                        $urlFileLama = url_file($dTemp['file']);
                                                        $statusBerkas = strtolower(trim((string)$dTemp['status']));
                                                        $ketStatus = trim((string)$dTemp['ket']);
                                                    }
                                                }
                                            ?>
                                                <div>
                                                    <div class="flex items-center justify-between mb-1">
                                                        <label class="block text-xs font-bold text-slate-700">
                                                            <?= htmlspecialchars($b['nama_berkas']) ?> (PDF) <span class="text-rose-500">*</span>
                                                        </label>
                                                        <?php if ($sudahAda): ?>
                                                        <div class="flex items-center gap-3">
                                                            <?php if ($urlFileLama): ?>
                                                            <a href="<?= $urlFileLama ?>" target="_blank" class="text-[10px] font-bold text-orange-600 hover:underline"><i class="fas fa-eye mr-1"></i>Lihat File</a>
                                                            <?php endif; ?>
                                                            
                                                            <?php if ($statusBerkas === 'diterima'): ?>
                                                                <span class="status-berkas-badge text-[10px] font-bold text-emerald-600" data-id="<?= $b['id'] ?>"><i class="fas fa-check-circle mr-1"></i>Diterima</span>
                                                            <?php elseif ($statusBerkas === 'ditolak'): ?>
                                                                <span class="status-berkas-badge text-[10px] font-bold text-rose-600" data-id="<?= $b['id'] ?>"><i class="fas fa-times-circle mr-1"></i>Ditolak</span>
                                                            <?php else: ?>
                                                                <span class="status-berkas-badge text-[10px] font-bold text-amber-600" data-id="<?= $b['id'] ?>"><i class="fas fa-clock mr-1"></i>Menunggu Verifikasi</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php else: ?>
                                                        <span class="status-berkas-badge text-[10px] font-semibold text-slate-400" data-id="<?= $b['id'] ?>">Belum diunggah</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <input 
                                                        type="file" 
                                                        accept=".pdf" 
                                                        <?= $statusBerkas === 'diterima' ? 'disabled' : '' ?>
                                                        class="auto-upload-input w-full text-xs text-slate-500 border border-slate-200 rounded-lg p-2 bg-white focus:bg-white focus:outline-none transition <?= $statusBerkas === 'diterima' ? 'bg-emerald-50 border-emerald-200 cursor-not-allowed opacity-75' : ($statusBerkas === 'ditolak' ? 'bg-rose-50 border-rose-200' : ($sudahAda ? 'bg-amber-50 border-amber-200' : '')) ?>" 
                                                        data-id="<?= $b['id'] ?>"
                                                        data-field="<?= htmlspecialchars($b['field']) ?>"
                                                    >
                                                    
                                                    <?php if ($statusBerkas === 'ditolak' && $ketStatus !== ''): ?>
                                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">
                                                        <i class="fas fa-exclamation-circle mr-1"></i>Alasan penolakan: <strong><?= htmlspecialchars($ketStatus) ?></strong>. Silakan unggah ulang.
                                                    </p>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </form>
                            <?php 
                                break;

                            case 6: 
                                // Step 6: Tahap Penelitian
                            ?>
                                <div class="space-y-4">
                                    <div class="rounded-xl border border-sky-100 bg-sky-50/70 p-5 space-y-3">
                                        <div class="flex items-start gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-500 text-white flex-shrink-0 text-base shadow-sm">
                                                <i class="fas fa-microscope"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-800">Sedang dalam Proses Penelitian</h4>
                                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                                    Saat ini Anda berada pada tahapan penelitian skripsi. Lakukan pengumpulan data, analisis, dan penelitian sesuai metodologi yang telah ditentukan oleh pembimbing.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="border-t border-sky-100 pt-3">
                                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                                <i class="fas fa-info-circle text-sky-500 mr-1"></i>Untuk komponen pendukung seperti <strong>Surat Izin Penelitian</strong>, surat pengantar instansi/lapangan, dan administrasi lainnya, diharapkan untuk berkonsultasi langsung dengan <strong>Operator Jurusan / Prodi</strong>.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Konfirmasi Kesiapan Ujian Hasil -->
                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-3">
                                        <label class="flex items-start gap-3 cursor-pointer select-none">
                                            <input type="checkbox" id="chkSiapHasil" class="mt-0.5 rounded border-slate-300 text-orange-500 focus:ring-orange-500 w-4 h-4 flex-shrink-0">
                                            <span class="text-xs text-slate-700 leading-normal">
                                                Saya menyatakan bahwa proses penelitian telah selesai, draf laporan hasil telah disusun, dan saya <strong class="text-slate-800 font-bold">benar-benar siap untuk melaksanakan Ujian Hasil Penelitian</strong>.
                                            </span>
                                        </label>

                                        <div class="pt-2 border-t border-slate-200 flex justify-end">
                                            <button 
                                                type="button" 
                                                id="btnSelesaiPenelitian" 
                                                disabled 
                                                data-step="6"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-300 px-5 py-2.5 text-xs font-bold text-slate-500 cursor-not-allowed transition min-w-[170px]"
                                            >
                                                <i class="fas fa-check"></i>Selesai Penelitian
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                break;

                            case 7: 
                                // Step 7: Mengupload berkas pendaftaran Ujian Hasil Penelitian
                            ?>
                                <form id="formSopKerja" method="post" enctype="multipart/form-data" class="space-y-4">
                                    <div class="rounded-lg bg-sky-50 border border-sky-100 p-3 text-xs text-sky-800">
                                        <i class="fas fa-info-circle mr-1 text-sky-600"></i>Silakan lengkapi berkas pendaftaran Ujian Hasil Penelitian berikut. File yang diunggah akan diverifikasi oleh Operator Jurusan/Prodi.
                                    </div>

                                    <?php if (!empty($berkasDibutuhkan)): ?>
                                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-4">
                                            <?php foreach ($berkasDibutuhkan as $b): 
                                                $sudahAda = in_array((int)$b['id'], $berkasSudahDunggah);
                                                $urlFileLama = '';
                                                $statusBerkas = 'menunggu';
                                                $ketStatus = '';
                                                
                                                if ($sudahAda) {
                                                    $qLama = query("SELECT file, status, ket FROM temp_skripsi WHERE nim = '$nim_clean' AND id_persyaratan = '{$b['id']}' LIMIT 1");
                                                    if ($qLama && mysqli_num_rows($qLama) > 0) {
                                                        $dTemp = mysqli_fetch_assoc($qLama);
                                                        $urlFileLama = url_file($dTemp['file']);
                                                        $statusBerkas = strtolower(trim((string)$dTemp['status']));
                                                        $ketStatus = trim((string)$dTemp['ket']);
                                                    }
                                                }
                                            ?>
                                                <div>
                                                    <div class="flex items-center justify-between mb-1">
                                                        <label class="block text-xs font-bold text-slate-700">
                                                            <?= htmlspecialchars($b['nama_berkas']) ?> (PDF) <span class="text-rose-500">*</span>
                                                        </label>
                                                        <?php if ($sudahAda): ?>
                                                        <div class="flex items-center gap-3">
                                                            <?php if ($urlFileLama): ?>
                                                            <a href="<?= $urlFileLama ?>" target="_blank" class="text-[10px] font-bold text-orange-600 hover:underline"><i class="fas fa-eye mr-1"></i>Lihat File</a>
                                                            <?php endif; ?>
                                                            
                                                            <?php if ($statusBerkas === 'diterima'): ?>
                                                                <span class="status-berkas-badge text-[10px] font-bold text-emerald-600" data-id="<?= $b['id'] ?>"><i class="fas fa-check-circle mr-1"></i>Diterima</span>
                                                            <?php elseif ($statusBerkas === 'ditolak'): ?>
                                                                <span class="status-berkas-badge text-[10px] font-bold text-rose-600" data-id="<?= $b['id'] ?>"><i class="fas fa-times-circle mr-1"></i>Ditolak</span>
                                                            <?php else: ?>
                                                                <span class="status-berkas-badge text-[10px] font-bold text-amber-600" data-id="<?= $b['id'] ?>"><i class="fas fa-clock mr-1"></i>Menunggu Verifikasi</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php else: ?>
                                                        <span class="status-berkas-badge text-[10px] font-semibold text-slate-400" data-id="<?= $b['id'] ?>">Belum diunggah</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <input 
                                                        type="file" 
                                                        accept=".pdf" 
                                                        <?= $statusBerkas === 'diterima' ? 'disabled' : '' ?>
                                                        class="auto-upload-input w-full text-xs text-slate-500 border border-slate-200 rounded-lg p-2 bg-white focus:bg-white focus:outline-none transition <?= $statusBerkas === 'diterima' ? 'bg-emerald-50 border-emerald-200 cursor-not-allowed opacity-75' : ($statusBerkas === 'ditolak' ? 'bg-rose-50 border-rose-200' : ($sudahAda ? 'bg-amber-50 border-amber-200' : '')) ?>" 
                                                        data-id="<?= $b['id'] ?>"
                                                        data-field="<?= htmlspecialchars($b['field']) ?>"
                                                    >
                                                    
                                                    <?php if ($statusBerkas === 'ditolak' && $ketStatus !== ''): ?>
                                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">
                                                        <i class="fas fa-exclamation-circle mr-1"></i>Alasan penolakan: <strong><?= htmlspecialchars($ketStatus) ?></strong>. Silakan unggah ulang.
                                                    </p>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </form>
                            <?php 
                                break;

                            default: 
                                // Wajib didefinisikan per step - tidak ada aksi jika belum terdaftar di case
                            ?>
                                <div class="p-8 text-center rounded-lg border border-dashed border-slate-200 bg-slate-50">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3 text-xl">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-700">Aktivitas Tahapan Ini Belum Dikonfigurasi</h4>
                                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Tahapan SOP ini belum memiliki lembar kerja khusus dalam sistem.</p>
                                </div>
                            <?php 
                                break;
                        endswitch; 
                        ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ===== KOLOM KECIL (md:col-span-5 lg:col-span-4): Progress Line Stepper ===== -->
        <div class="md:col-span-5 lg:col-span-4 space-y-4 reveal" style="animation-delay:.1s">
            
            <!-- Ringkasan Progress Bar -->
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-600">Progress Alur SOP</span>
                    <span class="text-xs font-bold text-orange-600"><?= $progres ?>%</span>
                </div>
                <div class="progres-bar-track">
                    <div class="progres-bar-fill" style="width:<?= $progres ?>%;"></div>
                </div>
                <p class="mt-2 text-[11px] text-slate-400"><?= $selesai ?> dari <?= $total ?> langkah SOP selesai.</p>
            </div>

            <!-- List Stepper SOP -->
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4 pb-2 border-b border-slate-100">Alur SOP Skripsi</h3>
                
                <div class="space-y-0 sop-scroll">
                    <?php foreach ($steps as $i => $s):
                        $isLast = ($i === $total - 1);
                        $badgeClass = $s['status'] === 'selesai' ? 'done' : ($s['status'] === 'aktif' ? 'aktif' : 'wait');
                        $cardClass  = $badgeClass;
                        $connClass  = $s['status'] === 'selesai' ? 'done' : '';
                    ?>
                    <div class="step-node">
                        <!-- Dot + Line -->
                        <div class="flex flex-col items-center">
                            <div class="step-dot <?= $badgeClass ?>">
                                <?php if ($s['status'] === 'selesai'): ?>
                                    <i class="fas fa-check"></i>
                                <?php elseif ($s['status'] === 'aktif'): ?>
                                    <i class="fas fa-pen"></i>
                                <?php else: ?>
                                    <?= $s['urutan'] ?>
                                <?php endif; ?>
                            </div>
                            <?php if (!$isLast): ?>
                            <div class="step-line <?= $connClass ?>"></div>
                            <?php endif; ?>
                        </div>

                        <!-- Text Info -->
                        <div class="step-item-card <?= $cardClass ?>">
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-[9px] font-bold text-slate-400 uppercase">Step <?= $s['urutan'] ?></span>
                                <span class="text-[9px] font-semibold text-slate-500"><?= htmlspecialchars($s['executor']) ?></span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-800 leading-snug mt-0.5"><?= htmlspecialchars($s['judul']) ?></h4>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

</main>

<div id="skripsiToast">
    <i class="fas fa-circle-check"></i>
    <span id="skripsiToastMsg" class="flex-1">Notifikasi</span>
    <button type="button" onclick="document.getElementById('skripsiToast').classList.remove('show')" class="text-white/70 hover:text-white ml-1"><i class="fas fa-xmark text-xs"></i></button>
</div>

<script>
function toast(msg, type) {
    var t = document.getElementById('skripsiToast');
    document.getElementById('skripsiToastMsg').textContent = msg;
    t.className = 'show ' + (type || 'info');
    clearTimeout(t._tid);
    t._tid = setTimeout(function(){ t.classList.remove('show'); }, 3500);
}

function setButtonLoading(btn, isLoading) {
    if (isLoading) {
        btn.classList.add('is-loading');
        btn.setAttribute('disabled', '');
        btn.innerHTML = '<span class="spinner-slate"></span> Memproses...';
    } else {
        btn.classList.remove('is-loading');
        btn.removeAttribute('disabled');
        btn.innerHTML = '<i class="fas fa-paper-plane"></i>' + (btn.getAttribute('data-text') || 'Kirim & Tandai Selesai');
    }
}

// Auto Upload Berkas saat event Change pada input file
document.querySelectorAll('.auto-upload-input').forEach(function(input) {
    input.addEventListener('change', function() {
        var fileInput = this;
        var idPersyaratan = this.getAttribute('data-id');
        var fieldTarget = this.getAttribute('data-field');
        var badge = document.querySelector('.status-berkas-badge[data-id="' + idPersyaratan + '"]');

        if (!fileInput.files[0]) return;

        // Visual loading
        if (badge) {
            badge.className = 'status-berkas-badge text-[10px] font-semibold text-orange-500';
            badge.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Mengunggah...';
        }
        fileInput.setAttribute('disabled', 'disabled');

        var formData = new FormData();
        formData.append('unggah-berkas-skripsi', '1');
        formData.append('id_persyaratan', idPersyaratan);
        formData.append('field_target', fieldTarget);
        formData.append('file', fileInput.files[0]);

        fetch('pages/skripsi/proses.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                toast(data.pesan, 'success');
                if (badge) {
                    badge.className = 'status-berkas-badge text-[10px] font-bold text-emerald-600';
                    badge.innerHTML = '<i class="fas fa-check-circle"></i> Berhasil diunggah';
                }
                fileInput.classList.add('bg-emerald-50', 'border-emerald-200');

                // Jika seluruh berkas di tahapan ini sudah diunggah & diproses server
                if (data.proses_selesai) {
                    toast('Semua berkas lengkap. Memperbarui progres...', 'success');
                    setTimeout(function(){ location.reload(); }, 1200);
                }
            } else {
                toast(data.pesan, 'error');
                if (badge) {
                    badge.className = 'status-berkas-badge text-[10px] font-semibold text-rose-500';
                    badge.innerHTML = 'Gagal mengunggah';
                }
                fileInput.removeAttribute('disabled');
            }
        })
        .catch(err => {
            toast('Terjadi kesalahan koneksi server.', 'error');
            if (badge) {
                badge.className = 'status-berkas-badge text-[10px] font-semibold text-rose-500';
                badge.innerHTML = 'Gagal koneksi';
            }
            fileInput.removeAttribute('disabled');
        });
    });
});

document.getElementById('btnSelesaikanLangkah')?.addEventListener('click', function(e) {
    e.preventDefault();
    var btn = this;
    var originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-slate"></span> Memproses...';
    btn.setAttribute('disabled', 'disabled');

    var formData = new FormData();
    formData.append('tandai-selesai-step', '1');

    fetch('pages/skripsi/proses.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status) {
            toast(data.pesan, 'success');
            setTimeout(function(){ location.reload(); }, 1000);
        } else {
            toast(data.pesan, 'error');
            btn.innerHTML = originalText;
            btn.removeAttribute('disabled');
        }
    })
    .catch(err => {
        toast('Terjadi kesalahan server.', 'error');
        btn.innerHTML = originalText;
        btn.removeAttribute('disabled');
    });
});

// Logika Step 6: Selesai Penelitian
var chkSiapHasil = document.getElementById('chkSiapHasil');
var btnSelesaiPenelitian = document.getElementById('btnSelesaiPenelitian');

if (chkSiapHasil && btnSelesaiPenelitian) {
    chkSiapHasil.addEventListener('change', function() {
        if (this.checked) {
            btnSelesaiPenelitian.removeAttribute('disabled');
            btnSelesaiPenelitian.className = 'inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-orange-500/20 hover:bg-orange-600 transition min-w-[170px]';
        } else {
            btnSelesaiPenelitian.setAttribute('disabled', 'disabled');
            btnSelesaiPenelitian.className = 'inline-flex items-center justify-center gap-2 rounded-lg bg-slate-300 px-5 py-2.5 text-xs font-bold text-slate-500 cursor-not-allowed transition min-w-[170px]';
        }
    });

    btnSelesaiPenelitian.addEventListener('click', function() {
        if (!this.hasAttribute('disabled')) {
            var btn = this;
            var originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-slate"></span> Memproses...';
            btn.setAttribute('disabled', 'disabled');

            var formData = new FormData();
            formData.append('tandai-selesai-step', '1');

            fetch('pages/skripsi/proses.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    toast('Tahap penelitian selesai. Melanjutkan ke step berikutnya.', 'success');
                    setTimeout(function(){ location.reload(); }, 1000);
                } else {
                    toast(data.pesan, 'error');
                    btn.innerHTML = originalText;
                    btn.removeAttribute('disabled');
                }
            })
            .catch(err => {
                toast('Terjadi kesalahan koneksi server.', 'error');
                btn.innerHTML = originalText;
                btn.removeAttribute('disabled');
            });
        }
    });
}

// Logika pendaftaran skripsi
var chkSks = document.getElementById('chkSks');
var chkKrs = document.getElementById('chkKrs');
var btnDaftar = document.getElementById('btnDaftarSkripsi');

function checkPersyaratan() {
    if (chkSks.checked && chkKrs.checked) {
        btnDaftar.removeAttribute('disabled');
        btnDaftar.className = 'mt-6 inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-orange-500/20 hover:bg-orange-600 transition focus:ring focus:ring-orange-500/30 min-w-[210px] h-[42px]';
    } else {
        btnDaftar.setAttribute('disabled', 'disabled');
        btnDaftar.className = 'mt-6 inline-flex items-center justify-center gap-2 rounded-lg bg-slate-300 px-5 py-2.5 text-sm font-bold text-slate-500 cursor-not-allowed transition min-w-[210px] h-[42px]';
    }
}

if(chkSks && chkKrs) {
    chkSks.addEventListener('change', checkPersyaratan);
    chkKrs.addEventListener('change', checkPersyaratan);
    
    btnDaftar.addEventListener('click', function() {
        if (!this.hasAttribute('disabled')) {
            var btn = this;
            var originalText = btn.innerHTML;
            
            // Tampilan loading
            btn.innerHTML = '<span class="spinner-slate"></span> Mendaftarkan...';
            btn.setAttribute('disabled', 'disabled');
            
            // Request AJAX ke proses.php
            var formData = new FormData();
            formData.append('daftar-skripsi', '1');

            fetch('pages/skripsi/proses.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    toast(data.pesan, 'success');
                    setTimeout(function() {
                        location.reload(); 
                    }, 1000);
                } else {
                    toast(data.pesan, 'error');
                    btn.innerHTML = originalText;
                    btn.removeAttribute('disabled');
                }
            })
            .catch(err => {
                toast('Terjadi kesalahan pada server.', 'error');
                btn.innerHTML = originalText;
                btn.removeAttribute('disabled');
            });
        }
    });
}
</script>
