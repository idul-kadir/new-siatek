<?php
/**
 * AJAX PROSES MANIPULASI BASIS DATA (SUPERADMIN)
 * Khusus Superadmin (Level 9 / tipe_login 'superadmin')
 * Mengelola 6 tabel inti sistem:
 * 1. progres_kp
 * 2. progres_skripsi
 * 3. temp_kp
 * 4. temp_skripsi
 * 5. format_nomor_surat
 * 6. nomor_surat
 */
error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once __DIR__ . '/../../../fungsi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// 1. Validasi Keamanan Superadmin (Strict Level 9 / tipe_login superadmin)
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
$userLevel  = (int)($_SESSION['level'] ?? 0);
$tipeLogin  = strtolower(trim((string)($_SESSION['tipe_login'] ?? '')));

if (!$sudahLogin || ($userLevel !== 9 && $tipeLogin !== 'superadmin')) {
    echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: Hanya Super Administrator yang memiliki wewenang manipulasi basis data.']);
    exit;
}

$action = trim((string)($_POST['action'] ?? ''));

// =========================================================================
// 1. STATS RINGKASAN RECORD 6 TABEL
// =========================================================================
if ($action === 'ambil-ringkasan-tabel') {
    $qPKp = query("SELECT COUNT(*) AS c FROM progres_kp");
    $cPKp = $qPKp ? (int)mysqli_fetch_assoc($qPKp)['c'] : 0;

    $qPSkr = query("SELECT COUNT(*) AS c FROM progres_skripsi");
    $cPSkr = $qPSkr ? (int)mysqli_fetch_assoc($qPSkr)['c'] : 0;

    $qTKp = query("SELECT COUNT(*) AS c FROM temp_kp");
    $cTKp = $qTKp ? (int)mysqli_fetch_assoc($qTKp)['c'] : 0;

    $qTSkr = query("SELECT COUNT(*) AS c FROM temp_skripsi");
    $cTSkr = $qTSkr ? (int)mysqli_fetch_assoc($qTSkr)['c'] : 0;

    $qFmt = query("SELECT COUNT(*) AS c FROM format_nomor_surat");
    $cFmt = $qFmt ? (int)mysqli_fetch_assoc($qFmt)['c'] : 0;

    $qNo = query("SELECT COUNT(*) AS c FROM nomor_surat");
    $cNo = $qNo ? (int)mysqli_fetch_assoc($qNo)['c'] : 0;

    echo json_encode([
        'status' => true,
        'data' => [
            'progres_kp'          => $cPKp,
            'progres_skripsi'     => $cPSkr,
            'temp_kp'             => $cTKp,
            'temp_skripsi'        => $cTSkr,
            'format_nomor_surat'  => $cFmt,
            'nomor_surat'         => $cNo,
        ]
    ]);
    exit;
}

// =========================================================================
// 2. HELPER MASTER LOOKUPS
// =========================================================================
if ($action === 'ambil-master-lookup') {
    $targetTabel = trim((string)($_POST['tabel'] ?? ''));

    // Master SOP KP (Memakai helper get_sop_kp_list agar kebal terhadap variasi nama kolom DB)
    $sopKp = function_exists('get_sop_kp_list') ? get_sop_kp_list() : [];

    // Master SOP Skripsi
    $sopSkripsi = [];
    $qSSkr = query("SELECT no, keterangan FROM sop_skripsi ORDER BY CAST(no AS UNSIGNED) ASC");
    if ($qSSkr) {
        while ($r = mysqli_fetch_assoc($qSSkr)) { $sopSkripsi[] = $r; }
    }

    // Master Syarat Berkas KP
    $syaratKp = [];
    $qSyKp = query("SELECT id, nama_berkas, progres FROM persyaratan_berkas_kp ORDER BY progres ASC, id ASC");
    if ($qSyKp) {
        while ($r = mysqli_fetch_assoc($qSyKp)) { $syaratKp[] = $r; }
    }

    // Master Syarat Berkas Skripsi
    $syaratSkripsi = [];
    $qSySkr = query("SELECT id, nama_berkas, progres FROM persyaratan_berkas_skripsi ORDER BY progres ASC, id ASC");
    if ($qSySkr) {
        while ($r = mysqli_fetch_assoc($qSySkr)) { $syaratSkripsi[] = $r; }
    }

    echo json_encode([
        'status'         => true,
        'sop_kp'         => $sopKp,
        'sop_skripsi'    => $sopSkripsi,
        'syarat_kp'      => $syaratKp,
        'syarat_skripsi' => $syaratSkripsi,
    ]);
    exit;
}

// =========================================================================
// 3. READ DATA TABEL DINAMIS (SEARCH + PAGINASI + RELATION ENRICHMENT)
// =========================================================================
if ($action === 'ambil-data-tabel') {
    $tabel = trim((string)($_POST['tabel'] ?? ''));
    $cari  = trim((string)($_POST['cari'] ?? ''));
    $page  = max(1, (int)($_POST['page'] ?? 1));
    $limit = max(5, min(100, (int)($_POST['limit'] ?? 10)));
    $offset = ($page - 1) * $limit;

    $cariClean = bersihkan($cari);

    switch ($tabel) {
        // -------------------------------------------------------------
        // TABEL: progres_kp
        // -------------------------------------------------------------
        case 'progres_kp':
            $where = "1=1";
            if ($cariClean !== '') {
                $where .= " AND (pk.mahasiswa LIKE '%$cariClean%' OR m.nama LIKE '%$cariClean%' OR pk.proses LIKE '%$cariClean%')";
            }
            $qCount = query("SELECT COUNT(*) AS c FROM progres_kp pk LEFT JOIN mahasiswa m ON pk.mahasiswa = m.nim WHERE $where");
            $totalRows = $qCount ? (int)mysqli_fetch_assoc($qCount)['c'] : 0;

            $sopMap = [];
            $sopList = function_exists('get_sop_kp_list') ? get_sop_kp_list() : [];
            foreach ($sopList as $sItem) {
                $sopMap[(int)$sItem['no']] = $sItem['keterangan'];
            }

            $sql = "SELECT pk.*, m.nama AS nama_mhs, m.prodi, p.nama AS nama_prodi
                    FROM progres_kp pk
                    LEFT JOIN mahasiswa m ON pk.mahasiswa = m.nim
                    LEFT JOIN prodi p ON m.prodi = p.kode
                    WHERE $where
                    ORDER BY pk.waktu DESC, pk.proses DESC
                    LIMIT $limit OFFSET $offset";
            $res = query($sql);
            $rows = [];
            if ($res) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $pNum = (int)($r['proses'] ?? 0);
                    $r['nama_proses'] = $sopMap[$pNum] ?? ('Langkah SOP #' . $pNum);
                    $waktuInt = is_numeric($r['waktu']) ? (int)$r['waktu'] : 0;
                    $r['waktu_format'] = $waktuInt > 0 ? date('d/m/Y H:i:s', $waktuInt) : '-';
                    $rows[] = $r;
                }
            }
            echo json_encode([
                'status'     => true,
                'tabel'      => $tabel,
                'total'      => $totalRows,
                'page'       => $page,
                'limit'      => $limit,
                'total_pages'=> ceil($totalRows / $limit),
                'rows'       => $rows
            ]);
            exit;

        // -------------------------------------------------------------
        // TABEL: progres_skripsi
        // -------------------------------------------------------------
        case 'progres_skripsi':
            $where = "1=1";
            if ($cariClean !== '') {
                $where .= " AND (ps.mahasiswa LIKE '%$cariClean%' OR m.nama LIKE '%$cariClean%' OR ps.proses LIKE '%$cariClean%')";
            }
            $qCount = query("SELECT COUNT(*) AS c FROM progres_skripsi ps LEFT JOIN mahasiswa m ON ps.mahasiswa = m.nim WHERE $where");
            $totalRows = $qCount ? (int)mysqli_fetch_assoc($qCount)['c'] : 0;

            $sql = "SELECT ps.*, m.nama AS nama_mhs, m.prodi, p.nama AS nama_prodi, sop.keterangan AS nama_proses
                    FROM progres_skripsi ps
                    LEFT JOIN mahasiswa m ON ps.mahasiswa = m.nim
                    LEFT JOIN prodi p ON m.prodi = p.kode
                    LEFT JOIN sop_skripsi sop ON ps.proses = sop.no
                    WHERE $where
                    ORDER BY ps.waktu DESC, ps.proses DESC
                    LIMIT $limit OFFSET $offset";
            $res = query($sql);
            $rows = [];
            if ($res) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $waktuInt = is_numeric($r['waktu']) ? (int)$r['waktu'] : 0;
                    $r['waktu_format'] = $waktuInt > 0 ? date('d/m/Y H:i:s', $waktuInt) : '-';
                    $rows[] = $r;
                }
            }
            echo json_encode([
                'status'     => true,
                'tabel'      => $tabel,
                'total'      => $totalRows,
                'page'       => $page,
                'limit'      => $limit,
                'total_pages'=> ceil($totalRows / $limit),
                'rows'       => $rows
            ]);
            exit;

        // -------------------------------------------------------------
        // TABEL: temp_kp
        // -------------------------------------------------------------
        case 'temp_kp':
            $where = "1=1";
            if ($cariClean !== '') {
                $where .= " AND (tk.nim LIKE '%$cariClean%' OR m.nama LIKE '%$cariClean%' OR tk.status LIKE '%$cariClean%' OR pbk.nama_berkas LIKE '%$cariClean%')";
            }
            $qCount = query("SELECT COUNT(*) AS c FROM temp_kp tk LEFT JOIN mahasiswa m ON tk.nim = m.nim LEFT JOIN persyaratan_berkas_kp pbk ON tk.id_persyaratan = pbk.id WHERE $where");
            $totalRows = $qCount ? (int)mysqli_fetch_assoc($qCount)['c'] : 0;

            $sql = "SELECT tk.*, m.nama AS nama_mhs, m.prodi, p.nama AS nama_prodi, pbk.nama_berkas, pbk.progres AS syarat_progres, f.nama_file
                    FROM temp_kp tk
                    LEFT JOIN mahasiswa m ON tk.nim = m.nim
                    LEFT JOIN prodi p ON m.prodi = p.kode
                    LEFT JOIN persyaratan_berkas_kp pbk ON tk.id_persyaratan = pbk.id
                    LEFT JOIN files f ON tk.file = f.id
                    WHERE $where
                    ORDER BY tk.id DESC
                    LIMIT $limit OFFSET $offset";
            $res = query($sql);
            $rows = [];
            if ($res) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $rows[] = $r;
                }
            }
            echo json_encode([
                'status'     => true,
                'tabel'      => $tabel,
                'total'      => $totalRows,
                'page'       => $page,
                'limit'      => $limit,
                'total_pages'=> ceil($totalRows / $limit),
                'rows'       => $rows
            ]);
            exit;

        // -------------------------------------------------------------
        // TABEL: temp_skripsi
        // -------------------------------------------------------------
        case 'temp_skripsi':
            $where = "1=1";
            if ($cariClean !== '') {
                $where .= " AND (ts.nim LIKE '%$cariClean%' OR m.nama LIKE '%$cariClean%' OR ts.status LIKE '%$cariClean%' OR pbs.nama_berkas LIKE '%$cariClean%')";
            }
            $qCount = query("SELECT COUNT(*) AS c FROM temp_skripsi ts LEFT JOIN mahasiswa m ON ts.nim = m.nim LEFT JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id WHERE $where");
            $totalRows = $qCount ? (int)mysqli_fetch_assoc($qCount)['c'] : 0;

            $sql = "SELECT ts.*, m.nama AS nama_mhs, m.prodi, p.nama AS nama_prodi, pbs.nama_berkas, pbs.progres AS syarat_progres, f.nama_file
                    FROM temp_skripsi ts
                    LEFT JOIN mahasiswa m ON ts.nim = m.nim
                    LEFT JOIN prodi p ON m.prodi = p.kode
                    LEFT JOIN persyaratan_berkas_skripsi pbs ON ts.id_persyaratan = pbs.id
                    LEFT JOIN files f ON ts.file = f.id
                    WHERE $where
                    ORDER BY ts.id DESC
                    LIMIT $limit OFFSET $offset";
            $res = query($sql);
            $rows = [];
            if ($res) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $rows[] = $r;
                }
            }
            echo json_encode([
                'status'     => true,
                'tabel'      => $tabel,
                'total'      => $totalRows,
                'page'       => $page,
                'limit'      => $limit,
                'total_pages'=> ceil($totalRows / $limit),
                'rows'       => $rows
            ]);
            exit;

        // -------------------------------------------------------------
        // TABEL: format_nomor_surat
        // -------------------------------------------------------------
        case 'format_nomor_surat':
            $where = "1=1";
            if ($cariClean !== '') {
                $where .= " AND (nama_surat LIKE '%$cariClean%' OR format_nomor LIKE '%$cariClean%')";
            }
            $qCount = query("SELECT COUNT(*) AS c FROM format_nomor_surat WHERE $where");
            $totalRows = $qCount ? (int)mysqli_fetch_assoc($qCount)['c'] : 0;

            $sql = "SELECT * FROM format_nomor_surat WHERE $where ORDER BY id ASC LIMIT $limit OFFSET $offset";
            $res = query($sql);
            $rows = [];
            if ($res) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $rows[] = $r;
                }
            }
            echo json_encode([
                'status'     => true,
                'tabel'      => $tabel,
                'total'      => $totalRows,
                'page'       => $page,
                'limit'      => $limit,
                'total_pages'=> ceil($totalRows / $limit),
                'rows'       => $rows
            ]);
            exit;

        // -------------------------------------------------------------
        // TABEL: nomor_surat
        // -------------------------------------------------------------
        case 'nomor_surat':
            $where = "1=1";
            if ($cariClean !== '') {
                $where .= " AND (nomor LIKE '%$cariClean%' OR keterangan LIKE '%$cariClean%' OR id LIKE '%$cariClean%' OR urutan LIKE '%$cariClean%')";
            }
            $qCount = query("SELECT COUNT(*) AS c FROM nomor_surat WHERE $where");
            $totalRows = $qCount ? (int)mysqli_fetch_assoc($qCount)['c'] : 0;

            $sql = "SELECT * FROM nomor_surat WHERE $where ORDER BY CAST(urutan AS UNSIGNED) DESC, id DESC LIMIT $limit OFFSET $offset";
            $res = query($sql);
            $rows = [];
            if ($res) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $rows[] = $r;
                }
            }
            echo json_encode([
                'status'     => true,
                'tabel'      => $tabel,
                'total'      => $totalRows,
                'page'       => $page,
                'limit'      => $limit,
                'total_pages'=> ceil($totalRows / $limit),
                'rows'       => $rows
            ]);
            exit;

        default:
            echo json_encode(['status' => false, 'pesan' => 'Tabel tidak dikenali dalam daftar modul basis data.']);
            exit;
    }
}

// =========================================================================
// 4. SIMPAN DATA (INSERT & UPDATE)
// =========================================================================
if ($action === 'simpan-data-tabel') {
    $tabel = trim((string)($_POST['tabel'] ?? ''));
    $mode  = trim((string)($_POST['mode'] ?? 'tambah')); // 'tambah' atau 'edit'
    $idKey = bersihkan($_POST['id_key'] ?? ''); // Kunci identifikasi baris jika edit

    switch ($tabel) {
        // -------------------------------------------------------------
        // SIMPAN: progres_kp
        // -------------------------------------------------------------
        case 'progres_kp':
            $mahasiswa = trim(bersihkan($_POST['mahasiswa'] ?? ''));
            $proses    = (int)($_POST['proses'] ?? 0);
            $waktuRaw  = trim((string)($_POST['waktu'] ?? ''));

            if ($mahasiswa === '' || $proses <= 0) {
                echo json_encode(['status' => false, 'pesan' => 'NIM Mahasiswa dan No Proses (SOP) wajib diisi']);
                exit;
            }

            // Verifikasi mahasiswa valid
            $cekMhs = query("SELECT nim FROM mahasiswa WHERE nim = '$mahasiswa' LIMIT 1");
            if (!$cekMhs || mysqli_num_rows($cekMhs) === 0) {
                echo json_encode(['status' => false, 'pesan' => "Mahasiswa dengan NIM '$mahasiswa' tidak ditemukan di database"]);
                exit;
            }

            $waktuInt = ($waktuRaw !== '') ? (int)strtotime($waktuRaw) : time();
            if ($waktuInt <= 0) $waktuInt = time();

            if ($mode === 'edit') {
                // Parse composite id: mahasiswa|proses|waktu
                $parts = explode('|', $idKey);
                $oldMhs = bersihkan($parts[0] ?? $mahasiswa);
                $oldPro = (int)($parts[1] ?? $proses);
                $oldWkt = bersihkan($parts[2] ?? '');

                $whereEdit = "mahasiswa = '$oldMhs' AND proses = $oldPro";
                if ($oldWkt !== '') {
                    $whereEdit .= " AND waktu = '$oldWkt'";
                }

                $up = query("UPDATE progres_kp SET mahasiswa = '$mahasiswa', proses = $proses, waktu = $waktuInt WHERE $whereEdit LIMIT 1");
                if ($up) {
                    echo json_encode(['status' => true, 'pesan' => 'Data progres_kp berhasil diperbarui']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui progres_kp: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
            } else {
                // Mode Tambah
                $ins = query("INSERT INTO progres_kp (waktu, proses, mahasiswa) VALUES ($waktuInt, $proses, '$mahasiswa')");
                if ($ins) {
                    echo json_encode(['status' => true, 'pesan' => 'Data progres_kp berhasil ditambahkan']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menambahkan progres_kp: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
            }
            exit;

        // -------------------------------------------------------------
        // SIMPAN: progres_skripsi
        // -------------------------------------------------------------
        case 'progres_skripsi':
            $mahasiswa = trim(bersihkan($_POST['mahasiswa'] ?? ''));
            $proses    = (int)($_POST['proses'] ?? 0);
            $waktuRaw  = trim((string)($_POST['waktu'] ?? ''));

            if ($mahasiswa === '' || $proses <= 0) {
                echo json_encode(['status' => false, 'pesan' => 'NIM Mahasiswa dan No Proses (SOP) wajib diisi']);
                exit;
            }

            $cekMhs = query("SELECT nim FROM mahasiswa WHERE nim = '$mahasiswa' LIMIT 1");
            if (!$cekMhs || mysqli_num_rows($cekMhs) === 0) {
                echo json_encode(['status' => false, 'pesan' => "Mahasiswa dengan NIM '$mahasiswa' tidak ditemukan di database"]);
                exit;
            }

            $waktuInt = ($waktuRaw !== '') ? (int)strtotime($waktuRaw) : time();
            if ($waktuInt <= 0) $waktuInt = time();

            if ($mode === 'edit') {
                $parts = explode('|', $idKey);
                $oldMhs = bersihkan($parts[0] ?? $mahasiswa);
                $oldPro = (int)($parts[1] ?? $proses);
                $oldWkt = bersihkan($parts[2] ?? '');

                $whereEdit = "mahasiswa = '$oldMhs' AND proses = $oldPro";
                if ($oldWkt !== '') {
                    $whereEdit .= " AND waktu = '$oldWkt'";
                }

                $up = query("UPDATE progres_skripsi SET mahasiswa = '$mahasiswa', proses = $proses, waktu = $waktuInt WHERE $whereEdit LIMIT 1");
                if ($up) {
                    echo json_encode(['status' => true, 'pesan' => 'Data progres_skripsi berhasil diperbarui']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui progres_skripsi: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
            } else {
                $ins = query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuInt, $proses, '$mahasiswa')");
                if ($ins) {
                    echo json_encode(['status' => true, 'pesan' => 'Data progres_skripsi berhasil ditambahkan']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menambahkan progres_skripsi: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
            }
            exit;

        // -------------------------------------------------------------
        // SIMPAN: temp_kp
        // -------------------------------------------------------------
        case 'temp_kp':
            $nim           = trim(bersihkan($_POST['nim'] ?? ''));
            $idPersyaratan = (int)($_POST['id_persyaratan'] ?? 0);
            $statusBerkas  = strtolower(trim(bersihkan($_POST['status_berkas'] ?? 'menunggu')));
            $ket           = trim(bersihkan($_POST['ket'] ?? ''));
            $fileId        = trim(bersihkan($_POST['file_id'] ?? ''));

            if ($nim === '' || $idPersyaratan <= 0) {
                echo json_encode(['status' => false, 'pesan' => 'NIM dan Persyaratan Berkas KP wajib ditentukan']);
                exit;
            }

            if (!in_array($statusBerkas, ['menunggu', 'diterima', 'ditolak'], true)) {
                $statusBerkas = 'menunggu';
            }

            if ($mode === 'edit') {
                $idRecord = (int)$idKey;
                if ($idRecord <= 0) {
                    echo json_encode(['status' => false, 'pesan' => 'ID Record temp_kp tidak valid']);
                    exit;
                }
                $up = query("UPDATE temp_kp SET nim = '$nim', id_persyaratan = $idPersyaratan, file = '$fileId', status = '$statusBerkas', ket = '$ket' WHERE id = $idRecord LIMIT 1");
                if ($up) {
                    echo json_encode(['status' => true, 'pesan' => 'Data temp_kp berhasil diperbarui']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui temp_kp: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
            } else {
                $ins = query("INSERT INTO temp_kp (id_persyaratan, nim, file, status, ket) VALUES ($idPersyaratan, '$nim', '$fileId', '$statusBerkas', '$ket')");
                if ($ins) {
                    echo json_encode(['status' => true, 'pesan' => 'Data temp_kp berhasil ditambahkan']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menambahkan temp_kp: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
            }
            exit;

        // -------------------------------------------------------------
        // SIMPAN: temp_skripsi
        // -------------------------------------------------------------
        case 'temp_skripsi':
            $nim           = trim(bersihkan($_POST['nim'] ?? ''));
            $idPersyaratan = (int)($_POST['id_persyaratan'] ?? 0);
            $statusBerkas  = strtolower(trim(bersihkan($_POST['status_berkas'] ?? 'menunggu')));
            $ket           = trim(bersihkan($_POST['ket'] ?? ''));
            $fileId        = trim(bersihkan($_POST['file_id'] ?? ''));

            if ($nim === '' || $idPersyaratan <= 0) {
                echo json_encode(['status' => false, 'pesan' => 'NIM dan Persyaratan Berkas Skripsi wajib ditentukan']);
                exit;
            }

            if (!in_array($statusBerkas, ['menunggu', 'diterima', 'ditolak'], true)) {
                $statusBerkas = 'menunggu';
            }

            if ($mode === 'edit') {
                $idRecord = (int)$idKey;
                if ($idRecord <= 0) {
                    echo json_encode(['status' => false, 'pesan' => 'ID Record temp_skripsi tidak valid']);
                    exit;
                }
                $up = query("UPDATE temp_skripsi SET nim = '$nim', id_persyaratan = $idPersyaratan, file = '$fileId', status = '$statusBerkas', ket = '$ket' WHERE id = $idRecord LIMIT 1");
                if ($up) {
                    echo json_encode(['status' => true, 'pesan' => 'Data temp_skripsi berhasil diperbarui']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui temp_skripsi: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
            } else {
                $ins = query("INSERT INTO temp_skripsi (id_persyaratan, nim, file, status, ket) VALUES ($idPersyaratan, '$nim', '$fileId', '$statusBerkas', '$ket')");
                if ($ins) {
                    echo json_encode(['status' => true, 'pesan' => 'Data temp_skripsi berhasil ditambahkan']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menambahkan temp_skripsi: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
            }
            exit;

        // -------------------------------------------------------------
        // SIMPAN: format_nomor_surat
        // -------------------------------------------------------------
        case 'format_nomor_surat':
            $namaSurat   = trim(bersihkan($_POST['nama_surat'] ?? ''));
            $formatNomor = trim(bersihkan($_POST['format_nomor'] ?? ''));

            if ($namaSurat === '' || $formatNomor === '') {
                echo json_encode(['status' => false, 'pesan' => 'Nama Surat dan Pola Format Nomor wajib diisi']);
                exit;
            }

            if ($mode === 'edit') {
                $idRecord = (int)$idKey;
                if ($idRecord <= 0) {
                    echo json_encode(['status' => false, 'pesan' => 'ID Record format_nomor_surat tidak valid']);
                    exit;
                }
                $up = query("UPDATE format_nomor_surat SET nama_surat = '$namaSurat', format_nomor = '$formatNomor' WHERE id = $idRecord LIMIT 1");
                if ($up) {
                    echo json_encode(['status' => true, 'pesan' => 'Format nomor surat berhasil diperbarui']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui format_nomor_surat: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
            } else {
                $ins = query("INSERT INTO format_nomor_surat (nama_surat, format_nomor) VALUES ('$namaSurat', '$formatNomor')");
                if ($ins) {
                    echo json_encode(['status' => true, 'pesan' => 'Format nomor surat baru berhasil ditambahkan']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menambahkan format_nomor_surat: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
            }
            exit;

        // -------------------------------------------------------------
        // SIMPAN: nomor_surat
        // -------------------------------------------------------------
        case 'nomor_surat':
            $urutan     = trim(bersihkan($_POST['urutan'] ?? ''));
            $nomor      = trim(bersihkan($_POST['nomor'] ?? ''));
            $tanggal    = trim(bersihkan($_POST['tanggal'] ?? ''));
            $keterangan = trim(bersihkan($_POST['keterangan'] ?? ''));

            if ($nomor === '') {
                echo json_encode(['status' => false, 'pesan' => 'Nomor Surat wajib diisi']);
                exit;
            }
            if ($tanggal === '') {
                $tanggal = date('Y-m-d');
            }

            if ($mode === 'edit') {
                $idRecord = bersihkan($idKey);
                if ($idRecord === '') {
                    echo json_encode(['status' => false, 'pesan' => 'ID Record nomor_surat tidak valid']);
                    exit;
                }
                $up = query("UPDATE nomor_surat SET urutan = '$urutan', nomor = '$nomor', tanggal = '$tanggal', keterangan = '$keterangan' WHERE id = '$idRecord' LIMIT 1");
                if ($up) {
                    echo json_encode(['status' => true, 'pesan' => 'Data nomor surat berhasil diperbarui']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui nomor_surat: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
            } else {
                // Generate ID unik jika belum ada
                $newId = bersihkan($_POST['id_custom'] ?? '');
                if ($newId === '') {
                    $newId = (string)time();
                    while (true) {
                        $cekId = query("SELECT id FROM nomor_surat WHERE id = '$newId' LIMIT 1");
                        if (!$cekId || mysqli_num_rows($cekId) === 0) break;
                        $newId = (string)(time() + rand(1, 9999));
                    }
                }
                $ins = query("INSERT INTO nomor_surat (id, urutan, nomor, tanggal, keterangan) VALUES ('$newId', '$urutan', '$nomor', '$tanggal', '$keterangan')");
                if ($ins) {
                    echo json_encode(['status' => true, 'pesan' => 'Nomor surat baru berhasil dicatat']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menambahkan nomor_surat: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
            }
            exit;

        default:
            echo json_encode(['status' => false, 'pesan' => 'Target tabel penyimpanan tidak valid.']);
            exit;
    }
}

// =========================================================================
// 5. HAPUS DATA RECORD
// =========================================================================
if ($action === 'hapus-data-tabel') {
    $tabel = trim((string)($_POST['tabel'] ?? ''));
    $idKey = bersihkan($_POST['id_key'] ?? '');

    if ($idKey === '') {
        echo json_encode(['status' => false, 'pesan' => 'Kunci baris yang akan dihapus tidak valid.']);
        exit;
    }

    switch ($tabel) {
        case 'progres_kp':
            $parts = explode('|', $idKey);
            $mhs = bersihkan($parts[0] ?? '');
            $pro = (int)($parts[1] ?? 0);
            $wkt = bersihkan($parts[2] ?? '');

            $whereDel = "mahasiswa = '$mhs' AND proses = $pro";
            if ($wkt !== '') {
                $whereDel .= " AND waktu = '$wkt'";
            }
            $del = query("DELETE FROM progres_kp WHERE $whereDel LIMIT 1");
            if ($del) {
                echo json_encode(['status' => true, 'pesan' => 'Baris progres_kp berhasil dihapus']);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus baris: ' . mysqli_error($GLOBALS['koneksi'])]);
            }
            exit;

        case 'progres_skripsi':
            $parts = explode('|', $idKey);
            $mhs = bersihkan($parts[0] ?? '');
            $pro = (int)($parts[1] ?? 0);
            $wkt = bersihkan($parts[2] ?? '');

            $whereDel = "mahasiswa = '$mhs' AND proses = $pro";
            if ($wkt !== '') {
                $whereDel .= " AND waktu = '$wkt'";
            }
            $del = query("DELETE FROM progres_skripsi WHERE $whereDel LIMIT 1");
            if ($del) {
                echo json_encode(['status' => true, 'pesan' => 'Baris progres_skripsi berhasil dihapus']);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus baris: ' . mysqli_error($GLOBALS['koneksi'])]);
            }
            exit;

        case 'temp_kp':
            $id = (int)$idKey;
            $del = query("DELETE FROM temp_kp WHERE id = $id LIMIT 1");
            if ($del) {
                echo json_encode(['status' => true, 'pesan' => 'Baris temp_kp berhasil dihapus']);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus baris: ' . mysqli_error($GLOBALS['koneksi'])]);
            }
            exit;

        case 'temp_skripsi':
            $id = (int)$idKey;
            $del = query("DELETE FROM temp_skripsi WHERE id = $id LIMIT 1");
            if ($del) {
                echo json_encode(['status' => true, 'pesan' => 'Baris temp_skripsi berhasil dihapus']);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus baris: ' . mysqli_error($GLOBALS['koneksi'])]);
            }
            exit;

        case 'format_nomor_surat':
            $id = (int)$idKey;
            $del = query("DELETE FROM format_nomor_surat WHERE id = $id LIMIT 1");
            if ($del) {
                echo json_encode(['status' => true, 'pesan' => 'Baris format_nomor_surat berhasil dihapus']);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus baris: ' . mysqli_error($GLOBALS['koneksi'])]);
            }
            exit;

        case 'nomor_surat':
            $id = bersihkan($idKey);
            $del = query("DELETE FROM nomor_surat WHERE id = '$id' LIMIT 1");
            if ($del) {
                echo json_encode(['status' => true, 'pesan' => 'Baris nomor_surat berhasil dihapus']);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus baris: ' . mysqli_error($GLOBALS['koneksi'])]);
            }
            exit;

        default:
            echo json_encode(['status' => false, 'pesan' => 'Tabel tidak valid untuk operasi penghapusan.']);
            exit;
    }
}

// Default fallback
echo json_encode(['status' => false, 'pesan' => 'Aksi tidak dikenali.']);
exit;
