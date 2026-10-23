<?php
/**
 * PROSES BIMBINGAN SKRIPSI - Konvensi php-native-crud.
 * Menangani AJAX request untuk halaman Bimbingan Skripsi:
 * - ambil-tabel-bimbingan-skripsi : Merender baris tabel mahasiswa bimbingan + progres %
 * - ambil-ringkasan-statistik    : JSON metrik (Total bimbingan, aktif, siap ujian, rata-rata progres)
 * - ambil-arsip-riwayat          : Merender drawer offcanvas timeline riwayat tahapan SOP 1-22
 *
 * Akses dibatasi ketat untuk Level 1 (Dosen), 5 (Kaprodi), 6 (Kajur/Sekjur), 9 (Superadmin).
 */

require_once __DIR__ . '/../../../../../fungsi.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Validasi otorisasi pengguna
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
$userLevel  = (int)($_SESSION['level'] ?? 0);

if (!$sudahLogin || !in_array($userLevel, [1, 5, 6, 9], true)) {
    http_response_code(403);
    echo json_encode(['status' => false, 'pesan' => 'Akses ditolak: Anda tidak memiliki izin untuk mengakses data bimbingan.']);
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

// ================= 1. AMBIL RINGKASAN STATISTIK BIMBINGAN =================
if (isset($_POST['ambil-ringkasan-statistik'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    $prodiFilterStat = bersihkan($_POST['prodi'] ?? '');
    $pembStat        = bersihkan($_POST['pembimbing'] ?? ($userLevel === 9 ? '' : 'saya'));

    // Ambil data mahasiswa bimbingan sesuai filter hak akses
    $colP1 = "COALESCE(sp.pembimbing1, sp_alt.pembimbing1)";
    $colP2 = "COALESCE(sp.pembimbing2, sp_alt.pembimbing2)";
    $where = ["1=1"];

    if ($userLevel === 1 || $pembStat === 'saya') {
        if (!empty($identitasDosen)) {
            $inDosen = "'" . implode("','", array_map('bersihkan', $identitasDosen)) . "'";
            $where[] = "($colP1 IN ($inDosen) OR $colP2 IN ($inDosen))";
        } elseif ($userLevel === 1) {
            echo json_encode(['status' => true, 'total' => 0, 'aktif' => 0, 'siap_ujian' => 0, 'alumni' => 0, 'rata_progres' => 0]);
            exit;
        }
    } elseif ($pembStat !== '') {
        $qTarget = query("SELECT id, nip FROM dosen WHERE id = '$pembStat' OR nip = '$pembStat' LIMIT 1");
        if ($qTarget && ($rT = mysqli_fetch_assoc($qTarget))) {
            $tIds = array_values(array_unique(array_filter([(string)$rT['id'], (string)$rT['nip']])));
        } else {
            $tIds = [$pembStat];
        }
        $inT = "'" . implode("','", array_map('bersihkan', $tIds)) . "'";
        $where[] = "($colP1 IN ($inT) OR $colP2 IN ($inT))";
    }

    if ($userLevel === 5) {
        if ($userProdi !== '') {
            $where[] = "m.prodi = '$userProdi'";
        } else {
            $where[] = "1=0";
        }
    } elseif ($userLevel === 6 || $userLevel === 9) {
        if ($prodiFilterStat !== '') {
            $where[] = "m.prodi = '$prodiFilterStat'";
        }
    }

    $whereSql = implode(" AND ", $where);

    $sql = "SELECT s.nim, s.status,
                   $colP1 AS p1,
                   $colP2 AS p2
            FROM skripsi s
            LEFT JOIN mahasiswa m ON s.nim = m.nim
            LEFT JOIN surat_penunjukkan sp ON s.penunjukkan_pembimbing = sp.id
            LEFT JOIN (
                SELECT nim, MAX(id) AS max_sp_id
                FROM surat_penunjukkan
                WHERE UPPER(keterangan) LIKE '%PEMBIMBING%'
                GROUP BY nim
            ) sp_latest ON s.nim = sp_latest.nim
            LEFT JOIN surat_penunjukkan sp_alt ON sp_latest.max_sp_id = sp_alt.id
            WHERE $whereSql";

    $res = query($sql);

    $totalMhs = 0;
    $aktifMhs = 0;
    $siapUjian = 0;
    $alumniMhs = 0;
    $sumPersen = 0;

    $totalSopCount = 22;
    $qCntSop = query("SELECT COUNT(*) AS c FROM sop_skripsi");
    if ($qCntSop && ($rC = mysqli_fetch_assoc($qCntSop))) {
        $totalSopCount = max(1, (int)$rC['c']);
    }

    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $nimMhs = $row['nim'];
            $stt = strtolower(trim((string)$row['status']));
            $pVal = get_progres_skripsi($nimMhs);
            $pNum = ($pVal === '-' || $pVal === '') ? 1 : (int)$pVal;

            $totalMhs++;

            if ($stt === 'alumni') {
                $alumniMhs++;
                $pct = 100;
            } else {
                if ($stt === 'tidak aktif') {
                    $pct = 0;
                } else {
                    $aktifMhs++;
                    $pct = min(100, max(0, (int)round((($pNum - 1) / $totalSopCount) * 100)));
                    if ($pct === 0 && $stt !== 'tidak aktif') $pct = 5;
                }
            }

            if (in_array($pNum, [10, 18], true) || ($pNum >= 9 && $pNum <= 11) || ($pNum >= 17 && $pNum <= 19)) {
                $siapUjian++;
            }

            $sumPersen += $pct;
        }
    }

    $rataProgres = ($totalMhs > 0) ? round($sumPersen / $totalMhs, 1) : 0;

    echo json_encode([
        'status'       => true,
        'total'        => $totalMhs,
        'aktif'        => $aktifMhs,
        'siap_ujian'   => $siapUjian,
        'alumni'       => $alumniMhs,
        'rata_progres' => $rataProgres
    ]);
    exit;
}

// ================= 2. AMBIL DATA TABEL BIMBINGAN SKRIPSI =================
if (isset($_POST['ambil-tabel-bimbingan-skripsi'])) {
    $cari       = bersihkan($_POST['cari'] ?? '');
    $prodi      = bersihkan($_POST['prodi'] ?? '');
    $pembimbing = bersihkan($_POST['pembimbing'] ?? '');
    $peran      = bersihkan($_POST['peran'] ?? '');
    $status     = bersihkan($_POST['status'] ?? '');
    $progres    = bersihkan($_POST['progres'] ?? '');

    $colP1 = "COALESCE(sp.pembimbing1, sp_alt.pembimbing1)";
    $colP2 = "COALESCE(sp.pembimbing2, sp_alt.pembimbing2)";

    $where = ["1=1"];

    // 1. Otorisasi Prodi
    if ($userLevel === 5) {
        // Kaprodi terkunci sesuai prodinya
        if ($userProdi !== '') {
            $where[] = "m.prodi = '$userProdi'";
        } else {
            $where[] = "1=0";
        }
    } elseif ($userLevel === 6 || $userLevel === 9) {
        if ($prodi !== '') {
            $where[] = "m.prodi = '$prodi'";
        }
    }

    // 2. Filter Dosen & Peran Pembimbing
    $targetDosenIds = [];

    if ($userLevel === 1) {
        // Level 1: Dosen pembimbing selalu difilter ke dirinya sendiri
        $targetDosenIds = $identitasDosen;
    } else {
        // Level 5, 6, 9
        if ($pembimbing === 'saya') {
            $targetDosenIds = $identitasDosen;
        } elseif ($pembimbing !== '') {
            $pembimbingBersih = bersihkan($pembimbing);
            $qTarget = query("SELECT id, nip FROM dosen WHERE id = '$pembimbingBersih' OR nip = '$pembimbingBersih' LIMIT 1");
            if ($qTarget && ($rT = mysqli_fetch_assoc($qTarget))) {
                $targetDosenIds = array_values(array_unique(array_filter([(string)$rT['id'], (string)$rT['nip']])));
            } else {
                $targetDosenIds = [$pembimbingBersih];
            }
        } elseif (in_array($peran, ['pembimbing1', 'pembimbing2'], true)) {
            // Jika dosen tidak dipilih secara spesifik tapi user memilih 'pembimbing1' atau 'pembimbing2',
            // otomatis targetkan ke akun yang sedang login (bimbingan saya)
            if (!empty($identitasDosen)) {
                $targetDosenIds = $identitasDosen;
            }
        }
    }

    if (!empty($targetDosenIds)) {
        $inTarget = "'" . implode("','", array_map('bersihkan', $targetDosenIds)) . "'";
        if ($peran === 'pembimbing1') {
            $where[] = "$colP1 IN ($inTarget)";
        } elseif ($peran === 'pembimbing2') {
            $where[] = "$colP2 IN ($inTarget)";
        } else {
            $where[] = "($colP1 IN ($inTarget) OR $colP2 IN ($inTarget))";
        }
    } elseif ($userLevel === 1) {
        $where[] = "1=0";
    }

    // 3. Filter Pencarian Mahasiswa
    if ($cari !== '') {
        $where[] = "(m.nama LIKE '%$cari%' OR s.nim LIKE '%$cari%' OR s.judul LIKE '%$cari%')";
    }

    // 4. Filter Status Skripsi
    if ($status !== '') {
        $where[] = "s.status = '$status'";
    }

    $whereSql = implode(" AND ", $where);

    // Ambil mapping SOP Skripsi (keterangan & eksekutor)
    $sopMap = [];
    $totalSop = 22;
    $qSop = query("SELECT no, keterangan, eksekutor FROM sop_skripsi ORDER BY CAST(no AS UNSIGNED) ASC");
    if ($qSop) {
        $countSop = 0;
        while ($sp = mysqli_fetch_assoc($qSop)) {
            $countSop++;
            $sopMap[(int)$sp['no']] = [
                'keterangan' => $sp['keterangan'],
                'eksekutor'  => strtolower(trim($sp['eksekutor'] ?? ''))
            ];
        }
        if ($countSop > 0) $totalSop = $countSop;
    }

    // Ambil list nama dosen aktif untuk label pembimbing
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

    // Query data bimbingan mahasiswa
    $sql = "SELECT s.*, m.nama AS nama_mhs, m.prodi, m.angkatan, p.nama AS nama_prodi,
                   COALESCE(sp.id, sp_alt.id) AS id_surat_pembimbing,
                   COALESCE(sp.no_surat, sp_alt.no_surat) AS no_surat_pembimbing,
                   $colP1 AS id_pembimbing1,
                   $colP2 AS id_pembimbing2,
                   COALESCE(sp.tanggal, sp_alt.tanggal) AS tgl_surat_pembimbing,
                   COALESCE(ps.waktu_terbaru, 0) AS waktu_progres
            FROM skripsi s
            LEFT JOIN mahasiswa m ON s.nim = m.nim
            LEFT JOIN prodi p ON m.prodi = p.kode
            LEFT JOIN surat_penunjukkan sp ON s.penunjukkan_pembimbing = sp.id
            LEFT JOIN (
                SELECT nim, MAX(id) AS max_sp_id
                FROM surat_penunjukkan
                WHERE UPPER(keterangan) LIKE '%PEMBIMBING%'
                GROUP BY nim
            ) sp_latest ON s.nim = sp_latest.nim
            LEFT JOIN surat_penunjukkan sp_alt ON sp_latest.max_sp_id = sp_alt.id
            LEFT JOIN (
                SELECT mahasiswa, MAX(waktu) AS waktu_terbaru
                FROM progres_skripsi
                GROUP BY mahasiswa
            ) ps ON s.nim = ps.mahasiswa
            WHERE $whereSql
            ORDER BY waktu_progres DESC, s.nim DESC";

    $ambil = query($sql);
    $dataFiltered = [];

    if ($ambil && mysqli_num_rows($ambil) > 0) {
        while ($d = mysqli_fetch_assoc($ambil)) {
            $progresVal = get_progres_skripsi($d['nim']);
            if ($progresVal === '-' || $progresVal === '') {
                $progresVal = 1;
            }
            $progresNum = (int)$progresVal;

            if ($progres !== '') {
                if ((string)$progresNum !== (string)$progres) {
                    continue;
                }
            }

            $d['progres_num']   = $progresNum;
            $sopItem            = $sopMap[$progresNum] ?? null;
            $d['progres_label'] = $sopItem ? $sopItem['keterangan'] : 'Tahapan Skripsi';

            // Hitung persentase progres
            $sttSkr = strtolower(trim((string)($d['status'] ?? '')));
            if ($sttSkr === 'alumni') {
                $persen = 100;
            } else {
                $persen = min(100, max(0, (int)round((($progresNum - 1) / $totalSop) * 100)));
                if ($persen === 0 && $sttSkr !== 'tidak aktif') {
                    $persen = 5;
                }
            }
            $d['progres_persen'] = $persen;

            // Deteksi peran dosen yang sedang login
            $isP1 = in_array((string)$d['id_pembimbing1'], $identitasDosen, true);
            $isP2 = in_array((string)$d['id_pembimbing2'], $identitasDosen, true);
            $d['is_pembimbing1'] = $isP1;
            $d['is_pembimbing2'] = $isP2;

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
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Judul Skripsi</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Dosen Pembimbing</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider min-w-[200px]">Progres Penyelesaian</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Status</th>
              <th class="py-3.5 px-5 font-semibold uppercase tracking-wider text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
          <?php foreach ($dataFiltered as $data):
              $namaMhs  = !empty($data['nama_mhs']) ? $data['nama_mhs'] : '-';
              $prodiNm  = !empty($data['nama_prodi']) ? $data['nama_prodi'] : (!empty($data['prodi']) ? $data['prodi'] : '');
              $namaP1   = $fnNamaDosen($data['id_pembimbing1']);
              $namaP2   = $fnNamaDosen($data['id_pembimbing2']);
              $persen   = $data['progres_persen'];
              $sopLabel = $data['progres_label'];
              $pNum     = $data['progres_num'];

              // Color mapping progress bar
              if ($persen >= 100) {
                  $barColor = 'bg-emerald-500';
                  $badgePct = 'bg-emerald-50 text-emerald-700 border-emerald-200';
              } elseif ($persen >= 70) {
                  $barColor = 'bg-teal-500';
                  $badgePct = 'bg-teal-50 text-teal-700 border-teal-200';
              } elseif ($persen >= 40) {
                  $barColor = 'bg-orange-500';
                  $badgePct = 'bg-orange-50 text-orange-700 border-orange-200';
              } else {
                  $barColor = 'bg-amber-500';
                  $badgePct = 'bg-amber-50 text-amber-700 border-amber-200';
              }

              $statusRaw = strtolower(trim((string)($data['status'] ?? 'aktif')));
              if ($statusRaw === 'alumni') {
                  $badgeStatus = 'bg-purple-100 text-purple-700';
                  $statusTxt   = 'Alumni';
              } elseif ($statusRaw === 'tidak aktif' || $statusRaw === 'kadaluarsa') {
                  $badgeStatus = 'bg-rose-100 text-rose-700';
                  $statusTxt   = 'Tidak Aktif';
              } elseif ($statusRaw === 'pengajuan') {
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

              <!-- 2. Judul Skripsi -->
              <td class="px-4 py-4 text-slate-700 align-top max-w-xs">
                <p class="text-xs leading-relaxed font-medium line-clamp-3" title="<?= tampil($data['judul'] ?? '-') ?>">
                  <?= !empty($data['judul']) ? tampil($data['judul']) : '<em class="text-slate-400">Belum ada judul tercatat</em>' ?>
                </p>
              </td>

              <!-- 3. Pembimbing & Peran -->
              <td class="px-4 py-4 align-top">
                <div class="space-y-1.5 text-xs">
                  <!-- Pembimbing 1 -->
                  <div class="flex items-start gap-1.5">
                    <span class="inline-flex shrink-0 items-center px-1.5 py-0.5 rounded text-[10px] font-bold <?= $data['is_pembimbing1'] ? 'bg-orange-100 text-orange-800 border border-orange-300 ring-1 ring-orange-400/40' : 'bg-slate-100 text-slate-600' ?>">
                      P1 <?= $data['is_pembimbing1'] ? '(Anda)' : '' ?>
                    </span>
                    <span class="text-slate-800 leading-snug font-medium truncate max-w-[200px]" title="<?= tampil($namaP1) ?>">
                      <?= tampil($namaP1) ?>
                    </span>
                  </div>
                  <!-- Pembimbing 2 -->
                  <div class="flex items-start gap-1.5">
                    <span class="inline-flex shrink-0 items-center px-1.5 py-0.5 rounded text-[10px] font-bold <?= $data['is_pembimbing2'] ? 'bg-orange-100 text-orange-800 border border-orange-300 ring-1 ring-orange-400/40' : 'bg-slate-100 text-slate-600' ?>">
                      P2 <?= $data['is_pembimbing2'] ? '(Anda)' : '' ?>
                    </span>
                    <span class="text-slate-800 leading-snug font-medium truncate max-w-[200px]" title="<?= tampil($namaP2) ?>">
                      <?= tampil($namaP2) ?>
                    </span>
                  </div>
                </div>
              </td>

              <!-- 4. Progressline & Persentase -->
              <td class="px-4 py-4 align-top">
                <div class="w-full">
                  <div class="flex items-center justify-between gap-2 mb-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold border <?= $badgePct ?>">
                      <i class="fas fa-chart-line mr-1 text-[10px]"></i> <?= $persen ?>%
                    </span>
                    <span class="text-[11px] font-semibold text-slate-500">
                      SOP <?= $pNum ?> / <?= $totalSop ?>
                    </span>
                  </div>
                  <!-- Progress Bar -->
                  <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden shadow-inner">
                    <div class="h-full rounded-full <?= $barColor ?> transition-all duration-500" style="width: <?= $persen ?>%"></div>
                  </div>
                  <!-- Label Tahapan Berjalan -->
                  <p class="text-[11px] text-slate-600 mt-1.5 font-medium leading-snug line-clamp-1" title="SOP <?= $pNum ?>: <?= tampil($sopLabel) ?>">
                    <?= tampil($sopLabel) ?>
                  </p>
                </div>
              </td>

              <!-- 5. Status -->
              <td class="px-4 py-4 align-top">
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold <?= $badgeStatus ?>">
                  <?= tampil($statusTxt) ?>
                </span>
              </td>

              <!-- 6. Aksi (Tombol Arsip & Progresline) -->
              <td class="px-5 py-4 align-top text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button type="button" class="btnArsip btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600 hover:scale-105 transition"
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
        <div class="p-12 text-center">
          <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl text-slate-400 mb-3 shadow-inner">
            <i class="fas fa-users-slash"></i>
          </div>
          <h3 class="text-sm font-bold text-slate-700">Tidak ada data bimbingan ditemukan</h3>
          <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
            Tidak ada mahasiswa yang cocok dengan kriteria filter yang Anda tentukan atau belum ada surat penunjukan pembimbing skripsi yang diterbitkan.
          </p>
        </div>
        <?php
    }
    echo ob_get_clean();
    exit;
}

// ================= 3. AMBIL ARSIP & RIWAYAT TAHAPAN (DRAWER PROGRESLINE) =================
if (isset($_POST['ambil-arsip-riwayat'])) {
    if (ob_get_length()) ob_clean();

    $nim = bersihkan($_POST['nim'] ?? '');
    if ($nim === '') {
        echo json_encode(['status' => false, 'pesan' => 'NIM tidak valid']);
        exit;
    }

    try {
        $sql = "SELECT s.*, m.nama AS nama_mhs, m.prodi, m.angkatan, p.nama AS nama_prodi 
                FROM skripsi s 
                LEFT JOIN mahasiswa m ON s.nim = m.nim 
                LEFT JOIN prodi p ON m.prodi = p.kode 
                WHERE s.nim = '$nim' LIMIT 1";
        $qMhs = query($sql);
        if (!$qMhs || mysqli_num_rows($qMhs) === 0) {
            echo json_encode(['status' => false, 'pesan' => 'Data mahasiswa tidak ditemukan']);
            exit;
        }

    $mhs = mysqli_fetch_assoc($qMhs);
    $namaMhs     = !empty($mhs['nama_mhs']) ? $mhs['nama_mhs'] : '-';
    $prodiNm     = !empty($mhs['nama_prodi']) ? $mhs['nama_prodi'] : (!empty($mhs['prodi']) ? $mhs['prodi'] : '-');
    $angkatanTxt = !empty($mhs['angkatan']) ? 'Angkatan ' . $mhs['angkatan'] : '';
    
    $progresVal = get_progres_skripsi($nim);
    if ($progresVal === '-' || $progresVal === '') $progresVal = 1;
    $progresNum = (int)$progresVal;

    // Ambil SEMUA tahapan SOP secara berurutan
    $allSop = [];
    $sopMap = [];
    $qSop = query("SELECT no, keterangan, eksekutor FROM sop_skripsi ORDER BY CAST(no AS UNSIGNED) ASC");
    if ($qSop) {
        while ($sp = mysqli_fetch_assoc($qSop)) {
            $allSop[] = $sp;
            $sopMap[(int)$sp['no']] = $sp['keterangan'];
        }
    }
    $labelProgresAktif = $sopMap[$progresNum] ?? 'Tahapan Skripsi';

    // Ambil riwayat waktu penyelesaian tahapan dari tabel progres_skripsi
    $riwayatWaktuSop = [];
    try {
        $qProg = query("SELECT * FROM progres_skripsi WHERE mahasiswa = '$nim'");
        if ($qProg) {
            while ($pr = mysqli_fetch_assoc($qProg)) {
                $pNo = (int)($pr['proses'] ?? $pr['progres'] ?? 0);
                $tUnix = 0;
                if (!empty($pr['id']) && is_numeric($pr['id']) && (int)$pr['id'] > 1000000000) {
                    $tUnix = (int)$pr['id'];
                } elseif (!empty($pr['waktu']) && is_numeric($pr['waktu']) && (int)$pr['waktu'] > 1000000000) {
                    $tUnix = (int)$pr['waktu'];
                } elseif (!empty($pr['waktu']) && is_numeric($pr['waktu']) && (int)$pr['waktu'] > 0) {
                    $tUnix = (int)$pr['waktu'];
                } elseif (!empty($pr['id']) && is_numeric($pr['id']) && (int)$pr['id'] > 0) {
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

    // Ambil data surat penunjukkan pembimbing
    $suratBimb = null;
    $idBimb = (int)($mhs['penunjukkan_pembimbing'] ?? 0);
    if ($idBimb > 0) {
        $suratBimb = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE id = $idBimb LIMIT 1"));
    }
    if (!$suratBimb) {
        $suratBimb = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PEMBIMBING SKRIPSI' ORDER BY id DESC LIMIT 1"));
    }

    // Ambil data surat penunjukkan penguji hasil
    $suratHasil = null;
    $idHasil = (int)($mhs['penunjukkan_hasil'] ?? 0);
    if ($idHasil > 0) {
        $suratHasil = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE id = $idHasil LIMIT 1"));
    }
    if (!$suratHasil) {
        $suratHasil = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PENGUJI HASIL' ORDER BY id DESC LIMIT 1"));
    }

    // Ambil data surat penunjukkan penguji tutup
    $suratTutup = null;
    $idTutup = (int)($mhs['penunjukkan_tutup'] ?? 0);
    if ($idTutup > 0) {
        $suratTutup = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE id = $idTutup LIMIT 1"));
    }
    if (!$suratTutup) {
        $suratTutup = mysqli_fetch_assoc(query("SELECT * FROM surat_penunjukkan WHERE nim = '$nim' AND keterangan = 'PENGUJI TUTUP' ORDER BY id DESC LIMIT 1"));
    }

    // Konversi nomor surat / nomor undangan ke teks resmi format nomor surat
    $noSuratBimb = !empty($suratBimb['no_surat']) ? (function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($suratBimb['no_surat']) : $suratBimb['no_surat']) : '';
    if ((empty($noSuratBimb) || is_numeric($noSuratBimb)) && !empty($suratBimb['id'])) {
        $altNo = function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($suratBimb['id']) : '';
        if (!empty($altNo) && !is_numeric($altNo)) {
            $noSuratBimb = $altNo;
        }
    }

    $noSuratHasil = !empty($suratHasil['no_surat']) ? (function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($suratHasil['no_surat']) : $suratHasil['no_surat']) : '';
    if ((empty($noSuratHasil) || is_numeric($noSuratHasil)) && !empty($suratHasil['id'])) {
        $altNo = function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($suratHasil['id']) : '';
        if (!empty($altNo) && !is_numeric($altNo)) {
            $noSuratHasil = $altNo;
        }
    }

    $noSuratTutup = !empty($suratTutup['no_surat']) ? (function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($suratTutup['no_surat']) : $suratTutup['no_surat']) : '';
    if ((empty($noSuratTutup) || is_numeric($noSuratTutup)) && !empty($suratTutup['id'])) {
        $altNo = function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($suratTutup['id']) : '';
        if (!empty($altNo) && !is_numeric($altNo)) {
            $noSuratTutup = $altNo;
        }
    }

    $noUndanganHasil = !empty($mhs['no_undangan_hasil']) ? (function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($mhs['no_undangan_hasil']) : $mhs['no_undangan_hasil']) : '';
    $noUndanganTutup = !empty($mhs['no_undangan_tutup']) ? (function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($mhs['no_undangan_tutup']) : $mhs['no_undangan_tutup']) : '';

    // Ambil master seluruh persyaratan berkas skripsi dikelompokkan berdasarkan nomor progres (SOP)
    $masterSyarat = [];
    $qMs = query("SELECT id, nama_berkas, progres FROM persyaratan_berkas_skripsi WHERE status = 'aktif' ORDER BY progres ASC, id ASC");
    if ($qMs) {
        while ($ms = mysqli_fetch_assoc($qMs)) {
            $masterSyarat[(int)$ms['progres']][] = $ms;
        }
    }

    // Ambil berkas mahasiswa dari tabel berkas_skripsi (Tabel UTAMA berkas tersimpan)
    $berkasMap = [];
    $qBs = query("SELECT bs.id AS id_berkas, bs.id_persyaratan, bs.file, f.nama_file 
                  FROM berkas_skripsi bs 
                  LEFT JOIN files f ON bs.file = f.id 
                  WHERE bs.nim = '$nim'");
    if ($qBs) {
        while ($br = mysqli_fetch_assoc($qBs)) {
            $berkasMap[(int)$br['id_persyaratan']] = [
                'id'        => (int)$br['id_berkas'],
                'file'      => $br['file'],
                'nama_file' => $br['nama_file'],
                'status'    => 'tersimpan',
                'ket'       => 'Tersimpan di berkas skripsi'
            ];
        }
    }

    // Ambil berkas dari temp_skripsi sebagai pelengkap jika ada berkas yang sedang dalam verifikasi
    $qTemp = query("SELECT ts.id AS id_temp, ts.id_persyaratan, ts.file, ts.status, ts.ket, f.nama_file 
                    FROM temp_skripsi ts 
                    LEFT JOIN files f ON ts.file = f.id 
                    WHERE ts.nim = '$nim'");
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

    <!-- Timeline Accordion Sequential Berdasarkan SOP Skripsi -->
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
                    <!-- SOP 1: Pengajuan Judul -->
                    <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 text-xs space-y-2">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Judul Skripsi Disetujui:</span>
                        <p class="font-medium text-slate-800 bg-white p-2.5 rounded-lg border border-slate-200 leading-relaxed">
                            <?= !empty($mhs['judul']) ? tampil($mhs['judul']) : '<em class="text-slate-400">Belum ada judul tercatat</em>' ?>
                        </p>
                        <?= $renderListBerkas($masterSyarat[1] ?? [], $berkasMap, $nim, 'Berkas Pendaftaran / Proposal Mahasiswa') ?>
                    </div>

                <?php elseif ($noSop === 3): ?>
                    <!-- SOP 3: Surat Penunjukkan Pembimbing -->
                    <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 text-xs space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Surat Penunjukkan Pembimbing</span>
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
                                <span class="text-slate-500">Dosen Pembimbing 1:</span>
                                <span class="font-bold text-slate-800"><?= tampil($fnNamaDosen($suratBimb['pembimbing1'] ?? '')) ?></span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded bg-white border border-slate-200">
                                <span class="text-slate-500">Dosen Pembimbing 2:</span>
                                <span class="font-bold text-slate-800"><?= tampil($fnNamaDosen($suratBimb['pembimbing2'] ?? '')) ?></span>
                            </div>
                        </div>
                        <div class="pt-1">
                            <a href="<?= base_url() ?>/administrator/dokumen/surat-penunjukkan-pembimbing-skripsi/<?= tampil($nim) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs">
                                <i class="fas fa-file-pdf"></i> Cetak Surat Penunjukkan Pembimbing <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        </div>
                        <?= $renderListBerkas($masterSyarat[3] ?? [], $berkasMap, $nim, 'Berkas Penunjukkan Pembimbing') ?>
                    </div>

                <?php elseif ($noSop === 9): ?>
                    <!-- SOP 9: Tim Dosen Penguji Seminar Hasil -->
                    <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 text-xs space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Surat Penunjukkan Penguji Hasil:</span>
                            <span class="font-mono text-[11px] font-bold text-slate-700 bg-white px-2.5 py-0.5 rounded border border-slate-200"><?= !empty($noSuratHasil) ? 'No: ' . tampil($noSuratHasil) : 'Nomor Surat Tercatat' ?></span>
                        </div>
                        <?php if (!empty($suratHasil['tanggal']) && $suratHasil['tanggal'] !== '0000-00-00'): ?>
                        <div class="text-[11px] text-slate-500">
                            <i class="far fa-calendar-alt text-slate-400 mr-1"></i> Tanggal Surat: <span class="font-semibold text-slate-700"><?= tgl_indo($suratHasil['tanggal']) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="space-y-1">
                            <?php for ($i = 1; $i <= 5; $i++): 
                                $idPenguji = $suratHasil['penguji' . $i] ?? '';
                                if (!empty($idPenguji)):
                            ?>
                            <div class="flex items-center justify-between p-1.5 rounded bg-white border border-slate-200">
                                <span class="text-slate-500">Penguji <?= $i ?>:</span>
                                <span class="font-semibold text-slate-800"><?= tampil($fnNamaDosen($idPenguji)) ?></span>
                            </div>
                            <?php endif; endfor; ?>
                        </div>
                        <div class="pt-1">
                            <a href="<?= base_url() ?>/administrator/dokumen/surat-penunjukkan-penguji/<?= tampil($nim) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs">
                                <i class="fas fa-file-pdf"></i> Cetak Surat Penguji Hasil <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        </div>
                        <?= $renderListBerkas($masterSyarat[9] ?? [], $berkasMap, $nim, 'Berkas Terkait SOP 9') ?>
                    </div>

                <?php elseif ($noSop === 10): ?>
                    <!-- SOP 10: Jadwal Ujian Seminar Hasil -->
                    <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 text-xs space-y-2">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Jadwal Ujian Seminar Hasil:</span>
                        <div class="grid grid-cols-2 gap-2 bg-white p-3 rounded-lg border border-slate-200">
                            <div><span class="text-slate-400 block text-[10px] uppercase font-bold">No. Undangan</span> <span class="font-bold text-slate-800"><?= !empty($noUndanganHasil) ? tampil($noUndanganHasil) : '-' ?></span></div>
                            <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal</span> <span class="font-bold text-slate-800"><?= !empty($mhs['tanggal_hasil']) ? tampil($mhs['tanggal_hasil']) : '-' ?></span></div>
                            <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Jam</span> <span class="font-bold text-slate-800"><?= !empty($mhs['jam_hasil']) ? tampil($mhs['jam_hasil']) : '-' ?></span></div>
                            <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Ruangan</span> <span class="font-bold text-slate-800"><?= !empty($mhs['ruangan_hasil']) ? tampil($mhs['ruangan_hasil']) : '-' ?></span></div>
                        </div>
                        <div class="pt-1">
                            <a href="<?= base_url() ?>/administrator/dokumen/komponen-hasil/<?= tampil($nim) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition shadow-2xs">
                                <i class="fas fa-file-pdf"></i> Cetak Komponen / Berita Acara Hasil <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        </div>
                        <?= $renderListBerkas($masterSyarat[10] ?? [], $berkasMap, $nim, 'Berkas Terkait SOP 10') ?>
                    </div>

                <?php elseif ($noSop === 17): ?>
                    <!-- SOP 17: Tim Penguji Ujian Tutup -->
                    <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 text-xs space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Surat Penunjukkan Penguji Tutup:</span>
                            <span class="font-mono text-[11px] font-bold text-slate-700 bg-white px-2.5 py-0.5 rounded border border-slate-200"><?= !empty($noSuratTutup) ? 'No: ' . tampil($noSuratTutup) : 'Nomor Surat Tercatat' ?></span>
                        </div>
                        <?php if (!empty($suratTutup['tanggal']) && $suratTutup['tanggal'] !== '0000-00-00'): ?>
                        <div class="text-[11px] text-slate-500">
                            <i class="far fa-calendar-alt text-slate-400 mr-1"></i> Tanggal Surat: <span class="font-semibold text-slate-700"><?= tgl_indo($suratTutup['tanggal']) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="space-y-1">
                            <?php for ($i = 1; $i <= 5; $i++): 
                                $idPenguji = $suratTutup['penguji' . $i] ?? '';
                                if (!empty($idPenguji)):
                            ?>
                            <div class="flex items-center justify-between p-1.5 rounded bg-white border border-slate-200">
                                <span class="text-slate-500">Penguji <?= $i ?>:</span>
                                <span class="font-semibold text-slate-800"><?= tampil($fnNamaDosen($idPenguji)) ?></span>
                            </div>
                            <?php endif; endfor; ?>
                        </div>
                        <div class="pt-1">
                            <a href="<?= base_url() ?>/administrator/dokumen/surat-penunjukkan-penguji/<?= tampil($nim) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs">
                                <i class="fas fa-file-pdf"></i> Cetak Surat Penguji Tutup <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        </div>
                        <?= $renderListBerkas($masterSyarat[17] ?? [], $berkasMap, $nim, 'Berkas Terkait SOP 17') ?>
                    </div>

                <?php elseif ($noSop === 18): ?>
                    <!-- SOP 18: Jadwal Sidang Tutup -->
                    <div class="data-section-box p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 text-xs space-y-2">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Jadwal Ujian Sidang Tutup:</span>
                        <div class="grid grid-cols-2 gap-2 bg-white p-3 rounded-lg border border-slate-200">
                            <div><span class="text-slate-400 block text-[10px] uppercase font-bold">No. Undangan</span> <span class="font-bold text-slate-800"><?= !empty($noUndanganTutup) ? tampil($noUndanganTutup) : '-' ?></span></div>
                            <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal</span> <span class="font-bold text-slate-800"><?= !empty($mhs['tanggal_tutup']) ? tampil($mhs['tanggal_tutup']) : '-' ?></span></div>
                            <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Jam</span> <span class="font-bold text-slate-800"><?= !empty($mhs['jam_tutup']) ? tampil($mhs['jam_tutup']) : '-' ?></span></div>
                            <div><span class="text-slate-400 block text-[10px] uppercase font-bold">Ruangan</span> <span class="font-bold text-slate-800"><?= !empty($mhs['ruangan_tutup']) ? tampil($mhs['ruangan_tutup']) : '-' ?></span></div>
                        </div>
                        <?= $renderListBerkas($masterSyarat[18] ?? [], $berkasMap, $nim, 'Berkas Terkait SOP 18') ?>
                    </div>

                <?php else: ?>
                    <?php if (!empty($masterSyarat[$noSop])): ?>
                        <!-- Jika SOP ini memiliki berkas di persyaratan_berkas_skripsi -->
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
        'status' => true,
        'nama'   => $namaMhs,
        'nim'    => $nim,
        'prodi'  => $prodiNm,
        'status_skripsi' => $mhs['status'] ?? 'aktif',
        'html'   => $htmlContent
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

// ================= FUNCTION KHUSUS HALAMAN INI =================
// Diletakkan di bagian paling bawah file sesuai konvensi php-native-crud.
?>
