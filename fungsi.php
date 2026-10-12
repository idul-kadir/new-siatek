<?php
require __DIR__ . '/koneksi.php';

function query($sql){
  global $koneksi;
  return mysqli_query($koneksi,$sql);
}

function bersihkan($data) {
  global $koneksi;
    // 1. Buang spasi di awal dan akhir
    $data = trim($data);
    
    // 2. Buang backslash (\)
    $data = stripslashes($data);
    
    // 3. Buang tag HTML / Script (XSS protection)
    $data = strip_tags($data);
    
    // 4. Escape karakter khusus SQL seperti ' atau " (SQL Injection protection)
    $data = mysqli_real_escape_string($koneksi, $data);
    
    return $data;
}

// Fungsi khusus untuk menampilkan data dari database agar aman di HTML,
// termasuk saat dipakai di atribut HTML seperti data-nama-file.
function tampil($data) {
  return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// ================= FUNCTION CATAT DATA FILE =================
// Dipakai upload_file() dan fungsi unduhan foto (simpan_foto_google).
// Mencatat metadata file yang SUDAH dipindahkan ke folder "file" root
// ke tabel "files" (struktur tabel files yang sudah ada).
// return: bool sukses/gagal.
function catat_file($id_file, $nama_file, $ekstensi, $tabel_asal){
  global $koneksi;
  $id_file_b      = bersihkan($id_file);
  $nama_file_b    = bersihkan($nama_file);
  $ekstensi_b     = bersihkan($ekstensi);
  $tabel_asal_b   = bersihkan($tabel_asal);
  $waktu          = time();
  $pengunggah     = bersihkan($_SESSION['id'] ?? '');
  // lokasi = path relatif file (mis. file/1787809027_d9cb8a4f0d18388e.jpg)
  $lokasi_b       = bersihkan('file/' . $nama_file);
  return query("INSERT INTO files (id, nama_file, format_file, waktu_unggah, tabel_asal, lokasi, pengunggah) VALUES ('$id_file_b', '$nama_file_b', '$ekstensi_b', $waktu, '$tabel_asal_b', '$lokasi_b', '$pengunggah')");
}

// ================= FUNCTION UPLOAD FILE =================
// Dipakai di halaman yang punya fitur upload file (foto, dokumen, lampiran dll).
// File fisik dipindahkan ke folder "file" di root project (__DIR__ = lokasi
// fungsi.php), datanya dicatat/diubah di tabel "files".
//
// @param string $id_file (opsional)
//   - KOSONG  = upload BARU: id unik + nama file dibuat otomatis, baris tabel "files"
//               di-INSERT, kolom entitas (mis. profil/foto) diisi id baru.
//   - BERISI  = MENGANTI file yang sudah ada: id TETAP dipakai (referensi kolom
//               entitas tidak berubah), tapi nama file fisik SELALU BARU
//               (id_file + suffix timestamp + random) supaya browser tidak cache.
//               File fisik lama dihapus, baris di tabel "files" di-UPDATE
//               nama_file, format_file, waktu & lokasi.
//
// return: id file (kalau berhasil) atau false (gagal).
function upload_file($file, $tabel_asal, $id_file = ''){
  global $koneksi;
  if(!isset($file['tmp_name']) || $file['error'] != 0){
    return false;
  }

  $nama_asli = $file['name'];
  $ekstensi  = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
  $waktu     = time();

  // tentukan id file
  $id_file = trim((string)$id_file);
  $ganti   = ($id_file !== '');
  if(!$ganti){
    // id file baru: gabungan waktu unix + string acak, praktis mustahil bentrok
    $id_file = $waktu . '_' . bin2hex(random_bytes(8));
  }

  // suffix singkat supaya nama file tidak kepanjangan
  // tapi tetap unik agar URL berbeda dari versi lama (anti-cache)
  $suffix       = bin2hex(random_bytes(4));
  $nama_file    = $id_file . '_' . $suffix . '.' . $ekstensi;
  $folder_tujuan= __DIR__ . '/file/' . $nama_file;
  $lokasi       = 'file/' . $nama_file;

  // mode ganti: hapus file fisik lama (nama_file lama yang tersimpan di tabel files)
  if($ganti){
    $rowLama = mysqli_fetch_assoc(query("SELECT nama_file FROM files WHERE id='" . mysqli_real_escape_string($koneksi, $id_file) . "' LIMIT 1"));
    if($rowLama){
      $lama = trim((string)($rowLama['nama_file'] ?? ''));
      if($lama !== '' && $lama !== $nama_file){
        $pathLama = __DIR__ . '/file/' . $lama;
        if(file_exists($pathLama)){ @unlink($pathLama); }
      }
    }
  }

  // pindahkan file baru ke folder file/ root
  $upload = move_uploaded_file($file['tmp_name'], $folder_tujuan);
  if(!$upload){
    return false;
  }

  if($ganti){
    // ubah HANYA nama_file, format_file, waktu & lokasi (id & tabel_asal tetap)
    $ok = query(
      "UPDATE files SET nama_file='" . bersihkan($nama_file) . "', format_file='" . bersihkan($ekstensi) . "', "
      . "waktu_unggah=" . (int)$waktu . ", lokasi='" . bersihkan($lokasi) . "' "
      . "WHERE id='" . bersihkan($id_file) . "'"
    );
  } else {
    // upload baru: insert baris baru
    $ok = catat_file($id_file, $nama_file, $ekstensi, $tabel_asal);
  }

  if(!$ok){
    @unlink($folder_tujuan);
    return false;
  }

  return $id_file;
}

// ================= FUNCTION BASE URL =================
// Menghitung base path URL proyek secara dinamis dari letak folder proyek
// (tempat fungsi.php ini berada = root proyek) terhadap DOCUMENT_ROOT server.
// Hasilnya otomatis menyesuaikan saat deploy: di docroot langsung -> '',
// di subfolder (mis. /redesain-siatek) -> '/redesain-siatek'.
// Jadi tidak perlu hardcode path dan tidak rusak pindah-pindah server.
function base_url(){
  // root proyek = lokasi file ini (fungsi.php ada sejajar folder "file")
  $root = str_replace('\\', '/', rtrim((string)__DIR__, '/'));
  $doc  = str_replace('\\', '/', rtrim((string)(getenv('DOCUMENT_ROOT') ?: ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/'));
  if ($doc === ''){
    // tidak dikenal (mis. CLI): fallback aman kosong
    return '';
  }
  if (strpos($root . '/', $doc . '/') === 0){
    // root proyek berada di dalam docroot -> base = sisa path setelah docroot
    return substr($root, strlen($doc));
  }
  // root proyek bukan di dalam docroot: biarkan '' (pakai path absolut dari web root)
  return '';
}

// ================= FUNCTION URL FILE =================
// Mengubah id file (yang disimpan di kolom entitas, misal profil/foto) menjadi
// URL publik yang bisa dipakai di atribut src, dengan lookup ke tabel "files".
// return: string URL (mis. /redesain-siatek/file/xxx.jpg) atau '' bila kosong/tak ditemukan.
function url_file($id_file){
  global $koneksi;
  $id_file = trim((string)$id_file);
  if($id_file === ''){
    return '';
  }
  $q = query("SELECT nama_file FROM files WHERE id = '" . mysqli_real_escape_string($koneksi, $id_file) . "' LIMIT 1");
  if(!$q){
    return '';
  }
  $r = mysqli_fetch_assoc($q);
  $nama_file = trim((string)($r['nama_file'] ?? ''));
  if($nama_file === ''){
    return '';
  }
  return base_url() . '/file/' . $nama_file;
}

// ================= FUNCTION VALIDASI FILE =================
// Memvalidasi format dan ukuran file sebelum diupload.
// Format yang diizinkan: gambar (jpg,jpeg,png,gif,webp,bmp,svg), dokumen (doc,docx,xls,xlsx,ppt,pptx,csv,txt), pdf.
// Ukuran: 8MB default, 50MB untuk level 9.
// return: ['valid' => true/false, 'pesan' => '...'].
function validasi_file($file, $level = 1){
  // cek apakah ada file yang diupload
  if(!isset($file['tmp_name']) || $file['error'] != 0){
    return ['valid' => false, 'pesan' => 'Gagal membuka file yang diunggah'];
  }

  // daftar ekstensi yang diizinkan
  $diperbolehkan = [
    // gambar
    'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg',
    // dokumen
    'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv', 'txt',
    // pdf
    'pdf'
  ];

  $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

  if(!in_array($ekstensi, $diperbolehkan, true)){
    return ['valid' => false, 'pesan' => 'Format file tidak diizinkan. Format yang boleh: JPG, JPEG, PNG, GIF, WEBP, BMP, SVG, DOC, DOCX, XLS, XLSX, PPT, PPTX, CSV, TXT, PDF'];
  }

  // tentukan batas ukuran berdasarkan level user
  $level = (int)$level;
  $batasMB = ($level === 9) ? 50 : 8;
  $batasByte = $batasMB * 1024 * 1024;

  if($file['size'] > $batasByte){
    return ['valid' => false, 'pesan' => 'Ukuran file maksimal ' . $batasMB . ' MB'];
  }

  return ['valid' => true, 'pesan' => ''];
}

// ================= FUNCTION CEK PROGRES SKRIPSI MAHASISWA =================
// Mengambil nomor proses/step skripsi terakhir yang dicatat untuk mahasiswa tertentu.
// Return: integer nomor proses (jika ada), atau '-' (jika belum ada).
function get_progres_skripsi($nim) {
    $nim_clean = bersihkan($nim);
    $q = query("SELECT proses FROM progres_skripsi WHERE mahasiswa = '$nim_clean' ORDER BY waktu DESC LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        return (int) $row['proses'];
    }
    return '-';
}