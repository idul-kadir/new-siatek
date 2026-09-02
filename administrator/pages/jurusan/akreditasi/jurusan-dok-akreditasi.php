<?php
/**
 * Halaman: Dokumen Akreditasi
 * Data dari tabel dokumen_akreditas, dikelompokkan per kriteria dari tabel
 * kategori-akreditas. Layout: accordion per kriteria (Kriteria 1-9).
 * JS untuk: buka/tutup accordion, cari + filter kriteria/tahun, buka semua,
 * pagination per kriteria, modal unggah/edit (AJAX ke proses.php), hapus
 * (konfirmasiHapus global), dan toast notifikasi.
 */
require __DIR__ . "/../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// pastikan user sudah login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    header('Location: ' . base_url() . '/');
    exit;
}

// ================= DATA AWAL =================
// daftar kriteria (untuk accordion + select kriteria di toolbar & modal)
$kategoriList = [];
$qKat = query("SELECT * FROM `kategori-akreditas` ORDER BY no ASC");
while($k = mysqli_fetch_assoc($qKat)){ $kategoriList[] = $k; }

// metadata tampilan tiap kriteria (ikon, warna, subjudul) - mengikuti desain halaman
$metaKriteria = [
    1 => ['ikon' => 'fa-bullseye',      'warna' => 'orange',  'sub' => 'Dokumen visi misi, renstra, dan strategi pencapaian'],
    2 => ['ikon' => 'fa-sitemap',       'warna' => 'sky',     'sub' => 'Organisasi, SOP, dan dokumen kerjasama'],
    3 => ['ikon' => 'fa-user-graduate', 'warna' => 'emerald', 'sub' => 'Statistik, penerimaan, beasiswa, dan layanan mahasiswa'],
    4 => ['ikon' => 'fa-users',         'warna' => 'violet',  'sub' => 'Dosen dan tenaga kependidikan'],
    5 => ['ikon' => 'fa-coins',         'warna' => 'indigo',  'sub' => 'Pengelolaan keuangan serta sarana prasarana'],
    6 => ['ikon' => 'fa-book-open',     'warna' => 'amber',   'sub' => 'Kurikulum, pembelajaran, dan penilaian'],
    7 => ['ikon' => 'fa-flask',         'warna' => 'rose',    'sub' => 'Penelitian dosen dan luaran penelitian'],
    8 => ['ikon' => 'fa-hands-helping', 'warna' => 'teal',    'sub' => 'Pengabdian dosen dan luaran pengabdian'],
    9 => ['ikon' => 'fa-trophy',        'warna' => 'lime',    'sub' => 'Capaian kinerja tridharma dan mutu lulusan'],
];

// dokumen dikelompokkan per kategori (join files untuk data file,
// join dosen untuk nama pengunggah dari id yang tersimpan di kolom pengupload)
$perKat = [];
$qDok = query("SELECT d.*, f.format_file, f.nama_file AS nama_file_server, u.nama AS nama_user FROM dokumen_akreditas d LEFT JOIN files f ON d.lokasi = f.id LEFT JOIN dosen u ON d.pengupload = u.id ORDER BY d.kategori ASC, d.tahun DESC, d.id DESC");
while($d = mysqli_fetch_assoc($qDok)){ $perKat[(int)$d['kategori']][] = $d; }

// tahun unik untuk filter toolbar
$tahunTersedia = [];
$qTh = query("SELECT DISTINCT tahun FROM dokumen_akreditas ORDER BY tahun DESC");
while($t = mysqli_fetch_assoc($qTh)){ $tahunTersedia[] = $t['tahun']; }

// total dokumen untuk ringkasan awal
$totalDok = 0;
foreach($perKat as $arr){ $totalDok += count($arr); }

// hak akses edit/hapus: pengupload sendiri ATAU level >= 5
$levelLogin = (int)($_SESSION['level'] ?? 1);
$idLogin    = (string)($_SESSION['id'] ?? '');
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    /* ===== Accordion ===== */
    .acc-item { position: relative; border: 1px solid #e2e8f0; background: #fff; border-radius: 1rem; overflow: hidden; box-shadow: 0 1px 2px rgba(15,23,42,.05); transition: border-color .2s ease, box-shadow .2s ease; }
    .acc-item::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--cat); opacity: 0; transition: opacity .2s ease; z-index: 1; }
    .acc-item.open { border-color: #cbd5e1; box-shadow: 0 10px 24px -12px rgba(15,23,42,.18); }
    .acc-item.open::before { opacity: 1; }
    .acc-c1 { --cat: #f97316; }
    .acc-c1.open .acc-chev { background: #f97316; color: #fff; }
    .acc-c1 .acc-badge { background: #ffedd5; color: #c2410c; }
    .acc-c1 .dok-row:hover { border-color: #fdba74; box-shadow: 0 3px 10px -4px rgba(249,115,22,.25); }
    .acc-c2 { --cat: #0ea5e9; }
    .acc-c2.open .acc-chev { background: #0ea5e9; color: #fff; }
    .acc-c2 .acc-badge { background: #e0f2fe; color: #0369a1; }
    .acc-c2 .dok-row:hover { border-color: #7dd3fc; box-shadow: 0 3px 10px -4px rgba(14,165,233,.25); }
    .acc-c3 { --cat: #10b981; }
    .acc-c3.open .acc-chev { background: #10b981; color: #fff; }
    .acc-c3 .acc-badge { background: #d1fae5; color: #047857; }
    .acc-c3 .dok-row:hover { border-color: #6ee7b7; box-shadow: 0 3px 10px -4px rgba(16,185,129,.25); }
    .acc-c4 { --cat: #8b5cf6; }
    .acc-c4.open .acc-chev { background: #8b5cf6; color: #fff; }
    .acc-c4 .acc-badge { background: #ede9fe; color: #6d28d9; }
    .acc-c4 .dok-row:hover { border-color: #c4b5fd; box-shadow: 0 3px 10px -4px rgba(139,92,246,.25); }
    .acc-c5 { --cat: #6366f1; }
    .acc-c5.open .acc-chev { background: #6366f1; color: #fff; }
    .acc-c5 .acc-badge { background: #e0e7ff; color: #4338ca; }
    .acc-c5 .dok-row:hover { border-color: #a5b4fc; box-shadow: 0 3px 10px -4px rgba(99,102,241,.25); }
    .acc-c6 { --cat: #f59e0b; }
    .acc-c6.open .acc-chev { background: #f59e0b; color: #fff; }
    .acc-c6 .acc-badge { background: #fef3c7; color: #b45309; }
    .acc-c6 .dok-row:hover { border-color: #fcd34d; box-shadow: 0 3px 10px -4px rgba(245,158,11,.25); }
    .acc-c7 { --cat: #f43f5e; }
    .acc-c7.open .acc-chev { background: #f43f5e; color: #fff; }
    .acc-c7 .acc-badge { background: #ffe4e6; color: #be123c; }
    .acc-c7 .dok-row:hover { border-color: #fda4af; box-shadow: 0 3px 10px -4px rgba(244,63,94,.25); }
    .acc-c8 { --cat: #14b8a6; }
    .acc-c8.open .acc-chev { background: #14b8a6; color: #fff; }
    .acc-c8 .acc-badge { background: #ccfbf1; color: #0f766e; }
    .acc-c8 .dok-row:hover { border-color: #5eead4; box-shadow: 0 3px 10px -4px rgba(20,184,166,.25); }
    .acc-c9 { --cat: #84cc16; }
    .acc-c9.open .acc-chev { background: #84cc16; color: #fff; }
    .acc-c9 .acc-badge { background: #ecfccb; color: #4d7c0f; }
    .acc-c9 .dok-row:hover { border-color: #bef264; box-shadow: 0 3px 10px -4px rgba(132,204,22,.25); }

    .acc-head { width: 100%; display: flex; align-items: center; gap: .9rem; padding: 1rem 1.25rem; text-align: left; background: #fff; cursor: pointer; transition: background .15s ease; }
    .acc-head:hover { background: #f8fafc; }
    .acc-chev { display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 9999px; background: #f1f5f9; color: #64748b; transition: transform .3s ease, background .15s ease, color .15s ease; }
    .acc-item.open .acc-chev { transform: rotate(180deg); }
    .acc-badge { min-width: 1.75rem; text-align: center; border-radius: 9999px; padding: .15rem .6rem; font-size: .72rem; font-weight: 700; }
    .acc-body { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .3s ease; }
    .acc-item.open .acc-body { grid-template-rows: 1fr; }
    .acc-inner { min-height: 0; overflow: hidden; }

    /* ===== Baris dokumen di dalam accordion ===== */
    .dok-row { display: flex; align-items: center; gap: .8rem; padding: .75rem .9rem; border-radius: .85rem; background: #fff; border: 1px solid #e2e8f0; transition: border-color .15s ease, box-shadow .15s ease; }
    .file-ico { display: inline-flex; align-items: center; justify-content: center; width: 2.75rem; height: 2.75rem; border-radius: .75rem; font-size: 1.15rem; flex-shrink: 0; }
    .file-pdf   { background: #fee2e2; color: #dc2626; }
    .file-word  { background: #dbeafe; color: #2563eb; }
    .file-excel { background: #d1fae5; color: #059669; }
    .file-img   { background: #fef3c7; color: #d97706; }
    .jenis-badge { display: inline-flex; align-items: center; gap: .3rem; border-radius: 9999px; padding: .12rem .6rem; font-size: .66rem; font-weight: 700; letter-spacing: .02em; text-transform: uppercase; }
    .jenis-sk       { background: #fff7ed; color: #c2410c; }
    .jenis-dokumen  { background: #eff6ff; color: #1d4ed8; }
    .jenis-berita   { background: #f5f3ff; color: #6d28d9; }
    .jenis-lainnya  { background: #f1f5f9; color: #475569; }
    .acc-empty { padding: 1.1rem; text-align: center; border: 1px dashed #e2e8f0; border-radius: .85rem; background: #fff; color: #64748b; font-size: .8rem; }
    .acc-empty i { margin-right: .4rem; }
    .pg-btn { min-width: 2rem; height: 2rem; border-radius: .5rem; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: .75rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .pg-btn:hover { border-color: #fdba74; color: #c2410c; }
    .pg-btn:disabled { opacity: .45; cursor: not-allowed; }
    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
    @media (prefers-reduced-motion: reduce) { .reveal { animation: none; } }

    /* Toast Notification */
    #toastContainer {
        position: fixed;
        top: 1.5rem;
        right: 1.5rem;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        pointer-events: none;
    }
    .toast-item {
        pointer-events: auto;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1.25rem;
        border-radius: 0.75rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #fff;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);
        transform: translateX(120%);
        opacity: 0;
        transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
        max-width: 380px;
    }
    .toast-item.show {
        transform: translateX(0);
        opacity: 1;
    }
    .toast-item.hide {
        transform: translateX(120%);
        opacity: 0;
    }
    .toast-success { background: linear-gradient(135deg, #059669, #10b981); }
    .toast-error   { background: linear-gradient(135deg, #dc2626, #ef4444); }
    .toast-info    { background: linear-gradient(135deg, #0284c7, #0ea5e9); }
    .toast-icon { font-size: 1rem; flex-shrink: 0; }
    .toast-close {
        margin-left: auto;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.15s;
        background: none;
        border: none;
        color: #fff;
        font-size: 1rem;
        padding: 0;
    }
    .toast-close:hover { opacity: 1; }
</style>
<main class="content-area content-scroll">

    <!-- Toast Container -->
    <div id="toastContainer"></div>

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-lg text-sky-600">
                    <i class="fas fa-award"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Dokumen Akreditasi</h1>
                    <p class="text-xs text-slate-500">Dokumen pendukung akreditasi jurusan Teknik Elektro & Komputer, dikelompokkan per kriteria (Kriteria 1-9).</p>
                </div>
            </div>
            <button type="button" data-upload-open class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600">
                <i class="fas fa-plus text-sm"></i>
                <span class="tip">Unggah Dokumen</span>
            </button>
        </div>
    </section>

    <!-- ===== Toolbar (cari + filter kriteria + tahun + reset) ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari nama dokumen, jenis, pengunggah..."
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-sky-400 focus:bg-white">
            </div>
            <select id="fKategori" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                <option value="">Semua Kriteria</option>
                <?php foreach($kategoriList as $k){ ?>
                <option value="c<?= $k['no']; ?>">Kriteria <?= $k['no']; ?> - <?= tampil($k['keterangan']); ?></option>
                <?php } ?>
            </select>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                <option value="">Semua Tahun</option>
                <?php foreach($tahunTersedia as $th){ ?>
                <option value="<?= $th; ?>"><?= $th; ?></option>
                <?php } ?>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
        </div>
    </section>

    <!-- ===== Ringkasan + Buka/Tutup Semua ===== -->
    <section class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs text-slate-500">Menampilkan <b id="jmlDitemukan" class="text-slate-800"><?= $totalDok; ?></b> dokumen</p>
        <button type="button" id="btnExpandAll" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100">
            <i class="fas fa-expand-alt text-[10px]"></i> Buka Semua
        </button>
    </section>

    <!-- ===== Accordion per Kriteria ===== -->
    <section class="space-y-4" id="accordionWrap">
        <?php $idx = 0; foreach($kategoriList as $kat){
            $no   = (int)$kat['no'];
            $cat  = 'c' . $no;
            $m    = $metaKriteria[$no] ?? ['ikon' => 'fa-folder', 'warna' => 'slate', 'sub' => ''];
            $rows = $perKat[$no] ?? [];
            $open = ($idx === 0) ? ' open' : '';
            $delay = ($idx > 0) ? ' style="animation-delay:.' . sprintf('%02d', $idx) . 's"' : '';
        ?>
        <div class="acc-item acc-c<?= $no; ?> reveal<?= $open; ?>" data-cat="<?= $cat; ?>"<?= $delay; ?>>
            <button type="button" class="acc-head" aria-expanded="<?= $idx === 0 ? 'true' : 'false'; ?>">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-<?= $m['warna']; ?>-100 text-lg text-<?= $m['warna']; ?>-600"><i class="fas <?= $m['ikon']; ?>"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">Kriteria <?= $no; ?> - <?= tampil($kat['keterangan']); ?></span>
                    <span class="block text-xs text-slate-500"><?= tampil($m['sub']); ?></span>
                </span>
                <span class="acc-badge" id="cnt-<?= $cat; ?>"><?= count($rows); ?></span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <?php foreach($rows as $data){
                            $url = url_file($data['lokasi'] ?? '');
                            // kelas ikon file berdasarkan format file
                            $fmt = strtolower(trim((string)($data['format_file'] ?? '')));
                            if(in_array($fmt, ['doc', 'docx'])){ $kelasIkon = 'file-word'; $ikon = 'fa-file-word'; }
                            elseif(in_array($fmt, ['xls', 'xlsx', 'csv'])){ $kelasIkon = 'file-excel'; $ikon = 'fa-file-excel'; }
                            elseif(in_array($fmt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'])){ $kelasIkon = 'file-img'; $ikon = 'fa-file-image'; }
                            else { $kelasIkon = 'file-pdf'; $ikon = 'fa-file-pdf'; }
                            // kelas badge jenis dokumen
                            $jenisVal = $data['jenis'];
                            if($jenisVal === 'SK'){ $kelasJenis = 'jenis-sk'; }
                            elseif($jenisVal === 'Dokumen'){ $kelasJenis = 'jenis-dokumen'; }
                            elseif($jenisVal === 'Berita Acara'){ $kelasJenis = 'jenis-berita'; }
                            else { $kelasJenis = 'jenis-lainnya'; }
                            $namaUpload = trim((string)($data['nama_user'] ?? $data['pengupload'] ?? ''));
                            $cari = strtolower($data['nama'] . ' ' . $data['jenis'] . ' ' . $kat['keterangan'] . ' ' . $namaUpload);
                        ?>
                        <div class="dok-row" data-id="<?= $data['id']; ?>" data-tahun="<?= $data['tahun']; ?>" data-cari="<?= tampil($cari); ?>" data-file="<?= tampil($data['lokasi'] ?? ''); ?>" data-namafile="<?= tampil($data['nama_file_server'] ?? ''); ?>" data-kategori="<?= $cat; ?>" data-jenis="<?= tampil($data['jenis']); ?>" data-pengupload="<?= tampil($namaUpload); ?>" data-ptext="<?= tampil($data['nama']); ?>">
                            <span class="file-ico <?= $kelasIkon; ?>"><i class="fas <?= $ikon; ?>"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800"><?= tampil($data['nama']); ?></p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge <?= $kelasJenis; ?>"><?= tampil($data['jenis']); ?></span>
                                    <?php if($namaUpload !== ''): ?>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600"><?= tampil($namaUpload); ?></span>
                                    <?php endif; ?>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700"><?= $data['tahun']; ?></span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5">
                                    <?php if($url !== ''): ?>
                                    <a href="<?= $url; ?>" target="_blank" rel="noopener" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a>
                                    <?php endif; ?>
                                    <?php $bolehKelola = $levelLogin >= 5 || (string)$data['pengupload'] === $idLogin; ?>
                                    <?php if($bolehKelola): ?>
                                    <a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                                    <a href="#" class="btnHapusAkreditasi btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600" data-id="<?= $data['id']; ?>"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a>
                                    <?php else: ?>
                                    <span class="btn-circle bg-slate-300 text-slate-500 opacity-50 cursor-not-allowed"><i class="fas fa-pen text-xs"></i><span class="tip">Tidak berhak</span></span>
                                    <span class="btn-circle bg-slate-300 text-slate-500 opacity-50 cursor-not-allowed"><i class="fas fa-trash text-xs"></i><span class="tip">Tidak berhak</span></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if(count($rows) === 0){ ?>
                        <p class="acc-empty"><i class="fas fa-inbox"></i>Belum ada dokumen pada kriteria ini. Klik tombol <b>+</b> di kanan atas untuk mengunggah.</p>
                        <?php } else { ?>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen yang cocok.</p>
                        <?php } ?>
                        <div class="acc-pager flex items-center justify-between border-t border-slate-100 pt-3<?= count($rows) === 0 ? ' hidden' : ''; ?>">
                            <button type="button" class="pg-btn pg-prev"><i class="fas fa-chevron-left text-xs"></i></button>
                            <p class="pg-label text-xs font-semibold text-slate-500">Halaman 1 / 1</p>
                            <button type="button" class="pg-btn pg-next"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $idx++; } ?>
    </section>

</main>

<!-- ===== Modal Unggah Dokumen ===== -->
<div class="modal-overlay" id="uploadModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-upload mr-1 text-[#0ea5e9]"></i>Unggah Dokumen Akreditasi</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-upload-close>&times;</button>
        </div>
        <form id="formUpload" enctype="multipart/form-data" class="p-5 space-y-4">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Nama / Judul Dokumen <span class="text-red-500">*</span></label>
                <input type="text" id="inpNama" name="nama" required placeholder="mis. SK Penetapan Visi Misi Jurusan"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400 focus:bg-white">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Kriteria</label>
                    <select id="inpKriteria" name="kategori" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <?php foreach($kategoriList as $k){ ?>
                        <option value="<?= $k['no']; ?>">Kriteria <?= $k['no']; ?> - <?= tampil($k['keterangan']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Jenis Dokumen</label>
                    <select id="inpJenis" name="jenis" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <option>SK</option><option>Dokumen</option><option>Berita Acara</option><option>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
                    <select id="inpTahun" name="tahun" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <?php for($y = date('Y'); $y >= 2000; $y--){ ?>
                        <option value="<?= $y; ?>"><?= $y; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Pengunggah</label>
                    <input type="text" id="inpPengunggah" value="<?= tampil($_SESSION['nama'] ?? 'Sistem'); ?>" readonly disabled
                           class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500 outline-none cursor-not-allowed">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">File Dokumen <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-3">
                    <i class="fas fa-file-pdf text-slate-400"></i>
                    <span id="namaFile" class="text-sm text-slate-500">Belum ada file dipilih</span>
                    <label class="ml-auto inline-flex cursor-pointer items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-700">
                        <i class="fas fa-folder-open text-xs"></i> Pilih File
                        <input type="file" id="inpFile" name="file_upload" accept="application/pdf,.pdf" class="hidden" required>
                    </label>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" data-upload-close class="px-3 py-2 text-xs rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium">Simpan Dokumen</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Modal Edit Dokumen ===== -->
<div class="modal-overlay" id="editModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-pen mr-1 text-[#0ea5e9]"></i>Edit Dokumen Akreditasi</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-edit-close>&times;</button>
        </div>
        <form id="formEdit" enctype="multipart/form-data" class="p-5 space-y-4">
            <input type="hidden" name="id" id="inpEditId" value="">
            <input type="hidden" name="file_lama" id="inpEditFileLama" value="">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Nama / Judul Dokumen</label>
                <input type="text" id="inpEditNama" name="nama" required placeholder="mis. SK Penetapan Visi Misi Jurusan"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400 focus:bg-white">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Kriteria</label>
                    <select id="inpEditKriteria" name="kategori" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <?php foreach($kategoriList as $k){ ?>
                        <option value="<?= $k['no']; ?>">Kriteria <?= $k['no']; ?> - <?= tampil($k['keterangan']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Jenis Dokumen</label>
                    <select id="inpEditJenis" name="jenis" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <option>SK</option><option>Dokumen</option><option>Berita Acara</option><option>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
                    <select id="inpEditTahun" name="tahun" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <?php for($y = date('Y'); $y >= 2000; $y--){ ?>
                        <option value="<?= $y; ?>"><?= $y; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Pengunggah</label>
                    <input type="text" value="<?= tampil($_SESSION['nama'] ?? 'Sistem'); ?>" readonly disabled
                           class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500 outline-none cursor-not-allowed">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">File Dokumen <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-3">
                    <i class="fas fa-file-pdf text-slate-400"></i>
                    <span id="namaFileEdit" class="text-sm text-slate-500">File lama - ganti jika perlu</span>
                    <label class="ml-auto inline-flex cursor-pointer items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-700">
                        <i class="fas fa-folder-open text-xs"></i> Pilih File
                        <input type="file" id="inpEditFile" name="file_upload" accept="application/pdf,.pdf" class="hidden">
                    </label>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" data-edit-close class="px-3 py-2 text-xs rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    var BASE_URL = '<?= base_url(); ?>';
    var PROSES_URL = BASE_URL + '/administrator/pages/jurusan/akreditasi/proses.php';
    var PAGE_SIZE = 5;
    var semuaTerbuka = false;

    /* ===== Toast Notification ===== */
    function toast(pesan, tipe) {
        tipe = tipe || 'info';
        var container = document.getElementById('toastContainer');
        var item = document.createElement('div');
        item.className = 'toast-item toast-' + tipe;
        var ikon = tipe === 'success' ? 'fa-check-circle' : (tipe === 'error' ? 'fa-times-circle' : 'fa-info-circle');
        item.innerHTML = '<i class="fas ' + ikon + ' toast-icon"></i><span>' + pesan + '</span><button class="toast-close" onclick="this.parentElement.classList.add(\'hide\');setTimeout(function(){item.remove()},350)">&times;</button>';
        container.appendChild(item);
        requestAnimationFrame(function(){ item.classList.add('show'); });
        setTimeout(function(){ item.classList.add('hide'); setTimeout(function(){ item.remove(); }, 350); }, 4000);
    }

    /* ===== Accordion: buka/tutup ===== */
    function setOpen(it, open) {
        it.classList.toggle("open", open);
        var h = it.querySelector(".acc-head");
        if (h) h.setAttribute("aria-expanded", open ? "true" : "false");
    }
    function updateExpandBtn() {
        var btnExpandAll = document.getElementById("btnExpandAll");
        if (btnExpandAll) btnExpandAll.innerHTML = semuaTerbuka
            ? "<i class=\"fas fa-compress-alt text-[10px]\"></i> Tutup Semua"
            : "<i class=\"fas fa-expand-alt text-[10px]\"></i> Buka Semua";
    }
    function openAll(open) {
        semuaTerbuka = open;
        document.querySelectorAll(".acc-item").forEach(function (it) { setOpen(it, open); });
        updateExpandBtn();
    }

    /* ===== Filter + pagination per kriteria ===== */
    function cocok(row) {
        var fCari = document.getElementById("fCari");
        var fTahun = document.getElementById("fTahun");
        var kata = (fCari && fCari.value || "").toLowerCase().trim();
        var thn = fTahun ? fTahun.value : "";
        if (kata !== "" && ((row.getAttribute("data-cari") || "").toLowerCase().indexOf(kata) === -1)) return false;
        if (thn !== "" && row.getAttribute("data-tahun") !== thn) return false;
        return true;
    }

    function renderItem(it) {
        var fKategori = document.getElementById("fKategori");
        var kat = fKategori ? fKategori.value : "";
        var cat = it.getAttribute("data-cat");
        var catMatch = kat === "" || kat === cat;
        var cntEl = document.getElementById("cnt-" + cat);
        var tampil = [];
        it.querySelectorAll(".dok-row").forEach(function (r) {
            var show = catMatch && cocok(r);
            r.style.display = show ? "" : "none";
            if (show) tampil.push(r);
        });
        // display diatur di render(); renderItem hanya atur tampil/sembunyi baris dalam
        // pesan kosong: bedakan kriteria memang kosong vs filter tidak menemukan apa pun
        var semua = it.querySelectorAll(".dok-row").length;
        var empty = it.querySelector(".acc-empty");
        if (empty) {
            if (semua === 0) {
                empty.innerHTML = '<i class="fas fa-inbox"></i>Belum ada dokumen pada kriteria ini. Klik tombol <b>+</b> di kanan atas untuk mengunggah.';
                empty.classList.remove("hidden");
            } else if (tampil.length === 0) {
                empty.innerHTML = '<i class="fas fa-inbox"></i>Tidak ada dokumen yang cocok dengan pencarian/filter.';
                empty.classList.remove("hidden");
            } else {
                empty.classList.add("hidden");
            }
        }
        // pagination disembunyikan saat tidak ada baris yang tampil
        var pager = it.querySelector(".acc-pager");
        if (pager) pager.classList.toggle("hidden", tampil.length === 0);
        var totalHal = Math.max(1, Math.ceil(tampil.length / PAGE_SIZE));
        var page = parseInt(it.getAttribute("data-page") || "1", 10);
        if (page < 1) page = 1;
        if (page > totalHal) page = totalHal;
        it.setAttribute("data-page", page);
        var mulai = (page - 1) * PAGE_SIZE;
        for (var i = 0; i < tampil.length; i++) {
            tampil[i].style.display = (i >= mulai && i < mulai + PAGE_SIZE) ? "" : "none";
        }
        if (cntEl) cntEl.textContent = tampil.length;
        var lbl = it.querySelector(".pg-label");
        if (lbl) lbl.textContent = "Halaman " + page + " / " + totalHal;
        var prev = it.querySelector(".pg-prev");
        var next = it.querySelector(".pg-next");
        if (prev) prev.disabled = page <= 1;
        if (next) next.disabled = page >= totalHal;
        return tampil.length;
    }

    function render() {
        var jmlEl = document.getElementById("jmlDitemukan");
        var fCari = document.getElementById("fCari");
        var fTahun = document.getElementById("fTahun");
        var fKategori = document.getElementById("fKategori");
        var total = 0;
        var adaFilter = Boolean((fCari && fCari.value) || (fTahun && fTahun.value) || (fKategori && fKategori.value));
        document.querySelectorAll(".acc-item").forEach(function (it) {
            var n = renderItem(it);
            total += n;
            // saat filter aktif, kriteria tanpa hasil yang tampil disembunyikan;
            // tanpa filter, kriteria kosong tetap tampil (agar tombol unggah terlihat)
            if (adaFilter) {
                setOpen(it, n > 0);
                it.style.display = n > 0 ? "" : "none";
            } else {
                it.style.display = "";
            }
        });
        if (jmlEl) jmlEl.textContent = total.toLocaleString("id-ID");
    }

    function resetPages() {
        document.querySelectorAll(".acc-item").forEach(function (it) { it.setAttribute("data-page", "1"); });
    }

    /* ===== Delegasi klik (tahan terhadap reload konten accordion) ===== */
    document.addEventListener("click", function (e) {
        // buka/tutup accordion
        var head = e.target.closest(".acc-head");
        if (head) {
            var itAcc = head.closest(".acc-item");
            setOpen(itAcc, !itAcc.classList.contains("open"));
            semuaTerbuka = false;
            updateExpandBtn();
            return;
        }
        // pagination per kriteria
        var prev = e.target.closest(".pg-prev");
        if (prev) {
            var itP = prev.closest(".acc-item");
            itP.setAttribute("data-page", Math.max(1, parseInt(itP.getAttribute("data-page") || "1", 10) - 1));
            renderItem(itP);
            return;
        }
        var next = e.target.closest(".pg-next");
        if (next) {
            var itN = next.closest(".acc-item");
            itN.setAttribute("data-page", parseInt(itN.getAttribute("data-page") || "1", 10) + 1);
            renderItem(itN);
            return;
        }
        // tombol edit di baris dokumen
        var editBtn = e.target.closest("[data-edit-open]");
        if (editBtn) {
            isiFormEdit(editBtn.closest(".dok-row"));
            return;
        }
        // tombol hapus di baris dokumen
        var hapusBtn = e.target.closest(".btnHapusAkreditasi");
        if (hapusBtn) {
            var row = hapusBtn.closest(".dok-row");
            var nama = row ? (row.getAttribute("data-ptext") || "dokumen ini") : "dokumen ini";
            var id = hapusBtn.getAttribute("data-id");
            if (typeof konfirmasiHapus === "function") {
                konfirmasiHapus(nama, function () { hapusDokumen(id); });
            } else {
                toast("Fungsi konfirmasi tidak tersedia", "error");
            }
            return;
        }
    });

    /* ===== Toolbar: cari + filter + reset ===== */
    (function () {
        var fCari = document.getElementById("fCari");
        var fTahun = document.getElementById("fTahun");
        var fKategori = document.getElementById("fKategori");
        var btnReset = document.getElementById("btnReset");
        var btnExpandAll = document.getElementById("btnExpandAll");

        if (fCari) fCari.addEventListener("input", function () { resetPages(); render(); });
        if (fTahun) fTahun.addEventListener("change", function () { resetPages(); render(); });
        if (fKategori) fKategori.addEventListener("change", function () { resetPages(); render(); });
        if (btnExpandAll) btnExpandAll.addEventListener("click", function () { openAll(!semuaTerbuka); });
        if (btnReset) btnReset.addEventListener("click", function () {
            if (fCari) fCari.value = "";
            if (fTahun) fTahun.value = "";
            if (fKategori) fKategori.value = "";
            openAll(false);
            var items = document.querySelectorAll(".acc-item");
            if (items[0]) setOpen(items[0], true);
            resetPages();
            render();
        });
    })();

    /* ===== Reload konten accordion saja lewat AJAX (tanpa reload halaman) ===== */
    function muatUlangTabel() {
        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { 'ambil-tabel-akreditasi': 1 },
            dataType: 'json',
            success: function (respon) {
                if (respon.status) {
                    $('#accordionWrap').html(respon.data);
                    resetPages();
                    render();
                }
            },
            error: function () {
                toast('Terjadi kesalahan pada server', 'error');
            }
        });
    }

    /* ===== Hapus dokumen ===== */
    function hapusDokumen(id) {
        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { 'hapus-data-akreditasi': 1, id: id },
            dataType: 'json',
            success: function (respon) {
                toast(respon.pesan, respon.status ? 'success' : 'error');
                if (respon.status) muatUlangTabel();
            },
            error: function () {
                toast('Terjadi kesalahan pada server', 'error');
            }
        });
    }

    /* ===== Modal Unggah ===== */
    (function () {
        var modal = document.getElementById("uploadModal");
        var inpFile = document.getElementById("inpFile");
        var namaFile = document.getElementById("namaFile");

        if (inpFile) inpFile.addEventListener("change", function () {
            if (namaFile) namaFile.textContent = inpFile.files && inpFile.files[0] ? inpFile.files[0].name : "Belum ada file dipilih";
        });
        document.querySelectorAll("[data-upload-open]").forEach(function (b) {
            b.addEventListener("click", function () { if (modal) modal.classList.add("show"); });
        });
        document.querySelectorAll("[data-upload-close]").forEach(function (b) {
            b.addEventListener("click", function () { if (modal) modal.classList.remove("show"); });
        });
        // tutup modal saat klik overlay
        modal.addEventListener("click", function (e) {
            if (e.target === modal) modal.classList.remove("show");
        });

         // kirim form tambah dokumen lewat AJAX (FormData karena ada input file)
         $('#formUpload').on('submit', function (e) {
             e.preventDefault();
             var form = this;
             var data = new FormData(form);
             data.append('tambah-data-akreditasi', 1);

             $.ajax({
                 url: PROSES_URL,
                 method: 'POST',
                 data: data,
                 contentType: false,
                 processData: false,
                 dataType: 'json',
                 success: function (respon) {
                     toast(respon.pesan, respon.status ? 'success' : 'error');
                     if (respon.status) {
                         if (modal) modal.classList.remove('show');
                         form.reset();
                         if (namaFile) namaFile.textContent = 'Belum ada file dipilih';
                         muatUlangTabel();
                     }
                 },
                 error: function (xhr, status, error) {
                     toast('Terjadi kesalahan pada server: ' + error, 'error');
                 }
             });
         });
     })();

    /* ===== Modal Edit ===== */
    (function () {
        var modalEdit = document.getElementById("editModal");
        var inpEditFile = document.getElementById("inpEditFile");
        var namaFileEdit = document.getElementById("namaFileEdit");

        document.querySelectorAll("[data-edit-close]").forEach(function (b) {
            b.addEventListener("click", function () { if (modalEdit) modalEdit.classList.remove("show"); });
        });

        // isi form edit dari atribut data-* baris dokumen yang diklik
        window.isiFormEdit = function (row) {
            if (!row || !modalEdit) return;
            document.getElementById("inpEditId").value = row.getAttribute("data-id") || "";
            document.getElementById("inpEditNama").value = row.getAttribute("data-ptext") || "";
            var kat = (row.getAttribute("data-kategori") || "c1").replace("c", "");
            document.getElementById("inpEditKriteria").value = kat;
            var jenis = row.getAttribute("data-jenis") || "";
            var selJenis = document.getElementById("inpEditJenis");
            for (var o = 0; o < selJenis.options.length; o++) {
                if (selJenis.options[o].text === jenis) { selJenis.selectedIndex = o; break; }
            }
            document.getElementById("inpEditTahun").value = row.getAttribute("data-tahun") || "";
            document.getElementById("inpEditFileLama").value = row.getAttribute("data-file") || "";
            var nf = row.getAttribute("data-namafile") || "";
            if (namaFileEdit) namaFileEdit.textContent = nf !== "" ? nf : "Belum ada file - pilih jika perlu";
            if (inpEditFile) inpEditFile.value = "";
            modalEdit.classList.add("show");
        };

        if (inpEditFile) inpEditFile.addEventListener("change", function () {
            if (namaFileEdit) namaFileEdit.textContent = inpEditFile.files && inpEditFile.files[0] ? inpEditFile.files[0].name : "File lama - ganti jika perlu";
        });

        // kirim form edit dokumen lewat AJAX (FormData karena ada input file)
        $('#formEdit').on('submit', function (e) {
            e.preventDefault();
            var form = this;
            var data = new FormData(form);
            data.append('edit-data-akreditasi', 1);

            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: data,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (respon) {
                    toast(respon.pesan, respon.status ? 'success' : 'error');
                    if (respon.status) {
                        if (modalEdit) modalEdit.classList.remove('show');
                        form.reset();
                        if (inpEditFile) inpEditFile.value = '';
                        muatUlangTabel();
                    }
                },
                error: function () {
                    toast('Terjadi kesalahan pada server', 'error');
                }
            });
        });
    })();

    /* ===== Inisialisasi tampilan awal ===== */
    (function () {
        var items = document.querySelectorAll(".acc-item");
        if (items[0]) setOpen(items[0], true);
        render();
    })();
</script>