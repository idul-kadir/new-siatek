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

// ================= AMBIL DATA TABEL SKRIPSI =================
if(isset($_POST['ambil-tabel-skripsi'])){
    $cari     = bersihkan($_POST['cari'] ?? '');
    $prodi    = bersihkan($_POST['prodi'] ?? '');
    $angkatan = bersihkan($_POST['angkatan'] ?? '');
    $status   = bersihkan($_POST['status'] ?? '');
    $progres  = bersihkan($_POST['progres'] ?? '');

    // Jika yang login level 3 atau 5, paksa filter hanya sesuai program studinya
    if (in_array($userLevel, [3, 5], true)) {
        $prodi = get_prodi_user();
    }

    $where = ["1=1"];
    if($cari !== '') {
        $where[] = "(m.nama LIKE '%$cari%' OR s.nim LIKE '%$cari%')";
    }
    if($prodi !== '') {
        $where[] = "m.prodi = '$prodi'";
    }
    if($angkatan !== '') {
        $where[] = "m.angkatan = '$angkatan'";
    }
    if($status !== '') {
        $where[] = "s.status = '$status'";
    }

    $whereSql = implode(" AND ", $where);

    // Ambil list SOP skripsi untuk mapping nomor proses ke nama tahapan & eksekutor
    $sopMap = [];
    $qSop = query("SELECT no, keterangan, eksekutor FROM sop_skripsi ORDER BY CAST(no AS UNSIGNED) ASC");
    if($qSop){
        while($sp = mysqli_fetch_assoc($qSop)){
            $sopMap[(int)$sp['no']] = [
                'keterangan' => $sp['keterangan'],
                'eksekutor'  => strtolower(trim($sp['eksekutor'] ?? ''))
            ];
        }
    }

    $sql = "SELECT s.*, m.nama AS nama_mhs, m.prodi, m.angkatan, p.nama AS nama_prodi, 
                   COALESCE(ps.waktu_terbaru, 0) AS waktu_progres
            FROM skripsi s 
            LEFT JOIN mahasiswa m ON s.nim = m.nim 
            LEFT JOIN prodi p ON m.prodi = p.kode 
            LEFT JOIN (
                SELECT mahasiswa, MAX(waktu) AS waktu_terbaru 
                FROM progres_skripsi 
                GROUP BY mahasiswa
            ) ps ON s.nim = ps.mahasiswa 
            WHERE $whereSql 
            ORDER BY waktu_progres DESC, s.nim DESC";

    $ambil = query($sql);

    // Filter berdasarkan progres (SOP) jika dipilih
    $dataFiltered = [];
    if($ambil && mysqli_num_rows($ambil) > 0){
        while($d = mysqli_fetch_assoc($ambil)){
            $progresVal = get_progres_skripsi($d['nim']);
            // Jika belum ada progres, default ke langkah 1 (Mahasiswa)
            if($progresVal === '-' || $progresVal === '') {
                $progresVal = 1;
            }
            if($progres !== '') {
                if((string)$progresVal !== (string)$progres) {
                    continue;
                }
            }
            $d['progres_num'] = $progresVal;
            $sopItem = $sopMap[(int)$progresVal] ?? null;
            $d['progres_label'] = $sopItem ? $sopItem['keterangan'] : '-';
            $d['eksekutor']     = $sopItem ? $sopItem['eksekutor'] : 'mahasiswa';
            $dataFiltered[] = $d;
        }
    }

    ob_start();
    if(count($dataFiltered) > 0){
        $no = 1;
        ?>
        <table class="w-full text-left text-sm">
          <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
            <tr>
                <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">Mahasiswa</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Judul</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Status</th>
              <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
        <?php
        foreach($dataFiltered as $data){
            $namaMhs = !empty($data['nama_mhs']) ? $data['nama_mhs'] : '-';
            $prodiNm = !empty($data['nama_prodi']) ? $data['nama_prodi'] : (!empty($data['prodi']) ? $data['prodi'] : '');
            $angkatanTxt = !empty($data['angkatan']) ? 'Angkatan ' . $data['angkatan'] : '';
            $statusSkripsi = strtolower(trim($data['status'] ?? ''));
            $isTidakAktif  = ($statusSkripsi === 'tidak aktif');
            $isAdminEksekutor = ($data['eksekutor'] === 'admin') || $isTidakAktif;
            ?>
            <tr class="transition hover:bg-orange-50 <?= ($no % 2 == 0) ? 'bg-slate-50/60' : 'bg-white' ?>">
                <td class="py-4 pl-5 pr-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm"><?= $no ?></span>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-1.5 leading-snug">
                                <p class="font-semibold text-slate-800"><?= tampil($namaMhs) ?></p>
                                <?php if($prodiNm !== ''): ?>
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
                    <p class="text-sm leading-snug"><?= tampil($data['judul'] ?? '-') ?></p>
                </td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold <?= ($data['status'] ?? '') === 'Pengajuan' ? 'bg-yellow-100 text-yellow-700' : (($data['status'] ?? '') === 'Bimbingan' ? 'bg-blue-100 text-blue-700' : (($data['status'] ?? '') === 'tidak aktif' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700')) ?>">
                        <?= tampil($data['status'] ?? 'Draft') ?>
                    </span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-1.5">
                        <!-- 1. Tombol Arsip -->
                        <button type="button" class="btnArsip btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600" data-nim="<?= tampil($data['nim']) ?>">
                            <i class="fas fa-box-archive text-xs"></i><span class="tip">Arsip</span>
                        </button>

                        <!-- 2. Tombol Lembar Kerja -->
                        <button type="button" class="btnLembarKerja btn-circle bg-orange-500 hover:bg-orange-600 text-white shadow-sm" data-nim="<?= tampil($data['nim']) ?>" data-progres="<?= tampil($data['progres_num']) ?>" data-status-skripsi="<?= tampil($data['status']) ?>" data-is-admin="<?= $isAdminEksekutor ? '1' : '0' ?>">
                            <i class="fas fa-clipboard-check text-xs"></i><span class="tip">Lembar Kerja</span>
                        </button>

                        <!-- 3. Tombol Hapus -->
                        <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-nim="<?= tampil($data['nim']) ?>">
                            <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                        </button>
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
            <i class="fas fa-book text-4xl text-slate-300"></i>
            <p class="mt-3 font-medium text-slate-500">Belum ada data skripsi yang sesuai filter</p>
            <p class="text-xs text-slate-400">Coba ubah kata kunci pencarian atau pilihan filter di atas.</p>
        </div>
        <?php
    }
    $html = ob_get_clean();
    echo json_encode(['status' => true, 'data' => $html]);
    exit;
}

// ================= MUAT LEMBAR KERJA =================
if(isset($_POST['muat-lembar-kerja'])){
    $nim = bersihkan($_POST['nim']);
    $statusSkripsi = bersihkan($_POST['status_skripsi']);
    $progres = bersihkan($_POST['progres']);

    ob_start();
    ?>
    <div class="space-y-4">
        <div class="rounded-lg bg-orange-50 p-4 border border-orange-100 mb-4">
            <?php
            // Ambil detail mahasiswa dari database
            $mhsRow = mysqli_fetch_assoc(query("SELECT nama, prodi, angkatan FROM mahasiswa WHERE nim='" . $nim . "' LIMIT 1"));
            $namaLengkap = $mhsRow ? $mhsRow['nama'] : 'Data tidak ditemukan';
            $prodiNm = $mhsRow && !empty($mhsRow['prodi']) ? tampil($mhsRow['prodi']) : '-';
            $angkatanTxt = $mhsRow && !empty($mhsRow['angkatan']) ? 'Angkatan ' . tampil($mhsRow['angkatan']) : '-';
            
            // Ambil nama prodi
            if($mhsRow && !empty($mhsRow['prodi'])){
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

        <?php if($statusSkripsi === 'tidak aktif'): ?>
        
        <!-- CASE 1: VERIFIKASI PENDAFTARAN (KARENA STATUS MASIH TIDAK AKTIF) -->
        <h5 class="font-bold text-slate-800 text-[14px] border-b border-slate-200 pb-2"><i class="fas fa-tasks mr-1.5 text-slate-400"></i>Tugas: Verifikasi Pendaftaran Skripsi</h5>
        <div class="py-3">
            <p class="text-sm text-slate-600 mb-4">Mahasiswa ini telah mendaftar skripsi namun statusnya masih tidak aktif. Silakan verifikasi kelayakan sebelum mengaktifkan status skripsi.</p>
            <form id="formAksiLembarKerja" class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                <input type="hidden" name="aksi" value="verifikasi-pendaftaran">
                <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-2">Tindakan</label>
                    <select name="tindakan" id="selTindakan" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                        <option value="">-- Pilih Tindakan --</option>
                        <option value="terima">Terima & Aktifkan Skripsi</option>
                        <option value="tolak">Tolak Pendaftaran</option>
                    </select>
                </div>
                
                <!-- Syarat Checklist (Hanya Tampil Jika Memilih "Terima") -->
                <div id="wrapSyarat" class="mb-4 hidden rounded-md bg-white border border-slate-200 p-3">
                    <p class="text-xs font-bold text-slate-700 mb-2">Konfirmasi Kelayakan (Wajib dicentang):</p>
                    <label class="flex items-start gap-2 mb-2 cursor-pointer">
                        <input type="checkbox" id="chkSyarat1" class="mt-0.5 accent-orange-500">
                        <span class="text-xs text-slate-600">Mahasiswa sudah menempuh minimal 120 SKS yang lulus (dibuktikan dengan transkrip).</span>
                    </label>
                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" id="chkSyarat2" class="mt-0.5 accent-orange-500">
                        <span class="text-xs text-slate-600">Mahasiswa sedang mengontrak mata kuliah Skripsi pada semester berjalan.</span>
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
                        btnEks.prop('disabled', false); // Jika tolak, bisa langsung proses
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
            $sopRow = mysqli_fetch_assoc(query("SELECT keterangan FROM sop_skripsi WHERE no='" . (int)$progres . "' LIMIT 1"));
            $namaTugasSop = ($sopRow && !empty($sopRow['keterangan'])) ? $sopRow['keterangan'] : 'Tahapan Skripsi';
        ?>

        <!-- CASE 2: PROSES BERJALAN SESUAI SOP SKRIPSI -->
        <h5 class="font-bold text-slate-800 text-[14px] border-b border-slate-200 pb-2"><i class="fas fa-tasks mr-1.5 text-slate-400"></i>Tugas: <?= tampil($namaTugasSop) ?></h5>
        <div class="py-3">
            <?php 
            // Progres Verifikasi Berkas yang harus diproses di halaman mhs-verifikasi
            $progresVerifikasi = ['2', '5', '8', '13', '16', '21'];
            if(in_array((string)$progres, $progresVerifikasi, true)): 
            ?>
                <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-500 mb-3 text-xl">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h6 class="font-bold text-blue-800 text-sm mb-2">Verifikasi Berkas Mahasiswa</h6>
                    <p class="text-xs text-blue-600 mb-4">Tugas pada tahapan ini adalah melakukan verifikasi terhadap berkas mahasiswa. Proses ini tidak dilakukan di halaman ini.</p>
                    <a href="<?= base_url() ?>/administrator/mhs-verifikasi" class="inline-block px-4 py-2 text-xs rounded-md bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-sm transition">
                        Buka Halaman Verifikasi Berkas
                    </a>
                </div>
            <?php else: ?>
                <p class="text-sm text-slate-600 mb-4">Pilih tindakan untuk menyelesaikan tahap skripsi saat ini dan memajukan progres mahasiswa ke tahap selanjutnya.</p>
                <form id="formAksiLembarKerja" class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                    <input type="hidden" name="aksi" value="proses-sop">
                    <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                    <input type="hidden" name="progres_sekarang" value="<?= tampil($progres) ?>">
                    
                    <?php
                    switch((int)$progres):
                        case 3:
                            // Form Khusus: SOP 3 (Penunjukkan Pembimbing)
                            ?>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-2">No. Surat Penunjukkan</label>
                                <input type="text" name="no_surat" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="Masukkan nomor surat..." required>
                            </div>
                            <div class="mb-4 grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-2">Pembimbing 1</label>
                                    <select name="pembimbing1" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                                        <option value="">-- Pilih Dosen --</option>
                                        <?php
                                        $qDosen = query("SELECT id, nama FROM dosen WHERE LOWER(posisi) = 'dosen' AND LOWER(status) = 'aktif' ORDER BY nama ASC");
                                        if($qDosen){
                                            while($dsn = mysqli_fetch_assoc($qDosen)){
                                                echo '<option value="' . tampil($dsn['id']) . '">' . tampil($dsn['nama']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-2">Pembimbing 2</label>
                                    <select name="pembimbing2" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                                        <option value="">-- Pilih Dosen --</option>
                                        <?php
                                        if($qDosen) mysqli_data_seek($qDosen, 0);
                                        if($qDosen){
                                            while($dsn = mysqli_fetch_assoc($qDosen)){
                                                echo '<option value="' . tampil($dsn['id']) . '">' . tampil($dsn['nama']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="tindakan" value="selesai">
                            <?php
                            break;

                        case 9:
                            // Form Khusus: SOP 9 (Penunjukkan Penguji Hasil)
                            // Ambil pembimbing 1 & 2 dari surat penunjukan sebelumnya sebagai nilai default/saran
                            $suratBimb = mysqli_fetch_assoc(query("SELECT sp.pembimbing1, sp.pembimbing2 FROM skripsi s JOIN surat_penunjukkan sp ON s.penunjukkan_pembimbing = sp.id WHERE s.nim = '$nim' LIMIT 1"));
                            $saranPenguji1 = $suratBimb ? (string)$suratBimb['pembimbing1'] : '';
                            $saranPenguji2 = $suratBimb ? (string)$suratBimb['pembimbing2'] : '';
                            ?>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-2">No. Surat Penunjukkan Penguji</label>
                                <input type="text" name="no_surat" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="Masukkan nomor surat penunjukkan penguji..." required>
                            </div>
                            <div class="space-y-3 mb-4">
                                <?php 
                                // Query list dosen sekali saja
                                $listDosen = [];
                                $qDosenAll = query("SELECT id, nama FROM dosen WHERE LOWER(posisi) = 'dosen' AND LOWER(status) = 'aktif' ORDER BY nama ASC");
                                if($qDosenAll){
                                    while($dRow = mysqli_fetch_assoc($qDosenAll)){
                                        $listDosen[] = $dRow;
                                    }
                                }

                                for($i = 1; $i <= 5; $i++): 
                                    // Default nilai terpilih: Penguji 1 -> Pembimbing 1, Penguji 2 -> Pembimbing 2
                                    $defaultTerpilih = '';
                                    $labelSaran = '';
                                    if($i === 1 && $saranPenguji1 !== ''){
                                        $defaultTerpilih = $saranPenguji1;
                                        $labelSaran = ' <span class="text-orange-600 font-normal">(Disarankan: Pembimbing 1)</span>';
                                    } elseif($i === 2 && $saranPenguji2 !== ''){
                                        $defaultTerpilih = $saranPenguji2;
                                        $labelSaran = ' <span class="text-orange-600 font-normal">(Disarankan: Pembimbing 2)</span>';
                                    }
                                ?>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Penguji <?= $i ?><?= $labelSaran ?></label>
                                    <select name="penguji<?= $i ?>" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                                        <option value="">-- Pilih Dosen Penguji <?= $i ?> --</option>
                                        <?php
                                        foreach($listDosen as $dsn){
                                            $selected = ((string)$dsn['id'] === (string)$defaultTerpilih) ? 'selected' : '';
                                            echo '<option value="' . tampil($dsn['id']) . '" ' . $selected . '>' . tampil($dsn['nama']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="tindakan" value="selesai">
                            <?php
                            break;

                        case 10:
                            // Form Khusus: SOP 10 (Jadwal Ujian Hasil)
                            ?>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor Undangan</label>
                                <input type="text" name="no_undangan_hasil" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="Masukkan nomor surat undangan..." required>
                            </div>
                            <div class="mb-4 grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Ujian</label>
                                    <input type="date" name="tanggal_hasil" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Jam Ujian</label>
                                    <input type="text" name="jam_hasil" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="contoh: 08:00 - Selesai" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Ruangan / Tempat Ujian</label>
                                <input type="text" name="ruangan_hasil" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="contoh: Ruang Sidang Jurusan" required>
                            </div>
                            <input type="hidden" name="tindakan" value="selesai">
                            <?php
                            break;

                        case 11:
                            // Form Khusus: SOP 11 (Unggah File Komponen Ujian Hasil) - Style & auto-upload event change persis portal mahasiswa
                            $qSyarat = query("SELECT id, nama_berkas FROM persyaratan_berkas_skripsi WHERE progres = 11 ORDER BY CASE WHEN nama_berkas LIKE '%Berita Acara%' THEN 0 ELSE 1 END ASC, nama_berkas ASC");
                            $listSyarat = [];
                            if($qSyarat){
                                while($sRow = mysqli_fetch_assoc($qSyarat)){
                                    $listSyarat[] = $sRow;
                                }
                                usort($listSyarat, function($a, $b) {
                                    $isBaA = stripos($a['nama_berkas'], 'Berita Acara') !== false ? 0 : 1;
                                    $isBaB = stripos($b['nama_berkas'], 'Berita Acara') !== false ? 0 : 1;
                                    if ($isBaA !== $isBaB) return $isBaA - $isBaB;
                                    return strnatcasecmp($a['nama_berkas'], $b['nama_berkas']);
                                });
                            }

                            // Ambil data dosen penguji dari surat_penunjukkan hasil mahasiswa
                            $dosenPenguji = [];
                            $qSuratPenguji = query("SELECT sp.penguji1, sp.penguji2, sp.penguji3, sp.penguji4, sp.penguji5 
                                                    FROM skripsi s 
                                                    JOIN surat_penunjukkan sp ON s.penunjukkan_hasil = sp.id 
                                                    WHERE s.nim = '$nim' LIMIT 1");
                            if($qSuratPenguji && mysqli_num_rows($qSuratPenguji) > 0){
                                $rPenguji = mysqli_fetch_assoc($qSuratPenguji);
                                for($p = 1; $p <= 5; $p++){
                                    $idDsn = trim((string)($rPenguji['penguji' . $p] ?? ''));
                                    if($idDsn !== ''){
                                        $qNama = query("SELECT nama FROM dosen WHERE id = '$idDsn' OR nip = '$idDsn' LIMIT 1");
                                        $dosenPenguji[$p] = ($qNama && mysqli_num_rows($qNama) > 0) ? mysqli_fetch_assoc($qNama)['nama'] : $idDsn;
                                    } else {
                                        $dosenPenguji[$p] = '';
                                    }
                                }
                            }
                            ?>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-4 mb-4">
                                <?php foreach($listSyarat as $syarat): 
                                    // Cek apakah berkas sudah pernah diunggah
                                    $idSyarat = (int)$syarat['id'];
                                    $qBerkas = query("SELECT b.file, f.nama_file FROM berkas_skripsi b LEFT JOIN files f ON b.file = f.id WHERE b.id_persyaratan = $idSyarat AND b.nim = '$nim' LIMIT 1");
                                    $berkasAda = ($qBerkas && mysqli_num_rows($qBerkas) > 0);
                                    $urlBerkas = '';
                                    if($berkasAda){
                                        $dFile = mysqli_fetch_assoc($qBerkas);
                                        $urlBerkas = url_file($dFile['file']);
                                    }

                                    // Mapping nama dosen penguji jika berkas merupakan Lembar Penilaian 1 - 5
                                    $namaPengujiTerkait = '';
                                    if(preg_match('/Lembar Penilaian (\d+)/i', $syarat['nama_berkas'], $mPen)){
                                        $idxP = (int)$mPen[1];
                                        if(!empty($dosenPenguji[$idxP])){
                                            $namaPengujiTerkait = htmlspecialchars($dosenPenguji[$idxP]);
                                        }
                                    }
                                ?>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-bold text-slate-700">
                                            <?= htmlspecialchars($syarat['nama_berkas']) ?> (PDF) <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="flex items-center gap-3">
                                            <?php if($urlBerkas): ?>
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
                                    <?php if($namaPengujiTerkait !== ''): ?>
                                    <p class="mt-1 text-[11px] text-slate-500 leading-tight">
                                        <i class="fas fa-user-tie text-orange-500 mr-1"></i>Dosen Penguji: <span class="font-semibold text-slate-700"><?= $namaPengujiTerkait ?></span>
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
                                        url: '<?= base_url() ?>/administrator/pages/pengelolaan/mahasiswa/skripsi/proses.php',
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

                                                // Jika seluruh berkas komponen sudah lengkap, progres otomatis naik ke 12
                                                if(res.selesai_semua){
                                                    toast('Seluruh komponen ujian telah lengkap diunggah! Progres mahasiswa dimajukan.', 'success');
                                                    setTimeout(function(){
                                                        $('#modalLembarKerja').removeClass('show');
                                                        muatUlangTabel();
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
                            <?php
                            break;

                        case 17:
                            // Form Khusus: SOP 17 (Penunjukkan Penguji Tutup)
                            // Ambil penguji 1 - 5 dari surat penunjukan hasil sebelumnya sebagai nilai default/saran
                            $suratHasil = mysqli_fetch_assoc(query("SELECT sp.penguji1, sp.penguji2, sp.penguji3, sp.penguji4, sp.penguji5 FROM skripsi s JOIN surat_penunjukkan sp ON s.penunjukkan_hasil = sp.id WHERE s.nim = '$nim' LIMIT 1"));
                            if(!$suratHasil){
                                $suratHasil = mysqli_fetch_assoc(query("SELECT penguji1, penguji2, penguji3, penguji4, penguji5 FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PENGUJI HASIL' ORDER BY id DESC LIMIT 1"));
                            }
                            ?>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-2">No. Surat Penunjukkan Penguji</label>
                                <input type="text" name="no_surat" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="Masukkan nomor surat penunjukkan penguji..." required>
                            </div>
                            <div class="space-y-3 mb-4">
                                <?php 
                                $listDosen = [];
                                $qDosenAll = query("SELECT id, nama FROM dosen WHERE LOWER(posisi) = 'dosen' AND LOWER(status) = 'aktif' ORDER BY nama ASC");
                                if($qDosenAll){
                                    while($dRow = mysqli_fetch_assoc($qDosenAll)){
                                        $listDosen[] = $dRow;
                                    }
                                }

                                for($i = 1; $i <= 5; $i++): 
                                    $saranPenguji = $suratHasil ? trim((string)($suratHasil['penguji' . $i] ?? '')) : '';
                                    $defaultTerpilih = '';
                                    $labelSaran = '';
                                    if($saranPenguji !== ''){
                                        $defaultTerpilih = $saranPenguji;
                                        $labelSaran = ' <span class="text-orange-600 font-normal">(Disarankan: Penguji Hasil ' . $i . ')</span>';
                                    }
                                ?>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Penguji <?= $i ?><?= $labelSaran ?></label>
                                    <select name="penguji<?= $i ?>" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                                        <option value="">-- Pilih Dosen Penguji <?= $i ?> --</option>
                                        <?php
                                        foreach($listDosen as $dsn){
                                            $selected = ((string)$dsn['id'] === (string)$defaultTerpilih) ? 'selected' : '';
                                            echo '<option value="' . tampil($dsn['id']) . '" ' . $selected . '>' . tampil($dsn['nama']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="tindakan" value="selesai">
                            <?php
                            break;

                        case 18:
                            // Form Khusus: SOP 18 (Jadwal Ujian Tutup)
                            ?>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor Undangan</label>
                                <input type="text" name="no_undangan_tutup" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="Masukkan nomor surat undangan..." required>
                            </div>
                            <div class="mb-4 grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Ujian</label>
                                    <input type="date" name="tanggal_tutup" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Jam Ujian</label>
                                    <input type="text" name="jam_tutup" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="contoh: 08:00 - Selesai" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Ruangan / Tempat Ujian</label>
                                <input type="text" name="ruangan_tutup" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="contoh: Ruang Sidang Jurusan" required>
                            </div>
                            <input type="hidden" name="tindakan" value="selesai">
                            <?php
                            break;

                        case 19:
                            // Form Khusus: SOP 19 (Unggah File Komponen / Lembar Penilaian Ujian Tutup)
                            $qSyarat = query("SELECT id, nama_berkas FROM persyaratan_berkas_skripsi WHERE progres = 19 ORDER BY CASE WHEN nama_berkas LIKE '%Berita Acara%' THEN 0 ELSE 1 END ASC, nama_berkas ASC");
                            $listSyarat = [];
                            if($qSyarat){
                                while($sRow = mysqli_fetch_assoc($qSyarat)){
                                    $listSyarat[] = $sRow;
                                }
                                usort($listSyarat, function($a, $b) {
                                    $isBaA = stripos($a['nama_berkas'], 'Berita Acara') !== false ? 0 : 1;
                                    $isBaB = stripos($b['nama_berkas'], 'Berita Acara') !== false ? 0 : 1;
                                    if ($isBaA !== $isBaB) return $isBaA - $isBaB;
                                    return strnatcasecmp($a['nama_berkas'], $b['nama_berkas']);
                                });
                            }

                            // Ambil data dosen penguji dari surat_penunjukkan tutup mahasiswa
                            $dosenPenguji = [];
                            $qSuratPenguji = query("SELECT sp.penguji1, sp.penguji2, sp.penguji3, sp.penguji4, sp.penguji5 
                                                    FROM skripsi s 
                                                    JOIN surat_penunjukkan sp ON s.penunjukkan_tutup = sp.id 
                                                    WHERE s.nim = '$nim' LIMIT 1");
                            if(!$qSuratPenguji || mysqli_num_rows($qSuratPenguji) === 0){
                                $qSuratPenguji = query("SELECT penguji1, penguji2, penguji3, penguji4, penguji5 
                                                        FROM surat_penunjukkan 
                                                        WHERE nim = '$nim' AND keterangan = 'PENGUJI TUTUP' 
                                                        ORDER BY id DESC LIMIT 1");
                            }
                            if($qSuratPenguji && mysqli_num_rows($qSuratPenguji) > 0){
                                $rPenguji = mysqli_fetch_assoc($qSuratPenguji);
                                for($p = 1; $p <= 5; $p++){
                                    $idDsn = trim((string)($rPenguji['penguji' . $p] ?? ''));
                                    if($idDsn !== ''){
                                        $qNama = query("SELECT nama FROM dosen WHERE id = '$idDsn' OR nip = '$idDsn' LIMIT 1");
                                        $dosenPenguji[$p] = ($qNama && mysqli_num_rows($qNama) > 0) ? mysqli_fetch_assoc($qNama)['nama'] : $idDsn;
                                    } else {
                                        $dosenPenguji[$p] = '';
                                    }
                                }
                            }
                            ?>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-4 mb-4">
                                <?php foreach($listSyarat as $syarat): 
                                    $idSyarat = (int)$syarat['id'];
                                    $qBerkas = query("SELECT b.file, f.nama_file FROM berkas_skripsi b LEFT JOIN files f ON b.file = f.id WHERE b.id_persyaratan = $idSyarat AND b.nim = '$nim' LIMIT 1");
                                    $berkasAda = ($qBerkas && mysqli_num_rows($qBerkas) > 0);
                                    $urlBerkas = '';
                                    if($berkasAda){
                                        $dFile = mysqli_fetch_assoc($qBerkas);
                                        $urlBerkas = url_file($dFile['file']);
                                    }

                                    // Mapping nama dosen penguji jika berkas merupakan Lembar Penilaian 1 - 5
                                    $namaPengujiTerkait = '';
                                    if(preg_match('/Lembar Penilaian (\d+)/i', $syarat['nama_berkas'], $mPen)){
                                        $idxP = (int)$mPen[1];
                                        if(!empty($dosenPenguji[$idxP])){
                                            $namaPengujiTerkait = htmlspecialchars($dosenPenguji[$idxP]);
                                        }
                                    }
                                ?>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-bold text-slate-700">
                                            <?= htmlspecialchars($syarat['nama_berkas']) ?> (PDF) <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="flex items-center gap-3">
                                            <?php if($urlBerkas): ?>
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
                                    <?php if($namaPengujiTerkait !== ''): ?>
                                    <p class="mt-1 text-[11px] text-slate-500 leading-tight">
                                        <i class="fas fa-user-tie text-orange-500 mr-1"></i>Dosen Penguji: <span class="font-semibold text-slate-700"><?= $namaPengujiTerkait ?></span>
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
                                        url: '<?= base_url() ?>/administrator/pages/pengelolaan/mahasiswa/skripsi/proses.php',
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

                                                // Jika seluruh berkas komponen sudah lengkap, progres otomatis naik
                                                if(res.selesai_semua){
                                                    toast('Seluruh komponen ujian telah lengkap diunggah! Progres mahasiswa dimajukan.', 'success');
                                                    setTimeout(function(){
                                                        $('#modalLembarKerja').removeClass('show');
                                                        muatUlangTabel();
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
                            <?php
                            break;

                        default:
                            // Tampilan default untuk tahapan yang belum diimplementasikan form khususnya
                            ?>
                            <div class="rounded-lg bg-slate-100 border border-slate-200 p-4 text-center">
                                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-slate-500 mb-2 text-base">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <h6 class="font-bold text-slate-700 text-xs mb-1">Tahapan Sedang Dipersiapkan</h6>
                                <p class="text-xs text-slate-500">Lembar kerja untuk tahapan ini belum memiliki formulir khusus.</p>
                            </div>
                            <?php
                            break;
                    endswitch;
                    
                    // Tombol submit manual hanya untuk tahap 3, 9, 10, 17, 18. Tahap 11 & 19 auto-upload per item (tanpa tombol).
                    if(in_array((string)$progres, ['3', '9', '10', '17', '18'], true)): ?>
                    <div class="flex justify-end">
                        <?php
                        $labelTombol = 'Simpan';
                        if(in_array((string)$progres, ['3', '9', '17'], true)) {
                            $labelTombol = 'Buat Surat';
                        } elseif(in_array((string)$progres, ['10', '18'], true)) {
                            $labelTombol = 'Simpan Jadwal';
                        }
                        ?>
                        <button type="submit" class="px-4 py-2 text-xs rounded-md bg-[#f97316] hover:bg-[#ea6a0f] text-white font-semibold shadow-sm" data-text="<?= $labelTombol ?>"><?= $labelTombol ?></button>
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
                    konfirmasiHapus('Pendaftaran skripsi ' + namaMhs + ' (data akan terhapus)', function() {
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
                url: '<?= base_url() ?>/administrator/pages/pengelolaan/mahasiswa/skripsi/proses.php',
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

                        // Jika respon menginstruksikan buka tab baru (misal cetak dokumen)
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
                        btn.innerHTML = btn.getAttribute('data-text') || 'Proses Keputusan';
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

// ================= PROSES LEMBAR KERJA =================
if(isset($_POST['proses-lembar-kerja'])){
    // Bersihkan buffer output jika ada warning/whitespace sebelum JSON
    if (ob_get_length()) ob_clean();

    $aksi = bersihkan($_POST['aksi'] ?? '');
    $nim = bersihkan($_POST['nim'] ?? '');
    $tindakan = bersihkan($_POST['tindakan'] ?? '');

    if($nim === ''){
        echo json_encode(['status' => false, 'pesan' => 'NIM tidak valid']);
        exit;
    }

    if($aksi === 'verifikasi-pendaftaran'){
        if($tindakan === 'terima'){
            // Aktifkan status skripsi menjadi Aktif (coba dengan A kapital dulu, kalau gagal pakai huruf kecil)
            $update = query("UPDATE skripsi SET status = 'Aktif' WHERE nim = '$nim'");
            if(!$update) {
                $update = query("UPDATE skripsi SET status = 'aktif' WHERE nim = '$nim'");
            }
            
            // Catat progres awal di progres_skripsi jika belum ada
            $waktu = time();
            $cekProg = query("SELECT proses FROM progres_skripsi WHERE mahasiswa = '$nim' LIMIT 1");
            if($cekProg && mysqli_num_rows($cekProg) === 0){
                query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktu, 1, '$nim')");
            }

            echo json_encode(['status' => true, 'pesan' => 'Pendaftaran berhasil diterima dan status skripsi diaktifkan']);
            exit;
        } elseif($tindakan === 'tolak'){
            // Hapus mahasiswa dari tabel skripsi
            $hapus = query("DELETE FROM skripsi WHERE nim = '$nim'");
            // Hapus juga riwayat progres jika ada
            @query("DELETE FROM progres_skripsi WHERE mahasiswa = '$nim'");

            echo json_encode(['status' => true, 'pesan' => 'Pendaftaran ditolak. Data mahasiswa telah dihapus dari pendaftaran skripsi']);
            exit;
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Tindakan tidak valid']);
            exit;
        }
    }

    if($aksi === 'proses-sop'){
        $progresSekarang = (int)($_POST['progres_sekarang'] ?? 1);
        
        switch($progresSekarang):
            case 3:
                // SOP 3: Penunjukkan Pembimbing
                $noSurat = bersihkan($_POST['no_surat'] ?? '');
                $pembimbing1 = bersihkan($_POST['pembimbing1'] ?? '');
                $pembimbing2 = bersihkan($_POST['pembimbing2'] ?? '');

                if($noSurat === '' || $pembimbing1 === '' || $pembimbing2 === ''){
                    echo json_encode(['status' => false, 'pesan' => 'Nomor surat dan pembimbing wajib diisi']);
                    exit;
                }

                // Simpan surat penunjukkan ke tabel terpisah dengan ID manual (timestamp integer)
                $waktuSistem = time();
                $tanggalSekarang = date('Y-m-d');
                $idSurat = $waktuSistem;
                
                $insertSurat = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, pembimbing1, pembimbing2, tanggal, keterangan) VALUES ($idSurat, '$noSurat', '$nim', '$pembimbing1', '$pembimbing2', '$tanggalSekarang', 'PEMBIMBING SKRIPSI')");

                if($insertSurat) {
                    // Update skripsi table with the new surat_penunjukkan ID
                    $updateSkripsi = query("UPDATE skripsi SET penunjukkan_pembimbing = '$idSurat' WHERE nim = '$nim'");

                    // Majukan progres (step/progres naik 1 tingkat, dari 3 ke 4)
                    $progresBaru = 4;
                    $waktuProgres = time() + 1;
                    query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, $progresBaru, '$nim')");
                    
                    echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan berhasil dibuat']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan data penunjukkan pembimbing: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
                exit;

            case 9:
                // SOP 9: Penunjukkan Penguji Hasil
                $noSurat  = bersihkan($_POST['no_surat'] ?? '');
                $penguji1 = bersihkan($_POST['penguji1'] ?? '');
                $penguji2 = bersihkan($_POST['penguji2'] ?? '');
                $penguji3 = bersihkan($_POST['penguji3'] ?? '');
                $penguji4 = bersihkan($_POST['penguji4'] ?? '');
                $penguji5 = bersihkan($_POST['penguji5'] ?? '');

                if($noSurat === '' || $penguji1 === '' || $penguji2 === '' || $penguji3 === '' || $penguji4 === '' || $penguji5 === ''){
                    echo json_encode(['status' => false, 'pesan' => 'Nomor surat dan kelima dosen penguji wajib diisi']);
                    exit;
                }

                $waktuSistem = time();
                $tanggalSekarang = date('Y-m-d');
                $idSurat = $waktuSistem;

                // Surat penunjukkan penguji murni mencatat penguji saja
                $insertSurat = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, penguji1, penguji2, penguji3, penguji4, penguji5, tanggal, keterangan) VALUES ($idSurat, '$noSurat', '$nim', '$penguji1', '$penguji2', '$penguji3', '$penguji4', '$penguji5', '$tanggalSekarang', 'PENGUJI HASIL')");

                if($insertSurat) {
                    // Update ke tabel skripsi kolom penunjukkan_hasil
                    query("UPDATE skripsi SET penunjukkan_hasil = '$idSurat' WHERE nim = '$nim'");

                    // Majukan progres (SOP 9 -> SOP 10)
                    $progresBaru = 10;
                    $waktuProgres = time() + 1;
                    query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, $progresBaru, '$nim')");

                    echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan penguji berhasil dibuat dan sudah diteruskan keakun Ketua Jurusan']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan data penunjukkan penguji: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
                exit;

            case 10:
                // SOP 10: Pengaturan Jadwal Ujian Hasil
                $noUndangan = bersihkan($_POST['no_undangan_hasil'] ?? '');
                $tanggal    = bersihkan($_POST['tanggal_hasil'] ?? '');
                $jam        = bersihkan($_POST['jam_hasil'] ?? '');
                $ruangan    = bersihkan($_POST['ruangan_hasil'] ?? '');

                if($noUndangan === '' || $tanggal === '' || $jam === '' || $ruangan === ''){
                    echo json_encode(['status' => false, 'pesan' => 'Nomor undangan, tanggal, jam, dan ruangan wajib diisi']);
                    exit;
                }

                $updateJadwal = query("UPDATE skripsi SET 
                    no_undangan_hasil = '$noUndangan', 
                    tanggal_hasil = '$tanggal', 
                    jam_hasil = '$jam', 
                    ruangan_hasil = '$ruangan' 
                    WHERE nim = '$nim'");

                if($updateJadwal){
                    // Majukan progres (SOP 10 -> SOP 11)
                    $progresBaru = 11;
                    $waktuProgres = time();
                    query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, $progresBaru, '$nim')");

                    // Link dummy cetak dokumen komponen ujian hasil
                    $cetakUrl = base_url() . "/administrator/dokumen/komponen-hasil/" . $nim;

                    echo json_encode([
                        'status'    => true, 
                        'pesan'     => 'Jadwal ujian hasil berhasil disimpan dan mahasiswa siap ujian',
                        'buka_tab'  => $cetakUrl
                    ]);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan jadwal ujian: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
                exit;

            case 11:
                // SOP 11: Unggah Berkas Komponen Ujian Hasil
                $qSyarat = query("SELECT id FROM persyaratan_berkas_skripsi WHERE progres = 11");
                $suksesUpload = 0;
                $waktuSistem = time();

                if($qSyarat){
                    while($syarat = mysqli_fetch_assoc($qSyarat)){
                        $idSyarat = (int)$syarat['id'];
                        $inputName = 'berkas_' . $idSyarat;

                        if(isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] == 0){
                            // Cek terlebih dahulu apakah berkas dengan persyaratan ini sudah ada
                            $cekBerkas = query("SELECT id, file FROM berkas_skripsi WHERE id_persyaratan = $idSyarat AND nim = '$nim' LIMIT 1");
                            $sudahAdaBerkas = ($cekBerkas && mysqli_num_rows($cekBerkas) > 0);
                            $idFileLama = '';
                            if($sudahAdaBerkas){
                                $rowBerkas = mysqli_fetch_assoc($cekBerkas);
                                $idFileLama = trim((string)($rowBerkas['file'] ?? ''));
                            }

                            // Kirim $idFileLama sebagai parameter ke-3 agar file fisik lama dihapus otomatis oleh upload_file()
                            $idFile = upload_file($_FILES[$inputName], 'skripsi', $idFileLama);
                            if($idFile !== false){
                                if($sudahAdaBerkas){
                                    query("UPDATE berkas_skripsi SET file = '$idFile' WHERE id_persyaratan = $idSyarat AND nim = '$nim'");
                                } else {
                                    query("INSERT INTO berkas_skripsi (id_persyaratan, nim, file) VALUES ($idSyarat, '$nim', '$idFile')");
                                }
                                $suksesUpload++;
                            }
                        }
                    }
                }

                if($suksesUpload > 0){
                    // Majukan progres (SOP 11 -> SOP 12)
                    $progresBaru = 12;
                    $waktuProgres = time() + 1;
                    query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, $progresBaru, '$nim')");

                    // Cek apakah mahasiswa sudah mengunggah semua persyaratan di step 12
                    $qTotal12 = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_skripsi WHERE progres = 12");
                    $jmlTotal12 = ($qTotal12 && mysqli_num_rows($qTotal12) > 0) ? (int)mysqli_fetch_assoc($qTotal12)['total'] : 0;

                    $qValid12 = query("SELECT COUNT(DISTINCT id_persyaratan) AS cnt FROM temp_skripsi WHERE nim = '$nim' AND status != 'ditolak' AND id_persyaratan IN (SELECT id FROM persyaratan_berkas_skripsi WHERE progres = 12)");
                    $jmlValid12 = ($qValid12 && mysqli_num_rows($qValid12) > 0) ? (int)mysqli_fetch_assoc($qValid12)['cnt'] : 0;

                    if($jmlTotal12 > 0 && $jmlValid12 >= $jmlTotal12){
                        $waktuProgres13 = $waktuProgres + 1;
                        query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres13, 13, '$nim')");
                    }

                    echo json_encode(['status' => true, 'pesan' => 'Seluruh berkas komponen ujian hasil berhasil diunggah dan progres dilanjutkan']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal mengunggah berkas komponen ujian']);
                }
                exit;

            case 17:
                // SOP 17: Penunjukkan Penguji Tutup
                $noSurat  = bersihkan($_POST['no_surat'] ?? '');
                $penguji1 = bersihkan($_POST['penguji1'] ?? '');
                $penguji2 = bersihkan($_POST['penguji2'] ?? '');
                $penguji3 = bersihkan($_POST['penguji3'] ?? '');
                $penguji4 = bersihkan($_POST['penguji4'] ?? '');
                $penguji5 = bersihkan($_POST['penguji5'] ?? '');

                if($noSurat === '' || $penguji1 === '' || $penguji2 === '' || $penguji3 === '' || $penguji4 === '' || $penguji5 === ''){
                    echo json_encode(['status' => false, 'pesan' => 'Nomor surat dan kelima dosen penguji wajib diisi']);
                    exit;
                }

                $waktuSistem = time();
                $tanggalSekarang = date('Y-m-d');
                $idSurat = $waktuSistem;

                // Simpan surat penunjukkan penguji tutup
                $insertSurat = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, penguji1, penguji2, penguji3, penguji4, penguji5, tanggal, keterangan) VALUES ($idSurat, '$noSurat', '$nim', '$penguji1', '$penguji2', '$penguji3', '$penguji4', '$penguji5', '$tanggalSekarang', 'PENGUJI TUTUP')");

                if($insertSurat) {
                    // Update ke tabel skripsi kolom penunjukkan_tutup
                    query("UPDATE skripsi SET penunjukkan_tutup = '$idSurat' WHERE nim = '$nim'");

                    // Majukan progres (SOP 17 -> SOP 18)
                    $progresBaru = 18;
                    $waktuProgres = time() + 1;
                    query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, $progresBaru, '$nim')");

                    echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan penguji tutup berhasil dibuat dan sudah diteruskan ke akun Ketua Jurusan']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan data penunjukkan penguji tutup: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
                exit;

            case 18:
                // SOP 18: Pengaturan Jadwal Ujian Tutup
                $noUndangan = bersihkan($_POST['no_undangan_tutup'] ?? '');
                $tanggal    = bersihkan($_POST['tanggal_tutup'] ?? '');
                $jam        = bersihkan($_POST['jam_tutup'] ?? '');
                $ruangan    = bersihkan($_POST['ruangan_tutup'] ?? '');

                if($noUndangan === '' || $tanggal === '' || $jam === '' || $ruangan === ''){
                    echo json_encode(['status' => false, 'pesan' => 'Nomor undangan, tanggal, jam, dan ruangan wajib diisi']);
                    exit;
                }

                $updateJadwal = query("UPDATE skripsi SET 
                    no_undangan_tutup = '$noUndangan', 
                    tanggal_tutup = '$tanggal', 
                    jam_tutup = '$jam', 
                    ruangan_tutup = '$ruangan' 
                    WHERE nim = '$nim'");

                if($updateJadwal){
                    // Majukan progres (SOP 18 -> SOP 19)
                    $progresBaru = 19;
                    $waktuProgres = time();
                    query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, $progresBaru, '$nim')");

                    // Link cetak dokumen komponen ujian tutup
                    $cetakUrl = base_url() . "/administrator/dokumen/komponen-tutup/" . $nim;

                    echo json_encode([
                        'status'    => true, 
                        'pesan'     => 'Jadwal ujian tutup berhasil disimpan dan mahasiswa siap ujian',
                        'buka_tab'  => $cetakUrl
                    ]);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan jadwal ujian tutup: ' . mysqli_error($GLOBALS['koneksi'])]);
                }
                exit;

            case 19:
                // SOP 19: Unggah Berkas Komponen / Lembar Penilaian Ujian Tutup
                $qSyarat = query("SELECT id FROM persyaratan_berkas_skripsi WHERE progres = 19");
                $suksesUpload = 0;

                if($qSyarat){
                    while($syarat = mysqli_fetch_assoc($qSyarat)){
                        $idSyarat = (int)$syarat['id'];
                        $inputName = 'berkas_' . $idSyarat;

                        if(isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] == 0){
                            $cekBerkas = query("SELECT id, file FROM berkas_skripsi WHERE id_persyaratan = $idSyarat AND nim = '$nim' LIMIT 1");
                            $sudahAdaBerkas = ($cekBerkas && mysqli_num_rows($cekBerkas) > 0);
                            $idFileLama = '';
                            if($sudahAdaBerkas){
                                $rowBerkas = mysqli_fetch_assoc($cekBerkas);
                                $idFileLama = trim((string)($rowBerkas['file'] ?? ''));
                            }

                            $idFile = upload_file($_FILES[$inputName], 'skripsi', $idFileLama);
                            if($idFile !== false){
                                if($sudahAdaBerkas){
                                    query("UPDATE berkas_skripsi SET file = '$idFile' WHERE id_persyaratan = $idSyarat AND nim = '$nim'");
                                } else {
                                    query("INSERT INTO berkas_skripsi (id_persyaratan, nim, file) VALUES ($idSyarat, '$nim', '$idFile')");
                                }
                                $suksesUpload++;
                            }
                        }
                    }
                }

                if($suksesUpload > 0){
                    // Majukan progres (SOP 19 -> SOP 20)
                    $progresBaru = 20;
                    $waktuProgres = time() + 1;
                    query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, $progresBaru, '$nim')");

                    // Cek apakah mahasiswa sudah mengunggah semua persyaratan di step 20
                    $qTotal20 = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_skripsi WHERE progres = 20");
                    $jmlTotal20 = ($qTotal20 && mysqli_num_rows($qTotal20) > 0) ? (int)mysqli_fetch_assoc($qTotal20)['total'] : 0;

                    $qValid20 = query("SELECT COUNT(DISTINCT id_persyaratan) AS cnt FROM temp_skripsi WHERE nim = '$nim' AND status != 'ditolak' AND id_persyaratan IN (SELECT id FROM persyaratan_berkas_skripsi WHERE progres = 20)");
                    $jmlValid20 = ($qValid20 && mysqli_num_rows($qValid20) > 0) ? (int)mysqli_fetch_assoc($qValid20)['cnt'] : 0;

                    if($jmlTotal20 > 0 && $jmlValid20 >= $jmlTotal20){
                        $waktuProgres21 = $waktuProgres + 1;
                        query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres21, 21, '$nim')");
                    }

                    echo json_encode(['status' => true, 'pesan' => 'Seluruh berkas komponen ujian tutup berhasil diunggah dan progres dilanjutkan']);
                } else {
                    echo json_encode(['status' => false, 'pesan' => 'Gagal mengunggah berkas komponen ujian tutup']);
                }
                exit;

            default:
                // Logic normal untuk tahapan lainnya
                if($tindakan === 'selesai'){
                    $progresBaru = $progresSekarang + 1;
                    $waktu = time();
                    
                    // Upload berkas balasan bila ada
                    $id_file = null;
                    if(isset($_FILES['file_balasan']) && $_FILES['file_balasan']['error'] == 0){
                        $id_file = upload_file($_FILES['file_balasan'], 'skripsi');
                    }

                    // Catat progres baru
                    $simpan = query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktu, $progresBaru, '$nim')");
                    
                    if($simpan){
                        echo json_encode(['status' => true, 'pesan' => 'Tahapan berhasil diselesaikan. Progres skripsi mahasiswa dimajukan']);
                    } else {
                        echo json_encode(['status' => false, 'pesan' => 'Gagal memajukan tahapan skripsi (Mungkin ada kendala database: ' . addslashes(mysqli_error($GLOBALS['koneksi'])) . ')']);
                    }
                    exit;
                } elseif($tindakan === 'tolak'){
                    // Upload berkas balasan bila ada (misalnya untuk file lembar koreksi revisi)
                    if(isset($_FILES['file_balasan']) && $_FILES['file_balasan']['error'] == 0){
                        upload_file($_FILES['file_balasan'], 'skripsi');
                    }

                    echo json_encode(['status' => true, 'pesan' => 'Tahapan ditolak / dikembalikan ke mahasiswa untuk revisi']);
                    exit;
                }
                break;
        endswitch;
    }

    echo json_encode(['status' => false, 'pesan' => 'Aksi lembar kerja tidak dikenali']);
    exit;
}

// ================= UNGGAH KOMPONEN UJIAN PER ITEM (EVENT CHANGE AUTO-UPLOAD) =================
if(isset($_POST['unggah-komponen-per-item'])){
    if (ob_get_length()) ob_clean();

    $idSyarat = (int)($_POST['id_persyaratan'] ?? 0);
    $nim = bersihkan($_POST['nim'] ?? '');

    if($idSyarat <= 0 || $nim === ''){
        echo json_encode(['status' => false, 'pesan' => 'Data tidak lengkap']);
        exit;
    }

    if(!isset($_FILES['file']) || $_FILES['file']['error'] !== 0){
        echo json_encode(['status' => false, 'pesan' => 'Gagal membuka file']);
        exit;
    }

    // Cek terlebih dahulu apakah berkas untuk persyaratan ini sudah pernah diunggah di berkas_skripsi
    $cek = query("SELECT id, file FROM berkas_skripsi WHERE id_persyaratan = $idSyarat AND nim = '$nim' LIMIT 1");
    $sudahAda = ($cek && mysqli_num_rows($cek) > 0);
    $idFileLama = '';
    if($sudahAda){
        $rowLama = mysqli_fetch_assoc($cek);
        $idFileLama = trim((string)($rowLama['file'] ?? ''));
    }

    // Kirim $idFileLama ke parameter ke-3 upload_file() agar file fisik lama dihapus otomatis
    $idFile = upload_file($_FILES['file'], 'skripsi', $idFileLama);
    if($idFile === false){
        echo json_encode(['status' => false, 'pesan' => 'Gagal mengunggah file']);
        exit;
    }

    // Simpan ke tabel berkas_skripsi (update atau insert)
    if($sudahAda){
        query("UPDATE berkas_skripsi SET file = '$idFile' WHERE id_persyaratan = $idSyarat AND nim = '$nim'");
    } else {
        // Kolom id adalah auto_increment, tidak perlu di-insert manual
        query("INSERT INTO berkas_skripsi (id_persyaratan, nim, file) VALUES ($idSyarat, '$nim', '$idFile')");
    }

    // Cari progres dari persyaratan yang diunggah (misal 11 untuk Ujian Hasil, 19 untuk Ujian Tutup)
    $qProgSyarat = query("SELECT progres FROM persyaratan_berkas_skripsi WHERE id = $idSyarat LIMIT 1");
    $progresSyarat = ($qProgSyarat && mysqli_num_rows($qProgSyarat) > 0) ? (int)mysqli_fetch_assoc($qProgSyarat)['progres'] : 11;
    $progresSelanjutnya = $progresSyarat + 1;

    // Cek apakah seluruh berkas untuk SOP progres ini sudah lengkap diunggah mahasiswa ini
    $qTotalSyarat = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_skripsi WHERE progres = $progresSyarat");
    $totalSyarat = $qTotalSyarat ? (int)mysqli_fetch_assoc($qTotalSyarat)['total'] : 6;

    $qUploaded = query("SELECT COUNT(DISTINCT b.id_persyaratan) AS total FROM berkas_skripsi b JOIN persyaratan_berkas_skripsi p ON b.id_persyaratan = p.id WHERE p.progres = $progresSyarat AND b.nim = '$nim'");
    $totalUploaded = $qUploaded ? (int)mysqli_fetch_assoc($qUploaded)['total'] : 0;

    $selesaiSemua = ($totalSyarat > 0 && $totalUploaded >= $totalSyarat);

    if($selesaiSemua){
        // Majukan progres otomatis ke tahap berikutnya (11 -> 12, atau 19 -> 20) jika belum mencapai tahap tersebut
        $currProg = get_progres_skripsi($nim);
        $waktuProgres = time() + 1;
        if((int)$currProg < $progresSelanjutnya){
            query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, $progresSelanjutnya, '$nim')");
            $currProg = $progresSelanjutnya;
        }

        // Setelah naik step, langsung cek apakah mahasiswa sudah mengunggah semua persyaratan di step tersebut (12 atau 20)
        if((int)$currProg === $progresSelanjutnya){
            $qTotalNext = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_skripsi WHERE progres = $progresSelanjutnya");
            $jmlTotalNext = ($qTotalNext && mysqli_num_rows($qTotalNext) > 0) ? (int)mysqli_fetch_assoc($qTotalNext)['total'] : 0;

            $qValidNext = query("SELECT COUNT(DISTINCT id_persyaratan) AS cnt FROM temp_skripsi WHERE nim = '$nim' AND status != 'ditolak' AND id_persyaratan IN (SELECT id FROM persyaratan_berkas_skripsi WHERE progres = $progresSelanjutnya)");
            $jmlValidNext = ($qValidNext && mysqli_num_rows($qValidNext) > 0) ? (int)mysqli_fetch_assoc($qValidNext)['cnt'] : 0;

            if($jmlTotalNext > 0 && $jmlValidNext >= $jmlTotalNext){
                $progresLompat = $progresSelanjutnya + 1;
                $waktuProgresLompat = $waktuProgres + 1;
                query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgresLompat, $progresLompat, '$nim')");
            }
        }
    }

    echo json_encode([
        'status'        => true,
        'pesan'         => 'Berkas berhasil disimpan',
        'selesai_semua' => $selesaiSemua
    ]);
    exit;
}

// ================= AMBIL SATU DATA (EDIT) =================
if(isset($_POST['ambil-skripsi'])){
    $nim = bersihkan($_POST['nim']);
    $ambil = query("SELECT s.*, m.nama AS nama_mhs FROM skripsi s LEFT JOIN mahasiswa m ON s.nim = m.nim WHERE s.nim = '$nim' LIMIT 1");
    
    if($ambil && mysqli_num_rows($ambil) > 0){
        $data = mysqli_fetch_assoc($ambil);
        echo json_encode([
            'status' => true, 
            'data' => [
                'nim' => $data['nim'],
                'nama' => $data['nama_mhs'] ?? '-',
                'judul' => $data['judul'] ?? '-',
                'status' => $data['status'] ?? 'Pengajuan'
            ]
        ]);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data tidak ditemukan']);
    }
    exit;
}

// ================= EDIT STATUS SKRIPSI =================
if(isset($_POST['edit-data-skripsi'])){
    $nim = bersihkan($_POST['nim']);
    $status = bersihkan($_POST['status']);
    
    if($nim === '' || $status === ''){
        echo json_encode(['status' => false, 'pesan' => 'Data tidak lengkap']);
        exit;
    }
    
    $update = query("UPDATE skripsi SET status = '" . $status . "' WHERE nim = '" . $nim . "'");
    
    if($update){
        echo json_encode(['status' => true, 'pesan' => 'Status skripsi berhasil diubah']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Gagal mengubah status']);
    }
    exit;
}

// ================= HAPUS DATA =================
if(isset($_POST['hapus-data-skripsi'])){
    echo json_encode(['status' => true, 'pesan' => 'Data skripsi berhasil dihapus']);
    exit;
}

echo json_encode(['status' => false, 'pesan' => 'Aksi tidak dikenali']);
?>