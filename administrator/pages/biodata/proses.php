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

/* NIP pengguna aktif: session login -> fallback admin/laboran */
$nipAktif = $_SESSION['nip'] ?? $_SESSION['username'] ?? '10041993';

function kirim(array $p) {
    echo json_encode($p);
    exit;
}

/* ===================== UBAH BIODATA ===================== */
if (isset($_POST['ubah-biodata'])) {

    $nama = trim((string)($_POST['nama'] ?? ''));
    if ($nama === '') kirim(['status' => false, 'pesan' => 'Nama lengkap wajib diisi']);

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
    $ok = mysqli_stmt_execute($stmt);

    if (!$ok) kirim(['status' => false, 'pesan' => 'Gagal menyimpan data']);

    kirim([
        'status' => true,
        'pesan'  => 'Data kepegawaian & akademik tersimpan.',
        'data'   => ['nama' => $nama, 'bidang' => array_filter(array_map('trim', explode(',', $bidang)), 'strlen')],
    ]);
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
    if (!$validLama) kirim(['status' => false, 'pesan' => 'Password saat ini salah']);
    if (strlen($baru) < 8) kirim(['status' => false, 'pesan' => 'Password baru minimal 8 karakter']);
    if (!preg_match('/[A-Z]/', $baru) || !preg_match('/[a-z]/', $baru) || !preg_match('/[0-9]/', $baru))
        kirim(['status' => false, 'pesan' => 'Password harus memuat huruf besar, kecil, dan angka']);
    if ($baru !== $konf) kirim(['status' => false, 'pesan' => 'Konfirmasi password belum sama']);

    $hash = password_hash($baru, PASSWORD_DEFAULT);
    $u = mysqli_prepare($GLOBALS['koneksi'], 'UPDATE dosen SET password=? WHERE nip=?');
    mysqli_stmt_bind_param($u, 'ss', $hash, $nipAktif);
    mysqli_stmt_execute($u);

    kirim(['status' => true, 'pesan' => 'Password berhasil diubah']);
}

/* ===================== TAMBAH RIWAYAT PENDIDIKAN ===================== */
if (isset($_POST['tambah-pendidikan'])) {

    $jenjang = (string)($_POST['jenjang'] ?? '');
    $prodiR  = trim((string)($_POST['prodi_rp'] ?? ''));
    $univ    = trim((string)($_POST['univ'] ?? ''));
    $tahun   = (int)($_POST['tahun_rp'] ?? 0);
    $valid   = ['TK','SD','SMP','SMA/SMK','D1','D2','D3','D4','S1','S2','S3'];

    if (!in_array($jenjang, $valid, true) || $prodiR === '' || $univ === '' || $tahun < 1950 || $tahun > 2100)
        kirim(['status' => false, 'pesan' => 'Data riwayat pendidikan belum lengkap/valid']);

    $s = mysqli_prepare(
        $GLOBALS['koneksi'],
        'INSERT INTO riwayat_pendidikan (nip, jenjang, program_studi, universitas, tahun) VALUES (?,?,?,?,?)'
    );
    mysqli_stmt_bind_param($s, 'ssssi', $nipAktif, $jenjang, $prodiR, $univ, $tahun);
    mysqli_stmt_execute($s);

    kirim(['status' => true, 'pesan' => 'Riwayat pendidikan ditambahkan']);
}

/* ===================== HAPUS RIWAYAT PENDIDIKAN ===================== */
if (isset($_GET['hapus-pendidikan'])) {

    $id = (int)$_GET['hapus-pendidikan'];
    $d  = mysqli_prepare($GLOBALS['koneksi'], 'DELETE FROM riwayat_pendidikan WHERE id=? AND nip=?');
    mysqli_stmt_bind_param($d, 'is', $id, $nipAktif);
    mysqli_stmt_execute($d);

    kirim(['status' => true, 'pesan' => 'Riwayat pendidikan dihapus']);
}

/* ===================== AMBIL DAFTAR PENDIDIKAN (reload konten) ===================== */
if (isset($_POST['detail-pendidikan'])) {

    $stmt = mysqli_prepare($GLOBALS['koneksi'], 'SELECT * FROM riwayat_pendidikan WHERE nip=? ORDER BY tahun DESC, id DESC');
    mysqli_stmt_bind_param($stmt, 's', $nipAktif);
    mysqli_stmt_execute($stmt);
    $resRp = mysqli_stmt_get_result($stmt);

    $html = '';
    if (mysqli_num_rows($resRp) === 0) {
        $html = '<p class="py-3 text-center text-xs italic text-slate-400">Belum ada data pendidikan — klik Tambah untuk mengisi.</p>';
    } else {
        while ($r = mysqli_fetch_assoc($resRp)) {
            $html .= '<div class="edu-row">'
                . '<span class="e-badge">' . tampil($r['jenjang']) . '</span>'
                . '<div class="min-w-0 flex-1"><p class="e-title truncate">' . tampil($r['program_studi']) . '</p>'
                . '<p class="e-sub">' . tampil($r['universitas']) . ($r['tahun'] ? ' &middot; ' . (int)$r['tahun'] : '') . '</p></div>'
                . '<a href="#" data-hapus-pendidikan="' . (int)$r['id'] . '" class="edu-del btn-circle bg-slate-200 text-slate-500 hover:bg-rose-100 hover:text-rose-600" title="Hapus"><i class="fas fa-trash text-[10px]"></i><span class="tip">Hapus</span></a>'
                . '</div>';
        }
    }

    kirim(['status' => true, 'data' => $html]);
}

/* Akses langsung tanpa aksi */
kirim(['status' => false, 'pesan' => 'Aksi tidak dikenali']);
