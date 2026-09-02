<?php
/*
    proses.php - Halaman Dokumen Akreditasi Jurusan
    ---------------------------------------------
    Semua proses tambah/ubah/hapus/ambil-tabel untuk halaman dokumen akreditasi.
    File ini dipanggil lewat AJAX (jQuery), BUKAN submit form biasa.
    Karena itu proses.php TIDAK PERNAH echo HTML/JS. Satu-satunya yang boleh
    di-echo adalah JSON lewat json_encode(). Urusan alert/reload/tampilan
    dikerjakan di JS pemanggil lewat success: callback.
    ---------------------------------------------
*/
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Debug log: log ke error_log untuk debugging AJAX
function debug_log($msg) {
    if (function_exists('error_log')) {
        error_log('[akreditasi-proses] ' . $msg);
    }
}
require __DIR__ . "/../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// pastikan user sudah login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
    exit;
}

// ================= AMBIL TABEL DOKUMEN AKREDITASI =================
// render ulang seluruh section accordion (9 kriteria + baris dokumen per kriteria),
// hasilnya dikirim sebagai HTML di dalam JSON untuk disuntikkan ke container accordion
if(isset($_POST['ambil-tabel-akreditasi'])){
    echo json_encode(['status' => true, 'data' => render_accordion_akreditasi()]);
}

// ================= TAMBAH DATA DOKUMEN AKREDITASI =================
if(isset($_POST['tambah-data-akreditasi'])){
    $nama     = bersihkan($_POST['nama'] ?? '');
    $kategori = (int) bersihkan($_POST['kategori'] ?? '');
    $jenis    = bersihkan($_POST['jenis'] ?? '');
    $tahun    = bersihkan($_POST['tahun'] ?? '');

    // validasi input wajib
    if($nama === '' || $kategori <= 0 || $jenis === '' || !preg_match('/^\d{4}$/', $tahun)){
        echo json_encode(['status' => false, 'pesan' => 'Nama, kriteria, jenis, dan tahun wajib diisi dengan benar']);
        exit;
    }

    // id pengunggah diambil dari session (FK ke tabel dosen), BUKAN nama
    $id_user = bersihkan($_SESSION['id'] ?? '');
    if($id_user === ''){
        echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
        exit;
    }

    // upload file dokumen (jika user mengunggah file)
    $id_file = null;
    if(isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0){
        // validasi format dan ukuran file sesuai level user
        $level = (int)($_SESSION['level'] ?? 1);
        $cek = validasi_file($_FILES['file_upload'], $level);
        if(!$cek['valid']){
            echo json_encode(['status' => false, 'pesan' => $cek['pesan']]);
            exit;
        }

        $id_file = upload_file($_FILES['file_upload'], 'dokumen_akreditas');
        if($id_file === false){
            echo json_encode(['status' => false, 'pesan' => 'Upload file gagal']);
            exit;
        }
    }

    // id baru = MAX(id) + 1 (kolom id tabel ini tidak auto_increment)
    $rowId = mysqli_fetch_assoc(query("SELECT IFNULL(MAX(id), 0) + 1 AS nid FROM dokumen_akreditas"));
    $id_baru = (int)($rowId['nid'] ?? 1);

    // simpan ke tabel dokumen_akreditas: kolom lokasi HANYA berisi id file (bukan nama/path),
    // kolom pengupload berisi id user dari session, kolom keterangan diisi string kosong (NOT NULL)
    $simpan = query("INSERT INTO dokumen_akreditas (id, kategori, jenis, nama, keterangan, tahun, lokasi, pengupload) VALUES ($id_baru, $kategori, '$jenis', '$nama', '', '$tahun', " . ($id_file !== null ? "'$id_file'" : "NULL") . ", '$id_user')");

    if($simpan){
        echo json_encode(['status' => true, 'pesan' => 'Dokumen berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Dokumen gagal ditambahkan']);
    }
}

// ================= EDIT DATA DOKUMEN AKREDITASI =================
if(isset($_POST['edit-data-akreditasi'])){
    $id       = bersihkan($_POST['id'] ?? '');
    $nama     = bersihkan($_POST['nama'] ?? '');
    $kategori = (int) bersihkan($_POST['kategori'] ?? '');
    $jenis    = bersihkan($_POST['jenis'] ?? '');
    $tahun    = bersihkan($_POST['tahun'] ?? '');
    $fileLama = bersihkan($_POST['file_lama'] ?? '');

    // cek kepemilikan / hak akses: pengupload sendiri ATAU level >= 5
    $row = mysqli_fetch_assoc(query("SELECT pengupload FROM dokumen_akreditas WHERE id='$id' LIMIT 1"));
    if(!$row){
        echo json_encode(['status' => false, 'pesan' => 'Dokumen tidak ditemukan']);
        exit;
    }
    $levelLogin = (int)($_SESSION['level'] ?? 1);
    $idLogin    = bersihkan($_SESSION['id'] ?? '');
    if($levelLogin < 5 && (string)$row['pengupload'] !== $idLogin){
        echo json_encode(['status' => false, 'pesan' => 'Anda tidak berhak mengedit dokumen ini']);
        exit;
    }

    // validasi input wajib
    if($id === '' || $nama === '' || $kategori <= 0 || $jenis === '' || !preg_match('/^\d{4}$/', $tahun)){
        echo json_encode(['status' => false, 'pesan' => 'Nama, kriteria, jenis, dan tahun wajib diisi dengan benar']);
        exit;
    }

    // file lama dipakai sebagai referensi id; jika ada file baru diunggah,
    // upload_file() mode ganti: file fisik lama dihapus, id tetap dipakai
    $id_file = $fileLama;
    if(isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0){
        // validasi format dan ukuran file sesuai level user
        $level = (int)($_SESSION['level'] ?? 1);
        $cek = validasi_file($_FILES['file_upload'], $level);
        if(!$cek['valid']){
            echo json_encode(['status' => false, 'pesan' => $cek['pesan']]);
            exit;
        }

        $id_file_baru = upload_file($_FILES['file_upload'], 'dokumen_akreditas', $fileLama);
        if($id_file_baru === false){
            echo json_encode(['status' => false, 'pesan' => 'Upload file gagal']);
            exit;
        }
        $id_file = $id_file_baru;
    }

    $update = query("UPDATE dokumen_akreditas SET kategori=$kategori, jenis='$jenis', nama='$nama', tahun='$tahun', lokasi=" . ($id_file !== '' ? "'$id_file'" : "NULL") . " WHERE id='$id'");

    if($update){
        echo json_encode(['status' => true, 'pesan' => 'Dokumen berhasil diubah']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Dokumen gagal diubah']);
    }
}

// ================= HAPUS DATA DOKUMEN AKREDITASI =================
if(isset($_POST['hapus-data-akreditasi'])){
    $id = bersihkan($_POST['id'] ?? '');

    // cek kepemilikan / hak akses: pengupload sendiri ATAU level >= 5
    $row = mysqli_fetch_assoc(query("SELECT pengupload FROM dokumen_akreditas WHERE id='$id' LIMIT 1"));
    if(!$row){
        echo json_encode(['status' => false, 'pesan' => 'Dokumen tidak ditemukan']);
        exit;
    }
    $levelLogin = (int)($_SESSION['level'] ?? 1);
    $idLogin    = bersihkan($_SESSION['id'] ?? '');
    if($levelLogin < 5 && (string)$row['pengupload'] !== $idLogin){
        echo json_encode(['status' => false, 'pesan' => 'Anda tidak berhak menghapus dokumen ini']);
        exit;
    }

    // hapus file fisik + record tabel files yang terkait (jika dokumen punya file)
    $row = mysqli_fetch_assoc(query("SELECT lokasi FROM dokumen_akreditas WHERE id='$id'"));
    if($row && !empty($row['lokasi'])){
        $rowFile = mysqli_fetch_assoc(query("SELECT nama_file FROM files WHERE id='" . bersihkan($row['lokasi']) . "'"));
        if($rowFile){
            $path = dirname(__DIR__, 4) . '/file/' . $rowFile['nama_file'];
            if(file_exists($path)){ @unlink($path); }
        }
        query("DELETE FROM files WHERE id='" . bersihkan($row['lokasi']) . "'");
    }

    $hapus = query("DELETE FROM dokumen_akreditas WHERE id='$id'");

    if($hapus){
        echo json_encode(['status' => true, 'pesan' => 'Dokumen berhasil dihapus']);
    } else {
        $err = mysqli_error($GLOBALS['koneksi']);
        echo json_encode(['status' => false, 'pesan' => 'Dokumen gagal dihapus: ' . $err]);
    }
}

// ================= FUNCTION KHUSUS HALAMAN INI =================

// metadata tampilan tiap kriteria (ikon, warna, subjudul) - mengikuti desain halaman
function kriteria_meta_akreditasi(){
    return [
        1 => ['ikon' => 'fa-bullseye',      'warna' => 'orange',  'sub' => 'Dokumen visi misi, renstra, dan strategi pencapaian'],
        2 => ['ikon' => 'fa-sitemap',       'warna' => 'sky',     'sub' => 'Organisasi, SOP, dan dokumen kerjasama'],
        3 => ['ikon' => 'fa-user-graduate', 'warna' => 'emerald', 'sub' => 'Statistik, penerimaan, beasiswa, dan layanan mahasiswa'],
        4 => ['ikon' => 'fa-users',         'warna' => 'violet',  'sub' => 'Dosen dan tenaga kependidikan'],
        5 => ['ikon' => 'fa-coins',         'warna' => 'indigo',  'sub' => 'Pengelolaan keuangan serta sarana prasarana'],
        6 => ['ikon' => 'fa-book-open',     'warna' => 'amber',   'sub' => 'Kurikulum, pembelajaran, dan penilaian'],
        7 => ['ikon' => 'fa-flask',         'warna' => 'rose',    'sub' => 'Penelitian dosen dan luaran penelitian'],
        8 => ['ikon' => 'fa-hands-helping', 'warna' => 'teal',    'sub' => 'Pengabdian dosen dan luaran pengabdian'],
        9 => ['ikon' => 'fa-trophy',        'warna' => 'lime',    'sub' => 'Capaian kinerja tridharma dan mutu lulusan'],
    ];
}

// kelas css ikon file berdasarkan format file (return [kelas-warna, nama-ikon])
function kelas_ikon_file_akreditasi($format){
    $format = strtolower(trim((string)$format));
    if(in_array($format, ['doc', 'docx'])) return ['file-word', 'fa-file-word'];
    if(in_array($format, ['xls', 'xlsx', 'csv'])) return ['file-excel', 'fa-file-excel'];
    if(in_array($format, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'])) return ['file-img', 'fa-file-image'];
    return ['file-pdf', 'fa-file-pdf'];
}

// kelas css badge jenis dokumen
function kelas_jenis_akreditasi($jenis){
    if($jenis === 'SK') return 'jenis-sk';
    if($jenis === 'Dokumen') return 'jenis-dokumen';
    if($jenis === 'Berita Acara') return 'jenis-berita';
    return 'jenis-lainnya';
}

// render seluruh section accordion (dipakai blok ambil-tabel;
// file tampilan me-render ulang dengan loop yang sama untuk tampilan awal)
function render_accordion_akreditasi(){
    $meta = kriteria_meta_akreditasi();

    // ambil daftar kriteria
    $kategoriList = [];
    $q = query("SELECT * FROM `kategori-akreditas` ORDER BY no ASC");
    while($k = mysqli_fetch_assoc($q)){ $kategoriList[] = $k; }

    // ambil dokumen dikelompokkan per kategori (join files untuk data file,
    // join dosen untuk nama pengunggah dari id yang tersimpan di kolom pengupload)
    $perKat = [];
    $q = query("SELECT d.*, f.format_file, f.nama_file AS nama_file_server, u.nama AS nama_user FROM dokumen_akreditas d LEFT JOIN files f ON d.lokasi = f.id LEFT JOIN dosen u ON d.pengupload = u.id ORDER BY d.kategori ASC, d.tahun DESC, d.id DESC");
    while($d = mysqli_fetch_assoc($q)){ $perKat[(int)$d['kategori']][] = $d; }

    ob_start();
    $idx = 0;

    // hak akses edit/hapus dihitung di sini (dipakai render ulang lewat AJAX)
    $levelLogin = (int)($_SESSION['level'] ?? 1);
    $idLogin    = (string)($_SESSION['id'] ?? '');

    foreach($kategoriList as $kat){
        $no    = (int)$kat['no'];
        $cat   = 'c' . $no;
        $m     = $meta[$no] ?? ['ikon' => 'fa-folder', 'warna' => 'slate', 'sub' => ''];
        $rows  = $perKat[$no] ?? [];
        $open  = ($idx === 0) ? ' open' : '';
        $delay = ($idx > 0) ? ' style="animation-delay:.' . sprintf('%02d', $idx) . 's"' : '';
        ?>
        <div class="acc-item acc-c<?= $no; ?> reveal<?= $open; ?>" data-cat="<?= $cat; ?>"<?= $delay; ?>>
            <button type="button" class="acc-head" aria-expanded="<?= $idx === 0 ? 'true' : 'false'; ?>">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-<?= $m['warna']; ?>-100 text-lg text-<?= $m['warna']; ?>-600"><i class="fas <?= $m['ikon']; ?>"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">Kriteria <?= $no; ?> - <?= tampil($kat['keterangan']); ?></span>
                    <span class="block text-xs text-slate-500"><?= tampil($m['sub']); ?></span>
                </span>
                <span class="acc-badge" id="cnt-<?= $cat; ?>"><?= count($rows); ?></span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <?php foreach($rows as $data){
                            $url = url_file($data['lokasi'] ?? '');
                            list($kelasIkon, $ikon) = kelas_ikon_file_akreditasi($data['format_file'] ?? '');
                            $kelasJenis = kelas_jenis_akreditasi($data['jenis']);
                            $namaUpload = trim((string)($data['nama_user'] ?? $data['pengupload'] ?? ''));
                            $cari = strtolower($data['nama'] . ' ' . $data['jenis'] . ' ' . $kat['keterangan'] . ' ' . $namaUpload);
                            // hak edit/hapus: pengupload sendiri ATAU level >= 5
                            $bolehKelola = $levelLogin >= 5 || (string)$data['pengupload'] === $idLogin;
                        ?>
                        <div class="dok-row" data-id="<?= $data['id']; ?>" data-tahun="<?= $data['tahun']; ?>" data-cari="<?= tampil($cari); ?>" data-file="<?= tampil($data['lokasi'] ?? ''); ?>" data-namafile="<?= tampil($data['nama_file_server'] ?? ''); ?>" data-kategori="<?= $cat; ?>" data-jenis="<?= tampil($data['jenis']); ?>" data-pengupload="<?= tampil($namaUpload); ?>" data-ptext="<?= tampil($data['nama']); ?>">
                            <span class="file-ico <?= $kelasIkon; ?>"><i class="fas <?= $ikon; ?>"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800"><?= tampil($data['nama']); ?></p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge <?= $kelasJenis; ?>"><?= tampil($data['jenis']); ?></span>
                                    <?php if($namaUpload !== ''): ?>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600"><?= tampil($namaUpload); ?></span>
                                    <?php endif; ?>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700"><?= $data['tahun']; ?></span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5">
                                    <?php if($url !== ''): ?>
                                    <a href="<?= $url; ?>" target="_blank" rel="noopener" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a>
                                    <?php endif; ?>
                                    <?php if($bolehKelola): ?>
                                    <a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                                    <a href="#" class="btnHapusAkreditasi btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600" data-id="<?= $data['id']; ?>"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a>
                                    <?php else: ?>
                                    <span class="btn-circle bg-slate-300 text-slate-500 opacity-50 cursor-not-allowed"><i class="fas fa-pen text-xs"></i><span class="tip">Tidak berhak</span></span>
                                    <span class="btn-circle bg-slate-300 text-slate-500 opacity-50 cursor-not-allowed"><i class="fas fa-trash text-xs"></i><span class="tip">Tidak berhak</span></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if(count($rows) === 0){ ?>
                        <p class="acc-empty"><i class="fas fa-inbox"></i>Belum ada dokumen pada kriteria ini. Klik tombol <b>+</b> di kanan atas untuk mengunggah.</p>
                        <?php } else { ?>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen yang cocok.</p>
                        <?php } ?>
                        <div class="acc-pager flex items-center justify-between border-t border-slate-100 pt-3<?= count($rows) === 0 ? ' hidden' : ''; ?>">
                            <button type="button" class="pg-btn pg-prev"><i class="fas fa-chevron-left text-xs"></i></button>
                            <p class="pg-label text-xs font-semibold text-slate-500">Halaman 1 / 1</p>
                            <button type="button" class="pg-btn pg-next"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $idx++;
    }
    return ob_get_clean();
}
?>