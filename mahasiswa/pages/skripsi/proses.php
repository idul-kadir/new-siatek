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

    // Ambil proses/step saat ini menggunakan helper get_progres_skripsi()
    $progresSaatIni = get_progres_skripsi($nim_clean);
    $stepProgres    = ($progresSaatIni !== '-') ? (int)$progresSaatIni : 0;

    if ($simpan) {
        // Ambil progres dari berkas persyaratan yang sedang diunggah
        $qProgBerkas   = query("SELECT progres FROM persyaratan_berkas_skripsi WHERE id = '$id_persyaratan' LIMIT 1");
        $progresBerkas = ($qProgBerkas && mysqli_num_rows($qProgBerkas) > 0) ? (int)mysqli_fetch_assoc($qProgBerkas)['progres'] : 0;

        $prosesSelesai = false;

        // Kenaikan step otomatis HANYA boleh terjadi jika progres mahasiswa saat ini ($stepProgres)
        // sama dengan progres berkas yang diunggah ($progresBerkas).
        // Jika mahasiswa mencicil berkas step 20 saat masih di step 19 (atau step 12 saat di step 11),
        // progres mahasiswa TIDAK BOLEH lompat.
        if ($progresBerkas > 0 && $stepProgres === $progresBerkas) {
            // Cek apakah semua berkas persyaratan untuk step ini sudah diunggah dan TIDAK ditolak
            $qSelesai = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_skripsi WHERE progres = '$stepProgres'");
            $jmlTotal = ($qSelesai && mysqli_num_rows($qSelesai) > 0) ? (int)mysqli_fetch_assoc($qSelesai)['total'] : 0;

            $qValid = query("SELECT COUNT(DISTINCT id_persyaratan) AS cnt FROM temp_skripsi WHERE nim = '$nim_clean' AND status != 'ditolak' AND id_persyaratan IN (SELECT id FROM persyaratan_berkas_skripsi WHERE progres = '$stepProgres')");
            $jmlValid = ($qValid && mysqli_num_rows($qValid) > 0) ? (int)mysqli_fetch_assoc($qValid)['cnt'] : 0;

            // Jika SEMUA berkas sudah diunggah dan tidak ada yang berstatus 'ditolak'
            if ($jmlTotal > 0 && $jmlValid >= $jmlTotal) {
                $waktu = time();
                $nextProses = $stepProgres + 1; // Pindah ke tahapan SOP selanjutnya
                
                // Selalu catat histori baru dengan waktu terbaru ke progres_skripsi
                query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ('$waktu', '$nextProses', '$nim_clean')");
                $prosesSelesai = true;
            }
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
    
    // Ambil proses/step saat ini menggunakan helper get_progres_skripsi()
    $progresSaatIni = get_progres_skripsi($nim_clean);
    $nextProses     = ($progresSaatIni !== '-') ? ((int)$progresSaatIni + 1) : 2;

    // Pindahkan langkah proses ke step selanjutnya
    $simpan = query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ('$waktu', '$nextProses', '$nim_clean')");

    if ($simpan) {
        echo json_encode(['status' => true, 'pesan' => 'Langkah SOP berhasil ditandai selesai']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal mengubah proses SOP']);
    }
    exit;
}

// ================= UBAH / TULIS JUDUL SKRIPSI =================
if (isset($_POST['ubah-judul-skripsi'])) {
    if (empty($nim_login)) {
        echo json_encode(['status' => false, 'pesan' => 'Sesi tidak valid, silakan login ulang']);
        exit;
    }

    $nim_clean  = bersihkan($nim_login);
    $judul_raw  = trim(strip_tags(stripslashes((string)($_POST['judul'] ?? ''))));
    $judul      = bersihkan($_POST['judul'] ?? '');

    if ($judul === '') {
        echo json_encode(['status' => false, 'pesan' => 'Judul skripsi tidak boleh kosong']);
        exit;
    }

    // Pastikan mahasiswa sudah terdaftar dan status skripsinya aktif
    $cekSkripsi = query("SELECT status FROM skripsi WHERE nim = '$nim_clean' LIMIT 1");
    if (!$cekSkripsi || mysqli_num_rows($cekSkripsi) === 0) {
        echo json_encode(['status' => false, 'pesan' => 'Data pendaftaran skripsi tidak ditemukan']);
        exit;
    }

    $rowSkripsi = mysqli_fetch_assoc($cekSkripsi);
    $stSkripsi  = strtolower(trim((string)$rowSkripsi['status']));
    if ($stSkripsi === 'tidak aktif' || $stSkripsi === 'nonaktif') {
        echo json_encode(['status' => false, 'pesan' => 'Status skripsi Anda belum aktif']);
        exit;
    }

    // Pastikan mahasiswa sudah melewati tahap 2 (progres > 2)
    $progresSekarang = get_progres_skripsi($nim_clean);
    if ($progresSekarang === '-' || (int)$progresSekarang <= 2) {
        echo json_encode(['status' => false, 'pesan' => 'Judul skripsi baru dapat ditulis atau diubah setelah melewati Tahap 2']);
        exit;
    }

    // Simpan perubahan judul ke tabel skripsi
    $update = query("UPDATE skripsi SET judul = '$judul' WHERE nim = '$nim_clean'");

    if ($update) {
        echo json_encode([
            'status' => true,
            'pesan'  => 'Judul skripsi berhasil disimpan',
            'judul'  => $judul_raw
        ]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan judul skripsi']);
    }
    exit;
}

// ================= CETAK LEMBAR PERSETUJUAN PEMBIMBING (STEP 14) =================
if (isset($_POST['cetak-persetujuan-pembimbing'])) {
    if (empty($nim_login)) {
        echo json_encode(['status' => false, 'pesan' => 'Sesi tidak valid']);
        exit;
    }

    $nim_clean = bersihkan($nim_login);
    $waktu     = time();

    // Ambil proses/step saat ini menggunakan helper get_progres_skripsi()
    $progresSaatIni = get_progres_skripsi($nim_clean);
    $naikStep       = false;

    // Hanya naikkan ke step 15 jika saat ini mahasiswa memang berada di step 14
    if ((int)$progresSaatIni === 14) {
        $simpan = query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ('$waktu', 15, '$nim_clean')");
        if (!$simpan) {
            echo json_encode(['status' => false, 'pesan' => 'Gagal memproses tahapan SOP']);
            exit;
        }
        $naikStep = true;
    }

    // Link dummy cetak dokumen lembar persetujuan pembimbing
    $cetakUrl = base_url() . '/mahasiswa/dokumen/persetujuan-pembimbing/' . $nim_clean;

    echo json_encode([
        'status'    => true,
        'pesan'     => $naikStep
            ? 'Berhasil mencetak lembar persetujuan pembimbing dan melanjutkan ke tahap berikutnya'
            : 'Membuka dokumen lembar persetujuan pembimbing',
        'buka_tab'  => $cetakUrl,
        'naik_step' => $naikStep
    ]);
    exit;
}
?>