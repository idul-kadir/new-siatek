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
    $kunciWhere = ($idAktif !== '') ? "id='" . bersihkan($idAktif) . "'" : "nip='" . bersihkan($nipAktif) . "'";

    $ok = query(
        "UPDATE dosen SET nip='" . bersihkan($nipBaru) . "', nama='" . bersihkan($nama) . "', "
        . "nidn='" . bersihkan($nidn) . "', "
        . "jabatan_fungsional='" . bersihkan($jabfung) . "', status_kepegawaian=" . $statusSql . ", "
        . "tahun_mulai_bertugas=" . $tahunSql . ", prodi='" . bersihkan($prodi) . "', "
        . "email='" . bersihkan($email) . "', no_telp='" . bersihkan($hp) . "', keahlian='" . bersihkan($bidang) . "' "
        . "WHERE " . $kunciWhere
    );

    if (!$ok) kirim(['status' => false, 'pesan' => 'Gagal menyimpan data']);

    // nip baru langsung disimpan ke session agar identitas tetap konsisten
    $_SESSION['nip'] = $nipBaru;

    kirim([
        'status' => true,
        'pesan'  => 'Data kepegawaian & akademik tersimpan.',
        'data'   => ['nip' => $nipBaru, 'nama' => $nama, 'bidang' => array_filter(array_map('trim', explode(',', $bidang)), 'strlen')],
    ]);
}

/* ===================== UBAH PASSWORD ===================== */
if (isset($_POST['ubah-password'])) {

    // kunci identitas: id (identitas utama); fallback nip bila session id kosong
    $kunciWhere = ($idAktif !== '') ? "id='" . bersihkan($idAktif) . "'" : "nip='" . bersihkan($nipAktif) . "'";

    $rowStored = mysqli_fetch_assoc(query("SELECT password FROM dosen WHERE " . $kunciWhere . " LIMIT 1"));
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
    query("UPDATE dosen SET password='" . bersihkan($hash) . "' WHERE " . $kunciWhere);

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

    query(
        "INSERT INTO riwayat_pendidikan (nip, jenjang, program_studi, universitas, tahun) VALUES ("
        . "'" . bersihkan($nipAktif) . "', '" . bersihkan($jenjang) . "', '" . bersihkan($prodiR) . "', "
        . "'" . bersihkan($univ) . "', " . (int)$tahun . ")"
    );

    kirim(['status' => true, 'pesan' => 'Riwayat pendidikan ditambahkan']);
}

/* ===================== HAPUS RIWAYAT PENDIDIKAN ===================== */
if (isset($_GET['hapus-pendidikan'])) {

    $id = (int)$_GET['hapus-pendidikan'];
    query("DELETE FROM riwayat_pendidikan WHERE id=" . (int)$id . " AND nip='" . bersihkan($nipAktif) . "'");

    kirim(['status' => true, 'pesan' => 'Riwayat pendidikan dihapus']);
}

/* ===================== AMBIL DAFTAR PENDIDIKAN (reload konten) =====================
   HTML ditulis dengan menutup/membuka tag PHP (bukan di-echo), sesuai aturan instruksi.
   Hasilnya ditangkap lewat output buffer supaya bisa dikirim balik sebagai bagian JSON. */
if (isset($_POST['detail-pendidikan'])) {

    $resRp = query("SELECT * FROM riwayat_pendidikan WHERE nip='" . bersihkan($nipAktif) . "' ORDER BY tahun DESC, id DESC");

    ob_start(); // mulai tangkap HTML yang ditulis di bawah
    if (mysqli_num_rows($resRp) === 0) {
        ?>
        <p class="py-3 text-center text-xs italic text-slate-400">Belum ada data pendidikan — klik Tambah untuk mengisi.</p>
        <?php
    } else {
        while ($r = mysqli_fetch_assoc($resRp)) {
        ?>
        <div class="edu-row">
            <span class="e-badge"><?= tampil($r['jenjang']) ?></span>
            <div class="min-w-0 flex-1">
                <p class="e-title truncate"><?= tampil($r['program_studi']) ?></p>
                <p class="e-sub"><?= tampil($r['universitas']) ?><?= $r['tahun'] ? ' &middot; ' . (int)$r['tahun'] : '' ?></p>
            </div>
            <a href="#" data-hapus-pendidikan="<?= (int)$r['id'] ?>" class="edu-del btn-circle bg-slate-200 text-slate-500 hover:bg-rose-100 hover:text-rose-600" title="Hapus"><i class="fas fa-trash text-[10px]"></i><span class="tip">Hapus</span></a>
        </div>
        <?php
        }
    }
    $html = ob_get_clean(); // tangkap semua HTML yang ditulis di atas

    kirim(['status' => true, 'data' => $html]);
}

/* ===================== UPLOAD FOTO PROFIL ===================== */
if (isset($_POST['unggah-foto'])) {

    // kunci identitas: id (identitas utama); fallback nip bila session id kosong
    $kunciWhere = ($idAktif !== '') ? "id='" . bersihkan($idAktif) . "'" : "nip='" . bersihkan($nipAktif) . "'";

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
    $profilLama = trim((string)mysqli_fetch_assoc(query("SELECT profil FROM dosen WHERE " . $kunciWhere . " LIMIT 1"))['profil'] ?? '');

    // simpan file fisik ke folder file/ root + catat/ubah tabel files.
    // Kolom profil tidak ganti-ganti id bila tinggal ganti foto.
    $id_file = upload_file($_FILES['foto'], 'dosen', $profilLama);
    if ($id_file === false) {
        kirim(['status' => false, 'pesan' => 'Upload foto gagal']);
    }

    // hanya kalau belum punya foto, isi kolom profil dengan id baru
    if ($profilLama === '') {
        query("UPDATE dosen SET profil='" . bersihkan($id_file) . "' WHERE " . $kunciWhere);
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
    $kunciWhere = ($idAktif !== '') ? "id='" . bersihkan($idAktif) . "'" : "nip='" . bersihkan($nipAktif) . "'";

    // ambil id file foto lama (kolom profil)
    $profilLama = trim((string)mysqli_fetch_assoc(query("SELECT profil FROM dosen WHERE " . $kunciWhere . " LIMIT 1"))['profil'] ?? '');

    // kalau ada foto, hapus record & file fisik di tabel files / folder file/ root
    if ($profilLama !== '') {
        $rowFile = mysqli_fetch_assoc(query("SELECT nama_file FROM files WHERE id='" . mysqli_real_escape_string($GLOBALS['koneksi'], $profilLama) . "' LIMIT 1"));
        if ($rowFile) {
            $namaFile = trim((string)($rowFile['nama_file'] ?? ''));
            if ($namaFile !== '') {
                // naik 3 level: biodata -> pages -> administrator -> root (folder "file" di root)
                $fisik = dirname(__DIR__, 3) . '/file/' . $namaFile;
                if (file_exists($fisik)) { @unlink($fisik); }
            }
        }
        query("DELETE FROM files WHERE id='" . mysqli_real_escape_string($GLOBALS['koneksi'], $profilLama) . "'");
    }

    // kosongkan kolom profil dosen
    query("UPDATE dosen SET profil=NULL WHERE " . $kunciWhere);
    $_SESSION['profil'] = '';

    kirim(['status' => true, 'pesan' => 'Foto profil dihapus']);
}

/* Akses langsung tanpa aksi */
kirim(['status' => false, 'pesan' => 'Aksi tidak dikenali']);
