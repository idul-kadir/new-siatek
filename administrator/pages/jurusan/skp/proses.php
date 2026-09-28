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

// ================= AMBIL TABEL SKP =================
if (isset($_POST['ambil-tabel-skp'])) {
    $ambil = query("
        SELECT 
            s.*, 
            d.nama AS nama_dosen, 
            d.nip,
            fd.nama_file AS draft_file,
            fd.waktu_unggah AS draft_waktu,
            ff.nama_file AS final_file,
            ff.waktu_unggah AS final_waktu
        FROM skp s
        LEFT JOIN dosen d ON s.dosen = d.id
        LEFT JOIN files fd ON s.penilaian = fd.id
        LEFT JOIN files ff ON s.hasil = ff.id
        ORDER BY s.tahun DESC, s.id DESC
    ");

    ob_start();
    if (mysqli_num_rows($ambil) > 0) {
        while ($data = mysqli_fetch_assoc($ambil)) {
            $status = ($data['hasil'] !== '') ? 'selesai' : 'menunggu';
            
            // Dummy semester dan keterangan karena tidak ada di tabel skp
            $semester = 'ganjil'; 
            $ket = ($status === 'selesai') ? 'Verifikasi selesai.' : 'Menunggu proses verifikasi.';

            $inisial = strtoupper(implode('', array_map(function($w){ return $w[0] ?? ''; }, array_slice(array_filter(explode(' ', preg_replace('/[^A-Za-z\s]/', '', $data['nama_dosen'])), function($w){ return preg_match('/^[A-Z]/i', $w); }), 0, 2))));
            if(!$inisial) $inisial = '--';

            $tglDraft = $data['draft_waktu'] ? date('d M Y', $data['draft_waktu']) : '-';
            
            // Generate pencarian
            $cari = strtolower(implode(' ', [$data['nama_dosen'], $data['nip'], $data['tahun'], $semester]));
            
            ?>
            <tr class="reveal" 
                data-id="<?= $data['id'] ?>"
                data-status="<?= $status ?>" 
                data-tahun="<?= $data['tahun'] ?>" 
                data-semester="<?= $semester ?>" 
                data-nama="<?= tampil($data['nama_dosen']) ?>" 
                data-nip="<?= tampil($data['nip']) ?>" 
                data-draft="<?= tampil($data['draft_file']) ?>" 
                data-draft-url="<?= url_file($data['penilaian']) ?>"
                data-final="<?= tampil($data['final_file']) ?>" 
                data-final-url="<?= url_file($data['hasil']) ?>"
                data-ket="<?= tampil($ket) ?>" 
                data-hari="<?= $tglDraft ?>" 
                data-cari="<?= tampil($cari) ?>">
                
                <td>
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-100 text-[11px] font-bold text-sky-700"><?= $inisial ?></span>
                        <div class="min-w-0">
                            <p class="font-semibold leading-snug text-slate-800"><?= tampil($data['nama_dosen']) ?></p>
                            <p class="text-[10px] text-slate-400">NIP <?= tampil($data['nip']) ?></p>
                        </div>
                    </div>
                </td>
                <td><span class="pr-badge bg-sky-50 text-sky-700"><?= $data['tahun'] ?> · <?= ucfirst($semester) ?></span></td>
                <td>
                    <?php if ($status === 'selesai'): ?>
                        <span class="st-badge bg-emerald-100 text-emerald-700"><i class="fas fa-check"></i>Selesai</span>
                    <?php else: ?>
                        <span class="st-badge bg-amber-100 text-amber-700"><i class="fas fa-hourglass-half"></i>Menunggu Review</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($data['draft_file']): ?>
                        <?php 
                        $extD = strtolower(pathinfo($data['draft_file'], PATHINFO_EXTENSION));
                        $isPdfD = ($extD === 'pdf');
                        ?>
                        <span class="file-chip">
                            <span class="f-ico <?= $isPdfD ? 'f-pdf' : 'f-word' ?>"><i class="fas <?= $isPdfD ? 'fa-file-pdf' : 'fa-file-word' ?>"></i></span>
                            <span class="f-meta">
                                <span class="f-name"><?= tampil($data['draft_file']) ?></span>
                                <span class="f-sub"><?= $tglDraft ?></span>
                            </span>
                            <a href="<?= url_file($data['penilaian']) ?>" target="_blank" class="f-down" title="Unduh draft"><i class="fas fa-download"></i></a>
                        </span>
                    <?php else: ?>
                        <span class="empty-dash"><i class="fas fa-minus"></i>Belum ada</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($data['final_file']): ?>
                        <?php 
                        $extF = strtolower(pathinfo($data['final_file'], PATHINFO_EXTENSION));
                        $isPdfF = ($extF === 'pdf');
                        ?>
                        <span class="file-chip">
                            <span class="f-ico <?= $isPdfF ? 'f-pdf' : 'f-word' ?>"><i class="fas <?= $isPdfF ? 'fa-file-pdf' : 'fa-file-word' ?>"></i></span>
                            <span class="f-meta">
                                <span class="f-name"><?= tampil($data['final_file']) ?></span>
                                <span class="f-sub">final</span>
                            </span>
                            <a href="<?= url_file($data['hasil']) ?>" target="_blank" class="f-down" title="Unduh final"><i class="fas fa-download"></i></a>
                        </span>
                    <?php else: ?>
                        <span class="empty-dash"><i class="fas fa-minus"></i>Belum ada</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="inline-flex items-center gap-1.5">
                        <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat / Review</span></button>
                        
                        <?php if ($status === 'selesai'): ?>
                            <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Perbaiki File Final</span></button>
                        <?php else: ?>
                            <button type="button" class="btn-final btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-arrow-up text-xs"></i><span class="tip">Unggah File Final</span></button>
                        <?php endif; ?>
                        
                        <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus SKP</span></button>
                    </div>
                </td>
            </tr>
            <?php
        }
    } else {
        ?>
        <tr class="empty-row">
            <td colspan="6" class="py-16 text-center">
                <i class="fas fa-bullseye text-4xl text-slate-300"></i>
                <p class="mt-3 font-medium text-slate-500">Tidak ada data SKP masuk</p>
                <p class="text-xs text-slate-400">SKP yang diunggah oleh dosen akan muncul di sini.</p>
            </td>
        </tr>
        <?php
    }
    $html = ob_get_clean();

    echo json_encode(['status' => true, 'data' => $html]);
}

// ================= UNGGAH FILE FINAL =================
if (isset($_POST['unggah-final-skp'])) {
    $id = bersihkan($_POST['id']);
    
    // ambil data skp lama
    $cek = query("SELECT hasil FROM skp WHERE id = '$id'");
    if (mysqli_num_rows($cek) == 0) {
        echo json_encode(['status' => false, 'pesan' => 'Data SKP tidak ditemukan']);
        exit;
    }
    $dt = mysqli_fetch_assoc($cek);
    $hasilLama = $dt['hasil'];
    $id_file = $hasilLama;

    if (isset($_FILES['file_final']) && $_FILES['file_final']['error'] == 0) {
        $level = (int)($_SESSION['level'] ?? 1);
        $validasi = validasi_file($_FILES['file_final'], $level);
        if (!$validasi['valid']) {
            echo json_encode(['status' => false, 'pesan' => $validasi['pesan']]);
            exit;
        }

        $id_file_baru = upload_file($_FILES['file_final'], 'skp', $hasilLama);
        if ($id_file_baru === false) {
            echo json_encode(['status' => false, 'pesan' => 'Gagal mengunggah file']);
            exit;
        }
        $id_file = $id_file_baru;
    }

    if ($id_file !== '') {
        $update = query("UPDATE skp SET hasil = '$id_file' WHERE id = '$id'");
        if ($update) {
            echo json_encode(['status' => true, 'pesan' => 'File final berhasil diunggah']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan data ke database']);
        }
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Tidak ada file yang diunggah']);
    }
}

// ================= HAPUS DATA SKP =================
if (isset($_POST['hapus-data-skp'])) {
    $id = bersihkan($_POST['id']);

    $cek = query("SELECT penilaian, hasil FROM skp WHERE id = '$id'");
    if (mysqli_num_rows($cek) > 0) {
        $dt = mysqli_fetch_assoc($cek);
        
        // Hapus file draft
        if ($dt['penilaian'] !== '') {
            $rowFile = mysqli_fetch_assoc(query("SELECT nama_file FROM files WHERE id='" . bersihkan($dt['penilaian']) . "'"));
            if ($rowFile) {
                $path = dirname(__DIR__, 4) . '/file/' . $rowFile['nama_file'];
                if (file_exists($path)) { @unlink($path); }
            }
            query("DELETE FROM files WHERE id='" . bersihkan($dt['penilaian']) . "'");
        }

        // Hapus file final
        if ($dt['hasil'] !== '') {
            $rowFile = mysqli_fetch_assoc(query("SELECT nama_file FROM files WHERE id='" . bersihkan($dt['hasil']) . "'"));
            if ($rowFile) {
                $path = dirname(__DIR__, 4) . '/file/' . $rowFile['nama_file'];
                if (file_exists($path)) { @unlink($path); }
            }
            query("DELETE FROM files WHERE id='" . bersihkan($dt['hasil']) . "'");
        }
        
        $hapus = query("DELETE FROM skp WHERE id = '$id'");
        if ($hapus) {
            echo json_encode(['status' => true, 'pesan' => 'Data SKP berhasil dihapus']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus data']);
        }
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data tidak ditemukan']);
    }
}
