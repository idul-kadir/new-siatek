<?php
require __DIR__ . "/../../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali.']);
    exit;
}

$userLevel = (int)($_SESSION['level'] ?? 0);
$isLevel3  = ($userLevel === 3);
$userProdi = $isLevel3 ? get_prodi_user() : '';

// ================= CEK NOTIFIKASI RINGAN (POLLING) =================
if (isset($_POST['cek-notifikasi']) || isset($_GET['cek-notifikasi'])) {
    // Lepas session lock segera agar request ringan & tidak memblokir navigasi
    session_write_close();

    // Hanya level 3, 4, 6, dan 9 yang memiliki wewenang verifikasi
    if (!in_array($userLevel, [3, 4, 6, 9], true)) {
        echo json_encode([
            'status'       => true,
            'total_berkas' => 0,
            'total_mhs'    => 0,
            'max_id'       => 0,
            'max_file'     => '',
            'fingerprint'  => '0',
        ]);
        exit;
    }

    // 1. Skripsi: tahap verifikasi admin 2, 5, 8, 13, 16, 21 (unggah: 1, 4, 7, 12, 15, 20)
    $joinMhsSkripsi = "";
    $condProdiSkripsi = "";
    if ($isLevel3) {
        if ($userProdi !== '') {
            $joinMhsSkripsi   = "JOIN mahasiswa m ON ts.nim = m.nim";
            $condProdiSkripsi = "AND m.prodi = '" . bersihkan($userProdi) . "'";
        } else {
            $condProdiSkripsi = "AND 1=0";
        }
    }

    $condVerifikasiSkripsi = "ts.status = 'menunggu' 
        AND pbs.progres IN (1, 4, 7, 12, 15, 20)
        AND (
            SELECT proses FROM progres_skripsi 
            WHERE mahasiswa = ts.nim 
            ORDER BY waktu DESC, proses DESC LIMIT 1
        ) = (pbs.progres + 1)";

    $sqlSkripsi = "SELECT 
                       COUNT(ts.id) AS total_berkas,
                       COALESCE(MAX(ts.id), 0) AS max_id,
                       COALESCE(MAX(ts.file), '') AS max_file
                   FROM temp_skripsi ts
                   JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id
                   $joinMhsSkripsi
                   WHERE $condVerifikasiSkripsi $condProdiSkripsi";

    $qSkripsi = query($sqlSkripsi);
    $rowSkripsi = ($qSkripsi && mysqli_num_rows($qSkripsi) > 0)
        ? mysqli_fetch_assoc($qSkripsi)
        : ['total_berkas' => 0, 'max_id' => 0, 'max_file' => ''];

    // 2. KP: tahap verifikasi admin 3, 5, 9, 11, 15 (unggah: 2, 4, 8, 10, 14)
    $joinMhsKp = "";
    $condProdiKp = "";
    if ($isLevel3) {
        if ($userProdi !== '') {
            $joinMhsKp   = "JOIN mahasiswa m ON tk.nim = m.nim";
            $condProdiKp = "AND m.prodi = '" . bersihkan($userProdi) . "'";
        } else {
            $condProdiKp = "AND 1=0";
        }
    }

    $condVerifikasiKp = "tk.status = 'menunggu' 
        AND pbk.progres IN (2, 4, 8, 10, 14)
        AND (
            SELECT proses FROM progres_kp 
            WHERE mahasiswa = tk.nim 
            ORDER BY waktu DESC, proses DESC LIMIT 1
        ) = (pbk.progres + 1)";

    $sqlKp = "SELECT 
                  COUNT(tk.id) AS total_berkas,
                  COALESCE(MAX(tk.id), 0) AS max_id,
                  COALESCE(MAX(tk.file), '') AS max_file
              FROM temp_kp tk
              JOIN persyaratan_berkas_kp pbk ON tk.id_persyaratan = pbk.id
              $joinMhsKp
              WHERE $condVerifikasiKp $condProdiKp";

    $qKp = query($sqlKp);
    $rowKp = ($qKp && mysqli_num_rows($qKp) > 0)
        ? mysqli_fetch_assoc($qKp)
        : ['total_berkas' => 0, 'max_id' => 0, 'max_file' => ''];

    // Distinct total mahasiswa (gabungan Skripsi dan KP)
    $sqlDistinctMhs = "SELECT COUNT(DISTINCT nim) AS total_mhs FROM (
        SELECT ts.nim 
        FROM temp_skripsi ts
        JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id
        $joinMhsSkripsi
        WHERE $condVerifikasiSkripsi $condProdiSkripsi
        UNION
        SELECT tk.nim 
        FROM temp_kp tk
        JOIN persyaratan_berkas_kp pbk ON tk.id_persyaratan = pbk.id
        $joinMhsKp
        WHERE $condVerifikasiKp $condProdiKp
    ) AS gabungan_antrian";

    $qDistinctMhs = query($sqlDistinctMhs);
    $rowDistinctMhs = ($qDistinctMhs && mysqli_num_rows($qDistinctMhs) > 0)
        ? mysqli_fetch_assoc($qDistinctMhs)
        : ['total_mhs' => 0];

    $totalBerkas = (int)($rowSkripsi['total_berkas'] ?? 0) + (int)($rowKp['total_berkas'] ?? 0);
    $totalMhs    = (int)($rowDistinctMhs['total_mhs'] ?? 0);
    $maxId       = max((int)($rowSkripsi['max_id'] ?? 0), (int)($rowKp['max_id'] ?? 0));
    $maxFile     = max((string)($rowSkripsi['max_file'] ?? ''), (string)($rowKp['max_file'] ?? ''));
    $fingerprint = $totalBerkas > 0 ? md5($totalBerkas . '|' . $totalMhs . '|' . $maxId . '|' . $maxFile) : '0';

    echo json_encode([
        'status'       => true,
        'total_berkas' => $totalBerkas,
        'total_mhs'    => $totalMhs,
        'max_id'       => $maxId,
        'max_file'     => $maxFile,
        'fingerprint'  => $fingerprint,
    ]);
    exit;
}

// ================= AMBIL ANTRIAN VERIFIKASI =================
if (isset($_POST['ambil-tabel']) || isset($_POST['ambil-antrian'])) {
    $cari    = bersihkan($_POST['cari'] ?? '');
    $status  = bersihkan($_POST['status'] ?? '');
    $prodi   = bersihkan($_POST['prodi'] ?? '');
    $layanan = strtolower(trim(bersihkan($_POST['layanan'] ?? '')));
    
    // Filter Prodi
    $filterProdiMhs = "";
    if ($isLevel3) {
        if ($userProdi !== '') {
            $filterProdiMhs = "AND m.prodi = '" . bersihkan($userProdi) . "'";
        } else {
            $filterProdiMhs = "AND 1=0";
        }
    } elseif ($prodi !== '') {
        $filterProdiMhs = "AND m.prodi = '$prodi'";
    }

    // Filter Pencarian NIM/Nama
    $filterCari = "";
    if ($cari !== '') {
        $filterCari = "AND (ts.nim LIKE '%$cari%' OR m.nama LIKE '%$cari%')";
        $filterCariKp = "AND (tk.nim LIKE '%$cari%' OR m.nama LIKE '%$cari%')";
    } else {
        $filterCariKp = "";
    }

    // Filter Status Berkas
    $filterStatusSkripsi = $status !== '' ? "AND ts.status = '$status'" : "";
    $filterStatusKp      = $status !== '' ? "AND tk.status = '$status'" : "";

    $queryParts = [];

    // Query Antrian Skripsi
    if ($layanan === '' || $layanan === 'semua' || $layanan === 'skripsi') {
        $queryParts[] = "SELECT 'skripsi' AS tipe,
                               ts.nim, m.nama AS nama_mahasiswa, m.prodi, p.nama AS nama_prodi,
                               MIN(ts.id) AS min_id,
                               COUNT(ts.id) AS total_berkas,
                               SUM(CASE WHEN ts.status = 'menunggu' THEN 1 ELSE 0 END) AS total_menunggu,
                               SUM(CASE WHEN ts.status = 'diterima' THEN 1 ELSE 0 END) AS total_diterima,
                               SUM(CASE WHEN ts.status = 'ditolak' THEN 1 ELSE 0 END) AS total_ditolak
                        FROM temp_skripsi ts 
                        JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id 
                        LEFT JOIN mahasiswa m ON ts.nim = m.nim
                        LEFT JOIN prodi p ON m.prodi = p.kode
                        WHERE pbs.progres IN (1, 4, 7, 12, 15, 20)
                          AND (
                              SELECT proses FROM progres_skripsi 
                              WHERE mahasiswa = ts.nim 
                              ORDER BY waktu DESC, proses DESC LIMIT 1
                          ) = (pbs.progres + 1)
                          $filterProdiMhs
                          $filterCari
                          $filterStatusSkripsi
                        GROUP BY ts.nim, m.nama, m.prodi, p.nama";
    }

    // Query Antrian KP
    if ($layanan === '' || $layanan === 'semua' || $layanan === 'kp') {
        $queryParts[] = "SELECT 'kp' AS tipe,
                               tk.nim, m.nama AS nama_mahasiswa, m.prodi, p.nama AS nama_prodi,
                               MIN(tk.id) AS min_id,
                               COUNT(tk.id) AS total_berkas,
                               SUM(CASE WHEN tk.status = 'menunggu' THEN 1 ELSE 0 END) AS total_menunggu,
                               SUM(CASE WHEN tk.status = 'diterima' THEN 1 ELSE 0 END) AS total_diterima,
                               SUM(CASE WHEN tk.status = 'ditolak' THEN 1 ELSE 0 END) AS total_ditolak
                        FROM temp_kp tk 
                        JOIN persyaratan_berkas_kp pbk ON tk.id_persyaratan = pbk.id 
                        LEFT JOIN mahasiswa m ON tk.nim = m.nim
                        LEFT JOIN prodi p ON m.prodi = p.kode
                        WHERE pbk.progres IN (2, 4, 8, 10, 14)
                          AND (
                              SELECT proses FROM progres_kp 
                              WHERE mahasiswa = tk.nim 
                              ORDER BY waktu DESC, proses DESC LIMIT 1
                          ) = (pbk.progres + 1)
                          $filterProdiMhs
                          $filterCariKp
                          $filterStatusKp
                        GROUP BY tk.nim, m.nama, m.prodi, p.nama";
    }

    if (empty($queryParts)) {
        $sqlMhs = "SELECT 'skripsi' AS tipe, '' AS nim WHERE 1=0";
    } elseif (count($queryParts) === 1) {
        $sqlMhs = $queryParts[0] . " ORDER BY total_menunggu DESC, min_id ASC";
    } else {
        $sqlMhs = "SELECT * FROM (" . implode(" UNION ALL ", $queryParts) . ") AS antrian_gabungan ORDER BY total_menunggu DESC, min_id ASC";
    }

    $qMhs = query($sqlMhs);
    
    ob_start();
    if ($qMhs && mysqli_num_rows($qMhs) > 0) {
        $noAntrian = 1;
        ?>
        <div class="space-y-4">
        <?php
        while ($mhs = mysqli_fetch_assoc($qMhs)) {
            $tipeMhs  = $mhs['tipe']; // 'skripsi' atau 'kp'
            $nimMhs   = $mhs['nim'];
            $namaMhs  = !empty($mhs['nama_mahasiswa']) ? $mhs['nama_mahasiswa'] : '-';
            $prodiMhs = !empty($mhs['nama_prodi']) ? $mhs['nama_prodi'] : ($mhs['prodi'] ?? '-');

            if ($tipeMhs === 'kp') {
                $sqlBerkas = "SELECT tk.id, tk.nim, tk.file, tk.status, tk.ket, pbk.nama_berkas, pbk.progres AS progres_unggah, sop.no AS sop_no, sop.status AS sop_ket, f.nama_file 
                              FROM temp_kp tk 
                              JOIN persyaratan_berkas_kp pbk ON tk.id_persyaratan = pbk.id 
                              LEFT JOIN sop_kp sop ON sop.no = (pbk.progres + 1)
                              LEFT JOIN files f ON tk.file = f.id
                              WHERE tk.nim = '$nimMhs' AND pbk.progres IN (2, 4, 8, 10, 14) " . ($status !== '' ? "AND tk.status = '$status'" : "") . " 
                              ORDER BY pbk.id ASC";
            } else {
                $sqlBerkas = "SELECT ts.id, ts.nim, ts.file, ts.status, ts.ket, pbs.nama_berkas, pbs.progres AS progres_unggah, sop.no AS sop_no, sop.keterangan AS sop_ket, f.nama_file 
                              FROM temp_skripsi ts 
                              JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id 
                              LEFT JOIN sop_skripsi sop ON sop.no = (pbs.progres + 1)
                              LEFT JOIN files f ON ts.file = f.id
                              WHERE ts.nim = '$nimMhs' AND pbs.progres IN (1, 4, 7, 12, 15, 20) " . ($status !== '' ? "AND ts.status = '$status'" : "") . " 
                              ORDER BY pbs.id ASC";
            }
            $qBerkas = query($sqlBerkas);
            ?>
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden transition hover:border-slate-300">
                <!-- Header Card Antrian -->
                <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50/80 border-b border-slate-200 px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg <?= $tipeMhs === 'kp' ? 'bg-blue-600' : 'bg-orange-500' ?> text-sm font-bold text-white shadow-sm">
                            #<?= $noAntrian ?>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-800 text-base leading-snug"><?= tampil($namaMhs) ?></h3>
                                <?php if ($tipeMhs === 'kp'): ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 text-blue-700 px-2 py-0.5 text-[10px] font-bold">
                                        <i class="fas fa-briefcase text-[9px]"></i> Kerja Praktek
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 text-orange-700 px-2 py-0.5 text-[10px] font-bold">
                                        <i class="fas fa-graduation-cap text-[9px]"></i> Skripsi
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                <span class="font-medium text-slate-700">NIM: <?= tampil($nimMhs) ?></span>
                                <span>&bull;</span>
                                <span><?= tampil($prodiMhs) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <?php if ($mhs['total_menunggu'] > 0): ?>
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                                <i class="fas fa-clock text-[10px]"></i> <?= $mhs['total_menunggu'] ?> Menunggu
                            </span>
                        <?php endif; ?>
                        <?php if ($mhs['total_diterima'] > 0): ?>
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                                <i class="fas fa-check-circle text-[10px]"></i> <?= $mhs['total_diterima'] ?> Diterima
                            </span>
                        <?php endif; ?>
                        <?php if ($mhs['total_ditolak'] > 0): ?>
                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-800">
                                <i class="fas fa-times-circle text-[10px]"></i> <?= $mhs['total_ditolak'] ?> Ditolak
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Body List Berkas -->
                <div class="p-4 divide-y divide-slate-100">
                    <?php
                    if ($qBerkas && mysqli_num_rows($qBerkas) > 0) {
                        while ($b = mysqli_fetch_assoc($qBerkas)) {
                            $fileUrl = base_url() . '/file/' . tampil($b['nama_file']);
                            $st = strtolower(trim($b['status'] ?? 'menunggu'));
                            $badgeSt = $st === 'diterima' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($st === 'ditolak' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200');
                            ?>
                            <div class="py-3 first:pt-0 last:pb-0 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-start gap-3 flex-1 min-w-[250px]">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg <?= $tipeMhs === 'kp' ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-500' ?> mt-0.5">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-semibold text-slate-800 text-sm"><?= tampil($b['nama_berkas']) ?></h4>
                                            <span class="inline-flex items-center rounded border px-2 py-0.5 text-[11px] font-medium <?= $badgeSt ?>">
                                                <?= ucfirst(tampil($st)) ?>
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            <span class="font-semibold text-slate-600">Tahap <?= tampil($b['sop_no']) ?>:</span> <?= tampil($b['sop_ket']) ?>
                                        </p>
                                        <?php if (!empty($b['ket'])): ?>
                                            <p class="text-xs text-slate-400 italic mt-0.5">Catatan: <?= tampil($b['ket']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <?php if ($st === 'menunggu'): ?>
                                        <button type="button" class="btnPratinjau rounded-lg <?= $tipeMhs === 'kp' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-orange-500 hover:bg-orange-600' ?> text-white px-3.5 py-1.5 text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5" 
                                                data-id="<?= $b['id'] ?>"
                                                data-tipe="<?= $tipeMhs ?>"
                                                data-url="<?= $fileUrl ?>"
                                                data-namaberkas="<?= tampil($b['nama_berkas']) ?>"
                                                data-mhs="<?= tampil($namaMhs) ?> (<?= tampil($nimMhs) ?>)"
                                                data-sop="Tahap <?= tampil($b['sop_no']) ?>: <?= tampil($b['sop_ket']) ?>"
                                                data-status="<?= $st ?>"
                                                data-badgestyle="<?= $badgeSt ?>"
                                                data-ket="<?= tampil($b['ket'] ?? '') ?>">
                                            <i class="fa-solid fa-magnifying-glass"></i> <span>Pratinjau &amp; Verifikasi</span>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btnPratinjauStatus rounded-lg px-3.5 py-1.5 text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5 <?= $st === 'diterima' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-rose-100 text-rose-700 hover:bg-rose-200' ?>"
                                                data-id="<?= $b['id'] ?>"
                                                data-tipe="<?= $tipeMhs ?>"
                                                data-url="<?= $fileUrl ?>"
                                                data-namaberkas="<?= tampil($b['nama_berkas']) ?>"
                                                data-mhs="<?= tampil($namaMhs) ?> (<?= tampil($nimMhs) ?>)"
                                                data-sop="Tahap <?= tampil($b['sop_no']) ?>: <?= tampil($b['sop_ket']) ?>"
                                                data-status="<?= $st ?>"
                                                data-badgestyle="<?= $badgeSt ?>"
                                                data-ket="<?= tampil($b['ket'] ?? '') ?>">
                                            <i class="fa-solid <?= $st === 'diterima' ? 'fa-circle-check' : 'fa-circle-xmark' ?>"></i> 
                                            <span><?= $st === 'diterima' ? 'Diterima' : 'Ditolak' ?></span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <?php
            $noAntrian++;
        }
        ?>
        </div>
        <?php
    } else {
        ?>
        <div class="py-16 text-center bg-white rounded-xl border border-slate-200 shadow-sm">
            <i class="fas fa-user-check text-4xl text-slate-300"></i>
            <p class="mt-3 font-medium text-slate-600">Tidak ada antrian verifikasi berkas</p>
            <p class="text-xs text-slate-400">Semua berkas mahasiswa telah diproses atau tidak sesuai filter.</p>
        </div>
        <?php
    }
    $html = ob_get_clean();
    echo json_encode(['status' => true, 'data' => $html]);
    exit;
}

// ================= VERIFIKASI INDIVIDUAL BERKAS =================
if (isset($_POST['verifikasi-berkas'])) {
    $id   = bersihkan($_POST['id']);
    $aksi = bersihkan($_POST['aksi']);
    $ket  = bersihkan($_POST['ket'] ?? '');
    $tipe = strtolower(trim(bersihkan($_POST['tipe'] ?? 'skripsi')));

    if (!in_array($aksi, ['diterima', 'ditolak'])) {
        echo json_encode(['status' => false, 'pesan' => 'Aksi tidak valid']);
        exit;
    }

    if ($aksi === 'ditolak' && empty($ket)) {
        echo json_encode(['status' => false, 'pesan' => 'Alasan penolakan wajib diisi']);
        exit;
    } elseif ($aksi === 'diterima' && empty($ket)) {
        $ket = "Berkas telah diverifikasi dan diterima.";
    }

    // ------------------ CASE A: KERJA PRAKTEK (KP) ------------------
    if ($tipe === 'kp') {
        // Proteksi level 3
        if ($isLevel3 && $userProdi !== '') {
            $cekMhs = mysqli_fetch_assoc(query("SELECT m.prodi FROM temp_kp tk JOIN mahasiswa m ON tk.nim = m.nim WHERE tk.id = '$id' LIMIT 1"));
            if (!$cekMhs || trim((string)$cekMhs['prodi']) !== $userProdi) {
                echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: mahasiswa bukan dari program studi Anda.']);
                exit;
            }
        }

        $q = query("UPDATE temp_kp SET status = '$aksi', ket = '$ket' WHERE id = '$id'");

        if ($q) {
            $qBerkasCurrent = query("SELECT nim, id_persyaratan FROM temp_kp WHERE id = '$id' LIMIT 1");
            if ($qBerkasCurrent && mysqli_num_rows($qBerkasCurrent) > 0) {
                $rowB = mysqli_fetch_assoc($qBerkasCurrent);
                $nimMhs = $rowB['nim'];
                $idPersyaratan = $rowB['id_persyaratan'];

                $qProg = query("SELECT progres FROM persyaratan_berkas_kp WHERE id = '$idPersyaratan' LIMIT 1");
                if ($qProg && mysqli_num_rows($qProg) > 0) {
                    $progresUnggah = (int)mysqli_fetch_assoc($qProg)['progres'];
                    $tahapAdmin = $progresUnggah + 1; // Tahap SOP KP: 3, 5, 9, 11, 15

                    // Total berkas wajib di tahap unggah ini
                    $qTotal = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_kp WHERE progres = '$progresUnggah'");
                    $totalWajib = ($qTotal && mysqli_num_rows($qTotal) > 0) ? (int)mysqli_fetch_assoc($qTotal)['total'] : 0;

                    // Total berkas yang sudah diterima
                    $qDiterima = query("SELECT COUNT(*) AS total FROM temp_kp tk 
                                        JOIN persyaratan_berkas_kp pbk ON tk.id_persyaratan = pbk.id 
                                        WHERE tk.nim = '$nimMhs' AND pbk.progres = '$progresUnggah' AND tk.status = 'diterima'");
                    $totalDiterima = ($qDiterima && mysqli_num_rows($qDiterima) > 0) ? (int)mysqli_fetch_assoc($qDiterima)['total'] : 0;

                    // Total berkas yang sudah diproses (diterima + ditolak)
                    $qSelesai = query("SELECT COUNT(*) AS total FROM temp_kp tk 
                                       JOIN persyaratan_berkas_kp pbk ON tk.id_persyaratan = pbk.id 
                                       WHERE tk.nim = '$nimMhs' AND pbk.progres = '$progresUnggah' AND tk.status IN ('diterima', 'ditolak')");
                    $totalSelesai = ($qSelesai && mysqli_num_rows($qSelesai) > 0) ? (int)mysqli_fetch_assoc($qSelesai)['total'] : 0;

                    // Total berkas yang ditolak
                    $qDitolak = query("SELECT COUNT(*) AS total FROM temp_kp tk 
                                       JOIN persyaratan_berkas_kp pbk ON tk.id_persyaratan = pbk.id 
                                       WHERE tk.nim = '$nimMhs' AND pbk.progres = '$progresUnggah' AND tk.status = 'ditolak'");
                    $totalDitolak = ($qDitolak && mysqli_num_rows($qDitolak) > 0) ? (int)mysqli_fetch_assoc($qDitolak)['total'] : 0;

                    if ($totalWajib > 0 && $totalSelesai >= $totalWajib) {
                        if ($totalDitolak > 0) {
                            // Mundurkan progres KP ke tahap mahasiswa unggah
                            $tahapSebelumnya = $tahapAdmin - 1;
                            $lastProsesDb = (int)get_progres_kp($nimMhs);
                            if ($lastProsesDb !== $tahapSebelumnya) {
                                set_progres_kp($nimMhs, $tahapSebelumnya);
                            }
                            echo json_encode([
                                'status' => true,
                                'pesan'  => 'Berkas ditolak. Mahasiswa dikembalikan ke tahap unggah berkas (' . $tahapSebelumnya . ')'
                            ]);
                            exit;
                        } elseif ($totalDiterima >= $totalWajib) {
                            // Pindahkan berkas yang diterima ke berkas_kp (persis seperti skripsi)
                            $qGetTemp = query("SELECT tk.id_persyaratan, tk.nim, tk.file FROM temp_kp tk 
                                               JOIN persyaratan_berkas_kp pbk ON tk.id_persyaratan = pbk.id 
                                               WHERE tk.nim = '$nimMhs' AND pbk.progres = '$progresUnggah' AND tk.status = 'diterima'");
                            if ($qGetTemp && mysqli_num_rows($qGetTemp) > 0) {
                                while ($rowT = mysqli_fetch_assoc($qGetTemp)) {
                                    $idP  = bersihkan($rowT['id_persyaratan']);
                                    $nMhs = bersihkan($rowT['nim']);
                                    $fId  = bersihkan($rowT['file']);

                                    $qCekAda = query("SELECT id FROM berkas_kp WHERE nim = '$nMhs' AND id_persyaratan = '$idP'");
                                    if ($qCekAda && mysqli_num_rows($qCekAda) > 0) {
                                        query("UPDATE berkas_kp SET file = '$fId' WHERE nim = '$nMhs' AND id_persyaratan = '$idP'");
                                    } else {
                                        query("INSERT INTO berkas_kp (id_persyaratan, nim, file) VALUES ('$idP', '$nMhs', '$fId')");
                                    }
                                }
                                query("DELETE tk FROM temp_kp tk 
                                       JOIN persyaratan_berkas_kp pbk ON tk.id_persyaratan = pbk.id 
                                       WHERE tk.nim = '$nimMhs' AND pbk.progres = '$progresUnggah'");
                            }

                            // Catat verifikasi selesai & majukan tahapan mahasiswa
                            $waktuNow = time();
                            $qCekAdmin = query("SELECT proses FROM progres_kp WHERE mahasiswa = '$nimMhs' AND proses = '$tahapAdmin'");
                            if (!$qCekAdmin || mysqli_num_rows($qCekAdmin) == 0) {
                                set_progres_kp($nimMhs, $tahapAdmin, $waktuNow);
                            }

                            $nextProsesAfterAdmin = $tahapAdmin + 1;
                            $qCekProgres = query("SELECT proses FROM progres_kp WHERE mahasiswa = '$nimMhs' AND proses = '$nextProsesAfterAdmin'");
                            if (!$qCekProgres || mysqli_num_rows($qCekProgres) == 0) {
                                set_progres_kp($nimMhs, $nextProsesAfterAdmin, $waktuNow + 1);
                            }
                        }
                    }
                }
            }

            echo json_encode(['status' => true, 'pesan' => 'Status berkas KP berhasil diperbarui']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui status berkas KP']);
        }
        exit;
    }

    // ------------------ CASE B: SKRIPSI ------------------
    // Proteksi level 3
    if ($isLevel3 && $userProdi !== '') {
        $cekMhs = mysqli_fetch_assoc(query("SELECT m.prodi FROM temp_skripsi ts JOIN mahasiswa m ON ts.nim = m.nim WHERE ts.id = '$id' LIMIT 1"));
        if (!$cekMhs || trim((string)$cekMhs['prodi']) !== $userProdi) {
            echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: mahasiswa bukan dari program studi Anda.']);
            exit;
        }
    }

    $q = query("UPDATE temp_skripsi SET status = '$aksi', ket = '$ket' WHERE id = '$id'");

    if ($q) {
        $qBerkasCurrent = query("SELECT nim, id_persyaratan FROM temp_skripsi WHERE id = '$id' LIMIT 1");
        if ($qBerkasCurrent && mysqli_num_rows($qBerkasCurrent) > 0) {
            $rowB = mysqli_fetch_assoc($qBerkasCurrent);
            $nimMhs = $rowB['nim'];
            $idPersyaratan = $rowB['id_persyaratan'];

            $qProg = query("SELECT progres FROM persyaratan_berkas_skripsi WHERE id = '$idPersyaratan' LIMIT 1");
            if ($qProg && mysqli_num_rows($qProg) > 0) {
                $progresUnggah = (int)mysqli_fetch_assoc($qProg)['progres'];
                $tahapAdmin = $progresUnggah + 1;

                $qTotal = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_skripsi WHERE progres = '$progresUnggah'");
                $totalWajib = ($qTotal && mysqli_num_rows($qTotal) > 0) ? (int)mysqli_fetch_assoc($qTotal)['total'] : 0;

                $qDiterima = query("SELECT COUNT(*) AS total FROM temp_skripsi ts 
                                    JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id 
                                    WHERE ts.nim = '$nimMhs' AND pbs.progres = '$progresUnggah' AND ts.status = 'diterima'");
                $totalDiterima = ($qDiterima && mysqli_num_rows($qDiterima) > 0) ? (int)mysqli_fetch_assoc($qDiterima)['total'] : 0;

                $qSelesaiVerifikasi = query("SELECT COUNT(*) AS total FROM temp_skripsi ts 
                                             JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id 
                                             WHERE ts.nim = '$nimMhs' AND pbs.progres = '$progresUnggah' AND ts.status IN ('diterima', 'ditolak')");
                $totalSelesaiVerifikasi = ($qSelesaiVerifikasi && mysqli_num_rows($qSelesaiVerifikasi) > 0) ? (int)mysqli_fetch_assoc($qSelesaiVerifikasi)['total'] : 0;

                $qDitolak = query("SELECT COUNT(*) AS total FROM temp_skripsi ts 
                                   JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id 
                                   WHERE ts.nim = '$nimMhs' AND pbs.progres = '$progresUnggah' AND ts.status = 'ditolak'");
                $totalDitolak = ($qDitolak && mysqli_num_rows($qDitolak) > 0) ? (int)mysqli_fetch_assoc($qDitolak)['total'] : 0;

                if ($totalWajib > 0 && $totalSelesaiVerifikasi >= $totalWajib) {
                    if ($totalDitolak > 0) {
                        $tahapSebelumnya = $tahapAdmin - 1;
                        $waktu = time();

                        $qLast = query("SELECT proses FROM progres_skripsi WHERE mahasiswa = '$nimMhs' ORDER BY waktu DESC LIMIT 1");
                        $lastProsesDb = ($qLast && mysqli_num_rows($qLast) > 0) ? (int)mysqli_fetch_assoc($qLast)['proses'] : 0;

                        if ($lastProsesDb !== $tahapSebelumnya) {
                            query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ('$waktu', '$tahapSebelumnya', '$nimMhs')");
                        }
                    } elseif ($totalDiterima >= $totalWajib) {
                        $qGetTemp = query("SELECT ts.id_persyaratan, ts.nim, ts.file FROM temp_skripsi ts 
                                           JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id 
                                           WHERE ts.nim = '$nimMhs' AND pbs.progres = '$progresUnggah' AND ts.status = 'diterima'");
                        if ($qGetTemp && mysqli_num_rows($qGetTemp) > 0) {
                            while ($rowT = mysqli_fetch_assoc($qGetTemp)) {
                                $idP  = bersihkan($rowT['id_persyaratan']);
                                $nMhs = bersihkan($rowT['nim']);
                                $fId  = bersihkan($rowT['file']);

                                $qCekAda = query("SELECT id FROM berkas_skripsi WHERE nim = '$nMhs' AND id_persyaratan = '$idP'");
                                if ($qCekAda && mysqli_num_rows($qCekAda) > 0) {
                                    query("UPDATE berkas_skripsi SET file = '$fId' WHERE nim = '$nMhs' AND id_persyaratan = '$idP'");
                                } else {
                                    query("INSERT INTO berkas_skripsi (id_persyaratan, nim, file) VALUES ('$idP', '$nMhs', '$fId')");
                                }
                            }
                            query("DELETE ts FROM temp_skripsi ts 
                                   JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id 
                                   WHERE ts.nim = '$nimMhs' AND pbs.progres = '$progresUnggah'");
                        }

                        $waktu = time();
                        $qCekProgresAdmin = query("SELECT proses FROM progres_skripsi WHERE mahasiswa = '$nimMhs' AND proses = '$tahapAdmin'");
                        if (!$qCekProgresAdmin || mysqli_num_rows($qCekProgresAdmin) == 0) {
                            query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ('$waktu', '$tahapAdmin', '$nimMhs')");
                        }

                        $nextProsesAfterAdmin = $tahapAdmin + 1;
                        $waktu = time() + 1;

                        $qCekProgres = query("SELECT proses FROM progres_skripsi WHERE mahasiswa = '$nimMhs' AND proses = '$nextProsesAfterAdmin'");
                        if (!$qCekProgres || mysqli_num_rows($qCekProgres) == 0) {
                            query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ('$waktu', '$nextProsesAfterAdmin', '$nimMhs')");
                        }
                    }
                }
            }
        }

        echo json_encode(['status' => true, 'pesan' => 'Status berkas berhasil diperbarui']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui status berkas']);
    }
    exit;
}

// ================= VERIFIKASI BATCH (SEMUA BERKAS MAHASISWA) =================
if (isset($_POST['verifikasi-semua-mhs'])) {
    $nim  = bersihkan($_POST['nim']);
    $tipe = strtolower(trim(bersihkan($_POST['tipe'] ?? '')));

    if (empty($nim)) {
        echo json_encode(['status' => false, 'pesan' => 'NIM tidak valid']);
        exit;
    }

    if ($isLevel3 && $userProdi !== '') {
        $cekMhs = mysqli_fetch_assoc(query("SELECT prodi FROM mahasiswa WHERE nim = '$nim' LIMIT 1"));
        if (!$cekMhs || trim((string)$cekMhs['prodi']) !== $userProdi) {
            echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: mahasiswa bukan dari program studi Anda.']);
            exit;
        }
    }

    $ket = "Berkas diverifikasi dan diterima sekaligus.";

    if ($tipe === 'kp') {
        $q = query("UPDATE temp_kp SET status = 'diterima', ket = '$ket' WHERE nim = '$nim' AND status = 'menunggu'");
    } elseif ($tipe === 'skripsi') {
        $q = query("UPDATE temp_skripsi SET status = 'diterima', ket = '$ket' WHERE nim = '$nim' AND status = 'menunggu'");
    } else {
        $q1 = query("UPDATE temp_skripsi SET status = 'diterima', ket = '$ket' WHERE nim = '$nim' AND status = 'menunggu'");
        $q2 = query("UPDATE temp_kp SET status = 'diterima', ket = '$ket' WHERE nim = '$nim' AND status = 'menunggu'");
        $q = ($q1 || $q2);
    }

    if ($q) {
        echo json_encode(['status' => true, 'pesan' => 'Semua berkas mahasiswa berhasil diterima']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal memproses berkas mahasiswa']);
    }
    exit;
}

echo json_encode(['status' => false, 'pesan' => 'Permintaan tidak valid']);
