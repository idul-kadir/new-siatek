<?php
/*
    =====================================================
    PROSES PENUNJUKKAN PEMBIMBING & PENGUJI (KAPRODI/ADMIN)
    =====================================================
    Handling AJAX request untuk pengelolaan penunjukkan
    dosen pembimbing dan penguji mahasiswa Skripsi & KP.
    Sesuai konvensi php-native-crud-instructions.md
*/

require_once __DIR__ . "/../../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

// Pastikan user sudah login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']) || !empty($_SESSION['id']);
if (!$sudahLogin) {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
    exit;
}

// Cek hak akses: Level 3 (Operator Prodi), 4 (Operator Jurusan), 5 (Kaprodi), 6, dan 9
$userLevel = (int)($_SESSION['level'] ?? 0);
if (!in_array($userLevel, [3, 4, 5, 6, 9], true)) {
    echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: Anda tidak memiliki izin untuk aksi ini']);
    exit;
}

$isFilterProdi = in_array($userLevel, [3, 5], true);
$userProdi     = $isFilterProdi ? get_prodi_user() : '';

// Lepas session lock segera agar request polling dan AJAX concurrent tidak saling tunggu
session_write_close();

// ================= AMBIL DATA TABEL ANTRIAN MENUNGGU =================
if (isset($_POST['ambil-tabel-antrian'])) {
    $cari     = bersihkan($_POST['cari'] ?? '');
    $kategori = bersihkan($_POST['kategori'] ?? ''); // 'semua', 'skripsi', 'kp'
    $jenis    = bersihkan($_POST['jenis'] ?? '');    // 'semua', 'pembimbing', 'penguji'
    $prodi    = bersihkan($_POST['prodi'] ?? '');

    $filterProdi = $isFilterProdi ? $userProdi : $prodi;

    // Jika operator prodi / kaprodi tapi prodi belum terdaftar di akun
    if ($isFilterProdi && $filterProdi === '') {
        ob_start();
        ?>
        <div class="py-12 text-center">
            <i class="fas fa-exclamation-circle text-4xl text-amber-400"></i>
            <p class="mt-3 font-semibold text-slate-700">Akun Program Studi Belum Terhubung</p>
            <p class="text-xs text-slate-500 mt-1">Akun Anda belum memiliki data Program Studi yang diampu. Hubungi Administrator.</p>
        </div>
        <?php
        $html = ob_get_clean();
        echo json_encode(['status' => true, 'data' => $html, 'total' => 0]);
        exit;
    }

    // Panggil function dari fungsi.php
    try {
        $daftarSemua = ambil_antrian_penunjukkan($filterProdi, $kategori, $cari);
    } catch (\Throwable $e) {
        ob_start();
        ?>
        <div class="py-12 text-center text-rose-500 text-sm">
            <i class="fas fa-exclamation-triangle text-3xl mb-2 text-rose-400"></i>
            <p class="font-semibold">Terjadi kendala saat memuat data antrian</p>
            <p class="text-xs text-slate-500 mt-1"><?= tampil($e->getMessage()) ?></p>
        </div>
        <?php
        $html = ob_get_clean();
        echo json_encode(['status' => false, 'pesan' => $e->getMessage(), 'data' => $html]);
        exit;
    }

    // Filter berdasarkan jenis jika dipilih
    $daftarAntrian = [];
    if (!empty($daftarSemua)) {
        foreach ($daftarSemua as $item) {
            if ($jenis === 'pembimbing' && $item['jenis_tugas'] !== 'pembimbing') {
                continue;
            }
            if ($jenis === 'penguji' && $item['jenis_tugas'] === 'pembimbing') {
                continue;
            }
            $daftarAntrian[] = $item;
        }
    }

    ob_start();
    if (!empty($daftarAntrian)) {
        $no = 1;
        ?>
        <table class="w-full text-left text-sm">
            <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
                <tr>
                    <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">Mahasiswa</th>
                    <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Kategori &amp; Tahapan</th>
                    <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Judul</th>
                    <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Kebutuhan</th>
                    <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            <?php foreach ($daftarAntrian as $row): 
                $namaMhs = !empty($row['nama_mhs']) ? $row['nama_mhs'] : '-';
                $prodiNm = !empty($row['nama_prodi']) ? $row['nama_prodi'] : (!empty($row['prodi']) ? $row['prodi'] : '');
                $nim     = !empty($row['nim']) ? $row['nim'] : '-';
                $badgeBg = ($row['sumber'] === 'skripsi') ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700';
                $badgeTeks = ($row['sumber'] === 'skripsi') ? 'Skripsi' : 'Kerja Praktek';
            ?>
                <tr class="transition hover:bg-orange-50 <?= ($no % 2 === 0) ? 'bg-slate-50/60' : 'bg-white' ?>">
                    <td class="py-4 pl-5 pr-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm"><?= $no ?></span>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-1.5 leading-snug">
                                    <p class="font-semibold text-slate-800"><?= tampil($namaMhs) ?></p>
                                    <?php if ($prodiNm !== ''): ?>
                                        <span class="text-slate-300">&bull;</span>
                                        <span class="text-xs font-medium text-slate-500"><?= tampil($prodiNm) ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="mt-0.5 text-xs text-slate-500 leading-snug font-mono">
                                    <?= tampil($nim) ?>
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $badgeBg ?>">
                            <?= $badgeTeks ?>
                        </span>
                        <p class="mt-1 text-xs text-slate-500 leading-snug"><?= tampil($row['tahapan_label']) ?></p>
                    </td>
                    <td class="px-4 py-4 text-slate-600">
                        <p class="text-sm leading-snug"><?= tampil($row['judul'] ?: '-') ?></p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-700">
                            <?= tampil($row['kebutuhan_label']) ?>
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1.5">
                            <button type="button" 
                                    class="btnTentukanDosen btn-circle bg-orange-500 hover:bg-orange-600 text-white shadow-sm"
                                    data-sumber="<?= tampil($row['sumber']) ?>"
                                    data-jenis-tugas="<?= tampil($row['jenis_tugas']) ?>"
                                    data-nim="<?= tampil($nim) ?>"
                                    data-nama="<?= tampil($namaMhs) ?>"
                                    data-prodi="<?= tampil($prodiNm) ?>"
                                    data-progres="<?= (int)$row['progres'] ?>"
                                    data-judul="<?= tampil($row['judul']) ?>">
                                <i class="fas fa-user-edit text-xs"></i><span class="tip">Tentukan Dosen</span>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php $no++; endforeach; ?>
            </tbody>
        </table>
        <?php
    } else {
        ?>
        <div class="py-16 text-center">
            <i class="fas fa-user-check text-4xl text-slate-300"></i>
            <p class="mt-3 font-medium text-slate-500">Semua Penunjukkan Telah Selesai</p>
            <p class="text-xs text-slate-400">Tidak ada mahasiswa yang menunggu penetapan pembimbing atau penguji saat ini.</p>
        </div>
        <?php
    }
    $html = ob_get_clean();
    echo json_encode(['status' => true, 'data' => $html, 'total' => count($daftarAntrian)]);
    exit;
}

// ================= AMBIL DETAIL FORM MODAL PENUNJUKKAN =================
if (isset($_POST['ambil-detail-penunjukkan'])) {
    $nim        = bersihkan($_POST['nim'] ?? '');
    $sumber     = bersihkan($_POST['sumber'] ?? '');
    $jenisTugas = bersihkan($_POST['jenis_tugas'] ?? '');

    // Operator Prodi & Kaprodi hanya boleh mengakses mahasiswa dari prodi yang diampu
    if ($isFilterProdi && $userProdi !== '') {
        $qMhs = query("SELECT prodi FROM mahasiswa WHERE nim = '$nim' LIMIT 1");
        $rMhs = $qMhs ? mysqli_fetch_assoc($qMhs) : null;
        if (!$rMhs || strtolower(trim((string)$rMhs['prodi'])) !== strtolower(trim($userProdi))) {
            echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: Mahasiswa di luar program studi Anda']);
            exit;
        }
    }

    // List dosen aktif sesuai aturan konvensi
    $listDosen = [];
    $qDosen = query("SELECT id, nama FROM dosen WHERE LOWER(posisi) = 'dosen' AND (status IS NULL OR LOWER(status) = 'aktif' OR status = '' OR status = '1') ORDER BY nama ASC");
    if (!$qDosen || mysqli_num_rows($qDosen) === 0) {
        $qDosen = query("SELECT id, nama FROM dosen ORDER BY nama ASC");
    }
    if ($qDosen) {
        while ($d = mysqli_fetch_assoc($qDosen)) {
            $listDosen[] = ['id' => $d['id'], 'nama' => $d['nama']];
        }
    }

    $saranDosen = [];
    if ($sumber === 'skripsi') {
        if ($jenisTugas === 'penguji_hasil') {
            // Saran: Penguji 1 & 2 mengacu ke Pembimbing 1 & 2 sebelumnya
            $qBimb = query("SELECT sp.pembimbing1, sp.pembimbing2 FROM skripsi s JOIN surat_penunjukkan sp ON s.penunjukkan_pembimbing = sp.id WHERE s.nim = '$nim' LIMIT 1");
            $rBimb = $qBimb ? mysqli_fetch_assoc($qBimb) : null;
            if (!$rBimb) {
                $qBimb2 = query("SELECT pembimbing1, pembimbing2 FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING SKRIPSI' ORDER BY id DESC LIMIT 1");
                $rBimb = $qBimb2 ? mysqli_fetch_assoc($qBimb2) : null;
            }
            if ($rBimb) {
                $saranDosen['penguji1'] = (string)($rBimb['pembimbing1'] ?? '');
                $saranDosen['penguji2'] = (string)($rBimb['pembimbing2'] ?? '');
            }
        } elseif ($jenisTugas === 'penguji_tutup') {
            // Saran: Ambil penguji 1-5 dari Penguji Hasil sebelumnya
            $qHasil = query("SELECT sp.penguji1, sp.penguji2, sp.penguji3, sp.penguji4, sp.penguji5 FROM skripsi s JOIN surat_penunjukkan sp ON s.penunjukkan_hasil = sp.id WHERE s.nim = '$nim' LIMIT 1");
            $rHasil = $qHasil ? mysqli_fetch_assoc($qHasil) : null;
            if (!$rHasil) {
                $qHasil2 = query("SELECT penguji1, penguji2, penguji3, penguji4, penguji5 FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PENGUJI HASIL' ORDER BY id DESC LIMIT 1");
                $rHasil = $qHasil2 ? mysqli_fetch_assoc($qHasil2) : null;
            }
            if ($rHasil) {
                $saranDosen['penguji1'] = (string)($rHasil['penguji1'] ?? '');
                $saranDosen['penguji2'] = (string)($rHasil['penguji2'] ?? '');
                $saranDosen['penguji3'] = (string)($rHasil['penguji3'] ?? '');
                $saranDosen['penguji4'] = (string)($rHasil['penguji4'] ?? '');
                $saranDosen['penguji5'] = (string)($rHasil['penguji5'] ?? '');
            }
        }
    } elseif ($sumber === 'kp') {
        if ($jenisTugas === 'penguji') {
            // Saran: Penguji 1 KP mengacu ke Pembimbing KP sebelumnya
            $qBimbKp = query("SELECT sp.pembimbing1 FROM surat_penunjukkan sp WHERE sp.nim = '$nim' AND sp.keterangan = 'PEMBIMBING KP' ORDER BY sp.id DESC LIMIT 1");
            $rBimbKp = $qBimbKp ? mysqli_fetch_assoc($qBimbKp) : null;
            if (!$rBimbKp) {
                $qBimbKp2 = query("SELECT pembimbing FROM kp WHERE nim = '$nim' LIMIT 1");
                $rBimbKp = $qBimbKp2 ? mysqli_fetch_assoc($qBimbKp2) : null;
                if ($rBimbKp && !empty($rBimbKp['pembimbing'])) {
                    $saranDosen['penguji1'] = (string)$rBimbKp['pembimbing'];
                }
            } else {
                $saranDosen['penguji1'] = (string)($rBimbKp['pembimbing1'] ?? '');
            }
        }
    }

    echo json_encode([
        'status'      => true,
        'list_dosen'  => $listDosen,
        'saran_dosen' => $saranDosen
    ]);
    exit;
}

// ================= SIMPAN PENUNJUKKAN DOSEN =================
if (isset($_POST['simpan-penunjukkan'])) {
    $nim             = bersihkan($_POST['nim'] ?? '');
    $sumber          = bersihkan($_POST['sumber'] ?? '');
    $jenisTugas      = bersihkan($_POST['jenis_tugas'] ?? '');
    $progresSekarang = (int)($_POST['progres_sekarang'] ?? 0);
    $noSurat         = bersihkan($_POST['no_surat'] ?? '');

    if ($nim === '' || $sumber === '' || $jenisTugas === '') {
        echo json_encode(['status' => false, 'pesan' => 'Data identitas penunjukkan tidak lengkap']);
        exit;
    }

    if ($noSurat === '') {
        echo json_encode(['status' => false, 'pesan' => 'Nomor surat penunjukkan wajib diisi']);
        exit;
    }

    // Operator Prodi & Kaprodi guard
    if ($isFilterProdi && $userProdi !== '') {
        $qMhs = query("SELECT prodi FROM mahasiswa WHERE nim = '$nim' LIMIT 1");
        $rMhs = $qMhs ? mysqli_fetch_assoc($qMhs) : null;
        if (!$rMhs || strtolower(trim((string)$rMhs['prodi'])) !== strtolower(trim($userProdi))) {
            echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: Mahasiswa di luar program studi Anda']);
            exit;
        }
    }

    $waktuSistem     = time();
    $tanggalSekarang = date('Y-m-d');
    $idSurat         = $waktuSistem;

    if ($sumber === 'skripsi') {
        if ($jenisTugas === 'pembimbing') {
            $pembimbing1 = bersihkan($_POST['pembimbing1'] ?? '');
            $pembimbing2 = bersihkan($_POST['pembimbing2'] ?? '');

            if ($pembimbing1 === '') {
                echo json_encode(['status' => false, 'pesan' => 'Dosen Pembimbing 1 wajib dipilih']);
                exit;
            }

            // Simpan ke surat_penunjukkan
            $ins = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, pembimbing1, pembimbing2, tanggal, keterangan) 
                          VALUES ($idSurat, '$noSurat', '$nim', '$pembimbing1', '$pembimbing2', '$tanggalSekarang', 'PEMBIMBING SKRIPSI')");

            if ($ins) {
                query("UPDATE skripsi SET penunjukkan_pembimbing = '$idSurat' WHERE nim = '$nim'");
                $progresBaru = 4;
                $waktuProgres = $waktuSistem + 1;
                query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, $progresBaru, '$nim')");

                echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan Pembimbing Skripsi berhasil dibuat']);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan penunjukkan pembimbing: ' . mysqli_error($GLOBALS['koneksi'])]);
            }
            exit;

        } elseif ($jenisTugas === 'penguji_hasil') {
            $penguji1 = bersihkan($_POST['penguji1'] ?? '');
            $penguji2 = bersihkan($_POST['penguji2'] ?? '');
            $penguji3 = bersihkan($_POST['penguji3'] ?? '');
            $penguji4 = bersihkan($_POST['penguji4'] ?? '');
            $penguji5 = bersihkan($_POST['penguji5'] ?? '');

            if ($penguji1 === '') {
                echo json_encode(['status' => false, 'pesan' => 'Dosen Penguji 1 wajib dipilih']);
                exit;
            }

            $ins = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, penguji1, penguji2, penguji3, penguji4, penguji5, tanggal, keterangan) 
                          VALUES ($idSurat, '$noSurat', '$nim', '$penguji1', '$penguji2', '$penguji3', '$penguji4', '$penguji5', '$tanggalSekarang', 'PENGUJI HASIL')");

            if ($ins) {
                query("UPDATE skripsi SET penunjukkan_hasil = '$idSurat' WHERE nim = '$nim'");
                $progresBaru = 10;
                $waktuProgres = $waktuSistem + 1;
                query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, $progresBaru, '$nim')");

                echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan Penguji Hasil berhasil dibuat']);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan penunjukkan penguji hasil: ' . mysqli_error($GLOBALS['koneksi'])]);
            }
            exit;

        } elseif ($jenisTugas === 'penguji_tutup') {
            $penguji1 = bersihkan($_POST['penguji1'] ?? '');
            $penguji2 = bersihkan($_POST['penguji2'] ?? '');
            $penguji3 = bersihkan($_POST['penguji3'] ?? '');
            $penguji4 = bersihkan($_POST['penguji4'] ?? '');
            $penguji5 = bersihkan($_POST['penguji5'] ?? '');

            if ($penguji1 === '') {
                echo json_encode(['status' => false, 'pesan' => 'Dosen Penguji 1 wajib dipilih']);
                exit;
            }

            $ins = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, penguji1, penguji2, penguji3, penguji4, penguji5, tanggal, keterangan) 
                          VALUES ($idSurat, '$noSurat', '$nim', '$penguji1', '$penguji2', '$penguji3', '$penguji4', '$penguji5', '$tanggalSekarang', 'PENGUJI TUTUP')");

            if ($ins) {
                query("UPDATE skripsi SET penunjukkan_tutup = '$idSurat' WHERE nim = '$nim'");
                $progresBaru = 18;
                $waktuProgres = $waktuSistem + 1;
                query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, $progresBaru, '$nim')");

                echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan Penguji Ujian Tutup berhasil dibuat']);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan penunjukkan penguji tutup: ' . mysqli_error($GLOBALS['koneksi'])]);
            }
            exit;
        }

    } elseif ($sumber === 'kp') {
        if ($jenisTugas === 'pembimbing') {
            $pembimbing1 = bersihkan($_POST['pembimbing1'] ?? '');

            if ($pembimbing1 === '') {
                echo json_encode(['status' => false, 'pesan' => 'Dosen Pembimbing KP wajib dipilih']);
                exit;
            }

            $ins = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, pembimbing1, tanggal, keterangan) 
                          VALUES ($idSurat, '$noSurat', '$nim', '$pembimbing1', '$tanggalSekarang', 'PEMBIMBING KP')");

            if ($ins) {
                query("UPDATE kp SET surat_penunjukkan_pembimbing = '$idSurat' WHERE nim = '$nim'");
                $progresBaru = 7;
                $waktuProgres = $waktuSistem + 1;
                set_progres_kp($nim, $progresBaru, $waktuProgres);

                echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan Pembimbing KP berhasil dibuat']);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan penunjukkan pembimbing KP: ' . mysqli_error($GLOBALS['koneksi'])]);
            }
            exit;

        } elseif ($jenisTugas === 'penguji') {
            $penguji1 = bersihkan($_POST['penguji1'] ?? '');
            $penguji2 = bersihkan($_POST['penguji2'] ?? '');

            if ($penguji1 === '') {
                echo json_encode(['status' => false, 'pesan' => 'Dosen Penguji 1 Seminar KP wajib dipilih']);
                exit;
            }

            $ins = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, penguji1, penguji2, tanggal, keterangan) 
                          VALUES ($idSurat, '$noSurat', '$nim', '$penguji1', '$penguji2', '$tanggalSekarang', 'PENGUJI KP')");

            if ($ins) {
                query("UPDATE kp SET surat_penunjukkan_penguji = '$idSurat' WHERE nim = '$nim'");
                $progresBaru = 13;
                $waktuProgres = $waktuSistem + 1;
                set_progres_kp($nim, $progresBaru, $waktuProgres);

                echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan Penguji Seminar KP berhasil dibuat']);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan penunjukkan penguji seminar KP: ' . mysqli_error($GLOBALS['koneksi'])]);
            }
            exit;
        }
    }

    echo json_encode(['status' => false, 'pesan' => 'Jenis penunjukkan tidak dikenali']);
    exit;
}

// ================= CEK BADGE LIVE PENUNJUKKAN =================
if (isset($_POST['cek-badge-penunjukkan'])) {
    $filterProdi = $isFilterProdi ? $userProdi : '';
    try {
        $antrian = ambil_antrian_penunjukkan($filterProdi, 'semua', '');
        echo json_encode([
            'status'         => true,
            'total_menunggu' => count($antrian)
        ]);
    } catch (\Throwable $e) {
        echo json_encode([
            'status'         => false,
            'total_menunggu' => 0
        ]);
    }
    exit;
}

