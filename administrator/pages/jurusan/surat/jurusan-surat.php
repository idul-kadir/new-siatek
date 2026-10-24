<?php
/**
 * Halaman: Surat Jurusan (SIATEK)
 * Canvas Editor Langsung dengan Toolbar di Samping (Sidebar Toolbar)
 * - Lembar A4 fisik (margin 2.5cm mutlak, Times New Roman 12pt).
 * - Toolbar kontrol & formatting di samping kiri untuk fleksibilitas maksimal.
 * - Tanpa QR validasi dummy (tanda tangan fisik bersih).
 * - Format teks, tabel, penomoran, sisip gambar, dan pengaturan jarak.
 */
require_once __DIR__ . '/../../../../fungsi.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    header('Location: ' . base_url() . '/');
    exit;
}

// Ambil daftar prodi untuk selector Kaprodi
$daftarProdi = [];
$qP = query("SELECT kode, nama, jenjang FROM prodi WHERE LOWER(status) = 'aktif' ORDER BY nama ASC");
if ($qP) {
    while ($p = mysqli_fetch_assoc($qP)) {
        $daftarProdi[] = $p;
    }
}

// Ambil daftar format nomor surat dari DB secara dinamis dikelompokkan berdasarkan format_nomor
$daftarFormatNomor = [];
try {
    // Pada MySQL 8.0+, pengurutan harus menggunakan ORDER BY, bukan GROUP BY ... ASC
    $qFN = query("SELECT MIN(id) AS id, format_nomor, MIN(nama_surat) AS nama_surat FROM `format_nomor_surat` GROUP BY format_nomor ORDER BY format_nomor ASC");
} catch (Throwable $e) {
    try {
        $qFN = query("SELECT * FROM `format_nomor_surat` GROUP BY format_nomor ORDER BY format_nomor ASC");
    } catch (Throwable $e2) {
        $qFN = query("SELECT DISTINCT format_nomor, id, nama_surat FROM `format_nomor_surat` ORDER BY format_nomor ASC");
    }
}
if (!empty($qFN)) {
    while ($fn = mysqli_fetch_assoc($qFN)) {
        if (!empty(trim((string)$fn['format_nomor']))) {
            $daftarFormatNomor[] = $fn;
        }
    }
}

// Ambil nomor surat terakhir di tahun ini dari tabel nomor_surat
$tahunIni = date('Y');
$infoNomorTerakhir = null;
$qLast = query("SELECT urutan, nomor, tanggal, keterangan FROM `nomor_surat` WHERE YEAR(tanggal) = '$tahunIni' ORDER BY CAST(urutan AS UNSIGNED) DESC, id DESC LIMIT 1");
if ($qLast && ($rL = mysqli_fetch_assoc($qLast))) {
    $infoNomorTerakhir = $rL;
}
?>

<!-- TinyMCE 6 Rich Text Editor (Full Suite: Table Tools, Resizing, Merge/Split Cells, Font Formatting) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>

<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle .tip { position: absolute; bottom: calc(100% + 6px); right: 0; background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    .pin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .pin-table thead th { background: #f8fafc; color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; text-align: left; padding: .65rem .85rem; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
    .pin-table tbody td { padding: .75rem .85rem; font-size: .8rem; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .pin-table tbody tr:hover { background: #f8fafc; }

    .pt-badge { display: inline-block; padding: .2rem .55rem; border-radius: .45rem; font-size: 11px; font-weight: 600; white-space: nowrap; }

    .pg-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 2rem; height: 2rem; border-radius: .5rem; padding: 0 .5rem; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: .75rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .pg-btn:hover { border-color: #fdba74; color: #c2410c; }
    .pg-btn:disabled { opacity: .45; cursor: not-allowed; }
    .pg-btn.on { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }

    /* Modal Overlay Standar SIATEK */
    .modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 1070;
        background: rgba(15,23,42,.6);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        backdrop-filter: blur(2px);
    }
    .modal-overlay.show {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Form Input & Select Standar SIATEK */
    .form-input, .form-select {
        width: 100% !important;
        border-radius: 0.5rem !important;
        border: 1px solid #cbd5e1 !important;
        background-color: #f8fafc !important;
        padding: 0.5rem 0.75rem !important;
        font-size: 0.75rem !important;
        color: #1e293b !important;
        outline: none !important;
        transition: all 0.15s ease !important;
        display: block !important;
        box-sizing: border-box !important;
    }
    .form-input:focus, .form-select:focus {
        border-color: #fb923c !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 2px rgba(251, 146, 60, 0.2) !important;
    }
    .form-label {
        display: block !important;
        margin-bottom: 0.25rem !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        color: #334155 !important;
    }

    /* Toast Notification Standar SIATEK */
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
        line-height: 1;
    }
    .toast-close:hover { opacity: 1; }

    /* Mencegah toolbar/popover floating TinyMCE terbuka otomatis saat kursor di dalam tabel */
    .tox-pop, .tox-pop--top, .tox-pop--bottom {
        display: none !important;
    }

    /* Modal Canvas Editor Fullscreen */
    .canvas-overlay { display: none; position: fixed; inset: 0; z-index: 1060; background: rgba(15,23,42,.85); backdrop-filter: blur(4px); align-items: stretch; justify-content: center; }
    .canvas-overlay.show { display: flex; }

    /* Meja Kerja Panggung Kertas A4 & F4 */
    .a4-desk {
        background: #1e293b;
        background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
        background-size: 16px 16px;
        overflow-y: auto;
        overflow-x: auto;
        padding: 24px 20px 140px; /* Jarak bawah solid agar batas bawah kertas selalu terlihat utuh saat di-scroll */
        display: block;
        flex: 1;
        width: 100%;
        text-align: center;
        cursor: default;
    }

    /* Container yang menampung lembar-lembar halaman A4/F4 fisik */
    #pagesContainer {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 36px; /* Jarak meja kerja realistis antar lembar kertas */
        margin: 0 auto;
        width: fit-content;
        cursor: default;
    }

    /* Satu Lembar Kertas Fisik A4 / F4 ΓÇö TINGGI TERKUNCI MUTLAK! TIDAK AKAN PERNAH MOLOR KELUAR UKURAN KERTAS */
    .a4-page {
        background: #ffffff;
        width: 210mm;
        height: 297mm;
        min-height: 297mm;
        max-height: 297mm;
        margin: 0 auto;
        padding: 20mm 25mm 15mm 25mm;
        border: 1px solid #94a3b8;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.55), 0 2px 6px rgba(0, 0, 0, 0.3);
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        line-height: 1.35;
        color: #000;
        box-sizing: border-box;
        position: relative;
        text-align: left;
        display: flex;
        flex-direction: column;
        overflow: hidden; /* Mutlak: Kertas fisik tidak boleh bertambah panjang saat ditekan enter */
        transition: width .2s ease, height .2s ease;
        cursor: text;
    }
    .a4-page.is-f4 {
        width: 215mm;
        height: 330mm;
        min-height: 330mm;
        max-height: 330mm;
        padding: 20mm 25mm 15mm 25mm;
    }
    @media (max-width: 992px) {
        .a4-desk { padding: 15px 10px 100px; }
        .a4-page { width: 100% !important; height: auto !important; min-height: auto !important; max-height: none !important; padding: 16mm 12mm; }
    }

    /* Area Editor Ketik di Dalam Halaman (Word-like Rich Text) */
    .page-editor {
        flex: 1;
        outline: none;
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        line-height: 1.35;
        color: #000000;
        caret-color: #000000; /* KURSOR KETIK TEGAS DAN JELAS */
        text-align: justify;
        word-break: break-word;
        cursor: text !important;
        padding: 0;
        margin: 0;
        overflow: hidden; /* Mencegah scrollbar internal merusak kertas */
    }
    .page-editor:focus {
        box-shadow: none;
        caret-color: #000000;
        cursor: text !important;
    }
    /* ===== ISOLASI GAYA DOKUMEN & FONT DARI INDUK (Mencegah Kebocoran 'Inter' dari administrator/index.php) ===== */
    #pagesContainer .page-editor {
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        color: #000000;
        font-weight: normal;
    }
    #pagesContainer .page-editor,
    #pagesContainer .page-editor * {
        font-family: inherit;
        cursor: text;
    }
    #pagesContainer .page-editor table,
    #pagesContainer .page-editor table tr,
    #pagesContainer .page-editor table td,
    #pagesContainer .page-editor table th {
        font-family: inherit;
        font-size: inherit;
        color: #000000;
        text-align: left;
        cursor: text !important;
    }
    #pagesContainer .page-editor table.ttd-table {
        margin-top: 24px;
        margin-bottom: 8px;
        border-collapse: collapse;
        width: 100%;
        font-family: inherit;
        font-size: inherit;
        font-weight: normal !important;
    }
    #pagesContainer .page-editor table.ttd-table tr,
    #pagesContainer .page-editor table.ttd-table td,
    #pagesContainer .page-editor table.ttd-table td * {
        padding: 0;
        border: none !important;
        font-family: inherit !important;
        font-size: inherit;
        font-weight: normal !important;
        line-height: 1.25;
        vertical-align: top;
        color: #000000 !important;
    }
    #pagesContainer .page-editor table.ttd-table td u {
        text-decoration: underline !important;
        font-weight: normal !important;
    }
    .page-editor img {
        cursor: pointer !important;
    }
    .page-editor:empty:before {
        content: attr(data-placeholder);
        color: #94a3b8;
        font-style: italic;
        pointer-events: none;
        cursor: text !important;
        display: block;
    }
    .page-editor p {
        margin: 0 0 0.35rem 0;
        font-family: inherit;
        font-size: inherit;
        line-height: inherit;
        min-height: 1.35em;
    }
    /* Tabel Standar Kanvas Surat (Word-grade Table) */
    .page-editor table:not(.ttd-table) {
        width: 100% !important;
        border-collapse: collapse !important;
        margin: 12px 0 !important;
        font-family: inherit;
        font-size: inherit;
    }
    .page-editor table:not(.ttd-table) td,
    .page-editor table:not(.ttd-table) th {
        border: 1px solid #000000 !important;
        padding: 6px 10px !important;
        height: 28px !important;
        min-height: 28px !important;
        vertical-align: top;
        font-family: inherit;
        font-size: 12pt;
        color: #000000;
        box-sizing: border-box;
    }
    .page-editor table:not(.ttd-table) th {
        background-color: #f8fafc;
        font-weight: bold;
    }
    .page-editor table.borderless td, .page-editor table.borderless th,
    .page-editor table.table-borderless td, .page-editor table.table-borderless th {
        border: none !important;
    }
    /* TinyMCE Ribbon Header & Dark Oxide Integration */
    #deskTopRibbon .tox-tinymce-inline {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        width: 100% !important;
        display: none !important;
    }
    #deskTopRibbon .tox-tinymce-inline.active-ribbon {
        display: block !important;
    }
    #deskTopRibbon .tox .tox-menubar {
        background: #0f172a !important;
        border-bottom: 1px solid #334155 !important;
        padding: 2px 8px !important;
    }
    #deskTopRibbon .tox .tox-toolbar,
    #deskTopRibbon .tox .tox-toolbar__primary,
    #deskTopRibbon .tox .tox-toolbar__overflow {
        background: #1e293b !important;
        border: none !important;
        padding: 2px 6px !important;
    }
    #deskTopRibbon .tox .tox-tbtn {
        color: #cbd5e1 !important;
    }
    #deskTopRibbon .tox .tox-tbtn:hover {
        background: #334155 !important;
        color: #ffffff !important;
    }
    #deskTopRibbon .tox .tox-tbtn--enabled,
    #deskTopRibbon .tox .tox-tbtn--active {
        background: #334155 !important;
        color: #38bdf8 !important;
    }
    #deskTopRibbon .tox .tox-mbtn {
        color: #cbd5e1 !important;
    }
    #deskTopRibbon .tox .tox-mbtn:hover,
    #deskTopRibbon .tox .tox-mbtn--active {
        background: #334155 !important;
        color: #ffffff !important;
    }
    /* TinyMCE Popups, Grid Picker, Contextual Floating Table Toolbar, and Dialogs must appear ABOVE canvasModal (z-index: 1060) */
    .tox.tox-tinymce-aux,
    .tox-dialog-wrap,
    .tox-dialog,
    .tox-pop {
        z-index: 99999 !important;
    }
    .page-editor ol {
        margin: 4px 0 6px 24px;
        padding-left: 0;
        list-style-type: decimal;
    }
    .page-editor ul {
        margin: 4px 0 6px 20px;
        padding-left: 0;
        list-style-type: disc;
    }
    .page-editor li {
        margin-bottom: 2px;
    }
    .page-editor img {
        max-width: 100%;
        height: auto;
        display: inline-block;
        margin: 6px auto;
        border-radius: 2px;
    }
    .page-editor hr {
        border: 0;
        border-top: 1px solid #000;
        margin: 10px 0;
    }

    /* Footer Batas Bawah Halaman (Visual Guide Nomor Halaman) */
    .page-footer-guide {
        height: 22px;
        margin-top: 4px;
        padding-top: 4px;
        border-top: 1.5px dashed #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #94a3b8;
        font-family: system-ui, -apple-system, sans-serif;
        font-size: 10px;
        font-weight: 600;
        user-select: none;
        pointer-events: auto;
    }
    .btn-hapus-halaman {
        background: #fee2e2;
        color: #ef4444;
        border: 1px solid #fca5a5;
        border-radius: 4px;
        padding: 1px 8px;
        font-size: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s ease;
    }
    .btn-hapus-halaman:hover {
        background: #ef4444;
        color: #ffffff;
    }
    }
    .word-canvas img {
        max-width: 100%;
        height: auto;
        display: inline-block;
        margin: 6px auto;
        border-radius: 2px;
    }
    .word-canvas hr {
        border: 0;
        border-top: 1px solid #000;
        margin: 10px 0;
    }

    /* Ribbon Toolbar di Atas Meja Kerja A4 (Ala Microsoft Word) */
    .desk-top-ribbon {
        background: #0f172a;
        border-bottom: 1px solid #334155;
        padding: 6px 14px;
        min-height: 44px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        z-index: 30;
    }

    /* Tombol & Input Tool di Ribbon & Sidebar */
    .sb-btn { display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: .375rem; color: #cbd5e1; background: #1e293b; border: 1px solid #334155; transition: all .12s ease; font-size: .8rem; }
    .sb-btn:hover { background: #334155; color: #fff; border-color: #475569; }
    .sb-btn.active { background: #ea580c; color: #fff; border-color: #ea580c; }
    .sb-select { width: 100%; background: #1e293b; border: 1px solid #334155; color: #e2e8f0; border-radius: .375rem; padding: .35rem .6rem; font-size: .75rem; outline: none; }
    .sb-select:focus { border-color: #ea580c; }
    .sb-input { width: 100%; background: #1e293b; border: 1px solid #334155; color: #e2e8f0; border-radius: .375rem; padding: .35rem .6rem; font-size: .75rem; outline: none; }
    .sb-input:focus { border-color: #ea580c; }
    .sb-section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; margin-bottom: .4rem; display: flex; align-items: center; gap: .4rem; }
</style>

<main class="content-area content-scroll">
    <!-- Toast Notification Container Standar SIATEK -->
    <div id="toastContainer"></div>

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Surat Jurusan</h1>
                    <p class="text-xs text-slate-500">Editor custom langsung di lembar A4 dengan toolbar samping yang fleksibel, nomor otomatis, dan cetak resmi.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="btnCatatEksternal" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-slate-300">
                    <i class="fas fa-file-arrow-up text-orange-500"></i>Catat Surat Eksternal
                </button>
                <button type="button" id="btnBuatSurat" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-orange-600">
                    <i class="fas fa-plus"></i>Buat Surat (Editor)
                </button>
            </div>
        </div>
    </section>

    <!-- ===== Banner Informasi Nomor Surat Terakhir ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-200 bg-gradient-to-r from-amber-50/80 via-orange-50/50 to-white p-3 shadow-sm text-xs" id="boxNomorTerakhir">
            <div class="flex items-center gap-3 min-w-0">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-500 text-white shadow-sm text-sm font-bold">
                    <i class="fas fa-hashtag"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-bold text-slate-800 text-sm font-mono tracking-tight" id="lblNomorTerakhir">
                            <?= !empty($infoNomorTerakhir['nomor']) ? htmlspecialchars($infoNomorTerakhir['nomor']) : 'Belum Ada Surat Tahun Ini' ?>
                        </span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                            Terakhir Dipakai
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-0.5">
                        <span><i class="fas fa-calendar-day mr-1 text-slate-400"></i><span id="lblTglTerakhir"><?= !empty($infoNomorTerakhir['tanggal']) ? (function_exists('tgl_indo') ? tgl_indo($infoNomorTerakhir['tanggal']) : $infoNomorTerakhir['tanggal']) : '-' ?></span></span>
                        <span class="line-clamp-1"><i class="fas fa-file-signature mr-1 text-slate-400"></i><span id="lblKetTerakhir" class="italic"><?= !empty($infoNomorTerakhir['keterangan']) ? htmlspecialchars($infoNomorTerakhir['keterangan']) : 'Belum ada catatan' ?></span></span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2.5 shrink-0 ml-auto">
                <div class="text-right">
                    <span class="block text-[9px] text-slate-400 font-semibold uppercase tracking-wider">Urutan Terakhir:</span>
                    <span class="font-mono font-bold text-slate-700 text-xs" id="lblUrutanTerakhir">
                        <?= !empty($infoNomorTerakhir['urutan']) ? sprintf('%03d', (int)$infoNomorTerakhir['urutan']) : '000' ?>
                    </span>
                </div>
                <div class="h-6 w-[1px] bg-amber-200 mx-0.5"></div>
                <div class="text-left bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg">
                    <span class="block text-[9px] text-emerald-600 font-semibold uppercase tracking-wider">Tersedia Berikutnya:</span>
                    <span class="font-mono font-bold text-emerald-700 text-xs" id="lblUrutanBerikutnya">
                        <?= sprintf('%03d', (!empty($infoNomorTerakhir['urutan']) ? ((int)$infoNomorTerakhir['urutan'] + 1) : 1)) ?>
                    </span>
                </div>
                <button type="button" id="btnRefreshNomorTerakhir" class="h-7 w-7 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition ml-1" title="Perbarui Data Nomor Terakhir">
                    <i class="fas fa-sync-alt text-xs"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- ===== Toolbar & Filter ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari perihal, nomor surat, penerima..."
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <select id="fKategori" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Kategori</option>
                <option value="Undangan">Undangan</option>
                <option value="Surat Keterangan">Surat Keterangan</option>
                <option value="Surat Pemberitahuan">Surat Pemberitahuan</option>
                <option value="Surat Tugas">Surat Tugas</option>
                <option value="Surat Pengantar">Surat Pengantar</option>
                <option value="Lainnya">Lainnya</option>
            </select>
            <select id="fTtd" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Penandatangan</option>
                <option value="kajur">Ketua Jurusan</option>
                <option value="sekjur">Sekretaris Jurusan</option>
                <option value="kaprodi">Ketua Prodi</option>
            </select>
            <select id="fKop" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Format Kop</option>
                <option value="1">Pakai Kop</option>
                <option value="0">Tanpa Kop</option>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">
                <i class="fas fa-times mr-1"></i>Reset
            </button>
        </div>
    </section>

    <!-- ===== Ringkasan ===== -->
    <section class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs text-slate-500" id="jmlInfo">Menampilkan <b class="text-slate-800">0</b> surat</p>
        <p class="text-xs text-slate-400"><i class="fas fa-feather-pointed mr-1 text-orange-500"></i>Klik ikon <i class="fas fa-pen mx-1 text-sky-600"></i>untuk edit langsung di lembar A4, atau ikon <i class="fas fa-print mx-1 text-emerald-600"></i>untuk cetak.</p>
    </section>

    <!-- ===== Tabel Surat ===== -->
    <section class="mb-8">
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="pin-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nomor Surat</th>
                            <th>Tanggal</th>
                            <th>Perihal</th>
                            <th>Tujuan / Penerima</th>
                            <th>Penandatangan</th>
                            <th class="text-center">Kop / Kertas</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="suratBody">
                        <!-- Diisi via AJAX -->
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

<!-- ===================================================================================== -->
<!-- ===== MODAL WORD CANVAS: LEMBAR A4 POLOS BEBAS EDIT SEPERTI MICROSOFT WORD ===== -->
<!-- ===================================================================================== -->
<div class="canvas-overlay" id="canvasModal" role="dialog" aria-modal="true">
    <div class="flex flex-col w-full h-full bg-slate-900 overflow-hidden">

        <!-- ===== BARIS 1: TOP APPLICATION BAR (DOKUMEN TITLE & ACTIONS) ===== -->
        <header class="h-14 shrink-0 bg-slate-950 border-b border-slate-800 px-4 flex items-center justify-between gap-3 text-slate-200 z-40">
            <!-- Sisi Kiri: Ikon & Judul / Perihal Dokumen -->
            <div class="flex items-center gap-3 flex-1 min-w-0 max-w-2xl">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-white text-base font-bold shadow">
                    <i class="fas fa-file-lines"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <input type="text" id="swPerihal" class="w-full bg-transparent border-b border-transparent hover:border-slate-700 focus:border-orange-500 text-white font-semibold text-sm px-1 py-0.5 outline-none truncate transition placeholder:text-slate-500" placeholder="Ketik Judul / Perihal Surat di sini...">
                    <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-0.5">
                        <span id="canvasTitle" class="truncate font-medium text-slate-300">Surat Baru (Lembar Polos)</span>
                    </div>
                </div>
            </div>

            <!-- Sisi Tengah: Quick Status Halaman -->
            <div class="hidden sm:flex items-center gap-2">
                <span id="pageCountDisplay" class="text-xs text-sky-400 font-bold bg-slate-900 px-3 py-1 rounded-full border border-slate-800 shadow-inner flex items-center gap-1.5">
                    <i class="fas fa-layer-group text-[10px]"></i> 1 Halaman
                </span>
            </div>

            <!-- Sisi Kanan: Tombol Aksi Simpan, Cetak, Tutup -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" id="btnSalinTemplateCanvas" class="hidden py-2 px-3 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white shadow transition flex items-center gap-1.5" title="Jadikan dokumen ini sebagai Surat Baru (Nomor urut & tanggal baru otomatis)">
                    <i class="fas fa-copy"></i> <span>Duplikat Template</span>
                </button>
                <button type="button" id="btnSimpanCanvas" class="py-2 px-3.5 text-xs font-bold rounded-lg bg-orange-500 hover:bg-orange-600 text-white shadow transition flex items-center gap-1.5">
                    <i class="fas fa-save"></i> <span>Simpan</span>
                </button>
                <button type="button" id="btnCetakCanvas" class="py-2 px-3.5 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow transition flex items-center gap-1.5" title="Cetak Dokumen Resmi ke Printer / Simpan PDF">
                    <i class="fas fa-print"></i> <span id="labelTombolCetak">Cetak Surat</span>
                </button>
                <div class="h-6 w-[1px] bg-slate-800 mx-1"></div>
                <button type="button" data-canvas-close class="h-9 w-9 flex items-center justify-center rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition text-lg font-bold" title="Tutup Editor">
                    &times;
                </button>
            </div>
        </header>

        <!-- ===== BARIS 2: WORD RIBBON TOOLBAR (FORMATTING ALIGNMENT, FONT, TABEL, DSB) ===== -->
        <header class="desk-top-ribbon shrink-0" id="deskTopRibbon">
            <div id="fallbackRibbon" class="flex flex-wrap items-center gap-1.5 w-full text-xs">
                <!-- Font & Size -->
                <select id="selFontFamily" class="sb-select !w-36 !py-1">
                    <option value="'Times New Roman', Times, serif" selected>Times New Roman</option>
                    <option value="Arial, Helvetica, sans-serif">Arial</option>
                    <option value="Calibri, Candara, Segoe, sans-serif">Calibri</option>
                    <option value="Georgia, serif">Georgia</option>
                    <option value="'Courier New', Courier, monospace">Courier New</option>
                    <option value="Verdana, Geneva, sans-serif">Verdana</option>
                </select>
                <select id="selFontSize" class="sb-select !w-24 !py-1">
                    <option value="10pt">10pt</option>
                    <option value="11pt">11pt</option>
                    <option value="12pt" selected>12pt (Resmi)</option>
                    <option value="14pt">14pt (Judul)</option>
                    <option value="16pt">16pt</option>
                    <option value="18pt">18pt (Besar)</option>
                    <option value="24pt">24pt</option>
                </select>

                <div class="h-5 w-[1px] bg-slate-700 mx-0.5"></div>

                <!-- Format B, I, U, S -->
                <button type="button" class="sb-btn" data-cmd="bold" title="Tebal (Ctrl+B)"><i class="fas fa-bold"></i></button>
                <button type="button" class="sb-btn" data-cmd="italic" title="Miring (Ctrl+I)"><i class="fas fa-italic"></i></button>
                <button type="button" class="sb-btn" data-cmd="underline" title="Garis Bawah (Ctrl+U)"><i class="fas fa-underline"></i></button>
                <button type="button" class="sb-btn" data-cmd="strikeThrough" title="Coret"><i class="fas fa-strikethrough"></i></button>

                <div class="h-5 w-[1px] bg-slate-700 mx-0.5"></div>

                <!-- Warna Teks & Highlight -->
                <label class="sb-btn cursor-pointer relative" title="Warna Teks">
                    <i class="fas fa-font text-orange-400"></i>
                    <input type="color" id="nativeForeColor" value="#000000" class="opacity-0 absolute inset-0 cursor-pointer w-full h-full">
                </label>
                <label class="sb-btn cursor-pointer relative" title="Highlight Warna Latar">
                    <i class="fas fa-highlighter text-yellow-400"></i>
                    <input type="color" id="nativeBackColor" value="#ffff00" class="opacity-0 absolute inset-0 cursor-pointer w-full h-full">
                </label>

                <div class="h-5 w-[1px] bg-slate-700 mx-0.5"></div>

                <!-- Alignment -->
                <button type="button" class="sb-btn" data-cmd="justifyLeft" title="Rata Kiri"><i class="fas fa-align-left"></i></button>
                <button type="button" class="sb-btn" data-cmd="justifyCenter" title="Rata Tengah"><i class="fas fa-align-center"></i></button>
                <button type="button" class="sb-btn" data-cmd="justifyRight" title="Rata Kanan"><i class="fas fa-align-right"></i></button>
                <button type="button" class="sb-btn" data-cmd="justifyFull" title="Rata Kanan Kiri (Justify)"><i class="fas fa-align-justify"></i></button>

                <div class="h-5 w-[1px] bg-slate-700 mx-0.5"></div>

                <!-- Lists & Indent & Tabulasi -->
                <button type="button" class="sb-btn" data-cmd="insertOrderedList" title="Penomoran (1, 2, 3...)"><i class="fas fa-list-ol"></i></button>
                <button type="button" class="sb-btn" data-cmd="insertUnorderedList" title="Daftar Poin (Bullets)"><i class="fas fa-list-ul"></i></button>
                <button type="button" class="sb-btn" data-cmd="outdent" title="Kurangi Indentasi (Shift+Tab)"><i class="fas fa-outdent"></i></button>
                <button type="button" class="sb-btn" data-cmd="indent" title="Tambah Indentasi"><i class="fas fa-indent"></i></button>
                <button type="button" class="sb-btn !w-auto !px-2 text-[11px] gap-1" id="btnInsertTab" title="Sisipkan Tabulasi / Tab Stop (Tombol Tab)"><i class="fas fa-arrow-right-to-bracket text-sky-400"></i><span>Tab</span></button>

                <div class="h-5 w-[1px] bg-slate-700 mx-0.5"></div>

                <!-- Spasi Baris & Paragraf (Word Style) -->
                <select id="selLineHeight" class="sb-select !w-24 !py-1" title="Spasi Baris (Line Spacing)">
                    <option value="1.0">1.0</option>
                    <option value="1.15">1.15</option>
                    <option value="1.35" selected>1.35</option>
                    <option value="1.5">1.5</option>
                    <option value="2.0">2.0</option>
                </select>

                <!-- Tombol Add / Remove Space Before Paragraph (Word) -->
                <button type="button" class="sb-btn !w-auto !px-2.5 text-[11px] gap-1.5" id="btnSpaceBefore" title="Tambah / Hapus Spasi Sebelum Paragraf (Add Space Before Paragraph)">
                    <i class="fas fa-arrow-up text-sky-400"></i>
                    <span id="labelSpaceBefore">Space Before</span>
                </button>

                <!-- Tombol Add / Remove Space After Paragraph (Word) -->
                <button type="button" class="sb-btn !w-auto !px-2.5 text-[11px] gap-1.5" id="btnSpaceAfter" title="Tambah / Hapus Spasi Setelah Paragraf (Add Space After Paragraph)">
                    <i class="fas fa-arrow-down text-orange-400"></i>
                    <span id="labelSpaceAfter">Space After</span>
                </button>

                <button type="button" id="btnInsertHr" class="sb-btn" title="Garis Pembatas Horizontal"><i class="fas fa-minus"></i></button>

                <div class="h-5 w-[1px] bg-slate-700 mx-0.5"></div>

                <!-- Sisipkan Tabel & Gambar -->
                <button type="button" id="btnQuickTable" class="sb-btn !w-auto !px-2 text-[11px] gap-1" title="Sisipkan Tabel di Kursor">
                    <i class="fas fa-table text-orange-400"></i><span>Tabel</span>
                </button>
                <label class="sb-btn !w-auto !px-2 text-[11px] gap-1 cursor-pointer" title="Sisipkan Gambar di Kursor">
                    <i class="fas fa-image text-orange-400"></i><span>Gambar</span>
                    <input type="file" id="inputGambarCanvas" accept="image/*" class="hidden">
                </label>

                <div class="h-5 w-[1px] bg-slate-700 mx-0.5"></div>

                <!-- Tambah Halaman Baru A4 / F4 (Word-like Multi-Page) -->
                <button type="button" id="btnTambahHalaman" class="sb-btn !w-auto !px-2.5 text-[11px] gap-1.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold shadow-sm border-blue-500" title="Tambah Halaman Baru (Shortcut: Ctrl+Enter)">
                    <i class="fas fa-file-circle-plus"></i><span>+ Halaman Baru</span>
                </button>

                <div class="h-5 w-[1px] bg-slate-700 mx-0.5"></div>

                <!-- Undo & Redo -->
                <button type="button" class="sb-btn" data-cmd="undo" title="Urungkan (Undo)"><i class="fas fa-rotate-left"></i></button>
                <button type="button" class="sb-btn" data-cmd="redo" title="Ulangi (Redo)"><i class="fas fa-rotate-right"></i></button>
                <button type="button" class="sb-btn" data-cmd="removeFormat" title="Hapus Format"><i class="fas fa-remove-format"></i></button>
            </div>
        </header>

        <!-- ===== BARIS 3: AREA UTAMA (MEJA KERJA KERTAS + SIDEBAR PENGATURAN DI SAMPING KANAN) ===== -->
        <div class="flex-1 flex overflow-hidden min-h-0 relative">
            <!-- MEJA KERJA A4 (KERTAS 100% POLOS / KOSONG BEBAS DIKETIK, MULTI-HALAMAN WORD) -->
            <main class="a4-desk flex-1 overflow-y-auto" id="deskScroll">
                <input type="hidden" id="cvId" value="">

                <!-- CONTAINER LEMBAR-LEMBAR HALAMAN A4 / F4 -->
                <div id="pagesContainer"></div>
            </main>

            <!-- SIDEBAR PENGATURAN DOKUMEN DI SAMPING KANAN (RAPI, TIDAK PADAT DI ATAS) -->
            <aside class="w-80 shrink-0 bg-slate-950 border-l border-slate-800 flex flex-col h-full overflow-hidden z-20 shadow-xl" id="docSidebar">
                <div class="px-4 py-3 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                        <i class="fas fa-sliders text-orange-400"></i> Pengaturan Surat
                    </span>
                    <span class="text-[10px] text-slate-500 font-mono">SIATEK v2</span>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar text-xs">
                    <!-- 1. KERTAS & KOP SURAT -->
                    <div class="bg-slate-900/90 rounded-xl border border-slate-800 p-3 space-y-3">
                        <div class="sb-section-title">
                            <i class="fas fa-file-lines text-orange-400"></i> Kertas & Kop Surat
                        </div>

                        <!-- Ukuran Kertas -->
                        <div>
                            <label class="block text-[11px] text-slate-400 font-semibold mb-1">Ukuran Kertas:</label>
                            <select id="swUkuranKertas" class="sb-select font-medium">
                                <option value="A4" selected>A4 (210 &times; 297 mm)</option>
                                <option value="F4">F4 / Folio (215 &times; 330 mm)</option>
                            </select>
                        </div>

                        <!-- Toggle Kop Resmi -->
                        <div class="pt-1">
                            <label class="flex items-center justify-between p-2 rounded-lg bg-slate-800/80 border border-slate-700/80 cursor-pointer hover:bg-slate-800 transition">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-heading text-sky-400"></i>
                                    <div>
                                        <div class="text-xs font-semibold text-slate-200">Kop Resmi UNG</div>
                                        <div class="text-[10px] text-slate-400">Tampilkan kop jurusan di hal. 1</div>
                                    </div>
                                </div>
                                <input type="checkbox" id="swPakaiKop" class="rounded text-orange-500 focus:ring-0 w-4 h-4 cursor-pointer" checked>
                            </label>
                        </div>
                    </div>

                    <!-- 2. IDENTITAS & NOMOR SURAT -->
                    <div class="bg-slate-900/90 rounded-xl border border-slate-800 p-3 space-y-3">
                        <div class="sb-section-title">
                            <i class="fas fa-hashtag text-orange-400"></i> Nomor & Kategori
                        </div>

                        <!-- Kategori Dokumen -->
                        <div>
                            <label class="block text-[11px] text-slate-400 font-semibold mb-1">Kategori Dokumen:</label>
                            <select id="swKategori" class="sb-select font-medium">
                                <option value="Undangan">Undangan</option>
                                <option value="Surat Keterangan">Surat Keterangan</option>
                                <option value="Surat Pemberitahuan">Surat Pemberitahuan</option>
                                <option value="Surat Tugas">Surat Tugas</option>
                                <option value="Surat Pengantar">Surat Pengantar</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <!-- Format Nomor Surat (Dari tabel format_nomor_surat) -->
                        <div>
                            <label class="block text-[11px] text-slate-400 font-semibold mb-1">
                                <i class="fas fa-list-ol text-orange-400 mr-1"></i>Pilih Format Nomor:
                            </label>
                            <select id="swPilihFormatNomor" class="sb-select font-medium text-[11px]">
                                <option value="">-- Format Otomatis Sesuai Kategori --</option>
                                <?php foreach ($daftarFormatNomor as $dfn): ?>
                                <option value="<?= $dfn['id'] ?>" data-fmt="<?= htmlspecialchars($dfn['format_nomor']) ?>">
                                    <?= htmlspecialchars(trim($dfn['nama_surat'])) ?> (<?= htmlspecialchars(trim($dfn['format_nomor'])) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Nomor Surat -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[11px] text-slate-400 font-semibold">Nomor Surat:</label>
                                <button type="button" id="btnTarikNomor" class="text-[10px] text-orange-400 hover:text-orange-300 font-bold flex items-center gap-1 transition" title="Tarik nomor urut berikutnya (+1 tahun ini) dari sistem">
                                    <i class="fas fa-sync-alt"></i> Auto No (+1)
                                </button>
                            </div>
                            <input type="text" id="swNomorSurat" class="sb-input font-mono text-xs" placeholder="Ketik / tarik nomor...">
                            <div class="mt-1 flex items-center justify-between text-[10px] text-slate-400">
                                <span id="hintNomorTerakhir">No. Terakhir: <strong class="text-slate-300 font-mono"><?= !empty($infoNomorTerakhir['nomor']) ? htmlspecialchars($infoNomorTerakhir['nomor']) : '-' ?></strong></span>
                                <button type="button" id="btnSisipNomor" class="px-2 py-0.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded text-[10px] font-medium border border-slate-700 transition flex items-center gap-1">
                                    <i class="fas fa-plus text-[8px] text-orange-400"></i> Sisip ke Kursor
                                </button>
                            </div>
                        </div>

                        <!-- Tanggal Surat & Opsi Cetak di Surat -->
                        <div class="pt-1 border-t border-slate-800/80">
                            <label class="block text-[11px] text-slate-400 font-semibold mb-1">Tanggal Surat:</label>
                            <input type="date" id="swTanggalSurat" class="sb-input" value="<?= date('Y-m-d') ?>">

                            <!-- Checkbox Tanggal Digunakan di Dalam Surat -->
                            <div class="mt-2.5 p-2 rounded-lg bg-slate-800/80 border border-slate-700/80 space-y-2">
                                <label class="flex items-start gap-2 cursor-pointer select-none">
                                    <input type="checkbox" id="swPakaiTanggalHeader" class="rounded text-orange-500 focus:ring-0 w-4 h-4 mt-0.5 cursor-pointer">
                                    <div>
                                        <div class="text-[11px] font-semibold text-slate-200">Gunakan Tanggal di Bawah Kop</div>
                                        <div class="text-[10px] text-slate-400 leading-tight">Posisi di bawah kop sebelah kanan: <span id="previewTglKanan" class="text-sky-300 font-medium">Gorontalo, <?= function_exists('tgl_indo') ? tgl_indo(date('Y-m-d')) : date('d F Y') ?></span></div>
                                    </div>
                                </label>

                                <button type="button" id="btnSisipTanggalKursor" class="w-full py-1 px-2 bg-slate-900 hover:bg-slate-700 text-slate-300 hover:text-white rounded text-[10px] font-medium border border-slate-700 transition flex items-center justify-center gap-1" title="Sisipkan teks tanggal langsung ke posisi kursor saat ini">
                                    <i class="fas fa-calendar-day text-orange-400"></i> Sisip Tanggal ke Posisi Kursor
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 3. PENANDATANGAN (TTD PEJABAT) -->
                    <div class="bg-slate-900/90 rounded-xl border border-slate-800 p-3 space-y-3">
                        <div class="sb-section-title">
                            <i class="fas fa-file-signature text-orange-400"></i> Tanda Tangan Pejabat
                        </div>

                        <div>
                            <label class="block text-[11px] text-slate-400 font-semibold mb-1">Pilih Jabatan:</label>
                            <select id="swTtdJenis" class="sb-select font-medium">
                                <option value="kajur" selected>Ketua Jurusan</option>
                                <option value="sekjur">Sekretaris Jurusan</option>
                                <option value="kaprodi">Ketua Program Studi</option>
                            </select>
                        </div>

                        <div id="wrapTtdProdi" class="hidden">
                            <label class="block text-[11px] text-slate-400 font-semibold mb-1">Pilih Program Studi:</label>
                            <select id="swTtdProdi" class="sb-select font-medium">
                                <?php foreach ($daftarProdi as $pr): 
                                    $namaProdiBersih = preg_replace('/^(S1|D3|D4|S2|S3)\s+/i', '', trim($pr['nama']));
                                    $labelProdi = ($pr['jenjang'] ? trim($pr['jenjang']) . ' ' : '') . $namaProdiBersih;
                                ?>
                                <option value="<?= htmlspecialchars($pr['kode']) ?>" data-nama="<?= htmlspecialchars($namaProdiBersih) ?>"><?= htmlspecialchars($labelProdi) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800/80 text-[11px] space-y-1">
                            <div class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Preview Pejabat:</div>
                            <div id="previewTtdNama" class="text-white font-semibold truncate">Nama Pejabat</div>
                            <div id="previewTtdNip" class="text-slate-400 font-mono text-[10px]">NIP. -</div>
                        </div>

                        <button type="button" id="btnSisipTtd" class="w-full py-2 bg-orange-600 hover:bg-orange-500 text-white font-bold rounded-lg transition flex items-center justify-center gap-1.5 shadow-sm text-xs">
                            <i class="fas fa-file-signature"></i> Sisipkan TTD ke Posisi Kursor
                        </button>
                    </div>

                    <!-- KONTROL SISIPKAN TABEL KUSTOM -->
                    <div class="bg-slate-900/90 rounded-xl border border-slate-800 p-3 space-y-2.5">
                        <div class="sb-section-title">
                            <i class="fas fa-table text-orange-400"></i> Sisipkan Tabel
                        </div>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Tentukan jumlah kolom & baris, lalu klik tombol di bawah untuk memasukkan tabel ke posisi kursor:
                        </p>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] text-slate-400 font-semibold mb-1">Kolom:</label>
                                <input type="number" id="tblJmlKolom" class="sb-input text-center font-bold !py-1 text-slate-100" value="3" min="1" max="15">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-400 font-semibold mb-1">Baris:</label>
                                <input type="number" id="tblJmlBaris" class="sb-input text-center font-bold !py-1 text-slate-100" value="3" min="1" max="50">
                            </div>
                        </div>
                        <button type="button" id="btnSisipTabelCustom" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-sky-400 hover:text-sky-300 font-semibold rounded-lg border border-slate-700 transition flex items-center justify-center gap-1.5 text-xs shadow-sm">
                            <i class="fas fa-plus"></i> + Sisipkan Tabel ke Kursor
                        </button>
                    </div>

                    <!-- 4. KONTROL LEMBAR HALAMAN -->
                    <div class="bg-slate-900/90 rounded-xl border border-slate-800 p-3 space-y-2">
                        <div class="sb-section-title">
                            <i class="fas fa-copy text-orange-400"></i> Kelola Halaman
                        </div>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Ketik bebas seperti Microsoft Word. Saat baris mencapai batas bawah kertas, otomatis lanjut ke halaman berikutnya (atau tekan <b>Ctrl + Enter</b>).
                        </p>
                        <button type="button" onclick="$('#btnTambahHalaman').trigger('click')" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold rounded-lg border border-slate-700 transition flex items-center justify-center gap-1.5 text-xs">
                            <i class="fas fa-plus text-sky-400"></i> + Tambah Lembar Baru
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<!-- ===== MODAL CATAT / UNGGAH SURAT EKSTERNAL ===== -->
<div class="modal-overlay" id="modalCatatEksternal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-200">
        <!-- Header Modal -->
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
            <h6 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fas fa-file-arrow-up text-orange-500"></i>
                <span id="titleModalEksternal">Catat Surat Eksternal</span>
            </h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none closeModalEksternal" data-modal-close>&times;</button>
        </div>

        <form id="formSuratEksternal" enctype="multipart/form-data">
            <input type="hidden" id="extId" name="id" value="">
            
            <div class="p-5 space-y-3.5 max-h-[72vh] overflow-y-auto">
                <!-- Nomor Surat -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="extNomorSurat" class="mb-1 block text-xs font-bold text-slate-700">Nomor Surat <span class="text-rose-500">*</span></label>
                        <button type="button" id="btnTarikNomorEksternal" class="text-[11px] text-orange-600 hover:text-orange-700 font-bold flex items-center gap-1 transition">
                            <i class="fas fa-sync-alt"></i> Tarik No (+1)
                        </button>
                    </div>
                    <input type="text" id="extNomorSurat" name="nomor_surat" required placeholder="Contoh: 026/UN47.B5.5/TD.06/2026"
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-mono outline-none focus:border-orange-400 focus:bg-white transition">
                </div>

                <!-- Perihal -->
                <div>
                    <label for="extPerihal" class="mb-1 block text-xs font-bold text-slate-700">Perihal / Hal Surat <span class="text-rose-500">*</span></label>
                    <input type="text" id="extPerihal" name="perihal" required placeholder="Tuliskan perihal surat..."
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white transition">
                </div>

                <!-- Kategori & Tanggal -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="extKategori" class="mb-1 block text-xs font-bold text-slate-700">Kategori Surat</label>
                        <select id="extKategori" name="kategori"
                                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white transition">
                            <option value="Kerja Sama / MoU">Kerja Sama / MoU</option>
                            <option value="Surat Tugas">Surat Tugas</option>
                            <option value="Surat Keterangan">Surat Keterangan</option>
                            <option value="Surat Pengantar">Surat Pengantar</option>
                            <option value="Undangan">Undangan</option>
                            <option value="Surat Keputusan (SK)">Surat Keputusan (SK)</option>
                            <option value="Lainnya" selected>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label for="extTanggalSurat" class="mb-1 block text-xs font-bold text-slate-700">Tanggal Surat <span class="text-rose-500">*</span></label>
                        <input type="date" id="extTanggalSurat" name="tanggal_surat" value="<?= date('Y-m-d') ?>" required
                               class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white transition">
                    </div>
                </div>

                <!-- Tujuan / Instansi Penerima -->
                <div>
                    <label for="extTujuan" class="mb-1 block text-xs font-bold text-slate-700">Tujuan / Instansi Penerima</label>
                    <input type="text" id="extTujuan" name="tujuan" placeholder="Nama instansi atau pihak penerima (opsional)"
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white transition">
                </div>

                <!-- Unggah Dokumen -->
                <div>
                    <label for="extFileDokumen" class="mb-1 block text-xs font-bold text-slate-700">
                        <i class="fas fa-paperclip mr-1 text-orange-500"></i>Berkas Dokumen
                    </label>
                    <input type="file" id="extFileDokumen" name="file_dokumen" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-500 border border-slate-200 rounded-lg p-2 bg-slate-50 focus:bg-white focus:outline-none transition">
                    <p class="mt-1 text-[11px] text-slate-400">Format berkas: PDF, DOCX, DOC, JPG, PNG (Maks. 10 MB)</p>
                    <div id="extFileCurrentWrap" class="hidden mt-2 p-2.5 rounded-lg bg-orange-50 border border-orange-200 flex items-center justify-between text-xs">
                        <span class="text-slate-700 flex items-center gap-1.5"><i class="fas fa-file-lines text-orange-600"></i><span id="extNamaFileCurrent" class="font-medium truncate max-w-[220px]"></span></span>
                        <a href="#" id="extFileCurrentLink" target="_blank" class="font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1"><i class="fas fa-eye"></i>Lihat Berkas</a>
                    </div>
                </div>

                <!-- Keterangan / Catatan Tambahan -->
                <div>
                    <label for="extKeterangan" class="mb-1 block text-xs font-bold text-slate-700">Keterangan / Catatan</label>
                    <textarea id="extKeterangan" name="keterangan" rows="2" placeholder="Tuliskan catatan tambahan bila ada..."
                              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white transition"></textarea>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="flex items-center justify-end gap-2 border-t border-slate-100 bg-slate-50 px-5 py-3">
                <button type="button" class="closeModalEksternal rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition" data-modal-close>
                    Batal
                </button>
                <button type="submit" id="btnSimpanEksternal" class="rounded-lg bg-orange-500 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-orange-600 transition flex items-center gap-1.5">
                    <i class="fas fa-save"></i> <span>Simpan Surat Eksternal</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php
// Single Source of Truth: Render Kop Surat langsung dari komponen kop-surat.php
$kopFilePath = dirname(__DIR__, 4) . '/surat/content-surat/kop-surat.php';
$kopHtmlContent = '';
if (file_exists($kopFilePath)) {
    ob_start();
    include $kopFilePath;
    $kopHtmlContent = ob_get_clean();
    // Hilangkan komentar HTML pembuka agar rapi
    $kopHtmlContent = preg_replace('/<!--.*?-->/s', '', $kopHtmlContent);
    $kopHtmlContent = trim($kopHtmlContent);
}
?>
<script>
var GLOBAL_KOP_HTML = <?= json_encode($kopHtmlContent) ?>;
(function() {
    var PROSES_URL = '<?= base_url() ?>/administrator/pages/jurusan/surat/proses.php';
    var rowsData = [];
    var halaman = 1;
    var PER_HALAMAN = 10;
    var isEditMode = false;
    var activeTableTarget = null;
    var currentPejabat = { dosen_id: '', nama: 'Nama Pejabat', nip: '-', jabatan: 'Ketua Jurusan' };
    var savedRange = null;

    // ================= TOAST NOTIFIKASI STANDAR SIATEK =================
    function toast(pesan, tipe) {
        tipe = tipe || 'info';
        var container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            document.body.appendChild(container);
        }
        var item = document.createElement('div');
        item.className = 'toast-item toast-' + tipe;
        var ikon = tipe === 'success' ? 'fa-circle-check' : (tipe === 'error' ? 'fa-circle-xmark' : 'fa-circle-info');
        item.innerHTML = '<i class="fas ' + ikon + ' toast-icon"></i><span class="leading-tight">' + pesan + '</span><button type="button" class="toast-close">&times;</button>';
        container.appendChild(item);
        requestAnimationFrame(function(){ item.classList.add('show'); });

        var dismiss = setTimeout(function(){
            item.classList.add('hide');
            setTimeout(function(){ item.remove(); }, 350);
        }, 4000);

        $(item).find('.toast-close').on('click', function() {
            clearTimeout(dismiss);
            item.classList.add('hide');
            setTimeout(function(){ item.remove(); }, 350);
        });
    }

    // Hindari native browser alert agar taat aturan UI aplikasi SIATEK
    window.alert = function(msg) {
        toast(msg, 'info');
    };

    // Format Tanggal Indonesia
    var NAMA_BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    function formatTglIndo(tglStr) {
        if (!tglStr) return '-';
        var p = tglStr.split('-');
        if (p.length !== 3) return tglStr;
        return parseInt(p[2], 10) + ' ' + NAMA_BULAN[parseInt(p[1], 10) - 1] + ' ' + p[0];
    }

    // Tanggal besok/minggu depan untuk template dinas
    function getTanggalFormat(offsetHari) {
        var d = new Date();
        d.setDate(d.getDate() + (offsetHari || 0));
        var yyyy = d.getFullYear();
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        var dd = String(d.getDate()).padStart(2, '0');
        return yyyy + '-' + mm + '-' + dd;
    }

    // Toggle Kop Surat pada Canvas
    function updateKopCanvas(pakai) {
        if (pakai) {
            $('#cvKop').show();
            $('#cvSpacerNoKop').hide();
        } else {
            $('#cvKop').hide();
            $('#cvSpacerNoKop').show();
        }
    }

    // Update Banner Tanggal di Bawah Kop Surat (Kanan)
    function updateTanggalHeaderCanvas() {
        var pakai = $('#swPakaiTanggalHeader').is(':checked');
        var tglVal = $('#swTanggalSurat').val();
        var tglTeks = 'Gorontalo, ' + formatTglIndo(tglVal);
        $('#previewTglKanan').text(tglTeks);

        if (pakai) {
            $('#cvTanggalWrap').show();
            $('#cvTanggalText').text(tglTeks);
        } else {
            $('#cvTanggalWrap').hide();
        }
    }

    // Load data pejabat dari DB
    function loadPejabat(jenis, prodi, callback) {
        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: {
                'ambil-data-pejabat': 1,
                'jenis': jenis,
                'prodi': prodi || ''
            },
            dataType: 'json',
            success: function(res) {
                if (res.status && res.data) {
                    currentPejabat = res.data;
                    $('#previewTtdNama').text(res.data.nama || 'Nama Pejabat');
                    $('#previewTtdNip').text('NIP. ' + (res.data.nip || '-'));
                    if (typeof callback === 'function') callback(res.data);
                }
            }
        });
    }

    // ================= MULTI-PAGE A4 WORD SYSTEM & NATIVE RICH TEXT ENGINE =================
    var currentActiveEditor = null;
    var savedRange = null;

    // ================= TOAST NOTIFIKASI STANDAR SIATEK =================
    function toast(pesan, tipe) {
        tipe = tipe || 'info';
        var container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            document.body.appendChild(container);
        }
        var item = document.createElement('div');
        item.className = 'toast-item toast-' + tipe;
        var ikon = tipe === 'success' ? 'fa-circle-check' : (tipe === 'error' ? 'fa-circle-xmark' : 'fa-circle-info');
        item.innerHTML = '<i class="fas ' + ikon + ' toast-icon"></i><span class="leading-tight">' + pesan + '</span><button type="button" class="toast-close">&times;</button>';
        container.appendChild(item);
        requestAnimationFrame(function(){ item.classList.add('show'); });

        var dismiss = setTimeout(function(){
            item.classList.add('hide');
            setTimeout(function(){ item.remove(); }, 350);
        }, 4000);

        $(item).find('.toast-close').on('click', function() {
            clearTimeout(dismiss);
            item.classList.add('hide');
            setTimeout(function(){ item.remove(); }, 350);
        });
    }

    // Hindari native browser alert agar taat aturan UI aplikasi SIATEK
    window.alert = function(msg) {
        toast(msg, 'info');
    };

    function getActiveEditor() {
        if (currentActiveEditor && document.body.contains(currentActiveEditor)) {
            return currentActiveEditor;
        }
        var firstEd = document.querySelector('.page-editor');
        return firstEd;
    }

    // ================= RICH TEXT ENGINE (TINYMCE 6 + FULL TABLE SUITE) =================
    function activateEditorRibbon(editor) {
        if (!editor) return;
        var container = (typeof editor.getContainer === 'function') ? editor.getContainer() : null;
        if (container) {
            $('#deskTopRibbon .tox-tinymce-inline').removeClass('active-ribbon');
            $(container).addClass('active-ribbon');
        }
    }

    // Helper Pembuat Template Tabel Standar Word
    function generateTableHtml(rows, cols) {
        var rCount = parseInt(rows) || 3;
        var cCount = parseInt(cols) || 3;
        if (rCount < 1) rCount = 1;
        if (cCount < 1) cCount = 1;

        var html = '<table class="border-grid" style="width: 100%; border-collapse: collapse; margin: 12px 0; font-family: \'Times New Roman\', Times, serif; font-size: 12pt;">';
        html += '<tbody>';
        for (var r = 0; r < rCount; r++) {
            html += '<tr>';
            for (var c = 0; c < cCount; c++) {
                var isHeader = (r === 0);
                var tag = isHeader ? 'th' : 'td';
                var bg = isHeader ? 'background: #f8fafc;' : '';
                var bold = isHeader ? 'font-weight: bold;' : '';
                var align = (c === 0 && cCount > 1) ? 'text-align: center; width: 45px;' : 'text-align: left;';
                var content = isHeader 
                    ? (c === 0 ? 'No.' : (c === 1 ? 'Uraian' : (c === 2 ? 'Keterangan' : 'Kolom ' + (c + 1))))
                    : (c === 0 && cCount > 1 ? r + '.' : '&nbsp;');
                html += '<' + tag + ' style="border: 1px solid #000000; padding: 6px 10px; height: 28px; ' + bg + bold + align + '">' + content + '</' + tag + '>';
            }
            html += '</tr>';
        }
        html += '</tbody></table><p><br></p>';
        return html;
    }

    function getTinyMceConfig(targetSelector) {
        return {
            selector: targetSelector || '.page-editor',
            inline: true,
            toolbar_persist: true,
            fixed_toolbar_container: '#deskTopRibbon',
            skin: 'oxide-dark',
            content_css: 'dark',
            menubar: 'edit insert view format table tools',
            menu: {
                edit: { title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall' },
                insert: { title: 'Sisipkan', items: 'image link table hr | charmap emoticons | insertdatetime' },
                view: { title: 'Tampilan', items: 'visualaid visualblocks' },
                format: { title: 'Format', items: 'bold italic underline strikethrough superscript subscript | styles blocks fontfamily fontsize align lineheight | forecolor backcolor | removeformat' },
                table: { title: 'Tabel', items: 'inserttable | cell row column | tableprops deletetable' },
                tools: { title: 'Alat', items: 'code wordcount' }
            },
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'wordcount'
            ],
            toolbar: [
                'undo redo | fontfamily fontsize | bold italic underline strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | lineheight',
                'bullist numlist outdent indent | quicktable table tableprops | image hr | removeformat code'
            ],
            font_family_formats: 'Times New Roman=times new roman,times,serif; Arial=arial,helvetica,sans-serif; Calibri=calibri,sans-serif; Georgia=georgia,serif; Courier New=courier new,courier,monospace; Verdana=verdana,sans-serif',
            font_size_formats: '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 24pt 36pt',
            line_height_formats: '1.0 1.15 1.25 1.35 1.5 2.0',
            table_toolbar: '',
            table_appearance_options: true,
            table_advtab: true,
            table_cell_advtab: true,
            table_row_advtab: true,
            table_resize_bars: true,
            table_default_attributes: {
                border: '1',
                class: 'border-grid'
            },
            table_default_styles: {
                'border-collapse': 'collapse',
                'width': '100%',
                'margin': '12px 0'
            },
            setup: function(editor) {
                // Tombol Cepat Sisipkan Tabel Standar (3x3) langsung di Toolbar Ribbon
                editor.ui.registry.addButton('quicktable', {
                    icon: 'table',
                    tooltip: 'Sisipkan Tabel Standar (3x3)',
                    text: 'Tabel (3x3)',
                    onAction: function() {
                        var tHtml = generateTableHtml(3, 3);
                        editor.insertContent(tHtml);
                    }
                });

                editor.on('init', function() {
                    $('#fallbackRibbon').hide();
                    var body = editor.getBody();
                    if (body) {
                        body.style.fontSize = '12pt';
                        body.style.fontFamily = "'Times New Roman', Times, serif";
                    }
                    if ($('#deskTopRibbon .tox-tinymce-inline.active-ribbon').length === 0) {
                        activateEditorRibbon(editor);
                    }
                });
                editor.on('focus', function() {
                    currentActiveEditor = editor.getBody();
                    activateEditorRibbon(editor);
                });
            }
        };
    }

    function initTinyMCE(targetSelector) {
        if (!window.tinymce) {
            $('#fallbackRibbon').show();
            return false;
        }

        try {
            var selector = targetSelector || '.page-editor';
            // Jika targetSelector berupa ID spesifik (misal penambahan lembar baru), inisialisasi hanya elemen tersebut
            if (targetSelector && targetSelector.startsWith('#')) {
                tinymce.init(getTinyMceConfig(selector));
                return true;
            }

            // Inisialisasi awal kanvas: bersihkan seluruh instance lama dan DOM container toolbar
            tinymce.remove();
            $('#deskTopRibbon .tox-tinymce-inline').remove();
            tinymce.init(getTinyMceConfig(selector));
            return true;
        } catch (e) {
            console.warn('TinyMCE initialization error, using fallback:', e);
            $('#fallbackRibbon').show();
            return false;
        }
    }

    // Simpan & Pulihkan Seleksi Kursor (Untuk Format Toolbar)
    function saveSelection() {
        var sel = window.getSelection();
        if (sel && sel.rangeCount > 0) {
            savedRange = sel.getRangeAt(0);
        }
    }

    function restoreSelection() {
        var ed = getActiveEditor();
        if (ed) {
            if (savedRange && ed.contains(savedRange.commonAncestorContainer)) {
                var sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(savedRange);
            } else {
                ed.focus();
            }
        }
    }

    // Helper normalisasi font-family
    function normalizeFontFamily(fontStr) {
        if (!fontStr) return "'Times New Roman', Times, serif";
        var f = fontStr.toLowerCase().replace(/['"]/g, '');
        if (f.indexOf('times') !== -1) return "'Times New Roman', Times, serif";
        if (f.indexOf('arial') !== -1) return "Arial, Helvetica, sans-serif";
        if (f.indexOf('calibri') !== -1) return "Calibri, Candara, Segoe, sans-serif";
        if (f.indexOf('georgia') !== -1) return "Georgia, serif";
        if (f.indexOf('courier') !== -1) return "'Courier New', Courier, monospace";
        if (f.indexOf('verdana') !== -1) return "Verdana, Geneva, sans-serif";
        return "'Times New Roman', Times, serif";
    }

    // Helper normalisasi font-size ke skala Word (10pt, 11pt, 12pt, 14pt, 16pt, 18pt, 24pt)
    function normalizeFontSize(sizeStr) {
        if (!sizeStr) return "12pt";
        var s = sizeStr.toString().toLowerCase().trim();
        if (s.indexOf('pt') !== -1) {
            var num = parseFloat(s);
            if (num <= 10.5) return "10pt";
            if (num <= 11.5) return "11pt";
            if (num <= 13) return "12pt";
            if (num <= 15) return "14pt";
            if (num <= 17) return "16pt";
            if (num <= 21) return "18pt";
            return "24pt";
        }
        if (s.indexOf('px') !== -1) {
            var px = parseFloat(s);
            var pt = px * 0.75;
            if (pt <= 10.5) return "10pt";
            if (pt <= 11.5) return "11pt";
            if (pt <= 13) return "12pt";
            if (pt <= 15) return "14pt";
            if (pt <= 17) return "16pt";
            if (pt <= 21) return "18pt";
            return "24pt";
        }
        return "12pt";
    }

    // Deteksi Format Aktif pada Posisi Kursor / Seleksi
    function getActiveFormatting() {
        var defaultFmt = { font: "'Times New Roman', Times, serif", size: "12pt" };
        var sel = window.getSelection();
        if (!sel || !sel.rangeCount) return defaultFmt;

        var ed = getActiveEditor();
        if (!ed) return defaultFmt;

        var range = sel.getRangeAt(0);
        var node = range.startContainer;
        if (node.nodeType === 3) node = node.parentNode;
        if (!node || !ed.contains(node)) return defaultFmt;

        var cur = node;
        var inlineFont = '';
        var inlineSize = '';
        while (cur && cur !== ed && cur !== document.body) {
            if (!inlineFont && cur.style && cur.style.fontFamily) {
                inlineFont = cur.style.fontFamily;
            }
            if (!inlineSize && cur.style && cur.style.fontSize) {
                inlineSize = cur.style.fontSize;
            }
            cur = cur.parentNode;
        }

        var comp = window.getComputedStyle(node);
        var effFont = inlineFont || comp.fontFamily;
        var effSize = inlineSize || comp.fontSize;

        return {
            font: normalizeFontFamily(effFont),
            size: normalizeFontSize(effSize)
        };
    }

    // Helper untuk mendeteksi elemen paragraf / blok teks yang disentuh kursor atau seleksi
    function getSelectedParagraphs() {
        var ed = getActiveEditor();
        if (!ed) return [];
        var sel = window.getSelection();
        if (!sel || !sel.rangeCount) return [];
        var range = sel.getRangeAt(0);
        if (!ed.contains(range.commonAncestorContainer)) return [];

        function isBlock(el) {
            if (!el || el.nodeType !== 1) return false;
            var t = el.tagName.toLowerCase();
            return t === 'p' || t === 'div' || t === 'li' || t === 'h1' || t === 'h2' || t === 'h3' || t === 'blockquote';
        }

        var startNode = range.startContainer;
        if (startNode.nodeType === 3) startNode = startNode.parentNode;
        var startBlock = startNode;
        while (startBlock && startBlock !== ed && !isBlock(startBlock)) {
            startBlock = startBlock.parentNode;
        }

        var endNode = range.endContainer;
        if (endNode.nodeType === 3) endNode = endNode.parentNode;
        var endBlock = endNode;
        while (endBlock && endBlock !== ed && !isBlock(endBlock)) {
            endBlock = endBlock.parentNode;
        }

        if (startBlock && startBlock !== ed && startBlock === endBlock) {
            return [startBlock];
        }

        var blocks = [];
        var allBlocks = ed.querySelectorAll('p, div, li, h1, h2, h3, blockquote');
        for (var i = 0; i < allBlocks.length; i++) {
            var b = allBlocks[i];
            var intersects = false;
            try {
                if (range.intersectsNode) {
                    intersects = range.intersectsNode(b);
                } else {
                    intersects = true;
                }
            } catch(e) {
                intersects = true;
            }
            if (intersects) {
                blocks.push(b);
            }
        }
        if (blocks.length === 0 && startBlock && startBlock !== ed) {
            blocks.push(startBlock);
        }
        return blocks;
    }

    // Sinkronkan Dropdown Toolbar dengan Format Posisi Kursor
    var toolbarSyncTimeout = null;
    function updateToolbarState() {
        if (window.tinymce && tinymce.activeEditor && !tinymce.activeEditor.isHidden()) return;
        if (document.activeElement && (document.activeElement.id === 'selFontFamily' || document.activeElement.id === 'selFontSize')) {
            return;
        }
        if (toolbarSyncTimeout) clearTimeout(toolbarSyncTimeout);
        toolbarSyncTimeout = setTimeout(function() {
            var ed = getActiveEditor();
            if (!ed || !$('#canvasModal').hasClass('show')) return;
            var sel = window.getSelection();
            if (!sel || !sel.rangeCount) return;
            var range = sel.getRangeAt(0);
            if (!ed.contains(range.commonAncestorContainer)) return;

            var fmt = getActiveFormatting();
            if (!document.activeElement || document.activeElement.id !== 'selFontFamily') {
                $('#selFontFamily').val(fmt.font);
            }
            if (!document.activeElement || document.activeElement.id !== 'selFontSize') {
                $('#selFontSize').val(fmt.size);
            }

            try {
                $('.sb-btn[data-cmd="bold"]').toggleClass('active bg-slate-700 text-sky-400', document.queryCommandState('bold'));
                $('.sb-btn[data-cmd="italic"]').toggleClass('active bg-slate-700 text-sky-400', document.queryCommandState('italic'));
                $('.sb-btn[data-cmd="underline"]').toggleClass('active bg-slate-700 text-sky-400', document.queryCommandState('underline'));
                $('.sb-btn[data-cmd="strikeThrough"]').toggleClass('active bg-slate-700 text-sky-400', document.queryCommandState('strikeThrough'));
            } catch(e) {}

            // Sinkronkan Status Spasi Paragraf (Space Before & Space After) seperti Microsoft Word
            try {
                var pList = getSelectedParagraphs();
                if (pList && pList.length > 0) {
                    var curP = pList[0];
                    var topVal = parseFloat(window.getComputedStyle(curP).marginTop || '0');
                    var hasBefore = (topVal > 3 || (curP.style.marginTop && parseFloat(curP.style.marginTop) > 0));

                    var botVal = parseFloat(window.getComputedStyle(curP).marginBottom || '0');
                    var hasAfter = (botVal >= 11 || (curP.style.marginBottom && parseFloat(curP.style.marginBottom) >= 8));

                    if (hasBefore) {
                        $('#btnSpaceBefore').addClass('active bg-slate-700 text-sky-400 border border-sky-500/50')
                            .attr('title', 'Hapus Spasi Sebelum Paragraf (Remove Space Before Paragraph)');
                        $('#labelSpaceBefore').text('Remove Space Before');
                    } else {
                        $('#btnSpaceBefore').removeClass('active bg-slate-700 text-sky-400 border border-sky-500/50')
                            .attr('title', 'Tambah Spasi Sebelum Paragraf (Add Space Before Paragraph)');
                        $('#labelSpaceBefore').text('Add Space Before');
                    }

                    if (hasAfter) {
                        $('#btnSpaceAfter').addClass('active bg-slate-700 text-orange-400 border border-orange-500/50')
                            .attr('title', 'Hapus Spasi Setelah Paragraf (Remove Space After Paragraph)');
                        $('#labelSpaceAfter').text('Remove Space After');
                    } else {
                        $('#btnSpaceAfter').removeClass('active bg-slate-700 text-orange-400 border border-orange-500/50')
                            .attr('title', 'Tambah Spasi Setelah Paragraf (Add Space After Paragraph)');
                        $('#labelSpaceAfter').text('Add Space After');
                    }

                    if (curP.style.lineHeight) {
                        $('#selLineHeight').val(curP.style.lineHeight);
                    }
                }
            } catch(e) {}
        }, 30);
    }

    function insertHtmlAtCursor(html) {
        var ed = getActiveEditor();
        if (!ed) return;
        restoreSelection();
        ed.focus();

        // Pastikan HTML yang disisipkan mewarisi styling aktif jika belum memiliki span, table, div, hr, atau img
        if (html.indexOf('<table') === -1 && html.indexOf('<div') === -1 && html.indexOf('<span') === -1 && html.indexOf('<img') === -1 && html.indexOf('<hr') === -1) {
            var curFmt = getActiveFormatting();
            var f = $('#selFontFamily').val() || curFmt.font || "'Times New Roman', Times, serif";
            var s = $('#selFontSize').val() || curFmt.size || "12pt";
            html = '<span style="font-family: ' + f + '; font-size: ' + s + ';">' + html + '</span>';
        }

        var sel = window.getSelection();
        var range = (sel && sel.rangeCount > 0) ? sel.getRangeAt(0) : null;

        // Jika tidak ada seleksi valid atau seleksi berada di luar editor aktif, posisikan kursor di akhir
        if (!range || !ed.contains(range.commonAncestorContainer)) {
            range = document.createRange();
            range.selectNodeContents(ed);
            range.collapse(false);
            if (sel) {
                sel.removeAllRanges();
                sel.addRange(range);
            }
        }

        // Cek apakah konten berupa elemen block (seperti table, div, blockquote, hr)
        var isBlock = /^\s*<(table|div|blockquote|hr)/i.test(html);
        var template = document.createElement('template');
        template.innerHTML = html.trim();
        var frag = template.content;
        var lastNode = frag.lastChild;

        if (isBlock) {
            // Temukan elemen blok level teratas yang merupakan direct child dari editor (misal <p>, <div>, <table>)
            var node = range.commonAncestorContainer;
            if (node.nodeType === 3) node = node.parentNode;
            var topBlock = node;
            while (topBlock && topBlock.parentNode && topBlock.parentNode !== ed) {
                topBlock = topBlock.parentNode;
            }

            if (topBlock && topBlock.parentNode === ed) {
                var blockText = (topBlock.textContent || '').replace(/\u200B/g, '').trim();
                var isEmptyBlock = (blockText === '' || topBlock.innerHTML === '<br>' || topBlock.innerHTML === '<b><br></b>' || topBlock.innerHTML === '');

                if (isEmptyBlock) {
                    // Blok kosong: gantikan langsung blok kosong ini dengan konten tabel
                    ed.insertBefore(frag, topBlock);
                    ed.removeChild(topBlock);
                } else {
                    // Blok ada isinya: sisipkan tabel persis SETELAH blok ini (bukan di dalam <p> atau <b>)
                    if (topBlock.nextSibling) {
                        ed.insertBefore(frag, topBlock.nextSibling);
                    } else {
                        ed.appendChild(frag);
                    }
                }

                var newRange = document.createRange();
                if (lastNode && lastNode.nodeType === 1 && lastNode.tagName.toLowerCase() === 'p') {
                    newRange.setStart(lastNode, 0);
                    newRange.collapse(true);
                } else if (lastNode) {
                    newRange.setStartAfter(lastNode);
                    newRange.collapse(true);
                } else {
                    newRange.selectNodeContents(ed);
                    newRange.collapse(false);
                }
                if (sel) {
                    sel.removeAllRanges();
                    sel.addRange(newRange);
                }
                saveSelection();
                return;
            }
        }

        // Standar Modern DOM Range Insertion
        range.deleteContents();
        range.insertNode(frag);

        if (lastNode) {
            var newRange = document.createRange();
            if (lastNode.nodeType === 1 && lastNode.tagName.toLowerCase() === 'p') {
                newRange.setStart(lastNode, 0);
                newRange.collapse(true);
            } else {
                newRange.setStartAfter(lastNode);
                newRange.collapse(true);
            }
            if (sel) {
                sel.removeAllRanges();
                sel.addRange(newRange);
            }
        }
        saveSelection();
    }

    function insertCanvasContent(html) {
        if (window.tinymce && tinymce.activeEditor && !tinymce.activeEditor.isHidden()) {
            tinymce.activeEditor.insertContent(html);
        } else {
            insertHtmlAtCursor(html);
        }
    }

    function updatePageCountBadge() {
        var total = $('#pagesContainer .a4-page').length;
        $('#pageCountDisplay').text(total + ' Halaman');
        var isF4 = ($('#swUkuranKertas').val() === 'F4');
        var sizeText = isF4 ? 'F4 (330 mm)' : 'A4 (297 mm)';

        $('#pagesContainer .a4-page').each(function(idx) {
            var pNum = idx + 1;
            $(this).attr('data-page', pNum);
            $(this).find('.page-num-text').text(pNum);
            $(this).find('.page-badge-num').text('Hal. ' + pNum);
            $(this).find('.page-size-text').text(sizeText);

            var btnDel = $(this).find('.btn-hapus-halaman');
            if (pNum > 1) {
                btnDel.show();
            } else {
                btnDel.hide();
            }
        });
    }

    function createPageHtml(pageIndex, contentHtml) {
        var isF4 = ($('#swUkuranKertas').val() === 'F4');
        var f4Class = isF4 ? ' is-f4' : '';
        var sizeLabel = isF4 ? 'F4 (330 mm)' : 'A4 (297 mm)';
        var edId = 'pageEditor_' + pageIndex + '_' + Math.random().toString(36).substr(2, 6);
        var innerContent = (contentHtml !== undefined && contentHtml !== null && contentHtml.trim() !== '') ? contentHtml : '<p><br></p>';

        var html = '<div class="a4-page' + f4Class + '" data-page="' + pageIndex + '">';

        // Kop Surat HANYA untuk Halaman 1 (Single Source of Truth dari kop-surat.php)
        if (pageIndex === 1) {
            html += (typeof GLOBAL_KOP_HTML === 'string' && GLOBAL_KOP_HTML.length > 0)
                ? GLOBAL_KOP_HTML
                : '<div id="cvKop" style="position: relative; margin-bottom: 4px; user-select: none;"></div>';
            html += '<div id="cvSpacerNoKop" style="height: 12mm; display: none;"></div>' +
            '<!-- TANGGAL SURAT DI BAWAH KOP SEBELAH KANAN -->' +
            '<div id="cvTanggalWrap" style="text-align: right; margin-top: 6px; margin-bottom: 10px; font-family: \'Times New Roman\', Times, serif; font-size: 12pt; user-select: none;">' +
                '<span id="cvTanggalText">Gorontalo, <?= function_exists('tgl_indo') ? tgl_indo(date('Y-m-d')) : date('d F Y') ?></span>' +
            '</div>';
        }

        var placeholder = (pageIndex === 1)
            ? "Ketik surat Anda di sini... Lembar ini 100% polos layaknya Microsoft Word. Tekan Enter untuk baris baru. Tekan Ctrl+Enter atau klik '+ Halaman Baru' untuk menambah lembar ke-2."
            : "Ketik isi halaman " + pageIndex + " di sini...";

        html += '<div id="' + edId + '" class="page-editor" contenteditable="true" spellcheck="false" data-placeholder="' + placeholder + '">' +
            innerContent +
        '</div>';

        html += '<div class="page-footer-guide select-none">' +
            '<span class="inline-flex items-center gap-1.5">' +
                '<i class="fas fa-file-lines text-orange-500"></i>' +
                '<span>Halaman <b class="page-num-text text-slate-700">' + pageIndex + '</b> &bull; Kertas <span class="page-size-text">' + sizeLabel + '</span></span>' +
            '</span>' +
            '<span class="page-badge-num px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold text-[10px]">Hal. ' + pageIndex + '</span>' +
            '<button type="button" class="btn-hapus-halaman" style="' + (pageIndex > 1 ? '' : 'display:none;') + '" title="Hapus Halaman ' + pageIndex + '">&times; Hapus Halaman</button>' +
        '</div>';

        html += '</div>';
        return html;
    }

    function renderPages(contentsArray) {
        if (window.tinymce) {
            try {
                tinymce.remove();
                $('#deskTopRibbon .tox-tinymce-inline').remove();
            } catch(e) {}
        }

        if (!contentsArray || contentsArray.length === 0) {
            contentsArray = ['<p><br></p>'];
        }

        var container = $('#pagesContainer');
        container.empty();

        for (var i = 0; i < contentsArray.length; i++) {
            var pageNum = i + 1;
            var pageHtml = createPageHtml(pageNum, contentsArray[i]);
            container.append(pageHtml);
        }

        var pakaiKop = $('#swPakaiKop').is(':checked');
        updateKopCanvas(pakaiKop);
        updateTanggalHeaderCanvas();

        updatePageCountBadge();

        setTimeout(function() {
            initTinyMCE();
            var firstEd = document.querySelector('.page-editor');
            if (firstEd) {
                currentActiveEditor = firstEd;
                if (window.tinymce && tinymce.get(firstEd.id)) {
                    var edInst = tinymce.get(firstEd.id);
                    edInst.focus();
                    activateEditorRibbon(edInst);
                } else {
                    firstEd.focus();
                }
            }
        }, 100);
    }

    function tambahHalamanBaru(contentHtml) {
        var nextNum = $('#pagesContainer .a4-page').length + 1;
        var pageHtml = createPageHtml(nextNum, contentHtml || '<p><br></p>');
        var newPage = $(pageHtml).appendTo('#pagesContainer');

        updatePageCountBadge();

        var newEd = newPage.find('.page-editor')[0];
        if (newEd && window.tinymce) {
            initTinyMCE('#' + newEd.id);
        }

        setTimeout(function() {
            if (newEd) {
                currentActiveEditor = newEd;
                if (window.tinymce && tinymce.get(newEd.id)) {
                    var edInst = tinymce.get(newEd.id);
                    edInst.focus();
                    activateEditorRibbon(edInst);
                } else {
                    newEd.focus();
                }
                newPage[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 150);

        return newPage;
    }

    function setCanvasContent(rawHtml) {
        if (!rawHtml || rawHtml === '<p><br></p>' || rawHtml === '<br>') {
            renderPages(['<p><br></p>']);
            return;
        }

        // Jika dokumen memuat tanggal header wrapper, kita sesuaikan state checkbox
        if (rawHtml.indexOf('cv-tanggal-header') !== -1) {
            $('#swPakaiTanggalHeader').prop('checked', true);
            // Bersihkan div cv-tanggal-header dari editor agar tidak dobel dengan banner atas kanvas
            rawHtml = rawHtml.replace(/<div class="cv-tanggal-header"[^>]*>.*?<\/div>/gi, '');
        }

        var delimiterRegex = /(?:<!--\s*SIATEK_PAGE_BREAK\s*-->|<div[^>]*class="[^"]*siatek-page-break[^"]*"[^>]*>.*?<\/div>|<!--\s*pagebreak\s*-->|<div[^>]*class="[^"]*pagebreak[^"]*"[^>]*>.*?<\/div>)/is;
        var pages = rawHtml.split(delimiterRegex);
        if (!pages || pages.length === 0) {
            pages = [rawHtml];
        }

        renderPages(pages);
    }

    function getCanvasContent() {
        var pages = [];
        var pakaiTanggalHeader = $('#swPakaiTanggalHeader').is(':checked');
        var tglVal = $('#swTanggalSurat').val();
        var tglTeks = 'Gorontalo, ' + formatTglIndo(tglVal);

        $('#pagesContainer .a4-page').each(function(idx) {
            var editor = $(this).find('.page-editor')[0];
            if (editor) {
                var content = '';
                if (window.tinymce && tinymce.get(editor.id)) {
                    content = tinymce.get(editor.id).getContent();
                } else {
                    content = editor.innerHTML.trim();
                }

                content = content.replace(/\u200B/g, '');

                // Jika halaman 1 dan opsi tanggal aktif, pastikan elemen tanggal masuk dalam konten dokumen yang tersimpan
                if (idx === 0 && pakaiTanggalHeader) {
                    if (content.indexOf('Gorontalo,') === -1 && content.indexOf('cv-tanggal-header') === -1) {
                        var headerTglHtml = '<div class="cv-tanggal-header" style="text-align: right; margin-top: 4px; margin-bottom: 12px; font-family: \'Times New Roman\', Times, serif; font-size: 12pt;">' + tglTeks + '</div>';
                        content = headerTglHtml + content;
                    }
                }
                pages.push(content);
            }
        });
        return pages.join('\n<!-- SIATEK_PAGE_BREAK -->\n');
    }

    // Render & Filter Tabel Utama
    function filterDanRender() {
        var cari = ($('#fCari').val() || '').toLowerCase().trim();
        var kat = $('#fKategori').val();
        var ttd = $('#fTtd').val();
        var kop = $('#fKop').val();

        var filtered = rowsData.filter(function(tr) {
            if (kat && tr.getAttribute('data-kategori') !== kat) return false;
            if (ttd && tr.getAttribute('data-ttd-jenis') !== ttd) return false;
            if (kop !== '' && tr.getAttribute('data-pakai-kop') !== kop) return false;
            if (cari && (tr.getAttribute('data-cari') || '').indexOf(cari) === -1) return false;
            return true;
        });

        var total = filtered.length;
        var jmlHal = Math.max(1, Math.ceil(total / PER_HALAMAN));
        if (halaman > jmlHal) halaman = jmlHal;

        rowsData.forEach(function(r) { r.style.display = 'none'; });

        var start = (halaman - 1) * PER_HALAMAN;
        var slice = filtered.slice(start, start + PER_HALAMAN);
        slice.forEach(function(r) { r.style.display = ''; });

        $('#jmlInfo').html('Menampilkan <b class="text-slate-800">' + slice.length + '</b> dari <b class="text-slate-800">' + total + '</b> surat');
        $('#pgInfo').text('Halaman ' + halaman + ' dari ' + jmlHal);

        var wrap = document.getElementById('pgWrap');
        wrap.innerHTML = '';

        var btnPrev = document.createElement('button');
        btnPrev.type = 'button';
        btnPrev.className = 'pg-btn';
        btnPrev.innerHTML = '<i class="fas fa-chevron-left text-xs"></i>';
        btnPrev.disabled = halaman <= 1;
        btnPrev.addEventListener('click', function() { if (halaman > 1) { halaman--; filterDanRender(); } });
        wrap.appendChild(btnPrev);

        for (var i = 1; i <= jmlHal; i++) {
            (function(n) {
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'pg-btn' + (n === halaman ? ' on' : '');
                b.textContent = n;
                b.addEventListener('click', function() { halaman = n; filterDanRender(); });
                wrap.appendChild(b);
            })(i);
        }

        var btnNext = document.createElement('button');
        btnNext.type = 'button';
        btnNext.className = 'pg-btn';
        btnNext.innerHTML = '<i class="fas fa-chevron-right text-xs"></i>';
        btnNext.disabled = halaman >= jmlHal;
        btnNext.addEventListener('click', function() { if (halaman < jmlHal) { halaman++; filterDanRender(); } });
        wrap.appendChild(btnNext);
    }

    // Ambil tabel via AJAX
    window.muatTabel = function() {
        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { 'ambil-tabel-surat-custom': 1 },
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    $('#suratBody').html(res.data);
                    rowsData = Array.prototype.slice.call(document.querySelectorAll('#suratBody tr:not(.empty-row)'));
                    halaman = 1;
                    filterDanRender();
                }
            }
        });
    };

    // Helper Operasi Tabel di Canvas (Native Fallback)
    function getSelectedCell() {
        var sel = window.getSelection();
        if (!sel || !sel.rangeCount) return null;
        var node = sel.anchorNode;
        while (node && node !== document.body) {
            if (node.nodeName === 'TD' || node.nodeName === 'TH') return node;
            node = node.parentNode;
        }
        return null;
    }

    function getSelectedTable() {
        var cell = getSelectedCell();
        if (cell) {
            var node = cell;
            while (node && node !== document.body) {
                if (node.nodeName === 'TABLE') return node;
                node = node.parentNode;
            }
        }
        return activeTableTarget;
    }

    $(document).ready(function() {
        try {
            document.execCommand('defaultParagraphSeparator', false, 'p');
        } catch (e) {}

        muatTabel();

        var modal = document.getElementById('canvasModal');

        // Cegah tombol toolbar menghilangkan fokus/seleksi teks di kanvas
        $(document).on('mousedown', '.sb-btn, #btnSisipTtd, #btnSisipNomor, #btnQuickTable, #btnInsertHr, #btnTambahHalaman, #btnSpaceBefore, #btnSpaceAfter, #btnInsertTab, #btnSisipTabelCustom', function(e) {
            e.preventDefault();
        });

        // Pengatur Ukuran Kertas (A4 / F4)
        function setPaperSize(size) {
            var s = (size || 'A4').toUpperCase();
            if (s === 'F4') {
                $('.a4-page').addClass('is-f4');
                $('#swUkuranKertas').val('F4');
                $('#labelTombolCetak').text('Cetak F4');
            } else {
                $('.a4-page').removeClass('is-f4');
                $('#swUkuranKertas').val('A4');
                $('#labelTombolCetak').text('Cetak A4');
            }
            updatePageCountBadge();
        }

        $('#swUkuranKertas').on('change', function() {
            setPaperSize($(this).val());
        });

        // Buka Canvas Editor
        function openCanvas(edit, d) {
            isEditMode = edit;
            if (edit && d) {
                $('#canvasTitle').text('Edit Surat — ' + (d.nomor_surat || 'Dokumen'));
                $('#cvId').val(d.id);
                $('#btnSalinTemplateCanvas').removeClass('hidden'); // Tampilkan tombol duplikat untuk surat yang sudah tersimpan
                $('#swTanggalSurat').val(d.tanggal_surat);
                $('#swPakaiKop').prop('checked', parseInt(d.pakai_kop, 10) === 1);
                updateKopCanvas(parseInt(d.pakai_kop, 10) === 1);
                setPaperSize(d.ukuran_kertas || 'A4');
                $('#swKategori').val(d.kategori || 'Undangan');
                $('#swNomorSurat').val(d.nomor_surat || '');
                $('#swPerihal').val(d.perihal || '');
                $('#swTtdJenis').val(d.ttd_jenis || 'kajur');

                if (d.ttd_jenis === 'kaprodi') {
                    $('#wrapTtdProdi').removeClass('hidden');
                    if (d.ttd_prodi) $('#swTtdProdi').val(d.ttd_prodi);
                } else {
                    $('#wrapTtdProdi').addClass('hidden');
                }

                currentPejabat = {
                    dosen_id: d.ttd_dosen_id || '',
                    nama: d.ttd_nama || 'Nama Pejabat',
                    nip: d.ttd_nip || '-',
                    jabatan: d.ttd_jabatan || 'Ketua Jurusan'
                };
                $('#previewTtdNama').text(currentPejabat.nama);
                $('#previewTtdNip').text('NIP. ' + (currentPejabat.nip || '-'));

                // Cek apakah di dalam d.isi_surat ada penanda tanggal header yang tersimpan
                var hasTanggalInDoc = (d.isi_surat && (d.isi_surat.indexOf('cv-tanggal-header') !== -1));
                $('#swPakaiTanggalHeader').prop('checked', hasTanggalInDoc);
                updateTanggalHeaderCanvas();

                // Muat isi kanvas persis apa yang tersimpan
                setCanvasContent(d.isi_surat || '<p><br></p>');
            } else {
                $('#canvasTitle').text('Surat Baru (Lembar Polos)');
                $('#cvId').val('');
                $('#btnSalinTemplateCanvas').addClass('hidden'); // Sembunyikan untuk surat baru
                $('#swTanggalSurat').val('<?= date('Y-m-d') ?>');
                $('#swPakaiTanggalHeader').prop('checked', false);
                $('#swPakaiKop').prop('checked', true);
                updateKopCanvas(true);
                updateTanggalHeaderCanvas();
                setPaperSize('A4');
                $('#swKategori').val('Undangan');
                $('#swPilihFormatNomor').val('');
                $('#swPerihal').val('');
                $('#swNomorSurat').val('');
                $('#swTtdJenis').val('kajur');
                $('#wrapTtdProdi').addClass('hidden');

                // 100% LEMBAR POLOS / KOSONG! Bebas diketik operator dari awal
                setCanvasContent('<p><br></p>');

                // Muat data pejabat dan nomor di background tanpa mengubah isi lembar polos
                loadPejabat('kajur', '');
                tarikNomorOtomatis();

                // Reset Toolbar Format ke Default Resmi (Times New Roman 12pt)
                $('#selFontFamily').val("'Times New Roman', Times, serif");
                $('#selFontSize').val("12pt");
                $('#selLineHeight').val("1.35");
            }

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';

            setTimeout(function() {
                var firstEd = document.querySelector('.page-editor');
                if (firstEd) {
                    currentActiveEditor = firstEd;
                    firstEd.focus();

                    // Tempatkan kursor di awal editor secara eksplisit dan tampilkan kursor
                    var range = document.createRange();
                    if (firstEd.firstChild) {
                        range.setStart(firstEd.firstChild, 0);
                        range.collapse(true);
                    } else {
                        range.selectNodeContents(firstEd);
                        range.collapse(true);
                    }
                    var sel = window.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(range);
                    savedRange = range;

                    updateToolbarState();
                }
            }, 120);
        }

        // Buka Canvas dengan Template Surat yang Sudah Ada (Duplikasi sebagai Surat Baru)
        function openCanvasTemplate(d) {
            isEditMode = false; // Status SURAT BARU, BUKAN edit!
            var judulTemplate = d.perihal || 'Template';
            $('#canvasTitle').text('Surat Baru (Template: ' + judulTemplate + ')');
            $('#cvId').val(''); // Wajib KOSONG agar tersimpan sebagai record baru!
            $('#btnSalinTemplateCanvas').addClass('hidden'); // Sembunyikan karena dokumen template ini belum disimpan
            $('#swTanggalSurat').val('<?= date('Y-m-d') ?>'); // Otomatis tanggal hari ini
            $('#swPakaiKop').prop('checked', parseInt(d.pakai_kop, 10) === 1);
            updateKopCanvas(parseInt(d.pakai_kop, 10) === 1);
            setPaperSize(d.ukuran_kertas || 'A4');
            $('#swKategori').val(d.kategori || 'Undangan');
            $('#swPerihal').val(d.perihal || '');
            $('#swNomorSurat').val(''); // Dikosongkan lalu otomatis ditarik nomor baru
            $('#swTtdJenis').val(d.ttd_jenis || 'kajur');

            if (d.ttd_jenis === 'kaprodi') {
                $('#wrapTtdProdi').removeClass('hidden');
                if (d.ttd_prodi) $('#swTtdProdi').val(d.ttd_prodi);
            } else {
                $('#wrapTtdProdi').addClass('hidden');
            }

            currentPejabat = {
                dosen_id: d.ttd_dosen_id || '',
                nama: d.ttd_nama || 'Nama Pejabat',
                nip: d.ttd_nip || '-',
                jabatan: d.ttd_jabatan || 'Ketua Jurusan'
            };
            $('#previewTtdNama').text(currentPejabat.nama);
            $('#previewTtdNip').text('NIP. ' + (currentPejabat.nip || '-'));

            var hasTanggalInDoc = (d.isi_surat && (d.isi_surat.indexOf('cv-tanggal-header') !== -1));
            $('#swPakaiTanggalHeader').prop('checked', hasTanggalInDoc);
            updateTanggalHeaderCanvas();

            // Muat isi template surat
            setCanvasContent(d.isi_surat || '<p><br></p>');

            // Tarik otomatis nomor surat urut baru berikutnya dari sistem
            tarikNomorOtomatis();

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';

            setTimeout(function() {
                var firstEd = document.querySelector('.page-editor');
                if (firstEd) {
                    currentActiveEditor = firstEd;
                    firstEd.focus();
                }
            }, 120);
        }

        function closeCanvas(force) {
            if (!force) {
                var rawIsi = getCanvasContent();
                var hasContent = rawIsi && rawIsi !== '<p><br></p>' && rawIsi !== '<br>' && rawIsi.trim() !== '';
                if (hasContent) {
                    if (!confirm('Tutup lembar surat? Pastikan Anda sudah menyimpan jika ingin menyimpan perubahan.')) {
                        return;
                    }
                }
            }
            if (window.tinymce) {
                try {
                    tinymce.remove();
                    $('#deskTopRibbon .tox-tinymce-inline').remove();
                } catch(e) {}
            }
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }

        // Proteksi Refresh: Cegah Refresh (F5 / Ctrl+R) saat mode editor aktif sebelum disimpan atau klik Close
        $(document).on('keydown', function(e) {
            if ($('#canvasModal').hasClass('show')) {
                var isF5 = (e.keyCode === 116 || e.key === 'F5');
                var isCtrlR = ((e.ctrlKey || e.metaKey) && (e.keyCode === 82 || e.key === 'r' || e.key === 'R'));

                if (isF5 || isCtrlR) {
                    e.preventDefault();
                    e.stopPropagation();
                    toast('Mode editor surat sedang aktif! Simpan surat atau klik tombol Tutup terlebih dahulu.', 'info');
                    return false;
                }
            }
        });

        // Proteksi browser reload / navigasi tab keluar saat editor aktif
        window.addEventListener('beforeunload', function(e) {
            if ($('#canvasModal').hasClass('show')) {
                e.preventDefault();
                e.returnValue = 'Mode editor surat sedang aktif! Surat belum disimpan.';
                return e.returnValue;
            }
        });

        $('#btnBuatSurat').on('click', function() {
            openCanvas(false);
        });

        $('[data-canvas-close]').on('click', function() {
            closeCanvas(false);
        });

        // Toggle Kop Switch
        $('#swPakaiKop').on('change', function() {
            updateKopCanvas($(this).is(':checked'));
        });

        // Tarik Nomor Otomatis dari SIATEK (Urutan + 1 di tahun ini + format terpilih + tahun)
        function tarikNomorOtomatis(callback) {
            var formatId = $('#swPilihFormatNomor').val();
            var kat = $('#swKategori').val() || 'surat custom';
            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: {
                    'ambil-nomor-otomatis': 1,
                    'format_id': formatId,
                    'kategori': kat
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status && res.nomor) {
                        $('#swNomorSurat').val(res.nomor);
                        if (typeof callback === 'function') callback(res.nomor);
                    }
                }
            });
        }

        $('#btnTarikNomor').on('click', function() {
            tarikNomorOtomatis();
        });

        // Ketika operator mengubah format nomor surat di dropdown
        $('#swPilihFormatNomor').on('change', function() {
            tarikNomorOtomatis();
        });

        // Ketika operator mengubah kategori surat
        $('#swKategori').on('change', function() {
            if (!$('#swPilihFormatNomor').val()) {
                tarikNomorOtomatis();
            }
        });

        // Event Perubahan Tanggal Surat & Checkbox Gunakan Tanggal di Bawah Kop
        $('#swTanggalSurat').on('change input', function() {
            updateTanggalHeaderCanvas();
        });

        $('#swPakaiTanggalHeader').on('change', function() {
            updateTanggalHeaderCanvas();
        });

        // Tombol Sisipkan Tanggal ke Posisi Kursor
        $('#btnSisipTanggalKursor').on('click', function() {
            var tglVal = $('#swTanggalSurat').val();
            var tglTeks = 'Gorontalo, ' + formatTglIndo(tglVal);
            insertCanvasContent(tglTeks);
        });

        // Sisipkan Nomor Surat ke Posisi Kursor
        $('#btnSisipNomor').on('click', function() {
            var no = $('#swNomorSurat').val().trim();
            if (no) {
                insertCanvasContent(no);
            } else {
                toast('Nomor surat belum terisi. Klik "Auto No (+1)" terlebih dahulu.', 'info');
            }
        });

        // Ubah Pejabat Penandatangan
        $('#swTtdJenis').on('change', function() {
            var val = $(this).val();
            if (val === 'kaprodi') {
                $('#wrapTtdProdi').removeClass('hidden');
                loadPejabat('kaprodi', $('#swTtdProdi').val());
            } else {
                $('#wrapTtdProdi').addClass('hidden');
                loadPejabat(val, '');
            }
        });

        $('#swTtdProdi').on('change', function() {
            if ($('#swTtdJenis').val() === 'kaprodi') {
                loadPejabat('kaprodi', $(this).val());
            }
        });

        // Sisipkan Blok Tanda Tangan ke Posisi Kursor (Mengikuti format font & size aktif)
        $('#btnSisipTtd').on('click', function() {
            var pj = currentPejabat || { dosen_id: '', nama: 'Nama Pejabat', nip: '-', jabatan: 'Ketua Jurusan' };
            var curFmt = getActiveFormatting();
            var fontVal = $('#selFontFamily').val() || curFmt.font || "'Times New Roman', Times, serif";
            var sizeVal = $('#selFontSize').val() || curFmt.size || "12pt";

            // Pastikan fontVal bersih dan konsisten
            var cleanFont = (fontVal.indexOf('Times') !== -1) ? "'Times New Roman', Times, serif" : fontVal;

            var ttdHtml = '<table class="borderless ttd-table" style="width: 100%; margin-top: 24px; border-collapse: collapse; font-family: ' + cleanFont + '; font-size: ' + sizeVal + '; font-weight: normal !important;">' +
                '<tbody>' +
                '<tr>' +
                    '<td style="width: 47%; border: none; padding: 0;"></td>' +
                    '<td style="width: 53%; border: none; padding: 0; line-height: 1.25; vertical-align: top; font-weight: normal !important; font-family: ' + cleanFont + '; font-size: ' + sizeVal + '; color: #000000;">' +
                        '<span style="font-weight: normal !important; font-family: ' + cleanFont + '; font-size: ' + sizeVal + '; color: #000000;">' + (pj.jabatan || 'Ketua Jurusan') + '</span>' +
                    '</td>' +
                '</tr>' +
                '<tr style="height: 75px;"><td style="border: none; padding: 0;"></td><td style="border: none; padding: 0;"></td></tr>' +
                '<tr>' +
                    '<td style="border: none; padding: 0;"></td>' +
                    '<td style="border: none; padding: 0; line-height: 1.15; vertical-align: top; font-weight: normal !important; font-family: ' + cleanFont + '; font-size: ' + sizeVal + '; color: #000000;">' +
                        '<u style="font-weight: normal !important; font-family: ' + cleanFont + '; font-size: ' + sizeVal + '; color: #000000; text-decoration: underline;">' + (pj.nama || 'Nama Pejabat') + '</u><br>' +
                        '<span style="font-weight: normal !important; font-family: ' + cleanFont + '; font-size: ' + sizeVal + '; color: #000000;">NIP. ' + (pj.nip || '-') + '</span>' +
                    '</td>' +
                '</tr>' +
                '</tbody>' +
            '</table><p><br></p>';

            insertCanvasContent(ttdHtml);
        });

        // Helper Apply Inline Style pada Range Seleksi (Cross-Browser Rich Text)
        // Menjamin pengubahan jenis huruf TIDAK AKAN mengubah ukuran font,
        // dan pengubahan ukuran font TIDAK AKAN mengubah jenis huruf!
        function applyStyleToSelection(cssProp, cssVal) {
            restoreSelection();
            var sel = window.getSelection();
            if (!sel || !sel.rangeCount) return;

            var ed = getActiveEditor();
            if (!ed) return;

            var range = sel.getRangeAt(0);
            if (!ed.contains(range.commonAncestorContainer)) return;

            // Baca formatting aktif saat ini agar properti pendamping tetap terkunci
            var curFmt = getActiveFormatting();
            var targetFont = (cssProp === 'font-family') ? cssVal : curFmt.font;
            var targetSize = (cssProp === 'font-size') ? cssVal : curFmt.size;

            if (range.isCollapsed) {
                // Kursor berkedip tanpa blok teks: sisipkan placeholder span dengan font & size aktif
                var emptySpan = document.createElement('span');
                emptySpan.style.fontFamily = targetFont;
                emptySpan.style.fontSize = targetSize;
                emptySpan.innerHTML = '&#8203;'; // zero-width space
                range.insertNode(emptySpan);

                var r = document.createRange();
                r.setStart(emptySpan.firstChild, 1);
                r.collapse(true);
                sel.removeAllRanges();
                sel.addRange(r);
                savedRange = r;

                $('#selFontFamily').val(targetFont);
                $('#selFontSize').val(targetSize);
                return;
            }

            var common = range.commonAncestorContainer;
            var commonElem = (common.nodeType === 1) ? common : common.parentNode;

            // Jika teks yang diblok persis berada dalam satu SPAN yang sama
            if (commonElem && commonElem.tagName === 'SPAN' && ed.contains(commonElem)) {
                var spanText = (commonElem.textContent || '').trim();
                var selText = sel.toString().trim();
                if (spanText === selText && spanText.length > 0) {
                    commonElem.style.fontFamily = targetFont;
                    commonElem.style.fontSize = targetSize;

                    // Bersihkan inner span yang bertentangan
                    $(commonElem).find('span').each(function() {
                        if (cssProp === 'font-family') this.style.fontFamily = '';
                        if (cssProp === 'font-size') this.style.fontSize = '';
                    });

                    var newRange = document.createRange();
                    newRange.selectNodeContents(commonElem);
                    sel.removeAllRanges();
                    sel.addRange(newRange);
                    savedRange = newRange;

                    $('#selFontFamily').val(targetFont);
                    $('#selFontSize').val(targetSize);
                    return;
                }
            }

            // Seleksi teks melintasi sebagian span atau teks biasa: ekstrak dan bungkus secara aman
            var frag = range.extractContents();
            var span = document.createElement('span');
            span.style.fontFamily = targetFont;
            span.style.fontSize = targetSize;

            // Bersihkan inner span di dalam fragmen agar tidak menimpa properti yang baru diubah
            var innerSpans = frag.querySelectorAll ? frag.querySelectorAll('span') : [];
            for (var i = 0; i < innerSpans.length; i++) {
                var ispan = innerSpans[i];
                if (cssProp === 'font-family') {
                    ispan.style.fontFamily = '';
                } else if (cssProp === 'font-size') {
                    ispan.style.fontSize = '';
                }
                var styleAttr = (ispan.getAttribute('style') || '').trim();
                if (!styleAttr || styleAttr === ';') {
                    while (ispan.firstChild) {
                        ispan.parentNode.insertBefore(ispan.firstChild, ispan);
                    }
                    if (ispan.parentNode) {
                        ispan.parentNode.removeChild(ispan);
                    }
                }
            }

            span.appendChild(frag);
            range.insertNode(span);

            // Bersihkan parent span jika parent menjadi redundan
            var parent = span.parentNode;
            if (parent && parent.tagName === 'SPAN' && parent.textContent.trim() === span.textContent.trim()) {
                if (parent.style.color && !span.style.color) span.style.color = parent.style.color;
                if (parent.style.backgroundColor && !span.style.backgroundColor) span.style.backgroundColor = parent.style.backgroundColor;
                if (parent.parentNode) {
                    parent.parentNode.replaceChild(span, parent);
                }
            }

            var newRange = document.createRange();
            newRange.selectNodeContents(span);
            sel.removeAllRanges();
            sel.addRange(newRange);
            savedRange = newRange;

            $('#selFontFamily').val(targetFont);
            $('#selFontSize').val(targetSize);
        }

        // Tangkap seleksi sebelum dropdown kehilangan fokus
        $('#selFontFamily, #selFontSize').on('mousedown focus', function() {
            saveSelection();
        });

        // ================= FALLBACK RIBBON FORMATTING ACTIONS =================
        $('.sb-btn[data-cmd]').on('click', function() {
            var cmd = $(this).data('cmd');
            restoreSelection();
            try { document.execCommand('styleWithCSS', false, true); } catch(e) {}
            document.execCommand(cmd, false, null);
            saveSelection();
            updateToolbarState();
        });

        // Warna Teks
        $('#nativeForeColor').on('input change', function() {
            var color = $(this).val();
            restoreSelection();
            try { document.execCommand('styleWithCSS', false, true); } catch(e) {}
            document.execCommand('foreColor', false, color);
            saveSelection();
        });

        // Highlight Warna Latar
        $('#nativeBackColor').on('input change', function() {
            var color = $(this).val();
            restoreSelection();
            try { document.execCommand('styleWithCSS', false, true); } catch(e) {}
            document.execCommand('hiliteColor', false, color);
            saveSelection();
        });

        // Font Family (Bekerja 100% pada teks yang diblok tanpa mengubah ukuran font!)
        $('#selFontFamily').on('change', function() {
            var val = $(this).val();
            applyStyleToSelection('font-family', val);
        });

        // Font Size (Bekerja 100% pada teks yang diblok tanpa mengubah jenis huruf!)
        $('#selFontSize').on('change', function() {
            var val = $(this).val(); // misal '10pt', '12pt', '14pt', '18pt'
            applyStyleToSelection('font-size', val);
        });

        // Add / Remove Space Before Paragraph (Word-Style)
        function toggleSpaceBefore() {
            restoreSelection();
            var pList = getSelectedParagraphs();
            if (!pList || pList.length === 0) return;

            var firstP = pList[0];
            var currentTop = parseFloat(window.getComputedStyle(firstP).marginTop || '0');
            var hasSpace = (currentTop > 3 || (firstP.style.marginTop && parseFloat(firstP.style.marginTop) > 0));

            for (var i = 0; i < pList.length; i++) {
                if (hasSpace) {
                    pList[i].style.marginTop = '0px';
                } else {
                    pList[i].style.marginTop = '10pt'; // Standar Word Spacing Before
                }
            }

            saveSelection();
            updateToolbarState();
        }

        // Add / Remove Space After Paragraph (Word-Style)
        function toggleSpaceAfter() {
            restoreSelection();
            var pList = getSelectedParagraphs();
            if (!pList || pList.length === 0) return;

            var firstP = pList[0];
            var currentBottom = parseFloat(window.getComputedStyle(firstP).marginBottom || '0');
            var hasSpace = (currentBottom >= 11 || (firstP.style.marginBottom && parseFloat(firstP.style.marginBottom) >= 8));

            for (var i = 0; i < pList.length; i++) {
                if (hasSpace) {
                    pList[i].style.marginBottom = '0px';
                } else {
                    pList[i].style.marginBottom = '10pt'; // Standar Word Spacing After
                }
            }

            saveSelection();
            updateToolbarState();
        }

        // Event Tombol Spasi Paragraf (Word)
        $('#btnSpaceBefore').on('click', function() {
            toggleSpaceBefore();
        });

        $('#btnSpaceAfter').on('click', function() {
            toggleSpaceAfter();
        });

        // Spasi Baris (Line Spacing) - Diterapkan ke paragraf yang dipilih atau seluruh editor
        $('#selLineHeight').on('change', function() {
            var lh = $(this).val();
            restoreSelection();
            var pList = getSelectedParagraphs();
            if (pList && pList.length > 0) {
                for (var i = 0; i < pList.length; i++) {
                    pList[i].style.lineHeight = lh;
                }
            } else {
                $('.page-editor').css('line-height', lh);
            }
            saveSelection();
        });

        // Sisipkan Garis Horizontal
        $('#btnInsertHr').on('click', function() {
            insertCanvasContent('<hr style="border: 0; border-top: 1px solid #000; margin: 12px 0;"><p><br></p>');
        });

        // Sisipkan Tabel Cepat di Kursor
        $('#btnQuickTable').on('click', function() {
            var tHtml = generateTableHtml(3, 3);
            insertCanvasContent(tHtml);
        });

        // Tombol Sidebar: Sisipkan Tabel Kustom (Bebas Kolom & Baris)
        $('#btnSisipTabelCustom').on('click', function() {
            var cols = parseInt($('#tblJmlKolom').val()) || 3;
            var rows = parseInt($('#tblJmlBaris').val()) || 3;
            var tHtml = generateTableHtml(rows, cols);
            insertCanvasContent(tHtml);
        });

        // Sisipkan Gambar ke Canvas dari Sidebar
        $('#inputGambarCanvas').on('change', function() {
            var file = this.files[0];
            if (!file) return;

            var fd = new FormData();
            fd.append('gambar_surat', file);
            fd.append('upload-gambar-surat', '1');

            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if (res.status && res.url) {
                        var imgHtml = '<div style="text-align: center; margin: 10px 0;"><img src="' + res.url + '" alt="Gambar Lampiran" style="max-width: 80%; height: auto; display: inline-block;"></div><p><br></p>';
                        insertCanvasContent(imgHtml);
                    } else {
                        toast(res.pesan || 'Gagal menyisipkan gambar', 'error');
                    }
                },
                error: function() {
                    toast('Terjadi kesalahan saat mengunggah gambar', 'error');
                }
            });
            this.value = '';
        });

        // Tombol Tambah Halaman Baru
        $('#btnTambahHalaman').on('click', function() {
            tambahHalamanBaru();
        });

        // Shortcut Word: Tekan Tab di sel terakhir tabel untuk otomatis menambah baris baru
        $(document).on('keydown', '.page-editor table td', function(e) {
            if (e.key === 'Tab' && !e.shiftKey) {
                var row = this.closest('tr');
                var table = this.closest('table');
                if (row && table && row === table.rows[table.rows.length - 1] && this === row.cells[row.cells.length - 1]) {
                    e.preventDefault();
                    var newRow = table.insertRow(-1);
                    var cellCount = row.cells.length;
                    for (var c = 0; c < cellCount; c++) {
                        var newCell = newRow.insertCell(-1);
                        newCell.style.cssText = row.cells[c].style.cssText;
                        newCell.innerHTML = (c === 0) ? (table.rows.length - 1) + '.' : '&nbsp;';
                        if (c === 0) newCell.style.textAlign = 'center';
                    }
                    if (newRow.cells[1]) {
                        newRow.cells[1].focus();
                    } else {
                        newRow.cells[0].focus();
                    }
                }
            }
        });

        // Normalisasi Paste: Teks yang di-paste mengikuti font & ukuran yang sedang aktif pada kursor
        $(document).on('paste', '.page-editor', function(e) {
            e.preventDefault();
            var clipboard = (e.originalEvent || e).clipboardData || window.clipboardData;
            var text = clipboard ? clipboard.getData('text/plain') : '';
            if (text) {
                var curFmt = getActiveFormatting();
                var f = curFmt.font || "'Times New Roman', Times, serif";
                var s = curFmt.size || "12pt";

                // Konversi newline menjadi paragraf atau line break bersih
                var lines = text.split(/\r\n|\r|\n/);
                var formattedHtml = '';
                lines.forEach(function(line) {
                    var safeLine = $('<div>').text(line).html();
                    if (safeLine.trim() === '') {
                        formattedHtml += '<p><br></p>';
                    } else {
                        formattedHtml += '<p style="font-family: ' + f + '; font-size: ' + s + '; margin: 0 0 0.35rem 0; line-height: 1.35;">' + safeLine + '</p>';
                    }
                });
                insertCanvasContent(formattedHtml);
            }
        });

        // Event Fokus & Perekaman Kursor & Sinkronisasi Toolbar pada Setiap Halaman
        $(document).on('focus click keyup mouseup', '.page-editor', function() {
            currentActiveEditor = this;
            saveSelection();
            updateToolbarState();
            if (window.tinymce && tinymce.get(this.id)) {
                activateEditorRibbon(tinymce.get(this.id));
            }
        });

        // Event selectionchange global untuk sinkronisasi otomatis status toolbar (font, size, bold, dsb)
        document.addEventListener('selectionchange', function() {
            if ($('#canvasModal').hasClass('show')) {
                updateToolbarState();
            }
        });

        // Event Tombol Hapus Halaman (Hanya untuk Halaman 2 dst)
        $(document).on('click', '.btn-hapus-halaman', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var pageDiv = $(this).closest('.a4-page');
            var pNum = pageDiv.attr('data-page');
            if (confirm('Hapus Halaman ' + pNum + ' beserta isinya?')) {
                var ed = pageDiv.find('.page-editor')[0];
                if (ed && window.tinymce && tinymce.get(ed.id)) {
                    var edInst = tinymce.get(ed.id);
                    var container = (typeof edInst.getContainer === 'function') ? edInst.getContainer() : null;
                    if (container) {
                        $(container).remove();
                    }
                    edInst.destroy();
                }
                pageDiv.remove();
                updatePageCountBadge();
                var prevEd = document.querySelector('.page-editor');
                if (prevEd) {
                    currentActiveEditor = prevEd;
                    if (window.tinymce && tinymce.get(prevEd.id)) {
                        var prevInst = tinymce.get(prevEd.id);
                        prevInst.focus();
                        activateEditorRibbon(prevInst);
                    } else {
                        prevEd.focus();
                    }
                }
            }
        });

        // Tombol Toolbar Sisipkan Tabulasi
        $('#btnInsertTab').on('click', function() {
            var tabHtml = '<span class="siatek-tab" style="display: inline-block; width: 36pt;">&nbsp;</span>';
            insertCanvasContent(tabHtml);
        });

        // Handler Enter, Tab & Navigasi Halaman Word: Ketika ketikan mencapai batas bawah kertas
        $(document).on('keydown', '.page-editor', function(e) {
            // 0. Shortcut & Tombol Tabulasi Microsoft Word (Tab & Shift+Tab)
            if (e.key === 'Tab') {
                if ($(e.target).closest('td, th').length) {
                    return; // Biarkan navigasi sel tabel yang berjalan
                }

                e.preventDefault();
                e.stopPropagation();

                var sel = window.getSelection();
                var inList = false;
                if (sel && sel.rangeCount) {
                    var node = sel.getRangeAt(0).startContainer;
                    if (node.nodeType === 3) node = node.parentNode;
                    if (node && $(node).closest('li').length) {
                        inList = true;
                    }
                }

                if (inList) {
                    if (e.shiftKey) {
                        try { document.execCommand('outdent', false, null); } catch(ex) {}
                    } else {
                        try { document.execCommand('indent', false, null); } catch(ex) {}
                    }
                    return;
                }

                if (e.shiftKey) {
                    try { document.execCommand('outdent', false, null); } catch(ex) {}
                } else {
                    var tabHtml = '<span class="siatek-tab" style="display: inline-block; width: 36pt;">&nbsp;</span>';
                    insertCanvasContent(tabHtml);
                }
                return;
            }

            // 1. Shortcut Microsoft Word: Ctrl+Enter / Cmd+Enter untuk Menambah Lembar Baru
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                tambahHalamanBaru();
                return;
            }

            // 2. Proteksi Enter di Batas Bawah Kertas: Jangan biarkan konten terdorong keluar dan hilang!
            if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey && !e.metaKey && !e.altKey) {
                var ed = this;
                var pageCard = $(ed).closest('.a4-page');
                var isPageFull = (ed.scrollHeight > (ed.clientHeight + 4));

                // Cek juga posisi kursor apakah berada di bagian bawah/elemen terakhir
                var sel = window.getSelection();
                var atBottom = false;
                if (sel && sel.rangeCount > 0) {
                    var range = sel.getRangeAt(0);
                    var node = range.endContainer;
                    if (node.nodeType === 3) node = node.parentNode;
                    var edRect = ed.getBoundingClientRect();
                    var nodeRect = node.getBoundingClientRect ? node.getBoundingClientRect() : null;
                    if (nodeRect && (nodeRect.bottom >= (edRect.bottom - 45))) {
                        atBottom = true;
                    }
                }

                // Jika halaman sudah penuh dan pengguna menekan Enter di bagian bawah:
                if (isPageFull || atBottom) {
                    // Cari halaman berikutnya jika sudah ada, atau buat lembar baru
                    var nextPageDiv = pageCard.next('.a4-page');
                    if (nextPageDiv.length === 0) {
                        e.preventDefault();
                        var newP = tambahHalamanBaru('<p><br></p>');
                        var nextEd = newP.find('.page-editor')[0];
                        if (nextEd) {
                            currentActiveEditor = nextEd;
                            nextEd.focus();
                        }
                    } else {
                        // Jika halaman berikutnya sudah ada dan pengguna di ujung bawah halaman saat ini
                        var nextEd = nextPageDiv.find('.page-editor')[0];
                        if (nextEd && (isPageFull || atBottom)) {
                            e.preventDefault();
                            currentActiveEditor = nextEd;
                            nextEd.focus();
                            // Taruh kursor di awal paragraf halaman berikutnya
                            var range = document.createRange();
                            range.selectNodeContents(nextEd);
                            range.collapse(true);
                            sel.removeAllRanges();
                            sel.addRange(range);
                        }
                    }
                }
            }
        });

        // Deteksi input teks terus-menerus (paste atau ketik panjang yang meluber ke bawah)
        $(document).on('input', '.page-editor', function() {
            var ed = this;
            if (ed.scrollHeight > (ed.clientHeight + 10)) {
                // Tampilkan indikasi halus jika overflow terjadi
                var pageDiv = $(ed).closest('.a4-page');
                var footerGuide = pageDiv.find('.page-footer-guide');
                if (!pageDiv.find('.overflow-warn-badge').length) {
                    $('<span class="overflow-warn-badge text-amber-600 bg-amber-50 border border-amber-300 px-2 py-0.5 rounded text-[10px] font-bold animate-pulse cursor-pointer" title="Konten mepet/melebihi batas kertas A4. Klik untuk pindah ke halaman berikutnya."><i class="fas fa-exclamation-triangle mr-1"></i>Halaman Penuh &rarr; Buat Hal. Baru</span>')
                        .insertBefore(footerGuide.find('.page-badge-num'))
                        .on('click', function() {
                            tambahHalamanBaru();
                        });
                }
            } else {
                $(this).closest('.a4-page').find('.overflow-warn-badge').remove();
            }
        });

        // Fungsi Inti Simpan Dokumen (Bisa dipanggil oleh tombol Simpan atau Cetak)
        function simpanSurat(callback, tutupSetelahSimpan) {
            var isiHtml = getCanvasContent();
            var perihal = $('#swPerihal').val().trim();
            var nomor = $('#swNomorSurat').val().trim();

            if (!isiHtml || isiHtml === '<p><br></p>' || isiHtml === '<br>') {
                toast('Isi surat pada lembar kerja tidak boleh kosong.', 'error');
                var ed = getActiveEditor();
                if (ed) ed.focus();
                return;
            }

            if (!perihal) {
                var textSnippet = $('<div>').html(isiHtml).text().trim();
                if (textSnippet) {
                    var firstLine = textSnippet.split('\n')[0].replace(/\s+/g, ' ').substring(0, 80).trim();
                    perihal = firstLine || ('Surat Jurusan ' + $('#swKategori').val());
                } else {
                    perihal = 'Surat Jurusan ' + $('#swKategori').val();
                }
                $('#swPerihal').val(perihal);
            }

            var pj = currentPejabat;
            var ukKertas = $('#swUkuranKertas').val() || 'A4';
            var cvId = ($('#cvId').val() || '').trim();
            var isEdit = Boolean(isEditMode && cvId !== '');

            var data = {
                id: cvId,
                pakai_kop: $('#swPakaiKop').is(':checked') ? 1 : 0,
                ukuran_kertas: ukKertas,
                nomor_surat: nomor,
                tanggal_surat: $('#swTanggalSurat').val(),
                kategori: $('#swKategori').val(),
                perihal: perihal,
                tujuan: '',
                ttd_jenis: $('#swTtdJenis').val(),
                ttd_prodi: $('#swTtdProdi').val() || '',
                ttd_dosen_id: pj.dosen_id || '',
                ttd_jabatan: pj.jabatan || 'Ketua Jurusan',
                ttd_nama: pj.nama || 'Nama Pejabat',
                ttd_nip: pj.nip || '-',
                isi_surat: isiHtml,
                penutup: '',
                tembusan: ''
            };

            data[isEdit ? 'edit-data-surat-custom' : 'tambah-data-surat-custom'] = 1;

            var btn = $('#btnSimpanCanvas');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <span>Menyimpan...</span>');

            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: data,
                dataType: 'json',
                success: function(res) {
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> <span>Simpan</span>');
                    if (res.status) {
                        if (res.id) {
                            $('#cvId').val(res.id);
                            isEditMode = true;
                            $('#btnSalinTemplateCanvas').removeClass('hidden');
                        }
                        var currentId = $('#cvId').val();
                        if (typeof callback === 'function') {
                            callback(currentId);
                        } else {
                            toast(res.pesan, 'success');
                        }
                        if (tutupSetelahSimpan) {
                            closeCanvas(true);
                        }
                        muatTabel();
                        refreshNomorTerakhir();
                    } else {
                        toast(res.pesan || 'Gagal menyimpan surat', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> <span>Simpan</span>');
                    var detailErr = '';
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json && json.pesan) detailErr = ': ' + json.pesan;
                    } catch(e) {
                        if (xhr.status) detailErr = ' (HTTP ' + xhr.status + ': ' + (xhr.statusText || error) + ')';
                    }
                    toast('Terjadi kesalahan pada server saat menyimpan' + detailErr, 'error');
                }
            });
        }

        // Tombol Simpan Surat
        $('#btnSimpanCanvas').on('click', function() {
            simpanSurat(null, true);
        });

        // Tombol Cetak Surat (Simpan otomatis lalu buka jendela cetak resmi A4 / F4)
        $('#btnCetakCanvas').on('click', function(e) {
            e.preventDefault();
            var ukKertas = $('#swUkuranKertas').val() || 'A4';
            simpanSurat(function(idSurat) {
                var printUrl = '<?= base_url() ?>/dokumen/surat-custom/' + idSurat + '?kertas=' + ukKertas;
                window.open(printUrl, '_blank');
            }, false);
        });

        // Buka Canvas untuk Edit / Lihat Surat
        $(document).on('click', '.btnEditSurat, .btnLihatSurat', function() {
            var id = $(this).data('id');
            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: { 'ambil-detail-surat': 1, 'id': id },
                dataType: 'json',
                success: function(res) {
                    if (res.status && res.data) {
                        openCanvas(true, res.data);
                    } else {
                        toast(res.pesan || 'Gagal memuat data surat', 'error');
                    }
                }
            });
        });

        // Buka Canvas untuk Duplikat Template Surat (Surat Baru dari Template)
        $(document).on('click', '.btnDuplikatSurat', function() {
            var id = $(this).data('id');
            var btn = $(this);
            btn.addClass('opacity-50 pointer-events-none');
            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: { 'ambil-detail-surat': 1, 'id': id },
                dataType: 'json',
                success: function(res) {
                    btn.removeClass('opacity-50 pointer-events-none');
                    if (res.status && res.data) {
                        openCanvasTemplate(res.data);
                    } else {
                        toast(res.pesan || 'Gagal memuat template surat', 'error');
                    }
                },
                error: function() {
                    btn.removeClass('opacity-50 pointer-events-none');
                    toast('Terjadi kesalahan saat memuat template surat', 'error');
                }
            });
        });

        // Tombol di Top Bar Canvas: Jadikan Dokumen yang Sedang Dibuka Sebagai Surat Baru / Template
        $('#btnSalinTemplateCanvas').on('click', function() {
            if ($('#cvId').val() === '') {
                toast('Dokumen ini sudah berstatus Surat Baru!', 'info');
                return;
            }
            if (confirm('Jadikan dokumen ini sebagai Surat Baru (Template)?\nSurat lama tidak akan tertimpa saat Anda mengklik tombol Simpan.')) {
                isEditMode = false; // Pastikan mode edit non-aktif
                $('#cvId').val(''); // Kosongkan ID agar tersimpan sebagai record baru
                $('#btnSalinTemplateCanvas').addClass('hidden'); // Sembunyikan karena dokumen sudah beralih jadi Surat Baru (belum disimpan)
                var perihal = $('#swPerihal').val() || 'Salinan';
                $('#canvasTitle').text('Surat Baru (Template: ' + perihal + ')');
                $('#swTanggalSurat').val('<?= date('Y-m-d') ?>');
                updateTanggalHeaderCanvas();
                tarikNomorOtomatis(function(newNo) {
                    toast('Mode Template Aktif! Dialihkan ke Surat Baru No: ' + newNo, 'success');
                });
            }
        });

        // ================= FITUR SURAT EKSTERNAL & REFRESH NOMOR TERAKHIR =================
        function refreshNomorTerakhir(callback) {
            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: { 'ambil-info-nomor-terakhir': 1 },
                dataType: 'json',
                success: function(res) {
                    if (res && res.nomor) {
                        $('#lblNomorTerakhir').text(res.nomor);
                        $('#lblUrutanTerakhir').text(res.urutan);
                        $('#lblUrutanBerikutnya').text(res.berikutnya);
                        $('#lblTglTerakhir').text(res.tanggal_fmt || res.tanggal);
                        $('#lblKetTerakhir').text(res.keterangan || 'Belum ada catatan');
                        $('#hintNomorTerakhir').html('No. Terakhir: <strong class="text-slate-300 font-mono">' + res.nomor + '</strong>');
                        if (typeof callback === 'function') callback(res);
                    }
                }
            });
        }

        $('#btnRefreshNomorTerakhir').on('click', function() {
            var icon = $(this).find('i');
            icon.addClass('fa-spin');
            refreshNomorTerakhir(function() {
                icon.removeClass('fa-spin');
            });
        });

        // Buka Modal Catat Surat Eksternal
        function bukaModalEksternal(data) {
            $('#formSuratEksternal')[0].reset();
            $('#extFileCurrentWrap').addClass('hidden');
            $('#extFileCurrentLink').attr('href', '#');
            $('#extNamaFileCurrent').text('');

            if (data && data.id) {
                // Mode Edit
                $('#titleModalEksternal').text('Edit Surat Eksternal');
                $('#btnSimpanEksternal span').text('Perbarui Surat Eksternal');
                $('#extId').val(data.id);
                $('#extNomorSurat').val(data.nomor_surat || '');
                $('#extPerihal').val(data.perihal || '');
                $('#extKategori').val(data.kategori || 'Lainnya');
                $('#extTanggalSurat').val(data.tanggal_surat || '<?= date('Y-m-d') ?>');
                $('#extTujuan').val(data.tujuan || '');
                $('#extKeterangan').val(data.isi_surat || '');

                if (data.url_file) {
                    $('#extFileCurrentWrap').removeClass('hidden');
                    $('#extFileCurrentLink').attr('href', data.url_file);
                    $('#extNamaFileCurrent').text(data.nama_file_asli || 'Dokumen Tersimpan');
                }
            } else {
                // Mode Baru
                $('#titleModalEksternal').text('Catat Surat Eksternal');
                $('#btnSimpanEksternal span').text('Simpan Surat Eksternal');
                $('#extId').val('');
                $('#extTanggalSurat').val('<?= date('Y-m-d') ?>');
                $('#extKategori').val('Kerja Sama / MoU');

                // Tarik nomor urut berikutnya otomatis
                tarikNomorOtomatis(function(newNo) {
                    $('#extNomorSurat').val(newNo);
                });
            }

            $('#modalCatatEksternal').addClass('show');
            document.body.style.overflow = 'hidden';
        }

        function tutupModalEksternal() {
            $('#modalCatatEksternal').removeClass('show');
            document.body.style.overflow = '';
        }

        $('#modalCatatEksternal').on('click', function(e) {
            if (e.target === this) tutupModalEksternal();
        });

        $('#btnCatatEksternal').on('click', function() {
            bukaModalEksternal(null);
        });

        $(document).on('click', '.btnEditEksternal', function() {
            var id = $(this).data('id');
            var btn = $(this);
            btn.addClass('opacity-50 pointer-events-none');
            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: { 'ambil-detail-surat': 1, 'id': id },
                dataType: 'json',
                success: function(res) {
                    btn.removeClass('opacity-50 pointer-events-none');
                    if (res.status && res.data) {
                        bukaModalEksternal(res.data);
                    } else {
                        toast(res.pesan || 'Gagal memuat data surat eksternal', 'error');
                    }
                },
                error: function() {
                    btn.removeClass('opacity-50 pointer-events-none');
                    toast('Terjadi kesalahan saat memuat data', 'error');
                }
            });
        });

        $('[data-modal-close], .closeModalEksternal').on('click', function() {
            tutupModalEksternal();
        });

        $('#btnTarikNomorEksternal').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true);
            tarikNomorOtomatis(function(newNo) {
                btn.prop('disabled', false);
                $('#extNomorSurat').val(newNo);
            });
        });

        // Tangani submit form langsung (misal saat tekan Enter di input)
        $('#formSuratEksternal').on('submit', function(e) {
            e.preventDefault();
            $('#btnSimpanEksternal').trigger('click');
        });

        // Submit Form Surat Eksternal (Upload File via AJAX FormData)
        $('#btnSimpanEksternal').on('click', function(e) {
            e.preventDefault();
            var noSurat = $('#extNomorSurat').val().trim();
            var perihal = $('#extPerihal').val().trim();

            if (!noSurat) {
                toast('Nomor surat wajib diisi!', 'error');
                $('#extNomorSurat').focus();
                return;
            }
            if (!perihal) {
                toast('Perihal / Hal surat wajib diisi!', 'error');
                $('#extPerihal').focus();
                return;
            }

            var formData = new FormData($('#formSuratEksternal')[0]);
            formData.append('simpan-surat-eksternal', 1);

            var btn = $('#btnSimpanEksternal');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <span>Menyimpan...</span>');

            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> <span>' + ($('#extId').val() ? 'Perbarui Surat Eksternal' : 'Simpan Surat Eksternal') + '</span>');
                    if (res.status) {
                        toast(res.pesan, 'success');
                        tutupModalEksternal();
                        muatTabel();
                        refreshNomorTerakhir();
                    } else {
                        toast(res.pesan || 'Gagal menyimpan surat eksternal', 'error');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> <span>Simpan Surat Eksternal</span>');
                    var pesanErr = 'Terjadi kesalahan pada server saat mengunggah surat';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.pesan) {
                        pesanErr = xhr.responseJSON.pesan;
                    }
                    toast(pesanErr, 'error');
                }
            });
        });

        // Hapus Surat
        $(document).on('click', '.btnHapusSurat', function() {
            var id = $(this).data('id');
            if (!confirm('Hapus surat ini dari daftar?')) return;

            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: { 'hapus-data-surat-custom': 1, 'id': id },
                dataType: 'json',
                success: function(res) {
                    if (res.status) {
                        toast(res.pesan, 'success');
                        muatTabel();
                        refreshNomorTerakhir();
                    } else {
                        toast(res.pesan || 'Gagal menghapus surat', 'error');
                    }
                },
                error: function() {
                    toast('Terjadi kesalahan pada server saat menghapus surat', 'error');
                }
            });
        });

        // Filter events
        $('#fCari').on('input', function() { halaman = 1; filterDanRender(); });
        $('#fKategori, #fTtd, #fKop').on('change', function() { halaman = 1; filterDanRender(); });
        $('#btnReset').on('click', function() {
            $('#fCari').val('');
            $('#fKategori').val('');
            $('#fTtd').val('');
            $('#fKop').val('');
            halaman = 1;
            filterDanRender();
        });
    });
})();
</script>
