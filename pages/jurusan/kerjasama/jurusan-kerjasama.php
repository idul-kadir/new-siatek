<?php
/**
 * Halaman: Kerja Sama
 * Tampilan murni HTML statis (tanpa data PHP / JavaScript dinamis).
 * Karakteristik data mengikuti tabel `kerjasama`:
 * instansi, tanggal (tahun), deskripsi, tingkat, bidang, bukti, file, logo.
 * Satu kerja sama dapat memiliki lebih dari satu dokumen (IA, PKS, MoU) —
 * link dokumen menyatu pada badge bukti, sehingga satu bukti = satu dokumen.
 * JS hanya untuk: tab tingkat, cari, filter, pagination 15/halaman, modal tambah/edit, dan hapus.
 */
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    .tile-orange  { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }
    .tile-sky     { background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); }
    .tile-emerald { background: linear-gradient(135deg, #047857 0%, #10b981 100%); }
    .tile-violet  { background: linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%); }
    .tile-corak { position: relative; overflow: hidden; }
    .tile-corak::before { content: ""; position: absolute; inset: 0; pointer-events: none;
        background-image: radial-gradient(rgba(255,255,255,.22) 1px, transparent 1px);
        background-size: 12px 12px; opacity: .35; mix-blend-mode: overlay; }
    .tile-corak > * { position: relative; }


    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    .tip-wrap { position: relative; }
    .tip-wrap .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); max-width: 260px; overflow: hidden; text-overflow: ellipsis; }
    .tip-wrap:hover .tip { opacity: 1; visibility: visible; }

    .tab-btn { display: inline-flex; align-items: center; gap: .5rem; border-radius: .6rem; border: 1px solid #e2e8f0;
        background: #fff; color: #475569; padding: .55rem .9rem; font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .tab-btn:hover { border-color: #fdba74; color: #c2410c; }
    .tab-btn .tdot { width: 8px; height: 8px; border-radius: 9999px; }
    .tab-btn .tnum { min-width: 22px; text-align: center; border-radius: 9999px; padding: 1px 6px; font-size: .7rem; background: #f1f5f9; color: #64748b; }
    .tab-btn[aria-selected="true"] { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }
    .tab-btn[aria-selected="true"] .tnum { background: rgba(255,255,255,.18); color: #fff; }

    .pg-btn { min-width: 2rem; height: 2rem; border-radius: .5rem; border: 1px solid #e2e8f0; background: #fff; color: #475569;
        font-size: .75rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .pg-btn:hover { border-color: #fdba74; color: #c2410c; }
    .pg-btn:disabled { opacity: .45; cursor: not-allowed; }

    .doc-row { display: flex; align-items: center; gap: .5rem; }
    .doc-row select { flex: 0 0 auto; width: 100px; }
    .doc-row .doc-file { flex: 1; min-width: 0; }
    .doc-row .doc-name { font-size: .75rem; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .doc-row .doc-del { flex: 0 0 auto; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Kerja Sama</h1>
                    <p class="text-xs text-slate-500">Data kerja sama jurusan dengan instansi — bidang pendidikan, pengabdian, dan penelitian.</p>
                </div>
            </div>
            <button type="button" id="btnTambah" class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600">
                <i class="fas fa-plus text-sm"></i>
                <span class="tip">Tambah Kerja Sama</span>
            </button>
        </div>
    </section>

    <!-- ===== Statistik (informasi saja, bukan tombol filter) ===== -->
    <section class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="tile-orange tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-orange-500/25">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Total Kerja Sama</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight">50</p>
                    <p class="mt-2 text-[11px] text-white/70">seluruh instansi mitra</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-building"></i></span>
            </div>
        </div>
        <div class="tile-sky tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-sky-500/25" style="animation-delay:.05s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Pendidikan</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight">22</p>
                    <p class="mt-2 text-[11px] text-white/70">bidang pendidikan</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-graduation-cap"></i></span>
            </div>
        </div>
        <div class="tile-emerald tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-emerald-500/25" style="animation-delay:.10s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Pengabdian</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight">25</p>
                    <p class="mt-2 text-[11px] text-white/70">bidang pengabdian</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-hands-helping"></i></span>
            </div>
        </div>
        <div class="tile-violet tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-violet-500/25" style="animation-delay:.15s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Penelitian</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight">3</p>
                    <p class="mt-2 text-[11px] text-white/70">bidang penelitian</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-flask"></i></span>
            </div>
        </div>
    </section>

    <!-- ===== Tabs Tingkat Kerja Sama ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap gap-2">
            <button type="button" class="tab-btn" data-tab="" aria-selected="true"><span class="tdot bg-slate-500"></span>Semua<span class="tnum" id="cnt-all">50</span></button>
            <button type="button" class="tab-btn" data-tab="Lokal" aria-selected="false"><span class="tdot bg-sky-500"></span>Lokal<span class="tnum" id="cnt-Lokal">40</span></button>
            <button type="button" class="tab-btn" data-tab="Nasional" aria-selected="false"><span class="tdot bg-emerald-500"></span>Nasional<span class="tnum" id="cnt-Nasional">9</span></button>
            <button type="button" class="tab-btn" data-tab="Internasional" aria-selected="false"><span class="tdot bg-violet-500"></span>Internasional<span class="tnum" id="cnt-Internasional">1</span></button>
        </div>
    </section>

    <!-- ===== Toolbar (cari + filter) ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari instansi, deskripsi…"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Tahun</option>
                <option>2026</option>
                <option>2025</option>
                <option>2024</option>
                <option>2023</option>
                <option>2022</option>
                <option>2021</option>
                <option>2020</option>
            </select>
            <select id="fBukti" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Bukti</option>
                <option>IA</option>
                <option>MoU</option>
                <option>PKS</option>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">
                <i class="fas fa-times mr-1"></i>Reset
            </button>
        </div>
    </section>

    <!-- ===== Tabel Kerja Sama ===== -->
    <section>
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Daftar Kerja Sama</h2>
                    <p class="mt-0.5 text-xs text-slate-500"><span id="jmlData">50</span> kerja sama ditemukan</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white">
                    <i class="fas fa-database"></i> <span id="badgeData">50</span> total
                </span>
            </div>

            <div id="areaTabel">
                <div id="noHasil" class="hidden py-16 text-center">
                    <i class="fas fa-handshake text-4xl text-slate-300"></i>
                    <p class="mt-3 font-medium text-slate-500">Tidak ada kerja sama ditemukan</p>
                    <p class="text-xs text-slate-400">Coba ubah kata kunci atau filter.</p>
                </div>

                <div class="max-h-[560px] overflow-auto" id="wrapTabel">
                <table class="w-full min-w-[1000px] text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
                        <tr>
                            <th class="py-3.5 pl-5 pr-4 font-semibold uppercase tracking-wider">Instansi</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tahun</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Bidang</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tingkat</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Bukti</th>
                            <th class="py-3.5 pr-5 font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tBody" class="divide-y divide-slate-100">
                        <!-- 1 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="IA" data-cari="badan pemberdayaan masyarakat –badan pemberdayaan desa tertinggalprovinsi gorontalo kerjasama bidang pendidikan dan pengajaran pendidikan lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">1</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">BADAN PEMBERDAYAAN MASYARAKAT –Badan Pemberdayaan Desa TertinggalProvinsi Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Bidang Pendidikan dan Pengajaran</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634475985_IA BPM-PDT PROV GTLO.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 2 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="IA" data-cari="bandar udara djalaluddin gorontalo kerjasama magang kerja praktek pendidikan lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">2</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Bandar Udara Djalaluddin Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Magang Kerja Praktek</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634539953_IA Magang Bandar udara.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 3 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="desa batukramat pengabdian dalam bentuk kegiatan kkn tematik desa membangun pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">3</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Desa Batukramat</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Pengabdian dalam bentuk kegiatan KKN Tematik Desa Membangun</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634540431_IA KKN Tematik Desa Batu Kramat.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 4 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="desa bongohulawa pengabdian dalam bentuk kegiatan kkn tematik desa membangun pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">4</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Desa Bongohulawa</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Pengabdian dalam bentuk kegiatan KKN tematik Desa membangun</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1622437752_CamScanner 05-31-2021 12.57.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 5 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="desa dulupi pengabdian dalam bentuk kegiatan kkn tematik desa membangun pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">5</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Desa Dulupi</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Pengabdian dalam bentuk kegiatan KKN Tematik Desa Membangun</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634540320_IA KKN Tematik Desa Dulupi.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 6 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="desa dunggala pengabdian dalam bentuk kkn tematik pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">6</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Desa Dunggala</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Pengabdian dalam bentuk KKN Tematik</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634980812_IA Desa Dunggala.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 7 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2022" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="desa dutohe barat kecamatan kabila kabupaten bone bolango untuk melaksanakan kegiatan pengabdian kepada masyarakat pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">7</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Desa Dutohe Barat Kecamatan Kabila Kabupaten Bone Bolango</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">untuk melaksanakan kegiatan pengabdian kepada masyarakat</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1655709130_IA Dutohe (200).pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 8 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2022" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="desa dutohe barat kecamatan kabila kabupaten bone bolango pengabdian kepada masyarkat utk kegiatan re-instalasi keamanan listrik rumah tinggal di desa dutohe barat kecamatan kabila kabupaten bone bolango provinsi gorontalo pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">8</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Desa Dutohe Barat Kecamatan Kabila Kabupaten Bone Bolango</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Pengabdian Kepada Masyarkat utk kegiatan Re-Instalasi Keamanan Listrik Rumah Tinggal Di Desa Dutohe Barat Kecamatan Kabila Kabupaten Bone Bolango Provinsi Gorontalo</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1655709413_IA Dutohe (188).pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 9 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="desa mongolato pengabdian dalam bentuk kegiatan kkn tematik desa membangun pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">9</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Desa Mongolato</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Pengabdian dalam bentuk kegiatan KKN Tematik desa membangun</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634540092_IA KKN Tematik Desa Mongolato.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 10 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="desa pinomon tiga kerja sama teluk tomini pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">10</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Desa Pinomon Tiga</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerja sama Teluk Tomini</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634475669_IA Desa Pinomontiga edit.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 11 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="desa tamboo kerja sama teluk tomini pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">11</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Desa Tamboo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerja sama teluk tomini</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634475583_IA Desa Tamboo Edit.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 12 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2022" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="desa tolotio kerja sama dengan instansi mitra pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">12</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Desa Tolotio</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerja sama dengan instansi mitra</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1671594017-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 13 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2022" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="desa tongo kerja sama dengan instansi mitra pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">13</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Desa Tongo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerja sama dengan instansi mitra</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1671594309-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 14 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="desa tunas jaya kerja sama teluk tomini pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">14</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Desa Tunas Jaya</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerja sama Teluk tomini</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634475460_IA Desa Tunas Jaya Edit.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 15 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="desa tunggulo selatan pengabdian dalam bentuk kegiatan kkn tematik desa membangun pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">15</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Desa Tunggulo Selatan</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Pengabdian dalam bentuk kegiatan KKN tematik Desa membangun</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1622442116_tunggulo.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 16 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="IA" data-cari="dinas perhubungan, pariwisata dan kominfo provinsi gorontalo kerjasama bidang pendidikan dan pengajaran pendidikan lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">16</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Dinas Perhubungan, Pariwisata Dan Kominfo Provinsi Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Bidang Pendidikan dan Pengajaran</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634476447_IA DISHUBPAR KOMINFO PROV GTLO.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 17 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="IA" data-cari="dinas pertambang dan energi provinsi gorontalo kerjasama bidang pendidikan dan pengajaran pendidikan lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">17</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Dinas Pertambang Dan Energi Provinsi Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Bidang Pendidikan Dan Pengajaran</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634476683_IA DISTAMBEN PROV GTLO.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 18 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2021" data-bidang="Pendidikan" data-tingkat="Nasional" data-bukti="MoU PKS IA" data-cari="kementerian komunikasi dan informatika penyelenggara program beasiswa talenta digital (digital talent scholarship) tahun anggaran 2021 pendidikan nasional mou pks ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">18</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">KEMENTERIAN KOMUNIKASI DAN INFORMATIKA</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">PENYELENGGARA PROGRAM BEASISWA TALENTA DIGITAL (DIGITAL TALENT SCHOLARSHIP) TAHUN ANGGARAN 2021</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2021</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Nasional</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>MoU<span class="tip">1646802670_IA FGA DTS Elektro.pdf</span></a> <a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>PKS<span class="tip">1635650566_PKS 2021.pdf</span></a> <a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1646802670_IA FGA DTS Elektro.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 19 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2021" data-bidang="Pendidikan" data-tingkat="Nasional" data-bukti="PKS" data-cari="kementrian teknologi informasi dan informatika program beasiswa kerjamasa digital talent scholarship pendidikan nasional pks">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">19</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Kementrian Teknologi Informasi dan Informatika</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Program Beasiswa Kerjamasa Digital Talent Scholarship</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2021</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Nasional</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>PKS<span class="tip">1635650566_PKS 2021.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 20 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="IA" data-cari="loka monitor spektrum frekuensi radio gorontalo kerjasama magang kerja praktek pendidikan lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">20</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Loka Monitor Spektrum Frekuensi Radio Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Magang Kerja Praktek</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634538991_IA Magang loka monitor spektrum.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 21 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="IA" data-cari="pltu molotabu kerjasama magang kerja praktek pendidikan lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">21</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">PLTU Molotabu</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Magang Kerja Praktek</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634538786_IA Magang PLTU Molotabu.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 22 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="IA" data-cari="pt. gomeds network kerjasama magang kerja praktek pendidikan lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">22</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">PT. Gomeds Network</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Magang Kerja Praktek</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634539470_IA Magang KP PT Gomeds.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 23 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="IA" data-cari="pt. hervest gorontalo indonesia kerjasama magang kerja praktek pendidikan lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">23</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">PT. Hervest Gorontalo Indonesia</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Magang Kerja Praktek</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634539804_IA Magang HArvest.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 24 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="IA" data-cari="pt. pabrik tolangohula kerjasama magang kerja praktek pendidikan lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">24</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">PT. Pabrik Tolangohula</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Magang Kerja Praktek</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634538713_IA Magang PT PG Tolangohula.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 25 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="IA" data-cari="pt. pln up3 area gorontalo kerjasama magang kerja praktek pendidikan lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">25</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">PT. PLN UP3 Area Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Magang Kerja Praktek</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634539652_IA Magang KP PLN UP3.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 26 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="IA" data-cari="pt. telekomunikasi indonesia (telkom) pusat gorontalo kerjasama magang kerja praktek pendidikan lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">26</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">PT. Telekomunikasi Indonesia (TELKOM) Pusat Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Magang Kerja Praktek</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634539357_IA Magang KP Telkom.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 27 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="IA" data-cari="pt. tvri gorontalo kerjasama magang kerja praktek pendidikan lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">27</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">PT. TVRI Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Magang Kerja Praktek</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634539202_IA Magang KP TVRI.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 28 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2022" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="sd lab universitas negeri gorontalo kerja sama dengan instansi mitra pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">28</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">SD LAB Universitas Negeri Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerja sama dengan instansi mitra</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1671594244-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 29 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2022" data-bidang="Pendidikan" data-tingkat="Nasional" data-bukti="IA" data-cari="universitas negeri yogyakarta kerjasama mbkm pendidikan nasional ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">29</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">UNIVERSITAS NEGERI YOGYAKARTA</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">kerjasama MBKM</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Nasional</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1655710289_IA UNG_UNIVERSITAS NEGERI YOGYAKARTA_27Feb2022 (1).pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 30 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Nasional" data-bukti="IA" data-cari="universitas semarang kerjasama bidang pendidikan dan pengajaran pendidikan nasional ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">30</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Universitas Semarang</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Bidang Pendidikan dan Pengajaran</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Nasional</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634538461_IA UNIVERSITAS NEGERI SEMARANG.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 31 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="IA" data-cari="universitas tadulako kerjasama bidang pendidikan dan pengajaran pendidikan lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">31</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Universitas Tadulako</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Bidang Pendidikan dan Pengajaran</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634538342_IA UNIVERSITAS NEGERI TADULAKO.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 32 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2022" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="IA" data-cari="universitas tadulako kerja sama dalam bidang pendidikan dan pengajaran pendidikan lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">32</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Universitas Tadulako</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerja sama dalam bidang pendidikan dan pengajaran</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1655709770_IA Tadulako (296).pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 33 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2022" data-bidang="Penelitian" data-tingkat="Lokal" data-bukti="IA" data-cari="universitas tadulako kerjasama dalam bidang penelitian kolaboratif penelitian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">33</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Universitas Tadulako</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama dalam bidang penelitian kolaboratif</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-xs font-bold">Penelitian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1655709914_IA Tadulako (336).pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 34 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Nasional" data-bukti="IA" data-cari="unversitas negeri padang kerjasama bidang pendidikan dan pengajaran pendidikan nasional ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">34</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Unversitas Negeri Padang</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama Bidang Pendidikan dan Pengajaran</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Nasional</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1634538564_IA UNIVERSITAS NEGERI PADANG.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 35 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2023" data-bidang="Pendidikan" data-tingkat="Nasional" data-bukti="PKS" data-cari="universitas halu oleo kerjasama dalam bidang tridharma perguruan tinggi, mbkm dan pengelola jurnal pendidikan nasional pks">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">35</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">UNIVERSITAS HALU OLEO</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama dalam bidang Tridharma Perguruan Tinggi, MBKM dan Pengelola Jurnal</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Nasional</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>PKS<span class="tip">1692330022-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 36 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2023" data-bidang="Pengabdian" data-tingkat="Nasional" data-bukti="PKS" data-cari="fortei (forum pendidikan tinggi teknik elektro indonesia) kerjasama dalam bidang pengelolaan dan penerbitan jurnal pengabdian eldimas pengabdian nasional pks">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">36</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">FORTEI (Forum Pendidikan Tinggi Teknik Elektro Indonesia)</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama dalam bidang Pengelolaan dan Penerbitan Jurnal Pengabdian ELDIMAS</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Nasional</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>PKS<span class="tip">1692330184-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 37 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2023" data-bidang="Penelitian" data-tingkat="Nasional" data-bukti="PKS" data-cari="fortei (forum pendidikan tinggi teknik elektro indonesia) kerjasama dalam bidang pengelolaan dan penerbitan jurnal penelitian jjeee penelitian nasional pks">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">37</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">FORTEI (Forum Pendidikan Tinggi Teknik Elektro Indonesia)</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerjasama dalam bidang Pengelolaan dan Penerbitan Jurnal Penelitian JJEEE</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-xs font-bold">Penelitian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Nasional</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>PKS<span class="tip">1692330231-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 38 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2023" data-bidang="Pendidikan" data-tingkat="Nasional" data-bukti="IA" data-cari="balai jasa konstruksi wilayah vi makassar pembekalan dan uji kompetensi kerja kostruksi bidang vokasional tingkat politeknik/pt pendidikan nasional ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">38</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Balai Jasa Konstruksi Wilayah VI Makassar</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Pembekalan dan Uji Kompetensi Kerja Kostruksi Bidang Vokasional Tingkat Politeknik/PT</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Nasional</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1700452744-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 39 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-bidang="Pendidikan" data-tingkat="Lokal" data-bukti="MoU IA" data-cari="pemda kabupaten gorontalo  sk no. 15/un47/hk.07.00/2020 tentang implementasi di bidang pendidikan, penelitian dan pengembangan pendidikan lokal mou ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">39</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Pemda Kabupaten Gorontalo </p>
                                        <p class="text-xs text-slate-500 line-clamp-1">SK no. 15/UN47/HK.07.00/2020 tentang implementasi di Bidang pendidikan, penelitian dan pengembangan</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>MoU<span class="tip">1727926776-kerjasama.pdf</span></a> <a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1692330184-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 40 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2024" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="dinas pertanian kabupaten gorontalo program pelaksanaan pengabdian kepada masyarakat kkn temarik membangun desa tahap ii pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">40</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Dinas Pertanian Kabupaten Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Program pelaksanaan pengabdian kepada masyarakat KKN temarik membangun desa tahap II</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2024</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1742183383-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 41 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2024" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="pemerintah kecamatan bulango ulu kabupaten bone bolango penyelenggara program pengabdian kepada masyarakat pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">41</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Pemerintah Kecamatan Bulango Ulu Kabupaten Bone Bolango</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Penyelenggara program pengabdian kepada masyarakat</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2024</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1742183479-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 42 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2024" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="pemerintah kecamatan tibawa kabupaten gorontalo penyelenggara program kuliah kerja nyata tematik tahun 2024 pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">42</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Pemerintah Kecamatan Tibawa Kabupaten Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Penyelenggara program kuliah kerja nyata tematik tahun 2024</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2024</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1742183563-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 43 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2025" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="smk negeri 5 gorontalo kerja sama dengan instansi mitra pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">43</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">SMK Negeri 5 Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerja sama dengan instansi mitra</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2025</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1761544428-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 44 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2025" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="PKS" data-cari="dinas pendidikan dan kebudayaan provinsi gorontalo kerja sama dengan instansi mitra pengabdian lokal pks">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">44</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Dinas Pendidikan dan Kebudayaan Provinsi Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerja sama dengan instansi mitra</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2025</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>PKS<span class="tip">1761544602-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 45 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2026" data-bidang="Penelitian" data-tingkat="Internasional" data-bukti="MoU IA PKS" data-cari="kerja sama internasional bondula kerja sama dengan instansi mitra penelitian internasional mou ia pks">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">45</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Kerja sama internasional bondula</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerja sama dengan instansi mitra</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2026</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-xs font-bold">Penelitian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2.5 py-1 text-xs font-bold">Internasional</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>MoU<span class="tip">1775525073-kerjasama.pdf</span></a> <a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1775525073-kerjasama.pdf</span></a> <a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>PKS<span class="tip">1775525073-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 46 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2025" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="dinas perumahan dan permukiman (perkim) kabupaten gorontalo pelaksanaan pengabdian dengan tema &quot;optimalisasi pemakaian energi listrik melalui sosialisasi dan audit energi dirumah susun sederhana sewa (rusunawa) kec. telaga&quot; pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">46</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Dinas Perumahan dan Permukiman (PERKIM) Kabupaten Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Pelaksanaan pengabdian dengan tema &quot;optimalisasi pemakaian energi listrik melalui sosialisasi dan audit energi dirumah susun sederhana sewa (RUSUNAWA) Kec. Telaga&quot;</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2025</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1777347039-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 47 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2025" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="PKS" data-cari="dinas perumahan dan permukiman (perkim) kabupaten gorontalo pengembangan program penelitian dan pengabdian pada masyarakat pengabdian lokal pks">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">47</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Dinas Perumahan dan Permukiman (PERKIM) Kabupaten Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Pengembangan program penelitian dan pengabdian pada masyarakat</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2025</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>PKS<span class="tip">1777347140-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 48 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2025" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="PKS" data-cari="badan pusat statistik provinsi gorontalo kerja sama dengan instansi mitra pengabdian lokal pks">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">48</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Badan Pusat Statistik Provinsi Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">Kerja sama dengan instansi mitra</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2025</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>PKS<span class="tip">1783923574-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 49 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2026" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="badan pusat statistik provinsi gorontalo perancangan pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">49</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Badan Pusat Statistik Provinsi Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">PERANCANGAN</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2026</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1783923631-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 50 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2024" data-bidang="Pengabdian" data-tingkat="Lokal" data-bukti="IA" data-cari="badan pusat statistik provinsi gorontalo pengelolaan pengabdian lokal ia">
                            <td class="py-4 pl-5 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">50</span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 leading-snug">Badan Pusat Statistik Provinsi Gorontalo</p>
                                        <p class="text-xs text-slate-500 line-clamp-1">PENGELOLAAN</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2024</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Pengabdian</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>
                            <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"><a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>IA<span class="tip">1783923808-kerjasama.pdf</span></a> </div></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div id="footerTabel" class="flex items-center justify-between border-t border-slate-100 px-5 py-3">
                <button type="button" id="btnPrev" class="pg-btn"><i class="fas fa-chevron-left text-xs"></i></button>
                <p class="text-xs font-semibold text-slate-500" id="lblPage">Halaman 1 / 1</p>
                <button type="button" id="btnNext" class="pg-btn"><i class="fas fa-chevron-right text-xs"></i></button>
            </div>
            </div>
        </div>
    </section>

</main>

<!-- ===== Modal Tambah / Edit Kerja Sama (frontend only) ===== -->
<div class="modal-overlay" id="ksModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm" id="ksModalTitle"><i class="fas fa-plus mr-1 text-[#f97316]"></i>Tambah Kerja Sama</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
        </div>
        <form id="ksForm" class="p-5 space-y-4">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Instansi</label>
                <input type="text" id="inpInstansi" required placeholder="mis. PT. PLN UP3 Gorontalo"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Deskripsi</label>
                <input type="text" id="inpDeskripsi" placeholder="Ringkasan kerja sama"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
                    <select id="inpTahun" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>2026</option><option>2025</option><option>2024</option><option>2023</option><option>2022</option><option>2021</option><option>2020</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Bidang</label>
                    <select id="inpBidang" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>Pendidikan</option><option>Pengabdian</option><option>Penelitian</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tingkat</label>
                    <select id="inpTingkat" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>Lokal</option><option>Nasional</option><option>Internasional</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Dokumen Bukti</label>
                <div id="docList" class="space-y-2"></div>
                <button type="button" id="btnAddDoc" class="mt-2 inline-flex items-center gap-1.5 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-orange-400 hover:text-orange-600">
                    <i class="fas fa-plus text-[10px]"></i> Tambah Dokumen
                </button>
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" data-modal-close class="px-3 py-2 text-xs rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-[#1a365d] hover:bg-[#234670] text-white font-medium">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var ITEM_PER_PAGE = 15;
    var tab = '', halaman = 1;

    var body = document.getElementById('tBody');
    var rows = Array.prototype.slice.call(body ? body.querySelectorAll('tr') : []);
    var fCari = document.getElementById('fCari');
    var fTahun = document.getElementById('fTahun');
    var fBukti = document.getElementById('fBukti');
    var jmlEl = document.getElementById('jmlData');
    var badgeEl = document.getElementById('badgeData');
    var kosongEl = document.getElementById('noHasil');
    var wrapTabel = document.getElementById('wrapTabel');
    var footerTabel = document.getElementById('footerTabel');
    var lblPage = document.getElementById('lblPage');
    var btnPrev = document.getElementById('btnPrev');
    var btnNext = document.getElementById('btnNext');

    function cocok(tr, ta) {
        var kata = (fCari && fCari.value || '').toLowerCase().trim();
        if (kata !== '' && (tr.getAttribute('data-cari') || '').indexOf(kata) === -1) return false;
        if (ta !== null && ta !== '' && tr.getAttribute('data-tingkat') !== ta) return false;
        if (fTahun && fTahun.value !== '' && tr.getAttribute('data-tahun') !== fTahun.value) return false;
        if (fBukti && fBukti.value !== '' && (tr.getAttribute('data-bukti') || '').split(' ').indexOf(fBukti.value) === -1) return false;
        return true;
    }

    function updateCounts() {
        var cnt = { '': 0, 'Lokal': 0, 'Nasional': 0, 'Internasional': 0 };
        for (var i = 0; i < rows.length; i++) {
            var ti = rows[i].getAttribute('data-tingkat');
            if (cocok(rows[i], null)) { cnt['']++; cnt[ti] = (cnt[ti] || 0) + 1; }
        }
        var c = document.getElementById('cnt-all');
        if (c) c.textContent = cnt[''].toLocaleString('id-ID');
        ['Lokal', 'Nasional', 'Internasional'].forEach(function (t) {
            var el = document.getElementById('cnt-' + t);
            if (el) el.textContent = (cnt[t] || 0).toLocaleString('id-ID');
        });
    }

    function render() {
        var tutup = (halaman - 1) * ITEM_PER_PAGE;
        var pos = 0, total = 0;
        for (var i = 0; i < rows.length; i++) {
            var tampil = cocok(rows[i], tab);
            if (tampil) {
                total++;
                rows[i].style.display = (pos >= tutup && pos < tutup + ITEM_PER_PAGE) ? '' : 'none';
                pos++;
            } else {
                rows[i].style.display = 'none';
            }
        }
        var jmlHal = Math.max(1, Math.ceil(total / ITEM_PER_PAGE));
        if (halaman > jmlHal) { halaman = jmlHal; render(); return; }
        if (jmlEl) jmlEl.textContent = total.toLocaleString('id-ID');
        if (badgeEl) badgeEl.textContent = total.toLocaleString('id-ID');
        if (lblPage) lblPage.textContent = 'Halaman ' + halaman + ' / ' + jmlHal;
        if (btnPrev) btnPrev.disabled = halaman <= 1;
        if (btnNext) btnNext.disabled = halaman >= jmlHal;
        if (kosongEl) kosongEl.classList.toggle('hidden', total > 0);
        if (wrapTabel) wrapTabel.style.display = total > 0 ? '' : 'none';
        if (footerTabel) footerTabel.style.display = total > 0 ? '' : 'none';
        updateCounts();
    }

    function setTab(t) {
        tab = t;
        halaman = 1;
        document.querySelectorAll('.tab-btn').forEach(function (b) {
            b.setAttribute('aria-selected', b.getAttribute('data-tab') === t ? 'true' : 'false');
        });
        render();
    }

    document.querySelectorAll('.tab-btn').forEach(function (b) {
        b.addEventListener('click', function () { setTab(b.getAttribute('data-tab')); });
    });
    if (fCari) fCari.addEventListener('input', function () { halaman = 1; render(); });
    if (fTahun) fTahun.addEventListener('change', function () { halaman = 1; render(); });
    if (fBukti) fBukti.addEventListener('change', function () { halaman = 1; render(); });
    if (btnPrev) btnPrev.addEventListener('click', function () { if (halaman > 1) { halaman--; render(); } });
    if (btnNext) btnNext.addEventListener('click', function () { halaman++; render(); });
    var btnReset = document.getElementById('btnReset');
    if (btnReset) btnReset.addEventListener('click', function () {
        if (fCari) fCari.value = '';
        if (fTahun) fTahun.value = '';
        if (fBukti) fBukti.value = '';
        setTab('');
    });

    function toast(msg) {
        var t = document.createElement('div');
        t.textContent = msg;
        t.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;background:#0f172a;color:#fff;padding:.6rem 1rem;border-radius:.6rem;font-size:.8rem;box-shadow:0 6px 18px rgba(15,23,42,.35);transition:opacity .3s ease;';
        document.body.appendChild(t);
        setTimeout(function () { t.style.opacity = '0'; setTimeout(function () { t.remove(); }, 300); }, 2200);
    }

    /* ===== Aksi: Edit & Hapus ===== */
    body.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-circle');
        if (!btn) return;
        var tr = btn.closest('tr');
        if (btn.classList.contains('bg-sky-500')) {
            openEdit(tr);
        } else if (btn.classList.contains('bg-rose-500')) {
            if (confirm('Hapus kerja sama ini?')) {
                tr.remove();
                rows = Array.prototype.slice.call(body.querySelectorAll('tr'));
                badgeNomor();
                render();
                toast('Kerja sama dihapus');
            }
        }
    });

    /* ===== Modal Tambah / Edit ===== */
    var modal = document.getElementById('ksModal');
    var mTitle = document.getElementById('ksModalTitle');
    var mForm = document.getElementById('ksForm');
    var mInstansi = document.getElementById('inpInstansi');
    var mTahun = document.getElementById('inpTahun');
    var mBidang = document.getElementById('inpBidang');
    var mTingkat = document.getElementById('inpTingkat');
    var mDeskripsi = document.getElementById('inpDeskripsi');
    var mEditing = null;

    /* ---- Dynamic doc list ---- */
    var docList = document.getElementById('docList');
    var btnAddDoc = document.getElementById('btnAddDoc');

    function addDocRow(list, type, fileName) {
        var d = document.createElement('div');
        d.className = 'doc-row';
        var fname = fileName || '';
        var displayName = fname ? fname : 'Belum ada file';
        d.innerHTML =
            '<select class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-2 text-sm outline-none focus:border-orange-400">' +
                '<option value="IA"' + (type === 'IA' ? ' selected' : '') + '>IA</option>' +
                '<option value="MoU"' + (type === 'MoU' ? ' selected' : '') + '>MoU</option>' +
                '<option value="PKS"' + (type === 'PKS' ? ' selected' : '') + '>PKS</option>' +
            '</select>' +
            '<div class="doc-file flex items-center gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-2">' +
                '<i class="fas fa-file-pdf text-slate-400 text-xs shrink-0"></i>' +
                '<span class="doc-name">' + displayName + '</span>' +
                '<label class="ml-auto inline-flex cursor-pointer items-center gap-1 rounded bg-slate-900 px-2 py-1 text-[10px] font-semibold text-white transition hover:bg-slate-700 shrink-0">' +
                    '<i class="fas fa-folder-open text-[9px]"></i> Pilih' +
                    '<input type="file" class="hidden" accept=".pdf,.doc,.docx">' +
                '</label>' +
            '</div>' +
            '<button type="button" class="doc-del btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" style="width:1.75rem;height:1.75rem;"><i class="fas fa-xmark text-[10px]"></i></button>';
        var fileInp = d.querySelector('input[type="file"]');
        var nameEl = d.querySelector('.doc-name');
        fileInp.addEventListener('change', function () {
            nameEl.textContent = fileInp.files.length ? fileInp.files[0].name : 'Belum ada file';
        });
        d.querySelector('.doc-del').addEventListener('click', function () { d.remove(); });
        list.appendChild(d);
    }

    function getDocs(list) {
        var docs = [];
        var rows = list.querySelectorAll('.doc-row');
        for (var i = 0; i < rows.length; i++) {
            var t = rows[i].querySelector('select').value;
            var n = rows[i].querySelector('.doc-name').textContent;
            if (n && n !== 'Belum ada file') docs.push({ type: t, name: n });
        }
        return docs;
    }

    function setDocs(list, docs) {
        list.innerHTML = '';
        for (var i = 0; i < docs.length; i++) addDocRow(list, docs[i].type, docs[i].name);
    }

    if (btnAddDoc) btnAddDoc.addEventListener('click', function () { addDocRow(docList, 'IA', ''); });

    /* ---- Open / Close ---- */
    function openTambah() {
        mEditing = null;
        mTitle.innerHTML = '<i class="fas fa-plus mr-1 text-[#f97316]"></i>Tambah Kerja Sama';
        mForm.reset();
        docList.innerHTML = '';
        addDocRow(docList, 'IA', '');
        if (mTahun) mTahun.value = '2026';
        if (modal) modal.classList.add('show');
    }

    function openEdit(tr) {
        mEditing = tr;
        var inst = tr.querySelector('p.font-semibold');
        var desc = tr.querySelector('p.line-clamp-1');
        mInstansi.value = inst ? inst.textContent : '';
        mDeskripsi.value = desc ? desc.textContent : '';
        mTahun.value = tr.getAttribute('data-tahun') || '';
        mBidang.value = tr.getAttribute('data-bidang') || '';
        mTingkat.value = tr.getAttribute('data-tingkat') || '';

        var badges = tr.querySelectorAll('td')[4].querySelectorAll('a.tip-wrap');
        var docs = [];
        for (var i = 0; i < badges.length; i++) {
            var btype = badges[i].textContent.trim();
            var tip = badges[i].querySelector('.tip');
            var bname = tip ? tip.textContent : '';
            docs.push({ type: btype, name: bname });
        }
        if (docs.length === 0) docs.push({ type: 'IA', name: '' });
        setDocs(docList, docs);

        mTitle.innerHTML = '<i class="fas fa-pen mr-1 text-[#0ea5e9]"></i>Edit Kerja Sama';
        if (modal) modal.classList.add('show');
    }

    document.getElementById('btnTambah').addEventListener('click', openTambah);
    modal.querySelectorAll('[data-modal-close]').forEach(function (b) {
        b.addEventListener('click', function () { modal.classList.remove('show'); });
    });
    modal.addEventListener('click', function (e) { if (e.target === modal) modal.classList.remove('show'); });

    /* ---- Helpers ---- */
    function buatBadgeBukti(type, name) {
        var fname = name || ('dokumen-' + type.toLowerCase() + '.pdf');
        return '<a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>' + type + '<span class="tip">' + fname + '</span></a>';
    }

    function warnaBidang(b) {
        if (b === 'Pengabdian') return 'bg-emerald-100 text-emerald-700';
        if (b === 'Penelitian') return 'bg-violet-100 text-violet-700';
        return 'bg-sky-100 text-sky-700';
    }

    function warnaTingkat(t) {
        if (t === 'Nasional') return 'bg-emerald-100 text-emerald-700';
        if (t === 'Internasional') return 'bg-rose-100 text-rose-700';
        return 'bg-slate-100 text-slate-600';
    }

    function badgeBuktiHTML(docs) {
        var h = '';
        for (var i = 0; i < docs.length; i++) h += buatBadgeBukti(docs[i].type, docs[i].name);
        return h;
    }

    function applyForm(tr) {
        var instansi = mInstansi.value.trim();
        var deskripsi = mDeskripsi.value.trim() || 'Kerja sama dengan instansi mitra';
        var tahun = mTahun.value || '2026';
        var bidang = mBidang.value || 'Pendidikan';
        var tingkat = mTingkat.value || 'Lokal';
        var docs = getDocs(docList);
        var buktiStr = docs.map(function (d) { return d.type; }).join(' ');

        tr.setAttribute('data-tahun', tahun);
        tr.setAttribute('data-bidang', bidang);
        tr.setAttribute('data-tingkat', tingkat);
        tr.setAttribute('data-bukti', buktiStr);
        tr.setAttribute('data-cari', (instansi + ' ' + deskripsi + ' ' + bidang + ' ' + tingkat + ' ' + buktiStr).toLowerCase());

        tr.querySelector('p.font-semibold').textContent = instansi;
        tr.querySelector('p.line-clamp-1').textContent = deskripsi;

        var td = tr.querySelectorAll('td');
        td[1].innerHTML = '<span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">' + tahun + '</span>';
        td[2].innerHTML = '<span class="inline-flex items-center rounded-full ' + warnaBidang(bidang) + ' px-2.5 py-1 text-xs font-bold">' + bidang + '</span>';
        td[3].innerHTML = '<span class="inline-flex items-center rounded-full ' + warnaTingkat(tingkat) + ' px-2.5 py-1 text-xs font-bold">' + tingkat + '</span>';
        td[4].innerHTML = '<div class="flex flex-wrap items-center gap-1">' + badgeBuktiHTML(docs) + '</div>';
    }

    function barisBaru() {
        return '<td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm"></span><div class="min-w-0"><p class="font-semibold text-slate-800 leading-snug">&#8212;</p><p class="text-xs text-slate-500 line-clamp-1"></p></div></div></td>' +
            '<td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2026</span></td>' +
            '<td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>' +
            '<td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>' +
            '<td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"></div></td>' +
            '<td class="py-4 pr-5"><div class="flex items-center gap-1.5"><button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button><button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button></div></td>';
    }

    function badgeNomor() {
        var nRows = body.querySelectorAll('tr');
        for (var i = 0; i < nRows.length; i++) {
            var numEl = nRows[i].querySelector('span.text-base.font-bold');
            if (numEl) numEl.textContent = (i + 1) + '';
        }
    }

    mForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var instansi = mInstansi.value.trim();
        if (instansi === '') { alert('Instansi wajib diisi.'); return; }
        var docs = getDocs(docList);
        if (docs.length === 0) { alert('Minimal satu dokumen bukti harus diisi.'); return; }
        if (mEditing) {
            applyForm(mEditing);
            toast('Perubahan disimpan');
        } else {
            var r = document.createElement('tr');
            r.className = 'bg-white transition hover:bg-orange-50';
            r.innerHTML = barisBaru();
            body.appendChild(r);
            rows = Array.prototype.slice.call(body.querySelectorAll('tr'));
            applyForm(rows[rows.length - 1]);
            badgeNomor();
            render();
            toast('Kerja sama ditambahkan');
        }
        modal.classList.remove('show');
        mForm.reset();
    });
})();
</script>