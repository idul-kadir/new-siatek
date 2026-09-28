<?php
/**
 * Halaman: Surat Penunjukkan (Jurusan)
 * Daftar surat penunjukkan dosen (pembimbing/penguji) skripsi & KP tingkat jurusan.
 * Data diambil dari tabel surat_penunjukkan DB "siatek" (nama mahasiswa & dosen
 * dari tabel mahasiswa/dosen).
 */
require_once __DIR__ . '/../../../fungsi.php';

// Mapping keterangan DB -> data-tab-jenis
$KET_MAP = [
    'PEMBIMBING SKRIPSI' => 'pemskr',
    'PEMBIMBING KP' => 'pemkp',
    'PENGUJI HASIL' => 'hasil',
    'PENGUJI KP' => 'pengkp',
    'PENGUJI TUTUP' => 'tutup',
];

// Ambil semua surat + nama mahasiswa
$qSurat = query("SELECT sp.id, sp.no_surat, TRIM(sp.nim) AS nim, sp.keterangan, sp.tanggal, m.nama FROM surat_penunjukkan sp LEFT JOIN mahasiswa m ON TRIM(sp.nim) = TRIM(m.nim) ORDER BY sp.id DESC");
$surat = [];
while ($r = mysqli_fetch_assoc($qSurat)) {
    $surat[] = [
        'id'          => $r['id'],
        'no_surat'    => $r['no_surat'],
        'nim'         => trim($r['nim']),
        'nama'        => $r['nama'] ?: trim($r['nim']),
        'ket'         => $r['keterangan'],
        'tahun'       => (int)date('Y', strtotime($r['tanggal'])),
        'tanggal_fmt' => date('d M Y', strtotime($r['tanggal'])),
        'url'         => 'surat-penunjukkan-' . ($KET_MAP[$r['keterangan']] ?? 'pemskr') . '/' . trim($r['nim']),
    ];
}
unset($qSurat);

// Ambil tahun unik dari DB untuk dropdown filter
$qTahun = query("SELECT DISTINCT YEAR(tanggal) AS thn FROM surat_penunjukkan ORDER BY thn DESC");
$daftarTahun = [];
while ($r = mysqli_fetch_assoc($qTahun)) {
    $daftarTahun[] = $r['thn'];
}
unset($qTahun);

// Ambil peta NIM ke nama mahasiswa untuk tooltip JavaScript (global scope)
$qMhsGbl = query("SELECT nim, nama FROM mahasiswa");
$nimMap = [];
while ($m = mysqli_fetch_assoc($qMhsGbl)) { $nimMap[$m['nim']] = $m['nama']; }
unset($qMhsGbl);

// Hitung statistik beban dosen per tahun
function buildStatistikDosen($koneksi, $tahun): array {
    $q = query("SELECT sp.keterangan, sp.tanggal, sp.pembimbing1, sp.pembimbing2, sp.penguji1, sp.penguji2, sp.penguji3, sp.penguji4, sp.penguji5, sp.nim FROM surat_penunjukkan sp WHERE YEAR(sp.tanggal)=" . (int)$tahun);
    $stats = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $ket = $r['keterangan'];
        $nim = $r['nim'] ?? '';
        if ($ket === 'PEMBIMBING SKRIPSI') {
            if ($r['pembimbing1'] !== '') {
                $stats[$r['pembimbing1']]['pemskr']['p1'] = ($stats[$r['pembimbing1']]['pemskr']['p1'] ?? 0) + 1;
                if ($nim !== '') $stats[$r['pembimbing1']]['pemskr']['p1_list'][] = $nim;
            }
            if ($r['pembimbing2'] !== '') {
                $stats[$r['pembimbing2']]['pemskr']['p2'] = ($stats[$r['pembimbing2']]['pemskr']['p2'] ?? 0) + 1;
                if ($nim !== '') $stats[$r['pembimbing2']]['pemskr']['p2_list'][] = $nim;
            }
        } elseif ($ket === 'PENGUJI HASIL') {
            foreach (['penguji1','penguji2','penguji3','penguji4','penguji5'] as $k) {
                if ($r[$k] !== '') {
                    $stats[$r[$k]]['hasil'] = ($stats[$r[$k]]['hasil'] ?? 0) + 1;
                    if ($nim !== '') $stats[$r[$k]]['hasil_list'][] = $nim;
                }
            }
        } elseif ($ket === 'PENGUJI TUTUP') {
            foreach (['penguji1','penguji2','penguji3','penguji4','penguji5'] as $k) {
                if ($r[$k] !== '') {
                    $stats[$r[$k]]['tutup'] = ($stats[$r[$k]]['tutup'] ?? 0) + 1;
                    if ($nim !== '') $stats[$r[$k]]['tutup_list'][] = $nim;
                }
            }
        } elseif ($ket === 'PEMBIMBING KP') {
            if ($r['pembimbing1'] !== '') {
                $stats[$r['pembimbing1']]['pemkp']['p1'] = ($stats[$r['pembimbing1']]['pemkp']['p1'] ?? 0) + 1;
                if ($nim !== '') $stats[$r['pembimbing1']]['pemkp']['p1_list'][] = $nim;
            }
            if ($r['pembimbing2'] !== '') {
                $stats[$r['pembimbing2']]['pemkp']['p2'] = ($stats[$r['pembimbing2']]['pemkp']['p2'] ?? 0) + 1;
                if ($nim !== '') $stats[$r['pembimbing2']]['pemkp']['p2_list'][] = $nim;
            }
        } elseif ($ket === 'PENGUJI KP') {
            foreach (['penguji1','penguji2','penguji3','penguji4','penguji5'] as $k) {
                if ($r[$k] !== '') {
                    $stats[$r[$k]]['pengkp'] = ($stats[$r[$k]]['pengkp'] ?? 0) + 1;
                    if ($nim !== '') $stats[$r[$k]]['pengkp_list'][] = $nim;
                }
            }
        }
    }
    // Map id/nip dosen ke nama
    $qDosen = query("SELECT id, nip, nama FROM dosen");
    $dosenMap = [];
    while ($d = mysqli_fetch_assoc($qDosen)) { $dosenMap[$d['id']] = $d['nama']; $dosenMap[$d['nip']] = $d['nama']; }
    // Also check nidn
    $qNidn = query("SELECT id, nidn FROM dosen");
    while ($d = mysqli_fetch_assoc($qNidn)) { if (!isset($dosenMap[$d['id']])) $dosenMap[$d['nidn']] = $d['nama']; }
    // Map NIM ke nama mahasiswa
    $qMhs = query("SELECT nim, nama FROM mahasiswa");
    $nimMap = [];
    while ($m = mysqli_fetch_assoc($qMhs)) { $nimMap[$m['nim']] = $m['nama']; }
    $out = [];
    $maxTotal = 0;
    $totalP1 = $totalP2 = $totalHas = $totalTutup = $totalKpP1 = $totalKpP2 = $totalPengkp = 0;
    foreach ($stats as $key => $v) {
        $nama = $dosenMap[$key] ?? 'Dosen tidak terdaftar';
        
        $p1_list = array_values(array_unique($v['pemskr']['p1_list'] ?? []));
        $p1 = count($p1_list);
        
        $p2_list = array_values(array_unique($v['pemskr']['p2_list'] ?? []));
        $p2 = count($p2_list);
        
        $h_list = array_values(array_unique($v['hasil_list'] ?? []));
        $h = count($h_list);
        
        $t_list = array_values(array_unique($v['tutup_list'] ?? []));
        $t = count($t_list);
        
        $kp1_list = array_values(array_unique($v['pemkp']['p1_list'] ?? []));
        $kp1 = count($kp1_list);
        
        $kp2_list = array_values(array_unique($v['pemkp']['p2_list'] ?? []));
        $kp2 = count($kp2_list);
        
        $kpj_list = array_values(array_unique($v['pengkp_list'] ?? []));
        $kpj = count($kpj_list);

        $total = $p1 + $p2 + $h + $t + $kp1 + $kp2 + $kpj;
        if ($total === 0) continue;
        $out[] = [
            'nama'    => $nama,
            'pemskr'  => ['p1' => $p1, 'p2' => $p2, 'p1_list' => $p1_list, 'p2_list' => $p2_list],
            'hasil'   => $h, 'hasil_list' => $h_list,
            'tutup'   => $t, 'tutup_list' => $t_list,
            'pemkp'   => ['p1' => $kp1, 'p2' => $kp2, 'p1_list' => $kp1_list, 'p2_list' => $kp2_list],
            'pengkp'  => $kpj, 'pengkp_list' => $kpj_list,
            'total'   => $total,
        ];
        $totalP1 += $p1; $totalP2 += $p2; $totalHas += $h; $totalTutup += $t;
        $totalKpP1 += $kp1; $totalKpP2 += $kp2; $totalPengkp += $kpj;
    }
    usort($out, fn($a, $b) => $b['total'] <=> $a['total']);
    $idx = 0;
    foreach ($out as &$row) {
        $row['no'] = ++$idx;
        // Convert NIM lists to names for tooltips
        $listToNames = function($list) use ($nimMap) {
            $names = [];
            foreach ($list as $nim) {
                $names[] = $nimMap[$nim] ?? $nim;
            }
            return $names;
        };
        $row['pemskr']['p1_names'] = $listToNames($row['pemskr']['p1_list']);
        $row['pemskr']['p2_names'] = $listToNames($row['pemskr']['p2_list']);
        $row['hasil_names'] = $listToNames($row['hasil_list']);
        $row['tutup_names'] = $listToNames($row['tutup_list']);
        $row['pemkp']['p1_names'] = $listToNames($row['pemkp']['p1_list']);
        $row['pemkp']['p2_names'] = $listToNames($row['pemkp']['p2_list']);
        $row['pengkp_names'] = $listToNames($row['pengkp_list']);
    }
    $out[] = [
        'isTotal' => true,
        'p1' => $totalP1, 'p2' => $totalP2, 'hasil' => $totalHas, 'tutup' => $totalTutup,
        'kp1' => $totalKpP1, 'kp2' => $totalKpP2, 'pengkp' => $totalPengkp,
        'total' => $totalP1 + $totalP2 + $totalHas + $totalTutup + $totalKpP1 + $totalKpP2 + $totalPengkp,
        'max_total' => $maxTotal,
    ];
    return $out;
}

$statistik = [];
if (!empty($daftarTahun)) {
    foreach ($daftarTahun as $thn) {
        $statistik[$thn] = buildStatistikDosen($koneksi, $thn);
    }
}
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
    @media (prefers-reduced-motion: reduce) { .reveal { animation: none; } }

    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
    .btn-circle .tip { position: absolute; bottom: calc(100% + 6px); right: 0; background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    /* Tooltip kini muncul DI ATAS tombol & melebar ke KIRI (ke dalam tabel),
       supaya tidak terpotong tepi kotak scroll (overflow auto memotong 2 arah)
       dan tidak tertimpa baris setelahnya. Baris di-hover tetap diangkat. */
    #tbPn tbody tr { position: relative; }
    #tbPn tbody tr:hover { z-index: 30; }

    .pt-badge { display: inline-block; padding: .18rem .55rem; border-radius: .45rem; font-size: 10px; font-weight: 700; white-space: nowrap; }

    .pin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .pin-table thead th { background: #f8fafc; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; text-align: left; padding: .6rem .85rem; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
    .pin-table tbody td { padding: .7rem .85rem; font-size: .78rem; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .pin-table tbody tr:last-child td { border-bottom: none; }
    .pin-table tbody tr { transition: background .15s ease; }
    .pin-table tbody tr:hover { background: #f8fafc; }


    .pg-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 2rem; height: 2rem; border-radius: .5rem; padding: 0 .5rem; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: .75rem; font-weight: 600; cursor: pointer; transition: all .15s ease; text-decoration: none; }
    .pg-btn:hover { border-color: #fdba74; color: #c2410c; }
    .pg-btn:disabled { opacity: .45; cursor: not-allowed; }
    .pg-btn.on { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }

    .stat-table { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 720px; }
    .stat-table thead th { background: #0f172a; color: #cbd5e1; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; padding: .55rem .75rem; white-space: nowrap; }
    .stat-table thead th.grp { background: #0f172a; color: #fff; text-align: center; }
    .stat-table thead th.gsep, .stat-table td.gsep { border-left: 1px solid rgba(148,163,184,.25); }
    .stat-table tbody td { padding: .65rem .75rem; font-size: .78rem; color: #334155; border-bottom: 1px solid #f1f5f9; cursor: default; position: relative; }
    .stat-table tbody td[data-nim-list] { cursor: pointer; }
    .stat-table tbody td[data-nim-list]:hover { background: #fef3c7; }
    .stat-tooltip { display: none; position: fixed; z-index: 99999; background: #0f172a; color: #f8fafc; border-radius: 8px; padding: .5rem .75rem; font-size: 12px; line-height: 1.5; min-width: 180px; max-width: 300px; box-shadow: 0 8px 24px rgba(0,0,0,.25); pointer-events: none; }
    .stat-tooltip::after { content: ''; position: absolute; top: 100%; left: 50%; transform: translateX(-50%); border: 6px solid transparent; border-top-color: #0f172a; }
    .stat-tooltip.tt-bottom::after { top: auto; bottom: 100%; border-top-color: transparent; border-bottom-color: #0f172a; }
    .stat-tooltip .tt-label { font-weight: 600; color: #f97316; margin-bottom: .25rem; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; }
    .stat-tooltip .tt-count { color: #94a3b8; margin-bottom: .35rem; font-size: 11px; }
    .stat-tooltip .tt-item { padding: .15rem 0; border-bottom: 1px solid rgba(148,163,184,.15); }
    .stat-tooltip .tt-item:last-child { border-bottom: none; }
    .stat-table tbody tr:last-child td { border-bottom: none; }
    .stat-table tbody tr:hover td { background: #f8fafc; }

    .modal-overlay { display: none; position: fixed; inset: 0; z-index: 1060; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem; }
    .modal-overlay.show { display: flex; }

    #toastPn { position: fixed; right: 20px; bottom: 20px; z-index: 2000; display: none; align-items: center; gap: .6rem; max-width: 360px; border-radius: .75rem; background: #0f172a; color: #fff; padding: .7rem 1rem; font-size: .82rem; box-shadow: 0 12px 24px rgba(15,23,42,.25); }
    #toastPn.show { display: flex; animation: riseIn .3s ease; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                <i class="fas fa-file-signature"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-800">Surat Penunjukkan</h1>
                <p class="text-xs text-slate-500">Daftar surat penunjukkan dosen pembimbing dan penguji skripsi tingkat jurusan, lengkap dengan akses cetak surat resmi A4.</p>
            </div>
        </div>
    </section>

    <!-- ===== Strip Statistik Beban Dosen ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-5 py-3.5 shadow-sm">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-sm text-slate-600"><i class="fas fa-chart-simple"></i></span>
                <div>
                    <h6 class="text-sm font-semibold text-slate-800">Statistik Beban Dosen</h6>
                    <p class="text-[11px] text-slate-400" id="stRingkas">-</p>
                </div>
            </div>
            <button type="button" id="btnStat" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-700">
                <i class="fas fa-table-cells"></i>Lihat Pembagian
            </button>
        </div>
    </section>

    <!-- ===== Toolbar ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari mahasiswa, NIM, dosen, nomor surat..."
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <select id="fJenis" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Kategori</option>
                <option value="pemskr">Pembimbing Skripsi</option>
                <option value="pemkp">Pembimbing KP</option>
                <option value="hasil">Penguji Hasil</option>
                <option value="pengkp">Penguji KP</option>
                <option value="tutup">Penguji Tutup</option>
            </select>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Tahun</option>
                <?php foreach ($daftarTahun as $thn): ?>
                <option value="<?= $thn; ?>"><?= $thn; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
        </div>
    </section>

    <!-- ===== Ringkasan ===== -->
    <section class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs text-slate-500" id="jmlInfo">Menampilkan <b class="text-slate-800">0</b> dari <b class="text-slate-800">0</b> surat</p>
        <p class="text-xs text-slate-400"><i class="fas fa-print mr-1 text-orange-400"></i>Klik ikon <i class="fas fa-file-pdf mx-1 text-rose-500"></i>untuk membuka/cetak surat resmi A4.</p>
    </section>

    <!-- ===== Tabel Surat Penunjukkan ===== -->
    <section>
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="max-h-[560px] overflow-y-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
                        <tr>
                            <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">No</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Jenis</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Nomor</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Mahasiswa</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tanggal</th>
                            <th class="py-3.5 px-5 text-right font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbPn" class="divide-y divide-slate-100">
                        <?php
                        $no = 1;
                        foreach ($surat as $s):
                            $bgC = $no % 2 !== 0 ? 'bg-white' : 'bg-slate-50/60';
                            $jns = $KET_MAP[$s['ket']] ?? 'pemskr';
                            $lbl = ucwords(strtolower($s['ket']));
                            $bdg = 'bg-slate-50 text-slate-700';
                            if ($jns === 'pemskr') $bdg = 'bg-sky-50 text-sky-700';
                            if ($jns === 'pemkp') $bdg = 'bg-cyan-50 text-cyan-700';
                            if ($jns === 'hasil') $bdg = 'bg-amber-50 text-amber-700';
                            if ($jns === 'pengkp') $bdg = 'bg-violet-50 text-violet-700';
                            if ($jns === 'tutup') $bdg = 'bg-orange-50 text-orange-700';
                            $cari = strtolower(implode(' ', [$s['nama'], $s['nim'], $s['no_surat'], $s['ket'], $s['tahun']]));
                        ?>
                        <tr class="<?= $bgC ?> transition hover:bg-orange-50 reveal"<?= $no > 30 ? ' style="display:none;"' : '' ?> data-tab-jenis="<?= $jns ?>" data-tahun="<?= $s['tahun'] ?>" data-nama="<?= tampil($s['nama']) ?>" data-nim="<?= tampil($s['nim']) ?>" data-nomor="<?= tampil($s['no_surat']) ?>" data-tanggal="<?= $s['tanggal_fmt'] ?>" data-url="<?= $s['url'] ?>" data-cari="<?= tampil($cari) ?>">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm"><?= $no++ ?></span></td>
                            <td class="px-4 py-4"><span class="pt-badge <?= $bdg ?>"><?= $lbl ?></span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500"><?= $s['no_surat'] ?></td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800"><?= htmlspecialchars($s['nama']) ?></p><p class="text-[10px] text-slate-400">NIM <?= $s['nim'] ?></p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap"><?= $s['tanggal_fmt'] ?></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-4 py-3">
                <p class="text-[11px] text-slate-400" id="pgInfo">Halaman 1 dari 1</p>
                <div class="inline-flex items-center gap-1.5" id="pgWrap"></div>
            </div>
        </div>
    </section>

</main>


<!-- ===== Modal Edit Surat Penunjukkan ===== -->
<div class="modal-overlay" id="editModal" role="dialog" aria-modal="true">
    <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3.5">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-100 text-sm text-sky-600"><i class="fas fa-pen"></i></span>
                <div>
                    <h6 class="text-sm font-semibold text-slate-900">Edit Surat Penunjukkan</h6>
                    <p class="text-[11px] text-slate-400">Perbarui data surat pada baris terpilih.</p>
                </div>
            </div>
            <button type="button" class="text-xl leading-none text-slate-400 hover:text-slate-700" data-edit-close>&times;</button>
        </div>
        <div class="space-y-3.5 p-5">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="efJenis" class="mb-1 block text-xs font-semibold text-slate-600">Jenis Surat</label>
                    <select id="efJenis" disabled class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500 outline-none">
                        <option value="pemskr">Pembimbing Skripsi</option>
                        <option value="pemkp">Pembimbing KP</option>
                        <option value="hasil">Penguji Hasil</option>
                        <option value="pengkp">Penguji KP</option>
                        <option value="tutup">Penguji Tutup</option>
                    </select>
                </div>
                <div>
                    <label for="efNama" class="mb-1 block text-xs font-semibold text-slate-600">Nama Mahasiswa</label>
                    <input type="text" id="efNama" readonly class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="efNomor" class="mb-1 block text-xs font-semibold text-slate-600">Nomor Surat <span class="text-rose-500">*</span></label>
                    <input type="text" id="efNomor" placeholder="636/UN47.B5.5/TD.06/2024" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                </div>
                <div>
                    <label for="efTanggal" class="mb-1 block text-xs font-semibold text-slate-600">Tanggal Surat</label>
                    <input type="text" id="efTanggal" placeholder="1 Okt 2024" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                </div>
            </div>
            <div id="efDosenWrap" class="grid grid-cols-2 gap-3"></div>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3">
            <button type="button" class="rounded-lg bg-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-300" data-edit-close>Batal</button>
            <button type="button" id="btnSimpanEdit" class="rounded-lg bg-orange-500 px-3 py-2 text-xs font-semibold text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-600"><i class="fas fa-check mr-1"></i>Simpan Perubahan</button>
        </div>
    </div>
</div>
<!-- ===== Modal Pembagian Beban Dosen ===== -->
<div class="modal-overlay" id="statModal" role="dialog" aria-modal="true">
    <div class="flex max-h-[92vh] w-full max-w-7xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-5 py-3.5">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-sm text-slate-600"><i class="fas fa-chart-simple"></i></span>
                <div>
                    <h6 class="text-sm font-semibold text-slate-900">Pembagian Beban Dosen</h6>
                    <p class="text-[11px] text-slate-400">Jumlah penunjukan per dosen, skripsi &amp; kerja praktek.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <select id="stTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold outline-none focus:border-orange-400">
                    <?php foreach ($daftarTahun as $thn): ?>
                    <option value="<?= $thn; ?>"<?= $thn === (int)$daftarTahun[0] ? ' selected' : ''; ?>><?= $thn; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="text-xl leading-none text-slate-400 hover:text-slate-700" data-stat-close>&times;</button>
            </div>
        </div>
        <div class="overflow-auto">
            <table class="stat-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-left">No</th>
                        <th rowspan="2" class="text-left">Dosen</th>
                        <th colspan="4" class="grp">Skripsi</th>
                        <th colspan="3" class="grp gsep">Kerja Praktek</th>
                        <th rowspan="2" class="text-right">Total</th>
                    </tr>
                    <tr>
                    <th class="text-center">Pemb 1</th>
                    <th class="text-center">Pemb 2</th>
                    <th class="text-center">Hasil</th>
                    <th class="text-center">Tutup</th>
                    <th class="gsep text-center">Pemb 1</th>
                    <th class="text-center">Pemb 2</th>
                    <th class="text-center">Penguji</th>
                    </tr>
                </thead>
                <tbody id="tbStat">
<?php foreach ($statistik as $thn => $rows): ?>
    <?php $maxTotal = 1; foreach ($rows as $_r) { if (!empty($_r['isTotal'])) $maxTotal = $_r['max_total']; } ?>
    <?php foreach ($rows as $row): ?>
        <?php if (!empty($row['isTotal'])): ?>
        <tr class="bg-slate-100/80 st-total" data-tahun="<?= $thn; ?>"<?= $thn !== (int)$daftarTahun[0] ? ' style="display:none"' : ''; ?>>
            <td class="pl-5 pr-3"></td>
            <td class="pr-5 font-bold uppercase tracking-wide text-slate-600">Total</td>
            <td class="text-center tabular-nums text-slate-700"><?= $row['p1'] ?: 0; ?></td>
            <td class="text-center tabular-nums text-slate-700"><?= $row['p2'] ?: 0; ?></td>
            <td class="text-center tabular-nums text-slate-700"><?= $row['hasil'] ?: 0; ?></td>
            <td class="text-center tabular-nums text-slate-700"><?= $row['tutup'] ?: 0; ?></td>
            <td class="gsep text-center tabular-nums text-slate-700"><?= $row['kp1'] ?: 0; ?></td>
            <td class="text-center tabular-nums text-slate-700"><?= $row['kp2'] ?: 0; ?></td>
            <td class="text-center tabular-nums text-slate-700"><?= $row['pengkp'] ?: 0; ?></td>
            <td class="px-5 text-right font-extrabold tabular-nums text-slate-900"><?= $row['total'] ?: 0; ?></td>
        </tr>
        <?php else: ?>
            <?php
            $pct = $maxTotal > 0 ? ($row['total'] / $maxTotal) * 100 : 0;
            if ($pct < 20) { $bg = 'bg-rose-50'; $tx = 'text-rose-700'; }
            elseif ($pct < 50) { $bg = 'bg-amber-50'; $tx = 'text-amber-700'; }
            else { $bg = 'bg-emerald-50'; $tx = 'text-emerald-700'; }
            ?>
            <tr class="transition hover:opacity-90" data-tahun="<?= $thn; ?>"<?= $thn !== (int)$daftarTahun[0] ? ' style="display:none"' : ''; ?>>
                <td class="pl-5 pr-3 text-slate-400"><?= $row['no']; ?></td>
                <td class="pr-5 font-medium text-slate-800"><?= htmlspecialchars($row['nama']); ?></td>
                <td class="text-center tabular-nums font-semibold <?= $row['pemskr']['p1'] ? $tx : 'text-slate-300'; ?>" data-nim-list="<?= htmlspecialchars(json_encode(array_values(array_unique($row['pemskr']['p1_list'])))); ?>" data-label="Pemb Skripsi 1"><?= $row['pemskr']['p1'] ?: 0; ?></td>
                <td class="text-center tabular-nums font-semibold <?= $row['pemskr']['p2'] ? $tx : 'text-slate-300'; ?>" data-nim-list="<?= htmlspecialchars(json_encode(array_values(array_unique($row['pemskr']['p2_list'])))); ?>" data-label="Pemb Skripsi 2"><?= $row['pemskr']['p2'] ?: 0; ?></td>
                <td class="text-center tabular-nums font-semibold <?= $row['hasil'] ? $tx : 'text-slate-300'; ?>" data-nim-list="<?= htmlspecialchars(json_encode(array_values(array_unique($row['hasil_list'])))); ?>" data-label="Penguji Hasil"><?= $row['hasil'] ?: 0; ?></td>
                <td class="text-center tabular-nums font-semibold <?= $row['tutup'] ? $tx : 'text-slate-300'; ?>" data-nim-list="<?= htmlspecialchars(json_encode(array_values(array_unique($row['tutup_list'])))); ?>" data-label="Penguji Tutup"><?= $row['tutup'] ?: 0; ?></td>
                <td class="gsep text-center tabular-nums font-semibold <?= $row['pemkp']['p1'] ? $tx : 'text-slate-300'; ?>" data-nim-list="<?= htmlspecialchars(json_encode(array_values(array_unique($row['pemkp']['p1_list'])))); ?>" data-label="Pemb KP 1"><?= $row['pemkp']['p1'] ?: 0; ?></td>
                <td class="text-center tabular-nums font-semibold <?= $row['pemkp']['p2'] ? $tx : 'text-slate-300'; ?>" data-nim-list="<?= htmlspecialchars(json_encode(array_values(array_unique($row['pemkp']['p2_list'])))); ?>" data-label="Pemb KP 2"><?= $row['pemkp']['p2'] ?: 0; ?></td>
                <td class="text-center tabular-nums font-semibold <?= $row['pengkp'] ? $tx : 'text-slate-300'; ?>" data-nim-list="<?= htmlspecialchars(json_encode(array_values(array_unique($row['pengkp_list'])))); ?>" data-label="Penguji KP"><?= $row['pengkp'] ?: 0; ?></td>
                <td class="px-5 text-right font-bold tabular-nums <?= $tx; ?>"><?= $row['total']; ?></td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endforeach; ?>
</tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 bg-slate-50 px-5 py-2.5">
            <p class="text-[11px] text-slate-400"><i class="fas fa-circle-info mr-1 text-orange-400"></i>Dihitung dari seluruh surat penunjukkan pada tahun terpilih.</p>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toastPn"></div>

<script>
(function () {
    var fCari = document.getElementById('fCari');
    var fTahun = document.getElementById('fTahun');
    var fJenis = document.getElementById('fJenis');
    var page = 1, PER = 30;

    function getRows() {
        return Array.prototype.slice.call(document.querySelectorAll('#tbPn tr'));
    }

    var LABEL_JENIS = {
        pemskr: 'Pembimbing Skripsi',
        pemkp:  'Pembimbing KP',
        hasil:  'Penguji Hasil',
        pengkp: 'Penguji KP',
        tutup:  'Penguji Tutup'
    };

    var toast = document.getElementById('toastPn');
    function showToast(msg, isErr) {
        toast.textContent = msg;
        toast.className = 'show ' + (isErr ? 'bg-rose-600' : 'bg-slate-900');
        clearTimeout(showToast._t);
        showToast._t = setTimeout(function () { toast.className = ''; }, 2000);
    }

    function recount() {
        var c = {};
        getRows().forEach(function (tr) {
            var j = tr.getAttribute('data-tab-jenis');
            c[j] = (c[j] || 0) + 1;
        });
        Array.prototype.forEach.call(fJenis.options, function (op) {
            if (!op.value || !LABEL_JENIS[op.value]) return;
            op.textContent = LABEL_JENIS[op.value] + ' (' + (c[op.value] || 0) + ')';
        });
    }

    function visible() {
        var kata = (fCari.value || '').toLowerCase().trim();
        var th = fTahun.value;
        var jn = fJenis.value;
        return getRows().filter(function (tr) {
            if (jn && tr.getAttribute('data-tab-jenis') !== jn) return false;
            if (th && tr.getAttribute('data-tahun') !== th) return false;
            return !kata || (tr.getAttribute('data-cari') || '').indexOf(kata) !== -1;
        });
    }

    function render() {
        var allRows = getRows();
        var v = visible();
        var pages = Math.max(1, Math.ceil(v.length / PER));
        if (page > pages) page = Math.max(1, pages);
        var start = (page - 1) * PER;
        var slice = v.slice(start, start + PER);
        
        allRows.forEach(function (tr) { tr.style.display = 'none'; });
        slice.forEach(function (tr) { tr.style.display = ''; });
        
        document.getElementById('jmlInfo').innerHTML = 'Menampilkan <b class="text-slate-800">' + slice.length + '</b> dari <b class="text-slate-800">' + v.length + '</b> surat';
        document.getElementById('pgInfo').textContent = 'Halaman ' + page + ' dari ' + pages;
        var wrap = document.getElementById('pgWrap');
        wrap.innerHTML = '';
        
        if (pages <= 1) return;

        var prev = document.createElement('button');
        prev.className = 'pg-btn'; prev.innerHTML = '<i class="fas fa-chevron-left"></i>'; prev.disabled = page <= 1;
        prev.addEventListener('click', function () { if (page > 1) { page--; render(); } });
        wrap.appendChild(prev);

        function createPageBtn(n) {
            var b = document.createElement('button');
            b.className = 'pg-btn' + (n === page ? ' on' : '');
            b.textContent = n;
            b.addEventListener('click', function () { page = n; render(); });
            wrap.appendChild(b);
        }

        function createDots() {
            var s = document.createElement('span');
            s.className = 'px-1 text-xs text-slate-400 select-none';
            s.textContent = '...';
            wrap.appendChild(s);
        }

        if (pages <= 7) {
            for (var i = 1; i <= pages; i++) {
                createPageBtn(i);
            }
        } else {
            createPageBtn(1);
            if (page > 4) {
                createDots();
            }
            var startP = Math.max(2, page - 2);
            var endP = Math.min(pages - 1, page + 2);
            if (page <= 4) endP = 5;
            if (page >= pages - 3) startP = pages - 4;
            for (var i = startP; i <= endP; i++) {
                createPageBtn(i);
            }
            if (page < pages - 3) {
                createDots();
            }
            createPageBtn(pages);
        }

        var next = document.createElement('button');
        next.className = 'pg-btn'; next.innerHTML = '<i class="fas fa-chevron-right"></i>'; next.disabled = page >= pages;
        next.addEventListener('click', function () { if (page < pages) { page++; render(); } });
        wrap.appendChild(next);
    }

    /* ===== Filter ===== */
    fCari.addEventListener('input', function () { page = 1; render(); });
    fTahun.addEventListener('change', function () { page = 1; render(); });
    fJenis.addEventListener('change', function () { page = 1; render(); });
    document.getElementById('btnReset').addEventListener('click', function () {
        fCari.value = ''; fTahun.value = ''; fJenis.value = ''; page = 1; render();
    });

    /* ===== Aksi baris: unduh/buka PDF & edit (dummy) ===== */
    function openSurat(tr) {
        var url = tr.getAttribute('data-url');
        if (!url) { showToast('Template surat kategori ini belum tersedia', true); return; }
        window.open('<?= base_url() ?>/administrator/dokumen/' + url, '_blank');
    }

    var DOSEN_LABELS = {
        pemskr: ['Pembimbing 1', 'Pembimbing 2'],
        pemkp:  ['Pembimbing 1', 'Pembimbing 2'],
        hasil:  ['Penguji 1', 'Penguji 2', 'Penguji 3', 'Penguji 4', 'Penguji 5'],
        pengkp: ['Penguji 1', 'Penguji 2', 'Penguji 3', 'Penguji 4', 'Penguji 5'],
        tutup:  ['Penguji 1', 'Penguji 2', 'Penguji 3', 'Penguji 4', 'Penguji 5']
    };
    var editModal = document.getElementById('editModal');
    var editRow = null;
    function esc(t) { return (t || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }

    /* Daftar nama dosen dibaca dari baris tabel statistik beban dosen (data tetap di HTML) */
    function dosenNames() {
        var seen = {}, out = [];
        Array.prototype.forEach.call(document.querySelectorAll('#tbStat tr:not(.st-total)'), function (tr) {
            var n = (tr.cells[1] ? tr.cells[1].textContent : '').trim();
            if (n && !seen[n]) { seen[n] = 1; out.push(n); }
        });
        return out.sort(function (a, b) { return a.localeCompare(b); });
    }
    function renderDosenFields(jenis, vals) {
        var wrap = document.getElementById('efDosenWrap');
        var labels = DOSEN_LABELS[jenis] || DOSEN_LABELS.pemskr;
        var names = dosenNames();
        var h = '';
        labels.forEach(function (lbl, i) {
            h += '<div><label class="mb-1 block text-xs font-semibold text-slate-600">' + lbl + '</label>'
                + '<select data-dosen-idx="' + i + '" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">'
                + '<option value="">- Pilih dosen -</option>';
            names.forEach(function (n) {
                h += '<option value="' + esc(n) + '"' + (vals[i] === n ? ' selected' : '') + '>' + esc(n) + '</option>';
            });
            h += '</select></div>';
        });
        wrap.innerHTML = h;
    }

    document.addEventListener('click', function (e) {
        var btnU = e.target.closest('.btn-unduh');
        if (btnU) { var tr = btnU.closest('tr'); if (tr) openSurat(tr); return; }
        var btnE = e.target.closest('.btn-edit');
        if (!btnE) return;
        editRow = btnE.closest('tr');
        if (!editRow) return;
        var jenis = editRow.getAttribute('data-tab-jenis') || 'pemskr';
        document.getElementById('efJenis').value = jenis;
        document.getElementById('efNama').value = editRow.getAttribute('data-nama') || '';
        document.getElementById('efNomor').value = editRow.getAttribute('data-nomor') || '';
        document.getElementById('efTanggal').value = editRow.getAttribute('data-tanggal') || '';
        renderDosenFields(jenis, (editRow.getAttribute('data-dosen') || '').split('|'));
        editModal.classList.add('show');
    });
    editModal.querySelectorAll('[data-edit-close]').forEach(function (b) {
        b.addEventListener('click', function () { editModal.classList.remove('show'); });
    });
    editModal.addEventListener('click', function (e) { if (e.target === editModal) editModal.classList.remove('show'); });

    document.getElementById('btnSimpanEdit').addEventListener('click', function () {
        if (!editRow) return;
        var nomor = document.getElementById('efNomor').value.trim();
        var tanggal = document.getElementById('efTanggal').value.trim();
        if (!nomor) { showToast('Nomor surat wajib diisi', true); return; }
        var dosenVals = Array.prototype.slice.call(document.querySelectorAll('#efDosenWrap select'))
            .map(function (s) { return s.value.trim(); }).filter(Boolean);
        editRow.setAttribute('data-nomor', nomor);
        editRow.setAttribute('data-tanggal', tanggal);
        editRow.setAttribute('data-dosen', dosenVals.join('|'));
        var nama = editRow.getAttribute('data-nama') || '';
        editRow.setAttribute('data-cari', (nama + ' ' + nomor + ' ' + tanggal + ' ' + dosenVals.join(' ')).toLowerCase());
        var tds = editRow.querySelectorAll('td');
        tds[2].textContent = nomor;
        tds[4].textContent = tanggal;
        editModal.classList.remove('show');
        render();
        showToast('Surat penunjukkan diperbarui (dummy)');
    });

    /* ===== Statistik Beban Dosen ===== */
    var stModal = document.getElementById('statModal');
    var stTahun = document.getElementById('stTahun');
    var tbStat = document.getElementById('tbStat');

    function renderStat() {
        var y = stTahun.value;
        var vis = 0, totT = 0;
        Array.prototype.forEach.call(tbStat.querySelectorAll('tr'), function (tr) {
            var show = tr.getAttribute('data-tahun') === y;
            tr.style.display = show ? '' : 'none';
            if (!show) return;
            if (tr.classList.contains('st-total')) totT = parseInt(tr.querySelector('td:last-child').textContent, 10) || 0;
            else vis++;
        });
        document.getElementById('stRingkas').textContent = vis + ' dosen \u00b7 ' + totT + ' penunjukan \u00b7 tahun ' + y;
    }

    stTahun.addEventListener('change', renderStat);
    document.getElementById('btnStat').addEventListener('click', function () {
        stModal.classList.add('show');
    });
    stModal.querySelectorAll('[data-stat-close]').forEach(function (b) {
        b.addEventListener('click', function () { stModal.classList.remove('show'); });
    });
    renderStat();

    /* ===== Tooltip NIM list ===== */
    var tooltipEl = document.createElement('div');
    tooltipEl.className = 'stat-tooltip';
    document.body.appendChild(tooltipEl);
    
    var nimMapJs = <?= json_encode($nimMap, JSON_UNESCAPED_UNICODE) ?>;
    
    document.addEventListener('click', function (e) {
        var cell = e.target.closest('td[data-nim-list]');
        if (!cell) {
            if (!e.target.closest('.stat-tooltip')) {
                tooltipEl.style.display = 'none';
            }
            return;
        }
        var nims = JSON.parse(cell.getAttribute('data-nim-list'));
        var label = cell.getAttribute('data-label') || '';
        if (nims.length === 0) return;
        
        // Hilangkan duplikat NIM sebelum ditampilkan
        nims = [...new Set(nims)];
        
        var html = '<div class="tt-label">' + label + '</div>';
        html += '<div class="tt-count">' + nims.length + ' mahasiswa</div>';
        html += '<div class="tt-list">';
        nims.forEach(function (nim) {
            var nama = nimMapJs[nim] || nim;
            html += '<div class="tt-item">' + nama + '</div>';
        });
        html += '</div>';
        
        tooltipEl.innerHTML = html;
        tooltipEl.style.display = 'block';
        tooltipEl.classList.remove('tt-bottom');
        
        var rect = cell.getBoundingClientRect();
        var ttRect = tooltipEl.getBoundingClientRect();
        
        var topPos = rect.top - ttRect.height - 8;
        if (topPos < 10) {
            topPos = rect.bottom + 8;
            tooltipEl.classList.add('tt-bottom');
        }
        tooltipEl.style.top = topPos + 'px';
        tooltipEl.style.left = (rect.left + rect.width / 2 - ttRect.width / 2) + 'px';
        
        if (tooltipEl.getBoundingClientRect().left < 0) { tooltipEl.style.left = '8px'; }
        if (tooltipEl.getBoundingClientRect().right > window.innerWidth) { tooltipEl.style.left = (window.innerWidth - tooltipEl.getBoundingClientRect().width - 8) + 'px'; }
    });

    recount();
    render();
})();
</script>