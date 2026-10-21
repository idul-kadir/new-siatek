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

    $where = ["1=1"];

    // Jika yang login level 3 atau 5, paksa filter hanya sesuai program studinya
    if (in_array($userLevel, [3, 5], true)) {
        $prodi = get_prodi_user();
        if ($prodi === '') {
            $where[] = "1=0"; // Level 3/5 tanpa prodi tidak boleh melihat data mahasiswa apapun
        } else {
            $where[] = "m.prodi = '$prodi'";
        }
    } elseif ($prodi !== '') {
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
                        <button type="button" class="btnArsip btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600" data-nim="<?= tampil($data['nim']) ?>" data-nama="<?= tampil($namaMhs) ?>" data-prodi="<?= tampil($prodiNm) ?>">
                            <i class="fas fa-box-archive text-xs"></i><span class="tip">Arsip</span>
                        </button>

                        <!-- 2. Tombol Lembar Kerja -->
                        <button type="button" class="btnLembarKerja btn-circle bg-orange-500 hover:bg-orange-600 text-white shadow-sm" data-nim="<?= tampil($data['nim']) ?>" data-progres="<?= tampil($data['progres_num']) ?>" data-status-skripsi="<?= tampil($data['status']) ?>" data-is-admin="<?= $isAdminEksekutor ? '1' : '0' ?>">
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
            // Progres Penunjukkan yang merupakan kewenangan Ketua Program Studi
            $progresPenunjukkan = ['3', '9', '17'];

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
            <?php elseif(in_array((string)$progres, $progresPenunjukkan, true)): ?>
                <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-500 mb-3 text-xl">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h6 class="font-bold text-blue-800 text-sm mb-2"><?= tampil($namaTugasSop) ?></h6>
                    <p class="text-xs text-blue-600">Saat ini pengaturan surat penunjukkan merupakan kewenangan Ketua Program Studi yang akan diatur di halaman penunjukkan.</p>
                </div>
            <?php else: ?>
                <p class="text-sm text-slate-600 mb-4">Pilih tindakan untuk menyelesaikan tahap skripsi saat ini dan memajukan progres mahasiswa ke tahap selanjutnya.</p>
                <form id="formAksiLembarKerja" class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                    <input type="hidden" name="aksi" value="proses-sop">
                    <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                    <input type="hidden" name="progres_sekarang" value="<?= tampil($progres) ?>">
                    
                    <?php
                    switch((int)$progres):
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
                            $qSyarat = query("SELECT id, nama_berkas FROM persyaratan_berkas_skripsi WHERE progres = 11 AND status = 'aktif' ORDER BY CASE WHEN nama_berkas LIKE '%Berita Acara%' THEN 0 ELSE 1 END ASC, nama_berkas ASC");
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
                            $qSyarat = query("SELECT id, nama_berkas FROM persyaratan_berkas_skripsi WHERE progres = 19 AND status = 'aktif' ORDER BY CASE WHEN nama_berkas LIKE '%Berita Acara%' THEN 0 ELSE 1 END ASC, nama_berkas ASC");
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
                    
                    // Tombol submit manual hanya untuk tahap 10, 18 (Jadwal Ujian). Tahap 11 & 19 auto-upload per item (tanpa tombol).
                    if(in_array((string)$progres, ['10', '18'], true)): ?>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 text-xs rounded-md bg-[#f97316] hover:bg-[#ea6a0f] text-white font-semibold shadow-sm" data-text="Simpan Jadwal">Simpan Jadwal</button>
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
                $qSyarat = query("SELECT id FROM persyaratan_berkas_skripsi WHERE progres = 11 AND status = 'aktif'");
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
                    $qTotal12 = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_skripsi WHERE progres = 12 AND status = 'aktif'");
                    $jmlTotal12 = ($qTotal12 && mysqli_num_rows($qTotal12) > 0) ? (int)mysqli_fetch_assoc($qTotal12)['total'] : 0;

                    $qValid12 = query("SELECT COUNT(DISTINCT id_persyaratan) AS cnt FROM temp_skripsi WHERE nim = '$nim' AND status != 'ditolak' AND id_persyaratan IN (SELECT id FROM persyaratan_berkas_skripsi WHERE progres = 12 AND status = 'aktif')");
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
                $qSyarat = query("SELECT id FROM persyaratan_berkas_skripsi WHERE progres = 19 AND status = 'aktif'");
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
                    $qTotal20 = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_skripsi WHERE progres = 20 AND status = 'aktif'");
                    $jmlTotal20 = ($qTotal20 && mysqli_num_rows($qTotal20) > 0) ? (int)mysqli_fetch_assoc($qTotal20)['total'] : 0;

                    $qValid20 = query("SELECT COUNT(DISTINCT id_persyaratan) AS cnt FROM temp_skripsi WHERE nim = '$nim' AND status != 'ditolak' AND id_persyaratan IN (SELECT id FROM persyaratan_berkas_skripsi WHERE progres = 20 AND status = 'aktif')");
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
    $qProgSyarat = query("SELECT progres FROM persyaratan_berkas_skripsi WHERE id = $idSyarat AND status = 'aktif' LIMIT 1");
    $progresSyarat = ($qProgSyarat && mysqli_num_rows($qProgSyarat) > 0) ? (int)mysqli_fetch_assoc($qProgSyarat)['progres'] : 11;
    $progresSelanjutnya = $progresSyarat + 1;

    // Cek apakah seluruh berkas untuk SOP progres ini sudah lengkap diunggah mahasiswa ini
    $qTotalSyarat = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_skripsi WHERE progres = $progresSyarat AND status = 'aktif'");
    $totalSyarat = $qTotalSyarat ? (int)mysqli_fetch_assoc($qTotalSyarat)['total'] : 6;

    $qUploaded = query("SELECT COUNT(DISTINCT b.id_persyaratan) AS total FROM berkas_skripsi b JOIN persyaratan_berkas_skripsi p ON b.id_persyaratan = p.id WHERE p.progres = $progresSyarat AND p.status = 'aktif' AND b.nim = '$nim'");
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
            $qTotalNext = query("SELECT COUNT(*) AS total FROM persyaratan_berkas_skripsi WHERE progres = $progresSelanjutnya AND status = 'aktif'");
            $jmlTotalNext = ($qTotalNext && mysqli_num_rows($qTotalNext) > 0) ? (int)mysqli_fetch_assoc($qTotalNext)['total'] : 0;

            $qValidNext = query("SELECT COUNT(DISTINCT id_persyaratan) AS cnt FROM temp_skripsi WHERE nim = '$nim' AND status != 'ditolak' AND id_persyaratan IN (SELECT id FROM persyaratan_berkas_skripsi WHERE progres = $progresSelanjutnya AND status = 'aktif')");
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

// ================= AMBIL DATA ARSIP & RIWAYAT MAHASISWA (DRAWER) =================
if(isset($_POST['ambil-arsip-riwayat'])){
    if (ob_get_length()) ob_clean();

    $nim = bersihkan($_POST['nim'] ?? '');
    if($nim === ''){
        echo json_encode(['status' => false, 'pesan' => 'NIM tidak valid']);
        exit;
    }

    $sql = "SELECT s.*, m.nama AS nama_mhs, m.prodi, m.angkatan, p.nama AS nama_prodi 
            FROM skripsi s 
            LEFT JOIN mahasiswa m ON s.nim = m.nim 
            LEFT JOIN prodi p ON m.prodi = p.kode 
            WHERE s.nim = '$nim' LIMIT 1";
    $qMhs = query($sql);
    if(!$qMhs || mysqli_num_rows($qMhs) === 0){
        echo json_encode(['status' => false, 'pesan' => 'Data mahasiswa tidak ditemukan']);
        exit;
    }

    $mhs = mysqli_fetch_assoc($qMhs);
    $namaMhs = !empty($mhs['nama_mhs']) ? $mhs['nama_mhs'] : '-';
    $prodiNm = !empty($mhs['nama_prodi']) ? $mhs['nama_prodi'] : (!empty($mhs['prodi']) ? $mhs['prodi'] : '-');
    $angkatanTxt = !empty($mhs['angkatan']) ? 'Angkatan ' . $mhs['angkatan'] : '';
    $progresVal = get_progres_skripsi($nim);
    if($progresVal === '-' || $progresVal === '') $progresVal = 1;
    $progresNum = (int)$progresVal;

    // Ambil SEMUA tahapan SOP secara berurutan sesuai SOP skripsi
    $allSop = [];
    $sopMap = [];
    $qSop = query("SELECT no, keterangan, eksekutor FROM sop_skripsi ORDER BY CAST(no AS UNSIGNED) ASC");
    if($qSop){
        while($sp = mysqli_fetch_assoc($qSop)){
            $allSop[] = $sp;
            $sopMap[(int)$sp['no']] = $sp['keterangan'];
        }
    }
    $labelProgresAktif = $sopMap[$progresNum] ?? 'Tahapan Skripsi';

    // Ambil riwayat penyelesaian tahapan dari tabel progres_skripsi
    // Instruksi user: "jika sop yang sudah selesai/dilewati maka harusnya ada tanggal dan jam berapa sop itu di selesaikan. ada di tabel progres_skripsi. id itu timeunixnya."
    $riwayatWaktuSop = [];
    try {
        $qProg = query("SELECT * FROM progres_skripsi WHERE mahasiswa = '$nim'");
        if($qProg){
            while($pr = mysqli_fetch_assoc($qProg)){
                $pNo = (int)($pr['proses'] ?? $pr['progres'] ?? 0);
                $tUnix = 0;
                // id adalah timeunix sesuai instruksi user
                if(!empty($pr['id']) && is_numeric($pr['id']) && (int)$pr['id'] > 1000000000){
                    $tUnix = (int)$pr['id'];
                } elseif(!empty($pr['waktu']) && is_numeric($pr['waktu']) && (int)$pr['waktu'] > 1000000000){
                    $tUnix = (int)$pr['waktu'];
                } elseif(!empty($pr['waktu']) && is_numeric($pr['waktu']) && (int)$pr['waktu'] > 0){
                    $tUnix = (int)$pr['waktu'];
                } elseif(!empty($pr['id']) && is_numeric($pr['id']) && (int)$pr['id'] > 0){
                    $tUnix = (int)$pr['id'];
                }
                if($pNo > 0 && $tUnix > 0){
                    $riwayatWaktuSop[$pNo] = $tUnix;
                }
            }
        }
    } catch (\Throwable $e) {
        // Fallback aman jika tabel atau nama kolom memiliki variasi
    }

    $formatWaktuSop = function($timeUnix) {
        if(!$timeUnix || !is_numeric($timeUnix) || (int)$timeUnix <= 0) return '-';
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

    $getWaktuSelesaiSop = function($noSop, $riwayatWaktuSop, $progresNum) {
        // 1. Cek waktu transisi ke tahap berikutnya jika ada (misal selesai SOP 3 saat masuk SOP 4)
        if(isset($riwayatWaktuSop[$noSop + 1]) && $riwayatWaktuSop[$noSop + 1] > 0){
            return $riwayatWaktuSop[$noSop + 1];
        }
        // 2. Cek waktu tercatat pada tahap tersebut
        if(isset($riwayatWaktuSop[$noSop]) && $riwayatWaktuSop[$noSop] > 0){
            return $riwayatWaktuSop[$noSop];
        }
        // 3. Cari catatan proses berikutnya yang terdekat
        foreach($riwayatWaktuSop as $p => $w){
            if($p >= $noSop && $w > 0){
                return $w;
            }
        }
        if(!empty($riwayatWaktuSop)){
            return reset($riwayatWaktuSop);
        }
        return 0;
    };

    // Ambil list semua dosen aktif untuk dropdown edit
    $listDosen = [];
    $dosenNameMap = [];
    $qDsn = query("SELECT id, nip, nama FROM dosen WHERE LOWER(posisi) = 'dosen' AND LOWER(status) = 'aktif' ORDER BY nama ASC");
    if($qDsn){
        while($ds = mysqli_fetch_assoc($qDsn)){
            $listDosen[] = $ds;
            $dosenNameMap[(string)$ds['id']] = $ds['nama'];
            if(!empty($ds['nip'])) $dosenNameMap[(string)$ds['nip']] = $ds['nama'];
        }
    }
    $getNamaDosen = function($id) use ($dosenNameMap) {
        $idStr = trim((string)$id);
        if($idStr === '') return '-';
        if(isset($dosenNameMap[$idStr])) return $dosenNameMap[$idStr];
        $q = query("SELECT nama FROM dosen WHERE id = '$idStr' OR nip = '$idStr' LIMIT 1");
        if($q && ($row = mysqli_fetch_assoc($q))) return $row['nama'];
        return $idStr;
    };

    // Ambil Surat Penunjukkan Pembimbing
    $suratBimb = null;
    $idBimb = (int)($mhs['penunjukkan_pembimbing'] ?? 0);
    if($idBimb > 0){
        $suratBimb = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE id = $idBimb LIMIT 1"));
    }
    if(!$suratBimb){
        $suratBimb = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING SKRIPSI' ORDER BY id DESC LIMIT 1"));
    }

    // Ambil Surat Penunjukkan Penguji Hasil
    $suratHasil = null;
    $idHasil = (int)($mhs['penunjukkan_hasil'] ?? 0);
    if($idHasil > 0){
        $suratHasil = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE id = $idHasil LIMIT 1"));
    }
    if(!$suratHasil){
        $suratHasil = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PENGUJI HASIL' ORDER BY id DESC LIMIT 1"));
    }

    // Ambil Surat Penunjukkan Penguji Tutup
    $suratTutup = null;
    $idTutup = (int)($mhs['penunjukkan_tutup'] ?? 0);
    if($idTutup > 0){
        $suratTutup = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE id = $idTutup LIMIT 1"));
    }
    if(!$suratTutup){
        $suratTutup = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PENGUJI TUTUP' ORDER BY id DESC LIMIT 1"));
    }

    // Ambil master seluruh persyaratan berkas skripsi dikelompokkan berdasarkan nomor progres (SOP)
    $masterSyarat = [];
    $qMs = query("SELECT id, nama_berkas, progres FROM persyaratan_berkas_skripsi WHERE status = 'aktif' ORDER BY progres ASC, id ASC");
    if($qMs){
        while($ms = mysqli_fetch_assoc($qMs)){
            $masterSyarat[(int)$ms['progres']][] = $ms;
        }
    }

    // Ambil berkas mahasiswa & operator dari tabel berkas_skripsi (Tabel UTAMA berkas tersimpan)
    $berkasMap = [];
    $qBs = query("SELECT bs.id AS id_berkas, bs.id_persyaratan, bs.file, f.nama_file 
                  FROM berkas_skripsi bs 
                  LEFT JOIN files f ON bs.file = f.id 
                  WHERE bs.nim = '$nim'");
    if($qBs){
        while($br = mysqli_fetch_assoc($qBs)){
            $berkasMap[(int)$br['id_persyaratan']] = [
                'id'        => (int)$br['id_berkas'],
                'file'      => $br['file'],
                'nama_file' => $br['nama_file'],
                'status'    => 'tersimpan',
                'ket'       => 'Tersimpan di berkas skripsi'
            ];
        }
    }

    // Ambil berkas dari temp_skripsi sebagai pelengkap jika ada berkas yang sedang dalam verifikasi (belum dipindah ke berkas_skripsi)
    $qTemp = query("SELECT ts.id AS id_temp, ts.id_persyaratan, ts.file, ts.status, ts.ket, f.nama_file 
                    FROM temp_skripsi ts 
                    LEFT JOIN files f ON ts.file = f.id 
                    WHERE ts.nim = '$nim'");
    if($qTemp){
        while($tr = mysqli_fetch_assoc($qTemp)){
            $idP = (int)$tr['id_persyaratan'];
            if(!isset($berkasMap[$idP]) || empty($berkasMap[$idP]['file'])){
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

    // Otoritas wewenang unggah ulang berkas hanya level 5, 6, dan 9
    $canUploadBerkas = in_array($userLevel, [5, 6, 9], true);

    // Helper render daftar berkas mahasiswa/operator + fitur lihat & unggah ulang
    $renderListBerkas = function($listSyarat, $berkasMap, $nim, $labelSection = 'Berkas Persyaratan') use ($canUploadBerkas) {
        if(empty($listSyarat)) return '';
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
                <?php foreach($listSyarat as $syarat): 
                    $idSyarat = (int)$syarat['id'];
                    $itemUploaded = $berkasMap[$idSyarat] ?? null;
                    $hasFile = ($itemUploaded && !empty($itemUploaded['file']));
                    $fileUrl = $hasFile ? url_file($itemUploaded['file']) : '';
                    $rawStatus = strtolower(trim((string)($itemUploaded['status'] ?? ($hasFile ? 'tersimpan' : 'belum ada'))));
                    
                    $statusBadgeClass = 'bg-slate-100 text-slate-500';
                    $statusLabel = 'Belum Diunggah';
                    if($rawStatus === 'diterima' || $rawStatus === 'tersimpan'){
                        $statusBadgeClass = 'bg-emerald-100 text-emerald-700';
                        $statusLabel = 'Tersimpan';
                    } elseif($rawStatus === 'menunggu'){
                        $statusBadgeClass = 'bg-amber-100 text-amber-700';
                        $statusLabel = 'Menunggu';
                    } elseif($rawStatus === 'ditolak'){
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
                                <?php if(!empty($itemUploaded['ket'])): ?>
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

                            <?php if($canUploadBerkas): ?>
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
                    SOP <?= $progresNum ?>: <?= tampil($labelProgresAktif) ?>
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
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Alur SOP Skripsi</span>
            <span class="text-[11px] font-medium text-slate-400">(<?= count($allSop) ?> Tahapan)</span>
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

    <!-- ===== Timeline Accordion Sequential Berdasarkan SOP Skripsi ===== -->
    <div class="space-y-3" id="accordionArsip">
        <?php 
        foreach($allSop as $sopItem):
            $noSop = (int)$sopItem['no'];
            $ketSop = $sopItem['keterangan'];
            $eksekutorSop = $sopItem['eksekutor'] ?? '';

            $isCompleted = ($noSop < $progresNum);
            $isCurrent   = ($noSop === $progresNum);
            $isFuture    = ($noSop > $progresNum);
            $canExpand   = $isCompleted; // HANYA SOP yang sudah selesai/dilewati yang bisa dibuka accordionnya

            $waktuSelesaiUnix = $isCompleted ? $getWaktuSelesaiSop($noSop, $riwayatWaktuSop, $progresNum) : 0;
            $waktuSelesaiFormatted = ($waktuSelesaiUnix > 0) ? $formatWaktuSop($waktuSelesaiUnix) : '-';

            // Tentukan status badge
            if($isCompleted){
                $badgeText = 'Selesai';
                $badgeStyle = 'bg-emerald-100 text-emerald-700 border border-emerald-200/60 font-semibold';
                $iconCircle = 'bg-emerald-500 text-white';
            } elseif($isCurrent){
                $badgeText = 'Sedang Berjalan';
                $badgeStyle = 'bg-orange-100 text-orange-700 border border-orange-200/60 font-bold';
                $iconCircle = 'bg-orange-500 text-white ring-2 ring-orange-400/40';
            } else {
                $badgeText = 'Belum Ditempuh';
                $badgeStyle = 'bg-slate-100 text-slate-500 font-medium';
                $iconCircle = 'bg-slate-200 text-slate-500';
            }
        ?>
        <div class="border border-slate-200 rounded-xl bg-white shadow-xs overflow-hidden transition-all duration-200 <?= $canExpand ? 'card-completed' : 'card-locked' ?>" data-card-stage="<?= $noSop ?>">
            <!-- Header Kartu SOP -->
            <?php if($canExpand): ?>
            <div class="flex items-center justify-between p-3.5 cursor-pointer bg-slate-50 hover:bg-slate-100/80 transition btn-toggle-accordion">
            <?php else: ?>
            <div class="flex items-center justify-between p-3.5 bg-slate-50/50 cursor-default select-none opacity-85" title="Tahapan sedang berlangsung atau belum ditempuh (tidak dapat dibuka)">
            <?php endif; ?>
                <div class="flex items-center gap-3 min-w-0">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg <?= $iconCircle ?> text-xs font-bold shadow-xs">
                        <?php if($isCompleted): ?>
                            <i class="fas fa-check"></i>
                        <?php elseif($isCurrent): ?>
                            <span class="text-[11px] font-bold"><?= $noSop ?></span>
                        <?php else: ?>
                            <span class="text-[11px] font-medium text-slate-500"><?= $noSop ?></span>
                        <?php endif; ?>
                    </span>
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-slate-800 leading-snug">
                            SOP <?= $noSop ?>: <?= tampil($ketSop) ?>
                        </h4>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-0.5 text-[10px]">
                            <?php if(!empty($eksekutorSop)): ?>
                                <span class="text-slate-500">Eksekutor: <strong class="text-slate-700 font-semibold"><?= tampil($eksekutorSop) ?></strong></span>
                            <?php endif; ?>
                            <?php if($isCompleted && !empty($waktuSelesaiFormatted) && $waktuSelesaiFormatted !== '-'): ?>
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
                    <?php if($canExpand): ?>
                        <i class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200 acc-chevron"></i>
                    <?php else: ?>
                        <span title="Tahapan belum selesai (tidak dapat dibuka)">
                            <i class="fas fa-lock text-[11px] text-slate-300"></i>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($canExpand): ?>
            <!-- Body Kartu SOP (HANYA UNTUK SOP YANG SUDAH SELESAI/LAMPAU) -->
            <div class="acc-body p-4 border-t border-slate-100 hidden" style="display: none;">
                
                <!-- ================= DETAIL SESUAI NOMOR SOP ================= -->
                
                <?php if($noSop === 1): ?>
                    <!-- SOP 1: Pengajuan Judul & Status Skripsi -->
                    <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60">
                        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                            <h5 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-file-signature text-orange-500"></i> Data Pokok Judul &amp; Status
                            </h5>
                            <button type="button" onclick="toggleEditBox(this, true)" class="btn-toggle-edit inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition shadow-2xs">
                                <i class="fas fa-pen-to-square"></i> Koreksi Data
                            </button>
                        </div>

                        <!-- View Mode -->
                        <div class="view-box space-y-3">
                            <div>
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Judul Skripsi</span>
                                <p class="text-xs font-medium text-slate-800 bg-white p-3 rounded-lg border border-slate-200/80 leading-relaxed target-judul-display">
                                    <?= !empty($mhs['judul']) ? tampil($mhs['judul']) : '<em class="text-slate-400">Belum ada judul tercatat</em>' ?>
                                </p>
                            </div>
                            <div>
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Status Skripsi</span>
                                <span class="text-xs font-bold text-slate-700 bg-white border border-slate-200 px-2.5 py-1 rounded inline-block target-status-display">
                                    <?= tampil($mhs['status'] ?? '-') ?>
                                </span>
                            </div>
                        </div>

                        <!-- Edit Mode (Hidden) -->
                        <div class="edit-box hidden pt-1" style="display: none;">
                            <form class="form-koreksi-arsip space-y-3" data-step="judul">
                                <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                                <input type="hidden" name="step_type" value="judul">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Skripsi</label>
                                    <textarea name="judul" rows="3" class="w-full text-xs rounded-lg border border-slate-300 p-2.5 focus:border-orange-500 focus:outline-none" required><?= tampil($mhs['judul'] ?? '') ?></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Status Skripsi</label>
                                    <select name="status" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none">
                                        <option value="Aktif" <?= ($mhs['status'] ?? '') === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="Pengajuan" <?= ($mhs['status'] ?? '') === 'Pengajuan' ? 'selected' : '' ?>>Pengajuan</option>
                                        <option value="Bimbingan" <?= ($mhs['status'] ?? '') === 'Bimbingan' ? 'selected' : '' ?>>Bimbingan</option>
                                        <option value="tidak aktif" <?= ($mhs['status'] ?? '') === 'tidak aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                                        <option value="Alumni" <?= ($mhs['status'] ?? '') === 'Alumni' ? 'selected' : '' ?>>Alumni</option>
                                        <option value="Kadaluarsa" <?= ($mhs['status'] ?? '') === 'Kadaluarsa' ? 'selected' : '' ?>>Kadaluarsa</option>
                                    </select>
                                </div>
                                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                                    <button type="button" onclick="toggleEditBox(this, false)" class="btn-cancel-edit px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                                    <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-orange-600 hover:bg-orange-700 text-white shadow-xs transition">Simpan Koreksi</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Berkas Pendaftaran Mahasiswa (SOP 1) dari berkas_skripsi -->
                    <?= $renderListBerkas($masterSyarat[1] ?? [], $berkasMap, $nim, 'Berkas Pendaftaran / Proposal Mahasiswa') ?>

                <?php elseif($noSop === 3): ?>
                    <!-- SOP 3: Surat Penunjukkan & Dosen Pembimbing -->
                    <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60">
                        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                            <h5 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-user-tie text-orange-500"></i> Data Surat &amp; Dosen Pembimbing
                            </h5>
                            <button type="button" onclick="toggleEditBox(this, true)" class="btn-toggle-edit inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition shadow-2xs">
                                <i class="fas fa-pen-to-square"></i> Koreksi Data
                            </button>
                        </div>

                        <!-- View Mode -->
                        <div class="view-box space-y-3">
                            <div class="grid grid-cols-2 gap-3 bg-white p-3 rounded-lg border border-slate-200/80">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">No. Surat Penunjukkan</span>
                                    <span class="text-xs font-semibold text-slate-800"><?= !empty($suratBimb['no_surat']) ? tampil($suratBimb['no_surat']) : '-' ?></span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tanggal Surat</span>
                                    <span class="text-xs text-slate-700"><?= !empty($suratBimb['tanggal']) ? tampil($suratBimb['tanggal']) : '-' ?></span>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs p-2.5 rounded-lg bg-white border border-slate-200/80">
                                    <span class="text-slate-500 font-medium">Pembimbing 1:</span>
                                    <span class="font-bold text-slate-800"><?= tampil($getNamaDosen($suratBimb['pembimbing1'] ?? '')) ?></span>
                                </div>
                                <div class="flex items-center justify-between text-xs p-2.5 rounded-lg bg-white border border-slate-200/80">
                                    <span class="text-slate-500 font-medium">Pembimbing 2:</span>
                                    <span class="font-bold text-slate-800"><?= tampil($getNamaDosen($suratBimb['pembimbing2'] ?? '')) ?></span>
                                </div>
                            </div>

                            <div class="pt-1">
                                <a href="<?= base_url() ?>/administrator/dokumen/surat-penunjukkan-pembimbing-skripsi/<?= tampil($nim) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs">
                                    <i class="fas fa-file-pdf"></i> Cetak Surat Penunjukkan Pembimbing <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Edit Mode (Hidden) -->
                        <div class="edit-box hidden pt-1" style="display: none;">
                            <form class="form-koreksi-arsip space-y-3" data-step="pembimbing">
                                <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                                <input type="hidden" name="step_type" value="pembimbing">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">No. Surat</label>
                                        <input type="text" name="no_surat" value="<?= tampil($suratBimb['no_surat'] ?? '') ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Surat</label>
                                        <input type="date" name="tanggal" value="<?= tampil($suratBimb['tanggal'] ?? date('Y-m-d')) ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Pembimbing 1</label>
                                    <select name="pembimbing1" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                        <option value="">-- Pilih Pembimbing 1 --</option>
                                        <?php foreach($listDosen as $dsn): 
                                            $sel = ((string)$dsn['id'] === (string)($suratBimb['pembimbing1'] ?? '') || (string)$dsn['nip'] === (string)($suratBimb['pembimbing1'] ?? '')) ? 'selected' : '';
                                        ?>
                                            <option value="<?= tampil($dsn['id']) ?>" <?= $sel ?>><?= tampil($dsn['nama']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Pembimbing 2</label>
                                    <select name="pembimbing2" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                        <option value="">-- Pilih Pembimbing 2 --</option>
                                        <?php foreach($listDosen as $dsn): 
                                            $sel = ((string)$dsn['id'] === (string)($suratBimb['pembimbing2'] ?? '') || (string)$dsn['nip'] === (string)($suratBimb['pembimbing2'] ?? '')) ? 'selected' : '';
                                        ?>
                                            <option value="<?= tampil($dsn['id']) ?>" <?= $sel ?>><?= tampil($dsn['nama']) ?></option>
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

                    <?= $renderListBerkas($masterSyarat[3] ?? [], $berkasMap, $nim, 'Berkas Penunjukkan Pembimbing') ?>

                <?php elseif($noSop === 9): ?>
                    <!-- SOP 9: Surat Penunjukkan Penguji Hasil -->
                    <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60">
                        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                            <h5 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-file-contract text-orange-500"></i> Tim Dosen Penguji Seminar Hasil
                            </h5>
                            <button type="button" onclick="toggleEditBox(this, true)" class="btn-toggle-edit inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition shadow-2xs">
                                <i class="fas fa-pen-to-square"></i> Koreksi Data
                            </button>
                        </div>

                        <!-- View Penguji Hasil -->
                        <div class="view-box text-xs space-y-2">
                            <p class="text-xs text-slate-600">No. Surat: <span class="font-bold text-slate-800"><?= !empty($suratHasil['no_surat']) ? tampil($suratHasil['no_surat']) : '-' ?></span></p>
                            <div class="grid grid-cols-1 gap-1.5 text-xs bg-white p-3 rounded-lg border border-slate-200/80">
                                <?php for($i=1; $i<=5; $i++): 
                                    $idPenguji = $suratHasil['penguji'.$i] ?? '';
                                    if(!empty($idPenguji)):
                                ?>
                                    <div class="flex items-center justify-between py-0.5 border-b border-slate-100 last:border-0">
                                        <span class="text-slate-500 font-medium">Penguji <?= $i ?>:</span> 
                                        <span class="font-bold text-slate-800"><?= tampil($getNamaDosen($idPenguji)) ?></span>
                                    </div>
                                <?php endif; endfor; ?>
                            </div>
                            <div class="pt-1">
                                <a href="<?= base_url() ?>/administrator/dokumen/surat-penunjukkan-penguji/<?= tampil($nim) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs">
                                    <i class="fas fa-file-pdf"></i> Cetak Surat Penguji Hasil <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Edit Penguji Hasil -->
                        <div class="edit-box hidden pt-1" style="display: none;">
                            <form class="form-koreksi-arsip space-y-3" data-step="penguji_hasil">
                                <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                                <input type="hidden" name="step_type" value="penguji_hasil">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">No. Surat</label>
                                    <input type="text" name="no_surat" value="<?= tampil($suratHasil['no_surat'] ?? '') ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                </div>
                                <?php for($i=1; $i<=5; $i++): 
                                    $currP = (string)($suratHasil['penguji'.$i] ?? '');
                                ?>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Penguji <?= $i ?></label>
                                    <select name="penguji<?= $i ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                        <option value="">-- Pilih Penguji <?= $i ?> --</option>
                                        <?php foreach($listDosen as $dsn): 
                                            $sel = ((string)$dsn['id'] === $currP || (string)$dsn['nip'] === $currP) ? 'selected' : '';
                                        ?>
                                            <option value="<?= tampil($dsn['id']) ?>" <?= $sel ?>><?= tampil($dsn['nama']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endfor; ?>
                                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                                    <button type="button" onclick="toggleEditBox(this, false)" class="btn-cancel-edit px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                                    <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-orange-600 hover:bg-orange-700 text-white shadow-xs transition">Simpan Koreksi</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?= $renderListBerkas($masterSyarat[9] ?? [], $berkasMap, $nim, 'Berkas Terkait SOP 9') ?>

                <?php elseif($noSop === 10): ?>
                    <!-- SOP 10: Jadwal Ujian Seminar Hasil -->
                    <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60">
                        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                            <h5 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-calendar-alt text-blue-500"></i> Jadwal Ujian Seminar Hasil
                            </h5>
                            <button type="button" onclick="toggleEditBox(this, true)" class="btn-toggle-edit inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition shadow-2xs">
                                <i class="fas fa-pen-to-square"></i> Koreksi Data
                            </button>
                        </div>

                        <!-- View Jadwal -->
                        <div class="view-box text-xs space-y-2">
                            <div class="grid grid-cols-2 gap-2.5 bg-white p-3 rounded-lg border border-slate-200/80 text-xs">
                                <div><span class="text-slate-400 block text-[10px] uppercase font-bold">No. Undangan</span> <span class="font-bold text-slate-800"><?= !empty($mhs['no_undangan_hasil']) ? tampil($mhs['no_undangan_hasil']) : '-' ?></span></div>
                                <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal</span> <span class="font-bold text-slate-800"><?= !empty($mhs['tanggal_hasil']) ? tampil($mhs['tanggal_hasil']) : '-' ?></span></div>
                                <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Jam</span> <span class="font-bold text-slate-800"><?= !empty($mhs['jam_hasil']) ? tampil($mhs['jam_hasil']) : '-' ?></span></div>
                                <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Ruangan</span> <span class="font-bold text-slate-800"><?= !empty($mhs['ruangan_hasil']) ? tampil($mhs['ruangan_hasil']) : '-' ?></span></div>
                            </div>
                            <div class="pt-1">
                                <a href="<?= base_url() ?>/administrator/dokumen/komponen-hasil/<?= tampil($nim) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition shadow-2xs">
                                    <i class="fas fa-file-pdf"></i> Cetak Komponen / Berita Acara Hasil <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Edit Jadwal -->
                        <div class="edit-box hidden pt-1" style="display: none;">
                            <form class="form-koreksi-arsip space-y-3" data-step="jadwal_hasil">
                                <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                                <input type="hidden" name="step_type" value="jadwal_hasil">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">No. Undangan</label>
                                    <input type="text" name="no_undangan_hasil" value="<?= tampil($mhs['no_undangan_hasil'] ?? '') ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal</label>
                                        <input type="date" name="tanggal_hasil" value="<?= tampil($mhs['tanggal_hasil'] ?? '') ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Jam</label>
                                        <input type="text" name="jam_hasil" value="<?= tampil($mhs['jam_hasil'] ?? '') ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Ruangan</label>
                                    <input type="text" name="ruangan_hasil" value="<?= tampil($mhs['ruangan_hasil'] ?? '') ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                </div>
                                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                                    <button type="button" onclick="toggleEditBox(this, false)" class="btn-cancel-edit px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                                    <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-orange-600 hover:bg-orange-700 text-white shadow-xs transition">Simpan Koreksi</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?= $renderListBerkas($masterSyarat[10] ?? [], $berkasMap, $nim, 'Berkas Terkait SOP 10') ?>

                <?php elseif($noSop === 17): ?>
                    <!-- SOP 17: Surat Penunjukkan Penguji Sidang Tutup -->
                    <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60">
                        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                            <h5 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-file-contract text-orange-500"></i> Tim Dosen Penguji Sidang Tutup
                            </h5>
                            <button type="button" onclick="toggleEditBox(this, true)" class="btn-toggle-edit inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition shadow-2xs">
                                <i class="fas fa-pen-to-square"></i> Koreksi Data
                            </button>
                        </div>

                        <!-- View Penguji Tutup -->
                        <div class="view-box text-xs space-y-2">
                            <p class="text-xs text-slate-600">No. Surat: <span class="font-bold text-slate-800"><?= !empty($suratTutup['no_surat']) ? tampil($suratTutup['no_surat']) : '-' ?></span></p>
                            <div class="grid grid-cols-1 gap-1.5 text-xs bg-white p-3 rounded-lg border border-slate-200/80">
                                <?php for($i=1; $i<=5; $i++): 
                                    $idPenguji = $suratTutup['penguji'.$i] ?? '';
                                    if(!empty($idPenguji)):
                                ?>
                                    <div class="flex items-center justify-between py-0.5 border-b border-slate-100 last:border-0">
                                        <span class="text-slate-500 font-medium">Penguji <?= $i ?>:</span> 
                                        <span class="font-bold text-slate-800"><?= tampil($getNamaDosen($idPenguji)) ?></span>
                                    </div>
                                <?php endif; endfor; ?>
                            </div>
                            <div class="pt-1">
                                <a href="<?= base_url() ?>/administrator/dokumen/surat-penunjukkan-penguji/<?= tampil($nim) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs">
                                    <i class="fas fa-file-pdf"></i> Cetak Surat Penguji Tutup <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Edit Penguji Tutup -->
                        <div class="edit-box hidden pt-1" style="display: none;">
                            <form class="form-koreksi-arsip space-y-3" data-step="penguji_tutup">
                                <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                                <input type="hidden" name="step_type" value="penguji_tutup">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">No. Surat</label>
                                    <input type="text" name="no_surat" value="<?= tampil($suratTutup['no_surat'] ?? '') ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                </div>
                                <?php for($i=1; $i<=5; $i++): 
                                    $currP = (string)($suratTutup['penguji'.$i] ?? '');
                                ?>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Penguji <?= $i ?></label>
                                    <select name="penguji<?= $i ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                        <option value="">-- Pilih Penguji <?= $i ?> --</option>
                                        <?php foreach($listDosen as $dsn): 
                                            $sel = ((string)$dsn['id'] === $currP || (string)$dsn['nip'] === $currP) ? 'selected' : '';
                                        ?>
                                            <option value="<?= tampil($dsn['id']) ?>" <?= $sel ?>><?= tampil($dsn['nama']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endfor; ?>
                                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                                    <button type="button" onclick="toggleEditBox(this, false)" class="btn-cancel-edit px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                                    <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-orange-600 hover:bg-orange-700 text-white shadow-xs transition">Simpan Koreksi</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?= $renderListBerkas($masterSyarat[17] ?? [], $berkasMap, $nim, 'Berkas Terkait SOP 17') ?>

                <?php elseif($noSop === 18): ?>
                    <!-- SOP 18: Jadwal Ujian Sidang Tutup -->
                    <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60">
                        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                            <h5 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-calendar-alt text-blue-500"></i> Jadwal Ujian Sidang Skripsi (Tutup)
                            </h5>
                            <button type="button" onclick="toggleEditBox(this, true)" class="btn-toggle-edit inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition shadow-2xs">
                                <i class="fas fa-pen-to-square"></i> Koreksi Data
                            </button>
                        </div>

                        <!-- View Jadwal Tutup -->
                        <div class="view-box text-xs space-y-2">
                            <div class="grid grid-cols-2 gap-2.5 bg-white p-3 rounded-lg border border-slate-200/80 text-xs">
                                <div><span class="text-slate-400 block text-[10px] uppercase font-bold">No. Undangan</span> <span class="font-bold text-slate-800"><?= !empty($mhs['no_undangan_tutup']) ? tampil($mhs['no_undangan_tutup']) : '-' ?></span></div>
                                <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal</span> <span class="font-bold text-slate-800"><?= !empty($mhs['tanggal_tutup']) ? tampil($mhs['tanggal_tutup']) : '-' ?></span></div>
                                <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Jam</span> <span class="font-bold text-slate-800"><?= !empty($mhs['jam_tutup']) ? tampil($mhs['jam_tutup']) : '-' ?></span></div>
                                <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Ruangan</span> <span class="font-bold text-slate-800"><?= !empty($mhs['ruangan_tutup']) ? tampil($mhs['ruangan_tutup']) : '-' ?></span></div>
                            </div>
                            <div class="pt-1">
                                <a href="<?= base_url() ?>/administrator/dokumen/komponen-tutup/<?= tampil($nim) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition shadow-2xs">
                                    <i class="fas fa-file-pdf"></i> Cetak Komponen / Berita Acara Tutup <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Edit Jadwal Tutup -->
                        <div class="edit-box hidden pt-1" style="display: none;">
                            <form class="form-koreksi-arsip space-y-3" data-step="jadwal_tutup">
                                <input type="hidden" name="nim" value="<?= tampil($nim) ?>">
                                <input type="hidden" name="step_type" value="jadwal_tutup">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">No. Undangan</label>
                                    <input type="text" name="no_undangan_tutup" value="<?= tampil($mhs['no_undangan_tutup'] ?? '') ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal</label>
                                        <input type="date" name="tanggal_tutup" value="<?= tampil($mhs['tanggal_tutup'] ?? '') ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Jam</label>
                                        <input type="text" name="jam_tutup" value="<?= tampil($mhs['jam_tutup'] ?? '') ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Ruangan</label>
                                    <input type="text" name="ruangan_tutup" value="<?= tampil($mhs['ruangan_tutup'] ?? '') ?>" class="w-full text-xs rounded-lg border border-slate-300 p-2 focus:border-orange-500 focus:outline-none" required>
                                </div>
                                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                                    <button type="button" onclick="toggleEditBox(this, false)" class="btn-cancel-edit px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                                    <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-orange-600 hover:bg-orange-700 text-white shadow-xs transition">Simpan Koreksi</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?= $renderListBerkas($masterSyarat[18] ?? [], $berkasMap, $nim, 'Berkas Terkait SOP 18') ?>

                <?php else: ?>
                    <!-- SOP Lainnya (SOP 2, 4, 5, 6, 7, 8, 11, 12, 13, 14, 15, 16, 19, 20, 21, dst.) -->
                    
                    <?php if(!empty($masterSyarat[$noSop])): ?>
                        <!-- Jika SOP ini memiliki berkas di persyaratan_berkas_skripsi -->
                        <?= $renderListBerkas($masterSyarat[$noSop], $berkasMap, $nim, 'Berkas Persyaratan (SOP ' . $noSop . ')') ?>
                    <?php else: ?>
                        <!-- Jika SOP bersifat prosedural / verifikasi tanpa berkas input langsung -->
                        <div class="py-2.5 px-3.5 rounded-lg bg-slate-50 border border-slate-200/80 text-xs text-slate-600">
                            <p class="leading-relaxed">
                                <i class="fas fa-info-circle text-orange-500 mr-1.5"></i>
                                Tahapan ini merupakan proses verifikasi/persetujuan oleh <span class="font-semibold text-slate-800"><?= tampil($eksekutorSop !== '' ? $eksekutorSop : 'Pengelola') ?></span>.
                                <?php if($isCompleted): ?>
                                    <span class="text-emerald-600 font-semibold block mt-1"><i class="fas fa-check-circle mr-1"></i>Tahapan ini telah selesai ditempuh.</span>
                                <?php elseif($isCurrent): ?>
                                    <span class="text-orange-600 font-semibold block mt-1"><i class="fas fa-spinner fa-spin mr-1"></i>Mahasiswa saat ini sedang dalam tahapan ini.</span>
                                <?php else: ?>
                                    <span class="text-slate-400 block mt-1"><i class="fas fa-clock mr-1"></i>Tahapan ini belum dimulai.</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    $statusSt = $mhs['status'] ?? 'Draft';
    $statusBadge = ($statusSt === 'Pengajuan' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 
                   ($statusSt === 'Bimbingan' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 
                   ($statusSt === 'tidak aktif' ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200')));

    $html = ob_get_clean();
    echo json_encode([
        'status' => true, 
        'data' => $html,
        'mhs' => [
            'nama' => $namaMhs,
            'nim' => $nim,
            'prodi' => $prodiNm,
            'angkatan' => $angkatanTxt,
            'status' => $statusSt,
            'badge_class' => $statusBadge,
            'progres_num' => $progresNum,
            'progres_label' => $labelProgresAktif
        ]
    ]);
    exit;
}

// ================= KOREKSI DATA ARSIP STEP (DRAWER) =================
if(isset($_POST['koreksi-arsip-step'])){
    if (ob_get_length()) ob_clean();

    $nim = bersihkan($_POST['nim'] ?? '');
    $stepType = bersihkan($_POST['step_type'] ?? '');

    if($nim === ''){
        echo json_encode(['status' => false, 'pesan' => 'NIM tidak valid']);
        exit;
    }

    if($stepType === 'judul'){
        $judul = bersihkan($_POST['judul'] ?? '');
        $status = bersihkan($_POST['status'] ?? '');
        if($judul === ''){
            echo json_encode(['status' => false, 'pesan' => 'Judul skripsi tidak boleh kosong']);
            exit;
        }

        $sqlSet = ["judul = '$judul'"];
        if($status !== ''){
            $sqlSet[] = "status = '$status'";
        }
        $update = query("UPDATE skripsi SET " . implode(", ", $sqlSet) . " WHERE nim = '$nim'");
        if($update){
            echo json_encode(['status' => true, 'pesan' => 'Judul dan status skripsi berhasil diperbarui']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui judul: ' . mysqli_error($GLOBALS['koneksi'])]);
        }
        exit;
    }

    if($stepType === 'pembimbing'){
        $noSurat = bersihkan($_POST['no_surat'] ?? '');
        $p1 = bersihkan($_POST['pembimbing1'] ?? '');
        $p2 = bersihkan($_POST['pembimbing2'] ?? '');
        $tgl = bersihkan($_POST['tanggal'] ?? date('Y-m-d'));

        if($noSurat === '' || $p1 === '' || $p2 === ''){
            echo json_encode(['status' => false, 'pesan' => 'Nomor surat dan pembimbing wajib diisi']);
            exit;
        }

        $sSkripsi = mysqli_fetch_assoc(query("SELECT penunjukkan_pembimbing FROM skripsi WHERE nim = '$nim' LIMIT 1"));
        $idSurat = (int)($sSkripsi['penunjukkan_pembimbing'] ?? 0);
        if($idSurat <= 0){
            $sSurat = mysqli_fetch_assoc(query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING SKRIPSI' ORDER BY id DESC LIMIT 1"));
            if($sSurat) $idSurat = (int)$sSurat['id'];
        }

        if($idSurat > 0){
            $ok = query("UPDATE surat_penunjukkan SET no_surat = '$noSurat', pembimbing1 = '$p1', pembimbing2 = '$p2', tanggal = '$tgl' WHERE id = $idSurat");
        } else {
            $idSurat = time();
            $ok = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, pembimbing1, pembimbing2, tanggal, keterangan) VALUES ($idSurat, '$noSurat', '$nim', '$p1', '$p2', '$tgl', 'PEMBIMBING SKRIPSI')");
            query("UPDATE skripsi SET penunjukkan_pembimbing = '$idSurat' WHERE nim = '$nim'");
        }

        if($ok){
            echo json_encode(['status' => true, 'pesan' => 'Data surat dan dosen pembimbing berhasil diperbarui']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui data pembimbing']);
        }
        exit;
    }

    if($stepType === 'penguji_hasil'){
        $noSurat = bersihkan($_POST['no_surat'] ?? '');
        $p1 = bersihkan($_POST['penguji1'] ?? '');
        $p2 = bersihkan($_POST['penguji2'] ?? '');
        $p3 = bersihkan($_POST['penguji3'] ?? '');
        $p4 = bersihkan($_POST['penguji4'] ?? '');
        $p5 = bersihkan($_POST['penguji5'] ?? '');
        $tgl = date('Y-m-d');

        if($noSurat === '' || $p1 === '' || $p2 === '' || $p3 === '' || $p4 === '' || $p5 === ''){
            echo json_encode(['status' => false, 'pesan' => 'Nomor surat dan 5 dosen penguji wajib diisi']);
            exit;
        }

        $sSkripsi = mysqli_fetch_assoc(query("SELECT penunjukkan_hasil FROM skripsi WHERE nim = '$nim' LIMIT 1"));
        $idSurat = (int)($sSkripsi['penunjukkan_hasil'] ?? 0);
        if($idSurat <= 0){
            $sSurat = mysqli_fetch_assoc(query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PENGUJI HASIL' ORDER BY id DESC LIMIT 1"));
            if($sSurat) $idSurat = (int)$sSurat['id'];
        }

        if($idSurat > 0){
            $ok = query("UPDATE surat_penunjukkan SET no_surat = '$noSurat', penguji1 = '$p1', penguji2 = '$p2', penguji3 = '$p3', penguji4 = '$p4', penguji5 = '$p5', tanggal = '$tgl' WHERE id = $idSurat");
        } else {
            $idSurat = time();
            $ok = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, penguji1, penguji2, penguji3, penguji4, penguji5, tanggal, keterangan) VALUES ($idSurat, '$noSurat', '$nim', '$p1', '$p2', '$p3', '$p4', '$p5', '$tgl', 'PENGUJI HASIL')");
            query("UPDATE skripsi SET penunjukkan_hasil = '$idSurat' WHERE nim = '$nim'");
        }

        if($ok){
            echo json_encode(['status' => true, 'pesan' => 'Data surat penguji hasil berhasil diperbarui']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui data penguji hasil']);
        }
        exit;
    }

    if($stepType === 'jadwal_hasil'){
        $noUndangan = bersihkan($_POST['no_undangan_hasil'] ?? '');
        $tanggal    = bersihkan($_POST['tanggal_hasil'] ?? '');
        $jam        = bersihkan($_POST['jam_hasil'] ?? '');
        $ruangan    = bersihkan($_POST['ruangan_hasil'] ?? '');

        if($noUndangan === '' || $tanggal === '' || $jam === '' || $ruangan === ''){
            echo json_encode(['status' => false, 'pesan' => 'Nomor undangan, tanggal, jam, dan ruangan wajib diisi']);
            exit;
        }

        $ok = query("UPDATE skripsi SET no_undangan_hasil = '$noUndangan', tanggal_hasil = '$tanggal', jam_hasil = '$jam', ruangan_hasil = '$ruangan' WHERE nim = '$nim'");
        if($ok){
            echo json_encode(['status' => true, 'pesan' => 'Jadwal seminar hasil berhasil diperbarui']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui jadwal seminar hasil']);
        }
        exit;
    }

    if($stepType === 'penguji_tutup'){
        $noSurat = bersihkan($_POST['no_surat'] ?? '');
        $p1 = bersihkan($_POST['penguji1'] ?? '');
        $p2 = bersihkan($_POST['penguji2'] ?? '');
        $p3 = bersihkan($_POST['penguji3'] ?? '');
        $p4 = bersihkan($_POST['penguji4'] ?? '');
        $p5 = bersihkan($_POST['penguji5'] ?? '');
        $tgl = date('Y-m-d');

        if($noSurat === '' || $p1 === '' || $p2 === '' || $p3 === '' || $p4 === '' || $p5 === ''){
            echo json_encode(['status' => false, 'pesan' => 'Nomor surat dan 5 dosen penguji tutup wajib diisi']);
            exit;
        }

        $sSkripsi = mysqli_fetch_assoc(query("SELECT penunjukkan_tutup FROM skripsi WHERE nim = '$nim' LIMIT 1"));
        $idSurat = (int)($sSkripsi['penunjukkan_tutup'] ?? 0);
        if($idSurat <= 0){
            $sSurat = mysqli_fetch_assoc(query("SELECT id FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PENGUJI TUTUP' ORDER BY id DESC LIMIT 1"));
            if($sSurat) $idSurat = (int)$sSurat['id'];
        }

        if($idSurat > 0){
            $ok = query("UPDATE surat_penunjukkan SET no_surat = '$noSurat', penguji1 = '$p1', penguji2 = '$p2', penguji3 = '$p3', penguji4 = '$p4', penguji5 = '$p5', tanggal = '$tgl' WHERE id = $idSurat");
        } else {
            $idSurat = time();
            $ok = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, penguji1, penguji2, penguji3, penguji4, penguji5, tanggal, keterangan) VALUES ($idSurat, '$noSurat', '$nim', '$p1', '$p2', '$p3', '$p4', '$p5', '$tgl', 'PENGUJI TUTUP')");
            query("UPDATE skripsi SET penunjukkan_tutup = '$idSurat' WHERE nim = '$nim'");
        }

        if($ok){
            echo json_encode(['status' => true, 'pesan' => 'Data surat penguji tutup berhasil diperbarui']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui data penguji tutup']);
        }
        exit;
    }

    if($stepType === 'jadwal_tutup'){
        $noUndangan = bersihkan($_POST['no_undangan_tutup'] ?? '');
        $tanggal    = bersihkan($_POST['tanggal_tutup'] ?? '');
        $jam        = bersihkan($_POST['jam_tutup'] ?? '');
        $ruangan    = bersihkan($_POST['ruangan_tutup'] ?? '');

        if($noUndangan === '' || $tanggal === '' || $jam === '' || $ruangan === ''){
            echo json_encode(['status' => false, 'pesan' => 'Nomor undangan, tanggal, jam, dan ruangan wajib diisi']);
            exit;
        }

        $ok = query("UPDATE skripsi SET no_undangan_tutup = '$noUndangan', tanggal_tutup = '$tanggal', jam_tutup = '$jam', ruangan_tutup = '$ruangan' WHERE nim = '$nim'");
        if($ok){
            echo json_encode(['status' => true, 'pesan' => 'Jadwal ujian sidang tutup berhasil diperbarui']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal memperbarui jadwal ujian tutup']);
        }
        exit;
    }

    echo json_encode(['status' => false, 'pesan' => 'Tipe tindakan koreksi tidak valid']);
    exit;
}

// ================= GANTI / UNGGAH ULANG BERKAS MAHASISWA & OPERATOR =================
if(isset($_POST['ganti-berkas-skripsi'])){
    if (ob_get_length()) ob_clean();

    // Otoritas wewenang unggah ulang hanya level 5, 6, dan 9
    if(!in_array($userLevel, [5, 6, 9], true)){
        echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: Anda tidak memiliki wewenang untuk mengunggah ulang berkas (hanya level 5, 6, dan 9)']);
        exit;
    }

    $nim = bersihkan($_POST['nim'] ?? '');
    $idPersyaratan = (int)($_POST['id_persyaratan'] ?? 0);

    if($nim === '' || $idPersyaratan <= 0 || !isset($_FILES['file']) || $_FILES['file']['error'] !== 0){
        echo json_encode(['status' => false, 'pesan' => 'File atau parameter tidak valid']);
        exit;
    }

    // 1. Cek berkas lama di berkas_skripsi (Tabel UTAMA berkas tersimpan)
    $idFileLama = '';
    $idRowBerkas = 0;
    $cekB = query("SELECT id, file FROM berkas_skripsi WHERE id_persyaratan = $idPersyaratan AND nim = '$nim' LIMIT 1");
    if($cekB && mysqli_num_rows($cekB) > 0){
        $rB = mysqli_fetch_assoc($cekB);
        $idRowBerkas = (int)$rB['id'];
        $idFileLama = trim((string)($rB['file'] ?? ''));
    }

    // Jika belum ada di berkas_skripsi, cek di temp_skripsi
    if($idFileLama === ''){
        $cekT = query("SELECT id, file FROM temp_skripsi WHERE id_persyaratan = $idPersyaratan AND nim = '$nim' LIMIT 1");
        if($cekT && mysqli_num_rows($cekT) > 0){
            $rT = mysqli_fetch_assoc($cekT);
            $idFileLama = trim((string)($rT['file'] ?? ''));
        }
    }

    $idFileBaru = upload_file($_FILES['file'], 'skripsi', $idFileLama);
    if(!$idFileBaru){
        echo json_encode(['status' => false, 'pesan' => 'Gagal mengunggah file baru']);
        exit;
    }

    // 2. Simpan/update di berkas_skripsi
    if($idRowBerkas > 0){
        query("UPDATE berkas_skripsi SET file = '$idFileBaru' WHERE id = $idRowBerkas");
    } else {
        query("INSERT INTO berkas_skripsi (id_persyaratan, nim, file) VALUES ($idPersyaratan, '$nim', '$idFileBaru')");
    }

    // 3. Sinkronkan juga ke temp_skripsi jika ada barisnya
    $cekTemp = query("SELECT id FROM temp_skripsi WHERE id_persyaratan = $idPersyaratan AND nim = '$nim'");
    if($cekTemp && mysqli_num_rows($cekTemp) > 0){
        query("UPDATE temp_skripsi SET file = '$idFileBaru', status = 'diterima', ket = 'Diperbarui oleh Administrator' WHERE id_persyaratan = $idPersyaratan AND nim = '$nim'");
    }

    $urlBaru = url_file($idFileBaru);

    echo json_encode([
        'status'   => true,
        'pesan'    => 'Berkas berhasil diunggah ulang dan tersimpan di berkas_skripsi',
        'url_file' => $urlBaru
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

// ================= HARD RESET DATA SKRIPSI (KHUSUS LEVEL 9 - KEAMANAN BERLAPIS) =================
if(isset($_POST['hapus-data-skripsi'])){
    if (ob_get_length()) ob_clean();

    // Lapis 1: Otoritas hanya Super Admin Level 9
    if($userLevel !== 9){
        echo json_encode([
            'status' => false, 
            'pesan' => 'Akses ditolak: Hanya Super Admin (Level 9) yang memiliki wewenang untuk melakukan Hard Reset skripsi.'
        ]);
        exit;
    }

    $nim = bersihkan($_POST['nim'] ?? '');
    $konfirmasiNim = bersihkan($_POST['konfirmasi_nim'] ?? '');

    // Lapis 2: Verifikasi kecocokan NIM konfirmasi
    if($nim === '' || $konfirmasiNim === '' || $nim !== $konfirmasiNim){
        echo json_encode([
            'status' => false, 
            'pesan' => 'Konfirmasi gagal: NIM konfirmasi tidak sesuai atau parameter tidak lengkap.'
        ]);
        exit;
    }

    // Lapis 3: Cek keberadaan data skripsi mahasiswa di database
    $cekSkripsi = query("SELECT s.*, m.nama AS nama_mhs FROM skripsi s LEFT JOIN mahasiswa m ON s.nim = m.nim WHERE s.nim = '$nim' LIMIT 1");
    if(!$cekSkripsi || mysqli_num_rows($cekSkripsi) === 0){
        echo json_encode([
            'status' => false, 
            'pesan' => 'Data skripsi mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.'
        ]);
        exit;
    }
    $skripsiData = mysqli_fetch_assoc($cekSkripsi);
    $namaMhs = !empty($skripsiData['nama_mhs']) ? $skripsiData['nama_mhs'] : $nim;

    // Kumpulkan seluruh file ID yang terkait dengan skripsi mahasiswa ini (dari berkas_skripsi & temp_skripsi)
    $fileIdsToDelete = [];
    $filePathsToDelete = [];

    // Tentukan folder file fisik root
    $folderUpload = dirname(__DIR__, 5) . '/file/';
    if(!is_dir($folderUpload)){
        $folderUpload = realpath(__DIR__ . '/../../../../..') . '/file/';
    }

    // 1. Dari berkas_skripsi
    $qB = query("SELECT file FROM berkas_skripsi WHERE nim = '$nim'");
    if($qB){
        while($r = mysqli_fetch_assoc($qB)){
            $fid = trim((string)($r['file'] ?? ''));
            if($fid !== '') $fileIdsToDelete[$fid] = true;
        }
    }

    // 2. Dari temp_skripsi
    $qT = query("SELECT file FROM temp_skripsi WHERE nim = '$nim'");
    if($qT){
        while($r = mysqli_fetch_assoc($qT)){
            $fid = trim((string)($r['file'] ?? ''));
            if($fid !== '') $fileIdsToDelete[$fid] = true;
        }
    }

    // Kumpulkan path file fisik sebelum data dihapus
    foreach(array_keys($fileIdsToDelete) as $fileId){
        $fileIdClean = bersihkan($fileId);
        $qF = query("SELECT nama_file FROM files WHERE id = '$fileIdClean' LIMIT 1");
        if($qF && mysqli_num_rows($qF) > 0){
            $rF = mysqli_fetch_assoc($qF);
            $namaFile = trim((string)($rF['nama_file'] ?? ''));
            if($namaFile !== ''){
                $filePathsToDelete[] = $folderUpload . $namaFile;
            }
        }
    }

    try {
        // Matikan sementara pengecekan foreign key agar tidak terjadi constraint violation
        query("SET FOREIGN_KEY_CHECKS = 0");

        // 1. Hapus catatan tabel anak (berkas_skripsi & temp_skripsi yang mereferensi files)
        query("DELETE FROM berkas_skripsi WHERE nim = '$nim'");
        query("DELETE FROM temp_skripsi WHERE nim = '$nim'");

        // 2. Hapus catatan tabel files
        foreach(array_keys($fileIdsToDelete) as $fileId){
            $fileIdClean = bersihkan($fileId);
            query("DELETE FROM files WHERE id = '$fileIdClean'");
        }

        // 3. Hapus riwayat log progres tahapan SOP
        query("DELETE FROM progres_skripsi WHERE mahasiswa = '$nim'");

        // 4. Hapus surat penunjukkan pembimbing & penguji terkait
        query("DELETE FROM surat_penunjukkan WHERE nim = '$nim'");
        $idBimb = (int)($skripsiData['penunjukkan_pembimbing'] ?? 0);
        $idHasil = (int)($skripsiData['penunjukkan_hasil'] ?? 0);
        $idTutup = (int)($skripsiData['penunjukkan_tutup'] ?? 0);
        if($idBimb > 0) query("DELETE FROM surat_penunjukkan WHERE id = $idBimb");
        if($idHasil > 0) query("DELETE FROM surat_penunjukkan WHERE id = $idHasil");
        if($idTutup > 0) query("DELETE FROM surat_penunjukkan WHERE id = $idTutup");

        // 5. Hapus record utama skripsi
        $hapusSkripsi = query("DELETE FROM skripsi WHERE nim = '$nim'");

        // Aktifkan kembali pengecekan foreign key
        query("SET FOREIGN_KEY_CHECKS = 1");

        // Hapus fisik file di folder storage setelah operasi database selesai
        foreach($filePathsToDelete as $filePath){
            if(file_exists($filePath) && is_file($filePath)){
                @unlink($filePath);
            }
        }

        if($hapusSkripsi){
            echo json_encode([
                'status' => true,
                'pesan'  => 'Hard reset berhasil! Seluruh data skripsi, berkas, dan riwayat mahasiswa ' . $namaMhs . ' (' . $nim . ') telah dihapus permanen.'
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'pesan'  => 'Gagal menghapus data utama skripsi dari database.'
            ]);
        }
    } catch(Throwable $e) {
        query("SET FOREIGN_KEY_CHECKS = 1");
        echo json_encode([
            'status' => false,
            'pesan'  => 'Gagal melakukan reset: ' . $e->getMessage()
        ]);
    }
    exit;
}

echo json_encode(['status' => false, 'pesan' => 'Aksi tidak dikenali']);
?>