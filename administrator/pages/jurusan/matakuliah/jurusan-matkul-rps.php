<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    /* ===== Hover lift & reveal ===== */
    .lift { transition: transform .2s ease, box-shadow .2s ease; }
    .lift:hover { transform: translateY(-3px); box-shadow: 0 14px 28px -14px rgba(15,23,42,.22); }
    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
    @media (prefers-reduced-motion: reduce) { .reveal, .lift { animation: none; transition: none; } }

    /* ===== Accordion ===== */
    .acc-item { position: relative; border: 1px solid #e2e8f0; background: #fff; border-radius: 1rem; overflow: hidden;
        box-shadow: 0 1px 2px rgba(15,23,42,.05); transition: border-color .2s ease, box-shadow .2s ease; }
    .acc-item::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--cat); opacity: 0; transition: opacity .2s ease; z-index: 1; }
    .acc-item.open { border-color: #cbd5e1; box-shadow: 0 10px 24px -12px rgba(15,23,42,.18); }
    .acc-item.open::before { opacity: 1; }
    .acc-item.is-active { border-color: var(--cat); }
    .acc-te  { --cat: #f97316; }
    .acc-tk  { --cat: #0ea5e9; }
    .acc-vok { --cat: #8b5cf6; }

    .acc-head { width: 100%; display: flex; align-items: center; gap: .9rem; padding: 1rem 1.25rem; text-align: left; background: #fff; cursor: pointer; transition: background .15s ease; }
    .acc-head:hover { background: #f8fafc; }
    .acc-chev { display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 9999px; background: #f1f5f9; color: #64748b; transition: transform .3s ease, background .15s ease, color .15s ease; }
    .acc-item.open .acc-chev { transform: rotate(180deg); }
    .acc-te.open  .acc-chev { background: #f97316; color: #fff; }
    .acc-tk.open  .acc-chev { background: #0ea5e9; color: #fff; }
    .acc-vok.open .acc-chev { background: #8b5cf6; color: #fff; }

    .acc-badge { min-width: 1.75rem; text-align: center; border-radius: 9999px; padding: .15rem .6rem; font-size: .72rem; font-weight: 700; }
    .acc-te  .acc-badge { background: #ffedd5; color: #c2410c; }
    .acc-tk  .acc-badge { background: #e0f2fe; color: #0369a1; }
    .acc-vok .acc-badge { background: #ede9fe; color: #6d28d9; }

    .acc-body { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .3s ease; }
    .acc-item.open .acc-body { grid-template-rows: 1fr; }
    .acc-inner { min-height: 0; overflow: hidden; }

    /* ===== Tombol aksi bulat ===== */
    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    /* ===== Pill semester ===== */
    .sem-pills { display: flex; gap: .4rem; flex-wrap: wrap; padding: .75rem 1.25rem .5rem; }
    .sem-pill { padding: .3rem .65rem; border-radius: 9999px; font-size: 11px; font-weight: 600; cursor: pointer; border: 1.5px solid #e2e8f0; background: #f8fafc; color: #64748b; transition: all .15s ease; }
    .sem-pill:hover { border-color: #f97316; color: #f97316; background: #fff7ed; }
    .sem-pill.active-all { border-color: #0f172a; color: #fff; background: #0f172a; }
    .sem-pill.active { border-color: #f97316; color: #fff; background: #f97316; }

    /* ===== Tabel ===== */
    .tbl-wrap { max-height: 420px; overflow: auto; }
    .tbl-wrap::-webkit-scrollbar { width: 5px; height: 5px; }
    .tbl-wrap::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9px; }
    .tbl-modern { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .8rem; }
    .tbl-modern thead th { position: sticky; top: 0; z-index: 5; background: #0f172a; color: #f1f5f9; padding: .6rem .8rem; text-transform: uppercase; letter-spacing: .05em; font-weight: 700; font-size: 10px; white-space: nowrap; }
    .tbl-modern tbody tr { transition: background .15s ease; }
    .tbl-modern tbody tr:nth-child(odd) { background: #f8fafc; }
    .tbl-modern tbody tr:nth-child(even) { background: #fff; }
    .tbl-modern tbody tr:hover { background: #fff7ed; }
    .tbl-modern tbody td { padding: .55rem .8rem; border-bottom: 1px solid #f1f5f9; }
    .sem-row td { background: #f1f5f9 !important; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; font-size: 10px; color: #334155; }

    /* ===== Badge status ===== */
    .badge-aktif { display: inline-flex; align-items: center; gap: .3rem; margin-left: .5rem; vertical-align: middle; transform: translateY(-2px); border-radius: 9999px; padding: .1rem .5rem; font-size: 9px; font-weight: 700; line-height: 1; text-transform: uppercase; letter-spacing: .04em; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-jejak { display: inline-flex; align-items: center; margin-left: .5rem; vertical-align: middle; transform: translateY(-2px); border-radius: 9999px; padding: .1rem .5rem; font-size: 9px; font-weight: 700; line-height: 1; text-transform: uppercase; letter-spacing: .04em; background: #f1f5f9; color: #64748b; }

    /* ===== Legend donut ===== */
    .legend-item { display: flex; align-items: center; gap: .5rem; font-size: 11px; color: #cbd5e1; }
    .legend-dot { width: 10px; height: 10px; border-radius: 9999px; flex-shrink: 0; }

    /* ===== Modal ===== */
    .modal-overlay { display: none; position: fixed; inset: 0; z-index: 90; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem; }
    .modal-overlay.show { display: flex; }

    /* ===== Empty state ===== */
    .empty-hidden { display: none; }
    .empty-show { display: flex; }
</style><main class="content-area content-scroll">
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600"><i class="fas fa-book"></i></div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Matakuliah / RPS</h1>
                    <p class="text-xs text-slate-500">135 matakuliah &middot; 6 kurikulum &middot; 3 program studi.</p>
                </div>
            </div>
            <button type="button" id="btnTambah" class="rounded-lg bg-orange-500 px-3 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 shadow-sm"><i class="fas fa-plus mr-1"></i>Tambah Matakuliah</button>
        </div>
    </section>
    <section class="mb-5">
        <div class="reveal relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#0f1f3d] via-[#1a365d] to-[#234670] text-white shadow-xl shadow-[#1a365d]/30">
            <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="xMidYMid slice" viewBox="0 0 800 240" aria-hidden="true"><defs>
                <radialGradient id="rpsH1" cx="90%" cy="0%" r="60%"><stop offset="0%" stop-color="#f97316" stop-opacity="0.35"/><stop offset="100%" stop-color="#f97316" stop-opacity="0"/></radialGradient>
                <radialGradient id="rpsH2" cx="0%" cy="100%" r="55%"><stop offset="0%" stop-color="#0ea5e9" stop-opacity="0.35"/><stop offset="100%" stop-color="#0ea5e9" stop-opacity="0"/></radialGradient>
            </defs>
                <rect width="800" height="240" fill="url(#rpsH2)"/><rect width="800" height="240" fill="url(#rpsH1)"/>
            </svg>
            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-6 px-6 py-7 lg:px-8">
                <div class="min-w-0">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-orange-200 ring-1 ring-white/15"><span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-orange-400"></span></span>RPS Kurikulum Aktif</span>
                    <h2 class="mt-3 text-xl lg:text-2xl font-bold tracking-tight">Kurikulum Tahun 2022</h2>
                    <p class="mt-1 max-w-lg text-sm text-slate-300">Jumlah RPS matakuliah pada kurikulum terbaru setiap program studi, dirinci per semester ganjil dan genap.</p>
                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur-sm">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0"><i class="fas fa-bolt text-sm shrink-0" style="color:#f97316"></i><p class="truncate text-xs font-semibold text-slate-200">S1 Teknik Elektro</p></div>
                                <span class="badge-aktif shrink-0">2022</span>
                            </div>
                            <div class="mt-3 flex items-end justify-between">
                                <div><p class="text-2xl font-extrabold leading-none">34</p><p class="mt-1 text-[11px] text-slate-400">total RPS</p></div>
                                <p class="text-xs font-bold" style="color:#f97316">106 SKS</p>
                            </div>
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <div class="rounded-lg bg-white/5 px-2.5 py-1.5 ring-1 ring-white/10"><p class="text-[10px] text-slate-400"><i class="fas fa-sun mr-1"></i>Ganjil</p><p class="text-sm font-bold">20 RPS</p></div>
                                <div class="rounded-lg bg-white/5 px-2.5 py-1.5 ring-1 ring-white/10"><p class="text-[10px] text-slate-400"><i class="fas fa-moon mr-1"></i>Genap</p><p class="text-sm font-bold">14 RPS</p></div>
                            </div>
                        </div>
                        <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur-sm">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0"><i class="fas fa-microchip text-sm shrink-0" style="color:#0ea5e9"></i><p class="truncate text-xs font-semibold text-slate-200">S1 Teknik Komputer</p></div>
                                <span class="badge-aktif shrink-0">2021</span>
                            </div>
                            <div class="mt-3 flex items-end justify-between">
                                <div><p class="text-2xl font-extrabold leading-none">27</p><p class="mt-1 text-[11px] text-slate-400">total RPS</p></div>
                                <p class="text-xs font-bold" style="color:#0ea5e9">85 SKS</p>
                            </div>
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <div class="rounded-lg bg-white/5 px-2.5 py-1.5 ring-1 ring-white/10"><p class="text-[10px] text-slate-400"><i class="fas fa-sun mr-1"></i>Ganjil</p><p class="text-sm font-bold">15 RPS</p></div>
                                <div class="rounded-lg bg-white/5 px-2.5 py-1.5 ring-1 ring-white/10"><p class="text-[10px] text-slate-400"><i class="fas fa-moon mr-1"></i>Genap</p><p class="text-sm font-bold">12 RPS</p></div>
                            </div>
                        </div>
                        <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur-sm">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0"><i class="fas fa-graduation-cap text-sm shrink-0" style="color:#8b5cf6"></i><p class="truncate text-xs font-semibold text-slate-200">S1 Pendidikan Vokasi Rekayasa Elektro</p></div>
                                <span class="badge-aktif shrink-0">2021</span>
                            </div>
                            <div class="mt-3 flex items-end justify-between">
                                <div><p class="text-2xl font-extrabold leading-none">25</p><p class="mt-1 text-[11px] text-slate-400">total RPS</p></div>
                                <p class="text-xs font-bold" style="color:#8b5cf6">77 SKS</p>
                            </div>
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <div class="rounded-lg bg-white/5 px-2.5 py-1.5 ring-1 ring-white/10"><p class="text-[10px] text-slate-400"><i class="fas fa-sun mr-1"></i>Ganjil</p><p class="text-sm font-bold">14 RPS</p></div>
                                <div class="rounded-lg bg-white/5 px-2.5 py-1.5 ring-1 ring-white/10"><p class="text-[10px] text-slate-400"><i class="fas fa-moon mr-1"></i>Genap</p><p class="text-sm font-bold">11 RPS</p></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-center justify-center lg:w-[230px]">
                    <p class="mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-300">Jumlah RPS per Prodi</p>
                    <div class="relative"><div style="width:140px;height:140px;"><canvas id="donutChart"></canvas></div>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none"><p class="text-xl font-extrabold">86</p><p class="text-[10px] text-slate-400">RPS</p></div>
                    </div>
                    <div class="mt-3 space-y-1.5">
                        <div class="legend-item"><span class="legend-dot" style="background:#f97316"></span><span class="truncate max-w-[130px]">S1 Teknik Elektro</span></div>
                        <div class="legend-item"><span class="legend-dot" style="background:#0ea5e9"></span><span class="truncate max-w-[130px]">S1 Teknik Komputer</span></div>
                        <div class="legend-item"><span class="legend-dot" style="background:#8b5cf6"></span><span class="truncate max-w-[130px]">S1 Pendidikan Vokasi Rekayasa Elektro</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]"><i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i><input type="search" id="fCari" placeholder="Cari kode, nama matakuliah..." class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white"></div>
            <select id="fProdi" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400"><option value="">Semua Prodi</option>
                <option>S1 Teknik Elektro</option>
                <option>S1 Teknik Komputer</option>
                <option>S1 Pendidikan Vokasi Rekayasa Elektro</option>
            </select>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400"><option value="">Semua Kurikulum</option>
                <option value="2022">Kurikulum 2022</option>
                <option value="2021">Kurikulum 2021</option>
                <option value="2018">Kurikulum 2018</option>
                <option value="2017">Kurikulum 2017</option>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
        </div>
        <p class="mt-2 text-[11px] text-slate-400"><i class="fas fa-lightbulb mr-1 text-amber-400"></i>Setiap prodi punya beberapa kurikulum. Klik accordion untuk melihat daftar matakuliahnya. Kurikulum terbaru otomatis terbuka.</p>
    </section>
    <div id="emptyState" class="empty-hidden flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white py-14 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400"><i class="fas fa-search text-lg"></i></div>
        <p class="mt-3 text-sm font-semibold text-slate-600">Matakuliah tidak ditemukan</p>
        <p class="mt-1 text-xs text-slate-400" id="emptyStateDetail">Tidak ada data yang cocok dengan filter yang dipilih.</p>
        <button type="button" id="btnEmptyReset" class="mt-4 rounded-lg bg-slate-100 px-4 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset Filter</button>
    </div>
    <section class="mb-6" data-prodi="S1 Teknik Elektro">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-orange-600 shadow-sm"><i class="fas fa-bolt text-sm"></i></div>
            <div><h2 class="text-base font-bold text-slate-800">S1 Teknik Elektro</h2><p class="text-[11px] text-slate-500">2 kurikulum &middot; 0 matakuliah &middot; 0 SKS</p></div>
        </div>
        <div class="space-y-4">
            <div class="acc-item acc-te is-active reveal" data-prodi="S1 Teknik Elektro" data-tahun="2022">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600 text-lg"><i class="fas fa-bolt"></i></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-bold text-slate-800">Kurikulum 2022
                            <span class="badge-aktif"><i class="fas fa-circle text-[5px]"></i>Aktif</span>
                        </span>
                        <span class="block text-xs text-slate-500">Tahun 2022 &middot; 0 matakuliah &middot; 0 SKS</span>
                    </span>
                    <span class="acc-badge">0</span>
                    <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
                </div>
                <div class="acc-body">
                    <div class="acc-inner">
                        <div class="sem-pills">
                            <span class="sem-pill active-all" data-sem="all">Semua</span>
                            <span class="sem-pill" data-sem="1">Sem 1 (0)</span>
                            <span class="sem-pill" data-sem="2">Sem 2 (0)</span>
                            <span class="sem-pill" data-sem="3">Sem 3 (0)</span>
                            <span class="sem-pill" data-sem="4">Sem 4 (0)</span>
                            <span class="sem-pill" data-sem="5">Sem 5 (0)</span>
                            <span class="sem-pill" data-sem="6">Sem 6 (0)</span>
                            <span class="sem-pill" data-sem="7">Sem 7 (0)</span>
                            <span class="sem-pill" data-sem="8">Sem 8 (0)</span>
                        </div>
                        <div class="border-t border-slate-100 bg-slate-50/60 p-4">
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <div class="tbl-wrap">
                                    <table class="tbl-modern">
                                        <thead><tr><th>Kode</th><th>Nama Matakuliah</th><th class="text-center">Sem</th><th class="text-center">SKS</th><th>Konsentrasi</th><th class="text-right">Aksi</th></tr></thead>
                                         <tbody></tbody></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="acc-item acc-te  reveal" data-prodi="S1 Teknik Elektro" data-tahun="2017">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600 text-lg"><i class="fas fa-bolt"></i></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-bold text-slate-800">Kurikulum KKNI 2017
                            <span class="badge-jejak">Jejak Digital</span>
                        </span>
                        <span class="block text-xs text-slate-500">Tahun 2017 &middot; 0 matakuliah &middot; 0 SKS</span>
                    </span>
                    <span class="acc-badge">0</span>
                    <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
                </div>
                <div class="acc-body">
                    <div class="acc-inner">
                        <div class="sem-pills">
                            <span class="sem-pill active-all" data-sem="all">Semua</span>
                            <span class="sem-pill" data-sem="1">Sem 1 (0)</span>
                            <span class="sem-pill" data-sem="2">Sem 2 (0)</span>
                            <span class="sem-pill" data-sem="3">Sem 3 (0)</span>
                            <span class="sem-pill" data-sem="4">Sem 4 (0)</span>
                            <span class="sem-pill" data-sem="5">Sem 5 (0)</span>
                            <span class="sem-pill" data-sem="6">Sem 6 (0)</span>
                            <span class="sem-pill" data-sem="7">Sem 7 (0)</span>
                            <span class="sem-pill" data-sem="8">Sem 8 (0)</span>
                        </div>
                        <div class="border-t border-slate-100 bg-slate-50/60 p-4">
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <div class="tbl-wrap">
                                    <table class="tbl-modern">
                                        <thead><tr><th>Kode</th><th>Nama Matakuliah</th><th class="text-center">Sem</th><th class="text-center">SKS</th><th>Konsentrasi</th><th class="text-right">Aksi</th></tr></thead>
                                        <tbody></tbody></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="mb-6" data-prodi="S1 Teknik Komputer">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-600 shadow-sm"><i class="fas fa-microchip text-sm"></i></div>
            <div><h2 class="text-base font-bold text-slate-800">S1 Teknik Komputer</h2><p class="text-[11px] text-slate-500">2 kurikulum &middot; 0 matakuliah &middot; 0 SKS</p></div>
        </div>
        <div class="space-y-4">
            <div class="acc-item acc-tk is-active reveal" data-prodi="S1 Teknik Komputer" data-tahun="2021">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600 text-lg"><i class="fas fa-microchip"></i></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-bold text-slate-800">Kurikulum 2021
                            <span class="badge-aktif"><i class="fas fa-circle text-[5px]"></i>Aktif</span>
                        </span>
                        <span class="block text-xs text-slate-500">Tahun 2021 &middot; 0 matakuliah &middot; 0 SKS</span>
                    </span>
                    <span class="acc-badge">0</span>
                    <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
                </div>
                <div class="acc-body">
                    <div class="acc-inner">
                        <div class="sem-pills">
                            <span class="sem-pill active-all" data-sem="all">Semua</span>
                            <span class="sem-pill" data-sem="1">Sem 1 (0)</span>
                            <span class="sem-pill" data-sem="2">Sem 2 (0)</span>
                            <span class="sem-pill" data-sem="3">Sem 3 (0)</span>
                            <span class="sem-pill" data-sem="4">Sem 4 (0)</span>
                            <span class="sem-pill" data-sem="5">Sem 5 (0)</span>
                            <span class="sem-pill" data-sem="6">Sem 6 (0)</span>
                            <span class="sem-pill" data-sem="7">Sem 7 (0)</span>
                            <span class="sem-pill" data-sem="8">Sem 8 (0)</span>
                        </div>
                        <div class="border-t border-slate-100 bg-slate-50/60 p-4">
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <div class="tbl-wrap">
                                    <table class="tbl-modern">
                                        <thead><tr><th>Kode</th><th>Nama Matakuliah</th><th class="text-center">Sem</th><th class="text-center">SKS</th><th>Konsentrasi</th><th class="text-right">Aksi</th></tr></thead>
                                        <tbody></tbody></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="acc-item acc-tk  reveal" data-prodi="S1 Teknik Komputer" data-tahun="2017">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600 text-lg"><i class="fas fa-microchip"></i></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-bold text-slate-800">Kurikulum KKNI 2017
                            <span class="badge-jejak">Jejak Digital</span>
                        </span>
                        <span class="block text-xs text-slate-500">Tahun 2017 &middot; 0 matakuliah &middot; 0 SKS</span>
                    </span>
                    <span class="acc-badge">0</span>
                    <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
                </div>
                <div class="acc-body">
                    <div class="acc-inner">
                        <div class="sem-pills">
                            <span class="sem-pill active-all" data-sem="all">Semua</span>
                            <span class="sem-pill" data-sem="1">Sem 1 (0)</span>
                            <span class="sem-pill" data-sem="2">Sem 2 (0)</span>
                            <span class="sem-pill" data-sem="3">Sem 3 (0)</span>
                            <span class="sem-pill" data-sem="4">Sem 4 (0)</span>
                            <span class="sem-pill" data-sem="5">Sem 5 (0)</span>
                            <span class="sem-pill" data-sem="6">Sem 6 (0)</span>
                            <span class="sem-pill" data-sem="7">Sem 7 (0)</span>
                            <span class="sem-pill" data-sem="8">Sem 8 (0)</span>
                        </div>
                        <div class="border-t border-slate-100 bg-slate-50/60 p-4">
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <div class="tbl-wrap">
                                    <table class="tbl-modern">
                                        <thead><tr><th>Kode</th><th>Nama Matakuliah</th><th class="text-center">Sem</th><th class="text-center">SKS</th><th>Konsentrasi</th><th class="text-right">Aksi</th></tr></thead>
                                        <tbody></tbody></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="mb-6" data-prodi="S1 Pendidikan Vokasi Rekayasa Elektro">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-600 shadow-sm"><i class="fas fa-graduation-cap text-sm"></i></div>
            <div><h2 class="text-base font-bold text-slate-800">S1 Pendidikan Vokasi Rekayasa Elektro</h2><p class="text-[11px] text-slate-500">2 kurikulum &middot; 0 matakuliah &middot; 0 SKS</p></div>
        </div>
        <div class="space-y-4">
            <div class="acc-item acc-vok is-active reveal" data-prodi="S1 Pendidikan Vokasi Rekayasa Elektro" data-tahun="2021">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600 text-lg"><i class="fas fa-graduation-cap"></i></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-bold text-slate-800">Kurikulum Vokasi 2021
                            <span class="badge-aktif"><i class="fas fa-circle text-[5px]"></i>Aktif</span>
                        </span>
                        <span class="block text-xs text-slate-500">Tahun 2021 &middot; 0 matakuliah &middot; 0 SKS</span>
                    </span>
                    <span class="acc-badge">0</span>
                    <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
                </div>
                <div class="acc-body">
                    <div class="acc-inner">
                        <div class="sem-pills">
                            <span class="sem-pill active-all" data-sem="all">Semua</span>
                            <span class="sem-pill" data-sem="1">Sem 1 (0)</span>
                            <span class="sem-pill" data-sem="2">Sem 2 (0)</span>
                            <span class="sem-pill" data-sem="3">Sem 3 (0)</span>
                            <span class="sem-pill" data-sem="4">Sem 4 (0)</span>
                            <span class="sem-pill" data-sem="5">Sem 5 (0)</span>
                            <span class="sem-pill" data-sem="6">Sem 6 (0)</span>
                            <span class="sem-pill" data-sem="7">Sem 7 (0)</span>
                            <span class="sem-pill" data-sem="8">Sem 8 (0)</span>
                        </div>
                        <div class="border-t border-slate-100 bg-slate-50/60 p-4">
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <div class="tbl-wrap">
                                    <table class="tbl-modern">
                                        <thead><tr><th>Kode</th><th>Nama Matakuliah</th><th class="text-center">Sem</th><th class="text-center">SKS</th><th>Konsentrasi</th><th class="text-right">Aksi</th></tr></thead>
                                        <tbody></tbody></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="acc-item acc-vok  reveal" data-prodi="S1 Pendidikan Vokasi Rekayasa Elektro" data-tahun="2018">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600 text-lg"><i class="fas fa-graduation-cap"></i></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-bold text-slate-800">Kurikulum Vokasi 2018
                            <span class="badge-jejak">Jejak Digital</span>
                        </span>
                        <span class="block text-xs text-slate-500">Tahun 2018 &middot; 0 matakuliah &middot; 0 SKS</span>
                    </span>
                    <span class="acc-badge">0</span>
                    <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
                </div>
                <div class="acc-body">
                    <div class="acc-inner">
                        <div class="sem-pills">
                            <span class="sem-pill active-all" data-sem="all">Semua</span>
                            <span class="sem-pill" data-sem="1">Sem 1 (0)</span>
                            <span class="sem-pill" data-sem="2">Sem 2 (0)</span>
                            <span class="sem-pill" data-sem="3">Sem 3 (0)</span>
                            <span class="sem-pill" data-sem="4">Sem 4 (0)</span>
                            <span class="sem-pill" data-sem="5">Sem 5 (0)</span>
                            <span class="sem-pill" data-sem="6">Sem 6 (0)</span>
                            <span class="sem-pill" data-sem="7">Sem 7 (0)</span>
                            <span class="sem-pill" data-sem="8">Sem 8 (0)</span>
                        </div>
                        <div class="border-t border-slate-100 bg-slate-50/60 p-4">
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <div class="tbl-wrap">
                                    <table class="tbl-modern">
                                        <thead><tr><th>Kode</th><th>Nama Matakuliah</th><th class="text-center">Sem</th><th class="text-center">SKS</th><th>Konsentrasi</th><th class="text-right">Aksi</th></tr></thead>
                                        <tbody></tbody></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<!-- ===== Modal Tambah Matakuliah ===== -->
<div class="modal-overlay" id="tambahModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-plus mr-1 text-orange-500"></i>Tambah Matakuliah & RPS</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-tambah-close>&times;</button>
        </div>
        <form id="frmTambah" class="p-5 space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kode</label>
                    <input type="text" id="tpKode" required placeholder="mis. EAD64173" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"></div>
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">SKS</label>
                    <input type="number" id="tpSKS" required min="1" max="6" value="3" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"></div>
            </div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Nama Matakuliah</label>
                <input type="text" id="tpNama" required placeholder="mis. Elektronika Industri" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"></div>
            <div class="grid grid-cols-3 gap-3">
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Program Studi</label>
                    <select id="tpProdi" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option value="S1 Teknik Elektro">S1 Teknik Elektro</option>
                        <option value="S1 Teknik Komputer">S1 Teknik Komputer</option>
                        <option value="S1 Pendidikan Vokasi Rekayasa Elektro">S1 Pendidikan Vokasi Rekayasa Elektro</option>
                    </select></div>
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kurikulum</label>
                    <select id="tpTahun" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option value="2022">Kurikulum 2022</option>
                        <option value="2021">Kurikulum 2021</option>
                        <option value="2018">Kurikulum 2018</option>
                        <option value="2017">Kurikulum 2017</option>
                        <option value="2016">Kurikulum 2016</option>
                    </select></div>
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Semester</label>
                    <select id="tpSem" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option>
                    </select></div>
            </div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Konsentrasi <span class="text-slate-400 font-normal">(opsional)</span></label>
                <input type="text" id="tpKons" placeholder="mis. Konversi Energi, Informatika" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"></div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Unggah RPS <span class="text-slate-400 font-normal">(PDF, maks 2MB)</span></label>
                <label class="relative flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center transition hover:border-orange-400 hover:bg-orange-50">
                    <input type="file" id="tpFile" accept=".pdf,.doc,.docx" class="sr-only">
                    <i class="fas fa-cloud-upload-alt text-xl text-orange-400"></i>
                    <p class="mt-2 text-xs font-medium text-slate-600"><span id="tpFileLabel">Klik untuk memilih file RPS</span></p>
                    <p class="text-[10px] text-slate-400">file akan ditautkan ke baris matakuliah</p>
                </label></div>
            <div class="flex justify-end gap-2 pt-1 border-t border-slate-100">
                <button type="button" data-tambah-close class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium transition"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>
<!-- ===== Modal Edit Matakuliah ===== -->
<div class="modal-overlay" id="editModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-pen mr-1 text-sky-500"></i>Edit Matakuliah</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-edit-close>&times;</button>
        </div>
        <form id="frmEdit" class="p-5 space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kode</label>
                    <input type="text" id="epKode" required placeholder="mis. EAD64173" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400 focus:bg-white"></div>
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">SKS</label>
                    <input type="number" id="epSKS" required min="1" max="6" value="3" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400 focus:bg-white"></div>
            </div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Nama Matakuliah</label>
                <input type="text" id="epNama" required placeholder="mis. Elektronika Industri" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400 focus:bg-white"></div>
            <div class="grid grid-cols-3 gap-3">
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Program Studi</label>
                    <select id="epProdi" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <option value="S1 Teknik Elektro">S1 Teknik Elektro</option>
                        <option value="S1 Teknik Komputer">S1 Teknik Komputer</option>
                        <option value="S1 Pendidikan Vokasi Rekayasa Elektro">S1 Pendidikan Vokasi Rekayasa Elektro</option>
                    </select></div>
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kurikulum</label>
                    <select id="epTahun" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <option value="2022">Kurikulum 2022</option>
                        <option value="2021">Kurikulum 2021</option>
                        <option value="2018">Kurikulum 2018</option>
                        <option value="2017">Kurikulum 2017</option>
                        <option value="2016">Kurikulum 2016</option>
                    </select></div>
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Semester</label>
                    <select id="epSem" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                        <option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option>
                    </select></div>
            </div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Konsentrasi <span class="text-slate-400 font-normal">(opsional)</span></label>
                <input type="text" id="epKons" placeholder="mis. Konversi Energi, Informatika" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400 focus:bg-white"></div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Unggah RPS <span class="text-slate-400 font-normal">(PDF, maks 2MB)</span></label>
                <label class="relative flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center transition hover:border-sky-400 hover:bg-sky-50">
                    <input type="file" id="epFile" accept=".pdf,.doc,.docx" class="sr-only">
                    <i class="fas fa-cloud-upload-alt text-xl text-sky-400"></i>
                    <p class="mt-2 text-xs font-medium text-slate-600"><span id="epFileLabel">Klik untuk memilih file RPS (opsional)</span></p>
                    <p class="text-[10px] text-slate-400">kosongkan jika tidak ingin mengganti RPS</p>
                </label></div>
            <div class="flex justify-end gap-2 pt-1 border-t border-slate-100">
                <button type="button" data-edit-close class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-sky-500 hover:bg-sky-600 text-white font-medium transition"><i class="fas fa-save mr-1"></i>Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<!-- ===== Modal Detail ===== -->
<div class="modal-overlay" id="detailModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="relative bg-slate-900 px-5 py-4">
            <div class="absolute right-0 top-0 h-full w-36 opacity-20" style="background:radial-gradient(circle at 70% 30%, #f97316, transparent 70%);"></div>
            <button type="button" class="absolute right-3 top-3 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-white/70 transition hover:bg-white/20 hover:text-white" data-modal-close aria-label="Tutup">&times;</button>
            <p class="text-[11px] font-medium uppercase tracking-wider text-orange-400"><i class="fas fa-book-open mr-1.5"></i>Detail Matakuliah</p>
            <h4 class="mt-1 text-lg font-bold leading-snug text-white" id="dtNama">&mdash;</h4>
            <div class="mt-2 inline-flex items-center rounded-lg bg-black/30 px-2.5 py-1 font-mono text-xs font-bold tracking-wider text-amber-300"><i class="fas fa-hashtag mr-1.5 text-[10px]"></i><span id="dtKode">&mdash;</span></div>
        </div>
        <div class="p-5 space-y-3">
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700"><i class="fas fa-layer-group text-[10px]"></i>Semester <span id="dtSem">&mdash;</span></span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-100 px-3 py-1.5 text-xs font-bold text-sky-700"><i class="fas fa-calculator text-[10px]"></i><span id="dtSKS">&mdash;</span> SKS</span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-violet-100 px-3 py-1.5 text-xs font-bold text-violet-700"><i class="fas fa-tags text-[10px]"></i><span id="dtKons">&mdash;</span></span>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Lokasi pada kurikulum</p>
                <div class="mt-2 space-y-2">
                    <div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-xs text-orange-600"><i class="fas fa-graduation-cap"></i></span><div class="min-w-0"><p class="text-[11px] text-slate-400">Program Studi</p><p class="text-sm font-semibold text-slate-800" id="dtProdi">&mdash;</p></div></div>
                    <div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-xs text-sky-600"><i class="fas fa-archive"></i></span><div class="min-w-0"><p class="text-[11px] text-slate-400">Kurikulum</p><p class="text-sm font-semibold text-slate-800" id="dtTahun">&mdash;</p></div></div>
                </div>
            </div>
            <div class="flex items-start gap-3 rounded-xl bg-amber-50 px-4 py-3">
                <i class="fas fa-lightbulb mt-0.5 text-xs text-amber-500"></i>
                <div><p class="text-sm font-semibold text-slate-800">Identitas Matakuliah</p><p class="mt-0.5 text-xs leading-relaxed text-slate-500">Kode, nama, semester, bobot SKS, dan konsentrasi &mdash; merujuk pada kurikulum yang sedang aktif atau jejak digitalnya.</p></div>
            </div>
        </div>
        <div class="border-t border-slate-100 px-5 py-3.5 flex justify-end">
            <button type="button" data-modal-close class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-medium text-white transition hover:bg-slate-700">Tutup</button>
        </div>
    </div>
</div>
<script>
(function () {
    var fCari = document.getElementById('fCari');
    var fProdi = document.getElementById('fProdi');
    var fTahun = document.getElementById('fTahun');
    var prosesUrl = '../../proses.php';

    // ===== LOAD DATA DARI DATABASE =====
    function loadData() {
        $.ajax({
            url: prosesUrl + '?action=ambil-data-matakuliah',
            dataType: 'json',
            success: function(res) {
                if (!res.status) return;
                renderFromData(res.data);
            },
            error: function() {
                toast('Gagal memuat data dari database');
            }
        });
    }

    function renderFromData(data) {
        // Kelompokkan data per prodi-tahun
        var grouped = {};
        data.forEach(function(mk) {
            var key = mk.prodi + '|' + mk.tahun;
            var prodi_class = '';
            if (mk.prodi.indexOf('Teknik Elektro') !== -1) prodi_class = 'te';
            else if (mk.prodi.indexOf('Teknik Komputer') !== -1) prodi_class = 'tk';
            else if (mk.prodi.indexOf('Vokasi') !== -1 || mk.prodi.indexOf('Pendidikan') !== -1) prodi_class = 'vok';
            if (!grouped[key]) grouped[key] = { prodi: mk.prodi, tahun: mk.tahun, nama_prodi: mk.nama_prodi, prodi_class: prodi_class };
            if (!grouped[key].matakuliah) grouped[key].matakuliah = [];
            grouped[key].matakuliah.push(mk);
        });

        // Clear semua tbody - hapus semua row termasuk semester rows
        document.querySelectorAll('.acc-item tbody').forEach(function(tb) {
            tb.innerHTML = '';
        });

        // Build semester rows per acc-item
        var semNames = ['Ganjil','Genap','Ganjil','Genap','Ganjil','Genap','Ganjil','Genap'];
        document.querySelectorAll('.acc-item tbody').forEach(function(tb) {
            for (var s = 1; s <= 8; s++) {
                var semTr = document.createElement('tr');
                semTr.className = 'sem-row';
                semTr.innerHTML = '<td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester ' + s + ' (' + semNames[s-1] + ')</td>';
                tb.appendChild(semTr);
            }
        });

        // Render setiap matakuliah
        data.forEach(function(mk) {
            var sem = mk.semester;
            var semColor = ['bg-emerald-100 text-emerald-700','bg-sky-100 text-sky-700','bg-violet-100 text-violet-700','bg-amber-100 text-amber-700','bg-rose-100 text-rose-700','bg-teal-100 text-teal-700','bg-indigo-100 text-indigo-700','bg-pink-100 text-pink-700'];
            var konsBadge = mk.konsentrasi && mk.konsentrasi !== '-'
                ? '<span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">' + mk.konsentrasi + '</span>'
                : '<span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span>';
            var rpsStatus = mk.rps ? '1' : '0';
            var rpsLink = mk.rps
                ? '<a href="' + base_url() + '/file/' + mk.rps + '" target="_blank" class="rps-link mt-0.5 inline-flex items-center gap-1 text-[10px] font-semibold text-amber-600 hover:text-amber-700" data-rps-status="1"><i class="fas fa-file-pdf text-[9px]"></i>Lihat RPS</a>'
                : '<a href="javascript:void(0)" class="rps-link mt-0.5 inline-flex items-center gap-1 text-[10px] font-semibold text-slate-300 cursor-not-allowed" data-rps-status="0"><i class="fas fa-file text-[9px]"></i>RPS belum tersedia</a>';

            // Cari accordion item yang sesuai
            var accItem = document.querySelector('.acc-item[data-prodi="' + mk.prodi + '"][data-tahun="' + mk.tahun + '"]');
            if (!accItem) return;

            var tb = accItem.querySelector('tbody');
            // Cari semester row
            var semRows = tb.querySelectorAll('tr.sem-row');
            var semRow = null;
            semRows.forEach(function(tr) {
                if (tr.textContent.indexOf('Semester ' + sem) !== -1) {
                    semRow = tr;
                }
            });

            var cari = (mk.kode + ' ' + mk.nama + ' ' + (mk.konsentrasi || '')).toLowerCase();
            var tr = document.createElement('tr');
            tr.className = 'bg-white transition hover:bg-orange-50';
            tr.setAttribute('data-matkul-id', mk.id);
            tr.setAttribute('data-sem', sem);
            tr.setAttribute('data-cari', cari);
            tr.setAttribute('data-rps', rpsStatus);
            tr.innerHTML = '<td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">' + mk.kode + '</span></td>'
                + '<td><p class="font-medium text-slate-800 leading-snug">' + mk.nama + '</p>' + rpsLink + '</td>'
                + '<td class="text-center"><span class="inline-flex items-center rounded-full ' + semColor[(parseInt(sem) - 1) % 8] + ' px-2 py-0.5 text-xs font-bold">Sem ' + sem + '</span></td>'
                + '<td class="text-center"><span class="text-sm font-bold text-slate-700">' + mk.sks + '</span></td>'
                + '<td>' + konsBadge + '</td>'
                + '<td class="text-right"><div class="flex items-center justify-end gap-1.5">'
                + '<button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>'
                + '<button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>'
                + '<button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>'
                + '</div></td>';

            if (semRow) {
                semRow.parentNode.insertBefore(tr, semRow.nextSibling);
            } else {
                tb.appendChild(tr);
            }
        });

        // Re-init RPS buttons
        initRpsButtons();
    }

    // Load data saat halaman siap
    $(document).ready(function() {
        loadData();
    });

    window.toggleAcc = function (head) { head.closest('.acc-item').classList.toggle('open'); };

    /* Suntik tautan 'RPS' di bawah nama matakuliah */
    function initRpsButtons() {
        document.querySelectorAll('.acc-item tbody tr[data-cari]').forEach(function (tr) {
            if (tr.querySelector('.rps-link')) return;
            var hasRps = tr.getAttribute('data-rps') === '1';
            var nameTd = tr.querySelectorAll('td')[1];
            if (!nameTd) return;
            var link = document.createElement('a');
            link.href = 'javascript:void(0)';
            link.className = 'rps-link mt-0.5 inline-flex items-center gap-1 text-[10px] font-semibold ' + (hasRps ? 'text-amber-600 hover:text-amber-700' : 'text-slate-300 cursor-not-allowed');
            link.setAttribute('data-rps-status', hasRps ? '1' : '0');
            link.innerHTML = '<i class="fas ' + (hasRps ? 'fa-file-pdf' : 'fa-file') + ' text-[9px]"></i>' + (hasRps ? 'Lihat RPS' : 'RPS belum tersedia');
            nameTd.appendChild(link);
        });
    }
    initRpsButtons();

    /* Donut chart " Chart.js dimuat setelah include halaman ini, jadi tunggu */
    var chartData = {
        labels: ["S1 Teknik Elektro","S1 Teknik Komputer","S1 Pendidikan Vokasi Rekayasa Elektro"],
        values: [0,0,0],
        colors: ["#f97316","#0ea5e9","#8b5cf6"]
    };
    function initDonut() {
        if (!window.Chart) { setTimeout(initDonut, 200); return; }
        var ctx = document.getElementById('donutChart'); if (!ctx) return;
        new Chart(ctx, {
            type: 'doughnut',
            data: { labels: chartData.labels, datasets: [{ data: chartData.values, backgroundColor: chartData.colors, borderWidth: 3, borderColor: 'rgba(255,255,255,.15)', hoverOffset: 8 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%',
                plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 8, callbacks: { label: function (c) { return ' ' + c.label + ': ' + c.raw + ' RPS'; } } } } }
        });
    }
    initDonut();

    /* Pill semester per accordion */
    document.addEventListener('click', function (e) {
        var pill = e.target.closest('.sem-pill');
        if (!pill) return;
        var wrap = pill.closest('.sem-pills');
        var accBody = wrap.closest('.acc-body');
        var semVal = pill.getAttribute('data-sem');
        wrap.querySelectorAll('.sem-pill').forEach(function (p) { p.classList.remove('active', 'active-all'); });
        pill.classList.add(semVal === 'all' ? 'active-all' : 'active');
        accBody.querySelectorAll('tbody tr').forEach(function (tr) {
            if (tr.classList.contains('sem-row')) { tr.style.display = (semVal === 'all') ? '' : 'none'; return; }
            tr.style.display = (semVal === 'all' || tr.getAttribute('data-sem') === semVal) ? '' : 'none';
        });
        filterAll();
    });

    /* Filter global: cari + prodi + kurikulum(tahun) " hormati pill semester aktif */
    function filterAll() {
        var kata = (fCari && fCari.value || '').toLowerCase().trim();
        var prodi = fProdi ? fProdi.value : '';
        var tahun = fTahun ? fTahun.value : '';
        var items = document.querySelectorAll('.acc-item');
        var totalVisible = 0;
        for (var i = 0; i < items.length; i++) {
            var it = items[i];
            var matchProdi = !prodi || it.getAttribute('data-prodi') === prodi;
            var matchTahun = !tahun || it.getAttribute('data-tahun') === tahun;
            var semVal = 'all';
            var activePill = it.querySelector('.sem-pill.active');
            if (activePill) semVal = activePill.getAttribute('data-sem');
            var trs = it.querySelectorAll('tbody tr');
            var visible = 0;
            for (var j = 0; j < trs.length; j++) {
                var tr = trs[j];
                if (tr.classList.contains('sem-row')) {
                    tr.style.display = (semVal === 'all') ? '' : 'none';
                    continue;
                }
                var matchKata = !kata || (tr.getAttribute('data-cari') || '').indexOf(kata) !== -1;
                var matchSem = (semVal === 'all') || tr.getAttribute('data-sem') === semVal;
                var show = matchKata && matchSem;
                tr.style.display = show ? '' : 'none';
                if (show) visible++;
            }
            it.style.display = (!matchProdi || !matchTahun || visible === 0) ? 'none' : '';
            if (matchProdi && matchTahun) totalVisible += visible;
        }
        var secs = document.querySelectorAll('section[data-prodi]');
        var anySectionVisible = false;
        for (var k = 0; k < secs.length; k++) {
            var sec = secs[k];
            var anyVisible = false;
            var secItems = sec.querySelectorAll('.acc-item');
            for (var l = 0; l < secItems.length; l++) {
                if (secItems[l].style.display !== 'none') { anyVisible = true; break; }
            }
            sec.style.display = anyVisible ? '' : 'none';
            if (anyVisible) anySectionVisible = true;
        }
        var emptyState = document.getElementById('emptyState');
        var detail = document.getElementById('emptyStateDetail');
        if (emptyState) {
            var showEmpty = totalVisible === 0 && (kata || prodi || tahun || document.querySelector('.sem-pill.active'));
            emptyState.classList.toggle('empty-show', showEmpty);
            emptyState.classList.toggle('empty-hidden', !showEmpty);
            if (detail) {
                var saran = '';
                if (kata) saran = 'Tidak ada matakuliah yang cocok dengan kata "' + kata + '".';
                else if (prodi && tahun) saran = 'Kurikulum ' + tahun + ' belum tersedia untuk ' + prodi + '.';
                else if (tahun) saran = 'Belum ada kurikulum tahun ' + tahun + '.';
                else if (prodi) saran = 'Belum ada data untuk ' + prodi + '.';
                else if (document.querySelector('.sem-pill.active')) saran = 'Tidak ada matakuliah pada semester yang dipilih.';
                else saran = 'Tidak ada data yang cocok dengan filter yang dipilih.';
                detail.textContent = saran + ' Coba ubah atau reset filter.';
            }
        }
    }

    function syncTahunOptions() {
        if (!fProdi || !fTahun) return;
        var prodi = fProdi.value;
        var years = [];
        document.querySelectorAll('.acc-item').forEach(function (a) {
            if (a.getAttribute('data-prodi') === prodi) {
                var t = a.getAttribute('data-tahun');
                if (t && t !== 'null' && years.indexOf(t) === -1) years.push(t);
            }
        });
        var opts = fTahun.querySelectorAll('option');
        for (var i = 0; i < opts.length; i++) {
            var o = opts[i];
            var val = o.getAttribute('value');
            if (!val) continue;
            o.style.display = (!prodi || years.indexOf(val) !== -1) ? '' : 'none';
        }
        if (prodi && fTahun.value && years.indexOf(fTahun.value) === -1) fTahun.value = '';
    }
    function bindFilters() {
        if (fCari) fCari.addEventListener('input', filterAll);
        if (fProdi) fProdi.addEventListener('change', function () { syncTahunOptions(); filterAll(); });
        if (fTahun) fTahun.addEventListener('change', filterAll);
        syncTahunOptions();
    }
    var btnReset = document.getElementById('btnReset');
    var btnEmptyReset = document.getElementById('btnEmptyReset');
    function resetFilters() {
        if (fCari) fCari.value = '';
        if (fProdi) fProdi.value = '';
        if (fTahun) fTahun.value = '';
        var opts = fTahun.querySelectorAll('option');
        for (var i = 0; i < opts.length; i++) opts[i].style.display = '';
        document.querySelectorAll('.sem-pill').forEach(function (p) { p.classList.remove('active', 'active-all'); });
        document.querySelectorAll('.sem-pill[data-sem="all"]').forEach(function (p) { p.classList.add('active-all'); });
        document.querySelectorAll('.acc-item tbody tr').forEach(function (tr) { tr.style.display = ''; });
        filterAll();
        document.querySelectorAll('.acc-item.open').forEach(function (a) { a.classList.remove('open'); });
    }
    if (btnReset) btnReset.addEventListener('click', resetFilters);
    if (btnEmptyReset) btnEmptyReset.addEventListener('click', resetFilters);
    bindFilters();

    function toast(msg) {
        var t = document.createElement('div');
        t.textContent = msg;
        t.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;background:#0f172a;color:#fff;padding:.6rem 1rem;border-radius:.6rem;font-size:.8rem;box-shadow:0 6px 18px rgba(15,23,42,.35);transition:opacity .3s ease;';
        document.body.appendChild(t);
        setTimeout(function () { t.style.opacity = '0'; setTimeout(function () { t.remove(); }, 300); }, 2200);
    }

    document.addEventListener('click', function (e) {
        var link = e.target.closest('.rps-link');
        if (link) {
            var tr = link.closest('tr');
            if (tr.getAttribute('data-rps') === '1') toast('Membuka RPS "' + (tr.querySelectorAll('td')[1] ? tr.querySelectorAll('td')[1].textContent.trim() : '') + '"');
            else toast('RPS belum tersedia untuk matakuliah ini');
            return;
        }
        var btn = e.target.closest('.btn-circle');
        if (!btn) return;
        var tr = btn.closest('tr');
        if (btn.classList.contains('btn-detail')) {
            openDetail(tr);
        } else if (btn.classList.contains('btn-edit')) {
            openEdit(tr);
        } else if (btn.classList.contains('btn-hapus')) {
            if (confirm('Hapus matakuliah ini?')) {
                var matkulId = tr.getAttribute('data-matkul-id');
                if (!matkulId) { toast('ID matakuliah tidak ditemukan'); return; }
                $.ajax({
                    url: prosesUrl + '?action=hapus-matakuliah',
                    method: 'POST',
                    data: { id: matkulId },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            tr.remove();
                            toast('Matakuliah berhasil dihapus');
                        } else {
                            toast('Gagal menghapus: ' + (res.pesan || 'Unknown error'));
                        }
                    },
                    error: function() {
                        toast('Terjadi kesalahan saat menghapus');
                    }
                });
            }
        }
    });

    function openDetail(tr) {
        var tds = tr.querySelectorAll('td');
        document.getElementById('dtKode').textContent = tds[0] ? tds[0].textContent.trim() : '---';
        document.getElementById('dtNama').textContent = tds[1] ? tds[1].textContent.trim() : '---';
        document.getElementById('dtSem').textContent = tds[2] ? tds[2].textContent.trim().replace('Sem ', '') : '---';
        document.getElementById('dtSKS').textContent = tds[3] ? tds[3].textContent.trim() : '---';
        document.getElementById('dtKons').textContent = tds[4] ? tds[4].textContent.trim() : '---';
        var accItem = tr.closest('.acc-item');
        document.getElementById('dtProdi').textContent = accItem ? accItem.getAttribute('data-prodi') : '---';
        document.getElementById('dtTahun').textContent = accItem ? 'Kurikulum ' + accItem.getAttribute('data-tahun') : '---';
        document.getElementById('detailModal').classList.add('show');
    }

    var dm = document.getElementById('detailModal');
    dm.querySelectorAll('[data-modal-close]').forEach(function (b) {
        b.addEventListener('click', function () { dm.classList.remove('show'); });
    });
    dm.addEventListener('click', function (e) { if (e.target === dm) dm.classList.remove('show'); });

    /* ===== Edit Matakuliah ===== */
    var editTarget = null;
    var em = document.getElementById('editModal');
    function openEdit(tr) {
        editTarget = tr;
        var tds = tr.querySelectorAll('td');
        var accItem = tr.closest('.acc-item');
        var kode = tds[0] ? tds[0].textContent.trim() : '';
        var nama = tds[1] ? tds[1].textContent.trim() : '';
        var semTxt = tds[2] ? tds[2].textContent.trim().replace('Sem ', '') : '1';
        var sks = tds[3] ? tds[3].textContent.trim() : '3';
        var kons = tds[4] ? tds[4].textContent.trim().replace(/^\u2014$/, '') : '';
        document.getElementById('epKode').value = kode;
        document.getElementById('epNama').value = nama;
        document.getElementById('epSem').value = /^\d+$/.test(semTxt) ? semTxt : '1';
        document.getElementById('epSKS').value = /^\d+$/.test(sks) ? sks : '3';
        document.getElementById('epKons').value = kons === '\u2014' ? '' : kons;
        if (accItem) {
            var prodi = accItem.getAttribute('data-prodi');
            document.getElementById('epProdi').value = prodi || '';
            var tahun = accItem.getAttribute('data-tahun');
            var tahunSel = document.getElementById('epTahun');
            for (var i = 0; i < tahunSel.options.length; i++) {
                if (tahunSel.options[i].value === tahun) { tahunSel.selectedIndex = i; break; }
            }
        }
        document.getElementById('epFile').value = '';
        document.getElementById('epFileLabel').textContent = 'Klik untuk memilih file RPS (opsional)';
        em.classList.add('show');
        document.getElementById('epKode').focus();
    }
    em.querySelectorAll('[data-edit-close]').forEach(function (b) {
        b.addEventListener('click', function () { em.classList.remove('show'); });
    });
    em.addEventListener('click', function (e) { if (e.target === em) em.classList.remove('show'); });

    var epFile = document.getElementById('epFile');
    if (epFile) epFile.addEventListener('change', function () {
        var l = document.getElementById('epFileLabel');
        l.textContent = epFile.files.length ? epFile.files[0].name : 'Klik untuk memilih file RPS (opsional)';
    });

    var frmEdit = document.getElementById('frmEdit');
    if (frmEdit) frmEdit.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!editTarget) { em.classList.remove('show'); return; }
        
        var matkulId = editTarget.getAttribute('data-matkul-id');
        if (!matkulId) { toast('ID matakuliah tidak ditemukan'); return; }
        
        var kode = document.getElementById('epKode').value.trim();
        var nama = document.getElementById('epNama').value.trim();
        var sem = document.getElementById('epSem').value;
        var sks = document.getElementById('epSKS').value || '3';
        var kons = document.getElementById('epKons').value.trim();
        var prodi = document.getElementById('epProdi').value;
        var tahun = document.getElementById('epTahun').value;
        
        if (!kode || !nama) return;
        
        // Buat FormData untuk upload file
        var formData = new FormData();
        formData.append('action', 'edit-matakuliah');
        formData.append('id', matkulId);
        formData.append('kode', kode);
        formData.append('nama', nama);
        formData.append('semester', sem);
        formData.append('sks', sks);
        formData.append('kurikulum', prodi + '-' + tahun);
        formData.append('konsentrasi', kons);
        if (epFile && epFile.files.length) {
            formData.append('rps_file', epFile.files[0]);
        }
        
        $.ajax({
            url: prosesUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    // Update DOM
                    var tds = editTarget.querySelectorAll('td');
                    tds[0].innerHTML = '<span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">' + kode + '</span>';
                    tds[1].innerHTML = '<p class="font-medium text-slate-800 leading-snug">' + nama + '</p>' + (epFile && epFile.files.length ? '<a href="javascript:void(0)" class="rps-link mt-0.5 inline-flex items-center gap-1 text-[10px] font-semibold text-amber-600 hover:text-amber-700" data-rps-status="1"><i class="fas fa-file-pdf text-[9px]"></i>Lihat RPS</a>' : '<a href="javascript:void(0)" class="rps-link mt-0.5 inline-flex items-center gap-1 text-[10px] font-semibold text-slate-300 cursor-not-allowed" data-rps-status="0"><i class="fas fa-file text-[9px]"></i>RPS belum tersedia</a>');
                    var semColor = ['bg-emerald-100 text-emerald-700','bg-sky-100 text-sky-700','bg-violet-100 text-violet-700','bg-amber-100 text-amber-700','bg-rose-100 text-rose-700','bg-teal-100 text-teal-700','bg-indigo-100 text-indigo-700','bg-pink-100 text-pink-700'];
                    tds[2].innerHTML = '<span class="inline-flex items-center rounded-full ' + semColor[(parseInt(sem) - 1) % 8] + ' px-2 py-0.5 text-xs font-bold">Sem ' + sem + '</span>';
                    tds[3].innerHTML = '<span class="text-sm font-bold text-slate-700">' + sks + '</span>';
                    tds[4].innerHTML = kons
                        ? '<span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">' + kons + '</span>'
                        : '<span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span>';
                    editTarget.setAttribute('data-sem', sem);
                    editTarget.setAttribute('data-cari', (kode + ' ' + nama + ' ' + kons).toLowerCase());
                    
                    var uploadInfo = epFile && epFile.files.length ? ' dan RPS "' + epFile.files[0].name + '" ditautkan' : '';
                    toast('Matakuliah "' + nama + '" diperbarui' + uploadInfo);
                    editTarget = null;
                    em.classList.remove('show');
                } else {
                    toast('Gagal memperbarui: ' + (res.pesan || 'Unknown error'));
                }
            },
            error: function() {
                toast('Terjadi kesalahan saat memperbarui');
            }
        });
    });

    /* ===== Tambah Matakuliah ===== */
    var tm = document.getElementById('tambahModal');
    var btnTambah = document.getElementById('btnTambah');
    if (btnTambah) btnTambah.addEventListener('click', function () {
        tm.classList.add('show');
        document.getElementById('tpKode').focus();
    });
    tm.querySelectorAll('[data-tambah-close]').forEach(function (b) {
        b.addEventListener('click', function () { tm.classList.remove('show'); });
    });
    tm.addEventListener('click', function (e) { if (e.target === tm) tm.classList.remove('show'); });

    var tpFile = document.getElementById('tpFile');
    if (tpFile) tpFile.addEventListener('change', function () {
        var l = document.getElementById('tpFileLabel');
        l.textContent = tpFile.files.length ? tpFile.files[0].name : 'Klik untuk memilih file RPS';
    });

    var frmTambah = document.getElementById('frmTambah');
    if (frmTambah) frmTambah.addEventListener('submit', function (e) {
        e.preventDefault();
        var kode = document.getElementById('tpKode').value.trim();
        var nama = document.getElementById('tpNama').value.trim();
        var prodi = document.getElementById('tpProdi').value;
        var tahun = document.getElementById('tpTahun').value;
        var sem = document.getElementById('tpSem').value;
        var sks = document.getElementById('tpSKS').value || '3';
        var kons = document.getElementById('tpKons').value.trim();
        if (!kode || !nama) return;
        
        // Buat FormData untuk upload file
        var formData = new FormData();
        formData.append('action', 'tambah-matakuliah');
        formData.append('kode', kode);
        formData.append('nama', nama);
        formData.append('semester', sem);
        formData.append('sks', sks);
        formData.append('kurikulum', prodi + '-' + tahun);
        formData.append('konsentrasi', kons);
        if (tpFile && tpFile.files.length) {
            formData.append('rps_file', tpFile.files[0]);
        }
        
        $.ajax({
            url: prosesUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    toast('Matakuliah "' + nama + '" berhasil ditambahkan');
                    tm.classList.remove('show');
                    frmTambah.reset();
                    document.getElementById('tpFileLabel').textContent = 'Klik untuk memilih file RPS';
                    // Reload data
                    loadData();
                } else {
                    toast('Gagal menambahkan: ' + (res.pesan || 'Unknown error'));
                }
            },
            error: function() {
                toast('Terjadi kesalahan saat menambahkan');
            }
        });
    });
})();</script>
