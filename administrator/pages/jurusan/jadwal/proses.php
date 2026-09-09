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

// ================= LIST MATAKULIAH UNTUK FORM BUKA KELAS =================
// utama: matakuliah pada TA terpilih; jika TA itu belum punya jadwal, pakai master matakuliah
if ($action === 'ambil_matkul') {
    $ta = bersihkan($_GET['ta'] ?? $_POST['ta'] ?? date('Y') . '/' . (date('Y') + 1) . ' - Ganjil');

    $ambil = query("SELECT DISTINCT m.id, m.kode, m.nama, m.sks, m.semester, k.prodi AS prodi_kode, p.nama AS prodi_nama
        FROM jadwal_kuliah j
        JOIN matakuliah m ON j.id_matakuliah = m.id
        LEFT JOIN kurikulum k ON m.kurikulum = k.kode
        LEFT JOIN prodi p ON k.prodi = p.kode
        WHERE j.tahun_ajaran = '$ta' AND TRIM(m.kode) != ''
        ORDER BY p.nama, m.nama");
    $data = [];
    while ($row = mysqli_fetch_assoc($ambil)) $data[] = $row;

    if (count($data) === 0) {
        $ambil = query("SELECT m.id, m.kode, m.nama, m.sks, m.semester, k.prodi AS prodi_kode, p.nama AS prodi_nama
            FROM matakuliah m
            LEFT JOIN kurikulum k ON m.kurikulum = k.kode
            LEFT JOIN prodi p ON k.prodi = p.kode
            WHERE TRIM(m.kode) != ''
            ORDER BY p.nama, m.nama");
        while ($row = mysqli_fetch_assoc($ambil)) $data[] = $row;
    }
    echo json_encode(['status' => true, 'data' => $data]);
    exit;
}

// ================= LIST TAHUN AJARAN =================
// gabungan: dua TA generate dari tahun sekarang (mis. 2026 -> 2025/2026 - Genap + 2026/2027 - Ganjil)
// + semua TA yang sudah ada di jadwal_kuliah
if ($action === 'ambil_tahun') {
    $y = (int)date('Y');
    $data = [
        ($y - 1) . '/' . $y . ' - Genap',
        $y . '/' . ($y + 1) . ' - Ganjil'
    ];
    $ambil = query("SELECT DISTINCT tahun_ajaran FROM jadwal_kuliah ORDER BY tahun_ajaran DESC");
    while ($row = mysqli_fetch_assoc($ambil)) {
        if (!in_array($row['tahun_ajaran'], $data)) $data[] = $row['tahun_ajaran'];
    }
    echo json_encode(['status' => true, 'data' => $data]);
    exit;
}

// ================= AMBIL JADWAL PER TAHUN AJARAN =================
if ($action === 'ambil_jadwal') {
    $ta = bersihkan($_GET['ta'] ?? $_POST['ta'] ?? '');
    if ($ta === '') {
        echo json_encode(['status' => false, 'pesan' => 'Tahun ajaran tidak valid']);
        exit;
    }

    $ambil = query("SELECT j.id, j.hari, j.jam, j.kelas, j.ruangan, j.tahun_ajaran,
               rk.nama AS ruangan_nama,
               m.id AS matkul_id, m.kode, m.nama, m.sks, m.semester, m.konsentrasi,
               k.prodi AS prodi_kode, p.nama AS prodi_nama,
               j.pengajar1, j.pengajar2,
               d1.nama AS dosen1_nama,
               d2.nama AS dosen2_nama
        FROM jadwal_kuliah j
        LEFT JOIN matakuliah m ON j.id_matakuliah = m.id
        LEFT JOIN dosen d1 ON j.pengajar1 = d1.id
        LEFT JOIN dosen d2 ON j.pengajar2 = d2.id
        LEFT JOIN ruang_kuliah rk ON j.ruangan = rk.id
        LEFT JOIN kurikulum k ON m.kurikulum = k.kode
        LEFT JOIN prodi p ON k.prodi = p.kode
        WHERE j.tahun_ajaran = '$ta'
        ORDER BY j.hari, rk.nama, j.jam");

    $data = [];
    while ($row = mysqli_fetch_assoc($ambil)) {
        $data[] = [
            'id'          => $row['id'],
            'hari'        => $row['hari'],
            'jam'         => $row['jam'],
            'kelas'       => $row['kelas'],
            'matkul_id'   => $row['matkul_id'],
            'kode'        => $row['kode'],
            'nama'        => $row['nama'],
            'sks'         => $row['sks'],
            'semester'    => $row['semester'],
            'prodi_kode'  => $row['prodi_kode'] ?: '',
            'prodi_nama'  => $row['prodi_nama'] ?: '',
            'pengajar1_id'=> $row['pengajar1'] ?: '',
            'pengajar2_id'=> $row['pengajar2'] ?: '',
            'dosen1'      => $row['dosen1_nama'] ?: '',
            'dosen2'      => $row['dosen2_nama'] ?: '',
            'ruangan_id'  => $row['ruangan'] ?: '',
            'ruangan'     => strtoupper($row['ruangan_nama'] ?: $row['ruangan'])
        ];
    }
    echo json_encode(['status' => true, 'data' => $data, 'tahun_ajaran' => $ta]);
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
