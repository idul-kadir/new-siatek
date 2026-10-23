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
if (!in_array($userLevel, [5, 6, 9], true)) {
    echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: Anda tidak memiliki izin untuk aksi ini']);
    exit;
}

$isFilterProdi = ($userLevel === 5);
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
                    <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Kategori</th>
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

    $listDosen = [];
    $saranDosen = [];

    try {
        // List dosen aktif sesuai aturan konvensi
        $qDosen = query("SELECT id, nama FROM dosen WHERE LOWER(posisi) = 'dosen' AND (status IS NULL OR LOWER(status) = 'aktif' OR status = '' OR status = '1') ORDER BY nama ASC");
        if (!$qDosen || mysqli_num_rows($qDosen) === 0) {
            $qDosen = query("SELECT id, nama FROM dosen ORDER BY nama ASC");
        }
        if ($qDosen) {
            while ($d = mysqli_fetch_assoc($qDosen)) {
                $listDosen[] = ['id' => $d['id'], 'nama' => $d['nama']];
            }
        }

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
                // Saran: Ambil penguji 1-4 dari Penguji Hasil sebelumnya
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
                }
            }
        } elseif ($sumber === 'kp') {
            if ($jenisTugas === 'penguji') {
                // Saran: Penguji 1 KP disarankan mengacu ke Pembimbing KP sebelumnya
                $pembimbingKpId = '';

                // 1. Cek langsung dari surat_penunjukkan keterangan PEMBIMBING KP
                $qSp = query("SELECT pembimbing1 FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING KP' ORDER BY id DESC LIMIT 1");
                if ($qSp && $rSp = mysqli_fetch_assoc($qSp)) {
                    $pembimbingKpId = $rSp['pembimbing1'] ?? '';
                }

                // 2. Jika belum ketemu, cek dari surat_penunjukkan_pembimbing di tabel kp
                if (empty($pembimbingKpId)) {
                    $qKp = query("SELECT surat_penunjukkan_pembimbing FROM kp WHERE nim = '$nim' LIMIT 1");
                    if ($qKp && $rKp = mysqli_fetch_assoc($qKp)) {
                        $idS = (int)($rKp['surat_penunjukkan_pembimbing'] ?? 0);
                        if ($idS > 0) {
                            $qSp2 = query("SELECT pembimbing1 FROM surat_penunjukkan WHERE id = $idS LIMIT 1");
                            if ($qSp2 && $rSp2 = mysqli_fetch_assoc($qSp2)) {
                                $pembimbingKpId = $rSp2['pembimbing1'] ?? '';
                            }
                        }
                    }
                }

                // Pastikan nilai saran penguji 1 cocok dengan dosen.id (jika nilai tersimpan berupa NIP)
                if (!empty($pembimbingKpId)) {
                    $valSafe = bersihkan($pembimbingKpId);
                    $qCekDosen = query("SELECT id FROM dosen WHERE id = '$valSafe' OR nip = '$valSafe' LIMIT 1");
                    if ($qCekDosen && $rDosen = mysqli_fetch_assoc($qCekDosen)) {
                        $saranDosen['penguji1'] = (string)$rDosen['id'];
                    } else {
                        $saranDosen['penguji1'] = (string)$pembimbingKpId;
                    }
                }
            }
        }
    } catch (\Throwable $e) {
        // Fallback: pastikan list_dosen tetap terisi jika terjadi error
        if (empty($listDosen)) {
            $qFallback = query("SELECT id, nama FROM dosen ORDER BY nama ASC");
            if ($qFallback) {
                while ($df = mysqli_fetch_assoc($qFallback)) {
                    $listDosen[] = ['id' => $df['id'], 'nama' => $df['nama']];
                }
            }
        }
    }

    // Auto-generate nomor surat sesuai jenis penunjukkan
    $jenisSurat = ($jenisTugas === 'pembimbing') ? 'surat penunjukkan pembimbing' : 'surat penunjukkan penguji';
    $noSuratOtomatis = '';
    if (function_exists('get_nomor_surat')) {
        $noSuratOtomatis = get_nomor_surat($jenisSurat);
        if ($noSuratOtomatis === 'Tidak Ditemukan') {
            $noSuratOtomatis = '';
        }
    }

    echo json_encode([
        'status'      => true,
        'list_dosen'  => $listDosen,
        'saran_dosen' => $saranDosen,
        'no_surat'    => $noSuratOtomatis,
        'jenis_surat' => $jenisSurat
    ]);
    exit;
}

// ================= SIMPAN PENUNJUKKAN DOSEN =================
if (isset($_POST['simpan-penunjukkan'])) {
    $nim             = bersihkan($_POST['nim'] ?? '');
    $sumber          = bersihkan($_POST['sumber'] ?? '');
    $jenisTugas      = bersihkan($_POST['jenis_tugas'] ?? '');
    $progresSekarang = (int)($_POST['progres_sekarang'] ?? 0);
    $noSuratTeks     = bersihkan($_POST['no_surat'] ?? '');

    if ($nim === '' || $sumber === '' || $jenisTugas === '') {
        echo json_encode(['status' => false, 'pesan' => 'Data identitas penunjukkan tidak lengkap']);
        exit;
    }

    // Pastikan nomor surat terisi; jika kosong dari form, ambil dari get_nomor_surat($kategori)
    $kategoriNomor = ($jenisTugas === 'pembimbing') ? 'surat penunjukkan pembimbing' : 'surat penunjukkan penguji';
    if ($noSuratTeks === '' || $noSuratTeks === 'Tidak Ditemukan') {
        $noSuratTeks = get_nomor_surat($kategoriNomor);
    }

    if ($noSuratTeks === '' || $noSuratTeks === 'Tidak Ditemukan') {
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

    // Helper ID unik untuk surat_penunjukkan baru (anti-collision)
    $dapatkanIdSuratBaru = function() use ($waktuSistem) {
        $idCandidate = $waktuSistem;
        while (true) {
            $cekSp = query("SELECT id FROM surat_penunjukkan WHERE id = $idCandidate LIMIT 1");
            if (!$cekSp || mysqli_num_rows($cekSp) === 0) {
                break;
            }
            $idCandidate++;
        }
        return $idCandidate;
    };

    try {
        if ($sumber === 'skripsi') {
            if ($jenisTugas === 'pembimbing') {
                $pembimbing1 = bersihkan($_POST['pembimbing1'] ?? '');
                $pembimbing2 = bersihkan($_POST['pembimbing2'] ?? '');

                if ($pembimbing1 === '') {
                    echo json_encode(['status' => false, 'pesan' => 'Dosen Pembimbing 1 wajib dipilih']);
                    exit;
                }

                $vPembimbing1 = ($pembimbing1 !== '') ? "'$pembimbing1'" : "NULL";
                $vPembimbing2 = ($pembimbing2 !== '') ? "'$pembimbing2'" : "NULL";

                // Masukkan teks nomor surat ke tabel nomor_surat, ambil integer ID-nya
                $idNomorSurat = (int)tambah_nomor($noSuratTeks, "Penunjukkan Pembimbing Skripsi - $nim");
                if ($idNomorSurat <= 0) {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal membuat nomor surat penunjukkan pembimbing skripsi']);
                    exit;
                }

                // Cek apakah sudah pernah ada surat penunjukkan pembimbing skripsi sebelumnya
                $qCek = query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING SKRIPSI' ORDER BY id DESC LIMIT 1");
                if ($qCek && ($rCek = mysqli_fetch_assoc($qCek))) {
                    $idSurat = (int)$rCek['id'];
                    $ins = query("UPDATE surat_penunjukkan SET no_surat = $idNomorSurat, pembimbing1 = $vPembimbing1, pembimbing2 = $vPembimbing2, tanggal = '$tanggalSekarang' WHERE id = $idSurat");
                } else {
                    $idSurat = $dapatkanIdSuratBaru();
                    $ins = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, pembimbing1, pembimbing2, tanggal, keterangan) 
                                  VALUES ($idSurat, $idNomorSurat, '$nim', $vPembimbing1, $vPembimbing2, '$tanggalSekarang', 'PEMBIMBING SKRIPSI')");
                }

                if ($ins) {
                    query("UPDATE skripsi SET penunjukkan_pembimbing = '$idSurat' WHERE nim = '$nim'");
                    if ($progresSekarang <= 3) {
                        $waktuProgres = $waktuSistem + 1;
                        query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, 4, '$nim')");
                    }

                    echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan Pembimbing Skripsi berhasil disimpan']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan penunjukkan pembimbing skripsi']);
                }
                exit;

            } elseif ($jenisTugas === 'penguji_hasil') {
                $penguji1 = bersihkan($_POST['penguji1'] ?? '');
                $penguji2 = bersihkan($_POST['penguji2'] ?? '');
                $penguji3 = bersihkan($_POST['penguji3'] ?? '');
                $penguji4 = bersihkan($_POST['penguji4'] ?? '');

                if ($penguji1 === '') {
                    echo json_encode(['status' => false, 'pesan' => 'Dosen Penguji 1 wajib dipilih']);
                    exit;
                }

                $vPenguji1 = ($penguji1 !== '') ? "'$penguji1'" : "NULL";
                $vPenguji2 = ($penguji2 !== '') ? "'$penguji2'" : "NULL";
                $vPenguji3 = ($penguji3 !== '') ? "'$penguji3'" : "NULL";
                $vPenguji4 = ($penguji4 !== '') ? "'$penguji4'" : "NULL";

                $idNomorSurat = (int)tambah_nomor($noSuratTeks, "Penunjukkan Penguji Hasil Skripsi - $nim");
                if ($idNomorSurat <= 0) {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal membuat nomor surat penguji hasil skripsi']);
                    exit;
                }

                $qCek = query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PENGUJI HASIL' ORDER BY id DESC LIMIT 1");
                if ($qCek && ($rCek = mysqli_fetch_assoc($qCek))) {
                    $idSurat = (int)$rCek['id'];
                    $ins = query("UPDATE surat_penunjukkan SET no_surat = $idNomorSurat, penguji1 = $vPenguji1, penguji2 = $vPenguji2, penguji3 = $vPenguji3, penguji4 = $vPenguji4, penguji5 = NULL, tanggal = '$tanggalSekarang' WHERE id = $idSurat");
                } else {
                    $idSurat = $dapatkanIdSuratBaru();
                    $ins = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, penguji1, penguji2, penguji3, penguji4, penguji5, tanggal, keterangan) 
                                  VALUES ($idSurat, $idNomorSurat, '$nim', $vPenguji1, $vPenguji2, $vPenguji3, $vPenguji4, NULL, '$tanggalSekarang', 'PENGUJI HASIL')");
                }

                if ($ins) {
                    query("UPDATE skripsi SET penunjukkan_hasil = '$idSurat' WHERE nim = '$nim'");
                    if ($progresSekarang <= 9) {
                        $waktuProgres = $waktuSistem + 1;
                        query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, 10, '$nim')");
                    }

                    echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan Penguji Hasil berhasil disimpan']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan penunjukkan penguji hasil']);
                }
                exit;

            } elseif ($jenisTugas === 'penguji_tutup') {
                $penguji1 = bersihkan($_POST['penguji1'] ?? '');
                $penguji2 = bersihkan($_POST['penguji2'] ?? '');
                $penguji3 = bersihkan($_POST['penguji3'] ?? '');
                $penguji4 = bersihkan($_POST['penguji4'] ?? '');

                if ($penguji1 === '') {
                    echo json_encode(['status' => false, 'pesan' => 'Dosen Penguji 1 wajib dipilih']);
                    exit;
                }

                $vPenguji1 = ($penguji1 !== '') ? "'$penguji1'" : "NULL";
                $vPenguji2 = ($penguji2 !== '') ? "'$penguji2'" : "NULL";
                $vPenguji3 = ($penguji3 !== '') ? "'$penguji3'" : "NULL";
                $vPenguji4 = ($penguji4 !== '') ? "'$penguji4'" : "NULL";

                $idNomorSurat = (int)tambah_nomor($noSuratTeks, "Penunjukkan Penguji Tutup Skripsi - $nim");
                if ($idNomorSurat <= 0) {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal membuat nomor surat penguji tutup skripsi']);
                    exit;
                }

                $qCek = query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PENGUJI TUTUP' ORDER BY id DESC LIMIT 1");
                if ($qCek && ($rCek = mysqli_fetch_assoc($qCek))) {
                    $idSurat = (int)$rCek['id'];
                    $ins = query("UPDATE surat_penunjukkan SET no_surat = $idNomorSurat, penguji1 = $vPenguji1, penguji2 = $vPenguji2, penguji3 = $vPenguji3, penguji4 = $vPenguji4, penguji5 = NULL, tanggal = '$tanggalSekarang' WHERE id = $idSurat");
                } else {
                    $idSurat = $dapatkanIdSuratBaru();
                    $ins = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, penguji1, penguji2, penguji3, penguji4, penguji5, tanggal, keterangan) 
                                  VALUES ($idSurat, $idNomorSurat, '$nim', $vPenguji1, $vPenguji2, $vPenguji3, $vPenguji4, NULL, '$tanggalSekarang', 'PENGUJI TUTUP')");
                }

                if ($ins) {
                    query("UPDATE skripsi SET penunjukkan_tutup = '$idSurat' WHERE nim = '$nim'");
                    if ($progresSekarang <= 17) {
                        $waktuProgres = $waktuSistem + 1;
                        query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, 18, '$nim')");
                    }

                    echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan Penguji Ujian Tutup berhasil disimpan']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan penunjukkan penguji tutup']);
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

                $idNomorSurat = (int)tambah_nomor($noSuratTeks, "Penunjukkan Pembimbing KP - $nim");
                if ($idNomorSurat <= 0) {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal membuat nomor surat pembimbing KP']);
                    exit;
                }

                $qCek = query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING KP' ORDER BY id DESC LIMIT 1");
                if ($qCek && ($rCek = mysqli_fetch_assoc($qCek))) {
                    $idSurat = (int)$rCek['id'];
                    $ins = query("UPDATE surat_penunjukkan SET no_surat = $idNomorSurat, pembimbing1 = '$pembimbing1', tanggal = '$tanggalSekarang' WHERE id = $idSurat");
                } else {
                    $idSurat = $dapatkanIdSuratBaru();
                    $ins = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, pembimbing1, tanggal, keterangan) 
                                  VALUES ($idSurat, $idNomorSurat, '$nim', '$pembimbing1', '$tanggalSekarang', 'PEMBIMBING KP')");
                }

                if ($ins) {
                    query("UPDATE kp SET surat_penunjukkan_pembimbing = '$idSurat' WHERE nim = '$nim'");
                    if ($progresSekarang <= 6) {
                        $waktuProgres = $waktuSistem + 1;
                        set_progres_kp($nim, 7, $waktuProgres);
                    }

                    echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan Pembimbing KP berhasil disimpan']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan penunjukkan pembimbing KP']);
                }
                exit;

            } elseif ($jenisTugas === 'penguji') {
                $penguji1 = bersihkan($_POST['penguji1'] ?? '');
                $penguji2 = bersihkan($_POST['penguji2'] ?? '');
                $penguji3 = bersihkan($_POST['penguji3'] ?? '');

                if ($penguji1 === '') {
                    echo json_encode(['status' => false, 'pesan' => 'Dosen Penguji 1 Seminar KP wajib dipilih']);
                    exit;
                }

                $vPenguji1 = ($penguji1 !== '') ? "'$penguji1'" : "NULL";
                $vPenguji2 = ($penguji2 !== '') ? "'$penguji2'" : "NULL";
                $vPenguji3 = ($penguji3 !== '') ? "'$penguji3'" : "NULL";

                $idNomorSurat = (int)tambah_nomor($noSuratTeks, "Penunjukkan Penguji Seminar KP - $nim");
                if ($idNomorSurat <= 0) {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal membuat nomor surat penguji seminar KP']);
                    exit;
                }

                $qCek = query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND (keterangan = 'PENGUJI KP' OR keterangan LIKE '%PENGUJI%KP%') ORDER BY id DESC LIMIT 1");
                if ($qCek && ($rCek = mysqli_fetch_assoc($qCek))) {
                    $idSurat = (int)$rCek['id'];
                    $ins = query("UPDATE surat_penunjukkan SET no_surat = $idNomorSurat, penguji1 = $vPenguji1, penguji2 = $vPenguji2, penguji3 = $vPenguji3, tanggal = '$tanggalSekarang' WHERE id = $idSurat");
                } else {
                    $idSurat = $dapatkanIdSuratBaru();
                    $ins = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, penguji1, penguji2, penguji3, tanggal, keterangan) 
                                  VALUES ($idSurat, $idNomorSurat, '$nim', $vPenguji1, $vPenguji2, $vPenguji3, '$tanggalSekarang', 'PENGUJI KP')");
                }

                if ($ins) {
                    query("UPDATE kp SET surat_penunjukkan_penguji = '$idSurat' WHERE nim = '$nim'");

                    echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan Penguji Seminar KP berhasil disimpan']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan penunjukkan penguji seminar KP']);
                }
                exit;
            }
        }

        echo json_encode(['status' => false, 'pesan' => 'Jenis penunjukkan tidak dikenali']);
        exit;

    } catch (\Throwable $e) {
        echo json_encode([
            'status' => false,
            'pesan'  => 'Gagal menyimpan penunjukkan: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ================= CEK BADGE LIVE PENUNJUKKAN =================
if (isset($_POST['cek-badge-penunjukkan'])) {
    if ($isFilterProdi && $userProdi === '') {
        echo json_encode(['status' => true, 'total_menunggu' => 0]);
        exit;
    }
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

