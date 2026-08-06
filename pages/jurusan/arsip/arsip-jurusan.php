<?php
/**
 * Halaman: Arsip Jurusan
 * CRUD berbasis session (dummy, belum terhubung database).
 * Kolom data: nama, tahun, file, pengupload.
 */

$tahunArsip = (int)date('Y');
$sKey = 'arsip_jurusan_items';

// Seed data dummy pada session pertama kali
if (!isset($_SESSION[$sKey]) || !is_array($_SESSION[$sKey])) {
    $_SESSION[$sKey] = [
        ['id'=>1, 'nama'=>'SK Pendirian Perguruan Tinggi',              'tahun'=>2026, 'file'=>'file/sk-pendirian.pdf',               'pengupload'=>'Humas'],
        ['id'=>2, 'nama'=>'BKD 25/26 GANJIL - ULFATUN NADIFA',         'tahun'=>2026, 'file'=>'file/bkd-ulfatun.pdf',               'pengupload'=>'20101993'],
        ['id'=>3, 'nama'=>'PANDUAN PELAKSANAAN KKN TEMATIK 2024',       'tahun'=>2026, 'file'=>'file/panduan-kkn-2024.pdf',         'pengupload'=>'20101993'],
        ['id'=>4, 'nama'=>'SOP KERJA PRAKTEK 2024',                     'tahun'=>2026, 'file'=>'file/sop-kp-2024.pdf',               'pengupload'=>'20101993'],
        ['id'=>5, 'nama'=>'SK Tim Penyusun Visi Prodi',                 'tahun'=>2025, 'file'=>'file/sk-tim-visi.pdf',               'pengupload'=>'10041993'],
        ['id'=>6, 'nama'=>'Kurikulum 2020 Prodi S1 Informatika',        'tahun'=>2025, 'file'=>'file/kurikulum-2020.pdf',            'pengupload'=>'10041993'],
        ['id'=>7, 'nama'=>'Laporan Akreditasi Unggul',                  'tahun'=>2025, 'file'=>'file/laporan-akreditasi.pdf',        'pengupload'=>'20101993'],
        ['id'=>8, 'nama'=>'Lembar Kerja LKPS 2024',                     'tahun'=>2024, 'file'=>'file/lkps-2024.xlsx',               'pengupload'=>'10041993'],
        ['id'=>9, 'nama'=>'Rencana Strategis Jurusan 2024-2028',        'tahun'=>2024, 'file'=>'file/renstra-2024-2028.pdf',        'pengupload'=>'10041993'],
        ['id'=>10,'nama'=>'MoU Kerjasama Industri',                     'tahun'=>2023, 'file'=>'file/mou-industri.pdf',             'pengupload'=>'20101993'],
        ['id'=>11,'nama'=>'Daftar Dosen & Jabatan Fungsional',          'tahun'=>2023, 'file'=>'file/daftar-dosen.xlsx',            'pengupload'=>'10041993'],
        ['id'=>12,'nama'=>'Dokumen AKMAL Pendampingan',                 'tahun'=>2022, 'file'=>'file/akmal-pendampingan.pdf',        'pengupload'=>'20101993'],
    ];
}

// ---------- Proses CRUD (dummy/session) ----------
// Nama pengunggah dari session (default jika belum ada data login)
$namaLogin = trim((string)($_SESSION['nama_user'] ?? $_SESSION['nama'] ?? ''));
if ($namaLogin === '') $namaLogin = 'Sistem';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');
    $nama = trim((string)($_POST['nama'] ?? ''));
    $tahun = (int)($_POST['tahun'] ?? 0);
    $pengupload = trim((string)($_POST['pengupload'] ?? ''));
    if ($pengupload === '') $pengupload = $namaLogin;

    // Ambil nama file terunggah (dummy: hanya disimpan namanya)
    $file = trim((string)($_POST['file_lama'] ?? ''));
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
        $file = 'file/' . basename((string)$_FILES['file_upload']['name']);
    }

    if ($action === 'tambah' && $nama !== '') {
        $_SESSION[$sKey][] = ['id' => time(), 'nama' => $nama, 'tahun' => $tahun, 'file' => $file, 'pengupload' => $pengupload];
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        foreach ($_SESSION[$sKey] as &$r) {
            if ((int)$r['id'] === $id) {
                $r['nama'] = $nama;
                $r['tahun'] = $tahun;
                if ($file !== '') $r['file'] = $file; // file lama dipertahankan jika tidak ganti
            }
        }
        unset($r);
    } elseif ($action === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        $_SESSION[$sKey] = array_values(array_filter($_SESSION[$sKey], fn($r) => (int)$r['id'] !== $id));
    }
    // redirect (PRG) agar form tidak re-submit saat refresh
    header('Location: index.php?page=arsip-jurusan', true, 303);
    exit;
}

$arsipItems = $_SESSION[$sKey];

// ---------- Statistik
$stat = [];
$stat['total'] = count($arsipItems);
$stat['tahun_terlama'] = count($arsipItems) ? min(array_column($arsipItems, 'tahun')) : 0;
$stat['tahun_terbaru'] = count($arsipItems) ? max(array_column($arsipItems, 'tahun')) : 0;
// pengunggah terbanyak
$_cnt = array_count_values(array_map('trim', array_map('strval', array_column($arsipItems, 'pengupload'))));
arsort($_cnt);
$_top = $_cnt ? array_key_first($_cnt) : '—';
$stat['pengunggah'] = $_top !== '' ? $_top : '—';
$stat['pengunggah_jml'] = $_cnt ? (int)reset($_cnt) : 0;

// Daftar tahun untuk filter & badge
$tahunTersedia = array_map('intval', array_unique(array_column($arsipItems, 'tahun')));
rsort($tahunTersedia);

// Helper: path file di-encode agar aman di href (termasuk spasi).
function arsip_href(string $path): string {
    if ($path === '') return '#';
    $parts = explode('/', $path);
    if (count($parts) > 1) {
        $dir = implode('/', array_map('rawurlencode', array_slice($parts, 0, -1)));
        $base = rawurlencode(end($parts));
        return $dir . '/' . $base;
    }
    return rawurlencode($path);
}
?>
<style>
    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }
    .content-scroll { overflow-y: auto; min-height: 0; }

    /* Kartu statistik gradien (senada dengan beranda) */
    .tile-orange  { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }
    .tile-sky     { background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); }
    .tile-emerald { background: linear-gradient(135deg, #047857 0%, #10b981 100%); }
    .tile-violet  { background: linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%); }
    .tile-corak { position: relative; overflow: hidden; }
    .tile-corak::before { content: ""; position: absolute; inset: 0; pointer-events: none;
        background-image: radial-gradient(rgba(255,255,255,.22) 1px, transparent 1px);
        background-size: 12px 12px; opacity: .35; mix-blend-mode: overlay; }
    .tile-corak > * { position: relative; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-archive"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Arsip Jurusan</h1>
                    <p class="text-xs text-slate-500">Dokumen arsip tingkat jurusan — data dummy (session) untuk pengembangan tampilan.</p>
                </div>
            </div>
            <button type="button" id="btnTambah" class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600">
                <i class="fas fa-plus text-sm"></i>
                <span class="tip">Tambah Dokumen</span>
            </button>
        </div>
    </section>

    <!-- ===== Statistik ===== -->
    <section class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="tile-orange tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-orange-500/25">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Total Dokumen</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight"><?= number_format((int)$stat['total'], 0, ',', '.') ?></p>
                    <p class="mt-2 text-[11px] text-white/70">seluruh arsip tersimpan</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-box-archive"></i></span>
            </div>
        </div>
        <div class="tile-sky tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-sky-500/25" style="animation-delay:.05s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Tahun Terlama</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight"><?= (int)$stat['tahun_terlama'] ?></p>
                    <p class="mt-2 text-[11px] text-white/70">arsip tertua</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-hourglass-start"></i></span>
            </div>
        </div>
        <div class="tile-emerald tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-emerald-500/25" style="animation-delay:.10s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Tahun Terbaru</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight"><?= (int)$stat['tahun_terbaru'] ?></p>
                    <p class="mt-2 text-[11px] text-white/70">arsip paling baru</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-layer-group"></i></span>
            </div>
        </div>
        <div class="tile-violet tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-violet-500/25" style="animation-delay:.15s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Pengunggah Terbanyak</p>
                    <p class="mt-2 text-xl font-bold tracking-tight truncate"><?= htmlspecialchars((string)$stat['pengunggah']) ?></p>
                    <p class="mt-2 text-[11px] text-white/70"><?= (int)$stat['pengunggah_jml'] ?> dokumen</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-user"></i></span>
            </div>
        </div>
    </section>

    <!-- ===== Toolbar (cari + filter tahun, live via JS) ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari nama dokumen, pengupload…"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Tahun</option>
                <?php foreach ($tahunTersedia as $tk): ?>
                    <option value="<?= $tk ?>"><?= $tk ?></option>
                <?php endforeach; ?>
            </select>
            <a href="?page=arsip-jurusan" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">
                <i class="fas fa-times mr-1"></i>Reset
            </a>
        </div>
    </section>

    <!-- ===== Daftar Arsip ===== -->
    <section>
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Daftar Dokumen</h2>
                    <p class="mt-0.5 text-xs text-slate-500"><span id="jmlArsip"><?= count($arsipItems) ?></span> arsip ditemukan</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">
                    <i class="fas fa-file-archive"></i> <span id="badgeArsip"><?= number_format(count($arsipItems), 0, ',', '.') ?></span> file
                </span>
            </div>
            <div id="noHasil" class="hidden py-16 text-center">
                <i class="fas fa-archive text-4xl text-slate-300"></i>
                <p class="mt-3 font-medium text-slate-500">Tidak ada arsip ditemukan</p>
                <p class="text-xs text-slate-400">Coba ubah kata kunci atau filter tahun.</p>
            </div>
            <div class="max-h-[560px] overflow-y-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
                        <tr>
                            <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">Dokumen</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tahun</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Diunggah oleh</th>
                            <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="arsipBody" class="divide-y divide-slate-100">
                        <?php
                        $i = 0;
                        foreach ($arsipItems as $row):
                            $nama = $row['nama'];
                            $tahun = (int)$row['tahun'];
                            $pengupload = $row['pengupload'];
                            $path = $row['file'];
                            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                            $extLabel = match ($ext) { 'pdf'=>'PDF', 'doc'=>'DOC', 'docx'=>'DOCX', 'xls'=>'XLS', 'xlsx'=>'XLSX', 'jpg'=>'JPG', 'png'=>'PNG', default=>strtoupper($ext ?: 'FILE') };
                            // warna tonjol per format file
                            $extColor = match ($ext) {
                                'pdf'  => 'bg-rose-500 text-white',
                                'doc', 'docx' => 'bg-sky-600 text-white',
                                'xls', 'xlsx' => 'bg-emerald-600 text-white',
                                'ppt', 'pptx' => 'bg-orange-500 text-white',
                                'jpg', 'jpeg', 'png' => 'bg-violet-500 text-white',
                                default => 'bg-slate-500 text-white',
                            };
                            $i++;
                            $zebra = $i % 2 === 0 ? 'bg-slate-50/60' : 'bg-white';
                        ?>
                        <tr class="transition hover:bg-orange-50 <?= $zebra ?>"
                            data-id="<?= (int)$row['id'] ?>"
                            data-tahun="<?= $tahun ?>"
                            data-nama="<?= htmlspecialchars(mb_strtolower($nama)) ?>"
                            data-ptext="<?= htmlspecialchars($nama) ?>"
                            data-pengupload="<?= htmlspecialchars(mb_strtolower($pengupload)) ?>"
                            data-file="<?= htmlspecialchars($path) ?>">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm"><?= $i ?></span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug"><?= htmlspecialchars($nama) ?></p>
                                        <span class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold <?= $extColor ?>"><i class="fas fa-file-export"></i><?= $extLabel ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700"><?= $tahun ?></span>
                            </td>
                            <td class="px-4 py-4 text-slate-600"><?= htmlspecialchars($pengupload !== '' ? $pengupload : '—') ?></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-1.5">
                                    <?php if ($path !== ''): ?>
                                        <a href="<?= htmlspecialchars(arsip_href($path)) ?>" target="_blank" rel="noopener"
                                           class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                                            <i class="fas fa-download text-xs"></i>
                                            <span class="tip">Unduh</span>
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                                        <i class="fas fa-pen text-xs"></i>
                                        <span class="tip">Edit</span>
                                    </button>
                                    <form method="post" action="" class="inline" onsubmit="return confirm('Hapus arsip ini?');">
                                        <input type="hidden" name="action" value="hapus">
                                        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                        <button type="submit" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600">
                                            <i class="fas fa-trash text-xs"></i>
                                            <span class="tip">Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <script>
    (function () {
        var q = document.getElementById('fCari');
        var t = document.getElementById('fTahun');
        var body = document.getElementById('arsipBody');
        var rows = Array.prototype.slice.call(body ? body.querySelectorAll('tr') : []);
        var jml = document.getElementById('jmlArsip');
        var badge = document.getElementById('badgeArsip');
        var kosong = document.getElementById('noHasil');

        function terapkan() {
            var kata = (q.value || '').toLowerCase().trim();
            var tahun = t.value;
            var tampil = 0;
            rows.forEach(function (r) {
                var ok = true;
                if (kata !== '') {
                    var teks = (r.getAttribute('data-nama') || '') + ' ' + (r.getAttribute('data-pengupload') || '');
                    if (teks.indexOf(kata) === -1) ok = false;
                }
                if (ok && tahun !== '' && r.getAttribute('data-tahun') !== tahun) ok = false;
                r.style.display = ok ? '' : 'none';
                if (ok) tampil++;
            });
            if (jml) jml.textContent = tampil;
            if (badge) badge.textContent = tampil.toLocaleString('id-ID');
            if (kosong) kosong.classList.toggle('hidden', tampil > 0);
        }

        if (q) q.addEventListener('input', terapkan);
        if (t) t.addEventListener('change', terapkan);
    })();

    /* ===== Modal Unggah / Edit ===== */
    document.addEventListener('DOMContentLoaded', function () {
        var overlay = document.getElementById('arsipModal');
        var title = document.getElementById('arsipModalTitle');
        var f = document.getElementById('arsipForm');
        var iId = document.getElementById('fId');
        var iNama = document.getElementById('fNama');
        var iTahun = document.getElementById('fInTahun');
        var iFile = document.getElementById('fFile');
        var iFileLama = document.getElementById('fFileLama');
        var act = document.getElementById('fAction');

        function show(t, isEdit) {
            title.textContent = t;
            act.value = isEdit ? 'edit' : 'tambah';
            if (!isEdit) {
                f.reset();
                iId.value = '';
            }
            overlay.classList.add('show');
        }
        function close() { overlay.classList.remove('show'); }

        document.getElementById('btnTambah').addEventListener('click', function () {
            show('Unggah Dokumen Baru', false);
        });
        // tombol close
        var cls = overlay.querySelectorAll('[data-modal-close]');
        for (var i = 0; i < cls.length; i++)
            cls[i].addEventListener('click', close);
        overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });

        // tombol edit per baris: isi field dari atribut data tr
        var edits = document.querySelectorAll('.btnCircleEdit');
        for (var j = 0; j < edits.length; j++) {
            edits[j].addEventListener('click', function () {
                var tr = this.closest('tr');
                iId.value = tr.getAttribute('data-id');
                iNama.value = tr.getAttribute('data-ptext');
                iTahun.value = tr.getAttribute('data-tahun');
                iFileLama.value = tr.getAttribute('data-file');
                iFile.value = ''; // input file tidak bisa diisi lewat JS
                show('Edit Dokumen', true);
            });
        }
    });
    </script>

    <!-- ===== Modal Unggah / Edit Data ===== -->
    <div class="modal-overlay" id="arsipModal" role="dialog" aria-modal="true">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200">
                <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-archive mr-1 text-[#f97316]"></i><span id="arsipModalTitle">Unggah Dokumen</span></h6>
                <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
            </div>
            <form method="post" action="" id="arsipForm" enctype="multipart/form-data">
                <input type="hidden" name="action" id="fAction" value="tambah">
                <input type="hidden" name="id" id="fId" value="">
                <input type="hidden" name="file_lama" id="fFileLama" value="">
                <input type="hidden" name="pengupload" value="<?= htmlspecialchars($namaLogin) ?>">
                <div class="grid gap-4 p-5">
                    <div>
                        <label for="fNama" class="mb-1 block text-xs font-semibold text-slate-600">Nama Dokumen</label>
                        <input type="text" id="fNama" name="nama" required
                               class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"
                               placeholder="contoh: SK Pendirian">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="fInTahun" class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
                            <select id="fInTahun" name="tahun" required
                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                                <?php for ($tk = $tahunArsip; $tk >= 2000; $tk--): ?>
                                    <option value="<?= $tk ?>" <?= $tk === $tahunArsip ? 'selected' : '' ?>><?= $tk ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label for="fInUpload" class="mb-1 block text-xs font-semibold text-slate-600">Diunggah oleh</label>
                            <input type="text" id="fInUpload" value="<?= htmlspecialchars($namaLogin) ?>" disabled
                                   class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500">
                        </div>
                    </div>
                    <div>
                        <label for="fFile" class="mb-1 block text-xs font-semibold text-slate-600">File Dokumen</label>
                        <input type="file" id="fFile" name="file_upload"
                               class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white file:mr-3 file:rounded-md file:border-0 file:bg-orange-500 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-orange-600">
                        <p class="mt-1 text-[11px] text-slate-400">Dummy: hanya nama file yang tersimpan, file belum benar-benar diunggah.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-2 px-5 py-3 border-t border-slate-200 bg-slate-50">
                    <button type="button" class="px-3 py-1.5 text-xs rounded-md bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium" data-modal-close>Batal</button>
                    <button type="submit" class="px-4 py-1.5 text-xs rounded-md bg-[#f97316] hover:bg-[#ea6a0f] text-white font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</main>