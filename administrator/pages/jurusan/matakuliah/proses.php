<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../../../uploads/rps/debug.log');
require_once __DIR__ . '/../../../../fungsi.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    error_log("AUTH FAILURE: Session not found");
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
    exit;
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Debug logging
$logFile = __DIR__ . '/../../../../uploads/rps/debug.log';
file_put_contents($logFile, "\n=== " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
file_put_contents($logFile, "Action: $action\n", FILE_APPEND);
file_put_contents($logFile, "POST: " . print_r($_POST, true) . "\n", FILE_APPEND);
file_put_contents($logFile, "FILES: " . print_r($_FILES, true) . "\n", FILE_APPEND);
file_put_contents($logFile, "Session user: " . ($_SESSION['user_id'] ?? 'none') . "\n", FILE_APPEND);
file_put_contents($logFile, "Session NIP: " . ($_SESSION['nip'] ?? 'none') . "\n", FILE_APPEND);

// ================= AMBIL DATA MATAKULIAH =================
if ($action === 'ambil-data-matakuliah') {
    $data = query("
        SELECT m.id, m.kode, m.nama, m.semester, m.sks, m.kurikulum, m.konsentrasi, m.keterangan, m.rps,
               k.prodi, k.tahun, k.nama as nama_kurikulum,
               p.nama as nama_prodi
        FROM matakuliah m
        JOIN kurikulum k ON m.kurikulum = k.kode
        JOIN prodi p ON k.prodi = p.kode
        ORDER BY p.nama ASC, k.tahun DESC, m.kode ASC
    ");
    $result = [];
    while ($row = mysqli_fetch_assoc($data)) {
        // Gunakan nama prodi lengkap untuk matching dengan HTML
        $prodi = $row['nama_prodi'] ?? $row['prodi'] ?? '';
        $tahun = $row['tahun'] ?? '2022';
        
        $result[] = [
            'id' => $row['id'],
            'kode' => $row['kode'],
            'nama' => $row['nama'],
            'semester' => (int)$row['semester'],
            'sks' => (int)$row['sks'],
            'kurikulum' => $row['kurikulum'],
            'konsentrasi' => $row['konsentrasi'] ?? '-',
            'keterangan' => $row['keterangan'] ?? '',
            'rps' => $row['rps'] ?? '',
            'prodi' => $prodi,
            'tahun' => $tahun,
            'nama_prodi' => $prodi
        ];
    }
    echo json_encode(['status' => true, 'data' => $result]);
    exit;
}

// ================= AMBIL DATA KURIKULUM =================
if ($action === 'ambil-kurikulum') {
    $prodi_kode = $_GET['prodi'] ?? '';
    
    if (empty($prodi_kode)) {
        echo json_encode(['status' => false, 'pesan' => 'Prodi harus diisi']);
        exit;
    }
    
    $kurikulum = [];
    $ambilKurikulum = query("SELECT k.kode, k.nama, k.tahun FROM kurikulum k WHERE k.prodi = '$prodi_kode' AND k.status = 'aktif' ORDER BY k.tahun DESC");
    while ($row = mysqli_fetch_assoc($ambilKurikulum)) {
        $kurikulum[] = $row;
    }
    
    echo json_encode(['status' => true, 'kurikulum' => $kurikulum]);
    exit;
}

// ================= AMBIL FILTER MATAKULIAH =================
if ($action === 'ambil-filter-matakuliah') {
    // Ambil daftar prodi
    $prodi = [];
    $ambilProdi = query("SELECT kode, nama FROM prodi ORDER BY nama ASC");
    while ($row = mysqli_fetch_assoc($ambilProdi)) {
        $prodi[] = $row;
    }
    
    // Ambil daftar kurikulum lengkap
    $kurikulum = [];
    $ambilKurikulum = query("SELECT k.kode, k.nama, k.tahun, k.prodi, p.nama as nama_prodi 
                            FROM kurikulum k 
                            JOIN prodi p ON k.prodi = p.kode 
                            ORDER BY p.nama ASC, k.tahun DESC");
    while ($row = mysqli_fetch_assoc($ambilKurikulum)) {
        $kurikulum[] = $row;
    }
    
    echo json_encode([
        'status' => true,
        'prodi' => $prodi,
        'kurikulum' => $kurikulum
    ]);
    exit;
}

// ================= AMBIL KURIKULUM BY PRODI =================
if ($action === 'ambil-kurikulum-by-prodi') {
    $prodi_kode = $_GET['prodi'] ?? '';
    
    if (empty($prodi_kode)) {
        echo json_encode(['status' => false, 'pesan' => 'Prodi harus diisi']);
        exit;
    }
    
    $kurikulum = [];
    $ambilKurikulum = query("SELECT k.kode, k.nama, k.tahun 
                            FROM kurikulum k 
                            WHERE k.prodi = '$prodi_kode' 
                            ORDER BY k.tahun DESC");
    while ($row = mysqli_fetch_assoc($ambilKurikulum)) {
        $kurikulum[] = $row;
    }
    
    echo json_encode(['status' => true, 'kurikulum' => $kurikulum]);
    exit;
}

// Tambah Matakuliah
if ($action === 'tambah-matakuliah') {
    $kode = bersihkan($_POST['kode'] ?? '');
    $nama = bersihkan($_POST['nama'] ?? '');
    $semester = (int)($_POST['semester'] ?? 1);
    $sks = (int)($_POST['sks'] ?? 3);
    $kurikulum = bersihkan($_POST['kurikulum'] ?? '');
    $konsentrasi = bersihkan($_POST['konsentrasi'] ?? '-');
    $keterangan = bersihkan($_POST['keterangan'] ?? '');
    $rps = null;

    // Validasi input
    if (empty($kode) || empty($nama) || empty($kurikulum)) {
        echo json_encode(['status' => false, 'pesan' => 'Kode, nama, dan kurikulum wajib diisi']);
        exit;
    }

    // Upload RPS jika ada file
    if (isset($_FILES['rps_file']) && $_FILES['rps_file']['error'] === 0) {
        $rps = upload_file($_FILES['rps_file'], 'matakuliah');
        if (!$rps) {
            echo json_encode(['status' => false, 'pesan' => 'Gagal mengupload file RPS']);
            exit;
        }
    }
    // Jika tidak ada file, $rps tetap null (NULL allowed di database)

    // Insert ke database
    $rps_value = ($rps !== null) ? "'$rps'" : "NULL";
    $insert = query("INSERT INTO matakuliah (kode, nama, semester, sks, kurikulum, konsentrasi, keterangan, rps) 
                     VALUES ('$kode', '$nama', $semester, $sks, '$kurikulum', '$konsentrasi', '$keterangan', $rps_value)");
    
    if ($insert) {
        $id_baru = mysqli_insert_id($koneksi);
        echo json_encode(['status' => true, 'pesan' => 'Matakuliah berhasil ditambahkan', 'id' => $id_baru]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan: ' . mysqli_error($koneksi)]);
    }
    exit;
}

// Edit Matakuliah
if ($action === 'edit-matakuliah') {
    $id = (int)($_POST['id'] ?? 0);
    $kode = bersihkan($_POST['kode'] ?? '');
    $nama = bersihkan($_POST['nama'] ?? '');
    $semester = (int)($_POST['semester'] ?? 1);
    $sks = (int)($_POST['sks'] ?? 3);
    $kurikulum = bersihkan($_POST['kurikulum'] ?? '');
    $konsentrasi = bersihkan($_POST['konsentrasi'] ?? '-');
    $keterangan = bersihkan($_POST['keterangan'] ?? '');

    if ($id <= 0 || empty($kode) || empty($nama) || empty($kurikulum)) {
        echo json_encode(['status' => false, 'pesan' => 'Data tidak valid']);
        exit;
    }

    // Upload RPS baru jika ada file
    $rps = null;
    if (isset($_FILES['rps_file']) && $_FILES['rps_file']['error'] === 0) {
        $rps = upload_file($_FILES['rps_file'], 'matakuliah');
        if (!$rps) {
            echo json_encode(['status' => false, 'pesan' => 'Gagal mengupload file RPS']);
            exit;
        }
    }

    // Update ke database
    if ($rps !== null) {
        $update = query("UPDATE matakuliah SET kode='$kode', nama='$nama', semester=$semester, sks=$sks, kurikulum='$kurikulum', konsentrasi='$konsentrasi', keterangan='$keterangan', rps='$rps' WHERE id='$id'");
    } else {
        $update = query("UPDATE matakuliah SET kode='$kode', nama='$nama', semester=$semester, sks=$sks, kurikulum='$kurikulum', konsentrasi='$konsentrasi', keterangan='$keterangan' WHERE id='$id'");
    }

    if ($update) {
        echo json_encode(['status' => true, 'pesan' => 'Matakuliah berhasil diperbarui']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan: ' . mysqli_error($koneksi)]);
    }
    exit;
}

// Hapus Matakuliah
if ($action === 'hapus-matakuliah') {
    $id = (int)($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode(['status' => false, 'pesan' => 'ID tidak valid']);
        exit;
    }
    
    // Hapus file RPS jika ada
    $cek = query("SELECT rps FROM matakuliah WHERE id='$id'");
    $row = mysqli_fetch_assoc($cek);
    if ($row && !empty($row['rps'])) {
        // Hapus dari tabel files
        $hapus_file = query("DELETE FROM files WHERE id='" . bersihkan($row['rps']) . "'");
    }
    
    // Hapus dari database
    $hapus = query("DELETE FROM matakuliah WHERE id='$id'");
    
    if ($hapus) {
        echo json_encode(['status' => true, 'pesan' => 'Matakuliah berhasil dihapus']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus: ' . mysqli_error($koneksi)]);
    }
    exit;
}

echo json_encode(['status' => false, 'pesan' => 'Aksi tidak dikenali']);
exit;

?>
