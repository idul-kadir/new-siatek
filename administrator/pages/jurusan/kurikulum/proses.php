<?php
require_once __DIR__ . '/../../../../fungsi.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
    exit;
}

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'ambil-kurikulum') {
        $kode = bersihkan($_GET['kode'] ?? '');
        if ($kode === '') {
            echo json_encode(['status' => false, 'pesan' => 'Kode tidak ditemukan']);
            exit;
        }
        $ambil = query("SELECT k.kode, k.nama, k.tahun, k.prodi, k.status, p.nama AS prodi_nama FROM kurikulum k LEFT JOIN prodi p ON k.prodi = p.kode WHERE k.kode='$kode' LIMIT 1");
        $data = mysqli_fetch_assoc($ambil);
        if ($data) {
            echo json_encode(['status' => true, 'data' => $data]);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Data tidak ditemukan']);
        }
        exit;
    }
    
    if ($action === 'ambil-tabel-kurikulum') {
        $filterTahun = bersihkan($_GET['tahun'] ?? '');
        $filterProdi = bersihkan($_GET['prodi'] ?? '');
        $filterStatus = bersihkan($_GET['status'] ?? '');
        $kataCari = bersihkan($_GET['cari'] ?? '');
        
        $sql = "SELECT k.kode, k.nama, k.tahun, k.prodi, k.status, p.nama AS prodi_nama FROM kurikulum k LEFT JOIN prodi p ON k.prodi = p.kode WHERE 1=1";
        if ($filterTahun !== '') {
            $sql .= " AND k.tahun = '$filterTahun'";
        }
        if ($filterProdi !== '') {
            $sql .= " AND k.prodi = '$filterProdi'";
        }
        if ($filterStatus !== '') {
            $sql .= " AND k.status = '$filterStatus'";
        }
        if ($kataCari !== '') {
            $sql .= " AND (k.kode LIKE '%$kataCari%' OR k.nama LIKE '%$kataCari%' OR p.nama LIKE '%$kataCari%')";
        }
        $sql .= " ORDER BY k.tahun DESC, k.kode ASC";
        
        $ambil = query($sql);
        if (!$ambil) {
            echo json_encode(['status' => false, 'pesan' => 'Query error: ' . mysqli_error($koneksi)]);
            exit;
        }
        $rows = [];
        while ($data = mysqli_fetch_assoc($ambil)) {
            $rows[] = $data;
        }
        echo json_encode(['status' => true, 'data' => $rows]);
        exit;
    }
    
    if ($action === 'ambil-prodi-kurikulum') {
        $ambil = query("SELECT kode, nama FROM prodi ORDER BY nama");
        $data = [];
        while ($row = mysqli_fetch_assoc($ambil)) {
            $data[] = $row;
        }
        echo json_encode(['status' => true, 'data' => $data]);
        exit;
    }
    
    if ($action === 'ambil-tahun-kurikulum') {
        $ambil = query("SELECT DISTINCT tahun FROM kurikulum ORDER BY tahun DESC");
        $data = [];
        $y = (int)date('Y');
        for ($i = $y - 10; $i <= $y + 1; $i++) {
            $data[] = ['tahun' => (string)$i];
        }
        while ($row = mysqli_fetch_assoc($ambil)) {
            $t = $row['tahun'];
            if (!in_array($t, array_column($data, 'tahun'))) {
                $data[] = ['tahun' => $t];
            }
        }
        usort($data, function($a, $b) {
            return strcmp($b['tahun'], $a['tahun']);
        });
        echo json_encode(['status' => true, 'data' => $data]);
        exit;
    }
    
    echo json_encode(['status' => false, 'pesan' => 'Aksi tidak dikenali']);
    exit;
}

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if (isset($_POST['tambah-data-kurikulum'])) {
        $nama = bersihkan($_POST['nama'] ?? '');
        $tahun = bersihkan($_POST['tahun'] ?? '');
        $prodi = bersihkan($_POST['prodi'] ?? '');
        $status = bersihkan($_POST['status'] ?? 'aktif');
        
        if ($nama === '' || $tahun === '' || $prodi === '') {
            echo json_encode(['status' => false, 'pesan' => 'Nama, Tahun, dan Prodi wajib diisi']);
            exit;
        }
        
        $kode = substr(time() . bin2hex(random_bytes(3)), 0, 15);
        
        $simpan = query("INSERT INTO kurikulum (kode, nama, tahun, prodi, status) VALUES ('$kode', '$nama', '$tahun', '$prodi', '$status')");
        
        if ($simpan) {
            echo json_encode(['status' => true, 'pesan' => 'Data kurikulum berhasil ditambahkan', 'kode' => $kode]);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Data kurikulum gagal ditambahkan']);
        }
        exit;
    }
    
    if (isset($_POST['edit-data-kurikulum'])) {
        $kode = bersihkan($_POST['kode'] ?? '');
        $nama = bersihkan($_POST['nama'] ?? '');
        $tahun = bersihkan($_POST['tahun'] ?? '');
        $prodi = bersihkan($_POST['prodi'] ?? '');
        $status = bersihkan($_POST['status'] ?? 'aktif');
        
        if ($kode === '' || $nama === '' || $tahun === '' || $prodi === '') {
            echo json_encode(['status' => false, 'pesan' => 'Semua field wajib diisi']);
            exit;
        }
        
        $update = query("UPDATE kurikulum SET nama='$nama', tahun='$tahun', prodi='$prodi', status='$status' WHERE kode='$kode'");
        
        if ($update) {
            echo json_encode(['status' => true, 'pesan' => 'Data kurikulum berhasil diubah']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Data kurikulum gagal diubah']);
        }
        exit;
    }
    
    if (isset($_POST['hapus-data-kurikulum'])) {
        $kode = bersihkan($_POST['kode'] ?? '');
        
        if ($kode === '') {
            echo json_encode(['status' => false, 'pesan' => 'Kode tidak ditemukan']);
            exit;
        }
        
        $hapus = query("DELETE FROM kurikulum WHERE kode='$kode'");
        
        if ($hapus) {
            echo json_encode(['status' => true, 'pesan' => 'Data kurikulum berhasil dihapus']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Data kurikulum gagal dihapus']);
        }
        exit;
    }
    
    echo json_encode(['status' => false, 'pesan' => 'Aksi tidak dikenali']);
    exit;
}

echo json_encode(['status' => false, 'pesan' => 'Metode tidak dikenali']);
exit;

?>