<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
require_once __DIR__ . '/../../../../fungsi.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
    exit;
}

// Cek apakah tabel surat_custom sudah dibuat di database dan pastikan seluruh kolom esensial ada
function cek_tabel_surat_custom() {
    $q = query("SHOW TABLES LIKE 'surat_custom'");
    $ada = ($q && mysqli_num_rows($q) > 0);
    if ($ada) {
        $qCol = query("SHOW COLUMNS FROM surat_custom LIKE 'ukuran_kertas'");
        if ($qCol && mysqli_num_rows($qCol) === 0) {
            @query("ALTER TABLE surat_custom ADD COLUMN ukuran_kertas VARCHAR(10) NOT NULL DEFAULT 'A4' AFTER pakai_kop");
        }
        $qColId = query("SHOW COLUMNS FROM surat_custom LIKE 'id_nomor_surat'");
        if ($qColId && mysqli_num_rows($qColId) === 0) {
            @query("ALTER TABLE surat_custom ADD COLUMN id_nomor_surat BIGINT(20) DEFAULT NULL AFTER nomor_surat");
        }
    }
    return $ada;
}

// Sanitasi konten HTML surat kaya (menjaga format tag p, table, img, style tanpa XSS berbahaya)
function bersihkan_html_surat($html) {
    global $koneksi;
    if (empty($html)) return '';
    $html = trim($html);
    $html = stripslashes($html);
    // Hapus script berbahaya & event handler injection
    $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
    $html = preg_replace('#<iframe(.*?)>(.*?)</iframe>#is', '', $html);
    $html = preg_replace('#\s*on\w+\s*=\s*(["\']).*?\1#is', '', $html);
    $html = preg_replace('#\s*on\w+\s*=\s*[^>\s]+#is', '', $html);
    $html = preg_replace('#href\s*=\s*(["\'])\s*javascript:[^"\']*?\1#is', '', $html);
    return mysqli_real_escape_string($koneksi, $html);
}

// ================= AMBIL KONTEN TABEL SURAT CUSTOM =================
if (isset($_POST['ambil-tabel-surat-custom'])) {
    if (!cek_tabel_surat_custom()) {
        ob_start();
        ?>
        <tr class="empty-row">
            <td colspan="7" class="py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-2xl text-amber-500 shadow-sm mb-3">
                    <i class="fas fa-database"></i>
                </div>
                <p class="font-bold text-slate-700 text-sm">Tabel Database Belum Dibuat</p>
                <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                    Tabel <code class="bg-slate-100 text-amber-700 px-1.5 py-0.5 rounded font-mono font-semibold">surat_custom</code> belum ditemukan di MySQL. Silakan jalankan script SQL yang diberikan untuk mulai menyimpan surat.
                </p>
            </td>
        </tr>
        <?php
        $html = ob_get_clean();
        echo json_encode(['status' => true, 'data' => $html, 'tabel_ada' => false]);
        exit;
    }

    $q = query("SELECT * FROM surat_custom ORDER BY tanggal_surat DESC, id DESC");

    ob_start();
    if ($q && mysqli_num_rows($q) > 0) {
        $no = 1;
        while ($row = mysqli_fetch_assoc($q)) {
            $tglFmt = function_exists('tgl_indo') ? tgl_indo($row['tanggal_surat']) : date('d M Y', strtotime($row['tanggal_surat']));
            $tahunSurat = date('Y', strtotime($row['tanggal_surat']));
            $pakaiKop = (int)($row['pakai_kop'] ?? 1);
            $ukuranKertas = strtoupper($row['ukuran_kertas'] ?? 'A4');
            if ($ukuranKertas !== 'F4') $ukuranKertas = 'A4';
            $urlCetak = base_url() . '/dokumen/surat-custom/' . $row['id'] . '?kertas=' . $ukuranKertas;
            $cariStr = strtolower(
                $row['nomor_surat'] . ' ' .
                $row['perihal'] . ' ' .
                $ukuranKertas . ' ' .
                ($row['tujuan'] ?? '') . ' ' .
                ($row['ttd_nama'] ?? '') . ' ' .
                ($row['ttd_jabatan'] ?? '') . ' ' .
                ($row['kategori'] ?? '')
            );
            ?>
            <tr class="transition hover:bg-slate-50 <?= ($no % 2 == 0) ? 'bg-slate-50/50' : 'bg-white' ?>"
                data-id="<?= $row['id'] ?>"
                data-tahun="<?= $tahunSurat ?>"
                data-kategori="<?= htmlspecialchars($row['kategori'] ?? 'Lainnya', ENT_QUOTES, 'UTF-8') ?>"
                data-ttd-jenis="<?= htmlspecialchars($row['ttd_jenis'] ?? 'kajur', ENT_QUOTES, 'UTF-8') ?>"
                data-pakai-kop="<?= $pakaiKop ?>"
                data-ukuran-kertas="<?= $ukuranKertas ?>"
                data-cari="<?= htmlspecialchars($cariStr, ENT_QUOTES, 'UTF-8') ?>">
                
                <td class="text-center font-semibold text-slate-500"><?= $no ?></td>
                
                <td class="font-semibold text-slate-800 whitespace-nowrap">
                    <?= htmlspecialchars($row['nomor_surat'], ENT_QUOTES, 'UTF-8') ?>
                </td>
                
                <td class="whitespace-nowrap text-slate-600">
                    <?= $tglFmt ?>
                </td>
                
                <td class="max-w-[260px]">
                    <p class="font-medium text-slate-800 line-clamp-1" title="<?= htmlspecialchars($row['perihal'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($row['perihal'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <span class="text-[10px] text-slate-400 font-medium">
                        <?= htmlspecialchars($row['kategori'] ?? 'Surat', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </td>
                
                <td class="text-slate-600">
                    <?php if (!empty($row['tujuan'])): ?>
                        <span class="line-clamp-1" title="<?= htmlspecialchars($row['tujuan'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($row['tujuan'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php else: ?>
                        <span class="text-slate-400">—</span>
                    <?php endif; ?>
                </td>
                
                <td>
                    <p class="font-semibold text-slate-700"><?= htmlspecialchars($row['ttd_nama'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="text-[11px] text-slate-400"><?= htmlspecialchars($row['ttd_jabatan'], ENT_QUOTES, 'UTF-8') ?></p>
                </td>
                
                <td class="text-center whitespace-nowrap">
                    <div class="flex flex-col items-center gap-1">
                        <?php if ($pakaiKop === 1): ?>
                            <span class="pt-badge bg-emerald-50 text-emerald-700">Kop: Ya</span>
                        <?php else: ?>
                            <span class="pt-badge bg-slate-100 text-slate-500">Tanpa Kop</span>
                        <?php endif; ?>
                        <span class="pt-badge text-[10px] font-bold <?= ($ukuranKertas === 'F4') ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-sky-100 text-sky-700 border border-sky-200' ?>"><?= $ukuranKertas ?></span>
                    </div>
                </td>
                
                <td class="text-right whitespace-nowrap">
                    <div class="inline-flex items-center justify-end gap-1.5">
                        <button type="button" class="btnLihatSurat btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700" data-id="<?= $row['id'] ?>" title="Pratinjau Lembar <?= $ukuranKertas ?>">
                            <i class="fas fa-eye text-xs"></i><span class="tip">Lihat <?= $ukuranKertas ?></span>
                        </button>
                        <a href="<?= $urlCetak ?>" target="_blank" rel="noopener" class="btn-circle bg-emerald-600 text-white shadow-sm hover:bg-emerald-700" title="Cetak Format <?= $ukuranKertas ?>">
                            <i class="fas fa-print text-xs"></i><span class="tip">Cetak <?= $ukuranKertas ?></span>
                        </a>
                        <button type="button" class="btnEditSurat btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600" data-id="<?= $row['id'] ?>" title="Edit Surat">
                            <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                        </button>
                        <button type="button" class="btnHapusSurat btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="<?= $row['id'] ?>" title="Hapus Surat">
                            <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                        </button>
                    </div>
                </td>
            </tr>
            <?php
            $no++;
        }
    } else {
        ?>
        <tr class="empty-row">
            <td colspan="7" class="py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-50 text-2xl text-orange-500 shadow-sm mb-3">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <p class="font-bold text-slate-700 text-sm">Belum Ada Surat Custom</p>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Klik tombol <b>+ Buat Surat</b> di atas untuk menyusun surat kustom resmi jurusan.</p>
            </td>
        </tr>
        <?php
    }
    $html = ob_get_clean();

    echo json_encode(['status' => true, 'data' => $html, 'tabel_ada' => true]);
    exit;
}

// ================= AMBIL STATISTIK SURAT =================
if (isset($_POST['ambil-statistik-surat-custom'])) {
    if (!cek_tabel_surat_custom()) {
        echo json_encode([
            'status' => true,
            'data' => [
                'total' => 0,
                'pakai_kop' => 0,
                'tanpa_kop' => 0,
                'tahun_ini' => 0
            ]
        ]);
        exit;
    }

    $tahunIni = date('Y');
    $qTotal = mysqli_fetch_assoc(query("SELECT COUNT(*) AS jml FROM surat_custom"))['jml'] ?? 0;
    $qKop = mysqli_fetch_assoc(query("SELECT COUNT(*) AS jml FROM surat_custom WHERE pakai_kop = 1"))['jml'] ?? 0;
    $qNoKop = mysqli_fetch_assoc(query("SELECT COUNT(*) AS jml FROM surat_custom WHERE pakai_kop = 0"))['jml'] ?? 0;
    $qTahun = mysqli_fetch_assoc(query("SELECT COUNT(*) AS jml FROM surat_custom WHERE YEAR(tanggal_surat) = '$tahunIni'"))['jml'] ?? 0;

    echo json_encode([
        'status' => true,
        'data' => [
            'total' => (int)$qTotal,
            'pakai_kop' => (int)$qKop,
            'tanpa_kop' => (int)$qNoKop,
            'tahun_ini' => (int)$qTahun
        ]
    ]);
    exit;
}

// ================= AMBIL FORMAT NOMOR SURAT & GENERATE NOMOR =================
if (isset($_POST['ambil-format-nomor-surat'])) {
    $q = query("SELECT id, nama_surat, format_nomor FROM format_nomor_surat ORDER BY id ASC");
    $list = [];
    $tahun = date('Y');

    // Ambil nomor urutan tertinggi di tahun ini dari tabel nomor_surat
    $urutanBerikutnya = 1;
    $cek_nomor = query("SELECT urutan FROM `nomor_surat` WHERE YEAR(tanggal) = '$tahun' ORDER BY CAST(urutan AS UNSIGNED) DESC, id DESC LIMIT 1");
    if ($cek_nomor && mysqli_num_rows($cek_nomor) > 0) {
        $data_nomor = mysqli_fetch_assoc($cek_nomor);
        $urutanBerikutnya = ((int)$data_nomor['urutan'] + 1);
    }
    $urutanStr = sprintf('%03d', $urutanBerikutnya);

    if ($q) {
        while ($r = mysqli_fetch_assoc($q)) {
            $fmtClean = trim((string)$r['format_nomor']);
            // Pastikan diawali slash jika belum ada
            if ($fmtClean !== '' && substr($fmtClean, 0, 1) !== '/') {
                $fmtClean = '/' . $fmtClean;
            }
            // Pastikan diakhiri slash jika belum ada
            if ($fmtClean !== '' && substr($fmtClean, -1) !== '/') {
                $fmtClean .= '/';
            }
            $nomorPreview = $urutanStr . $fmtClean . $tahun;
            $list[] = [
                'id' => $r['id'],
                'nama_surat' => trim($r['nama_surat']),
                'format_nomor' => $r['format_nomor'],
                'nomor_preview' => $nomorPreview
            ];
        }
    }

    echo json_encode([
        'status' => true,
        'data' => $list,
        'urutan' => $urutanStr,
        'tahun' => $tahun
    ]);
    exit;
}

// ================= AMBIL NOMOR SURAT OTOMATIS =================
if (isset($_POST['ambil-nomor-otomatis'])) {
    $formatId = isset($_POST['format_id']) ? (int)$_POST['format_id'] : 0;
    $tahun = date('Y');

    // Ambil nomor urutan tertinggi di tahun ini dari tabel nomor_surat
    $urutanBerikutnya = 1;
    $cek_nomor = query("SELECT urutan FROM `nomor_surat` WHERE YEAR(tanggal) = '$tahun' ORDER BY CAST(urutan AS UNSIGNED) DESC, id DESC LIMIT 1");
    if ($cek_nomor && mysqli_num_rows($cek_nomor) > 0) {
        $data_nomor = mysqli_fetch_assoc($cek_nomor);
        $urutanBerikutnya = ((int)$data_nomor['urutan'] + 1);
    }
    $urutanStr = sprintf('%03d', $urutanBerikutnya);

    $nomor = '';
    if ($formatId > 0) {
        $qF = query("SELECT * FROM format_nomor_surat WHERE id = '$formatId' LIMIT 1");
        if ($qF && ($rF = mysqli_fetch_assoc($qF))) {
            $fmtClean = trim((string)$rF['format_nomor']);
            if ($fmtClean !== '' && substr($fmtClean, 0, 1) !== '/') {
                $fmtClean = '/' . $fmtClean;
            }
            if ($fmtClean !== '' && substr($fmtClean, -1) !== '/') {
                $fmtClean .= '/';
            }
            $nomor = $urutanStr . $fmtClean . $tahun;
        }
    }

    if (empty($nomor)) {
        $kategori = bersihkan($_POST['kategori'] ?? 'surat custom');
        $nomor = function_exists('get_nomor_surat') ? get_nomor_surat($kategori) : '';
    }
    
    // Fallback bila helper belum ada
    if (empty($nomor) || $nomor === 'Tidak Ditemukan') {
        $nomor = $urutanStr . '/UN47.B5.5/TD.06/' . $tahun;
    }

    echo json_encode(['status' => true, 'nomor' => $nomor]);
    exit;
}

// ================= AMBIL DAFTAR PROGRAM STUDI =================
if (isset($_POST['ambil-daftar-prodi'])) {
    $q = query("SELECT kode, nama, jenjang, status FROM prodi ORDER BY nama ASC");
    $list = [];
    if ($q) {
        while ($r = mysqli_fetch_assoc($q)) {
            $list[] = $r;
        }
    }
    echo json_encode(['status' => true, 'data' => $list]);
    exit;
}

// ================= UPLOAD GAMBAR SURAT =================
if (isset($_POST['upload-gambar-surat']) || isset($_FILES['gambar_surat'])) {
    if (!isset($_FILES['gambar_surat']) || $_FILES['gambar_surat']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => false, 'pesan' => 'Tidak ada file gambar yang diunggah']);
        exit;
    }

    $file = $_FILES['gambar_surat'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode(['status' => false, 'pesan' => 'Format file tidak didukung. Harap unggah gambar JPG, PNG, atau WebP']);
        exit;
    }

    $folderTujuan = dirname(__DIR__, 4) . '/file';
    if (!is_dir($folderTujuan)) {
        @mkdir($folderTujuan, 0777, true);
    }

    $namaFileBaru = 'surat_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetPath = $folderTujuan . '/' . $namaFileBaru;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $urlGambar = base_url() . '/file/' . $namaFileBaru;
        echo json_encode(['status' => true, 'pesan' => 'Gambar berhasil disisipkan', 'url' => $urlGambar]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal memindahkan file gambar']);
    }
    exit;
}

// ================= AMBIL DATA PEJABAT TTD =================
if (isset($_POST['ambil-data-pejabat'])) {
    $jenis = bersihkan($_POST['jenis'] ?? 'kajur');
    $prodiKode = bersihkan($_POST['prodi'] ?? '');

    $res = [
        'dosen_id' => '',
        'nama'     => '',
        'nip'      => '',
        'jabatan'  => ''
    ];

    if ($jenis === 'kajur') {
        $q = query("SELECT j.kajur, d.nama AS nama_dosen, d.nip AS nip_dosen FROM jurusan j LEFT JOIN dosen d ON j.kajur = d.id LIMIT 1");
        if ($q && ($r = mysqli_fetch_assoc($q))) {
            $res['dosen_id'] = $r['kajur'] ?? '';
            $res['nama']     = $r['nama_dosen'] ?? '';
            $res['nip']      = $r['nip_dosen'] ?? '';
            $res['jabatan']  = 'Ketua Jurusan';
        }
    } elseif ($jenis === 'sekjur') {
        $q = query("SELECT j.sekjur, d.nama AS nama_dosen, d.nip AS nip_dosen FROM jurusan j LEFT JOIN dosen d ON j.sekjur = d.id LIMIT 1");
        if ($q && ($r = mysqli_fetch_assoc($q))) {
            $res['dosen_id'] = $r['sekjur'] ?? '';
            $res['nama']     = $r['nama_dosen'] ?? '';
            $res['nip']      = $r['nip_dosen'] ?? '';
            $res['jabatan']  = 'Sekretaris Jurusan';
        }
    } elseif ($jenis === 'kaprodi') {
        $whereProdi = !empty($prodiKode) ? "WHERE p.kode = '$prodiKode'" : "LIMIT 1";
        $q = query("SELECT p.kode, p.nama AS nama_prodi, p.jenjang, p.kaprodi, d.nama AS nama_dosen, d.nip AS nip_dosen FROM prodi p LEFT JOIN dosen d ON (p.kaprodi = d.id OR p.kaprodi = d.nip) $whereProdi LIMIT 1");
        if ($q && ($r = mysqli_fetch_assoc($q))) {
            $namaProdiBersih = preg_replace('/^(S1|D3|D4|S2|S3)\s+/i', '', trim($r['nama_prodi']));
            $res['dosen_id'] = $r['kaprodi'] ?? '';
            $res['nama']     = $r['nama_dosen'] ?? '';
            $res['nip']      = $r['nip_dosen'] ?? '';
            $res['jabatan']  = 'Ketua Program Studi ' . $namaProdiBersih;
        }
    }

    echo json_encode(['status' => true, 'data' => $res]);
    exit;
}

// ================= TAMBAH DATA SURAT CUSTOM =================
if (isset($_POST['tambah-data-surat-custom'])) {
    if (!cek_tabel_surat_custom()) {
        echo json_encode(['status' => false, 'pesan' => 'Tabel surat_custom belum dibuat di database. Harap jalankan script SQL terlebih dahulu.']);
        exit;
    }

    $nomorSurat   = bersihkan($_POST['nomor_surat'] ?? '');
    $kategori     = bersihkan($_POST['kategori'] ?? 'Undangan');
    $perihal      = bersihkan($_POST['perihal'] ?? '');
    $tanggalSurat = bersihkan($_POST['tanggal_surat'] ?? date('Y-m-d'));
    $tujuan       = bersihkan($_POST['tujuan'] ?? '');
    $pakaiKop     = isset($_POST['pakai_kop']) ? (int)$_POST['pakai_kop'] : 1;
    $ukuranKertas = strtoupper(bersihkan($_POST['ukuran_kertas'] ?? 'A4'));
    if ($ukuranKertas !== 'F4') $ukuranKertas = 'A4';
    $isiSurat     = bersihkan_html_surat($_POST['isi_surat'] ?? '');
    $penutup      = bersihkan($_POST['penutup'] ?? '');
    $ttdJenis     = bersihkan($_POST['ttd_jenis'] ?? 'kajur');
    $ttdProdi     = bersihkan($_POST['ttd_prodi'] ?? '');
    $ttdDosenId   = bersihkan($_POST['ttd_dosen_id'] ?? '');
    $ttdJabatan   = bersihkan($_POST['ttd_jabatan'] ?? '');
    $ttdNama      = bersihkan($_POST['ttd_nama'] ?? '');
    $ttdNip       = bersihkan($_POST['ttd_nip'] ?? '');
    $tembusan     = bersihkan($_POST['tembusan'] ?? '');
    $pembuat      = $_SESSION['username'] ?? ($_SESSION['user_id'] ?? 'operator');

    if (empty($perihal)) {
        echo json_encode(['status' => false, 'pesan' => 'Perihal / Hal surat wajib diisi']);
        exit;
    }
    if (empty($isiSurat)) {
        echo json_encode(['status' => false, 'pesan' => 'Isi / Badan surat wajib diisi']);
        exit;
    }

    // Jika nomor surat masih kosong, generate otomatis
    if (empty($nomorSurat)) {
        $nomorSurat = function_exists('get_nomor_surat') ? get_nomor_surat('surat custom') : ('001/UN47.B5.5/TD.06/' . date('Y'));
    }

    // Catat nomor surat ke master nomor_surat jika helper tersedia
    $idNomorSurat = 'NULL';
    if (function_exists('tambah_nomor')) {
        $idReg = tambah_nomor($nomorSurat, "Surat Custom: " . $perihal);
        if ($idReg && is_numeric($idReg)) {
            $idNomorSurat = (int)$idReg;
        }
    }

    $ttdDosenIdVal = (!empty($ttdDosenId) && is_numeric($ttdDosenId)) ? (int)$ttdDosenId : "NULL";

    $sql = "INSERT INTO surat_custom (
        nomor_surat, id_nomor_surat, kategori, perihal, tanggal_surat,
        tujuan, pakai_kop, ukuran_kertas, isi_surat, penutup, ttd_jenis,
        ttd_prodi, ttd_dosen_id, ttd_jabatan, ttd_nama, ttd_nip,
        tembusan, pembuat
    ) VALUES (
        '$nomorSurat', $idNomorSurat, '$kategori', '$perihal', '$tanggalSurat',
        '$tujuan', $pakaiKop, '$ukuranKertas', '$isiSurat', '$penutup', '$ttdJenis',
        '$ttdProdi', $ttdDosenIdVal, '$ttdJabatan', '$ttdNama', '$ttdNip',
        '$tembusan', '$pembuat'
    )";

    $simpan = query($sql);

    if ($simpan) {
        $insertId = mysqli_insert_id($koneksi);
        echo json_encode(['status' => true, 'pesan' => 'Surat custom berhasil disimpan', 'id' => $insertId]);
    } else {
        $dbError = mysqli_error($koneksi);
        echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan surat custom ke database' . ($dbError ? ': ' . $dbError : '')]);
    }
    exit;
}

// ================= AMBIL DETAIL SURAT (UNTUK EDIT) =================
if (isset($_POST['ambil-detail-surat'])) {
    if (!cek_tabel_surat_custom()) {
        echo json_encode(['status' => false, 'pesan' => 'Tabel belum ada']);
        exit;
    }

    $id = bersihkan($_POST['id'] ?? '');
    $q = query("SELECT * FROM surat_custom WHERE id = '$id' LIMIT 1");

    if ($q && ($row = mysqli_fetch_assoc($q))) {
        echo json_encode(['status' => true, 'data' => $row]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data surat tidak ditemukan']);
    }
    exit;
}

// ================= EDIT DATA SURAT CUSTOM =================
if (isset($_POST['edit-data-surat-custom'])) {
    if (!cek_tabel_surat_custom()) {
        echo json_encode(['status' => false, 'pesan' => 'Tabel belum ada']);
        exit;
    }

    $id           = bersihkan($_POST['id'] ?? '');
    $nomorSurat   = bersihkan($_POST['nomor_surat'] ?? '');
    $kategori     = bersihkan($_POST['kategori'] ?? 'Undangan');
    $perihal      = bersihkan($_POST['perihal'] ?? '');
    $tanggalSurat = bersihkan($_POST['tanggal_surat'] ?? date('Y-m-d'));
    $tujuan       = bersihkan($_POST['tujuan'] ?? '');
    $pakaiKop     = isset($_POST['pakai_kop']) ? (int)$_POST['pakai_kop'] : 1;
    $ukuranKertas = strtoupper(bersihkan($_POST['ukuran_kertas'] ?? 'A4'));
    if ($ukuranKertas !== 'F4') $ukuranKertas = 'A4';
    $isiSurat     = bersihkan_html_surat($_POST['isi_surat'] ?? '');
    $penutup      = bersihkan($_POST['penutup'] ?? '');
    $ttdJenis     = bersihkan($_POST['ttd_jenis'] ?? 'kajur');
    $ttdProdi     = bersihkan($_POST['ttd_prodi'] ?? '');
    $ttdDosenId   = bersihkan($_POST['ttd_dosen_id'] ?? '');
    $ttdJabatan   = bersihkan($_POST['ttd_jabatan'] ?? '');
    $ttdNama      = bersihkan($_POST['ttd_nama'] ?? '');
    $ttdNip       = bersihkan($_POST['ttd_nip'] ?? '');
    $tembusan     = bersihkan($_POST['tembusan'] ?? '');

    if (empty($id)) {
        echo json_encode(['status' => false, 'pesan' => 'ID surat tidak valid']);
        exit;
    }
    if (empty($perihal)) {
        echo json_encode(['status' => false, 'pesan' => 'Perihal / Hal surat wajib diisi']);
        exit;
    }
    if (empty($isiSurat)) {
        echo json_encode(['status' => false, 'pesan' => 'Isi surat tidak boleh kosong']);
        exit;
    }

    $ttdDosenIdVal = (!empty($ttdDosenId) && is_numeric($ttdDosenId)) ? (int)$ttdDosenId : "NULL";

    $sql = "UPDATE surat_custom SET
        nomor_surat   = '$nomorSurat',
        kategori      = '$kategori',
        perihal       = '$perihal',
        tanggal_surat = '$tanggalSurat',
        tujuan        = '$tujuan',
        pakai_kop     = $pakaiKop,
        ukuran_kertas = '$ukuranKertas',
        isi_surat     = '$isiSurat',
        penutup       = '$penutup',
        ttd_jenis     = '$ttdJenis',
        ttd_prodi     = '$ttdProdi',
        ttd_dosen_id  = $ttdDosenIdVal,
        ttd_jabatan   = '$ttdJabatan',
        ttd_nama      = '$ttdNama',
        ttd_nip       = '$ttdNip',
        tembusan      = '$tembusan'
        WHERE id      = '$id'";

    $update = query($sql);

    if ($update) {
        echo json_encode(['status' => true, 'pesan' => 'Data surat custom berhasil diperbarui']);
    } else {
        $dbError = mysqli_error($koneksi);
        echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui surat' . ($dbError ? ': ' . $dbError : '')]);
    }
    exit;
}

// ================= HAPUS DATA SURAT CUSTOM =================
if (isset($_POST['hapus-data-surat-custom'])) {
    if (!cek_tabel_surat_custom()) {
        echo json_encode(['status' => false, 'pesan' => 'Tabel belum ada']);
        exit;
    }

    $id = bersihkan($_POST['id'] ?? '');
    if (empty($id)) {
        echo json_encode(['status' => false, 'pesan' => 'ID surat tidak valid']);
        exit;
    }

    $hapus = query("DELETE FROM surat_custom WHERE id = '$id'");

    if ($hapus) {
        echo json_encode(['status' => true, 'pesan' => 'Surat berhasil dihapus']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus surat']);
    }
    exit;
}
