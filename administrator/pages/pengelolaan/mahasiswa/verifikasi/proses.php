<?php
require __DIR__ . "/../../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali.']);
    exit;
}

// ================= AMBIL ANTRIAN VERIFIKASI =================
if (isset($_POST['ambil-tabel']) || isset($_POST['ambil-antrian'])) {
    $cari   = bersihkan($_POST['cari'] ?? '');
    $status = bersihkan($_POST['status'] ?? '');
    
    $where = ["1=1"];
    if ($status !== '') {
        $where[] = "ts.status = '$status'";
    }
    if ($cari !== '') {
        $where[] = "(ts.nim LIKE '%$cari%' OR m.nama LIKE '%$cari%')";
    }

    // Hanya progres yang merupakan tugas "admin" (verifikasi) yang muncul
    // Tahap verifikasi adalah n+1 dari tahap unggah oleh mahasiswa (contoh: unggah tahap 1, verifikasi di tahap 2)
    // Di temp_skripsi (dan persyaratan_berkas_skripsi) id_persyaratan-nya terhubung ke progres tempat MAHASISWA mengunggahnya.
    // Misal: Mahasiswa unggah KRS di progres 1. Maka di tabel pbs, progres = 1.
    // Tapi tahap verifikasi yang seharusnya menampilkan itu adalah tahap 2.
    // Karena halaman ini MENAMPILKAN apa yang harus diverifikasi admin, maka kita filter `pbs.progres`
    // berdasarkan TAHAP UNGGAH-nya: 1, 4, 7, 12, 15, 20
    $where[] = "pbs.progres IN (1, 4, 7, 12, 15, 20)";

    // Filter: hanya tampilkan mahasiswa yang PROGRES SKRIPSI TERAKHIRNYA sesuai dengan tahap verifikasi
    // Progres verifikasi = progres unggah + 1
    // Filter ini memastikan bahwa jika mahasiswa sudah melangkah ke progres lain (misal progres 3), 
    // berkas verifikasi progres 2 mereka tidak lagi muncul dalam daftar (sudah "berlalu").
    $where[] = "(
        SELECT proses FROM progres_skripsi 
        WHERE mahasiswa = ts.nim 
        ORDER BY waktu DESC LIMIT 1
    ) = (pbs.progres + 1)";

    $whereSql = implode(" AND ", $where);

    // Ambil data mahasiswa yang masuk antrian verifikasi
    $sqlMhs = "SELECT ts.nim, m.nama AS nama_mahasiswa, m.prodi, p.nama AS nama_prodi,
                      MIN(ts.id) AS min_id,
                      COUNT(ts.id) AS total_berkas,
                      SUM(CASE WHEN ts.status = 'menunggu' THEN 1 ELSE 0 END) AS total_menunggu,
                      SUM(CASE WHEN ts.status = 'diterima' THEN 1 ELSE 0 END) AS total_diterima,
                      SUM(CASE WHEN ts.status = 'ditolak' THEN 1 ELSE 0 END) AS total_ditolak
               FROM temp_skripsi ts 
               JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id 
               LEFT JOIN mahasiswa m ON ts.nim = m.nim
               LEFT JOIN prodi p ON m.prodi = p.kode
               WHERE $whereSql 
               GROUP BY ts.nim, m.nama, m.prodi, p.nama 
               ORDER BY total_menunggu DESC, min_id ASC";
            
    $qMhs = query($sqlMhs);
    
    ob_start();
    if ($qMhs && mysqli_num_rows($qMhs) > 0) {
        $noAntrian = 1;
        ?>
        <div class="space-y-4">
        <?php
        while ($mhs = mysqli_fetch_assoc($qMhs)) {
            $nimMhs   = $mhs['nim'];
            $namaMhs  = !empty($mhs['nama_mahasiswa']) ? $mhs['nama_mahasiswa'] : '-';
            $prodiMhs = !empty($mhs['nama_prodi']) ? $mhs['nama_prodi'] : ($mhs['prodi'] ?? '-');

            // Ambil detail berkas mahasiswa ini (hanya yang masuk dalam progres admin)
            $sqlBerkas = "SELECT ts.id, ts.nim, ts.file, ts.status, ts.ket, pbs.nama_berkas, pbs.progres AS progres_unggah, sop.no AS sop_no, sop.keterangan AS sop_ket, f.nama_file 
                          FROM temp_skripsi ts 
                          JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id 
                          LEFT JOIN sop_skripsi sop ON sop.no = (pbs.progres + 1)
                          LEFT JOIN files f ON ts.file = f.id
                          WHERE ts.nim = '$nimMhs' AND pbs.progres IN (1, 4, 7, 12, 15, 20) " . ($status !== '' ? "AND ts.status = '$status'" : "") . " 
                          ORDER BY pbs.id ASC";
            $qBerkas = query($sqlBerkas);
            ?>
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden transition hover:border-slate-300">
                <!-- Header Card Antrian -->
                <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50/80 border-b border-slate-200 px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">
                            #<?= $noAntrian ?>
                        </span>
                        <div>
                            <h3 class="font-bold text-slate-800 text-base leading-snug"><?= tampil($namaMhs) ?></h3>
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
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 mt-0.5">
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
                                        <button type="button" class="btnPratinjau rounded-lg bg-orange-500 hover:bg-orange-600 text-white px-3.5 py-1.5 text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5" 
                                                data-id="<?= $b['id'] ?>"
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

    $q = query("UPDATE temp_skripsi SET status = '$aksi', ket = '$ket' WHERE id = '$id'");

    if ($q) {
        // Cek jika seluruh berkas persyaratan pada tahap tersebut sudah di-ACC/diterima oleh Admin
        $qBerkasCurrent = query("SELECT nim, id_persyaratan FROM temp_skripsi WHERE id = '$id' LIMIT 1");
        if ($qBerkasCurrent && mysqli_num_rows($qBerkasCurrent) > 0) {
            $rowB = mysqli_fetch_assoc($qBerkasCurrent);
            $nimMhs = $rowB['nim'];
            $idPersyaratan = $rowB['id_persyaratan'];

            // Cari tahapan progres unggah mahasiswa
            $qProg = query("SELECT progres FROM persyaratan_berkas_skripsi WHERE id = '$idPersyaratan' LIMIT 1");
            if ($qProg && mysqli_num_rows($qProg) > 0) {
                $progresUnggah = (int)mysqli_fetch_assoc($qProg)['progres'];
                $tahapAdmin = $progresUnggah + 1; // Progres verifikasi admin

                // Hitung total berkas wajib di tahap ini
                $qTotal = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_skripsi WHERE progres = '$progresUnggah'");
                $totalWajib = ($qTotal && mysqli_num_rows($qTotal) > 0) ? (int)mysqli_fetch_assoc($qTotal)['total'] : 0;

                // Hitung total berkas yang sudah DITERIMA oleh Admin
                $qDiterima = query("SELECT COUNT(*) AS total FROM temp_skripsi ts 
                                    JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id 
                                    WHERE ts.nim = '$nimMhs' AND pbs.progres = '$progresUnggah' AND ts.status = 'diterima'");
                $totalDiterima = ($qDiterima && mysqli_num_rows($qDiterima) > 0) ? (int)mysqli_fetch_assoc($qDiterima)['total'] : 0;

                // Hitung total berkas yang sudah diverifikasi (diterima + ditolak)
                $qSelesaiVerifikasi = query("SELECT COUNT(*) AS total FROM temp_skripsi ts 
                                             JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id 
                                             WHERE ts.nim = '$nimMhs' AND pbs.progres = '$progresUnggah' AND ts.status IN ('diterima', 'ditolak')");
                $totalSelesaiVerifikasi = ($qSelesaiVerifikasi && mysqli_num_rows($qSelesaiVerifikasi) > 0) ? (int)mysqli_fetch_assoc($qSelesaiVerifikasi)['total'] : 0;

                // Hitung total berkas yang DITOLAK oleh Admin
                $qDitolak = query("SELECT COUNT(*) AS total FROM temp_skripsi ts 
                                   JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id 
                                   WHERE ts.nim = '$nimMhs' AND pbs.progres = '$progresUnggah' AND ts.status = 'ditolak'");
                $totalDitolak = ($qDitolak && mysqli_num_rows($qDitolak) > 0) ? (int)mysqli_fetch_assoc($qDitolak)['total'] : 0;

                // Cek apakah SEMUA berkas sudah diverifikasi (tidak ada lagi yang "menunggu")
                if ($totalWajib > 0 && $totalSelesaiVerifikasi >= $totalWajib) {
                    
                    if ($totalDitolak > 0) {
                        // Jika ADA berkas yang DITOLAK: Mundurkan progres_skripsi ke tahap sebelumnya (agar mahasiswa bisa unggah ulang)
                        $tahapSebelumnya = $tahapAdmin - 1; // Mundur satu tahap (ke tahap unggah)
                        $waktu = time();

                        // Hanya insert progres mundur jika yang TERAKHIR bukan tahap tersebut (biar tidak terekam berkali-kali)
                        $qLast = query("SELECT proses FROM progres_skripsi WHERE mahasiswa = '$nimMhs' ORDER BY waktu DESC LIMIT 1");
                        $lastProsesDb = ($qLast && mysqli_num_rows($qLast) > 0) ? (int)mysqli_fetch_assoc($qLast)['proses'] : 0;

                        if ($lastProsesDb !== $tahapSebelumnya) {
                            query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ('$waktu', '$tahapSebelumnya', '$nimMhs')");
                        }
                    } elseif ($totalDiterima >= $totalWajib) {
                        // 1. Pindahkan berkas yang diterima dari temp_skripsi ke berkas_skripsi
                        $qGetTemp = query("SELECT ts.id_persyaratan, ts.nim, ts.file FROM temp_skripsi ts 
                                           JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id 
                                           WHERE ts.nim = '$nimMhs' AND pbs.progres = '$progresUnggah' AND ts.status = 'diterima'");
                        if ($qGetTemp && mysqli_num_rows($qGetTemp) > 0) {
                            while ($rowT = mysqli_fetch_assoc($qGetTemp)) {
                                $idP  = bersihkan($rowT['id_persyaratan']);
                                $nMhs = bersihkan($rowT['nim']);
                                $fId  = bersihkan($rowT['file']);

                                // Cek apakah sudah ada di berkas_skripsi
                                $qCekAda = query("SELECT id FROM berkas_skripsi WHERE nim = '$nMhs' AND id_persyaratan = '$idP'");
                                if ($qCekAda && mysqli_num_rows($qCekAda) > 0) {
                                    query("UPDATE berkas_skripsi SET file = '$fId' WHERE nim = '$nMhs' AND id_persyaratan = '$idP'");
                                } else {
                                    query("INSERT INTO berkas_skripsi (id_persyaratan, nim, file) VALUES ('$idP', '$nMhs', '$fId')");
                                }
                            }
                            // Hapus berkas yang sudah dipindahkan dari temp_skripsi
                            query("DELETE ts FROM temp_skripsi ts 
                                   JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id 
                                   WHERE ts.nim = '$nimMhs' AND pbs.progres = '$progresUnggah'");
                        }

                        // 2. Otomatis majukan progres_skripsi ke tahap berikutnya (setelah verifikasi admin)
                        $waktu = time();
                        
                        // Catat progres admin selesai (verifikasi)
                        $qCekProgresAdmin = query("SELECT proses FROM progres_skripsi WHERE mahasiswa = '$nimMhs' AND proses = '$tahapAdmin'");
                        if (!$qCekProgresAdmin || mysqli_num_rows($qCekProgresAdmin) == 0) {
                            query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ('$waktu', '$tahapAdmin', '$nimMhs')");
                        }

                        $nextProsesAfterAdmin = $tahapAdmin + 1; // Tahap lanjutan setelah verifikasi admin selesai
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
    $nim = bersihkan($_POST['nim']);

    if (empty($nim)) {
        echo json_encode(['status' => false, 'pesan' => 'NIM tidak valid']);
        exit;
    }

    $ket = "Berkas diverifikasi dan diterima sekaligus.";
    $q = query("UPDATE temp_skripsi SET status = 'diterima', ket = '$ket' WHERE nim = '$nim' AND status = 'menunggu'");

    if ($q) {
        echo json_encode(['status' => true, 'pesan' => 'Semua berkas mahasiswa berhasil diterima']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal memproses berkas mahasiswa']);
    }
    exit;
}

echo json_encode(['status' => false, 'pesan' => 'Permintaan tidak valid']);

