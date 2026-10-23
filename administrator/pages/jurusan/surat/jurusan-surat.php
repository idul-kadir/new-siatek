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

// Ambil daftar format nomor surat dari DB
$daftarFormatNomor = [];
$qFN = query("SELECT id, nama_surat, format_nomor FROM format_nomor_surat ORDER BY id ASC");
if ($qFN) {
    while ($fn = mysqli_fetch_assoc($qFN)) {
        $daftarFormatNomor[] = $fn;
    }
}
?>

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
    }

    /* Container yang menampung lembar-lembar halaman A4/F4 fisik */
    #pagesContainer {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 36px; /* Jarak meja kerja realistis antar lembar kertas */
        margin: 0 auto;
        width: fit-content;
    }

    /* Satu Lembar Kertas Fisik A4 / F4 — TINGGI TERKUNCI MUTLAK! TIDAK AKAN PERNAH MOLOR KELUAR UKURAN KERTAS */
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
        font-family: 'Times New Roman', Georgia, serif;
        font-size: 12pt;
        line-height: 1.25;
        color: #000;
        box-sizing: border-box;
        position: relative;
        text-align: left;
        display: flex;
        flex-direction: column;
        overflow: hidden; /* Mutlak: Kertas fisik tidak boleh bertambah panjang saat ditekan enter */
        transition: width .2s ease, height .2s ease;
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
        color: #000;
        text-align: justify;
        word-break: break-word;
        cursor: text;
        padding: 0;
        margin: 0;
        overflow: hidden; /* Mencegah scrollbar internal merusak kertas */
    }
    .page-editor:focus {
        box-shadow: none;
    }
    .page-editor:empty:before {
        content: attr(data-placeholder);
        color: #94a3b8;
        font-style: italic;
        pointer-events: none;
        display: block;
    }
    .page-editor p {
        margin: 0 0 0.35rem 0;
        line-height: inherit;
    }
    .page-editor table {
        width: 100%;
        border-collapse: collapse;
        margin: 8px 0;
        font-size: inherit;
    }
    .page-editor table td, .page-editor table th {
        padding: 4px 6px;
        vertical-align: top;
    }
    .page-editor table.border-grid td, .page-editor table.border-grid th,
    .page-editor table.table-bordered td, .page-editor table.table-bordered th {
        border: 1px solid #000;
    }
    .page-editor table.borderless td, .page-editor table.borderless th,
    .page-editor table.table-borderless td, .page-editor table.table-borderless th {
        border: none !important;
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
            <div>
                <button type="button" id="btnBuatSurat" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-orange-600">
                    <i class="fas fa-plus"></i>Buat Surat
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
                    <option value="Times New Roman" selected>Times New Roman</option>
                    <option value="Arial">Arial</option>
                    <option value="Calibri">Calibri</option>
                    <option value="Georgia">Georgia</option>
                </select>
                <select id="selFontSize" class="sb-select !w-24 !py-1">
                    <option value="2">10pt</option>
                    <option value="3" selected>12pt (Resmi)</option>
                    <option value="4">14pt (Judul)</option>
                    <option value="5">18pt (Besar)</option>
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

                <!-- Lists & Indent -->
                <button type="button" class="sb-btn" data-cmd="insertOrderedList" title="Penomoran (1, 2, 3...)"><i class="fas fa-list-ol"></i></button>
                <button type="button" class="sb-btn" data-cmd="insertUnorderedList" title="Daftar Poin (Bullets)"><i class="fas fa-list-ul"></i></button>
                <button type="button" class="sb-btn" data-cmd="outdent" title="Kurangi Indentasi"><i class="fas fa-outdent"></i></button>
                <button type="button" class="sb-btn" data-cmd="indent" title="Tambah Indentasi"><i class="fas fa-indent"></i></button>

                <div class="h-5 w-[1px] bg-slate-700 mx-0.5"></div>

                <!-- Spasi Baris -->
                <select id="selLineHeight" class="sb-select !w-24 !py-1" title="Spasi Baris">
                    <option value="1.0">1.0</option>
                    <option value="1.15">1.15</option>
                    <option value="1.35" selected>1.35</option>
                    <option value="1.5">1.5</option>
                    <option value="2.0">2.0</option>
                </select>

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
                            <div class="mt-1.5 flex justify-end">
                                <button type="button" id="btnSisipNomor" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded text-[11px] font-medium border border-slate-700 transition flex items-center gap-1">
                                    <i class="fas fa-plus text-[9px] text-orange-400"></i> Sisipkan Nomor ke Kursor
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
                                    <input type="checkbox" id="swPakaiTanggalHeader" class="rounded text-orange-500 focus:ring-0 w-4 h-4 mt-0.5 cursor-pointer" checked>
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
                                <?php foreach ($daftarProdi as $pr): ?>
                                <option value="<?= htmlspecialchars($pr['kode']) ?>"><?= htmlspecialchars(($pr['jenjang'] ? $pr['jenjang'] . ' ' : '') . $pr['nama']) ?></option>
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

<script>
(function() {
    var PROSES_URL = '<?= base_url() ?>/administrator/pages/jurusan/surat/proses.php';
    var rowsData = [];
    var halaman = 1;
    var PER_HALAMAN = 10;
    var isEditMode = false;
    var activeTableTarget = null;
    var currentPejabat = { dosen_id: '', nama: 'Nama Pejabat', nip: '-', jabatan: 'Ketua Jurusan' };
    var savedRange = null;

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

    function getActiveEditor() {
        if (currentActiveEditor && document.body.contains(currentActiveEditor)) {
            return currentActiveEditor;
        }
        var firstEd = document.querySelector('.page-editor');
        return firstEd;
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

    function insertHtmlAtCursor(html) {
        var ed = getActiveEditor();
        if (!ed) return;
        restoreSelection();
        ed.focus();
        if (document.queryCommandSupported('insertHTML')) {
            document.execCommand('insertHTML', false, html);
        } else {
            var sel = window.getSelection();
            if (sel && sel.rangeCount) {
                var range = sel.getRangeAt(0);
                range.deleteContents();
                var el = document.createElement('div');
                el.innerHTML = html;
                var frag = document.createDocumentFragment(), node, lastNode;
                while ((node = el.firstChild)) {
                    lastNode = frag.appendChild(node);
                }
                range.insertNode(frag);
                if (lastNode) {
                    range = range.cloneRange();
                    range.setStartAfter(lastNode);
                    range.collapse(true);
                    sel.removeAllRanges();
                    sel.addRange(range);
                }
            }
        }
        saveSelection();
    }

    function insertCanvasContent(html) {
        insertHtmlAtCursor(html);
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

        // Kop Surat HANYA untuk Halaman 1
        if (pageIndex === 1) {
            html += '<div id="cvKop" style="position: relative; min-height: 24mm; margin-bottom: 4px; user-select: none;">' +
                '<img src="<?= base_url() ?>/surat/img/logo.png" alt="Logo UNG" style="position: absolute; top: 0; left: 0; width: 22mm;">' +
                '<div style="text-align: center; margin: 0 22mm;">' +
                    '<p style="font-size: 12pt; margin: 0; line-height: 1.22;">KEMENTERIAN PENDIDIKAN,KEBUDAYAAN,<br>RISET DAN TEKNOLOGI<br>UNIVERSITAS NEGERI GORONTALO<br>FAKULTAS TEKNIK<br>JURUSAN TEKNIK ELEKTRO DAN KOMPUTER</p>' +
                    '<p style="font-size: 10pt; margin: 0; line-height: 1.22;">Jalan B.J.Habibie Desa Moutong Kecamatan Tilongkabila Kab.Bone Bolango<br>Telp. (0435) 821125, Fax. (0435) 821752 Gorontalo<br>Laman :http:// www.ft.ung.ac.id</p>' +
                '</div>' +
                '<hr style="border: 1.5px solid #000; margin: 4px 0 0;">' +
            '</div>' +
            '<div id="cvSpacerNoKop" style="height: 12mm; display: none;"></div>' +
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
            var firstEd = document.querySelector('.page-editor');
            if (firstEd) {
                currentActiveEditor = firstEd;
                firstEd.focus();
            }
        }, 50);
    }

    function tambahHalamanBaru(contentHtml) {
        var nextNum = $('#pagesContainer .a4-page').length + 1;
        var pageHtml = createPageHtml(nextNum, contentHtml || '<p><br></p>');
        var newPage = $(pageHtml).appendTo('#pagesContainer');

        updatePageCountBadge();

        setTimeout(function() {
            var ed = newPage.find('.page-editor')[0];
            if (ed) {
                currentActiveEditor = ed;
                ed.focus();
                newPage[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 50);

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
                var content = editor.innerHTML.trim();
                // Jika halaman 1 dan opsi tanggal aktif, pastikan elemen tanggal masuk dalam konten dokumen yang tersimpan
                if (idx === 0 && pakaiTanggalHeader) {
                    // Cek apakah konten editor belum memiliki tanggal
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
        $(document).on('mousedown', '.sb-btn, #btnSisipTtd, #btnSisipNomor, #btnQuickTable, #btnInsertHr, #btnTambahHalaman', function(e) {
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

                // Cek apakah di dalam d.isi_surat sudah ada tanggal kanan tersimpan
                var hasTanggalInDoc = (d.isi_surat && d.isi_surat.indexOf('Gorontalo,') !== -1);
                $('#swPakaiTanggalHeader').prop('checked', !hasTanggalInDoc);
                updateTanggalHeaderCanvas();

                // Muat isi kanvas persis apa yang tersimpan
                setCanvasContent(d.isi_surat || '<p><br></p>');
            } else {
                $('#canvasTitle').text('Surat Baru (Lembar Polos)');
                $('#cvId').val('');
                $('#swTanggalSurat').val('<?= date('Y-m-d') ?>');
                $('#swPakaiTanggalHeader').prop('checked', true);
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
            }

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';

            setTimeout(function() {
                var firstEd = document.querySelector('.page-editor');
                if (firstEd) {
                    currentActiveEditor = firstEd;
                    firstEd.focus();
                    saveSelection();
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
                    alert('Mode editor surat sedang aktif! Halaman tidak dapat di-refresh sebelum surat disimpan atau klik tombol "Tutup" terlebih dahulu.');
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
                alert('Nomor surat belum terisi. Klik "Auto No (+1)" terlebih dahulu.');
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

        // Sisipkan Blok Tanda Tangan ke Posisi Kursor
        $('#btnSisipTtd').on('click', function() {
            var pj = currentPejabat;
            var ttdHtml = '<table class="borderless ttd-table" style="width: 100%; margin-top: 20px; border-collapse: collapse;">' +
                '<tr>' +
                    '<td style="width: 47%;"></td>' +
                    '<td style="width: 53%; line-height: 1.25; vertical-align: top;">' + pj.jabatan + ',</td>' +
                '</tr>' +
                '<tr style="height: 55px;"><td></td><td></td></tr>' +
                '<tr>' +
                    '<td></td>' +
                    '<td style="line-height: 1.15; padding: 0; vertical-align: top;">' +
                        '<b><u>' + pj.nama + '</u></b><br>' +
                        '<span>NIP. ' + (pj.nip || '-') + '</span>' +
                    '</td>' +
                '</tr>' +
            '</table><p><br></p>';

            insertCanvasContent(ttdHtml);
        });

        // ================= FALLBACK RIBBON FORMATTING ACTIONS =================
        $('.sb-btn[data-cmd]').on('click', function() {
            var cmd = $(this).data('cmd');
            restoreSelection();
            document.execCommand(cmd, false, null);
            saveSelection();
        });

        // Warna Teks
        $('#nativeForeColor').on('input change', function() {
            var color = $(this).val();
            restoreSelection();
            document.execCommand('foreColor', false, color);
            saveSelection();
        });

        // Highlight Warna Latar
        $('#nativeBackColor').on('input change', function() {
            var color = $(this).val();
            restoreSelection();
            document.execCommand('hiliteColor', false, color);
            saveSelection();
        });

        // Font Family
        $('#selFontFamily').on('change', function() {
            var val = $(this).val();
            restoreSelection();
            document.execCommand('fontName', false, val);
            saveSelection();
        });

        // Font Size
        $('#selFontSize').on('change', function() {
            var val = $(this).val();
            restoreSelection();
            document.execCommand('fontSize', false, val);
            saveSelection();
        });

        // Spasi Baris
        $('#selLineHeight').on('change', function() {
            var lh = $(this).val();
            $('.page-editor').css('line-height', lh);
        });

        // Sisipkan Garis Horizontal
        $('#btnInsertHr').on('click', function() {
            insertCanvasContent('<hr style="border: 0; border-top: 1px solid #000; margin: 12px 0;"><p><br></p>');
        });

        // Sisipkan Tabel Cepat di Kursor
        $('#btnQuickTable').on('click', function() {
            var tHtml = '<table class="border-grid" style="width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 12pt;"><tbody>' +
                '<tr><th style="border: 1px solid #000; padding: 4px 8px; width: 45px; text-align: center;">No.</th><th style="border: 1px solid #000; padding: 4px 8px;">Uraian</th><th style="border: 1px solid #000; padding: 4px 8px;">Keterangan</th></tr>' +
                '<tr><td style="border: 1px solid #000; padding: 4px 8px; text-align: center;">1.</td><td style="border: 1px solid #000; padding: 4px 8px;">&nbsp;</td><td style="border: 1px solid #000; padding: 4px 8px;">&nbsp;</td></tr>' +
                '<tr><td style="border: 1px solid #000; padding: 4px 8px; text-align: center;">2.</td><td style="border: 1px solid #000; padding: 4px 8px;">&nbsp;</td><td style="border: 1px solid #000; padding: 4px 8px;">&nbsp;</td></tr>' +
                '</tbody></table><p><br></p>';
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
                        alert(res.pesan || 'Gagal menyisipkan gambar');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengunggah gambar');
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

        // Event Fokus & Perekaman Kursor pada Setiap Halaman
        $(document).on('focus click keyup', '.page-editor', function() {
            currentActiveEditor = this;
            saveSelection();
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
                    tinymce.get(ed.id).destroy();
                }
                pageDiv.remove();
                updatePageCountBadge();
                var prevEd = document.querySelector('.page-editor');
                if (prevEd) {
                    currentActiveEditor = prevEd;
                    prevEd.focus();
                }
            }
        });

        // Handler Enter & Navigasi Halaman Word: Ketika ketikan mencapai batas bawah kertas
        $(document).on('keydown', '.page-editor', function(e) {
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
                alert('Isi surat pada lembar kerja tidak boleh kosong.');
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

            var data = {
                id: $('#cvId').val(),
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

            data[isEditMode ? 'edit-data-surat-custom' : 'tambah-data-surat-custom'] = 1;

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
                        }
                        var currentId = $('#cvId').val();
                        if (typeof callback === 'function') {
                            callback(currentId);
                        } else {
                            alert(res.pesan);
                        }
                        if (tutupSetelahSimpan) {
                            closeCanvas(true);
                        }
                        muatTabel();
                    } else {
                        alert(res.pesan || 'Gagal menyimpan surat');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> <span>Simpan</span>');
                    alert('Terjadi kesalahan pada server saat menyimpan');
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
                        alert(res.pesan || 'Gagal memuat data surat');
                    }
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
                    alert(res.pesan);
                    if (res.status) {
                        muatTabel();
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan pada server');
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
