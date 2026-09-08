<?php
/**
 * proses.php - CRUD Ruangan & Jadwal Kuliah
 * Folder: administrator/pages/jurusan/jadwal/
 *
 * Aksi (semua via POST/GET, response selalu JSON):
 *   GET  ?action=list_ruangan          -> JSON daftar ruangan aktif
 *   POST action=tambah_ruangan         -> tambah ruangan
 *   POST action=update_ruangan&id=...  -> update ruangan
 *   POST action=hapus_ruangan&id=...   -> soft delete ruangan
 *
 * Tabel: ruang_kuliah (id, nama, kapasitas, kategori, status)
 *        jadwal_kuliah (id, id_matakuliah, hari, jam, kelas, pengajar1, pengajar2, ruangan, tahun_ajaran)
 */

require_once __DIR__ . '/../../../../fungsi.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
    exit;
}

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = ($method === 'GET') ? ($_GET['action'] ?? '') : ($_POST['action'] ?? '');

// ================= LIST RUANGAN =================
if ($action === 'list_ruangan') {
    $ambil = query("SELECT id, nama, kapasitas, kategori, status FROM ruang_kuliah WHERE status = 'aktif' ORDER BY nama");
    $data = [];
    while ($row = mysqli_fetch_assoc($ambil)) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

// ================= TAMBAH RUANGAN =================
if ($action === 'tambah_ruangan' && $method === 'POST') {
    $nama      = bersihkan($_POST['nama'] ?? '');
    $kapasitas = (int)($_POST['kapasitas'] ?? 0);
    $kategori  = bersihkan($_POST['kategori'] ?? 'Umum');

    if (empty($nama)) {
        echo json_encode(['status' => false, 'pesan' => 'Nama ruangan tidak boleh kosong']);
        exit;
    }

    // id auto-generate dari nama (uppercase, alphanumeric saja)
    $id = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $nama));
    if ($id === '') $id = 'R' . time();

    $cek = query("SELECT id FROM ruang_kuliah WHERE id = '" . bersihkan($id) . "'");
    if (mysqli_num_rows($cek) > 0) {
        $id .= '_' . substr(bin2hex(random_bytes(2)), 0, 4);
    }

    $simpan = query("INSERT INTO ruang_kuliah (id, nama, kapasitas, kategori, status) VALUES ('" . bersihkan($id) . "', '$nama', $kapasitas, '" . bersihkan($kategori) . "', 'aktif')");

    if ($simpan) {
        echo json_encode(['status' => true, 'pesan' => 'Ruangan berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Ruangan gagal ditambahkan']);
    }
    exit;
}

// ================= UPDATE RUANGAN =================
if ($action === 'update_ruangan' && $method === 'POST') {
    $id        = bersihkan($_POST['id'] ?? '');
    $nama      = bersihkan($_POST['nama'] ?? '');
    $kapasitas = (int)($_POST['kapasitas'] ?? 0);
    $kategori  = bersihkan($_POST['kategori'] ?? 'Umum');

    if (empty($id) || empty($nama)) {
        echo json_encode(['status' => false, 'pesan' => 'ID dan nama ruangan tidak boleh kosong']);
        exit;
    }

    $update = query("UPDATE ruang_kuliah SET nama = '$nama', kapasitas = $kapasitas, kategori = '$kategori' WHERE id = '$id'");

    if ($update) {
        echo json_encode(['status' => true, 'pesan' => 'Ruangan berhasil diperbarui']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Ruangan gagal diperbarui']);
    }
    exit;
}

// ================= HAPUS RUANGAN =================
if ($action === 'hapus_ruangan' && $method === 'POST') {
    $id = bersihkan($_POST['id'] ?? '');

    if (empty($id)) {
        echo json_encode(['status' => false, 'pesan' => 'ID ruangan tidak valid']);
        exit;
    }

    $hapus = query("UPDATE ruang_kuliah SET status = 'tidak aktif' WHERE id = '$id'");

    if ($hapus) {
        echo json_encode(['status' => true, 'pesan' => 'Ruangan berhasil dihapus']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Ruangan gagal dihapus']);
    }
    exit;
}

echo json_encode(['status' => false, 'pesan' => 'Aksi tidak dikenali']);
?>
