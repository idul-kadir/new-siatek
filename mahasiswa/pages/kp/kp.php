<?php
/**
 * Halaman: Kerja Praktek Mahasiswa (Data Dinamis)
 * Mengikuti arsitektur dan tata letak Skripsi Mahasiswa secara presisi (1:1).
 */
require_once __DIR__ . '/../../../fungsi.php';
mysqli_set_charset($GLOBALS['koneksi'], 'utf8mb4');
$nim = $_SESSION['nim'] ?? $_SESSION['username'] ?? $_SESSION['user_id'] ?? '';

/* --- Ambil Data Utama Mahasiswa & Kerja Praktek --- */
$dMhs = ['nama' => '', 'prodi' => '', 'angkatan' => '', 'pa' => '', 'nip_pa' => ''];
$dKp  = [
    'judul'               => 'Belum mengajukan judul Kerja Praktek',
    'instansi'            => '-',
    'lokasi'              => '-',
    'pembimbing'          => 'Belum Ditentukan',
    'nip_pemb'            => '-',
    'penguji_1'           => 'Belum Ditentukan',
    'penguji_2'           => 'Belum Ditentukan',
    'tgl_mulai'           => 'Belum dijadwalkan',
    'tgl_selesai'         => 'Belum dijadwalkan',
    'jadwal_seminar'      => 'Belum dijadwalkan',
    'jam_seminar'         => '-',
    'ruangan_seminar'     => '-'
];
$progresSaatIni = 'Menunggu data SOP';

// Ambil Mahasiswa
$qMhs = query("SELECT nama, prodi, angkatan, dosen_pa FROM mahasiswa WHERE nim = '$nim' LIMIT 1");
if ($qMhs && mysqli_num_rows($qMhs) > 0) {
    $rowMhs = mysqli_fetch_assoc($qMhs);
    $dMhs['nama']     = $rowMhs['nama'];
    $dMhs['angkatan'] = $rowMhs['angkatan'];

    $kodeProdi = bersihkan($rowMhs['prodi']);
    $qProdi = query("SELECT nama FROM prodi WHERE kode = '$kodeProdi' OR nama = '$kodeProdi' LIMIT 1");
    if ($qProdi && mysqli_num_rows($qProdi) > 0) {
        $dMhs['prodi'] = mysqli_fetch_assoc($qProdi)['nama'];
    } else {
        $dMhs['prodi'] = $rowMhs['prodi'];
    }

    $valDosenPaId = '';
    if (!empty($rowMhs['dosen_pa'])) {
        $nipPa = $rowMhs['dosen_pa'];
        $dMhs['nip_pa'] = $nipPa;
        $qPa = query("SELECT id, nama, nip FROM dosen WHERE nip = '$nipPa' OR id = '$nipPa' LIMIT 1");
        if ($qPa && mysqli_num_rows($qPa) > 0) {
            $rPa = mysqli_fetch_assoc($qPa);
            $dMhs['pa'] = $rPa['nama'];
            $dMhs['nip_pa'] = $rPa['nip'] ?: $nipPa;
            $valDosenPaId = $rPa['id'];
        } else {
            $dMhs['pa'] = $nipPa;
            $valDosenPaId = $nipPa;
        }
    } else {
        $dMhs['pa'] = '-';
        $dMhs['nip_pa'] = '-';
    }
}

// Ambil list Dosen aktif untuk dropdown Penasihat Akademik (PA) sesuai aturan php-native-crud-instructions.md
$listDosenPa = [];
$qDsnPa = query("SELECT id, nama, nip FROM dosen WHERE LOWER(posisi) = 'dosen' AND LOWER(status) = 'aktif' ORDER BY nama ASC");
if ($qDsnPa && mysqli_num_rows($qDsnPa) > 0) {
    while ($rd = mysqli_fetch_assoc($qDsnPa)) {
        $listDosenPa[] = $rd;
    }
}

// Ambil Data Kerja Praktek & Pembimbing dari tabel kp
$judulAsli  = '';
$statusKpDb = null;
$qKp = query("SELECT * FROM kp WHERE nim = '$nim' LIMIT 1");

if ($qKp && mysqli_num_rows($qKp) > 0) {
    $rowKp = mysqli_fetch_assoc($qKp);
    $statusKpDb = strtolower(trim((string)($rowKp['status'] ?? '')));
    $judulAsli  = trim((string)($rowKp['judul'] ?? ''));
    if ($judulAsli !== '') {
        $dKp['judul'] = $judulAsli;
    }

    $instansiDb = trim((string)($rowKp['instansi'] ?? $rowKp['tempat_kp'] ?? $rowKp['tempat'] ?? ''));
    if ($instansiDb === '' && !empty($rowKp['lokasi'])) {
        $instansiDb = trim((string)$rowKp['lokasi']);
    }
    if ($instansiDb !== '') {
        $dKp['instansi'] = $instansiDb;
    }

    $lokasiDb = trim((string)($rowKp['alamat'] ?? $rowKp['kota'] ?? ''));
    if ($lokasiDb === '' && !empty($rowKp['lokasi']) && $rowKp['lokasi'] !== $instansiDb) {
        $lokasiDb = trim((string)$rowKp['lokasi']);
    }
    if ($lokasiDb !== '') {
        $dKp['lokasi'] = $lokasiDb;
    }

    // Jadwal Seminar KP (jika ada kolom tanggal_seminar / tanggal)
    $tglSem = !empty($rowKp['tanggal_seminar']) ? $rowKp['tanggal_seminar'] : ($rowKp['tanggal'] ?? '');
    if (!empty($tglSem) && $tglSem !== '0000-00-00') {
        $dKp['jadwal_seminar'] = date('d M Y', strtotime($tglSem));
    }
    if (!empty($rowKp['jam_seminar'])) {
        $dKp['jam_seminar'] = $rowKp['jam_seminar'];
    } elseif (!empty($rowKp['jam'])) {
        $dKp['jam_seminar'] = $rowKp['jam'];
    }
    if (!empty($rowKp['ruangan'])) {
        $dKp['ruangan_seminar'] = $rowKp['ruangan'];
    }

    // Ambil Pembimbing dari tabel surat_penunjukkan (keterangan PEMBIMBING KP)
    $qSuratBimb = query("SELECT pembimbing1, no_surat FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan LIKE '%PEMBIMBING%KP%' ORDER BY id DESC LIMIT 1");
    if ($qSuratBimb && mysqli_num_rows($qSuratBimb) > 0) {
        $sb = mysqli_fetch_assoc($qSuratBimb);
        $idP1 = $sb['pembimbing1'];
        $qDsn = query("SELECT nama, nip FROM dosen WHERE id = '$idP1' OR nip = '$idP1' LIMIT 1");
        if ($qDsn && mysqli_num_rows($qDsn) > 0) {
            $rd = mysqli_fetch_assoc($qDsn);
            $dKp['pembimbing'] = $rd['nama'];
            $dKp['nip_pemb']   = $rd['nip'] ?: $idP1;
        } else {
            $dKp['pembimbing'] = $idP1;
            $dKp['nip_pemb']   = $idP1;
        }
    } elseif (!empty($rowKp['pembimbing'])) {
        $idP1 = $rowKp['pembimbing'];
        $qDsn = query("SELECT nama, nip FROM dosen WHERE id = '$idP1' OR nip = '$idP1' LIMIT 1");
        if ($qDsn && mysqli_num_rows($qDsn) > 0) {
            $rd = mysqli_fetch_assoc($qDsn);
            $dKp['pembimbing'] = $rd['nama'];
            $dKp['nip_pemb']   = $rd['nip'] ?: $idP1;
        } else {
            $dKp['pembimbing'] = $idP1;
            $dKp['nip_pemb']   = $idP1;
        }
    }

    // Ambil Penguji Seminar KP (keterangan PENGUJI KP)
    $qSuratPeng = query("SELECT penguji1, penguji2 FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan LIKE '%PENGUJI%KP%' ORDER BY id DESC LIMIT 1");
    if ($qSuratPeng && mysqli_num_rows($qSuratPeng) > 0) {
        $spg = mysqli_fetch_assoc($qSuratPeng);
        if (!empty($spg['penguji1'])) {
            $qP1 = query("SELECT nama FROM dosen WHERE id = '{$spg['penguji1']}' OR nip = '{$spg['penguji1']}' LIMIT 1");
            $dKp['penguji_1'] = ($qP1 && mysqli_num_rows($qP1) > 0) ? mysqli_fetch_assoc($qP1)['nama'] : $spg['penguji1'];
        }
        if (!empty($spg['penguji2'])) {
            $qP2 = query("SELECT nama FROM dosen WHERE id = '{$spg['penguji2']}' OR nip = '{$spg['penguji2']}' LIMIT 1");
            $dKp['penguji_2'] = ($qP2 && mysqli_num_rows($qP2) > 0) ? mysqli_fetch_assoc($qP2)['nama'] : $spg['penguji2'];
        }
    }
}

/* --- Ambil Data Rencana Pengajuan Kerja Praktek (Step 1) --- */
$valSks        = '';
$valTglMulai   = '';
$valTglSelesai = '';
$valTempat     = '';
$valAlamat     = '';
$valTujuan     = '';

if (!empty($rowKp)) {
    // Ambil data rencana pengajuan KP mahasiswa dari tabel kp
    $valSks        = $rowKp['sks'] ?? '';
    $rawMulai      = !empty($rowKp['tanggal_mulai']) && $rowKp['tanggal_mulai'] !== '0000-00-00'
                       ? $rowKp['tanggal_mulai']
                       : ($rowKp['tgl_start'] ?? '');
    if (!empty($rawMulai) && $rawMulai !== '0000-00-00') {
        $valTglMulai = date('Y-m-d', strtotime($rawMulai));
        $dKp['tgl_mulai'] = date('d M Y', strtotime($rawMulai));
    }
    $rawSelesai    = !empty($rowKp['tanggal_selesai']) && $rowKp['tanggal_selesai'] !== '0000-00-00'
                       ? $rowKp['tanggal_selesai']
                       : ($rowKp['tanggal_penarikan'] ?? '');
    if (!empty($rawSelesai) && $rawSelesai !== '0000-00-00') {
        $valTglSelesai = date('Y-m-d', strtotime($rawSelesai));
        $dKp['tgl_selesai'] = date('d M Y', strtotime($rawSelesai));
    }
    $valTempat     = $rowKp['nama_instansi'] ?? $rowKp['instansi'] ?? $rowKp['tempat'] ?? '';
    $valAlamat     = $rowKp['alamat_instansi'] ?? $rowKp['alamat'] ?? '';
    $valTujuan     = $rowKp['pimpinan_instansi'] ?? '';
}

// Jika tanggal mulai ada tapi tanggal selesai masih kosong, hitung default otomatis berdasarkan durasi
if (!empty($valTglMulai) && empty($valTglSelesai)) {
    $valTglSelesai = hitung_tanggal_selesai_kp($valTglMulai);
    if (!empty($valTglSelesai)) {
        $dKp['tgl_selesai'] = date('d M Y', strtotime($valTglSelesai));
    }
}

/* --- Ambil Status Progres KP dari tabel progres_kp / kp via helper di fungsi.php --- */
$currentProgres = get_progres_kp($nim);
$lastProsesId = ($currentProgres !== '-') ? (int)$currentProgres : 0;

/* --- Ambil data alur SOP Kerja Praktek via helper get_sop_kp_list() di fungsi.php --- */
$steps = [];
$rawSop = get_sop_kp_list();
foreach ($rawSop as $row) {
    $noSop = (int)$row['no'];

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

    $judulFormatted = ucfirst($row['keterangan']);
    if ($st === 'aktif') {
        $progresSaatIni = $judulFormatted;
    }

    $eks = !empty($row['eksekutor']) ? $row['eksekutor'] : 'Mahasiswa';
    $steps[] = [
        'id'       => $noSop,
        'urutan'   => $noSop,
        'judul'    => $judulFormatted,
        'desc'     => 'Eksekutor: ' . ucfirst(strtolower($eks)),
        'executor' => ucfirst(strtolower($eks)),
        'status'   => $st,
    ];
}

$total    = count($steps);
$selesai  = count(array_filter($steps, fn($s) => $s['status'] === 'selesai'));
$progres  = $total > 0 ? round($selesai / $total * 100) : 0;

/* Ambil step aktif saat ini untuk Lembar Kerja */
$currentStep = null;
foreach ($steps as $s) {
    if ($s['status'] === 'aktif') {
        $currentStep = $s;
        break;
    }
}
if (!$currentStep && !empty($steps)) {
    $currentStep = $steps[$total - 1];
    $progresSaatIni = 'Semua tahapan selesai';
}

/* Flag Kondisi Status Pendaftaran KP (Persis seperti Skripsi) */
$hasKpRecord  = ($statusKpDb !== null);
$isKpInactive = ($statusKpDb === 'tidak aktif' || $statusKpDb === 'nonaktif');
$hasProgres   = ($currentProgres !== '-');
$canEditJudul = ($hasKpRecord && !$isKpInactive && $lastProsesId > 2);

/* --- Ambil Persyaratan Berkas sesuai progres saat ini (Persis Skripsi) --- */
$berkasDibutuhkan   = [];
$berkasSudahDunggah = [];
$nim_clean = bersihkan($nim);
if ($hasKpRecord && !$isKpInactive && $lastProsesId > 0) {
    $qBerkas = query("SELECT id, nama_berkas, tabel, field FROM persyaratan_berkas_kp WHERE progres = '$lastProsesId' ORDER BY id ASC");
    if ($qBerkas && mysqli_num_rows($qBerkas) > 0) {
        while ($b = mysqli_fetch_assoc($qBerkas)) {
            $berkasDibutuhkan[] = $b;
        }
    }

    $qSudah = query("SELECT id_persyaratan FROM temp_kp WHERE nim = '$nim_clean' AND id_persyaratan IN (SELECT id FROM persyaratan_berkas_kp WHERE progres = '$lastProsesId')");
    if ($qSudah && mysqli_num_rows($qSudah) > 0) {
        while ($t = mysqli_fetch_assoc($qSudah)) {
            $berkasSudahDunggah[] = (int)$t['id_persyaratan'];
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

    #kpToast {
        position: fixed; right: 20px; top: 20px; z-index: 2000;
        display: none; align-items: center; gap: .6rem; max-width: 360px;
        border-radius: .75rem; padding: .7rem 1rem; font-size: .82rem;
        box-shadow: 0 12px 24px rgba(15,23,42,.18); color: #fff;
    }
    #kpToast.show { display: flex; animation: riseIn .3s ease; }
    #kpToast.success { background: linear-gradient(135deg,#16a34a,#15803d); }
    #kpToast.error   { background: linear-gradient(135deg,#dc2626,#b91c1c); }
    #kpToast.info    { background: linear-gradient(135deg,#1a365d,#234670); }
</style>

<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Kerja Praktek Mahasiswa</h1>
                    <p class="text-xs text-slate-500">Lembar Kerja Aktivitas Kerja Praktek Sesuai Prosedur SOP</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">
                <i class="fas fa-list-check"></i><?= $selesai ?>/<?= $total ?> SOP Selesai
            </span>
        </div>
    </section>

    <!-- ===== Panel Informasi Kerja Praktek ===== -->
    <section class="mb-6 reveal">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col xl:flex-row gap-6">
            
            <!-- Kiri: Identitas, Judul & Jadwal -->
            <div class="flex-1 space-y-5 border-b xl:border-b-0 xl:border-r border-slate-100 pb-5 xl:pb-0 xl:pr-6">
                <div>
                    <!-- Badge Identitas Mahasiswa -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-900 text-white px-3 py-1 text-xs font-bold shadow-sm">
                            <i class="fas fa-user-graduate text-orange-400"></i><?= htmlspecialchars($dMhs['nama'] ?: 'Nama Mahasiswa') ?>
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 text-orange-700 px-3 py-1 text-xs font-bold">
                            <i class="fas fa-id-card"></i><?= htmlspecialchars($nim) ?>
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-100 text-sky-700 px-3 py-1 text-xs font-bold">
                            <i class="fas fa-graduation-cap"></i><?= htmlspecialchars($dMhs['prodi'] ?: '-') ?> (<?= htmlspecialchars($dMhs['angkatan'] ?: '-') ?>)
                        </span>
                    </div>

                    <!-- Judul Kerja Praktek -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Judul / Topik Kerja Praktek</p>
                            <?php if ($canEditJudul): ?>
                            <button type="button" id="btnToggleEditJudul" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 px-2.5 py-1 text-[11px] font-bold transition">
                                <i class="fas fa-pen text-[10px]"></i>
                                <span id="txtBtnEditJudul"><?= $judulAsli !== '' ? 'Ubah Judul' : 'Tulis Judul' ?></span>
                            </button>
                            <?php endif; ?>
                        </div>
                        <div id="tampilanJudulWrap">
                            <h2 id="teksJudulKp" class="text-lg font-extrabold text-slate-800 leading-snug">
                                <?= htmlspecialchars($dKp['judul']) ?>
                            </h2>
                        </div>
                        <?php if ($canEditJudul): ?>
                        <form id="formUbahJudul" class="hidden pt-1 space-y-2.5">
                            <textarea id="inputJudulKp" name="judul" rows="2" required placeholder="Tuliskan judul / topik Kerja Praktek Anda di sini..." class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-800 outline-none focus:border-orange-500 focus:bg-white transition"><?= htmlspecialchars($judulAsli) ?></textarea>
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" id="btnBatalJudul" class="rounded-lg bg-slate-100 hover:bg-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 transition">
                                    Batal
                                </button>
                                <button type="submit" id="btnSimpanJudul" data-text="Simpan Judul" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition">
                                    Simpan Judul
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Informasi Instansi & Seminar KP -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2.5 px-3.5 py-2 rounded-lg bg-slate-50 border border-slate-100">
                            <i class="fas fa-building text-orange-500 text-sm"></i>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Instansi Tempat Magang</p>
                                <p class="text-xs font-bold text-slate-700" id="badgeInstansiKp">
                                    <?= htmlspecialchars($dKp['instansi'] !== '-' ? $dKp['instansi'] : 'Belum Ditentukan') ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 px-3.5 py-2 rounded-lg bg-slate-50 border border-slate-100">
                            <i class="fas fa-calendar-check text-sky-500 text-sm"></i>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Jadwal Seminar KP</p>
                                <p class="text-xs font-bold text-slate-700">
                                    <?= htmlspecialchars($dKp['jadwal_seminar']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan: Dosen (PA & Dosen Pembimbing KP) -->
            <div class="w-full xl:w-72 flex-shrink-0 space-y-4">
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 flex-shrink-0 text-[11px] font-bold">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Penasihat Akademik</p>
                            <p class="text-[11px] font-bold text-slate-800"><?= htmlspecialchars($dMhs['pa']) ?></p>
                            <p class="text-[10px] text-slate-400">NIP. <?= htmlspecialchars($dMhs['nip_pa']) ?></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 text-orange-600 flex-shrink-0 text-[11px] font-bold">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Dosen Pembimbing KP</p>
                            <p class="text-[11px] font-bold text-slate-800"><?= htmlspecialchars($dKp['pembimbing']) ?></p>
                            <p class="text-[10px] text-slate-400">NIP. <?= htmlspecialchars($dKp['nip_pemb']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ===== Tata Letak 2 Kolom (Besar: Lembar Kerja SOP | Kecil: Progress Line) ===== -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start">

        <?php if (!$hasKpRecord): ?>
        <!-- BELUM TERDAFTAR DI KERJA PRAKTEK (PERSIS SEPERTI SKRIPSI) -->
        <div class="md:col-span-12 reveal">
            <div class="rounded-xl border border-slate-200 bg-white p-8 md:p-10 text-center shadow-sm">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-100 text-3xl text-orange-500 mx-auto mb-4">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Mulai Kerja Praktek</h3>
                <p class="mt-2 text-sm text-slate-500 max-w-lg mx-auto">Anda belum terdaftar dalam aktivitas Kerja Praktek. Pastikan Anda telah memenuhi persyaratan utama di bawah ini sebelum mendaftar.</p>

                <!-- Box Persyaratan Checkbox (Hanya 2 Checkbox Sederhana seperti Skripsi) -->
                <div class="mt-6 max-w-xl mx-auto rounded-xl border border-slate-200 bg-slate-50 p-5 text-left space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Persyaratan Wajib Pendaftaran:</h4>
                    
                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" id="chkSks" class="mt-0.5 rounded border-slate-300 text-orange-500 focus:ring-orange-500 w-4 h-4 flex-shrink-0">
                        <span class="text-xs text-slate-700 leading-normal">
                            <strong class="font-bold text-slate-800">SKS dicapai minimal 100 telah terpenuhi</strong> (dibuktikan dengan dokumen Lampiran Transkrip Nilai).
                        </span>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" id="chkKrs" class="mt-0.5 rounded border-slate-300 text-orange-500 focus:ring-orange-500 w-4 h-4 flex-shrink-0">
                        <span class="text-xs text-slate-700 leading-normal">
                            <strong class="font-bold text-slate-800">Mengontrak Mata Kuliah Kerja Praktek</strong> pada semester berjalan saat ini (dibuktikan dengan Kartu Rencana Studi / KRS).
                        </span>
                    </label>
                </div>
                
                <button type="button" id="btnDaftarKp" disabled class="mt-6 inline-flex items-center justify-center gap-2 rounded-lg bg-slate-300 px-5 py-2.5 text-sm font-bold text-slate-500 cursor-not-allowed transition min-w-[210px] h-[42px]">
                    <i class="fas fa-rocket"></i>Daftar Kerja Praktek Sekarang
                </button>
            </div>
        </div>

        <?php elseif ($isKpInactive): ?>
        <!-- SUDAH TERDAFTAR TAPI STATUS TIDAK AKTIF (PERSIS SEPERTI SKRIPSI) -->
        <div class="md:col-span-12 reveal">
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-2xl text-amber-600 flex-shrink-0">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-bold text-amber-900">Pendaftaran Kerja Praktek Anda Masih Non-Aktif</h3>
                        <p class="mt-1 text-sm text-amber-800 leading-relaxed">
                            Pendaftaran Anda tercatat dalam sistem namun aktivasi tahapan Kerja Praktek masih menunggu konfirmasi.
                        </p>
                        <p class="mt-3 text-xs text-amber-700 font-medium border-t border-amber-200 pt-3">
                            <i class="fas fa-exclamation-triangle text-amber-500 mr-1"></i>
                            Harap segera menghadap <strong class="text-amber-900 font-bold">Operator Jurusan/Prodi</strong> untuk mengaktifkan pendaftaran Kerja Praktek dengan membawa dokumen 
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
                
                <?php if ($total === 0): ?>
                <!-- SOP BELUM DIKONFIGURASI -->
                <div class="p-8 text-center rounded-lg border border-dashed border-slate-200 bg-slate-50">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3 text-xl">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-700">Alur SOP Kerja Praktek Belum Dikonfigurasi</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Tabel SOP Kerja Praktek di database belum memiliki data tahapan. Silakan hubungi Administrator/Operator Jurusan.</p>
                </div>
                <?php else: ?>

                <!-- Header Lembar Kerja -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-bold">
                                <i class="fas fa-clipboard-check"></i>
                            </span>
                            <h3 class="text-base font-bold text-slate-800"><?= htmlspecialchars($currentStep['judul']) ?></h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Eksekutor SOP: <strong class="text-orange-600"><?= htmlspecialchars($currentStep['executor']) ?></strong></p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">
                        Aktif
                    </span>
                </div>

                <!-- Konten Aktivitas SOP -->
                <div class="space-y-4">
                    <?php if (strtolower($currentStep['executor']) === 'mahasiswa'): ?>
                    <div class="rounded-lg bg-orange-50/60 border border-orange-100 p-4 text-xs text-slate-700 leading-relaxed">
                        <i class="fas fa-info-circle text-orange-500 mr-1.5"></i>
                        Silakan lengkapi berkas dan persyaratan berikut untuk menyelesaikan tahapan <strong><?= htmlspecialchars($currentStep['judul']) ?></strong>.
                    </div>
                    <?php endif; ?>

                    <?php if (strtolower($currentStep['executor']) !== 'mahasiswa' && (int)$currentStep['urutan'] != 2): ?>
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
                                // Step 1: Pengisian Rencana KP & Cetak Lembar Persetujuan Penasihat Akademik (PA)
                            ?>
                                <div class="space-y-5">
                                    <!-- Header Info Lembar Persetujuan PA -->
                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                        <div class="flex items-start gap-3.5">
                                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100 text-orange-600 flex-shrink-0 text-base">
                                                <i class="fas fa-file-signature"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-800">Tahap 1: Pengajuan &amp; Lembar Persetujuan PA</h4>
                                                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                                                    Lengkapi rincian rencana pelaksanaan Kerja Praktek Anda di bawah ini untuk dicantumkan pada Lembar Persetujuan Penasihat Akademik (PA).
                                                </p>
                                                <?php if (!empty($dMhs['pa']) && $dMhs['pa'] !== '-'): ?>
                                                <p class="text-[11px] text-slate-600 font-medium mt-1.5 flex items-center gap-1.5">
                                                    <i class="fas fa-user-tie text-orange-500"></i>Dosen PA: <strong><?= htmlspecialchars($dMhs['pa']) ?></strong> (NIP. <?= htmlspecialchars($dMhs['nip_pa']) ?>)
                                                </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Form Pengisian Rencana Kerja Praktek -->
                                    <form id="formPengajuanKp" class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 shadow-xs space-y-4">
                                        <div class="border-b border-slate-100 pb-3">
                                            <h5 class="text-xs font-bold uppercase tracking-wider text-slate-700">Formulir Rencana Pelaksanaan Kerja Praktek</h5>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Semua kolom bertanda bintang (<span class="text-rose-500">*</span>) wajib diisi dengan data valid.</p>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Dosen Penasihat Akademik (PA) -->
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Dosen Penasihat Akademik (PA) <span class="text-rose-500">*</span>
                                                </label>
                                                <select name="dosen_pa" id="inputDosenPaKp" required
                                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-orange-500 focus:bg-white transition">
                                                    <option value="">-- Pilih Dosen Penasihat Akademik (PA) --</option>
                                                    <?php foreach ($listDosenPa as $dsn): ?>
                                                        <option value="<?= htmlspecialchars((string)$dsn['id']) ?>" <?= (!empty($valDosenPaId) && ($valDosenPaId == $dsn['id'] || (isset($dsn['nip']) && $valDosenPaId == $dsn['nip']))) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($dsn['nama']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <p class="text-[11px] text-slate-400 mt-1"><i class="fas fa-info-circle mr-1"></i>Pilih Dosen Penasihat Akademik (PA) Anda untuk menandatangani lembar persetujuan KP.</p>
                                            </div>

                                            <!-- 1. Jumlah SKS Ditempuh -->
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Jumlah SKS yang Telah Ditempuh <span class="text-rose-500">*</span>
                                                </label>
                                                <input type="number" name="sks" id="inputSksKp" min="100" max="200" required
                                                    placeholder="Contoh: 110"
                                                    value="<?= htmlspecialchars((string)$valSks) ?>"
                                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-orange-500 focus:bg-white transition">
                                                <p class="text-[11px] text-slate-400 mt-1"><i class="fas fa-info-circle mr-1"></i>Syarat minimal SKS yang telah ditempuh dan lulus adalah 100 SKS.</p>
                                            </div>

                                            <!-- 2. Rencana Tanggal Mulai -->
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Rencana Tanggal Mulai KP <span class="text-rose-500">*</span>
                                                </label>
                                                <input type="date" name="tanggal_mulai" id="inputTglMulaiKp" required
                                                    value="<?= htmlspecialchars((string)$valTglMulai) ?>"
                                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-orange-500 focus:bg-white transition">
                                            </div>

                                            <!-- 3. Rencana Tanggal Selesai (Otomatis) -->
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Rencana Tanggal Selesai KP <span class="text-rose-500">*</span>
                                                </label>
                                                <input type="date" name="tanggal_selesai" id="inputTglSelesaiKp" required readonly tabindex="-1"
                                                    value="<?= htmlspecialchars((string)$valTglSelesai) ?>"
                                                    class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3.5 py-2 text-xs font-medium text-slate-600 outline-none cursor-not-allowed pointer-events-none select-none">
                                                <p class="text-[11px] text-slate-400 mt-1">
                                                    <i class="fas fa-lock text-slate-400 mr-1"></i>Otomatis terhitung <?= defined('DURASI_DEFAULT_KP_BULAN') ? (int)DURASI_DEFAULT_KP_BULAN : 2 ?> bulan dari tanggal mulai.
                                                </p>
                                            </div>

                                            <!-- 4. Tempat / Instansi KP -->
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Tempat / Instansi Kerja Praktek <span class="text-rose-500">*</span>
                                                </label>
                                                <input type="text" name="tempat" id="inputTempatKp" required
                                                    placeholder="Contoh: PT. PLN (Persero) UP3 Gorontalo / Dinas Kominfo"
                                                    value="<?= htmlspecialchars((string)$valTempat) ?>"
                                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-orange-500 focus:bg-white transition">
                                            </div>

                                            <!-- 5. Alamat Tempat KP -->
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Alamat Lengkap Tempat KP <span class="text-rose-500">*</span>
                                                </label>
                                                <textarea name="alamat" id="inputAlamatKp" rows="2" required
                                                    placeholder="Contoh: Jl. Jendral Sudirman No. 25, Kota Gorontalo"
                                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-orange-500 focus:bg-white transition"><?= htmlspecialchars((string)$valAlamat) ?></textarea>
                                            </div>

                                            <!-- 6. Tujuan Surat -->
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Tujuan Surat (Jabatan / Pimpinan Instansi) <span class="text-rose-500">*</span>
                                                </label>
                                                <input type="text" name="tujuan_surat" id="inputTujuanSuratKp" required
                                                    placeholder="Contoh: Kepala Dinas Komunikasi dan Informatika Provinsi Gorontalo"
                                                    value="<?= htmlspecialchars((string)$valTujuan) ?>"
                                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-orange-500 focus:bg-white transition">
                                                <p class="text-[11px] text-slate-400 mt-1"><i class="fas fa-info-circle mr-1"></i>Pihak pimpinan atau pejabat yang dituju untuk surat pengantar/permohonan pelaksanaan KP.</p>
                                            </div>
                                        </div>

                                        <!-- Tombol Aksi Simpan & Cetak -->
                                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                                            <button type="submit" id="btnSimpanCetakPa" data-text="Simpan &amp; Cetak Persetujuan PA"
                                                class="inline-flex items-center gap-2 rounded-lg bg-orange-500 hover:bg-orange-600 px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-orange-500/20 transition cursor-pointer">
                                                <i class="fas fa-print"></i>Simpan &amp; Cetak Persetujuan PA
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php 
                                break;

                            case 2:
                                // Step 2: Lembar Persetujuan Penasihat Akademik (PA) & Pengajuan Rencana KP
                            ?>
                                <div class="space-y-5">
                                    <!-- Header Info Tahap 2 -->
                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                        <div class="flex items-start gap-3.5">
                                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100 text-orange-600 flex-shrink-0 text-base">
                                                <i class="fas fa-file-signature"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-800">Tahap 2: <?= htmlspecialchars($currentStep['judul']) ?></h4>
                                                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                                                    Formulir rencana pelaksanaan Kerja Praktek dan Lembar Persetujuan Penasihat Akademik (PA).
                                                </p>
                                                <?php if (!empty($dMhs['pa']) && $dMhs['pa'] !== '-'): ?>
                                                <p class="text-[11px] text-slate-600 font-medium mt-1.5 flex items-center gap-1.5">
                                                    <i class="fas fa-user-tie text-orange-500"></i>Dosen PA: <strong><?= htmlspecialchars($dMhs['pa']) ?></strong> (NIP. <?= htmlspecialchars($dMhs['nip_pa']) ?>)
                                                </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Informasi: Silakan Menghadap ke Operator Prodi / Jurusan -->
                                    <div class="rounded-xl border border-blue-200 bg-blue-50/70 p-4 text-xs text-blue-900 flex items-start gap-3.5 shadow-xs">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-500 text-white shrink-0 mt-0.5">
                                            <i class="fas fa-circle-info text-base"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <strong class="font-bold block text-blue-950 text-xs">Informasi Alur Pengajuan:</strong>
                                            <p class="text-blue-800 leading-relaxed text-[11px]">
                                                Anda masih dapat memperbarui data formulir rencana Kerja Praktek di bawah ini jika terdapat perubahan. 
                                                <strong>Jika ada yang kurang jelas, silakan menghadap ke Operator Program Studi / Jurusan.</strong>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Form Pengisian Rencana Kerja Praktek (Sama seperti Case 1) -->
                                    <form id="formPengajuanKp" class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 shadow-xs space-y-4">
                                        <div class="border-b border-slate-100 pb-3">
                                            <h5 class="text-xs font-bold uppercase tracking-wider text-slate-700">Formulir Rencana Pelaksanaan Kerja Praktek</h5>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Semua kolom bertanda bintang (<span class="text-rose-500">*</span>) wajib diisi dengan data valid.</p>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Dosen Penasihat Akademik (PA) -->
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Dosen Penasihat Akademik (PA) <span class="text-rose-500">*</span>
                                                </label>
                                                <select name="dosen_pa" id="inputDosenPaKp" required
                                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-orange-500 focus:bg-white transition">
                                                    <option value="">-- Pilih Dosen Penasihat Akademik (PA) --</option>
                                                    <?php foreach ($listDosenPa as $dsn): ?>
                                                        <option value="<?= htmlspecialchars((string)$dsn['id']) ?>" <?= (!empty($valDosenPaId) && ($valDosenPaId == $dsn['id'] || (isset($dsn['nip']) && $valDosenPaId == $dsn['nip']))) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($dsn['nama']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <p class="text-[11px] text-slate-400 mt-1"><i class="fas fa-info-circle mr-1"></i>Pilih Dosen Penasihat Akademik (PA) Anda untuk menandatangani lembar persetujuan KP.</p>
                                            </div>

                                            <!-- 1. Jumlah SKS Ditempuh -->
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Jumlah SKS yang Telah Ditempuh <span class="text-rose-500">*</span>
                                                </label>
                                                <input type="number" name="sks" id="inputSksKp" min="100" max="200" required
                                                    placeholder="Contoh: 110"
                                                    value="<?= htmlspecialchars((string)$valSks) ?>"
                                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-orange-500 focus:bg-white transition">
                                                <p class="text-[11px] text-slate-400 mt-1"><i class="fas fa-info-circle mr-1"></i>Syarat minimal SKS yang telah ditempuh dan lulus adalah 100 SKS.</p>
                                            </div>

                                            <!-- 2. Rencana Tanggal Mulai -->
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Rencana Tanggal Mulai KP <span class="text-rose-500">*</span>
                                                </label>
                                                <input type="date" name="tanggal_mulai" id="inputTglMulaiKp" required
                                                    value="<?= htmlspecialchars((string)$valTglMulai) ?>"
                                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-orange-500 focus:bg-white transition">
                                            </div>

                                            <!-- 3. Rencana Tanggal Selesai (Otomatis) -->
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Rencana Tanggal Selesai KP <span class="text-rose-500">*</span>
                                                </label>
                                                <input type="date" name="tanggal_selesai" id="inputTglSelesaiKp" required readonly tabindex="-1"
                                                    value="<?= htmlspecialchars((string)$valTglSelesai) ?>"
                                                    class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3.5 py-2 text-xs font-medium text-slate-600 outline-none cursor-not-allowed pointer-events-none select-none">
                                                <p class="text-[11px] text-slate-400 mt-1">
                                                    <i class="fas fa-lock text-slate-400 mr-1"></i>Otomatis terhitung <?= defined('DURASI_DEFAULT_KP_BULAN') ? (int)DURASI_DEFAULT_KP_BULAN : 2 ?> bulan dari tanggal mulai.
                                                </p>
                                            </div>

                                            <!-- 4. Tempat / Instansi KP -->
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Tempat / Instansi Kerja Praktek <span class="text-rose-500">*</span>
                                                </label>
                                                <input type="text" name="tempat" id="inputTempatKp" required
                                                    placeholder="Contoh: PT. PLN (Persero) UP3 Gorontalo / Dinas Kominfo"
                                                    value="<?= htmlspecialchars((string)$valTempat) ?>"
                                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-orange-500 focus:bg-white transition">
                                            </div>

                                            <!-- 5. Alamat Tempat KP -->
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Alamat Lengkap Tempat KP <span class="text-rose-500">*</span>
                                                </label>
                                                <textarea name="alamat" id="inputAlamatKp" rows="2" required
                                                    placeholder="Contoh: Jl. Jendral Sudirman No. 25, Kota Gorontalo"
                                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-orange-500 focus:bg-white transition"><?= htmlspecialchars((string)$valAlamat) ?></textarea>
                                            </div>

                                            <!-- 6. Tujuan Surat -->
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                                    Tujuan Surat (Jabatan / Pimpinan Instansi) <span class="text-rose-500">*</span>
                                                </label>
                                                <input type="text" name="tujuan_surat" id="inputTujuanSuratKp" required
                                                    placeholder="Contoh: Kepala Dinas Komunikasi dan Informatika Provinsi Gorontalo"
                                                    value="<?= htmlspecialchars((string)$valTujuan) ?>"
                                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-orange-500 focus:bg-white transition">
                                                <p class="text-[11px] text-slate-400 mt-1"><i class="fas fa-info-circle mr-1"></i>Pihak pimpinan atau pejabat yang dituju untuk surat pengantar/permohonan pelaksanaan KP.</p>
                                            </div>
                                        </div>

                                        <!-- Tombol Aksi di Case 2: Cetak & Simpan Data (Tidak Otomatis Naik Step) -->
                                        <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                                            <a href="<?= base_url() ?>/mahasiswa/dokumen/persetujuan-pa/<?= $nim_clean ?>" target="_blank"
                                                class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-700 shadow-xs transition cursor-pointer">
                                                <i class="fas fa-print text-orange-500"></i>Cetak Lembar Persetujuan PA
                                            </a>
                                            <button type="submit" id="btnSimpanKpStep2" data-text="Simpan Data"
                                                class="inline-flex items-center gap-2 rounded-lg bg-orange-500 hover:bg-orange-600 px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-orange-500/20 transition cursor-pointer">
                                                <i class="fas fa-save"></i>Simpan Data
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Upload Berkas Step 2 (Jika ada berkas yang dikonfigurasi di persyaratan_berkas_kp) -->
                                    <?php if (!empty($berkasDibutuhkan)): ?>
                                    <div class="mt-6 border-t border-slate-100 pt-5 space-y-4">
                                        <div class="border-b border-slate-100 pb-2">
                                            <h5 class="text-xs font-bold uppercase tracking-wider text-slate-700">Unggah Berkas Persyaratan</h5>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Unggah berkas bukti persetujuan PA atau dokumen yang dipersyaratkan.</p>
                                        </div>
                                        <form id="formSopKerja" method="post" enctype="multipart/form-data" class="space-y-4">
                                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-4">
                                                <?php foreach ($berkasDibutuhkan as $b): 
                                                    $sudahAda = in_array((int)$b['id'], $berkasSudahDunggah);
                                                    $urlFileLama = '';
                                                    $statusBerkas = 'menunggu';
                                                    $ketStatus = '';

                                                    if ($sudahAda) {
                                                        $qLama = query("SELECT file, status, ket FROM temp_kp WHERE nim = '$nim_clean' AND id_persyaratan = '{$b['id']}' LIMIT 1");
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

                                                        <?php if ($sudahAda && $statusBerkas === 'ditolak' && $ketStatus !== ''): ?>
                                                        <div class="mb-2 p-2 rounded bg-rose-50 border border-rose-100 text-[11px] text-rose-700">
                                                            <i class="fas fa-exclamation-circle mr-1"></i><strong>Catatan Penolakan:</strong> <?= htmlspecialchars($ketStatus) ?>
                                                        </div>
                                                        <?php endif; ?>

                                                        <div class="flex items-center gap-2">
                                                            <input type="file" name="berkas_<?= $b['id'] ?>" data-id="<?= $b['id'] ?>" accept=".pdf"
                                                                class="input-berkas-sop block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 transition cursor-pointer border border-slate-200 rounded-lg bg-white p-1">
                                                            <button type="button" class="btn-upload-single hidden inline-flex items-center gap-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 px-3 py-2 text-xs font-bold text-white shadow-xs transition" data-id="<?= $b['id'] ?>">
                                                                <i class="fas fa-upload"></i>Unggah
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            <?php 
                                break;

                            default:
                                // Tahapan Kerja Praktek Mahasiswa Lainnya
                            ?>
                                <?php if (!empty($berkasDibutuhkan)): ?>
                                <form id="formSopKerja" method="post" enctype="multipart/form-data" class="space-y-4">
                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-4">
                                        <?php foreach ($berkasDibutuhkan as $b): 
                                            $sudahAda = in_array((int)$b['id'], $berkasSudahDunggah);
                                            $urlFileLama = '';
                                            $statusBerkas = 'menunggu';
                                            $ketStatus = '';

                                            if ($sudahAda) {
                                                $qLama = query("SELECT file, status, ket FROM temp_kp WHERE nim = '$nim_clean' AND id_persyaratan = '{$b['id']}' LIMIT 1");
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
                                </form>
                                <?php else: ?>
                                <div class="p-8 text-center rounded-lg border border-dashed border-slate-200 bg-slate-50">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3 text-xl">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-700">Aktivitas Tahapan Ini Belum Dikonfigurasi</h4>
                                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Tahapan SOP ini belum memiliki berkas persyaratan atau lembar kerja khusus dalam sistem.</p>
                                </div>
                                <?php endif; ?>
                            <?php 
                                break;
                        endswitch;
                        ?>

                    <?php endif; ?>
                </div>
                <?php endif; ?>

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
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4 pb-2 border-b border-slate-100">Alur SOP Kerja Praktek</h3>
                
                <?php if ($total === 0): ?>
                    <p class="text-xs text-slate-400 py-3 text-center italic">Belum ada tahapan di tabel sop_kp.</p>
                <?php else: ?>
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
                <?php endif; ?>
            </div>

        </div>
        <?php endif; ?>

    </div>

</main>

<div id="kpToast">
    <i class="fas fa-circle-check"></i>
    <span id="kpToastMsg" class="flex-1">Notifikasi</span>
    <button type="button" onclick="document.getElementById('kpToast').classList.remove('show')" class="text-white/70 hover:text-white ml-1"><i class="fas fa-xmark text-xs"></i></button>
</div>

<script>
var PROSES_URL = '<?= base_url() ?>/mahasiswa/pages/kp/proses.php';

function toast(msg, type) {
    var t = document.getElementById('kpToast');
    document.getElementById('kpToastMsg').textContent = msg;
    t.className = 'show ' + (type || 'info');
    clearTimeout(t._tid);
    t._tid = setTimeout(function(){ t.classList.remove('show'); }, 3500);
}

function setButtonLoading(btn, isLoading) {
    if (!btn) return;
    if (isLoading) {
        btn.classList.add('is-loading');
        btn.setAttribute('disabled', '');
        btn.innerHTML = '<span class="spinner-slate"></span> ' + (btn.getAttribute('data-text') || btn.textContent);
    } else {
        btn.classList.remove('is-loading');
        btn.removeAttribute('disabled');
        btn.innerHTML = btn.getAttribute('data-text') || btn.textContent;
    }
}

// 1. Auto Upload Berkas saat event Change pada input file
document.querySelectorAll('.auto-upload-input').forEach(function(input) {
    input.addEventListener('change', function() {
        var fileInput = this;
        var idPersyaratan = this.getAttribute('data-id');
        var fieldTarget = this.getAttribute('data-field');
        var badge = document.querySelector('.status-berkas-badge[data-id="' + idPersyaratan + '"]');

        if (!fileInput.files[0]) return;

        if (badge) {
            badge.className = 'status-berkas-badge text-[10px] font-semibold text-orange-500';
            badge.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Mengunggah...';
        }
        fileInput.setAttribute('disabled', 'disabled');

        var formData = new FormData();
        formData.append('unggah-berkas-kp', '1');
        formData.append('id_persyaratan', idPersyaratan);
        formData.append('field_target', fieldTarget);
        formData.append('file', fileInput.files[0]);

        fetch(PROSES_URL, {
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

// 2. Logika Pendaftaran KP (Persis Skripsi: hanya 2 checkbox SKS & KRS)
var chkSks = document.getElementById('chkSks');
var chkKrs = document.getElementById('chkKrs');
var btnDaftar = document.getElementById('btnDaftarKp');

function checkPersyaratan() {
    if (chkSks && chkKrs && btnDaftar) {
        if (chkSks.checked && chkKrs.checked) {
            btnDaftar.removeAttribute('disabled');
            btnDaftar.className = 'mt-6 inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-orange-500/20 hover:bg-orange-600 transition focus:ring focus:ring-orange-500/30 min-w-[210px] h-[42px] cursor-pointer';
        } else {
            btnDaftar.setAttribute('disabled', 'disabled');
            btnDaftar.className = 'mt-6 inline-flex items-center justify-center gap-2 rounded-lg bg-slate-300 px-5 py-2.5 text-sm font-bold text-slate-500 cursor-not-allowed transition min-w-[210px] h-[42px]';
        }
    }
}

if (chkSks && chkKrs && btnDaftar) {
    chkSks.addEventListener('change', checkPersyaratan);
    chkKrs.addEventListener('change', checkPersyaratan);
    
    btnDaftar.addEventListener('click', function() {
        if (!this.hasAttribute('disabled')) {
            var btn = this;
            var originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-slate"></span> Mendaftarkan...';
            btn.setAttribute('disabled', 'disabled');
            
            var formData = new FormData();
            formData.append('daftar-kp', '1');

            fetch(PROSES_URL, {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(text => {
                var data;
                try {
                    data = JSON.parse(text);
                } catch(e) {
                    var cleanMsg = text.replace(/<[^>]+>/g, '').trim().substring(0, 150);
                    throw new Error(cleanMsg || 'Respon server tidak valid');
                }
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
                toast(err.message || 'Terjadi kesalahan pada server.', 'error');
                btn.innerHTML = originalText;
                btn.removeAttribute('disabled');
            });
        }
    });
}

// 4. Logika Ubah / Tulis Judul KP (Persis Skripsi)
var nilaiJudulTersimpan = $('#inputJudulKp').length ? $('#inputJudulKp').val() : '';

$('#btnToggleEditJudul').on('click', function() {
    $('#tampilanJudulWrap').addClass('hidden');
    $(this).addClass('hidden');
    $('#formUbahJudul').removeClass('hidden');
    $('#inputJudulKp').trigger('focus');
});

$('#btnBatalJudul').on('click', function() {
    $('#inputJudulKp').val(nilaiJudulTersimpan);
    $('#formUbahJudul').addClass('hidden');
    $('#tampilanJudulWrap').removeClass('hidden');
    $('#btnToggleEditJudul').removeClass('hidden');
});

$('#formUbahJudul').on('submit', function(e) {
    e.preventDefault();
    var judulBaru = $.trim($('#inputJudulKp').val());
    if (judulBaru === '') {
        toast('Judul Kerja Praktek tidak boleh kosong', 'error');
        return;
    }

    var btnSimpan = document.getElementById('btnSimpanJudul');
    setButtonLoading(btnSimpan, true);

    $.ajax({
        url: PROSES_URL,
        method: 'POST',
        data: {
            'ubah-judul-kp': 1,
            'judul': judulBaru
        },
        dataType: 'json',
        success: function(respon) {
            toast(respon.pesan, respon.status ? 'success' : 'error');
            if (respon.status) {
                var judulTampil = respon.judul || judulBaru;
                nilaiJudulTersimpan = judulTampil;
                $('#inputJudulKp').val(judulTampil);
                $('#teksJudulKp').text(judulTampil);
                $('#txtBtnEditJudul').text('Ubah Judul');
                $('#formUbahJudul').addClass('hidden');
                $('#tampilanJudulWrap').removeClass('hidden');
                $('#btnToggleEditJudul').removeClass('hidden');
            }
        },
        error: function() {
            toast('Terjadi kesalahan pada server.', 'error');
        },
        complete: function() {
            setButtonLoading(btnSimpan, false);
        }
    });
});

// ==============================================================================
// PENGATURAN DURASI KERJA PRAKTEK (KP)
// Default: 2 Bulan (mengacu pada konstanta DURASI_DEFAULT_KP_BULAN di fungsi.php)
// CATATAN PENGATURAN:
// Jika ada perubahan kebijakan durasi pelaksanaan KP (misalkan menjadi 3 bulan):
// - Cukup ubah angka pada konstanta `define('DURASI_DEFAULT_KP_BULAN', 2);` di file fungsi.php
// - Atau Anda juga dapat langsung menyesuaikan variabel `DURASI_KP_BULAN` di bawah ini.
// ==============================================================================
var DURASI_KP_BULAN = <?= defined('DURASI_DEFAULT_KP_BULAN') ? (int)DURASI_DEFAULT_KP_BULAN : 2 ?>;

/**
 * Helper untuk menghitung tanggal selesai otomatis (+N bulan) berdasarkan tanggal mulai.
 * Mengantisipasi peralihan tahun dan overflow jumlah hari per bulan.
 */
function hitungTanggalSelesaiOtomatis(strTglMulai, durasiBulan) {
    if (!strTglMulai) return '';
    var parts = strTglMulai.split('-');
    if (parts.length !== 3) return '';
    var y = parseInt(parts[0], 10);
    var m = parseInt(parts[1], 10) - 1; // 0-indexed bulan (0 = Januari)
    var d = parseInt(parts[2], 10);

    var targetM = m + durasiBulan;
    var targetDate = new Date(y, targetM, d);

    // Antisipasi overflow jika tanggal melampaui jumlah hari di bulan tujuan
    var expectedMonth = ((targetM % 12) + 12) % 12;
    if (targetDate.getMonth() !== expectedMonth) {
        targetDate = new Date(y, targetM + 1, 0);
    }

    var yyyy = targetDate.getFullYear();
    var mm = String(targetDate.getMonth() + 1).padStart(2, '0');
    var dd = String(targetDate.getDate()).padStart(2, '0');
    return yyyy + '-' + mm + '-' + dd;
}

// Event otomatis: Saat tanggal mulai dipilih atau diubah, tanggal selesai otomatis terisi
$(document).on('change input', '#inputTglMulaiKp', function() {
    var valMulai = $(this).val();
    if (valMulai) {
        var tglSelesaiOtomatis = hitungTanggalSelesaiOtomatis(valMulai, DURASI_KP_BULAN);
        $('#inputTglSelesaiKp').val(tglSelesaiOtomatis);
    }
});

// Jika pada initial load tanggal mulai sudah terisi tetapi tanggal selesai masih kosong
if ($('#inputTglMulaiKp').val() && !$('#inputTglSelesaiKp').val()) {
    $('#inputTglSelesaiKp').val(hitungTanggalSelesaiOtomatis($('#inputTglMulaiKp').val(), DURASI_KP_BULAN));
}

// 5. Logika Step 1: Form Pengajuan Rencana KP & Cetak Persetujuan PA
function kirimPengajuanKp(isCetak, btnElement) {
    var dosenPa = $.trim($('#inputDosenPaKp').val());
    var sks = parseInt($('#inputSksKp').val(), 10) || 0;
    var tglMulai = $('#inputTglMulaiKp').val();
    var tglSelesai = $('#inputTglSelesaiKp').val();
    var tempat = $.trim($('#inputTempatKp').val());
    var alamat = $.trim($('#inputAlamatKp').val());
    var tujuanSurat = $.trim($('#inputTujuanSuratKp').val());

    if (!dosenPa) {
        toast('Silakan pilih Dosen Penasihat Akademik (PA)', 'error');
        $('#inputDosenPaKp').trigger('focus');
        return;
    }
    if (sks < 100) {
        toast('Jumlah SKS yang telah ditempuh minimal 100 SKS', 'error');
        $('#inputSksKp').trigger('focus');
        return;
    }
    if (!tglMulai) {
        toast('Silakan pilih rencana tanggal mulai KP', 'error');
        $('#inputTglMulaiKp').trigger('focus');
        return;
    }
    if (!tglSelesai) {
        toast('Silakan pilih rencana tanggal selesai KP', 'error');
        $('#inputTglSelesaiKp').trigger('focus');
        return;
    }
    if (tglSelesai < tglMulai) {
        toast('Tanggal selesai KP tidak boleh sebelum tanggal mulai', 'error');
        $('#inputTglSelesaiKp').trigger('focus');
        return;
    }
    if (tempat === '') {
        toast('Tempat / instansi Kerja Praktek wajib diisi', 'error');
        $('#inputTempatKp').trigger('focus');
        return;
    }
    if (alamat === '') {
        toast('Alamat tempat KP wajib diisi', 'error');
        $('#inputAlamatKp').trigger('focus');
        return;
    }
    if (tujuanSurat === '') {
        toast('Tujuan surat wajib diisi', 'error');
        $('#inputTujuanSuratKp').trigger('focus');
        return;
    }

    if (btnElement) {
        setButtonLoading(btnElement, true);
    }

    $.ajax({
        url: PROSES_URL,
        method: 'POST',
        data: {
            'simpan-pengajuan-kp': 1,
            'dosen_pa': dosenPa,
            'sks': sks,
            'tanggal_mulai': tglMulai,
            'tanggal_selesai': tglSelesai,
            'tempat': tempat,
            'alamat': alamat,
            'tujuan_surat': tujuanSurat,
            'cetak': isCetak ? 1 : 0
        },
        dataType: 'json',
        success: function(respon) {
            toast(respon.pesan, respon.status ? 'success' : 'error');
            if (respon.status) {
                if (tempat !== '') {
                    $('#badgeInstansiKp').text(tempat);
                }
                if (respon.buka_tab) {
                    window.open(respon.buka_tab, '_blank');
                }
                if (respon.naik_step) {
                    setTimeout(function() {
                        location.reload();
                    }, 1200);
                } else if (btnElement) {
                    setButtonLoading(btnElement, false);
                }
            } else if (btnElement) {
                setButtonLoading(btnElement, false);
            }
        },
        error: function(xhr) {
            var msg = 'Terjadi kesalahan pada server.';
            if (xhr.responseJSON && xhr.responseJSON.pesan) {
                msg = xhr.responseJSON.pesan;
            } else if (xhr.responseText) {
                try {
                    var parsed = JSON.parse(xhr.responseText);
                    if (parsed.pesan) msg = parsed.pesan;
                } catch(e) {
                    var text = xhr.responseText.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim();
                    if (text) msg = text.substring(0, 180);
                }
            }
            toast(msg, 'error');
            if (btnElement) {
                setButtonLoading(btnElement, false);
            }
        }
    });
}

$(document).on('submit', '#formPengajuanKp', function(e) {
    e.preventDefault();
    var btnCetak = document.getElementById('btnSimpanCetakPa');
    var btnSimpan = document.getElementById('btnSimpanKpStep2');
    var activeBtn = btnSimpan || btnCetak;

    if (activeBtn && activeBtn.hasAttribute('disabled')) return;

    // Di Step 1 (btnSimpanCetakPa): simpan & cetak (buka dokumen). Di Step 2 (btnSimpanKpStep2): hanya simpan perubahan data tanpa buka tab baru & tanpa naik step
    var isCetak = (btnCetak !== null && !btnSimpan);
    kirimPengajuanKp(isCetak, activeBtn);
});
</script>