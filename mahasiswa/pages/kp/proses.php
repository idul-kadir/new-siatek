<?php
/**
 * PROSES KERJA PRAKTEK (KP) MAHASISWA
 * ---------------------------------------------
 * Backend AJAX Controller untuk aktivitas Kerja Praktek mahasiswa.
 * Mengikuti arsitektur proses.php skripsi secara presisi (1:1).
 */

require_once __DIR__ . '/../../../fungsi.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$nim_login = $_SESSION['nim'] ?? $_SESSION['username'] ?? $_SESSION['user_id'] ?? '';
if (empty($_SESSION['id']) && !empty($nim_login)) {
    $_SESSION['id'] = $nim_login;
}

header('Content-Type: application/json; charset=utf-8');

// ================= UNGGAH BERKAS (SATUAN) KE TEMP_KP =================
if (isset($_POST['unggah-berkas-kp'])) {
    if (empty($nim_login)) {
        echo json_encode(['status' => false, 'pesan' => 'Sesi tidak valid, silakan login ulang']);
        exit;
    }

    $id_persyaratan = bersihkan($_POST['id_persyaratan'] ?? '');
    $field_target   = bersihkan($_POST['field_target'] ?? '');

    if (!isset($_FILES['file']) || $_FILES['file']['error'] != 0) {
        echo json_encode(['status' => false, 'pesan' => 'File gagal diunggah atau tidak ditemukan']);
        exit;
    }

    $nim_clean = bersihkan($nim_login);

    $cekTemp = query("SELECT id, file, status FROM temp_kp WHERE nim = '$nim_clean' AND id_persyaratan = '$id_persyaratan'");
    if ($cekTemp && mysqli_num_rows($cekTemp) > 0) {
        $rowTemp = mysqli_fetch_assoc($cekTemp);
        
        if (strtolower(trim((string)$rowTemp['status'])) === 'diterima') {
            echo json_encode(['status' => false, 'pesan' => 'Berkas ini telah diterima oleh operator dan tidak dapat diunggah ulang']);
            exit;
        }

        $id_file_lama = $rowTemp['file'];
        $id_file = upload_file($_FILES['file'], 'temp_kp', $id_file_lama);
        if (!$id_file) {
            echo json_encode(['status' => false, 'pesan' => 'Gagal mengunggah dan mengganti file server']);
            exit;
        }

        $simpan = query("UPDATE temp_kp SET file = '$id_file', status = 'menunggu', ket = '' WHERE nim = '$nim_clean' AND id_persyaratan = '$id_persyaratan'");
    } else {
        $id_file = upload_file($_FILES['file'], 'temp_kp');
        if (!$id_file) {
            echo json_encode(['status' => false, 'pesan' => 'Gagal mengunggah file ke server']);
            exit;
        }

        $simpan = query("INSERT INTO temp_kp (id_persyaratan, nim, file, status, ket) VALUES ('$id_persyaratan', '$nim_clean', '$id_file', 'menunggu', '')");
    }

    $progresSaatIni = get_progres_kp($nim_clean);
    $stepProgres    = (int)$progresSaatIni;

    if ($simpan) {
        $prosesSelesai = false;

        $qProgBerkas = query("SELECT progres FROM persyaratan_berkas_kp WHERE id = '$id_persyaratan' LIMIT 1");
        $progresBerkas = ($qProgBerkas && mysqli_num_rows($qProgBerkas) > 0) ? (int)mysqli_fetch_assoc($qProgBerkas)['progres'] : 0;

        if ($progresBerkas > 0 && $stepProgres === $progresBerkas) {
            $qSelesai = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_kp WHERE progres = '$stepProgres'");
            $jmlTotal = ($qSelesai && mysqli_num_rows($qSelesai) > 0) ? (int)mysqli_fetch_assoc($qSelesai)['total'] : 0;

            $qValid = query("SELECT COUNT(DISTINCT id_persyaratan) AS cnt FROM temp_kp WHERE nim = '$nim_clean' AND status != 'ditolak' AND id_persyaratan IN (SELECT id FROM persyaratan_berkas_kp WHERE progres = '$stepProgres')");
            $jmlValid = ($qValid && mysqli_num_rows($qValid) > 0) ? (int)mysqli_fetch_assoc($qValid)['cnt'] : 0;

            if ($jmlTotal > 0 && $jmlValid >= $jmlTotal) {
                $nextProses = $stepProgres + 1;
                set_progres_kp($nim_clean, $nextProses);
                $prosesSelesai = true;
            }
        }

        echo json_encode(['status' => true, 'pesan' => 'Berkas berhasil diunggah', 'proses_selesai' => $prosesSelesai]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Berkas gagal disimpan ke database sementara']);
    }
    exit;
}

// ================= DAFTAR KERJA PRAKTEK =================
if (isset($_POST['daftar-kp'])) {
    if (empty($nim_login)) {
        echo json_encode(['status' => false, 'pesan' => 'Sesi tidak valid, silakan login ulang']);
        exit;
    }

    $nim_clean = bersihkan($nim_login);

    // Cek apakah sudah terdaftar
    $cek = query("SELECT nim FROM kp WHERE nim = '$nim_clean'");
    if ($cek && mysqli_num_rows($cek) > 0) {
        echo json_encode(['status' => false, 'pesan' => 'Anda sudah terdaftar di Kerja Praktek']);
        exit;
    }

    // Insert ke tabel kp dengan status 'tidak aktif' (menunggu verifikasi pendaftaran)
    $simpan = query("INSERT INTO kp (nim, judul, status) VALUES ('$nim_clean', '', 'tidak aktif')");

    // Inisialisasi awal proses progres_kp ke step 1
    $waktu = time();
    query("INSERT INTO progres_kp (waktu, proses, mahasiswa) VALUES ($waktu, 1, '$nim_clean')");

    if ($simpan) {
        echo json_encode(['status' => true, 'pesan' => 'Berhasil mendaftar Kerja Praktek']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal mendaftar Kerja Praktek, terjadi kesalahan']);
    }
    exit;
}

// ================= SIMPAN RENCANA KERJA PRAKTEK & CETAK LEMBAR PERSETUJUAN PA (STEP 1) =================
if (isset($_POST['simpan-pengajuan-kp'])) {
    // 1. Validasi sesi login mahasiswa
    if (empty($nim_login)) {
        echo json_encode(['status' => false, 'pesan' => 'Sesi tidak valid, silakan login ulang']);
        exit;
    }

    // 2. Ambil dan bersihkan seluruh input dari form pengajuan KP (SQL injection & XSS protection)
    $nim_clean    = bersihkan($nim_login);
    $dosen_pa     = bersihkan($_POST['dosen_pa'] ?? '');
    $sks          = (int)bersihkan($_POST['sks'] ?? 0);
    $tgl_mulai    = bersihkan($_POST['tanggal_mulai'] ?? '');
    $tgl_selesai  = bersihkan($_POST['tanggal_selesai'] ?? '');
    $tempat       = bersihkan($_POST['tempat'] ?? '');
    $alamat       = bersihkan($_POST['alamat'] ?? '');
    $tujuan_surat = bersihkan($_POST['tujuan_surat'] ?? '');
    $isCetak      = !empty($_POST['cetak']);

    // 3. Validasi Dosen Penasihat Akademik (PA)
    if ($dosen_pa === '') {
        echo json_encode(['status' => false, 'pesan' => 'Dosen Penasihat Akademik (PA) wajib dipilih']);
        exit;
    }

    // 4. Validasi minimal SKS yang telah ditempuh (syarat SOP: minimal 100 SKS)
    if ($sks < 100) {
        echo json_encode(['status' => false, 'pesan' => 'Jumlah SKS yang telah ditempuh minimal 100 SKS']);
        exit;
    }

    // 5. Validasi tanggal mulai pelaksanaan KP
    if ($tgl_mulai === '') {
        echo json_encode(['status' => false, 'pesan' => 'Rencana tanggal mulai KP wajib diisi']);
        exit;
    }

    // 6. Hitung tanggal selesai otomatis (default 2 bulan dari tanggal mulai) jika belum terisi
    if ($tgl_selesai === '') {
        $tgl_selesai = hitung_tanggal_selesai_kp($tgl_mulai);
    }

    // 7. Validasi kolom tempat, alamat, dan tujuan surat tidak boleh kosong
    if ($tempat === '' || $alamat === '' || $tujuan_surat === '') {
        echo json_encode(['status' => false, 'pesan' => 'Tempat instansi, alamat, dan tujuan surat wajib diisi']);
        exit;
    }

    // 8. Simpan / perbarui Dosen PA mahasiswa pada tabel mahasiswa
    $updateMhs = query("UPDATE mahasiswa SET dosen_pa = '$dosen_pa' WHERE nim = '$nim_clean'");
    if (!$updateMhs) {
        echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui data Dosen PA: ' . mysqli_error($GLOBALS['koneksi'])]);
        exit;
    }

    // 9. Simpan pembaruan data pengajuan rencana KP mahasiswa ke tabel kp
    $update = query("UPDATE kp SET 
        sks               = '$sks',
        tgl_start         = '$tgl_mulai',
        tanggal_mulai     = '$tgl_mulai',
        tanggal_penarikan = '$tgl_selesai',
        tanggal_selesai   = '$tgl_selesai',
        nama_instansi     = '$tempat',
        instansi          = '$tempat',
        tempat            = '$tempat',
        alamat_instansi   = '$alamat',
        pimpinan_instansi = '$tujuan_surat'
    WHERE nim = '$nim_clean'");

    // Jika terjadi kegagalan saat update ke database
    if (!$update) {
        echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui data pengajuan: ' . mysqli_error($GLOBALS['koneksi'])]);
        exit;
    }

    // 10. Logika Cetak: jika tombol cetak ditekan dan progres mahasiswa masih di Tahap 1, majukan progres ke Tahap 2
    $naikStep = false;
    $cetakUrl = '';
    if ($isCetak) {
        $progresSaatIni = get_progres_kp($nim_clean);
        if ((int)$progresSaatIni === 1) {
            $simpanProg = set_progres_kp($nim_clean, 2);
            if (!$simpanProg) {
                echo json_encode(['status' => false, 'pesan' => 'Gagal memproses peningkatan tahapan SOP']);
                exit;
            }
            $naikStep = true;
        }
        $cetakUrl = base_url() . '/mahasiswa/dokumen/persetujuan-pa/' . $nim_clean;
    }

    // 11. Berikan respon JSON standar sesuai konvensi php-native-crud
    echo json_encode([
        'status'    => true,
        'pesan'     => $naikStep
            ? 'Data berhasil disimpan dan melanjutkan ke tahap berikutnya'
            : 'Data rencana Kerja Praktek berhasil disimpan',
        'buka_tab'  => $cetakUrl,
        'naik_step' => $naikStep
    ]);
    exit;
}

// ================= UBAH / TULIS JUDUL KP =================
if (isset($_POST['ubah-judul-kp'])) {
    if (empty($nim_login)) {
        echo json_encode(['status' => false, 'pesan' => 'Sesi tidak valid, silakan login ulang']);
        exit;
    }

    $nim_clean = bersihkan($nim_login);
    $judul_raw = trim(strip_tags(stripslashes((string)($_POST['judul'] ?? ''))));
    $judul     = bersihkan($_POST['judul'] ?? '');

    if ($judul === '') {
        echo json_encode(['status' => false, 'pesan' => 'Judul Kerja Praktek tidak boleh kosong']);
        exit;
    }

    $cekKp = query("SELECT status FROM kp WHERE nim = '$nim_clean' LIMIT 1");
    if (!$cekKp || mysqli_num_rows($cekKp) === 0) {
        echo json_encode(['status' => false, 'pesan' => 'Data pendaftaran Kerja Praktek tidak ditemukan']);
        exit;
    }

    $rowKp = mysqli_fetch_assoc($cekKp);
    $stKp  = strtolower(trim((string)$rowKp['status']));
    if ($stKp === 'tidak aktif' || $stKp === 'nonaktif') {
        echo json_encode(['status' => false, 'pesan' => 'Status Kerja Praktek Anda belum aktif']);
        exit;
    }

    $progresSekarang = get_progres_kp($nim_clean);
    if ($progresSekarang === '-' || (int)$progresSekarang <= 2) {
        echo json_encode(['status' => false, 'pesan' => 'Judul Kerja Praktek baru dapat ditulis atau diubah setelah melewati Tahap 2']);
        exit;
    }

    $update = query("UPDATE kp SET judul = '$judul' WHERE nim = '$nim_clean'");

    if ($update) {
        echo json_encode([
            'status' => true,
            'pesan'  => 'Judul Kerja Praktek berhasil disimpan',
            'judul'  => $judul_raw
        ]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan judul Kerja Praktek']);
    }
    exit;
}

// ================= CETAK LEMBAR PERSETUJUAN PA (STEP 1) =================
if (isset($_POST['cetak-persetujuan-pa'])) {
    if (empty($nim_login)) {
        echo json_encode(['status' => false, 'pesan' => 'Sesi tidak valid']);
        exit;
    }

    $nim_clean = bersihkan($nim_login);
    $progresSaatIni = get_progres_kp($nim_clean);
    $naikStep = false;

    // Jika saat ini di step 1, otomatis majukan proses ke step 2
    if ((int)$progresSaatIni === 1) {
        $simpan = set_progres_kp($nim_clean, 2);
        if (!$simpan) {
            echo json_encode(['status' => false, 'pesan' => 'Gagal memproses kemajuan tahapan SOP']);
            exit;
        }
        $naikStep = true;
    }

    $cetakUrl = base_url() . '/mahasiswa/dokumen/persetujuan-pa/' . $nim_clean;

    echo json_encode([
        'status'    => true,
        'pesan'     => $naikStep
            ? 'Berhasil mencetak lembar persetujuan PA dan melanjutkan ke tahap berikutnya'
            : 'Membuka dokumen lembar persetujuan PA',
        'buka_tab'  => $cetakUrl,
        'naik_step' => $naikStep
    ]);
    exit;
}

// Default response
echo json_encode(['status' => false, 'pesan' => 'Aksi tidak dikenali']);
exit;

