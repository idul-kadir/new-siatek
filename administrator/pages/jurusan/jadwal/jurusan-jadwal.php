<?php
/**
 * Halaman Jadwal Kuliah (Jurusan)
 * Folder: administrator/pages/jurusan/jadwal/jurusan-jadwal.php
 *
 * Load dari DB: jadwal_kuliah, ruang_kuliah, dosen, matakuliah
 * Tahun ajaran otomatis dari DB (terbaru).
 */

require_once __DIR__ . '/../../../../fungsi.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$tahun_ajaran = '';
$qta = query("SELECT DISTINCT tahun_ajaran FROM jadwal_kuliah ORDER BY tahun_ajaran DESC LIMIT 1");
if ($qta && mysqli_num_rows($qta) > 0) {
    $rowta = mysqli_fetch_assoc($qta);
    $tahun_ajaran = $rowta['tahun_ajaran'];
}

// semua tahun ajaran yang tersedia di DB (untuk dropdown)
$daftar_tahun = [];
$q2 = query("SELECT DISTINCT tahun_ajaran FROM jadwal_kuliah ORDER BY tahun_ajaran DESC");
while ($t = mysqli_fetch_assoc($q2)) $daftar_tahun[] = $t['tahun_ajaran'];

$ambil_jadwal = query("
    SELECT j.id, j.hari, j.jam, j.kelas, j.ruangan, j.tahun_ajaran,
           rk.nama AS ruangan_nama,
           m.kode, m.nama, m.sks, m.semester, m.konsentrasi, m.id AS matkul_id,
           k.prodi AS prodi_kode, p.nama AS prodi_nama,
           j.pengajar1, j.pengajar2,
           d1.nama AS dosen1_nama,
           d2.nama AS dosen2_nama
    FROM jadwal_kuliah j
    LEFT JOIN matakuliah m ON j.id_matakuliah = m.id
    LEFT JOIN dosen d1 ON j.pengajar1 = d1.id
    LEFT JOIN dosen d2 ON j.pengajar2 = d2.id
    LEFT JOIN ruang_kuliah rk ON j.ruangan = rk.id
    LEFT JOIN kurikulum k ON m.kurikulum = k.kode
    LEFT JOIN prodi p ON k.prodi = p.kode
    WHERE j.tahun_ajaran = '" . bersihkan($tahun_ajaran) . "'
    ORDER BY j.hari, j.ruangan, j.jam
");

$jadwal = [
    'SENIN' => [], 'SELASA' => [], 'RABU' => [], 'KAMIS' => [], 'JUMAT' => [], 'SABTU' => [], 'UNDEFINED' => []
];

$all_ruangan = [];
$all_prodi = [];
$all_dosen = [];
$all_matkul = [];

// prodi dropdown langsung dari tabel prodi (semua yang aktif)
$ambil_prodi = query("SELECT kode, nama FROM prodi WHERE status = 'Aktif' ORDER BY nama");
while ($p = mysqli_fetch_assoc($ambil_prodi)) {
    $all_prodi[$p['kode']] = $p['nama'];
}

while ($row = mysqli_fetch_assoc($ambil_jadwal)) {
    $hari = strtoupper($row['hari']);
    if (!isset($jadwal[$hari])) $hari = 'UNDEFINED';
    // prodi_id = kode prodi (via kurikulum matakuliah) untuk matching filter
    // prodi    = nama prodi untuk display
    $prodi_id = $row['prodi_kode'] ?: '';
    $prodi    = $row['prodi_nama'] ?: '';
    $ruangan_nama = strtoupper($row['ruangan_nama'] ?: $row['ruangan']);

    $jadwal[$hari][] = [
        'id'         => $row['id'],
        'matkul_id'  => $row['matkul_id'],
        'kode'       => $row['kode'],
        'nama'       => $row['nama'],
        'sks'        => $row['sks'],
        'semester'   => $row['semester'],
        'kelas'      => $row['kelas'],
        'ruangan'    => $ruangan_nama,
        'ruangan_id' => $row['ruangan'],
        'jam'        => $row['jam'],
        'dosen1'     => $row['dosen1_nama'],
        'dosen2'     => $row['dosen2_nama'],
        'pengajar1_id' => $row['pengajar1'] ?? '',
        'pengajar2_id' => $row['pengajar2'] ?? '',
        'prodi'      => $prodi,
        'prodi_id'   => $prodi_id
    ];

    if ($ruangan_nama) $all_ruangan[$ruangan_nama] = true;
    if ($row['kode']) $all_matkul[$row['kode']] = true;
    if ($row['dosen1_nama']) $all_dosen[$row['dosen1_nama']] = true;
    if ($row['dosen2_nama']) $all_dosen[$row['dosen2_nama']] = true;
}

// ================= GROUPING PER HARI PER RUANGAN =================
foreach ($jadwal as $hari => $items) {
    $grouped = [];
    foreach ($items as $item) {
        $ruang = $item['ruangan'];
        if (!isset($grouped[$ruang])) $grouped[$ruang] = [];
        $grouped[$ruang][] = $item;
    }
    $jadwal[$hari] = $grouped;
}

$total_sesi = 0;
foreach ($jadwal as $rooms) foreach ($rooms as $items) $total_sesi += count($items);

// ================= DATA MASTER UNTUK FORM BUKA KELAS BARU =================
// daftar matakuliah diambil dari jadwal_kuliah tahun ajaran aktif saja
$matkul_list = [];
$ambil_matkul = query("SELECT DISTINCT m.id, m.kode, m.nama, m.sks, m.semester, k.prodi AS prodi_kode, p.nama AS prodi_nama
    FROM jadwal_kuliah j
    JOIN matakuliah m ON j.id_matakuliah = m.id
    LEFT JOIN kurikulum k ON m.kurikulum = k.kode
    LEFT JOIN prodi p ON k.prodi = p.kode
    WHERE j.tahun_ajaran = '" . bersihkan($tahun_ajaran) . "' AND TRIM(m.kode) != ''
    ORDER BY p.nama, m.nama");
while ($row = mysqli_fetch_assoc($ambil_matkul)) $matkul_list[] = $row;

$dosen_list = [];
$ambil_dosen = query("SELECT id, nama FROM dosen WHERE LOWER(posisi) = 'dosen' AND LOWER(status) = 'aktif' ORDER BY nama");
while ($row = mysqli_fetch_assoc($ambil_dosen)) $dosen_list[] = $row;

$ruangan_list = [];
$ambil_ruang = query("SELECT id, nama FROM ruang_kuliah WHERE status = 'aktif' ORDER BY nama");
while ($row = mysqli_fetch_assoc($ambil_ruang)) $ruangan_list[] = $row;
?>

<style>
    .content-scroll { overflow-y: auto; min-height: 0; }
    .tab-btn { display: inline-flex; align-items: center; gap: .5rem; border-radius: .6rem; border: 1px solid #e2e8f0; background: #fff; color: #475569; padding: .55rem .95rem; font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .tab-btn:hover { border-color: #93c5fd; color: #2563eb; }
    .tab-btn .tnum { min-width: 22px; text-align: center; border-radius: 9999px; padding: 1px 6px; font-size: .7rem; background: #f1f5f9; color: #64748b; }
    .tab-btn .tnum.hit { background: #dcfce7 !important; color: #16a34a !important; font-weight: 700; }
    .tab-btn[aria-selected="true"] { background: #2563eb; border-color: #2563eb; color: #fff; }
    .tab-btn[aria-selected="true"] .tnum { background: rgba(255,255,255,.2); color: #fff; }
    .tab-btn[aria-selected="true"] .tnum.hit { background: #dcfce7 !important; color: #16a34a !important; }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }
    .tile-orange  { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }
    .tile-sky     { background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); }
    .tile-emerald { background: linear-gradient(135deg, #047857 0%, #10b981 100%); }
    .tile-violet  { background: linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%); }
    .tile-corak { position: relative; overflow: hidden; }
    .tile-corak::before { content: ""; position: absolute; inset: 0; pointer-events: none; background-image: radial-gradient(rgba(255,255,255,.22) 1px, transparent 1px); background-size: 12px 12px; opacity: .35; mix-blend-mode: overlay; }
    .tile-corak > * { position: relative; }
    .mk-card { transition: opacity .2s ease; }
    .mk-card.hide { display: none; }
    .modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,.5); display: flex; align-items: center; justify-content: center; z-index: 1050; padding: 16px; visibility: hidden; opacity: 0; transition: opacity .2s ease, visibility .2s ease; }
    .modal-overlay.show { visibility: visible; opacity: 1; }
    .form-field label { display: block; margin-bottom: .3rem; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .03em; }
    .form-field input, .form-field select { width: 100%; border: 1px solid #e2e8f0; background: #f8fafc; border-radius: .5rem; padding: .55rem .7rem; font-size: .85rem; color: #1e293b; outline: none; transition: all .15s ease; }
    .form-field input:focus, .form-field select:focus { border-color: #60a5fa; background: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
    .card-flash { animation: cardFlash 1.2s ease; }
    @keyframes cardFlash { 0% { box-shadow: 0 0 0 3px rgba(16,185,129,.55); } 100% { box-shadow: none; } }

    #toastContainer { position: fixed; top: 16px; right: 16px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; pointer-events: none; }
    .toast-item { pointer-events: auto; display: flex; align-items: center; gap: 8px; min-width: 260px; max-width: 380px; padding: 12px 16px; border-radius: 10px; color: #fff; font-size: 13px; font-weight: 600; box-shadow: 0 8px 24px rgba(0,0,0,.18); opacity: 0; transform: translateX(100%); transition: all .3s ease; }
    .toast-item.show { opacity: 1; transform: translateX(0); }
    .toast-item.hide { opacity: 0; transform: translateX(100%); }
    .toast-success { background: linear-gradient(135deg, #059669, #10b981); }
    .toast-error { background: linear-gradient(135deg, #dc2626, #f87171); }
    .toast-info { background: linear-gradient(135deg, #2563eb, #60a5fa); }
    .toast-icon { font-size: 16px; }
    .toast-close { margin-left: auto; background: none; border: none; color: rgba(255,255,255,.8); font-size: 18px; cursor: pointer; line-height: 1; }
</style>
<div id="toastContainer"></div>

<script>
/* ============ TOAST (global, dipakai semua blok) ============ */
function toast(pesan, tipe) {
    tipe = tipe || 'info';
    var container = document.getElementById('toastContainer');
    if (!container) return;
    var item = document.createElement('div');
    item.className = 'toast-item toast-' + tipe;
    var ikon = tipe === 'success' ? 'fa-check-circle' : (tipe === 'error' ? 'fa-times-circle' : 'fa-info-circle');
    item.innerHTML = '<i class="fas ' + ikon + ' toast-icon"></i><span>' + pesan + '</span><button class="toast-close">&times;</button>';
    container.appendChild(item);
    setTimeout(function () { item.classList.add('show'); }, 10);
    setTimeout(function () { item.classList.remove('show'); item.classList.add('hide'); setTimeout(function () { item.remove(); }, 300); }, 3500);
    item.querySelector('.toast-close').addEventListener('click', function () { item.classList.remove('show'); item.classList.add('hide'); setTimeout(function () { item.remove(); }, 300); });
}
</script>

<main class="content-area content-scroll">

<!-- ================= VIEW JADWAL ================= -->
<div id="view-jadwal">

    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600"><i class="fas fa-calendar-week"></i></div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Jadwal Kuliah</h1>
                    <p class="text-xs text-slate-500">Jadwal per hari per ruangan - mata kuliah, jam, dan pengajar.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="btnRuangan" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-b from-blue-600 to-blue-700 px-3.5 py-2 text-xs font-bold text-white shadow-lg shadow-blue-600/30 ring-1 ring-blue-800/50 transition hover:from-blue-500 hover:to-blue-600 hover:shadow-blue-500/40 active:scale-95"><span class="flex h-6 w-6 items-center justify-center rounded-md bg-white/20 text-[11px]"><i class="fas fa-door-open"></i></span> Kelola Ruangan</button>
                <select id="fTahun" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 outline-none focus:border-blue-500">
                    <?php foreach ($daftar_tahun as $t): ?>
                    <option value="<?= tampil($t) ?>" <?= $t === $tahun_ajaran ? 'selected' : '' ?>><?= tampil($t) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </section>

    <section class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="tile-corak tile-orange rounded-xl p-4 text-white shadow-md">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0"><p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Sesi</p><p class="mt-1 text-2xl font-bold tracking-tight" id="statTotal"><?= $total_sesi ?></p></div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-calendar-check"></i></span>
            </div>
        </div>
        <div class="tile-corak tile-emerald rounded-xl p-4 text-white shadow-md">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0"><p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Ruangan</p><p class="mt-1 text-2xl font-bold tracking-tight"><?= count($all_ruangan) ?></p></div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-door-open"></i></span>
            </div>
        </div>
        <div class="tile-corak tile-sky rounded-xl p-4 text-white shadow-md">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0"><p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Mata Kuliah</p><p class="mt-1 text-2xl font-bold tracking-tight"><?= count($all_matkul) ?></p></div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-book-open"></i></span>
            </div>
        </div>
        <div class="tile-corak tile-violet rounded-xl p-4 text-white shadow-md">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0"><p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Dosen Pengajar</p><p class="mt-1 text-2xl font-bold tracking-tight"><?= count($all_dosen) ?></p></div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-chalkboard-teacher"></i></span>
            </div>
        </div>
    </section>

    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2">
            <?php
            $hari_labels = [
                'SENIN' => 'Senin', 'SELASA' => 'Selasa', 'RABU' => 'Rabu',
                'KAMIS' => 'Kamis', 'JUMAT' => 'Jumat', 'SABTU' => 'Sabtu', 'UNDEFINED' => 'Undefined'
            ];
            foreach ($hari_labels as $key => $label):
                $count = 0;
                foreach ($jadwal[$key] as $items) $count += count($items);
            ?>
            <button type="button" class="tab-btn" data-tab="<?= $key ?>"><i class="fas fa-calendar-day text-[11px]"></i><?= $label ?><span class="tnum"><?= $count ?></span></button>
            <?php endforeach; ?>
            <button type="button" id="btnTambah" class="inline-flex items-center gap-1.5 rounded-lg border border-orange-500 bg-orange-500 px-3 py-2 text-xs font-semibold text-white transition hover:bg-orange-600"><i class="fas fa-plus"></i> Buka Kelas Baru</button>
            <button type="button" id="btnReset" class="ml-auto inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"><i class="fas fa-rotate"></i> Reset</button>
        </div>
    </section>

    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[180px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari mata kuliah, dosen, kode, kelas..." class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-blue-400 focus:bg-white">
            </div>
            <select id="fProdi" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-blue-400">
                <option value="">Semua Prodi</option>
                <?php foreach ($all_prodi as $pKode => $pNama): ?>
                <option value="<?= tampil($pKode) ?>"><?= tampil($pNama) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="fDosen" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-blue-400">
                <option value="">Semua Dosen</option>
                <?php foreach (array_keys($all_dosen) as $dosen): ?>
                <option value="<?= tampil($dosen) ?>"><?= tampil($dosen) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" id="btnReset2" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
            <span class="hidden text-xs text-slate-400 sm:inline" id="lblHasil"></span>
        </div>
    </section>

<?php
// ================= RENDER CARD JADWAL =================
function renderCard($item) {
    $kode    = tampil($item['kode'] ?? '');
    $nama    = tampil($item['nama'] ?? '');
    $kelas   = tampil($item['kelas'] ?? '');
    $sks     = tampil($item['sks'] ?? '');
    $semester= tampil($item['semester'] ?? '');
    $jam     = tampil($item['jam'] ?? 'Belum dijadwalkan');
    $dosen1  = tampil($item['dosen1'] ?? '');
    $dosen2  = tampil($item['dosen2'] ?? '');
    $prodi   = tampil($item['prodi'] ?? '');
    $prodi_id= tampil($item['prodi_id'] ?? '');
    $matkul_id = tampil($item['matkul_id'] ?? '');
    $jadwal_id = tampil($item['id'] ?? '');
    $kelas_raw = tampil($item['kelas'] ?? '');
    $jam_raw   = tampil($item['jam'] ?? '');
    $pengajar1_id = tampil($item['pengajar1_id'] ?? '');
    $pengajar2_id = tampil($item['pengajar2_id'] ?? '');
    $ruangan_id = tampil($item['ruangan_id'] ?? '');

    $data_cari = strtolower($kode . ' ' . $nama . ' ' . $dosen1 . ' ' . $dosen2 . ' ' . $prodi . ' ' . $kelas);
    $data_dosen = strtolower($dosen1 . '|' . $dosen2);
    $jam_esc = htmlspecialchars($jam_raw, ENT_QUOTES, 'UTF-8');
    $jam_val = preg_replace('/\s*-\s*.*/', '', $jam_raw); // ambil jam masuk saja (bagian sebelum "-")

    $badge = ($dosen1 && $dosen2)
        ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span>'
        : '<span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-[10px] font-bold">Belum Ditentukan</span>';

    $pengajar_li = '';
    if ($dosen1) $pengajar_li .= '<li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>' . $dosen1 . '</span></li>';
    if ($dosen2) $pengajar_li .= '<li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>' . $dosen2 . '</span></li>';

    $data_id = $jadwal_id;
    $data_matkul = $matkul_id;
    $data_kelas = $kelas_raw;
    $data_jam = $jam_val;
    $data_d1 = $pengajar1_id;
    $data_d2 = $pengajar2_id;
    $data_ruangan = $ruangan_id;

    return <<<HTML
    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow" data-cari="{$data_cari}" data-prodi="{$prodi_id}" data-dosen="{$data_dosen}" data-id="{$data_id}" data-matkul="{$data_matkul}" data-kelas="{$data_kelas}" data-jam="{$data_jam}" data-d1="{$data_d1}" data-d2="{$data_d2}" data-ruangan="{$data_ruangan}">
        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">{$nama}</h4>
            <div class="flex shrink-0 gap-1"><button type="button" class="jadwal-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="jadwal-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
        </div>
        <div class="flex-1 px-4 py-3">
            <dl class="space-y-1.5 text-[13px]">
                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">{$kode}</dd></div>
                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">{$kelas}</dd></div>
                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester {$semester}</dd></div>
                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">{$sks}</dd></div>
                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">{$prodi}</dd></div>
                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">{$jam}</dd></div>
            </dl>
            <div class="mt-3 border-t border-slate-100 pt-2.5">
                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p>{$badge}</div>
                <ul class="space-y-1 text-[12px] text-slate-600">{$pengajar_li}</ul>
            </div>
        </div>
    </article>
HTML;
}

// ================= RENDER PER HARI =================
$ruang_colors = [
    'LAB. UNPROTECT' => '#334155',
    'R.K 2.11' => '#334155',
    'R.K 3.16' => '#334155',
    'LAB INSTALASI' => '#334155',
    'LAB. KOMPUTER 1' => '#0369a1',
    'LAB. KOMPUTER 2' => '#0369a1',
    'LAB. TENAGA LISTRIK' => '#b45309',
    'LAB. TEKNIK KENDALI' => '#6d28d9',
    'LAB. ELEKTRONIKA DAN KOMUNIKASI' => '#f97316'
];

foreach ($jadwal as $hari => $rooms):
    $label = $hari_labels[$hari];
    $ruang_count = count(array_filter(array_keys($rooms), function($r) { return $r !== ''; }));
?>
<section class="tab-panel" id="panel-<?= $hari ?>" data-tabbody>
    <div class="mb-3 flex items-center justify-between"><h2 class="text-base font-semibold text-slate-800"><?= $label ?> <span class="text-sm font-normal text-slate-400"> <?= $ruang_count ?> ruangan</span></h2></div>
<?php foreach ($rooms as $ruangan => $items): ?>
<?php if ($ruangan === '' && $hari === 'UNDEFINED'): ?>
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="grid gap-4 p-4 md:grid-cols-2 xl:grid-cols-3" data-room="">
<?php foreach ($items as $item): ?>
<?= renderCard($item) ?>
<?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
<?php $color = $ruang_colors[$ruangan] ?? '#334155'; ?>
    <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div style="background:linear-gradient(90deg,<?= $color ?> 0%,#64748b 100%);height:6px;"></div>
        <div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
            <div class="mb-1 flex items-center justify-center gap-2" style="color:<?= $color ?>"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
            <h3 class="text-base font-bold text-slate-800"><span style="color:<?= $color ?>"><?= tampil($ruangan) ?></span></h3>
            <p class="mt-0.5 text-[11px] text-slate-500"><?= count($items) ?> sesi</p>
        </div>
        <div class="p-4">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="<?= tampil($ruangan) ?>">
<?php foreach ($items as $item): ?>
<?= renderCard($item) ?>
<?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php endforeach; ?>
</section>
<?php endforeach; ?>

</div>

<!-- ================= VIEW RUANGAN ================= -->
<section id="view-ruangan" style="display:none">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-lg text-blue-600"><i class="fas fa-door-open"></i></div>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-800">Kelola Ruangan</h1>
                <p class="text-xs text-slate-500">Tambah, ubah, dan hapus ruangan laboratorium & kelas.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="btnTambahRuang" class="inline-flex items-center gap-1.5 rounded-lg border border-blue-600 bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"><i class="fas fa-plus"></i> Tambah Ruangan</button>
            <button type="button" id="btnKembaliJadwal" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"><i class="fas fa-arrow-left"></i> Kembali</button>
        </div>
    </div>
    <section class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="tile-corak tile-sky rounded-xl p-4 text-white shadow-md"><div class="flex items-start justify-between gap-2"><div class="min-w-0"><p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Total Ruangan</p><p class="mt-1 text-2xl font-bold tracking-tight" id="statJmlRuang">0</p></div><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-door-open"></i></span></div></div>
        <div class="tile-corak tile-emerald rounded-xl p-4 text-white shadow-md"><div class="flex items-start justify-between gap-2"><div class="min-w-0"><p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Kapasitas Total</p><p class="mt-1 text-2xl font-bold tracking-tight" id="statKapasitas">0</p></div><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-user-group"></i></span></div></div>
        <div class="tile-corak tile-orange rounded-xl p-4 text-white shadow-md"><div class="flex items-start justify-between gap-2"><div class="min-w-0"><p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Laboratorium</p><p class="mt-1 text-2xl font-bold tracking-tight" id="statLab">0</p></div><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-flask"></i></span></div></div>
        <div class="tile-corak tile-violet rounded-xl p-4 text-white shadow-md"><div class="flex items-start justify-between gap-2"><div class="min-w-0"><p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Ruang Kelas</p><p class="mt-1 text-2xl font-bold tracking-tight" id="statKelas">0</p></div><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-chalkboard"></i></span></div></div>
    </section>
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[180px]"><i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i><input type="search" id="fCariRuang" placeholder="Cari nama ruangan..." class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-blue-400 focus:bg-white"></div>
            <select id="fTipeRuang" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-blue-400">
                <option value="">Semua Tipe</option>
                <option value="Laboratorium">Laboratorium</option>
                <option value="Ruang Kelas">Ruang Kelas</option>
            </select>
        </div>
    </section>
    <div id="gridRuang" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"></div>
    <p id="lblRuangKosong" class="mt-6 hidden text-center text-sm text-slate-400">Belum ada ruangan. Klik <b>Tambah Ruangan</b> untuk membuat ruangan baru.</p>
</section>

</main>

<!-- Modal Ruangan -->
<div class="modal-overlay" id="ruangModal">
    <div class="flex max-h-[88vh] w-full max-w-md flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
            <h6 class="text-sm font-semibold text-slate-900"><i class="fas fa-door-open mr-1 text-blue-600"></i><span id="ruangModalTitle">Tambah Ruangan</span></h6>
            <button type="button" data-ruang-close class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"><i class="fas fa-times"></i></button>
        </div>
        <div class="overflow-y-auto p-5">
            <div class="space-y-4">
                <div class="form-field"><label>Nama Ruangan</label><input type="text" id="rNama" placeholder="Contoh: LAB. KOMPUTER 1" class="w-full"></div>
                <div class="form-field"><label>Kapasitas</label><input type="number" id="rKapasitas" placeholder="30" class="w-full"></div>
                <div class="form-field"><label>Kategori</label><select id="rKategori" class="w-full"><option value="Umum">Umum</option><option value="Komputer">Komputer</option><option value="Elektronika">Elektronika</option><option value="Kendali">Kendali</option><option value="Tenaga Listrik">Tenaga Listrik</option></select></div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3">
            <button type="button" data-ruang-close class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Batal</button>
            <button type="button" id="btnSaveRuang" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Simpan</button>
        </div>
    </div>
</div>

<!-- Modal Buka Kelas Baru -->
<div class="modal-overlay" id="jadwalModal">
    <div class="flex max-h-[88vh] w-full max-w-lg flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
            <h6 class="text-sm font-semibold text-slate-900"><i class="fas fa-plus mr-1 text-orange-600"></i><span id="modalTitleJadwal">Buka Kelas Baru</span></h6>
            <button type="button" data-jadwal-close class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"><i class="fas fa-times"></i></button>
        </div>
        <div class="overflow-y-auto p-5">
            <div class="space-y-4">
                <div class="form-field"><label>Mata Kuliah</label><select id="fMatkul" class="w-full"></select>
                    <input type="hidden" id="fProdiKode">
                    <p class="mt-1 text-[11px] text-slate-500">Prodi: <span id="lblProdi" class="font-semibold text-slate-600">-</span></p>
                    <p class="mt-0.5 text-[11px] text-slate-500">Selesai: <strong id="lblSelesai">-</strong></p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="form-field"><label>Hari</label>
                        <select id="fHari" class="w-full">
                            <option value="SENIN">Senin</option>
                            <option value="SELASA">Selasa</option>
                            <option value="RABU">Rabu</option>
                            <option value="KAMIS">Kamis</option>
                            <option value="JUMAT">Jumat</option>
                            <option value="SABTU">Sabtu</option>
                        </select>
                    </div>
                    <div class="form-field"><label>Kelas</label>
                        <select id="fKelas" class="w-full">
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="form-field"><label>Ruangan</label><select id="fRuang" class="w-full"></select></div>
                    <div class="form-field"><label>Jam Masuk</label><input type="time" id="fMasuk" class="w-full"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="form-field"><label>Pengajar 1</label><select id="fDosen1" class="w-full"></select></div>
                    <div class="form-field"><label>Pengajar 2</label><select id="fDosen2" class="w-full"></select></div>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3">
            <button type="button" data-jadwal-close class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Batal</button>
            <button type="button" id="btnSaveJadwal" class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600">Simpan</button>
        </div>
    </div>
</div>

<script>
(function () {
    var PROSES_URL = '<?= base_url() ?>/administrator/pages/jurusan/jadwal/proses.php';
    var tabBtn = document.querySelectorAll('.tab-btn[data-tab]');
    var tabAktif = 'SENIN';

    // ================= TAB FILTERING =================
    function terapkanCari() {
        var q = (document.getElementById('fCari').value || '').toLowerCase().trim();
        var prodi = document.getElementById('fProdi').value;
        var dosen = document.getElementById('fDosen').value;
        var hit = 0;
        document.querySelectorAll('.mk-card').forEach(function (c) {
            var match = true;
            if (q) match = match && (c.getAttribute('data-cari') || '').indexOf(q) !== -1;
            if (prodi) match = match && (c.getAttribute('data-prodi') || '') === prodi;
            if (dosen) match = match && (c.getAttribute('data-dosen') || '').indexOf(dosen.toLowerCase()) !== -1;
            c.classList.toggle('hide', !match);
            if (match) hit++;
        });
        var lbl = document.getElementById('lblHasil');
        lbl.textContent = q || prodi || dosen ? hit + ' hasil' : '';
        lbl.classList.toggle('hidden', !(q || prodi || dosen));
        syncBadges();
    }

    document.getElementById('fCari').addEventListener('input', terapkanCari);
    document.getElementById('fProdi').addEventListener('change', terapkanCari);
    document.getElementById('fDosen').addEventListener('change', terapkanCari);
    document.getElementById('btnReset').addEventListener('click', function () {
        document.getElementById('fCari').value = '';
        document.getElementById('fProdi').value = '';
        document.getElementById('fDosen').value = '';
        terapkanCari();
    });
    document.getElementById('btnReset2').addEventListener('click', function () {
        document.getElementById('fCari').value = '';
        document.getElementById('fProdi').value = '';
        document.getElementById('fDosen').value = '';
        terapkanCari();
    });

    function syncBadges() {
        tabBtn.forEach(function (b) {
            var t = b.getAttribute('data-tab');
            var panel = document.getElementById('panel-' + t);
            if (panel) b.querySelector('.tnum').textContent = panel.querySelectorAll('.mk-card:not(.hide)').length;
        });
    }

    function setTab(tabId) {
        tabAktif = tabId;
        tabBtn.forEach(function (b) {
            var isActive = b.getAttribute('data-tab') === tabId;
            b.setAttribute('aria-selected', isActive ? 'true' : 'false');
            if (isActive) b.classList.add('hit');
        });
        document.querySelectorAll('.tab-panel').forEach(function (p) {
            p.classList.toggle('active', p.id === 'panel-' + tabId);
        });
    }

    tabBtn.forEach(function (b) {
        b.addEventListener('click', function () { setTab(b.getAttribute('data-tab')); });
    });
    syncBadges();
    setTab('SENIN');

    /* ============ TAMBAH JADWAL ============ */
    var PROSES_URL = '<?= base_url() ?>/administrator/pages/jurusan/jadwal/proses.php';
    var TAHUN_AJARAN = '<?= addslashes($tahun_ajaran) ?>';

    var matkulList = <?= json_encode($matkul_list) ?>;
    var dosenList  = <?= json_encode($dosen_list) ?>;
    var ruanganList = <?= json_encode($ruangan_list) ?>;

    function isiSelect(el, list, placeholder) {
        el.innerHTML = '';
        if (placeholder) {
            var ph = document.createElement('option');
            ph.value = '';
            ph.textContent = placeholder;
            el.appendChild(ph);
        }
        list.forEach(function (item) {
            var o = document.createElement('option');
            o.value = item.v;
            o.textContent = item.t;
            el.appendChild(o);
        });
    }

    // isi select matakuliah pakai optgroup per prodi
    function isiMatkul(el) {
        el.innerHTML = '';
        var ph = document.createElement('option');
        ph.value = '';
        ph.textContent = 'Pilih Mata Kuliah';
        el.appendChild(ph);

        var groups = {};
        matkulList.forEach(function (m) {
            var key = m.prodi_nama || 'Lainnya';
            if (!groups[key]) groups[key] = [];
            groups[key].push(m);
        });
        Object.keys(groups).sort().forEach(function (gName) {
            var og = document.createElement('optgroup');
            og.label = gName;
            groups[gName].forEach(function (m) {
                var o = document.createElement('option');
                o.value = m.id;
                o.textContent = m.nama + ' (' + m.kode + ') - Semester ' + m.semester;
                o.dataset.sks = m.sks;
                o.dataset.prodiKode = m.prodi_kode || '';
                o.dataset.prodiNama = m.prodi_nama || '';
                og.appendChild(o);
            });
            el.appendChild(og);
        });
    }

    function fmtWaktu(totalMenit) {
        totalMenit = Math.max(0, totalMenit) % 1440;
        var h = Math.floor(totalMenit / 60), m = totalMenit % 60;
        return (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
    }

    function hitungSelesai() {
        var t = document.getElementById('fMasuk').value;
        var sks = parseInt(document.getElementById('fMatkul').selectedOptions[0]?.dataset.sks, 10) || 0;
        if (!t || !sks) { document.getElementById('lblSelesai').textContent = '-'; return ''; }
        var p = t.split(':');
        document.getElementById('lblSelesai').textContent = fmtWaktu((+p[0] * 60 + +p[1]) + sks * 50);
        return fmtWaktu((+p[0] * 60 + +p[1]) + sks * 50);
    }

    // isi dropdown saat halaman dibuka
    var eMatkul = document.getElementById('fMatkul');
    isiMatkul(eMatkul);
    isiSelect(document.getElementById('fDosen1'), dosenList.map(function (d) { return { v: d.id, t: d.nama }; }), 'Pilih Pengajar');
    (function () {
        var el = document.getElementById('fDosen2');
        el.innerHTML = '';
        var ph0 = document.createElement('option');
        ph0.value = '';
        ph0.textContent = 'Pilih Pengajar';
        el.appendChild(ph0);
        dosenList.forEach(function (d) {
            var o = document.createElement('option');
            o.value = d.id;
            o.textContent = d.nama;
            el.appendChild(o);
        });
    })();
    isiSelect(document.getElementById('fRuang'), ruanganList.map(function (r) { return { v: r.id, t: r.nama }; }), 'Pilih Ruangan');

    eMatkul.addEventListener('change', function () {
        var o = eMatkul.selectedOptions[0];
        document.getElementById('lblProdi').textContent = o?.dataset.prodiNama || '-';
        document.getElementById('fProdiKode').value = o?.dataset.prodiKode || '';
        hitungSelesai();
    });
    document.getElementById('fMasuk').addEventListener('change', hitungSelesai);

    var jadwalModal = document.getElementById('jadwalModal');
    document.getElementById('btnTambah').addEventListener('click', function () {
        // reset form
        eMatkul.value = '';
        document.getElementById('fHari').value = tabAktif;
        document.getElementById('fKelas').value = 'A';
        document.getElementById('fRuang').value = '';
        document.getElementById('fMasuk').value = '';
        document.getElementById('fDosen1').value = '';
        document.getElementById('fDosen2').value = '';
        document.getElementById('lblProdi').textContent = '-';
        document.getElementById('lblSelesai').textContent = '-';
        jadwalModal.classList.add('show');
    });
    jadwalModal.querySelectorAll('[data-jadwal-close]').forEach(function (b) {
        b.addEventListener('click', function () { jadwalModal.classList.remove('show'); });
    });
    jadwalModal.addEventListener('click', function (e) { if (e.target === jadwalModal) jadwalModal.classList.remove('show'); });

    // mode form: null = tambah, id jadwal = edit
    var editingId = null;
    var editCardEl = null;

    // simpan lewat AJAX (tambah kelas baru atau edit jadwal)
    document.getElementById('btnSaveJadwal').addEventListener('click', function () {
        var id_matakuliah = eMatkul.value;
        var hari = document.getElementById('fHari').value;
        var kelas = document.getElementById('fKelas').value;
        var ruangan = document.getElementById('fRuang').value;
        var masuk = document.getElementById('fMasuk').value;
        var d1 = document.getElementById('fDosen1').value;
        var d2 = document.getElementById('fDosen2').value || '';
        var prodi = document.getElementById('fProdiKode').value || '';
        var selesai = hitungSelesai();

        if (!id_matakuliah || !hari || !ruangan || !masuk) {
            toast('Mata kuliah, hari, ruangan, dan jam wajib diisi', 'error');
            return;
        }
        var jam = masuk + ' - ' + (selesai || '-');

        var isEdit = !!editingId;
        var body = 'action=' + (isEdit ? 'update_jadwal' : 'tambah_jadwal') +
            (isEdit ? '&id=' + encodeURIComponent(editingId) : '') +
            '&id_matakuliah=' + encodeURIComponent(id_matakuliah) +
            '&hari=' + encodeURIComponent(hari) +
            '&kelas=' + encodeURIComponent(kelas) +
            '&prodi=' + encodeURIComponent(prodi) +
            '&pengajar1=' + encodeURIComponent(d1) +
            '&pengajar2=' + encodeURIComponent(d2) +
            '&ruangan=' + encodeURIComponent(ruangan) +
            '&jam=' + encodeURIComponent(jam) +
            '&tahun_ajaran=' + encodeURIComponent(TAHUN_AJARAN);

        fetch(PROSES_URL, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                if (!json.status) { toast(json.pesan || 'Gagal menyimpan jadwal', 'error'); return; }
                toast(json.pesan, 'success');
                jadwalModal.classList.remove('show');

                var mk = matkulList.find(function (m) { return String(m.id) === String(id_matakuliah); }) || {};
                var rg = ruanganList.find(function (r) { return String(r.id) === String(ruangan); }) || {};
                var ds1 = dosenList.find(function (d) { return String(d.id) === String(d1); });
                var ds2 = dosenList.find(function (d) { return String(d.id) === String(d2); });
                if (isEdit) {
                    gantiKartu(editingId, { id: editingId, matkulId: id_matakuliah, hari: hari, kelas: kelas, ruanganId: ruangan, ruangan: (rg.nama || ruangan).toUpperCase(), jam: jam, dosen1: ds1 ? ds1.nama : '', dosen2: ds2 ? ds2.nama : '', d1: d1, d2: d2, prodi: prodi, prodiNama: mk.prodi_nama || '', kode: mk.kode, nama: mk.nama, sks: mk.sks, semester: mk.semester }, hari, ruangan);
                } else {
                    sisipKartu({ id: json.id || '', matkulId: id_matakuliah, hari: hari, kelas: kelas, ruanganId: ruangan, ruangan: (rg.nama || ruangan).toUpperCase(), jam: jam, dosen1: ds1 ? ds1.nama : '', dosen2: ds2 ? ds2.nama : '', d1: d1, d2: d2, prodi: prodi, prodiNama: mk.prodi_nama || '', kode: mk.kode, nama: mk.nama, sks: mk.sks, semester: mk.semester });
                }
                editingId = null; editCardEl = null;
            })
            .catch(function () { toast('Terjadi kesalahan pada server', 'error'); });
    });

    // ================= EVENT DELEGATION: EDIT & HAPUS KARTU JADWAL =================
    document.addEventListener('click', function (e) {
        var btnEdit = e.target.closest('.jadwal-edit');
        if (btnEdit) { bukaFormEdit(btnEdit.closest('.mk-card')); return; }

        var btnHapus = e.target.closest('.jadwal-hapus');
        if (!btnHapus) return;
        var card = btnHapus.closest('.mk-card');
        var nama = card.querySelector('h4').textContent.trim();
        konfirmasiHapus(nama, function () {
            fetch(PROSES_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=hapus_jadwal&id=' + encodeURIComponent(card.getAttribute('data-id'))
            }).then(function (res) { return res.json(); }).then(function (json) {
                if (!json.status) { toast(json.pesan || 'Gagal menghapus', 'error'); return; }
                var panelForBadges = card.closest('.tab-panel');
                var st = document.getElementById('statTotal');
                if (st) st.textContent = Math.max(0, (parseInt(st.textContent, 10) || 0) - 1);
                card.remove();
                toast(json.pesan, 'success');
                syncBadges();
                terapkanCari();
            }).catch(function () { toast('Kesalahan jaringan', 'error'); });
        });
    });

    // buka modal edit, prefill dari atribut kartu
    function bukaFormEdit(card) {
        editingId = card.getAttribute('data-id');
        editCardEl = card;
        document.getElementById('modalTitleJadwal').textContent = 'Edit Jadwal';
        eMatkul.value = card.getAttribute('data-matkul') || '';
        eMatkul.dispatchEvent(new Event('change'));
        document.getElementById('fHari').value = card.closest('.tab-panel').id.replace('panel-', '');
        document.getElementById('fKelas').value = card.getAttribute('data-kelas') || 'A';
        document.getElementById('fRuang').value = card.getAttribute('data-ruangan') || '';
        document.getElementById('fMasuk').value = card.getAttribute('data-jam') || '';
        document.getElementById('fDosen1').value = card.getAttribute('data-d1') || '';
        document.getElementById('fDosen2').value = card.getAttribute('data-d2') || '';
        jadwalModal.classList.add('show');
    }

    // ================= SISIP KARTU JADWAL BARU KE PANEL =================
    function sisipKartu(o) {
        var panel = document.getElementById('panel-' + o.hari) || document.getElementById('panel-UNDEFINED');
        if (!panel) return;
        var grid = cariGridAtauBuat(panel, o.ruangan);
        if (!grid) return;
        var art = buatKartuArticle(o);
        grid.appendChild(art);

        art.classList.add('card-flash');
        setTimeout(function () { art.classList.remove('card-flash'); }, 1300);
        syncBadges();
        terapkanCari();

        // update statistik total sesi
        var st = document.getElementById('statTotal');
        if (st) st.textContent = (parseInt(st.textContent, 10) || 0) + 1;
    }

    // ================= GANTI KARTU LAMA (MODE EDIT) =================
    function gantiKartu(id, o, hari, ruanganId) {
        var kartuLama = document.querySelector('.mk-card[data-id="' + id + '"]');
        if (!kartuLama) return;
        var artBaru = buatKartuArticle(o);
        artBaru.setAttribute('data-id', id);

        // pindah ke panel/grid baru bila hari atau ruangan berubah
        var gridBaru = null;
        var panelLama = kartuLama.closest('.tab-panel');
        var panelTujuan = document.getElementById('panel-' + hari) || panelLama;
        panelTujuan.querySelectorAll('.grid[data-room]').forEach(function (g) {
            if ((g.getAttribute('data-room') || '').toUpperCase() === o.ruangan) gridBaru = g;
        });
        if (!gridBaru) gridBaru = cariGridAtauBuat(panelTujuan, o.ruangan);
        if (gridBaru) gridBaru.appendChild(artBaru); else panelTujuan.appendChild(artBaru);

        kartuLama.replaceWith(artBaru);
        artBaru.classList.add('card-flash');
        setTimeout(function () { artBaru.classList.remove('card-flash'); }, 1300);
        syncBadges();
        terapkanCari();
    }

    // pastikan grid data-room ada pada panel, buat blok ruangan baru bila belum
    function cariGridAtauBuat(panel, oRuang) {
        if (!panel) return null;
        var grid = null;
        panel.querySelectorAll('.grid[data-room]').forEach(function (g) {
            if ((g.getAttribute('data-room') || '').toUpperCase() === oRuang) grid = g;
        });
        if (!grid) {
            var blok = document.createElement('div');
            blok.className = 'mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm';
            blok.innerHTML =
                '<div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div>' +
                '<div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">' +
                '<div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>' +
                '<h3 class="text-base font-bold text-slate-800"><span style="color:#334155">' + oRuang + '</span></h3>' +
                '<p class="mt-0.5 text-[11px] text-slate-500">1 sesi</p>' +
                '</div>' +
                '<div class="p-4"><div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="' + oRuang + '"></div></div>';
            panel.appendChild(blok);
            var g2 = blok.querySelector('.grid[data-room]');
            if (g2) grid = g2;
        }
        return grid;
    }

    /* ============ GANTI TAHUN AJARAN (re-render via AJAX) ============ */
    var RUANG_COLORS = <?= json_encode($ruang_colors) ?>;
    var HARI_KEYS = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'UNDEFINED'];
    var HARI_LABELS = { SENIN: 'Senin', SELASA: 'Selasa', RABU: 'Rabu', KAMIS: 'Kamis', JUMAT: 'Jumat', SABTU: 'Sabtu', UNDEFINED: 'Undefined' };
    var TA_AKTIF = TAHUN_AJARAN;

    function getWarnaRuang(nama) {
        var key = (nama || '').toUpperCase();
        if (RUANG_COLORS[key]) return RUANG_COLORS[key];
        return '#334155';
    }

    function renderSemuaPanel(list, ta) {
        TA_AKTIF_LBL.textContent = ta;

        var html = '';
        var mkSet = {}, dosenSet = {};
        list.forEach(function (o) {
            if (o.kode) mkSet[o.kode] = true;
            if (o.dosen1) dosenSet[o.dosen1] = true;
            if (o.dosen2) dosenSet[o.dosen2] = true;
        });

        HARI_KEYS.forEach(function (hp) {
            var items = list.filter(function (o) { return (o.hari || 'UNDEFINED').toUpperCase() === hp; });
            if (!items.length) return;

            var grouped = {};
            items.forEach(function (o) {
                var g = o.ruangan || '';
                if (!grouped[g]) grouped[g] = [];
                grouped[g].push(o);
            });

            var panelHTML = '<section class="tab-panel' + (hp === tabAktif ? ' active' : '') + '" id="panel-' + hp + '" data-tabbody>';
            panelHTML += '<div class="mb-3 flex items-center justify-between"><h2 class="text-base font-semibold text-slate-800">' + HARI_LABELS[hp] + ' <span class="text-sm font-normal text-slate-400"> ' + (Object.keys(grouped).filter(function (r) { return r !== ''; }).length) + ' ruangan</span></h2></div>';
            Object.keys(grouped).forEach(function (ruang) {
                var warna = getWarnaRuang(ruang);
                if (!ruang) {
                    panelHTML += '<div class="rounded-2xl border border-slate-200 bg-white shadow-sm"><div class="grid gap-4 p-4 md:grid-cols-2 xl:grid-cols-3" data-room=""></div></div>';
                    grouped[ruang].forEach(function (o) { panelHTML = panelHTML.replace('</div>', buatKartuArticle(o).outerHTML + '</div>'); });
                } else {
                    panelHTML += '<div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">' +
                        '<div style="background:linear-gradient(90deg,' + warna + ' 0%,#64748b 100%);height:6px;"></div>' +
                        '<div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">' +
                        '<div class="mb-1 flex items-center justify-center gap-2" style="color:' + warna + '"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>' +
                        '<h3 class="text-base font-bold text-slate-800"><span style="color:' + warna + '">' + ruang + '</span></h3>' +
                        '<p class="mt-0.5 text-[11px] text-slate-500">' + grouped[ruang].length + ' sesi</p></div>' +
                        '<div class="p-4"><div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="' + ruang + '">' +
                        grouped[ruang].map(function (o) { return buatKartuArticle(o).outerHTML; }).join('') +
                        '</div></div></div>';
                }
            });
            panelHTML += '</section>';
            containerJadwal.insertAdjacentHTML('beforeend', panelHTML);
        });
    }

    document.getElementById('fTahun').addEventListener('change', function () {
        var ta = this.value;
        if (!ta || ta === TA_AKTIF_LBL_Sel) return;
        fetch(PROSES_URL + '?action=ambil_jadwal&ta=' + encodeURIComponent(ta))
            .then(function (res) { return res.json(); })
            .then(function (json) {
                if (!json.status) { toast(json.pesan || 'Gagal memuat jadwal', 'error'); return; }
                TA_AKTIF_LBL_S = ta;
                TEWAJAR = json.data;
                containerJadwal.innerHTML = '';
                renderSemuaPanel(json.data, ta);
                syncBadges();
            })
            .catch(function () { toast('Terjadi kesalahan pada server', 'error'); });
    });
})();
    function buatKartuArticle(o) {
        var dataCari = ((o.kode || '') + ' ' + (o.nama || '') + ' ' + (o.dosen1 || '') + ' ' + (o.dosen2 || '') + ' ' + (o.prodi || '') + ' ' + (o.kelas || '')).toLowerCase();
        var badge = (o.dosen1 && o.dosen2)
            ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span>'
            : '<span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-[10px] font-bold">Belum Ditentukan</span>';
        var li = '';
        if (o.dosen1) li += '<li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>' + o.dosen1 + '</span></li>';
        if (o.dosen2) li += '<li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>' + o.dosen2 + '</span></li>';

        var art = document.createElement('article');
        art.className = 'mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow';
        art.setAttribute('data-cari', dataCari);
        art.setAttribute('data-prodi', o.prodi || '');
        art.setAttribute('data-id', o.id || '');
        art.setAttribute('data-matkul', o.matkulId || '');
        art.setAttribute('data-kelas', o.kelas || '');
        art.setAttribute('data-jam', (o.jam || '').split(' - ')[0]);
        art.setAttribute('data-d1', o.d1 || '');
        art.setAttribute('data-d2', o.d2 || '');
        art.setAttribute('data-ruangan', o.ruanganId || '');
        art.innerHTML =
            '<div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">' +
            '<h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">' + (o.nama || '') + '</h4>' +
            '<div class="flex shrink-0 gap-1"><button type="button" class="jadwal-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="jadwal-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div></div>' +
            '<div class="flex-1 px-4 py-3"><dl class="space-y-1.5 text-[13px]">' +
            '<div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">' + (o.kode || '') + '</dd></div>' +
            '<div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">' + (o.kelas || '-') + '</dd></div>' +
            '<div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester ' + (o.semester || '-') + '</dd></div>' +
            '<div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">' + (o.sks || '-') + '</dd></div>' +
            '<div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">' + (o.prodiNama || o.prodi || "-") + '</dd></div>' +
            '<div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">' + (o.jam || '') + '</dd></div>' +
            '</dl>' +
            '<div class="mt-3 border-t border-slate-100 pt-2.5">' +
            '<div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p>' + badge + '</div>' +
            '<ul class="space-y-1 text-[12px] text-slate-600">' + li + '</ul>' +
            '</div></div>';
        return art;
    }
})();

/* ============ CRUD Ruangan ============ */
(function () {
    var viewJadwal = document.getElementById('view-jadwal');
    var viewRuang = document.getElementById('view-ruangan');
    var ruangModal = document.getElementById('ruangModal');
    var ruangData = [];
    var editIndex = -1;
    var PROSES_URL = '<?= base_url() ?>/administrator/pages/jurusan/jadwal/proses.php';

    // ================= ICON PER RUANGAN =================
    function metaRuang(nama) {
        var n = (nama || '').toUpperCase();
        if (n.indexOf('KOMPUTER') !== -1) return { ico: 'bg-sky-100 text-sky-600', icon: 'fa-desktop' };
        if (n.indexOf('ELEKTRONIKA') !== -1) return { ico: 'bg-emerald-100 text-emerald-600', icon: 'fa-microchip' };
        if (n.indexOf('TENAGA LISTRIK') !== -1) return { ico: 'bg-amber-100 text-amber-600', icon: 'fa-bolt' };
        if (n.indexOf('KENDALI') !== -1) return { ico: 'bg-violet-100 text-violet-600', icon: 'fa-gamepad' };
        if (n.indexOf('INSTALASI') !== -1) return { ico: 'bg-orange-100 text-orange-600', icon: 'fa-plug' };
        if (n.indexOf('R.K') !== -1) return { ico: 'bg-indigo-100 text-indigo-600', icon: 'fa-chalkboard' };
        return { ico: 'bg-slate-100 text-slate-600', icon: 'fa-door-open' };
    }

    function tipeRuang(nama) {
        return /^lab/i.test(nama) ? 'Laboratorium' : 'Ruang Kelas';
    }

    function kartuRuang(r, i) {
        var meta = metaRuang(r.nama);
        var namaDisplay = (r.nama || '').toUpperCase();
        var card = document.createElement('div');
        card.className = 'flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow';

        var head = document.createElement('div');
        head.className = 'flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3';

        var left = document.createElement('div');
        left.className = 'flex min-w-0 items-center gap-3';
        var ic = document.createElement('span');
        ic.className = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ' + meta.ico;
        ic.innerHTML = '<i class="fas ' + meta.icon + ' text-sm"></i>';
        var tt = document.createElement('div');
        tt.className = 'min-w-0';
        var h3 = document.createElement('h3');
        h3.className = 'truncate text-sm font-bold text-slate-800';
        h3.textContent = namaDisplay;
        var sub = document.createElement('p');
        sub.className = 'text-[11px] text-slate-500';
        sub.textContent = tipeRuang(r.nama) + ' - Kapasitas ' + (r.kapasitas || '-');
        tt.appendChild(h3); tt.appendChild(sub);
        left.appendChild(ic); left.appendChild(tt);

        var btns = document.createElement('div');
        btns.className = 'flex shrink-0 gap-1';
        var btnE = document.createElement('button');
        btnE.type = 'button'; btnE.className = 'ruang-edit rounded-md border border-blue-200 bg-blue-50 px-2 py-1 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100';
        btnE.textContent = 'Edit'; btnE.setAttribute('data-idx', i);
        var btnH = document.createElement('button');
        btnH.type = 'button'; btnH.className = 'ruang-hapus rounded-md border border-red-200 bg-red-50 px-2 py-1 text-[10px] font-semibold text-red-600 transition hover:bg-red-100';
        btnH.textContent = 'Hapus'; btnH.setAttribute('data-idx', i);
        btns.appendChild(btnE); btns.appendChild(btnH);

        head.appendChild(left); head.appendChild(btns);
        card.appendChild(head);
        return card;
    }

    // ================= RENDER RUANGAN =================
    function renderRuang() {
        var q = (document.getElementById('fCariRuang').value || '').toLowerCase();
        var tipe = document.getElementById('fTipeRuang').value;
        var grid = document.getElementById('gridRuang');
        var empty = document.getElementById('lblRuangKosong');
        grid.innerHTML = '';
        var jml = 0, kap = 0, lab = 0, kls = 0;
        ruangData.forEach(function (r, i) {
            if (q && (r.nama || '').toLowerCase().indexOf(q) === -1) return;
            var rt = tipeRuang(r.nama);
            if (tipe && rt !== tipe) return;
            grid.appendChild(kartuRuang(r, i));
            jml++; kap += parseInt(r.kapasitas) || 0;
            if (rt === 'Laboratorium') lab++; else kls++;
        });
        empty.classList.toggle('hidden', jml > 0);
        document.getElementById('statJmlRuang').textContent = jml;
        document.getElementById('statKapasitas').textContent = kap;
        document.getElementById('statLab').textContent = lab;
        document.getElementById('statKelas').textContent = kls;

        // ================= EDIT/HAPUS EVENT =================
        grid.querySelectorAll('.ruang-edit').forEach(function (b) {
            b.addEventListener('click', function () { bukaFormRuang(parseInt(b.getAttribute('data-idx'), 10)); });
        });
        grid.querySelectorAll('.ruang-hapus').forEach(function (b) {
            b.addEventListener('click', function () {
                var idx = parseInt(b.getAttribute('data-idx'), 10);
                var nama = ruangData[idx].nama;
                if (typeof konfirmasiHapus === 'function') {
                    konfirmasiHapus(nama, function () {
                        fetch(PROSES_URL, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'action=hapus_ruangan&id=' + encodeURIComponent(ruangData[idx].id)
                        }).then(function (res) { return res.json(); }).then(function (json) {
                            if (json.status) { toast('Ruangan dihapus', 'success'); muatRuangDariDb(); }
                            else toast(json.pesan || 'Gagal menghapus', 'error');
                        }).catch(function () { toast('Kesalahan jaringan', 'error'); });
                    });
                }
            });
        });
    }

    // ================= MUAT RUANGAN DARI DB =================
    function muatRuangDariDb() {
        fetch(PROSES_URL + '?action=list_ruangan').then(function (res) { return res.json(); }).then(function (json) {
            ruangData = Array.isArray(json) ? json : (json.data || []);
            renderRuang();
        }).catch(function () { toast('Gagal memuat data ruangan', 'error'); });
    }

    // ================= MODAL FORM =================
    function bukaFormRuang(idx) {
        editIndex = idx;
        if (idx !== null && ruangData[idx]) {
            document.getElementById('ruangModalTitle').textContent = 'Edit Ruangan';
            document.getElementById('rNama').value = ruangData[idx].nama || '';
            document.getElementById('rKapasitas').value = ruangData[idx].kapasitas || '';
            document.getElementById('rKategori').value = ruangData[idx].kategori || 'Umum';
        } else {
            document.getElementById('ruangModalTitle').textContent = 'Tambah Ruangan';
            document.getElementById('rNama').value = '';
            document.getElementById('rKapasitas').value = '';
            document.getElementById('rKategori').value = 'Umum';
        }
        ruangModal.classList.add('show');
    }

    function simpanRuang() {
        var nama = (document.getElementById('rNama').value || '').trim();
        var kapasitas = document.getElementById('rKapasitas').value;
        var kategori = document.getElementById('rKategori').value;
        if (!nama) { toast('Nama ruangan wajib diisi', 'error'); return; }
        var body = 'action=' + (editIndex !== null && editIndex >= 0 ? 'update_ruangan' : 'tambah_ruangan') +
            '&nama=' + encodeURIComponent(nama) + '&kapasitas=' + encodeURIComponent(kapasitas) +
            '&kategori=' + encodeURIComponent(kategori);
        if (editIndex !== null && editIndex >= 0) body += '&id=' + encodeURIComponent(ruangData[editIndex].id);
        fetch(PROSES_URL, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                if (json.status) {
                    toast(editIndex !== null ? 'Ruangan diperbarui' : 'Ruangan ditambahkan', 'success');
                    ruangModal.classList.remove('show');
                    muatRuangDariDb();
                } else toast(json.pesan || 'Gagal menyimpan', 'error');
            }).catch(function () { toast('Terjadi kesalahan pada server', 'error'); });
    }

    // ================= EVENT HANDLERS =================
    document.getElementById('btnRuangan').addEventListener('click', function () {
        viewJadwal.style.display = 'none';
        viewRuang.style.display = '';
        muatRuangDariDb();
    });
    document.getElementById('btnKembaliJadwal').addEventListener('click', function () {
        viewRuang.style.display = 'none';
        viewJadwal.style.display = '';
    });
    document.getElementById('btnTambahRuang').addEventListener('click', function () { bukaFormRuang(null); });
    document.getElementById('btnSaveRuang').addEventListener('click', simpanRuang);
    document.getElementById('fCariRuang').addEventListener('input', renderRuang);
    document.getElementById('fTipeRuang').addEventListener('change', renderRuang);
    ruangModal.querySelectorAll('[data-ruang-close]').forEach(function (b) { b.addEventListener('click', function () { ruangModal.classList.remove('show'); }); });
    ruangModal.addEventListener('click', function (e) { if (e.target === ruangModal) ruangModal.classList.remove('show'); });
})();
</script>
