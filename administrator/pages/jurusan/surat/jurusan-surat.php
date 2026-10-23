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
?>

<!-- TinyMCE 6 Rich Text Editor (Full Word Suite: Table Tools, Colors, Font Formatting, etc.) -->
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
        padding: 30px 20px 140px; /* Jarak bawah 140px solid agar batas bawah kertas selalu terlihat utuh saat di-scroll */
        display: block;
        flex: 1;
        width: 100%;
        text-align: center;
    }
    .a4-paper-sheet {
        background: #ffffff;
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto 50px auto;
        padding: 25mm; /* Margin Mutlak SIATEK 2.5 cm sekeliling */
        border: 1px solid #94a3b8; /* Garis batas fisik tepi kertas jelas */
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.55), 0 2px 6px rgba(0, 0, 0, 0.3);
        font-family: 'Times New Roman', Georgia, serif;
        font-size: 12pt;
        line-height: 1.25;
        color: #000;
        box-sizing: border-box;
        position: relative;
        text-align: left;
        transition: width .2s ease, min-height .2s ease;
    }
    .a4-paper-sheet.is-f4 {
        width: 215mm;
        min-height: 330mm;
    }
    @media (max-width: 992px) {
        .a4-desk { padding: 15px 10px 100px; }
        .a4-paper-sheet { width: 100% !important; min-height: auto; padding: 18mm 12mm; }
    }

    /* Kanvas Dokumen Word A4 Bebas (Satu Kesatuan Area Ketik) */
    .word-canvas {
        min-height: 230mm;
        outline: none;
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        line-height: 1.35;
        color: #000;
        text-align: justify;
        word-break: break-word;
        cursor: text;
        padding: 4px 2px;
    }
    .word-canvas:focus {
        box-shadow: 0 0 0 1px rgba(234, 88, 12, 0.2);
    }
    .word-canvas:empty:before {
        content: attr(data-placeholder);
        color: #94a3b8;
        font-style: italic;
        pointer-events: none;
        display: block;
    }
    .word-canvas p {
        margin: 0 0 0.35rem 0;
        line-height: inherit;
    }
    .word-canvas table {
        width: 100%;
        border-collapse: collapse;
        margin: 8px 0;
        font-size: inherit;
    }
    .word-canvas table td, .word-canvas table th {
        padding: 4px 6px;
        vertical-align: top;
    }
    .word-canvas table.border-grid td, .word-canvas table.border-grid th,
    .word-canvas table.table-bordered td, .word-canvas table.table-bordered th {
        border: 1px solid #000;
    }
    .word-canvas table.borderless td, .word-canvas table.borderless th,
    .word-canvas table.table-borderless td, .word-canvas table.table-borderless th {
        border: none !important;
    }
    .word-canvas ol {
        margin: 4px 0 6px 24px;
        padding-left: 0;
        list-style-type: decimal;
    }
    .word-canvas ul {
        margin: 4px 0 6px 20px;
        padding-left: 0;
        list-style-type: disc;
    }
    .word-canvas li {
        margin-bottom: 2px;
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
    .desk-top-ribbon .tox-tinymce-inline {
        z-index: 30 !important;
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
    }
    .desk-top-ribbon .tox.tox-tinymce-inline .tox-editor-header {
        background: #0f172a !important;
        border: none !important;
    }
    .desk-top-ribbon .tox .tox-menubar,
    .desk-top-ribbon .tox .tox-toolbar,
    .desk-top-ribbon .tox .tox-toolbar__primary,
    .desk-top-ribbon .tox .tox-toolbar__overflow {
        background: #0f172a !important;
        border: none !important;
    }
    .desk-top-ribbon .tox .tox-mbtn,
    .desk-top-ribbon .tox .tox-tbtn {
        color: #e2e8f0 !important;
    }
    .desk-top-ribbon .tox .tox-mbtn:hover,
    .desk-top-ribbon .tox .tox-tbtn:hover {
        background: #334155 !important;
        color: #ffffff !important;
    }
    .desk-top-ribbon .tox .tox-tbtn--enabled {
        background: #ea580c !important;
        color: #ffffff !important;
    }

    /* Tombol & Input Tool di Sidebar */
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

        <!-- ===== BARIS 1: TOP APPLICATION BAR (DOKUMEN, KOP, NOMOR, TTD, SIMPAN, CETAK, TUTUP) ===== -->
        <header class="h-14 shrink-0 bg-slate-950 border-b border-slate-800 px-4 flex items-center justify-between gap-3 text-slate-200 z-40">
            <!-- Sisi Kiri: Ikon & Judul / Perihal Dokumen -->
            <div class="flex items-center gap-3 flex-1 min-w-0 max-w-xl">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-white text-sm font-bold shadow">
                    <i class="fas fa-file-lines"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <input type="text" id="swPerihal" class="w-full bg-transparent border-b border-transparent hover:border-slate-700 focus:border-orange-500 text-white font-semibold text-sm px-1 py-0.5 outline-none truncate transition placeholder:text-slate-500" placeholder="Ketik Judul / Perihal Surat di sini...">
                    <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-0.5">
                        <span id="canvasTitle" class="truncate font-medium text-slate-300">Surat Baru (Lembar Polos)</span>
                    </div>
                </div>
            </div>

            <!-- Sisi Tengah: Pengaturan Dokumen (Kop, Ukuran Kertas, No Surat, Tanggal, Sisip TTD) -->
            <div class="flex items-center gap-2 text-xs">
                <!-- Toggle Kop Resmi -->
                <label class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700 cursor-pointer hover:bg-slate-800 text-slate-300 select-none transition" title="Gunakan Kop Surat Resmi Jurusan UNG">
                    <input type="checkbox" id="swPakaiKop" class="rounded text-orange-500 focus:ring-0" checked>
                    <span class="font-medium text-[11px]">Kop Resmi</span>
                </label>

                <!-- Pilihan Ukuran Kertas (A4 / F4) -->
                <div class="flex items-center gap-1 bg-slate-800/80 border border-slate-700 rounded-lg p-0.5" title="Pilih Ukuran Kertas Cetak">
                    <span class="text-[10px] text-slate-400 font-bold px-1.5"><i class="fas fa-scroll text-orange-400 mr-1"></i>Kertas:</span>
                    <select id="swUkuranKertas" class="bg-slate-900 border border-slate-700 text-white font-semibold text-[11px] rounded px-2 py-0.5 outline-none cursor-pointer">
                        <option value="A4" selected>A4 (210×297 mm)</option>
                        <option value="F4">F4 / Folio (215×330 mm)</option>
                    </select>
                </div>

                <!-- Nomor Surat (Input + Auto No + Sisipkan) -->
                <div class="flex items-center gap-1 bg-slate-800/80 border border-slate-700 rounded-lg px-2 py-1">
                    <span class="text-[11px] text-slate-400 font-medium">No:</span>
                    <input type="text" id="swNomorSurat" class="bg-transparent border-none text-[11px] text-white font-mono w-44 outline-none placeholder:text-slate-500" placeholder="Nomor surat...">
                    <button type="button" id="btnTarikNomor" class="text-[10px] text-orange-400 hover:text-orange-300 font-semibold px-1 py-0.5 rounded hover:bg-slate-700" title="Tarik nomor surat otomatis berikutnya dari SIATEK">
                        <i class="fas fa-sync-alt"></i> Auto
                    </button>
                    <button type="button" id="btnSisipNomor" class="text-[10px] text-slate-400 hover:text-white font-medium px-1 py-0.5 rounded hover:bg-slate-700" title="Sisipkan nomor surat ke posisi kursor pada lembar">
                        +Sisip
                    </button>
                </div>

                <!-- Tanggal Dokumen -->
                <input type="date" id="swTanggalSurat" class="bg-slate-800/80 border border-slate-700 rounded-lg text-slate-300 text-[11px] px-2 py-1 outline-none focus:border-orange-500" value="<?= date('Y-m-d') ?>">

                <!-- Kategori Dokumen -->
                <select id="swKategori" class="bg-slate-800/80 border border-slate-700 rounded-lg text-slate-300 text-[11px] px-2 py-1 outline-none">
                    <option value="Undangan">Undangan</option>
                    <option value="Surat Keterangan">Surat Keterangan</option>
                    <option value="Surat Pemberitahuan">Surat Pemberitahuan</option>
                    <option value="Surat Tugas">Surat Tugas</option>
                    <option value="Surat Pengantar">Surat Pengantar</option>
                    <option value="Lainnya">Lainnya</option>
                </select>

                <!-- Sisip TTD Pejabat (Kajur, Sekjur, Kaprodi) -->
                <div class="flex items-center gap-1 bg-slate-800/80 border border-slate-700 rounded-lg p-0.5">
                    <select id="swTtdJenis" class="bg-transparent border-none text-slate-300 text-[11px] px-1 py-0.5 outline-none cursor-pointer">
                        <option value="kajur" selected class="bg-slate-900 text-white">Kajur</option>
                        <option value="sekjur" class="bg-slate-900 text-white">Sekjur</option>
                        <option value="kaprodi" class="bg-slate-900 text-white">Kaprodi</option>
                    </select>
                    <select id="swTtdProdi" class="hidden bg-transparent border-none text-slate-300 text-[11px] px-1 py-0.5 outline-none cursor-pointer max-w-[120px]">
                        <?php foreach ($daftarProdi as $pr): ?>
                        <option value="<?= htmlspecialchars($pr['kode']) ?>" class="bg-slate-900 text-white"><?= htmlspecialchars(($pr['jenjang'] ? $pr['jenjang'] . ' ' : '') . $pr['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" id="btnSisipTtd" class="px-2 py-1 bg-orange-600 hover:bg-orange-500 text-white text-[11px] font-semibold rounded transition flex items-center gap-1 shadow-sm" title="Sisipkan blok tanda tangan pejabat pada posisi kursor">
                        <i class="fas fa-file-signature text-[10px]"></i> Sisip TTD
                    </button>
                </div>
            </div>

            <!-- Sisi Kanan: Tombol Aksi Simpan, Cetak, Tutup -->
            <div class="flex items-center gap-2">
                <button type="button" id="btnSimpanCanvas" class="py-1.5 px-3 text-xs font-bold rounded-lg bg-orange-500 hover:bg-orange-600 text-white shadow transition flex items-center gap-1.5">
                    <i class="fas fa-save"></i> <span>Simpan</span>
                </button>
                <button type="button" id="btnCetakCanvas" class="py-1.5 px-3 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow transition flex items-center gap-1.5" title="Cetak Dokumen Resmi ke Printer / Simpan PDF">
                    <i class="fas fa-print"></i> <span id="labelTombolCetak">Cetak Surat</span>
                </button>
                <button type="button" data-canvas-close class="h-8 w-8 flex items-center justify-center rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition text-lg" title="Tutup Editor">
                    &times;
                </button>
            </div>
        </header>

        <!-- ===== BARIS 2: WORD RIBBON TOOLBAR (UNTUK TINYMCE / NATIVE RICH TEXT) ===== -->
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

                <!-- Undo & Redo -->
                <button type="button" class="sb-btn" data-cmd="undo" title="Urungkan (Undo)"><i class="fas fa-rotate-left"></i></button>
                <button type="button" class="sb-btn" data-cmd="redo" title="Ulangi (Redo)"><i class="fas fa-rotate-right"></i></button>
                <button type="button" class="sb-btn" data-cmd="removeFormat" title="Hapus Format"><i class="fas fa-remove-format"></i></button>
            </div>
        </header>

        <!-- ===== BARIS 3: MEJA KERJA A4 (KERTAS 100% POLOS / KOSONG BEBAS DIKETIK) ===== -->
        <main class="a4-desk flex-1 overflow-y-auto" id="deskScroll">
            <div class="a4-paper-sheet" id="paperSheet">
                <input type="hidden" id="cvId" value="">

                <!-- KOP SURAT RESMI (ON / OFF DARI HEADER) -->
                <div id="cvKop" style="position: relative; min-height: 24mm; margin-bottom: 4px; user-select: none;">
                    <img src="<?= base_url() ?>/surat/img/logo.png" alt="Logo UNG" style="position: absolute; top: 0; left: 0; width: 22mm;">
                    <div style="text-align: center; margin: 0 22mm;">
                        <p style="font-size: 12pt; margin: 0; line-height: 1.22;">KEMENTERIAN PENDIDIKAN,KEBUDAYAAN,<br>RISET DAN TEKNOLOGI<br>UNIVERSITAS NEGERI GORONTALO<br>FAKULTAS TEKNIK<br>JURUSAN TEKNIK ELEKTRO DAN KOMPUTER</p>
                        <p style="font-size: 10pt; margin: 0; line-height: 1.22;">Jalan B.J.Habibie Desa Moutong Kecamatan Tilongkabila Kab.Bone Bolango<br>Telp. (0435) 821125, Fax. (0435) 821752 Gorontalo<br>Laman :http:// www.ft.ung.ac.id</p>
                    </div>
                    <hr style="border: 1.5px solid #000; margin: 4px 0 0;">
                </div>
                <div id="cvSpacerNoKop" style="height: 12mm; display: none;"></div>

                <!-- AREA LEMBAR KERJA 100% POLOS / KOSONG BEBAS EDIT SEPERTI WORD -->
                <div id="wordCanvas"
                     contenteditable="true"
                     spellcheck="false"
                     class="word-canvas"
                     data-placeholder="Ketik surat Anda di sini... Lembar ini 100% polos layaknya Microsoft Word. Tekan Enter untuk membuat baris kosong, hapus apa saja dengan Backspace, atur format teks, atau sisipkan tabel dan gambar tanpa batas.">
                </div>

                <!-- INDIKATOR BATAS BAWAH KERTAS (VISUAL GUIDE) -->
                <div id="paperBottomGuide" class="paper-bottom-guide select-none" style="margin-top: 35px; padding-top: 12px; border-top: 1.5px dashed #cbd5e1; text-align: center; color: #94a3b8; font-family: system-ui, -apple-system, sans-serif; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; pointer-events: none;">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-500 shadow-sm border border-slate-200">
                        <i class="fas fa-arrows-down-to-line text-orange-500"></i>
                        <span id="labelBatasBawah">Batas Bawah Kertas A4 (297 mm &bull; Margin Bawah 2.5 cm)</span>
                    </span>
                </div>

            </div>
        </main>
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
                    if (typeof callback === 'function') callback(res.data);
                }
            }
        });
    }

    // ================= RICH TEXT ENGINE (TINYMCE + NATIVE FALLBACK) =================
    function initTinyMCE() {
        if (!window.tinymce) return false;
        if (tinymce.get('wordCanvas')) return true;

        try {
            tinymce.init({
                selector: '#wordCanvas',
                inline: true,
                toolbar_persist: true,
                fixed_toolbar_container: '#deskTopRibbon',
                menubar: 'edit insert view format table tools',
                menu: {
                    edit: { title: 'Edit', items: 'undo redo | cut copy paste | selectall' },
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
                    'bullist numlist outdent indent | table tableprops | image hr | removeformat code'
                ],
                font_family_formats: 'Times New Roman=times new roman,times,serif; Arial=arial,helvetica,sans-serif; Calibri=calibri,sans-serif; Georgia=georgia,serif',
                font_size_formats: '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 24pt 36pt',
                line_height_formats: '1 1.15 1.25 1.35 1.5 2',
                table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | tablecellprops tablemergecells tablesplitcells',
                table_appearance_options: true,
                table_advtab: true,
                table_cell_advtab: true,
                table_row_advtab: true,
                skin: 'oxide-dark',
                content_css: 'default',
                images_upload_handler: function (blobInfo, progress) {
                    return new Promise(function (resolve, reject) {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', PROSES_URL);
                        xhr.onload = function () {
                            if (xhr.status !== 200) {
                                reject('HTTP Error: ' + xhr.status);
                                return;
                            }
                            try {
                                var json = JSON.parse(xhr.responseText);
                                if (!json || !json.status || !json.url) {
                                    reject(json.pesan || 'Gagal mengunggah gambar');
                                    return;
                                }
                                resolve(json.url);
                            } catch (e) {
                                reject('Respon server tidak valid');
                            }
                        };
                        xhr.onerror = function () {
                            reject('Koneksi upload gambar gagal');
                        };
                        var formData = new FormData();
                        formData.append('gambar_surat', blobInfo.blob(), blobInfo.filename());
                        formData.append('upload-gambar-surat', '1');
                        xhr.send(formData);
                    });
                },
                setup: function (editor) {
                    editor.on('init', function () {
                        $('#fallbackRibbon').hide();
                    });
                }
            });
            return true;
        } catch (e) {
            console.warn('TinyMCE initialization error, using native fallback:', e);
            return false;
        }
    }

    function setCanvasContent(html) {
        if (window.tinymce && tinymce.get('wordCanvas')) {
            tinymce.get('wordCanvas').setContent(html);
        } else {
            $('#wordCanvas').html(html);
        }
    }

    function getCanvasContent() {
        if (window.tinymce && tinymce.get('wordCanvas')) {
            return tinymce.get('wordCanvas').getContent();
        }
        return $('#wordCanvas').html().trim();
    }

    function insertCanvasContent(html) {
        if (window.tinymce && tinymce.get('wordCanvas')) {
            tinymce.get('wordCanvas').insertContent(html);
        } else {
            insertHtmlAtCursor(html);
        }
    }

    // Simpan & Pulihkan Seleksi Kursor (Untuk Fallback Native)
    function saveSelection() {
        var sel = window.getSelection();
        if (sel && sel.rangeCount > 0) {
            savedRange = sel.getRangeAt(0);
        }
    }

    function restoreSelection() {
        var canvas = document.getElementById('wordCanvas');
        if (savedRange) {
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(savedRange);
        } else {
            canvas.focus();
        }
    }

    function insertHtmlAtCursor(html) {
        restoreSelection();
        document.getElementById('wordCanvas').focus();
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
        var canvas = document.getElementById('wordCanvas');

        // Rekam posisi kursor saat mengetik atau mengklik di kanvas
        $('#wordCanvas').on('keyup mouseup focus', function() {
            saveSelection();
        });

        // Cegah tombol toolbar menghilangkan fokus/seleksi teks di kanvas
        $(document).on('mousedown', '.sb-btn, #btnSisipTtd, #btnSisipNomor, #btnQuickTable, #btnInsertHr', function(e) {
            e.preventDefault();
        });

        // Pengatur Ukuran Kertas (A4 / F4)
        function setPaperSize(size) {
            var s = (size || 'A4').toUpperCase();
            if (s === 'F4') {
                $('#paperSheet').addClass('is-f4');
                $('#labelBatasBawah').html('Batas Bawah Kertas F4 / Folio (330 mm &bull; Margin Bawah 2.5 cm)');
                $('#swUkuranKertas').val('F4');
                $('#labelTombolCetak').text('Cetak F4');
            } else {
                $('#paperSheet').removeClass('is-f4');
                $('#labelBatasBawah').html('Batas Bawah Kertas A4 (297 mm &bull; Margin Bawah 2.5 cm)');
                $('#swUkuranKertas').val('A4');
                $('#labelTombolCetak').text('Cetak A4');
            }
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
                    $('#swTtdProdi').removeClass('hidden');
                    if (d.ttd_prodi) $('#swTtdProdi').val(d.ttd_prodi);
                } else {
                    $('#swTtdProdi').addClass('hidden');
                }

                currentPejabat = {
                    dosen_id: d.ttd_dosen_id || '',
                    nama: d.ttd_nama || 'Nama Pejabat',
                    nip: d.ttd_nip || '-',
                    jabatan: d.ttd_jabatan || 'Ketua Jurusan'
                };

                // Muat isi kanvas persis apa yang tersimpan
                setCanvasContent(d.isi_surat || '<p><br></p>');
            } else {
                $('#canvasTitle').text('Surat Baru (Lembar Polos)');
                $('#cvId').val('');
                $('#swTanggalSurat').val('<?= date('Y-m-d') ?>');
                $('#swPakaiKop').prop('checked', true);
                updateKopCanvas(true);
                setPaperSize('A4');
                $('#swKategori').val('Undangan');
                $('#swPerihal').val('');
                $('#swNomorSurat').val('');
                $('#swTtdJenis').val('kajur');
                $('#swTtdProdi').addClass('hidden');

                // 100% LEMBAR POLOS / KOSONG! Bebas diketik operator dari awal
                setCanvasContent('<p><br></p>');

                // Muat data pejabat dan nomor di background tanpa mengubah isi lembar polos
                loadPejabat('kajur', '');
                tarikNomorOtomatis();
            }

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';

            setTimeout(function() {
                initTinyMCE();
                canvas.focus();
                saveSelection();
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

        // Tarik Nomor Otomatis dari SIATEK
        function tarikNomorOtomatis(callback) {
            var kat = $('#swKategori').val() || 'surat custom';
            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: { 'ambil-nomor-otomatis': 1, 'kategori': kat },
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

        // Sisipkan Nomor Surat ke Posisi Kursor
        $('#btnSisipNomor').on('click', function() {
            var no = $('#swNomorSurat').val().trim();
            if (no) {
                insertCanvasContent(no);
            } else {
                alert('Nomor surat belum terisi. Klik "Auto No" terlebih dahulu.');
            }
        });

        // Ubah Pejabat Penandatangan
        $('#swTtdJenis').on('change', function() {
            var val = $(this).val();
            if (val === 'kaprodi') {
                $('#swTtdProdi').removeClass('hidden');
                loadPejabat('kaprodi', $('#swTtdProdi').val());
            } else {
                $('#swTtdProdi').addClass('hidden');
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
            $('#wordCanvas').css('line-height', lh);
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

        // Fungsi Inti Simpan Dokumen (Bisa dipanggil oleh tombol Simpan atau Cetak)
        function simpanSurat(callback, tutupSetelahSimpan) {
            var isiHtml = getCanvasContent();
            var perihal = $('#swPerihal').val().trim();
            var nomor = $('#swNomorSurat').val().trim();

            if (!isiHtml || isiHtml === '<p><br></p>' || isiHtml === '<br>') {
                alert('Isi surat pada lembar kerja tidak boleh kosong.');
                document.getElementById('wordCanvas').focus();
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
