<?php
/**
 * PROSES LOGIN SIATEK - konvensi php-native-crud.
 * Semua request lewat AJAX; respons HANYA JSON {status, pesan, ...}.
 *
 * Aksi:
 *   masuk-login   -> verifikasi NIP/NIM + password (staf: dosen, mahasiswa)
 *   google-login  -> placeholder (aktif setelah kredensial OAuth diisi)
 */

require_once __DIR__ . '/../fungsi.php';

if (session_status() === PHP_SESSION_NONE) session_start();
mysqli_set_charset($GLOBALS['koneksi'], 'utf8mb4');

$BASE = '/redesain-siatek';

/* ===================== KONFIGURASI LOGIN GOOGLE =====================
 * Cara aktifkan:
 *   1. Buka https://console.cloud.google.com -> APIs & Services -> Credentials
 *   2. Create Credentials -> OAuth client ID -> Web application
 *   3. Authorized redirect URI (sama persis):
 *        http://localhost/redesain-siatek/login/proses.php      (lokal)
 *        https://siatek.web.id/login/proses.php                 (produksi)
 *   4. Isi dua konstanta di bawah ini. Selesai.
 * Login hanya diterima bila email Google terdaftar di kolom `email`
 * tabel dosen atau mahasiswa. */
define('GOOGLE_CLIENT_ID',     '116659103026-hbbc0e5g0menji91ufmc7v8fcdhebl4r.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI',  'http://localhost/redesain-siatek/login/proses.php');
/* Bila ingin membatasi domain email (mis. hanya @ung.ac.id), isi 'ung.ac.id'.
   Kosongkan ('') untuk menerima email apapun yang terdaftar. */
define('GOOGLE_ALLOWED_DOMAIN', '');

// ================= PROSES LOGIN (NIP / NIM) =================
if (isset($_POST['masuk-login'])) {

    $nip = bersihkan($_POST['nip'] ?? '');
    $pw  = (string)($_POST['password'] ?? ''); // password jangan di-escape

    if ($nip === '' || $pw === '') {
        echo json_encode(['status' => false, 'pesan' => 'NIP/NIM dan password wajib diisi']);
        exit;
    }

    // ---------- 1) Coba sebagai staf (tabel dosen) ----------
    $row = mysqli_fetch_assoc(query("SELECT * FROM dosen WHERE nip = '$nip' LIMIT 1"));

    if ($row) {
        $stored = (string)$row['password'];
        $valid  = password_verify($pw, $stored)
               || hash_equals(md5($pw), $stored)
               || hash_equals($stored, $pw);

        if (!$valid) {
            echo json_encode(['status' => false, 'pesan' => 'Password salah']);
            exit;
        }
        if (strtolower(trim((string)$row['status'])) !== 'aktif') {
            echo json_encode(['status' => false, 'pesan' => 'Akun Anda tidak aktif. Hubungi administrator']);
            exit;
        }

        // upgrade hash lama (md5/plain) -> bcrypt transparan
        if (!str_starts_with($stored, '$2y$') && !str_starts_with($stored, '$argon2')) {
            $hash = password_hash($pw, PASSWORD_DEFAULT);
            query("UPDATE dosen SET password='$hash' WHERE nip='$nip'");
        }

        session_regenerate_id(true);
        $_SESSION['user_id']    = $row['nip'];
        $_SESSION['nip']        = $row['nip'];
        $_SESSION['username']   = $row['nip'];
        $_SESSION['nama']       = $row['nama'];
        $_SESSION['tipe_login'] = $row['tipe_login'];
        $_SESSION['level']      = $row['level'];
        $_SESSION['peran']      = 'staf';
        $_SESSION['profil']     = $row['profil'] ?? '';

        echo json_encode([
            'status' => true,
            'pesan'  => 'Login berhasil. Mengalihkan...',
            'target' => $BASE . '/administrator',
        ]);
        exit;
    }

    // ---------- 2) Coba sebagai mahasiswa (tabel mahasiswa) ----------
    $m = mysqli_fetch_assoc(query("SELECT * FROM mahasiswa WHERE nim = '$nip' LIMIT 1"));

    if ($m) {
        $stored = (string)$m['password'];
        $valid  = password_verify($pw, $stored)
               || hash_equals(md5($pw), $stored)
               || hash_equals($stored, $pw);

        if (!$valid) {
            echo json_encode(['status' => false, 'pesan' => 'Password salah']);
            exit;
        }
        if (strtolower(trim((string)$m['status'])) === 'nonaktif') {
            echo json_encode(['status' => false, 'pesan' => 'Akun Anda tidak aktif. Hubungi administrator']);
            exit;
        }

        if (!str_starts_with($stored, '$2y$') && !str_starts_with($stored, '$argon2')) {
            $hash = password_hash($pw, PASSWORD_DEFAULT);
            query("UPDATE mahasiswa SET password='$hash' WHERE nim='$nip'");
        }

        session_regenerate_id(true);
        $_SESSION['user_id']  = $m['nim'];
        $_SESSION['username'] = $m['nim'];
        $_SESSION['nim']      = $m['nim'];
        $_SESSION['nama']     = $m['nama'];
        $_SESSION['prodi']    = $m['prodi'];
        $_SESSION['peran']    = 'mahasiswa';
        $_SESSION['profil']   = $m['foto'] ?? '';

        echo json_encode([
            'status' => true,
            'pesan'  => 'Login berhasil. Mengalihkan...',
            'target' => $BASE . '/mahasiswa',
        ]);
        exit;
    }

    // NIP/NIM tidak ditemukan di kedua tabel
    echo json_encode(['status' => false, 'pesan' => 'NIP/NIM tidak terdaftar']);
    exit;
}

// ================= LOGIN VIA GOOGLE =================
// Tombol di form meminta URL persetujuan Google (bila sudah dikonfigurasi).
if (isset($_POST['google-login'])) {

    if (GOOGLE_CLIENT_ID === '' || GOOGLE_CLIENT_SECRET === '') {
        echo json_encode([
            'status' => false,
            'pesan'  => 'Login via Google belum dikonfigurasi pada server ini.',
        ]);
        exit;
    }

    // token anti-CSRF disimpan di session, diverifikasi saat callback
    $state = bin2hex(random_bytes(16));
    $_SESSION['google_state'] = $state;

    $params = [
        'client_id'     => GOOGLE_CLIENT_ID,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope'         => 'openid email profile',
        'state'         => $state,
        'prompt'        => 'select_account',
    ];
    $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

    echo json_encode(['status' => true, 'pesan' => 'Mengalihkan ke Google...', 'target' => $authUrl]);
    exit;
}

// ================= CALLBACK GOOGLE =================
// Google men-redirect ke sini membawa ?code=...&state=... (atau ?error=...)
if (isset($_GET['error'])) {
    header('Location: /redesain-siatek/?err=ggagal');
    exit;
}
if (isset($_GET['code'])) {

    // state wajib sama dgn yang dikirim saat memulai (anti CSRF)
    if (!isset($_GET['state']) || empty($_SESSION['google_state'])
        || !hash_equals($_SESSION['google_state'], (string)$_GET['state'])) {
        unset($_SESSION['google_state']);
        header('Location: /redesain-siatek/?err=gstate');
        exit;
    }
    unset($_SESSION['google_state']);

    // konfigurasi belum diisi tapi callback datang -> tolak
    if (GOOGLE_CLIENT_ID === '' || GOOGLE_CLIENT_SECRET === '') {
        header('Location: /redesain-siatek/?err=gconf');
        exit;
    }

    // tukar code menjadi access token
    $token = google_http_post('https://oauth2.googleapis.com/token', [
        'code'          => $_GET['code'],
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'grant_type'    => 'authorization_code',
    ]);
    if (empty($token['access_token'])) {
        header('Location: /redesain-siatek/?err=ggagal');
        exit;
    }

    // ambil identitas akun Google
    $ch = curl_init('https://openidconnect.googleapis.com/v1/userinfo');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $token['access_token']],
    ]);
    $ui   = json_decode(curl_exec($ch), true);
    curl_close($ch);
    if (empty($ui['email']) || empty($ui['email_verified'])) {
        header('Location: /redesain-siatek/?err=gemail');
        exit;
    }

    // batasan domain bila diisi (mis. hanya @ung.ac.id)
    $domain = strtolower((string)$ui['email']);
    if (GOOGLE_ALLOWED_DOMAIN !== '' && substr(strrchr($domain, '@'), 1) !== GOOGLE_ALLOWED_DOMAIN) {
        header('Location: /redesain-siatek/?err=gdomain');
        exit;
    }

    // cari akur terdaftar: dosen dulu, lalu mahasiswa (berdasar email)
    $email = bersihkan(strtolower(trim((string)$ui['email'])));
    $row   = mysqli_fetch_assoc(query("SELECT * FROM dosen WHERE LOWER(email) = '$email' LIMIT 1"));
    $tipe  = $row ? 'staf' : null;
    if (!$row) {
        $m    = mysqli_fetch_assoc(query("SELECT * FROM mahasiswa WHERE LOWER(email) = '$email' LIMIT 1"));
        $tipe = $m ? 'mahasiswa' : null;
        $row  = $m;
    }
    if (!$row) { header('Location: /redesain-siatek/?err=gemail'); exit; }
    if ($tipe === 'staf' && strtolower(trim((string)$row['status'])) !== 'aktif') {
        header('Location: /redesain-siatek/?err=nonaktif'); exit;
    }
    if ($tipe === 'mahasiswa' && strtolower(trim((string)$row['status'])) === 'nonaktif') {
        header('Location: /redesain-siatek/?err=nonaktif'); exit;
    }

    login_sukses($row, $tipe, (string)($ui['picture'] ?? ''));
}

// Akses langsung tanpa aksi
echo json_encode(['status' => false, 'pesan' => 'Aksi tidak dikenali']);

/* ===================== FUNCTIONS (paling bawah file) ===================== */

// POST form-urlencoded ke Google, hasil JSON decode
function google_http_post($url, $fields) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($fields),
    ]);
    $res  = curl_exec($ch);
    curl_close($ch);
    return json_decode((string)$res, true) ?: [];
}

// buka session untuk akun yg lolos verifikasi (staf/mahasiswa) + simpan foto
function login_sukses($akun, $tipe, $fotoGoogle = '') {
    global $BASE;

    session_regenerate_id(true);
    if ($tipe === 'staf') {
        $_SESSION['user_id']    = $akun['id'];
        $_SESSION['nip']        = $akun['nip'];
        $_SESSION['username']   = $akun['nip'];
        $_SESSION['nama']       = $akun['nama'];
        $_SESSION['tipe_login'] = $akun['tipe_login'];
        $_SESSION['level']      = $akun['level'];
        $_SESSION['peran']      = 'staf';
        $_SESSION['profil']     = $akun['profil'] ?? '';
        $key = $akun['nip'];
        // kolom foto di tabel dosen bernama `profil` (menyimpan id file dari tabel files)
        // hanya simpan foto Google jika user belum punya foto
        if ($fotoGoogle !== '' && empty($akun['profil'])) {
            $id_file = simpan_foto_google($fotoGoogle, 'dosen');
            if ($id_file !== '') {
                query("UPDATE dosen SET profil='" . bersihkan($id_file) . "' WHERE nip='" . $key . "'");
                $_SESSION['profil'] = $id_file;
            }
        }
        $target = '/administrator';
    } else {
        $_SESSION['user_id']  = $akun['nim'];
        $_SESSION['username'] = $akun['nim'];
        $_SESSION['nim']      = $akun['nim'];
        $_SESSION['nama']     = $akun['nama'];
        $_SESSION['prodi']    = $akun['prodi'];
        $_SESSION['peran']    = 'mahasiswa';
        $_SESSION['profil']   = $akun['foto'] ?? '';
        $key = $akun['nim'];
        // kolom foto di tabel mahasiswa bernama `foto` (menyimpan id file dari tabel files)
        // hanya simpan foto Google jika user belum punya foto
        if ($fotoGoogle !== '' && empty($akun['foto'])) {
            $id_file = simpan_foto_google($fotoGoogle, 'mahasiswa');
            if ($id_file !== '') {
                query("UPDATE mahasiswa SET foto='" . bersihkan($id_file) . "' WHERE nim='" . $key . "'");
                $_SESSION['profil'] = $id_file;
            }
        }
        $target = '/mahasiswa';
    }

    header('Location: ' . $BASE . $target);
    exit;
}

// unduh foto Google lalu simpan ke folder "file" di root project + catat ke tabel "files"
// mengikuti konvensi upload file (file fisik di root, data di tabel files, entitas simpan id file).
// return: id file (untuk disimpan di kolom profil/foto entitas) atau '' bila gagal.
function simpan_foto_google($url, $tabel_asal) {
    $url = trim((string)$url);
    if ($url === '' || !str_starts_with($url, 'http')) return '';

    // unduh isi gambar
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $data = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200 || $data === false || $data === '') return '';

    // ekstensi default jpg (foto profil Google umumnya jpeg/png)
    $ekstensi = 'jpg';

    // buat id file (gabungan waktu unix + string acak, mustahil bentrok)
    $id_file   = time() . '_' . bin2hex(random_bytes(8));
    $nama_file = $id_file . '.' . $ekstensi;

    // folder "file" di root project (sejajar fungsi.php); login/ berada satu
    // tingkat di bawah root, jadi root = dirname(__DIR__)
    $folder = dirname(__DIR__) . '/file';
    if (!is_dir($folder)) @mkdir($folder, 0775, true);

    $pathFull = $folder . DIRECTORY_SEPARATOR . $nama_file;
    if (@file_put_contents($pathFull, $data) === false) return '';

    // catat metadata ke tabel files; kalau gagal, hapus file fisiknya
    if (!catat_file($id_file, $nama_file, $ekstensi, $tabel_asal)) {
        @unlink($pathFull);
        return '';
    }

    return $id_file;
}
