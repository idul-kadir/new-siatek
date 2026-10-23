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
        $stClean = bersihkan($status);
        $stLower = strtolower(trim($stClean));
        if ($stLower === 'selesai') {
            $where[] = "(LOWER(k.status) = 'selesai' OR LOWER(k.status) = 'alumni')";
        } else {
            $where[] = "LOWER(k.status) = '$stLower'";
        }
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
            $tempatKp = !empty($data['nama_instansi']) ? $data['nama_instansi'] : (!empty($data['instansi']) ? $data['instansi'] : (!empty($data['tempat_kp']) ? $data['tempat_kp'] : (!empty($data['tempat']) ? $data['tempat'] : (!empty($data['lokasi']) ? $data['lokasi'] : ''))));
            $judulKp  = !empty($data['judul']) ? $data['judul'] : '';

            $statusBadgeClass = 'bg-emerald-100 text-emerald-700';
            if ($statusKp === 'pengajuan' || $statusKp === 'draft') {
                $statusBadgeClass = 'bg-yellow-100 text-yellow-700';
            } elseif ($statusKp === 'bimbingan') {
                $statusBadgeClass = 'bg-blue-100 text-blue-700';
            } elseif ($statusKp === 'tidak aktif') {
                $statusBadgeClass = 'bg-rose-100 text-rose-700';
            } elseif ($statusKp === 'selesai' || $statusKp === 'alumni') {
                $statusBadgeClass = 'bg-purple-100 text-purple-700';
            } elseif ($statusKp === 'kadaluarsa') {
                $statusBadgeClass = 'bg-slate-100 text-slate-700';
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
            $rawSop = get_sop_kp_list();
            foreach ($rawSop as $sp) {
                if ((int)$sp['no'] === (int)$progres) {
                    $namaTugasSop = $sp['keterangan'];
                    break;
                }
            }

            // Ambil data detail KP mahasiswa untuk prefill formulir
            $kpDetail = mysqli_fetch_assoc(query("SELECT * FROM kp WHERE nim = '$nim' LIMIT 1"));
        ?>

        <!-- CASE 2: PROSES BERJALAN SESUAI SOP KP -->
        <h5 class="font-bold text-slate-800 text-[14px] border-b border-slate-200 pb-2"><i class="fas fa-tasks mr-1.5 text-slate-400"></i>Tugas: <?= tampil($namaTugasSop) ?></h5>
        <div class="py-3">
            <?php
            // Progres Verifikasi Berkas yang harus diproses di antrian halaman mhs-verifikasi
            $progresVerifikasi = [3, 5, 9, 11, 15];
            if (in_array((int)$progres, $progresVerifikasi, true)):
            ?>
                <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-500 mb-3 text-xl">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h6 class="font-bold text-blue-800 text-sm mb-2">Verifikasi Berkas Mahasiswa</h6>
                    <p class="text-xs text-blue-600 mb-4">Tugas pada tahapan ini adalah melakukan verifikasi terhadap berkas mahasiswa di antrian verifikasi. Proses ini tidak dilakukan di halaman ini.</p>
                    <a href="<?= base_url() ?>/administrator/mhs-verifikasi" class="inline-block px-4 py-2 text-xs rounded-md bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-sm transition">
                        Buka Halaman Verifikasi Berkas
                    </a>
                </div>
            <?php elseif ((int)$progres === 6): ?>
                <div class="rounded-lg bg-amber-50 border border-amber-200 p-5 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600 mb-3 text-xl">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h6 class="font-bold text-amber-900 text-sm mb-2">Penunjukkan Pembimbing Kerja Praktek</h6>
                    <p class="text-xs text-amber-800 leading-relaxed max-w-md mx-auto">
                        Proses pembuatan surat penunjukkan dilakukan di halaman <strong>Penunjukkan</strong> dan yang memiliki kewenangan adalah <strong>Ketua Program Studi</strong>.
                    </p>
                </div>
            <?php elseif ((int)$progres === 12): 
                $suratPenguji = trim((string)($kpDetail['surat_penunjukkan_penguji'] ?? ''));
                $belumAdaPenguji = empty($suratPenguji) || $suratPenguji === '0';
            ?>
                <?php if ($belumAdaPenguji): ?>
                <div class="rounded-lg bg-amber-50 border border-amber-200 p-5 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600 mb-3 text-xl">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <h6 class="font-bold text-amber-900 text-sm mb-2">Menunggu Pengaturan Penguji</h6>
                    <p class="text-xs text-amber-800 leading-relaxed max-w-md mx-auto">
                        Mahasiswa ini sedang menunggu pengaturan penguji dari <strong>Ketua Program Studi</strong>.
                    </p>
                </div>
                <?php else: ?>
                <div class="rounded-lg bg-blue-50 border border-blue-200 p-5 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600 mb-3 text-xl">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h6 class="font-bold text-blue-900 text-sm mb-2">Mahasiswa Siap Ujian</h6>
                    <p class="text-xs text-blue-700 leading-relaxed mb-4 max-w-md mx-auto">
                        Penguji telah ditetapkan dan mahasiswa siap melaksanakan ujian.
                    </p>
                    <a href="<?= base_url() ?>/administrator/mhs-ujian" class="inline-block px-4 py-2 text-xs rounded-md bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-sm transition">
                        Buka Halaman Ujian
                    </a>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <form id="formAksiLembarKerja" class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                    <input type="hidden" name="aksi" value="proses-sop">
                    <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                    <input type="hidden" name="progres_sekarang" value="<?= tampil($progres) ?>">

                    <?php
                    switch ((int)$progres):
                        case 6:
                            // Form Khusus: SOP 6 (Penunjukkan Pembimbing KP - 1 Pembimbing)
                            $suratBimbLembar = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING KP' ORDER BY id DESC LIMIT 1"));
                            if (!$suratBimbLembar) {
                                $sKp = mysqli_fetch_assoc(query("SELECT surat_penunjukkan_pembimbing FROM kp WHERE nim = '$nim' LIMIT 1"));
                                $idS = $sKp['surat_penunjukkan_pembimbing'] ?? 0;
                                if ($idS) {
                                    $suratBimbLembar = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE id = '$idS' LIMIT 1"));
                                }
                            }
                            $saranP1 = $suratBimbLembar['pembimbing1'] ?? '';
                            $saranNoSurat = $suratBimbLembar['no_surat'] ?? '';
                            ?>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-2">No. Surat Penunjukkan Pembimbing KP</label>
                                <input type="text" name="no_surat" value="<?= tampil($saranNoSurat) ?>" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="Contoh: 123/UN47.B5/TU/2026" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-2">Dosen Pembimbing KP</label>
                                <select name="pembimbing1" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                                    <option value="">-- Pilih Dosen Pembimbing --</option>
                                    <?php
                                    $qDosen = query("SELECT id, nama FROM dosen WHERE LOWER(posisi) = 'dosen' AND LOWER(status) = 'aktif' ORDER BY nama ASC");
                                    if ($qDosen) {
                                        while ($dsn = mysqli_fetch_assoc($qDosen)) {
                                            $sel = ($dsn['id'] == $saranP1) ? 'selected' : '';
                                            echo '<option value="' . tampil($dsn['id']) . '" ' . $sel . '>' . tampil($dsn['nama']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <input type="hidden" name="jenis_aksi" value="simpan-pembimbing">
                            <?php
                            break;

                        case 7:
                            // Form Khusus: SOP 7 (Mencetak Surat Pengantar untuk Penarikan KP)
                            $tempatKpDisplay = !empty($kpDetail['nama_instansi']) ? $kpDetail['nama_instansi'] : (!empty($kpDetail['instansi']) ? $kpDetail['instansi'] : (!empty($kpDetail['tempat']) ? $kpDetail['tempat'] : (!empty($kpDetail['tempat_kp']) ? $kpDetail['tempat_kp'] : '')));
                            $noSuratOtomatis = get_nomor_surat('penarikan kp');
                            ?>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-2">No. Surat Pengantar Penarikan</label>
                                <input type="text" name="no_surat" value="<?= tampil($noSuratOtomatis) ?>" class="w-full rounded-md border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-700 outline-none cursor-not-allowed" readonly required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-2">Instansi / Perusahaan Tujuan</label>
                                <input type="text" name="instansi" value="<?= tampil($tempatKpDisplay) ?>" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="Nama instansi atau perusahaan tempat KP..." required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-2">Tanggal Surat</label>
                                <input type="date" name="tanggal_surat" value="<?= date('Y-m-d') ?>" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                            </div>
                            <input type="hidden" name="jenis_aksi" value="cetak-surat-penarikan">
                            <?php
                            break;

                        case 12:
                            // Form Khusus: SOP 12 (Penunjukkan Penguji Seminar KP - Penguji 1, 2, & 3)
                            $suratBimb = mysqli_fetch_assoc(query("SELECT pembimbing1 FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING KP' ORDER BY id DESC LIMIT 1"));
                            $saranPenguji1 = $suratBimb ? (string)$suratBimb['pembimbing1'] : '';

                            // Ambil list dosen aktif
                            $listDosen = [];
                            $qDosenAll = query("SELECT id, nama FROM dosen WHERE LOWER(posisi) = 'dosen' AND LOWER(status) = 'aktif' ORDER BY nama ASC");
                            if ($qDosenAll) {
                                while ($dsn = mysqli_fetch_assoc($qDosenAll)) {
                                    $listDosen[] = $dsn;
                                }
                            }
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
                                        foreach ($listDosen as $dsn) {
                                            $sel = ($dsn['id'] == $saranPenguji1) ? 'selected' : '';
                                            echo '<option value="' . tampil($dsn['id']) . '" ' . $sel . '>' . tampil($dsn['nama']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Dosen Penguji 2</label>
                                    <select name="penguji2" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                                        <option value="">-- Pilih Dosen Penguji 2 --</option>
                                        <?php
                                        foreach ($listDosen as $dsn) {
                                            echo '<option value="' . tampil($dsn['id']) . '">' . tampil($dsn['nama']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Dosen Penguji 3</label>
                                    <select name="penguji3" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                                        <option value="">-- Pilih Dosen Penguji 3 --</option>
                                        <?php
                                        foreach ($listDosen as $dsn) {
                                            echo '<option value="' . tampil($dsn['id']) . '">' . tampil($dsn['nama']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="jenis_aksi" value="simpan-penguji">
                            <?php
                            break;

                        case 13:
                            // Form Khusus: SOP 13 (Unggah Berkas Komponen Seminar KP) - Style & auto-upload event change persis skripsi
                            $qSyarat = query("SELECT id, nama_berkas FROM persyaratan_berkas_kp WHERE progres = 13");
                            $listSyarat = [];
                            if ($qSyarat) {
                                while ($sRow = mysqli_fetch_assoc($qSyarat)) {
                                    $listSyarat[] = $sRow;
                                }
                                usort($listSyarat, function($a, $b) {
                                    $isBaA = stripos($a['nama_berkas'], 'Berita Acara') !== false ? 0 : 1;
                                    $isBaB = stripos($b['nama_berkas'], 'Berita Acara') !== false ? 0 : 1;
                                    if ($isBaA !== $isBaB) return $isBaA - $isBaB;
                                    return strnatcasecmp($a['nama_berkas'], $b['nama_berkas']);
                                });
                            }

                            // Ambil data dosen penguji dari surat_penunjukkan KP mahasiswa (Penguji 1 - 3)
                            $dosenPenguji = [1 => '', 2 => '', 3 => ''];
                            $qKpRow = query("SELECT surat_penunjukkan_penguji, surat_penunjukkan_pembimbing FROM kp WHERE nim = '$nim' LIMIT 1");
                            $rowKp = ($qKpRow && mysqli_num_rows($qKpRow) > 0) ? mysqli_fetch_assoc($qKpRow) : [];
                            $idSuratPenguji = (int)($rowKp['surat_penunjukkan_penguji'] ?? 0);
                            $idSuratPembimbing = (int)($rowKp['surat_penunjukkan_pembimbing'] ?? 0);

                            $qSuratPenguji = ($idSuratPenguji > 0)
                                ? query("SELECT penguji1, penguji2, penguji3 FROM surat_penunjukkan WHERE id = $idSuratPenguji LIMIT 1")
                                : query("SELECT penguji1, penguji2, penguji3 FROM surat_penunjukkan WHERE nim = '$nim' AND (keterangan LIKE '%PENGUJI%KP%' OR keterangan LIKE '%SEMINAR%KP%' OR keterangan = 'PENGUJI KP') ORDER BY id DESC LIMIT 1");

                            if ($qSuratPenguji && mysqli_num_rows($qSuratPenguji) > 0) {
                                $rPenguji = mysqli_fetch_assoc($qSuratPenguji);
                                for ($p = 1; $p <= 3; $p++) {
                                    $idDsn = trim((string)($rPenguji['penguji' . $p] ?? ''));
                                    if ($idDsn !== '') {
                                        $qNama = query("SELECT nama FROM dosen WHERE id = '$idDsn' OR nip = '$idDsn' LIMIT 1");
                                        $dosenPenguji[$p] = ($qNama && mysqli_num_rows($qNama) > 0) ? mysqli_fetch_assoc($qNama)['nama'] : $idDsn;
                                    }
                                }
                            }

                            // Fallback Penguji 1: Jika Penguji 1 belum diatur di surat penguji, ambil dari Dosen Pembimbing KP
                            if (empty($dosenPenguji[1])) {
                                $qBimb = ($idSuratPembimbing > 0)
                                    ? query("SELECT pembimbing1 FROM surat_penunjukkan WHERE id = $idSuratPembimbing LIMIT 1")
                                    : query("SELECT pembimbing1 FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan LIKE '%PEMBIMBING%KP%' ORDER BY id DESC LIMIT 1");
                                if ($qBimb && mysqli_num_rows($qBimb) > 0) {
                                    $rBimb = mysqli_fetch_assoc($qBimb);
                                    $idP1 = trim((string)($rBimb['pembimbing1'] ?? ''));
                                    if ($idP1 !== '') {
                                        $qNamaP1 = query("SELECT nama FROM dosen WHERE id = '$idP1' OR nip = '$idP1' LIMIT 1");
                                        $dosenPenguji[1] = ($qNamaP1 && mysqli_num_rows($qNamaP1) > 0) ? mysqli_fetch_assoc($qNamaP1)['nama'] : $idP1;
                                    }
                                }
                            }
                            ?>
                            <?php if (!empty($listSyarat)): ?>
                            <div class="space-y-4">
                                <?php foreach ($listSyarat as $syarat): 
                                    $idSyarat = (int)$syarat['id'];
                                    $qBerkas = query("SELECT b.file, f.nama_file FROM berkas_kp b LEFT JOIN files f ON b.file = f.id WHERE b.id_persyaratan = $idSyarat AND b.nim = '$nim' LIMIT 1");
                                    $berkasAda = ($qBerkas && mysqli_num_rows($qBerkas) > 0);
                                    $urlBerkas = '';
                                    if ($berkasAda) {
                                        $dFile = mysqli_fetch_assoc($qBerkas);
                                        $urlBerkas = url_file($dFile['file']);
                                    }

                                    // Mapping nama dosen penguji 1 - 3 berdasarkan nama berkas
                                    $namaPengujiTerkait = '';
                                    $labelPengujiTerkait = '';
                                    $idxP = 0;
                                    $namaBerkas = $syarat['nama_berkas'];

                                    if (stripos($namaBerkas, 'berita acara') === false) {
                                        if (preg_match('/(?:penguji|pembimbing|koreksi|penilaian|revisi|pengesahan)[^\d]*([1-3])/i', $namaBerkas, $mPen)) {
                                            $idxP = (int)$mPen[1];
                                        } elseif (preg_match('/([1-3])/', $namaBerkas, $mPen)) {
                                            $idxP = (int)$mPen[1];
                                        } elseif (stripos($namaBerkas, 'pembimbing') !== false) {
                                            $idxP = 1;
                                        }
                                    }

                                    if ($idxP >= 1 && $idxP <= 3) {
                                        $labelPengujiTerkait = "Dosen Penguji $idxP";
                                        if (!empty($dosenPenguji[$idxP])) {
                                            $namaPengujiTerkait = $dosenPenguji[$idxP];
                                        }
                                    }
                                ?>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-bold text-slate-700">
                                            <?= htmlspecialchars($syarat['nama_berkas']) ?> (PDF) <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="flex items-center gap-3">
                                            <?php if ($urlBerkas): ?>
                                            <a href="<?= $urlBerkas ?>" target="_blank" class="text-[10px] font-bold text-orange-600 hover:underline"><i class="fas fa-eye mr-1"></i>Lihat File</a>
                                            <?php endif; ?>
                                            <span class="status-berkas-badge text-[10px] font-semibold <?= $berkasAda ? 'text-emerald-600 font-bold' : 'text-slate-400' ?>" data-badge-id="<?= $idSyarat ?>">
                                                <?= $berkasAda ? '<i class="fas fa-check-circle mr-1"></i>Tersimpan' : 'Belum diunggah' ?>
                                            </span>
                                        </div>
                                    </div>
                                    <input 
                                        type="file" 
                                        accept=".pdf" 
                                        class="auto-upload-operator w-full text-xs text-slate-500 border border-slate-200 rounded-lg p-2 bg-white focus:bg-white focus:outline-none transition <?= $berkasAda ? 'bg-emerald-50 border-emerald-200' : '' ?>"
                                        data-id-syarat="<?= $idSyarat ?>"
                                        data-nim="<?= tampil($nim) ?>"
                                    >
                                    <?php if ($idxP >= 1 && $idxP <= 3): ?>
                                    <p class="mt-1 text-[11px] text-slate-500 leading-tight">
                                        <i class="fas fa-user-tie text-orange-500 mr-1"></i><?= $labelPengujiTerkait ?>: 
                                        <?php if (!empty($namaPengujiTerkait)): ?>
                                            <span class="font-semibold text-slate-700"><?= htmlspecialchars($namaPengujiTerkait) ?></span>
                                        <?php else: ?>
                                            <span class="text-amber-600 italic">Belum diatur di Surat Penunjukkan (SOP 12)</span>
                                        <?php endif; ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <script>
                            $(document).ready(function(){
                                $('.auto-upload-operator').on('change', function(){
                                    var fileInput = this;
                                    var idSyarat = $(fileInput).data('id-syarat');
                                    var nimMhs = $(fileInput).data('nim');
                                    var badge = $('[data-badge-id="' + idSyarat + '"]');

                                    if(!fileInput.files || fileInput.files.length === 0) return;

                                    badge.removeClass('text-slate-400 text-emerald-600 text-rose-500').addClass('text-orange-500 font-semibold');
                                    badge.html('<i class="fas fa-spinner animate-spin mr-1"></i>Mengunggah...');
                                    $(fileInput).prop('disabled', true);

                                    var formData = new FormData();
                                    formData.append('unggah-komponen-per-item', '1');
                                    formData.append('id_persyaratan', idSyarat);
                                    formData.append('nim', nimMhs);
                                    formData.append('file', fileInput.files[0]);

                                    $.ajax({
                                        url: '<?= base_url() ?>/administrator/pages/pengelolaan/mahasiswa/kp/proses.php',
                                        method: 'POST',
                                        data: formData,
                                        contentType: false,
                                        processData: false,
                                        dataType: 'json',
                                        success: function(res) {
                                            if(res.status){
                                                toast(res.pesan, 'success');
                                                badge.removeClass('text-orange-500').addClass('text-emerald-600 font-bold');
                                                badge.html('<i class="fas fa-check-circle mr-1"></i>Tersimpan');
                                                $(fileInput).addClass('bg-emerald-50 border-emerald-200');

                                                if(res.selesai_semua){
                                                    toast(res.pesan_selesai || 'Seluruh berkas komponen telah lengkap diunggah! Progres mahasiswa dimajukan.', 'success');
                                                    setTimeout(function(){
                                                        $('#modalLembarKerja').removeClass('show');
                                                        if (typeof muatUlangTabel === 'function') {
                                                            muatUlangTabel();
                                                        }
                                                    }, 1200);
                                                }
                                            } else {
                                                toast(res.pesan, 'error');
                                                badge.removeClass('text-orange-500').addClass('text-rose-500 font-semibold');
                                                badge.html('<i class="fas fa-times-circle mr-1"></i>Gagal');
                                            }
                                            $(fileInput).prop('disabled', false);
                                        },
                                        error: function(xhr, status, err) {
                                            var detail = xhr.responseText ? xhr.responseText.substring(0, 150) : err;
                                            toast('Error upload: ' + detail, 'error');
                                            badge.removeClass('text-orange-500').addClass('text-rose-500 font-semibold');
                                            badge.html('Gagal koneksi');
                                            $(fileInput).prop('disabled', false);
                                        }
                                    });
                                });
                            });
                            </script>
                            <?php else: ?>
                            <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 mb-4 text-center">
                                <p class="text-xs text-amber-800">Belum ada daftar persyaratan berkas yang dikonfigurasi untuk SOP 13 di tabel persyaratan_berkas_kp.</p>
                            </div>
                            <?php endif; ?>
                            <?php
                            break;

                        default:
                            // Form Default: Konfirmasi Penyelesaian Tahapan
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
                            break;
                    endswitch;

                    // Label tombol submit dinamis (Hanya untuk tahapan yang memerlukan submit manual, SOP 13 auto-upload tanpa tombol)
                    $labelTombol = 'Simpan & Lanjutkan';
                    if (in_array((int)$progres, [6, 12], true)) {
                        $labelTombol = 'Buat Surat';
                    } elseif ((int)$progres === 7) {
                        $labelTombol = 'Cetak Surat';
                    }
                    ?>

                    <?php if ((int)$progres !== 13): ?>
                    <div class="flex justify-end mt-4">
                        <button type="submit" class="px-4 py-2 text-xs rounded-md bg-[#f97316] hover:bg-[#ea6a0f] text-white font-semibold shadow-sm transition" data-text="<?= $labelTombol ?>"><?= $labelTombol ?></button>
                    </div>
                    <?php endif; ?>
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

        if ($progresSekarang === 6 || $jenisAksi === 'simpan-pembimbing') {
            $noSurat = bersihkan($_POST['no_surat'] ?? '');
            $pembimbing1 = bersihkan($_POST['pembimbing1'] ?? '');

            if ($noSurat === '' || $pembimbing1 === '') {
                echo json_encode(['status' => false, 'pesan' => 'Nomor surat dan dosen pembimbing wajib diisi']);
                exit;
            }

            $suratBimb = mysqli_fetch_assoc(query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING KP' ORDER BY id DESC LIMIT 1"));
            if ($suratBimb && !empty($suratBimb['id'])) {
                $idSurat = $suratBimb['id'];
                $ins = query("UPDATE surat_penunjukkan SET no_surat = '$noSurat', pembimbing1 = '$pembimbing1', tanggal = '$tanggalSekarang' WHERE id = '$idSurat'");
            } else {
                $idSurat = $waktuSistem;
                $ins = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, pembimbing1, tanggal, keterangan) VALUES ($idSurat, '$noSurat', '$nim', '$pembimbing1', '$tanggalSekarang', 'PEMBIMBING KP')");
            }

            query("UPDATE kp SET surat_penunjukkan_pembimbing = '$idSurat' WHERE nim = '$nim'");
            set_progres_kp($nim, $progresBaru);
            echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan pembimbing KP berhasil disimpan dan tahapan dilanjutkan']);
            exit;
        } elseif ($progresSekarang === 7 || $jenisAksi === 'cetak-surat-penarikan') {
            $instansi = bersihkan($_POST['instansi'] ?? '');
            $noSurat  = bersihkan($_POST['no_surat'] ?? '');
            if ($instansi !== '') {
                query("UPDATE kp SET nama_instansi = '$instansi' WHERE nim = '$nim'");
            }

            // Catat nomor surat ke tabel nomor_surat jika valid
            if ($noSurat !== '' && $noSurat !== 'Tidak Ditemukan') {
               $id_no_surat = tambah_nomor($noSurat, 'Penarikan KP - ' . $nim);
               query("UPDATE `kp` SET `no_surat_penarikan`='$id_no_surat' WHERE nim='$nim' ");
            }

            // Majukan tahapan mahasiswa (step naik)
            set_progres_kp($nim, $progresBaru);

            // Buka dokumen surat penarikan KP di tab baru
            $urlSuratPenarikan = base_url() . "/administrator/dokumen/surat-penarikan-kp/" . $nim;

            echo json_encode([
                'status'   => true,
                'pesan'    => 'Surat penarikan KP berhasil dicetak dan tahapan dimajukan',
                'buka_tab' => $urlSuratPenarikan
            ]);
            exit;
        } elseif ($progresSekarang === 12 || $jenisAksi === 'simpan-penguji') {
            $noSurat  = bersihkan($_POST['no_surat'] ?? '');
            $penguji1 = bersihkan($_POST['penguji1'] ?? '');
            $penguji2 = bersihkan($_POST['penguji2'] ?? '');
            $penguji3 = bersihkan($_POST['penguji3'] ?? '');

            if ($noSurat === '' || $penguji1 === '') {
                echo json_encode(['status' => false, 'pesan' => 'Nomor surat dan Penguji 1 wajib diisi']);
                exit;
            }

            // Cek apakah surat penunjukkan penguji sudah ada sebelumnya
            $suratPeng = mysqli_fetch_assoc(query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND (keterangan = 'PENGUJI KP' OR keterangan LIKE '%PENGUJI%KP%') ORDER BY id DESC LIMIT 1"));
            if ($suratPeng && !empty($suratPeng['id'])) {
                $idSurat = (int)$suratPeng['id'];
                $ins = query("UPDATE surat_penunjukkan SET no_surat = '$noSurat', penguji1 = '$penguji1', penguji2 = '$penguji2', penguji3 = '$penguji3', tanggal = '$tanggalSekarang' WHERE id = $idSurat");
            } else {
                $idSurat = $waktuSistem;
                $ins = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, penguji1, penguji2, penguji3, tanggal, keterangan) VALUES ($idSurat, '$noSurat', '$nim', '$penguji1', '$penguji2', '$penguji3', '$tanggalSekarang', 'PENGUJI KP')");
            }

            query("UPDATE kp SET surat_penunjukkan_penguji = '$idSurat' WHERE nim = '$nim'");
            echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan penguji seminar KP berhasil disimpan']);
            exit;
        } elseif ($progresSekarang === 13 || $jenisAksi === 'unggah-berkas-13') {
            // Upload berkas jika ada file yang dikirim langsung melalui form submit biasa
            $qSyarat = query("SELECT id FROM persyaratan_berkas_kp WHERE progres = 13");
            if ($qSyarat) {
                while ($syarat = mysqli_fetch_assoc($qSyarat)) {
                    $idSyarat = (int)$syarat['id'];
                    $inputName = 'berkas_' . $idSyarat;

                    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] == 0) {
                        $cekBerkas = query("SELECT id, file FROM berkas_kp WHERE id_persyaratan = $idSyarat AND nim = '$nim' LIMIT 1");
                        $sudahAdaBerkas = ($cekBerkas && mysqli_num_rows($cekBerkas) > 0);
                        $idFileLama = '';
                        if ($sudahAdaBerkas) {
                            $rowBerkas = mysqli_fetch_assoc($cekBerkas);
                            $idFileLama = trim((string)($rowBerkas['file'] ?? ''));
                        }

                        $idFile = upload_file($_FILES[$inputName], 'kp', $idFileLama);
                        if ($idFile !== false) {
                            if ($sudahAdaBerkas) {
                                query("UPDATE berkas_kp SET file = '$idFile' WHERE id_persyaratan = $idSyarat AND nim = '$nim'");
                            } else {
                                query("INSERT INTO berkas_kp (id_persyaratan, nim, file) VALUES ($idSyarat, '$nim', '$idFile')");
                            }
                        }
                    }
                }
            }

            // Majukan tahapan mahasiswa (SOP 13 -> SOP 14)
            set_progres_kp($nim, 14);

            $pesanSukses = 'Berkas persyaratan SOP 13 berhasil disimpan dan tahapan mahasiswa dimajukan ke SOP 14';

            // Cek langsung apakah berkas persyaratan SOP 14 sudah lengkap di temp_kp
            $qSyarat14 = query("SELECT id FROM persyaratan_berkas_kp WHERE progres = 14");
            $totalSyarat14 = ($qSyarat14 && mysqli_num_rows($qSyarat14) > 0) ? mysqli_num_rows($qSyarat14) : 0;
            if ($totalSyarat14 > 0) {
                $qValid14 = query("SELECT COUNT(DISTINCT id_persyaratan) AS cnt FROM temp_kp WHERE nim = '$nim' AND status != 'ditolak' AND id_persyaratan IN (SELECT id FROM persyaratan_berkas_kp WHERE progres = 14)");
                $jmlValid14 = ($qValid14 && mysqli_num_rows($qValid14) > 0) ? (int)mysqli_fetch_assoc($qValid14)['cnt'] : 0;
                if ($jmlValid14 >= $totalSyarat14) {
                    set_progres_kp($nim, 15);
                    $pesanSukses = 'Seluruh berkas SOP 13 & berkas revisi SOP 14 telah lengkap! Progres otomatis langsung dimajukan ke SOP 15.';
                }
            }

            echo json_encode([
                'status' => true,
                'pesan'  => $pesanSukses
            ]);
            exit;
        } else {
            // Lanjut ke tahapan berikutnya secara umum
            set_progres_kp($nim, $progresBaru);
            if ($progresBaru >= 16) {
                query("UPDATE kp SET status = 'Selesai' WHERE nim = '$nim'");
            }
            echo json_encode(['status' => true, 'pesan' => 'Tahapan berhasil diselesaikan dan progres dimajukan']);
            exit;
        }
    }

    echo json_encode(['status' => false, 'pesan' => 'Aksi tidak dikenal']);
    exit;
}

// ================= UNGGAH KOMPONEN PER ITEM (EVENT CHANGE AUTO-UPLOAD KP) =================
if (isset($_POST['unggah-komponen-per-item'])) {
    if (ob_get_length()) ob_clean();

    $idSyarat = (int)($_POST['id_persyaratan'] ?? 0);
    $nim = bersihkan($_POST['nim'] ?? '');

    if ($idSyarat <= 0 || $nim === '') {
        echo json_encode(['status' => false, 'pesan' => 'Data tidak lengkap']);
        exit;
    }

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== 0) {
        echo json_encode(['status' => false, 'pesan' => 'Gagal membuka file']);
        exit;
    }

    // Validasi file (PDF, ukuran)
    $val = validasi_file($_FILES['file'], $userLevel);
    if (!$val['valid']) {
        echo json_encode(['status' => false, 'pesan' => $val['pesan']]);
        exit;
    }

    // Cek terlebih dahulu apakah berkas untuk persyaratan ini sudah pernah diunggah di berkas_kp
    $cek = query("SELECT id, file FROM berkas_kp WHERE id_persyaratan = $idSyarat AND nim = '$nim' LIMIT 1");
    $sudahAda = ($cek && mysqli_num_rows($cek) > 0);
    $idFileLama = '';
    if ($sudahAda) {
        $rowLama = mysqli_fetch_assoc($cek);
        $idFileLama = trim((string)($rowLama['file'] ?? ''));
    }

    // Kirim $idFileLama ke parameter ke-3 upload_file() agar file fisik lama dihapus otomatis
    $idFile = upload_file($_FILES['file'], 'kp', $idFileLama);
    if ($idFile === false) {
        echo json_encode(['status' => false, 'pesan' => 'Gagal mengunggah file']);
        exit;
    }

    // Simpan ke tabel berkas_kp (update atau insert)
    if ($sudahAda) {
        query("UPDATE berkas_kp SET file = '$idFile' WHERE id_persyaratan = $idSyarat AND nim = '$nim'");
    } else {
        query("INSERT INTO berkas_kp (id_persyaratan, nim, file) VALUES ($idSyarat, '$nim', '$idFile')");
    }

    // Cari progres dari persyaratan yang diunggah
    $qProgSyarat = query("SELECT progres FROM persyaratan_berkas_kp WHERE id = $idSyarat LIMIT 1");
    $progresSyarat = ($qProgSyarat && mysqli_num_rows($qProgSyarat) > 0) ? (int)mysqli_fetch_assoc($qProgSyarat)['progres'] : 13;
    $progresSelanjutnya = $progresSyarat + 1;

    // Cek apakah seluruh berkas untuk SOP progres ini sudah lengkap diunggah mahasiswa ini
    $qTotalSyarat = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_kp WHERE progres = $progresSyarat");
    $totalSyarat = $qTotalSyarat ? (int)mysqli_fetch_assoc($qTotalSyarat)['total'] : 0;

    $qUploaded = query("SELECT COUNT(DISTINCT b.id_persyaratan) AS total FROM berkas_kp b JOIN persyaratan_berkas_kp p ON b.id_persyaratan = p.id WHERE p.progres = $progresSyarat AND b.nim = '$nim'");
    $totalUploaded = $qUploaded ? (int)mysqli_fetch_assoc($qUploaded)['total'] : 0;

    $selesaiSemua = ($totalSyarat > 0 && $totalUploaded >= $totalSyarat);
    $pesanSelesai = 'Seluruh berkas komponen telah lengkap diunggah! Progres mahasiswa dimajukan.';
    $tahapBaru    = $progresSelanjutnya;

    if ($selesaiSemua) {
        // 1. Majukan progres otomatis ke tahap berikutnya (misal: SOP 13 -> SOP 14)
        $currProg = get_progres_kp($nim);
        if ((int)$currProg < $progresSelanjutnya) {
            set_progres_kp($nim, $progresSelanjutnya);
            $tahapBaru = $progresSelanjutnya;
        } else {
            $tahapBaru = (int)$currProg;
        }

        // 2. Jika saat ini berada di SOP 14, segera cek apakah berkas persyaratan SOP 14 sudah lengkap di temp_kp
        if ($tahapBaru === 14) {
            $qSyarat14 = query("SELECT id FROM persyaratan_berkas_kp WHERE progres = 14");
            $totalSyarat14 = ($qSyarat14 && mysqli_num_rows($qSyarat14) > 0) ? mysqli_num_rows($qSyarat14) : 0;

            if ($totalSyarat14 > 0) {
                $qValid14 = query("SELECT COUNT(DISTINCT id_persyaratan) AS cnt FROM temp_kp WHERE nim = '$nim' AND status != 'ditolak' AND id_persyaratan IN (SELECT id FROM persyaratan_berkas_kp WHERE progres = 14)");
                $jmlValid14 = ($qValid14 && mysqli_num_rows($qValid14) > 0) ? (int)mysqli_fetch_assoc($qValid14)['cnt'] : 0;

                // Jika semua berkas SOP 14 sudah diunggah ke temp_kp, langsung naik ke SOP 15!
                if ($jmlValid14 >= $totalSyarat14) {
                    set_progres_kp($nim, 15);
                    $tahapBaru = 15;
                    $pesanSelesai = 'Seluruh berkas SOP 13 & berkas revisi SOP 14 telah lengkap! Progres otomatis langsung dimajukan ke SOP 15.';
                } else {
                    $pesanSelesai = 'Seluruh berkas komponen SOP 13 telah lengkap diunggah! Progres mahasiswa dimajukan ke SOP 14.';
                }
            } else {
                $pesanSelesai = 'Seluruh berkas komponen SOP 13 telah lengkap diunggah! Progres mahasiswa dimajukan ke SOP 14.';
            }
        }
    }

    echo json_encode([
        'status'        => true,
        'pesan'         => 'Berkas berhasil diunggah dan disimpan ke berkas KP',
        'selesai_semua' => $selesaiSemua,
        'progres_baru'  => $tahapBaru,
        'pesan_selesai' => $pesanSelesai
    ]);
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
    if ($currentStage === '-' || $currentStage === '' || $currentStage === null) $currentStage = 1;
    $currentStage = (int)$currentStage;

    // Ambil list SOP KP
    $sopList = get_sop_kp_list();
    $sopMap = [];
    foreach ($sopList as $sp) {
        $sopMap[(int)$sp['no']] = $sp['keterangan'];
    }
    $labelProgresAktif = $sopMap[$currentStage] ?? 'Tahapan Kerja Praktek';

    // Ambil riwayat penyelesaian tahapan dari tabel progres_kp
    $riwayatWaktuSop = [];
    try {
        $qProg = query("SELECT * FROM progres_kp WHERE mahasiswa = '$nim'");
        if ($qProg) {
            while ($pr = mysqli_fetch_assoc($qProg)) {
                $pNo = (int)($pr['proses'] ?? $pr['progres'] ?? 0);
                $tUnix = 0;
                if (!empty($pr['waktu']) && is_numeric($pr['waktu']) && (int)$pr['waktu'] > 0) {
                    $tUnix = (int)$pr['waktu'];
                } elseif (!empty($pr['id']) && is_numeric($pr['id']) && (int)$pr['id'] > 1000000000) {
                    $tUnix = (int)$pr['id'];
                }
                if ($pNo > 0 && $tUnix > 0) {
                    $riwayatWaktuSop[$pNo] = $tUnix;
                }
            }
        }
    } catch (\Throwable $e) {}

    $formatWaktuSop = function($timeUnix) {
        if (!$timeUnix || !is_numeric($timeUnix) || (int)$timeUnix <= 0) return '-';
        $bulanIndo = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
        $t = (int)$timeUnix;
        $d = date('d', $t);
        $m = (int)date('n', $t);
        $y = date('Y', $t);
        $jam = date('H:i', $t);
        $b = $bulanIndo[$m] ?? date('M', $t);
        return "$d $b $y, $jam";
    };

    $getWaktuSelesaiSop = function($noSop, $riwayatWaktuSop, $currentStage) {
        // 1. Cek waktu transisi ke tahap berikutnya jika ada (misal selesai SOP 3 saat masuk SOP 4)
        if (isset($riwayatWaktuSop[$noSop + 1]) && $riwayatWaktuSop[$noSop + 1] > 0) {
            return $riwayatWaktuSop[$noSop + 1];
        }
        // 2. Cek waktu tercatat pada tahap tersebut
        if (isset($riwayatWaktuSop[$noSop]) && $riwayatWaktuSop[$noSop] > 0) {
            return $riwayatWaktuSop[$noSop];
        }
        // 3. Cari catatan proses berikutnya yang terdekat
        foreach ($riwayatWaktuSop as $p => $w) {
            if ($p >= $noSop && $w > 0) {
                return $w;
            }
        }
        if (!empty($riwayatWaktuSop)) {
            return reset($riwayatWaktuSop);
        }
        return 0;
    };

    // Ambil detail data KP mahasiswa
    $kpDetail = mysqli_fetch_assoc(query("SELECT * FROM kp WHERE nim = '$nim' LIMIT 1"));

    // Ambil surat-surat penunjukkan KP mahasiswa ini
    $suratBimb = null;
    if (!empty($kpDetail['surat_penunjukkan_pembimbing'])) {
        $suratBimb = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE id = '{$kpDetail['surat_penunjukkan_pembimbing']}' LIMIT 1"));
    }
    if (!$suratBimb) {
        $suratBimb = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING KP' ORDER BY id DESC LIMIT 1"));
    }

    $suratPeng = null;
    if (!empty($kpDetail['surat_penunjukkan_penguji'])) {
        $suratPeng = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE id = '{$kpDetail['surat_penunjukkan_penguji']}' LIMIT 1"));
    }
    if (!$suratPeng) {
        $suratPeng = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE nim = '$nim' AND (keterangan = 'PENGUJI KP' OR keterangan LIKE '%PENGUJI%KP%' OR keterangan LIKE '%SEMINAR%KP%') ORDER BY id DESC LIMIT 1"));
    }

    // Ambil dosen names
    $dosenListQ = query("SELECT id, nama FROM dosen");
    $dosenNames = [];
    if ($dosenListQ) {
        while ($d = mysqli_fetch_assoc($dosenListQ)) {
            $dosenNames[$d['id']] = $d['nama'];
        }
    }

    // Ambil master seluruh persyaratan berkas KP dikelompokkan berdasarkan nomor progres (SOP)
    $masterSyarat = [];
    $qMs = query("SELECT id, nama_berkas, progres FROM persyaratan_berkas_kp ORDER BY progres ASC, id ASC");
    if ($qMs) {
        while ($ms = mysqli_fetch_assoc($qMs)) {
            $masterSyarat[(int)$ms['progres']][] = $ms;
        }
    }

    // Ambil berkas mahasiswa dari tabel berkas_kp (Tabel UTAMA berkas tersimpan)
    $berkasMap = [];
    $qBk = query("SELECT bk.id AS id_berkas, bk.id_persyaratan, bk.file, f.nama_file 
                  FROM berkas_kp bk 
                  LEFT JOIN files f ON bk.file = f.id 
                  WHERE bk.nim = '$nim'");
    if ($qBk) {
        while ($br = mysqli_fetch_assoc($qBk)) {
            $berkasMap[(int)$br['id_persyaratan']] = [
                'id'        => (int)$br['id_berkas'],
                'file'      => $br['file'],
                'nama_file' => $br['nama_file'],
                'status'    => 'tersimpan',
                'ket'       => 'Tersimpan di berkas KP'
            ];
        }
    }

    // Lengkapi dengan berkas dari temp_kp
    $qTemp = query("SELECT tk.id AS id_temp, tk.id_persyaratan, tk.file, tk.status, tk.ket, f.nama_file 
                    FROM temp_kp tk 
                    LEFT JOIN files f ON tk.file = f.id 
                    WHERE tk.nim = '$nim'");
    if ($qTemp) {
        while ($tr = mysqli_fetch_assoc($qTemp)) {
            $idP = (int)$tr['id_persyaratan'];
            if (!isset($berkasMap[$idP]) || empty($berkasMap[$idP]['file'])) {
                $berkasMap[$idP] = [
                    'id'        => (int)$tr['id_temp'],
                    'file'      => $tr['file'],
                    'nama_file' => $tr['nama_file'],
                    'status'    => $tr['status'],
                    'ket'       => $tr['ket']
                ];
            }
        }
    }

    // Otoritas wewenang unggah ulang berkas: Level 5 (Kaprodi), 6 (Pimpinan/KTU), 9 (Super Admin) - sesuai hirarki Skripsi
    $canUploadBerkas = in_array($userLevel, [5, 6, 9], true);

    // Helper render daftar berkas mahasiswa/operator + fitur lihat & unggah ulang
    $renderListBerkas = function($listSyarat, $berkasMap, $nim, $labelSection = 'Berkas Persyaratan') use ($canUploadBerkas) {
        if (empty($listSyarat)) return '';
        ob_start();
        ?>
        <div class="mt-3.5 pt-3 border-t border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <h5 class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                    <i class="fas fa-folder-open text-amber-500"></i> <?= tampil($labelSection) ?>
                </h5>
                <span class="text-[10px] text-slate-400 font-medium">Format: PDF</span>
            </div>
            <div class="space-y-2">
                <?php foreach ($listSyarat as $syarat): 
                    $idSyarat = (int)$syarat['id'];
                    $itemUploaded = $berkasMap[$idSyarat] ?? null;
                    $hasFile = ($itemUploaded && !empty($itemUploaded['file']));
                    $fileUrl = $hasFile ? url_file($itemUploaded['file']) : '';
                    $rawStatus = strtolower(trim((string)($itemUploaded['status'] ?? ($hasFile ? 'tersimpan' : 'belum ada'))));
                    
                    $statusBadgeClass = 'bg-slate-100 text-slate-500';
                    $statusLabel = 'Belum Diunggah';
                    if ($rawStatus === 'diterima' || $rawStatus === 'tersimpan') {
                        $statusBadgeClass = 'bg-emerald-100 text-emerald-700';
                        $statusLabel = 'Tersimpan';
                    } elseif ($rawStatus === 'menunggu') {
                        $statusBadgeClass = 'bg-amber-100 text-amber-700';
                        $statusLabel = 'Menunggu';
                    } elseif ($rawStatus === 'ditolak') {
                        $statusBadgeClass = 'bg-rose-100 text-rose-700';
                        $statusLabel = 'Ditolak';
                    }
                ?>
                <div class="p-3 rounded-lg border border-slate-200 bg-white hover:border-slate-300 transition shadow-2xs item-berkas-row" data-syarat-id="<?= $idSyarat ?>">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-800 leading-snug">
                                <i class="fas fa-file-pdf mr-1.5 <?= $hasFile ? 'text-rose-500' : 'text-slate-300' ?>"></i>
                                <?= tampil($syarat['nama_berkas']) ?>
                            </p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold <?= $statusBadgeClass ?> status-berkas-tag">
                                    <?= $statusLabel ?>
                                </span>
                                <?php if (!empty($itemUploaded['ket'])): ?>
                                    <span class="text-[10px] text-slate-500 italic truncate max-w-[240px]" title="<?= tampil($itemUploaded['ket']) ?>">
                                        "<?= tampil($itemUploaded['ket']) ?>"
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="<?= $fileUrl ?>" target="_blank" class="link-lihat-file inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition <?= $hasFile ? '' : 'hidden' ?>">
                                <i class="fas fa-eye text-orange-500"></i> Lihat ↗
                            </a>

                            <?php if ($canUploadBerkas): ?>
                            <label class="cursor-pointer inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition shadow-2xs btn-upload-ulang-label">
                                <i class="fas fa-cloud-arrow-up"></i> <span class="label-txt"><?= $hasFile ? 'Unggah Ulang' : 'Unggah File' ?></span>
                                <input type="file" accept=".pdf" class="hidden input-ganti-berkas" 
                                       data-id-syarat="<?= $idSyarat ?>" 
                                       data-nim="<?= tampil($nim) ?>">
                            </label>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    };

    ob_start();
    ?>
    <!-- Indikator Tahap Aktif Mahasiswa Saat Ini -->
    <div class="flex items-center justify-between p-3.5 rounded-xl bg-orange-50 border border-orange-200/90 mb-4 text-xs shadow-2xs">
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-white text-xs font-bold shadow-xs">
                <i class="fas fa-play text-[10px]"></i>
            </span>
            <div class="min-w-0">
                <span class="text-[11px] text-slate-500 block leading-tight">Tahapan Sedang Berjalan:</span>
                <p class="font-bold text-orange-800 leading-snug truncate">
                    SOP <?= $currentStage ?>: <?= tampil($labelProgresAktif) ?>
                </p>
            </div>
        </div>
        <span class="shrink-0 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-orange-200/70 text-orange-800 border border-orange-300/60">
            Sedang Berlangsung
        </span>
    </div>

    <!-- ===== Toolbar Kontrol Accordion ===== -->
    <div class="flex items-center justify-between mb-3 px-1">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Alur SOP Kerja Praktek</span>
            <span class="text-[11px] font-medium text-slate-400">(<?= count($sopList) ?> Tahapan)</span>
        </div>
        <div class="flex items-center gap-1.5">
            <button type="button" id="btnBukaSemuaAcc" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                <i class="fas fa-angle-double-down mr-1"></i> Buka Selesai
            </button>
            <button type="button" id="btnTutupSemuaAcc" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                <i class="fas fa-angle-double-up mr-1"></i> Tutup Semua
            </button>
        </div>
    </div>

    <!-- ===== Timeline Accordion Sequential Berdasarkan SOP KP ===== -->
    <div class="space-y-3" id="accordionArsip">
        <?php foreach ($sopList as $idx => $sop):
            $stepNum = (int)$sop['no'];
            $ketSop = $sop['keterangan'];
            $eksekutorSop = $sop['eksekutor'] ?? '';

            $isCompleted = ($stepNum < $currentStage);
            $isCurrent   = ($stepNum === $currentStage);
            $isFuture    = ($stepNum > $currentStage);
            $canExpand   = $isCompleted; // HANYA SOP yang sudah selesai yang bisa dibuka accordionnya

            $waktuSelesaiUnix = $isCompleted ? $getWaktuSelesaiSop($stepNum, $riwayatWaktuSop, $currentStage) : 0;
            $waktuSelesaiFormatted = ($waktuSelesaiUnix > 0) ? $formatWaktuSop($waktuSelesaiUnix) : '-';

            // Tentukan status badge
            if ($isCompleted) {
                $badgeText = 'Selesai';
                $badgeStyle = 'bg-emerald-100 text-emerald-700 border border-emerald-200/60 font-semibold';
                $iconCircle = 'bg-emerald-500 text-white';
            } elseif ($isCurrent) {
                $badgeText = 'Sedang Berjalan';
                $badgeStyle = 'bg-orange-100 text-orange-700 border border-orange-200/60 font-bold';
                $iconCircle = 'bg-orange-500 text-white ring-2 ring-orange-400/40';
            } else {
                $badgeText = 'Belum Ditempuh';
                $badgeStyle = 'bg-slate-100 text-slate-500 font-medium';
                $iconCircle = 'bg-slate-200 text-slate-500';
            }
        ?>
        <div class="border border-slate-200 rounded-xl bg-white shadow-xs overflow-hidden transition-all duration-200 <?= $canExpand ? 'card-completed' : 'card-locked' ?>" data-card-stage="<?= $stepNum ?>">
            <!-- Header Kartu SOP -->
            <?php if ($canExpand): ?>
            <div class="flex items-center justify-between p-3.5 cursor-pointer bg-slate-50 hover:bg-slate-100/80 transition btn-toggle-accordion">
            <?php else: ?>
            <div class="flex items-center justify-between p-3.5 bg-slate-50/50 cursor-default select-none opacity-85" title="Tahapan sedang berlangsung atau belum ditempuh (tidak dapat dibuka)">
            <?php endif; ?>
                <div class="flex items-center gap-3 min-w-0">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg <?= $iconCircle ?> text-xs font-bold shadow-xs">
                        <?php if ($isCompleted): ?>
                            <i class="fas fa-check"></i>
                        <?php elseif ($isCurrent): ?>
                            <span class="text-[11px] font-bold"><?= $stepNum ?></span>
                        <?php else: ?>
                            <span class="text-[11px] font-medium text-slate-500"><?= $stepNum ?></span>
                        <?php endif; ?>
                    </span>
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-slate-800 leading-snug">
                            SOP <?= $stepNum ?>: <?= tampil($ketSop) ?>
                        </h4>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-0.5 text-[10px]">
                            <?php if (!empty($eksekutorSop)): ?>
                                <span class="text-slate-500">Eksekutor: <strong class="text-slate-700 font-semibold"><?= tampil($eksekutorSop) ?></strong></span>
                            <?php endif; ?>
                            <?php if ($isCompleted && !empty($waktuSelesaiFormatted) && $waktuSelesaiFormatted !== '-'): ?>
                                <span class="inline-flex items-center gap-1 font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-1.5 py-0.5 rounded text-[10px]">
                                    <i class="fas fa-check-circle text-emerald-600 text-[10px]"></i> Selesai: <?= $waktuSelesaiFormatted ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-[11px] px-2 py-0.5 rounded <?= $badgeStyle ?>">
                        <?= $badgeText ?>
                    </span>
                    <?php if ($canExpand): ?>
                        <i class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200 acc-chevron"></i>
                    <?php else: ?>
                        <span title="Tahapan belum selesai (tidak dapat dibuka)">
                            <i class="fas fa-lock text-[11px] text-slate-300"></i>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($canExpand): ?>
            <!-- Body Kartu SOP (HANYA UNTUK SOP YANG SUDAH SELESAI/LAMPAU) -->
            <div class="acc-body p-4 border-t border-slate-100 hidden space-y-3.5 text-xs" style="display: none;">
                
                <?php
                switch ((int)$stepNum):
                    case 1:
                        // SOP 1: Data Pokok Judul, Tempat & Status KP
                        ?>
                        <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60">
                            <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                                <h5 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <i class="fas fa-file-signature text-orange-500"></i> Data Pokok Judul, Tempat &amp; Status KP
                                </h5>
                                <button type="button" onclick="toggleEditBox(this, true)" class="btn-toggle-edit inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition shadow-2xs">
                                    <i class="fas fa-pen-to-square"></i> Koreksi Data
                                </button>
                            </div>

                            <!-- View Mode -->
                            <div class="view-box space-y-3">
                                <div>
                                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Judul Kerja Praktek</span>
                                    <p class="text-xs font-medium text-slate-800 bg-white p-3 rounded-lg border border-slate-200/80 leading-relaxed target-judul-display">
                                        <?= !empty($kpDetail['judul']) ? tampil($kpDetail['judul']) : '<em class="text-slate-400">Belum ada judul tercatat</em>' ?>
                                    </p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Tempat / Instansi KP</span>
                                        <p class="text-xs font-medium text-slate-800 bg-white p-3 rounded-lg border border-slate-200/80 leading-relaxed">
                                            <?= !empty($kpDetail['nama_instansi']) ? tampil($kpDetail['nama_instansi']) : (!empty($kpDetail['instansi']) ? tampil($kpDetail['instansi']) : (!empty($kpDetail['tempat_kp']) ? tampil($kpDetail['tempat_kp']) : '-')) ?>
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Status KP</span>
                                        <div class="p-2.5 bg-white rounded-lg border border-slate-200/80">
                                            <span class="text-xs font-bold text-slate-700 bg-white border border-slate-200 px-2.5 py-1 rounded inline-block target-status-display">
                                                <?= tampil($kpDetail['status'] ?? '-') ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Mode (Hidden) -->
                            <div class="edit-box hidden pt-1" style="display: none;">
                                <form class="form-koreksi-arsip space-y-3" data-step="data-pokok">
                                    <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                                    <input type="hidden" name="tipe_koreksi" value="data-pokok">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Judul Kerja Praktek</label>
                                        <textarea name="judul" rows="2" class="w-full text-xs rounded-lg border border-slate-300 p-2.5 focus:border-orange-500 focus:outline-none" required><?= tampil($kpDetail['judul'] ?? '') ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Tempat / Instansi KP</label>
                                        <input type="text" name="instansi" value="<?= tampil($kpDetail['nama_instansi'] ?? ($kpDetail['instansi'] ?? ($kpDetail['tempat_kp'] ?? ''))) ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Status KP</label>
                                        <select name="status" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none">
                                            <option value="Aktif" <?= in_array(strtolower($kpDetail['status'] ?? ''), ['aktif', 'bimbingan']) ? 'selected' : '' ?>>Aktif</option>
                                            <option value="tidak aktif" <?= strtolower($kpDetail['status'] ?? '') === 'tidak aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                                            <option value="Selesai" <?= in_array(strtolower($kpDetail['status'] ?? ''), ['selesai', 'alumni']) ? 'selected' : '' ?>>Selesai</option>
                                            <option value="Kadaluarsa" <?= strtolower($kpDetail['status'] ?? '') === 'kadaluarsa' ? 'selected' : '' ?>>Kadaluarsa</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                                        <button type="button" onclick="toggleEditBox(this, false)" class="btn-cancel-edit px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                                        <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-orange-600 hover:bg-orange-700 text-white shadow-xs transition">Simpan Koreksi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php if (!empty($masterSyarat[1])): ?>
                            <?= $renderListBerkas($masterSyarat[1], $berkasMap, $nim, 'Berkas Persyaratan (SOP 1)') ?>
                        <?php endif; ?>
                        <?php
                        break;

                    case 6:
                        // SOP 6: Surat Penunjukkan Pembimbing KP
                        $p1Id = $suratBimb['pembimbing1'] ?? '';
                        $p1Name = !empty($p1Id) ? ($dosenNames[$p1Id] ?? $p1Id) : '';
                        $idSuratBimb = $suratBimb['id'] ?? '';
                        $noSuratBimb = $suratBimb['no_surat'] ?? '';
                        $tglSuratBimb = $suratBimb['tanggal'] ?? '';
                        ?>
                        <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60">
                            <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                                <h5 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <i class="fas fa-file-contract text-orange-500"></i> Surat Penunjukkan Pembimbing KP
                                </h5>
                                <button type="button" onclick="toggleEditBox(this, true)" class="btn-toggle-edit inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition shadow-2xs">
                                    <i class="fas fa-pen-to-square"></i> Koreksi Data
                                </button>
                            </div>

                            <!-- View Mode -->
                            <div class="view-box space-y-2.5">
                                <div class="grid grid-cols-2 gap-2.5 bg-white p-3 rounded-lg border border-slate-200/80 text-xs">
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">No. Surat</span>
                                        <span class="font-bold text-slate-800"><?= !empty($noSuratBimb) ? tampil($noSuratBimb) : '-' ?></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal Surat</span>
                                        <span class="font-bold text-slate-800"><?= !empty($tglSuratBimb) ? tampil($tglSuratBimb) : '-' ?></span>
                                    </div>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-slate-200/80 text-xs">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Dosen Pembimbing</span>
                                    <span class="font-bold text-slate-800"><?= !empty($p1Name) ? tampil($p1Name) : '<em class="text-slate-400 font-normal">Belum diatur</em>' ?></span>
                                </div>
                            </div>

                            <!-- Edit Mode (Hidden) -->
                            <div class="edit-box hidden pt-1" style="display: none;">
                                <form class="form-koreksi-arsip space-y-3" data-step="surat-pembimbing">
                                    <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                                    <input type="hidden" name="id_surat" value="<?= $idSuratBimb ?>">
                                    <input type="hidden" name="tipe_koreksi" value="surat-pembimbing">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">No. Surat</label>
                                            <input type="text" name="no_surat" value="<?= tampil($noSuratBimb) ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" placeholder="Contoh: 123/UN47.B5/TU/2026" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Surat</label>
                                            <input type="date" name="tanggal" value="<?= tampil(!empty($tglSuratBimb) ? $tglSuratBimb : date('Y-m-d')) ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Dosen Pembimbing</label>
                                        <select name="pembimbing1" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                            <option value="">-- Pilih Dosen Pembimbing --</option>
                                            <?php foreach ($dosenNames as $dId => $dNm): ?>
                                                <option value="<?= $dId ?>" <?= ($dId == $p1Id) ? 'selected' : '' ?>><?= tampil($dNm) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                                        <button type="button" onclick="toggleEditBox(this, false)" class="btn-cancel-edit px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                                        <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-orange-600 hover:bg-orange-700 text-white shadow-xs transition">Simpan Koreksi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php if (!empty($masterSyarat[6])): ?>
                            <?= $renderListBerkas($masterSyarat[6], $berkasMap, $nim, 'Berkas Persyaratan (SOP 6)') ?>
                        <?php endif; ?>
                        <?php
                        break;

                    case 7:
                        // SOP 7: Surat Pengantar Penarikan KP
                        $tempatKpArsip = !empty($kpDetail['nama_instansi']) ? $kpDetail['nama_instansi'] : (!empty($kpDetail['instansi']) ? $kpDetail['instansi'] : (!empty($kpDetail['tempat']) ? $kpDetail['tempat'] : (!empty($kpDetail['tempat_kp']) ? $kpDetail['tempat_kp'] : '-')));
                        $urlSuratPenarikanArsip = base_url() . "/administrator/dokumen/surat-penarikan-kp/" . $nim;
                        $qNomorArsip = query("SELECT nomor FROM nomor_surat WHERE keterangan LIKE '%Penarikan KP%' AND keterangan LIKE '%$nim%' ORDER BY id DESC LIMIT 1");
                        $nomorSuratArsip = ($qNomorArsip && $rNA = mysqli_fetch_assoc($qNomorArsip)) ? $rNA['nomor'] : '-';
                        ?>
                        <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60">
                            <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                                <h5 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <i class="fas fa-file-invoice text-orange-500"></i> Surat Pengantar Penarikan KP
                                </h5>
                                <a href="<?= $urlSuratPenarikanArsip ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition shadow-2xs">
                                    <i class="fas fa-print"></i> Buka / Cetak Dokumen ↗
                                </a>
                            </div>
                            <div class="bg-white p-3 rounded-lg border border-slate-200/80 text-xs space-y-2">
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Instansi / Tempat KP</span>
                                        <span class="font-bold text-slate-800"><?= tampil($tempatKpArsip) ?></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">No. Surat</span>
                                        <span class="font-bold text-slate-800"><?= tampil($nomorSuratArsip) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($masterSyarat[7])): ?>
                            <?= $renderListBerkas($masterSyarat[7], $berkasMap, $nim, 'Berkas Persyaratan (SOP 7)') ?>
                        <?php endif; ?>
                        <?php
                        break;

                    case 12:
                        // SOP 12: Surat Penunjukkan Penguji KP (Penguji 1 - 3)
                        $pg1Id = $suratPeng['penguji1'] ?? '';
                        $pg2Id = $suratPeng['penguji2'] ?? '';
                        $pg3Id = $suratPeng['penguji3'] ?? '';
                        $pg1Name = !empty($pg1Id) ? ($dosenNames[$pg1Id] ?? $pg1Id) : '';
                        $pg2Name = !empty($pg2Id) ? ($dosenNames[$pg2Id] ?? $pg2Id) : '';
                        $pg3Name = !empty($pg3Id) ? ($dosenNames[$pg3Id] ?? $pg3Id) : '';
                        $idSuratPeng = $suratPeng['id'] ?? '';
                        $noSuratPeng = $suratPeng['no_surat'] ?? '';
                        $tglSuratPeng = $suratPeng['tanggal'] ?? '';
                        ?>
                        <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60">
                            <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                                <h5 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <i class="fas fa-file-contract text-orange-500"></i> Surat Penunjukkan Penguji KP
                                </h5>
                                <button type="button" onclick="toggleEditBox(this, true)" class="btn-toggle-edit inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition shadow-2xs">
                                    <i class="fas fa-pen-to-square"></i> Koreksi Data
                                </button>
                            </div>

                            <!-- View Mode -->
                            <div class="view-box space-y-2.5">
                                <div class="grid grid-cols-2 gap-2.5 bg-white p-3 rounded-lg border border-slate-200/80 text-xs">
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">No. Surat</span>
                                        <span class="font-bold text-slate-800"><?= !empty($noSuratPeng) ? tampil($noSuratPeng) : '-' ?></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal Surat</span>
                                        <span class="font-bold text-slate-800"><?= !empty($tglSuratPeng) ? tampil($tglSuratPeng) : '-' ?></span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2.5 bg-white p-3 rounded-lg border border-slate-200/80 text-xs">
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Penguji 1</span>
                                        <span class="font-bold text-slate-800"><?= !empty($pg1Name) ? tampil($pg1Name) : '<em class="text-slate-400 font-normal">Belum diatur</em>' ?></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Penguji 2</span>
                                        <span class="font-bold text-slate-800"><?= !empty($pg2Name) ? tampil($pg2Name) : '<em class="text-slate-400 font-normal">-</em>' ?></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Penguji 3</span>
                                        <span class="font-bold text-slate-800"><?= !empty($pg3Name) ? tampil($pg3Name) : '<em class="text-slate-400 font-normal">-</em>' ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Mode (Hidden) -->
                            <div class="edit-box hidden pt-1" style="display: none;">
                                <form class="form-koreksi-arsip space-y-3" data-step="surat-penguji">
                                    <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                                    <input type="hidden" name="id_surat" value="<?= $idSuratPeng ?>">
                                    <input type="hidden" name="tipe_koreksi" value="surat-penguji">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">No. Surat</label>
                                            <input type="text" name="no_surat" value="<?= tampil($noSuratPeng) ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" placeholder="Contoh: 124/UN47.B5/TU/2026" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Surat</label>
                                            <input type="date" name="tanggal" value="<?= tampil(!empty($tglSuratPeng) ? $tglSuratPeng : date('Y-m-d')) ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Dosen Penguji 1</label>
                                        <select name="penguji1" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                            <option value="">-- Pilih Dosen Penguji 1 --</option>
                                            <?php foreach ($dosenNames as $dId => $dNm): ?>
                                                <option value="<?= $dId ?>" <?= ($dId == $pg1Id) ? 'selected' : '' ?>><?= tampil($dNm) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Dosen Penguji 2</label>
                                        <select name="penguji2" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none">
                                            <option value="">-- Tidak Ada / Kosong --</option>
                                            <?php foreach ($dosenNames as $dId => $dNm): ?>
                                                <option value="<?= $dId ?>" <?= ($dId == $pg2Id) ? 'selected' : '' ?>><?= tampil($dNm) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Dosen Penguji 3</label>
                                        <select name="penguji3" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none">
                                            <option value="">-- Tidak Ada / Kosong --</option>
                                            <?php foreach ($dosenNames as $dId => $dNm): ?>
                                                <option value="<?= $dId ?>" <?= ($dId == $pg3Id) ? 'selected' : '' ?>><?= tampil($dNm) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                                        <button type="button" onclick="toggleEditBox(this, false)" class="btn-cancel-edit px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                                        <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-orange-600 hover:bg-orange-700 text-white shadow-xs transition">Simpan Koreksi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php if (!empty($masterSyarat[12])): ?>
                            <?= $renderListBerkas($masterSyarat[12], $berkasMap, $nim, 'Berkas Persyaratan (SOP 12)') ?>
                        <?php endif; ?>
                        <?php
                        break;

                    case 13:
                        // SOP 13: Jadwal Seminar Kerja Praktek
                        $tglSem = !empty($kpDetail['tanggal_seminar']) ? $kpDetail['tanggal_seminar'] : ($kpDetail['tanggal'] ?? '');
                        $jamSem = !empty($kpDetail['jam_seminar']) ? $kpDetail['jam_seminar'] : ($kpDetail['jam'] ?? '');
                        $ruangSem = $kpDetail['ruangan'] ?? '';
                        $noUnd = $kpDetail['no_undangan'] ?? '';
                        ?>
                        <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60">
                            <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                                <h5 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <i class="fas fa-calendar-check text-orange-500"></i> Jadwal Seminar Kerja Praktek
                                </h5>
                                <button type="button" onclick="toggleEditBox(this, true)" class="btn-toggle-edit inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition shadow-2xs">
                                    <i class="fas fa-pen-to-square"></i> Koreksi Data
                                </button>
                            </div>

                            <!-- View Mode -->
                            <div class="view-box space-y-2.5">
                                <div class="grid grid-cols-2 gap-2.5 bg-white p-3 rounded-lg border border-slate-200/80 text-xs">
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">No. Undangan</span>
                                        <span class="font-bold text-slate-800"><?= !empty($noUnd) ? tampil($noUnd) : '-' ?></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal</span>
                                        <span class="font-bold text-slate-800"><?= !empty($tglSem) ? tampil($tglSem) : '-' ?></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Jam</span>
                                        <span class="font-bold text-slate-800"><?= !empty($jamSem) ? tampil($jamSem) : '-' ?></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Ruangan</span>
                                        <span class="font-bold text-slate-800"><?= !empty($ruangSem) ? tampil($ruangSem) : '-' ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Mode (Hidden) -->
                            <div class="edit-box hidden pt-1" style="display: none;">
                                <form class="form-koreksi-arsip space-y-3" data-step="jadwal-seminar">
                                    <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                                    <input type="hidden" name="tipe_koreksi" value="jadwal-seminar">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">No. Undangan</label>
                                        <input type="text" name="no_undangan" value="<?= tampil($noUnd) ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal</label>
                                            <input type="date" name="tanggal_seminar" value="<?= tampil($tglSem) ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Jam</label>
                                            <input type="text" name="jam_seminar" value="<?= tampil($jamSem) ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Ruangan</label>
                                        <input type="text" name="ruangan" value="<?= tampil($ruangSem) ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                    </div>
                                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                                        <button type="button" onclick="toggleEditBox(this, false)" class="btn-cancel-edit px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                                        <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-orange-600 hover:bg-orange-700 text-white shadow-xs transition">Simpan Koreksi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php if (!empty($masterSyarat[13])): ?>
                            <?= $renderListBerkas($masterSyarat[13], $berkasMap, $nim, 'Berkas Persyaratan (SOP 13)') ?>
                        <?php endif; ?>
                        <?php
                        break;

                    default:
                        // Tahapan lainnya: render berkas jika ada di persyaratan_berkas_kp (misal step 2, 4, 8, 10, 14)
                        if (!empty($masterSyarat[$stepNum])) {
                            echo $renderListBerkas($masterSyarat[$stepNum], $berkasMap, $nim, 'Berkas Persyaratan (SOP ' . $stepNum . ')');
                        } else {
                            ?>
                            <div class="py-2.5 px-3.5 rounded-lg bg-slate-50 border border-slate-200/80 text-xs text-slate-600">
                                <p class="leading-relaxed">
                                    <i class="fas fa-info-circle text-orange-500 mr-1.5"></i>
                                    Tahapan ini merupakan proses verifikasi/persetujuan oleh <span class="font-semibold text-slate-800"><?= tampil($eksekutorSop !== '' ? $eksekutorSop : 'Pengelola') ?></span>.
                                    <?php if ($isCompleted): ?>
                                        <span class="text-emerald-600 font-semibold block mt-1"><i class="fas fa-check-circle mr-1"></i>Tahapan ini telah selesai ditempuh<?= ($waktuSelesaiFormatted !== '-' ? ' pada ' . $waktuSelesaiFormatted : '') ?>.</span>
                                    <?php elseif ($isCurrent): ?>
                                        <span class="text-orange-600 font-semibold block mt-1"><i class="fas fa-spinner fa-spin mr-1"></i>Mahasiswa saat ini sedang dalam tahapan ini.</span>
                                    <?php else: ?>
                                        <span class="text-slate-400 block mt-1"><i class="fas fa-clock mr-1"></i>Tahapan ini belum dimulai.</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <?php
                        }
                        break;
                endswitch;
                ?>

                <div class="text-[11px] text-slate-500 flex items-center justify-between pt-2 border-t border-slate-200/60 mt-3">
                    <span class="flex items-center gap-1.5 text-emerald-600 font-medium">
                        <i class="fas fa-check-circle"></i>
                        Tahap ini telah diselesaikan <?= ($waktuSelesaiFormatted !== '-' ? 'pada ' . $waktuSelesaiFormatted : '') ?>
                    </span>
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
    if (ob_get_length()) ob_clean();

    $nim = bersihkan($_POST['nim'] ?? '');
    $tipe = bersihkan($_POST['tipe_koreksi'] ?? '');

    if ($nim === '') {
        echo json_encode(['status' => false, 'pesan' => 'NIM tidak valid']);
        exit;
    }

    try {
        if ($tipe === 'data-pokok') {
            $judul    = bersihkan($_POST['judul'] ?? '');
            $instansi = bersihkan($_POST['instansi'] ?? '');
            $status   = bersihkan($_POST['status'] ?? '');

            if ($judul === '' || $instansi === '') {
                echo json_encode(['status' => false, 'pesan' => 'Judul dan Tempat/Instansi KP wajib diisi']);
                exit;
            }

            // Dapatkan kolom tabel kp secara dinamis agar query selalu valid
            $colRes = query("SHOW COLUMNS FROM kp");
            $kpCols = [];
            if ($colRes) {
                while ($c = mysqli_fetch_assoc($colRes)) {
                    $kpCols[] = strtolower($c['Field']);
                }
            }

            $setFields = ["judul = '$judul'", "status = '$status'"];
            if (in_array('nama_instansi', $kpCols, true)) {
                $setFields[] = "nama_instansi = '$instansi'";
            }
            if (in_array('instansi', $kpCols, true)) {
                $setFields[] = "instansi = '$instansi'";
            }
            if (in_array('tempat', $kpCols, true)) {
                $setFields[] = "tempat = '$instansi'";
            }
            if (in_array('tempat_kp', $kpCols, true)) {
                $setFields[] = "tempat_kp = '$instansi'";
            }

            $up = query("UPDATE kp SET " . implode(', ', $setFields) . " WHERE nim = '$nim'");

            if ($up) {
                echo json_encode(['status' => true, 'pesan' => 'Data pokok Kerja Praktek berhasil diperbarui']);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui data pokok: ' . mysqli_error($GLOBALS['koneksi'])]);
            }
            exit;
        } elseif ($tipe === 'surat-pembimbing') {
            $idSurat = bersihkan($_POST['id_surat'] ?? '');
            $noSurat = bersihkan($_POST['no_surat'] ?? '');
            $p1 = bersihkan($_POST['pembimbing1'] ?? '');
            $tgl = bersihkan($_POST['tanggal'] ?? date('Y-m-d'));

            if ($noSurat === '' || $p1 === '') {
                echo json_encode(['status' => false, 'pesan' => 'Nomor surat dan Dosen Pembimbing wajib diisi']);
                exit;
            }

            if (empty($idSurat)) {
                $sKp = mysqli_fetch_assoc(query("SELECT surat_penunjukkan_pembimbing FROM kp WHERE nim = '$nim' LIMIT 1"));
                $idSurat = $sKp['surat_penunjukkan_pembimbing'] ?? 0;
                if (!$idSurat) {
                    $sSurat = mysqli_fetch_assoc(query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING KP' ORDER BY id DESC LIMIT 1"));
                    if ($sSurat) $idSurat = $sSurat['id'];
                }
            }

            if (!empty($idSurat)) {
                $up = query("UPDATE surat_penunjukkan SET no_surat = '$noSurat', pembimbing1 = '$p1', tanggal = '$tgl' WHERE id = '$idSurat'");
            } else {
                $idSurat = time();
                $up = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, pembimbing1, tanggal, keterangan) VALUES ($idSurat, '$noSurat', '$nim', '$p1', '$tgl', 'PEMBIMBING KP')");
                query("UPDATE kp SET surat_penunjukkan_pembimbing = '$idSurat' WHERE nim = '$nim'");
            }

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
            $pg3 = bersihkan($_POST['penguji3'] ?? '');
            $tgl = bersihkan($_POST['tanggal'] ?? date('Y-m-d'));

            if ($noSurat === '' || $pg1 === '') {
                echo json_encode(['status' => false, 'pesan' => 'Nomor surat dan Penguji 1 wajib diisi']);
                exit;
            }

            if (empty($idSurat)) {
                $sKp = mysqli_fetch_assoc(query("SELECT surat_penunjukkan_penguji FROM kp WHERE nim = '$nim' LIMIT 1"));
                $idSurat = $sKp['surat_penunjukkan_penguji'] ?? 0;
                if (!$idSurat) {
                    $sSurat = mysqli_fetch_assoc(query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PENGUJI KP' ORDER BY id DESC LIMIT 1"));
                    if ($sSurat) $idSurat = $sSurat['id'];
                }
            }

            if (!empty($idSurat)) {
                $up = query("UPDATE surat_penunjukkan SET no_surat = '$noSurat', penguji1 = '$pg1', penguji2 = '$pg2', penguji3 = '$pg3', tanggal = '$tgl' WHERE id = '$idSurat'");
            } else {
                $idSurat = time();
                $up = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, penguji1, penguji2, penguji3, tanggal, keterangan) VALUES ($idSurat, '$noSurat', '$nim', '$pg1', '$pg2', '$pg3', '$tgl', 'PENGUJI KP')");
                query("UPDATE kp SET surat_penunjukkan_penguji = '$idSurat' WHERE nim = '$nim'");
            }

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

            if ($noUnd === '' || $tglSem === '' || $jamSem === '' || $ruangSem === '') {
                echo json_encode(['status' => false, 'pesan' => 'Nomor undangan, tanggal, jam, dan ruangan wajib diisi']);
                exit;
            }

            $up = query("UPDATE kp SET no_undangan = '$noUnd', tanggal_seminar = '$tglSem', jam_seminar = '$jamSem', ruangan = '$ruangSem' WHERE nim = '$nim'");

            if ($up) {
                echo json_encode(['status' => true, 'pesan' => 'Koreksi jadwal seminar KP berhasil disimpan']);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan perubahan: ' . mysqli_error($GLOBALS['koneksi'])]);
            }
            exit;
        }

        echo json_encode(['status' => false, 'pesan' => 'Tipe koreksi tidak valid']);
        exit;
    } catch (\Throwable $e) {
        echo json_encode(['status' => false, 'pesan' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        exit;
    }
}

// ================= GANTI / UNGGAH ULANG BERKAS MAHASISWA & OPERATOR =================
if (isset($_POST['ganti-berkas-kp'])) {
    if (ob_get_length()) ob_clean();

    // Otoritas wewenang unggah ulang berkas: Level 5, 6, dan 9 (sesuai hirarki Skripsi)
    if (!in_array($userLevel, [5, 6, 9], true)) {
        echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: Anda tidak memiliki wewenang untuk mengunggah ulang berkas (hanya level 5, 6, dan 9)']);
        exit;
    }

    $nim = bersihkan($_POST['nim'] ?? '');
    $idPersyaratan = (int)($_POST['id_persyaratan'] ?? 0);

    if ($nim === '' || $idPersyaratan <= 0 || !isset($_FILES['file']) || $_FILES['file']['error'] !== 0) {
        echo json_encode(['status' => false, 'pesan' => 'File atau parameter tidak valid']);
        exit;
    }

    // Validasi file (PDF, ukuran, dll)
    $val = validasi_file($_FILES['file'], $userLevel);
    if (!$val['valid']) {
        echo json_encode(['status' => false, 'pesan' => $val['pesan']]);
        exit;
    }

    // 1. Cek berkas lama di berkas_kp (Tabel UTAMA berkas arsip)
    $idFileLama = '';
    $idRowBerkas = 0;
    $cekB = query("SELECT id, file FROM berkas_kp WHERE id_persyaratan = $idPersyaratan AND nim = '$nim' LIMIT 1");
    if ($cekB && mysqli_num_rows($cekB) > 0) {
        $rB = mysqli_fetch_assoc($cekB);
        $idRowBerkas = (int)$rB['id'];
        $idFileLama = trim((string)($rB['file'] ?? ''));
    }

    // Unggah file baru ke kategori 'kp' dan hapus file fisik lama
    $idFileBaru = upload_file($_FILES['file'], 'kp', $idFileLama);
    if (!$idFileBaru) {
        echo json_encode(['status' => false, 'pesan' => 'Gagal mengunggah file baru']);
        exit;
    }

    try {
        // 2. Simpan/update langsung ke berkas_kp (Arsip Berkas KP)
        if ($idRowBerkas > 0) {
            query("UPDATE berkas_kp SET file = '$idFileBaru' WHERE id = $idRowBerkas");
        } else {
            query("INSERT INTO berkas_kp (id_persyaratan, nim, file) VALUES ($idPersyaratan, '$nim', '$idFileBaru')");
        }
    } catch (\Throwable $e) {
        echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan ke berkas_kp: ' . $e->getMessage()]);
        exit;
    }

    $urlBaru = url_file($idFileBaru);

    echo json_encode([
        'status'   => true,
        'pesan'    => 'Berkas berhasil diunggah ulang dan tersimpan di berkas KP',
        'url_file' => $urlBaru
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
