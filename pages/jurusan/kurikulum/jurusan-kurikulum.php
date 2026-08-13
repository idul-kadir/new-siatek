<?php
/**
 * Halaman: Kurikulum
 * Data mengikuti tabel `kurikulum`: kode, nama, tahun, prodi, status.
 * JS hanya untuk: cari, filter prodi/status, tab tahun, modal tambah/edit, hapus.
 */
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    .tab-btn { display: inline-flex; align-items: center; gap: .5rem; border-radius: .6rem; border: 1px solid #e2e8f0;
        background: #fff; color: #475569; padding: .55rem .9rem; font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .tab-btn:hover { border-color: #fdba74; color: #c2410c; }
    .tab-btn .tdot { width: 8px; height: 8px; border-radius: 9999px; }
    .tab-btn .tnum { min-width: 22px; text-align: center; border-radius: 9999px; padding: 1px 6px; font-size: .7rem; background: #f1f5f9; color: #64748b; }
    .tab-btn[aria-selected="true"] { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }
    .tab-btn[aria-selected="true"] .tnum { background: rgba(255,255,255,.18); color: #fff; }

    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-book-open"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Kurikulum</h1>
                    <p class="text-xs text-slate-500">Data kurikulum program studi — tahun berlaku, status, dan prodi.</p>
                </div>
            </div>
            <button type="button" id="btnTambah" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-3.5 py-2 text-xs font-semibold text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-600">
                <i class="fas fa-plus text-[10px]"></i> Tambah Kurikulum
            </button>
        </div>
    </section>



    <!-- ===== Tabs Tahun ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap gap-2">
            <button type="button" class="tab-btn" data-tab="" aria-selected="true"><span class="tdot bg-slate-500"></span>Semua<span class="tnum" id="cnt-all">6</span></button>
            <button type="button" class="tab-btn" data-tab="2017" aria-selected="false"><span class="tdot bg-amber-500"></span>2017<span class="tnum" id="cnt-2017">1</span></button>
            <button type="button" class="tab-btn" data-tab="2025" aria-selected="false"><span class="tdot bg-sky-500"></span>2025<span class="tnum" id="cnt-2025">2</span></button>
            <button type="button" class="tab-btn" data-tab="2026" aria-selected="false"><span class="tdot bg-emerald-500"></span>2026<span class="tnum" id="cnt-2026">3</span></button>
        </div>
    </section>

    <!-- ===== Toolbar ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari kode, nama kurikulum…"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <select id="fProdi" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Prodi</option>
                <option>S1 Teknik Elektro</option>
                <option>S1 Teknik Komputer</option>
                <option>S1 Pendidikan Vokasi Rekayasa Elektro</option>
            </select>
            <select id="fStatus" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Status</option>
                <option>Aktif</option>
                <option>Tidak Aktif</option>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">
                <i class="fas fa-times mr-1"></i>Reset
            </button>
        </div>
    </section>

    <!-- ===== Tabel Kurikulum ===== -->
    <section>
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Daftar Kurikulum</h2>
                    <p class="mt-0.5 text-xs text-slate-500"><span id="jmlData">6</span> kurikulum ditemukan</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white">
                    <i class="fas fa-database"></i> <span id="badgeData">6</span> total
                </span>
            </div>

            <div id="areaTabel">
                <div id="noHasil" class="hidden py-16 text-center">
                    <i class="fas fa-book-open text-4xl text-slate-300"></i>
                    <p class="mt-3 font-medium text-slate-500">Tidak ada kurikulum ditemukan</p>
                    <p class="text-xs text-slate-400">Coba ubah kata kunci atau filter.</p>
                </div>

                <div class="max-h-[560px] overflow-auto" id="wrapTabel">
                <table class="w-full min-w-[800px] text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
                        <tr>
                            <th class="py-3.5 pl-5 pr-4 font-semibold uppercase tracking-wider">Kode</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Nama Kurikulum</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tahun</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Program Studi</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Status</th>
                            <th class="py-3.5 pr-5 font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tBody" class="divide-y divide-slate-100">
                        <!-- 1 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2017" data-prodi="S1 Teknik Elektro" data-status="Tidak Aktif" data-cari="1753425335 kurikulum kkni mbkm s1 teknik elektro tidak aktif">
                            <td class="py-4 pl-5 pr-4">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 font-mono">1753425335</span>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-800 leading-snug">Kurikulum KKNI - MBKM</p>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2017</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">S1 Teknik Elektro</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2.5 py-1 text-xs font-bold">Tidak Aktif</span></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                    <button type="button" class="btn-circle btn-edit bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle btn-hapus bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 2 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2025" data-prodi="S1 Teknik Elektro" data-status="Aktif" data-cari="1753425420 kurikulum 2025 s1 teknik elektro aktif">
                            <td class="py-4 pl-5 pr-4">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 font-mono">1753425420</span>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-800 leading-snug">Kurikulum 2025</p>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2025</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">S1 Teknik Elektro</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Aktif</span></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                    <button type="button" class="btn-circle btn-edit bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle btn-hapus bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 3 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2025" data-prodi="S1 Teknik Komputer" data-status="Aktif" data-cari="1753425426 kurikulum 2025 s1 teknik komputer aktif">
                            <td class="py-4 pl-5 pr-4">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 font-mono">1753425426</span>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-800 leading-snug">Kurikulum 2025</p>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2025</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">S1 Teknik Komputer</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Aktif</span></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                    <button type="button" class="btn-circle btn-edit bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle btn-hapus bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 4 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2026" data-prodi="S1 Pendidikan Vokasi Rekayasa Elektro" data-status="Aktif" data-cari="1784211306 kurikulum obe s1 pendidikan vokasi rekayasa elektro aktif">
                            <td class="py-4 pl-5 pr-4">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 font-mono">1784211306</span>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-800 leading-snug">Kurikulum OBE</p>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2026</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-xs font-bold">S1 Pendidikan Vokasi Rekayasa Elektro</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Aktif</span></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                    <button type="button" class="btn-circle btn-edit bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle btn-hapus bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 5 -->
                        <tr class="bg-white transition hover:bg-orange-50" data-tahun="2026" data-prodi="S1 Teknik Elektro" data-status="Aktif" data-cari="1785722041 kurikulum obe s1 teknik elektro aktif">
                            <td class="py-4 pl-5 pr-4">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 font-mono">1785722041</span>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-800 leading-snug">Kurikulum OBE</p>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2026</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">S1 Teknik Elektro</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Aktif</span></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                    <button type="button" class="btn-circle btn-edit bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle btn-hapus bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                        <!-- 6 -->
                        <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2026" data-prodi="S1 Teknik Komputer" data-status="Aktif" data-cari="1785725609 kurikulum obe s1 teknik komputer aktif">
                            <td class="py-4 pl-5 pr-4">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 font-mono">1785725609</span>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-800 leading-snug">Kurikulum OBE</p>
                            </td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2026</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">S1 Teknik Komputer</span></td>
                            <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-1 text-xs font-bold">Aktif</span></td>
                            <td class="py-4 pr-5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                    <button type="button" class="btn-circle btn-edit bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                    <button type="button" class="btn-circle btn-hapus bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </section>

</main>

<!-- ===== Modal Tambah / Edit Kurikulum ===== -->
<div class="modal-overlay" id="kkModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm" id="kkModalTitle"><i class="fas fa-plus mr-1 text-[#f97316]"></i>Tambah Kurikulum</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
        </div>
        <form id="kkForm" class="p-5 space-y-4">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Kode Kurikulum</label>
                <input type="text" id="inpKode" required placeholder="mis. 1785722041"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white font-mono">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Kurikulum</label>
                <input type="text" id="inpNama" required placeholder="mis. Kurikulum OBE"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
                    <select id="inpTahun" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>2026</option><option>2025</option><option>2024</option><option>2023</option><option>2022</option><option>2021</option><option>2020</option><option>2017</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Status</label>
                    <select id="inpStatus" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>Aktif</option><option>Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Program Studi</label>
                <select id="inpProdi" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                    <option>S1 Teknik Elektro</option><option>S1 Teknik Komputer</option><option>S1 Pendidikan Vokasi Rekayasa Elektro</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" data-modal-close class="px-3 py-2 text-xs rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-[#1a365d] hover:bg-[#234670] text-white font-medium">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Modal Detail Kurikulum ===== -->
<div class="modal-overlay" id="detailModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-eye mr-1 text-slate-500"></i>Detail Kurikulum</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
        </div>
        <div class="p-5 space-y-3">
            <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-sm font-bold text-slate-600"><i class="fas fa-hashtag"></i></span>
                <div class="min-w-0">
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Kode</p>
                    <p class="text-sm font-bold text-slate-800 font-mono" id="dtKode">—</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-sm font-bold text-orange-600"><i class="fas fa-book"></i></span>
                <div class="min-w-0">
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Nama Kurikulum</p>
                    <p class="text-sm font-bold text-slate-800" id="dtNama">—</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-sm font-bold text-slate-600"><i class="fas fa-calendar"></i></span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Tahun</p>
                        <p class="text-sm font-bold text-slate-800" id="dtTahun">—</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-sm font-bold text-slate-600"><i class="fas fa-toggle-on"></i></span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Status</p>
                        <p class="text-sm font-bold text-slate-800" id="dtStatus">—</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sm font-bold text-sky-600"><i class="fas fa-graduation-cap"></i></span>
                <div class="min-w-0">
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Program Studi</p>
                    <p class="text-sm font-bold text-slate-800" id="dtProdi">—</p>
                </div>
            </div>
        </div>
        <div class="border-t border-slate-100 px-5 py-3 flex justify-end">
            <button type="button" data-modal-close class="px-4 py-2 text-xs rounded-lg bg-slate-900 hover:bg-slate-700 text-white font-medium transition">Tutup</button>
        </div>
    </div>
</div>

<script>
(function () {
    var tab = '';

    var body = document.getElementById('tBody');
    var rows = Array.prototype.slice.call(body ? body.querySelectorAll('tr') : []);
    var fCari = document.getElementById('fCari');
    var fProdi = document.getElementById('fProdi');
    var fStatus = document.getElementById('fStatus');
    var jmlEl = document.getElementById('jmlData');
    var badgeEl = document.getElementById('badgeData');
    var kosongEl = document.getElementById('noHasil');
    var wrapTabel = document.getElementById('wrapTabel');

    function cocok(tr, ta) {
        var kata = (fCari && fCari.value || '').toLowerCase().trim();
        if (kata !== '' && (tr.getAttribute('data-cari') || '').indexOf(kata) === -1) return false;
        if (ta !== '' && tr.getAttribute('data-tahun') !== ta) return false;
        if (fProdi && fProdi.value !== '' && tr.getAttribute('data-prodi') !== fProdi.value) return false;
        if (fStatus && fStatus.value !== '' && tr.getAttribute('data-status') !== fStatus.value) return false;
        return true;
    }

    function updateCounts() {
        var cnt = { '': 0, '2017': 0, '2025': 0, '2026': 0 };
        for (var i = 0; i < rows.length; i++) {
            var ta = rows[i].getAttribute('data-tahun');
            if (cocok(rows[i], '')) { cnt['']++; cnt[ta] = (cnt[ta] || 0) + 1; }
        }
        var c = document.getElementById('cnt-all');
        if (c) c.textContent = cnt[''];
        ['2017', '2025', '2026'].forEach(function (t) {
            var el = document.getElementById('cnt-' + t);
            if (el) el.textContent = cnt[t] || 0;
        });
    }

    function render() {
        var total = 0;
        for (var i = 0; i < rows.length; i++) {
            var tampil = cocok(rows[i], tab);
            rows[i].style.display = tampil ? '' : 'none';
            if (tampil) total++;
        }
        if (jmlEl) jmlEl.textContent = total;
        if (badgeEl) badgeEl.textContent = total;
        if (kosongEl) kosongEl.classList.toggle('hidden', total > 0);
        if (wrapTabel) wrapTabel.style.display = total > 0 ? '' : 'none';
        updateCounts();
    }

    function setTab(t) {
        tab = t;
        document.querySelectorAll('.tab-btn').forEach(function (b) {
            b.setAttribute('aria-selected', b.getAttribute('data-tab') === t ? 'true' : 'false');
        });
        render();
    }

    document.querySelectorAll('.tab-btn').forEach(function (b) {
        b.addEventListener('click', function () { setTab(b.getAttribute('data-tab')); });
    });
    if (fCari) fCari.addEventListener('input', function () { render(); });
    if (fProdi) fProdi.addEventListener('change', function () { render(); });
    if (fStatus) fStatus.addEventListener('change', function () { render(); });
    var btnReset = document.getElementById('btnReset');
    if (btnReset) btnReset.addEventListener('click', function () {
        if (fCari) fCari.value = '';
        if (fProdi) fProdi.value = '';
        if (fStatus) fStatus.value = '';
        setTab('');
    });

    function toast(msg) {
        var t = document.createElement('div');
        t.textContent = msg;
        t.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;background:#0f172a;color:#fff;padding:.6rem 1rem;border-radius:.6rem;font-size:.8rem;box-shadow:0 6px 18px rgba(15,23,42,.35);transition:opacity .3s ease;';
        document.body.appendChild(t);
        setTimeout(function () { t.style.opacity = '0'; setTimeout(function () { t.remove(); }, 300); }, 2200);
    }

    /* ===== Aksi ===== */
    body.addEventListener('click', function (e) {
        var btn = e.target.closest('button');
        if (!btn) return;
        var tr = btn.closest('tr');
        if (btn.classList.contains('btn-edit')) {
            openEdit(tr);
        } else if (btn.classList.contains('btn-hapus')) {
            if (confirm('Hapus kurikulum ini?')) {
                tr.remove();
                rows = Array.prototype.slice.call(body.querySelectorAll('tr'));
                render();
                toast('Kurikulum dihapus');
            }
        } else if (btn.textContent.indexOf('Detail') !== -1) {
            openDetail(tr);
        }
    });

    /* ===== Modal ===== */
    var modal = document.getElementById('kkModal');
    var mTitle = document.getElementById('kkModalTitle');
    var mForm = document.getElementById('kkForm');
    var mKode = document.getElementById('inpKode');
    var mNama = document.getElementById('inpNama');
    var mTahun = document.getElementById('inpTahun');
    var mStatus = document.getElementById('inpStatus');
    var mProdi = document.getElementById('inpProdi');
    var mEditing = null;

    function openTambah() {
        mEditing = null;
        mTitle.innerHTML = '<i class="fas fa-plus mr-1 text-[#f97316]"></i>Tambah Kurikulum';
        mForm.reset();
        if (mTahun) mTahun.value = '2026';
        if (modal) modal.classList.add('show');
    }

    function openEdit(tr) {
        mEditing = tr;
        mKode.value = tr.querySelector('.font-mono') ? tr.querySelector('.font-mono').textContent : '';
        mNama.value = tr.querySelector('p.font-semibold') ? tr.querySelector('p.font-semibold').textContent : '';
        mTahun.value = tr.getAttribute('data-tahun') || '2026';
        mProdi.value = tr.getAttribute('data-prodi') || '';
        mStatus.value = tr.getAttribute('data-status') || 'Aktif';
        mTitle.innerHTML = '<i class="fas fa-pen mr-1 text-[#0ea5e9]"></i>Edit Kurikulum';
        if (modal) modal.classList.add('show');
    }

    function openDetail(tr) {
        var kode = tr.querySelector('.font-mono') ? tr.querySelector('.font-mono').textContent : '—';
        var nama = tr.querySelector('p.font-semibold') ? tr.querySelector('p.font-semibold').textContent : '—';
        var tahun = tr.getAttribute('data-tahun') || '—';
        var prodi = tr.getAttribute('data-prodi') || '—';
        var status = tr.getAttribute('data-status') || '—';
        document.getElementById('dtKode').textContent = kode;
        document.getElementById('dtNama').textContent = nama;
        document.getElementById('dtTahun').textContent = tahun;
        document.getElementById('dtProdi').textContent = prodi;
        document.getElementById('dtStatus').textContent = status;
        document.getElementById('detailModal').classList.add('show');
    }

    document.getElementById('btnTambah').addEventListener('click', openTambah);
    modal.querySelectorAll('[data-modal-close]').forEach(function (b) {
        b.addEventListener('click', function () { modal.classList.remove('show'); });
    });
    modal.addEventListener('click', function (e) { if (e.target === modal) modal.classList.remove('show'); });

    var detailModal = document.getElementById('detailModal');
    detailModal.querySelectorAll('[data-modal-close]').forEach(function (b) {
        b.addEventListener('click', function () { detailModal.classList.remove('show'); });
    });
    detailModal.addEventListener('click', function (e) { if (e.target === detailModal) detailModal.classList.remove('show'); });

    function warnaProdi(p) {
        if (p === 'S1 Teknik Komputer') return 'bg-emerald-100 text-emerald-700';
        if (p === 'S1 Pendidikan Vokasi Rekayasa Elektro') return 'bg-violet-100 text-violet-700';
        return 'bg-sky-100 text-sky-700';
    }

    function warnaStatus(s) {
        if (s === 'Aktif') return 'bg-emerald-100 text-emerald-700';
        return 'bg-rose-100 text-rose-700';
    }

    function applyForm(tr) {
        var kode = mKode.value.trim();
        var nama = mNama.value.trim();
        var tahun = mTahun.value || '2026';
        var prodi = mProdi.value || 'S1 Teknik Elektro';
        var status = mStatus.value || 'Aktif';

        tr.setAttribute('data-tahun', tahun);
        tr.setAttribute('data-prodi', prodi);
        tr.setAttribute('data-status', status);
        tr.setAttribute('data-cari', (kode + ' ' + nama + ' ' + prodi + ' ' + status).toLowerCase());

        tr.querySelector('.font-mono').textContent = kode;
        tr.querySelector('p.font-semibold').textContent = nama;

        var td = tr.querySelectorAll('td');
        td[2].innerHTML = '<span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">' + tahun + '</span>';
        td[3].innerHTML = '<span class="inline-flex items-center rounded-full ' + warnaProdi(prodi) + ' px-2.5 py-1 text-xs font-bold">' + prodi + '</span>';
        td[4].innerHTML = '<span class="inline-flex items-center rounded-full ' + warnaStatus(status) + ' px-2.5 py-1 text-xs font-bold">' + status + '</span>';
    }

    mForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var kode = mKode.value.trim();
        var nama = mNama.value.trim();
        if (kode === '' || nama === '') { alert('Kode dan Nama wajib diisi.'); return; }
        if (mEditing) {
            applyForm(mEditing);
            toast('Perubahan disimpan');
        } else {
            var r = document.createElement('tr');
            r.className = 'bg-white transition hover:bg-orange-50';
            r.setAttribute('data-tahun', mTahun.value || '2026');
            r.setAttribute('data-prodi', mProdi.value || 'S1 Teknik Elektro');
            r.setAttribute('data-status', mStatus.value || 'Aktif');
            r.setAttribute('data-cari', (kode + ' ' + nama + ' ' + (mProdi.value || '') + ' ' + (mStatus.value || '')).toLowerCase());
            r.innerHTML =
                '<td class="py-4 pl-5 pr-4"><span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 font-mono">' + kode + '</span></td>' +
                '<td class="px-4 py-4"><p class="font-semibold text-slate-800 leading-snug">' + nama + '</p></td>' +
                '<td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">' + (mTahun.value || '2026') + '</span></td>' +
                '<td class="px-4 py-4"><span class="inline-flex items-center rounded-full ' + warnaProdi(mProdi.value) + ' px-2.5 py-1 text-xs font-bold">' + (mProdi.value || 'S1 Teknik Elektro') + '</span></td>' +
                '<td class="px-4 py-4"><span class="inline-flex items-center rounded-full ' + warnaStatus(mStatus.value) + ' px-2.5 py-1 text-xs font-bold">' + (mStatus.value || 'Aktif') + '</span></td>' +
                '<td class="py-4 pr-5"><div class="flex items-center gap-1.5">' +
                    '<button type="button" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>' +
                    '<button type="button" class="btn-circle btn-edit bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>' +
                    '<button type="button" class="btn-circle btn-hapus bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>' +
                '</div></td>';
            body.appendChild(r);
            rows = Array.prototype.slice.call(body.querySelectorAll('tr'));
            render();
            toast('Kurikulum ditambahkan');
        }
        modal.classList.remove('show');
        mForm.reset();
    });
})();
</script>
