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

// ================= TAMBAH JADWAL =================
if ($action === 'tambah_jadwal' && $method === 'POST') {
    $id_matakuliah = bersihkan($_POST['id_matakuliah'] ?? '');
    $hari          = bersihkan($_POST['hari'] ?? '');
    $kelas         = bersihkan($_POST['kelas'] ?? '');
    $prodi         = bersihkan($_POST['prodi'] ?? '');
    $pengajar1     = bersihkan($_POST['pengajar1'] ?? '');
    $pengajar2     = bersihkan($_POST['pengajar2'] ?? '');
    $ruangan       = bersihkan($_POST['ruangan'] ?? '');
    $jam           = bersihkan($_POST['jam'] ?? '');
    $tahun_ajaran  = bersihkan($_POST['tahun_ajaran'] ?? '');

    if (empty($id_matakuliah) || empty($hari) || empty($ruangan) || empty($jam) || empty($tahun_ajaran)) {
        echo json_encode(['status' => false, 'pesan' => 'Data tidak lengkap']);
        exit;
    }

    // cek matakuliah+kelas sudah dibuka di tahun ajaran ini
    $cekKelas = query("SELECT id FROM jadwal_kuliah WHERE id_matakuliah = '$id_matakuliah' AND kelas = '$kelas' AND tahun_ajaran = '$tahun_ajaran'");
    if (mysqli_num_rows($cekKelas) > 0) {
        echo json_encode(['status' => false, 'pesan' => 'Kelas ' . $kelas . ' untuk mata kuliah ini sudah dibuka di tahun ajaran ini']);
        exit;
    }

    // cek tumpang tindih: hari + jam + ruangan sama di tahun ajaran sama
    $cek = query("SELECT id FROM jadwal_kuliah WHERE hari = '$hari' AND jam = '$jam' AND ruangan = '$ruangan' AND tahun_ajaran = '$tahun_ajaran'");
    if (mysqli_num_rows($cek) > 0) {
        echo json_encode(['status' => false, 'pesan' => 'Jadwal bentrok: hari, jam, dan ruangan sudah dipakai']);
        exit;
    }

    // kolom pengajar FK ke dosen, nilai kosong harus NULL (bukan string kosong)
    $p1Sql = ($pengajar1 !== '') ? "'$pengajar1'" : "NULL";
    $p2Sql = ($pengajar2 !== '') ? "'$pengajar2'" : "NULL";

    $simpan = query("INSERT INTO jadwal_kuliah (id_matakuliah, hari, jam, kelas, prodi, pengajar1, pengajar2, ruangan, tahun_ajaran) VALUES ('$id_matakuliah', '$hari', '$jam', '$kelas', '$prodi', $p1Sql, $p2Sql, '$ruangan', '$tahun_ajaran')");

    if ($simpan) {
        echo json_encode(['status' => true, 'pesan' => 'Jadwal berhasil ditambahkan', 'id' => mysqli_insert_id($koneksi)]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Jadwal gagal ditambahkan']);
    }
    exit;
}

// ================= UPDATE JADWAL =================
if ($action === 'update_jadwal' && $method === 'POST') {
    $id = bersihkan($_POST['id'] ?? '');
    $id_matakuliah = bersihkan($_POST['id_matakuliah'] ?? '');
    $hari          = bersihkan($_POST['hari'] ?? '');
    $kelas         = bersihkan($_POST['kelas'] ?? '');
    $prodi         = bersihkan($_POST['prodi'] ?? '');
    $pengajar1     = bersihkan($_POST['pengajar1'] ?? '');
    $pengajar2     = bersihkan($_POST['pengajar2'] ?? '');
    $ruangan       = bersihkan($_POST['ruangan'] ?? '');
    $jam           = bersihkan($_POST['jam'] ?? '');
    $tahun_ajaran  = bersihkan($_POST['tahun_ajaran'] ?? '');

    if (empty($id) || empty($id_matakuliah) || empty($hari) || empty($ruangan) || empty($jam) || empty($tahun_ajaran)) {
        echo json_encode(['status' => false, 'pesan' => 'Data tidak lengkap']);
        exit;
    }

    // kolom pengajar FK ke dosen, nilai kosong harus NULL (bukan string kosong)
    $p1Sql = ($pengajar1 !== '') ? "'$pengajar1'" : "NULL";
    $p2Sql = ($pengajar2 !== '') ? "'$pengajar2'" : "NULL";

    // cek matakuliah+kelas sudah dibuka di tahun ajaran ini (kecuali baris ini sendiri)
    $cekKelas = query("SELECT id FROM jadwal_kuliah WHERE id_matakuliah = '$id_matakuliah' AND kelas = '$kelas' AND tahun_ajaran = '$tahun_ajaran' AND id != '$id'");
    if (mysqli_num_rows($cekKelas) > 0) {
        echo json_encode(['status' => false, 'pesan' => 'Kelas ' . $kelas . ' untuk mata kuliah ini sudah dibuka di tahun ajaran ini']);
        exit;
    }

    // cek tumpang tindih hari + jam + ruangan (kecuali baris ini)
    $cek = query("SELECT id FROM jadwal_kuliah WHERE hari = '$hari' AND jam = '$jam' AND ruangan = '$ruangan' AND tahun_ajaran = '$tahun_ajaran' AND id != '$id'");
    if (mysqli_num_rows($cek) > 0) {
        echo json_encode(['status' => false, 'pesan' => 'Jadwal bentrok: hari, jam, dan ruangan sudah dipakai']);
        exit;
    }

    $update = query("UPDATE jadwal_kuliah SET id_matakuliah = '$id_matakuliah', hari = '$hari', jam = '$jam', kelas = '$kelas', prodi = '$prodi', pengajar1 = $p1Sql, pengajar2 = $p2Sql, ruangan = '$ruangan', tahun_ajaran = '$tahun_ajaran' WHERE id = '$id'");

    if ($update) {
        echo json_encode(['status' => true, 'pesan' => 'Jadwal berhasil diperbarui']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Jadwal gagal diperbarui']);
    }
    exit;
}

// ================= HAPUS JADWAL =================
if ($action === 'hapus_jadwal' && $method === 'POST') {
    $id = bersihkan($_POST['id'] ?? '');
    if (empty($id)) {
        echo json_encode(['status' => false, 'pesan' => 'ID jadwal tidak valid']);
        exit;
    }
    $hapus = query("DELETE FROM jadwal_kuliah WHERE id = '$id'");
    if ($hapus) {
        echo json_encode(['status' => true, 'pesan' => 'Jadwal berhasil dihapus']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Jadwal gagal dihapus']);
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
