<?php
/**
 * Halaman: Dokumen Akademik
 * Data dummy murni HTML (tanpa PHP/DB). Layout: accordion per kategori
 * (Kurikulum, RPS, Silabus, Lainnya). JS hanya untuk: buka/tutup accordion,
 * cari + filter tahun, buka semua/tutup semua, dan modal unggah (dummy).
 */
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    /* ===== Accordion ===== */
    .acc-item { position: relative; border: 1px solid #e2e8f0; background: #fff; border-radius: 1rem; overflow: hidden;
        box-shadow: 0 1px 2px rgba(15,23,42,.05); transition: border-color .2s ease, box-shadow .2s ease; }
    .acc-item::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--cat); opacity: 0; transition: opacity .2s ease; z-index: 1; }
    .acc-item.open { border-color: #cbd5e1; box-shadow: 0 10px 24px -12px rgba(15,23,42,.18); }
    .acc-item.open::before { opacity: 1; }
    .acc-kurikulum { --cat: #f97316; }
    .acc-rps       { --cat: #0ea5e9; }
    .acc-silabus   { --cat: #10b981; }
    .acc-lainnya   { --cat: #8b5cf6; }

    .acc-head { width: 100%; display: flex; align-items: center; gap: .9rem; padding: 1rem 1.25rem; text-align: left; background: #fff; cursor: pointer; transition: background .15s ease; }
    .acc-head:hover { background: #f8fafc; }
    .acc-chev { display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 9999px; background: #f1f5f9; color: #64748b; transition: transform .3s ease, background .15s ease, color .15s ease; }
    .acc-item.open .acc-chev { transform: rotate(180deg); }
    .acc-kurikulum.open .acc-chev { background: #f97316; color: #fff; }
    .acc-rps.open       .acc-chev { background: #0ea5e9; color: #fff; }
    .acc-silabus.open   .acc-chev { background: #10b981; color: #fff; }
    .acc-lainnya.open   .acc-chev { background: #8b5cf6; color: #fff; }

    .acc-badge { min-width: 1.75rem; text-align: center; border-radius: 9999px; padding: .15rem .6rem; font-size: .72rem; font-weight: 700; }
    .acc-kurikulum .acc-badge { background: #ffedd5; color: #c2410c; }
    .acc-rps       .acc-badge { background: #e0f2fe; color: #0369a1; }
    .acc-silabus   .acc-badge { background: #d1fae5; color: #047857; }
    .acc-lainnya   .acc-badge { background: #ede9fe; color: #6d28d9; }

    .acc-body { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .3s ease; }
    .acc-item.open .acc-body { grid-template-rows: 1fr; }
    .acc-inner { min-height: 0; overflow: hidden; }

    /* ===== Baris dokumen di dalam accordion ===== */
    .dok-row { display: flex; align-items: center; gap: .8rem; padding: .75rem .9rem; border-radius: .85rem; background: #fff; border: 1px solid #e2e8f0; transition: border-color .15s ease, box-shadow .15s ease; }
    .acc-kurikulum .dok-row:hover { border-color: #fdba74; box-shadow: 0 3px 10px -4px rgba(249,115,22,.25); }
    .acc-rps       .dok-row:hover { border-color: #7dd3fc; box-shadow: 0 3px 10px -4px rgba(14,165,233,.25); }
    .acc-silabus   .dok-row:hover { border-color: #6ee7b7; box-shadow: 0 3px 10px -4px rgba(16,185,129,.25); }
    .acc-lainnya   .dok-row:hover { border-color: #c4b5fd; box-shadow: 0 3px 10px -4px rgba(139,92,246,.25); }

    .file-ico { display: inline-flex; align-items: center; justify-content: center; width: 2.75rem; height: 2.75rem; border-radius: .75rem; font-size: 1.15rem; flex-shrink: 0; }
    .file-pdf   { background: #fee2e2; color: #dc2626; }
    .file-word  { background: #dbeafe; color: #2563eb; }
    .file-excel { background: #d1fae5; color: #059669; }
    .file-img   { background: #fef3c7; color: #d97706; }

    .acc-empty { padding: 1.1rem; text-align: center; border: 1px dashed #e2e8f0; border-radius: .85rem; background: #fff; color: #64748b; font-size: .8rem; }
    .acc-empty i { margin-right: .4rem; }

    .pg-btn { min-width: 2rem; height: 2rem; border-radius: .5rem; border: 1px solid #e2e8f0; background: #fff; color: #475569;
        font-size: .75rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .pg-btn:hover { border-color: #fdba74; color: #c2410c; }
    .pg-btn:disabled { opacity: .45; cursor: not-allowed; }

    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
    @media (prefers-reduced-motion: reduce) { .reveal { animation: none; } }

    #toastDok { position: fixed; right: 20px; bottom: 20px; z-index: 2000; display: none; align-items: center; gap: .6rem;
        max-width: 360px; border-radius: .75rem; background: #0f172a; color: #fff; padding: .7rem 1rem; font-size: .82rem;
        box-shadow: 0 12px 24px rgba(15,23,42,.25); }
    #toastDok.show { display: flex; animation: riseIn .3s ease; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Dokumen Akademik</h1>
                    <p class="text-xs text-slate-500">Kurikulum, RPS, silabus, dan dokumen akademik jurusan Teknik Elektro &amp; Komputer.</p>
                </div>
            </div>
            <button type="button" data-upload-open class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600">
                <i class="fas fa-plus text-sm"></i>
                <span class="tip">Unggah Dokumen</span>
            </button>
        </div>
    </section>

    <!-- ===== Toolbar (cari + filter tahun + reset) ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari nama dokumen, mata kuliah, kode, prodi, pengunggah…"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <select id="fKategori" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Kategori</option>
                <option value="kurikulum">Kurikulum</option>
                <option value="rps">RPS</option>
                <option value="silabus">Silabus</option>
                <option value="lainnya">Lainnya</option>
            </select>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Tahun</option>
                <option value="2025">2025</option>
                <option value="2024">2024</option>
                <option value="2023">2023</option>
                <option value="2022">2022</option>
                <option value="2021">2021</option>
                <option value="2020">2020</option>
                <option value="2019">2019</option>
                <option value="2018">2018</option>
                <option value="2017">2017</option>
                <option value="2016">2016</option>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
        </div>
    </section>

    <!-- ===== Ringkasan + Buka/Tutup Semua ===== -->
    <section class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs text-slate-500">Menampilkan <b id="jmlDitemukan" class="text-slate-800">20</b> dokumen</p>
        <button type="button" id="btnExpandAll" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100">
            <i class="fas fa-expand-alt text-[10px]"></i> Buka Semua
        </button>
    </section>

    <!-- ===== Accordion per Kategori ===== -->
    <section class="space-y-4">

        <!-- KURIKULUM -->
        <div class="acc-item acc-kurikulum reveal" data-cat="kurikulum">
            <button type="button" class="acc-head" aria-expanded="true">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600"><i class="fas fa-book-open"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">Kurikulum</span>
                    <span class="block text-xs text-slate-500">Dokumen kurikulum jurusan (S1 &amp; D3)</span>
                </span>
                <span class="acc-badge" id="cnt-kurikulum">4</span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <div class="dok-row" data-tahun="2022" data-cari="kurikulum 2022 d3 teknik elektro sk rektor no 1321 un43 2022">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Kurikulum 2022</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">D3 Teknik Elektro</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">SK Rektor No. 1321/UN43/2022</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2020" data-cari="kurikulum 2020 s1 teknik komputer sk rektor no 1205 un43 2020">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Kurikulum 2020</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">S1 Teknik Komputer</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">SK Rektor No. 1205/UN43/2020</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2020</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2020" data-cari="kurikulum 2020 s1 teknik elektro sk rektor no 1204 un43 2020">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Kurikulum 2020</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">S1 Teknik Elektro</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">SK Rektor No. 1204/UN43/2020</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2020</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2016" data-cari="kurikulum 2016 s1 teknik elektro sk rektor no 0845 un43 2016">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Kurikulum 2016</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">S1 Teknik Elektro</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">SK Rektor No. 0845/UN43/2016</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2016</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen Kurikulum yang cocok.</p>
                        <div class="acc-pager flex items-center justify-between border-t border-slate-100 pt-3">
                            <button type="button" class="pg-btn pg-prev"><i class="fas fa-chevron-left text-xs"></i></button>
                            <p class="pg-label text-xs font-semibold text-slate-500">Halaman 1 / 1</p>
                            <button type="button" class="pg-btn pg-next"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RPS -->
        <div class="acc-item acc-rps reveal" data-cat="rps" style="animation-delay:.04s">
            <button type="button" class="acc-head" aria-expanded="false">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-lg text-sky-600"><i class="fas fa-file-lines"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">RPS</span>
                    <span class="block text-xs text-slate-500">Rencana Pembelajaran Semester tiap mata kuliah</span>
                </span>
                <span class="acc-badge" id="cnt-rps">6</span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <div class="dok-row" data-tahun="2024" data-cari="rps rangkaian listrik i eli-101 s1 teknik elektro">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Rangkaian Listrik I</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-mono font-medium text-slate-600">ELI-101</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Smt 1 &middot; 3 SKS</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">S1 Teknik Elektro</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2024</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2024" data-cari="rps sistem kendali eli-208 s1 teknik elektro">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Sistem Kendali</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-mono font-medium text-slate-600">ELI-208</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Smt 4 &middot; 3 SKS</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">S1 Teknik Elektro</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2024</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2024" data-cari="rps basis data kom-205 s1 teknik komputer">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Basis Data</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-mono font-medium text-slate-600">KOM-205</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Smt 3 &middot; 3 SKS</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">S1 Teknik Komputer</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2024</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2023" data-cari="rps jaringan komputer kom-301 s1 teknik komputer">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Jaringan Komputer</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-mono font-medium text-slate-600">KOM-301</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Smt 5 &middot; 3 SKS</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">S1 Teknik Komputer</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2023</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2023" data-cari="rps elektronika daya eli-305 s1 teknik elektro">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Elektronika Daya</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-mono font-medium text-slate-600">ELI-305</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Smt 5 &middot; 3 SKS</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">S1 Teknik Elektro</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2023</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2023" data-cari="rps sistem digital tek-203 s1 teknik elektro">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Sistem Digital</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-mono font-medium text-slate-600">TEK-203</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Smt 3 &middot; 3 SKS</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">S1 Teknik Elektro</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2023</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen RPS yang cocok.</p>
                        <div class="acc-pager flex items-center justify-between border-t border-slate-100 pt-3">
                            <button type="button" class="pg-btn pg-prev"><i class="fas fa-chevron-left text-xs"></i></button>
                            <p class="pg-label text-xs font-semibold text-slate-500">Halaman 1 / 1</p>
                            <button type="button" class="pg-btn pg-next"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SILABUS -->
        <div class="acc-item acc-silabus reveal" data-cat="silabus" style="animation-delay:.08s">
            <button type="button" class="acc-head" aria-expanded="false">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-lg text-emerald-600"><i class="fas fa-clipboard-list"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">Silabus</span>
                    <span class="block text-xs text-slate-500">Silabus setiap mata kuliah</span>
                </span>
                <span class="acc-badge" id="cnt-silabus">5</span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <div class="dok-row" data-tahun="2024" data-cari="silabus mesin listrik eli-306 s1 teknik elektro">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Mesin Listrik</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-mono font-medium text-slate-600">ELI-306</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Smt 6 &middot; 3 SKS</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">S1 Teknik Elektro</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2024</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2023" data-cari="silabus pemrograman terstruktur kom-102 s1 teknik komputer">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Pemrograman Terstruktur</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-mono font-medium text-slate-600">KOM-102</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Smt 1 &middot; 3 SKS</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">S1 Teknik Komputer</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2023</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2023" data-cari="silabus mikrokontroler eli-302 s1 teknik elektro">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Mikrokontroler</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-mono font-medium text-slate-600">ELI-302</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Smt 5 &middot; 3 SKS</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">S1 Teknik Elektro</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2023</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="silabus matematika teknik uni-201 s1 teknik komputer">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Matematika Teknik</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-mono font-medium text-slate-600">UNI-201</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Smt 3 &middot; 3 SKS</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">S1 Teknik Komputer</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="silabus fisika dasar ii uni-104 s1 teknik elektro">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Fisika Dasar II</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-mono font-medium text-slate-600">UNI-104</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Smt 2 &middot; 3 SKS</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">S1 Teknik Elektro</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen Silabus yang cocok.</p>
                        <div class="acc-pager flex items-center justify-between border-t border-slate-100 pt-3">
                            <button type="button" class="pg-btn pg-prev"><i class="fas fa-chevron-left text-xs"></i></button>
                            <p class="pg-label text-xs font-semibold text-slate-500">Halaman 1 / 1</p>
                            <button type="button" class="pg-btn pg-next"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LAINNYA -->
        <div class="acc-item acc-lainnya reveal" data-cat="lainnya" style="animation-delay:.12s">
            <button type="button" class="acc-head" aria-expanded="false">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-lg text-violet-600"><i class="fas fa-folder-open"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">Lainnya</span>
                    <span class="block text-xs text-slate-500">Kalender akademik, surat edaran, panduan, formulir</span>
                </span>
                <span class="acc-badge" id="cnt-lainnya">5</span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <div class="dok-row" data-tahun="2025" data-cari="kalender akademik semester ganjil 2025 2026 bagian akademik">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Kalender Akademik Semester Ganjil 2025/2026</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-violet-100 px-2 py-0.5 font-medium text-violet-700">Kalender</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Bagian Akademik</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2025</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2025" data-cari="surat edaran uts ganjil 2025 2026 bagian akademik">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Surat Edaran UTS Ganjil 2025/2026</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-violet-100 px-2 py-0.5 font-medium text-violet-700">Surat Edaran</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Bagian Akademik</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2025</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2024" data-cari="panduan skripsi dan tugas akhir 2024 humas jtek">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Panduan Skripsi &amp; Tugas Akhir</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-violet-100 px-2 py-0.5 font-medium text-violet-700">Panduan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Humas JTEK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2024</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2024" data-cari="kalender akademik semester genap 2024 2025 bagian akademik">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Kalender Akademik Semester Genap 2024/2025</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-violet-100 px-2 py-0.5 font-medium text-violet-700">Kalender</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Bagian Akademik</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2024</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2023" data-cari="format krs dan khs formulir bagian akademik">
                            <span class="file-ico file-excel"><i class="fas fa-file-excel"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Format KRS &amp; KHS</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="rounded-full bg-violet-100 px-2 py-0.5 font-medium text-violet-700">Formulir</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Bagian Akademik</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2023</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen lain yang cocok.</p>
                        <div class="acc-pager flex items-center justify-between border-t border-slate-100 pt-3">
                            <button type="button" class="pg-btn pg-prev"><i class="fas fa-chevron-left text-xs"></i></button>
                            <p class="pg-label text-xs font-semibold text-slate-500">Halaman 1 / 1</p>
                            <button type="button" class="pg-btn pg-next"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

</main>

<!-- ===== Modal Unggah Dokumen (dummy) ===== -->
<div class="modal-overlay" id="uploadModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-upload mr-1 text-[#f97316]"></i>Unggah Dokumen Akademik</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-upload-close>&times;</button>
        </div>
        <form id="formUpload" class="p-5 space-y-4">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Nama / Judul Dokumen</label>
                <input type="text" required placeholder="mis. RPS Rangkaian Listrik II"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Kategori</label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>Kurikulum</option>
                        <option>RPS</option>
                        <option>Silabus</option>
                        <option>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Program Studi</label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>S1 Teknik Elektro</option>
                        <option>S1 Teknik Komputer</option>
                        <option>D3 Teknik Elektro</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>2025</option><option>2024</option><option>2023</option><option>2022</option><option>2021</option><option>2020</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Semester</label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>Ganjil</option><option>Genap</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">File Dokumen</label>
                <div class="flex items-center gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-3">
                    <i class="fas fa-file-pdf text-slate-400"></i>
                    <span id="namaFile" class="text-sm text-slate-500">Belum ada file dipilih</span>
                    <label class="ml-auto inline-flex cursor-pointer items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-700">
                        <i class="fas fa-folder-open text-xs"></i> Pilih File
                        <input type="file" id="inpFile" class="hidden">
                    </label>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" data-upload-close class="px-3 py-2 text-xs rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-[#1a365d] hover:bg-[#234670] text-white font-medium">Simpan Dokumen</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Modal Edit Dokumen (dummy) ===== -->
<div class="modal-overlay" id="editModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-pen mr-1 text-[#0ea5e9]"></i>Edit Dokumen Akademik</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-edit-close>&times;</button>
        </div>
        <form id="formEdit" class="p-5 space-y-4">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Nama / Judul Dokumen</label>
                <input type="text" id="inpEditNama" required placeholder="mis. RPS Rangkaian Listrik II"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Kategori</label>
                    <select id="inpEditKategori" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>Kurikulum</option>
                        <option>RPS</option>
                        <option>Silabus</option>
                        <option>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Program Studi</label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>S1 Teknik Elektro</option>
                        <option>S1 Teknik Komputer</option>
                        <option>D3 Teknik Elektro</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>2025</option><option>2024</option><option>2023</option><option>2022</option><option>2021</option><option>2020</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Semester</label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>Ganjil</option><option>Genap</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">File Dokumen</label>
                <div class="flex items-center gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-3">
                    <i class="fas fa-file-pdf text-slate-400"></i>
                    <span class="text-sm text-slate-500">File lama — ganti jika perlu</span>
                    <label class="ml-auto inline-flex cursor-pointer items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-700">
                        <i class="fas fa-folder-open text-xs"></i> Pilih File
                        <input type="file" id="inpEditFile" class="hidden">
                    </label>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" data-edit-close class="px-3 py-2 text-xs rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-[#1a365d] hover:bg-[#234670] text-white font-medium">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div id="toastDok"><i class="fas fa-circle-check text-emerald-400"></i><span id="toastTeks"></span></div>

<script>
(function () {
    'use strict';
    var items = Array.prototype.slice.call(document.querySelectorAll('.acc-item'));
    var fCari = document.getElementById('fCari');
    var fTahun = document.getElementById('fTahun');
    var fKategori = document.getElementById('fKategori');
    var btnReset = document.getElementById('btnReset');
    var btnExpandAll = document.getElementById('btnExpandAll');
    var jmlEl = document.getElementById('jmlDitemukan');
    var semuaTerbuka = false;

    function setOpen(it, open) {
        it.classList.toggle('open', open);
        var h = it.querySelector('.acc-head');
        if (h) h.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    function openAll(open) {
        semuaTerbuka = open;
        items.forEach(function (it) { setOpen(it, open); });
        if (btnExpandAll) btnExpandAll.innerHTML = open
            ? '<i class="fas fa-compress-alt text-[10px]"></i> Tutup Semua'
            : '<i class="fas fa-expand-alt text-[10px]"></i> Buka Semua';
    }

    items.forEach(function (it) {
        var h = it.querySelector('.acc-head');
        if (h) h.addEventListener('click', function () {
            setOpen(it, !it.classList.contains('open'));
            semuaTerbuka = false;
            if (btnExpandAll) btnExpandAll.innerHTML = '<i class="fas fa-expand-alt text-[10px]"></i> Buka Semua';
        });
    });
    if (btnExpandAll) btnExpandAll.addEventListener('click', function () { openAll(!semuaTerbuka); });

    function cocok(row) {
        var kata = (fCari && fCari.value || '').toLowerCase().trim();
        var thn = fTahun ? fTahun.value : '';
        if (kata !== '' && ((row.getAttribute('data-cari') || '').toLowerCase().indexOf(kata) === -1)) return false;
        if (thn !== '' && row.getAttribute('data-tahun') !== thn) return false;
        return true;
    }

    var PAGE_SIZE = 5;

    function renderItem(it) {
        var kat = fKategori ? fKategori.value : '';
        var cat = it.getAttribute('data-cat');
        var catMatch = kat === '' || kat === cat;
        var cntEl = document.getElementById('cnt-' + cat);

        var tampil = [];
        it.querySelectorAll('.dok-row').forEach(function (r) {
            var show = catMatch && cocok(r);
            r.style.display = show ? '' : 'none';
            if (show) tampil.push(r);
        });
        it.style.display = catMatch ? '' : 'none';
        var empty = it.querySelector('.acc-empty');
        if (empty) empty.classList.toggle('hidden', tampil.length > 0);

        var totalHal = Math.max(1, Math.ceil(tampil.length / PAGE_SIZE));
        var page = parseInt(it.getAttribute('data-page') || '1', 10);
        if (page < 1) page = 1;
        if (page > totalHal) page = totalHal;
        it.setAttribute('data-page', page);

        var mulai = (page - 1) * PAGE_SIZE;
        for (var i = 0; i < tampil.length; i++) {
            tampil[i].style.display = (i >= mulai && i < mulai + PAGE_SIZE) ? '' : 'none';
        }

        if (cntEl) cntEl.textContent = tampil.length;
        var lbl = it.querySelector('.pg-label');
        if (lbl) lbl.textContent = 'Halaman ' + page + ' / ' + totalHal;
        var prev = it.querySelector('.pg-prev');
        var next = it.querySelector('.pg-next');
        if (prev) prev.disabled = page <= 1;
        if (next) next.disabled = page >= totalHal;
        return tampil.length;
    }

    function render() {
        var total = 0;
        var adaFilter = Boolean((fCari && fCari.value) || (fTahun && fTahun.value) || (fKategori && fKategori.value));
        items.forEach(function (it) {
            var n = renderItem(it);
            total += n;
            if (adaFilter) setOpen(it, n > 0);
        });
        if (jmlEl) jmlEl.textContent = total.toLocaleString('id-ID');
    }

    function resetPages() {
        items.forEach(function (it) { it.setAttribute('data-page', '1'); });
    }

    items.forEach(function (it) {
        var prev = it.querySelector('.pg-prev');
        var next = it.querySelector('.pg-next');
        if (prev) prev.addEventListener('click', function () {
            it.setAttribute('data-page', Math.max(1, parseInt(it.getAttribute('data-page') || '1', 10) - 1));
            renderItem(it);
        });
        if (next) next.addEventListener('click', function () {
            it.setAttribute('data-page', parseInt(it.getAttribute('data-page') || '1', 10) + 1);
            renderItem(it);
        });
    });

    if (fCari) fCari.addEventListener('input', function () { resetPages(); render(); });
    if (fTahun) fTahun.addEventListener('change', function () { resetPages(); render(); });
    if (fKategori) fKategori.addEventListener('change', function () { resetPages(); render(); });
    if (btnReset) btnReset.addEventListener('click', function () {
        if (fCari) fCari.value = '';
        if (fTahun) fTahun.value = '';
        if (fKategori) fKategori.value = '';
        openAll(false);
        if (items[0]) setOpen(items[0], true);
        resetPages();
        render();
    });

    /* ===== Modal unggah (dummy — belum terhubung ke server) ===== */
    var modal = document.getElementById('uploadModal');
    var inpFile = document.getElementById('inpFile');
    var namaFile = document.getElementById('namaFile');
    if (inpFile) inpFile.addEventListener('change', function () {
        if (namaFile) namaFile.textContent = inpFile.files && inpFile.files[0] ? inpFile.files[0].name : 'Belum ada file dipilih';
    });
    document.querySelectorAll('[data-upload-open]').forEach(function (b) {
        b.addEventListener('click', function () { if (modal) modal.classList.add('show'); });
    });
    document.querySelectorAll('[data-upload-close]').forEach(function (b) {
        b.addEventListener('click', function () { if (modal) modal.classList.remove('show'); });
    });
    var form = document.getElementById('formUpload');
    if (form) form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (modal) modal.classList.remove('show');
        var toast = document.getElementById('toastDok');
        var teks = document.getElementById('toastTeks');
        if (teks) teks.textContent = 'Dokumen disimpan (dummy) — belum terhubung ke server.';
        if (toast) { toast.classList.add('show'); setTimeout(function () { toast.classList.remove('show'); }, 2800); }
        form.reset();
        if (namaFile) namaFile.textContent = 'Belum ada file dipilih';
    });

    /* ===== Modal edit (dummy — isi diambil dari baris yang diklik) ===== */
    var modalEdit = document.getElementById('editModal');
    var inpEditNama = document.getElementById('inpEditNama');
    var inpEditFile = document.getElementById('inpEditFile');
    document.querySelectorAll('[data-edit-open]').forEach(function (b) {
        b.addEventListener('click', function () {
            var row = b.closest('.dok-row');
            var judul = row ? row.querySelector('p.truncate') : null;
            if (inpEditNama) inpEditNama.value = judul ? judul.textContent : '';
            if (modalEdit) modalEdit.classList.add('show');
        });
    });
    document.querySelectorAll('[data-edit-close]').forEach(function (b) {
        b.addEventListener('click', function () { if (modalEdit) modalEdit.classList.remove('show'); });
    });
    var formEdit = document.getElementById('formEdit');
    if (formEdit) formEdit.addEventListener('submit', function (e) {
        e.preventDefault();
        if (modalEdit) modalEdit.classList.remove('show');
        var toast = document.getElementById('toastDok');
        var teks = document.getElementById('toastTeks');
        if (teks) teks.textContent = 'Perubahan disimpan (dummy) — belum terhubung ke server.';
        if (toast) { toast.classList.add('show'); setTimeout(function () { toast.classList.remove('show'); }, 2800); }
        formEdit.reset();
        if (inpEditFile) inpEditFile.value = '';
    });

    if (items[0]) setOpen(items[0], true);
    render();
})();
</script>
