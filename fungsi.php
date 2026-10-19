<?php
if (defined('SIATEK_FUNGSI_LOADED')) {
    return;
}
define('SIATEK_FUNGSI_LOADED', true);

require_once __DIR__ . '/koneksi.php';

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
  $pengunggah     = bersihkan($_SESSION['id'] ?? $_SESSION['nim'] ?? $_SESSION['user_id'] ?? $_SESSION['username'] ?? $_SESSION['nip'] ?? '');
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

  // pastikan folder file/ di root tujuan upload ada
  $folder_dir = __DIR__ . '/file';
  if (!is_dir($folder_dir)) {
    @mkdir($folder_dir, 0777, true);
  }

  // pindahkan file baru ke folder file/ root
  $upload = move_uploaded_file($file['tmp_name'], $folder_tujuan);
  if(!$upload){
    return false;
  }

  if($ganti){
    // ubah nama_file, format_file, waktu, lokasi, dan pastikan pengunggah tercatat
    $pengunggah = bersihkan($_SESSION['id'] ?? $_SESSION['nim'] ?? $_SESSION['user_id'] ?? $_SESSION['username'] ?? $_SESSION['nip'] ?? '');
    $setPengunggah = ($pengunggah !== '') ? ", pengunggah='" . $pengunggah . "'" : "";
    $ok = query(
      "UPDATE files SET nama_file='" . bersihkan($nama_file) . "', format_file='" . bersihkan($ekstensi) . "', "
      . "waktu_unggah=" . (int)$waktu . ", lokasi='" . bersihkan($lokasi) . "'$setPengunggah "
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
    $q = query("SELECT proses FROM progres_skripsi WHERE mahasiswa = '$nim_clean' ORDER BY waktu DESC, proses DESC LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        return (int) $row['proses'];
    }
    return '-';
}

// ================= FUNCTION CEK PROGRES KP MAHASISWA =================
// Mengambil nomor proses/step KP terakhir yang dicatat untuk mahasiswa tertentu.
// Return: integer nomor proses (jika ada), atau '-' (jika belum ada).
function get_progres_kp($nim) {
    $nim_clean = bersihkan($nim);
    $q = query("SELECT proses FROM progres_kp WHERE mahasiswa = '$nim_clean' ORDER BY waktu DESC, proses DESC LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        return (int) $row['proses'];
    }
    return '-';
}

// ================= FUNCTION MAJUKAN / SIMPAN PROGRES KP =================
function set_progres_kp($nim, $stepBaru, $waktu = null) {
    $nim_clean = bersihkan($nim);
    $stepBaru  = (int)$stepBaru;
    $waktu     = ($waktu !== null) ? (int)$waktu : time();
    return query("INSERT INTO progres_kp (waktu, proses, mahasiswa) VALUES ($waktu, $stepBaru, '$nim_clean')");
}

// ================= KONFIGURASI & HELPER DURASI KERJA PRAKTEK (KP) =================
// Standar durasi pelaksanaan Kerja Praktek (dalam satuan bulan).
// Silakan ubah angka ini (misalkan menjadi 3 atau lainnya) jika ada perubahan kebijakan durasi KP.
define('DURASI_DEFAULT_KP_BULAN', 2);

/**
 * Menghitung estimasi tanggal selesai Kerja Praktek secara otomatis berdasarkan tanggal mulai.
 * @param string $tanggalMulai Format: Y-m-d (contoh: '2026-10-15')
 * @param int|null $durasiBulan Jumlah bulan durasi (default mengacu ke DURASI_DEFAULT_KP_BULAN)
 * @return string Tanggal selesai dalam format 'Y-m-d'
 */
function hitung_tanggal_selesai_kp($tanggalMulai, $durasiBulan = null) {
    if (empty($tanggalMulai) || $tanggalMulai === '0000-00-00') {
        return '';
    }
    $bulan = ($durasiBulan !== null) ? (int)$durasiBulan : DURASI_DEFAULT_KP_BULAN;
    try {
        $dt = new DateTime($tanggalMulai);
        $dt->modify("+{$bulan} month");
        return $dt->format('Y-m-d');
    } catch (Exception $e) {
        return '';
    }
}

// ================= FUNCTION AMBIL DAFTAR SOP KP =================
function get_sop_kp_list() {
    $qSop = query("SELECT * FROM sop_kp");
    $list = [];
    if ($qSop) {
        while ($r = mysqli_fetch_assoc($qSop)) {
            $noVal  = $r['no'] ?? $r['id'] ?? $r['proses'] ?? $r['nomor'] ?? reset($r);
            $ketVal = $r['keterangan'] ?? $r['nama'] ?? $r['tahapan'] ?? $r['deskripsi'] ?? $r['judul'] ?? '';
            if ($ketVal === '') {
                foreach ($r as $k => $v) {
                    if (!in_array(strtolower($k), ['id', 'no', 'nomor', 'proses', 'waktu']) && !is_numeric($v) && trim((string)$v) !== '') {
                        $ketVal = $v;
                        break;
                    }
                }
            }
            $eksVal = $r['eksekutor'] ?? $r['aktor'] ?? $r['role'] ?? 'mahasiswa';
            $list[] = [
                'no'         => (int)$noVal,
                'keterangan' => trim((string)$ketVal),
                'eksekutor'  => strtolower(trim((string)$eksVal))
            ];
        }
        usort($list, fn($a, $b) => $a['no'] <=> $b['no']);
    }
    return $list;
}

// ================= FUNCTION AMBIL PRODI USER LOGIN =================
// Mengambil kode prodi staf/user yang sedang login dari session atau database
function get_prodi_user() {
    if (!empty($_SESSION['prodi'])) {
        return trim((string)$_SESSION['prodi']);
    }
    $nip      = bersihkan((string)($_SESSION['nip'] ?? ''));
    $userId   = bersihkan((string)($_SESSION['user_id'] ?? ''));
    $username = bersihkan((string)($_SESSION['username'] ?? ''));
    $sessId   = bersihkan((string)($_SESSION['id'] ?? ''));

    $identifiers = array_unique(array_filter([$nip, $userId, $username, $sessId]));
    if (empty($identifiers)) {
        return '';
    }

    $idListEsc = "'" . implode("','", array_map('bersihkan', $identifiers)) . "'";

    $level = (int)($_SESSION['level'] ?? 0);
    if ($level === 5) {
        $q = query("SELECT kode FROM prodi WHERE kaprodi IN ($idListEsc) LIMIT 1");
        if ($q && ($r = mysqli_fetch_assoc($q)) && !empty($r['kode'])) {
            $_SESSION['prodi'] = trim((string)$r['kode']);
            return $_SESSION['prodi'];
        }
    }

    $qDosen = query("SELECT prodi FROM dosen WHERE nip IN ($idListEsc) OR id IN ($idListEsc) OR nidn IN ($idListEsc) LIMIT 1");
    if ($qDosen && ($rD = mysqli_fetch_assoc($qDosen)) && !empty($rD['prodi'])) {
        $_SESSION['prodi'] = trim((string)$rD['prodi']);
        return $_SESSION['prodi'];
    }

    return '';
}

// ================= FUNCTION FORMAT TANGGAL INDONESIA =================
function tgl_indo($tanggal) {
    if (empty($tanggal) || $tanggal === '0000-00-00') return '-';
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $timestamp = strtotime($tanggal);
    if (!$timestamp) return $tanggal;
    $pecahkan = explode('-', date('Y-m-d', $timestamp));
    return (int)$pecahkan[2] . ' ' . ($bulan[(int)$pecahkan[1]] ?? '') . ' ' . $pecahkan[0];
}

// ================= FUNCTION KONFIGURASI STEP PENUNJUKKAN =================
// Menentukan nomor step SOP untuk penunjukkan pembimbing dan penguji.
// Skripsi: step 3 (Pembimbing), step 9 (Penguji Hasil), step 17 (Penguji Tutup).
// Kerja Praktek: step 6 (Pembimbing KP), step 12 (Penguji Seminar KP).
function get_step_penunjukkan_config() {
    return [
        'skripsi' => [
            'pembimbing'    => [3],
            'penguji_hasil' => [9],
            'penguji_tutup' => [17]
        ],
        'kp' => [
            'pembimbing' => [6],
            'penguji'    => [12]
        ]
    ];
}

// ================= FUNCTION AMBIL ANTRIAN PENUNJUKKAN MAHASISWA =================
// Mengambil daftar mahasiswa yang sedang menunggu penetapan pembimbing/penguji secara batch (cepat & anti-timeout).
function ambil_antrian_penunjukkan($filterProdi = '', $kategoriFilter = 'semua', $cari = '') {
    $hasil = [];

    // 1. Skripsi (Step 3 = Pembimbing, Step 9 = Penguji Hasil, Step 17 = Penguji Tutup)
    if ($kategoriFilter === 'semua' || $kategoriFilter === '' || $kategoriFilter === 'skripsi') {
        $whereSkr = ["(s.status IS NULL OR s.status = '' OR LOWER(s.status) NOT IN ('tidak aktif', 'alumni', 'kadaluarsa', 'ditolak'))"];
        if ($filterProdi !== '') {
            $prodiBersih = bersihkan($filterProdi);
            $whereSkr[] = "(m.prodi = '$prodiBersih' OR p.kode = '$prodiBersih')";
        }
        if ($cari !== '') {
            $cariBersih = bersihkan($cari);
            $whereSkr[] = "(s.nim LIKE '%$cariBersih%' OR m.nama LIKE '%$cariBersih%' OR s.judul LIKE '%$cariBersih%')";
        }
        $whereSqlSkr = implode(" AND ", $whereSkr);

        $sqlSkr = "SELECT s.nim, s.judul, s.status, m.nama AS nama_mhs, m.prodi, p.nama AS nama_prodi,
                          (SELECT ps.proses FROM progres_skripsi ps WHERE ps.mahasiswa = s.nim ORDER BY ps.waktu DESC, ps.proses DESC LIMIT 1) AS latest_step
                   FROM skripsi s
                   LEFT JOIN mahasiswa m ON s.nim = m.nim
                   LEFT JOIN prodi p ON m.prodi = p.kode
                   WHERE $whereSqlSkr
                   ORDER BY s.nim DESC";

        $qSkr = query($sqlSkr);
        if ($qSkr && mysqli_num_rows($qSkr) > 0) {
            while ($r = mysqli_fetch_assoc($qSkr)) {
                $progresNum = (int)($r['latest_step'] ?? 0);
                if (!in_array($progresNum, [3, 9, 17], true)) {
                    continue;
                }

                $nim = trim((string)$r['nim']);
                $jenisTugas = '';
                $kebutuhanLabel = '';
                $tahapanLabel = '';

                if ($progresNum === 3) {
                    $jenisTugas = 'pembimbing';
                    $kebutuhanLabel = 'Pembimbing Skripsi';
                    $tahapanLabel = 'Proposal Skripsi';
                } elseif ($progresNum === 9) {
                    $jenisTugas = 'penguji_hasil';
                    $kebutuhanLabel = 'Penguji Ujian Hasil';
                    $tahapanLabel = 'Ujian Hasil Skripsi';
                } elseif ($progresNum === 17) {
                    $jenisTugas = 'penguji_tutup';
                    $kebutuhanLabel = 'Penguji Ujian Tutup';
                    $tahapanLabel = 'Ujian Tutup Skripsi';
                }

                $hasil[] = [
                    'sumber'          => 'skripsi',
                    'nim'             => $nim,
                    'nama_mhs'        => $r['nama_mhs'] ?: $nim,
                    'prodi'           => $r['prodi'] ?? '',
                    'nama_prodi'      => $r['nama_prodi'] ?: ($r['prodi'] ?? '-'),
                    'judul'           => $r['judul'] ?? '',
                    'progres'         => $progresNum,
                    'tahapan_label'   => $tahapanLabel,
                    'jenis_tugas'     => $jenisTugas,
                    'kebutuhan_label' => $kebutuhanLabel
                ];
            }
        }
    }

    // 2. Kerja Praktek (Step 6 = Pembimbing KP, Step 12 = Penguji Seminar KP)
    if ($kategoriFilter === 'semua' || $kategoriFilter === '' || $kategoriFilter === 'kp') {
        $whereKp = ["(k.status IS NULL OR k.status = '' OR LOWER(k.status) NOT IN ('tidak aktif', 'alumni', 'kadaluarsa', 'ditolak'))"];
        if ($filterProdi !== '') {
            $prodiBersih = bersihkan($filterProdi);
            $whereKp[] = "(m.prodi = '$prodiBersih' OR p.kode = '$prodiBersih')";
        }
        if ($cari !== '') {
            $cariBersih = bersihkan($cari);
            $whereKp[] = "(k.nim LIKE '%$cariBersih%' OR m.nama LIKE '%$cariBersih%' OR k.judul LIKE '%$cariBersih%' OR k.tempat LIKE '%$cariBersih%')";
        }
        $whereSqlKp = implode(" AND ", $whereKp);

        $sqlKp = "SELECT k.nim, k.judul, k.tempat, k.status, m.nama AS nama_mhs, m.prodi, p.nama AS nama_prodi,
                         (SELECT pk.proses FROM progres_kp pk WHERE pk.mahasiswa = k.nim ORDER BY pk.waktu DESC, pk.proses DESC LIMIT 1) AS latest_step
                  FROM kp k
                  LEFT JOIN mahasiswa m ON k.nim = m.nim
                  LEFT JOIN prodi p ON m.prodi = p.kode
                  WHERE $whereSqlKp
                  ORDER BY k.nim DESC";

        $qKp = query($sqlKp);
        if ($qKp && mysqli_num_rows($qKp) > 0) {
            while ($r = mysqli_fetch_assoc($qKp)) {
                $progresNum = (int)($r['latest_step'] ?? 0);
                if (!in_array($progresNum, [6, 12], true)) {
                    continue;
                }

                $nim = trim((string)$r['nim']);
                $jenisTugas = '';
                $kebutuhanLabel = '';
                $tahapanLabel = '';

                if ($progresNum === 6) {
                    $jenisTugas = 'pembimbing';
                    $kebutuhanLabel = 'Pembimbing KP';
                    $tahapanLabel = 'Pendaftaran KP';
                } elseif ($progresNum === 12) {
                    $jenisTugas = 'penguji';
                    $kebutuhanLabel = 'Penguji Seminar KP';
                    $tahapanLabel = 'Seminar KP';
                }

                $judulDisplay = $r['judul'] ?: ($r['tempat'] ? 'Lokasi KP: ' . $r['tempat'] : '-');
                $hasil[] = [
                    'sumber'          => 'kp',
                    'nim'             => $nim,
                    'nama_mhs'        => $r['nama_mhs'] ?: $nim,
                    'prodi'           => $r['prodi'] ?? '',
                    'nama_prodi'      => $r['nama_prodi'] ?: ($r['prodi'] ?? '-'),
                    'judul'           => $judulDisplay,
                    'progres'         => $progresNum,
                    'tahapan_label'   => $tahapanLabel,
                    'jenis_tugas'     => $jenisTugas,
                    'kebutuhan_label' => $kebutuhanLabel
                ];
            }
        }
    }

    return $hasil;
}