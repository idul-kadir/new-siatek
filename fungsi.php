<?php
if (defined('SIATEK_FUNGSI_LOADED')) {
    return;
}
define('SIATEK_FUNGSI_LOADED', true);

// ================= PENGATURAN TIMEZONE =================
// Standar zona waktu aplikasi: WITA (Asia/Makassar, UTC+8)
date_default_timezone_set('Asia/Makassar');

require_once __DIR__ . '/koneksi.php';

// Sinkronkan timezone sesi MySQL ke WITA (+08:00)
if (isset($koneksi) && $koneksi instanceof mysqli) {
    mysqli_query($koneksi, "SET time_zone = '+08:00'");
}

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

if (!function_exists('tanggal_indo')) {
    function tanggal_indo($tanggal) {
        return tgl_indo($tanggal);
    }
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
            $whereKp[] = "(k.nim LIKE '%$cariBersih%' OR m.nama LIKE '%$cariBersih%' OR k.judul LIKE '%$cariBersih%' OR k.nama_instansi LIKE '%$cariBersih%')";
        }
        $whereSqlKp = implode(" AND ", $whereKp);

        $sqlKp = "SELECT k.nim, k.judul, k.nama_instansi, k.status, k.surat_penunjukkan_penguji, m.nama AS nama_mhs, m.prodi, p.nama AS nama_prodi,
                         (SELECT pk.proses FROM progres_kp pk WHERE pk.mahasiswa = k.nim ORDER BY pk.waktu DESC, pk.proses DESC LIMIT 1) AS latest_step,
                         (SELECT COUNT(*) FROM surat_penunjukkan sp 
                          WHERE ((k.surat_penunjukkan_penguji IS NOT NULL AND k.surat_penunjukkan_penguji != '' AND k.surat_penunjukkan_penguji != '0' AND sp.id = k.surat_penunjukkan_penguji)
                                 OR (sp.nim = k.nim AND LOWER(sp.keterangan) LIKE '%penguji kp%'))) AS jml_surat_penguji
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

                // Khusus KP: jika progres mahasiswa KP ada di progres 12 dan surat penunjukkan penguji sudah ada, maka tidak perlu dimunculkan di antrian penunjukkan
                if ($progresNum === 12) {
                    $hasSuratCol = !empty($r['surat_penunjukkan_penguji']) && (string)$r['surat_penunjukkan_penguji'] !== '0';
                    $hasSuratTbl = ((int)($r['jml_surat_penguji'] ?? 0) > 0);
                    if ($hasSuratCol || $hasSuratTbl) {
                        continue;
                    }
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

                $judulDisplay = $r['judul'] ?: (!empty($r['nama_instansi']) ? 'Lokasi KP: ' . $r['nama_instansi'] : '-');
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

// ================= FUNCTION CACHE DAFTAR DOSEN =================
function dapatkan_semua_dosen_map() {
    static $dosenMap = null;
    if ($dosenMap === null) {
        $dosenMap = [];
        $q = query("SELECT id, nama FROM dosen ORDER BY nama ASC");
        if ($q) {
            while ($d = mysqli_fetch_assoc($q)) {
                $dosenMap[(string)$d['id']] = $d['nama'];
            }
        }
    }
    return $dosenMap;
}

// ================= FUNCTION AMBIL PENGUJI UJIAN MAHASISWA =================
function ambil_penguji_ujian($nim, $kategori, $idSuratTerdaftar = 0) {
    global $koneksi;
    $nim = bersihkan($nim);
    $kategori = bersihkan($kategori);
    $idSurat = (int)$idSuratTerdaftar;
    $dosenMap = dapatkan_semua_dosen_map();

    if ($idSurat <= 0 && $nim !== '') {
        if ($kategori === 'skripsi_hasil') {
            $q = query("SELECT penunjukkan_hasil FROM skripsi WHERE nim = '$nim' LIMIT 1");
            if ($q && ($r = mysqli_fetch_assoc($q)) && !empty($r['penunjukkan_hasil'])) {
                $idSurat = (int)$r['penunjukkan_hasil'];
            }
            if ($idSurat <= 0) {
                $q2 = query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PENGUJI HASIL' ORDER BY id DESC LIMIT 1");
                if ($q2 && ($r2 = mysqli_fetch_assoc($q2))) {
                    $idSurat = (int)$r2['id'];
                }
            }
        } elseif ($kategori === 'skripsi_tutup') {
            $q = query("SELECT penunjukkan_tutup FROM skripsi WHERE nim = '$nim' LIMIT 1");
            if ($q && ($r = mysqli_fetch_assoc($q)) && !empty($r['penunjukkan_tutup'])) {
                $idSurat = (int)$r['penunjukkan_tutup'];
            }
            if ($idSurat <= 0) {
                $q2 = query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PENGUJI TUTUP' ORDER BY id DESC LIMIT 1");
                if ($q2 && ($r2 = mysqli_fetch_assoc($q2))) {
                    $idSurat = (int)$r2['id'];
                }
            }
        } elseif ($kategori === 'kp') {
            $q = query("SELECT surat_penunjukkan_penguji FROM kp WHERE nim = '$nim' LIMIT 1");
            if ($q && ($r = mysqli_fetch_assoc($q)) && !empty($r['surat_penunjukkan_penguji'])) {
                $idSurat = (int)$r['surat_penunjukkan_penguji'];
            }
            if ($idSurat <= 0) {
                $q2 = query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND (keterangan = 'PENGUJI KP' OR keterangan LIKE '%PENGUJI%KP%') ORDER BY id DESC LIMIT 1");
                if ($q2 && ($r2 = mysqli_fetch_assoc($q2))) {
                    $idSurat = (int)$r2['id'];
                }
            }
        }
    }

    $daftarPenguji = [];
    if ($idSurat > 0) {
        $qSp = query("SELECT penguji1, penguji2, penguji3, penguji4, penguji5 FROM surat_penunjukkan WHERE id = $idSurat LIMIT 1");
        if ($qSp && ($sp = mysqli_fetch_assoc($qSp))) {
            for ($i = 1; $i <= 5; $i++) {
                $idDosen = trim((string)($sp["penguji$i"] ?? ''));
                if ($idDosen !== '' && isset($dosenMap[$idDosen])) {
                    $daftarPenguji[] = [
                        'peran' => 'Penguji ' . $i,
                        'id'    => $idDosen,
                        'nama'  => $dosenMap[$idDosen]
                    ];
                }
            }
        }
    }

    return $daftarPenguji;
}

// ================= FUNCTION AMBIL DAFTAR PESERTA BATCH UJIAN =================
function ambil_daftar_peserta_batch($idBatch, $filterProdi = '', $cari = '') {
    global $koneksi;
    $idBatch = (int)$idBatch;
    $hasil = [];

    // Filter dasar berdasarkan ID batch: hanya peserta yang kategorinya kosong atau NULL yang bisa diatur
    $where = [
        "pb.id_batch = '$idBatch'",
        "(pb.kategori IS NULL OR pb.kategori = '')"
    ];
    if ($filterProdi !== '') {
        $pClean = bersihkan($filterProdi);
        $where[] = "(m.prodi = '$pClean' OR pr.kode = '$pClean')";
    }
    if ($cari !== '') {
        $cClean = bersihkan($cari);
        $where[] = "(pb.mahasiswa LIKE '%$cClean%' OR m.nama LIKE '%$cClean%')";
    }
    $whereSql = implode(" AND ", $where);

    // Query data peserta beserta relasi tabel mahasiswa, prodi, skripsi, kp, nomor_surat, dan tahapan SOP
    $sql = "SELECT pb.*, pb.id AS id_peserta, pb.id_batch, pb.mahasiswa AS nim,
                   m.nama AS nama_mhs, m.prodi, m.angkatan, pr.nama AS nama_prodi,
                   (SELECT ps.proses FROM progres_skripsi ps WHERE ps.mahasiswa = pb.mahasiswa ORDER BY ps.waktu DESC, ps.proses DESC LIMIT 1) AS step_skripsi,
                   (SELECT pk.proses FROM progres_kp pk WHERE pk.mahasiswa = pb.mahasiswa ORDER BY pk.waktu DESC, pk.proses DESC LIMIT 1) AS step_kp,
                   s.penunjukkan_hasil, s.penunjukkan_tutup,
                   s.no_undangan_hasil, s.tanggal_hasil, s.jam_hasil, s.ruangan_hasil,
                   s.no_undangan_tutup, s.tanggal_tutup, s.jam_tutup, s.ruangan_tutup,
                   ns_hasil.nomor AS nomor_surat_hasil,
                   ns_tutup.nomor AS nomor_surat_tutup,
                   k.surat_penunjukkan_penguji,
                   k.no_undangan AS no_undangan_kp, k.tanggal_seminar, k.jam_seminar, k.ruangan AS ruangan_kp,
                   ns_kp.nomor AS nomor_surat_kp
            FROM peserta_batch pb
            LEFT JOIN mahasiswa m ON pb.mahasiswa = m.nim
            LEFT JOIN prodi pr ON m.prodi = pr.kode
            LEFT JOIN skripsi s ON pb.mahasiswa = s.nim
            LEFT JOIN nomor_surat ns_hasil ON s.no_undangan_hasil = ns_hasil.id
            LEFT JOIN nomor_surat ns_tutup ON s.no_undangan_tutup = ns_tutup.id
            LEFT JOIN kp k ON pb.mahasiswa = k.nim
            LEFT JOIN nomor_surat ns_kp ON k.no_undangan = ns_kp.id
            WHERE $whereSql
            ORDER BY pb.id DESC";

    $q = query($sql);
    if ($q && mysqli_num_rows($q) > 0) {
        while ($r = mysqli_fetch_assoc($q)) {
            $stepSkr = (int)($r['step_skripsi'] ?? 0);
            $stepKp  = (int)($r['step_kp'] ?? 0);

            $kategoriUjian  = '';
            $tahapanLabel   = 'Peserta Ujian';
            $badgeClass     = 'bg-slate-100 text-slate-700';
            $iconClass      = 'fas fa-graduation-cap';
            $idSuratPenguji = 0;

            // Deteksi jenis ujian berdasarkan nomor step SOP
            // Syarat: jika kp sudah bukan progres 12 dan skripsi sudah bukan progres 10 dan 18,
            // mahasiswa tidak boleh ada lagi di halaman ujian ini.
            if ($stepSkr === 10) {
                $kategoriUjian  = 'skripsi_hasil';
                $tahapanLabel   = 'Ujian Hasil';
                $badgeClass     = 'bg-amber-100 text-amber-800 border border-amber-200';
                $iconClass      = 'fas fa-file-alt text-amber-600';
                $idSuratPenguji = (int)($r['penunjukkan_hasil'] ?? 0);
            } elseif ($stepSkr === 18) {
                $kategoriUjian  = 'skripsi_tutup';
                $tahapanLabel   = 'Ujian Tutup';
                $badgeClass     = 'bg-purple-100 text-purple-800 border border-purple-200';
                $iconClass      = 'fas fa-award text-purple-600';
                $idSuratPenguji = (int)($r['penunjukkan_tutup'] ?? 0);
            } elseif ($stepKp === 12) {
                $kategoriUjian  = 'kp';
                $tahapanLabel   = 'Seminar KP';
                $badgeClass     = 'bg-blue-100 text-blue-800 border border-blue-200';
                $iconClass      = 'fas fa-briefcase text-blue-600';
                $idSuratPenguji = (int)($r['surat_penunjukkan_penguji'] ?? 0);
            } else {
                // Lewati jika sudah bukan progres 12 (KP) atau 10/18 (Skripsi)
                continue;
            }

            // Ambil daftar dosen penguji mahasiswa
            $penguji = ambil_penguji_ujian($r['nim'], $kategoriUjian, $idSuratPenguji);

            // Ekstrak rincian jadwal yang tersimpan
            $noUndangan = isset($r['nomor_undangan']) && $r['nomor_undangan'] !== '' ? (string)$r['nomor_undangan'] : '';
            $tglUjian   = isset($r['tanggal']) && $r['tanggal'] !== '' ? (string)$r['tanggal'] : '';
            $jamUjian   = isset($r['jam']) && $r['jam'] !== '' ? (string)$r['jam'] : '';
            $ruangUjian = isset($r['ruang']) && $r['ruang'] !== '' ? (string)$r['ruang'] : '';

            // Jika di tabel peserta_batch masih kosong, ambil dari data skripsi / kp yang tersimpan
            if ($noUndangan === '' && $tglUjian === '') {
                if (in_array($stepSkr, [8, 9, 10, 11], true)) {
                    $noUndangan = (string)(!empty($r['nomor_surat_hasil']) ? $r['nomor_surat_hasil'] : ($r['no_undangan_hasil'] ?? ''));
                    $tglUjian   = (string)($r['tanggal_hasil'] ?? '');
                    $jamUjian   = (string)($r['jam_hasil'] ?? '');
                    $ruangUjian = (string)($r['ruangan_hasil'] ?? '');
                } elseif (in_array($stepSkr, [16, 17, 18, 19, 20], true)) {
                    $noUndangan = (string)(!empty($r['nomor_surat_tutup']) ? $r['nomor_surat_tutup'] : ($r['no_undangan_tutup'] ?? ''));
                    $tglUjian   = (string)($r['tanggal_tutup'] ?? '');
                    $jamUjian   = (string)($r['jam_tutup'] ?? '');
                    $ruangUjian = (string)($r['ruangan_tutup'] ?? '');
                } elseif (in_array($stepKp, [11, 12, 13, 14, 15], true)) {
                    $noUndangan = (string)(!empty($r['nomor_surat_kp']) ? $r['nomor_surat_kp'] : ($r['no_undangan_kp'] ?? ''));
                    $tglUjian   = (string)($r['tanggal_seminar'] ?? '');
                    $jamUjian   = (string)($r['jam_seminar'] ?? '');
                    $ruangUjian = (string)($r['ruangan_kp'] ?? '');
                }
            }

            // Ambil teks nomor surat asli dari tabel nomor_surat jika yang tersimpan adalah ID
            $noUndangan = dapatkan_nomor_surat_teks($noUndangan);

            $sudahDiatur = (!empty($noUndangan) || !empty($tglUjian) || !empty($jamUjian) || !empty($ruangUjian) || $stepSkr >= 11 || $stepKp >= 14);

            $hasil[] = [
                'id_peserta'     => (int)$r['id_peserta'],
                'id_batch'       => (int)$r['id_batch'],
                'nim'            => $r['nim'],
                'nama_mhs'       => $r['nama_mhs'] ?: $r['nim'],
                'prodi'          => $r['prodi'] ?? '',
                'nama_prodi'     => $r['nama_prodi'] ?: ($r['prodi'] ?? '-'),
                'angkatan'       => $r['angkatan'] ?? '',
                'kategori'       => $kategoriUjian,
                'tahapan_label'  => $tahapanLabel,
                'badge_class'    => $badgeClass,
                'icon_class'     => $iconClass,
                'penguji'        => $penguji,
                'nomor_undangan' => $noUndangan,
                'tanggal_ujian'  => $tglUjian,
                'jam_ujian'      => $jamUjian,
                'ruang_ujian'    => $ruangUjian,
                'sudah_diatur'   => $sudahDiatur,
            ];
        }
    }

    return $hasil;
}

// ================= FUNCTION AMBIL ANTRIAN SIAP UJIAN =================
function ambil_antrian_siap_ujian($idBatchAktif, $filterProdi = '', $kategori = 'semua', $cari = '') {
    global $koneksi;
    $idBatchAktif = (int)$idBatchAktif;
    $hasil = [];

    // Kumpulkan NIM yang sudah ada di batch aktif ini dan BELUM diatur jadwalnya (kategori kosong/NULL).
    // Mahasiswa yang kategorinya sudah terisi (artinya sudah selesai di batch ini) tetap muncul
    // di antrian jika mereka punya tahapan ujian baru (misal: selesai KP, kini step 10 Skripsi).
    $sudahDiBatch = [];
    if ($idBatchAktif > 0) {
        $qSdh = query("SELECT mahasiswa FROM peserta_batch WHERE id_batch = '$idBatchAktif' AND (kategori IS NULL OR kategori = '')");
        if ($qSdh) {
            while ($sRow = mysqli_fetch_assoc($qSdh)) {
                $sudahDiBatch[trim((string)$sRow['mahasiswa'])] = true;
            }
        }
    }

    // 1. DATA ANTRIAN DARI SKRIPSI (Ujian Hasil & Ujian Tutup)
    if ($kategori === 'semua' || $kategori === 'skripsi_hasil' || $kategori === 'skripsi_tutup') {
        $whereSkr = ["(s.status IS NULL OR s.status = '' OR LOWER(s.status) NOT IN ('tidak aktif', 'alumni', 'kadaluarsa', 'ditolak'))"];
        if ($filterProdi !== '') {
            $pClean = bersihkan($filterProdi);
            $whereSkr[] = "(m.prodi = '$pClean' OR pr.kode = '$pClean')";
        }
        if ($cari !== '') {
            $cClean = bersihkan($cari);
            $whereSkr[] = "(s.nim LIKE '%$cClean%' OR m.nama LIKE '%$cClean%')";
        }
        $whereSqlSkr = implode(" AND ", $whereSkr);

        $sqlSkr = "SELECT s.nim, s.status, m.nama AS nama_mhs, m.prodi, m.angkatan, pr.nama AS nama_prodi,
                          (SELECT ps.proses FROM progres_skripsi ps WHERE ps.mahasiswa = s.nim ORDER BY ps.waktu DESC, ps.proses DESC LIMIT 1) AS step_terbaru
                   FROM skripsi s
                   LEFT JOIN mahasiswa m ON s.nim = m.nim
                   LEFT JOIN prodi pr ON m.prodi = pr.kode
                   WHERE $whereSqlSkr
                   ORDER BY s.nim DESC";

        $qSkr = query($sqlSkr);
        if ($qSkr && mysqli_num_rows($qSkr) > 0) {
            while ($r = mysqli_fetch_assoc($qSkr)) {
                $nim = trim((string)$r['nim']);

                // Lewati jika mahasiswa sudah terdaftar di batch aktif saat ini
                if (isset($sudahDiBatch[$nim])) {
                    continue;
                }

                $step    = (int)($r['step_terbaru'] ?? 0);
                $isHasil = ($step === 10);
                $isTutup = ($step === 18);

                if (!$isHasil && !$isTutup) {
                    continue; // Hanya mahasiswa dengan progres tepat 10 (Hasil) atau 18 (Tutup)
                }

                if ($kategori === 'skripsi_hasil' && !$isHasil) continue;
                if ($kategori === 'skripsi_tutup' && !$isTutup) continue;

                if ($isHasil) {
                    $katUjian     = 'skripsi_hasil';
                    $tahapanLabel = 'Ujian Hasil';
                    $badgeClass   = 'bg-amber-100 text-amber-800 border border-amber-200';
                    $iconClass    = 'fas fa-file-alt text-amber-600';
                } else {
                    $katUjian     = 'skripsi_tutup';
                    $tahapanLabel = 'Ujian Tutup';
                    $badgeClass   = 'bg-purple-100 text-purple-800 border border-purple-200';
                    $iconClass    = 'fas fa-award text-purple-600';
                }

                $penguji = ambil_penguji_ujian($nim, $katUjian);

                $hasil[] = [
                    'sumber'        => 'skripsi',
                    'kategori'      => $katUjian,
                    'nim'           => $nim,
                    'nama_mhs'      => $r['nama_mhs'] ?: $nim,
                    'prodi'         => $r['prodi'] ?? '',
                    'nama_prodi'    => $r['nama_prodi'] ?: ($r['prodi'] ?? '-'),
                    'angkatan'      => $r['angkatan'] ?? '',
                    'tahapan_label' => $tahapanLabel,
                    'badge_class'   => $badgeClass,
                    'icon_class'    => $iconClass,
                    'penguji'       => $penguji,
                ];
            }
        }
    }

    // 2. DATA ANTRIAN DARI KERJA PRAKTEK (Seminar KP)
    // Syarat wajib: Harus sudah ada penunjukkan dosen penguji
    if ($kategori === 'semua' || $kategori === 'kp') {
        $whereKp = ["(k.status IS NULL OR k.status = '' OR LOWER(k.status) NOT IN ('tidak aktif', 'alumni', 'kadaluarsa', 'ditolak'))"];
        if ($filterProdi !== '') {
            $pClean = bersihkan($filterProdi);
            $whereKp[] = "(m.prodi = '$pClean' OR pr.kode = '$pClean')";
        }
        if ($cari !== '') {
            $cClean = bersihkan($cari);
            $whereKp[] = "(k.nim LIKE '%$cClean%' OR m.nama LIKE '%$cClean%')";
        }
        $whereSqlKp = implode(" AND ", $whereKp);

        $sqlKp = "SELECT k.nim, k.status, k.surat_penunjukkan_penguji, m.nama AS nama_mhs, m.prodi, m.angkatan, pr.nama AS nama_prodi,
                         (SELECT pk.proses FROM progres_kp pk WHERE pk.mahasiswa = k.nim ORDER BY pk.waktu DESC, pk.proses DESC LIMIT 1) AS step_terbaru,
                         (SELECT COUNT(*) FROM surat_penunjukkan sp WHERE sp.nim = k.nim AND (sp.keterangan LIKE '%PENGUJI%KP%' OR sp.keterangan LIKE '%PENGUJI SEMINAR%')) AS ada_surat_penguji
                  FROM kp k
                  LEFT JOIN mahasiswa m ON k.nim = m.nim
                  LEFT JOIN prodi pr ON m.prodi = pr.kode
                  WHERE $whereSqlKp
                  ORDER BY k.nim DESC";

        $qKp = query($sqlKp);
        if ($qKp && mysqli_num_rows($qKp) > 0) {
            while ($r = mysqli_fetch_assoc($qKp)) {
                $nim = trim((string)$r['nim']);

                // Lewati jika sudah ada di batch aktif
                if (isset($sudahDiBatch[$nim])) {
                    continue;
                }

                $step = (int)($r['step_terbaru'] ?? 0);

                // Validasi penunjukkan penguji KP
                $punyaPenguji = (
                    (!empty($r['surat_penunjukkan_penguji']) && (int)$r['surat_penunjukkan_penguji'] > 0) ||
                    ((int)($r['ada_surat_penguji'] ?? 0) > 0) ||
                    ($step >= 13)
                );

                // Lewati jika belum ada penguji
                if (!$punyaPenguji) {
                    continue;
                }

                // Hanya sertakan mahasiswa yang berada tepat pada tahap seminar KP (progres 12)
                if ($step !== 12) {
                    continue;
                }

                $penguji = ambil_penguji_ujian($nim, 'kp', (int)($r['surat_penunjukkan_penguji'] ?? 0));

                $hasil[] = [
                    'sumber'        => 'kp',
                    'kategori'      => 'kp',
                    'nim'           => $nim,
                    'nama_mhs'      => $r['nama_mhs'] ?: $nim,
                    'prodi'         => $r['prodi'] ?? '',
                    'nama_prodi'    => $r['nama_prodi'] ?: ($r['prodi'] ?? '-'),
                    'angkatan'      => $r['angkatan'] ?? '',
                    'tahapan_label' => 'Seminar KP',
                    'badge_class'   => 'bg-blue-100 text-blue-800 border border-blue-200',
                    'icon_class'    => 'fas fa-briefcase text-blue-600',
                    'penguji'       => $penguji,
                ];
            }
        }
    }

    return $hasil;
}

// ================= FUNCTION DAPATKAN ID DOSEN PEMBUAT BATCH =================
function dapatkan_id_dosen_pembuat() {
    global $koneksi;

    // 1. Coba verifikasi apakah ID pada session login ($_SESSION['id']) terdaftar di tabel dosen
    if (!empty($_SESSION['id'])) {
        $sessId = bersihkan((string)$_SESSION['id']);
        $cek = query("SELECT id FROM dosen WHERE id = '$sessId' LIMIT 1");
        if ($cek && mysqli_num_rows($cek) > 0) {
            $r = mysqli_fetch_assoc($cek);
            return (string)$r['id'];
        }
    }

    // 2. Jika bukan ID numerik langsung, periksa berdasarkan NIP, username, atau user_id
    $nip = bersihkan((string)($_SESSION['nip'] ?? $_SESSION['username'] ?? $_SESSION['user_id'] ?? ''));
    if ($nip !== '') {
        $cek = query("SELECT id FROM dosen WHERE nip = '$nip' OR nidn = '$nip' LIMIT 1");
        if ($cek && mysqli_num_rows($cek) > 0) {
            $r = mysqli_fetch_assoc($cek);
            return (string)$r['id'];
        }
    }

    // 3. Fallback jika operator login dengan akun admin/superadmin yang tidak memiliki record di dosen:
    $cekFirst = query("SELECT id FROM dosen ORDER BY id ASC LIMIT 1");
    if ($cekFirst && mysqli_num_rows($cekFirst) > 0) {
        $r = mysqli_fetch_assoc($cekFirst);
        return (string)$r['id'];
    }

    return '1';
}

// ================= FUNCTION CEK KELAYAKAN BUKA BATCH BARU =================
function cek_kelayakan_buka_batch_baru() {
    global $koneksi;

    // 1. Cek apakah ada batch yang statusnya masih terbuka/aktif ('buka')
    $qBuka = query("SELECT id FROM batch_ujian WHERE status = 'buka' LIMIT 1");
    if ($qBuka && mysqli_num_rows($qBuka) > 0) {
        return [
            'layak'        => false,
            'belum_diatur' => 0,
            'pesan'        => 'Masih terdapat batch ujian yang sedang aktif/terbuka. Tutup batch tersebut terlebih dahulu sebelum membuka batch baru.'
        ];
    }

    // 2. Ambil batch paling baru (yang telah ditutup)
    $qPrev = query("SELECT id FROM batch_ujian ORDER BY id DESC LIMIT 1");
    if (!$qPrev || mysqli_num_rows($qPrev) === 0) {
        // Belum ada batch sama sekali di database -> boleh buka batch pertama
        return [
            'layak'        => true,
            'belum_diatur' => 0,
            'pesan'        => 'Belum ada batch ujian sebelumnya. Anda dapat membuka batch baru.'
        ];
    }

    $rPrev   = mysqli_fetch_assoc($qPrev);
    $idBatch = (int)$rPrev['id'];

    // 3. Periksa seluruh peserta pada batch terakhir tersebut:
    $qPeserta = query("SELECT pb.*, 
                              (SELECT ps.proses FROM progres_skripsi ps WHERE ps.mahasiswa = pb.mahasiswa ORDER BY ps.waktu DESC, ps.proses DESC LIMIT 1) AS step_skripsi,
                              (SELECT pk.proses FROM progres_kp pk WHERE pk.mahasiswa = pb.mahasiswa ORDER BY pk.waktu DESC, pk.proses DESC LIMIT 1) AS step_kp
                       FROM peserta_batch pb 
                       WHERE pb.id_batch = '$idBatch'");

    $totalPeserta = 0;
    $belumDiatur  = 0;

    if ($qPeserta && mysqli_num_rows($qPeserta) > 0) {
        while ($p = mysqli_fetch_assoc($qPeserta)) {
            $totalPeserta++;
            $stepSkr = (int)($p['step_skripsi'] ?? 0);
            $stepKp  = (int)($p['step_kp'] ?? 0);

            $katPb   = strtolower(trim((string)($p['kategori'] ?? '')));

            // Evaluasi kebutuhan jadwal sesuai kategori pendaftaran di batch:
            // 1. Peserta terdaftar KP: hanya cek apakah step KP masih 12 (menunggu seminar KP)
            // 2. Peserta terdaftar Skripsi Hasil: hanya cek apakah step Skripsi masih 10
            // 3. Peserta terdaftar Skripsi Tutup: hanya cek apakah step Skripsi masih 18
            // 4. Kategori belum ditentukan: cek apakah ada progres yang berada di tahap tunggu jadwal (10, 18, 12)
            if ($katPb === 'kp') {
                $masihPerluJadwal = ($stepKp === 12);
            } elseif ($katPb === 'skripsi_hasil') {
                $masihPerluJadwal = ($stepSkr === 10);
            } elseif ($katPb === 'skripsi_tutup') {
                $masihPerluJadwal = ($stepSkr === 18);
            } else {
                $masihPerluJadwal = ($stepSkr === 10 || $stepSkr === 18 || $stepKp === 12);
            }

            if ($masihPerluJadwal) {
                $belumDiatur++;
            }
        }
    }

    if ($belumDiatur > 0) {
        return [
            'layak'        => false,
            'belum_diatur' => $belumDiatur,
            'pesan'        => "Masih terdapat $belumDiatur peserta yang belum diatur jadwal ujiannya. Silakan lengkapi jadwal seluruh peserta terlebih dahulu sebelum membuka batch baru."
        ];
    }

    return [
        'layak'        => true,
        'belum_diatur' => 0,
        'pesan'        => 'Seluruh peserta telah diatur jadwalnya. Anda dapat membuka batch baru jika diperlukan.'
    ];
}

function get_nomor_surat($kategori){
  $tahun = date('Y');
  $katClean = bersihkan($kategori);
  $katLower = strtolower(trim($katClean));
  $nomor = 'Tidak Ditemukan';

  // 1. Coba exact match
  $cek_format = query("SELECT * FROM `format_nomor_surat` WHERE LOWER(TRIM(nama_surat)) = '$katLower' LIMIT 1");

  // 2. Coba alternatif ejaan (penunjukan vs penunjukkan, dengan/tanpa kata 'surat')
  if(!$cek_format || mysqli_num_rows($cek_format) === 0){
    $alt = [];
    $alt[] = str_replace('penunjukkan', 'penunjukan', $katLower);
    $alt[] = str_replace('penunjukan', 'penunjukkan', $katLower);
    $alt[] = trim(preg_replace('/^surat\s+/', '', $katLower));
    $alt[] = str_replace('penunjukkan', 'penunjukan', end($alt));
    $alt[] = str_replace('penunjukan', 'penunjukkan', end($alt));
    $altFiltered = array_unique(array_filter($alt));
    if (!empty($altFiltered)) {
      $altEscaped = "'" . implode("','", array_map('bersihkan', $altFiltered)) . "'";
      $cek_format = query("SELECT * FROM `format_nomor_surat` WHERE LOWER(TRIM(nama_surat)) IN ($altEscaped) LIMIT 1");
    }
  }

  // 3. Coba pencarian LIKE fleksibel jika belum ketemu
  if(!$cek_format || mysqli_num_rows($cek_format) === 0){
    if(strpos($katLower, 'penguji') !== false){
      $cek_format = query("SELECT * FROM `format_nomor_surat` WHERE LOWER(nama_surat) LIKE '%penguji%' LIMIT 1");
    } elseif(strpos($katLower, 'pembimbing') !== false){
      $cek_format = query("SELECT * FROM `format_nomor_surat` WHERE LOWER(nama_surat) LIKE '%pembimbing%' LIMIT 1");
    }
  }

  // 4. Jika kategori belum ada di format_nomor_surat, ambil template format yang ada dan auto-insert
  if(!$cek_format || mysqli_num_rows($cek_format) === 0){
    $qAny = query("SELECT format_nomor FROM `format_nomor_surat` WHERE format_nomor IS NOT NULL AND format_nomor != '' ORDER BY id ASC LIMIT 1");
    $fmtDef = ($qAny && ($rAny = mysqli_fetch_assoc($qAny)) && !empty($rAny['format_nomor'])) 
              ? $rAny['format_nomor'] 
              : '/UN28.1.28/TD.06/';
    
    // Auto insert agar ke depannya selalu match exact
    query("INSERT INTO `format_nomor_surat` (`nama_surat`, `format_nomor`) VALUES ('$katClean', '$fmtDef')");
    $cek_format = query("SELECT * FROM `format_nomor_surat` WHERE LOWER(TRIM(nama_surat)) = '$katLower' LIMIT 1");
  }

  if($cek_format && mysqli_num_rows($cek_format) > 0){
    $data_format = mysqli_fetch_assoc($cek_format);

    $cek_nomor = query("SELECT * FROM `nomor_surat` WHERE YEAR(tanggal) = '$tahun' ORDER BY CAST(urutan AS UNSIGNED) DESC, id DESC LIMIT 1");
    if($cek_nomor && mysqli_num_rows($cek_nomor) > 0){
      $data_nomor = mysqli_fetch_assoc($cek_nomor);
      $urutan = sprintf('%03d', ((int)$data_nomor['urutan'] + 1));
    } else {
      $urutan = sprintf('%03d', 1);
    }
    $nomor = $urutan . $data_format['format_nomor'] . $tahun;
  }

  return $nomor;
}

function tambah_nomor($nomor,$keterangan=NULL){
  $nomor = bersihkan($nomor);
  if ($nomor === '') return false;

  // Cek apakah nomor surat sudah pernah dicatat di tabel nomor_surat
  $cekNomor = query("SELECT id FROM `nomor_surat` WHERE nomor = '$nomor' LIMIT 1");
  if ($cekNomor && ($rN = mysqli_fetch_assoc($cekNomor))) {
    return $rN['id'];
  }

  $id = time();
  // Pastikan ID unik tidak bentrok dengan data lain
  while (true) {
    $cekId = query("SELECT id FROM `nomor_surat` WHERE id = '$id' LIMIT 1");
    if (!$cekId || mysqli_num_rows($cekId) === 0) {
      break;
    }
    $id++;
  }

  $pecah = explode('/',$nomor);
  $urutan = (int)($pecah[0] ?? 0);
  $keterangan = bersihkan($keterangan ?? '');
  $tanggal = date('Y-m-d');
  $tambah = query("INSERT INTO `nomor_surat`(`id`, `urutan`, `nomor`, `tanggal`, `keterangan`) VALUES ('$id','$urutan','$nomor','$tanggal','$keterangan')");
  if($tambah){
    return $id;
  }
  return false;
}

// ================= FUNCTION AMBIL TEKS NOMOR SURAT ASLI =================
function dapatkan_nomor_surat_teks($idOrNomor) {
  global $koneksi;
  $val = trim((string)$idOrNomor);
  if ($val === '') return '';

  // Jika sudah berupa format teks nomor surat (mengandung '/')
  if (strpos($val, '/') !== false) {
    return $val;
  }

  // Jika berupa numeric ID (merujuk ke id pada tabel nomor_surat)
  if (is_numeric($val)) {
    $q = query("SELECT nomor FROM `nomor_surat` WHERE id = '$val' LIMIT 1");
    if ($q && ($r = mysqli_fetch_assoc($q)) && !empty($r['nomor'])) {
      return $r['nomor'];
    }
  }

  return $val;
}

// ================= FUNCTION AMBIL BATCH UJIAN AKTIF =================
function get_batch_ujian_aktif() {
    $nowTs = time();

    // Deteksi ketersediaan kolom status pada batch_ujian
    $colsBatch = [];
    $qCols = query("SHOW COLUMNS FROM batch_ujian");
    if ($qCols) {
        while ($rC = mysqli_fetch_assoc($qCols)) {
            $colsBatch[] = strtolower($rC['Field']);
        }
    }
    $hasStatusCol = in_array('status', $colsBatch, true);
    $colStatusSql = $hasStatusCol ? ', status' : '';

    $q = query("SELECT id, tanggal_buka, tanggal_tutup, pembuat $colStatusSql FROM batch_ujian ORDER BY id DESC");
    if ($q && mysqli_num_rows($q) > 0) {
        while ($row = mysqli_fetch_assoc($q)) {
            $tglBuka  = $row['tanggal_buka'];
            $tglTutup = $row['tanggal_tutup'];
            $bukaTs   = strtotime($tglBuka . ' 00:00:00');
            $tutupTs  = strtotime($tglTutup . ' 23:59:59');

            $statusDb = strtolower(trim((string)($row['status'] ?? '')));
            $isManuallyClosed = ($hasStatusCol && in_array($statusDb, ['tutup', 'selesai', 'closed'], true));
            $isExplicitOpen = ($hasStatusCol && in_array($statusDb, ['buka', 'aktif', 'open'], true));

            // Aktif jika status 'buka' ATAU tanggal hari ini dalam rentang buka-tutup (dan tidak ditutup manual)
            $isAktif = $isExplicitOpen || (!$isManuallyClosed && $bukaTs && $tutupTs && $nowTs >= $bukaTs && $nowTs <= $tutupTs);

            if ($isAktif) {
                $diffDetik = $tutupTs - $nowTs;
                $sisaHari  = max(0, (int)ceil($diffDetik / 86400));
                $sisaHariLabel = ($sisaHari <= 0) ? 'Berakhir hari ini' : ($sisaHari . ' hari tersisa');

                return [
                    'id'              => (int)$row['id'],
                    'tanggal_buka'    => $tglBuka,
                    'tanggal_tutup'   => $tglTutup,
                    'periode_display' => tgl_indo($tglBuka) . ' s.d. ' . tgl_indo($tglTutup),
                    'sisa_hari'       => $sisaHari,
                    'sisa_hari_label' => $sisaHariLabel
                ];
            }
        }
    }
    return null;
}

// ================= FUNCTION CEK STATUS PESERTA BATCH =================
function get_status_peserta_batch($nim, $idBatch) {
    $nimClean = bersihkan($nim);
    $idBatch  = (int)$idBatch;
    $q = query("SELECT * FROM peserta_batch WHERE mahasiswa = '$nimClean' AND id_batch = $idBatch LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        return mysqli_fetch_assoc($q);
    }
    return null;
}
