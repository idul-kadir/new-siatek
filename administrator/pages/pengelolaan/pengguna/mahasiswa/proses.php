<?php
/**
 * PROSES CRUD PENGGUNA MAHASISWA
 * Menangani request AJAX untuk tambah, edit, reset password, hapus, dan ambil data mahasiswa.
 * Respons selalu berupa JSON standar {status: bool, pesan: string, ...}.
 */
error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once __DIR__ . '/../../../../../fungsi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan user sudah login sebagai staf
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
$peran      = $_SESSION['peran'] ?? (!empty($_SESSION['nim']) ? 'mahasiswa' : 'staf');

if (!$sudahLogin || $peran === 'mahasiswa') {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir atau akses tidak diizinkan. Silakan login kembali.']);
    exit;
}

$userLevel     = (int)($_SESSION['level'] ?? 0);
$isFilterProdi = in_array($userLevel, [3, 5], true);
$userProdi     = $isFilterProdi ? (function_exists('get_prodi_user') ? get_prodi_user() : trim((string)($_SESSION['prodi'] ?? ''))) : '';

// ================= AMBIL DATA TABEL MAHASISWA & STATISTIK FILTERED =================
if (isset($_POST['ambil-tabel-mahasiswa'])) {
    $cari     = bersihkan($_POST['cari'] ?? '');
    $prodi    = bersihkan($_POST['prodi'] ?? '');
    $angkatan = bersihkan($_POST['angkatan'] ?? '');
    $status   = bersihkan($_POST['status'] ?? '');
    $page     = max(1, (int)($_POST['page'] ?? 1));
    $limit    = max(5, min(100, (int)($_POST['limit'] ?? 15)));
    $offset   = ($page - 1) * $limit;

    // Filter Prodi jika terkunci level 3/5
    if ($isFilterProdi) {
        $prodi = ($userProdi !== '') ? $userProdi : '__PRODI_KOSONG__';
    }

    // 1. Base Where Clauses (Scope Filter Aktif: Prodi, Angkatan, Cari)
    $baseWhereClauses = [];
    if ($prodi !== '') {
        if ($prodi === 's1teknikkomputer' || $prodi === 's1teknikomputer') {
            $baseWhereClauses[] = "(m.prodi IN ('s1teknikkomputer', 's1teknikomputer') OR p.kode = 's1teknikkomputer' OR p.nama LIKE '%Teknik Komputer%')";
        } else {
            $baseWhereClauses[] = "(m.prodi = '$prodi' OR p.kode = '$prodi' OR p.nama = '$prodi')";
        }
    }
    if ($angkatan !== '') {
        $baseWhereClauses[] = "m.angkatan = '$angkatan'";
    }
    if ($cari !== '') {
        $baseWhereClauses[] = "(m.nim LIKE '%$cari%' OR m.nama LIKE '%$cari%' OR m.email LIKE '%$cari%' OR m.no_hp LIKE '%$cari%')";
    }

    $baseWhereSql = '';
    if (!empty($baseWhereClauses)) {
        $baseWhereSql = 'WHERE ' . implode(' AND ', $baseWhereClauses);
    }

    // 2. Hitung Statistik Overview Berdasarkan Scope Filter yang Sedang Aktif
    $qStatTotal = query("SELECT COUNT(*) AS total FROM mahasiswa m LEFT JOIN prodi p ON (m.prodi = p.kode OR m.prodi = p.nama OR (m.prodi = 's1teknikomputer' AND p.kode = 's1teknikkomputer')) $baseWhereSql");
    $statTotal  = (int)(mysqli_fetch_assoc($qStatTotal)['total'] ?? 0);

    $whereTanpaPaClause = "(m.dosen_pa IS NULL OR m.dosen_pa = '' OR m.dosen_pa = '0')";
    $whereTanpaPaSql    = $baseWhereSql ? "$baseWhereSql AND $whereTanpaPaClause" : "WHERE $whereTanpaPaClause";
    $qStatTanpaPa       = query("SELECT COUNT(*) AS total FROM mahasiswa m LEFT JOIN prodi p ON (m.prodi = p.kode OR m.prodi = p.nama OR (m.prodi = 's1teknikomputer' AND p.kode = 's1teknikkomputer')) $whereTanpaPaSql");
    $statTanpaPa        = (int)(mysqli_fetch_assoc($qStatTanpaPa)['total'] ?? 0);

    // Rincian setiap status riil dalam lingkup filter aktif
    $statStatusList = [];
    $qStatStatus = query("SELECT m.status, COUNT(*) AS jml FROM mahasiswa m LEFT JOIN prodi p ON (m.prodi = p.kode OR m.prodi = p.nama OR (m.prodi = 's1teknikomputer' AND p.kode = 's1teknikkomputer')) $baseWhereSql GROUP BY m.status ORDER BY jml DESC");
    if ($qStatStatus) {
        while ($rs = mysqli_fetch_assoc($qStatStatus)) {
            $stKey = trim((string)$rs['status']);
            if ($stKey === '') {
                $stKey = 'Tidak Diketahui';
            }
            $statStatusList[$stKey] = (int)$rs['jml'];
        }
    }

    // 3. Table Where Clauses (Ditambahkan Filter Status jika Dipilih)
    $tableWhereClauses = $baseWhereClauses;
    if ($status === 'tanpa_pa') {
        $tableWhereClauses[] = "(m.dosen_pa IS NULL OR m.dosen_pa = '' OR m.dosen_pa = '0')";
    } elseif ($status === 'Tidak Diketahui') {
        $tableWhereClauses[] = "(m.status IS NULL OR m.status = '')";
    } elseif ($status !== '') {
        $tableWhereClauses[] = "LOWER(m.status) = LOWER('$status')";
    }

    $tableWhereSql = '';
    if (!empty($tableWhereClauses)) {
        $tableWhereSql = 'WHERE ' . implode(' AND ', $tableWhereClauses);
    }

    // Hitung total data tabel terpaginasi
    $qCount = query("SELECT COUNT(*) AS total FROM mahasiswa m LEFT JOIN prodi p ON (m.prodi = p.kode OR m.prodi = p.nama OR (m.prodi = 's1teknikomputer' AND p.kode = 's1teknikkomputer')) $tableWhereSql");
    $totalFiltered = (int)(mysqli_fetch_assoc($qCount)['total'] ?? 0);
    $totalPages    = ceil($totalFiltered / $limit);

    // Ambil baris data mahasiswa (JOIN prodi langsung ke tabel prodi)
    $sqlData = "SELECT m.*, COALESCE(p.nama, m.prodi) AS nama_prodi, d.nama AS nama_dosen_pa, d.nip AS nip_dosen_pa 
                FROM mahasiswa m 
                LEFT JOIN prodi p ON (m.prodi = p.kode OR m.prodi = p.nama OR (m.prodi = 's1teknikomputer' AND p.kode = 's1teknikkomputer')) 
                LEFT JOIN dosen d ON (m.dosen_pa = d.id OR m.dosen_pa = d.nip) 
                $tableWhereSql 
                ORDER BY m.angkatan DESC, m.nama ASC 
                LIMIT $limit OFFSET $offset";
    $ambil = query($sqlData);

    $daftarMahasiswa = [];
    if ($ambil && mysqli_num_rows($ambil) > 0) {
        while ($row = mysqli_fetch_assoc($ambil)) {
            // Format nama mahasiswa agar setiap awal kata menjadi huruf kapital (Title Case)
            $row['nama'] = format_nama_title_case($row['nama']);
            $daftarMahasiswa[] = $row;
        }
    }

    // Render TABEL ASLI SIATEK (Format Standar & Rapi)
    ob_start();
    if (!empty($daftarMahasiswa)) {
        $no = $offset + 1;
        foreach ($daftarMahasiswa as $data) {
            $statusRaw   = trim((string)$data['status']);
            $statusLabel = $statusRaw !== '' ? $statusRaw : 'Tidak Diketahui';
            $stLower     = strtolower($statusRaw);

            // Warna badge status sesuai nilai status riil
            $badgeClass = 'bg-slate-100 text-slate-700 border border-slate-200';
            if (str_contains($stLower, 'aktif') && !str_contains($stLower, 'tidak') && !str_contains($stLower, 'non')) {
                $badgeClass = 'bg-emerald-100 text-emerald-800 border border-emerald-200';
            } elseif (str_contains($stLower, 'lulus') || str_contains($stLower, 'alumni')) {
                $badgeClass = 'bg-sky-100 text-sky-800 border border-sky-200';
            } elseif (str_contains($stLower, 'cuti')) {
                $badgeClass = 'bg-amber-100 text-amber-800 border border-amber-200';
            } elseif (str_contains($stLower, 'drop out') || $stLower === 'do') {
                $badgeClass = 'bg-rose-100 text-rose-800 border border-rose-200';
            } elseif (str_contains($stLower, 'keluar') || str_contains($stLower, 'pindah')) {
                $badgeClass = 'bg-purple-100 text-purple-800 border border-purple-200';
            } elseif (str_contains($stLower, 'tidak aktif') || str_contains($stLower, 'non-aktif') || str_contains($stLower, 'nonaktif')) {
                $badgeClass = 'bg-slate-200 text-slate-700 border border-slate-300';
            }

            // Bersihkan nomor WhatsApp
            $rawHp = preg_replace('/[^0-9]/', '', (string)$data['no_hp']);
            if (str_starts_with($rawHp, '0')) {
                $rawHp = '62' . substr($rawHp, 1);
            }
            ?>
            <tr class="transition hover:bg-orange-50/60 <?= ($no % 2 === 0) ? 'bg-slate-50/40' : 'bg-white' ?>"
                data-nim="<?= tampil($data['nim']) ?>"
                data-nama="<?= tampil($data['nama']) ?>"
                data-prodi="<?= tampil($data['prodi']) ?>"
                data-nama-prodi="<?= tampil($data['nama_prodi']) ?>"
                data-angkatan="<?= tampil($data['angkatan']) ?>"
                data-status="<?= tampil($data['status']) ?>"
                data-dosen-pa="<?= tampil($data['dosen_pa']) ?>"
                data-email="<?= tampil($data['email']) ?>"
                data-nohp="<?= tampil($data['no_hp']) ?>">
                
                <!-- 1. Nomor Urut Kotak Oranye Khas SIATEK -->
                <td class="py-3.5 px-4 text-center">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-xs mx-auto">
                        <?= $no ?>
                    </span>
                </td>

                <!-- 2. Mahasiswa (Nama & NIM) -->
                <td class="py-3.5 px-4">
                    <div class="font-bold text-slate-800 text-sm leading-snug">
                        <?= tampil($data['nama']) ?>
                    </div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="font-mono text-xs font-semibold text-slate-600">
                            <?= tampil($data['nim']) ?>
                        </span>
                        <button type="button" class="btnSalinNim text-slate-400 hover:text-orange-600 transition" title="Salin NIM" data-nim="<?= tampil($data['nim']) ?>">
                            <i class="fas fa-copy text-xs"></i>
                        </button>
                    </div>
                </td>

                <!-- 3. Program Studi (Nama Resmi) & Angkatan -->
                <td class="py-3.5 px-4 text-slate-600">
                    <div class="font-semibold text-slate-800 text-xs">
                        <?= tampil($data['nama_prodi']) ?>
                    </div>
                    <div class="text-[11px] text-slate-500 mt-0.5">
                        Angkatan <?= tampil($data['angkatan'] ?? '-') ?>
                    </div>
                </td>

                <!-- 4. Kontak (WhatsApp & Email) -->
                <td class="py-3.5 px-4">
                    <div class="space-y-1">
                        <?php if (!empty($rawHp)): ?>
                            <div>
                                <a href="https://wa.me/<?= $rawHp ?>" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 hover:text-emerald-900 transition" title="Kirim Pesan WhatsApp">
                                    <i class="fab fa-whatsapp text-emerald-600"></i>
                                    <span><?= tampil($data['no_hp']) ?></span>
                                </a>
                            </div>
                        <?php else: ?>
                            <span class="text-[11px] text-slate-400 italic">No. HP belum diisi</span>
                        <?php endif; ?>

                        <?php if (!empty($data['email'])): ?>
                            <div>
                                <a href="mailto:<?= tampil($data['email']) ?>"
                                   class="inline-flex items-center gap-1 text-[11px] text-slate-500 hover:text-slate-800 transition" title="<?= tampil($data['email']) ?>">
                                    <i class="fas fa-envelope text-slate-400"></i>
                                    <span class="truncate max-w-[170px]"><?= tampil($data['email']) ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </td>

                <!-- 5. Dosen Pembimbing Akademik (PA) -->
                <td class="py-3.5 px-4">
                    <?php if (!empty($data['nama_dosen_pa'])): ?>
                        <div class="font-medium text-slate-800 text-xs">
                            <?= tampil($data['nama_dosen_pa']) ?>
                        </div>
                        <?php if (!empty($data['nip_dosen_pa'])): ?>
                            <div class="text-[11px] font-mono text-slate-400 mt-0.5">
                                NIP: <?= tampil($data['nip_dosen_pa']) ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            <i class="fas fa-exclamation-circle text-amber-500"></i> Belum Ada PA
                        </span>
                    <?php endif; ?>
                </td>

                <!-- 6. Status Akun -->
                <td class="py-3.5 px-4 text-center">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $badgeClass ?>">
                        <?= tampil($statusLabel) ?>
                    </span>
                </td>

                <!-- 7. Aksi (Semua Tombol Berbentuk Lingkaran Seragam / btn-circle khas SIATEK) -->
                <td class="py-3.5 px-4 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                        <?php if ($userLevel === 9): ?>
                        <button type="button" class="btnResetPw btn-circle bg-amber-500 text-white shadow-xs hover:bg-amber-600 transition"
                                data-nim="<?= tampil($data['nim']) ?>"
                                data-nama="<?= tampil($data['nama']) ?>">
                            <i class="fas fa-key text-xs"></i>
                            <span class="tip">Reset Password</span>
                        </button>

                        <?php endif; ?><!-- Tombol Edit (btn-circle sky) -->
                        <button type="button" class="btnEdit btn-circle bg-sky-500 text-white shadow-xs hover:bg-sky-600 transition"
                                data-nim="<?= tampil($data['nim']) ?>"
                                data-nama="<?= tampil($data['nama']) ?>"
                                data-prodi="<?= tampil($data['prodi']) ?>"
                                data-nama-prodi="<?= tampil($data['nama_prodi']) ?>"
                                data-angkatan="<?= tampil($data['angkatan']) ?>"
                                data-status="<?= tampil($data['status']) ?>"
                                data-dosen-pa="<?= tampil($data['dosen_pa']) ?>"
                                data-email="<?= tampil($data['email']) ?>"
                                data-nohp="<?= tampil($data['no_hp']) ?>">
                            <i class="fas fa-pen text-xs"></i>
                            <span class="tip">Edit Profil</span>
                        </button>

                        <?php if (in_array($userLevel, [6, 9], true)): ?>
                        <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-xs hover:bg-rose-600 transition"
                                data-nim="<?= tampil($data['nim']) ?>"
                                data-nama="<?= tampil($data['nama']) ?>">
                            <i class="fas fa-trash text-xs"></i>
                            <span class="tip">Hapus Akun</span>
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php
            $no++;
        }
    } else {
        ?>
        <tr class="empty-row">
            <td colspan="7" class="py-16 text-center text-slate-500">
                <i class="fas fa-user-slash text-4xl text-slate-300"></i>
                <p class="mt-3 font-semibold text-slate-600">Tidak ada data mahasiswa ditemukan</p>
                <p class="text-xs text-slate-400 mt-0.5">Coba sesuaikan kata kunci pencarian atau filter yang dipilih.</p>
            </td>
        </tr>
        <?php
    }
    $tbodyHtml = ob_get_clean();

    echo json_encode([
        'status'         => true,
        'tbody'          => $tbodyHtml,
        'total_filtered' => $totalFiltered,
        'page'           => $page,
        'limit'          => $limit,
        'total_pages'    => $totalPages,
        'stats'          => [
            'total'       => $statTotal,
            'tanpa_pa'    => $statTanpaPa,
            'status_list' => $statStatusList,
        ],
    ]);
    exit;
}

// ================= AMBIL STATISTIK MAHASISWA =================
if (isset($_POST['ambil-statistik-mahasiswa'])) {
    $whereProdi = '';
    if ($isFilterProdi) {
        $whereProdi = ($userProdi !== '') 
            ? "WHERE (prodi = '$userProdi' OR prodi IN (SELECT nama FROM prodi WHERE kode = '$userProdi'))" 
            : "WHERE 1=0";
    }

    $qTotal = query("SELECT COUNT(*) AS total FROM mahasiswa $whereProdi");
    $total  = (int)(mysqli_fetch_assoc($qTotal)['total'] ?? 0);

    $whereTanpaPa = $whereProdi ? "$whereProdi AND (dosen_pa IS NULL OR dosen_pa = '' OR dosen_pa = '0')" : "WHERE (dosen_pa IS NULL OR dosen_pa = '' OR dosen_pa = '0')";
    $qTanpaPa = query("SELECT COUNT(*) AS total FROM mahasiswa $whereTanpaPa");
    $tanpaPa  = (int)(mysqli_fetch_assoc($qTanpaPa)['total'] ?? 0);

    $statusList = [];
    $qStatus = query("SELECT status, COUNT(*) AS jml FROM mahasiswa $whereProdi GROUP BY status ORDER BY jml DESC");
    if ($qStatus) {
        while ($rs = mysqli_fetch_assoc($qStatus)) {
            $stKey = trim((string)$rs['status']);
            if ($stKey === '') {
                $stKey = 'Tidak Diketahui';
            }
            $statusList[$stKey] = (int)$rs['jml'];
        }
    }

    echo json_encode([
        'status' => true,
        'data'   => [
            'total'       => $total,
            'tanpa_pa'    => $tanpaPa,
            'status_list' => $statusList,
        ],
    ]);
    exit;
}

// ================= TAMBAH DATA MAHASISWA =================
if (isset($_POST['tambah-data-mahasiswa'])) {
    $nim      = bersihkan($_POST['nim'] ?? '');
    $namaRaw  = bersihkan($_POST['nama'] ?? '');
    $nama     = format_nama_title_case($namaRaw);
    $prodi    = bersihkan($_POST['prodi'] ?? '');
    $angkatan = bersihkan($_POST['angkatan'] ?? '');
    $status   = bersihkan($_POST['status'] ?? 'Aktif');
    $dosen_pa = bersihkan($_POST['dosen_pa'] ?? '');
    $email    = bersihkan($_POST['email'] ?? '');
    $no_hp    = bersihkan($_POST['no_hp'] ?? '');
    $pwInput  = trim((string)($_POST['password'] ?? ''));

    if ($isFilterProdi) {
        $prodi = $userProdi;
    }

    // Normalisasi kode prodi jika typo legacy
    if ($prodi === 's1teknikomputer') {
        $prodi = 's1teknikkomputer';
    }

    if ($nim === '' || $nama === '') {
        echo json_encode(['status' => false, 'pesan' => 'NIM dan Nama Lengkap wajib diisi.']);
        exit;
    }

    if (cek_nim_ganda($nim)) {
        echo json_encode(['status' => false, 'pesan' => 'NIM ' . $nim . ' sudah terdaftar di sistem.']);
        exit;
    }

    // Default password menggunakan NIM bila dikosongkan
    $pwFinal = ($pwInput !== '') ? $pwInput : $nim;
    $pwHash  = password_hash($pwFinal, PASSWORD_DEFAULT);

    $dosenPaSql  = ($dosen_pa !== '' && $dosen_pa !== '0') ? "'$dosen_pa'" : "NULL";
    $angkatanSql = ($angkatan !== '') ? "'$angkatan'" : "NULL";

    $sql = "INSERT INTO mahasiswa (nim, nama, password, email, no_hp, status, prodi, angkatan, dosen_pa) 
            VALUES ('$nim', '$nama', '$pwHash', '$email', '$no_hp', '$status', '$prodi', $angkatanSql, $dosenPaSql)";
    $simpan = query($sql);

    if ($simpan) {
        echo json_encode(['status' => true, 'pesan' => 'Data mahasiswa berhasil ditambahkan.']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menambahkan data mahasiswa ke database.']);
    }
    exit;
}

// ================= EDIT DATA MAHASISWA =================
if (isset($_POST['edit-data-mahasiswa'])) {
    $nim      = bersihkan($_POST['nim'] ?? '');
    $namaRaw  = bersihkan($_POST['nama'] ?? '');
    $nama     = format_nama_title_case($namaRaw);
    $prodi    = bersihkan($_POST['prodi'] ?? '');
    $angkatan = bersihkan($_POST['angkatan'] ?? '');
    $status   = bersihkan($_POST['status'] ?? 'Aktif');
    $dosen_pa = bersihkan($_POST['dosen_pa'] ?? '');
    $email    = bersihkan($_POST['email'] ?? '');
    $no_hp    = bersihkan($_POST['no_hp'] ?? '');

    if ($isFilterProdi) {
        $cekMhs = query("SELECT prodi FROM mahasiswa WHERE nim = '$nim' LIMIT 1");
        $mhsCur = $cekMhs ? mysqli_fetch_assoc($cekMhs) : null;
        if (!$mhsCur || ($mhsCur['prodi'] !== $userProdi && $mhsCur['prodi'] !== $namaProdiUser)) {
            echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: Anda hanya dapat mengubah data mahasiswa pada program studi Anda.']);
            exit;
        }
        $prodi = $userProdi;
    }

    // Normalisasi kode prodi jika typo legacy
    if ($prodi === 's1teknikomputer') {
        $prodi = 's1teknikkomputer';
    }

    if ($nim === '' || $nama === '') {
        echo json_encode(['status' => false, 'pesan' => 'NIM dan Nama Lengkap wajib diisi.']);
        exit;
    }

    $dosenPaSql  = ($dosen_pa !== '' && $dosen_pa !== '0') ? "'$dosen_pa'" : "NULL";
    $angkatanSql = ($angkatan !== '') ? "'$angkatan'" : "NULL";

    $sql = "UPDATE mahasiswa SET 
                nama = '$nama', 
                prodi = '$prodi', 
                angkatan = $angkatanSql, 
                status = '$status', 
                dosen_pa = $dosenPaSql, 
                email = '$email', 
                no_hp = '$no_hp' 
            WHERE nim = '$nim'";
    $update = query($sql);

    if ($update) {
        echo json_encode(['status' => true, 'pesan' => 'Data mahasiswa berhasil diperbarui.']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui data mahasiswa.']);
    }
    exit;
}

// ================= RESET PASSWORD MAHASISWA =================
if (isset($_POST['reset-password-mahasiswa'])) {
    if ($userLevel !== 9) {
        echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: Hanya Superadmin (Level 9) yang berwenang mereset password.']);
        exit;
    }
    $nim     = bersihkan($_POST['nim'] ?? '');
    $pwBaru  = trim((string)($_POST['password_baru'] ?? ''));

    if ($nim === '') {
        echo json_encode(['status' => false, 'pesan' => 'NIM mahasiswa tidak valid.']);
        exit;
    }

    // Bila password baru kosong, kembalikan ke password default yaitu NIM
    $pwFinal = ($pwBaru !== '') ? $pwBaru : $nim;
    $pwHash  = password_hash($pwFinal, PASSWORD_DEFAULT);

    $sql = "UPDATE mahasiswa SET password = '$pwHash' WHERE nim = '$nim'";
    $update = query($sql);

    if ($update) {
        $pesan = ($pwBaru !== '') ? 'Password berhasil diubah.' : 'Password berhasil direset ke default (NIM).';
        echo json_encode(['status' => true, 'pesan' => $pesan]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal mereset password mahasiswa.']);
    }
    exit;
}

// ================= HAPUS DATA MAHASISWA =================
if (isset($_POST['hapus-data-mahasiswa'])) {
    if (!in_array($userLevel, [6, 9], true)) {
        echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: Hanya Admin Jurusan (Level 6) dan Superadmin (Level 9) yang berwenang menghapus data mahasiswa.']);
        exit;
    }
    $nim = bersihkan($_POST['nim'] ?? '');

    if ($nim === '') {
        echo json_encode(['status' => false, 'pesan' => 'NIM mahasiswa tidak valid.']);
        exit;
    }

    $hapus = query("DELETE FROM mahasiswa WHERE nim = '$nim'");

    if ($hapus) {
        echo json_encode(['status' => true, 'pesan' => 'Data mahasiswa berhasil dihapus.']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus data mahasiswa. Mahasiswa mungkin memiliki relasi data lain.']);
    }
    exit;
}

// ================= DOWNLOAD TEMPLATE CSV UNTUK IMPORT =================
if (isset($_GET['download-template'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=template_import_mahasiswa_siatek.csv');
    
    $output = fopen('php://output', 'w');
    // Tulis BOM UTF-8 agar karakter terbaca sempurna di Microsoft Excel
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    
    // Header Kolom Baku Sederhana: Hanya data identitas mahasiswa (Tanpa kolom prodi/angkatan agar aman dan tidak bocor ke luar sistem)
    fputcsv($output, ['NIM', 'Nama Lengkap', 'No HP/WA', 'Email'], ';');
    
    // Contoh Baris Panduan (Termasuk demonstrasi NIM yang berawalan angka 0)
    fputcsv($output, ['0531427001', 'Fauzan Ahmad', '081234567890', 'fauzan@ung.ac.id'], ';');
    fputcsv($output, ['531427002', 'Dewi Lestari', '082198765432', 'dewi.lestari@ung.ac.id'], ';');
    fputcsv($output, ['0521427003', 'Rahmat Hidayat', '085211223344', 'rahmat@ung.ac.id'], ';');
    
    fclose($output);
    exit;
}

// ================= PROSES IMPORT MAHASISWA MASSAL =================
if (isset($_POST['import-mahasiswa-massal'])) {
    if (!isset($_FILES['file_import']) || $_FILES['file_import']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => false, 'pesan' => 'Mohon pilih berkas Excel atau CSV terlebih dahulu.']);
        exit;
    }

    $prodi = bersihkan($_POST['prodi'] ?? '');
    if ($prodi === '') {
        echo json_encode(['status' => false, 'pesan' => 'Program Studi tujuan wajib dipilih oleh operator.']);
        exit;
    }

    $angkatan = bersihkan($_POST['angkatan'] ?? (string)date('Y'));
    if ($angkatan === '') {
        $angkatan = (string)date('Y');
    }

    $modeGanda = bersihkan($_POST['mode_ganda'] ?? 'skip'); // skip atau update

    $fileTmpName = $_FILES['file_import']['tmp_name'];
    $fileName    = $_FILES['file_import']['name'];
    $fileExt     = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExt, ['csv', 'xlsx', 'xls', 'txt'], true)) {
        echo json_encode(['status' => false, 'pesan' => 'Format berkas tidak didukung. Harap unggah berkas .csv atau .xlsx']);
        exit;
    }

    $barisData = [];

    if ($fileExt === 'csv' || $fileExt === 'txt') {
        $handle = fopen($fileTmpName, 'r');
        if ($handle !== false) {
            // Cek BOM UTF-8
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            // Deteksi pembatas (koma atau titik koma atau tab)
            $firstLine = fgets($handle);
            rewind($handle);
            if ($bom === "\xEF\xBB\xBF") {
                fread($handle, 3);
            }
            
            $countSemicolon = substr_count($firstLine, ';');
            $countComma     = substr_count($firstLine, ',');
            $countTab       = substr_count($firstLine, "\t");

            $delimiter = ';';
            if ($countComma > $countSemicolon && $countComma > $countTab) {
                $delimiter = ',';
            } elseif ($countTab > $countSemicolon && $countTab > $countComma) {
                $delimiter = "\t";
            }

            $isFirstRow = true;
            while (($row = fgetcsv($handle, 2000, $delimiter)) !== false) {
                if (empty($row) || (count($row) === 1 && trim((string)$row[0]) === '')) {
                    continue;
                }
                // Lewati baris header jika baris pertama terdapat kata 'nim' atau 'nama'
                if ($isFirstRow) {
                    $isFirstRow = false;
                    $rowStr = strtolower(implode(' ', $row));
                    if (str_contains($rowStr, 'nim') || str_contains($rowStr, 'nama')) {
                        continue;
                    }
                }
                $barisData[] = $row;
            }
            fclose($handle);
        }
    } elseif ($fileExt === 'xlsx' || $fileExt === 'xls') {
        if (!class_exists('ZipArchive')) {
            echo json_encode([
                'status' => false, 
                'pesan'  => 'Ekstensi ZIP pada PHP web server belum diaktifkan untuk membaca .xlsx secara langsung. Silakan simpan / ekspor file Anda sebagai format .CSV (Comma/Semicolon delimited) lalu unggah kembali.'
            ]);
            exit;
        }

        // Parsing berkas OpenXML Spreadsheet (.xlsx) secara native
        $zip = new ZipArchive();
        if ($zip->open($fileTmpName) === true) {
            // 1. Baca shared strings
            $sharedStrings = [];
            $sharedXmlStr = $zip->getFromName('xl/sharedStrings.xml');
            if ($sharedXmlStr !== false) {
                $xmlObj = @simplexml_load_string($sharedXmlStr);
                if ($xmlObj && isset($xmlObj->si)) {
                    foreach ($xmlObj->si as $si) {
                        if (isset($si->t)) {
                            $sharedStrings[] = (string)$si->t;
                        } elseif (isset($si->r)) {
                            $tVal = '';
                            foreach ($si->r as $rItem) {
                                $tVal .= (string)$rItem->t;
                            }
                            $sharedStrings[] = $tVal;
                        } else {
                            $sharedStrings[] = '';
                        }
                    }
                }
            }

            // 2. Baca sheet pertama
            $sheetXmlStr = $zip->getFromName('xl/worksheets/sheet1.xml');
            if ($sheetXmlStr !== false) {
                $sheetXml = @simplexml_load_string($sheetXmlStr);
                if ($sheetXml && isset($sheetXml->sheetData->row)) {
                    $isFirstRow = true;
                    foreach ($sheetXml->sheetData->row as $rowEl) {
                        $colArr = [];
                        foreach ($rowEl->c as $c) {
                            $type = (string)$c['t'];
                            $val = isset($c->v) ? (string)$c->v : '';
                            if ($type === 's' && isset($sharedStrings[(int)$val])) {
                                $val = $sharedStrings[(int)$val];
                            }
                            $colArr[] = trim((string)$val);
                        }

                        if (empty($colArr) || (count($colArr) === 1 && $colArr[0] === '')) {
                            continue;
                        }

                        if ($isFirstRow) {
                            $isFirstRow = false;
                            $rowStr = strtolower(implode(' ', $colArr));
                            if (str_contains($rowStr, 'nim') || str_contains($rowStr, 'nama')) {
                                continue;
                            }
                        }
                        $barisData[] = $colArr;
                    }
                }
            }
            $zip->close();
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal membaca berkas Excel. Silakan coba simpan sebagai format .CSV.']);
            exit;
        }
    }

    if (empty($barisData)) {
        echo json_encode(['status' => false, 'pesan' => 'Tidak ada baris data mahasiswa yang dapat dibaca dari berkas.']);
        exit;
    }

    $sukses = 0;
    $diperbarui = 0;
    $dilewati = 0;
    $gagal = 0;

    foreach ($barisData as $b) {
        // PERHATIKAN: Jaga NIM sebagai raw string utuh (menjaga awalan '0' atau karakter unik lain)
        $nimRaw = trim((string)($b[0] ?? ''));
        // Hapus tanda petik bungkus jika ada
        $nimRaw = trim($nimRaw, "'\" \t\n\r\0\x0B");
        $nim    = bersihkan($nimRaw);

        $namaRaw = bersihkan(trim((string)($b[1] ?? '')));
        $nama    = format_nama_title_case($namaRaw);
        $no_hp   = bersihkan(trim((string)($b[2] ?? '')));
        $email = bersihkan(trim((string)($b[3] ?? '')));

        if ($nim === '' || $nama === '') {
            $gagal++;
            continue;
        }

        $status = 'Aktif';

        // Cek apakah NIM sudah ada di database
        if (cek_nim_ganda($nim)) {
            if ($modeGanda === 'update') {
                $sqlUp = "UPDATE mahasiswa SET 
                            nama = '$nama', 
                            prodi = '$prodi', 
                            angkatan = '$angkatan', 
                            status = '$status', 
                            no_hp = IF('$no_hp' != '', '$no_hp', no_hp), 
                            email = IF('$email' != '', '$email', email) 
                          WHERE nim = '$nim'";
                if (query($sqlUp)) {
                    $diperbarui++;
                } else {
                    $gagal++;
                }
            } else {
                $dilewati++;
            }
            continue;
        }

        // Password default: disamakan dengan NIM mahasiswa
        $pwHash = password_hash($nim, PASSWORD_DEFAULT);

        $sqlIns = "INSERT INTO mahasiswa (nim, nama, password, email, no_hp, status, prodi, angkatan) 
                   VALUES ('$nim', '$nama', '$pwHash', '$email', '$no_hp', '$status', '$prodi', '$angkatan')";
        if (query($sqlIns)) {
            $sukses++;
        } else {
            $gagal++;
        }
    }

    $pesanHasil = "Berhasil memproses import! Ditambahkan: $sukses mahasiswa baru.";
    if ($diperbarui > 0) {
        $pesanHasil .= " Diperbarui: $diperbarui.";
    }
    if ($dilewati > 0) {
        $pesanHasil .= " Dilewati (sudah ada): $dilewati.";
    }
    if ($gagal > 0) {
        $pesanHasil .= " Gagal/baris kosong: $gagal.";
    }

    echo json_encode([
        'status' => true,
        'pesan'  => $pesanHasil,
        'detail' => [
            'sukses'     => $sukses,
            'diperbarui' => $diperbarui,
            'dilewati'   => $dilewati,
            'gagal'      => $gagal,
        ],
    ]);
    exit;
}

// ================= FUNCTION KHUSUS HALAMAN INI =================
/**
 * Normalisasi nama/kode prodi dari input file import ke kode resmi database
 */
function normalisasi_kode_prodi($input) {
    $clean = strtolower(trim((string)$input));
    $cleanNoSpace = preg_replace('/[^a-z0-9]/', '', $clean);

    if (str_contains($cleanNoSpace, 'komputer') || $cleanNoSpace === 's1teknikkomputer' || $cleanNoSpace === 's1teknikomputer') {
        return 's1teknikkomputer';
    }
    if (str_contains($cleanNoSpace, 'vokasional') || str_contains($cleanNoSpace, 'pvre') || $cleanNoSpace === 's1pendidikanvokasionalrekayasaelektro') {
        return 's1pendidikanvokasionalrekayasaelektro';
    }
    if (str_contains($cleanNoSpace, 'elektro') || $cleanNoSpace === 's1teknikelektro') {
        return 's1teknikelektro';
    }
    return $input;
}

/**
 * Memeriksa apakah NIM sudah terdaftar di tabel mahasiswa
 */
function cek_nim_ganda($nim) {
    $cek = query("SELECT nim FROM mahasiswa WHERE nim = '$nim' LIMIT 1");
    return ($cek && mysqli_num_rows($cek) > 0);
}

/**
 * Format nama agar setiap awal kata menjadi huruf kapital (Title Case),
 * misalnya 'ABDUL HALIM PARIS' atau 'abdul halim' menjadi 'Abdul Halim Paris'.
 * Menjaga gelar akademik jika ada di belakang tanda koma (contoh: ', S.T.').
 */
function format_nama_title_case($nama) {
    $nama = trim((string)$nama);
    if ($nama === '') {
        return '';
    }

    if (str_contains($nama, ',')) {
        $parts = explode(',', $nama, 2);
        $namaUtama = trim($parts[0]);
        $gelar     = trim($parts[1] ?? '');

        $namaCap = mb_convert_case($namaUtama, MB_CASE_TITLE, 'UTF-8');
        return $gelar !== '' ? $namaCap . ', ' . $gelar : $namaCap;
    }

    return mb_convert_case($nama, MB_CASE_TITLE, 'UTF-8');
}
