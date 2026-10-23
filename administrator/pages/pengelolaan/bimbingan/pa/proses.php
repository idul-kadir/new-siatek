<?php
/**
 * PROSES BIMBINGAN PENASIHAT AKADEMIK (PA) - Konvensi php-native-crud.
 * Menangani AJAX request untuk halaman Bimbingan PA:
 * - ambil-tabel-bimbingan-pa : Merender baris tabel mahasiswa bimbingan PA + status & isian terakhir
 * - ambil-ringkasan-statistik: JSON metrik (Total bimbingan, aktif, cuti, tidak aktif/perlu perhatian)
 * - ambil-riwayat-pa        : Merender drawer offcanvas timeline riwayat pengisian Buku PA per semester
 * - ambil-detail-isian      : JSON satu entri isian_pa untuk modal edit
 * - simpan-isian-pa         : Simpan entri baru atau perbarui entri Buku PA di tabel isian_pa
 * - hapus-isian-pa          : Hapus entri tertentu di isian_pa
 *
 * Hak Akses: Level 1 (Dosen), 5 (Kaprodi), 6 (Kajur/Sekjur), 9 (Superadmin).
 */

require_once __DIR__ . '/../../../../../fungsi.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Validasi Otorisasi Pengguna
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
$userLevel  = (int)($_SESSION['level'] ?? 0);

if (!$sudahLogin || !in_array($userLevel, [1, 5, 6, 9], true)) {
    http_response_code(403);
    echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: Anda tidak memiliki izin untuk mengelola data bimbingan PA.']);
    exit;
}

$idDosenSession  = bersihkan((string)($_SESSION['id'] ?? ''));
$nipDosenSession = bersihkan((string)($_SESSION['nip'] ?? $_SESSION['user_id'] ?? ''));
$userProdi       = function_exists('get_prodi_user') ? get_prodi_user() : trim((string)($_SESSION['prodi'] ?? ''));

// Sinkronisasi data identitas dosen yang login (id dan nip)
$identitasDosen = array_values(array_unique(array_filter([$idDosenSession, $nipDosenSession])));
if (!empty($identitasDosen)) {
    $inDsnTemp = "'" . implode("','", array_map('bersihkan', $identitasDosen)) . "'";
    $qDsnLogin = query("SELECT id, nip FROM dosen WHERE id IN ($inDsnTemp) OR nip IN ($inDsnTemp) LIMIT 1");
    if ($qDsnLogin && ($rDL = mysqli_fetch_assoc($qDsnLogin))) {
        $identitasDosen[] = (string)$rDL['id'];
        $identitasDosen[] = (string)$rDL['nip'];
        $identitasDosen = array_values(array_unique(array_filter($identitasDosen)));
        $idDosenSession = (string)$rDL['id'];
        $nipDosenSession = (string)$rDL['nip'];
    }
}

/**
 * Helper: Bangun WHERE clause untuk query mahasiswa bimbingan PA
 */
function build_where_pa($pembFilter, $prodiFilter, $angkatanFilter, $statusFilter, $cariFilter, $userLevel, $userProdi, $identitasDosen) {
    $where = ["1=1"];

    // 1. Filter Pembimbing / Hak Akses
    if ($userLevel === 1) {
        // Dosen biasa WAJIB hanya melihat mahasiswa bimbingan PA dirinya
        if (!empty($identitasDosen)) {
            $inDsn = "'" . implode("','", array_map('bersihkan', $identitasDosen)) . "'";
            $where[] = "m.dosen_pa IN ($inDsn)";
        } else {
            $where[] = "1=0";
        }
    } elseif ($pembFilter === 'saya') {
        // Kaprodi / Kajur / Sekjur / Admin saat mode "Bimbingan Saya"
        if (!empty($identitasDosen)) {
            $inDsn = "'" . implode("','", array_map('bersihkan', $identitasDosen)) . "'";
            $where[] = "m.dosen_pa IN ($inDsn)";
        } else {
            $where[] = "1=0";
        }
    }

    // 2. Filter Program Studi
    if ($userLevel === 5 && $pembFilter !== 'saya') {
        // Kaprodi saat melihat semua bimbingan terkunci ke prodinya
        if (!empty($userProdi)) {
            $kd = bersihkan($userProdi);
            $where[] = "(m.prodi = '$kd' OR m.prodi = (SELECT nama FROM prodi WHERE kode = '$kd' LIMIT 1) OR m.prodi = (SELECT kode FROM prodi WHERE nama = '$kd' LIMIT 1))";
        }
    } elseif (!empty($prodiFilter)) {
        $pr = bersihkan($prodiFilter);
        $where[] = "(m.prodi = '$pr' OR m.prodi = (SELECT nama FROM prodi WHERE kode = '$pr' LIMIT 1) OR m.prodi = (SELECT kode FROM prodi WHERE nama = '$pr' LIMIT 1))";
    }

    // 3. Filter Angkatan
    // Ketentuan: Angkatan yang dihitung cuma sampai tahun - 7 (khusus filter dalam mode semua mahasiswa kecuali superadmin)
    if ($pembFilter === 'semua' && $userLevel !== 9) {
        $tahunBatas = (int)date('Y') - 7;
        $where[] = "CAST(m.angkatan AS UNSIGNED) >= $tahunBatas";
    }

    if (!empty($angkatanFilter)) {
        $ang = (int)$angkatanFilter;
        $where[] = "m.angkatan = '$ang'";
    }

    // 4. Filter Status
    if (!empty($statusFilter)) {
        if ($statusFilter === 'belum_isi') {
            $where[] = "last_pa.status IS NULL";
        } else {
            $st = bersihkan($statusFilter);
            $where[] = "last_pa.status = '$st'";
        }
    }

    // 5. Filter Pencarian Cepat
    if (!empty($cariFilter)) {
        $c = bersihkan($cariFilter);
        $where[] = "(m.nim LIKE '%$c%' OR m.nama LIKE '%$c%' OR d.nama LIKE '%$c%')";
    }

    return implode(' AND ', $where);
}

// ================= 1. AMBIL RINGKASAN STATISTIK PA =================
if (isset($_POST['ambil-ringkasan-statistik'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    $prodiFilter    = bersihkan($_POST['prodi'] ?? '');
    $pembFilter     = bersihkan($_POST['pembimbing'] ?? ($userLevel === 9 ? '' : 'saya'));
    $angkatanFilter = bersihkan($_POST['angkatan'] ?? '');
    $statusFilter   = bersihkan($_POST['status'] ?? '');
    $cariFilter     = bersihkan($_POST['cari'] ?? '');

    $whereSql = build_where_pa($pembFilter, $prodiFilter, $angkatanFilter, $statusFilter, $cariFilter, $userLevel, $userProdi, $identitasDosen);

    // Subquery untuk mendapatkan status isian_pa terbaru per mahasiswa
    $subQueryLastPa = "
        SELECT p1.*
        FROM isian_pa p1
        INNER JOIN (
            SELECT nim, MAX(id) as max_id
            FROM isian_pa
            GROUP BY nim
        ) p2 ON p1.id = p2.max_id
    ";

    $sql = "
        SELECT 
            COUNT(*) AS total_mhs,
            SUM(CASE WHEN last_pa.status = 'Terdaftar - Aktif' THEN 1 ELSE 0 END) AS total_aktif,
            SUM(CASE WHEN last_pa.status = 'Cuti' THEN 1 ELSE 0 END) AS total_cuti,
            SUM(CASE WHEN last_pa.status IN ('Terdaftar - Tidak Aktif', 'Tidak Aktif') THEN 1 ELSE 0 END) AS total_tidak_aktif,
            SUM(CASE WHEN last_pa.status IS NULL THEN 1 ELSE 0 END) AS total_belum_isi
        FROM mahasiswa m
        LEFT JOIN dosen d ON (m.dosen_pa = d.id OR m.dosen_pa = d.nip)
        LEFT JOIN ($subQueryLastPa) last_pa ON m.nim = last_pa.nim
        WHERE $whereSql
    ";

    $q = query($sql);
    $data = [
        'total'       => 0,
        'aktif'       => 0,
        'cuti'        => 0,
        'tidak_aktif' => 0,
        'belum_isi'   => 0
    ];

    if ($q && ($row = mysqli_fetch_assoc($q))) {
        $data['total']       = (int)($row['total_mhs'] ?? 0);
        $data['aktif']       = (int)($row['total_aktif'] ?? 0);
        $data['cuti']        = (int)($row['total_cuti'] ?? 0);
        $data['tidak_aktif'] = (int)($row['total_tidak_aktif'] ?? 0);
        $data['belum_isi']   = (int)($row['total_belum_isi'] ?? 0);
    }

    echo json_encode(['status' => true, 'data' => $data]);
    exit;
}

// ================= 2. AMBIL TABEL DATA BIMBINGAN PA =================
if (isset($_POST['ambil-tabel-bimbingan-pa'])) {
    $prodiFilter    = bersihkan($_POST['prodi'] ?? '');
    $pembFilter     = bersihkan($_POST['pembimbing'] ?? ($userLevel === 9 ? '' : 'saya'));
    $angkatanFilter = bersihkan($_POST['angkatan'] ?? '');
    $statusFilter   = bersihkan($_POST['status'] ?? '');
    $cariFilter     = bersihkan($_POST['cari'] ?? '');

    $page  = max(1, (int)($_POST['page'] ?? 1));
    $limit = max(5, min(100, (int)($_POST['limit'] ?? 15)));
    $offset = ($page - 1) * $limit;

    $whereSql = build_where_pa($pembFilter, $prodiFilter, $angkatanFilter, $statusFilter, $cariFilter, $userLevel, $userProdi, $identitasDosen);

    $subQueryLastPa = "
        SELECT p1.*
        FROM isian_pa p1
        INNER JOIN (
            SELECT nim, MAX(id) as max_id
            FROM isian_pa
            GROUP BY nim
        ) p2 ON p1.id = p2.max_id
    ";

    // Hitung total data
    $qCount = query("
        SELECT COUNT(*) AS total
        FROM mahasiswa m
        LEFT JOIN dosen d ON (m.dosen_pa = d.id OR m.dosen_pa = d.nip)
        LEFT JOIN ($subQueryLastPa) last_pa ON m.nim = last_pa.nim
        WHERE $whereSql
    ");
    $totalData = ($qCount && ($rc = mysqli_fetch_assoc($qCount))) ? (int)$rc['total'] : 0;
    $totalPages = max(1, (int)ceil($totalData / $limit));

    // Ambil data halaman aktif
    $sqlData = "
        SELECT 
            m.nim,
            m.nama,
            m.prodi,
            p.nama AS nama_prodi,
            m.angkatan,
            m.dosen_pa,
            d.nama AS nama_dosen_pa,
            d.nip AS nip_dosen_pa,
            d.id AS id_dosen_pa,
            last_pa.id AS id_isian_terakhir,
            last_pa.semester AS semester_terakhir,
            last_pa.ips AS ips_terakhir,
            last_pa.ipk AS ipk_terakhir,
            last_pa.sks AS sks_terakhir,
            last_pa.status AS status_terakhir,
            last_pa.catatan AS catatan_terakhir,
            last_pa.tanggal AS tanggal_terakhir
        FROM mahasiswa m
        LEFT JOIN prodi p ON (m.prodi = p.kode OR m.prodi = p.nama)
        LEFT JOIN dosen d ON (m.dosen_pa = d.id OR m.dosen_pa = d.nip)
        LEFT JOIN ($subQueryLastPa) last_pa ON m.nim = last_pa.nim
        WHERE $whereSql
        ORDER BY m.angkatan DESC, m.nim ASC
        LIMIT $offset, $limit
    ";
    $qData = query($sqlData);

    if (!$qData || mysqli_num_rows($qData) === 0): ?>
        <tr>
            <td colspan="7" class="py-12 text-center text-slate-400">
                <i class="fas fa-folder-open text-3xl mb-2 text-slate-300"></i>
                <p class="text-sm font-medium">Tidak ada data mahasiswa bimbingan PA yang sesuai dengan filter.</p>
                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian, filter status, atau pilihan pembimbing.</p>
            </td>
        </tr>
    <?php else:
        $no = $offset + 1;
        while ($row = mysqli_fetch_assoc($qData)):
            $nimMhs        = tampil($row['nim']);
            $namaMhs       = tampil(ucwords(strtolower(trim($row['nama']))));
            $prodiMhs      = !empty($row['nama_prodi']) ? tampil($row['nama_prodi']) : (!empty($row['prodi']) ? tampil($row['prodi']) : '-');
            $angkatanMhs   = tampil($row['angkatan'] ?? '-');
            $namaPa        = !empty($row['nama_dosen_pa']) ? tampil($row['nama_dosen_pa']) : (!empty($row['dosen_pa']) ? tampil($row['dosen_pa']) : '<span class="text-rose-500 italic">Belum ditentukan</span>');
            
            // Cek apakah mahasiswa ini dibimbing langsung oleh dosen yang sedang login
            $isBimbinganSaya = false;
            if (!empty($identitasDosen) && in_array((string)$row['dosen_pa'], $identitasDosen, true)) {
                $isBimbinganSaya = true;
            } elseif (!empty($row['nip_dosen_pa']) && in_array((string)$row['nip_dosen_pa'], $identitasDosen, true)) {
                $isBimbinganSaya = true;
            } elseif (!empty($row['id_dosen_pa']) && in_array((string)$row['id_dosen_pa'], $identitasDosen, true)) {
                $isBimbinganSaya = true;
            }

            // Status terkini
            $st = $row['status_terakhir'] ?? '';
            $badgeStatusClass = 'bg-slate-100 text-slate-600 border-slate-200';
            $badgeStatusText  = 'Belum Ada Isian';
            $tooltipStatus    = 'Belum ada rekaman evaluasi Buku PA untuk mahasiswa ini.';

            if ($st === 'Terdaftar - Aktif') {
                $badgeStatusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                $badgeStatusText  = 'Terdaftar - Aktif';
                $tooltipStatus    = 'Mahasiswa terdaftar dalam sistem kampus (SIAT/SIAKAD) dan aktif dalam kegiatan perkuliahan.';
            } elseif ($st === 'Terdaftar - Tidak Aktif') {
                $badgeStatusClass = 'bg-amber-50 text-amber-700 border-amber-200';
                $badgeStatusText  = 'Terdaftar - Tidak Aktif';
                $tooltipStatus    = 'Mahasiswa terdaftar namun tidak aktif dalam kegiatan perkuliahan.';
            } elseif ($st === 'Cuti') {
                $badgeStatusClass = 'bg-sky-50 text-sky-700 border-sky-200';
                $badgeStatusText  = 'Cuti';
                $tooltipStatus    = 'Mahasiswa yang sedang cuti.';
            } elseif ($st === 'Tidak Aktif') {
                $badgeStatusClass = 'bg-rose-50 text-rose-700 border-rose-200';
                $badgeStatusText  = 'Tidak Aktif';
                $tooltipStatus    = 'Mahasiswa yang dalam sistem kampus sudah tidak aktif / Mengundurkan diri.';
            }

            // Progres akademik terakhir
            $semTxt = !empty($row['semester_terakhir']) ? 'Sem. ' . (int)$row['semester_terakhir'] : '-';
            $ipkVal = isset($row['ipk_terakhir']) && $row['ipk_terakhir'] !== null ? number_format((float)$row['ipk_terakhir'], 2) : '-';
            $ipsVal = isset($row['ips_terakhir']) && $row['ips_terakhir'] !== null ? number_format((float)$row['ips_terakhir'], 2) : '-';
            $sksVal = isset($row['sks_terakhir']) && $row['sks_terakhir'] !== null ? (int)$row['sks_terakhir'] . ' SKS' : '-';
            $tglVal = !empty($row['tanggal_terakhir']) ? tgl_indo($row['tanggal_terakhir']) : '-';
            ?>
            <tr class="hover:bg-slate-50/80 transition-colors border-b border-slate-100">
                <!-- 1. Nomor urut (Orange Badge) -->
                <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-orange-500 text-white font-bold text-xs shadow-xs">
                        <?= $no++ ?>
                    </span>
                </td>

                <!-- 2. Data Mahasiswa -->
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 font-bold text-xs">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-slate-800 text-[13px] leading-snug">
                                <?= $namaMhs ?>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="font-mono text-xs text-slate-500 font-medium"><?= $nimMhs ?></span>
                                <span class="inline-flex items-center rounded px-1.5 py-0.2 bg-slate-100 text-slate-600 text-[10px] font-medium border border-slate-200">
                                    Angk. <?= $angkatanMhs ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </td>

                <!-- 3. Program Studi -->
                <td class="px-4 py-3">
                    <span class="text-xs font-medium text-slate-700"><?= $prodiMhs ?></span>
                </td>

                <!-- 4. Dosen PA -->
                <td class="px-4 py-3">
                    <div class="text-xs font-medium text-slate-800 flex items-center gap-1.5">
                        <span><?= $namaPa ?></span>
                        <?php if ($isBimbinganSaya): ?>
                            <span class="inline-flex items-center text-amber-500 text-xs" title="Mahasiswa Bimbingan Anda">
                                <i class="fas fa-star"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($row['nip_dosen_pa'])): ?>
                        <div class="text-[11px] font-mono text-slate-400 mt-0.5">NIP: <?= tampil($row['nip_dosen_pa']) ?></div>
                    <?php endif; ?>
                </td>

                <!-- 5. Evaluasi Akademik Terakhir -->
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold">
                            <?= $semTxt ?>
                        </span>
                        <div class="text-xs">
                            <span class="text-slate-500">IPK:</span> <strong class="text-slate-800"><?= $ipkVal ?></strong>
                            <span class="text-slate-300 mx-1">|</span>
                            <span class="text-slate-500">IPS:</span> <strong class="text-slate-800"><?= $ipsVal ?></strong>
                            <span class="text-slate-300 mx-1">|</span>
                            <span class="text-slate-600 font-medium"><?= $sksVal ?></span>
                        </div>
                    </div>
                    <?php if (!empty($row['catatan_terakhir'])): ?>
                        <?php 
                        $plainCatatan = trim(strip_tags($row['catatan_terakhir']));
                        ?>
                        <?php if ($plainCatatan !== ''): ?>
                        <p class="text-[11px] text-slate-500 italic mt-1 line-clamp-1" title="<?= tampil($plainCatatan) ?>">
                            <i class="far fa-comment-dots text-slate-400 mr-1"></i>"<?= tampil($plainCatatan) ?>"
                        </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>

                <!-- 6. Status Akademik Terkini (dengan Tooltip Panduan) -->
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-medium <?= $badgeStatusClass ?> cursor-help"
                          title="<?= $tooltipStatus ?>">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        <?= $badgeStatusText ?>
                    </span>
                    <div class="text-[10px] text-slate-400 mt-0.5 pl-1">Tgl: <?= $tglVal ?></div>
                </td>

                <!-- 7. Aksi -->
                <td class="px-4 py-3 text-center whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                        <?php 
                        // Hak akses tombol isian bimbingan:
                        // Level 9 (Superadmin) selalu berwenang.
                        // Dalam mode 'semua' (untuk Kajur, Sekjur, atau Kaprodi), tidak boleh ada tombol isian bimbingan.
                        // Hanya dosen PA bersangkutan (mode 'saya' && $isBimbinganSaya) yang berwenang mengisi.
                        $canIsiBimbingan = ($userLevel === 9) || ($pembFilter !== 'semua' && $isBimbinganSaya);
                        ?>
                        <?php if ($canIsiBimbingan): ?>
                        <!-- Tombol Isi Buku PA (Hanya Dosen PA atau Level 9) -->
                        <button type="button" 
                                class="btn-circle bg-orange-500 hover:bg-orange-600 text-white shadow-sm transition hover:scale-105"
                                onclick="bukaModalIsianPA('<?= $nimMhs ?>', '<?= addslashes($namaMhs) ?>', '<?= addslashes($prodiMhs) ?>')"
                                title="Pengisian Buku PA Semester">
                            <i class="fas fa-edit text-xs"></i>
                            <span class="tip">Isi Buku PA</span>
                        </button>
                        <?php endif; ?>

                        <!-- Tombol Riwayat Pengisian PA (Drawer) -->
                        <button type="button" 
                                class="btn-circle bg-emerald-500 hover:bg-emerald-600 text-white shadow-sm transition hover:scale-105"
                                onclick="bukaDrawerRiwayatPA('<?= $nimMhs ?>')"
                                title="Lihat Riwayat & Arsip Buku PA">
                            <i class="fas fa-box-archive text-xs"></i>
                            <span class="tip">Riwayat Buku PA</span>
                        </button>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        <tr class="hidden" id="paPaginationMeta" data-total-pages="<?= $totalPages ?>" data-total-data="<?= $totalData ?>" data-page="<?= $page ?>"></tr>
        <?php
    endif;
    exit;
}

// ================= 3. AMBIL RIWAYAT BUKU PA MAHASISWA (DRAWER) =================
if (isset($_POST['ambil-riwayat-pa'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    $nimMhs = bersihkan($_POST['nim'] ?? '');
    if (empty($nimMhs)) {
        echo json_encode(['status' => false, 'pesan' => 'NIM mahasiswa tidak valid']);
        exit;
    }

    // Detail mahasiswa
    $qMhs = query("
        SELECT m.nim, m.nama, m.prodi, p.nama AS nama_prodi, m.angkatan, m.dosen_pa,
               d.nama AS nama_pa, d.nip AS nip_pa
        FROM mahasiswa m
        LEFT JOIN prodi p ON (m.prodi = p.kode OR m.prodi = p.nama)
        LEFT JOIN dosen d ON (m.dosen_pa = d.id OR m.dosen_pa = d.nip)
        WHERE m.nim = '$nimMhs' LIMIT 1
    ");

    if (!$qMhs || mysqli_num_rows($qMhs) === 0) {
        echo json_encode(['status' => false, 'pesan' => 'Data mahasiswa tidak ditemukan']);
        exit;
    }

    $mhs = mysqli_fetch_assoc($qMhs);

    // Cek kewenangan pengisian / manipulasi data bimbingan mahasiswa ini
    $isDosenPaMhs = false;
    if (!empty($identitasDosen)) {
        if (!empty($mhs['dosen_pa']) && in_array((string)$mhs['dosen_pa'], $identitasDosen, true)) {
            $isDosenPaMhs = true;
        } elseif (!empty($mhs['nip_pa']) && in_array((string)$mhs['nip_pa'], $identitasDosen, true)) {
            $isDosenPaMhs = true;
        }
    }
    $pembFilterReq = bersihkan($_POST['pembimbing'] ?? ($userLevel === 9 ? '' : 'saya'));
    $canEditBimbingan = ($userLevel === 9) || ($pembFilterReq !== 'semua' && $isDosenPaMhs);

    // Ambil seluruh riwayat isian_pa
    $qRiwayat = query("
        SELECT * FROM isian_pa 
        WHERE nim = '$nimMhs' 
        ORDER BY semester ASC, id ASC
    ");

    $listRiwayat = [];
    while ($r = mysqli_fetch_assoc($qRiwayat)) {
        $listRiwayat[] = [
            'id'          => (int)$r['id'],
            'semester'    => (int)$r['semester'],
            'ips'         => $r['ips'] !== null ? number_format((float)$r['ips'], 2) : '-',
            'ipk'         => $r['ipk'] !== null ? number_format((float)$r['ipk'], 2) : '-',
            'sks'         => (int)$r['sks'],
            'status'      => $r['status'],
            'catatan'     => $r['catatan'],
            'tanggal'     => !empty($r['tanggal']) ? tgl_indo($r['tanggal']) : '-',
            'tanggal_raw' => $r['tanggal'],
            'rekaman'     => !empty($r['rekaman']) ? tgl_indo($r['rekaman']) : '-'
        ];
    }

    echo json_encode([
        'status'    => true,
        'can_edit'  => $canEditBimbingan,
        'mahasiswa' => [
            'nim'      => $mhs['nim'],
            'nama'     => tampil(ucwords(strtolower(trim($mhs['nama'])))),
            'prodi'    => !empty($mhs['nama_prodi']) ? $mhs['nama_prodi'] : ($mhs['prodi'] ?? '-'),
            'angkatan' => $mhs['angkatan'],
            'pa'       => !empty($mhs['nama_pa']) ? $mhs['nama_pa'] : ($mhs['dosen_pa'] ?? '-')
        ],
        'riwayat'   => $listRiwayat
    ]);
    exit;
}

// ================= 4. AMBIL DETAIL ISIAN PA (UNTUK EDIT) =================
if (isset($_POST['ambil-detail-isian'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    $idIsian = (int)($_POST['id'] ?? 0);
    $q = query("SELECT * FROM isian_pa WHERE id = '$idIsian' LIMIT 1");
    if ($q && ($row = mysqli_fetch_assoc($q))) {
        echo json_encode(['status' => true, 'data' => $row]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data isian tidak ditemukan']);
    }
    exit;
}

// ================= 5. SIMPAN / UPDATE ISIAN BUKU PA =================
if (isset($_POST['simpan-isian-pa'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    $idIsian  = (int)($_POST['id'] ?? 0);
    $nim      = bersihkan($_POST['nim'] ?? '');
    $semester = (int)($_POST['semester'] ?? 0);
    $ips      = (float)str_replace(',', '.', $_POST['ips'] ?? 0);
    $ipk      = (float)str_replace(',', '.', $_POST['ipk'] ?? 0);
    $sks      = (int)($_POST['sks'] ?? 0);
    $status      = bersihkan($_POST['status'] ?? '');
    $catatanRaw  = $_POST['catatan'] ?? '';
    // Izinkan tag rich text yang aman: formatting, list, paragraph, break
    $catatanSafe = strip_tags($catatanRaw, '<b><strong><i><em><u><ul><ol><li><p><br><span><div>');
    $catatan     = mysqli_real_escape_string($koneksi, trim($catatanSafe));
    $tanggal     = bersihkan($_POST['tanggal'] ?? date('Y-m-d'));
    $rekaman     = date('Y-m-d');

    // Validasi
    if (empty($nim)) {
        echo json_encode(['status' => false, 'pesan' => 'Mahasiswa wajib dipilih']);
        exit;
    }
    if (empty(trim(strip_tags($catatanSafe)))) {
        echo json_encode(['status' => false, 'pesan' => 'Catatan & Evaluasi Dosen PA wajib diisi']);
        exit;
    }
    if ($semester < 1 || $semester > 14) {
        echo json_encode(['status' => false, 'pesan' => 'Semester harus berada di rentang 1 s.d. 14']);
        exit;
    }
    $allowedStatus = ['Terdaftar - Aktif', 'Terdaftar - Tidak Aktif', 'Cuti', 'Tidak Aktif'];
    if (!in_array($status, $allowedStatus, true)) {
        echo json_encode(['status' => false, 'pesan' => 'Status akademik yang dipilih tidak valid']);
        exit;
    }
    if (empty($tanggal)) {
        $tanggal = date('Y-m-d');
    }

    // Pastikan mahasiswa ada di database
    $qCekMhs = query("SELECT nim, dosen_pa FROM mahasiswa WHERE nim = '$nim' LIMIT 1");
    if (!$qCekMhs || mysqli_num_rows($qCekMhs) === 0) {
        echo json_encode(['status' => false, 'pesan' => 'Data mahasiswa tidak ditemukan di sistem']);
        exit;
    }
    $rCekMhs = mysqli_fetch_assoc($qCekMhs);

    // Validasi kewenangan pengisian: Hanya Dosen PA yang bersangkutan atau Superadmin (Level 9)
    if ($userLevel !== 9) {
        $dosenPaMhs = (string)($rCekMhs['dosen_pa'] ?? '');
        if (empty($identitasDosen) || !in_array($dosenPaMhs, $identitasDosen, true)) {
            echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: Hanya Dosen Penasihat Akademik (PA) mahasiswa bersangkutan yang berwenang mengisi atau mengubah evaluasi Buku PA.']);
            exit;
        }
    }

    if ($idIsian > 0) {
        // Mode UPDATE
        $sql = "UPDATE isian_pa SET 
                    semester = '$semester',
                    ips      = '$ips',
                    ipk      = '$ipk',
                    sks      = '$sks',
                    catatan  = '$catatan',
                    status   = '$status',
                    tanggal  = '$tanggal',
                    rekaman  = '$rekaman'
                WHERE id = '$idIsian'";
        $res = query($sql);
        if ($res) {
            echo json_encode(['status' => true, 'pesan' => 'Data evaluasi Buku PA berhasil diperbarui']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui data: ' . mysqli_error($koneksi)]);
        }
    } else {
        // Mode INSERT
        // Cek apakah semester ini sudah pernah diisi
        $qCekSem = query("SELECT id FROM isian_pa WHERE nim = '$nim' AND semester = '$semester' LIMIT 1");
        if ($qCekSem && mysqli_num_rows($qCekSem) > 0) {
            echo json_encode(['status' => false, 'pesan' => "Evaluasi untuk Semester $semester mahasiswa ini sudah ada. Silakan gunakan tombol edit pada riwayat jika ingin memperbarui."]);
            exit;
        }

        $sql = "INSERT INTO isian_pa (nim, semester, ips, ipk, sks, catatan, tanggal, status, rekaman) 
                VALUES ('$nim', '$semester', '$ips', '$ipk', '$sks', '$catatan', '$tanggal', '$status', '$rekaman')";
        $res = query($sql);
        if ($res) {
            echo json_encode(['status' => true, 'pesan' => 'Pengisian Buku PA berhasil disimpan']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan isian PA: ' . mysqli_error($koneksi)]);
        }
    }
    exit;
}

// ================= 6. HAPUS ISIAN BUKU PA =================
if (isset($_POST['hapus-isian-pa'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    $idIsian = (int)($_POST['id'] ?? 0);
    if ($idIsian <= 0) {
        echo json_encode(['status' => false, 'pesan' => 'ID data tidak valid']);
        exit;
    }

    // Validasi kewenangan hapus: Hanya Dosen PA yang bersangkutan atau Superadmin (Level 9)
    if ($userLevel !== 9) {
        $qCekHapus = query("SELECT i.id, m.dosen_pa FROM isian_pa i JOIN mahasiswa m ON i.nim = m.nim WHERE i.id = '$idIsian' LIMIT 1");
        if (!$qCekHapus || ($rHapus = mysqli_fetch_assoc($qCekHapus)) === null) {
            echo json_encode(['status' => false, 'pesan' => 'Data evaluasi tidak ditemukan']);
            exit;
        }
        $dosenPaMhs = (string)($rHapus['dosen_pa'] ?? '');
        if (empty($identitasDosen) || !in_array($dosenPaMhs, $identitasDosen, true)) {
            echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: Hanya Dosen Penasihat Akademik (PA) mahasiswa bersangkutan yang berwenang menghapus data evaluasi Buku PA.']);
            exit;
        }
    }

    $qDel = query("DELETE FROM isian_pa WHERE id = '$idIsian'");
    if ($qDel) {
        echo json_encode(['status' => true, 'pesan' => 'Entri Buku PA berhasil dihapus']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus entri Buku PA: ' . mysqli_error($koneksi)]);
    }
    exit;
}

// Jika tidak ada action yang cocok
http_response_code(400);
echo json_encode(['status' => false, 'pesan' => 'Permintaan tidak dikenali']);
exit;
