<?php
/**
 * Halaman: Peminjaman Inventaris (Jurusan)
 * Tampilan murni HTML statis (dummy) — SEMUA data ditulis langsung di HTML.
 * Struktur meniru tabel `form_peminjaman` (peminjam, item, tgl pinjam/kembali,
 * keperluan, status proses/selesai, bukti, keterangan) dan `property` (barang).
 * Benda yang sedang dipinjam = kartu (tanpa jadwal kembali; hanya penghitung
 * lama dipinjam dengan kode warna: <2 jam normal, >=2 jam warning, >=4 jam danger).
 * Riwayat = tabel. JS untuk: tab, cari/filter, pagination riwayat, konfirmasi
 * pengembalian, modal detail, hapus, penghitung waktu berjalan.
 */
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
    @media (prefers-reduced-motion: reduce) { .reveal { animation: none; } }

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
    .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

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

    .pin-card { border: 1px solid #e2e8f0; background: #fff; border-radius: 14px; padding: .85rem .95rem; box-shadow: 0 1px 2px rgba(15,23,42,.05); transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
    .pin-card:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -12px rgba(15,23,42,.22); border-color: #c7d2fe; }
    .pin-card.st-ok { border-color: #e2e8f0; border-top: 3px solid #0ea5e9; background: #fff; }
    .pin-card.st-warn { border-color: #fde68a; border-top: 3px solid #f59e0b; background: linear-gradient(180deg, #fffbeb 0%, #fff 72%); box-shadow: 0 6px 18px -8px rgba(245,158,11,.4); }
    .pin-card.st-danger { border-color: #fecdd3; border-top: 3px solid #f43f5e; background: linear-gradient(180deg, #fff1f2 0%, #fff 72%); box-shadow: 0 8px 22px -8px rgba(244,63,94,.55); }
    .warnbar { display: flex; align-items: center; gap: .45rem; padding: .45rem .7rem; border-radius: 10px; font-size: 11px; font-weight: 700; margin-bottom: .7rem; }
    .warnbar.hidden { display: none; }
    .warnbar-warn { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .warnbar-danger { background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; animation: warnPulse 1.6s ease-in-out infinite; }
    @keyframes warnPulse { 0%,100% { box-shadow: 0 0 0 0 rgba(244,63,94,.4); } 50% { box-shadow: 0 0 0 7px rgba(244,63,94,0); } }

    .livedot { display: inline-block; width: 8px; height: 8px; border-radius: 9999px; background: #0ea5e9; animation: dotBlink 1.6s ease-in-out infinite; }
    @keyframes dotBlink { 0%,100% { opacity: .45; } 50% { opacity: 1; } }

    .el-chip { display: inline-flex; align-items: center; gap: .4rem; padding: .3rem .65rem; border-radius: 9999px; font-size: 11px; font-weight: 700; line-height: 1; transition: background .2s ease, color .2s ease; }
    .el-chip .el-now { display: inline-block; min-width: 6.4rem; text-align: left; font-variant-numeric: tabular-nums; }
    .el-sky { background: #e0f2fe; color: #0369a1; }
    .el-amber { background: #fef3c7; color: #b45309; }
    .el-rose { background: #ffe4e6; color: #be123c; }
    .el-tag { font-size: 10px; font-weight: 700; }
    .el-tag-sky { color: #0284c7; }
    .el-tag-amber { color: #d97706; }
    .el-tag-rose { color: #e11d48; }

    .pin-ico { display: inline-flex; align-items: center; justify-content: center; width: 2.7rem; height: 2.7rem; border-radius: 14px; flex-shrink: 0; }
    .pin-ico-sm { width: 2.1rem; height: 2.1rem; border-radius: 10px; font-size: .8rem; }
    .st-badge { display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .55rem; border-radius: 9999px; font-size: 10px; font-weight: 700; line-height: 1; white-space: nowrap; }

    .ipick { display: inline-flex; align-items: center; justify-content: center; width: 2.15rem; height: 2.15rem; border-radius: .65rem; border: 1px solid #e2e8f0; background: #fff; color: #64748b; cursor: pointer; transition: all .15s ease; }
    .ipick:hover { border-color: #c4b5fd; color: #7c3aed; }
    .ipick.active { border-color: #7c3aed; background: #f5f3ff; color: #6d28d9; box-shadow: 0 0 0 1px #7c3aed; }
    .cpick { width: 1.7rem; height: 1.7rem; border-radius: 9999px; border: 2px solid #fff; background: linear-gradient(135deg, var(--a), var(--b)); cursor: pointer; transition: all .15s ease; box-shadow: 0 0 0 1px #e2e8f0; }
    .cpick:hover { transform: scale(1.12); }
    .cpick.active { box-shadow: 0 0 0 2px #fff, 0 0 0 4px #7c3aed; transform: scale(1.05); }

    .pin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .pin-table thead th { background: #f8fafc; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; text-align: left; padding: .6rem .85rem; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
    .pin-table tbody td { padding: .65rem .85rem; font-size: .78rem; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .pin-table tbody tr:last-child td { border-bottom: none; }
    .pin-table tbody tr { transition: background .15s ease; }
    .pin-table tbody tr:hover { background: #f8fafc; }

    .modal-overlay { display: none; position: fixed; inset: 0; z-index: 90; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem; }
    .modal-overlay.show { display: flex; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-box-open"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Peminjaman Inventaris</h2>
                    <p class="text-xs text-slate-500">Kelola peminjaman kunci lab, ruangan, dan proyektor.</p>
                </div>
            </div>
            <button type="button" id="btnTambah" class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600">
                <i class="fas fa-plus text-sm"></i>
                <span class="tip">Tambah Barang Inventaris</span>
            </button>
        </div>
    </section>

    <!-- ===== Statistik ===== -->
    <section class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="tile-orange tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-orange-500/25">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Total Peminjaman</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight" id="tileTotal">16</p>
                    <p class="mt-2 text-[11px] text-white/70">seluruh transaksi</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-box"></i></span>
            </div>
        </div>
        <div class="tile-sky tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-sky-500/25" style="animation-delay:.05s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Sedang Dipinjam</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight" id="tileProses">6</p>
                    <p class="mt-2 text-[11px] text-white/70">berlangsung saat ini</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-arrow-right-arrow-left"></i></span>
            </div>
        </div>
        <div class="tile-emerald tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-emerald-500/25" style="animation-delay:.10s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Telah Dikembalikan</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight" id="tileSelesai">10</p>
                    <p class="mt-2 text-[11px] text-white/70">riwayat selesai</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-check"></i></span>
            </div>
        </div>
        <div class="tile-violet tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-violet-500/25" style="animation-delay:.15s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Barang Tersedia</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight" id="tileBarang">9</p>
                    <p class="mt-2 text-[11px] text-white/70">jenis inventaris</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-warehouse"></i></span>
            </div>
        </div>
    </section>

    <!-- ===== Tab Status ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap gap-2">
            <button type="button" class="tab-btn" data-tab="semua" aria-selected="true"><span class="tdot bg-slate-500"></span>Semua<span class="tnum" id="cnt-semua">16</span></button>
            <button type="button" class="tab-btn" data-tab="proses" aria-selected="false"><span class="tdot bg-sky-500"></span>Sedang Dipinjam<span class="tnum" id="cnt-proses">6</span></button>
            <button type="button" class="tab-btn" data-tab="selesai" aria-selected="false"><span class="tdot bg-emerald-500"></span>Riwayat<span class="tnum" id="cnt-selesai">10</span></button>
            <button type="button" class="tab-btn" data-tab="inventaris" aria-selected="false"><span class="tdot bg-violet-500"></span>Inventaris<span class="tnum" id="cnt-inventaris">9</span></button>
        </div>
    </section>

    <!-- ===== Toolbar ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari peminjam, barang, keperluan…"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <select id="fBarang" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Barang</option>
                <option>Kunci Lab. Komputer 1</option>
                <option>Kunci Lab. Komputer 2</option>
                <option>Kunci Lab. Kendali</option>
                <option>Kunci Lab. Tenaga Listrik</option>
                <option>Kunci Lab. Elektronika</option>
                <option>Kunci R.K. 2.11</option>
                <option>Kunci R.K. 3.16</option>
                <option>Proyektor Viewson</option>
                <option>Proyektor Sony</option>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
        </div>
    </section>

    <!-- ===== Ringkasan ===== -->
    <section class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs text-slate-500">Menampilkan <b id="jmlDitemukan" class="text-slate-800">16</b> <span id="jmlWord">peminjaman</span></p>
        <p class="text-xs text-slate-400"><i class="fas fa-lightbulb mr-1 text-amber-400"></i>Kartu berwarna oranye/merah menandakan peminjaman sudah berjalan lebih dari 2 jam / 4 jam.</p>
    </section>

    <!-- ===== Sedang Dipinjam (kartu) ===== -->
    <section id="wrapProses">
        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
            <h3 class="flex items-center gap-2 text-[12px] font-bold text-slate-700"><span class="livedot"></span>Sedang Dipinjam <span class="font-normal text-slate-400">— berlangsung saat ini</span></h3>
        </div>
        <div class="grid gap-3 md:grid-cols-2" id="listProses">

            <div class="pin-card reveal st-ok" data-status="proses" data-dipinjam="18 Agu 2026, 08:00" data-barang="Kunci Lab. Komputer 1" data-peminjam="Rohayati Idris — 05202425001" data-keperluan="Kuliah" data-cari="rohayati idris 05202425001 kunci lab komputer 1 kuliah">
                <div class="warnbar hidden"><i class="fas fa-triangle-exclamation"></i><span class="warnbar-txt"></span></div>
                <div class="flex items-start gap-3">
                    <span class="pin-ico bg-sky-100 text-sky-600"><i class="fas fa-key"></i></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold leading-snug text-slate-800">Kunci Lab. Komputer 1</p>
                        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-500">
                            <span><i class="fas fa-user mr-1 text-slate-400"></i>Rohayati Idris — 05202425001</span>
                            <span><i class="fas fa-calendar-day mr-1 text-slate-400"></i>Dipinjam 18 Agu 2026, 08:00</span>
                        </div>
                    </div>
                    <div class="ml-auto flex shrink-0 flex-col items-end gap-1.5">
                        <span class="el-chip el-sky"><i class="fas fa-stopwatch"></i><b class="el-now">0 mnt</b></span>
                        <span class="el-tag el-tag-sky">Baru &lt; 2 jam</span>
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-dashed border-slate-100 pt-3">
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600"><i class="fas fa-graduation-cap mr-0.5"></i>Kuliah</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                        <button type="button" class="btn-kembali btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-arrow-left text-xs"></i><span class="tip">Konfirmasi Kembali</span></button>
                    </div>
                </div>
            </div>

            <div class="pin-card reveal st-ok" data-status="proses" data-dipinjam="18 Agu 2026, 10:00" data-barang="Proyektor Viewson" data-peminjam="Malik Abdul Azis — 521414034" data-keperluan="Praktikum" data-cari="malik abdul azis 521414034 proyektor viewson konverter vga to hdmi praktikum">
                <div class="warnbar hidden"><i class="fas fa-triangle-exclamation"></i><span class="warnbar-txt"></span></div>
                <div class="flex items-start gap-3">
                    <span class="pin-ico bg-indigo-100 text-indigo-600"><i class="fas fa-video"></i></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold leading-snug text-slate-800">Proyektor Viewson <span class="font-normal text-slate-400">+ Konverter VGA to HDMI</span></p>
                        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-500">
                            <span><i class="fas fa-user mr-1 text-slate-400"></i>Malik Abdul Azis — 521414034</span>
                            <span><i class="fas fa-calendar-day mr-1 text-slate-400"></i>Dipinjam 18 Agu 2026, 10:00</span>
                        </div>
                    </div>
                    <div class="ml-auto flex shrink-0 flex-col items-end gap-1.5">
                        <span class="el-chip el-sky"><i class="fas fa-stopwatch"></i><b class="el-now">0 mnt</b></span>
                        <span class="el-tag el-tag-sky">Baru &lt; 2 jam</span>
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-dashed border-slate-100 pt-3">
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600"><i class="fas fa-flask mr-0.5"></i>Praktikum</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                        <button type="button" class="btn-kembali btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-arrow-left text-xs"></i><span class="tip">Konfirmasi Kembali</span></button>
                    </div>
                </div>
            </div>

            <div class="pin-card reveal st-danger" data-status="proses" data-dipinjam="17 Agu 2026, 14:00" data-barang="Kunci Lab. Kendali" data-peminjam="Fajar Octavianus E. P. Tuelah — 521415001" data-keperluan="Skripsi" data-cari="fajar octavianus e p tuelah 521415001 kunci lab kendali skripsi">
                <div class="warnbar warnbar-danger"><i class="fas fa-triangle-exclamation"></i><span class="warnbar-txt">Sudah dipinjam lebih dari 4 jam — segera tindak lanjuti</span></div>
                <div class="flex items-start gap-3">
                    <span class="pin-ico bg-violet-100 text-violet-600"><i class="fas fa-key"></i></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold leading-snug text-slate-800">Kunci Lab. Kendali</p>
                        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-500">
                            <span><i class="fas fa-user mr-1 text-slate-400"></i>Fajar Octavianus E. P. Tuelah — 521415001</span>
                            <span><i class="fas fa-calendar-day mr-1 text-slate-400"></i>Dipinjam 17 Agu 2026, 14:00</span>
                        </div>
                    </div>
                    <div class="ml-auto flex shrink-0 flex-col items-end gap-1.5">
                        <span class="el-chip el-rose"><i class="fas fa-stopwatch"></i><b class="el-now">1 hari</b></span>
                        <span class="el-tag el-tag-rose">Danger ≥ 4 jam</span>
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-dashed border-slate-100 pt-3">
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600"><i class="fas fa-user-graduate mr-0.5"></i>Skripsi</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                        <button type="button" class="btn-kembali btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-arrow-left text-xs"></i><span class="tip">Konfirmasi Kembali</span></button>
                    </div>
                </div>
            </div>

            <div class="pin-card reveal st-ok" data-status="proses" data-dipinjam="18 Agu 2026, 09:00" data-barang="Kunci Lab. Tenaga Listrik" data-peminjam="Muh Zulfikar Padmon — 521414024" data-keperluan="Kuliah" data-cari="muh zulfikar padmon 521414024 kunci lab tenaga listrik kuliah">
                <div class="warnbar hidden"><i class="fas fa-triangle-exclamation"></i><span class="warnbar-txt"></span></div>
                <div class="flex items-start gap-3">
                    <span class="pin-ico bg-emerald-100 text-emerald-600"><i class="fas fa-bolt"></i></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold leading-snug text-slate-800">Kunci Lab. Tenaga Listrik</p>
                        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-500">
                            <span><i class="fas fa-user mr-1 text-slate-400"></i>Muh Zulfikar Padmon — 521414024</span>
                            <span><i class="fas fa-calendar-day mr-1 text-slate-400"></i>Dipinjam 18 Agu 2026, 09:00</span>
                        </div>
                    </div>
                    <div class="ml-auto flex shrink-0 flex-col items-end gap-1.5">
                        <span class="el-chip el-sky"><i class="fas fa-stopwatch"></i><b class="el-now">0 mnt</b></span>
                        <span class="el-tag el-tag-sky">Baru &lt; 2 jam</span>
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-dashed border-slate-100 pt-3">
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600"><i class="fas fa-graduation-cap mr-0.5"></i>Kuliah</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                        <button type="button" class="btn-kembali btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-arrow-left text-xs"></i><span class="tip">Konfirmasi Kembali</span></button>
                    </div>
                </div>
            </div>

            <div class="pin-card reveal st-ok" data-status="proses" data-dipinjam="18 Agu 2026, 08:30" data-barang="Proyektor Sony" data-peminjam="Reka Nindya Putri Salilama — 521414006" data-keperluan="Seminar" data-cari="reka nindya putri salilama 521414006 proyektor sony tas merah seminar">
                <div class="warnbar hidden"><i class="fas fa-triangle-exclamation"></i><span class="warnbar-txt"></span></div>
                <div class="flex items-start gap-3">
                    <span class="pin-ico bg-rose-100 text-rose-600"><i class="fas fa-video"></i></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold leading-snug text-slate-800">Proyektor Sony <span class="font-normal text-slate-400">(tas merah)</span></p>
                        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-500">
                            <span><i class="fas fa-user mr-1 text-slate-400"></i>Reka Nindya Putri Salilama — 521414006</span>
                            <span><i class="fas fa-calendar-day mr-1 text-slate-400"></i>Dipinjam 18 Agu 2026, 08:30</span>
                        </div>
                    </div>
                    <div class="ml-auto flex shrink-0 flex-col items-end gap-1.5">
                        <span class="el-chip el-sky"><i class="fas fa-stopwatch"></i><b class="el-now">0 mnt</b></span>
                        <span class="el-tag el-tag-sky">Baru &lt; 2 jam</span>
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-dashed border-slate-100 pt-3">
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600"><i class="fas fa-microphone mr-0.5"></i>Seminar</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                        <button type="button" class="btn-kembali btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-arrow-left text-xs"></i><span class="tip">Konfirmasi Kembali</span></button>
                    </div>
                </div>
            </div>

            <div class="pin-card reveal st-ok" data-status="proses" data-dipinjam="18 Agu 2026, 07:45" data-barang="Kunci R.K. 3.16" data-peminjam="Indriani Hulima — 521414015" data-keperluan="Kuliah" data-cari="indriani hulima 521414015 kunci r k 3 16 kuliah">
                <div class="warnbar hidden"><i class="fas fa-triangle-exclamation"></i><span class="warnbar-txt"></span></div>
                <div class="flex items-start gap-3">
                    <span class="pin-ico bg-amber-100 text-amber-600"><i class="fas fa-door-open"></i></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold leading-snug text-slate-800">Kunci R.K. 3.16</p>
                        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-500">
                            <span><i class="fas fa-user mr-1 text-slate-400"></i>Indriani Hulima — 521414015</span>
                            <span><i class="fas fa-calendar-day mr-1 text-slate-400"></i>Dipinjam 18 Agu 2026, 07:45</span>
                        </div>
                    </div>
                    <div class="ml-auto flex shrink-0 flex-col items-end gap-1.5">
                        <span class="el-chip el-sky"><i class="fas fa-stopwatch"></i><b class="el-now">0 mnt</b></span>
                        <span class="el-tag el-tag-sky">Baru &lt; 2 jam</span>
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-dashed border-slate-100 pt-3">
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600"><i class="fas fa-graduation-cap mr-0.5"></i>Kuliah</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                        <button type="button" class="btn-kembali btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-arrow-left text-xs"></i><span class="tip">Konfirmasi Kembali</span></button>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ===== Riwayat (tabel) ===== -->
    <section id="wrapRiwayat" class="mt-6">
        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
            <h3 class="text-[12px] font-bold text-slate-700"><i class="fas fa-history mr-1 text-emerald-500"></i>Riwayat Peminjaman <span class="font-normal text-slate-400">— barang telah dikembalikan</span></h3>
        </div>
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="pin-table">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Peminjam</th>
                            <th>Dipinjam</th>
                            <th>Dikembalikan</th>
                            <th>Keperluan</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbRiwayat">

                        <tr class="reveal" data-status="selesai" data-barang="Kunci Lab. Komputer 2" data-peminjam="Diaz Cipta Pratama Talawo — 521416018" data-dipinjam="17 Agu 2026, 08:00" data-kembali="17 Agu 2026, 11:00" data-keperluan="Kuliah" data-cari="diaz cipta pratama talawo 521416018 kunci lab komputer 2 kuliah">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-sky-100 text-sky-600"><i class="fas fa-key"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci Lab. Komputer 2</span></div></td>
                            <td>Diaz Cipta Pratama Talawo <span class="text-slate-400">— 521416018</span></td>
                            <td>17 Agu 2026, 08:00</td>
                            <td><span class="inline-flex items-center gap-1 text-emerald-600"><i class="fas fa-check text-[9px]"></i>17 Agu 2026, 11:00</span></td>
                            <td><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">Kuliah</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Riwayat</span></button>
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Riwayat</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="selesai" data-barang="Kunci Lab. Komputer 1" data-peminjam="Ahmad Laba — 521416010" data-dipinjam="17 Agu 2026, 13:00" data-kembali="17 Agu 2026, 15:30" data-keperluan="Kuliah" data-cari="ahmad laba 521416010 kunci lab komputer 1 kuliah">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-sky-100 text-sky-600"><i class="fas fa-key"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci Lab. Komputer 1</span></div></td>
                            <td>Ahmad Laba <span class="text-slate-400">— 521416010</span></td>
                            <td>17 Agu 2026, 13:00</td>
                            <td><span class="inline-flex items-center gap-1 text-emerald-600"><i class="fas fa-check text-[9px]"></i>17 Agu 2026, 15:30</span></td>
                            <td><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">Kuliah</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Riwayat</span></button>
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Riwayat</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="selesai" data-barang="Kunci Lab. Elektronika" data-peminjam="Devita Gude — 521416016" data-dipinjam="16 Agu 2026, 09:00" data-kembali="16 Agu 2026, 12:00" data-keperluan="Praktikum" data-cari="devita gude 521416016 kunci lab elektronika praktikum">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-emerald-100 text-emerald-600"><i class="fas fa-microchip"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci Lab. Elektronika</span></div></td>
                            <td>Devita Gude <span class="text-slate-400">— 521416016</span></td>
                            <td>16 Agu 2026, 09:00</td>
                            <td><span class="inline-flex items-center gap-1 text-emerald-600"><i class="fas fa-check text-[9px]"></i>16 Agu 2026, 12:00</span></td>
                            <td><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600"><i class="fas fa-flask mr-0.5"></i>Praktikum</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Riwayat</span></button>
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Riwayat</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="selesai" data-barang="Kunci Lab. Kendali" data-peminjam="Riyan Mursali — 521414059" data-dipinjam="16 Agu 2026, 13:00" data-kembali="16 Agu 2026, 16:00" data-keperluan="Kuliah" data-cari="riyan mursali 521414059 kunci lab kendali kuliah">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-violet-100 text-violet-600"><i class="fas fa-key"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci Lab. Kendali</span></div></td>
                            <td>Riyan Mursali <span class="text-slate-400">— 521414059</span></td>
                            <td>16 Agu 2026, 13:00</td>
                            <td><span class="inline-flex items-center gap-1 text-emerald-600"><i class="fas fa-check text-[9px]"></i>16 Agu 2026, 16:00</span></td>
                            <td><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">Kuliah</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Riwayat</span></button>
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Riwayat</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="selesai" data-barang="Kunci R.K. 2.11" data-peminjam="Jainaldi Tongkad — 521416031" data-dipinjam="15 Agu 2026, 08:00" data-kembali="15 Agu 2026, 10:30" data-keperluan="Kuliah" data-cari="jainaldi tongkad 521416031 kunci r k 2 11 kuliah">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-amber-100 text-amber-600"><i class="fas fa-door-open"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci R.K. 2.11</span></div></td>
                            <td>Jainaldi Tongkad <span class="text-slate-400">— 521416031</span></td>
                            <td>15 Agu 2026, 08:00</td>
                            <td><span class="inline-flex items-center gap-1 text-emerald-600"><i class="fas fa-check text-[9px]"></i>15 Agu 2026, 10:30</span></td>
                            <td><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">Kuliah</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Riwayat</span></button>
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Riwayat</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="selesai" data-barang="Proyektor Viewson" data-peminjam="Hermanto Idrus — 521416013" data-dipinjam="15 Agu 2026, 13:00" data-kembali="15 Agu 2026, 15:00" data-keperluan="Presentasi" data-cari="hermanto idrus 521416013 proyektor viewson konverter vga to hdmi presentasi">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-indigo-100 text-indigo-600"><i class="fas fa-video"></i></span><span class="font-semibold leading-snug text-slate-800">Proyektor Viewson <span class="font-normal text-slate-400">+ Konverter VGA to HDMI</span></span></div></td>
                            <td>Hermanto Idrus <span class="text-slate-400">— 521416013</span></td>
                            <td>15 Agu 2026, 13:00</td>
                            <td><span class="inline-flex items-center gap-1 text-emerald-600"><i class="fas fa-check text-[9px]"></i>15 Agu 2026, 15:00</span></td>
                            <td><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600"><i class="fas fa-microphone mr-0.5"></i>Presentasi</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Riwayat</span></button>
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Riwayat</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="selesai" data-barang="Kunci Lab. Tenaga Listrik" data-peminjam="Mohamad Alif Akbar Lantowa — 521414009" data-dipinjam="14 Agu 2026, 09:00" data-kembali="14 Agu 2026, 12:00" data-keperluan="Praktikum" data-cari="mohamad alif akbar lantowa 521414009 kunci lab tenaga listrik praktikum">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-emerald-100 text-emerald-600"><i class="fas fa-bolt"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci Lab. Tenaga Listrik</span></div></td>
                            <td>Mohamad Alif Akbar Lantowa <span class="text-slate-400">— 521414009</span></td>
                            <td>14 Agu 2026, 09:00</td>
                            <td><span class="inline-flex items-center gap-1 text-emerald-600"><i class="fas fa-check text-[9px]"></i>14 Agu 2026, 12:00</span></td>
                            <td><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600"><i class="fas fa-flask mr-0.5"></i>Praktikum</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Riwayat</span></button>
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Riwayat</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="selesai" data-barang="Kunci Lab. Komputer 1" data-peminjam="Rolin Ismail — 521413047" data-dipinjam="14 Agu 2026, 13:00" data-kembali="14 Agu 2026, 16:30" data-keperluan="Kuliah" data-cari="rolin ismail 521413047 kunci lab komputer 1 kuliah">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-sky-100 text-sky-600"><i class="fas fa-key"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci Lab. Komputer 1</span></div></td>
                            <td>Rolin Ismail <span class="text-slate-400">— 521413047</span></td>
                            <td>14 Agu 2026, 13:00</td>
                            <td><span class="inline-flex items-center gap-1 text-emerald-600"><i class="fas fa-check text-[9px]"></i>14 Agu 2026, 16:30</span></td>
                            <td><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">Kuliah</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Riwayat</span></button>
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Riwayat</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="selesai" data-barang="Proyektor Sony" data-peminjam="Bunawan Kolopita — 521413028" data-dipinjam="13 Agu 2026, 08:30" data-kembali="13 Agu 2026, 11:00" data-keperluan="Seminar" data-cari="bunawan kolopita 521413028 proyektor sony tas merah seminar">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-rose-100 text-rose-600"><i class="fas fa-video"></i></span><span class="font-semibold leading-snug text-slate-800">Proyektor Sony <span class="font-normal text-slate-400">(tas merah)</span></span></div></td>
                            <td>Bunawan Kolopita <span class="text-slate-400">— 521413028</span></td>
                            <td>13 Agu 2026, 08:30</td>
                            <td><span class="inline-flex items-center gap-1 text-emerald-600"><i class="fas fa-check text-[9px]"></i>13 Agu 2026, 11:00</span></td>
                            <td><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600"><i class="fas fa-microphone mr-0.5"></i>Seminar</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Riwayat</span></button>
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Riwayat</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-status="selesai" data-barang="Kunci R.K. 3.16" data-peminjam="Selvana Y Umar — 521421007" data-dipinjam="13 Agu 2026, 13:00" data-kembali="13 Agu 2026, 15:30" data-keperluan="Kuliah" data-cari="selvana y umar 521421007 kunci r k 3 16 kuliah">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-amber-100 text-amber-600"><i class="fas fa-door-open"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci R.K. 3.16</span></div></td>
                            <td>Selvana Y Umar <span class="text-slate-400">— 521421007</span></td>
                            <td>13 Agu 2026, 13:00</td>
                            <td><span class="inline-flex items-center gap-1 text-emerald-600"><i class="fas fa-check text-[9px]"></i>13 Agu 2026, 15:30</span></td>
                            <td><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">Kuliah</span></td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Riwayat</span></button>
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Riwayat</span></button>
                            </div></td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- ===== Inventaris Barang ===== -->
    <section id="wrapInventaris" class="mt-6" style="display:none">
        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
            <h3 class="text-[12px] font-bold text-slate-700"><i class="fas fa-warehouse mr-1 text-violet-500"></i>Inventaris Barang <span class="font-normal text-slate-400">— daftar aset jurusan</span></h3>
        </div>
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="pin-table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbInventaris">

                        <tr class="reveal" data-nama="Kunci Lab. Komputer 1" data-jumlah="1" data-status="tersedia" data-ikon="fa-key" data-warna="sky" data-cari="kunci lab komputer 1 kunci akses lab komputer 1">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-sky-100 text-sky-600"><i class="fas fa-key"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci Lab. Komputer 1</span></div></td>
                            <td>1</td>
                            <td><span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700"><i class="fas fa-check mr-0.5"></i>Tersedia</span></td>
                            <td class="text-slate-500">Kunci akses Lab. Komputer 1</td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Barang</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Barang</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-nama="Kunci Lab. Komputer 2" data-jumlah="1" data-status="tersedia" data-ikon="fa-key" data-warna="sky" data-cari="kunci lab komputer 2 kunci akses lab komputer 2">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-sky-100 text-sky-600"><i class="fas fa-key"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci Lab. Komputer 2</span></div></td>
                            <td>1</td>
                            <td><span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700"><i class="fas fa-check mr-0.5"></i>Tersedia</span></td>
                            <td class="text-slate-500">Kunci akses Lab. Komputer 2</td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Barang</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Barang</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-nama="Kunci Lab. Kendali" data-jumlah="1" data-status="tersedia" data-ikon="fa-key" data-warna="violet" data-cari="kunci lab kendali kunci akses lab kendali">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-violet-100 text-violet-600"><i class="fas fa-key"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci Lab. Kendali</span></div></td>
                            <td>1</td>
                            <td><span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700"><i class="fas fa-check mr-0.5"></i>Tersedia</span></td>
                            <td class="text-slate-500">Kunci akses Lab. Kendali</td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Barang</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Barang</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-nama="Kunci Lab. Tenaga Listrik" data-jumlah="1" data-status="tersedia" data-ikon="fa-bolt" data-warna="emerald" data-cari="kunci lab tenaga listrik kunci akses lab tenaga listrik">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-emerald-100 text-emerald-600"><i class="fas fa-bolt"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci Lab. Tenaga Listrik</span></div></td>
                            <td>1</td>
                            <td><span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700"><i class="fas fa-check mr-0.5"></i>Tersedia</span></td>
                            <td class="text-slate-500">Kunci akses Lab. Tenaga Listrik</td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Barang</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Barang</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-nama="Kunci Lab. Elektronika" data-jumlah="1" data-status="tersedia" data-ikon="fa-microchip" data-warna="emerald" data-cari="kunci lab elektronika kunci akses lab elektronika">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-emerald-100 text-emerald-600"><i class="fas fa-microchip"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci Lab. Elektronika</span></div></td>
                            <td>1</td>
                            <td><span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700"><i class="fas fa-check mr-0.5"></i>Tersedia</span></td>
                            <td class="text-slate-500">Kunci akses Lab. Elektronika</td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Barang</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Barang</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-nama="Kunci R.K. 2.11" data-jumlah="1" data-status="tersedia" data-ikon="fa-door-open" data-warna="amber" data-cari="kunci r k 2 11 kunci ruang kuliah r k 2 11">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-amber-100 text-amber-600"><i class="fas fa-door-open"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci R.K. 2.11</span></div></td>
                            <td>1</td>
                            <td><span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700"><i class="fas fa-check mr-0.5"></i>Tersedia</span></td>
                            <td class="text-slate-500">Kunci ruang kuliah R.K. 2.11</td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Barang</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Barang</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-nama="Kunci R.K. 3.16" data-jumlah="1" data-status="tersedia" data-ikon="fa-door-open" data-warna="amber" data-cari="kunci r k 3 16 kunci ruang kuliah r k 3 16">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-amber-100 text-amber-600"><i class="fas fa-door-open"></i></span><span class="font-semibold leading-snug text-slate-800">Kunci R.K. 3.16</span></div></td>
                            <td>1</td>
                            <td><span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700"><i class="fas fa-check mr-0.5"></i>Tersedia</span></td>
                            <td class="text-slate-500">Kunci ruang kuliah R.K. 3.16</td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Barang</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Barang</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-nama="Proyektor Viewson" data-jumlah="1" data-status="tersedia" data-ikon="fa-video" data-warna="indigo" data-cari="proyektor viewson proyektor viewsonic plus konverter vga to hdmi">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-indigo-100 text-indigo-600"><i class="fas fa-video"></i></span><span class="font-semibold leading-snug text-slate-800">Proyektor Viewson</span></div></td>
                            <td>1</td>
                            <td><span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700"><i class="fas fa-check mr-0.5"></i>Tersedia</span></td>
                            <td class="text-slate-500">Proyektor ViewSonic + konverter VGA to HDMI</td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Barang</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Barang</span></button>
                            </div></td>
                        </tr>

                        <tr class="reveal" data-nama="Proyektor Sony" data-jumlah="1" data-status="tersedia" data-ikon="fa-video" data-warna="rose" data-cari="proyektor sony proyektor sony tas merah">
                            <td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm bg-rose-100 text-rose-600"><i class="fas fa-video"></i></span><span class="font-semibold leading-snug text-slate-800">Proyektor Sony</span></div></td>
                            <td>1</td>
                            <td><span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700"><i class="fas fa-check mr-0.5"></i>Tersedia</span></td>
                            <td class="text-slate-500">Proyektor Sony (tas merah)</td>
                            <td><div class="inline-flex items-center gap-1.5">
                                <button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Barang</span></button>
                                <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Barang</span></button>
                            </div></td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- ===== Pagination (riwayat) ===== -->
    <section id="pgSection" class="mt-5 flex flex-wrap items-center justify-between gap-3">
        <p class="text-xs text-slate-400" id="pgInfo">Halaman 1 dari 2</p>
        <div class="flex items-center gap-1.5" id="pgWrap"></div>
    </section>
</main>

<!-- ===== Modal Tambah Barang Inventaris ===== -->
<div class="modal-overlay" id="tambahModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm" id="modalJudul"><i class="fas fa-plus mr-1 text-violet-500"></i>Tambah Barang Inventaris</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-tambah-close>&times;</button>
        </div>
        <form id="frmTambah" class="p-5 space-y-3">
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Nama Barang</label>
                <input type="text" id="tpNama" required placeholder="mis. Kabel Rol 5M" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white"></div>
            <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-2.5">
                <span class="pin-ico pin-ico-sm bg-sky-100 text-sky-600" id="ipPrev"><i class="fas fa-key"></i></span>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-800">Pratinjau Barang</p>
                    <p class="text-[11px] text-slate-500">Ikon &amp; warna akan tampil di daftar inventaris.</p>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Ikon</label>
                <input type="hidden" id="tpIkon" value="fa-key">
                <div class="flex flex-wrap gap-1.5" id="ikonWrap">
                    <button type="button" class="ipick active" data-ikon="fa-key" title="Kunci"><i class="fas fa-key"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-video" title="Proyektor"><i class="fas fa-video"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-tv" title="TV / Monitor"><i class="fas fa-tv"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-desktop" title="Komputer"><i class="fas fa-desktop"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-laptop" title="Laptop"><i class="fas fa-laptop"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-print" title="Printer"><i class="fas fa-print"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-keyboard" title="Keyboard"><i class="fas fa-keyboard"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-mobile-screen" title="HP / Tablet"><i class="fas fa-mobile-screen"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-memory" title="Flashdisk / Memori"><i class="fas fa-memory"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-camera" title="Kamera / CCTV"><i class="fas fa-camera"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-server" title="Server"><i class="fas fa-server"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-network-wired" title="Jaringan"><i class="fas fa-network-wired"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-wifi" title="WiFi"><i class="fas fa-wifi"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-door-open" title="Ruangan"><i class="fas fa-door-open"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-bolt" title="Tenaga Listrik"><i class="fas fa-bolt"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-plug" title="Stop Kontak / Colokan"><i class="fas fa-plug"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-microchip" title="Elektronika"><i class="fas fa-microchip"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-lightbulb" title="Lampu"><i class="fas fa-lightbulb"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-fan" title="AC / Kipas"><i class="fas fa-fan"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-headset" title="Headset / Audio"><i class="fas fa-headset"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-couch" title="Sofa / Furnitur"><i class="fas fa-couch"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-chair" title="Kursi / Meja"><i class="fas fa-chair"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-book" title="Buku / Modul"><i class="fas fa-book"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-tools" title="Perkakas / Alat Kerja"><i class="fas fa-tools"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-fire-extinguisher" title="APAR"><i class="fas fa-fire-extinguisher"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-medkit" title="P3K / Kesehatan"><i class="fas fa-medkit"></i></button>
                    <button type="button" class="ipick" data-ikon="fa-box-open" title="Barang Umum"><i class="fas fa-box-open"></i></button>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Warna</label>
                <input type="hidden" id="tpWarna" value="sky">
                <div class="flex flex-wrap gap-1.5" id="warnaWrap">
                    <button type="button" class="cpick active" data-warna="sky" title="Biru" style="--a:#38bdf8;--b:#0ea5e9"></button>
                    <button type="button" class="cpick" data-warna="blue" title="Biru Tua" style="--a:#60a5fa;--b:#2563eb"></button>
                    <button type="button" class="cpick" data-warna="indigo" title="Indigo" style="--a:#818cf8;--b:#4f46e5"></button>
                    <button type="button" class="cpick" data-warna="violet" title="Ungu" style="--a:#a78bfa;--b:#7c3aed"></button>
                    <button type="button" class="cpick" data-warna="purple" title="Ungu Tua" style="--a:#c084fc;--b:#9333ea"></button>
                    <button type="button" class="cpick" data-warna="fuchsia" title="Fuchsia" style="--a:#e879f9;--b:#c026d3"></button>
                    <button type="button" class="cpick" data-warna="pink" title="Merah Muda" style="--a:#f472b6;--b:#db2777"></button>
                    <button type="button" class="cpick" data-warna="rose" title="Merah" style="--a:#fb7185;--b:#e11d48"></button>
                    <button type="button" class="cpick" data-warna="red" title="Merah Tua" style="--a:#f87171;--b:#dc2626"></button>
                    <button type="button" class="cpick" data-warna="orange" title="Oranye" style="--a:#fb923c;--b:#ea580c"></button>
                    <button type="button" class="cpick" data-warna="amber" title="Kuning" style="--a:#fbbf24;--b:#d97706"></button>
                    <button type="button" class="cpick" data-warna="yellow" title="Kuning Terang" style="--a:#facc15;--b:#ca8a04"></button>
                    <button type="button" class="cpick" data-warna="lime" title="Lime" style="--a:#a3e635;--b:#65a30d"></button>
                    <button type="button" class="cpick" data-warna="green" title="Hijau" style="--a:#4ade80;--b:#16a34a"></button>
                    <button type="button" class="cpick" data-warna="emerald" title="Emerald" style="--a:#34d399;--b:#059669"></button>
                    <button type="button" class="cpick" data-warna="teal" title="Teal" style="--a:#2dd4bf;--b:#0d9488"></button>
                    <button type="button" class="cpick" data-warna="cyan" title="Cyan" style="--a:#22d3ee;--b:#0891b2"></button>
                    <button type="button" class="cpick" data-warna="slate" title="Abu-abu" style="--a:#cbd5e1;--b:#64748b"></button>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Jumlah</label>
                    <input type="number" id="tpJumlah" min="1" value="1" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400"></div>
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Status</label>
                    <select id="tpStatus" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400">
                        <option value="tersedia">Tersedia</option>
                        <option value="tidak tersedia">Tidak Tersedia</option>
                    </select></div>
            </div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Keterangan</label>
                <textarea id="tpKet" rows="2" placeholder="mis. Kabel rol 5 meter, 4 stop kontak" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white"></textarea></div>
            <div class="flex justify-end gap-2 pt-1 border-t border-slate-100">
                <button type="button" data-tambah-close class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">Batal</button>
                <button type="submit" id="btnTambahSubmit" class="px-4 py-2 text-xs rounded-lg bg-violet-500 hover:bg-violet-600 text-white font-medium transition"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Modal Konfirmasi Pengembalian ===== -->
<div class="modal-overlay" id="kembaliModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-arrow-left mr-1 text-emerald-500"></i>Konfirmasi Pengembalian</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-kembali-close>&times;</button>
        </div>
        <div class="p-5 space-y-3">
            <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-2.5">
                <span class="pin-ico bg-emerald-100 text-emerald-600"><i class="fas fa-box-open"></i></span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold text-slate-800" id="kbBarang">—</p>
                    <p class="text-[11px] text-slate-500" id="kbPeminjam">—</p>
                </div>
            </div>
            <form id="frmKembali">
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Keterangan (opsional)</label>
                    <textarea id="kbKet" rows="2" placeholder="mis. Barang dikembalikan dalam keadaan baik." class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-emerald-400"></textarea></div>
                <div class="flex justify-end gap-2 pt-1 border-t border-slate-100">
                    <button type="button" data-kembali-close class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-medium transition"><i class="fas fa-check mr-1"></i>Konfirmasi Kembali</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== Modal Detail ===== -->
<div class="modal-overlay" id="detailModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-eye mr-1 text-slate-700"></i>Detail Peminjaman</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-detail-close>&times;</button>
        </div>
        <div class="p-5">
            <div class="flex items-center gap-3">
                <span class="pin-ico bg-sky-100 text-sky-600"><i class="fas fa-box-open"></i></span>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-800" id="dtBarang">—</p>
                    <span class="st-badge" id="dtStatus">—</span>
                </div>
            </div>
            <div class="mt-4 space-y-2.5 text-sm">
                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2"><span class="text-xs text-slate-500">Peminjam</span><span class="text-xs font-semibold text-slate-700" id="dtPeminjam">—</span></div>
                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2"><span class="text-xs text-slate-500">Tanggal Pinjam</span><span class="text-xs font-semibold text-slate-700" id="dtPinjam">—</span></div>
                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2"><span class="text-xs text-slate-500">Dikembalikan</span><span class="text-xs font-semibold text-slate-700" id="dtKembali">—</span></div>
                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2"><span class="text-xs text-slate-500">Keperluan</span><span class="text-xs font-semibold text-slate-700" id="dtKeperluan">—</span></div>
                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2"><span class="text-xs text-slate-500">Keterangan</span><span class="text-xs font-semibold text-slate-700" id="dtKet">—</span></div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    function toast(msg) {
        var t = document.createElement('div');
        t.textContent = msg;
        t.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;background:#0f1f3d;color:#fff;padding:.6rem 1rem;border-radius:.6rem;font-size:.8rem;box-shadow:0 6px 18px rgba(15,23,42,.35);transition:opacity .3s ease;';
        document.body.appendChild(t);
        setTimeout(function () { t.style.opacity = '0'; setTimeout(function () { t.remove(); }, 300); }, 2400);
    }

    function parseDT(s) {
        var p = s.match(/(\d{1,2}) ([a-z]{3}) (\d{4}), (\d{2}):(\d{2})/i);
        if (!p) return null;
        var mon = { jan: 0, feb: 1, mar: 2, apr: 3, mei: 4, jun: 5, jul: 6, agu: 7, sep: 8, okt: 9, nov: 10, des: 11 };
        return new Date(+p[3], mon[p[2].toLowerCase()], +p[1], +p[4], +p[5]);
    }
    var MON = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    function fmtDT(d) {
        function z(n) { return (n < 10 ? '0' : '') + n; }
        return d.getDate() + ' ' + MON[d.getMonth()] + ' ' + d.getFullYear() + ', ' + z(d.getHours()) + ':' + z(d.getMinutes());
    }
    function fmtDurL(ms) {
        ms = Math.max(0, ms);
        var s = Math.floor(ms / 1000), m = Math.floor(s / 60), h = Math.floor(m / 60), d = Math.floor(h / 24);
        s = s % 60; m = m % 60; h = h % 24;
        if (d > 0) return d + ' hari ' + h + ' jam';
        if (h > 0) return h + ' jam ' + m + ' mnt';
        if (m > 0) return m + ' mnt ' + s + ' dtk';
        return s + ' dtk';
    }
    function pinMeta(barang) {
        if (barang.indexOf('Elektronika') !== -1) return { ico: 'bg-emerald-100 text-emerald-600', icon: 'fa-microchip' };
        if (barang.indexOf('Tenaga Listrik') !== -1) return { ico: 'bg-emerald-100 text-emerald-600', icon: 'fa-bolt' };
        if (barang.indexOf('Kendali') !== -1) return { ico: 'bg-violet-100 text-violet-600', icon: 'fa-key' };
        if (barang.indexOf('Proyektor Sony') !== -1) return { ico: 'bg-rose-100 text-rose-600', icon: 'fa-video' };
        if (barang.indexOf('Proyektor') !== -1) return { ico: 'bg-indigo-100 text-indigo-600', icon: 'fa-video' };
        if (barang.indexOf('R.K.') !== -1) return { ico: 'bg-amber-100 text-amber-600', icon: 'fa-door-open' };
        return { ico: 'bg-sky-100 text-sky-600', icon: 'fa-key' };
    }
    var COLOR = {
        sky: 'bg-sky-100 text-sky-600',
        blue: 'bg-blue-100 text-blue-600',
        indigo: 'bg-indigo-100 text-indigo-600',
        violet: 'bg-violet-100 text-violet-600',
        purple: 'bg-purple-100 text-purple-600',
        fuchsia: 'bg-fuchsia-100 text-fuchsia-600',
        pink: 'bg-pink-100 text-pink-600',
        rose: 'bg-rose-100 text-rose-600',
        red: 'bg-red-100 text-red-600',
        orange: 'bg-orange-100 text-orange-600',
        amber: 'bg-amber-100 text-amber-600',
        yellow: 'bg-yellow-100 text-yellow-600',
        lime: 'bg-lime-100 text-lime-600',
        green: 'bg-green-100 text-green-600',
        emerald: 'bg-emerald-100 text-emerald-600',
        teal: 'bg-teal-100 text-teal-600',
        cyan: 'bg-cyan-100 text-cyan-600',
        slate: 'bg-slate-100 text-slate-600'
    };

    var cards = Array.prototype.slice.call(document.querySelectorAll('#listProses .pin-card'));
    var rows = Array.prototype.slice.call(document.querySelectorAll('#tbRiwayat tr'));
    var inv = Array.prototype.slice.call(document.querySelectorAll('#tbInventaris tr'));
    var fCari = document.getElementById('fCari');
    var fBarang = document.getElementById('fBarang');
    var jmlDitemukan = document.getElementById('jmlDitemukan');
    var activeTab = 'semua';
    var PER = 8, page = 1;

    /* ===== Tab ===== */
    document.querySelectorAll('.tab-btn').forEach(function (b) {
        b.addEventListener('click', function () {
            document.querySelectorAll('.tab-btn').forEach(function (x) { x.setAttribute('aria-selected', 'false'); });
            b.setAttribute('aria-selected', 'true');
            activeTab = b.getAttribute('data-tab');
            page = 1;
            render();
        });
    });

    /* ===== Filter ===== */
    function visibleCards() {
        var kata = (fCari.value || '').toLowerCase().trim();
        var brg = fBarang.value;
        return cards.filter(function (c) {
            if (brg && c.getAttribute('data-barang') !== brg) return false;
            return !kata || c.getAttribute('data-cari').indexOf(kata) !== -1;
        });
    }
    function visibleRows() {
        var kata = (fCari.value || '').toLowerCase().trim();
        var brg = fBarang.value;
        return rows.filter(function (r) {
            if (brg && r.getAttribute('data-barang') !== brg) return false;
            return !kata || r.getAttribute('data-cari').indexOf(kata) !== -1;
        });
    }
    function visibleInv() {
        var kata = (fCari.value || '').toLowerCase().trim();
        var brg = fBarang.value;
        return inv.filter(function (r) {
            if (brg && r.getAttribute('data-nama') !== brg) return false;
            return !kata || r.getAttribute('data-cari').indexOf(kata) !== -1;
        });
    }

    /* ===== Render ===== */
    function render() {
        var vinv = (activeTab === 'inventaris') ? visibleInv() : [];
        var vc = (activeTab === 'selesai' || activeTab === 'inventaris') ? [] : visibleCards();
        var vr = (activeTab === 'proses' || activeTab === 'inventaris') ? [] : visibleRows();
        var pages = Math.max(1, Math.ceil(vr.length / PER));
        if (page > pages) page = pages;
        var start = (page - 1) * PER;
        cards.forEach(function (c) { c.style.display = (vc.indexOf(c) !== -1) ? '' : 'none'; });
        rows.forEach(function (r) { r.style.display = 'none'; });
        vr.forEach(function (r, i) { r.style.display = (i >= start && i < start + PER) ? '' : 'none'; });
        inv.forEach(function (r) { r.style.display = (vinv.indexOf(r) !== -1) ? '' : 'none'; });
        if (activeTab === 'inventaris') {
            jmlDitemukan.textContent = vinv.length;
            document.getElementById('jmlWord').textContent = 'barang';
        } else {
            jmlDitemukan.textContent = vc.length + vr.length;
            document.getElementById('jmlWord').textContent = 'peminjaman';
        }
        document.getElementById('wrapProses').style.display = (activeTab === 'selesai' || activeTab === 'inventaris') ? 'none' : '';
        document.getElementById('wrapRiwayat').style.display = (activeTab === 'proses' || activeTab === 'inventaris') ? 'none' : '';
        document.getElementById('wrapInventaris').style.display = (activeTab === 'inventaris') ? '' : 'none';
        document.getElementById('pgSection').style.display = (activeTab === 'proses' || activeTab === 'inventaris') ? 'none' : '';
        document.getElementById('pgInfo').textContent = 'Halaman ' + page + ' dari ' + pages;
        var wrap = document.getElementById('pgWrap');
        wrap.innerHTML = '';
        var prev = document.createElement('button');
        prev.className = 'pg-btn';
        prev.innerHTML = '<i class="fas fa-chevron-left text-[10px]"></i>';
        prev.disabled = page === 1;
        prev.onclick = function () { page--; render(); };
        wrap.appendChild(prev);
        for (var i = 1; i <= pages; i++) {
            (function (p) {
                var b = document.createElement('button');
                b.className = 'pg-btn';
                b.textContent = p;
                if (p === page) { b.style.background = '#1e3a5f'; b.style.color = '#fff'; b.style.borderColor = '#1e3a5f'; }
                b.onclick = function () { page = p; render(); };
                wrap.appendChild(b);
            })(i);
        }
        var next = document.createElement('button');
        next.className = 'pg-btn';
        next.innerHTML = '<i class="fas fa-chevron-right text-[10px]"></i>';
        next.disabled = page === pages;
        next.onclick = function () { page++; render(); };
        wrap.appendChild(next);
    }

    if (fCari) fCari.addEventListener('input', function () { page = 1; render(); });
    if (fBarang) fBarang.addEventListener('change', function () { page = 1; render(); });
    var btnReset = document.getElementById('btnReset');
    if (btnReset) btnReset.addEventListener('click', function () {
        fCari.value = '';
        fBarang.value = '';
        activeTab = 'semua';
        document.querySelectorAll('.tab-btn').forEach(function (x) { x.setAttribute('aria-selected', 'false'); });
        document.querySelector('.tab-btn[data-tab="semua"]').setAttribute('aria-selected', 'true');
        page = 1;
        render();
    });

    /* ===== Penghitung waktu & kode warna kartu ===== */
    function updateElapsed() {
        var now = new Date();
        cards.forEach(function (c) {
            var a = parseDT(c.getAttribute('data-dipinjam'));
            if (!a) return;
            var h = (now - a) / 3600000;
            var level = h >= 4 ? 'danger' : (h >= 2 ? 'warning' : 'ok');
            c.classList.remove('st-ok', 'st-warn', 'st-danger');
            c.classList.add(level === 'danger' ? 'st-danger' : (level === 'warning' ? 'st-warn' : 'st-ok'));
            var wb = c.querySelector('.warnbar');
            if (wb) {
                wb.className = 'warnbar';
                if (level === 'danger') {
                    wb.classList.add('warnbar-danger');
                    wb.querySelector('.warnbar-txt').textContent = 'Sudah dipinjam lebih dari 4 jam — segera tindak lanjuti';
                } else if (level === 'warning') {
                    wb.classList.add('warnbar-warn');
                    wb.querySelector('.warnbar-txt').textContent = 'Sudah dipinjam lebih dari 2 jam — perlu perhatian';
                } else {
                    wb.classList.add('hidden');
                }
            }
            var chip = c.querySelector('.el-chip');
            chip.className = 'el-chip el-' + level;
            c.querySelector('.el-now').textContent = fmtDurL(now - a);
            var tag = c.querySelector('.el-tag');
            tag.className = 'el-tag el-tag-' + level;
            tag.textContent = level === 'danger' ? 'Danger — sudah ≥ 4 jam' : (level === 'warning' ? 'Perhatian — sudah ≥ 2 jam' : 'Baru — belum 2 jam');
        });
    }

    /* ===== Modal helpers ===== */
    function bindClose(modal, attr) {
        modal.querySelectorAll('[' + attr + ']').forEach(function (b) {
            b.addEventListener('click', function () { modal.classList.remove('show'); });
        });
        modal.addEventListener('click', function (e) { if (e.target === modal) modal.classList.remove('show'); });
    }
    var tm = document.getElementById('tambahModal');
    var km = document.getElementById('kembaliModal');
    var dm = document.getElementById('detailModal');
    bindClose(tm, 'data-tambah-close');
    bindClose(km, 'data-kembali-close');
    bindClose(dm, 'data-detail-close');

    var editTarget = null, editIsInv = false;
    var btnTambah = document.getElementById('btnTambah');
    if (btnTambah) btnTambah.addEventListener('click', function () {
        document.getElementById('frmTambah').reset();
        editTarget = null;
        syncPickers();
        previewInv();
        setTambahMode(true);
        tm.classList.add('show');
    });

    var ipIkon = document.getElementById('tpIkon');
    var ipWarna = document.getElementById('tpWarna');
    function previewInv() {
        var box = document.getElementById('ipPrev');
        if (!box) return;
        box.className = 'pin-ico pin-ico-sm ' + (COLOR[ipWarna.value] || COLOR.sky);
        box.innerHTML = '<i class="fas ' + (ipIkon.value || 'fa-key') + '"></i>';
    }
    function bindPicker(wrapId, hidden, attr, onPick) {
        var wrap = document.getElementById(wrapId);
        if (!wrap) return;
        wrap.addEventListener('click', function (e) {
            var b = e.target.closest('button[data-' + attr + ']');
            if (!b) return;
            wrap.querySelectorAll('button').forEach(function (x) { x.classList.remove('active'); });
            b.classList.add('active');
            hidden.value = b.getAttribute('data-' + attr);
            onPick();
        });
    }
    function syncPickers() {
        [['ikonWrap', 'ikon', 'tpIkon'], ['warnaWrap', 'warna', 'tpWarna']].forEach(function (p) {
            var wrap = document.getElementById(p[0]);
            var v = document.getElementById(p[2]).value;
            wrap.querySelectorAll('button').forEach(function (x) {
                x.classList.toggle('active', x.getAttribute('data-' + p[1]) === v);
            });
        });
    }
    bindPicker('ikonWrap', ipIkon, 'ikon', previewInv);
    bindPicker('warnaWrap', ipWarna, 'warna', previewInv);

    function invRowHTML(nama, jumlah, status, ket, meta) {
        var stTxt = status === 'tersedia' ? 'Tersedia' : 'Tidak Tersedia';
        return '<td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm ' + meta.ico + '"><i class="fas ' + meta.icon + '"></i></span><span class="font-semibold leading-snug text-slate-800">' + nama + '</span></div></td>' +
            '<td>' + jumlah + '</td>' +
            '<td><span class="rounded-full px-2 py-0.5 text-[10px] font-semibold ' + (status === 'tersedia' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700') + '">' + (status === 'tersedia' ? '<i class="fas fa-check mr-0.5"></i>' : '<i class="fas fa-times mr-0.5"></i>') + stTxt + '</span></td>' +
            '<td class="text-slate-500">' + (ket || '—') + '</td>' +
            '<td><div class="inline-flex items-center gap-1.5">' +
            '<button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Barang</span></button>' +
            '<button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Barang</span></button></div></td>';
    }
    function histRowHTML(info, ket, meta) {
        return '<td><div class="flex items-center gap-2.5"><span class="pin-ico pin-ico-sm ' + meta.ico + '"><i class="fas ' + meta.icon + '"></i></span><span class="font-semibold leading-snug text-slate-800">' + info.barang + '</span></div></td>' +
            '<td>' + info.peminjam.replace(/\s*—\s*/g, ' <span class="text-slate-400">— </span>') + '</td>' +
            '<td>' + info.pinjam + '</td>' +
            '<td><span class="inline-flex items-center gap-1 text-emerald-600"><i class="fas fa-check text-[9px]"></i>' + info.kembali + '</span></td>' +
            '<td><div class="flex flex-wrap items-center gap-1"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">' + info.keperluan + '</span>' +
            (ket ? '<span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-600"><i class="fas fa-comment mr-0.5"></i>' + ket + '</span>' : '') + '</div></td>' +
            '<td><div class="inline-flex items-center gap-1.5">' +
            '<button type="button" class="btn-edit btn-circle bg-amber-500 text-white shadow-sm hover:bg-amber-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit Riwayat</span></button>' +
            '<button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>' +
            '<button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus Riwayat</span></button></div></td>';
    }
    function warnaOf(ico) {
        for (var k in COLOR) { if (COLOR[k] === ico) return k; }
        return 'sky';
    }
    function setTambahMode(add) {
        var judul = add
            ? '<i class="fas fa-plus mr-1 text-violet-500"></i>Tambah Barang Inventaris'
            : (editIsInv
                ? '<i class="fas fa-pen mr-1 text-amber-500"></i>Edit Barang Inventaris'
                : '<i class="fas fa-pen mr-1 text-amber-500"></i>Edit Riwayat Peminjaman');
        document.getElementById('modalJudul').innerHTML = judul;
        document.getElementById('btnTambahSubmit').innerHTML = '<i class="fas fa-save mr-1"></i>' + (add ? 'Simpan' : 'Simpan Perubahan');
    }

    document.getElementById('frmTambah').addEventListener('submit', function (e) {
        e.preventDefault();
        var nama = document.getElementById('tpNama').value.trim();
        if (!nama) return;
        var jumlah = document.getElementById('tpJumlah').value || '1';
        var status = document.getElementById('tpStatus').value;
        var ket = document.getElementById('tpKet').value.trim();
        var meta = { icon: ipIkon.value || 'fa-key', warna: ipWarna.value || 'sky', ico: COLOR[ipWarna.value] || COLOR.sky };
        if (editTarget) {
            if (editIsInv) {
                var namaLama = editTarget.getAttribute('data-nama');
                editTarget.setAttribute('data-nama', nama);
                editTarget.setAttribute('data-jumlah', jumlah);
                editTarget.setAttribute('data-status', status);
                editTarget.setAttribute('data-ikon', meta.icon);
                editTarget.setAttribute('data-warna', meta.warna);
                editTarget.setAttribute('data-cari', (nama + ' ' + ket).toLowerCase());
                editTarget.innerHTML = invRowHTML(nama, jumlah, status, ket, meta);
                var sel = document.getElementById('fBarang');
                for (var i = sel.options.length - 1; i >= 0; i--) {
                    if (sel.options[i].value === namaLama) sel.remove(i);
                }
                var exists = Array.prototype.some.call(sel.options, function (o) { return o.value === nama; });
                if (!exists) {
                    var opt = document.createElement('option');
                    opt.textContent = nama;
                    sel.appendChild(opt);
                }
                toast('Barang "' + nama + '" diperbarui');
            } else {
                var info = infoOf(editTarget);
                info.barang = nama;
                editTarget.setAttribute('data-barang', nama);
                editTarget.setAttribute('data-cari', (nama + ' ' + info.peminjam + ' ' + info.keperluan).toLowerCase());
                editTarget.innerHTML = histRowHTML(info, ket, meta);
                toast('Riwayat "' + nama + '" diperbarui');
            }
            editTarget = null;
            tm.classList.remove('show');
            render();
            return;
        }
        var tr = document.createElement('tr');
        tr.className = 'reveal';
        tr.setAttribute('data-nama', nama);
        tr.setAttribute('data-jumlah', jumlah);
        tr.setAttribute('data-status', status);
        tr.setAttribute('data-ikon', meta.icon);
        tr.setAttribute('data-warna', meta.warna);
        tr.setAttribute('data-cari', (nama + ' ' + ket).toLowerCase());
        tr.innerHTML = invRowHTML(nama, jumlah, status, ket, meta);
        document.getElementById('tbInventaris').insertBefore(tr, document.getElementById('tbInventaris').firstChild);
        inv.unshift(tr);
        var sel = document.getElementById('fBarang');
        var exists = Array.prototype.some.call(sel.options, function (o) { return o.value === nama; });
        if (!exists) {
            var opt = document.createElement('option');
            opt.textContent = nama;
            sel.appendChild(opt);
        }
        updateInvCount();
        tm.classList.remove('show');
        render();
        updateElapsed();
        toast('Barang "' + nama + '" ditambahkan ke inventaris');
    });

    function updateCounts() {
        var c = { proses: cards.length, selesai: rows.length };
        document.getElementById('cnt-semua').textContent = c.proses + c.selesai;
        document.getElementById('cnt-proses').textContent = c.proses;
        document.getElementById('cnt-selesai').textContent = c.selesai;
        var t1 = document.getElementById('tileTotal'); if (t1) t1.textContent = c.proses + c.selesai;
        var t2 = document.getElementById('tileProses'); if (t2) t2.textContent = c.proses;
        var t3 = document.getElementById('tileSelesai'); if (t3) t3.textContent = c.selesai;
    }

    function updateInvCount() {
        document.getElementById('cnt-inventaris').textContent = inv.length;
        var tb = document.getElementById('tileBarang'); if (tb) tb.textContent = inv.length;
    }

    /* ===== Aksi (kartu & baris tabel) ===== */
    function infoOf(src) {
        return {
            barang: src.getAttribute('data-barang'),
            peminjam: src.getAttribute('data-peminjam'),
            pinjam: src.getAttribute('data-dipinjam'),
            kembali: src.getAttribute('data-kembali') || '',
            keperluan: src.getAttribute('data-keperluan'),
            status: src.getAttribute('data-status')
        };
    }
    var kbTarget = null;
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-circle');
        if (!btn) return;
        var src = btn.closest('.pin-card') || btn.closest('tr');
        if (!src) return;
        var info = infoOf(src);
        if (btn.classList.contains('btn-kembali')) {
            kbTarget = src;
            document.getElementById('kbBarang').textContent = info.barang;
            document.getElementById('kbPeminjam').textContent = info.peminjam;
            document.getElementById('kbKet').value = '';
            km.classList.add('show');
        } else if (btn.classList.contains('btn-hapus')) {
            if (btn.closest('#tbInventaris')) {
                var namaInv = src.getAttribute('data-nama');
                if (confirm('Hapus barang "' + namaInv + '" dari inventaris?')) {
                    src.remove();
                    inv = inv.filter(function (r) { return r !== src; });
                    var sel = document.getElementById('fBarang');
                    for (var i = sel.options.length - 1; i >= 0; i--) {
                        if (sel.options[i].value === namaInv) sel.remove(i);
                    }
                    updateInvCount();
                    render();
                    toast('Barang inventaris dihapus');
                }
                return;
            }
            if (confirm('Hapus riwayat peminjaman "' + info.barang + '"?')) {
                src.remove();
                rows = rows.filter(function (r) { return r !== src; });
                updateCounts();
                render();
                toast('Riwayat peminjaman dihapus');
            }
        } else if (btn.classList.contains('btn-edit')) {
            editIsInv = !!btn.closest('#tbInventaris');
            editTarget = src;
            document.getElementById('frmTambah').reset();
            if (editIsInv) {
                var mInv = pinMeta(src.getAttribute('data-nama'));
                document.getElementById('tpNama').value = src.getAttribute('data-nama');
                document.getElementById('tpJumlah').value = src.getAttribute('data-jumlah') || '1';
                document.getElementById('tpStatus').value = src.getAttribute('data-status');
                var ketInv = src.querySelector('td:nth-child(4)');
                document.getElementById('tpKet').value = ketInv && ketInv.textContent.trim() !== '—' ? ketInv.textContent.trim() : '';
                document.getElementById('tpIkon').value = src.getAttribute('data-ikon') || mInv.icon;
                document.getElementById('tpWarna').value = src.getAttribute('data-warna') || warnaOf(mInv.ico);
            } else {
                var mHist = pinMeta(src.getAttribute('data-barang'));
                document.getElementById('tpNama').value = src.getAttribute('data-barang');
                document.getElementById('tpJumlah').value = '1';
                document.getElementById('tpStatus').value = src.getAttribute('data-status') === 'proses' ? 'tidak tersedia' : 'tersedia';
                var note = src.querySelector('.fa-comment');
                document.getElementById('tpKet').value = note ? note.parentNode.textContent.trim() : '';
                document.getElementById('tpIkon').value = mHist.icon;
                document.getElementById('tpWarna').value = warnaOf(mHist.ico);
            }
            syncPickers();
            previewInv();
            setTambahMode(false);
            tm.classList.add('show');
        } else if (btn.classList.contains('btn-detail')) {
            document.getElementById('dtBarang').textContent = info.barang;
            document.getElementById('dtPeminjam').textContent = info.peminjam;
            var st = document.getElementById('dtStatus');
            if (info.status === 'proses') {
                st.className = 'st-badge bg-sky-100 text-sky-700';
                st.innerHTML = '<i class="fas fa-arrow-right-arrow-left"></i>Dipinjam';
                document.getElementById('dtKembali').textContent = 'Belum dikembalikan';
            } else {
                st.className = 'st-badge bg-emerald-100 text-emerald-700';
                st.innerHTML = '<i class="fas fa-check"></i>Selesai';
                document.getElementById('dtKembali').textContent = info.kembali || '—';
            }
            document.getElementById('dtPinjam').textContent = info.pinjam || '—';
            document.getElementById('dtKeperluan').textContent = info.keperluan || '—';
            var note = src.querySelector('.fa-comment');
            document.getElementById('dtKet').textContent = note ? note.parentNode.textContent.trim() : '—';
            dm.classList.add('show');
        }
    });

    document.getElementById('frmKembali').addEventListener('submit', function (e) {
        e.preventDefault();
        if (!kbTarget) { km.classList.remove('show'); return; }
        var info = infoOf(kbTarget);
        var ket = document.getElementById('kbKet').value.trim();
        var now = new Date();
        var meta = pinMeta(info.barang);
        var tr = document.createElement('tr');
        tr.className = 'reveal';
        tr.setAttribute('data-status', 'selesai');
        tr.setAttribute('data-barang', info.barang);
        tr.setAttribute('data-peminjam', info.peminjam);
        tr.setAttribute('data-dipinjam', info.pinjam);
        tr.setAttribute('data-kembali', fmtDT(now));
        tr.setAttribute('data-keperluan', info.keperluan);
        tr.setAttribute('data-cari', (info.barang + ' ' + info.peminjam + ' ' + info.keperluan).toLowerCase());
        tr.innerHTML = histRowHTML({ barang: info.barang, peminjam: info.peminjam, pinjam: info.pinjam, kembali: fmtDT(now), keperluan: info.keperluan }, ket, meta);
        document.getElementById('tbRiwayat').insertBefore(tr, document.getElementById('tbRiwayat').firstChild);

        rows.push(tr);
        kbTarget.remove();
        cards = cards.filter(function (c) { return c !== kbTarget; });
        kbTarget = null;
        updateCounts();
        km.classList.remove('show');
        render();
        updateElapsed();
        toast('Pengembalian dikonfirmasi');
    });

    render();
    updateCounts();
    updateInvCount();
    updateElapsed();
    setInterval(updateElapsed, 1000);
})();
</script>