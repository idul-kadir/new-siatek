<?php
// require function induk, karena butuh query(), bersihkan(), dan upload_file()
require_once __DIR__ . '/../../../../fungsi.php';

// ================= TAMBAH/EDIT DATA ARSIP =================
if (isset($_POST['simpan-data-arsip'])) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $userId = $_SESSION['id'] ?? '';
    
    // ambil & bersihkan semua input dari form
    $aksi      = bersihkan($_POST['aksi'] ?? '');
    $id        = bersihkan($_POST['id'] ?? '');
    $tahun     = bersihkan($_POST['tahun'] ?? '');
    $nama_file = bersihkan($_POST['nama_file'] ?? '');
    $akses     = bersihkan($_POST['akses'] ?? '');
    $penyintas = $_POST['penyintas'] ?? [];
    
    // validasi akses terbatas
    if ($akses == 'terbatas' && empty($penyintas)) {
        echo json_encode(['status' => false, 'pesan' => 'Pilih minimal 1 dosen untuk akses Terbatas!']);
        exit;
    }
    
    if ($aksi == 'tambah') {
        // pastikan file diupload
        if (!isset($_FILES['lampiran']) || $_FILES['lampiran']['error'] != 0) {
            echo json_encode(['status' => false, 'pesan' => 'Silakan unggah dokumen!']);
            exit;
        }
        
        $validasi = validasi_file($_FILES['lampiran']);
        if (!$validasi['valid']) {
            echo json_encode(['status' => false, 'pesan' => $validasi['pesan']]);
            exit;
        }
        
        // batas ukuran file maks 20MB
        if ($_FILES['lampiran']['size'] > 20971520) {
            echo json_encode(['status' => false, 'pesan' => 'Ukuran file maksimal 20MB']);
            exit;
        }
        
        // upload file fisik & simpan metadata ke tabel files
        $id_file = upload_file($_FILES['lampiran'], 'arsip_lain');
        if ($id_file === false) {
            echo json_encode(['status' => false, 'pesan' => 'Upload file gagal']);
            exit;
        }
        
        $idBaru = time() . '_' . bin2hex(random_bytes(8));
        $simpan = query("INSERT INTO arsip_lain (id, deskripsi, tahun, semester, file, pengupload, jenis_akses) VALUES ('$idBaru', '$nama_file', '$tahun', '-', '$id_file', '$userId', '$akses')");
        
        if ($simpan) {
            if ($akses == 'terbatas' && is_array($penyintas)) {
                foreach ($penyintas as $p) {
                    $pid = bersihkan($p);
                    query("INSERT INTO pemilik_file (id_arsip, pemilik, partner, keterangan) VALUES ('$idBaru', '$pid', 'arsip_lain', '')");
                }
            }
            echo json_encode(['status' => true, 'pesan' => 'Dokumen berhasil diunggah']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Dokumen gagal diunggah']);
        }
    } 
    else if ($aksi == 'edit') {
        // pastikan hanya pengunggah asli yang bisa edit
        $cekOwner = mysqli_fetch_assoc(query("SELECT pengupload, file FROM arsip_lain WHERE id = '$id'"));
        if (!$cekOwner || $cekOwner['pengupload'] != $userId) {
            echo json_encode(['status' => false, 'pesan' => 'Anda tidak memiliki hak untuk mengedit dokumen ini!']);
            exit;
        }
        
        $id_file_lama = $cekOwner['file'];
        
        // jika ada upload file baru
        if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] == 0) {
            $validasi = validasi_file($_FILES['lampiran']);
            if (!$validasi['valid']) {
                echo json_encode(['status' => false, 'pesan' => $validasi['pesan']]);
                exit;
            }
            if ($_FILES['lampiran']['size'] > 20971520) {
                echo json_encode(['status' => false, 'pesan' => 'Ukuran file maksimal 20MB']);
                exit;
            }
            
            $uploadNew = upload_file($_FILES['lampiran'], 'arsip_lain', $id_file_lama);
            if ($uploadNew === false) {
                echo json_encode(['status' => false, 'pesan' => 'Upload file pengganti gagal']);
                exit;
            }
        }
        
        $update = query("UPDATE arsip_lain SET deskripsi='$nama_file', tahun='$tahun', jenis_akses='$akses' WHERE id='$id'");
        
        if ($update) {
            // hapus relasi lama
            query("DELETE FROM pemilik_file WHERE id_arsip = '$id'");
            // simpan relasi baru jika terbatas
            if ($akses == 'terbatas' && is_array($penyintas)) {
                foreach ($penyintas as $p) {
                    $pid = bersihkan($p);
                    query("INSERT INTO pemilik_file (id_arsip, pemilik, partner, keterangan) VALUES ('$id', '$pid', '', '')");
                }
            }
            echo json_encode(['status' => true, 'pesan' => 'Dokumen berhasil diubah']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Dokumen gagal diubah']);
        }
    }
}

// ================= HAPUS DATA ARSIP =================
if (isset($_POST['hapus-data-arsip'])) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $userId = $_SESSION['id'] ?? '';
    $id = bersihkan($_POST['id'] ?? '');
    
    // pastikan hanya pengunggah asli yang bisa hapus
    $cekOwner = mysqli_fetch_assoc(query("SELECT pengupload, file FROM arsip_lain WHERE id = '$id'"));
    if (!$cekOwner || $cekOwner['pengupload'] != $userId) {
        echo json_encode(['status' => false, 'pesan' => 'Anda tidak memiliki hak untuk menghapus dokumen ini!']);
        exit;
    }
    
    $id_file_lama = $cekOwner['file'];
    
    $hapus = query("DELETE FROM arsip_lain WHERE id='$id'");
    if ($hapus) {
        query("DELETE FROM pemilik_file WHERE id_arsip = '$id'");
        
        // hapus file fisik dan datanya di tabel files
        $qFile = query("SELECT nama_file FROM files WHERE id = '$id_file_lama'");
        if ($qFile && mysqli_num_rows($qFile) > 0) {
            $fLama = mysqli_fetch_assoc($qFile)['nama_file'];
            $pathLama = __DIR__ . '/../../../../file/' . $fLama;
            if (file_exists($pathLama)) { @unlink($pathLama); }
            query("DELETE FROM files WHERE id = '$id_file_lama'");
        }
        
        echo json_encode(['status' => true, 'pesan' => 'Dokumen berhasil dihapus']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Dokumen gagal dihapus']);
    }
}
