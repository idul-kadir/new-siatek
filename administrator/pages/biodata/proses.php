<?php
/**
 * PROSES BIODATA — semua aksi simpan halaman Biodata ditangani di sini.
 * Dipanggil dari pages/biodata/biodata.php (form method="post").
 *
 * Aksi:
 *   POST ubah-biodata       -> update profil dosen/pegawai + bidang ilmu (keahlian)
 *   POST ubah-password      -> verifikasi lama lalu ganti password (password_hash)
 *   POST tambah-pendidikan  -> insert riwayat pendidikan
 *   GET  hapus-pendidikan   -> delete riwayat pendidikan milik sendiri
 *
 * Semua query memakai prepared statement (lebih aman daripada escaping manual).
 */
require __DIR__ . "/../../fungsi.php";

if (session_status() === PHP_SESSION_NONE) session_start();
mysqli_set_charset($GLOBALS['koneksi'], 'utf8mb4');

/* NIP pengguna aktif: session login -> fallback admin/laboran */
$nipAktif = $_SESSION['nip'] ?? $_SESSION['username'] ?? '10041993';
if (isset($_GET['nip']) && preg_match('/^[0-9]{6,25}$/', (string)$_GET['nip'])) {
    $nipAktif = $_GET['nip']; // mode lihat biodata orang lain (opsional)
}

function prg(array $p) {
    header('Location: /redesain-siatek/administrator/biodata?' . http_build_query($p));
    exit;
}

/* ===================== UBAH BIODATA ===================== */
if (isset($_POST['ubah-biodata'])) {

    $nama = trim((string)($_POST['nama'] ?? ''));
    if ($nama === '') prg(['err' => 'nama']);

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

    $stmt = mysqli_prepare(
        $GLOBALS['koneksi'],
        'UPDATE dosen SET nama=?, nidn=?, jabatan_fungsional=?, status_kepegawaian=?,
         tahun_mulai_bertugas=?, prodi=?, email=?, no_telp=?, keahlian=? WHERE nip=?'
    );
    mysqli_stmt_bind_param($stmt, 'ssssisssss', $nama, $nidn, $jabfung, $status, $tahunN, $prodi, $email, $hp, $bidang, $nipAktif);
    mysqli_stmt_execute($stmt);

    prg(['ok' => 'akademik']);
}

/* ===================== UBAH PASSWORD ===================== */
if (isset($_POST['ubah-password'])) {

    $q = mysqli_prepare($GLOBALS['koneksi'], 'SELECT password FROM dosen WHERE nip=? LIMIT 1');
    mysqli_stmt_bind_param($q, 's', $nipAktif);
    mysqli_stmt_execute($q);
    $stored = (string)(mysqli_fetch_row(mysqli_stmt_get_result($q))[0] ?? '');

    $lama = (string)($_POST['pw_lama'] ?? '');
    $baru = (string)($_POST['pw_baru'] ?? '');
    $konf = (string)($_POST['pw_konfirmasi'] ?? '');

    /* dukung data lama: bcrypt modern, plain, atau md5 */
    $validLama = $lama !== '' && (
        password_verify($lama, $stored) ||
        hash_equals($stored, md5($lama)) ||
        hash_equals($stored, $lama)
    );
    if (!$validLama) prg(['err' => 'pwlama']);
    if (strlen($baru) < 8) prg(['err' => 'pwpndek']);
    if (!preg_match('/[A-Z]/', $baru) || !preg_match('/[a-z]/', $baru) || !preg_match('/[0-9]/', $baru))
        prg(['err' => 'pwlemah']);
    if ($baru !== $konf) prg(['err' => 'pwbeda']);

    $hash = password_hash($baru, PASSWORD_DEFAULT);
    $u = mysqli_prepare($GLOBALS['koneksi'], 'UPDATE dosen SET password=? WHERE nip=?');
    mysqli_stmt_bind_param($u, 'ss', $hash, $nipAktif);
    mysqli_stmt_execute($u);

    prg(['ok' => 'password']);
}

/* ===================== TAMBAH RIWAYAT PENDIDIKAN ===================== */
if (isset($_POST['tambah-pendidikan'])) {

    $jenjang = (string)($_POST['jenjang'] ?? '');
    $prodiR  = trim((string)($_POST['prodi_rp'] ?? ''));
    $univ    = trim((string)($_POST['univ'] ?? ''));
    $tahun   = (int)($_POST['tahun_rp'] ?? 0);
    $valid   = ['TK','SD','SMP','SMA/SMK','D1','D2','D3','D4','S1','S2','S3'];

    if (!in_array($jenjang, $valid, true) || $prodiR === '' || $univ === '' || $tahun < 1950 || $tahun > 2100)
        prg(['err' => 'rp']);

    $s = mysqli_prepare(
        $GLOBALS['koneksi'],
        'INSERT INTO riwayat_pendidikan (nip, jenjang, program_studi, universitas, tahun) VALUES (?,?,?,?,?)'
    );
    mysqli_stmt_bind_param($s, 'ssssi', $nipAktif, $jenjang, $prodiR, $univ, $tahun);
    mysqli_stmt_execute($s);

    prg(['ok' => 'rp_add']);
}

/* ===================== HAPUS RIWAYAT PENDIDIKAN ===================== */
if (isset($_GET['hapus-pendidikan'])) {

    $id = (int)$_GET['hapus-pendidikan'];
    $d  = mysqli_prepare($GLOBALS['koneksi'], 'DELETE FROM riwayat_pendidikan WHERE id=? AND nip=?');
    mysqli_stmt_bind_param($d, 'is', $id, $nipAktif);
    mysqli_stmt_execute($d);

    prg(['ok' => 'rp_del']);
}

/* Akses langsung tanpa aksi: kembali ke halaman biodata */
prg([]);
