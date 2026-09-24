<?php
require __DIR__ . '/../../../../fungsi.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) && !isset($_SESSION['nip']) && !isset($_SESSION['username'])) {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
    exit;
}

// ================= AMBIL DATA TABEL MATAKULIAH MBKM =================
if (isset($_POST['ambil-tabel-matkul'])) {
    $query = query("SELECT kode, nama, sks FROM matakuliah_mbkm ORDER BY kode ASC");
    $dataMatkul = [];
    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $dataMatkul[] = $row;
        }
    }
    $totalMatkul = count($dataMatkul);
    
    ob_start();
    ?>
    <div class="mb-3 flex items-center justify-between">
      <p class="text-xs font-medium text-slate-500"><i class="fas fa-list mr-1 text-orange-400"></i>Menampilkan <span id="countNow" class="font-bold text-slate-700"><?= $totalMatkul ?></span> dari <?= $totalMatkul ?> matakuliah</p>
    </div>
    <div class="mk-grid" id="mkGrid">
      <?php foreach ($dataMatkul as $row): 
        $sks = (int)$row['sks'];
        if ($sks >= 5) {
          $barColor = '#f59e0b'; $ringColor = '#fde68a'; $badgeBg = 'bg-amber-100 text-amber-700'; $dotBg = 'bg-amber-500'; $icoBg = 'bg-amber-100 text-amber-500'; $icoClass = 'fa-certificate';
        } elseif ($sks == 4) {
          $barColor = '#8b5cf6'; $ringColor = '#ddd6fe'; $badgeBg = 'bg-violet-100 text-violet-700'; $dotBg = 'bg-violet-500'; $icoBg = 'bg-violet-100 text-violet-500'; $icoClass = 'fa-brain';
        } elseif ($sks == 3) {
          $barColor = '#0ea5e9'; $ringColor = '#bae6fd'; $badgeBg = 'bg-sky-100 text-sky-700'; $dotBg = 'bg-sky-500'; $icoBg = 'bg-sky-100 text-sky-500'; $icoClass = 'fa-book-open';
        } else {
          $barColor = '#64748b'; $ringColor = '#cbd5e1'; $badgeBg = 'bg-slate-100 text-slate-700'; $dotBg = 'bg-slate-400'; $icoBg = 'bg-slate-100 text-slate-500'; $icoClass = 'fa-layer-group';
        }
        $dataCari = strtolower(tampil($row['kode'] . ' ' . $row['nama']));
      ?>
      <article class="mk-card lift reveal" data-sks="<?= $sks ?>" data-cari="<?= $dataCari ?>" style="--bar:<?= $barColor ?>;--ring:<?= $ringColor ?>">
        <div class="mk-bar"></div>
        <div class="p-4">
          <div class="flex items-start justify-between gap-2">
            <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500"><?= tampil($row['kode']) ?></code>
            <span class="badge-sks <?= $badgeBg ?>"><span class="h-1.5 w-1.5 rounded-full <?= $dotBg ?>"></span><?= $sks ?> SKS</span>
          </div>
          <div class="mt-3 flex items-start gap-3">
            <span class="mk-ico <?= $icoBg ?>"><i class="fas <?= $icoClass ?>"></i></span>
            <h3 class="text-[13px] font-semibold leading-snug text-slate-800"><?= tampil($row['nama']) ?></h3>
          </div>
          <div class="mk-act flex gap-2">
            <button type="button" class="btn-edit rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700" data-kode="<?= tampil($row['kode']) ?>"><i class="fas fa-pen mr-1 text-[10px]"></i>Edit</button>
            <button type="button" class="btn-hapus rounded-lg bg-rose-500 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-rose-600" data-kode="<?= tampil($row['kode']) ?>" data-nama="<?= tampil($row['nama']) ?>"><i class="fas fa-trash mr-1 text-[10px]"></i>Hapus</button>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <p class="mt-3 text-[11px] text-slate-400"><i class="fas fa-lightbulb mr-1 text-amber-400"></i>Matakuliah kesetaraan program MBKM yang diakui dan dapat dikonversi SKS-nya.</p>
    <?php
    $html = ob_get_clean();
    echo json_encode(['status' => true, 'data' => $html]);
    exit;
}

// ================= TAMBAH DATA =================
if (isset($_POST['tambah-data-matkul'])) {
    $kode = bersihkan($_POST['kode'] ?? '');
    $nama = bersihkan($_POST['nama'] ?? '');
    $sks  = (int)($_POST['sks'] ?? 0);

    if (empty($kode) || empty($nama) || $sks <= 0) {
        echo json_encode(['status' => false, 'pesan' => 'Kode, nama, dan SKS wajib diisi']);
        exit;
    }

    $cek = query("SELECT kode FROM matakuliah_mbkm WHERE kode = '$kode'");
    if (mysqli_num_rows($cek) > 0) {
        echo json_encode(['status' => false, 'pesan' => 'Kode matakuliah sudah ada']);
        exit;
    }

    $simpan = query("INSERT INTO matakuliah_mbkm (kode, nama, sks) VALUES ('$kode', '$nama', $sks)");
    if ($simpan) {
        echo json_encode(['status' => true, 'pesan' => 'Matakuliah berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menambahkan matakuliah']);
    }
    exit;
}

// ================= EDIT DATA =================
if (isset($_POST['edit-data-matkul'])) {
    $kodeLama = bersihkan($_POST['kodeLama'] ?? '');
    $kode = bersihkan($_POST['kode'] ?? '');
    $nama = bersihkan($_POST['nama'] ?? '');
    $sks  = (int)($_POST['sks'] ?? 0);

    if (empty($kodeLama) || empty($kode) || empty($nama) || $sks <= 0) {
        echo json_encode(['status' => false, 'pesan' => 'Data tidak valid']);
        exit;
    }

    if ($kodeLama !== $kode) {
        $cek = query("SELECT kode FROM matakuliah_mbkm WHERE kode = '$kode'");
        if (mysqli_num_rows($cek) > 0) {
            echo json_encode(['status' => false, 'pesan' => 'Kode matakuliah sudah ada']);
            exit;
        }
    }

    $update = query("UPDATE matakuliah_mbkm SET kode='$kode', nama='$nama', sks=$sks WHERE kode='$kodeLama'");
    if ($update) {
        echo json_encode(['status' => true, 'pesan' => 'Matakuliah berhasil diubah']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal mengubah matakuliah']);
    }
    exit;
}

// ================= HAPUS DATA =================
if (isset($_POST['hapus-data-matkul'])) {
    $kode = bersihkan($_POST['kode'] ?? '');
    if (empty($kode)) {
        echo json_encode(['status' => false, 'pesan' => 'Kode tidak valid']);
        exit;
    }
    $hapus = query("DELETE FROM matakuliah_mbkm WHERE kode='$kode'");
    if ($hapus) {
        echo json_encode(['status' => true, 'pesan' => 'Matakuliah berhasil dihapus']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus matakuliah']);
    }
    exit;
}

// ================= AMBIL DETAIL DATA =================
if (isset($_POST['ambil-detail-matkul'])) {
    $kode = bersihkan($_POST['kode'] ?? '');
    $q = query("SELECT * FROM matakuliah_mbkm WHERE kode='$kode'");
    $row = mysqli_fetch_assoc($q);
    if ($row) {
        echo json_encode(['status' => true, 'data' => $row]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Matakuliah tidak ditemukan']);
    }
    exit;
}
