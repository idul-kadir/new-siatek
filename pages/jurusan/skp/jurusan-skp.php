<?php
/**
 * Halaman: SKP — Sasaran Kinerja Pegawai (Jurusan)
 * Monitoring SKP dosen yang sudah masuk (diunggah dari akun masing-masing dosen).
 * Setiap 1 data SKP memiliki 2 file:
 *   - File Draft  : hasil unggahan dosen (pengisian SKP).
 *   - File Final  : hasil verifikasi pejabat jurusan.
 * Alur: draft masuk → menunggu review → pejabat memeriksa & mengunggah file final,
 * lalu baris berpindah ke bagian "Selesai". Status di list utama hanya satu:
 * "Menunggu Review". Dilengkapi filter per tahun & semester (ganjil/genap) serta
 * pagination. Tampilan HTML statis (dummy) — JS untuk tab, cari, filter,
 * pagination, detail/review, unggah final, dan hapus.
 */
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
    @media (prefers-reduced-motion: reduce) { .reveal { animation: none; } }

    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    .tab-btn { display: inline-flex; align-items: center; gap: .5rem; border-radius: .6rem; border: 1px solid #e2e8f0;
        background: #fff; color: #475569; padding: .55rem .9rem; font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .tab-btn:hover { border-color: #7dd3fc; color: #0369a1; }
    .tab-btn .tdot { width: 8px; height: 8px; border-radius: 9999px; }
    .tab-btn .tnum { min-width: 22px; text-align: center; border-radius: 9999px; padding: 1px 6px; font-size: .7rem; background: #f1f5f9; color: #64748b; }
    .tab-btn[aria-selected="true"] { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }
    .tab-btn[aria-selected="true"] .tnum { background: rgba(255,255,255,.18); color: #fff; }

    .st-badge { display: inline-flex; align-items: center; gap: .3rem; padding: .22rem .6rem; border-radius: 9999px; font-size: 10px; font-weight: 700; line-height: 1; white-space: nowrap; }
    .pr-badge { display: inline-block; padding: .18rem .55rem; border-radius: .45rem; font-size: 10px; font-weight: 700; white-space: nowrap; }

    .pin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .pin-table thead th { background: #f8fafc; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; text-align: left; padding: .6rem .85rem; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
    .pin-table tbody td { padding: .7rem .85rem; font-size: .78rem; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .pin-table tbody tr:last-child td { border-bottom: none; }
    .pin-table tbody tr { transition: background .15s ease; }
    .pin-table tbody tr:hover { background: #f8fafc; }

    .file-chip { display: inline-flex; align-items: center; gap: .5rem; min-width: 0; max-width: 220px; padding: .32rem .55rem; border-radius: .6rem; border: 1px solid #e2e8f0; background: #f8fafc; }
    .file-chip .f-ico { display: inline-flex; align-items: center; justify-content: center; width: 1.6rem; height: 1.6rem; border-radius: .45rem; font-size: .72rem; flex-shrink: 0; }
    .f-pdf   { background: #fee2e2; color: #dc2626; }
    .f-word  { background: #dbeafe; color: #2563eb; }
    .file-chip .f-meta { min-width: 0; }
    .file-chip .f-name { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 600; font-size: .72rem; color: #334155; }
    .file-chip .f-sub { display: block; font-size: .63rem; color: #94a3b8; }
    .f-down { display: inline-flex; align-items: center; justify-content: center; width: 1.6rem; height: 1.6rem; border-radius: .4rem; background: #fff; border: 1px solid #e2e8f0; color: #64748b; cursor: pointer; flex-shrink: 0; transition: all .15s ease; }
    .f-down:hover { background: #0e7490; border-color: #0e7490; color: #fff; }

    .empty-dash { display: inline-flex; align-items: center; gap: .35rem; color: #94a3b8; font-style: italic; }
    .empty-dash i { color: #cbd5e1; }

    .pg-btn { min-width: 2rem; height: 2rem; border-radius: .5rem; padding: 0 .5rem; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: .75rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .pg-btn:hover { border-color: #7dd3fc; color: #0369a1; }
    .pg-btn:disabled { opacity: .45; cursor: not-allowed; }
    .pg-btn.on { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }

    .modal-overlay { display: none; position: fixed; inset: 0; z-index: 90; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem; }
    .modal-overlay.show { display: flex; }

    #toastSkp { position: fixed; right: 20px; bottom: 20px; z-index: 2000; display: none; align-items: center; gap: .6rem; max-width: 360px; border-radius: .75rem; background: #0f172a; color: #fff; padding: .7rem 1rem; font-size: .82rem; box-shadow: 0 12px 24px rgba(15,23,42,.25); }
    #toastSkp.show { display: flex; animation: riseIn .3s ease; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-lg text-sky-600">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">SKP — Sasaran Kinerja Pegawai</h1>
                    <p class="text-xs text-slate-500">Monitoring SKP dosen yang sudah masuk. Draft diunggah dari akun masing-masing dosen, lalu diverifikasi pejabat jurusan menjadi file final.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Tab Status ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap gap-2">
            <button type="button" class="tab-btn" data-tab="semua" aria-selected="false"><span class="tdot bg-slate-500"></span>Semua<span class="tnum" id="cnt-semua">10</span></button>
            <button type="button" class="tab-btn" data-tab="menunggu" aria-selected="true"><span class="tdot bg-amber-500"></span>Menunggu Review<span class="tnum" id="cnt-menunggu">7</span></button>
            <button type="button" class="tab-btn" data-tab="selesai" aria-selected="false"><span class="tdot bg-emerald-500"></span>Selesai<span class="tnum" id="cnt-selesai">3</span></button>
        </div>
    </section>

    <!-- ===== Toolbar ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari nama dosen, NIP…"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-sky-400 focus:bg-white">
            </div>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                <option value="">Semua Tahun</option>
                <option value="2026">2026</option>
                <option value="2025">2025</option>
            </select>
            <select id="fSemester" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                <option value="">Semua Semester</option>
                <option value="ganjil">Ganjil</option>
                <option value="genap">Genap</option>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
        </div>
    </section>

    <!-- ===== Ringkasan ===== -->
    <section class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs text-slate-500" id="jmlInfo">Menampilkan <b class="text-slate-800">0</b> dari <b class="text-slate-800">0</b> data</p>
        <p class="text-xs text-slate-400"><i class="fas fa-lightbulb mr-1 text-amber-400"></i>Setiap SKP memerlukan 2 file: <b>draft</b> (unggahan dosen) dan <b>final</b> (hasil verifikasi).</p>
    </section>

    <!-- ===== Tabel SKP ===== -->
    <section>
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="pin-table">
                    <thead>
                        <tr>
                            <th>Pegawai</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>File Draft</th>
                            <th>File Final</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbSkp">

                        <tr class="reveal" data-status="menunggu" data-tahun="2026" data-semester="ganjil" data-nama="Ir. Hendra Wijaya, M.Eng." data-nip="197302151999031002" data-draft="SKP_Draft_Hendra_2026-Ganjil.pdf" data-final="" data-ket="" data-hari="18 Agu 2026" data-cari="ir hendra wijaya m eng 197302151999031002 2026 ganjil">
                            <td><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-100 text-[11px] font-bold text-sky-700">HW</span><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Ir. Hendra Wijaya, M.Eng.</p><p class="text-[10px] text-slate-400">NIP 197302151999031002</p></div></div></td>
                            <td><span class="pr-badge bg-sky-50 text-sky-700">2026 · Ganjil</span></td>
                            <td><span class="st-badge bg-amber-100 text-amber-700"><i class="fas fa-hourglass-half"></i>Menunggu Review</span></td>
                            <td><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">SKP_Draft_Hendra_2026-Ganjil.pdf</span><span class="f-sub">214 KB · 18 Agu 2026</span></span><button type="button" class="f-down" title="Unduh draft"><i class="fas fa-download"></i></button></span></td>
                            <td><span class="empty-dash"><i class="fas fa-minus"></i>Belum ada</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat / Review</span></button>
                                <button type="button" class="btn-final btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-arrow-up text-xs"></i><span class="tip">Unggah File Final</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus SKP</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="menunggu" data-tahun="2026" data-semester="ganjil" data-nama="Rina Marlina, M.M." data-nip="198105202005012004" data-draft="SKP_Draft_Rina_2026-Ganjil.pdf" data-final="" data-ket="" data-hari="18 Agu 2026" data-cari="rina marlina m m 198105202005012004 2026 ganjil">
                            <td><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-rose-100 text-[11px] font-bold text-rose-700">RM</span><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Rina Marlina, M.M.</p><p class="text-[10px] text-slate-400">NIP 198105202005012004</p></div></div></td>
                            <td><span class="pr-badge bg-sky-50 text-sky-700">2026 · Ganjil</span></td>
                            <td><span class="st-badge bg-amber-100 text-amber-700"><i class="fas fa-hourglass-half"></i>Menunggu Review</span></td>
                            <td><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">SKP_Draft_Rina_2026-Ganjil.pdf</span><span class="f-sub">180 KB · 18 Agu 2026</span></span><button type="button" class="f-down" title="Unduh draft"><i class="fas fa-download"></i></button></span></td>
                            <td><span class="empty-dash"><i class="fas fa-minus"></i>Belum ada</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat / Review</span></button>
                                <button type="button" class="btn-final btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-arrow-up text-xs"></i><span class="tip">Unggah File Final</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus SKP</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="menunggu" data-tahun="2026" data-semester="genap" data-nama="Deni Kurniawan, M.Sc." data-nip="198611122011011003" data-draft="SKP_Draft_Deni_2026-Genap.pdf" data-final="" data-ket="" data-hari="16 Agu 2026" data-cari="deni kurniawan m sc 198611122011011003 2026 genap">
                            <td><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-violet-100 text-[11px] font-bold text-violet-700">DK</span><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Deni Kurniawan, M.Sc.</p><p class="text-[10px] text-slate-400">NIP 198611122011011003</p></div></div></td>
                            <td><span class="pr-badge bg-fuchsia-50 text-fuchsia-700">2026 · Genap</span></td>
                            <td><span class="st-badge bg-amber-100 text-amber-700"><i class="fas fa-hourglass-half"></i>Menunggu Review</span></td>
                            <td><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">SKP_Draft_Deni_2026-Genap.pdf</span><span class="f-sub">166 KB · 16 Agu 2026</span></span><button type="button" class="f-down" title="Unduh draft"><i class="fas fa-download"></i></button></span></td>
                            <td><span class="empty-dash"><i class="fas fa-minus"></i>Belum ada</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat / Review</span></button>
                                <button type="button" class="btn-final btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-arrow-up text-xs"></i><span class="tip">Unggah File Final</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus SKP</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="menunggu" data-tahun="2026" data-semester="ganjil" data-nama="Dr. Ratna Dewi, M.Kom." data-nip="197608251999032003" data-draft="SKP_Draft_Ratna_2026-Ganjil.pdf" data-final="" data-ket="" data-hari="14 Agu 2026" data-cari="dr ratna dewi m kom 197608251999032003 2026 ganjil">
                            <td><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-[11px] font-bold text-emerald-700">RD</span><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Dr. Ratna Dewi, M.Kom.</p><p class="text-[10px] text-slate-400">NIP 197608251999032003</p></div></div></td>
                            <td><span class="pr-badge bg-sky-50 text-sky-700">2026 · Ganjil</span></td>
                            <td><span class="st-badge bg-amber-100 text-amber-700"><i class="fas fa-hourglass-half"></i>Menunggu Review</span></td>
                            <td><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">SKP_Draft_Ratna_2026-Ganjil.pdf</span><span class="f-sub">198 KB · 14 Agu 2026</span></span><button type="button" class="f-down" title="Unduh draft"><i class="fas fa-download"></i></button></span></td>
                            <td><span class="empty-dash"><i class="fas fa-minus"></i>Belum ada</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat / Review</span></button>
                                <button type="button" class="btn-final btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-arrow-up text-xs"></i><span class="tip">Unggah File Final</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus SKP</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="menunggu" data-tahun="2026" data-semester="ganjil" data-nama="Dr. Agus Prabowo, M.T." data-nip="197001012000031001" data-draft="SKP_Draft_Agus_2026-Ganjil.pdf" data-final="" data-ket="" data-hari="13 Agu 2026" data-cari="dr agus prabowo m t 197001012000031001 2026 ganjil">
                            <td><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-[11px] font-bold text-indigo-700">AP</span><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Dr. Agus Prabowo, M.T.</p><p class="text-[10px] text-slate-400">NIP 197001012000031001</p></div></div></td>
                            <td><span class="pr-badge bg-sky-50 text-sky-700">2026 · Ganjil</span></td>
                            <td><span class="st-badge bg-amber-100 text-amber-700"><i class="fas fa-hourglass-half"></i>Menunggu Review</span></td>
                            <td><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">SKP_Draft_Agus_2026-Ganjil.pdf</span><span class="f-sub">221 KB · 13 Agu 2026</span></span><button type="button" class="f-down" title="Unduh draft"><i class="fas fa-download"></i></button></span></td>
                            <td><span class="empty-dash"><i class="fas fa-minus"></i>Belum ada</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat / Review</span></button>
                                <button type="button" class="btn-final btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-arrow-up text-xs"></i><span class="tip">Unggah File Final</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus SKP</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="menunggu" data-tahun="2025" data-semester="ganjil" data-nama="Andi Saputra, M.Kom." data-nip="198412052008011005" data-draft="SKP_Draft_Andi_2025-Ganjil.pdf" data-final="" data-ket="" data-hari="11 Agu 2026" data-cari="andi saputra m kom 198412052008011005 2025 ganjil">
                            <td><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-[11px] font-bold text-amber-700">AS</span><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Andi Saputra, M.Kom.</p><p class="text-[10px] text-slate-400">NIP 198412052008011005</p></div></div></td>
                            <td><span class="pr-badge bg-slate-100 text-slate-600">2025 · Ganjil</span></td>
                            <td><span class="st-badge bg-amber-100 text-amber-700"><i class="fas fa-hourglass-half"></i>Menunggu Review</span></td>
                            <td><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">SKP_Draft_Andi_2025-Ganjil.pdf</span><span class="f-sub">174 KB · 11 Agu 2026</span></span><button type="button" class="f-down" title="Unduh draft"><i class="fas fa-download"></i></button></span></td>
                            <td><span class="empty-dash"><i class="fas fa-minus"></i>Belum ada</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat / Review</span></button>
                                <button type="button" class="btn-final btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-arrow-up text-xs"></i><span class="tip">Unggah File Final</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus SKP</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="menunggu" data-tahun="2026" data-semester="ganjil" data-nama="Dr. Maya Sari, M.T." data-nip="197812152005012006" data-draft="SKP_Draft_Maya_2026-Ganjil.pdf" data-final="" data-ket="Capaian target belum sesuai bukti pendukung - sedang dikomunikasikan dengan dosen." data-hari="12 Agu 2026" data-cari="dr maya sari m t 197812152005012006 2026 ganjil">
                            <td><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-rose-100 text-[11px] font-bold text-rose-700">MS</span><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Dr. Maya Sari, M.T.</p><p class="text-[10px] text-slate-400">NIP 197812152005012006</p></div></div></td>
                            <td><span class="pr-badge bg-sky-50 text-sky-700">2026 · Ganjil</span></td>
                            <td><span class="st-badge bg-amber-100 text-amber-700"><i class="fas fa-hourglass-half"></i>Menunggu Review</span></td>
                            <td><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">SKP_Draft_Maya_2026-Ganjil.pdf</span><span class="f-sub">201 KB · 12 Agu 2026</span></span><button type="button" class="f-down" title="Unduh draft"><i class="fas fa-download"></i></button></span></td>
                            <td><span class="empty-dash"><i class="fas fa-minus"></i>Belum ada</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat / Review</span></button>
                                <button type="button" class="btn-final btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-arrow-up text-xs"></i><span class="tip">Unggah File Final</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus SKP</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="selesai" data-tahun="2026" data-semester="ganjil" data-nama="Prof. Dr. Ahmad Fauzi, M.T." data-nip="197205101997021002" data-draft="SKP_Draft_Ahmad_2026-Ganjil.pdf" data-final="SKP_Final_Ahmad_2026-Ganjil.pdf" data-ket="Verifikasi selesai, capaian sesuai target." data-hari="9 Agu 2026" data-cari="prof dr ahmad fauzi m t 197205101997021002 2026 ganjil">
                            <td><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-violet-100 text-[11px] font-bold text-violet-700">AF</span><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Prof. Dr. Ahmad Fauzi, M.T.</p><p class="text-[10px] text-slate-400">NIP 197205101997021002</p></div></div></td>
                            <td><span class="pr-badge bg-sky-50 text-sky-700">2026 · Ganjil</span></td>
                            <td><span class="st-badge bg-emerald-100 text-emerald-700"><i class="fas fa-check"></i>Selesai</span></td>
                            <td><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">SKP_Draft_Ahmad_2026-Ganjil.pdf</span><span class="f-sub">247 KB · 9 Agu 2026</span></span><button type="button" class="f-down" title="Unduh draft"><i class="fas fa-download"></i></button></span></td>
                            <td><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">SKP_Final_Ahmad_2026-Ganjil.pdf</span><span class="f-sub">235 KB · final</span></span><button type="button" class="f-down" title="Unduh final"><i class="fas fa-download"></i></button></span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat / Review</span></button>
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Perbaiki File Final</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus SKP</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="selesai" data-tahun="2026" data-semester="ganjil" data-nama="Dr. Siti Aminah, M.M." data-nip="198009152006042002" data-draft="SKP_Draft_Siti_2026-Ganjil.pdf" data-final="SKP_Final_Siti_2026-Ganjil.pdf" data-ket="Disetujui dengan catatan." data-hari="8 Agu 2026" data-cari="dr siti aminah m m 198009152006042002 2026 ganjil">
                            <td><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-teal-100 text-[11px] font-bold text-teal-700">SA</span><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Dr. Siti Aminah, M.M.</p><p class="text-[10px] text-slate-400">NIP 198009152006042002</p></div></div></td>
                            <td><span class="pr-badge bg-sky-50 text-sky-700">2026 · Ganjil</span></td>
                            <td><span class="st-badge bg-emerald-100 text-emerald-700"><i class="fas fa-check"></i>Selesai</span></td>
                            <td><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">SKP_Draft_Siti_2026-Ganjil.pdf</span><span class="f-sub">189 KB · 8 Agu 2026</span></span><button type="button" class="f-down" title="Unduh draft"><i class="fas fa-download"></i></button></span></td>
                            <td><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">SKP_Final_Siti_2026-Ganjil.pdf</span><span class="f-sub">187 KB · final</span></span><button type="button" class="f-down" title="Unduh final"><i class="fas fa-download"></i></button></span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat / Review</span></button>
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Perbaiki File Final</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus SKP</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="selesai" data-tahun="2025" data-semester="genap" data-nama="Budi Santoso, M.T." data-nip="197801102005011003" data-draft="SKP_Draft_Budi_2025-Genap.pdf" data-final="SKP_Final_Budi_2025-Genap.pdf" data-ket="Lengkap dan disetujui." data-hari="2 Jul 2026" data-cari="budi santoso m t 197801102005011003 2025 genap">
                            <td><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-cyan-100 text-[11px] font-bold text-cyan-700">BS</span><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Budi Santoso, M.T.</p><p class="text-[10px] text-slate-400">NIP 197801102005011003</p></div></div></td>
                            <td><span class="pr-badge bg-fuchsia-50 text-fuchsia-700">2025 · Genap</span></td>
                            <td><span class="st-badge bg-emerald-100 text-emerald-700"><i class="fas fa-check"></i>Selesai</span></td>
                            <td><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">SKP_Draft_Budi_2025-Genap.pdf</span><span class="f-sub">205 KB · 2 Jul 2026</span></span><button type="button" class="f-down" title="Unduh draft"><i class="fas fa-download"></i></button></span></td>
                            <td><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">SKP_Final_Budi_2025-Genap.pdf</span><span class="f-sub">202 KB · final</span></span><button type="button" class="f-down" title="Unduh final"><i class="fas fa-download"></i></button></span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat / Review</span></button>
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Perbaiki File Final</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus SKP</span></button>
                            </div></td>
                        </tr>

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

<!-- ===== Modal Detail / Review SKP ===== -->
<div class="modal-overlay" id="detailModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-bullseye mr-1 text-sky-500"></i>Detail &amp; Review SKP</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-detail-close>&times;</button>
        </div>
        <div class="p-5 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-slate-50 p-3.5">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sm font-bold text-sky-700" id="dtInisial">--</span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-800" id="dtNama">—</p>
                        <p class="text-[11px] text-slate-500" id="dtNip">—</p>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <span class="st-badge" id="dtStatus">—</span>
                    <span class="pr-badge bg-sky-50 text-sky-700" id="dtPeriode">—</span>
                </div>
            </div>

            <div>
                <p class="mb-2 text-[11px] font-bold text-slate-600 uppercase tracking-wide">File dalam SKP ini</p>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <div class="rounded-xl border border-amber-200 bg-amber-50/60 p-3">
                        <p class="mb-2 flex items-center gap-1.5 text-[11px] font-bold text-amber-700"><i class="fas fa-pen-to-square"></i>File Draft <span class="font-normal text-amber-500">— unggahan dosen</span></p>
                        <div id="dtDraft"></div>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-3">
                        <p class="mb-2 flex items-center gap-1.5 text-[11px] font-bold text-emerald-700"><i class="fas fa-file-circle-check"></i>File Final <span class="font-normal text-emerald-500">— hasil verifikasi</span></p>
                        <div id="dtFinal"></div>
                    </div>
                </div>
            </div>

            <div>
                <p class="mb-1 text-[11px] font-bold text-slate-600 uppercase tracking-wide">Catatan Verifikasi</p>
                <p class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-600" id="dtKet">—</p>
            </div>

            <div id="dtActions" class="flex flex-wrap items-center justify-end gap-2 pt-1 border-t border-slate-100">
                <button type="button" class="btn-final-lg rounded-lg bg-emerald-500 hover:bg-emerald-600 px-4 py-2 text-xs font-semibold text-white transition"><i class="fas fa-file-arrow-up mr-1"></i>Unggah File Final</button>
            </div>
        </div>
    </div>
</div>

<!-- ===== Modal Unggah File Final ===== -->
<div class="modal-overlay" id="finalModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-file-circle-check mr-1 text-emerald-500"></i>Unggah File Final</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-final-close>&times;</button>
        </div>
        <form id="frmFinal" class="p-5 space-y-3">
            <p class="text-xs text-slate-500">Memverifikasi SKP: <b id="fpNama" class="text-slate-700">—</b></p>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">File Final (hasil verifikasi) <span class="text-rose-500">*</span></label>
                <div class="flex items-center gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-2.5">
                    <i class="fas fa-file-upload text-emerald-500"></i>
                    <span class="text-xs text-slate-500" id="fpFileLabel">Belum ada file dipilih</span>
                    <label class="ml-auto cursor-pointer rounded-lg bg-emerald-500 px-3 py-1.5 text-[11px] font-semibold text-white transition hover:bg-emerald-600">
                        Pilih File
                        <input type="file" id="fpFile" accept=".pdf,.doc,.docx" class="hidden">
                    </label>
                </div></div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Catatan Verifikasi</label>
                <textarea id="fpKet" rows="2" placeholder="mis. SKP disetujui, capaian sesuai target." class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-emerald-400 focus:bg-white"></textarea></div>
            <div class="flex justify-end gap-2 pt-1 border-t border-slate-100">
                <button type="button" data-final-close class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-medium transition"><i class="fas fa-check mr-1"></i>Simpan Final</button>
            </div>
        </form>
    </div>
</div>

<div id="toastSkp"></div>

<script>
(function () {
    var tb = document.getElementById('tbSkp');
    var skp = Array.prototype.slice.call(document.querySelectorAll('#tbSkp tr'));
    var fCari = document.getElementById('fCari');
    var fTahun = document.getElementById('fTahun');
    var fSemester = document.getElementById('fSemester');
    var activeTab = 'menunggu';
    var page = 1, PER = 6;

    function miniChip(nama, label) {
        var ext = (nama.split('.').pop() || 'pdf').toLowerCase();
        var cls = ext === 'doc' || ext === 'docx' ? 'f-word' : 'f-pdf';
        var ico = ext === 'doc' || ext === 'docx' ? 'fa-file-word' : 'fa-file-pdf';
        return '<span class="file-chip"><span class="f-ico ' + cls + '"><i class="fas ' + ico + '"></i></span>' +
            '<span class="f-meta"><span class="f-name">' + nama + '</span><span class="f-sub">' + label + '</span></span>' +
            '<button type="button" class="f-down" title="Unduh ' + nama + '"><i class="fas fa-download"></i></button></span>';
    }

    function perBadge(tahun, sem) {
        var m = { '2026': ['bg-sky-50 text-sky-700', 'ganjil'], '2025': ['bg-slate-100 text-slate-600', 'ganjil'] };
        var key = m[tahun] ? tahun : '2026';
        var txt = key + ' · ' + (sem.charAt(0).toUpperCase() + sem.slice(1));
        if (sem === 'genap') return '<span class="pr-badge bg-fuchsia-50 text-fuchsia-700">' + txt + '</span>';
        return '<span class="pr-badge ' + m[key][0] + '">' + txt + '</span>';
    }
    function stBadge(st) {
        if (st === 'selesai') return '<span class="st-badge bg-emerald-100 text-emerald-700"><i class="fas fa-check"></i>Selesai</span>';
        return '<span class="st-badge bg-amber-100 text-amber-700"><i class="fas fa-hourglass-half"></i>Menunggu Review</span>';
    }
    function initial(k) {
        return k.split(/\s+/).filter(function (w) { return /^[A-Z]/.test(w); }).slice(0, 2).map(function (w) { return w[0]; }).join('').toUpperCase() || '--';
    }
    function chipHTML(f, sub) {
        return f ? miniChip(f, sub) : '<span class="empty-dash"><i class="fas fa-minus"></i>Belum ada</span>';
    }

    function invRow(tr) {
        var nama = tr.getAttribute('data-nama');
        var nip = tr.getAttribute('data-nip');
        var dr = tr.getAttribute('data-draft');
        var fin = tr.getAttribute('data-final') || '';
        var st = tr.getAttribute('data-status');
        var tahun = tr.getAttribute('data-tahun');
        var sem = tr.getAttribute('data-semester');
        var hari = tr.getAttribute('data-hari');
        var btnFinal = st === 'selesai' ?
            '<button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Perbaiki File Final</span></button>' :
            '<button type="button" class="btn-final btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-arrow-up text-xs"></i><span class="tip">Unggah File Final</span></button>';
        return '<td><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-100 text-[11px] font-bold text-sky-700">' + initial(nama) + '</span><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">' + nama + '</p><p class="text-[10px] text-slate-400">NIP ' + nip + '</p></div></div></td>' +
            '<td>' + perBadge(tahun, sem) + '</td>' +
            '<td>' + stBadge(st) + '</td>' +
            '<td>' + chipHTML(dr, dr + ' · ' + hari) + '</td>' +
            '<td>' + chipHTML(fin, fin ? fin + ' · final' : '') + '</td>' +
            '<td><div class="inline-flex items-center gap-1.5">' +
            '<button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat / Review</span></button>' +
            btnFinal +
            '<button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus SKP</span></button></div></td>';
    }

    function recount() {
        var m = 0, s = 0;
        skp.forEach(function (tr) {
            if (tr.getAttribute('data-status') === 'selesai') s++; else m++;
        });
        document.getElementById('cnt-semua').textContent = skp.length;
        document.getElementById('cnt-menunggu').textContent = m;
        document.getElementById('cnt-selesai').textContent = s;
    }

    function visible() {
        var kata = (fCari.value || '').toLowerCase().trim();
        var th = fTahun.value;
        var sem = fSemester.value;
        return skp.filter(function (tr) {
            if (activeTab !== 'semua' && tr.getAttribute('data-status') !== activeTab) return false;
            if (th && tr.getAttribute('data-tahun') !== th) return false;
            if (sem && tr.getAttribute('data-semester') !== sem) return false;
            return !kata || tr.getAttribute('data-cari').indexOf(kata) !== -1;
        });
    }
    function render() {
        var v = visible();
        var pages = Math.max(1, Math.ceil(v.length / PER));
        if (page > pages) page = pages;
        var start = (page - 1) * PER;
        var slice = v.slice(start, start + PER);
        skp.forEach(function (tr) { tr.style.display = 'none'; });
        slice.forEach(function (tr) { tr.style.display = ''; });
        document.getElementById('jmlInfo').innerHTML = 'Menampilkan <b class="text-slate-800">' + slice.length + '</b> dari <b class="text-slate-800">' + v.length + '</b> data';
        document.getElementById('pgInfo').textContent = 'Halaman ' + page + ' dari ' + pages;
        var wrap = document.getElementById('pgWrap');
        wrap.innerHTML = '';
        var prev = document.createElement('button');
        prev.className = 'pg-btn';
        prev.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prev.disabled = page <= 1;
        prev.addEventListener('click', function () { if (page > 1) { page--; render(); } });
        wrap.appendChild(prev);
        for (var i = 1; i <= pages; i++) {
            (function (n) {
                var b = document.createElement('button');
                b.className = 'pg-btn' + (n === page ? ' on' : '');
                b.textContent = n;
                b.addEventListener('click', function () { page = n; render(); });
                wrap.appendChild(b);
            })(i);
        }
        var next = document.createElement('button');
        next.className = 'pg-btn';
        next.innerHTML = '<i class="fas fa-chevron-right"></i>';
        next.disabled = page >= pages;
        next.addEventListener('click', function () { if (page < pages) { page++; render(); } });
        wrap.appendChild(next);
    }

    document.querySelectorAll('.tab-btn').forEach(function (b) {
        b.addEventListener('click', function () {
            document.querySelectorAll('.tab-btn').forEach(function (x) { x.setAttribute('aria-selected', 'false'); });
            b.setAttribute('aria-selected', 'true');
            activeTab = b.getAttribute('data-tab');
            page = 1;
            render();
        });
    });
    fCari.addEventListener('input', function () { page = 1; render(); });
    fTahun.addEventListener('change', function () { page = 1; render(); });
    fSemester.addEventListener('change', function () { page = 1; render(); });
    document.getElementById('btnReset').addEventListener('click', function () {
        fCari.value = '';
        fTahun.value = '';
        fSemester.value = '';
        page = 1;
        render();
    });

    /* ===== Modal helpers ===== */
    function bindClose(m, attr) {
        m.querySelectorAll('[' + attr + ']').forEach(function (b) {
            b.addEventListener('click', function () { m.classList.remove('show'); });
        });
        m.addEventListener('click', function (e) { if (e.target === m) m.classList.remove('show'); });
    }
    var dm = document.getElementById('detailModal');
    var fm = document.getElementById('finalModal');
    bindClose(dm, 'data-detail-close');
    bindClose(fm, 'data-final-close');

    var fpFile = document.getElementById('fpFile');
    fpFile.addEventListener('change', function () {
        document.getElementById('fpFileLabel').textContent = fpFile.files.length ? fpFile.files[0].name : 'Belum ada file dipilih';
    });

    /* ===== Aksi ===== */
    var finalTarget = null;
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-circle, .btn-final-lg');
        if (!btn) return;
        var src = btn.closest('tr');
        if (!src) return;
        var nama = src.getAttribute('data-nama');
        if (btn.classList.contains('btn-final') || btn.classList.contains('btn-final-lg') || btn.classList.contains('btn-edit')) {
            finalTarget = src;
            document.getElementById('fpNama').textContent = nama;
            document.getElementById('fpFileLabel').textContent = 'Belum ada file dipilih';
            document.getElementById('fpKet').value = '';
            fpFile.value = '';
            fm.classList.add('show');
            return;
        }
        if (btn.classList.contains('btn-hapus')) {
            if (confirm('Hapus data SKP "' + nama + '"?')) {
                src.remove();
                skp = skp.filter(function (r) { return r !== src; });
                recount();
                render();
                toast('Data SKP dihapus');
            }
            return;
        }
        if (btn.classList.contains('btn-detail')) {
            var st = src.getAttribute('data-status');
            document.getElementById('dtNama').textContent = nama;
            document.getElementById('dtNip').textContent = 'NIP ' + src.getAttribute('data-nip');
            document.getElementById('dtInisial').textContent = initial(nama);
            document.getElementById('dtPeriode').innerHTML = perBadge(src.getAttribute('data-tahun'), src.getAttribute('data-semester'));
            document.getElementById('dtStatus').innerHTML = stBadge(st);
            document.getElementById('dtDraft').innerHTML = chipHTML(src.getAttribute('data-draft'), src.getAttribute('data-draft') + ' · ' + src.getAttribute('data-hari'));
            var fin = src.getAttribute('data-final') || '';
            document.getElementById('dtFinal').innerHTML = chipHTML(fin, fin ? fin + ' · final' : '');
            document.getElementById('dtKet').textContent = src.getAttribute('data-ket') || '—';
            document.getElementById('dtActions').style.display = (st === 'selesai') ? 'none' : 'flex';
            dm.classList.add('show');
        }
    });

    document.getElementById('frmFinal').addEventListener('submit', function (e) {
        e.preventDefault();
        if (!finalTarget) { fm.classList.remove('show'); return; }
        var tahun = finalTarget.getAttribute('data-tahun');
        var sem = finalTarget.getAttribute('data-semester');
        var fname = fpFile.files.length ? fpFile.files[0].name : 'SKP_Final_' + finalTarget.getAttribute('data-nama').split(/\s+/)[1] + '_' + tahun + '-' + sem.charAt(0).toUpperCase() + sem.slice(1) + '.pdf';
        var ket = document.getElementById('fpKet').value.trim();
        var namaFinal = finalTarget.getAttribute('data-nama');
        var sudahSelesai = finalTarget.getAttribute('data-status') === 'selesai';
        finalTarget.setAttribute('data-final', fname);
        finalTarget.setAttribute('data-ket', ket || 'Verifikasi selesai.');
        if (!sudahSelesai) finalTarget.setAttribute('data-status', 'selesai');
        finalTarget.setAttribute('data-cari', finalTarget.getAttribute('data-cari') + ' ' + fname.toLowerCase());
        finalTarget.innerHTML = invRow(finalTarget);
        recount();
        render();
        fm.classList.remove('show');
        dm.classList.remove('show');
        toast(sudahSelesai ? 'File final "' + namaFinal + '" diperbarui' : 'SKP "' + namaFinal + '" selesai diverifikasi');
        finalTarget = null;
    });

    function toast(msg) {
        var t = document.getElementById('toastSkp');
        t.innerHTML = '<i class="fas fa-check-circle text-emerald-400"></i>' + msg;
        t.classList.add('show');
        clearTimeout(t._tm);
        t._tm = setTimeout(function () { t.classList.remove('show'); }, 2600);
    }

    recount();
    render();
})();
</script>