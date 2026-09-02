<?php
require __DIR__ . "/../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// pastikan user sudah login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
    exit;
}

// ================= AMBIL DATA TABEL BERITA =================
// return JSON: data = HTML grid kartu, lead = HTML kartu unggulan (berita terbaru)
if(isset($_POST['ambil-tabel-berita'])){
    $ambil = query("SELECT * FROM berita ORDER BY tanggal DESC, id DESC");
    $semua = [];
    while($data = mysqli_fetch_assoc($ambil)){
        $semua[] = $data;
    }

    // ---------- KARTU UNGGULAN (LEAD) = berita terbaru ----------
    ob_start();
    if(count($semua) > 0){
        $d = $semua[0];
        $url = url_file($d['gambar']);
        ?>
        <article class="grid overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:grid-cols-2" data-lead data-id="<?= $d['id'] ?>">
            <div class="news-cover min-h-[220px] md:min-h-[300px]">
                <?php if($url !== ''): ?>
                <img src="<?= $url ?>" alt="<?= tampil($d['judul']) ?>" loading="lazy">
                <?php else: ?>
                <div class="news-cover-empty"><i class="fas fa-newspaper"></i></div>
                <?php endif; ?>
            </div>
            <div class="flex flex-col p-6 md:p-8">
                <div class="mb-3 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-orange-700">
                        <i class="fas fa-fire"></i> Terbaru
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600"><?= tampil($d['kategori']) ?></span>
                    <span class="ml-auto text-xs font-medium text-slate-400"><?= fmtTgl($d['tanggal']) ?></span>
                </div>
                <h2 class="text-lg font-bold leading-snug tracking-tight text-slate-800 md:text-2xl"><?= tampil($d['judul']) ?></h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-500"><?= tampil($d['deskripsi']) ?></p>
                <div class="mt-auto flex items-center justify-between pt-6">
                    <span class="text-xs text-slate-400"><i class="fas fa-user-pen mr-1"></i><?= tampil($d['penulis']) ?></span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                data-id="<?= $d['id'] ?>" data-judul="<?= tampil($d['judul']) ?>" data-kategori="<?= tampil($d['kategori']) ?>"
                                data-penulis="<?= tampil($d['penulis']) ?>" data-tanggal="<?= tampil($d['tanggal']) ?>" data-gambar="<?= $url ?>">
                            <i class="fas fa-eye text-xs"></i><span class="tip">Baca</span>
                        </button>
                        <button type="button" class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600" data-id="<?= $d['id'] ?>">
                            <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                        </button>
                        <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="<?= $d['id'] ?>">
                            <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                        </button>
                    </div>
                </div>
            </div>
            <template data-isi><?= isi_html($d['isi']) ?></template>
        </article>
        <?php
    }
    $lead = ob_get_clean();

    // ---------- GRID KARTU BERITA (sisanya) ----------
    ob_start();
    if(count($semua) > 1){
        for($i = 1; $i < count($semua); $i++){
            $d = $semua[$i];
            $url = url_file($d['gambar']);
            ?>
            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" data-id="<?= $d['id'] ?>">
                <div class="news-cover aspect-[16/10]">
                    <?php if($url !== ''): ?>
                    <img src="<?= $url ?>" alt="<?= tampil($d['judul']) ?>" loading="lazy">
                    <?php else: ?>
                    <div class="news-cover-empty"><i class="fas fa-newspaper"></i></div>
                    <?php endif; ?>
                </div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-orange-500 px-2.5 py-0.5 font-semibold text-white"><?= tampil($d['kategori']) ?></span>
                        <span class="font-medium text-slate-400"><?= fmtTgl($d['tanggal']) ?></span>
                    </div>
                    <h3 class="line-clamp-2 text-[15px] font-semibold leading-snug text-slate-800"><?= tampil($d['judul']) ?></h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500"><?= tampil($d['deskripsi']) ?></p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i><?= tampil($d['penulis']) ?></span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                    data-id="<?= $d['id'] ?>" data-judul="<?= tampil($d['judul']) ?>" data-kategori="<?= tampil($d['kategori']) ?>"
                                    data-penulis="<?= tampil($d['penulis']) ?>" data-tanggal="<?= tampil($d['tanggal']) ?>" data-gambar="<?= $url ?>">
                                <i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span>
                            </button>
                            <button type="button" class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600" data-id="<?= $d['id'] ?>">
                                <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                            </button>
                            <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="<?= $d['id'] ?>">
                                <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
                <template data-isi><?= isi_html($d['isi']) ?></template>
            </article>
            <?php
        }
    }
    $html = ob_get_clean();

    echo json_encode(['status' => true, 'data' => $html, 'lead' => $lead, 'total' => count($semua)]);
}

// ================= AMBIL DETAIL SATU BERITA (untuk mode edit) =================
if(isset($_POST['ambil-berita'])){
    $id = bersihkan($_POST['id']);
    $data = mysqli_fetch_assoc(query("SELECT * FROM berita WHERE id='$id'"));

    if($data){
        // url gambar lama untuk preview
        $data['url_gambar'] = url_file($data['gambar']);
        // cek apakah gambar ini aktif di gambar_beranda (untuk status switch)
        $rowBeranda = mysqli_fetch_assoc(query("SELECT id FROM gambar_beranda WHERE gambar='" . bersihkan($data['gambar']) . "' LIMIT 1"));
        $data['tampil_beranda'] = $rowBeranda ? 1 : 0;

        echo json_encode(['status' => true, 'data' => $data]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Berita tidak ditemukan']);
    }
}

// ================= TAMBAH DATA BERITA =================
if(isset($_POST['tambah-data-berita'])){
    $judul = bersihkan($_POST['judul']);
    $kategori = bersihkan($_POST['kategori']);
    $penulis = bersihkan($_POST['penulis']);
    $tanggal = bersihkan($_POST['tanggal']);
    $deskripsi = mb_substr(bersihkan($_POST['deskripsi']), 0, 255);
    $isi = bersihkan($_POST['isi']);
    $sumber = bersihkan($_POST['sumber'] ?? '');
    $tampilBeranda = isset($_POST['tampil_beranda']) && $_POST['tampil_beranda'] == '1';

    if($judul === '' || $isi === '' || $penulis === '' || $tanggal === ''){
        echo json_encode(['status' => false, 'pesan' => 'Judul, isi, penulis, dan tanggal wajib diisi']);
        exit;
    }

    // gambar wajib diunggah
    if(!isset($_FILES['gambar_upload']) || $_FILES['gambar_upload']['error'] != 0){
        echo json_encode(['status' => false, 'pesan' => 'Gambar berita wajib diunggah']);
        exit;
    }

    // validasi & upload gambar
    $level = (int)($_SESSION['level'] ?? 1);
    $cek = validasi_file($_FILES['gambar_upload'], $level);
    if(!$cek['valid']){
        echo json_encode(['status' => false, 'pesan' => $cek['pesan']]);
        exit;
    }

    $id_file = upload_file($_FILES['gambar_upload'], 'berita');
    if($id_file === false){
        echo json_encode(['status' => false, 'pesan' => 'Upload gambar gagal']);
        exit;
    }

    // slug sederhana dari judul untuk kolom keterangan
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $_POST['judul']), '-'));

    $simpan = query("INSERT INTO berita (judul, gambar, isi, deskripsi, sumber, kategori, penulis, tanggal, log, keterangan) VALUES ('$judul', '$id_file', '$isi', '$deskripsi', '$sumber', '$kategori', '$penulis', '$tanggal', " . time() . ", '$slug')");

    if(!$simpan){
        $err = mysqli_error($GLOBALS['koneksi']);
        echo json_encode(['status' => false, 'pesan' => 'Berita gagal ditambahkan: ' . $err]);
        exit;
    }

    // jika switch tampilkan di beranda aktif, simpan gambar juga ke tabel gambar_beranda
    if($tampilBeranda){
        query("INSERT INTO gambar_beranda (gambar, judul) VALUES ('$id_file', '$judul')");
    }

    echo json_encode(['status' => true, 'pesan' => 'Berita berhasil ditambahkan']);
}

// ================= EDIT DATA BERITA =================
if(isset($_POST['edit-data-berita'])){
    $id = bersihkan($_POST['id']);
    $judul = bersihkan($_POST['judul']);
    $kategori = bersihkan($_POST['kategori']);
    $penulis = bersihkan($_POST['penulis']);
    $tanggal = bersihkan($_POST['tanggal']);
    $deskripsi = mb_substr(bersihkan($_POST['deskripsi']), 0, 255);
    $isi = bersihkan($_POST['isi']);
    $sumber = bersihkan($_POST['sumber'] ?? '');
    $gambarLama = bersihkan($_POST['gambar_lama'] ?? '');
    $tampilBeranda = isset($_POST['tampil_beranda']) && $_POST['tampil_beranda'] == '1';

    if($judul === '' || $isi === '' || $penulis === '' || $tanggal === ''){
        echo json_encode(['status' => false, 'pesan' => 'Judul, isi, penulis, dan tanggal wajib diisi']);
        exit;
    }

    // upload gambar baru jika ada
    $id_file = $gambarLama;
    if(isset($_FILES['gambar_upload']) && $_FILES['gambar_upload']['error'] == 0){
        $level = (int)($_SESSION['level'] ?? 1);
        $cek = validasi_file($_FILES['gambar_upload'], $level);
        if(!$cek['valid']){
            echo json_encode(['status' => false, 'pesan' => $cek['pesan']]);
            exit;
        }

        $id_file_baru = upload_file($_FILES['gambar_upload'], 'berita', $gambarLama);
        if($id_file_baru === false){
            echo json_encode(['status' => false, 'pesan' => 'Upload gambar gagal']);
            exit;
        }
        $id_file = $id_file_baru;
    }

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $_POST['judul']), '-'));

    $update = query("UPDATE berita SET judul='$judul', gambar=" . ($id_file !== '' ? "'$id_file'" : "NULL") . ", isi='$isi', deskripsi='$deskripsi', sumber='$sumber', kategori='$kategori', penulis='$penulis', tanggal='$tanggal', log=" . time() . ", keterangan='$slug' WHERE id='$id'");

    if(!$update){
        $err = mysqli_error($GLOBALS['koneksi']);
        echo json_encode(['status' => false, 'pesan' => 'Berita gagal diubah: ' . $err]);
        exit;
    }

    // sinkronkan ke gambar_beranda.
    // kunci pencarian = id file gambar (gambar lama ATAU gambar baru), bukan judul.
    if($id_file !== ''){
        $rowBeranda = mysqli_fetch_assoc(query("SELECT id FROM gambar_beranda WHERE gambar='" . bersihkan($id_file) . "' LIMIT 1"));
        if($tampilBeranda && !$rowBeranda){
            // switch aktif tapi belum ada di beranda -> tambah
            query("INSERT INTO gambar_beranda (gambar, judul) VALUES ('$id_file', '$judul')");
        } elseif(!$tampilBeranda && $rowBeranda){
            // switch dimatikan dan ada di beranda -> hapus
            query("DELETE FROM gambar_beranda WHERE id=" . (int)$rowBeranda['id']);
        }
    }

    echo json_encode(['status' => true, 'pesan' => 'Berita berhasil diubah']);
}

// ================= HAPUS DATA BERITA =================
if(isset($_POST['hapus-data-berita'])){
    $id = bersihkan($_POST['id']);

    // ambil judul & id gambar untuk dihapus
    $row = mysqli_fetch_assoc(query("SELECT judul, gambar FROM berita WHERE id='$id'"));
    if($row){
        // hapus juga dari gambar_beranda (berdasarkan id gambar yang sama)
        query("DELETE FROM gambar_beranda WHERE gambar='" . bersihkan($row['gambar']) . "'");

        if($row['gambar'] !== ''){
            // hapus file fisik
            $rowFile = mysqli_fetch_assoc(query("SELECT nama_file FROM files WHERE id='" . bersihkan($row['gambar']) . "'"));
            if($rowFile){
                $path = dirname(__DIR__, 4) . '/file/' . $rowFile['nama_file'];
                if(file_exists($path)){ @unlink($path); }
            }
            // hapus record files
            query("DELETE FROM files WHERE id='" . bersihkan($row['gambar']) . "'");
        }
    }

    $hapus = query("DELETE FROM berita WHERE id='$id'");

    if($hapus){
        echo json_encode(['status' => true, 'pesan' => 'Berita berhasil dihapus']);
    } else {
        $err = mysqli_error($GLOBALS['koneksi']);
        echo json_encode(['status' => false, 'pesan' => 'Berita gagal dihapus: ' . $err]);
    }
}

// ================= FUNGSI PENDUKUNG =================
// format tanggal YYYY-MM-DD -> hari, tanggal bulan tahun (dipakai di HTML kartu)
function fmtTgl($v){
    if(!$v) return '';
    $p = explode('-', $v);
    if(count($p) !== 3) return $v;
    $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $ts = mktime(0, 0, 0, (int)$p[1], (int)$p[2], (int)$p[0]);
    if(!$ts) return $v;
    return $hari[(int)date('w', $ts)] . ', ' . (int)$p[2] . ' ' . $bulan[(int)$p[1] - 1] . ' ' . $p[0];
}

// ubah isi berita (teks polos, paragraf dipisah "||" atau baris baru) menjadi HTML <p>
// dipakai untuk <template data-isi> di tiap kartu (sumber isi modal Lihat)
function isi_html($src){
    $parts = preg_split('/\|\||\n/', (string)$src);
    $html = '';
    foreach($parts as $p){
        $p = trim($p);
        if($p !== '') $html .= '<p>' . tampil($p) . '</p>';
    }
    return $html;
}
?>
