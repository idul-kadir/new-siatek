<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
mysqli_report(MYSQLI_REPORT_OFF);
require __DIR__ . "/../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// pastikan user sudah login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
    exit;
}

// ================= AMBIL STATISTIK KERJASAMA =================
if(isset($_POST['ambil-statistik-kerjasama'])){
    $total = (int)(mysqli_fetch_assoc(query("SELECT COUNT(*) AS jml FROM kerjasama"))['jml'] ?? 0);
    $pendidikan = (int)(mysqli_fetch_assoc(query("SELECT COUNT(*) AS jml FROM kerjasama WHERE bidang='Pendidikan'"))['jml'] ?? 0);
    $pengabdian = (int)(mysqli_fetch_assoc(query("SELECT COUNT(*) AS jml FROM kerjasama WHERE bidang='Pengabdian'"))['jml'] ?? 0);
    $penelitian = (int)(mysqli_fetch_assoc(query("SELECT COUNT(*) AS jml FROM kerjasama WHERE bidang='Penelitian'"))['jml'] ?? 0);

    echo json_encode([
        'status' => true,
        'data' => [
            'total' => $total,
            'pendidikan' => $pendidikan,
            'pengabdian' => $pengabdian,
            'penelitian' => $penelitian
        ]
    ]);
}

// ================= AMBIL DATA TABEL KERJASAMA =================
if(isset($_POST['ambil-tabel-kerjasama'])){
    $ambil = query("SELECT * FROM kerjasama ORDER BY tahun DESC, id DESC");

    ob_start();
    if(mysqli_num_rows($ambil) > 0){
        $no = 1;
        while($data = mysqli_fetch_assoc($ambil)){
            $id_ks = (int)$data['id'];
            $bukti_q = query("SELECT b.id, b.nama_bukti, b.file, f.format_file FROM bukti_kerjasama b LEFT JOIN files f ON b.file = f.id WHERE b.id_kerjasama = $id_ks ORDER BY b.id ASC");
            ?>
            <tr class="transition hover:bg-orange-50 <?= ($no % 2 == 0) ? 'bg-slate-50/60' : 'bg-white' ?>" data-id="<?= $data['id'] ?>" data-tahun="<?= $data['tahun'] ?>" data-bidang="<?= $data['bidang'] ?>" data-tingkat="<?= $data['tingkat'] ?>" data-cari="<?= strtolower($data['instansi'] . ' ' . $data['deskripsi']) ?>">
                <td class="py-4 pl-5 pr-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm"><?= $no ?></span>
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-800 leading-snug"><?= tampil($data['instansi']) ?></p>
                            <p class="text-xs text-slate-500 line-clamp-1"><?= tampil($data['deskripsi']) ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700"><?= $data['tahun'] ?></span>
                </td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold"><?= tampil($data['bidang']) ?></span>
                </td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold"><?= tampil($data['tingkat']) ?></span>
                </td>
                <td class="px-4 py-4">
                    <?php
                    $nama_bukti_list = [];
                    $bukti_ids = [];
                    ?>
                    <div class="flex flex-wrap items-center gap-1">
                        <?php
                        if($bukti_q && mysqli_num_rows($bukti_q) > 0):
                            while($b = mysqli_fetch_assoc($bukti_q)):
                                $url = url_file($b['file']);
                                $nama_bukti_list[] = $b['nama_bukti'];
                                $bukti_ids[] = (int)$b['id'];
                                if($url === '') continue;
                                $ekstensi = strtoupper($b['format_file'] ?? '');
                                ?>
                                <a href="<?= $url ?>" target="_blank" rel="noopener" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200">
                                    <i class="fas fa-download text-[10px]"></i><?= tampil($b['nama_bukti']) ?>
                                    <span class="tip"><?= $ekstensi !== '' ? $ekstensi : 'File' ?></span>
                                </a>
                            <?php endwhile; endif; ?>
                    </div>
                </td>
                <td class="py-4 pr-5">
                    <div class="flex items-center gap-1.5">
                        <button type="button" class="btn-circle btn-circle-lg bg-sky-500 text-white shadow-sm hover:bg-sky-600 btnEdit" data-bukti='<?= tampil(json_encode($nama_bukti_list)) ?>' data-bukti-ids='<?= tampil(json_encode($bukti_ids)) ?>'>
                            <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                        </button>
                        <button type="button" class="btn-circle btn-circle-lg bg-rose-500 text-white shadow-sm hover:bg-rose-600 btnHapus" data-id="<?= $data['id'] ?>">
                            <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                        </button>
                    </div>
                </td>
            </tr>
            <?php
            $no++;
        }
    } else {
        ?>
        <tr class="empty-row">
            <td colspan="6" class="py-16 text-center">
                <i class="fas fa-handshake text-4xl text-slate-300"></i>
                <p class="mt-3 font-medium text-slate-500">Tidak ada kerja sama ditemukan</p>
                <p class="text-xs text-slate-400">Klik tombol + untuk menambah kerja sama baru.</p>
            </td>
        </tr>
        <?php
    }
    $html = ob_get_clean();

    echo json_encode(['status' => true, 'data' => $html]);
}

// ================= TAMBAH DATA KERJASAMA =================
if(isset($_POST['tambah-data-kerjasama'])){
    $instansi = bersihkan($_POST['instansi']);
    $tahun = bersihkan($_POST['tahun']);
    $deskripsi = bersihkan($_POST['deskripsi']);
    $tingkat = bersihkan($_POST['tingkat']);
    $bidang = bersihkan($_POST['bidang']);

    if($instansi === '' || $tahun === '' || $deskripsi === '' || $tingkat === '' || $bidang === ''){
        echo json_encode(['status' => false, 'pesan' => 'Semua field wajib diisi']);
        exit;
    }

    // INSERT kerja sama dulu — kolom id bukan auto_increment, kita isi unix timestamp
    $simpan = query("INSERT INTO kerjasama (id, instansi, tahun, deskripsi, tingkat, bidang) VALUES (UNIX_TIMESTAMP(), '$instansi', '$tahun', '$deskripsi', '$tingkat', '$bidang')");

    if(!$simpan){
        echo json_encode(['status' => false, 'pesan' => 'Data gagal ditambahkan: ' . mysqli_error($GLOBALS['koneksi'])]);
        exit;
    }

    $id_ks = time();

    // proses dokumen bukti via upload_file() dari fungsi.php
    $ada_file = 0;

    for($i = 0; $i < 10; $i++){
        if(!isset($_FILES['file_'.$i]) || $_FILES['file_'.$i]['error'] != 0) continue;

        $level = (int)($_SESSION['level'] ?? 1);
        $cek = validasi_file($_FILES['file_'.$i], $level);
        if(!$cek['valid']){
            echo json_encode(['status' => false, 'pesan' => $cek['pesan']]);
            exit;
        }

        $id_file = upload_file($_FILES['file_'.$i], 'kerjasama');
        if($id_file === false){
            echo json_encode(['status' => false, 'pesan' => 'Upload file gagal']);
            exit;
        }

        $jenis_bukti = bersihkan($_POST['jenis_'.$i] ?? 'IA');
        $simpan_bukti = query("INSERT INTO bukti_kerjasama (id_kerjasama, nama_bukti, file) VALUES ($id_ks, '$jenis_bukti', '$id_file')");
        $ada_file++;
    }

    echo json_encode(['status' => true, 'pesan' => 'Data berhasil ditambahkan' . ($ada_file > 0 ? ' dengan '.$ada_file.' dokumen' : '')]);
}

// ================= EDIT DATA KERJASAMA =================
if(isset($_POST['edit-data-kerjasama'])){
    $id = mysqli_real_escape_string($koneksi, $_POST['id'] ?? '');
    $instansi = mysqli_real_escape_string($koneksi, $_POST['instansi'] ?? '');
    $tahun = mysqli_real_escape_string($koneksi, $_POST['tahun'] ?? '');
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi'] ?? '');
    if(!isset($_POST['tingkat']) || $_POST['tingkat'] === '') $tingkat = mysqli_real_escape_string($koneksi, '');
    else $tingkat = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
    if(!isset($_POST['bidang']) || $_POST['bidang'] === '') $bidang = mysqli_real_escape_string($koneksi, '');
    else $bidang = mysqli_real_escape_string($koneksi, $_POST['bidang']);

    if($instansi === '' || $tahun === '' || $deskripsi === '' || $tingkat === '' || $bidang === ''){
        echo json_encode(['status' => false, 'pesan' => 'Semua field wajib diisi']);
        exit;
    }

    $update = query("UPDATE kerjasama SET instansi='$instansi', tahun='$tahun', deskripsi='$deskripsi', tingkat='$tingkat', bidang='$bidang' WHERE id='$id'");

    if(!$update){
        echo json_encode(['status' => false, 'pesan' => 'Data gagal diubah: ' . mysqli_error($GLOBALS['koneksi'])]);
        exit;
    }

    // Hapus bukti yang TIDAK dikirim di form (user menghapusnya lewat tombol del)
    if(isset($_POST['id_bukti']) && is_array($_POST['id_bukti'])){
        $idArray = array_values($_POST['id_bukti']);
        $idList = array_map('intval', $idArray);
        $idList = array_filter($idList, function($v) { return $v > 0; });
        $idList = array_values($idList);
        if(count($idList) > 0){
            $idListStr = implode(',', $idList);
            query("DELETE FROM bukti_kerjasama WHERE id_kerjasama='$id' AND id NOT IN ($idListStr)");
        } else {
            query("DELETE FROM bukti_kerjasama WHERE id_kerjasama='$id'");
        }
    } else {
        query("DELETE FROM bukti_kerjasama WHERE id_kerjasama='$id'");
    }

    // Update nama_bukti untuk dokumen existing (file tidak diganti)
    if(isset($_POST['id_bukti']) && is_array($_POST['id_bukti'])){
        $idArray = array_values($_POST['id_bukti']);
        $jenisArray = isset($_POST['jenis_bukti']) ? array_values($_POST['jenis_bukti']) : [];
        for($k=0; $k<count($idArray); $k++){
            $id_bukti_bersih = (int)$idArray[$k];
            if($id_bukti_bersih <= 0) continue;
            $jenis_baru = mysqli_real_escape_string($koneksi, $jenisArray[$k] ?? 'IA');
            query("UPDATE bukti_kerjasama SET nama_bukti='$jenis_baru' WHERE id='$id_bukti_bersih' AND id_kerjasama='$id'");
        }
    }

    // proses dokumen bukti baru via upload_file() dari fungsi.php
    for($i = 0; $i < 10; $i++){
        if(!isset($_FILES['file_'.$i]) || $_FILES['file_'.$i]['error'] != 0) continue;

        $level = (int)($_SESSION['level'] ?? 1);
        $cek = validasi_file($_FILES['file_'.$i], $level);
        if(!$cek['valid']){
            echo json_encode(['status' => false, 'pesan' => $cek['pesan']]);
            exit;
        }

        $id_file = upload_file($_FILES['file_'.$i], 'kerjasama');
        if($id_file === false){
            echo json_encode(['status' => false, 'pesan' => 'Upload file gagal']);
            exit;
        }

        $jenis_bukti = bersihkan($_POST['jenis_'.$i] ?? 'IA');
        query("INSERT INTO bukti_kerjasama (id_kerjasama, nama_bukti, file) VALUES ('$id', '$jenis_bukti', '$id_file')");
    }

    echo json_encode(['status' => true, 'pesan' => 'Data berhasil diubah']);
}

// ================= UPDATE JENIS BUKTI (AJAX) =================
if(isset($_POST['update-jenis-bukti'])){
    $id_bukti = (int)($_POST['id_bukti'] ?? 0);
    $jenis_bukti = mysqli_real_escape_string($koneksi, $_POST['jenis_bukti'] ?? 'Dokumen');
    
    if($id_bukti <= 0){
        echo json_encode(['status' => false, 'pesan' => 'ID bukti tidak valid']);
        exit;
    }
    
    $update = query("UPDATE bukti_kerjasama SET nama_bukti='$jenis_bukti' WHERE id='$id_bukti'");
    
    if($update){
        echo json_encode(['status' => true, 'pesan' => 'Jenis bukti berhasil diubah']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal mengubah jenis bukti']);
    }
    exit;
}

// ================= HAPUS BUKTI KERJASAMA (PER DOKUMEN) =================
if(isset($_POST['hapus-bukti-kerjasama'])){
    $id_bukti = (int)($_POST['id_bukti'] ?? 0);
    if($id_bukti <= 0){
        echo json_encode(['status' => false, 'pesan' => 'ID bukti tidak valid']);
        exit;
    }

    // ambil data bukti (untuk hapus file fisik)
    $row = mysqli_fetch_assoc(query("SELECT b.file, b.id_kerjasama FROM bukti_kerjasama b WHERE b.id='$id_bukti'"));
    if(!$row){
        echo json_encode(['status' => false, 'pesan' => 'Bukti tidak ditemukan']);
        exit;
    }

    // hapus file fisik + record files kalau ada
    if(!empty($row['file'])){
        $id_file = bersihkan($row['file']);
        $rowFile = mysqli_fetch_assoc(query("SELECT nama_file FROM files WHERE id='$id_file'"));
        if($rowFile){
            $path = dirname(__DIR__, 4) . '/file/' . $rowFile['nama_file'];
            if(file_exists($path)){ @unlink($path); }
        }
        query("DELETE FROM files WHERE id='$id_file'");
    }

    $hapus = query("DELETE FROM bukti_kerjasama WHERE id='$id_bukti'");

    if($hapus){
        echo json_encode(['status' => true, 'pesan' => 'Dokumen berhasil dihapus']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Hapus gagal: ' . mysqli_error($GLOBALS['koneksi'])]);
    }
}

// ================= HAPUS DATA KERJASAMA =================
if(isset($_POST['hapus-data-kerjasama'])){
    $id = bersihkan($_POST['id']);

    // hapus file fisik dari tabel files yang terkait bukti
    $bukti = query("SELECT b.file FROM bukti_kerjasama b WHERE b.id_kerjasama='$id'");
    $files_to_delete = [];
    if($bukti){
        while($b = mysqli_fetch_assoc($bukti)){
            if(!empty($b['file'])) $files_to_delete[] = $b['file'];
        }
    }

    foreach($files_to_delete as $id_file){
        $rowFile = mysqli_fetch_assoc(query("SELECT nama_file FROM files WHERE id='" . bersihkan($id_file) . "'"));
        if($rowFile){
            $path = dirname(__DIR__, 4) . '/file/' . $rowFile['nama_file'];
            if(file_exists($path)){ @unlink($path); }
        }
        query("DELETE FROM files WHERE id='" . bersihkan($id_file) . "'");
    }

    query("DELETE FROM bukti_kerjasama WHERE id_kerjasama='$id'");

    $hapus = query("DELETE FROM kerjasama WHERE id='$id'");

    if($hapus){
        echo json_encode(['status' => true, 'pesan' => 'Data berhasil dihapus']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data gagal dihapus: ' . mysqli_error($GLOBALS['koneksi'])]);
    }
}
?>
