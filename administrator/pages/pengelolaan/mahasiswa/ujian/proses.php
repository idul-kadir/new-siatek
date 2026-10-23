<?php
/**
 * ==============================================================================
 * PROSES BACKEND AJAX: PENGELOLAAN BATCH UJIAN & ANTRIAN PESERTA UJIAN MAHASISWA
 * ==============================================================================
 * Sesuai konvensi standar: php-native-crud-instructions.md
 * 
 * Karakteristik file ini:
 * 1. Seluruh request dikirim via AJAX (jQuery $.ajax / $.post), respons selalu JSON.
 * 2. Menggunakan query() dan sanitasi input bersihkan() dari fungsi.php.
 * 3. Output HTML yang di-render di backend dibersihkan menggunakan tampil().
 * 4. Seluruh fungsi pembantu didefinisikan secara global di fungsi.php.
 * 
 * Tabel database terkait:
 * - batch_ujian   : id (int/unix timestamp), tanggal_buka (date), tanggal_tutup (date), pembuat (FK dosen.id)
 * - peserta_batch : id (auto_increment), id_batch (int), mahasiswa (varchar/NIM)
 * ==============================================================================
 */

require_once __DIR__ . "/../../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// Header respons standar selalu JSON UTF-8
header('Content-Type: application/json; charset=utf-8');

// Global exception handler agar kegagalan query/sistem tidak bocor sebagai HTML yang merusak respons AJAX
set_exception_handler(function (Throwable $e) {
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode([
        'status' => false,
        'pesan'  => 'Terjadi kesalahan sistem: ' . $e->getMessage()
    ]);
    exit;
});

// -----------------------------------------------------------------------------
// GUARD 1: VALIDASI STATUS LOGIN USER
// -----------------------------------------------------------------------------
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']) || !empty($_SESSION['id']);
if (!$sudahLogin) {
    echo json_encode([
        'status' => false, 
        'pesan'  => 'Sesi Anda telah berakhir. Silakan login kembali.'
    ]);
    exit;
}

// -----------------------------------------------------------------------------
// GUARD 2: VALIDASI HAK AKSES OPERATOR
// Level 3: Operator Prodi, Level 4: Operator Jurusan, Level 5: Kaprodi, Level 6: Admin, Level 9: Superadmin
// -----------------------------------------------------------------------------
$userLevel = (int)($_SESSION['level'] ?? 0);
if (!in_array($userLevel, [3, 4, 6, 9], true)) {
    echo json_encode([
        'status' => false, 
        'pesan'  => 'Akses ditolak: Anda tidak memiliki wewenang untuk mengelola data ujian.'
    ]);
    exit;
}

// Pembatasan data khusus Level 3 & 5 (harus sesuai program studi yang diampu)
$isFilterProdi = ($userLevel === 3);
$userProdi     = $isFilterProdi ? get_prodi_user() : '';

// Ambil identitas pembuat batch dari session login
$operatorId = bersihkan($_SESSION['username'] ?? $_SESSION['nip'] ?? $_SESSION['user_id'] ?? $_SESSION['nama'] ?? 'Operator');


// =============================================================================
// AKSI 1: CEK STATUS BATCH YANG SEDANG DIBUKA / AKTIF / TERTUTUP
// -----------------------------------------------------------------------------
// Menentukan apakah saat ini ada batch ujian yang statusnya terbuka/aktif
// atau tertutup, serta mengambil daftar seluruh batch untuk seleksi riwayat.
// =============================================================================
if (isset($_POST['ambil-status-batch-aktif'])) {
    // Lepas lock session lebih awal agar request concurrent tidak saling menunggu
    session_write_close();

    $idDiminta = (int)($_POST['id_batch'] ?? 0);

    // Ambil seluruh batch diurutkan dari yang terbaru (ID terbesar)
    // Kolom `status` kini telah ditambahkan ke tabel batch_ujian ('buka' / 'tutup')
    $sql = "SELECT b.id, b.tanggal_buka, b.tanggal_tutup, b.pembuat, b.status,
                   d.nama AS nama_pembuat,
                   COUNT(p.id) AS total_peserta
            FROM batch_ujian b
            LEFT JOIN dosen d ON b.pembuat = d.id
            LEFT JOIN peserta_batch p ON b.id = p.id_batch
            GROUP BY b.id, b.tanggal_buka, b.tanggal_tutup, b.pembuat, b.status, d.nama
            ORDER BY b.id DESC";
    
    $q = query($sql);

    $nowTs = time();
    $activeBatch = null;
    $allBatches = [];

    if ($q && mysqli_num_rows($q) > 0) {
        while ($row = mysqli_fetch_assoc($q)) {
            $id       = (int)$row['id'];
            $tglBuka  = $row['tanggal_buka'];
            $tglTutup = $row['tanggal_tutup'];

            // Hitung status keaktifan batch berdasarkan kolom status dan rentang tanggal
            $bukaTs  = strtotime($tglBuka . ' 00:00:00');
            $tutupTs = strtotime($tglTutup . ' 23:59:59');

            $statusDb = strtolower(trim((string)($row['status'] ?? 'buka')));
            $isManuallyClosed = in_array($statusDb, ['tutup', 'selesai', 'closed'], true);

            // Batch aktif jika status 'buka' dan belum melewati batas penutupan
            $isAktif = (!$isManuallyClosed && $tutupTs && $nowTs <= $tutupTs);

            // Label status visual untuk antarmuka
            $statusLabel = 'Ditutup / Selesai';
            $statusClass = 'bg-slate-100 text-slate-700 border-slate-200';
            if ($isAktif) {
                if ($nowTs < $bukaTs) {
                    $statusLabel = 'Dibuka (Mendatang)';
                    $statusClass = 'bg-blue-50 text-blue-700 border-blue-200';
                } else {
                    $statusLabel = 'Dibuka / Aktif';
                    $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                }
            }

            $batchData = [
                'id'              => $id,
                'nama'            => 'Batch Ujian',
                'tanggal_buka'    => $tglBuka,
                'tanggal_tutup'   => $tglTutup,
                'buka_ts'         => $bukaTs,
                'tutup_ts'        => $tutupTs,
                'periode_display' => tgl_indo($tglBuka) . ' s.d. ' . tgl_indo($tglTutup),
                'pembuat'         => !empty($row['nama_pembuat']) ? $row['nama_pembuat'] : ($row['pembuat'] ?: '-'),
                'total_peserta'   => (int)$row['total_peserta'],
                'status'          => $statusDb,
                'is_aktif'        => $isAktif,
                'is_tertutup'     => !$isAktif,
                'status_label'    => $statusLabel,
                'status_class'    => $statusClass,
            ];

            $allBatches[] = $batchData;

            // Prioritas batch yang dipilih:
            // 1. Jika ada ID spesifik yang dipilih oleh operator
            // 2. Jika tidak ada, ambil batch aktif yang tanggalnya sedang berjalan saat ini
            if ($idDiminta > 0 && $id === $idDiminta) {
                $activeBatch = $batchData;
            } elseif ($activeBatch === null && $isAktif && $idDiminta <= 0) {
                $activeBatch = $batchData;
            }
        }
    }

    // Jika tidak ada batch aktif yang sedang terbuka, namun ada batch di sistem (batch tertutup/selesai),
    // pilih batch paling baru agar operator tetap bisa melihat daftar peserta dan mengatur jadwal ujian
    if ($activeBatch === null && !empty($allBatches) && $idDiminta <= 0) {
        $activeBatch = $allBatches[0];
    }

    // Cek kelayakan untuk membuka batch baru:
    // Syarat: seluruh peserta pada batch sebelumnya wajib sudah diatur jadwalnya (sudah naik step semua)
    $kelayakanBatchBaru = cek_kelayakan_buka_batch_baru();

    echo json_encode([
        'status'               => true,
        'ada_batch'            => !empty($allBatches),
        'ada_batch_aktif'      => ($activeBatch !== null && $activeBatch['is_aktif']),
        'batch_aktif'          => $activeBatch,
        'semua_batch'          => $allBatches,
        'server_now'           => $nowTs,
        'bisa_buka_batch_baru' => $kelayakanBatchBaru['layak'],
        'sisa_belum_diatur'    => $kelayakanBatchBaru['belum_diatur'],
        'pesan_kelayakan'      => $kelayakanBatchBaru['pesan']
    ]);
    exit;
}


// =============================================================================
// AKSI 2: BUKA BATCH UJIAN BARU
// -----------------------------------------------------------------------------
// Menyimpan record periode batch baru ke tabel batch_ujian.
// Kolom id menggunakan unix timestamp (time()).
// Kolom tanggal_buka & tanggal_tutup bertipe DATE (YYYY-MM-DD).
// =============================================================================
if (isset($_POST['tambah-batch'])) {
    // Validasi syarat: buka batch baru hanya jika seluruh peserta batch sebelumnya sudah diatur jadwalnya (sudah naik step)
    $cekKelayakan = cek_kelayakan_buka_batch_baru();
    if (!$cekKelayakan['layak']) {
        echo json_encode([
            'status' => false,
            'pesan'  => $cekKelayakan['pesan']
        ]);
        exit;
    }

    $tglBuka  = bersihkan($_POST['tanggal_buka'] ?? '');
    $tglTutup = bersihkan($_POST['tanggal_tutup'] ?? '');

    // Validasi input tanggal
    if (empty($tglBuka) || empty($tglTutup)) {
        echo json_encode(['status' => false, 'pesan' => 'Tanggal mulai buka dan penutupan wajib diisi.']);
        exit;
    }

    $bukaTs  = strtotime($tglBuka . ' 00:00:00');
    $tutupTs = strtotime($tglTutup . ' 23:59:59');

    if (!$bukaTs || !$tutupTs) {
        echo json_encode(['status' => false, 'pesan' => 'Format tanggal yang dimasukkan tidak valid.']);
        exit;
    }

    if ($tutupTs < $bukaTs) {
        echo json_encode(['status' => false, 'pesan' => 'Tanggal penutupan tidak boleh lebih awal dari tanggal mulai buka.']);
        exit;
    }

    // Gunakan unix timestamp saat ini sebagai ID batch (time())
    $nextId = (int)time();

    // Dapatkan ID dosen yang valid untuk mengisi foreign key pembuat
    $idDosenPembuat = dapatkan_id_dosen_pembuat();

    try {
        // Kolom status kini telah ada pada tabel batch_ujian, diset default 'buka'
        $simpan = query("INSERT INTO batch_ujian (id, tanggal_buka, tanggal_tutup, pembuat, status) VALUES ('$nextId', '$tglBuka', '$tglTutup', '$idDosenPembuat', 'buka')");

        if ($simpan) {
            echo json_encode([
                'status'   => true, 
                'pesan'    => "Batch ujian berhasil dibuka untuk periode " . tgl_indo($tglBuka) . " s.d. " . tgl_indo($tglTutup) . ".",
                'id_batch' => $nextId
            ]);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal membuka batch ujian: ' . mysqli_error($koneksi)]);
        }
    } catch (Throwable $e) {
        echo json_encode(['status' => false, 'pesan' => 'Gagal membuka batch: ' . $e->getMessage()]);
    }
    exit;
}


// =============================================================================
// AKSI 3: UBAH PERIODE BATCH UJIAN
// -----------------------------------------------------------------------------
// Memperbarui rentang tanggal buka dan tutup untuk batch tertentu.
// =============================================================================
if (isset($_POST['edit-batch'])) {
    $idBatch  = (int)($_POST['id_batch'] ?? 0);
    $tglBuka  = bersihkan($_POST['tanggal_buka'] ?? '');
    $tglTutup = bersihkan($_POST['tanggal_tutup'] ?? '');

    if ($idBatch <= 0) {
        echo json_encode(['status' => false, 'pesan' => 'ID batch tidak valid.']);
        exit;
    }

    // Validasi: Batch yang sudah ditutup tidak boleh diedit lagi KECUALI jika belum memiliki peserta sama sekali (koreksi salah input)
    $cekB = query("SELECT b.status, COUNT(p.id) AS total_peserta 
                   FROM batch_ujian b 
                   LEFT JOIN peserta_batch p ON b.id = p.id_batch 
                   WHERE b.id = '$idBatch' 
                   GROUP BY b.id, b.status LIMIT 1");
    if ($cekB && ($rB = mysqli_fetch_assoc($cekB))) {
        $st = strtolower(trim((string)($rB['status'] ?? 'buka')));
        $jmlPeserta = (int)($rB['total_peserta'] ?? 0);
        if (in_array($st, ['tutup', 'selesai', 'closed'], true) && $jmlPeserta > 0) {
            echo json_encode(['status' => false, 'pesan' => 'Batch ujian telah ditutup dan memiliki peserta sehingga periode tidak dapat diedit lagi.']);
            exit;
        }
    }

    if (empty($tglBuka) || empty($tglTutup)) {
        echo json_encode(['status' => false, 'pesan' => 'Tanggal mulai buka dan penutupan wajib diisi.']);
        exit;
    }

    $bukaTs  = strtotime($tglBuka . ' 00:00:00');
    $tutupTs = strtotime($tglTutup . ' 23:59:59');

    if (!$bukaTs || !$tutupTs) {
        echo json_encode(['status' => false, 'pesan' => 'Format tanggal tidak valid.']);
        exit;
    }

    if ($tutupTs < $bukaTs) {
        echo json_encode(['status' => false, 'pesan' => 'Tanggal penutupan tidak boleh lebih awal dari tanggal mulai buka.']);
        exit;
    }

    try {
        $update = query("UPDATE batch_ujian SET tanggal_buka = '$tglBuka', tanggal_tutup = '$tglTutup', status = 'buka' WHERE id = '$idBatch'");

        if ($update) {
            echo json_encode(['status' => true, 'pesan' => "Periode batch ujian berhasil diperbarui."]);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui periode batch: ' . mysqli_error($koneksi)]);
        }
    } catch (Throwable $e) {
        echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui batch: ' . $e->getMessage()]);
    }
    exit;
}


// =============================================================================
// AKSI 4: TUTUP BATCH UJIAN
// -----------------------------------------------------------------------------
// Menutup batch ujian yang sedang aktif sehingga pendaftaran terkunci
// dan tombol aksi peserta berganti menjadi tombol untuk mengatur jadwal.
// Data batch dan relasi peserta TETAP DIPERTAHANKAN (tidak dihapus).
// =============================================================================
if (isset($_POST['hapus-batch'])) {
    $idBatch = (int)($_POST['id_batch'] ?? 0);

    if ($idBatch <= 0) {
        echo json_encode(['status' => false, 'pesan' => 'ID batch tidak valid.']);
        exit;
    }

    try {
        // Update status batch ujian menjadi 'tutup'
        $update = query("UPDATE batch_ujian SET status = 'tutup' WHERE id = '$idBatch'");

        if ($update) {
            echo json_encode([
                'status'   => true, 
                'pesan'    => "Batch ujian telah ditutup. Tombol aksi kini diganti menjadi tombol mengatur untuk menetapkan jadwal ujian peserta.",
                'id_batch' => $idBatch
            ]);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal menutup batch: ' . mysqli_error($koneksi)]);
        }
    } catch (Throwable $e) {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menutup batch: ' . $e->getMessage()]);
    }
    exit;
}


// =============================================================================
// AKSI KHUSUS: AMBIL NOMOR SURAT UNDANGAN TERBARU SECARA REAL-TIME (AJAX)
// -----------------------------------------------------------------------------
// Menghasilkan nomor surat fresh saat modal atur jadwal dibuka agar tidak duplikat
// =============================================================================
if (isset($_POST['ambil-nomor-undangan-terbaru'])) {
    session_write_close();
    header('Content-Type: application/json');
    $nomorFresh = function_exists('get_nomor_surat') ? get_nomor_surat('undangan menguji') : '';
    echo json_encode([
        'status' => !empty($nomorFresh),
        'nomor'  => $nomorFresh
    ]);
    exit;
}


// =============================================================================
// AKSI 5: ATUR JADWAL UJIAN PESERTA BATCH (NOMOR UNDANGAN, JAM, TANGGAL, RUANG)
// -----------------------------------------------------------------------------
// Menyimpan detail jadwal pelaksanaan ujian mahasiswa peserta ke tabel peserta_batch.
// Sekaligus menyinkronkan data jadwal ke tabel skripsi / kp serta memajukan
// tahapan progres mahasiswa (naik step).
// =============================================================================
if (isset($_POST['atur-jadwal-peserta'])) {
    $idPeserta     = (int)($_POST['id_peserta'] ?? 0);
    $idBatch       = (int)($_POST['id_batch'] ?? 0);
    $nim           = bersihkan($_POST['nim'] ?? '');
    $kategori      = bersihkan($_POST['kategori'] ?? '');
    $nomorUndangan = tambah_nomor($_POST['nomor_undangan'],'Undangan mahasiswa '.$kategori .' '.$nim);
    $tanggal       = bersihkan($_POST['tanggal'] ?? '');
    $jam           = bersihkan($_POST['jam'] ?? '');
    $ruang         = bersihkan($_POST['ruang'] ?? '');

    if ($idPeserta <= 0) {
        echo json_encode(['status' => false, 'pesan' => 'ID peserta tidak valid.']);
        exit;
    }

    if (empty($nomorUndangan) || empty($tanggal) || empty($jam) || empty($ruang)) {
        echo json_encode(['status' => false, 'pesan' => 'Seluruh rincian (Nomor Undangan, Tanggal, Jam, dan Ruang Ujian) wajib diisi.']);
        exit;
    }

    // SINKRONISASI JADWAL KE SKRIPSI / KP BERDASARKAN KATEGORI EKSPLISIT DARI FORM
    $waktuSekarang = time();

    if ($kategori === 'skripsi_hasil') {
        // Tahapan Ujian Hasil: simpan jadwal & naikkan ke Step 11
        query("UPDATE skripsi SET 
                   no_undangan_hasil = '$nomorUndangan',
                   tanggal_hasil     = '$tanggal',
                   jam_hasil         = '$jam',
                   ruangan_hasil     = '$ruang'
               WHERE nim = '$nim'");
        query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuSekarang, 11, '$nim')");

    } elseif ($kategori === 'skripsi_tutup') {
        // Tahapan Ujian Tutup: simpan jadwal & naikkan ke Step 19
        query("UPDATE skripsi SET 
                   no_undangan_tutup = '$nomorUndangan',
                   tanggal_tutup     = '$tanggal',
                   jam_tutup         = '$jam',
                   ruangan_tutup     = '$ruang'
               WHERE nim = '$nim'");
        query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuSekarang, 19, '$nim')");

    } elseif ($kategori === 'kp') {
        // Tahapan Seminar KP: simpan jadwal & naikkan ke Step 14
        try {
            query("UPDATE kp SET 
                       no_undangan     = '$nomorUndangan',
                       tanggal_seminar = '$tanggal',
                       jam_seminar     = '$jam',
                       ruangan         = '$ruang'
                   WHERE nim = '$nim'");
        } catch (Throwable $e) {
            // Fallback aman jika kolom no_undangan pada tabel kp masih bertipe INT di database
            query("UPDATE kp SET 
                       tanggal_seminar = '$tanggal',
                       jam_seminar     = '$jam',
                       ruangan         = '$ruang'
                   WHERE nim = '$nim'");
        }
        if (function_exists('set_progres_kp')) {
            set_progres_kp($nim, 13, $waktuSekarang);
        } else {
            query("INSERT INTO progres_kp (waktu, proses, mahasiswa) VALUES ($waktuSekarang, 13, '$nim')");
        }

    } else {
        echo json_encode(['status' => false, 'pesan' => 'Kategori ujian tidak valid atau tidak dikenali.']);
        exit;
    }

    // Mapping nilai kategori sesuai tipe kolom di database: `kategori` set('kp','hasil','tutup')
    $katDb = 'kp';
    if ($kategori === 'skripsi_hasil' || $kategori === 'hasil') {
        $katDb = 'hasil';
    } elseif ($kategori === 'skripsi_tutup' || $kategori === 'tutup') {
        $katDb = 'tutup';
    } elseif ($kategori === 'kp') {
        $katDb = 'kp';
    }

    // Update kategori pada tabel peserta_batch sesuai nilai SET yang valid ('kp', 'hasil', 'tutup')
    query("UPDATE peserta_batch SET kategori = '$katDb' WHERE id = '$idPeserta'");

    echo json_encode([
        'status' => true,
        'pesan'  => "Jadwal ujian untuk mahasiswa ($nim) berhasil disimpan dan tahapan progres berhasil dinaikkan."
    ]);
    exit;
}


// =============================================================================
// AKSI 6: AMBIL TABEL PESERTA DARI BATCH (SISI KIRI)
// -----------------------------------------------------------------------------
// Mengembalikan baris tabel HTML daftar mahasiswa yang sudah terdaftar
// pada batch ujian.
// - Jika BATCH DIBUKA: Tombol aksi adalah "Keluarkan"
// - Jika BATCH DITUTUP: Tombol aksi diganti menjadi "Mengatur" (jadwal ujian)
// =============================================================================
if (isset($_POST['ambil-tabel-peserta'])) {
    session_write_close();

    $idBatch = (int)($_POST['id_batch'] ?? 0);
    $cari    = bersihkan($_POST['cari'] ?? '');
    $prodi   = bersihkan($_POST['prodi'] ?? '');

    $filterProdi = $isFilterProdi ? ($userProdi !== '' ? $userProdi : '__PRODI_KOSONG__') : $prodi;

    // Jika belum ada ID batch yang dipilih/terbuka
    if ($idBatch <= 0) {
        ob_start();
        ?>
        <div class="py-12 text-center text-slate-500">
            <i class="fas fa-layer-group text-3xl mb-2 text-slate-300"></i>
            <p class="font-medium text-sm text-slate-700">Belum ada batch ujian yang terbuka</p>
            <p class="text-xs text-slate-400 mt-1">Buka batch ujian terlebih dahulu melalui formulir di sebelah kiri.</p>
        </div>
        <?php
        $html = ob_get_clean();
        echo json_encode(['status' => true, 'data' => $html, 'total' => 0, 'is_batch_aktif' => false]);
        exit;
    }

    // Tentukan status keaktifan batch ini langsung dari kolom `status` pada batch_ujian
    $isBatchAktif = false;
    $qB = query("SELECT tanggal_buka, tanggal_tutup, status FROM batch_ujian WHERE id = '$idBatch' LIMIT 1");
    if ($qB && ($rB = mysqli_fetch_assoc($qB))) {
        $now = time();
        $bTs = strtotime($rB['tanggal_buka'] . ' 00:00:00');
        $tTs = strtotime($rB['tanggal_tutup'] . ' 23:59:59');
        $statusDb = strtolower(trim((string)($rB['status'] ?? 'buka')));
        $isManuallyClosed = in_array($statusDb, ['tutup', 'selesai', 'closed'], true);
        $isBatchAktif = (!$isManuallyClosed && $tTs && $now <= $tTs);
    }

    // Panggil helper untuk mengambil data peserta batch
    $pesertaList = ambil_daftar_peserta_batch($idBatch, $filterProdi, $cari);

    ob_start();
    if (!empty($pesertaList)) {
        $no = 1;
        ?>
        <table class="w-full text-left text-xs">
            <thead class="sticky top-0 z-10 bg-slate-900 text-white">
                <tr>
                    <?php if ($isBatchAktif): ?>
                        <th class="py-3 px-3 text-center w-8">
                            <input type="checkbox" id="checkAllPeserta" class="rounded accent-orange-500 cursor-pointer">
                        </th>
                    <?php endif; ?>
                    <th class="py-3 px-2 text-center w-8">No</th>
                    <th class="py-3 px-3 font-semibold uppercase tracking-wider">Mahasiswa</th>
                    <th class="py-3 px-3 font-semibold uppercase tracking-wider">Program Studi</th>
                    <th class="py-3 px-3 font-semibold uppercase tracking-wider">Kategori</th>
                    <th class="py-3 px-3 text-center font-semibold uppercase tracking-wider <?= $isBatchAktif ? 'w-24' : 'w-32' ?>">
                        <?= $isBatchAktif ? 'Aksi' : 'Pengaturan' ?>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <?php foreach ($pesertaList as $p): ?>
                <tr class="hover:bg-slate-50 transition">
                    <?php if ($isBatchAktif): ?>
                        <!-- Checkbox seleksi massal (Hanya aktif saat batch terbuka) -->
                        <td class="py-2.5 px-3 text-center">
                            <input type="checkbox" class="cb-peserta rounded accent-orange-500 cursor-pointer" value="<?= tampil($p['id_peserta']) ?>" data-nim="<?= tampil($p['nim']) ?>">
                        </td>
                    <?php endif; ?>
                    <td class="py-2.5 px-2 text-center font-semibold text-slate-400">
                        <?= $no++ ?>
                    </td>
                    <!-- Identitas mahasiswa: Nama, NIM, Angkatan & Info Jadwal jika sudah diatur -->
                    <td class="py-2.5 px-3">
                        <div class="font-bold text-slate-800 text-xs"><?= tampil($p['nama_mhs']) ?></div>
                        <div class="text-[11px] font-mono text-slate-500 flex items-center gap-1.5 mt-0.5">
                            <i class="fas fa-id-card text-[10px] text-slate-400"></i>
                            <span><?= tampil($p['nim']) ?></span>
                            <?php if(!empty($p['angkatan'])): ?>
                                <span class="text-slate-300">&bull;</span>
                                <span>Angk. <?= tampil($p['angkatan']) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Jika batch ditutup, tampilkan informasi jadwal yang telah diatur -->
                        <?php if (!$isBatchAktif): ?>
                            <?php if (!empty($p['sudah_diatur'])): ?>
                                <div class="mt-1.5 flex flex-wrap items-center gap-1 text-[10px]">
                                    <?php if (!empty($p['nomor_undangan'])): ?>
                                        <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-1.5 py-0.5 text-slate-700 font-mono border border-slate-200" title="Nomor Undangan">
                                            <i class="far fa-envelope text-slate-400"></i> <?= tampil($p['nomor_undangan']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($p['tanggal_ujian'])): ?>
                                        <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-1.5 py-0.5 text-slate-700 border border-slate-200" title="Tanggal Ujian">
                                            <i class="far fa-calendar-alt text-orange-500"></i> <?= tgl_indo($p['tanggal_ujian']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($p['jam_ujian'])): ?>
                                        <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-1.5 py-0.5 text-slate-700 border border-slate-200" title="Jam Ujian">
                                            <i class="far fa-clock text-blue-500"></i> <?= tampil($p['jam_ujian']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($p['ruang_ujian'])): ?>
                                        <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-1.5 py-0.5 text-slate-700 border border-slate-200" title="Ruang Ujian">
                                            <i class="fas fa-door-open text-emerald-500"></i> <?= tampil($p['ruang_ujian']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="mt-1 flex items-center gap-1 text-[11px] font-medium text-amber-600">
                                    <i class="fas fa-exclamation-circle text-[10px]"></i>
                                    <span>Jadwal ujian belum diatur</span>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <!-- Program studi -->
                    <td class="py-2.5 px-3">
                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-700 bg-slate-100 rounded px-2 py-0.5">
                            <i class="fas fa-graduation-cap text-[9px] text-orange-500"></i>
                            <?= tampil($p['nama_prodi']) ?>
                        </span>
                    </td>
                    <!-- Kategori ujian -->
                    <td class="py-2.5 px-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold <?= $p['badge_class'] ?>">
                            <i class="<?= $p['icon_class'] ?> text-[9px]"></i>
                            <?= tampil($p['tahapan_label']) ?>
                        </span>
                    </td>

                    <!-- Tombol Aksi: Berbeda sesuai status batch (Keluarkan saat batch aktif, Mengatur saat batch ditutup) -->
                    <td class="py-2.5 px-3 text-center">
                        <?php if ($isBatchAktif): ?>
                            <!-- Batch Terbuka: Tombol Keluarkan Peserta -->
                            <button type="button" 
                                class="btn-keluarkan-peserta inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 transition"
                                data-id-peserta="<?= tampil($p['id_peserta']) ?>"
                                data-nim="<?= tampil($p['nim']) ?>"
                                data-nama="<?= tampil($p['nama_mhs']) ?>"
                                title="Keluarkan dari batch ini">
                                <i class="fas fa-user-minus"></i> Keluarkan
                            </button>
                        <?php else: ?>
                            <!-- Batch Ditutup / Tertutup: Tombol Mengatur Jadwal (Warna Orange Khas Template SIATEK) -->
                            <button type="button" 
                                class="btn-atur-jadwal inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white shadow-sm transition"
                                data-id-peserta="<?= tampil($p['id_peserta']) ?>"
                                data-id-batch="<?= tampil($p['id_batch']) ?>"
                                data-nim="<?= tampil($p['nim']) ?>"
                                data-nama="<?= tampil($p['nama_mhs']) ?>"
                                data-kategori="<?= tampil($p['kategori'] ?? '') ?>"
                                data-tahapan="<?= tampil($p['tahapan_label']) ?>"
                                data-penguji="<?= htmlspecialchars(json_encode($p['penguji'] ?? []), ENT_QUOTES, 'UTF-8') ?>"
                                data-nomor-undangan="<?= tampil($p['nomor_undangan'] ?? '') ?>"
                                data-tanggal="<?= tampil($p['tanggal_ujian'] ?? '') ?>"
                                data-jam="<?= tampil($p['jam_ujian'] ?? '') ?>"
                                data-ruang="<?= tampil($p['ruang_ujian'] ?? '') ?>"
                                title="Atur nomor undangan, jam, tanggal, dan ruang ujian">
                                <i class="fas fa-sliders-h text-xs"></i>
                                <span>Mengatur</span>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    } else {
        ?>
        <div class="py-12 text-center text-slate-500">
            <div class="w-10 h-10 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-2 text-sm">
                <i class="fas fa-user-friends"></i>
            </div>
            <p class="font-semibold text-xs text-slate-700"><?= $isBatchAktif ? 'Belum Ada Peserta dalam Batch Ini' : 'Tidak Ada Peserta yang Perlu Diatur' ?></p>
            <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">
                <?= $isBatchAktif ? 'Pilih mahasiswa pada antrian di sebelah kanan untuk dimasukkan ke batch ini.' : 'Seluruh peserta telah selesai diatur jadwalnya dan telah melanjutkan ke tahapan berikutnya.' ?>
            </p>
        </div>
        <?php
    }
    $html = ob_get_clean();

    echo json_encode([
        'status'         => true,
        'data'           => $html,
        'total'          => count($pesertaList),
        'is_batch_aktif' => $isBatchAktif
    ]);
    exit;
}


// =============================================================================
// AKSI 6: AMBIL LIST ANTRIAN MAHASISWA SIAP UJIAN (SISI KANAN)
// -----------------------------------------------------------------------------
// Mengembalikan daftar mahasiswa yang sudah sampai pada tahapan ujian
// (Skripsi Hasil, Skripsi Tutup, Seminar KP) dan belum masuk ke batch aktif saat ini.
// Khusus KP: Mahasiswa WAJIB SUDAH ADA penunjukkan penguji.
// Checkbox berstatus disabled jika belum ada batch ujian yang terbuka.
// =============================================================================
if (isset($_POST['ambil-tabel-antrian'])) {
    session_write_close();

    $idBatchActive = (int)($_POST['id_batch'] ?? 0);
    $batchTerbuka  = ($_POST['batch_terbuka'] ?? '0') === '1';
    $cari          = bersihkan($_POST['cari'] ?? '');
    $prodi         = bersihkan($_POST['prodi'] ?? '');
    $kategori      = bersihkan($_POST['kategori'] ?? 'semua');

    $filterProdi = $isFilterProdi ? ($userProdi !== '' ? $userProdi : '__PRODI_KOSONG__') : $prodi;

    // Ambil daftar antrian mahasiswa melalui fungsi pembantu
    $antrian = ambil_antrian_siap_ujian($idBatchActive, $filterProdi, $kategori, $cari);

    ob_start();
    if (!empty($antrian)) {
        ?>
        <div class="divide-y divide-slate-100 bg-white">
            <?php foreach ($antrian as $a): 
                // Aturan checkbox: aktif jika ada batch terbuka, disabled jika belum ada batch terbuka
                $checkboxAttr = $batchTerbuka 
                    ? 'class="cb-antrian rounded accent-orange-500 cursor-pointer w-4 h-4 mt-0.5"'
                    : 'class="rounded accent-slate-400 cursor-not-allowed opacity-40 w-4 h-4 mt-0.5" disabled';
            ?>
            <div class="p-3 hover:bg-slate-50 transition flex items-start gap-3">
                <!-- Checkbox pilihan mahasiswa -->
                <div class="pt-0.5">
                    <input type="checkbox" <?= $checkboxAttr ?> value="<?= tampil($a['nim']) ?>">
                </div>

                <!-- Informasi identitas lengkap mahasiswa -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-bold text-slate-800 text-xs truncate" title="<?= tampil($a['nama_mhs']) ?>">
                            <?= tampil($a['nama_mhs']) ?>
                        </span>
                        <!-- Badge jenis tahapan ujian -->
                        <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold <?= $a['badge_class'] ?>">
                            <i class="<?= $a['icon_class'] ?> text-[9px]"></i>
                            <?= tampil($a['tahapan_label']) ?>
                        </span>
                    </div>

                    <!-- Detail: NIM, Angkatan, Program Studi -->
                    <div class="text-[11px] text-slate-500 mt-1 flex flex-wrap items-center gap-1.5 font-mono">
                        <span class="text-slate-700 font-semibold"><?= tampil($a['nim']) ?></span>
                        <span class="text-slate-300">&bull;</span>
                        <span class="text-slate-600 font-sans">Angk. <?= tampil($a['angkatan'] ?: '-') ?></span>
                        <span class="text-slate-300">&bull;</span>
                        <span class="text-slate-600 font-sans truncate"><?= tampil($a['nama_prodi']) ?></span>
                    </div>

                    <!-- Dosen Penguji (Disusun Terstruktur per Baris) -->
                    <?php if (!empty($a['penguji'])): ?>
                    <div class="mt-1.5 p-1.5 rounded-md bg-slate-50 border border-slate-200/70 text-[10.5px]">
                        <div class="text-[9.5px] font-bold text-slate-500 flex items-center gap-1 mb-0.5">
                            <i class="fas fa-user-tie text-orange-500 text-[9px]"></i>
                            <span>Dosen Penguji:</span>
                        </div>
                        <div class="space-y-0.5 pl-1">
                            <?php foreach ($a['penguji'] as $idx => $dsn): 
                                $peran = !empty($dsn['peran']) ? $dsn['peran'] : ('Penguji ' . ($idx + 1));
                            ?>
                                <div class="flex items-start gap-1 leading-tight">
                                    <span class="font-bold text-orange-600 shrink-0 text-[9.5px] w-14"><?= tampil($peran) ?>:</span>
                                    <span class="text-slate-700 font-medium"><?= tampil($dsn['nama']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Tombol aksi cepat tambah satuan (hanya tampil jika batch sedang terbuka) -->
                <?php if ($batchTerbuka): ?>
                <div class="shrink-0 pt-0.5">
                    <button type="button" 
                        class="btn-masukkan-peserta px-2.5 py-1 rounded bg-orange-500 hover:bg-orange-600 text-white text-[11px] font-semibold shadow-sm transition"
                        data-nim="<?= tampil($a['nim']) ?>"
                        data-nama="<?= tampil($a['nama_mhs']) ?>"
                        title="Masukkan ke batch terbuka">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
    } else {
        ?>
        <div class="py-12 text-center text-slate-400 p-4">
            <div class="w-10 h-10 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-2 text-sm">
                <i class="fas fa-user-check"></i>
            </div>
            <p class="font-semibold text-xs text-slate-700">Tidak Ada Mahasiswa dalam Antrian</p>
            <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">
                Semua mahasiswa siap ujian telah dimasukkan ke batch terbuka, atau belum ada mahasiswa yang memenuhi kualifikasi (khusus KP: wajib sudah ada penunjukkan penguji).
            </p>
        </div>
        <?php
    }

    $html = ob_get_clean();
    echo json_encode([
        'status' => true,
        'data'   => $html,
        'total'  => count($antrian)
    ]);
    exit;
}


// =============================================================================
// AKSI 7: MASUKKAN MAHASISWA KE BATCH (SATUAN ATAU MASSAL VIA CHECKBOX)
// -----------------------------------------------------------------------------
// Menyimpan pendaftaran mahasiswa ke tabel peserta_batch.
// Menghapus data dari batch lama jika sebelumnya mahasiswa pernah terdaftar
// di batch lain, sehingga satu mahasiswa hanya aktif di satu periode batch.
// =============================================================================
if (isset($_POST['tambah-peserta-batch'])) {
    $idBatch = (int)($_POST['id_batch'] ?? 0);
    $nims    = $_POST['nims'] ?? [];

    // Jika dikirim tunggal via tombol +
    if (!empty($_POST['nim'])) {
        $nims = [$_POST['nim']];
    }

    if ($idBatch <= 0) {
        echo json_encode(['status' => false, 'pesan' => 'Batch ujian terbuka belum tersedia. Buka batch terlebih dahulu.']);
        exit;
    }

    if (!is_array($nims) || empty($nims)) {
        echo json_encode(['status' => false, 'pesan' => 'Pilih minimal satu mahasiswa untuk dimasukkan.']);
        exit;
    }

    // Pastikan batch tujuan valid dan ada di database
    $cekBatch = query("SELECT id FROM batch_ujian WHERE id = '$idBatch' LIMIT 1");
    if (!$cekBatch || mysqli_num_rows($cekBatch) === 0) {
        echo json_encode(['status' => false, 'pesan' => "Batch ujian tidak ditemukan."]);
        exit;
    }

    $sukses   = 0;
    $dilewati = 0;

    try {
        foreach ($nims as $nimRaw) {
            $nimBersih = bersihkan($nimRaw);
            if ($nimBersih === '') continue;

            // Cek apakah mahasiswa sudah terdaftar di batch tujuan ini dengan kategori masih kosong (belum diatur)
            $cek = query("SELECT id FROM peserta_batch WHERE id_batch = '$idBatch' AND mahasiswa = '$nimBersih' AND (kategori IS NULL OR kategori = '') LIMIT 1");
            if ($cek && mysqli_num_rows($cek) > 0) {
                $dilewati++;
                continue;
            }

            // Simpan pendaftaran baru ke batch aktif saat ini (mahasiswa boleh masuk batch berbeda untuk ujian berbeda)
            $ins = query("INSERT INTO peserta_batch (id_batch, mahasiswa) VALUES ('$idBatch', '$nimBersih')");
            if ($ins) {
                $sukses++;
            }
        }

        $pesan = "$sukses mahasiswa berhasil dimasukkan ke batch ujian.";
        if ($dilewati > 0) {
            $pesan .= " ($dilewati mahasiswa sudah terdaftar sebelumnya).";
        }

        echo json_encode([
            'status'   => ($sukses > 0 || $dilewati > 0),
            'pesan'    => $pesan,
            'sukses'   => $sukses,
            'dilewati' => $dilewati
        ]);
    } catch (Throwable $e) {
        echo json_encode(['status' => false, 'pesan' => 'Gagal memasukkan mahasiswa: ' . $e->getMessage()]);
    }
    exit;
}


// =============================================================================
// AKSI 8: KELUARKAN MAHASISWA DARI BATCH (SATUAN ATAU MASSAL VIA CHECKBOX)
// -----------------------------------------------------------------------------
// Menghapus data mahasiswa dari tabel peserta_batch sehingga kembali ke antrian.
// =============================================================================
if (isset($_POST['hapus-peserta-batch'])) {
    $idBatch    = (int)($_POST['id_batch'] ?? 0);
    $idPeserta  = (int)($_POST['id_peserta'] ?? 0);
    $nim        = bersihkan($_POST['nim'] ?? '');
    $idsPeserta = $_POST['ids_peserta'] ?? [];

    if ($idBatch <= 0) {
        echo json_encode(['status' => false, 'pesan' => 'ID batch tidak valid.']);
        exit;
    }

    try {
        // 1. Eksekusi pengeluaran massal via kumpulan id peserta
        if (is_array($idsPeserta) && !empty($idsPeserta)) {
            $idsEscaped = array_map('intval', $idsPeserta);
            $idsStr = implode(',', $idsEscaped);
            $del = query("DELETE FROM peserta_batch WHERE id_batch = '$idBatch' AND id IN ($idsStr)");

            if ($del) {
                $jml = mysqli_affected_rows($koneksi);
                echo json_encode(['status' => true, 'pesan' => "$jml mahasiswa berhasil dikeluarkan dari batch ujian."]);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal mengeluarkan peserta: ' . mysqli_error($koneksi)]);
            }
            exit;
        }

        // 2. Eksekusi pengeluaran tunggal
        if ($idPeserta > 0) {
            $del = query("DELETE FROM peserta_batch WHERE id = '$idPeserta' AND id_batch = '$idBatch'");
        } elseif ($nim !== '') {
            $del = query("DELETE FROM peserta_batch WHERE id_batch = '$idBatch' AND mahasiswa = '$nim'");
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Data peserta yang akan dikeluarkan tidak lengkap.']);
            exit;
        }

        if ($del) {
            echo json_encode(['status' => true, 'pesan' => "Mahasiswa berhasil dikeluarkan dari batch ujian."]);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal mengeluarkan mahasiswa: ' . mysqli_error($koneksi)]);
        }
    } catch (Throwable $e) {
        echo json_encode(['status' => false, 'pesan' => 'Gagal mengeluarkan mahasiswa: ' . $e->getMessage()]);
    }
    exit;
}
