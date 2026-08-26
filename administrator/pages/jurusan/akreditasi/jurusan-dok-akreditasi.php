<?php
/**
 * Halaman: Dokumen Akreditasi
 * Data dummy murni HTML (tanpa PHP/DB). Layout: accordion per kriteria
 * (Kriteria 1-9). JS hanya untuk: buka/tutup accordion, cari + filter kategori/tahun,
 * buka semua/tutup semua, pagination per kriteria, dan modal unggah/edit (dummy).
 */
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
    #toastDok { position: fixed; right: 20px; bottom: 20px; z-index: 2000; display: none; align-items: center; gap: .6rem; max-width: 360px; border-radius: .75rem; background: #0f172a; color: #fff; padding: .7rem 1rem; font-size: .82rem; box-shadow: 0 12px 24px rgba(15,23,42,.25); }
    #toastDok.show { display: flex; animation: riseIn .3s ease; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-lg text-sky-600">
                    <i class="fas fa-award"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Dokumen Akreditasi</h1>
                    <p class="text-xs text-slate-500">Dokumen pendukung akreditasi jurusan Teknik Elektro &amp; Komputer, dikelompokkan per kriteria (Kriteria 1-9).</p>
                </div>
            </div>
            <button type="button" data-upload-open class="btn-circle btn-circle-lg bg-sky-500 text-white shadow-md shadow-sky-500/25 hover:bg-sky-600">
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
                <option value="c1">Kriteria 1 - Visi, Misi, Tujuan dan Strategi</option>
                <option value="c2">Kriteria 2 - Tata Pamong, Tata Kelola dan Kerjasama</option>
                <option value="c3">Kriteria 3 - Mahasiswa</option>
                <option value="c4">Kriteria 4 - Sumber Daya Manusia</option>
                <option value="c5">Kriteria 5 - Keuangan, Sarana dan Prasarana</option>
                <option value="c6">Kriteria 6 - Pendidikan</option>
                <option value="c7">Kriteria 7 - Penelitian</option>
                <option value="c8">Kriteria 8 - Pengabdian kepada Masyarakat</option>
                <option value="c9">Kriteria 9 - Luaran dan Capaian Tridharma</option>
            </select>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                <option value="">Semua Tahun</option>
                <option value="2022">2022</option>
                <option value="2021">2021</option>
                <option value="2020">2020</option>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
        </div>
    </section>

    <!-- ===== Ringkasan + Buka/Tutup Semua ===== -->
    <section class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs text-slate-500">Menampilkan <b id="jmlDitemukan" class="text-slate-800">108</b> dokumen</p>
        <button type="button" id="btnExpandAll" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100">
            <i class="fas fa-expand-alt text-[10px]"></i> Buka Semua
        </button>
    </section>

    <!-- ===== Accordion per Kriteria ===== -->
    <section class="space-y-4">
        <!-- KRITERIA 1 -->
        <div class="acc-item acc-c1 reveal open" data-cat="c1">
            <button type="button" class="acc-head" aria-expanded="true">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600"><i class="fas fa-bullseye"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">Kriteria 1 - Visi, Misi, Tujuan dan Strategi</span>
                    <span class="block text-xs text-slate-500">Dokumen visi misi, renstra, dan strategi pencapaian</span>
                </span>
                <span class="acc-badge" id="cnt-c1">12</span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <div class="dok-row" data-tahun="2021" data-cari="dokumen visi misi jurusan dokumen admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Dokumen Visi Misi Jurusan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="rencana strategis 2021-2025 dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Rencana Strategis 2021-2025</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="rencana operasional 2021-2025 dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Rencana Operasional 2021-2025</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2020" data-cari="sk penetapan visi misi jurusan sk admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SK Penetapan Visi Misi Jurusan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2020</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="berita acara sosialisasi visi misi berita acara sekretaris jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Berita Acara Sosialisasi Visi Misi</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-berita">Berita Acara</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Sekretaris Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="matriks analisis swot dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Matriks Analisis SWOT</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2020" data-cari="sk tim penyusun renstra sk admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SK Tim Penyusun Renstra</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2020</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan evaluasi capaian renstra 2022 lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Evaluasi Capaian Renstra 2022</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="panduan mekanisme evaluasi visi misi dokumen admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Panduan Mekanisme Evaluasi Visi Misi</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan umpan balik pemangku kepentingan lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Umpan Balik Pemangku Kepentingan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2020" data-cari="berita acara penetapan visi misi berita acara sekretaris jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Berita Acara Penetapan Visi Misi</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-berita">Berita Acara</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Sekretaris Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2020</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="dokumen strategi pencapaian dokumen admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Dokumen Strategi Pencapaian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen  yang cocok.</p>
                        <div class="acc-pager flex items-center justify-between border-t border-slate-100 pt-3">
                            <button type="button" class="pg-btn pg-prev"><i class="fas fa-chevron-left text-xs"></i></button>
                            <p class="pg-label text-xs font-semibold text-slate-500">Halaman 1 / 1</p>
                            <button type="button" class="pg-btn pg-next"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- KRITERIA 2 -->
        <div class="acc-item acc-c2 reveal " data-cat="c2" style="animation-delay:.01s">
            <button type="button" class="acc-head" aria-expanded="false">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-lg text-sky-600"><i class="fas fa-sitemap"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">Kriteria 2 - Tata Pamong, Tata Kelola dan Kerjasama</span>
                    <span class="block text-xs text-slate-500">Organisasi, SOP, dan dokumen kerjasama</span>
                </span>
                <span class="acc-badge" id="cnt-c2">12</span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <div class="dok-row" data-tahun="2020" data-cari="sk struktur organisasi jurusan sk admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SK Struktur Organisasi Jurusan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2020</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2020" data-cari="bagan organisasi jurusan dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Bagan Organisasi Jurusan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2020</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="sop tata kelola jurusan dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SOP Tata Kelola Jurusan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="sk pengangkatan ketua jurusan sk admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SK Pengangkatan Ketua Jurusan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan kinerja jurusan lainnya sekretaris jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Kinerja Jurusan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Sekretaris Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="dokumen kerjasama dalam negeri dokumen koordinator prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Dokumen Kerjasama Dalam Negeri</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Koordinator Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="dokumen kerjasama internasional dokumen koordinator prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Dokumen Kerjasama Internasional</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Koordinator Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="berita acara rapat tinjauan manajemen berita acara sekretaris jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Berita Acara Rapat Tinjauan Manajemen</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-berita">Berita Acara</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Sekretaris Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="sk penjaminan mutu jurusan sk gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SK Penjaminan Mutu Jurusan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan audit mutu internal lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Audit Mutu Internal</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="surat keputusan kerjasama mou moa sk koordinator prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Surat Keputusan Kerjasama MoU MoA</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Koordinator Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan pelaksanaan kerjasama lainnya koordinator prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Pelaksanaan Kerjasama</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Koordinator Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen  yang cocok.</p>
                        <div class="acc-pager flex items-center justify-between border-t border-slate-100 pt-3">
                            <button type="button" class="pg-btn pg-prev"><i class="fas fa-chevron-left text-xs"></i></button>
                            <p class="pg-label text-xs font-semibold text-slate-500">Halaman 1 / 1</p>
                            <button type="button" class="pg-btn pg-next"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- KRITERIA 3 -->
        <div class="acc-item acc-c3 reveal " data-cat="c3" style="animation-delay:.02s">
            <button type="button" class="acc-head" aria-expanded="false">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-lg text-emerald-600"><i class="fas fa-user-graduate"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">Kriteria 3 - Mahasiswa</span>
                    <span class="block text-xs text-slate-500">Statistik, penerimaan, beasiswa, dan layanan mahasiswa</span>
                </span>
                <span class="acc-badge" id="cnt-c3">12</span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <div class="dok-row" data-tahun="2022" data-cari="statistik mahasiswa aktif dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Statistik Mahasiswa Aktif</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="daftar mahasiswa baru 2022 dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Daftar Mahasiswa Baru 2022</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan daya tampung dan peminat lainnya admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Daya Tampung dan Peminat</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="pedoman penerimaan mahasiswa baru dokumen admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Pedoman Penerimaan Mahasiswa Baru</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="rekapitulasi prestasi mahasiswa dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Rekapitulasi Prestasi Mahasiswa</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="berita acara pbak mahasiswa baru berita acara sekretaris jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Berita Acara PBAK Mahasiswa Baru</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-berita">Berita Acara</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Sekretaris Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan beasiswa mahasiswa lainnya admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Beasiswa Mahasiswa</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="data lulusan dan masa studi dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Data Lulusan dan Masa Studi</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="hasil tracer study lulusan lainnya koordinator prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Hasil Tracer Study Lulusan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Koordinator Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="pedoman bimbingan konseling mahasiswa dokumen admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Pedoman Bimbingan Konseling Mahasiswa</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="statistik kelulusan tepat waktu dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Statistik Kelulusan Tepat Waktu</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="berita acara orientasi mahasiswa berita acara sekretaris jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Berita Acara Orientasi Mahasiswa</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-berita">Berita Acara</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Sekretaris Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen  yang cocok.</p>
                        <div class="acc-pager flex items-center justify-between border-t border-slate-100 pt-3">
                            <button type="button" class="pg-btn pg-prev"><i class="fas fa-chevron-left text-xs"></i></button>
                            <p class="pg-label text-xs font-semibold text-slate-500">Halaman 1 / 1</p>
                            <button type="button" class="pg-btn pg-next"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- KRITERIA 4 -->
        <div class="acc-item acc-c4 reveal " data-cat="c4" style="animation-delay:.03s">
            <button type="button" class="acc-head" aria-expanded="false">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-lg text-violet-600"><i class="fas fa-users"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">Kriteria 4 - Sumber Daya Manusia</span>
                    <span class="block text-xs text-slate-500">Dosen dan tenaga kependidikan</span>
                </span>
                <span class="acc-badge" id="cnt-c4">12</span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <div class="dok-row" data-tahun="2022" data-cari="daftar dosen tetap dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Daftar Dosen Tetap</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="daftar dosen tidak tetap dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Daftar Dosen Tidak Tetap</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="sk dosen tetap jurusan sk admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SK Dosen Tetap Jurusan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="data tenaga kependidikan dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Data Tenaga Kependidikan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="beban kerja dosen bkd lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Beban Kerja Dosen BKD</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="sk mutasi dosen sk admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SK Mutasi Dosen</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="sertifikat kompetensi dosen dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Sertifikat Kompetensi Dosen</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="rencana pengembangan sdm dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Rencana Pengembangan SDM</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="daftar dosen pembimbing akademik dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Daftar Dosen Pembimbing Akademik</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="data kualifikasi pendidikan dosen dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Data Kualifikasi Pendidikan Dosen</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="sk dosen pengampu mata kuliah sk admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SK Dosen Pengampu Mata Kuliah</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan kepuasan kinerja sdm lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Kepuasan Kinerja SDM</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen  yang cocok.</p>
                        <div class="acc-pager flex items-center justify-between border-t border-slate-100 pt-3">
                            <button type="button" class="pg-btn pg-prev"><i class="fas fa-chevron-left text-xs"></i></button>
                            <p class="pg-label text-xs font-semibold text-slate-500">Halaman 1 / 1</p>
                            <button type="button" class="pg-btn pg-next"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- KRITERIA 5 -->
        <div class="acc-item acc-c5 reveal " data-cat="c5" style="animation-delay:.04s">
            <button type="button" class="acc-head" aria-expanded="false">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-lg text-indigo-600"><i class="fas fa-coins"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">Kriteria 5 - Keuangan, Sarana dan Prasarana</span>
                    <span class="block text-xs text-slate-500">Pengelolaan keuangan serta sarana prasarana</span>
                </span>
                <span class="acc-badge" id="cnt-c5">12</span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <div class="dok-row" data-tahun="2022" data-cari="laporan keuangan jurusan lainnya bendahara jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Keuangan Jurusan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Bendahara Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="dokumen sumber dana dokumen bendahara jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Dokumen Sumber Dana</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Bendahara Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="daftar ruang dan fasilitas dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Daftar Ruang dan Fasilitas</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="inventaris sarana dan prasarana lainnya admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Inventaris Sarana dan Prasarana</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan pemeliharaan prasarana lainnya admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Pemeliharaan Prasarana</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="sk pengelolaan keuangan sk admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SK Pengelolaan Keuangan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="dokumen perawatan sarana dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Dokumen Perawatan Sarana</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="data ketersediaan buku perpustakaan dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Data Ketersediaan Buku Perpustakaan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan penggunaan anggaran lainnya bendahara jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Penggunaan Anggaran</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Bendahara Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="sop pengelolaan sarana prasarana dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SOP Pengelolaan Sarana Prasarana</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="daftar peralatan laboratorium dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Daftar Peralatan Laboratorium</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan audit keuangan lainnya bendahara jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Audit Keuangan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Bendahara Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen  yang cocok.</p>
                        <div class="acc-pager flex items-center justify-between border-t border-slate-100 pt-3">
                            <button type="button" class="pg-btn pg-prev"><i class="fas fa-chevron-left text-xs"></i></button>
                            <p class="pg-label text-xs font-semibold text-slate-500">Halaman 1 / 1</p>
                            <button type="button" class="pg-btn pg-next"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- KRITERIA 6 -->
        <div class="acc-item acc-c6 reveal " data-cat="c6" style="animation-delay:.05s">
            <button type="button" class="acc-head" aria-expanded="false">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-lg text-amber-600"><i class="fas fa-book-open"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">Kriteria 6 - Pendidikan</span>
                    <span class="block text-xs text-slate-500">Kurikulum, pembelajaran, dan penilaian</span>
                </span>
                <span class="acc-badge" id="cnt-c6">12</span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <div class="dok-row" data-tahun="2022" data-cari="kurikulum jurusan 2022 dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Kurikulum Jurusan 2022</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="sk pembentukan tim kurikulum sk admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SK Pembentukan Tim Kurikulum</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="pedoman akademik dokumen admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Pedoman Akademik</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="contoh rps mata kuliah dokumen koordinator prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Contoh RPS Mata Kuliah</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Koordinator Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan pelaksanaan perkuliahan lainnya sekretaris jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Pelaksanaan Perkuliahan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Sekretaris Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="berita acara rapat kurikulum berita acara sekretaris jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Berita Acara Rapat Kurikulum</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-berita">Berita Acara</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Sekretaris Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="statistik ketersediaan rps dokumen koordinator prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Statistik Ketersediaan RPS</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Koordinator Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="laporan penyusunan kurikulum lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Penyusunan Kurikulum</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="dokumen penilaian pembelajaran dokumen koordinator prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Dokumen Penilaian Pembelajaran</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Koordinator Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="kalender akademik dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Kalender Akademik</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="konsorsium dan jejaring kurikulum lainnya koordinator prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Konsorsium dan Jejaring Kurikulum</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Koordinator Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="berita acara monev pembelajaran berita acara gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Berita Acara Monev Pembelajaran</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-berita">Berita Acara</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen  yang cocok.</p>
                        <div class="acc-pager flex items-center justify-between border-t border-slate-100 pt-3">
                            <button type="button" class="pg-btn pg-prev"><i class="fas fa-chevron-left text-xs"></i></button>
                            <p class="pg-label text-xs font-semibold text-slate-500">Halaman 1 / 1</p>
                            <button type="button" class="pg-btn pg-next"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- KRITERIA 7 -->
        <div class="acc-item acc-c7 reveal " data-cat="c7" style="animation-delay:.06s">
            <button type="button" class="acc-head" aria-expanded="false">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-lg text-rose-600"><i class="fas fa-flask"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">Kriteria 7 - Penelitian</span>
                    <span class="block text-xs text-slate-500">Penelitian dosen dan luaran penelitian</span>
                </span>
                <span class="acc-badge" id="cnt-c7">12</span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <div class="dok-row" data-tahun="2022" data-cari="daftar penelitian dosen dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Daftar Penelitian Dosen</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="pedoman penelitian dokumen admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Pedoman Penelitian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="sk penelitian dosen sk admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SK Penelitian Dosen</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan penelitian dosen lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Penelitian Dosen</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="rekapitulasi luaran penelitian lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Rekapitulasi Luaran Penelitian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="daftar publikasi jurnal dosen dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Daftar Publikasi Jurnal Dosen</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="daftar prosiding seminar dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Daftar Prosiding Seminar</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="data perolehan hibah penelitian dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Data Perolehan Hibah Penelitian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan monev penelitian lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Monev Penelitian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="data hki dosen dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Data HKI Dosen</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="sk reviewer penelitian sk admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SK Reviewer Penelitian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="evaluasi capaian penelitian lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Evaluasi Capaian Penelitian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen  yang cocok.</p>
                        <div class="acc-pager flex items-center justify-between border-t border-slate-100 pt-3">
                            <button type="button" class="pg-btn pg-prev"><i class="fas fa-chevron-left text-xs"></i></button>
                            <p class="pg-label text-xs font-semibold text-slate-500">Halaman 1 / 1</p>
                            <button type="button" class="pg-btn pg-next"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- KRITERIA 8 -->
        <div class="acc-item acc-c8 reveal " data-cat="c8" style="animation-delay:.07s">
            <button type="button" class="acc-head" aria-expanded="false">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-teal-100 text-lg text-teal-600"><i class="fas fa-hands-helping"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">Kriteria 8 - Pengabdian kepada Masyarakat</span>
                    <span class="block text-xs text-slate-500">Pengabdian dosen dan luaran pengabdian</span>
                </span>
                <span class="acc-badge" id="cnt-c8">12</span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <div class="dok-row" data-tahun="2022" data-cari="daftar pengabdian dosen dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Daftar Pengabdian Dosen</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2021" data-cari="pedoman pengabdian masyarakat dokumen admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Pedoman Pengabdian Masyarakat</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="sk pelaksanaan pengabdian sk admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">SK Pelaksanaan Pengabdian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan pengabdian masyarakat lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Pengabdian Masyarakat</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="rekapitulasi luaran pengabdian lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Rekapitulasi Luaran Pengabdian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="data kkn mahasiswa dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Data KKN Mahasiswa</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="data hibah pengabdian dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Data Hibah Pengabdian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="publikasi hasil pengabdian dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Publikasi Hasil Pengabdian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan monev pengabdian lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Monev Pengabdian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="berita acara kegiatan pengabdian berita acara sekretaris jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Berita Acara Kegiatan Pengabdian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-berita">Berita Acara</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Sekretaris Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="dokumen pelatihan dan pendampingan dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Dokumen Pelatihan dan Pendampingan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="evaluasi capaian pengabdian lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Evaluasi Capaian Pengabdian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen  yang cocok.</p>
                        <div class="acc-pager flex items-center justify-between border-t border-slate-100 pt-3">
                            <button type="button" class="pg-btn pg-prev"><i class="fas fa-chevron-left text-xs"></i></button>
                            <p class="pg-label text-xs font-semibold text-slate-500">Halaman 1 / 1</p>
                            <button type="button" class="pg-btn pg-next"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- KRITERIA 9 -->
        <div class="acc-item acc-c9 reveal " data-cat="c9" style="animation-delay:.08s">
            <button type="button" class="acc-head" aria-expanded="false">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-lime-100 text-lg text-lime-600"><i class="fas fa-trophy"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-800">Kriteria 9 - Luaran dan Capaian Tridharma</span>
                    <span class="block text-xs text-slate-500">Capaian kinerja tridharma dan mutu lulusan</span>
                </span>
                <span class="acc-badge" id="cnt-c9">12</span>
                <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
            </button>
            <div class="acc-body">
                <div class="acc-inner">
                    <div class="space-y-2.5 border-t border-slate-100 bg-slate-50/60 p-4">
                        <div class="dok-row" data-tahun="2021" data-cari="sertifikat akreditasi program studi sk admin akreditasi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Sertifikat Akreditasi Program Studi</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-sk">SK</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Akreditasi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2021</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="capaian ipk mahasiswa dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Capaian IPK Mahasiswa</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="data kinerja penelitian dosen dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Data Kinerja Penelitian Dosen</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="data kinerja pengabdian dosen dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Data Kinerja Pengabdian Dosen</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="rekapitulasi hki dan paten dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Rekapitulasi HKI dan Paten</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="daftar publikasi internasional dokumen gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Daftar Publikasi Internasional</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="skoring dan matriks capaian lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Skoring dan Matriks Capaian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="laporan evaluasi diri lainnya gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Laporan Evaluasi Diri</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="data mutu lulusan dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Data Mutu Lulusan</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="statistik kualifikasi dosen s2 s3 dokumen admin prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Statistik Kualifikasi Dosen S2 S3</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-dokumen">Dokumen</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Admin Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="berita acara verifikasi capaian berita acara gkm jurusan">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Berita Acara Verifikasi Capaian</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-berita">Berita Acara</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">GKM Jurusan</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <div class="dok-row" data-tahun="2022" data-cari="tabel capaian sembilan kriteria lainnya koordinator prodi">
                            <span class="file-ico file-pdf"><i class="fas fa-file-pdf"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">Tabel Capaian Sembilan Kriteria</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="jenis-badge jenis-lainnya">Lainnya</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">Koordinator Prodi</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700">2022</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <div class="flex shrink-0 items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a><a href="#" data-edit-open class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a><a href="#" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></a></div>
                            </div>
                        </div>
                        <p class="acc-empty hidden"><i class="fas fa-inbox"></i>Tidak ada dokumen  yang cocok.</p>
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
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-upload mr-1 text-[#0ea5e9]"></i>Unggah Dokumen Akreditasi</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-upload-close>&times;</button>
        </div>
        <form id="formUpload" class="p-5 space-y-4">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Nama / Judul Dokumen</label>
                <input type="text" required placeholder="mis. SK Penetapan Visi Misi Jurusan"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400 focus:bg-white">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Kriteria</label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <option>Kriteria 1 - Visi, Misi, Tujuan dan Strategi</option>
                        <option>Kriteria 2 - Tata Pamong, Tata Kelola dan Kerjasama</option>
                        <option>Kriteria 3 - Mahasiswa</option>
                        <option>Kriteria 4 - Sumber Daya Manusia</option>
                        <option>Kriteria 5 - Keuangan, Sarana dan Prasarana</option>
                        <option>Kriteria 6 - Pendidikan</option>
                        <option>Kriteria 7 - Penelitian</option>
                        <option>Kriteria 8 - Pengabdian kepada Masyarakat</option>
                        <option>Kriteria 9 - Luaran dan Capaian Tridharma</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Jenis Dokumen</label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <option>SK</option><option>Dokumen</option><option>Berita Acara</option><option>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <option>2022</option><option>2021</option><option>2020</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Pengunggah</label>
                    <input type="text" id="inpPengunggah" value="Admin Akreditasi" readonly disabled
                           class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500 outline-none cursor-not-allowed">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">File Dokumen</label>
                <div class="flex items-center gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-3">
                    <i class="fas fa-file-pdf text-slate-400"></i>
                    <span id="namaFile" class="text-sm text-slate-500">Belum ada file dipilih</span>
                    <label class="ml-auto inline-flex cursor-pointer items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-700">
                        <i class="fas fa-folder-open text-xs"></i> Pilih File
                        <input type="file" id="inpFile" accept="application/pdf,.pdf" class="hidden">
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
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-pen mr-1 text-[#0ea5e9]"></i>Edit Dokumen Akreditasi</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-edit-close>&times;</button>
        </div>
        <form id="formEdit" class="p-5 space-y-4">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Nama / Judul Dokumen</label>
                <input type="text" id="inpEditNama" required placeholder="mis. SK Penetapan Visi Misi Jurusan"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400 focus:bg-white">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Kriteria</label>
                    <select id="inpEditKriteria" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <option>Kriteria 1 - Visi, Misi, Tujuan dan Strategi</option>
                        <option>Kriteria 2 - Tata Pamong, Tata Kelola dan Kerjasama</option>
                        <option>Kriteria 3 - Mahasiswa</option>
                        <option>Kriteria 4 - Sumber Daya Manusia</option>
                        <option>Kriteria 5 - Keuangan, Sarana dan Prasarana</option>
                        <option>Kriteria 6 - Pendidikan</option>
                        <option>Kriteria 7 - Penelitian</option>
                        <option>Kriteria 8 - Pengabdian kepada Masyarakat</option>
                        <option>Kriteria 9 - Luaran dan Capaian Tridharma</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Jenis Dokumen</label>
                    <select id="inpEditJenis" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <option>SK</option><option>Dokumen</option><option>Berita Acara</option><option>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
                    <select id="inpEditTahun" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <option>2022</option><option>2021</option><option>2020</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Pengunggah</label>
                    <input type="text" value="Admin Akreditasi" readonly disabled
                           class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500 outline-none cursor-not-allowed">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">File Dokumen</label>
                <div class="flex items-center gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-3">
                    <i class="fas fa-file-pdf text-slate-400"></i>
                    <span id="namaFileEdit" class="text-sm text-slate-500">File lama - ganti jika perlu</span>
                    <label class="ml-auto inline-flex cursor-pointer items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-700">
                        <i class="fas fa-folder-open text-xs"></i> Pilih File
                        <input type="file" id="inpEditFile" accept="application/pdf,.pdf" class="hidden">
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
    "use strict";
    var items = Array.prototype.slice.call(document.querySelectorAll(".acc-item"));
    var fCari = document.getElementById("fCari");
    var fTahun = document.getElementById("fTahun");
    var fKategori = document.getElementById("fKategori");
    var btnReset = document.getElementById("btnReset");
    var btnExpandAll = document.getElementById("btnExpandAll");
    var jmlEl = document.getElementById("jmlDitemukan");
    var semuaTerbuka = false;

    function setOpen(it, open) {
        it.classList.toggle("open", open);
        var h = it.querySelector(".acc-head");
        if (h) h.setAttribute("aria-expanded", open ? "true" : "false");
    }
    function openAll(open) {
        semuaTerbuka = open;
        items.forEach(function (it) { setOpen(it, open); });
        if (btnExpandAll) btnExpandAll.innerHTML = open
            ? "<i class=\"fas fa-compress-alt text-[10px]\"></i> Tutup Semua"
            : "<i class=\"fas fa-expand-alt text-[10px]\"></i> Buka Semua";
    }

    items.forEach(function (it) {
        var h = it.querySelector(".acc-head");
        if (h) h.addEventListener("click", function () {
            setOpen(it, !it.classList.contains("open"));
            semuaTerbuka = false;
            if (btnExpandAll) btnExpandAll.innerHTML = "<i class=\"fas fa-expand-alt text-[10px]\"></i> Buka Semua";
        });
    });
    if (btnExpandAll) btnExpandAll.addEventListener("click", function () { openAll(!semuaTerbuka); });

    function cocok(row) {
        var kata = (fCari && fCari.value || "").toLowerCase().trim();
        var thn = fTahun ? fTahun.value : "";
        if (kata !== "" && ((row.getAttribute("data-cari") || "").toLowerCase().indexOf(kata) === -1)) return false;
        if (thn !== "" && row.getAttribute("data-tahun") !== thn) return false;
        return true;
    }

    var PAGE_SIZE = 5;

    function renderItem(it) {
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
        it.style.display = catMatch ? "" : "none";
        var empty = it.querySelector(".acc-empty");
        if (empty) empty.classList.toggle("hidden", tampil.length > 0);
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
        var total = 0;
        var adaFilter = Boolean((fCari && fCari.value) || (fTahun && fTahun.value) || (fKategori && fKategori.value));
        items.forEach(function (it) {
            var n = renderItem(it);
            total += n;
            if (adaFilter) setOpen(it, n > 0);
        });
        if (jmlEl) jmlEl.textContent = total.toLocaleString("id-ID");
    }

    function resetPages() {
        items.forEach(function (it) { it.setAttribute("data-page", "1"); });
    }

    items.forEach(function (it) {
        var prev = it.querySelector(".pg-prev");
        var next = it.querySelector(".pg-next");
        if (prev) prev.addEventListener("click", function () {
            it.setAttribute("data-page", Math.max(1, parseInt(it.getAttribute("data-page") || "1", 10) - 1));
            renderItem(it);
        });
        if (next) next.addEventListener("click", function () {
            it.setAttribute("data-page", parseInt(it.getAttribute("data-page") || "1", 10) + 1);
            renderItem(it);
        });
    });

    if (fCari) fCari.addEventListener("input", function () { resetPages(); render(); });
    if (fTahun) fTahun.addEventListener("change", function () { resetPages(); render(); });
    if (fKategori) fKategori.addEventListener("change", function () { resetPages(); render(); });
    if (btnReset) btnReset.addEventListener("click", function () {
        if (fCari) fCari.value = "";
        if (fTahun) fTahun.value = "";
        if (fKategori) fKategori.value = "";
        openAll(false);
        if (items[0]) setOpen(items[0], true);
        resetPages();
        render();
    });

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
    var form = document.getElementById("formUpload");
    if (form) form.addEventListener("submit", function (e) {
        e.preventDefault();
        if (modal) modal.classList.remove("show");
        var toast = document.getElementById("toastDok");
        var teks = document.getElementById("toastTeks");
        if (teks) teks.textContent = "Dokumen disimpan (dummy) - belum terhubung ke server.";
        if (toast) { toast.classList.add("show"); setTimeout(function () { toast.classList.remove("show"); }, 2800); }
        form.reset();
        if (namaFile) namaFile.textContent = "Belum ada file dipilih";
    });

    var modalEdit = document.getElementById("editModal");
    var inpEditNama = document.getElementById("inpEditNama");
    var inpEditFile = document.getElementById("inpEditFile");
    var inpEditKriteria = document.getElementById("inpEditKriteria");
    var inpEditJenis = document.getElementById("inpEditJenis");
    var inpEditTahun = document.getElementById("inpEditTahun");
    document.querySelectorAll("[data-edit-open]").forEach(function (b) {
        b.addEventListener("click", function () {
            var row = b.closest(".dok-row");
            if (!row) return;
            var judul = row.querySelector("p.truncate");
            if (inpEditNama) inpEditNama.value = judul ? judul.textContent : "";
            var acc = row.closest(".acc-item");
            if (inpEditKriteria && acc) {
                var num = parseInt((acc.getAttribute("data-cat") || "c1").replace("c", ""), 10) || 1;
                if (inpEditKriteria.options[num - 1]) inpEditKriteria.selectedIndex = num - 1;
            }
            if (inpEditJenis) {
                var jb = row.querySelector(".jenis-badge");
                var jenis = jb ? jb.textContent.trim() : "";
                for (var o = 0; o < inpEditJenis.options.length; o++) {
                    if (inpEditJenis.options[o].text === jenis) { inpEditJenis.selectedIndex = o; break; }
                }
            }
            if (inpEditTahun) {
                var chips = row.querySelectorAll(".rounded-full");
                var thn = "";
                for (var c = 0; c < chips.length; c++) {
                    var txt = chips[c].textContent.trim();
                    if (/^\d{4}$/.test(txt)) { thn = txt; break; }
                }
                for (var o2 = 0; o2 < inpEditTahun.options.length; o2++) {
                    if (inpEditTahun.options[o2].text === thn) { inpEditTahun.selectedIndex = o2; break; }
                }
            }
            var nfe = document.getElementById("namaFileEdit");
            if (nfe) nfe.textContent = "File lama (PDF) - ganti jika perlu";
            if (modalEdit) modalEdit.classList.add("show");
        });
    });
    document.querySelectorAll("[data-edit-close]").forEach(function (b) {
        b.addEventListener("click", function () { if (modalEdit) modalEdit.classList.remove("show"); });
    });
    var formEdit = document.getElementById("formEdit");
    if (formEdit) formEdit.addEventListener("submit", function (e) {
        e.preventDefault();
        if (modalEdit) modalEdit.classList.remove("show");
        var toast = document.getElementById("toastDok");
        var teks = document.getElementById("toastTeks");
        if (teks) teks.textContent = "Perubahan disimpan (dummy) - belum terhubung ke server.";
        if (toast) { toast.classList.add("show"); setTimeout(function () { toast.classList.remove("show"); }, 2800); }
        formEdit.reset();
        if (inpEditFile) inpEditFile.value = "";
    });

    if (items[0]) setOpen(items[0], true);
    render();
})();
</script>
