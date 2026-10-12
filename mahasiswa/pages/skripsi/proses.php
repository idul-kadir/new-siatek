<?php
/*
    PROSES SKRIPSI MAHASISWA
    ---------------------------------------------
    Semua aksi terkait skripsi di sisi mahasiswa diproses di sini.
*/

require_once __DIR__ . '/../../../fungsi.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$nim_login = $_SESSION['nim'] ?? '';

// ================= UNGGAH BERKAS (SATUAN) KE TEMP_SKRIPSI =================
if (isset($_POST['unggah-berkas-skripsi'])) {
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

    // Cek apakah berkas ini sudah pernah diunggah sebelumnya di tabel temp_skripsi
    $cekTemp = query("SELECT id, file, status FROM temp_skripsi WHERE nim = '$nim_clean' AND id_persyaratan = '$id_persyaratan'");
    if ($cekTemp && mysqli_num_rows($cekTemp) > 0) {
        $rowTemp = mysqli_fetch_assoc($cekTemp);
        
        // Proteksi di sisi backend: Jika status berkas sudah "diterima", tolak proses upload ulang
        if (strtolower(trim((string)$rowTemp['status'])) === 'diterima') {
            echo json_encode(['status' => false, 'pesan' => 'Berkas ini telah diterima oleh operator dan tidak dapat diunggah ulang']);
            exit;
        }

        // Karena ini unggah ulang, panggil upload_file DENGAN parameter ke-3 (id_file lama)
        // Fungsi ini akan menghapus file fisik lama secara otomatis
        $id_file_lama = $rowTemp['file'];
        $id_file = upload_file($_FILES['file'], 'temp_skripsi', $id_file_lama);
        if (!$id_file) {
            echo json_encode(['status' => false, 'pesan' => 'Gagal mengunggah dan mengganti file server']);
            exit;
        }

        // Update data temp lama, status kembali "menunggu" dan ket kosong (karena ini uplaod ulang / perbaikan)
        $simpan = query("UPDATE temp_skripsi SET file = '$id_file', status = 'menunggu', ket = '' WHERE nim = '$nim_clean' AND id_persyaratan = '$id_persyaratan'");
    } else {
        // Upload file baru ke folder file/ di root
        $id_file = upload_file($_FILES['file'], 'temp_skripsi');
        if (!$id_file) {
            echo json_encode(['status' => false, 'pesan' => 'Gagal mengunggah file ke server']);
            exit;
        }

        // Insert baru ke temp, ket dikosongkan karena hanya diisi operator untuk alasan penolakan
        $simpan = query("INSERT INTO temp_skripsi (id_persyaratan, nim, file, status, ket) VALUES ('$id_persyaratan', '$nim_clean', '$id_file', 'menunggu', '')");
    }

    // Ambil proses/step saat ini dari tabel progres_skripsi (progres terakhir mahasiswa)
    $qStep = query("SELECT proses FROM progres_skripsi WHERE mahasiswa = '$nim_clean' ORDER BY waktu DESC LIMIT 1");
    $stepProgres = ($qStep && mysqli_num_rows($qStep) > 0) ? mysqli_fetch_assoc($qStep)['proses'] : 0;

    if ($simpan) {
        // Cek apakah semua berkas persyaratan untuk step ini sudah diunggah dan TIDAK ditolak
        $qSelesai = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_skripsi WHERE progres = '$stepProgres'");
        $jmlTotal = ($qSelesai && mysqli_num_rows($qSelesai) > 0) ? mysqli_fetch_assoc($qSelesai)['total'] : 0;

        $qValid = query("SELECT COUNT(*) AS cnt FROM temp_skripsi WHERE nim = '$nim_clean' AND status != 'ditolak' AND id_persyaratan IN (SELECT id FROM persyaratan_berkas_skripsi WHERE progres = '$stepProgres')");
        $jmlValid = ($qValid && mysqli_num_rows($qValid) > 0) ? mysqli_fetch_assoc($qValid)['cnt'] : 0;

        $prosesSelesai = false;
        // Jika SEMUA berkas sudah diunggah dan tidak ada yang berstatus 'ditolak'
        if ($jmlTotal > 0 && $jmlValid >= $jmlTotal) {
            $waktu = time();
            $nextProses = (int)$stepProgres + 1; // Pindah ke tahapan SOP selanjutnya
            
            // Selalu catat histori baru dengan waktu terbaru ke progres_skripsi
            query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ('$waktu', '$nextProses', '$nim_clean')");
            $prosesSelesai = true;
        }

        echo json_encode(['status' => true, 'pesan' => 'Berkas berhasil diunggah', 'proses_selesai' => $prosesSelesai]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Berkas gagal disimpan ke database sementara']);
    }
    exit;
}

// ================= DAFTAR SKRIPSI =================
if (isset($_POST['daftar-skripsi'])) {
    if (empty($nim_login)) {
        echo json_encode(['status' => false, 'pesan' => 'Sesi tidak valid, silakan login ulang']);
        exit;
    }

    $nim_clean = bersihkan($nim_login);

    // Cek apakah sudah terdaftar
    $cek = query("SELECT nim FROM skripsi WHERE nim = '$nim_clean'");
    if ($cek && mysqli_num_rows($cek) > 0) {
        echo json_encode(['status' => false, 'pesan' => 'Anda sudah terdaftar di skripsi']);
        exit;
    }

    // Insert ke tabel skripsi dengan status 'tidak aktif' (menunggu judul/pembimbing dll)
    // penunjukkan_pembimbing di-set NULL karena tabel bertipe INT
    $simpan = query("INSERT INTO skripsi (nim, judul, penunjukkan_pembimbing, status) VALUES ('$nim_clean', '', NULL, 'tidak aktif')");

    // Inisialisasi awal proses progres_skripsi ke step 1
    $waktu = time();
    query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ('$waktu', 1, '$nim_clean')");

    if ($simpan) {
        echo json_encode(['status' => true, 'pesan' => 'Berhasil mendaftar skripsi']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal mendaftar skripsi, terjadi kesalahan']);
    }
    exit;
}

// ================= TANDAI SELESAI LANGKAH SOP =================
if (isset($_POST['tandai-selesai-step'])) {
    if (empty($nim_login)) {
        echo json_encode(['status' => false, 'pesan' => 'Sesi tidak valid']);
        exit;
    }

    $nim_clean = bersihkan($nim_login);
    $waktu     = time();
    
    // Ambil proses/step saat ini
    $q_proses = query("SELECT proses FROM progres_skripsi WHERE mahasiswa = '$nim_clean' ORDER BY waktu DESC LIMIT 1");
    if ($q_proses && mysqli_num_rows($q_proses) > 0) {
        $lastProses = (int) mysqli_fetch_assoc($q_proses)['proses'];
        $nextProses = $lastProses + 1;
    } else {
        $nextProses = 2;
    }

    // Pindahkan langkah proses ke step selanjutnya
    $simpan = query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ('$waktu', '$nextProses', '$nim_clean')");

    if ($simpan) {
        echo json_encode(['status' => true, 'pesan' => 'Langkah SOP berhasil ditandai selesai']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal mengubah proses SOP']);
    }
    exit;
}
?>