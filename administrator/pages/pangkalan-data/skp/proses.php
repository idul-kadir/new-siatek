<?php
/**
 * PROSES CRUD SKP (Pangkalan Data)
 */
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

try {
    require_once __DIR__ . '/../../../../fungsi.php';
    if (session_status() === PHP_SESSION_NONE) session_start();

    // Pastikan user sudah login
    $userId = $_SESSION['id'] ?? $_SESSION['user_id'] ?? '';
    if (empty($userId)) {
        echo json_encode(['status' => false, 'pesan' => 'Sesi Anda telah berakhir, silakan login kembali.']);
        exit;
    }

// ================= AMBIL TABEL SKP =================
if (isset($_POST['ambil-tabel-skp'])) {
    $tahunFilter = isset($_POST['tahun']) ? bersihkan($_POST['tahun']) : '';

    $sql = "SELECT s.*, 
            p.nama_file as p_nama, p.format_file as p_format, p.waktu_unggah as p_waktu,
            h.nama_file as h_nama, h.format_file as h_format, h.waktu_unggah as h_waktu,
            d.nama AS nama_dosen
            FROM skp s
            LEFT JOIN files p ON s.penilaian = p.id
            LEFT JOIN files h ON s.hasil = h.id
            LEFT JOIN dosen d ON s.dosen = d.id
            WHERE s.dosen = '$userId'";

    if ($tahunFilter !== '') {
        $sql .= " AND s.tahun = '$tahunFilter'";
    }
    $sql .= " ORDER BY s.tahun DESC, s.id DESC";

    $ambil = query($sql);

    ob_start();
    if ($ambil && mysqli_num_rows($ambil) > 0) {
        $no = 1;
        while ($row = mysqli_fetch_assoc($ambil)) {
            $urlPenilaian = ($row['penilaian'] !== '') ? url_file($row['penilaian']) : '';
            $urlHasil     = ($row['hasil'] !== null && $row['hasil'] !== '') ? url_file($row['hasil']) : '';

            $extP = strtolower(pathinfo($row['p_nama'] ?? '', PATHINFO_EXTENSION));
            $extH = strtolower(pathinfo($row['h_nama'] ?? '', PATHINFO_EXTENSION));

            $icoP = 'fa-file-alt';
            if (in_array($extP, ['pdf'])) $icoP = 'fa-file-pdf text-red-500';
            else if (in_array($extP, ['doc', 'docx'])) $icoP = 'fa-file-word text-blue-500';
            else if (in_array($extP, ['xls', 'xlsx'])) $icoP = 'fa-file-excel text-emerald-500';
            else if (in_array($extP, ['jpg', 'jpeg', 'png'])) $icoP = 'fa-file-image text-amber-500';

            $icoH = 'fa-file-alt';
            if (in_array($extH, ['pdf'])) $icoH = 'fa-file-pdf text-red-500';
            else if (in_array($extH, ['doc', 'docx'])) $icoH = 'fa-file-word text-blue-500';
            else if (in_array($extH, ['xls', 'xlsx'])) $icoH = 'fa-file-excel text-emerald-500';
            else if (in_array($extH, ['jpg', 'jpeg', 'png'])) $icoH = 'fa-file-image text-amber-500';
            ?>
            <div class="dok-row flex flex-wrap items-center justify-between gap-3 p-4 mb-3 rounded-xl border border-slate-200 bg-white shadow-sm hover:border-slate-300 transition">
              <div class="flex items-center gap-3 min-w-[200px]">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600 font-bold">
                  <i class="fas fa-file-contract"></i>
                </div>
                <div>
                  <h4 class="font-bold text-slate-800 text-sm">SKP Tahun <?= $row['tahun'] ?></h4>
                  <p class="text-[11px] text-slate-400">Dosen: <?= tampil($row['nama_dosen'] ?: 'Saya') ?></p>
                </div>
              </div>

              <div class="flex flex-wrap items-center gap-4 text-xs">
                <!-- File Penilaian (Draft) -->
                <div class="flex items-center gap-2">
                  <span class="text-slate-400 font-medium">Draft:</span>
                  <?php if (!empty($row['penilaian']) && !empty($urlPenilaian)): ?>
                    <a href="<?= $urlPenilaian ?>" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700 hover:bg-slate-200 font-medium transition" title="<?= tampil($row['p_nama']) ?>">
                      <i class="fas <?= $icoP ?>"></i>
                      <span class="max-w-[120px] truncate"><?= tampil($row['p_nama']) ?></span>
                      <i class="fas fa-download text-[10px] text-slate-400"></i>
                    </a>
                  <?php else: ?>
                    <span class="text-slate-400 italic">Belum ada</span>
                  <?php endif; ?>
                </div>

                <!-- File Hasil (Final) -->
                <div class="flex items-center gap-2">
                  <span class="text-slate-400 font-medium">Final:</span>
                  <?php if (!empty($row['hasil']) && !empty($urlHasil)): ?>
                    <a href="<?= $urlHasil ?>" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-2.5 py-1 text-emerald-700 hover:bg-emerald-100 font-medium transition" title="<?= tampil($row['h_nama']) ?>">
                      <i class="fas <?= $icoH ?>"></i>
                      <span class="max-w-[120px] truncate"><?= tampil($row['h_nama']) ?></span>
                      <i class="fas fa-download text-[10px] text-emerald-500"></i>
                    </a>
                  <?php else: ?>
                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700"><i class="fas fa-clock mr-1"></i> Menunggu Verifikasi</span>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Tombol Aksi -->
              <div class="flex items-center gap-1.5 ml-auto">
                <button type="button" onclick="editSkp('<?= $row['id'] ?>', '<?= $row['tahun'] ?>')" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700" title="Edit">
                  <i class="fas fa-pen text-xs"></i>
                  <span class="tip">Edit SKP</span>
                </button>
                <button type="button" onclick="hapusSkp('<?= $row['id'] ?>', '<?= $row['tahun'] ?>')" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600" title="Hapus">
                  <i class="fas fa-trash text-xs"></i>
                  <span class="tip">Hapus SKP</span>
                </button>
              </div>
            </div>
            <?php
            $no++;
        }
    } else {
        ?>
        <div class="dok-empty" style="display:block;">
          <i class="fas fa-folder-open text-2xl text-slate-300 mb-2 block"></i>
          Belum ada data SKP yang diunggah.
        </div>
        <?php
    }
    $html = ob_get_clean();

    echo json_encode(['status' => true, 'data' => $html]);
    exit;
}

// ================= TAMBAH DATA SKP =================
if (isset($_POST['tambah-data-skp'])) {
    $tahun = bersihkan($_POST['tahun']);

    if (empty($tahun)) {
        echo json_encode(['status' => false, 'pesan' => 'Tahun wajib diisi']);
        exit;
    }

    if (!isset($_FILES['penilaian']) || $_FILES['penilaian']['error'] != 0) {
        echo json_encode(['status' => false, 'pesan' => 'File penilaian (draft) wajib diunggah']);
        exit;
    }

    $validasi = validasi_file($_FILES['penilaian'], $_SESSION['level'] ?? 1);
    if (!$validasi['valid']) {
        echo json_encode(['status' => false, 'pesan' => $validasi['pesan']]);
        exit;
    }

    // Upload file draft (wajib punya ID baru di tabel files)
    $idPenilaian = upload_file($_FILES['penilaian'], 'skp');
    if ($idPenilaian === false) {
        echo json_encode(['status' => false, 'pesan' => 'Gagal mencatat file penilaian ke tabel file']);
        exit;
    }

    $userIdB = bersihkan($userId);
    $idPenilaianB = bersihkan($idPenilaian);
    $tahunB = bersihkan($tahun);
    $simpan = query("INSERT INTO skp (dosen, penilaian, hasil, tahun) VALUES ('$userIdB', '$idPenilaianB', NULL, '$tahunB')");

    if ($simpan) {
        echo json_encode(['status' => true, 'pesan' => 'Data SKP berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan data SKP ke database: ' . mysqli_error($koneksi)]);
    }
    exit;
}

// ================= EDIT DATA SKP =================
if (isset($_POST['edit-data-skp'])) {
    $id    = bersihkan($_POST['id']);
    $tahun = bersihkan($_POST['tahun']);

    if (empty($id) || empty($tahun)) {
        echo json_encode(['status' => false, 'pesan' => 'Data input tidak lengkap']);
        exit;
    }

    // Ambil data lama
    $qLama = query("SELECT * FROM skp WHERE id = '$id' AND dosen = '$userId' LIMIT 1");
    if (!$qLama || mysqli_num_rows($qLama) == 0) {
        echo json_encode(['status' => false, 'pesan' => 'Data SKP tidak ditemukan atau Anda tidak memiliki akses']);
        exit;
    }
    $rowLama = mysqli_fetch_assoc($qLama);

    $idPenilaian = $rowLama['penilaian'];
    if (isset($_FILES['penilaian']) && $_FILES['penilaian']['error'] == 0) {
        $validasi = validasi_file($_FILES['penilaian'], $_SESSION['level'] ?? 1);
        if (!$validasi['valid']) {
            echo json_encode(['status' => false, 'pesan' => $validasi['pesan']]);
            exit;
        }

        $idPenilaianBaru = upload_file($_FILES['penilaian'], 'skp', $rowLama['penilaian']);
        if ($idPenilaianBaru === false) {
            echo json_encode(['status' => false, 'pesan' => 'Gagal mengunggah file penilaian']);
            exit;
        }
        $idPenilaian = $idPenilaianBaru;
    }

    $update = query("UPDATE skp SET tahun = '" . bersihkan($tahun) . "', penilaian = '" . bersihkan($idPenilaian) . "' WHERE id = '" . bersihkan($id) . "' AND dosen = '" . bersihkan($userId) . "'");

    if ($update) {
        echo json_encode(['status' => true, 'pesan' => 'Data SKP berhasil diperbarui']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui data SKP ke database: ' . mysqli_error($koneksi)]);
    }
    exit;
}

// ================= HAPUS DATA SKP =================
if (isset($_POST['hapus-data-skp'])) {
    $id = bersihkan($_POST['id']);

    $qCek = query("SELECT * FROM skp WHERE id = '$id' AND dosen = '$userId' LIMIT 1");
    if ($qCek && mysqli_num_rows($qCek) > 0) {
        $dt = mysqli_fetch_assoc($qCek);
        $idPenilaian = $dt['penilaian'];
        $idHasil     = $dt['hasil'];

        // 1. Hapus record skp dulu agar FK constraint files tidak gagal
        $hapus = query("DELETE FROM skp WHERE id = '$id' AND dosen = '$userId'");

        if ($hapus) {
            // 2. Hapus file penilaian
            if (!empty($idPenilaian)) {
                $rP = mysqli_fetch_assoc(query("SELECT nama_file FROM files WHERE id = '" . bersihkan($idPenilaian) . "' LIMIT 1"));
                if ($rP && !empty($rP['nama_file'])) {
                    $pathP = __DIR__ . '/../../../../file/' . $rP['nama_file'];
                    if (file_exists($pathP)) { @unlink($pathP); }
                }
                query("DELETE FROM files WHERE id = '" . bersihkan($idPenilaian) . "'");
            }

            // 3. Hapus file hasil
            if (!empty($idHasil)) {
                $rH = mysqli_fetch_assoc(query("SELECT nama_file FROM files WHERE id = '" . bersihkan($idHasil) . "' LIMIT 1"));
                if ($rH && !empty($rH['nama_file'])) {
                    $pathH = __DIR__ . '/../../../../file/' . $rH['nama_file'];
                    if (file_exists($pathH)) { @unlink($pathH); }
                }
                query("DELETE FROM files WHERE id = '" . bersihkan($idHasil) . "'");
            }

            echo json_encode(['status' => true, 'pesan' => 'Data SKP berhasil dihapus']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus data SKP']);
        }
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data SKP tidak ditemukan atau Anda tidak memiliki akses']);
    }
    exit;
}

} catch (Throwable $e) {
    echo json_encode(['status' => false, 'pesan' => 'Server Error: ' . $e->getMessage()]);
}
