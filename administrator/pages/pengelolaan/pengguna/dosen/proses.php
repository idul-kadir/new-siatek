<?php
/**
 * PROSES CRUD PENGGUNA DOSEN & STAF
 * Menangani request AJAX untuk ambil tabel, statistik terfilter, tambah, edit, reset password, dan hapus dosen/staf.
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

// ================= AMBIL DATA TABEL DOSEN & STATISTIK FILTERED =================
if (isset($_POST['ambil-tabel-dosen'])) {
    $cari   = bersihkan($_POST['cari'] ?? '');
    $prodi  = bersihkan($_POST['prodi'] ?? '');
    $status = bersihkan($_POST['status'] ?? '');
    $level  = bersihkan($_POST['level'] ?? '');
    $page   = max(1, (int)($_POST['page'] ?? 1));
    $limit  = max(5, min(100, (int)($_POST['limit'] ?? 15)));
    $offset = ($page - 1) * $limit;

    // Filter prodi jika terkunci level prodi
    if ($isFilterProdi && $userProdi !== '') {
        $prodi = $userProdi;
    }

    // 1. Base Where Clauses (Scope Filter Aktif: Prodi, Level, Cari)
    $baseWhereClauses = [];
    if ($prodi !== '') {
        $baseWhereClauses[] = "(d.prodi = '$prodi' OR p.kode = '$prodi' OR p.nama = '$prodi')";
    }
    if ($level !== '') {
        $baseWhereClauses[] = "d.level = '$level'";
    }
    if ($cari !== '') {
        $baseWhereClauses[] = "(d.nip LIKE '%$cari%' OR d.nidn LIKE '%$cari%' OR d.nama LIKE '%$cari%' OR d.email LIKE '%$cari%' OR d.no_telp LIKE '%$cari%' OR d.jabatan_fungsional LIKE '%$cari%')";
    }

    $baseWhereSql = '';
    if (!empty($baseWhereClauses)) {
        $baseWhereSql = 'WHERE ' . implode(' AND ', $baseWhereClauses);
    }

    // 2. Hitung Statistik Overview Berdasarkan Scope Filter yang Sedang Aktif
    $qStatTotal = query("SELECT COUNT(*) AS total FROM dosen d LEFT JOIN prodi p ON (d.prodi = p.kode OR d.prodi = p.nama) $baseWhereSql");
    $statTotal  = (int)(mysqli_fetch_assoc($qStatTotal)['total'] ?? 0);

    // Hitung dosen tanpa NIDN dalam lingkup filter aktif
    $whereTanpaNidnClause = "(d.nidn IS NULL OR d.nidn = '' OR d.nidn = '0')";
    $whereTanpaNidnSql    = $baseWhereSql ? "$baseWhereSql AND $whereTanpaNidnClause" : "WHERE $whereTanpaNidnClause";
    $qStatTanpaNidn       = query("SELECT COUNT(*) AS total FROM dosen d LEFT JOIN prodi p ON (d.prodi = p.kode OR d.prodi = p.nama) $whereTanpaNidnSql");
    $statTanpaNidn        = (int)(mysqli_fetch_assoc($qStatTanpaNidn)['total'] ?? 0);

    // Rincian setiap status riil dalam lingkup filter aktif (misal: aktif, tidak aktif)
    $statStatusList = [];
    $qStatStatus = query("SELECT d.status, COUNT(*) AS jml FROM dosen d LEFT JOIN prodi p ON (d.prodi = p.kode OR d.prodi = p.nama) $baseWhereSql GROUP BY d.status ORDER BY jml DESC");
    if ($qStatStatus) {
        while ($rs = mysqli_fetch_assoc($qStatStatus)) {
            $stKey = trim((string)$rs['status']);
            if ($stKey === '') {
                $stKey = 'Tidak Diketahui';
            }
            $statStatusList[$stKey] = (int)$rs['jml'];
        }
    }

    // 3. Where Clauses Tabel Lengkap (Menyertakan filter status spesifik jika diklik)
    $tableWhereClauses = $baseWhereClauses;
    if ($status !== '') {
        $tableWhereClauses[] = "LOWER(d.status) = LOWER('$status')";
    }
    $tableWhereSql = '';
    if (!empty($tableWhereClauses)) {
        $tableWhereSql = 'WHERE ' . implode(' AND ', $tableWhereClauses);
    }

    // Hitung total data tabel terpaginasi
    $qCount = query("SELECT COUNT(*) AS total FROM dosen d LEFT JOIN prodi p ON (d.prodi = p.kode OR d.prodi = p.nama) $tableWhereSql");
    $totalFiltered = (int)(mysqli_fetch_assoc($qCount)['total'] ?? 0);
    $totalPages    = ceil($totalFiltered / $limit);

    // Ambil baris data dosen & staf
    $sqlData = "SELECT d.*, COALESCE(p.nama, d.prodi) AS nama_prodi 
                FROM dosen d 
                LEFT JOIN prodi p ON (d.prodi = p.kode OR d.prodi = p.nama) 
                $tableWhereSql 
                ORDER BY d.nama ASC 
                LIMIT $limit OFFSET $offset";
    $ambil = query($sqlData);

    $daftarDosen = [];
    if ($ambil && mysqli_num_rows($ambil) > 0) {
        while ($row = mysqli_fetch_assoc($ambil)) {
            $row['nama'] = format_nama_title_case($row['nama']);
            $daftarDosen[] = $row;
        }
    }

    // Render TABEL ASLI SIATEK (Format Standar & Rapi)
    ob_start();
    if (!empty($daftarDosen)) {
        $no = $offset + 1;
        foreach ($daftarDosen as $data) {
            $statusRaw   = trim((string)$data['status']);
            $statusLabel = $statusRaw !== '' ? ucfirst($statusRaw) : 'Tidak Diketahui';
            $stLower     = strtolower($statusRaw);

            // Warna badge status
            $badgeClass = 'bg-slate-100 text-slate-700 border border-slate-200';
            if ($stLower === 'aktif') {
                $badgeClass = 'bg-emerald-100 text-emerald-800 border border-emerald-200';
            } elseif ($stLower === 'tidak aktif' || str_contains($stLower, 'non')) {
                $badgeClass = 'bg-rose-100 text-rose-800 border border-rose-200';
            }

            // Label hak akses / level
            $lvl = (int)($data['level'] ?? 1);
            $lvlInfo = dapatkan_label_level_dosen($lvl);

            // Bersihkan nomor kontak untuk WhatsApp
            $rawTelp = preg_replace('/[^0-9]/', '', (string)$data['no_telp']);
            if (str_starts_with($rawTelp, '0')) {
                $rawTelp = '62' . substr($rawTelp, 1);
            }

            $namaProdiTampil = trim((string)$data['nama_prodi']);
            if ($namaProdiTampil === '' || $namaProdiTampil === '-') {
                $namaProdiTampil = 'Jurusan Teknik Elektro';
            }
            ?>
            <tr class="transition hover:bg-orange-50/60 <?= ($no % 2 === 0) ? 'bg-slate-50/40' : 'bg-white' ?>"
                data-id="<?= tampil($data['id']) ?>"
                data-nip="<?= tampil($data['nip']) ?>"
                data-nidn="<?= tampil($data['nidn']) ?>"
                data-nama="<?= tampil($data['nama']) ?>"
                data-prodi="<?= tampil($data['prodi']) ?>"
                data-level="<?= (int)$data['level'] ?>"
                data-status="<?= tampil($data['status']) ?>"
                data-posisi="<?= tampil($data['posisi']) ?>"
                data-jafung="<?= tampil($data['jabatan_fungsional']) ?>"
                data-kepegawaian="<?= tampil($data['status_kepegawaian'] ?? '') ?>"
                data-keterangan="<?= tampil($data['keterangan']) ?>"
                data-email="<?= tampil($data['email']) ?>"
                data-notelp="<?= tampil($data['no_telp']) ?>">

                <!-- 1. Nomor Urut Kotak Oranye Khas SIATEK -->
                <td class="py-3.5 px-4 text-center">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-xs mx-auto">
                        <?= $no ?>
                    </span>
                </td>

                <!-- 2. Dosen / Staf (Nama & NIP / NIDN) -->
                <td class="py-3.5 px-4">
                    <div class="font-bold text-slate-800 text-sm leading-snug">
                        <?= tampil($data['nama']) ?>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-0.5 text-[11px] text-slate-500 font-mono">
                        <?php if (!empty($data['nip'])): ?>
                            <span class="inline-flex items-center gap-1">
                                <span class="text-slate-400">NIP:</span> <?= tampil($data['nip']) ?>
                                <button type="button" class="btnSalinTeks text-slate-400 hover:text-orange-600 transition" title="Salin NIP" data-teks="<?= tampil($data['nip']) ?>">
                                    <i class="fas fa-copy text-[10px]"></i>
                                </button>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($data['nidn'])): ?>
                            <span class="inline-flex items-center gap-1">
                                <span class="text-slate-400">NIDN:</span> <?= tampil($data['nidn']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </td>

                <!-- 3. Program Studi & Jabatan Fungsional -->
                <td class="py-3.5 px-4 text-slate-600">
                    <div class="font-semibold text-slate-800 text-xs">
                        <?= tampil($namaProdiTampil) ?>
                    </div>
                    <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                            <?= !empty($data['jabatan_fungsional']) ? tampil($data['jabatan_fungsional']) : 'Tenaga Pengajar' ?>
                        </span>
                        <?php if (!empty($data['keterangan'])): ?>
                            <span class="text-[10px] text-slate-400 uppercase font-mono">
                                (<?= tampil($data['keterangan']) ?>)
                            </span>
                        <?php endif; ?>
                    </div>
                </td>

                <!-- 4. Hak Akses / Peran Sistem -->
                <td class="py-3.5 px-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold <?= $lvlInfo['badge'] ?>">
                        <i class="fas <?= $lvlInfo['icon'] ?> text-[10px]"></i>
                        <span><?= $lvlInfo['label'] ?></span>
                    </span>
                    <?php if (!empty($data['posisi']) && strtolower($data['posisi']) !== 'dosen'): ?>
                        <div class="text-[10px] text-slate-400 font-medium mt-0.5 pl-1">
                            Posisi: <?= ucfirst(tampil($data['posisi'])) ?>
                        </div>
                    <?php endif; ?>
                </td>

                <!-- 5. Kontak (WhatsApp / Telepon & Email) -->
                <td class="py-3.5 px-4">
                    <div class="space-y-1">
                        <?php if (!empty($rawTelp)): ?>
                            <div>
                                <a href="https://wa.me/<?= $rawTelp ?>" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 hover:text-emerald-900 transition" title="Kirim Pesan WhatsApp">
                                    <i class="fab fa-whatsapp text-emerald-600"></i>
                                    <span><?= tampil($data['no_telp']) ?></span>
                                </a>
                            </div>
                        <?php else: ?>
                            <span class="text-[11px] text-slate-400 italic">No. Telp belum diisi</span>
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

                <!-- 6. Status Akun -->
                <td class="py-3.5 px-4 text-center">
                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                        <?= tampil($statusLabel) ?>
                    </span>
                </td>

                <!-- 7. Aksi (.btn-circle SIATEK) -->
                <td class="py-3.5 px-4 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                        <!-- Reset Password -->
                        <button type="button" class="btnResetPw btn-circle bg-amber-500 text-white shadow-xs hover:bg-amber-600 transition"
                                data-id="<?= tampil($data['id']) ?>"
                                data-nip="<?= tampil($data['nip']) ?>"
                                data-nama="<?= tampil($data['nama']) ?>">
                            <i class="fas fa-key text-xs"></i>
                            <span class="tip">Reset Password</span>
                        </button>

                        <!-- Edit Data -->
                        <button type="button" class="btnEdit btn-circle bg-sky-500 text-white shadow-xs hover:bg-sky-600 transition"
                                data-id="<?= tampil($data['id']) ?>"
                                data-nip="<?= tampil($data['nip']) ?>"
                                data-nidn="<?= tampil($data['nidn']) ?>"
                                data-nama="<?= tampil($data['nama']) ?>"
                                data-prodi="<?= tampil($data['prodi']) ?>"
                                data-level="<?= (int)$data['level'] ?>"
                                data-status="<?= tampil($data['status']) ?>"
                                data-posisi="<?= tampil($data['posisi']) ?>"
                                data-jafung="<?= tampil($data['jabatan_fungsional']) ?>"
                                data-kepegawaian="<?= tampil($data['status_kepegawaian'] ?? '') ?>"
                                data-keterangan="<?= tampil($data['keterangan']) ?>"
                                data-email="<?= tampil($data['email']) ?>"
                                data-notelp="<?= tampil($data['no_telp']) ?>">
                            <i class="fas fa-pencil-alt text-xs"></i>
                            <span class="tip">Edit Dosen</span>
                        </button>

                        <!-- Hapus Data -->
                        <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-xs hover:bg-rose-600 transition"
                                data-id="<?= tampil($data['id']) ?>"
                                data-nip="<?= tampil($data['nip']) ?>"
                                data-nama="<?= tampil($data['nama']) ?>">
                            <i class="fas fa-trash-alt text-xs"></i>
                            <span class="tip">Hapus Dosen</span>
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
            <td colspan="7" class="py-16 text-center text-slate-500">
                <i class="fas fa-user-slash text-4xl text-slate-300"></i>
                <p class="mt-3 font-semibold text-slate-600">Tidak ada data dosen / staf ditemukan</p>
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
            'tanpa_nidn'  => $statTanpaNidn,
            'status_list' => $statStatusList,
        ],
    ]);
    exit;
}

// ================= TAMBAH DATA DOSEN =================
if (isset($_POST['tambah-data-dosen'])) {
    $nip        = bersihkan($_POST['nip'] ?? '');
    $nidn       = bersihkan($_POST['nidn'] ?? '');
    $namaRaw    = bersihkan($_POST['nama'] ?? '');
    $nama       = format_nama_title_case($namaRaw);
    $prodi      = bersihkan($_POST['prodi'] ?? '');
    $level      = max(1, (int)($_POST['level'] ?? 1));
    $status     = bersihkan($_POST['status'] ?? 'aktif');
    $posisi     = bersihkan($_POST['posisi'] ?? 'dosen');
    $jafung     = bersihkan($_POST['jabatan_fungsional'] ?? '');
    $kepeg      = bersihkan($_POST['status_kepegawaian'] ?? '');
    $keterangan = bersihkan($_POST['keterangan'] ?? 'dtps');
    $email      = bersihkan($_POST['email'] ?? '');
    $no_telp    = bersihkan($_POST['no_telp'] ?? '');
    $pwInput    = trim((string)($_POST['password'] ?? ''));

    if ($nip === '' || $nama === '') {
        echo json_encode(['status' => false, 'pesan' => 'NIP dan Nama Lengkap wajib diisi.']);
        exit;
    }

    if (cek_nip_dosen_ganda($nip)) {
        echo json_encode(['status' => false, 'pesan' => 'NIP ' . $nip . ' sudah terdaftar di sistem.']);
        exit;
    }

    // Default password menggunakan NIP bila dikosongkan
    $pwFinal = ($pwInput !== '') ? $pwInput : $nip;
    $pwHash  = password_hash($pwFinal, PASSWORD_DEFAULT);

    // Generate ID acak 10 karakter heksadesimal standar dosen SIATEK
    $idBaru = substr(bin2hex(random_bytes(5)), 0, 10);
    while (cek_id_dosen_ada($idBaru)) {
        $idBaru = substr(bin2hex(random_bytes(5)), 0, 10);
    }

    $kepegSql = ($kepeg !== '') ? "'$kepeg'" : "NULL";

    $sql = "INSERT INTO dosen (id, nip, nidn, nama, password, ket, tipe_login, posisi, alamat, no_telp, tentang, email, ttd, level, status, prodi, id_sister, jabatan_fungsional, status_kepegawaian, sertifikasi, scholar, keahlian, keterangan) 
            VALUES ('$idBaru', '$nip', '$nidn', '$nama', '$pwHash', '', '', '$posisi', '', '$no_telp', '', '$email', '', $level, '$status', '$prodi', '', '$jafung', $kepegSql, '', '', '', '$keterangan')";
    $simpan = query($sql);

    if ($simpan) {
        echo json_encode(['status' => true, 'pesan' => 'Data dosen / staf berhasil ditambahkan.']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menambahkan data dosen ke basis data.']);
    }
    exit;
}

// ================= EDIT DATA DOSEN =================
if (isset($_POST['edit-data-dosen'])) {
    $id         = bersihkan($_POST['id'] ?? '');
    $nip        = bersihkan($_POST['nip'] ?? '');
    $nidn       = bersihkan($_POST['nidn'] ?? '');
    $namaRaw    = bersihkan($_POST['nama'] ?? '');
    $nama       = format_nama_title_case($namaRaw);
    $prodi      = bersihkan($_POST['prodi'] ?? '');
    $level      = max(1, (int)($_POST['level'] ?? 1));
    $status     = bersihkan($_POST['status'] ?? 'aktif');
    $posisi     = bersihkan($_POST['posisi'] ?? 'dosen');
    $jafung     = bersihkan($_POST['jabatan_fungsional'] ?? '');
    $kepeg      = bersihkan($_POST['status_kepegawaian'] ?? '');
    $keterangan = bersihkan($_POST['keterangan'] ?? 'dtps');
    $email      = bersihkan($_POST['email'] ?? '');
    $no_telp    = bersihkan($_POST['no_telp'] ?? '');

    if ($id === '' || $nip === '' || $nama === '') {
        echo json_encode(['status' => false, 'pesan' => 'ID, NIP, dan Nama Lengkap wajib diisi.']);
        exit;
    }

    // Cek apakah NIP bentrok dengan dosen lain
    $cekBentrok = query("SELECT id FROM dosen WHERE nip = '$nip' AND id != '$id' LIMIT 1");
    if ($cekBentrok && mysqli_num_rows($cekBentrok) > 0) {
        echo json_encode(['status' => false, 'pesan' => 'NIP ' . $nip . ' telah digunakan oleh pengguna lain.']);
        exit;
    }

    $kepegSql = ($kepeg !== '') ? "'$kepeg'" : "NULL";

    $sql = "UPDATE dosen SET 
                nip = '$nip', 
                nidn = '$nidn', 
                nama = '$nama', 
                prodi = '$prodi', 
                level = $level, 
                status = '$status', 
                posisi = '$posisi', 
                jabatan_fungsional = '$jafung', 
                status_kepegawaian = $kepegSql, 
                keterangan = '$keterangan', 
                email = '$email', 
                no_telp = '$no_telp' 
            WHERE id = '$id'";
    $update = query($sql);

    if ($update) {
        echo json_encode(['status' => true, 'pesan' => 'Data dosen / staf berhasil diperbarui.']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui data dosen.']);
    }
    exit;
}

// ================= RESET PASSWORD DOSEN =================
if (isset($_POST['reset-password-dosen'])) {
    $id     = bersihkan($_POST['id'] ?? '');
    $pwBaru = trim((string)($_POST['password_baru'] ?? ''));

    if ($id === '') {
        echo json_encode(['status' => false, 'pesan' => 'ID dosen tidak valid.']);
        exit;
    }

    $qDosen = query("SELECT nip FROM dosen WHERE id = '$id' LIMIT 1");
    if (!$qDosen || mysqli_num_rows($qDosen) === 0) {
        echo json_encode(['status' => false, 'pesan' => 'Data dosen tidak ditemukan.']);
        exit;
    }
    $rDsn = mysqli_fetch_assoc($qDosen);
    $nip = trim((string)$rDsn['nip']);

    // Default password menggunakan NIP
    $pwFinal = ($pwBaru !== '') ? $pwBaru : $nip;
    $pwHash  = password_hash($pwFinal, PASSWORD_DEFAULT);

    $sql = "UPDATE dosen SET password = '$pwHash' WHERE id = '$id'";
    $update = query($sql);

    if ($update) {
        $pesan = ($pwBaru !== '') ? 'Password berhasil diubah.' : 'Password berhasil direset ke default (NIP).';
        echo json_encode(['status' => true, 'pesan' => $pesan]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal mereset password dosen.']);
    }
    exit;
}

// ================= HAPUS DATA DOSEN =================
if (isset($_POST['hapus-data-dosen'])) {
    $id = bersihkan($_POST['id'] ?? '');

    if ($id === '') {
        echo json_encode(['status' => false, 'pesan' => 'ID dosen tidak valid.']);
        exit;
    }

    // Lindungi akun sendiri agar tidak terhapus
    $idSesi = (string)($_SESSION['id'] ?? '');
    $nipSesi = (string)($_SESSION['nip'] ?? $_SESSION['user_id'] ?? '');
    if ($id === $idSesi) {
        echo json_encode(['status' => false, 'pesan' => 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.']);
        exit;
    }

    $hapus = query("DELETE FROM dosen WHERE id = '$id'");

    if ($hapus) {
        echo json_encode(['status' => true, 'pesan' => 'Data dosen / staf berhasil dihapus.']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus data dosen. Dosen mungkin masih memiliki relasi data bimbingan, ujian, atau arsip di sistem.']);
    }
    exit;
}

// ================= FUNCTION KHUSUS HALAMAN PENGGUNA DOSEN =================
function cek_nip_dosen_ganda($nip) {
    $cek = query("SELECT id FROM dosen WHERE nip = '$nip' LIMIT 1");
    return ($cek && mysqli_num_rows($cek) > 0);
}

function cek_id_dosen_ada($id) {
    $cek = query("SELECT id FROM dosen WHERE id = '$id' LIMIT 1");
    return ($cek && mysqli_num_rows($cek) > 0);
}

function dapatkan_label_level_dosen($lvl) {
    return match ((int)$lvl) {
        9 => ['label' => 'Superadmin',     'badge' => 'bg-purple-100 text-purple-800 border border-purple-200', 'icon' => 'fa-crown'],
        6 => ['label' => 'Pengelola Jurusan', 'badge' => 'bg-indigo-100 text-indigo-800 border border-indigo-200', 'icon' => 'fa-building'],
        5 => ['label' => 'Ketua Prodi',    'badge' => 'bg-sky-100 text-sky-800 border border-sky-200',       'icon' => 'fa-graduation-cap'],
        3 => ['label' => 'Operator Prodi',  'badge' => 'bg-amber-100 text-amber-800 border border-amber-200', 'icon' => 'fa-user-gear'],
        4 => ['label' => 'Operator Jurusan','badge' => 'bg-teal-100 text-teal-800 border border-teal-200',   'icon' => 'fa-user-shield'],
        default => ['label' => 'Dosen Pengajar', 'badge' => 'bg-emerald-100 text-emerald-800 border border-emerald-200', 'icon' => 'fa-chalkboard-user'],
    };
}

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
