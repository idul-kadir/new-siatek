<?php
require_once __DIR__ . "/../../../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

/**
 * PROSES BIMBINGAN KERJA PRAKTEK (KP) - Konvensi php-native-crud.
 * Menangani AJAX request untuk halaman Bimbingan Kerja Praktek:
 * - ambil-ringkasan-statistik : Mengembalikan data 4 kartu metrik summary
 * - ambil-tabel-bimbingan-kp : Merender baris tabel mahasiswa bimbingan + progres %
 * - ambil-arsip-riwayat      : Merender konten drawer offcanvas progresline & berkas SOP KP
 */

// 1. Validasi Autentikasi & Hak Akses (Level 1, 5, 6, 9)
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
$userLevel  = (int)($_SESSION['level'] ?? 0);

if (!$sudahLogin || !in_array($userLevel, [1, 5, 6, 9], true)) {
    if (ob_get_length()) ob_clean();
    echo json_encode(['status' => false, 'pesan' => 'Akses ditolak']);
    exit;
}

// Identitas Dosen Session
$idDosenSession  = bersihkan((string)($_SESSION['id'] ?? ''));
$nipDosenSession = bersihkan((string)($_SESSION['nip'] ?? $_SESSION['user_id'] ?? ''));
$isKaprodi       = ($userLevel === 5);
$userProdi       = $isKaprodi ? (function_exists('get_prodi_user') ? get_prodi_user() : trim((string)($_SESSION['prodi'] ?? ''))) : '';

// Jika idDosenSession kosong, lookup dari tabel dosen berdasarkan NIP/Session
if ($idDosenSession === '' && $nipDosenSession !== '') {
    $qCekDsn = query("SELECT id, nip FROM dosen WHERE nip = '$nipDosenSession' OR id = '$nipDosenSession' LIMIT 1");
    if ($qCekDsn && ($rDsn = mysqli_fetch_assoc($qCekDsn))) {
        $idDosenSession  = (string)$rDsn['id'];
        $nipDosenSession = (string)$rDsn['nip'];
    }
}

// ================= 1. AMBIL RINGKASAN STATISTIK (METRIC SUMMARY CARDS) =================
if (isset($_POST['ambil-ringkasan-statistik'])) {
    if (ob_get_length()) ob_clean();

    $prodiFilter = bersihkan($_POST['prodi'] ?? '');
    $pembFilter  = bersihkan($_POST['pembimbing'] ?? '');

    $where = ["1=1"];

    // 1. Filter Program Studi
    if ($isKaprodi) {
        if ($userProdi !== '') {
            $where[] = "m.prodi = '$userProdi'";
        } else {
            $where[] = "1=0";
        }
    } elseif ($prodiFilter !== '') {
        $where[] = "m.prodi = '$prodiFilter'";
    }

    // 2. Filter Pembimbing
    if ($pembFilter === 'saya') {
        $where[] = "(sp.pembimbing1 = '$idDosenSession' OR sp.pembimbing1 = '$nipDosenSession')";
    } elseif ($pembFilter !== '') {
        $qD = query("SELECT id, nip FROM dosen WHERE id = '$pembFilter' OR nip = '$pembFilter' LIMIT 1");
        if ($qD && ($rD = mysqli_fetch_assoc($qD))) {
            $valId  = $rD['id'];
            $valNip = $rD['nip'];
            $where[] = "(sp.pembimbing1 = '$valId' OR sp.pembimbing1 = '$valNip')";
        } else {
            $where[] = "sp.pembimbing1 = '$pembFilter'";
        }
    } elseif ($userLevel === 1) {
        // Dosen murni wajib dibatasi ke bimbingannya saja
        $where[] = "(sp.pembimbing1 = '$idDosenSession' OR sp.pembimbing1 = '$nipDosenSession')";
    }

    $whereSql = implode(" AND ", $where);

    $sql = "SELECT k.nim, k.status
            FROM kp k
            LEFT JOIN mahasiswa m ON k.nim = m.nim
            LEFT JOIN surat_penunjukkan sp ON (
                (k.surat_penunjukkan_pembimbing IS NOT NULL AND k.surat_penunjukkan_pembimbing != '' AND k.surat_penunjukkan_pembimbing != '0' AND sp.id = k.surat_penunjukkan_pembimbing)
                OR (sp.nim = k.nim AND sp.keterangan = 'PEMBIMBING KP')
            )
            WHERE $whereSql";

    $res = query($sql);
    $total     = 0;
    $aktif     = 0;
    $siapUjian = 0;
    $totalPersen = 0;

    $sopKpList = get_sop_kp_list();
    $totalSop = count($sopKpList);
    if ($totalSop <= 0) $totalSop = 15;

    if ($res && mysqli_num_rows($res) > 0) {
        while ($r = mysqli_fetch_assoc($res)) {
            $total++;
            $nimMhs = $r['nim'];
            $stt = strtolower(trim((string)($r['status'] ?? '')));
            
            // Hitung status aktif
            if ($stt === 'aktif' || $stt === 'bimbingan' || $stt === 'pengajuan' || $stt === '' || $stt === 'draft') {
                $aktif++;
            }

            // Progres mahasiswa saat ini
            $pVal = get_progres_kp($nimMhs);
            if ($pVal === '-' || $pVal === '' || (int)$pVal <= 0) $pVal = 1;
            $pNum = (int)$pVal;

            // Tahap seminar KP (SOP 12, 13, 14 dst atau status seminar)
            if ($pNum >= 12 || $stt === 'seminar' || $stt === 'ujian') {
                $siapUjian++;
            }

            // Persentase kelulusan tahapan SOP
            if ($stt === 'selesai' || $stt === 'alumni') {
                $persen = 100;
            } else {
                $persen = min(100, (int)round(($pNum / $totalSop) * 100));
                if ($persen === 0 && $stt !== 'tidak aktif') {
                    $persen = 7; // baseline tahapan awal
                }
            }
            $totalPersen += $persen;
        }
    }

    $rataProgres = ($total > 0) ? (int)round($totalPersen / $total) : 0;

    echo json_encode([
        'status'       => true,
        'total'        => $total,
        'aktif'        => $aktif,
        'siap_ujian'   => $siapUjian,
        'rata_progres' => $rataProgres
    ]);
    exit;
}

// ================= 2. AMBIL DATA TABEL BIMBINGAN KERJA PRAKTEK =================
if (isset($_POST['ambil-tabel-bimbingan-kp'])) {
    if (ob_get_length()) ob_clean();

    $cari        = bersihkan($_POST['cari'] ?? '');
    $prodiFilter = bersihkan($_POST['prodi'] ?? '');
    $pembFilter  = bersihkan($_POST['pembimbing'] ?? '');
    $status      = bersihkan($_POST['status'] ?? '');
    $progres     = bersihkan($_POST['progres'] ?? '');

    $where = ["1=1"];

    // 1. Filter Program Studi
    if ($isKaprodi) {
        if ($userProdi !== '') {
            $where[] = "m.prodi = '$userProdi'";
        } else {
            $where[] = "1=0";
        }
    } elseif ($prodiFilter !== '') {
        $where[] = "m.prodi = '$prodiFilter'";
    }

    // 2. Filter Dosen Pembimbing
    if ($pembFilter === 'saya') {
        $where[] = "(sp.pembimbing1 = '$idDosenSession' OR sp.pembimbing1 = '$nipDosenSession')";
    } elseif ($pembFilter !== '') {
        $qD = query("SELECT id, nip FROM dosen WHERE id = '$pembFilter' OR nip = '$pembFilter' LIMIT 1");
        if ($qD && ($rD = mysqli_fetch_assoc($qD))) {
            $valId  = $rD['id'];
            $valNip = $rD['nip'];
            $where[] = "(sp.pembimbing1 = '$valId' OR sp.pembimbing1 = '$valNip')";
        } else {
            $where[] = "sp.pembimbing1 = '$pembFilter'";
        }
    } elseif ($userLevel === 1) {
        // Level 1 selalu dibatasi ke bimbingannya sendiri
        $where[] = "(sp.pembimbing1 = '$idDosenSession' OR sp.pembimbing1 = '$nipDosenSession')";
    }

    // 3. Filter Pencarian Teks
    if ($cari !== '') {
        $where[] = "(
            k.nim LIKE '%$cari%' 
            OR m.nama LIKE '%$cari%' 
            OR k.judul LIKE '%$cari%' 
            OR k.tempat LIKE '%$cari%' 
            OR k.nama_instansi LIKE '%$cari%' 
            OR k.instansi LIKE '%$cari%' 
            OR k.tempat_kp LIKE '%$cari%' 
            OR d.nama LIKE '%$cari%'
        )";
    }

    // 4. Filter Status KP
    if ($status !== '') {
        $stLower = strtolower(trim($status));
        if ($stLower === 'aktif') {
            $where[] = "(LOWER(k.status) = 'aktif' OR LOWER(k.status) = 'bimbingan' OR LOWER(k.status) = 'pengajuan' OR k.status IS NULL OR k.status = '' OR LOWER(k.status) = 'draft')";
        } elseif ($stLower === 'selesai' || $stLower === 'alumni') {
            $where[] = "(LOWER(k.status) = 'selesai' OR LOWER(k.status) = 'alumni')";
        } else {
            $where[] = "LOWER(k.status) = '$stLower'";
        }
    }

    // Ambil mapping SOP KP (keterangan & eksekutor)
    $sopMap = [];
    $sopKpList = get_sop_kp_list();
    foreach ($sopKpList as $sp) {
        $sopMap[(int)$sp['no']] = $sp;
    }
    $totalSop = count($sopKpList);
    if ($totalSop <= 0) $totalSop = 15;

    $whereSql = implode(" AND ", $where);

    $sql = "SELECT k.*, m.nama AS nama_mhs, m.prodi, m.angkatan, p.nama AS nama_prodi,
                   sp.id AS id_surat_pemb, sp.no_surat AS no_surat_pemb, sp.pembimbing1 AS id_pembimbing,
                   d.nama AS nama_pembimbing, d.nip AS nip_pembimbing
            FROM kp k
            LEFT JOIN mahasiswa m ON k.nim = m.nim
            LEFT JOIN prodi p ON m.prodi = p.kode
            LEFT JOIN surat_penunjukkan sp ON (
                (k.surat_penunjukkan_pembimbing IS NOT NULL AND k.surat_penunjukkan_pembimbing != '' AND k.surat_penunjukkan_pembimbing != '0' AND sp.id = k.surat_penunjukkan_pembimbing)
                OR (sp.nim = k.nim AND sp.keterangan = 'PEMBIMBING KP')
            )
            LEFT JOIN dosen d ON (sp.pembimbing1 = d.id OR sp.pembimbing1 = d.nip)
            WHERE $whereSql
            ORDER BY k.nim DESC";

    $ambil = query($sql);
    $dataFiltered = [];

    if ($ambil && mysqli_num_rows($ambil) > 0) {
        while ($d = mysqli_fetch_assoc($ambil)) {
            $progresVal = get_progres_kp($d['nim']);
            if ($progresVal === '-' || $progresVal === '' || (int)$progresVal <= 0) {
                $progresVal = 1;
            }
            $pNum = (int)$progresVal;

            // Filter Tahapan SOP
            if ($progres !== '') {
                if ((int)$progres !== $pNum) {
                    continue;
                }
            }

            $sopItem = $sopMap[$pNum] ?? null;
            $d['progres_num']   = $pNum;
            $d['progres_label'] = $sopItem ? $sopItem['keterangan'] : ('Tahap ' . $pNum);

            // Hitung persentase progres
            $sttKp = strtolower(trim((string)($d['status'] ?? '')));
            if ($sttKp === 'selesai' || $sttKp === 'alumni') {
                $persen = 100;
            } else {
                $persen = min(100, (int)round(($pNum / $totalSop) * 100));
                if ($persen === 0 && $sttKp !== 'tidak aktif') {
                    $persen = 7;
                }
            }
            $d['persen'] = $persen;

            // Flag apakah pembimbingnya adalah dosen yang sedang login
            $isSaya = (
                (!empty($d['id_pembimbing']) && ((string)$d['id_pembimbing'] === $idDosenSession || (string)$d['id_pembimbing'] === $nipDosenSession)) ||
                (!empty($d['nip_pembimbing']) && ((string)$d['nip_pembimbing'] === $nipDosenSession || (string)$d['nip_pembimbing'] === $idDosenSession))
            );
            $d['is_saya'] = $isSaya;

            $dataFiltered[] = $d;
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
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Judul &amp; Tempat KP</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Dosen Pembimbing</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Progres Tahapan KP</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Status</th>
              <th class="py-3.5 px-5 font-semibold uppercase tracking-wider text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
          <?php foreach ($dataFiltered as $data):
              $namaMhs  = !empty($data['nama_mhs']) ? $data['nama_mhs'] : '-';
              $prodiNm  = !empty($data['nama_prodi']) ? $data['nama_prodi'] : (!empty($data['prodi']) ? $data['prodi'] : '');
              $persen   = $data['persen'];
              $pNum     = $data['progres_num'];
              $sopLabel = $data['progres_label'];

              // Tempat / Instansi KP
              $tempatKp = !empty($data['nama_instansi']) ? $data['nama_instansi'] : (!empty($data['instansi']) ? $data['instansi'] : (!empty($data['tempat_kp']) ? $data['tempat_kp'] : (!empty($data['tempat']) ? $data['tempat'] : '')));

              // Warna Progress Bar & Badge
              if ($persen >= 100) {
                  $barColor = 'bg-emerald-500';
                  $badgePct = 'bg-emerald-50 text-emerald-700 border-emerald-200';
              } elseif ($persen >= 60) {
                  $barColor = 'bg-orange-500';
                  $badgePct = 'bg-orange-50 text-orange-700 border-orange-200';
              } elseif ($persen >= 30) {
                  $barColor = 'bg-amber-500';
                  $badgePct = 'bg-amber-50 text-amber-700 border-amber-200';
              } else {
                  $barColor = 'bg-slate-400';
                  $badgePct = 'bg-slate-50 text-slate-700 border-slate-200';
              }

              $statusRaw = strtolower(trim((string)($data['status'] ?? 'aktif')));
              if ($statusRaw === 'selesai' || $statusRaw === 'alumni') {
                  $badgeStatus = 'bg-purple-100 text-purple-700';
                  $statusTxt   = 'Selesai';
              } elseif ($statusRaw === 'tidak aktif' || $statusRaw === 'kadaluarsa') {
                  $badgeStatus = 'bg-rose-100 text-rose-700';
                  $statusTxt   = 'Tidak Aktif';
              } elseif ($statusRaw === 'pengajuan' || $statusRaw === 'draft') {
                  $badgeStatus = 'bg-yellow-100 text-yellow-700';
                  $statusTxt   = 'Pengajuan';
              } else {
                  $badgeStatus = 'bg-emerald-100 text-emerald-700';
                  $statusTxt   = 'Bimbingan Aktif';
              }
          ?>
            <tr class="transition hover:bg-orange-50/60 <?= ($no % 2 === 0) ? 'bg-slate-50/50' : 'bg-white' ?>">
              <!-- 1. Identitas Mahasiswa -->
              <td class="py-4 pl-5 pr-4 align-top">
                <div class="flex items-start gap-3">
                  <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">
                    <?= $no ?>
                  </span>
                  <div class="min-w-0">
                    <p class="font-bold text-slate-800 leading-snug"><?= tampil($namaMhs) ?></p>
                    <div class="flex flex-wrap items-center gap-1.5 mt-0.5 text-xs text-slate-500">
                      <span class="font-mono font-semibold text-slate-700"><?= tampil($data['nim']) ?></span>
                      <?php if ($prodiNm !== ''): ?>
                        <span class="text-slate-300">&bull;</span>
                        <span class="text-slate-500"><?= tampil($prodiNm) ?></span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </td>

              <!-- 2. Judul & Tempat KP -->
              <td class="px-4 py-4 text-slate-700 align-top max-w-xs">
                <?php if ($tempatKp !== ''): ?>
                  <p class="text-xs font-bold text-slate-800 leading-snug flex items-center gap-1.5">
                    <i class="fas fa-building text-orange-500 text-[11px]"></i> <?= tampil($tempatKp) ?>
                  </p>
                <?php endif; ?>
                <p class="text-xs leading-relaxed font-medium text-slate-600 mt-1 line-clamp-3" title="<?= tampil($data['judul'] ?? '-') ?>">
                  <?= !empty($data['judul']) ? tampil($data['judul']) : '<em class="text-slate-400">Belum ada judul tercatat</em>' ?>
                </p>
              </td>

              <!-- 3. Dosen Pembimbing KP -->
              <td class="px-4 py-4 align-top">
                <div class="space-y-1 text-xs">
                  <?php if (!empty($data['nama_pembimbing'])): ?>
                    <div class="flex items-start gap-1.5">
                      <span class="inline-flex shrink-0 items-center px-1.5 py-0.5 rounded text-[10px] font-bold <?= $data['is_saya'] ? 'bg-orange-100 text-orange-800 border border-orange-300 ring-1 ring-orange-400/40' : 'bg-slate-100 text-slate-600' ?>">
                        <?= $data['is_saya'] ? '★ Pembimbing Saya' : 'Pembimbing' ?>
                      </span>
                      <span class="font-semibold text-slate-800 leading-tight">
                        <?= tampil($data['nama_pembimbing']) ?>
                      </span>
                    </div>
                  <?php else: ?>
                    <span class="text-xs text-slate-400 italic">Belum ada pembimbing</span>
                  <?php endif; ?>
                </div>
              </td>

              <!-- 4. Progresline SOP & Persentase -->
              <td class="px-4 py-4 align-top min-w-[200px]">
                <div class="space-y-1.5">
                  <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-700">SOP <?= $pNum ?>: <?= tampil($sopLabel) ?></span>
                    <span class="inline-block px-1.5 py-0.2 rounded text-[11px] font-bold border <?= $badgePct ?>">
                      <?= $persen ?>%
                    </span>
                  </div>
                  <!-- Progress Bar -->
                  <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="<?= $barColor ?> h-2 rounded-full transition-all duration-300" style="width: <?= $persen ?>%"></div>
                  </div>
                  <p class="text-[10px] text-slate-400 leading-tight">
                    <?= $persen >= 100 ? 'Seluruh tahapan SOP KP selesai' : 'Tahap berjalan dari ' . $totalSop . ' SOP' ?>
                  </p>
                </div>
              </td>

              <!-- 5. Status KP -->
              <td class="px-4 py-4 align-top">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold <?= $badgeStatus ?>">
                  <?= $statusTxt ?>
                </span>
              </td>

              <!-- 6. Aksi -->
              <td class="py-4 pl-4 pr-5 text-right align-top">
                <div class="flex items-center justify-end gap-1.5">
                  <button type="button" class="btnArsip btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600 transition"
                    data-nim="<?= tampil($data['nim']) ?>"
                    data-nama="<?= tampil($namaMhs) ?>"
                    data-prodi="<?= tampil($prodiNm) ?>">
                    <i class="fas fa-box-archive text-xs"></i>
                    <span class="tip">Arsip &amp; Progresline</span>
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
        <div class="py-16 text-center text-slate-400">
          <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl text-slate-400 mb-3">
            <i class="fas fa-folder-open"></i>
          </div>
          <h3 class="text-sm font-bold text-slate-700">Tidak Ada Data Bimbingan Kerja Praktek</h3>
          <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
            Tidak ada mahasiswa yang cocok dengan kriteria filter yang Anda tentukan atau belum ada surat penunjukan pembimbing KP yang diterbitkan.
          </p>
        </div>
        <?php
    }
    echo ob_get_clean();
    exit;
}

// ================= 3. AMBIL ARSIP & RIWAYAT TAHAPAN (DRAWER PROGRESLINE KP) =================
if (isset($_POST['ambil-arsip-riwayat'])) {
    if (ob_get_length()) ob_clean();

    $nim = bersihkan($_POST['nim'] ?? '');
    if ($nim === '') {
        echo json_encode(['status' => false, 'pesan' => 'NIM tidak valid']);
        exit;
    }

    try {
        $sql = "SELECT k.*, m.nama AS nama_mhs, m.prodi, m.angkatan, p.nama AS nama_prodi 
                FROM kp k 
                LEFT JOIN mahasiswa m ON k.nim = m.nim 
                LEFT JOIN prodi p ON m.prodi = p.kode 
                WHERE k.nim = '$nim' LIMIT 1";
        $qMhs = query($sql);
        if (!$qMhs || mysqli_num_rows($qMhs) === 0) {
            echo json_encode(['status' => false, 'pesan' => 'Data mahasiswa tidak ditemukan']);
            exit;
        }

        $mhs = mysqli_fetch_assoc($qMhs);
        $namaMhs     = !empty($mhs['nama_mhs']) ? $mhs['nama_mhs'] : '-';
        $prodiNm     = !empty($mhs['nama_prodi']) ? $mhs['nama_prodi'] : (!empty($mhs['prodi']) ? $mhs['prodi'] : '-');
        $angkatanTxt = !empty($mhs['angkatan']) ? 'Angkatan ' . $mhs['angkatan'] : '';
        
        $progresVal = get_progres_kp($nim);
        if ($progresVal === '-' || $progresVal === '' || (int)$progresVal <= 0) $progresVal = 1;
        $progresNum = (int)$progresVal;

        // Ambil SEMUA tahapan SOP KP secara berurutan
        $sopKpList = get_sop_kp_list();
        $allSop = [];
        $sopMap = [];
        foreach ($sopKpList as $sp) {
            $allSop[] = $sp;
            $sopMap[(int)$sp['no']] = $sp['keterangan'];
        }
        $labelProgresAktif = $sopMap[$progresNum] ?? 'Tahapan Kerja Praktek';

        // Ambil riwayat waktu penyelesaian tahapan dari tabel progres_kp
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

        $getWaktuSelesaiSop = function($noSop, $riwayatWaktuSop, $progresNum) {
            if (isset($riwayatWaktuSop[$noSop + 1]) && $riwayatWaktuSop[$noSop + 1] > 0) {
                return $riwayatWaktuSop[$noSop + 1];
            }
            if (isset($riwayatWaktuSop[$noSop]) && $riwayatWaktuSop[$noSop] > 0) {
                return $riwayatWaktuSop[$noSop];
            }
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

        // Mapping nama dosen
        $dosenNameMap = [];
        $qDsn = query("SELECT id, nip, nama FROM dosen WHERE LOWER(posisi) = 'dosen' AND LOWER(status) = 'aktif' ORDER BY nama ASC");
        if ($qDsn) {
            while ($ds = mysqli_fetch_assoc($qDsn)) {
                $dosenNameMap[(string)$ds['id']]  = $ds['nama'];
                $dosenNameMap[(string)$ds['nip']] = $ds['nama'];
            }
        }
        $fnNamaDosen = function($idOrNip) use ($dosenNameMap) {
            if (empty($idOrNip)) return '-';
            $key = (string)$idOrNip;
            return $dosenNameMap[$key] ?? $idOrNip;
        };

        // Ambil data surat penunjukkan pembimbing KP (SOP 6)
        $suratBimb = null;
        $idBimb = (int)($mhs['surat_penunjukkan_pembimbing'] ?? 0);
        if ($idBimb > 0) {
            $suratBimb = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE id = $idBimb LIMIT 1"));
        }
        if (!$suratBimb) {
            $suratBimb = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING KP' ORDER BY id DESC LIMIT 1"));
        }

        // Ambil data surat penunjukkan penguji seminar KP (SOP 12)
        $suratPeng = null;
        $idPeng = (int)($mhs['surat_penunjukkan_penguji'] ?? 0);
        if ($idPeng > 0) {
            $suratPeng = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE id = $idPeng LIMIT 1"));
        }
        if (!$suratPeng) {
            $suratPeng = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE nim = '$nim' AND (keterangan = 'PENGUJI KP' OR keterangan LIKE '%PENGUJI%KP%' OR keterangan LIKE '%SEMINAR%KP%') ORDER BY id DESC LIMIT 1"));
        }

        // Konversi nomor surat / nomor undangan ke teks resmi format nomor surat
        $noSuratBimb = !empty($suratBimb['no_surat']) ? (function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($suratBimb['no_surat']) : $suratBimb['no_surat']) : '';
        if ((empty($noSuratBimb) || is_numeric($noSuratBimb)) && !empty($suratBimb['id'])) {
            $altNo = function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($suratBimb['id']) : '';
            if (!empty($altNo) && !is_numeric($altNo)) {
                $noSuratBimb = $altNo;
            }
        }

        $noSuratPeng = !empty($suratPeng['no_surat']) ? (function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($suratPeng['no_surat']) : $suratPeng['no_surat']) : '';
        if ((empty($noSuratPeng) || is_numeric($noSuratPeng)) && !empty($suratPeng['id'])) {
            $altNo = function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($suratPeng['id']) : '';
            if (!empty($altNo) && !is_numeric($altNo)) {
                $noSuratPeng = $altNo;
            }
        }

        $noUndanganSeminar = !empty($mhs['no_undangan']) ? (function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($mhs['no_undangan']) : $mhs['no_undangan']) : '';

        // Surat Penarikan KP (SOP 7)
        $qNomorPenarikan = query("SELECT nomor FROM nomor_surat WHERE keterangan LIKE '%Penarikan KP%' AND keterangan LIKE '%$nim%' ORDER BY id DESC LIMIT 1");
        $noSuratPenarikan = ($qNomorPenarikan && $rNP = mysqli_fetch_assoc($qNomorPenarikan)) ? $rNP['nomor'] : '-';

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

        // Ambil berkas dari temp_kp sebagai pelengkap jika ada berkas yang sedang dalam verifikasi
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

        // Helper render daftar berkas (mode lihat / preview bagi dosen)
        $renderListBerkas = function($listSyarat, $berkasMap, $nim, $labelSection = 'Berkas Persyaratan') {
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
                    <div class="p-2.5 rounded-lg border border-slate-200 bg-white hover:border-slate-300 transition shadow-2xs">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-slate-800 truncate" title="<?= tampil($syarat['nama_berkas']) ?>">
                                    <i class="fas fa-file-pdf mr-1.5 <?= $hasFile ? 'text-rose-500' : 'text-slate-300' ?>"></i>
                                    <?= tampil($syarat['nama_berkas']) ?>
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold <?= $statusBadgeClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                    <?php if (!empty($itemUploaded['ket'])): ?>
                                        <span class="text-[10px] text-slate-500 italic truncate max-w-[200px]" title="<?= tampil($itemUploaded['ket']) ?>">
                                            "<?= tampil($itemUploaded['ket']) ?>"
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($hasFile): ?>
                            <div class="shrink-0">
                                <a href="<?= $fileUrl ?>" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                    <i class="fas fa-eye text-orange-500"></i> Lihat ↗
                                </a>
                            </div>
                            <?php endif; ?>
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

        <!-- Toolbar Kontrol Accordion -->
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Alur SOP Kerja Praktek</span>
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

        <!-- Timeline Accordion Sequential Berdasarkan SOP KP -->
        <div class="space-y-3" id="accordionArsip">
            <?php foreach ($allSop as $sopItem):
                $noSop = (int)$sopItem['no'];
                $ketSop = $sopItem['keterangan'];
                $eksekutorSop = $sopItem['eksekutor'] ?? '';

                $isCompleted = ($noSop < $progresNum);
                $isCurrent   = ($noSop === $progresNum);
                $isFuture    = ($noSop > $progresNum);
                $canExpand   = $isCompleted;

                $waktuSelesaiUnix = $isCompleted ? $getWaktuSelesaiSop($noSop, $riwayatWaktuSop, $progresNum) : 0;
                $waktuSelesaiFormatted = ($waktuSelesaiUnix > 0) ? $formatWaktuSop($waktuSelesaiUnix) : '-';

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
            <div class="border border-slate-200 rounded-xl bg-white shadow-xs overflow-hidden transition-all duration-200 <?= $canExpand ? 'card-completed' : 'card-locked' ?>" data-card-stage="<?= $noSop ?>">
                <?php if ($canExpand): ?>
                <div class="flex items-center justify-between p-3.5 cursor-pointer bg-slate-50 hover:bg-slate-100/80 transition btn-toggle-accordion">
                <?php else: ?>
                <div class="flex items-center justify-between p-3.5 bg-slate-50/50 cursor-default select-none opacity-85" title="Tahapan sedang berlangsung atau belum ditempuh">
                <?php endif; ?>
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg <?= $iconCircle ?> text-xs font-bold shadow-xs">
                            <?php if ($isCompleted): ?>
                                <i class="fas fa-check"></i>
                            <?php elseif ($isCurrent): ?>
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
                            <span title="Tahapan belum selesai (terkunci)">
                                <i class="fas fa-lock text-[11px] text-slate-300"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($canExpand): ?>
                <div class="acc-body p-4 border-t border-slate-100 hidden" style="display: none;">
                    
                    <?php if ($noSop === 1): ?>
                        <!-- SOP 1: Pengajuan Judul & Tempat/Instansi KP -->
                        <?php
                        $tempatKpDetail = !empty($mhs['nama_instansi']) ? $mhs['nama_instansi'] : (!empty($mhs['instansi']) ? $mhs['instansi'] : (!empty($mhs['tempat_kp']) ? $mhs['tempat_kp'] : (!empty($mhs['tempat']) ? $mhs['tempat'] : '-')));
                        ?>
                        <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 text-xs space-y-2.5">
                            <div>
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Tempat / Instansi KP:</span>
                                <p class="font-bold text-slate-800 bg-white p-2.5 rounded-lg border border-slate-200 leading-relaxed flex items-center gap-1.5">
                                    <i class="fas fa-building text-orange-500"></i> <?= tampil($tempatKpDetail) ?>
                                </p>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Judul Kerja Praktek:</span>
                                <p class="font-medium text-slate-800 bg-white p-2.5 rounded-lg border border-slate-200 leading-relaxed">
                                    <?= !empty($mhs['judul']) ? tampil($mhs['judul']) : '<em class="text-slate-400">Belum ada judul tercatat</em>' ?>
                                </p>
                            </div>
                            <?= $renderListBerkas($masterSyarat[1] ?? [], $berkasMap, $nim, 'Berkas Pendaftaran Kerja Praktek (SOP 1)') ?>
                        </div>

                    <?php elseif ($noSop === 6): ?>
                        <!-- SOP 6: Surat Penunjukkan Pembimbing KP -->
                        <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 text-xs space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Surat Penunjukkan Pembimbing KP</span>
                                <span class="font-mono font-bold text-slate-700 bg-white px-2.5 py-0.5 rounded border border-slate-200">
                                    <?= !empty($noSuratBimb) ? 'No: ' . tampil($noSuratBimb) : 'Nomor Surat Tercatat' ?>
                                </span>
                            </div>
                            <?php if (!empty($suratBimb['tanggal']) && $suratBimb['tanggal'] !== '0000-00-00'): ?>
                            <div class="text-[11px] text-slate-500">
                                <i class="far fa-calendar-alt text-slate-400 mr-1"></i> Tanggal Surat: <span class="font-semibold text-slate-700"><?= tgl_indo($suratBimb['tanggal']) ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="space-y-1.5 pt-1">
                                <div class="flex items-center justify-between p-2 rounded bg-white border border-slate-200">
                                    <span class="text-slate-500">Dosen Pembimbing KP:</span>
                                    <span class="font-bold text-slate-800"><?= tampil($fnNamaDosen($suratBimb['pembimbing1'] ?? '')) ?></span>
                                </div>
                            </div>
                            <div class="pt-1">
                                <a href="<?= base_url() ?>/administrator/dokumen/surat-penunjukkan-pembimbing-kp/<?= tampil($nim) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs">
                                    <i class="fas fa-file-pdf"></i> Cetak Surat Penunjukkan Pembimbing KP <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>
                            </div>
                            <?= $renderListBerkas($masterSyarat[6] ?? [], $berkasMap, $nim, 'Berkas Penunjukkan Pembimbing KP (SOP 6)') ?>
                        </div>

                    <?php elseif ($noSop === 7): ?>
                        <!-- SOP 7: Surat Pengantar Penarikan KP -->
                        <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 text-xs space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Surat Pengantar Penarikan KP</span>
                                <span class="font-mono text-[11px] font-bold text-slate-700 bg-white px-2 py-0.5 rounded border border-slate-200">
                                    <?= !empty($noSuratPenarikan) ? 'No: ' . tampil($noSuratPenarikan) : 'Nomor Surat Tercatat' ?>
                                </span>
                            </div>
                            <div class="pt-1">
                                <a href="<?= base_url() ?>/administrator/dokumen/surat-penarikan-kp/<?= tampil($nim) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition shadow-2xs">
                                    <i class="fas fa-file-pdf"></i> Cetak Surat Pengantar Penarikan KP <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>
                            </div>
                            <?= $renderListBerkas($masterSyarat[7] ?? [], $berkasMap, $nim, 'Berkas Terkait Penarikan KP (SOP 7)') ?>
                        </div>

                    <?php elseif ($noSop === 12): ?>
                        <!-- SOP 12: Surat Penunjukkan Penguji Seminar KP -->
                        <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 text-xs space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Surat Penunjukkan Penguji Seminar KP:</span>
                                <span class="font-mono text-[11px] font-bold text-slate-700 bg-white px-2.5 py-0.5 rounded border border-slate-200">
                                    <?= !empty($noSuratPeng) ? 'No: ' . tampil($noSuratPeng) : 'Nomor Surat Tercatat' ?>
                                </span>
                            </div>
                            <?php if (!empty($suratPeng['tanggal']) && $suratPeng['tanggal'] !== '0000-00-00'): ?>
                            <div class="text-[11px] text-slate-500">
                                <i class="far fa-calendar-alt text-slate-400 mr-1"></i> Tanggal Surat: <span class="font-semibold text-slate-700"><?= tgl_indo($suratPeng['tanggal']) ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="space-y-1">
                                <?php for ($i = 1; $i <= 3; $i++): 
                                    $idPenguji = $suratPeng['penguji' . $i] ?? '';
                                    if (!empty($idPenguji)):
                                ?>
                                <div class="flex items-center justify-between p-1.5 rounded bg-white border border-slate-200">
                                    <span class="text-slate-500">Penguji <?= $i ?>:</span>
                                    <span class="font-semibold text-slate-800"><?= tampil($fnNamaDosen($idPenguji)) ?></span>
                                </div>
                                <?php endif; endfor; ?>
                            </div>
                            <div class="pt-1">
                                <a href="<?= base_url() ?>/administrator/dokumen/surat-penunjukkan-penguji-kp/<?= tampil($nim) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs">
                                    <i class="fas fa-file-pdf"></i> Cetak Surat Penguji Seminar KP <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>
                            </div>
                            <?= $renderListBerkas($masterSyarat[12] ?? [], $berkasMap, $nim, 'Berkas Terkait Penguji Seminar KP (SOP 12)') ?>
                        </div>

                    <?php elseif ($noSop === 13): ?>
                        <!-- SOP 13: Jadwal Seminar Kerja Praktek -->
                        <?php
                        $tglSem   = !empty($mhs['tanggal_seminar']) ? $mhs['tanggal_seminar'] : ($mhs['tanggal'] ?? '');
                        $jamSem   = !empty($mhs['jam_seminar']) ? $mhs['jam_seminar'] : ($mhs['jam'] ?? '');
                        $ruangSem = $mhs['ruangan'] ?? '-';
                        ?>
                        <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 text-xs space-y-2">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Jadwal Seminar Kerja Praktek:</span>
                            <div class="grid grid-cols-2 gap-2 bg-white p-3 rounded-lg border border-slate-200">
                                <div><span class="text-slate-400 block text-[10px] uppercase font-bold">No. Undangan</span> <span class="font-bold text-slate-800"><?= !empty($noUndanganSeminar) ? tampil($noUndanganSeminar) : '-' ?></span></div>
                                <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal</span> <span class="font-bold text-slate-800"><?= !empty($tglSem) ? tgl_indo($tglSem) : '-' ?></span></div>
                                <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Jam</span> <span class="font-bold text-slate-800"><?= !empty($jamSem) ? tampil($jamSem) : '-' ?></span></div>
                                <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Ruangan</span> <span class="font-bold text-slate-800"><?= !empty($ruangSem) ? tampil($ruangSem) : '-' ?></span></div>
                            </div>
                            <?= $renderListBerkas($masterSyarat[13] ?? [], $berkasMap, $nim, 'Berkas Terkait Jadwal Seminar (SOP 13)') ?>
                        </div>

                    <?php else: ?>
                        <?php if (!empty($masterSyarat[$noSop])): ?>
                            <!-- Jika SOP ini memiliki berkas di persyaratan_berkas_kp -->
                            <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 text-xs">
                                <?= $renderListBerkas($masterSyarat[$noSop], $berkasMap, $nim, 'Berkas Persyaratan (SOP ' . $noSop . ')') ?>
                            </div>
                        <?php else: ?>
                            <!-- Jika SOP bersifat prosedural / verifikasi tanpa berkas langsung -->
                            <div class="py-2.5 px-3.5 rounded-lg bg-slate-50 border border-slate-200/80 text-xs text-slate-600">
                                <p class="leading-relaxed">
                                    <i class="fas fa-info-circle text-orange-500 mr-1.5"></i>
                                    Tahapan ini merupakan proses verifikasi/persetujuan oleh <span class="font-semibold text-slate-800"><?= tampil($eksekutorSop !== '' ? $eksekutorSop : 'Pengelola') ?></span>.
                                    <?php if ($isCompleted): ?>
                                        <span class="text-emerald-600 font-semibold block mt-1"><i class="fas fa-check-circle mr-1"></i>Tahapan ini telah selesai ditempuh.</span>
                                    <?php elseif ($isCurrent): ?>
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
        $htmlContent = ob_get_clean();

        echo json_encode([
            'status'    => true,
            'nama'      => $namaMhs,
            'nim'       => $nim,
            'prodi'     => $prodiNm,
            'status_kp' => $mhs['status'] ?? 'aktif',
            'html'      => $htmlContent
        ]);
        exit;
    } catch (\Throwable $e) {
        if (ob_get_length()) ob_clean();
        echo json_encode([
            'status' => false,
            'pesan'  => 'Terjadi kesalahan sistem: ' . $e->getMessage()
        ]);
        exit;
    }
}
?>
