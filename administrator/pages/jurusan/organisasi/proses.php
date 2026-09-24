<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
require __DIR__ . "/../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
    exit;
}

// ================= AMBIL DATA JURUSAN & PEJABAT =================
if(isset($_POST['ambil-pejabat-jurusan'])){
    $ambil = query("SELECT j.*, 
        d1.nama AS nama_kajur, d1.nip AS nip_kajur,
        d2.nama AS nama_sekjur, d2.nip AS nip_sekjur,
        d3.nama AS nama_kalab, d3.nip AS nip_kalab,
        d4.nama AS nama_penjamu, d4.nip AS nip_penjamu,
        d5.nama AS nama_pengelolah, d5.nip AS nip_pengelolah
        FROM jurusan j
        LEFT JOIN dosen d1 ON j.kajur = d1.id
        LEFT JOIN dosen d2 ON j.sekjur = d2.id
        LEFT JOIN dosen d3 ON j.kalab = d3.id
        LEFT JOIN dosen d4 ON j.penjamu = d4.id
        LEFT JOIN dosen d5 ON j.pengelolah_jurnal = d5.id
        LIMIT 1");

    if($row = mysqli_fetch_assoc($ambil)){
        echo json_encode(['status' => true, 'data' => $row]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data jurusan tidak ditemukan']);
    }
    exit;
}

// ================= UPDATE PEJABAT STRUKTURAL JURUSAN =================
if(isset($_POST['edit-pejabat-jurusan'])){
    $jabatan = bersihkan($_POST['jabatan'] ?? '');
    $dosen_id = bersihkan($_POST['dosen_id'] ?? '');

    $kolomMap = [
        'Ketua Jurusan' => 'kajur',
        'Sekretaris Jurusan' => 'sekjur',
        'Kepala Laboratorium' => 'kalab',
        'Penjamin Mutu' => 'penjamu',
        'Ketua Pengelola Jurnal' => 'pengelolah_jurnal'
    ];

    if(!isset($kolomMap[$jabatan])){
        echo json_encode(['status' => false, 'pesan' => 'Jabatan tidak valid']);
        exit;
    }

    $kolom = $kolomMap[$jabatan];

    // Ambil ID / NIP dosen pejabat LAMA sebelum diupdate
    $qLama = query("SELECT `$kolom` AS pejabat_lama FROM jurusan LIMIT 1");
    $rowLama = mysqli_fetch_assoc($qLama);
    $pejabatLama = $rowLama['pejabat_lama'] ?? '';

    // Lakukan UPDATE di tabel jurusan
    $update = query("UPDATE jurusan SET `$kolom`='$dosen_id' WHERE id='123' OR id IS NOT NULL LIMIT 1");

    if($update){
        // Jika pejabat lama berbeda dengan pejabat baru, reset level pejabat lama jadi 1 (dosen biasa)
        if(!empty($pejabatLama) && $pejabatLama !== $dosen_id){
            query("UPDATE dosen SET level=1 WHERE id='$pejabatLama' OR nip='$pejabatLama'");
        }

        // Naikkan level pejabat BARU (jika Kajur / Sekjur) menjadi 6
        if(in_array($jabatan, ['Ketua Jurusan', 'Sekretaris Jurusan']) && !empty($dosen_id)){
            query("UPDATE dosen SET level=6 WHERE id='$dosen_id' OR nip='$dosen_id'");
        }

        echo json_encode(['status' => true, 'pesan' => "Pejabat $jabatan berhasil diperbarui"]);
    } else {
        echo json_encode(['status' => false, 'pesan' => "Gagal memperbarui pejabat $jabatan"]);
    }
    exit;
}

// ================= AMBIL KONTEN GRID PRODI =================
if(isset($_POST['ambil-tabel-prodi'])){
    $ambil = query("SELECT p.*, d.nama AS nama_kaprodi FROM prodi p LEFT JOIN dosen d ON p.kaprodi = d.nip OR p.kaprodi = d.id ORDER BY p.nama ASC");
    
    ob_start();
    if(mysqli_num_rows($ambil) > 0){
        $colors = [
            ['pr' => '#6366f1', 'b1' => '#4f46e5', 'b2' => '#818cf8', 'text' => 'text-indigo-600', 'code' => 'VOK'],
            ['pr' => '#f43f5e', 'b1' => '#e11d48', 'b2' => '#fb7185', 'text' => 'text-rose-600', 'code' => 'TE'],
            ['pr' => '#14b8a6', 'b1' => '#0d9488', 'b2' => '#2dd4bf', 'text' => 'text-teal-600', 'code' => 'TK'],
            ['pr' => '#eab308', 'b1' => '#d97706', 'b2' => '#fbbf24', 'text' => 'text-amber-600', 'code' => 'PRD']
        ];
        $idx = 0;
        while($data = mysqli_fetch_assoc($ambil)){
            $isAktif = strtolower($data['status']) === 'aktif';
            if($isAktif) {
                $c = $colors[$idx % count($colors)];
                $idx++;
            } else {
                $c = ['pr' => '#94a3b8', 'b1' => '#64748b', 'b2' => '#94a3b8', 'text' => 'text-slate-500', 'code' => 'OFF'];
            }
            $statusLabel = ucfirst($data['status']);
            $kaprodiNama = !empty($data['nama_kaprodi']) ? $data['nama_kaprodi'] : (!empty($data['kaprodi']) ? $data['kaprodi'] : '-');
            ?>
            <article class="prodi-card reveal <?= !$isAktif ? 'opacity-75 grayscale-[30%]' : '' ?>" data-kode="<?= tampil($data['kode']) ?>" data-cari="<?= strtolower(tampil($data['nama']) . ' ' . $kaprodiNama) ?>" style="--pr:<?= $c['pr'] ?>;--b1:<?= $c['b1'] ?>;--b2:<?= $c['b2'] ?>">
                <div class="prodi-banner">
                    <span class="shape" style="width:70px;height:70px;top:-24px;right:-16px"></span>
                    <span class="shape" style="width:40px;height:40px;bottom:-14px;left:52%"></span>
                    <span class="badge-pro bg-white/20 text-white backdrop-blur-sm"><i class="fas fa-circle text-[6px]"></i><?= tampil($statusLabel) ?></span>
                </div>
                <span class="prodi-avatar bg-white <?= $c['text'] ?>"><?= tampil(strtoupper(substr($data['kode'], 0, 3))) ?></span>
                <div class="px-4 pb-4 pt-6">
                    <h3 class="text-sm font-bold leading-snug text-slate-800"><?= tampil($data['nama']) ?></h3>
                    <code class="mt-1 block text-[10px] text-slate-400"><?= tampil($data['kode']) ?></code>
                    <div class="mt-3 flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                        <div class="min-w-0"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Kaprodi</p><p class="truncate text-xs font-semibold text-slate-700"><?= tampil($kaprodiNama) ?></p></div>
                        <span class="badge-pro bg-sky-100 text-sky-700 shrink-0"><?= tampil($data['jenjang']) ?></span>
                    </div>
                    <div class="mt-3 flex items-center justify-end gap-1.5">
                        <button type="button" class="btn-aksi btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700" data-kode="<?= tampil($data['kode']) ?>" data-nama="<?= tampil($data['nama']) ?>"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                        <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600" data-kode="<?= tampil($data['kode']) ?>" data-nama="<?= tampil($data['nama']) ?>" data-kaprodi="<?= tampil($data['kaprodi']) ?>" data-jenjang="<?= tampil($data['jenjang']) ?>" data-status="<?= tampil($data['status']) ?>"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                        <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-kode="<?= tampil($data['kode']) ?>" data-nama="<?= tampil($data['nama']) ?>"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                    </div>
                </div>
            </article>
            <?php
        }
    } else {
        ?>
        <div class="col-span-full py-8 text-center text-slate-400 text-sm">Belum ada data program studi.</div>
        <?php
    }
    $html = ob_get_clean();
    echo json_encode(['status' => true, 'data' => $html]);
    exit;
}

// ================= TAMBAH PRODI =================
if(isset($_POST['tambah-data-prodi'])){
    $kode = bersihkan($_POST['kode'] ?? '');
    $nama = bersihkan($_POST['nama'] ?? '');
    $kaprodi = bersihkan($_POST['kaprodi'] ?? '');
    $jenjang = bersihkan($_POST['jenjang'] ?? '');
    $status = bersihkan($_POST['status'] ?? 'Aktif');

    if(empty($kode) || empty($nama)){
        echo json_encode(['status' => false, 'pesan' => 'Kode dan nama prodi wajib diisi']);
        exit;
    }

    $cek = query("SELECT kode FROM prodi WHERE kode='$kode'");
    if(mysqli_num_rows($cek) > 0){
        echo json_encode(['status' => false, 'pesan' => 'Kode prodi sudah ada']);
        exit;
    }

    $simpan = query("INSERT INTO prodi (kode, nama, kaprodi, jenjang, status) VALUES ('$kode', '$nama', '$kaprodi', '$jenjang', '$status')");

    if($simpan && !empty($kaprodi) && strtolower($status) !== 'tidak aktif'){
        query("UPDATE dosen SET level=5 WHERE id='$kaprodi' OR nip='$kaprodi'");
    }

    if($simpan){
        echo json_encode(['status' => true, 'pesan' => 'Program studi berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menambahkan program studi']);
    }
    exit;
}

// ================= EDIT PRODI =================
if(isset($_POST['edit-data-prodi'])){
    $kode_lama = bersihkan($_POST['kode_lama'] ?? '');
    $kode = bersihkan($_POST['kode'] ?? '');
    $nama = bersihkan($_POST['nama'] ?? '');
    $kaprodi = bersihkan($_POST['kaprodi'] ?? '');
    $jenjang = bersihkan($_POST['jenjang'] ?? '');
    $status = bersihkan($_POST['status'] ?? 'Aktif');

    if(empty($kode) || empty($nama)){
        echo json_encode(['status' => false, 'pesan' => 'Kode dan nama prodi wajib diisi']);
        exit;
    }

    // Ambil kaprodi LAMA sebelum update
    $qLama = query("SELECT kaprodi FROM prodi WHERE kode='$kode_lama' LIMIT 1");
    $lamaRow = mysqli_fetch_assoc($qLama);
    $kaprodiLama = $lamaRow['kaprodi'] ?? '';

    $update = query("UPDATE prodi SET kode='$kode', nama='$nama', kaprodi='$kaprodi', jenjang='$jenjang', status='$status' WHERE kode='$kode_lama'");

    if($update){
        // Reset level kaprodi lama (jika berbeda atau jika prodi dinonaktifkan)
        $isTidakAktif = strtolower($status) === 'tidak aktif';
        if(!empty($kaprodiLama)){
            if($kaprodiLama !== $kaprodi || $isTidakAktif){
                query("UPDATE dosen SET level=1 WHERE id='$kaprodiLama' OR nip='$kaprodiLama'");
            }
        }
        
        // Naikkan level kaprodi BARU hanya jika status prodi bukan "Tidak Aktif"
        if(!empty($kaprodi) && !$isTidakAktif){
            query("UPDATE dosen SET level=5 WHERE id='$kaprodi' OR nip='$kaprodi'");
        }
        
        echo json_encode(['status' => true, 'pesan' => 'Program studi berhasil diperbarui']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui program studi']);
    }
    exit;
}

// ================= HAPUS PRODI =================
if(isset($_POST['hapus-data-prodi'])){
    $kode = bersihkan($_POST['kode'] ?? '');
    
    // Reset kaprodi sebelum dihapus
    $qLama = query("SELECT kaprodi FROM prodi WHERE kode='$kode' LIMIT 1");
    $lamaRow = mysqli_fetch_assoc($qLama);
    if(!empty($lamaRow['kaprodi'])){
        query("UPDATE dosen SET level=1 WHERE id='$lamaRow[kaprodi]' OR nip='$lamaRow[kaprodi]'");
    }

    $hapus = query("DELETE FROM prodi WHERE kode='$kode'");

    if($hapus){
        echo json_encode(['status' => true, 'pesan' => 'Program studi berhasil dihapus']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus program studi']);
    }
    exit;
}
