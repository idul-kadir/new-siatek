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
    .badge-aktif { display: inline-flex; align-items: center; gap: .35rem; border-radius: 9999px; padding: .18rem .6rem; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-jejak { display: inline-flex; align-items: center; border-radius: 9999px; padding: .18rem .6rem; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; background: #f1f5f9; color: #64748b; }

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
    <div id="emptyState" class="empty-hidden items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white py-14 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400"><i class="fas fa-search text-lg"></i></div>
        <p class="mt-3 text-sm font-semibold text-slate-600">Matakuliah tidak ditemukan</p>
        <p class="mt-1 text-xs text-slate-400" id="emptyStateDetail">Tidak ada data yang cocok dengan filter yang dipilih.</p>
        <button type="button" id="btnEmptyReset" class="mt-4 rounded-lg bg-slate-100 px-4 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset Filter</button>
    </div>
    <section class="mb-6" data-prodi="S1 Teknik Elektro">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-orange-600 shadow-sm"><i class="fas fa-bolt text-sm"></i></div>
            <div><h2 class="text-base font-bold text-slate-800">S1 Teknik Elektro</h2><p class="text-[11px] text-slate-500">2 kurikulum &middot; 53 matakuliah &middot; 165 SKS</p></div>
        </div>
        <div class="space-y-4">
            <div class="acc-item acc-te is-active reveal" data-prodi="S1 Teknik Elektro" data-tahun="2022">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600 text-lg"><i class="fas fa-bolt"></i></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-bold text-slate-800">Kurikulum 2022
                            <span class="badge-aktif"><i class="fas fa-circle text-[5px]"></i>Aktif</span>
                        </span>
                        <span class="block text-xs text-slate-500">Tahun 2022 &middot; 34 matakuliah &middot; 106 SKS</span>
                    </span>
                    <span class="acc-badge">34</span>
                    <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
                </div>
                <div class="acc-body">
                    <div class="acc-inner">
                        <div class="sem-pills">
                            <span class="sem-pill active-all" data-sem="all">Semua</span>
                            <span class="sem-pill" data-sem="1">Sem 1 (5)</span>
                            <span class="sem-pill" data-sem="2">Sem 2 (4)</span>
                            <span class="sem-pill" data-sem="3">Sem 3 (4)</span>
                            <span class="sem-pill" data-sem="4">Sem 4 (5)</span>
                            <span class="sem-pill" data-sem="5">Sem 5 (6)</span>
                            <span class="sem-pill" data-sem="6">Sem 6 (4)</span>
                            <span class="sem-pill" data-sem="7">Sem 7 (5)</span>
                            <span class="sem-pill" data-sem="8">Sem 8 (1)</span>
                        </div>
                        <div class="border-t border-slate-100 bg-slate-50/60 p-4">
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <div class="tbl-wrap">
                                    <table class="tbl-modern">
                                        <thead><tr><th>Kode</th><th>Nama Matakuliah</th><th class="text-center">Sem</th><th class="text-center">SKS</th><th>Konsentrasi</th><th class="text-right">Aksi</th></tr></thead>
                                        <tbody>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 1 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ead60913 matematika teknik umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD60913</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Matematika Teknik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ead61014 fisika listrik* umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD61014</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Fisika Listrik*</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">4</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ead61112 tata tulis laporan dan karya ilmiah umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD61112</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Tata Tulis Laporan dan Karya Ilmiah</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">2</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ead61213 pengantar teknik elektro umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD61213</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pengantar Teknik Elektro</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ead61313 pemrograman komputer umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD61313</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pemrograman Komputer</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 2 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="ead61422 matematika diskrit umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD61422</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Matematika Diskrit</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">2</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="ead61523 rangkaian elektronika umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD61523</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Rangkaian Elektronika</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="ead61623 sistem digital umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD61623</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Digital</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="ead61724 praktikum rangkaian elektronika umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD61724</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Praktikum Rangkaian Elektronika</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">4</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 3 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="ead61833 sinyal dan sistem umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD61833</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sinyal dan Sistem</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="ead61933 pemrograman mikroprosesor umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD61933</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pemrograman Mikroprosesor</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="ead62033 teknik medan elektromagnetik umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD62033</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Teknik Medan Elektromagnetik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="ead62133 sistem kontrol umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD62133</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Kontrol</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 4 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="ead62243 peralatan elektronika umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD62243</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Peralatan Elektronika</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="ead62343 teknologi komponen umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD62343</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Teknologi Komponen</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="ead62443 pengolahan sinyal digital konversi energi">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD62443</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pengolahan Sinyal Digital</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Konversi Energi</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="ead62543 pengendali presisi konversi energi">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD62543</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pengendali Presisi</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Konversi Energi</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="ead62643 konverter daya konversi energi">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD62643</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Konverter Daya</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Konversi Energi</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 5 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="ead62753 manajemen proyek teknik konversi energi">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD62753</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Manajemen Proyek Teknik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Konversi Energi</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="ead62853 teknik tenaga listrik konversi energi">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD62853</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Teknik Tenaga Listrik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Konversi Energi</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="ead62953 kendali motor listrik konversi energi">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD62953</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Kendali Motor Listrik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Konversi Energi</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="ead63053 tata kelola ti informatika">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD63053</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Tata Kelola TI</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Informatika</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="ead63153 sistem informasi geografis informatika">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD63153</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Informasi Geografis</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Informatika</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="ead63253 basis data terdistribusi informatika">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD63253</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Basis Data Terdistribusi</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Informatika</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 6 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="6" data-cari="ead63363 sistem operasi informatika">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD63363</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Operasi</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-teal-100 text-teal-700 px-2 py-0.5 text-xs font-bold">Sem 6</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Informatika</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="6" data-cari="ead63463 grafika komputer informatika">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD63463</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Grafika Komputer</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-teal-100 text-teal-700 px-2 py-0.5 text-xs font-bold">Sem 6</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Informatika</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="6" data-cari="ead63563 jaringan komputer lanjut informatika">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD63563</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Jaringan Komputer Lanjut</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-teal-100 text-teal-700 px-2 py-0.5 text-xs font-bold">Sem 6</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Informatika</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="6" data-cari="ead63663 pembelajaran mesin informatika">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD63663</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pembelajaran Mesin</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-teal-100 text-teal-700 px-2 py-0.5 text-xs font-bold">Sem 6</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Informatika</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 7 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="ead63773 robotika robotika dan mekatronika">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD63773</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Robotika</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Robotika dan Mekatronika</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="ead63873 kendali robot robotika dan mekatronika">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD63873</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Kendali Robot</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Robotika dan Mekatronika</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="ead63973 pengolahan citra digital robotika dan mekatronika">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD63973</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pengolahan Citra Digital</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Robotika dan Mekatronika</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="ead64073 mekatronika robotika dan mekatronika">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAD64073</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Mekatronika</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Robotika dan Mekatronika</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="ung0511600874 kkn ">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">UNG0511600874</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">KKN</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">4</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 8 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="8" data-cari="eal605886 tugas akhir ">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EAL605886</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Tugas Akhir</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-pink-100 text-pink-700 px-2 py-0.5 text-xs font-bold">Sem 8</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">6</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                        <span class="block text-xs text-slate-500">Tahun 2017 &middot; 19 matakuliah &middot; 59 SKS</span>
                    </span>
                    <span class="acc-badge">19</span>
                    <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
                </div>
                <div class="acc-body">
                    <div class="acc-inner">
                        <div class="sem-pills">
                            <span class="sem-pill active-all" data-sem="all">Semua</span>
                            <span class="sem-pill" data-sem="1">Sem 1 (4)</span>
                            <span class="sem-pill" data-sem="2">Sem 2 (3)</span>
                            <span class="sem-pill" data-sem="3">Sem 3 (3)</span>
                            <span class="sem-pill" data-sem="4">Sem 4 (2)</span>
                            <span class="sem-pill" data-sem="5">Sem 5 (2)</span>
                            <span class="sem-pill" data-sem="6">Sem 6 (2)</span>
                            <span class="sem-pill" data-sem="7">Sem 7 (2)</span>
                            <span class="sem-pill" data-sem="8">Sem 8 (1)</span>
                        </div>
                        <div class="border-t border-slate-100 bg-slate-50/60 p-4">
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <div class="tbl-wrap">
                                    <table class="tbl-modern">
                                        <thead><tr><th>Kode</th><th>Nama Matakuliah</th><th class="text-center">Sem</th><th class="text-center">SKS</th><th>Konsentrasi</th><th class="text-right">Aksi</th></tr></thead>
                                        <tbody>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 1 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="eli101 matematika i umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI101</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Matematika I</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="eli102 fisika dasar i umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI102</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Fisika Dasar I</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="eli103 pengantar teknik elektro umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI103</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pengantar Teknik Elektro</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">2</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="eli104 bahasa inggris teknik umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI104</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Bahasa Inggris Teknik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">2</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 2 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="eli201 matematika ii umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI201</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Matematika II</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="eli202 rangkaian listrik i umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI202</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Rangkaian Listrik I</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="eli203 sistem digital umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI203</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Digital</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 3 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="eli301 elektronika analog umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI301</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Elektronika Analog</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="eli302 rangkaian listrik ii umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI302</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Rangkaian Listrik II</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="eli303 sistem mikroprosesor umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI303</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Mikroprosesor</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 4 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="eli401 medan elektromagnetik umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI401</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Medan Elektromagnetik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="eli402 sistem kontrol konversi energi">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI402</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Kontrol</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Konversi Energi</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 5 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="eli501 sistem tenaga listrik konversi energi">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI501</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Tenaga Listrik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Konversi Energi</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="eli502 konversi energi listrik konversi energi">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI502</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Konversi Energi Listrik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Konversi Energi</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 6 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="6" data-cari="eli601 elektronika daya konversi energi">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI601</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Elektronika Daya</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-teal-100 text-teal-700 px-2 py-0.5 text-xs font-bold">Sem 6</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Konversi Energi</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="6" data-cari="eli602 pengolahan sinyal digital umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI602</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pengolahan Sinyal Digital</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-teal-100 text-teal-700 px-2 py-0.5 text-xs font-bold">Sem 6</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 7 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="eli701 robotika robotika">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI701</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Robotika</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Robotika</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="ung0511600874 kkn ">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">UNG0511600874</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">KKN</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">4</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 8 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="8" data-cari="eli801 tugas akhir ">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ELI801</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Tugas Akhir</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-pink-100 text-pink-700 px-2 py-0.5 text-xs font-bold">Sem 8</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">6</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                        </tbody>
                                    </table>
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
            <div><h2 class="text-base font-bold text-slate-800">S1 Teknik Komputer</h2><p class="text-[11px] text-slate-500">2 kurikulum &middot; 45 matakuliah &middot; 143 SKS</p></div>
        </div>
        <div class="space-y-4">
            <div class="acc-item acc-tk is-active reveal" data-prodi="S1 Teknik Komputer" data-tahun="2021">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600 text-lg"><i class="fas fa-microchip"></i></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-bold text-slate-800">Kurikulum 2021
                            <span class="badge-aktif"><i class="fas fa-circle text-[5px]"></i>Aktif</span>
                        </span>
                        <span class="block text-xs text-slate-500">Tahun 2021 &middot; 27 matakuliah &middot; 85 SKS</span>
                    </span>
                    <span class="acc-badge">27</span>
                    <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
                </div>
                <div class="acc-body">
                    <div class="acc-inner">
                        <div class="sem-pills">
                            <span class="sem-pill active-all" data-sem="all">Semua</span>
                            <span class="sem-pill" data-sem="1">Sem 1 (5)</span>
                            <span class="sem-pill" data-sem="2">Sem 2 (4)</span>
                            <span class="sem-pill" data-sem="3">Sem 3 (4)</span>
                            <span class="sem-pill" data-sem="4">Sem 4 (4)</span>
                            <span class="sem-pill" data-sem="5">Sem 5 (3)</span>
                            <span class="sem-pill" data-sem="6">Sem 6 (3)</span>
                            <span class="sem-pill" data-sem="7">Sem 7 (3)</span>
                            <span class="sem-pill" data-sem="8">Sem 8 (1)</span>
                        </div>
                        <div class="border-t border-slate-100 bg-slate-50/60 p-4">
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <div class="tbl-wrap">
                                    <table class="tbl-modern">
                                        <thead><tr><th>Kode</th><th>Nama Matakuliah</th><th class="text-center">Sem</th><th class="text-center">SKS</th><th>Konsentrasi</th><th class="text-right">Aksi</th></tr></thead>
                                        <tbody>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 1 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ebd60913 aljabar linear dan matriks umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD60913</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Aljabar Linear dan Matriks</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ebd61014 fisika dasar umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD61014</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Fisika Dasar</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">4</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ebd61113 pengantar teknologi informasi umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD61113</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pengantar Teknologi Informasi</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ebd61213 kalkulus umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD61213</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Kalkulus</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ebd61312 bahasa inggris teknik umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD61312</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Bahasa Inggris Teknik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">2</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 2 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="ebd61423 statistika dan probabilitas umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD61423</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Statistika dan Probabilitas</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="ebd61523 rangkaian digital umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD61523</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Rangkaian Digital</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="ebd61623 pemrograman web umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD61623</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pemrograman Web</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="ebd61723 struktur data dan algoritma umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD61723</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Struktur Data dan Algoritma</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 3 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="ebd61833 arsitektur komputer umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD61833</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Arsitektur Komputer</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="ebd61933 basis data umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD61933</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Basis Data</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="ebd62033 jaringan komputer umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD62033</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Jaringan Komputer</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="ebd62133 sistem operasi umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD62133</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Operasi</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 4 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="ebd62243 pemrograman berorientasi objek rekayasa perangkat lunak">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD62243</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pemrograman Berorientasi Objek</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Perangkat Lunak</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="ebd62343 rekayasa perangkat lunak rekayasa perangkat lunak">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD62343</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Rekayasa Perangkat Lunak</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Perangkat Lunak</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="ebd62443 sistem terdistribusi rekayasa perangkat lunak">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD62443</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Terdistribusi</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Perangkat Lunak</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="ebd62543 web service dan api rekayasa perangkat lunak">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD62543</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Web Service dan API</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Perangkat Lunak</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 5 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="ebd62653 grafika komputer rekayasa perangkat lunak">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD62653</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Grafika Komputer</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Perangkat Lunak</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="ebd62753 pengujian perangkat lunak rekayasa perangkat lunak">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD62753</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pengujian Perangkat Lunak</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Perangkat Lunak</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="ebd62853 manajemen proyek ti rekayasa perangkat lunak">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD62853</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Manajemen Proyek TI</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Perangkat Lunak</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 6 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="6" data-cari="ebd62963 kecerdasan buatan jaringan dan keamanan">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD62963</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Kecerdasan Buatan</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-teal-100 text-teal-700 px-2 py-0.5 text-xs font-bold">Sem 6</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Jaringan dan Keamanan</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="6" data-cari="ebd63063 keamanan jaringan jaringan dan keamanan">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD63063</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Keamanan Jaringan</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-teal-100 text-teal-700 px-2 py-0.5 text-xs font-bold">Sem 6</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Jaringan dan Keamanan</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="6" data-cari="ebd63163 pemrograman mobile jaringan dan keamanan">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD63163</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pemrograman Mobile</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-teal-100 text-teal-700 px-2 py-0.5 text-xs font-bold">Sem 6</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Jaringan dan Keamanan</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 7 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="ebd63273 cloud computing jaringan dan keamanan">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD63273</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Cloud Computing</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Jaringan dan Keamanan</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="ebd63373 internet of things jaringan dan keamanan">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD63373</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Internet of Things</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Jaringan dan Keamanan</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="ung0511600874 kkn ">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">UNG0511600874</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">KKN</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">4</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 8 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="8" data-cari="ebd634886 tugas akhir ">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">EBD634886</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Tugas Akhir</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-pink-100 text-pink-700 px-2 py-0.5 text-xs font-bold">Sem 8</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">6</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                        <span class="block text-xs text-slate-500">Tahun 2017 &middot; 18 matakuliah &middot; 58 SKS</span>
                    </span>
                    <span class="acc-badge">18</span>
                    <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
                </div>
                <div class="acc-body">
                    <div class="acc-inner">
                        <div class="sem-pills">
                            <span class="sem-pill active-all" data-sem="all">Semua</span>
                            <span class="sem-pill" data-sem="1">Sem 1 (4)</span>
                            <span class="sem-pill" data-sem="2">Sem 2 (3)</span>
                            <span class="sem-pill" data-sem="3">Sem 3 (3)</span>
                            <span class="sem-pill" data-sem="4">Sem 4 (2)</span>
                            <span class="sem-pill" data-sem="5">Sem 5 (2)</span>
                            <span class="sem-pill" data-sem="6">Sem 6 (1)</span>
                            <span class="sem-pill" data-sem="7">Sem 7 (2)</span>
                            <span class="sem-pill" data-sem="8">Sem 8 (1)</span>
                        </div>
                        <div class="border-t border-slate-100 bg-slate-50/60 p-4">
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <div class="tbl-wrap">
                                    <table class="tbl-modern">
                                        <thead><tr><th>Kode</th><th>Nama Matakuliah</th><th class="text-center">Sem</th><th class="text-center">SKS</th><th>Konsentrasi</th><th class="text-right">Aksi</th></tr></thead>
                                        <tbody>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 1 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="kom101 algoritma dan pemrograman umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM101</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Algoritma dan Pemrograman</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="kom102 dasar sistem komputer umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM102</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Dasar Sistem Komputer</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="kom103 kalkulus i umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM103</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Kalkulus I</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="kom104 fisika dasar umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM104</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Fisika Dasar</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 2 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="kom201 struktur data umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM201</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Struktur Data</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="kom202 rangkaian digital umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM202</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Rangkaian Digital</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="kom203 pemrograman terstruktur umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM203</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pemrograman Terstruktur</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 3 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="kom301 basis data umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM301</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Basis Data</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="kom302 jaringan komputer umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM302</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Jaringan Komputer</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="kom303 sistem operasi umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM303</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Operasi</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 4 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="kom401 rekayasa perangkat lunak rekayasa perangkat lunak">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM401</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Rekayasa Perangkat Lunak</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Perangkat Lunak</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="kom402 pemrograman web lanjut rekayasa perangkat lunak">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM402</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pemrograman Web Lanjut</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Perangkat Lunak</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 5 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="kom501 sistem terdistribusi rekayasa perangkat lunak">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM501</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Terdistribusi</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Perangkat Lunak</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="kom502 grafika komputer rekayasa perangkat lunak">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM502</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Grafika Komputer</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Perangkat Lunak</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 6 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="6" data-cari="kom601 kecerdasan buatan umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM601</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Kecerdasan Buatan</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-teal-100 text-teal-700 px-2 py-0.5 text-xs font-bold">Sem 6</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 7 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="kom701 keamanan jaringan jaringan dan keamanan">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM701</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Keamanan Jaringan</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Jaringan dan Keamanan</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="ung0511600874 kkn ">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">UNG0511600874</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">KKN</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">4</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 8 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="8" data-cari="kom801 tugas akhir ">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">KOM801</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Tugas Akhir</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-pink-100 text-pink-700 px-2 py-0.5 text-xs font-bold">Sem 8</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">6</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                        </tbody>
                                    </table>
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
            <div><h2 class="text-base font-bold text-slate-800">S1 Pendidikan Vokasi Rekayasa Elektro</h2><p class="text-[11px] text-slate-500">2 kurikulum &middot; 37 matakuliah &middot; 116 SKS</p></div>
        </div>
        <div class="space-y-4">
            <div class="acc-item acc-vok is-active reveal" data-prodi="S1 Pendidikan Vokasi Rekayasa Elektro" data-tahun="2021">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600 text-lg"><i class="fas fa-graduation-cap"></i></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-bold text-slate-800">Kurikulum Vokasi 2021
                            <span class="badge-aktif"><i class="fas fa-circle text-[5px]"></i>Aktif</span>
                        </span>
                        <span class="block text-xs text-slate-500">Tahun 2021 &middot; 25 matakuliah &middot; 77 SKS</span>
                    </span>
                    <span class="acc-badge">25</span>
                    <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
                </div>
                <div class="acc-body">
                    <div class="acc-inner">
                        <div class="sem-pills">
                            <span class="sem-pill active-all" data-sem="all">Semua</span>
                            <span class="sem-pill" data-sem="1">Sem 1 (5)</span>
                            <span class="sem-pill" data-sem="2">Sem 2 (4)</span>
                            <span class="sem-pill" data-sem="3">Sem 3 (4)</span>
                            <span class="sem-pill" data-sem="4">Sem 4 (4)</span>
                            <span class="sem-pill" data-sem="5">Sem 5 (3)</span>
                            <span class="sem-pill" data-sem="6">Sem 6 (2)</span>
                            <span class="sem-pill" data-sem="7">Sem 7 (2)</span>
                            <span class="sem-pill" data-sem="8">Sem 8 (1)</span>
                        </div>
                        <div class="border-t border-slate-100 bg-slate-50/60 p-4">
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <div class="tbl-wrap">
                                    <table class="tbl-modern">
                                        <thead><tr><th>Kode</th><th>Nama Matakuliah</th><th class="text-center">Sem</th><th class="text-center">SKS</th><th>Konsentrasi</th><th class="text-right">Aksi</th></tr></thead>
                                        <tbody>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 1 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ecl60812 pengantar vokasi teknik elektro umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL60812</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pengantar Vokasi Teknik Elektro</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">2</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ecl60913 dasar elektro umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL60913</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Dasar Elektro</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ecl61013 kalkulus umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL61013</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Kalkulus</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ecl61112 bahasa inggris teknik umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL61112</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Bahasa Inggris Teknik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">2</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="ecl61213 pengantar rancang bangun umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL61213</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pengantar Rancang Bangun</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 2 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="ecl61323 rangkaian listrik umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL61323</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Rangkaian Listrik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="ecl61423 pengukuran elektronika umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL61423</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pengukuran Elektronika</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="ecl61523 sistem mikroprosesor umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL61523</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Mikroprosesor</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="ecl61623 praktikum rangkaian umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL61623</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Praktikum Rangkaian</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 3 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="ecl61733 pendidikan dan pelatihan umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL61733</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pendidikan dan Pelatihan</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="ecl61833 metodologi penelitian umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL61833</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Metodologi Penelitian</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="ecl61933 sistem kelistrikan umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL61933</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Kelistrikan</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="ecl62033 elektronika daya umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL62033</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Elektronika Daya</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 4 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="ecl62143 pendidikan kejuruan pendidikan">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL62143</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pendidikan Kejuruan</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Pendidikan</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="ecl62243 pengembangan kurikulum pendidikan">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL62243</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pengembangan Kurikulum</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Pendidikan</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="ecl62343 media pembelajaran pendidikan">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL62343</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Media Pembelajaran</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Pendidikan</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="ecl62443 teknologi plc pendidikan">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL62443</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Teknologi PLC</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Pendidikan</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 5 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="ecl62553 kerja praktik rekayasa elektro">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL62553</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Kerja Praktik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Elektro</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="ecl62653 kendali otomatis rekayasa elektro">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL62653</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Kendali Otomatis</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Elektro</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="ecl62753 instalasi penerangan rekayasa elektro">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL62753</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Instalasi Penerangan</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Elektro</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 6 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="6" data-cari="ecl62863 teknik inverter rekayasa elektro">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL62863</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Teknik Inverter</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-teal-100 text-teal-700 px-2 py-0.5 text-xs font-bold">Sem 6</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Elektro</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="6" data-cari="ecl62963 bengkel elektronika rekayasa elektro">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL62963</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Bengkel Elektronika</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-teal-100 text-teal-700 px-2 py-0.5 text-xs font-bold">Sem 6</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Elektro</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 7 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="ecl63073 program studi rekayasa elektro">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL63073</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Program Studi</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Elektro</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="ung0511600874 kkn ">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">UNG0511600874</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">KKN</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">4</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 8 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="8" data-cari="ecl631886 tugas akhir ">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">ECL631886</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Tugas Akhir</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-pink-100 text-pink-700 px-2 py-0.5 text-xs font-bold">Sem 8</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">6</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                        <span class="block text-xs text-slate-500">Tahun 2018 &middot; 12 matakuliah &middot; 39 SKS</span>
                    </span>
                    <span class="acc-badge">12</span>
                    <span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span>
                </div>
                <div class="acc-body">
                    <div class="acc-inner">
                        <div class="sem-pills">
                            <span class="sem-pill active-all" data-sem="all">Semua</span>
                            <span class="sem-pill" data-sem="1">Sem 1 (3)</span>
                            <span class="sem-pill" data-sem="2">Sem 2 (2)</span>
                            <span class="sem-pill" data-sem="3">Sem 3 (2)</span>
                            <span class="sem-pill" data-sem="4">Sem 4 (1)</span>
                            <span class="sem-pill" data-sem="5">Sem 5 (1)</span>
                            <span class="sem-pill" data-sem="6">Sem 6 (1)</span>
                            <span class="sem-pill" data-sem="7">Sem 7 (1)</span>
                            <span class="sem-pill" data-sem="8">Sem 8 (1)</span>
                        </div>
                        <div class="border-t border-slate-100 bg-slate-50/60 p-4">
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <div class="tbl-wrap">
                                    <table class="tbl-modern">
                                        <thead><tr><th>Kode</th><th>Nama Matakuliah</th><th class="text-center">Sem</th><th class="text-center">SKS</th><th>Konsentrasi</th><th class="text-right">Aksi</th></tr></thead>
                                        <tbody>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 1 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="vok101 pengantar vokasi umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">VOK101</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pengantar Vokasi</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">2</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="vok102 dasar-dasar elektro umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">VOK102</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Dasar-Dasar Elektro</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="1" data-cari="vok103 matematika dasar umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">VOK103</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Matematika Dasar</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-bold">Sem 1</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 2 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="vok201 rangkaian listrik umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">VOK201</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Rangkaian Listrik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="2" data-cari="vok202 pengukuran elektronika umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">VOK202</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pengukuran Elektronika</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2 py-0.5 text-xs font-bold">Sem 2</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 3 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="vok301 elektronika daya umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">VOK301</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Elektronika Daya</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="3" data-cari="vok302 sistem kelistrikan umum">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">VOK302</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Sistem Kelistrikan</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2 py-0.5 text-xs font-bold">Sem 3</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-[11px] font-bold">Umum</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 4 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="4" data-cari="vok401 pendidikan kejuruan pendidikan">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">VOK401</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Pendidikan Kejuruan</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-bold">Sem 4</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Pendidikan</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 5 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="5" data-cari="vok501 kerja praktik rekayasa elektro">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">VOK501</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Kerja Praktik</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-rose-100 text-rose-700 px-2 py-0.5 text-xs font-bold">Sem 5</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Elektro</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 6 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="6" data-cari="vok601 kendali otomatis rekayasa elektro">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">VOK601</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Kendali Otomatis</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-teal-100 text-teal-700 px-2 py-0.5 text-xs font-bold">Sem 6</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">3</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">Rekayasa Elektro</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 7 (Ganjil)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="7" data-cari="ung0511600874 kkn ">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">UNG0511600874</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">KKN</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs font-bold">Sem 7</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">4</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                            <tr class="sem-row"><td colspan="6"><i class="fas fa-layer-group mr-1.5 text-[10px]"></i>Semester 8 (Genap)</td></tr>
                                            <tr class="bg-white transition hover:bg-orange-50" data-sem="8" data-cari="vok801 tugas akhir ">
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">VOK801</span></td>
                                                <td><p class="font-medium text-slate-800 leading-snug">Tugas Akhir</p></td>
                                                <td class="text-center"><span class="inline-flex items-center rounded-full bg-pink-100 text-pink-700 px-2 py-0.5 text-xs font-bold">Sem 8</span></td>
                                                <td class="text-center"><span class="text-sm font-bold text-slate-700">6</span></td>
                                                <td><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span></td>
                                                <td class="text-right"><div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                                </div></td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                        <option>S1 Teknik Elektro</option>
                        <option>S1 Teknik Komputer</option>
                        <option>S1 Pendidikan Vokasi Rekayasa Elektro</option>
                    </select></div>
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kurikulum</label>
                    <select id="tpTahun" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option value="2022">Kurikulum 2022</option>
                        <option value="2021">Kurikulum 2021</option>
                        <option value="2018">Kurikulum 2018</option>
                        <option value="2017">Kurikulum 2017</option>
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
<!-- ===== Modal Detail ===== -->
<div class="modal-overlay" id="detailModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-eye mr-1 text-slate-500"></i>Detail Matakuliah</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
        </div>
        <div class="p-5 space-y-3">
            <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-sm font-bold text-slate-600"><i class="fas fa-hashtag"></i></span><div class="min-w-0"><p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Kode</p><p class="text-sm font-bold text-slate-800 font-mono" id="dtKode">&mdash;</p></div></div>
            <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-sm font-bold text-orange-600"><i class="fas fa-book"></i></span><div class="min-w-0"><p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Nama Matakuliah</p><p class="text-sm font-bold text-slate-800" id="dtNama">&mdash;</p></div></div>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-sm font-bold text-slate-600"><i class="fas fa-calendar"></i></span><div class="min-w-0"><p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Semester</p><p class="text-sm font-bold text-slate-800" id="dtSem">&mdash;</p></div></div>
                <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-sm font-bold text-slate-600"><i class="fas fa-calculator"></i></span><div class="min-w-0"><p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">SKS</p><p class="text-sm font-bold text-slate-800" id="dtSKS">&mdash;</p></div></div>
            </div>
            <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sm font-bold text-sky-600"><i class="fas fa-layer-group"></i></span><div class="min-w-0"><p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Konsentrasi</p><p class="text-sm font-bold text-slate-800" id="dtKons">&mdash;</p></div></div>
        </div>
        <div class="border-t border-slate-100 px-5 py-3 flex justify-end">
            <button type="button" data-modal-close class="px-4 py-2 text-xs rounded-lg bg-slate-900 hover:bg-slate-700 text-white font-medium transition">Tutup</button>
        </div>
    </div>
</div>
<script>
(function () {
    var fCari = document.getElementById('fCari');
    var fProdi = document.getElementById('fProdi');
    var fTahun = document.getElementById('fTahun');

    window.toggleAcc = function (head) { head.closest('.acc-item').classList.toggle('open'); };

    /* Donut chart — Chart.js dimuat setelah include halaman ini, jadi tunggu */
    var chartData = {
        labels: ["S1 Teknik Elektro","S1 Teknik Komputer","S1 Pendidikan Vokasi Rekayasa Elektro"],
        values: [34,27,25],
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

    /* Filter global: cari + prodi + kurikulum(tahun) — hormati pill semester aktif */
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
                if (kata) detail.textContent = 'Matakuliah dengan kata "' + kata + '" tidak ditemukan.';
                else if (prodi && tahun) detail.textContent = 'Belum ada kurikulum ' + tahun + ' di ' + prodi + '.';
                else if (prodi) detail.textContent = 'Belum ada data untuk ' + prodi + '.';
                else if (tahun) detail.textContent = 'Belum ada kurikulum tahun ' + tahun + '.';
                else if (document.querySelector('.sem-pill.active')) detail.textContent = 'Tidak ada matakuliah pada semester yang dipilih.';
                else detail.textContent = 'Tidak ada data yang cocok dengan filter yang dipilih.';
            }
        }
    }

    if (fCari) fCari.addEventListener('input', filterAll);
    if (fProdi) fProdi.addEventListener('change', filterAll);
    if (fTahun) fTahun.addEventListener('change', filterAll);
    var btnReset = document.getElementById('btnReset');
    var btnEmptyReset = document.getElementById('btnEmptyReset');
    function resetFilters() {
        if (fCari) fCari.value = '';
        if (fProdi) fProdi.value = '';
        if (fTahun) fTahun.value = '';
        document.querySelectorAll('.sem-pill').forEach(function (p) { p.classList.remove('active', 'active-all'); });
        document.querySelectorAll('.sem-pill[data-sem="all"]').forEach(function (p) { p.classList.add('active-all'); });
        document.querySelectorAll('.acc-item tbody tr').forEach(function (tr) { tr.style.display = ''; });
        filterAll();
        document.querySelectorAll('.acc-item.open').forEach(function (a) { a.classList.remove('open'); });
    }
    if (btnReset) btnReset.addEventListener('click', resetFilters);
    if (btnEmptyReset) btnEmptyReset.addEventListener('click', resetFilters);

    function toast(msg) {
        var t = document.createElement('div');
        t.textContent = msg;
        t.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;background:#0f172a;color:#fff;padding:.6rem 1rem;border-radius:.6rem;font-size:.8rem;box-shadow:0 6px 18px rgba(15,23,42,.35);transition:opacity .3s ease;';
        document.body.appendChild(t);
        setTimeout(function () { t.style.opacity = '0'; setTimeout(function () { t.remove(); }, 300); }, 2200);
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-circle');
        if (!btn) return;
        var tr = btn.closest('tr');
        if (btn.classList.contains('btn-detail')) {
            openDetail(tr);
        } else if (btn.classList.contains('btn-edit')) {
            toast('Mode edit - fitur segera hadir');
        } else if (btn.classList.contains('btn-hapus')) {
            if (confirm('Hapus matakuliah ini?')) { tr.remove(); toast('Matakuliah dihapus'); }
        }
    });

    function openDetail(tr) {
        var tds = tr.querySelectorAll('td');
        document.getElementById('dtKode').textContent = tds[0] ? tds[0].textContent.trim() : '---';
        document.getElementById('dtNama').textContent = tds[1] ? tds[1].textContent.trim() : '---';
        document.getElementById('dtSem').textContent = tds[2] ? tds[2].textContent.trim() : '---';
        document.getElementById('dtSKS').textContent = tds[3] ? tds[3].textContent.trim() : '---';
        document.getElementById('dtKons').textContent = tds[4] ? tds[4].textContent.trim() : '---';
        document.getElementById('detailModal').classList.add('show');
    }

    var dm = document.getElementById('detailModal');
    dm.querySelectorAll('[data-modal-close]').forEach(function (b) {
        b.addEventListener('click', function () { dm.classList.remove('show'); });
    });
    dm.addEventListener('click', function (e) { if (e.target === dm) dm.classList.remove('show'); });

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
        var it = document.querySelector('.acc-item[data-prodi="' + prodi + '"][data-tahun="' + tahun + '"]');
        if (!it) { toast('Accordion kurikulum tidak ditemukan'); return; }
        if (!it.classList.contains('open')) it.classList.add('open');
        it.querySelectorAll('.sem-pill').forEach(function (p) { p.classList.remove('active', 'active-all'); });
        var pill = it.querySelector('.sem-pill[data-sem="' + sem + '"]');
        if (pill) pill.classList.add('active');
        var tb = it.querySelector('tbody');
        var semRow = null;
        tb.querySelectorAll('tr').forEach(function (tr) {
            if (tr.classList.contains('sem-row') && tr.textContent.indexOf('Semester ' + sem) !== -1) { semRow = tr; }
        });
        var cari = (kode + ' ' + nama + ' ' + kons).toLowerCase();
        var konsBadge = kons ? '<span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">' + kons + '</span>'
                             : '<span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span>';
        var semColor = ['bg-emerald-100 text-emerald-700','bg-sky-100 text-sky-700','bg-violet-100 text-violet-700','bg-amber-100 text-amber-700','bg-rose-100 text-rose-700','bg-teal-100 text-teal-700','bg-indigo-100 text-indigo-700','bg-pink-100 text-pink-700'];
        var tr = document.createElement('tr');
        tr.className = 'bg-white transition hover:bg-orange-50';
        tr.setAttribute('data-sem', sem);
        tr.setAttribute('data-cari', cari);
        tr.innerHTML = '<td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">' + kode + '</span></td>'
            + '<td><p class="font-medium text-slate-800 leading-snug">' + nama + '</p></td>'
            + '<td class="text-center"><span class="inline-flex items-center rounded-full ' + semColor[(parseInt(sem) - 1) % 8] + ' px-2 py-0.5 text-xs font-bold">Sem ' + sem + '</span></td>'
            + '<td class="text-center"><span class="text-sm font-bold text-slate-700">' + sks + '</span></td>'
            + '<td>' + konsBadge + '</td>'
            + '<td class="text-right"><div class="flex items-center justify-end gap-1.5">'
            + '<button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>'
            + '<button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>'
            + '<button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>'
            + '</div></td>';
        if (semRow && semRow.nextSibling) semRow.parentNode.insertBefore(tr, semRow.nextSibling);
        else tb.appendChild(tr);
        var uploadInfo = tpFile && tpFile.files.length ? ' dan RPS "' + tpFile.files[0].name + '" ditautkan' : '';
        toast('Matakuliah "' + nama + '" ditambahkan' + uploadInfo);
        tm.classList.remove('show');
        frmTambah.reset();
        document.getElementById('tpFileLabel').textContent = 'Klik untuk memilih file RPS';
    });
})();</script>
