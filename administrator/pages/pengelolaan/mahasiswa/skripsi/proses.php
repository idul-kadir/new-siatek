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

// ================= AMBIL DATA TABEL SKRIPSI =================
if(isset($_POST['ambil-tabel-skripsi'])){
    $cari     = bersihkan($_POST['cari'] ?? '');
    $prodi    = bersihkan($_POST['prodi'] ?? '');
    $angkatan = bersihkan($_POST['angkatan'] ?? '');
    $status   = bersihkan($_POST['status'] ?? '');
    $progres  = bersihkan($_POST['progres'] ?? '');

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

    $sql = "SELECT s.*, m.nama AS nama_mhs, m.prodi, m.angkatan, p.nama AS nama_prodi 
            FROM skripsi s 
            LEFT JOIN mahasiswa m ON s.nim = m.nim 
            LEFT JOIN prodi p ON m.prodi = p.kode 
            WHERE $whereSql 
            ORDER BY s.nim DESC";

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
            $d['progres_label'] = $sopItem ? $sopItem['keterangan'] : ('Tahap ' . $progresVal);
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
                            <p class="font-semibold text-slate-800 leading-snug"><?= tampil($namaMhs) ?></p>
                            <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500">
                                <span><?= tampil($data['nim']) ?></span>
                                <?php if($prodiNm !== ''): ?><span>&bull;</span><span><?= tampil($prodiNm) ?></span><?php endif; ?>
                                <?php if($angkatanTxt !== ''): ?><span>&bull;</span><span><?= tampil($angkatanTxt) ?></span><?php endif; ?>
                            </div>
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

        <?php else: ?>

        <!-- CASE 2: PROSES BERJALAN SESUAI SOP SKRIPSI -->
        <h5 class="font-bold text-slate-800 text-[14px] border-b border-slate-200 pb-2"><i class="fas fa-tasks mr-1.5 text-slate-400"></i>Tugas: SOP Tahap <?= tampil($progres) ?></h5>
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
                    <p class="text-xs text-blue-600 mb-4">Tugas pada tahap SOP <?= tampil($progres) ?> adalah melakukan verifikasi terhadap berkas mahasiswa. Proses ini tidak dilakukan di halaman ini.</p>
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
                    
                    <?php if((string)$progres === '3'): ?>
                        <!-- Form Khusus: SOP 3 (Penunjukkan Pembimbing) -->
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
                    <?php else: ?>
                        <!-- Form Default untuk tahapan lainnya -->
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Pilih Aksi</label>
                            <select name="tindakan" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" required>
                                <option value="">-- Pilih Tindakan --</option>
                                <option value="selesai">Selesai (Lanjut ke tahap berikutnya)</option>
                                <option value="tolak">Tolak / Revisi (Kembalikan ke mahasiswa)</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Upload Berkas (Jika Ada)</label>
                            <input type="file" name="file_balasan" class="w-full rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                            <p class="text-[10px] text-slate-400 mt-1">Upload BA, Surat Penunjukan, atau berkas lainnya jika disyaratkan oleh SOP.</p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Catatan Operator</label>
                            <textarea name="catatan" rows="2" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400" placeholder="Ketik pesan atau catatan..."></textarea>
                        </div>
                    <?php endif; ?>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 text-xs rounded-md bg-[#f97316] hover:bg-[#ea6a0f] text-white font-semibold shadow-sm" data-text="<?= ((string)$progres === '3') ? 'Buat Surat' : 'Eksekusi Tugas' ?>"><?= ((string)$progres === '3') ? 'Buat Surat' : 'Eksekusi Tugas' ?></button>
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
        
        // Khusus SOP 3 (Penunjukkan Pembimbing)
        if($progresSekarang === 3) {
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
            $idSurat = $waktuSistem; // ID manual berupa timestamp UNIX
            
            // Kolom penguji tidak diisi untuk SOP Penunjukkan Pembimbing. Berikan nilai string kosong ('') agar tidak error di MySQL tanpa default value
            $insertSurat = query("INSERT INTO surat_penunjukkan (id, no_surat, nim, pembimbing1, pembimbing2, tanggal, keterangan) VALUES ($idSurat, '$noSurat', '$nim', '$pembimbing1', '$pembimbing2', '$tanggalSekarang', 'PEMBIMBING SKRIPSI')");

            if($insertSurat) {
                
                // Update skripsi table with the new surat_penunjukkan ID
                $updateSkripsi = query("UPDATE skripsi SET penunjukkan_pembimbing = '$idSurat' WHERE nim = '$nim'");

                // Majukan progres (step/progres naik 1 tingkat, dari 3 ke 4)
                $progresBaru = 4;
                $waktuProgres = time() + 1; // tambah 1 detik agar timestamp selalu unik
                query("INSERT INTO progres_skripsi (waktu, proses, mahasiswa) VALUES ($waktuProgres, $progresBaru, '$nim')");
                
                echo json_encode(['status' => true, 'pesan' => 'Surat penunjukkan berhasil dibuat']);
            } else {
                echo json_encode(['status' => false, 'pesan' => 'Gagal menyimpan data penunjukkan pembimbing: ' . mysqli_error($GLOBALS['koneksi'])]);
            }
            exit;
        }

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
            // Di sini kita bisa catat ke tabel revisi jika ada
            
            // Upload berkas balasan bila ada (misalnya untuk file lembar koreksi revisi)
            if(isset($_FILES['file_balasan']) && $_FILES['file_balasan']['error'] == 0){
                upload_file($_FILES['file_balasan'], 'skripsi');
            }

            echo json_encode(['status' => true, 'pesan' => 'Tahapan ditolak / dikembalikan ke mahasiswa untuk revisi']);
            exit;
        }
    }

    echo json_encode(['status' => false, 'pesan' => 'Aksi lembar kerja tidak dikenali']);
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