<?php
/**
 * PROSES BIODATA — semua aksi simpan halaman Biodata ditangani di sini.
 * Dipanggil VIA AJAX (jQuery) dari pages/biodata/biodata.php.
 * Respons selalu JSON {status: bool, pesan: '...', data?: {...}}.
 *
 * Aksi:
 *   POST ubah-biodata       -> update profil dosen/pegawai + bidang ilmu (keahlian)
 *   POST ubah-password      -> verifikasi lama lalu ganti password (password_hash)
 *   POST tambah-pendidikan  -> insert riwayat pendidikan
 *   GET  hapus-pendidikan   -> delete riwayat pendidikan milik sendiri
 *   POST detail-pendidikan  -> kembalikan HTML daftar riwayat pendidikan (untuk reload konten)
 */
require __DIR__ . "/../../fungsi.php";

if (session_status() === PHP_SESSION_NONE) session_start();
mysqli_set_charset($GLOBALS['koneksi'], 'utf8mb4');

/* Identitas pengguna aktif dari session login (daftar akses langsung tanpa login
   ditolak). Kunci identitas utama = kolom id (dosen.id); nip sebagai pembaca fallback. */
$idAktif  = (string)($_SESSION['id'] ?? '');
$nipAktif = (string)($_SESSION['nip'] ?? $_SESSION['username'] ?? '');

if ($idAktif === '' && $nipAktif === '') {
    kirim(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
}

function kirim(array $p) {
    echo json_encode($p);
    exit;
}

/* ===================== UBAH BIODATA ===================== */
if (isset($_POST['ubah-biodata'])) {

    $nama = trim((string)($_POST['nama'] ?? ''));
    if ($nama === '') kirim(['status' => false, 'pesan' => 'Nama lengkap wajib diisi']);

    $nipBaru = trim((string)($_POST['nip'] ?? ''));
    if ($nipBaru === '') kirim(['status' => false, 'pesan' => 'NIP wajib diisi']);

    $nidn    = preg_replace('/[^0-9]/', '', (string)($_POST['nidn'] ?? ''));
    $jabfung = trim((string)($_POST['jabfung'] ?? ''));
    $status  = in_array($_POST['status_kepegawaian'] ?? '', ['PNS', 'PPPK', 'Kontrak'], true)
             ? $_POST['status_kepegawaian'] : null;
    $tahun   = (int)($_POST['tahun_mulai'] ?? 0);
    $tahunN  = $tahun > 1900 ? $tahun : null;
    $prodi   = trim((string)($_POST['prodi'] ?? ''));
    $email   = trim((string)($_POST['email'] ?? ''));
    $hp      = trim((string)($_POST['no_hp'] ?? ''));

    /* Bidang ilmu dinamis: tag dipisah "|" dari JS -> disimpan pisah koma */
    $bidang = implode(', ', array_filter(array_map('trim', explode('|', (string)($_POST['bidang'] ?? ''))), 'strlen'));

    // kolom nullable: kalau kosong isi NULL (bukan string kosong) — string kosong
    // pada kolom integer/opsional bisa memicu error strict MySQL.
    $statusSql = ($status === null) ? 'NULL' : "'" . bersihkan($status) . "'";
    $tahunSql  = ($tahunN === null) ? 'NULL' : (int)$tahunN;

    // kunci identitas: id (identitas utama); fallback nip bila session id kosong
    $kunciWhere = ($nimAktif !== '') ? "nim='" . bersihkan($nimAktif) . "'" : "nim='" . bersihkan($nimAktif) . "'";

    $ok = query(
        "UPDATE mahasiswa SET nama='" . bersihkan($nama) . "', "
        . "prodi='" . bersihkan($prodi) . "', "
        . "email='" . bersihkan($email) . "', no_hp='" . bersihkan($hp) . "' "
        . "WHERE " . $kunciWhere
    );

    if (!$ok) kirim(['status' => false, 'pesan' => 'Gagal menyimpan data']);

    // nip baru langsung disimpan ke session agar identitas tetap konsisten
    $_SESSION['nim'] = $nimAktif;

    kirim([
        'status' => true,
        'pesan'  => 'Data tersimpan.',
        'data'   => ['nim' => $nimAktif, 'nama' => $nama],
    ]);
}

/* ===================== UBAH PASSWORD ===================== */
if (isset($_POST['ubah-password'])) {

    // kunci identitas: id (identitas utama); fallback nip bila session id kosong
    $kunciWhere = ($nimAktif !== '') ? "nim='" . bersihkan($nimAktif) . "'" : "nim='" . bersihkan($nimAktif) . "'";

    $rowStored = mysqli_fetch_assoc(query("SELECT password FROM mahasiswa WHERE " . $kunciWhere . " LIMIT 1"));
    $stored = (string)($rowStored['password'] ?? '');

    $lama = (string)($_POST['pw_lama'] ?? '');
    $baru = (string)($_POST['pw_baru'] ?? '');
    $konf = (string)($_POST['pw_konfirmasi'] ?? '');

    /* dukung data lama: bcrypt modern, plain, atau md5 */
    $validLama = $lama !== '' && (
        password_verify($lama, $stored) ||
        hash_equals($stored, md5($lama)) ||
        hash_equals($stored, $lama)
    );
    if (!$validLama) kirim(['status' => false, 'pesan' => 'Password saat ini salah']);
    if (strlen($baru) < 8) kirim(['status' => false, 'pesan' => 'Password baru minimal 8 karakter']);
    if (!preg_match('/[A-Z]/', $baru) || !preg_match('/[a-z]/', $baru) || !preg_match('/[0-9]/', $baru))
        kirim(['status' => false, 'pesan' => 'Password harus memuat huruf besar, kecil, dan angka']);
    if ($baru !== $konf) kirim(['status' => false, 'pesan' => 'Konfirmasi password belum sama']);

    $hash = password_hash($baru, PASSWORD_DEFAULT);
    query("UPDATE mahasiswa SET password='" . bersihkan($hash) . "' WHERE " . $kunciWhere);

    kirim(['status' => true, 'pesan' => 'Password berhasil diubah']);
}

/* ===================== UPLOAD FOTO PROFIL ===================== */
if (isset($_POST['unggah-foto'])) {

    // kunci identitas: id (identitas utama); fallback nip bila session id kosong
    $kunciWhere = ($nimAktif !== '') ? "nim='" . bersihkan($nimAktif) . "'" : "nim='" . bersihkan($nimAktif) . "'";

    // wajib ada file yang diunggah tanpa error
    if (empty($_FILES['foto']) || $_FILES['foto']['error'] != 0) {
        kirim(['status' => false, 'pesan' => 'Pilih file foto terlebih dahulu']);
    }

    // batasi tipe & ukuran sebelum diproses
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext     = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        kirim(['status' => false, 'pesan' => 'Format file harus JPG/PNG/GIF/WEBP']);
    }
    if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
        kirim(['status' => false, 'pesan' => 'Ukuran foto maksimal 2 MB']);
    }

    // id file lama (kolom profil = id di tabel files). Kalau sudah ada, file yang
    // sama -> upload_file() mode ganti: id tetap, nama/format/waktu/lokasi diubah,
    // file fisik lama dihapus. Kalau kosong -> upload baru + kolom profil diisi.
    $profilLama = trim((string)mysqli_fetch_assoc(query("SELECT foto FROM mahasiswa WHERE " . $kunciWhere . " LIMIT 1"))['foto'] ?? '');

    // simpan file fisik ke folder file/ root + catat/ubah tabel files.
    // Kolom profil tidak ganti-ganti id bila tinggal ganti foto.
    $id_file = upload_file($_FILES['foto'], 'mahasiswa', $profilLama);
    if ($id_file === false) {
        kirim(['status' => false, 'pesan' => 'Upload foto gagal']);
    }

    // hanya kalau belum punya foto, isi kolom profil dengan id baru
    if ($profilLama === '') {
        query("UPDATE mahasiswa SET foto='" . bersihkan($id_file) . "' WHERE " . $kunciWhere);
    }

    // sesi ikut diperbarui supaya navbar langsung tampil foto baru
    $_SESSION['profil'] = $id_file;

    kirim([
        'status' => true,
        'pesan'  => 'Foto profil berhasil diperbarui',
        'data'   => ['foto_url' => url_file($id_file)],
    ]);
}

/* ===================== HAPUS FOTO PROFIL ===================== */
if (isset($_POST['hapus-foto'])) {

    // kunci identitas: id (identitas utama); fallback nip bila session id kosong
    $kunciWhere = ($nimAktif !== '') ? "nim='" . bersihkan($nimAktif) . "'" : "nim='" . bersihkan($nimAktif) . "'";

    // ambil id file foto lama (kolom profil)
    $profilLama = trim((string)mysqli_fetch_assoc(query("SELECT foto FROM mahasiswa WHERE " . $kunciWhere . " LIMIT 1"))['foto'] ?? '');

    // kalau ada foto, hapus record & file fisik di tabel files / folder file/ root
    if ($profilLama !== '') {
        $rowFile = mysqli_fetch_assoc(query("SELECT nama_file FROM files WHERE id='" . mysqli_real_escape_string($GLOBALS['koneksi'], $profilLama) . "' LIMIT 1"));
        if ($rowFile) {
            $namaFile = trim((string)($rowFile['nama_file'] ?? ''));
            if ($namaFile !== '') {
                // naik 3 level: biodata -> pages -> mahasiswa -> root (folder "file" di root)
                $fisik = dirname(__DIR__, 3) . '/file/' . $namaFile;
                if (file_exists($fisik)) { @unlink($fisik); }
            }
        }
        query("DELETE FROM files WHERE id='" . mysqli_real_escape_string($GLOBALS['koneksi'], $profilLama) . "'");
    }

    // kosongkan kolom profil dosen
    query("UPDATE mahasiswa SET foto=NULL WHERE " . $kunciWhere);
    $_SESSION['profil'] = '';

    kirim(['status' => true, 'pesan' => 'Foto profil dihapus']);
}

/* Akses langsung tanpa aksi */
kirim(['status' => false, 'pesan' => 'Aksi tidak dikenali']);
