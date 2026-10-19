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
 * 4. Seluruh fungsi pembantu (helper functions) diletakkan di bagian paling bawah file.
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
if (!in_array($userLevel, [3, 4, 5, 6, 9], true)) {
    echo json_encode([
        'status' => false, 
        'pesan'  => 'Akses ditolak: Anda tidak memiliki wewenang untuk mengelola data ujian.'
    ]);
    exit;
}

// Pembatasan data khusus Level 3 & 5 (harus sesuai program studi yang diampu)
$isFilterProdi = in_array($userLevel, [3, 5], true);
$userProdi     = $isFilterProdi ? get_prodi_user() : '';

// Ambil identitas pembuat batch dari session login
$operatorId = bersihkan($_SESSION['username'] ?? $_SESSION['nip'] ?? $_SESSION['user_id'] ?? $_SESSION['nama'] ?? 'Operator');


// =============================================================================
// AKSI 1: CEK STATUS BATCH YANG SEDANG DIBUKA / AKTIF
// -----------------------------------------------------------------------------
// Menentukan apakah saat ini ada batch ujian yang statusnya terbuka/aktif
// berdasarkan tanggal hari ini berada di antara tanggal_buka dan tanggal_tutup.
// =============================================================================
if (isset($_POST['ambil-status-batch-aktif'])) {
    // Lepas lock session lebih awal agar request concurrent tidak saling menunggu
    session_write_close();

    $idDiminta = (int)($_POST['id_batch'] ?? 0);

    // Ambil seluruh batch diurutkan dari yang terbaru (ID terbesar)
    $sql = "SELECT b.id, b.tanggal_buka, b.tanggal_tutup, b.pembuat, 
                   d.nama AS nama_pembuat,
                   COUNT(p.id) AS total_peserta
            FROM batch_ujian b
            LEFT JOIN dosen d ON b.pembuat = d.id
            LEFT JOIN peserta_batch p ON b.id = p.id_batch
            GROUP BY b.id, b.tanggal_buka, b.tanggal_tutup, b.pembuat, d.nama
            ORDER BY b.id DESC";
    
    try {
        $q = query($sql);
    } catch (Throwable $e) {
        echo json_encode([
            'status'          => false,
            'pesan'           => 'Gagal memuat batch ujian: ' . $e->getMessage(),
            'ada_batch_aktif' => false,
            'batch_aktif'     => null,
            'semua_batch'     => []
        ]);
        exit;
    }

    $nowTs = time();
    $activeBatch = null;
    $allBatches = [];

    if ($q && mysqli_num_rows($q) > 0) {
        while ($row = mysqli_fetch_assoc($q)) {
            $id       = (int)$row['id'];
            $tglBuka  = $row['tanggal_buka'];
            $tglTutup = $row['tanggal_tutup'];

            // Hitung status keaktifan batch berdasarkan rentang tanggal hari ini
            $bukaTs  = strtotime($tglBuka . ' 00:00:00');
            $tutupTs = strtotime($tglTutup . ' 23:59:59');
            $isAktif = ($bukaTs && $tutupTs && $nowTs >= $bukaTs && $nowTs <= $tutupTs);

            // Label status visual untuk antarmuka
            $statusLabel = 'Ditutup / Selesai';
            $statusClass = 'bg-slate-100 text-slate-600 border-slate-200';
            if ($nowTs < $bukaTs) {
                $statusLabel = 'Mendatang';
                $statusClass = 'bg-blue-50 text-blue-700 border-blue-200';
            } elseif ($isAktif) {
                $statusLabel = 'Dibuka / Aktif';
                $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            }

            $batchData = [
                'id'              => $id,
                'nama'            => 'Batch Ujian',
                'tanggal_buka'    => $tglBuka,
                'tanggal_tutup'   => $tglTutup,
                'periode_display' => tgl_indo($tglBuka) . ' s.d. ' . tgl_indo($tglTutup),
                'pembuat'         => !empty($row['nama_pembuat']) ? $row['nama_pembuat'] : ($row['pembuat'] ?: '-'),
                'total_peserta'   => (int)$row['total_peserta'],
                'is_aktif'        => $isAktif,
                'status_label'    => $statusLabel,
                'status_class'    => $statusClass,
            ];

            $allBatches[] = $batchData;

            // Prioritas batch aktif yang ditampilkan:
            // 1. Jika ada ID spesifik yang dipilih oleh operator
            // 2. Jika tidak ada, ambil batch aktif yang tanggalnya sedang berjalan saat ini
            if ($idDiminta > 0 && $id === $idDiminta) {
                $activeBatch = $batchData;
            } elseif ($activeBatch === null && $isAktif && $idDiminta <= 0) {
                $activeBatch = $batchData;
            }
        }
    }

    echo json_encode([
        'status'          => true,
        'ada_batch_aktif' => ($activeBatch !== null),
        'batch_aktif'     => $activeBatch,
        'semua_batch'     => $allBatches,
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
        $simpan = query("INSERT INTO batch_ujian (id, tanggal_buka, tanggal_tutup, pembuat) VALUES ('$nextId', '$tglBuka', '$tglTutup', '$idDosenPembuat')");

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
        $update = query("UPDATE batch_ujian SET tanggal_buka = '$tglBuka', tanggal_tutup = '$tglTutup' WHERE id = '$idBatch'");

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
// AKSI 4: TUTUP / HAPUS BATCH UJIAN
// -----------------------------------------------------------------------------
// Menghapus data batch dari tabel batch_ujian beserta relasi pesertanya
// di tabel peserta_batch.
// =============================================================================
if (isset($_POST['hapus-batch'])) {
    $idBatch = (int)($_POST['id_batch'] ?? 0);

    if ($idBatch <= 0) {
        echo json_encode(['status' => false, 'pesan' => 'ID batch tidak valid.']);
        exit;
    }

    try {
        // 1. Hapus seluruh data peserta yang terdaftar di batch ini terlebih dahulu
        query("DELETE FROM peserta_batch WHERE id_batch = '$idBatch'");

        // 2. Hapus entri batch ujian
        $hapus = query("DELETE FROM batch_ujian WHERE id = '$idBatch'");

        if ($hapus) {
            echo json_encode(['status' => true, 'pesan' => "Batch ujian berhasil ditutup dan dihapus dari sistem."]);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus batch: ' . mysqli_error($koneksi)]);
        }
    } catch (Throwable $e) {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menutup batch: ' . $e->getMessage()]);
    }
    exit;
}


// =============================================================================
// AKSI 5: AMBIL TABEL PESERTA DARI BATCH TERBUKA (SISI KIRI)
// -----------------------------------------------------------------------------
// Mengembalikan baris tabel HTML daftar mahasiswa yang sudah terdaftar
// pada batch ujian aktif saat ini.
// =============================================================================
if (isset($_POST['ambil-tabel-peserta'])) {
    session_write_close();

    $idBatch = (int)($_POST['id_batch'] ?? 0);
    $cari    = bersihkan($_POST['cari'] ?? '');
    $prodi   = bersihkan($_POST['prodi'] ?? '');

    $filterProdi = $isFilterProdi ? $userProdi : $prodi;

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
        echo json_encode(['status' => true, 'data' => $html, 'total' => 0]);
        exit;
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
                    <th class="py-3 px-3 text-center w-8">
                        <input type="checkbox" id="checkAllPeserta" class="rounded accent-orange-500 cursor-pointer">
                    </th>
                    <th class="py-3 px-2 text-center w-8">No</th>
                    <th class="py-3 px-3 font-semibold uppercase tracking-wider">Mahasiswa</th>
                    <th class="py-3 px-3 font-semibold uppercase tracking-wider">Program Studi</th>
                    <th class="py-3 px-3 font-semibold uppercase tracking-wider">Kategori</th>
                    <th class="py-3 px-3 text-center font-semibold uppercase tracking-wider w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <?php foreach ($pesertaList as $p): ?>
                <tr class="hover:bg-slate-50 transition">
                    <!-- Checkbox seleksi massal -->
                    <td class="py-2.5 px-3 text-center">
                        <input type="checkbox" class="cb-peserta rounded accent-orange-500 cursor-pointer" value="<?= tampil($p['id_peserta']) ?>" data-nim="<?= tampil($p['nim']) ?>">
                    </td>
                    <td class="py-2.5 px-2 text-center font-semibold text-slate-400">
                        <?= $no++ ?>
                    </td>
                    <!-- Identitas mahasiswa: Nama, NIM, Angkatan -->
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
                    <!-- Tombol aksi keluarkan dari batch -->
                    <td class="py-2.5 px-3 text-center">
                        <button type="button" 
                            class="btn-keluarkan-peserta inline-flex items-center gap-1 px-2 py-1 rounded text-[11px] font-semibold bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 transition"
                            data-id-peserta="<?= tampil($p['id_peserta']) ?>"
                            data-nim="<?= tampil($p['nim']) ?>"
                            data-nama="<?= tampil($p['nama_mhs']) ?>"
                            title="Keluarkan dari batch ini">
                            <i class="fas fa-user-minus"></i> Keluarkan
                        </button>
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
            <p class="font-semibold text-xs text-slate-700">Belum Ada Peserta dalam Batch Ini</p>
            <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">
                Pilih mahasiswa pada antrian di sebelah kanan untuk dimasukkan ke batch ini.
            </p>
        </div>
        <?php
    }

    $html = ob_get_clean();
    echo json_encode([
        'status' => true,
        'data'   => $html,
        'total'  => count($pesertaList)
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

    $filterProdi = $isFilterProdi ? $userProdi : $prodi;

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

            // Cek apakah mahasiswa sudah terdaftar di batch tujuan ini
            $cek = query("SELECT id FROM peserta_batch WHERE id_batch = '$idBatch' AND mahasiswa = '$nimBersih' LIMIT 1");
            if ($cek && mysqli_num_rows($cek) > 0) {
                $dilewati++;
                continue;
            }

            // Hapus pendaftaran mahasiswa dari batch sebelumnya (jika ada)
            query("DELETE FROM peserta_batch WHERE mahasiswa = '$nimBersih'");

            // Simpan pendaftaran baru ke batch aktif saat ini
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


// =============================================================================
// FUNGSI-FUNGSI PEMBANTU (HELPER FUNCTIONS)
// Diletakkan di bagian paling bawah file sesuai konvensi coding PHP native
// =============================================================================

/**
 * Mengambil daftar mahasiswa yang saat ini sudah terdaftar sebagai peserta
 * dalam batch ujian tertentu.
 *
 * @param int $idBatch ID batch ujian yang sedang dibuka
 * @param string $filterProdi Filter kode prodi jika akun operator dibatasi per prodi
 * @param string $cari Kata kunci pencarian nama atau NIM mahasiswa
 * @return array List data peserta ujian
 */
function ambil_daftar_peserta_batch($idBatch, $filterProdi = '', $cari = '') {
    global $koneksi;
    $idBatch = (int)$idBatch;
    $hasil = [];

    // Filter dasar berdasarkan ID batch
    $where = ["pb.id_batch = '$idBatch'"];
    if ($filterProdi !== '') {
        $pClean = bersihkan($filterProdi);
        $where[] = "(m.prodi = '$pClean' OR pr.kode = '$pClean')";
    }
    if ($cari !== '') {
        $cClean = bersihkan($cari);
        $where[] = "(pb.mahasiswa LIKE '%$cClean%' OR m.nama LIKE '%$cClean%')";
    }
    $whereSql = implode(" AND ", $where);

    // Query data peserta beserta relasi tabel mahasiswa, prodi, dan tahapan SOP skripsi/KP
    $sql = "SELECT pb.id AS id_peserta, pb.id_batch, pb.mahasiswa AS nim,
                   m.nama AS nama_mhs, m.prodi, m.angkatan, pr.nama AS nama_prodi,
                   (SELECT ps.proses FROM progres_skripsi ps WHERE ps.mahasiswa = pb.mahasiswa ORDER BY ps.waktu DESC, ps.proses DESC LIMIT 1) AS step_skripsi,
                   (SELECT pk.proses FROM progres_kp pk WHERE pk.mahasiswa = pb.mahasiswa ORDER BY pk.waktu DESC, pk.proses DESC LIMIT 1) AS step_kp
            FROM peserta_batch pb
            LEFT JOIN mahasiswa m ON pb.mahasiswa = m.nim
            LEFT JOIN prodi pr ON m.prodi = pr.kode
            WHERE $whereSql
            ORDER BY pb.id DESC";

    $q = query($sql);
    if ($q && mysqli_num_rows($q) > 0) {
        while ($r = mysqli_fetch_assoc($q)) {
            $stepSkr = (int)($r['step_skripsi'] ?? 0);
            $stepKp  = (int)($r['step_kp'] ?? 0);

            $tahapanLabel = 'Peserta Ujian';
            $badgeClass   = 'bg-slate-100 text-slate-700';
            $iconClass    = 'fas fa-graduation-cap';

            // Deteksi jenis ujian berdasarkan nomor step SOP
            if (in_array($stepSkr, [8, 9, 10, 11], true)) {
                $tahapanLabel = 'Ujian Hasil';
                $badgeClass   = 'bg-amber-100 text-amber-800 border border-amber-200';
                $iconClass    = 'fas fa-file-alt text-amber-600';
            } elseif (in_array($stepSkr, [16, 17, 18, 19, 20], true)) {
                $tahapanLabel = 'Ujian Tutup';
                $badgeClass   = 'bg-purple-100 text-purple-800 border border-purple-200';
                $iconClass    = 'fas fa-award text-purple-600';
            } elseif (in_array($stepKp, [11, 12, 13, 14, 15], true)) {
                $tahapanLabel = 'Seminar KP';
                $badgeClass   = 'bg-blue-100 text-blue-800 border border-blue-200';
                $iconClass    = 'fas fa-briefcase text-blue-600';
            }

            $hasil[] = [
                'id_peserta'    => (int)$r['id_peserta'],
                'id_batch'      => (int)$r['id_batch'],
                'nim'           => $r['nim'],
                'nama_mhs'      => $r['nama_mhs'] ?: $r['nim'],
                'prodi'         => $r['prodi'] ?? '',
                'nama_prodi'    => $r['nama_prodi'] ?: ($r['prodi'] ?? '-'),
                'angkatan'      => $r['angkatan'] ?? '',
                'tahapan_label' => $tahapanLabel,
                'badge_class'   => $badgeClass,
                'icon_class'    => $iconClass,
            ];
        }
    }

    return $hasil;
}

/**
 * Mengambil antrian mahasiswa yang telah memenuhi kualifikasi untuk ujian
 * dan BELUM terdaftar di batch ujian aktif saat ini.
 * 
 * Aturan kualifikasi:
 * 1. Skripsi Ujian Hasil: Progres SOP 8, 9, atau 10.
 * 2. Skripsi Ujian Tutup: Progres SOP 16, 17, atau 18.
 * 3. Kerja Praktek (KP): Wajib SUDAH ADA penunjukkan penguji seminar KP
 *    (tercatat di kp.surat_penunjukkan_penguji, surat_penunjukkan, atau progres >= 13).
 *
 * @param int $idBatchAktif ID batch aktif yang sedang dibuka (peserta di batch ini di-exclude)
 * @param string $filterProdi Filter prodi operator
 * @param string $kategori Filter kategori ('semua', 'skripsi_hasil', 'skripsi_tutup', 'kp')
 * @param string $cari Kata kunci pencarian
 * @return array List mahasiswa siap ujian
 */
function ambil_antrian_siap_ujian($idBatchAktif, $filterProdi = '', $kategori = 'semua', $cari = '') {
    global $koneksi;
    $idBatchAktif = (int)$idBatchAktif;
    $hasil = [];

    // Kumpulkan daftar NIM mahasiswa yang saat ini SUDAH berada di batch aktif
    // agar mereka tidak muncul dobel di daftar antrian
    $sudahDiBatch = [];
    if ($idBatchAktif > 0) {
        $qSdh = query("SELECT mahasiswa FROM peserta_batch WHERE id_batch = '$idBatchAktif'");
        if ($qSdh) {
            while ($sRow = mysqli_fetch_assoc($qSdh)) {
                $sudahDiBatch[trim((string)$sRow['mahasiswa'])] = true;
            }
        }
    }

    // -------------------------------------------------------------------------
    // 1. DATA ANTRIAN DARI SKRIPSI (Ujian Hasil & Ujian Tutup)
    // -------------------------------------------------------------------------
    if ($kategori === 'semua' || $kategori === 'skripsi_hasil' || $kategori === 'skripsi_tutup') {
        $whereSkr = ["(s.status IS NULL OR s.status = '' OR LOWER(s.status) NOT IN ('tidak aktif', 'alumni', 'kadaluarsa', 'ditolak'))"];
        if ($filterProdi !== '') {
            $pClean = bersihkan($filterProdi);
            $whereSkr[] = "(m.prodi = '$pClean' OR pr.kode = '$pClean')";
        }
        if ($cari !== '') {
            $cClean = bersihkan($cari);
            $whereSkr[] = "(s.nim LIKE '%$cClean%' OR m.nama LIKE '%$cClean%')";
        }
        $whereSqlSkr = implode(" AND ", $whereSkr);

        $sqlSkr = "SELECT s.nim, s.status, m.nama AS nama_mhs, m.prodi, m.angkatan, pr.nama AS nama_prodi,
                          (SELECT ps.proses FROM progres_skripsi ps WHERE ps.mahasiswa = s.nim ORDER BY ps.waktu DESC, ps.proses DESC LIMIT 1) AS step_terbaru
                   FROM skripsi s
                   LEFT JOIN mahasiswa m ON s.nim = m.nim
                   LEFT JOIN prodi pr ON m.prodi = pr.kode
                   WHERE $whereSqlSkr
                   ORDER BY s.nim DESC";

        $qSkr = query($sqlSkr);
        if ($qSkr && mysqli_num_rows($qSkr) > 0) {
            while ($r = mysqli_fetch_assoc($qSkr)) {
                $nim = trim((string)$r['nim']);

                // Lewati jika mahasiswa sudah terdaftar di batch aktif saat ini
                if (isset($sudahDiBatch[$nim])) {
                    continue;
                }

                $step    = (int)($r['step_terbaru'] ?? 0);
                $isHasil = in_array($step, [8, 9, 10], true);
                $isTutup = in_array($step, [16, 17, 18], true);

                if (!$isHasil && !$isTutup) {
                    continue; // Belum mencapai tahap ujian hasil maupun tutup
                }

                if ($kategori === 'skripsi_hasil' && !$isHasil) continue;
                if ($kategori === 'skripsi_tutup' && !$isTutup) continue;

                if ($isHasil) {
                    $tahapanLabel = 'Ujian Hasil';
                    $badgeClass   = 'bg-amber-100 text-amber-800 border border-amber-200';
                    $iconClass    = 'fas fa-file-alt text-amber-600';
                } else {
                    $tahapanLabel = 'Ujian Tutup';
                    $badgeClass   = 'bg-purple-100 text-purple-800 border border-purple-200';
                    $iconClass    = 'fas fa-award text-purple-600';
                }

                $hasil[] = [
                    'sumber'        => 'skripsi',
                    'nim'           => $nim,
                    'nama_mhs'      => $r['nama_mhs'] ?: $nim,
                    'prodi'         => $r['prodi'] ?? '',
                    'nama_prodi'    => $r['nama_prodi'] ?: ($r['prodi'] ?? '-'),
                    'angkatan'      => $r['angkatan'] ?? '',
                    'tahapan_label' => $tahapanLabel,
                    'badge_class'   => $badgeClass,
                    'icon_class'    => $iconClass,
                ];
            }
        }
    }

    // -------------------------------------------------------------------------
    // 2. DATA ANTRIAN DARI KERJA PRAKTEK (Seminar KP)
    // Syarat wajib: Harus sudah ada penunjukkan dosen penguji
    // -------------------------------------------------------------------------
    if ($kategori === 'semua' || $kategori === 'kp') {
        $whereKp = ["(k.status IS NULL OR k.status = '' OR LOWER(k.status) NOT IN ('tidak aktif', 'alumni', 'kadaluarsa', 'ditolak'))"];
        if ($filterProdi !== '') {
            $pClean = bersihkan($filterProdi);
            $whereKp[] = "(m.prodi = '$pClean' OR pr.kode = '$pClean')";
        }
        if ($cari !== '') {
            $cClean = bersihkan($cari);
            $whereKp[] = "(k.nim LIKE '%$cClean%' OR m.nama LIKE '%$cClean%')";
        }
        $whereSqlKp = implode(" AND ", $whereKp);

        $sqlKp = "SELECT k.nim, k.status, k.surat_penunjukkan_penguji, m.nama AS nama_mhs, m.prodi, m.angkatan, pr.nama AS nama_prodi,
                         (SELECT pk.proses FROM progres_kp pk WHERE pk.mahasiswa = k.nim ORDER BY pk.waktu DESC, pk.proses DESC LIMIT 1) AS step_terbaru,
                         (SELECT COUNT(*) FROM surat_penunjukkan sp WHERE sp.nim = k.nim AND (sp.keterangan LIKE '%PENGUJI%KP%' OR sp.keterangan LIKE '%PENGUJI SEMINAR%')) AS ada_surat_penguji
                  FROM kp k
                  LEFT JOIN mahasiswa m ON k.nim = m.nim
                  LEFT JOIN prodi pr ON m.prodi = pr.kode
                  WHERE $whereSqlKp
                  ORDER BY k.nim DESC";

        $qKp = query($sqlKp);
        if ($qKp && mysqli_num_rows($qKp) > 0) {
            while ($r = mysqli_fetch_assoc($qKp)) {
                $nim = trim((string)$r['nim']);

                // Lewati jika sudah ada di batch aktif
                if (isset($sudahDiBatch[$nim])) {
                    continue;
                }

                $step = (int)($r['step_terbaru'] ?? 0);

                // Validasi penunjukkan penguji KP
                $punyaPenguji = (
                    (!empty($r['surat_penunjukkan_penguji']) && (int)$r['surat_penunjukkan_penguji'] > 0) ||
                    ((int)($r['ada_surat_penguji'] ?? 0) > 0) ||
                    ($step >= 13)
                );

                // Lewati jika belum ada penguji
                if (!$punyaPenguji) {
                    continue;
                }

                // Hanya sertakan mahasiswa yang berada pada tahap seminar KP
                if ($step < 12) {
                    continue;
                }

                $hasil[] = [
                    'sumber'        => 'kp',
                    'nim'           => $nim,
                    'nama_mhs'      => $r['nama_mhs'] ?: $nim,
                    'prodi'         => $r['prodi'] ?? '',
                    'nama_prodi'    => $r['nama_prodi'] ?: ($r['prodi'] ?? '-'),
                    'angkatan'      => $r['angkatan'] ?? '',
                    'tahapan_label' => 'Seminar KP',
                    'badge_class'   => 'bg-blue-100 text-blue-800 border border-blue-200',
                    'icon_class'    => 'fas fa-briefcase text-blue-600',
                ];
            }
        }
    }

    return $hasil;
}

/**
 * Mendapatkan ID dosen yang valid untuk mengisi kolom `pembuat` pada tabel `batch_ujian`.
 * Tabel `batch_ujian` memiliki foreign key constraint (batch_ujian_ibfk_1)
 * yang mewajibkan kolom `pembuat` mereferensikan kolom `id` pada tabel `dosen`.
 *
 * @return string ID dosen yang valid
 */
function dapatkan_id_dosen_pembuat() {
    global $koneksi;

    // 1. Coba verifikasi apakah ID pada session login ($_SESSION['id']) terdaftar di tabel dosen
    if (!empty($_SESSION['id'])) {
        $sessId = bersihkan((string)$_SESSION['id']);
        $cek = query("SELECT id FROM dosen WHERE id = '$sessId' LIMIT 1");
        if ($cek && mysqli_num_rows($cek) > 0) {
            $r = mysqli_fetch_assoc($cek);
            return (string)$r['id'];
        }
    }

    // 2. Jika bukan ID numerik langsung, periksa berdasarkan NIP, username, atau user_id
    $nip = bersihkan((string)($_SESSION['nip'] ?? $_SESSION['username'] ?? $_SESSION['user_id'] ?? ''));
    if ($nip !== '') {
        $cek = query("SELECT id FROM dosen WHERE nip = '$nip' OR nidn = '$nip' LIMIT 1");
        if ($cek && mysqli_num_rows($cek) > 0) {
            $r = mysqli_fetch_assoc($cek);
            return (string)$r['id'];
        }
    }

    // 3. Fallback jika operator login dengan akun admin/superadmin yang tidak memiliki record di dosen:
    // Gunakan ID dosen pertama yang ada di database agar foreign key constraint tetap valid
    $cekFirst = query("SELECT id FROM dosen ORDER BY id ASC LIMIT 1");
    if ($cekFirst && mysqli_num_rows($cekFirst) > 0) {
        $r = mysqli_fetch_assoc($cekFirst);
        return (string)$r['id'];
    }

    // Nilai fallback darurat jika tabel dosen kosong sama sekali
    return '1';
}
