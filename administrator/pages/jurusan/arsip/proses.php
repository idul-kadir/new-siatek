<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
require __DIR__ . "/../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// pastikan user sudah login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
    exit;
}

// ================= AMBIL DATA TABEL ARSIP JURUSAN =================
if(isset($_POST['ambil-tabel-arsip-jurusan'])){
    $ambil = query("SELECT a.*, d.nama AS nama_user FROM arsip_jurusan a LEFT JOIN dosen d ON a.pengupload = d.id ORDER BY a.tahun DESC, a.id DESC");

    ob_start();
    if(mysqli_num_rows($ambil) > 0){
        $no = 1;
        while($data = mysqli_fetch_assoc($ambil)){
            $url = url_file($data['file']);
            $ekstensi = '';
            if($data['file'] !== ''){
                $rowFile = mysqli_fetch_assoc(query("SELECT format_file FROM files WHERE id='" . bersihkan($data['file']) . "' LIMIT 1"));
                $ekstensi = strtoupper($rowFile['format_file'] ?? '');
            }
            ?>
            <tr class="transition hover:bg-orange-50 <?= ($no % 2 == 0) ? 'bg-slate-50/60' : 'bg-white' ?>" data-id="<?= $data['id'] ?>" data-tahun="<?= $data['tahun'] ?>" data-nama="<?= strtolower($data['nama']) ?>" data-ptext="<?= tampil($data['nama']) ?>" data-pengupload="<?= strtolower($data['nama_user'] ?? $data['pengupload']) ?>" data-file="<?= $data['file'] ?>">
                <td class="py-4 pl-5 pr-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm"><?= $no ?></span>
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-800 leading-snug"><?= tampil($data['nama']) ?></p>
                            <?php if($ekstensi !== ''): ?>
                            <span class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold <?= in_array($ekstensi, ['PDF']) ? 'bg-rose-500 text-white' : 'bg-emerald-600 text-white' ?>"><i class="fas fa-file-export"></i><?= $ekstensi ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700"><?= $data['tahun'] ?></span>
                </td>
                <td class="px-4 py-4 text-slate-600"><?= tampil($data['nama_user'] ?? $data['pengupload']) ?></td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-1.5">
                        <?php if($url !== ''): ?>
                        <a href="<?= $url ?>" target="_blank" rel="noopener" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                            <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
                        </a>
                        <?php endif; ?>
                        <button type="button" class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                            <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                        </button>
                        <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="<?= $data['id'] ?>">
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
            <td colspan="4" class="py-16 text-center">
                <i class="fas fa-archive text-4xl text-slate-300"></i>
                <p class="mt-3 font-medium text-slate-500">Tidak ada arsip ditemukan</p>
                <p class="text-xs text-slate-400">Klik tombol + untuk menambah arsip baru.</p>
            </td>
        </tr>
        <?php
    }
    $html = ob_get_clean();

    echo json_encode(['status' => true, 'data' => $html]);
}

// ================= AMBIL STATISTIK =================
if(isset($_POST['ambil-statistik-arsip'])){
    $total = mysqli_fetch_assoc(query("SELECT COUNT(*) AS jml FROM arsip_jurusan"))['jml'] ?? 0;
    $tahunMin = mysqli_fetch_assoc(query("SELECT MIN(tahun) AS thn FROM arsip_jurusan"))['thn'] ?? '-';
    $tahunMax = mysqli_fetch_assoc(query("SELECT MAX(tahun) AS thn FROM arsip_jurusan"))['thn'] ?? '-';
    $penguploadTerbanyak = mysqli_fetch_assoc(query("SELECT a.pengupload, d.nama AS nama_user, COUNT(*) AS jml FROM arsip_jurusan a LEFT JOIN dosen d ON a.pengupload = d.id GROUP BY a.pengupload, d.nama ORDER BY jml DESC LIMIT 1"));
    $namaUpload = $penguploadTerbanyak['nama_user'] ?? $penguploadTerbanyak['pengupload'] ?? '-';
    $jmlUpload = $penguploadTerbanyak['jml'] ?? 0;

    echo json_encode([
        'status' => true,
        'data' => [
            'total' => (int)$total,
            'tahun_min' => $tahunMin,
            'tahun_max' => $tahunMax,
            'pengupload_nama' => $namaUpload,
            'pengupload_jml' => (int)$jmlUpload
        ]
    ]);
}

// ================= TAMBAH DATA ARSIP JURUSAN =================
if(isset($_POST['tambah-data-arsip-jurusan'])){
    $nama = bersihkan($_POST['nama']);
    $tahun = bersihkan($_POST['tahun']);
    $id_user = bersihkan($_SESSION['id'] ?? '');

    if($nama === '' || $tahun === ''){
        echo json_encode(['status' => false, 'pesan' => 'Nama dokumen dan tahun wajib diisi']);
        exit;
    }

    if($id_user === ''){
        echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
        exit;
    }

    // upload file lampiran
    $id_file = null;
    if(isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0){
        // validasi format dan ukuran file
        $level = (int)($_SESSION['level'] ?? 1);
        $cek = validasi_file($_FILES['file_upload'], $level);
        if(!$cek['valid']){
            echo json_encode(['status' => false, 'pesan' => $cek['pesan']]);
            exit;
        }

        $id_file = upload_file($_FILES['file_upload'], 'arsip_jurusan');
        if($id_file === false){
            echo json_encode(['status' => false, 'pesan' => 'Upload file gagal']);
            exit;
        }
    }

    $simpan = query("INSERT INTO arsip_jurusan (nama, tahun, file, pengupload) VALUES ('$nama', '$tahun', " . ($id_file !== null ? "'$id_file'" : "NULL") . ", '$id_user')");

    if($simpan){
        echo json_encode(['status' => true, 'pesan' => 'Data berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data gagal ditambahkan']);
    }
}

// ================= EDIT DATA ARSIP JURUSAN =================
if(isset($_POST['edit-data-arsip-jurusan'])){
    $id = bersihkan($_POST['id']);
    $nama = bersihkan($_POST['nama']);
    $tahun = bersihkan($_POST['tahun']);

    if($nama === '' || $tahun === ''){
        echo json_encode(['status' => false, 'pesan' => 'Nama dokumen dan tahun wajib diisi']);
        exit;
    }

    // ambil file lama
    $fileLama = $_POST['file_lama'] ?? '';
    $id_file = $fileLama;

    // upload file baru jika ada
    if(isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0){
        // validasi format dan ukuran file
        $level = (int)($_SESSION['level'] ?? 1);
        $cek = validasi_file($_FILES['file_upload'], $level);
        if(!$cek['valid']){
            echo json_encode(['status' => false, 'pesan' => $cek['pesan']]);
            exit;
        }

        $id_file_baru = upload_file($_FILES['file_upload'], 'arsip_jurusan', $fileLama);
        if($id_file_baru === false){
            echo json_encode(['status' => false, 'pesan' => 'Upload file gagal']);
            exit;
        }
        $id_file = $id_file_baru;
    }

    $update = query("UPDATE arsip_jurusan SET nama='$nama', tahun='$tahun', file=" . ($id_file !== '' ? "'$id_file'" : "NULL") . " WHERE id='$id'");

    if($update){
        echo json_encode(['status' => true, 'pesan' => 'Data berhasil diubah']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data gagal diubah']);
    }
}

// ================= HAPUS DATA ARSIP JURUSAN =================
if(isset($_POST['hapus-data-arsip-jurusan'])){
    $id = bersihkan($_POST['id']);

    // ambil id file untuk dihapus
    $row = mysqli_fetch_assoc(query("SELECT file FROM arsip_jurusan WHERE id='$id'"));
    if($row && $row['file'] !== ''){
        // hapus file fisik
        $rowFile = mysqli_fetch_assoc(query("SELECT nama_file FROM files WHERE id='" . bersihkan($row['file']) . "'"));
        if($rowFile){
            $path = dirname(__DIR__, 4) . '/file/' . $rowFile['nama_file'];
            if(file_exists($path)){ @unlink($path); }
        }
        // hapus record files
        query("DELETE FROM files WHERE id='" . bersihkan($row['file']) . "'");
    }

    $hapus = query("DELETE FROM arsip_jurusan WHERE id='$id'");

    if($hapus){
        echo json_encode(['status' => true, 'pesan' => 'Data berhasil dihapus']);
    } else {
        $err = mysqli_error($GLOBALS['koneksi']);
        echo json_encode(['status' => false, 'pesan' => 'Data gagal dihapus: ' . $err]);
    }
}
?>
