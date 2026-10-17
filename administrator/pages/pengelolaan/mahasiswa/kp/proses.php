<?php
require __DIR__ . "/../../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

// pastikan user sudah login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
    exit;
}

$userLevel = (int)($_SESSION['level'] ?? 0);

// ================= AMBIL DATA TABEL KP =================
if (isset($_POST['ambil-tabel-kp'])) {
    $cari     = bersihkan($_POST['cari'] ?? '');
    $prodi    = bersihkan($_POST['prodi'] ?? '');
    $angkatan = bersihkan($_POST['angkatan'] ?? '');
    $status   = bersihkan($_POST['status'] ?? '');
    $progres  = bersihkan($_POST['progres'] ?? '');

    $where = ["1=1"];

    // Jika level 3 atau 5, paksa filter hanya sesuai program studi user
    if (in_array($userLevel, [3, 5], true)) {
        $prodi = get_prodi_user();
        if ($prodi === '') {
            $where[] = "1=0";
        } else {
            $where[] = "m.prodi = '$prodi'";
        }
    } elseif ($prodi !== '') {
        $where[] = "m.prodi = '$prodi'";
    }

    if ($angkatan !== '') {
        $where[] = "m.angkatan = '$angkatan'";
    }
    if ($status !== '') {
        $where[] = "k.status = '$status'";
    }
    if ($cari !== '') {
        $where[] = "(k.nim LIKE '%$cari%' OR m.nama LIKE '%$cari%')";
    }

    $whereSql = implode(" AND ", $where);

    // Ambil list SOP KP untuk mapping nomor proses ke nama tahapan & eksekutor
    $sopMap = [];
    $rawSopList = get_sop_kp_list();
    foreach ($rawSopList as $sp) {
        $sopMap[(int)$sp['no']] = [
            'keterangan' => $sp['keterangan'],
            'eksekutor'  => $sp['eksekutor']
        ];
    }

    $sql = "SELECT k.*, m.nama AS nama_mhs, m.prodi, m.angkatan, p.nama AS nama_prodi
            FROM kp k
            LEFT JOIN mahasiswa m ON k.nim = m.nim
            LEFT JOIN prodi p ON m.prodi = p.kode
            WHERE $whereSql
            ORDER BY k.nim DESC";

    $ambil = query($sql);

    // Filter berdasarkan progres (SOP) jika dipilih
    $dataFiltered = [];
    if ($ambil && mysqli_num_rows($ambil) > 0) {
        while ($d = mysqli_fetch_assoc($ambil)) {
            $progresVal = get_progres_kp($d['nim']);
            if ($progresVal === '-' || $progresVal === '' || $progresVal === 0) {
                $progresVal = 1;
            }
            if ($progres !== '') {
                if ((string)$progresVal !== (string)$progres) {
                    continue;
                }
            }
            $d['progres_num']   = $progresVal;
            $sopItem            = $sopMap[(int)$progresVal] ?? null;
            $d['progres_label'] = $sopItem ? $sopItem['keterangan'] : ('Tahap ' . $progresVal);
            $d['eksekutor']     = $sopItem ? $sopItem['eksekutor'] : 'mahasiswa';
            $dataFiltered[]     = $d;
        }
    }

    ob_start();
    if (count($dataFiltered) > 0) {
        $no = 1;
        ?>
        <table class="w-full text-left text-sm">
          <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
            <tr>
              <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">Mahasiswa</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Judul / Tempat KP</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Status</th>
              <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
        <?php
        foreach ($dataFiltered as $data) {
            $namaMhs = !empty($data['nama_mhs']) ? $data['nama_mhs'] : '-';
            $prodiNm = !empty($data['nama_prodi']) ? $data['nama_prodi'] : (!empty($data['prodi']) ? $data['prodi'] : '');
            $statusKp = strtolower(trim($data['status'] ?? ''));
            $isTidakAktif = ($statusKp === 'tidak aktif');
            $isAdminEksekutor = ($data['eksekutor'] === 'admin') || $isTidakAktif;

            // Judul / Tempat KP
            $tempatKp = !empty($data['instansi']) ? $data['instansi'] : (!empty($data['tempat_kp']) ? $data['tempat_kp'] : (!empty($data['tempat']) ? $data['tempat'] : (!empty($data['lokasi']) ? $data['lokasi'] : '')));
            $judulKp  = !empty($data['judul']) ? $data['judul'] : '';

            $statusBadgeClass = 'bg-emerald-100 text-emerald-700';
            if ($statusKp === 'pengajuan' || $statusKp === 'draft') {
                $statusBadgeClass = 'bg-yellow-100 text-yellow-700';
            } elseif ($statusKp === 'bimbingan') {
                $statusBadgeClass = 'bg-blue-100 text-blue-700';
            } elseif ($statusKp === 'tidak aktif') {
                $statusBadgeClass = 'bg-rose-100 text-rose-700';
            }
            ?>
            <tr class="transition hover:bg-orange-50 <?= ($no % 2 == 0) ? 'bg-slate-50/60' : 'bg-white' ?>">
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
                            <p class="mt-0.5 text-xs text-slate-500 leading-snug">
                                <?= tampil($data['progres_label']) ?>
                            </p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 text-slate-600">
                    <?php if ($tempatKp !== '' && $judulKp !== ''): ?>
                        <p class="text-sm font-semibold text-slate-800 leading-snug"><?= tampil($tempatKp) ?></p>
                        <p class="text-xs text-slate-500 mt-1 leading-snug"><?= tampil($judulKp) ?></p>
                    <?php elseif ($tempatKp !== ''): ?>
                        <p class="text-sm leading-snug text-slate-700"><?= tampil($tempatKp) ?></p>
                    <?php elseif ($judulKp !== ''): ?>
                        <p class="text-sm leading-snug text-slate-700"><?= tampil($judulKp) ?></p>
                    <?php else: ?>
                        <span class="text-xs text-slate-400 italic">Belum mengisi judul/tempat</span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold <?= $statusBadgeClass ?>">
                        <?= tampil($data['status'] ?? 'Draft') ?>
                    </span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-1.5">
                        <!-- 1. Tombol Arsip -->
                        <button type="button" class="btnArsip btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600" data-nim="<?= tampil($data['nim']) ?>" data-nama="<?= tampil($namaMhs) ?>" data-prodi="<?= tampil($prodiNm) ?>">
                            <i class="fas fa-box-archive text-xs"></i><span class="tip">Arsip</span>
                        </button>

                        <!-- 2. Tombol Lembar Kerja -->
                        <button type="button" class="btnLembarKerja btn-circle bg-orange-500 hover:bg-orange-600 text-white shadow-sm" data-nim="<?= tampil($data['nim']) ?>" data-progres="<?= tampil($data['progres_num']) ?>" data-status-kp="<?= tampil($data['status'] ?? '') ?>" data-is-admin="<?= $isAdminEksekutor ? '1' : '0' ?>">
                            <i class="fas fa-clipboard-check text-xs"></i><span class="tip">Lembar Kerja</span>
                        </button>

                        <?php if ($userLevel === 9): ?>
                        <!-- 3. Tombol Hard Reset (Khusus Super Admin Level 9) -->
                        <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-nim="<?= tampil($data['nim']) ?>" data-nama="<?= tampil($namaMhs) ?>">
                            <i class="fas fa-trash text-xs"></i><span class="tip">Hard Reset</span>
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php
            $no++;
        }
        ?>
          </tbody>
        </table>
        <?php
    } else {
        ?>
        <div class="py-16 text-center">
            <i class="fas fa-briefcase text-4xl text-slate-300"></i>
            <p class="mt-3 font-medium text-slate-500">Belum ada data kerja praktek yang sesuai filter</p>
            <p class="text-xs text-slate-400">Coba ubah kata kunci pencarian atau pilihan filter di atas.</p>
        </div>
        <?php
    }
    $html = ob_get_clean();
    echo json_encode(['status' => true, 'data' => $html]);
    exit;
}

// ================= MUAT LEMBAR KERJA =================
if (isset($_POST['muat-lembar-kerja'])) {
    $nim = bersihkan($_POST['nim'] ?? '');
    $statusKp = strtolower(trim($_POST['status_kp'] ?? ''));
    $progres = (int)($_POST['progres'] ?? 1);

    ob_start();
    ?>
    <div class="space-y-4">
        <div class="rounded-lg bg-orange-50 p-4 border border-orange-100 mb-4">
            <?php
            $mhsRow = mysqli_fetch_assoc(query("SELECT nama, prodi, angkatan FROM mahasiswa WHERE nim='" . $nim . "' LIMIT 1"));
            $namaLengkap = $mhsRow ? $mhsRow['nama'] : 'Data tidak ditemukan';
            $prodiNm = $mhsRow && !empty($mhsRow['prodi']) ? tampil($mhsRow['prodi']) : '-';
            $angkatanTxt = $mhsRow && !empty($mhsRow['angkatan']) ? 'Angkatan ' . tampil($mhsRow['angkatan']) : '-';

            if ($mhsRow && !empty($mhsRow['prodi'])) {
                $prodiDetail = mysqli_fetch_assoc(query("SELECT nama FROM prodi WHERE kode='" . bersihkan($mhsRow['prodi']) . "' LIMIT 1"));
                $prodiLengkap = $prodiDetail ? tampil($prodiDetail['nama']) : $prodiNm;
            } else {
                $prodiLengkap = '-';
            }
            ?>
            <h4 class="font-bold text-orange-800 text-sm mb-3"><i class="fas fa-user-graduate mr-2"></i>Informasi Mahasiswa</h4>
            <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-xs">
                <p class="text-orange-600">NIM: <span class="font-semibold text-slate-800"><?= tampil($nim) ?></span></p>
                <p class="text-orange-600">Nama: <span class="font-semibold text-slate-800"><?= tampil($namaLengkap) ?></span></p>
                <p class="text-orange-600">Program Studi: <span class="font-semibold text-slate-800"><?= tampil($prodiLengkap) ?></span></p>
                <p class="text-orange-600">Angkatan: <span class="font-semibold text-slate-800"><?= tampil($angkatanTxt) ?></span></p>
            </div>
        </div>

        <?php if ($statusKp === 'tidak aktif'): ?>
        
        <!-- CASE 1: VERIFIKASI PENDAFTARAN KERJA PRAKTEK -->
        <h5 class="font-bold text-slate-800 text-[14px] border-b border-slate-200 pb-2"><i class="fas fa-tasks mr-1.5 text-slate-400"></i>Tugas: Verifikasi Pendaftaran Kerja Praktek</h5>
        <div class="py-3">
            <p class="text-sm text-slate-600 mb-4">Mahasiswa ini telah mendaftar Kerja Praktek namun statusnya masih tidak aktif. Silakan verifikasi kelayakan sebelum mengaktifkan status KP.</p>
            <form id="formAksiLembarKerja" class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                <input type="hidden" name="aksi" value="verifikasi-pendaftaran">
                <input type="hidden" name="nim" value="<?= tampil($nim) ?>">

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-2">Tindakan</label>
                    <select name="tindakan" id="selTindakan" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                        <option value="">-- Pilih Tindakan --</option>
                        <option value="terima">Terima &amp; Aktifkan Kerja Praktek</option>
                        <option value="tolak">Tolak Pendaftaran</option>
                    </select>
                </div>

                <!-- Syarat Checklist (Hanya Tampil Jika Memilih "Terima") -->
                <div id="wrapSyarat" class="mb-4 hidden rounded-md bg-white border border-slate-200 p-3">
                    <p class="text-xs font-bold text-slate-700 mb-2">Konfirmasi Kelayakan (Wajib dicentang):</p>
                    <label class="flex items-start gap-2 mb-2 cursor-pointer">
                        <input type="checkbox" id="chkSyarat1" class="mt-0.5 accent-orange-500">
                        <span class="text-xs text-slate-600">Mahasiswa telah memenuhi syarat minimal 100 SKS tempuh yang lulus.</span>
                    </label>
                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" id="chkSyarat2" class="mt-0.5 accent-orange-500">
                        <span class="text-xs text-slate-600">Mahasiswa sedang mengontrak mata kuliah Kerja Praktek pada semester berjalan.</span>
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" id="btnEksekusi" class="px-4 py-2 text-xs rounded-md bg-[#f97316] hover:bg-[#ea6a0f] text-white font-semibold shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed" data-text="Proses Keputusan" disabled>Proses Keputusan</button>
                </div>
            </form>
        </div>
        <script>
            $(document).ready(function(){
                var selTindakan = $('#selTindakan');
                var wrapSyarat = $('#wrapSyarat');
                var chk1 = $('#chkSyarat1');
                var chk2 = $('#chkSyarat2');
                var btnEks = $('#btnEksekusi');

                function checkValidity() {
                    var act = selTindakan.val();
                    if(act === 'terima') {
                        wrapSyarat.removeClass('hidden');
                        if(chk1.is(':checked') && chk2.is(':checked')) {
                            btnEks.prop('disabled', false);
                        } else {
                            btnEks.prop('disabled', true);
                        }
                    } else if(act === 'tolak') {
                        wrapSyarat.addClass('hidden');
                        btnEks.prop('disabled', false);
                    } else {
                        wrapSyarat.addClass('hidden');
                        btnEks.prop('disabled', true);
                    }
                }

                selTindakan.on('change', function(){
                    chk1.prop('checked', false);
                    chk2.prop('checked', false);
                    checkValidity();
                });

                chk1.add(chk2).on('change', checkValidity);
            });
        </script>

        <?php else:
            $namaTugasSop = 'Tahapan Kerja Praktek';
            $ketLower = '';
            $rawSop = get_sop_kp_list();
            foreach ($rawSop as $sp) {
                if ($sp['no'] === $progres) {
                    $namaTugasSop = $sp['keterangan'];
                    $ketLower = strtolower($sp['keterangan']);
                    break;
                }
            }
        ?>

        <!-- CASE 2: PROSES BERJALAN SESUAI SOP KP -->
        <h5 class="font-bold text-slate-800 text-[14px] border-b border-slate-200 pb-2"><i class="fas fa-tasks mr-1.5 text-slate-400"></i>Tugas: <?= tampil($namaTugasSop) ?></h5>
        <div class="py-3">
            <?php
            // Cek apakah tahapan ini adalah verifikasi berkas di menu verifikasi
            if (strpos($ketLower, 'verifikasi berkas') !== false || strpos($ketLower, 'verifikasi dokumen') !== false):
            ?>
                <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-500 mb-3 text-xl">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h6 class="font-bold text-blue-800 text-sm mb-2">Verifikasi Berkas Mahasiswa</h6>
                    <p class="text-xs text-blue-600 mb-4">Tugas pada tahapan ini adalah melakukan verifikasi terhadap berkas mahasiswa di antrian verifikasi.</p>
                    <a href="<?= base_url() ?>/administrator/mhs-verifikasi" class="inline-block px-4 py-2 text-xs rounded-md bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-sm transition">
                        Buka Halaman Verifikasi Berkas
                    </a>
                </div>
            <?php else: ?>
                <p class="text-sm text-slate-600 mb-4">Pilih tindakan untuk menyelesaikan tahap kerja praktek saat ini dan memajukan progres mahasiswa ke tahap selanjutnya.</p>
                <form id="formAksiLembarKerja" class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                    <input type="hidden" name="aksi" value="proses-sop">
                    <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                    <input type="hidden" name="progres_sekarang" value="<?= tampil($progres) ?>">

                    <?php
                    // Deteksi form berdasarkan tipe SOP KP
                    $isFormPembimbing = (strpos($ketLower, 'pembimbing') !== false && (strpos($ketLower, 'penunjukkan') !== false || strpos($ketLower, 'surat') !== false || strpos($ketLower, 'buat') !== false));
                    $isFormPenguji    = (strpos($ketLower, 'penguji') !== false && (strpos($ketLower, 'penunjukkan') !== false || strpos($ketLower, 'surat') !== false || strpos($ketLower, 'buat') !== false));
                    $isFormJadwal     = (strpos($ketLower, 'jadwal') !== false || strpos($ketLower, 'undangan') !== false || (strpos($ketLower, 'seminar') !== false && strpos($ketLower, 'siap') !== false));

                    if ($isFormPembimbing):
                        // Form Surat Penunjukkan Pembimbing KP (1 Pembimbing)
                    ?>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-600 mb-2">No. Surat Penunjukkan Pembimbing KP</label>
                            <input type="text" name="no_surat" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="Masukkan nomor surat penunjukkan..." required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Dosen Pembimbing KP</label>
                            <select name="pembimbing1" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                                <option value="">-- Pilih Dosen Pembimbing --</option>
                                <?php
                                $qDosen = query("SELECT id, nama FROM dosen WHERE LOWER(posisi) = 'dosen' AND LOWER(status) = 'aktif' ORDER BY nama ASC");
                                if ($qDosen) {
                                    while ($dsn = mysqli_fetch_assoc($qDosen)) {
                                        echo '<option value="' . tampil($dsn['id']) . '">' . tampil($dsn['nama']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <input type="hidden" name="jenis_aksi" value="simpan-pembimbing">
                    <?php
                    elseif ($isFormPenguji):
                        // Form Surat Penunjukkan Penguji Seminar KP (Penguji 1 & 2)
                        // Ambil pembimbing KP sebelumnya untuk saran
                        $suratBimb = mysqli_fetch_assoc(query("SELECT pembimbing1 FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING KP' ORDER BY id DESC LIMIT 1"));
                        $saranPenguji1 = $suratBimb ? (string)$suratBimb['pembimbing1'] : '';
                    ?>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-600 mb-2">No. Surat Penunjukkan Penguji Seminar KP</label>
                            <input type="text" name="no_surat" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="Masukkan nomor surat penunjukkan penguji..." required>
                        </div>
                        <div class="space-y-3 mb-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Dosen Penguji 1 <?= $saranPenguji1 ? '<span class="text-orange-600 font-normal">(Disarankan: Pembimbing KP)</span>' : '' ?></label>
                                <select name="penguji1" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                                    <option value="">-- Pilih Dosen Penguji 1 --</option>
                                    <?php
                                    $qDosen = query("SELECT id, nama FROM dosen WHERE LOWER(posisi) = 'dosen' AND LOWER(status) = 'aktif' ORDER BY nama ASC");
                                    if ($qDosen) {
                                        while ($dsn = mysqli_fetch_assoc($qDosen)) {
                                            $sel = ($dsn['id'] == $saranPenguji1) ? 'selected' : '';
                                            echo '<option value="' . tampil($dsn['id']) . '" ' . $sel . '>' . tampil($dsn['nama']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Dosen Penguji 2</label>
                                <select name="penguji2" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                                    <option value="">-- Pilih Dosen Penguji 2 --</option>
                                    <?php
                                    if ($qDosen) mysqli_data_seek($qDosen, 0);
                                    if ($qDosen) {
                                        while ($dsn = mysqli_fetch_assoc($qDosen)) {
                                            echo '<option value="' . tampil($dsn['id']) . '">' . tampil($dsn['nama']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="jenis_aksi" value="simpan-penguji">
                    <?php
                    elseif ($isFormJadwal):
                        // Form Jadwal Seminar KP
                    ?>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor Undangan Seminar KP</label>
                            <input type="text" name="no_undangan" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="Masukkan nomor surat undangan seminar..." required>
                        </div>
                        <div class="mb-4 grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Seminar</label>
                                <input type="date" name="tanggal_seminar" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Jam Seminar</label>
                                <input type="text" name="jam_seminar" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="contoh: 09:00 - Selesai" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Ruangan / Tempat Seminar</label>
                            <input type="text" name="ruangan" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="contoh: Ruang Sidang Jurusan" required>
                        </div>
                        <input type="hidden" name="jenis_aksi" value="simpan-jadwal">
                    <?php
                    else:
                        // Default form tahapan: konfirmasi penyelesaian tahap
                    ?>
                        <div class="rounded-lg bg-white border border-slate-200 p-4 mb-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-orange-600 font-bold">
                                    <i class="fas fa-check"></i>
                                </span>
                                <div>
                                    <p class="text-xs font-bold text-slate-700">Konfirmasi Penyelesaian Tahapan</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Tandai tahapan ini telah selesai dikerjakan dan majukan mahasiswa ke tahapan SOP selanjutnya.</p>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="jenis_aksi" value="lanjut-tahap">
                    <?php
                    endif;
                    ?>

                    <div class="flex justify-end mt-4">
                        <button type="submit" class="px-4 py-2 text-xs rounded-md bg-[#f97316] hover:bg-[#ea6a0f] text-white font-semibold shadow-sm transition" data-text="Simpan &amp; Lanjutkan">Simpan &amp; Lanjutkan</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <script>
        $('#formAksiLembarKerja').on('submit', function(e){
            e.preventDefault();
            var form = this;
            var btn = form.querySelector('button[type="submit"]');

            // Konfirmasi khusus jika menolak pendaftaran menggunakan modal
            if($(form).find('select[name="tindakan"]').val() === 'tolak' && $(form).find('input[name="aksi"]').val() === 'verifikasi-pendaftaran') {
                if(typeof konfirmasiHapus === 'function') {
                    // Pakai modal konfirmasiHapus global jika ada, beserta nama mahasiswa
                    var namaMhs = "<?= addslashes(tampil($namaLengkap)) ?>";
                    konfirmasiHapus('Pendaftaran KP ' + namaMhs + ' (data akan terhapus)', function() {
                        eksekusiLembarKerja(form, btn);
                    });
                } else {
                    eksekusiLembarKerja(form, btn); 
                }
                return false;
            }

            eksekusiLembarKerja(form, btn);
        });

        function eksekusiLembarKerja(form, btn) {
            if(typeof window.setButtonLoading === 'function') {
                window.setButtonLoading(btn, true);
            } else {
                btn.classList.add('is-loading');
                btn.setAttribute('disabled', '');
                btn.innerHTML = '<span class="spinner-slate"></span> Memproses...';
            }

            var formData = new FormData(form);
            formData.append('proses-lembar-kerja', '1');

            $.ajax({
                url: '<?= base_url() ?>/administrator/pages/pengelolaan/mahasiswa/kp/proses.php',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(respon) {
                    toast(respon.pesan, respon.status ? 'success' : 'error');
                    if (respon.status) {
                        $('#modalLembarKerja').removeClass('show');
                        muatUlangTabel();
                        if (respon.buka_tab) {
                            window.open(respon.buka_tab, '_blank');
                        }
                    }
                },
                error: function(xhr, status, err) {
                    var detail = xhr.responseText ? xhr.responseText.substring(0, 150) : err;
                    toast('Error: ' + detail, 'error');
                },
                complete: function() {
                    if(typeof window.setButtonLoading === 'function') {
                        window.setButtonLoading(btn, false);
                    } else {
                        btn.classList.remove('is-loading');
                        btn.removeAttribute('disabled');
                        btn.innerHTML = btn.getAttribute('data-text') || 'Simpan & Lanjutkan';
                    }
                }
            });
        }
    </script>
    <?php
    $html = ob_get_clean();
    echo json_encode(['status' => true, 'data' => $html]);
    exit;
}

// ================= PROSES LEMBAR KERJA (SUBMIT) =================
if (isset($_POST['proses-lembar-kerja'])) {
    $aksi = bersihkan($_POST['aksi'] ?? '');
    $nim = bersihkan($_POST['nim'] ?? '');

    if ($nim === '') {
        echo json_encode(['status' => false, 'pesan' => 'NIM tidak valid']);
        exit;
    }

    if ($aksi === 'verifikasi-pendaftaran') {
        $tindakan = bersihkan($_POST['tindakan'] ?? '');
        if ($tindakan === 'terima') {
            $update = query("UPDATE kp SET status = 'Aktif' WHERE nim = '$nim'");
            if (!$update) {
                $update = query("UPDATE kp SET status = 'aktif' WHERE nim = '$nim'");
            }
            set_progres_kp($nim, 1);
            echo json_encode(['status' => true, 'pesan' => 'Pendaftaran berhasil diterima dan status Kerja Praktek diaktifkan']);
            exit;
        } elseif ($tindakan === 'tolak') {
            query("DELETE FROM kp WHERE nim = '$nim'");
            query("DELETE FROM temp_kp WHERE nim = '$nim'");
            echo json_encode(['status' => true, 'pesan' => 'Pendaftaran Kerja Praktek ditolak. Data telah dihapus']);
            exit;
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Tindakan tidak valid']);
            exit;
        }
    }

    if ($aksi === 'proses-sop') {
        $progresSekarang = (int)($_POST['progres_sekarang'] ?? 1);
        $jenisAksi = bersihkan($_POST['jenis_aksi'] ?? '');
        $waktuSistem = time();
        $tanggalSekarang = date('Y-m-d');
        $progresBaru = $progresSekarang + 1;

        if ($jenisAksi === 'simpan-pembimbing') {
            $noSurat = bersihkan($_POST['no_surat'] ?? '');
            $pembimbing1 = bersihkan($_POST['pembimbing1'] ?? '');

            if ($noSurat === '' || $pembimbing1 === '') {
                echo json_encode(['status' => false, 'pesan' => 'Nomor surat dan dosen pembimbing wajib diisi']);
                exit;
            }

            $idSurat = $waktuSistem;
            $ins = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, pembimbing1, tanggal, keterangan) VALUES ($idSurat, '$noSurat', '$nim', '$pembimbing1', '$tanggalSekarang', 'PEMBIMBING KP')");

            // Update kolom di kp jika ada
            query("UPDATE kp SET penunjukkan_pembimbing = '$idSurat' WHERE nim = '$nim'");
            query("UPDATE kp SET pembimbing = '$pembimbing1' WHERE nim = '$nim'");

            set_progres_kp($nim, $progresBaru);
            echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan pembimbing KP berhasil dibuat']);
            exit;
        } elseif ($jenisAksi === 'simpan-penguji') {
            $noSurat  = bersihkan($_POST['no_surat'] ?? '');
            $penguji1 = bersihkan($_POST['penguji1'] ?? '');
            $penguji2 = bersihkan($_POST['penguji2'] ?? '');

            if ($noSurat === '' || $penguji1 === '') {
                echo json_encode(['status' => false, 'pesan' => 'Nomor surat dan penguji wajib diisi']);
                exit;
            }

            $idSurat = $waktuSistem;
            $ins = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, penguji1, penguji2, tanggal, keterangan) VALUES ($idSurat, '$noSurat', '$nim', '$penguji1', '$penguji2', '$tanggalSekarang', 'PENGUJI KP')");

            query("UPDATE kp SET penunjukkan_penguji = '$idSurat' WHERE nim = '$nim'");
            set_progres_kp($nim, $progresBaru);
            echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan penguji seminar KP berhasil dibuat']);
            exit;
        } elseif ($jenisAksi === 'simpan-jadwal') {
            $noUndangan = bersihkan($_POST['no_undangan'] ?? '');
            $tgl        = bersihkan($_POST['tanggal_seminar'] ?? '');
            $jam        = bersihkan($_POST['jam_seminar'] ?? '');
            $ruangan    = bersihkan($_POST['ruangan'] ?? '');

            if ($noUndangan === '' || $tgl === '' || $jam === '' || $ruangan === '') {
                echo json_encode(['status' => false, 'pesan' => 'Semua kolom jadwal seminar wajib diisi']);
                exit;
            }

            query("UPDATE kp SET no_undangan = '$noUndangan', tanggal_seminar = '$tgl', jam_seminar = '$jam', ruangan = '$ruangan' WHERE nim = '$nim'");
            query("UPDATE kp SET tanggal = '$tgl', jam = '$jam' WHERE nim = '$nim'");
            set_progres_kp($nim, $progresBaru);

            echo json_encode([
                'status' => true,
                'pesan'  => 'Jadwal seminar KP berhasil disimpan dan diteruskan'
            ]);
            exit;
        } else {
            // Lanjut ke tahapan berikutnya secara umum
            set_progres_kp($nim, $progresBaru);
            echo json_encode(['status' => true, 'pesan' => 'Tahapan berhasil diselesaikan dan progres dimajukan']);
            exit;
        }
    }

    echo json_encode(['status' => false, 'pesan' => 'Aksi tidak dikenal']);
    exit;
}

// ================= AMBIL ARSIP & RIWAYAT (DRAWER) =================
if (isset($_POST['ambil-arsip-riwayat'])) {
    $nim = bersihkan($_POST['nim'] ?? '');

    $mhsQ = query("SELECT m.nim, m.nama, m.prodi, m.angkatan, p.nama AS nama_prodi, k.status 
                   FROM mahasiswa m 
                   LEFT JOIN prodi p ON m.prodi = p.kode 
                   LEFT JOIN kp k ON m.nim = k.nim 
                   WHERE m.nim = '$nim' LIMIT 1");

    if (!$mhsQ || mysqli_num_rows($mhsQ) === 0) {
        echo json_encode(['status' => false, 'pesan' => 'Data mahasiswa tidak ditemukan']);
        exit;
    }

    $mhs = mysqli_fetch_assoc($mhsQ);
    $statusMhs = $mhs['status'] ?? 'Draft';
    $statusClass = 'bg-emerald-100 text-emerald-800 border border-emerald-200';
    if (strtolower($statusMhs) === 'tidak aktif') {
        $statusClass = 'bg-rose-100 text-rose-800 border border-rose-200';
    } elseif (strtolower($statusMhs) === 'bimbingan') {
        $statusClass = 'bg-blue-100 text-blue-800 border border-blue-200';
    }

    $currentStage = get_progres_kp($nim);

    // Ambil list SOP KP
    $sopList = get_sop_kp_list();

    // Ambil surat-surat penunjukkan KP mahasiswa ini
    $suratBimb = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING KP' ORDER BY id DESC LIMIT 1"));
    $suratPeng = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PENGUJI KP' ORDER BY id DESC LIMIT 1"));

    // Ambil dosen names
    $dosenListQ = query("SELECT id, nama FROM dosen");
    $dosenNames = [];
    if ($dosenListQ) {
        while ($d = mysqli_fetch_assoc($dosenListQ)) {
            $dosenNames[$d['id']] = $d['nama'];
        }
    }

    // Ambil detail data KP mahasiswa
    $kpDetail = mysqli_fetch_assoc(query("SELECT * FROM kp WHERE nim = '$nim' LIMIT 1"));

    ob_start();
    ?>
    <div class="mb-4 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Alur &amp; Arsip KP</span>
      </div>
      <div class="flex items-center gap-1.5">
        <button type="button" id="btnBukaSemuaAcc" class="rounded-md border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-600 shadow-xs hover:bg-slate-50">
          <i class="fas fa-angles-down mr-1 text-slate-400"></i>Buka Semua
        </button>
        <button type="button" id="btnTutupSemuaAcc" class="rounded-md border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-600 shadow-xs hover:bg-slate-50">
          <i class="fas fa-angles-up mr-1 text-slate-400"></i>Tutup Semua
        </button>
      </div>
    </div>

    <div class="space-y-3" id="accordionArsip">
      <?php foreach ($sopList as $idx => $sop):
        $stepNum = (int)$sop['no'];
        $isCompleted = ($stepNum < $currentStage);
        $isCurrent   = ($stepNum === $currentStage);
        $isFuture    = ($stepNum > $currentStage);
        $ketLower    = strtolower($sop['keterangan']);

        $cardClass = 'border-slate-200 bg-white';
        $iconClass = 'bg-slate-100 text-slate-400';
        $statusLabel = 'Belum Tercapai';
        $badgeClass = 'bg-slate-100 text-slate-500';

        if ($isCompleted) {
            $cardClass = 'card-completed border-emerald-200 bg-white hover:border-emerald-300';
            $iconClass = 'bg-emerald-100 text-emerald-600';
            $statusLabel = 'Selesai';
            $badgeClass = 'bg-emerald-100 text-emerald-700';
        } elseif ($isCurrent) {
            $cardClass = 'card-current border-orange-300 bg-orange-50/30 ring-1 ring-orange-200';
            $iconClass = 'bg-orange-100 text-orange-600 animate-pulse';
            $statusLabel = 'Sedang Berlangsung';
            $badgeClass = 'bg-orange-100 text-orange-700 font-bold';
        }
      ?>
      <div class="rounded-xl border shadow-xs overflow-hidden transition-all <?= $cardClass ?>" data-card-stage="<?= $stepNum ?>">
        
        <!-- Accordion Header -->
        <div class="flex items-center justify-between p-3.5 <?= $isCompleted ? 'btn-toggle-accordion cursor-pointer hover:bg-slate-50/80 select-none' : '' ?>">
          <div class="flex items-center gap-3 min-w-0">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-xs font-bold <?= $iconClass ?>">
              <?php if ($isCompleted): ?>
                <i class="fas fa-check text-[11px]"></i>
              <?php else: ?>
                <?= $stepNum ?>
              <?php endif; ?>
            </span>
            <div class="min-w-0">
              <h4 class="text-xs font-bold text-slate-800 leading-snug truncate"><?= tampil($sop['keterangan']) ?></h4>
              <p class="text-[11px] text-slate-400 mt-0.5">Eksekutor: <span class="font-medium text-slate-600 uppercase"><?= tampil($sop['eksekutor'] ?? 'MAHASISWA') ?></span></p>
            </div>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold <?= $badgeClass ?>"><?= $statusLabel ?></span>
            <?php if ($isCompleted): ?>
              <i class="fas fa-chevron-down text-slate-400 text-xs acc-chevron transition-transform duration-200"></i>
            <?php endif; ?>
          </div>
        </div>

        <!-- Accordion Body (Hanya ada untuk completed step) -->
        <?php if ($isCompleted): ?>
        <div class="acc-body hidden border-t border-slate-100 bg-slate-50/50 p-4 space-y-3.5 text-xs">
          
          <?php
          // Cek apakah step ini berhubungan dengan surat pembimbing
          if (strpos($ketLower, 'pembimbing') !== false && (strpos($ketLower, 'penunjukkan') !== false || strpos($ketLower, 'surat') !== false) && $suratBimb):
              $p1Name = $dosenNames[$suratBimb['pembimbing1']] ?? $suratBimb['pembimbing1'];
          ?>
            <div class="data-section-box rounded-lg border border-slate-200 bg-white p-3.5">
              <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-2">
                <span class="font-bold text-slate-700 flex items-center gap-1.5"><i class="fas fa-file-contract text-orange-500"></i>Surat Penunjukkan Pembimbing KP</span>
                <button type="button" class="btn-toggle-edit text-[11px] font-semibold text-orange-600 hover:text-orange-700"><i class="fas fa-pen mr-1"></i>Koreksi Data</button>
              </div>
              <div class="view-box space-y-1">
                <p class="text-slate-500">No. Surat: <span class="font-semibold text-slate-800"><?= tampil($suratBimb['no_surat']) ?></span></p>
                <p class="text-slate-500">Tanggal: <span class="font-semibold text-slate-800"><?= tampil($suratBimb['tanggal']) ?></span></p>
                <p class="text-slate-500">Dosen Pembimbing: <span class="font-semibold text-slate-800"><?= tampil($p1Name) ?></span></p>
              </div>
              <div class="edit-box hidden">
                <form class="form-koreksi-arsip space-y-2.5">
                  <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                  <input type="hidden" name="id_surat" value="<?= $suratBimb['id'] ?>">
                  <input type="hidden" name="tipe_koreksi" value="surat-pembimbing">
                  <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">No. Surat</label>
                    <input type="text" name="no_surat" value="<?= tampil($suratBimb['no_surat']) ?>" class="w-full text-xs rounded border border-slate-300 p-1.5 outline-none focus:border-orange-400" required>
                  </div>
                  <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Dosen Pembimbing</label>
                    <select name="pembimbing1" class="w-full text-xs rounded border border-slate-300 p-1.5 outline-none focus:border-orange-400" required>
                      <?php foreach ($dosenNames as $dId => $dNm): ?>
                        <option value="<?= $dId ?>" <?= ($dId == $suratBimb['pembimbing1']) ? 'selected' : '' ?>><?= tampil($dNm) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="flex items-center gap-2 pt-1">
                    <button type="button" class="btn-cancel-edit px-2.5 py-1 text-[11px] rounded bg-slate-100 text-slate-600 font-medium">Batal</button>
                    <button type="submit" class="px-2.5 py-1 text-[11px] rounded bg-orange-500 hover:bg-orange-600 text-white font-medium">Simpan Perubahan</button>
                  </div>
                </form>
              </div>
            </div>
          <?php
          // Cek apakah step ini berhubungan dengan penguji
          elseif (strpos($ketLower, 'penguji') !== false && (strpos($ketLower, 'penunjukkan') !== false || strpos($ketLower, 'surat') !== false) && $suratPeng):
              $pg1Name = $dosenNames[$suratPeng['penguji1']] ?? $suratPeng['penguji1'];
              $pg2Name = $dosenNames[$suratPeng['penguji2']] ?? $suratPeng['penguji2'];
          ?>
            <div class="data-section-box rounded-lg border border-slate-200 bg-white p-3.5">
              <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-2">
                <span class="font-bold text-slate-700 flex items-center gap-1.5"><i class="fas fa-file-contract text-orange-500"></i>Surat Penunjukkan Penguji KP</span>
                <button type="button" class="btn-toggle-edit text-[11px] font-semibold text-orange-600 hover:text-orange-700"><i class="fas fa-pen mr-1"></i>Koreksi Data</button>
              </div>
              <div class="view-box space-y-1">
                <p class="text-slate-500">No. Surat: <span class="font-semibold text-slate-800"><?= tampil($suratPeng['no_surat']) ?></span></p>
                <p class="text-slate-500">Penguji 1: <span class="font-semibold text-slate-800"><?= tampil($pg1Name) ?></span></p>
                <p class="text-slate-500">Penguji 2: <span class="font-semibold text-slate-800"><?= tampil($pg2Name ?: '-') ?></span></p>
              </div>
              <div class="edit-box hidden">
                <form class="form-koreksi-arsip space-y-2.5">
                  <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                  <input type="hidden" name="id_surat" value="<?= $suratPeng['id'] ?>">
                  <input type="hidden" name="tipe_koreksi" value="surat-penguji">
                  <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">No. Surat</label>
                    <input type="text" name="no_surat" value="<?= tampil($suratPeng['no_surat']) ?>" class="w-full text-xs rounded border border-slate-300 p-1.5 outline-none focus:border-orange-400" required>
                  </div>
                  <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Dosen Penguji 1</label>
                    <select name="penguji1" class="w-full text-xs rounded border border-slate-300 p-1.5 outline-none focus:border-orange-400" required>
                      <?php foreach ($dosenNames as $dId => $dNm): ?>
                        <option value="<?= $dId ?>" <?= ($dId == $suratPeng['penguji1']) ? 'selected' : '' ?>><?= tampil($dNm) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Dosen Penguji 2</label>
                    <select name="penguji2" class="w-full text-xs rounded border border-slate-300 p-1.5 outline-none focus:border-orange-400">
                      <option value="">-- Tidak Ada / Kosong --</option>
                      <?php foreach ($dosenNames as $dId => $dNm): ?>
                        <option value="<?= $dId ?>" <?= ($dId == $suratPeng['penguji2']) ? 'selected' : '' ?>><?= tampil($dNm) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="flex items-center gap-2 pt-1">
                    <button type="button" class="btn-cancel-edit px-2.5 py-1 text-[11px] rounded bg-slate-100 text-slate-600 font-medium">Batal</button>
                    <button type="submit" class="px-2.5 py-1 text-[11px] rounded bg-orange-500 hover:bg-orange-600 text-white font-medium">Simpan Perubahan</button>
                  </div>
                </form>
              </div>
            </div>
          <?php
          // Cek apakah step ini berhubungan dengan jadwal seminar
          elseif ((strpos($ketLower, 'jadwal') !== false || strpos($ketLower, 'undangan') !== false) && $kpDetail && (!empty($kpDetail['tanggal_seminar']) || !empty($kpDetail['tanggal']))):
              $tglSem = !empty($kpDetail['tanggal_seminar']) ? $kpDetail['tanggal_seminar'] : ($kpDetail['tanggal'] ?? '-');
              $jamSem = !empty($kpDetail['jam_seminar']) ? $kpDetail['jam_seminar'] : ($kpDetail['jam'] ?? '-');
              $ruangSem = $kpDetail['ruangan'] ?? '-';
              $noUnd = $kpDetail['no_undangan'] ?? '-';
          ?>
            <div class="data-section-box rounded-lg border border-slate-200 bg-white p-3.5">
              <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-2">
                <span class="font-bold text-slate-700 flex items-center gap-1.5"><i class="fas fa-calendar-check text-orange-500"></i>Jadwal Seminar KP</span>
                <button type="button" class="btn-toggle-edit text-[11px] font-semibold text-orange-600 hover:text-orange-700"><i class="fas fa-pen mr-1"></i>Koreksi Data</button>
              </div>
              <div class="view-box space-y-1">
                <p class="text-slate-500">No. Undangan: <span class="font-semibold text-slate-800"><?= tampil($noUnd) ?></span></p>
                <p class="text-slate-500">Tanggal: <span class="font-semibold text-slate-800"><?= tampil($tglSem) ?></span></p>
                <p class="text-slate-500">Jam: <span class="font-semibold text-slate-800"><?= tampil($jamSem) ?></span></p>
                <p class="text-slate-500">Ruangan: <span class="font-semibold text-slate-800"><?= tampil($ruangSem) ?></span></p>
              </div>
              <div class="edit-box hidden">
                <form class="form-koreksi-arsip space-y-2.5">
                  <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                  <input type="hidden" name="tipe_koreksi" value="jadwal-seminar">
                  <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">No. Undangan</label>
                    <input type="text" name="no_undangan" value="<?= tampil($noUnd) ?>" class="w-full text-xs rounded border border-slate-300 p-1.5 outline-none focus:border-orange-400" required>
                  </div>
                  <div class="grid grid-cols-2 gap-2">
                    <div>
                      <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tanggal</label>
                      <input type="date" name="tanggal_seminar" value="<?= tampil($tglSem) ?>" class="w-full text-xs rounded border border-slate-300 p-1.5 outline-none focus:border-orange-400" required>
                    </div>
                    <div>
                      <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jam</label>
                      <input type="text" name="jam_seminar" value="<?= tampil($jamSem) ?>" class="w-full text-xs rounded border border-slate-300 p-1.5 outline-none focus:border-orange-400" required>
                    </div>
                  </div>
                  <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Ruangan</label>
                    <input type="text" name="ruangan" value="<?= tampil($ruangSem) ?>" class="w-full text-xs rounded border border-slate-300 p-1.5 outline-none focus:border-orange-400" required>
                  </div>
                  <div class="flex items-center gap-2 pt-1">
                    <button type="button" class="btn-cancel-edit px-2.5 py-1 text-[11px] rounded bg-slate-100 text-slate-600 font-medium">Batal</button>
                    <button type="submit" class="px-2.5 py-1 text-[11px] rounded bg-orange-500 hover:bg-orange-600 text-white font-medium">Simpan Perubahan</button>
                  </div>
                </form>
              </div>
            </div>
          <?php endif; ?>

          <div class="text-[11px] text-slate-500 flex items-center justify-between">
            <span class="flex items-center gap-1.5 text-emerald-600 font-medium"><i class="fas fa-check-circle"></i>Tahap ini telah diselesaikan</span>
          </div>
        </div>
        <?php endif; ?>

      </div>
      <?php endforeach; ?>
    </div>
    <?php
    $html = ob_get_clean();
    echo json_encode([
        'status' => true,
        'mhs'    => [
            'nama'        => $mhs['nama'],
            'nim'         => $mhs['nim'],
            'prodi'       => $mhs['nama_prodi'] ?: $mhs['prodi'],
            'angkatan'    => $mhs['angkatan'],
            'status'      => $statusMhs,
            'badge_class' => $statusClass
        ],
        'data'   => $html
    ]);
    exit;
}

// ================= KOREKSI ARSIP STEP =================
if (isset($_POST['koreksi-arsip-step'])) {
    $nim = bersihkan($_POST['nim'] ?? '');
    $tipe = bersihkan($_POST['tipe_koreksi'] ?? '');

    if ($nim === '') {
        echo json_encode(['status' => false, 'pesan' => 'NIM tidak valid']);
        exit;
    }

    if ($tipe === 'surat-pembimbing') {
        $idSurat = bersihkan($_POST['id_surat'] ?? '');
        $noSurat = bersihkan($_POST['no_surat'] ?? '');
        $p1 = bersihkan($_POST['pembimbing1'] ?? '');

        $up = query("UPDATE surat_penunjukkan SET no_surat = '$noSurat', pembimbing1 = '$p1' WHERE id = '$idSurat'");
        query("UPDATE kp SET pembimbing = '$p1' WHERE nim = '$nim'");

        if ($up) {
            echo json_encode(['status' => true, 'pesan' => 'Koreksi surat penunjukkan pembimbing berhasil disimpan']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan perubahan: ' . mysqli_error($GLOBALS['koneksi'])]);
        }
        exit;
    } elseif ($tipe === 'surat-penguji') {
        $idSurat = bersihkan($_POST['id_surat'] ?? '');
        $noSurat = bersihkan($_POST['no_surat'] ?? '');
        $pg1 = bersihkan($_POST['penguji1'] ?? '');
        $pg2 = bersihkan($_POST['penguji2'] ?? '');

        $up = query("UPDATE surat_penunjukkan SET no_surat = '$noSurat', penguji1 = '$pg1', penguji2 = '$pg2' WHERE id = '$idSurat'");

        if ($up) {
            echo json_encode(['status' => true, 'pesan' => 'Koreksi surat penunjukkan penguji berhasil disimpan']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan perubahan: ' . mysqli_error($GLOBALS['koneksi'])]);
        }
        exit;
    } elseif ($tipe === 'jadwal-seminar') {
        $noUnd = bersihkan($_POST['no_undangan'] ?? '');
        $tglSem = bersihkan($_POST['tanggal_seminar'] ?? '');
        $jamSem = bersihkan($_POST['jam_seminar'] ?? '');
        $ruangSem = bersihkan($_POST['ruangan'] ?? '');

        $up = query("UPDATE kp SET no_undangan = '$noUnd', tanggal_seminar = '$tglSem', jam_seminar = '$jamSem', ruangan = '$ruangSem' WHERE nim = '$nim'");
        query("UPDATE kp SET tanggal = '$tglSem', jam = '$jamSem' WHERE nim = '$nim'");

        if ($up) {
            echo json_encode(['status' => true, 'pesan' => 'Koreksi jadwal seminar KP berhasil disimpan']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan perubahan: ' . mysqli_error($GLOBALS['koneksi'])]);
        }
        exit;
    }

    echo json_encode(['status' => false, 'pesan' => 'Tipe koreksi tidak valid']);
    exit;
}

// ================= GANTI BERKAS KP =================
if (isset($_POST['ganti-berkas-kp'])) {
    $nim = bersihkan($_POST['nim'] ?? '');
    $idPersyaratan = (int)($_POST['id_persyaratan'] ?? 0);

    if (!isset($_FILES['file']) || $_FILES['file']['error'] != 0) {
        echo json_encode(['status' => false, 'pesan' => 'File tidak ditemukan atau terjadi kesalahan unggah']);
        exit;
    }

    $val = validasi_file($_FILES['file'], $userLevel);
    if (!$val['valid']) {
        echo json_encode(['status' => false, 'pesan' => $val['pesan']]);
        exit;
    }

    $idFile = upload_file($_FILES['file'], 'temp_kp');
    if (!$idFile) {
        echo json_encode(['status' => false, 'pesan' => 'Gagal mengunggah dan menyimpan file']);
        exit;
    }

    // Catat ke temp_kp jika ada
    $cekTemp = query("SELECT id FROM temp_kp WHERE nim = '$nim' AND id_persyaratan = $idPersyaratan LIMIT 1");
    if ($cekTemp && mysqli_num_rows($cekTemp) > 0) {
        query("UPDATE temp_kp SET file = '$idFile', status = 'diterima', waktu = " . time() . " WHERE nim = '$nim' AND id_persyaratan = $idPersyaratan");
    } else {
        query("INSERT INTO temp_kp (nim, id_persyaratan, file, status, waktu) VALUES ('$nim', $idPersyaratan, '$idFile', 'diterima', " . time() . ")");
    }

    echo json_encode([
        'status'   => true,
        'pesan'    => 'Berkas berhasil diganti dan disimpan',
        'url_file' => url_file($idFile)
    ]);
    exit;
}

// ================= HARD RESET DATA KP (SUPER ADMIN LEVEL 9) =================
if (isset($_POST['hapus-data-kp'])) {
    if ($userLevel !== 9) {
        echo json_encode(['status' => false, 'pesan' => 'Akses ditolak. Fitur ini hanya untuk Super Admin (Level 9)']);
        exit;
    }

    $nim = bersihkan($_POST['nim'] ?? '');
    $konfirmasiNim = bersihkan($_POST['konfirmasi_nim'] ?? '');

    if ($nim === '' || $konfirmasiNim !== $nim) {
        echo json_encode(['status' => false, 'pesan' => 'Validasi gagal: NIM konfirmasi tidak sesuai']);
        exit;
    }

    // 1. Hapus dari tabel kp
    $delKp = query("DELETE FROM kp WHERE nim = '$nim'");

    // 2. Hapus dari temp_kp
    @query("DELETE FROM temp_kp WHERE nim = '$nim'");

    // 3. Hapus progres_kp jika ada
    @query("DELETE FROM progres_kp WHERE mahasiswa = '$nim'");

    // 4. Hapus surat penunjukkan KP
    @query("DELETE FROM surat_penunjukkan WHERE nim = '$nim' AND (keterangan = 'PEMBIMBING KP' OR keterangan = 'PENGUJI KP')");

    if ($delKp) {
        echo json_encode(['status' => true, 'pesan' => 'Data Kerja Praktek mahasiswa ' . $nim . ' berhasil direset total']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus data: ' . mysqli_error($GLOBALS['koneksi'])]);
    }
    exit;
}

// Fallback jika tidak ada request yang cocok
echo json_encode(['status' => false, 'pesan' => 'Permintaan tidak dikenali']);
exit;
