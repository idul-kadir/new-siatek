<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    .tab-btn { display: inline-flex; align-items: center; gap: .5rem; border-radius: .6rem; border: 1px solid #e2e8f0;
        background: #fff; color: #475569; padding: .55rem .95rem; font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .tab-btn:hover { border-color: #93c5fd; color: #2563eb; }
    .tab-btn .tnum { min-width: 22px; text-align: center; border-radius: 9999px; padding: 1px 6px; font-size: .7rem; background: #f1f5f9; color: #64748b; transition: all .15s ease; }
    .tab-btn .tnum.hit { background: #dcfce7 !important; color: #16a34a !important; font-weight: 700; }
    .tab-btn[aria-selected="true"] { background: #2563eb; border-color: #2563eb; color: #fff; }
    .tab-btn[aria-selected="true"] .tnum { background: rgba(255,255,255,.2); color: #fff; }
    .tab-btn[aria-selected="true"] .tnum.hit { background: #dcfce7 !important; color: #16a34a !important; }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    .tile-orange  { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }
    .tile-sky     { background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); }
    .tile-emerald { background: linear-gradient(135deg, #047857 0%, #10b981 100%); }
    .tile-violet  { background: linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%); }
    .tile-corak { position: relative; overflow: hidden; }
    .tile-corak::before { content: ""; position: absolute; inset: 0; pointer-events: none;
        background-image: radial-gradient(rgba(255,255,255,.22) 1px, transparent 1px);
        background-size: 12px 12px; opacity: .35; mix-blend-mode: overlay; }
    .tile-corak > * { position: relative; }
    .mk-card { transition: opacity .2s ease; }
    .mk-card.hide { display: none; }

    .modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,.5); display: flex; align-items: center; justify-content: center;
        z-index: 1050; padding: 16px; visibility: hidden; opacity: 0; transition: opacity .2s ease, visibility .2s ease; }
    .modal-overlay.show { visibility: visible; opacity: 1; }

    .form-field label { display: block; margin-bottom: .3rem; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .03em; }
    .form-field input, .form-field select { width: 100%; border: 1px solid #e2e8f0; background: #f8fafc; border-radius: .5rem; padding: .55rem .7rem; font-size: .85rem; color: #1e293b; outline: none; transition: all .15s ease; }
    .info-field label { display: block; margin-bottom: .2rem; font-size: 10px; font-weight: 600; color: #3b82f6; text-transform: uppercase; letter-spacing: .04em; }
    .info-field p { margin: 0; font-size: .85rem; font-weight: 600; color: #1e293b; }
    .info-field p small { font-weight: 400; color: #64748b; }
    .form-hint { font-size: 11px; color: #94a3b8; font-style: italic; margin: -0.45rem 0 0; }
    .form-field input:focus, .form-field select:focus { border-color: #60a5fa; background: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }

    .card-flash { animation: cardFlash 1.2s ease; }
    @keyframes cardFlash { 0% { box-shadow: 0 0 0 3px rgba(16,185,129,.55); } 100% { box-shadow: none; } }
</style>
<main class="content-area content-scroll">

    <!-- ================= VIEW JADWAL ================= -->
    <div id="view-jadwal">

    <!-- Page Header -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Jadwal Kuliah</h1>
                    <p class="text-xs text-slate-500">Jadwal per hari per ruangan — mata kuliah, jam, dan pengajar.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="btnRuangan" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-b from-blue-600 to-blue-700 px-3.5 py-2 text-xs font-bold text-white shadow-lg shadow-blue-600/30 ring-1 ring-blue-800/50 transition hover:from-blue-500 hover:to-blue-600 hover:shadow-blue-500/40 active:scale-95"><span class="flex h-6 w-6 items-center justify-center rounded-md bg-white/20 text-[11px]"><i class="fas fa-door-open"></i></span> Kelola Ruangan<i class="fas fa-arrow-right ml-0.5 text-[9px] text-blue-200 transition group-hover:translate-x-0.5"></i></button>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-900 px-3 py-1.5 text-xs font-bold text-white">
                    <i class="fas fa-calendar-days"></i> 2026/2027 - GANJIL
                </span>
            </div>
        </div>
    </section>

    <!-- Statistik ringkas -->
    <section class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="tile-corak tile-orange rounded-xl p-4 text-white shadow-md">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Total Sesi</p>
                    <p class="mt-1 text-2xl font-bold tracking-tight">192</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-calendar-check"></i></span>
            </div>
        </div>
        <div class="tile-corak tile-emerald rounded-xl p-4 text-white shadow-md">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Laboratorium</p>
                    <p class="mt-1 text-2xl font-bold tracking-tight" id="statLabJadwal">9</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-door-open"></i></span>
            </div>
        </div>
        <div class="tile-corak tile-sky rounded-xl p-4 text-white shadow-md">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Mata Kuliah</p>
                    <p class="mt-1 text-2xl font-bold tracking-tight">90</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-book-open"></i></span>
            </div>
        </div>
        <div class="tile-corak tile-violet rounded-xl p-4 text-white shadow-md">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Belum Terjadwal</p>
                    <p class="mt-1 text-2xl font-bold tracking-tight">192</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-hourglass-half"></i></span>
            </div>
        </div>
    </section>

    <!-- Navigasi Hari -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" class="tab-btn" data-tab="SENIN"><i class="fas fa-calendar-day text-[11px]"></i>Senin<span class="tnum">0</span></button>
            <button type="button" class="tab-btn" data-tab="SELASA"><i class="fas fa-calendar-day text-[11px]"></i>Selasa<span class="tnum">0</span></button>
            <button type="button" class="tab-btn" data-tab="RABU"><i class="fas fa-calendar-day text-[11px]"></i>Rabu<span class="tnum">0</span></button>
            <button type="button" class="tab-btn" data-tab="KAMIS"><i class="fas fa-calendar-day text-[11px]"></i>Kamis<span class="tnum">0</span></button>
            <button type="button" class="tab-btn" data-tab="JUMAT"><i class="fas fa-calendar-day text-[11px]"></i>Jumat<span class="tnum">0</span></button>
            <button type="button" class="tab-btn" data-tab="SABTU"><i class="fas fa-calendar-day text-[11px]"></i>Sabtu<span class="tnum">0</span></button>
            <button type="button" class="tab-btn" data-tab="UNDEFINED"><i class="fas fa-question text-[11px]"></i>Undefined<span class="tnum">192</span></button>
            <button type="button" id="btnTambah" class="inline-flex items-center gap-1.5 rounded-lg border border-blue-600 bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"><i class="fas fa-plus"></i> Tambah Jadwal</button>
            <button type="button" id="btnReset" class="ml-auto inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"><i class="fas fa-rotate"></i> Reset</button>
        </div>
    </section>

    <!-- Toolbar cari + filter -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[180px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari mata kuliah, dosen, kode, kelas…"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-blue-400 focus:bg-white">
            </div>
            <select id="fProdi" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-blue-400">
                <option value="">Semua Prodi</option>
                <option value="Teknik Elektro">Teknik Elektro</option>
                <option value="Teknik Komputer">Teknik Komputer</option>
                <option value="Pendidikan Vokasional Rekayasa Elektro">Pendidikan Vokasional Rekayasa Elektro</option>
            </select>
            <select id="fDosen" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-blue-400">
                <option value="">Semua Dosen</option>
                <option value="Taufiq Ismail Yusuf, ST., M.Si.">Taufiq Ismail Yusuf, ST., M.Si.</option>
                <option value="Ervan Hasan Harun, ST., MT.">Ervan Hasan Harun, ST., MT.</option>
                <option value="Salmawaty Tansa, ST., M.Eng.">Salmawaty Tansa, ST., M.Eng.</option>
                <option value="Ade Irawaty Tolago, ST., MT.">Ade Irawaty Tolago, ST., MT.</option>
                <option value="Ifan Wiranto, ST., MT.">Ifan Wiranto, ST., MT.</option>
                <option value="Afifah Farhanah Akadji, S.Si., M.Si.">Afifah Farhanah Akadji, S.Si., M.Si.</option>
                <option value="Andi Sitti Dwi Auliyani, S.Pd., M.Pd">Andi Sitti Dwi Auliyani, S.Pd., M.Pd</option>
                <option value="Yasin Mohamad, ST., MT.">Yasin Mohamad, ST., MT.</option>
                <option value="Dr. Mohamad Syafri Tuloli, ST, MT">Dr. Mohamad Syafri Tuloli, ST, MT</option>
                <option value="Zainudin Bonok, ST.,MT.">Zainudin Bonok, ST.,MT.</option>
                <option value="Amirudin Yunus Dako, ST., M.Eng.">Amirudin Yunus Dako, ST., M.Eng.</option>
                <option value="Ulfatun Nadifa, S.Pd, M.Kom.">Ulfatun Nadifa, S.Pd, M.Kom.</option>
                <option value="Iskandar Z. Nasibu, S.Pd, M.Eng.">Iskandar Z. Nasibu, S.Pd, M.Eng.</option>
                <option value="Wildan, S.Pd. M.Pd">Wildan, S.Pd. M.Pd</option>
                <option value="Dr. Bambang Panji Asmara, ST., MT.">Dr. Bambang Panji Asmara, ST., MT.</option>
                <option value="Rahmat D.R. Dako, ST., M.Eng.">Rahmat D.R. Dako, ST., M.Eng.</option>
                <option value="Abdul Gani F. Lihawa, S.Kom., M.Kom">Abdul Gani F. Lihawa, S.Kom., M.Kom</option>
                <option value="Syahrir Abdussamad, ST., MT.">Syahrir Abdussamad, ST., MT.</option>
                <option value="Ikhsan Hidayat, S.Kom., MT.">Ikhsan Hidayat, S.Kom., MT.</option>
                <option value="Hendy Prasetyo, S.S.T., M.T.">Hendy Prasetyo, S.S.T., M.T.</option>
                <option value="Jumiati Ilham, ST., MT.">Jumiati Ilham, ST., MT.</option>
                <option value="Nurul, S.Pd., M.Pd">Nurul, S.Pd., M.Pd</option>
                <option value="Dr. Lanto M. Kamil Amali, ST., MT.">Dr. Lanto M. Kamil Amali, ST., MT.</option>
                <option value="Dr.Ir. Arifin Matoka, MT.">Dr.Ir. Arifin Matoka, MT.</option>
                <option value="Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.">Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.</option>
                <option value="Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D">Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D</option>
                <option value="Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.">Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.</option>
            </select>
            <button type="button" id="btnReset2" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
            <span class="hidden text-xs text-slate-400 sm:inline" id="lblHasil"></span>
        </div>
    </section>

<!-- PANEL SENIN -->
<section class="tab-panel" id="panel-SENIN" data-tabbody>
  <div class="mb-3 flex items-center justify-between"><h2 class="text-base font-semibold text-slate-800">Senin <span class="text-sm font-normal text-slate-400">· 9 ruangan</span></h2></div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">LAB. UNPROTECT</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. UNPROTECT">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601013 kalkulus 1 b ifan wiranto, st., mt. afifah farhanah akadji, s.si., m.si. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="ifan wiranto, st., mt.|afifah farhanah akadji, s.si., m.si.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Kalkulus 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601013</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Afifah Farhanah Akadji, S.Si., M.Si.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602332 tata tulis laporan dan karya ilmiah a amirudin yunus dako, st., m.eng. syahrir abdussamad, st., mt. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="amirudin yunus dako, st., m.eng.|syahrir abdussamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Tata Tulis Laporan dan Karya Ilmiah</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602332</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601513 probabilitas dan statistika c nurul, s.pd., m.pd ade irawaty tolago, st., mt. teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="nurul, s.pd., m.pd|ade irawaty tolago, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Probabilitas dan Statistika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601513</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Nurul, S.Pd., M.Pd</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601413 teknologi informasi e zainudin bonok, st.,mt. dr. mohamad syafri tuloli, st, mt teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="zainudin bonok, st.,mt.|dr. mohamad syafri tuloli, st, mt">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Teknologi Informasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601413</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">E</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Zainudin Bonok, ST.,MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Mohamad Syafri Tuloli, ST, MT</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">R.K 2.11</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="R.K 2.11">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal600913 fisika 1 a ade irawaty tolago, st., mt. taufiq ismail yusuf, st., m.si. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="ade irawaty tolago, st., mt.|taufiq ismail yusuf, st., m.si.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Fisika 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL600913</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Taufiq Ismail Yusuf, ST., M.Si.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602232 rangkaian listrik 2 b yasin mohamad, st., mt. prof. dr. ir. sardi salim, m. pd.,ipu., asean eng. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="yasin mohamad, st., mt.|prof. dr. ir. sardi salim, m. pd.,ipu., asean eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Rangkaian Listrik 2</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602232</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604452 sistem operasi c ikhsan hidayat, s.kom., mt. dr. bambang panji asmara, st., mt. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="ikhsan hidayat, s.kom., mt.|dr. bambang panji asmara, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Sistem Operasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604452</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 14:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ikhsan Hidayat, S.Kom., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal600913 fisika 1 b taufiq ismail yusuf, st., m.si. ade irawaty tolago, st., mt. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="taufiq ismail yusuf, st., m.si.|ade irawaty tolago, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Fisika 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL600913</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Taufiq Ismail Yusuf, ST., M.Si.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">R.K 3.16</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="R.K 3.16">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602732 desain digital &amp; logika b salmawaty tansa, st., m.eng. dr. bambang panji asmara, st., mt. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="salmawaty tansa, st., m.eng.|dr. bambang panji asmara, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Desain Digital &amp; Logika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602732</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 09:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="nas0503600412 bahasa indonesia c dosen luar dosen luar teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="dosen luar|dosen luar">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Bahasa Indonesia</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">NAS0503600412</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-[10px] font-bold">Belum Ditentukan</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601413 kimia  a ulfatun nadifa, s.pd, m.kom. jumiati ilham, st., mt. teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="ulfatun nadifa, s.pd, m.kom.|jumiati ilham, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Kimia </h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601413</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Jumiati Ilham, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601413 kimia  c jumiati ilham, st., mt. ulfatun nadifa, s.pd, m.kom. teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="jumiati ilham, st., mt.|ulfatun nadifa, s.pd, m.kom.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Kimia </h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601413</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Jumiati Ilham, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#0369a1 0%,#0ea5e9 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f0f9ff 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#0369a1"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#0369a1">LAB. KOMPUTER 2</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 20 · 3 sesi · Komputer</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. KOMPUTER 2">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601913 pemrograman dasar c dr. mohamad syafri tuloli, st, mt abdul gani f. lihawa, s.kom., m.kom teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="dr. mohamad syafri tuloli, st, mt|abdul gani f. lihawa, s.kom., m.kom">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pemrograman Dasar</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601913</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Mohamad Syafri Tuloli, ST, MT</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal603033 pemrograman client/server d abdul gani f. lihawa, s.kom., m.kom rahmat d.r. dako, st., m.eng. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="abdul gani f. lihawa, s.kom., m.kom|rahmat d.r. dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pemrograman Client/Server</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL603033</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">D</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604652 desain rekayasa komputasi 1 a abdul gani f. lihawa, s.kom., m.kom rahmat d.r. dako, st., m.eng. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="abdul gani f. lihawa, s.kom., m.kom|rahmat d.r. dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Desain Rekayasa Komputasi 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604652</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 17:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#0369a1 0%,#0ea5e9 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f0f9ff 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#0369a1"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#0369a1">LAB. KOMPUTER 1</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 20 · 3 sesi · Komputer</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. KOMPUTER 1">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601113 dasar-dasar pemrograman 1 e ulfatun nadifa, s.pd, m.kom. amirudin yunus dako, st., m.eng. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="ulfatun nadifa, s.pd, m.kom.|amirudin yunus dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Dasar-Dasar Pemrograman 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601113</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">E</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604652 desain rekayasa komputasi 1 b rahmat d.r. dako, st., m.eng. abdul gani f. lihawa, s.kom., m.kom teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="rahmat d.r. dako, st., m.eng.|abdul gani f. lihawa, s.kom., m.kom">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Desain Rekayasa Komputasi 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604652</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604353 jaringan komputasi c ikhsan hidayat, s.kom., mt. dr. bambang panji asmara, st., mt. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="ikhsan hidayat, s.kom., mt.|dr. bambang panji asmara, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Jaringan Komputasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604353</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">14:40 - 17:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ikhsan Hidayat, S.Kom., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#b45309 0%,#f59e0b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#fffbeb 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#b45309"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#b45309">LAB. TENAGA LISTRIK</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 5 sesi · Tenaga Listrik</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. TENAGA LISTRIK">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602232 rangkaian listrik 2 a prof. dr. ir. sardi salim, m. pd.,ipu., asean eng. yasin mohamad, st., mt. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="prof. dr. ir. sardi salim, m. pd.,ipu., asean eng.|yasin mohamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Rangkaian Listrik 2</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602232</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 09:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604152 teknik instalasi  a dr. lanto m. kamil amali, st., mt. wildan, s.pd. m.pd teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="dr. lanto m. kamil amali, st., mt.|wildan, s.pd. m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Teknik Instalasi </h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604152</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">09:40 - 11:20</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Wildan, S.Pd. M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604152 teknik instalasi a dr. lanto m. kamil amali, st., mt. wildan, s.pd. m.pd teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="dr. lanto m. kamil amali, st., mt.|wildan, s.pd. m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Teknik Instalasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604152</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">09:40 - 11:20</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Wildan, S.Pd. M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602232 rangkaian listrik 2 c prof. dr. ir. sardi salim, m. pd.,ipu., asean eng. yasin mohamad, st., mt. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="prof. dr. ir. sardi salim, m. pd.,ipu., asean eng.|yasin mohamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Rangkaian Listrik 2</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602232</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 14:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead608373 dinamika dan stabilitas sistem tenaga listrik a dr.ir. arifin matoka, mt. yasin mohamad, st., mt. teknik elektro 7" data-prodi="Teknik Elektro" data-dosen="dr.ir. arifin matoka, mt.|yasin mohamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Dinamika dan Stabilitas Sistem Tenaga Listrik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD608373</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr.Ir. Arifin Matoka, MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#6d28d9 0%,#a78bfa 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f5f3ff 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#6d28d9"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#6d28d9">LAB. TEKNIK KENDALI</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Kendali</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. TEKNIK KENDALI">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead608773 sistem komunikasi bergerak  a zainudin bonok, st.,mt. syahrir abdussamad, st., mt. teknik elektro 7" data-prodi="Teknik Elektro" data-dosen="zainudin bonok, st.,mt.|syahrir abdussamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Sistem Komunikasi Bergerak </h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD608773</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Zainudin Bonok, ST.,MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602732 desain digital &amp; logika c salmawaty tansa, st., m.eng. dr. bambang panji asmara, st., mt. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="salmawaty tansa, st., m.eng.|dr. bambang panji asmara, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Desain Digital &amp; Logika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602732</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead608573 robotika a hendy prasetyo, s.s.t., m.t. iskandar z. nasibu, s.pd, m.eng. teknik elektro 7" data-prodi="Teknik Elektro" data-dosen="hendy prasetyo, s.s.t., m.t.|iskandar z. nasibu, s.pd, m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Robotika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD608573</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Hendy Prasetyo, S.S.T., M.T.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead603933 metode numerik b syahrir abdussamad, st., mt. ervan hasan harun, st., mt. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="syahrir abdussamad, st., mt.|ervan hasan harun, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Metode Numerik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD603933</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ervan Hasan Harun, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#f97316 0%,#fb923c 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#fff7ed 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#f97316"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#f97316">LAB. ELEKTRONIKA DAN KOMUNIKASI</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 3 sesi · Elektronika</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. ELEKTRONIKA DAN KOMUNIKASI">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead606352 piranti optoelektronika a hendy prasetyo, s.s.t., m.t. syahrir abdussamad, st., mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="hendy prasetyo, s.s.t., m.t.|syahrir abdussamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Piranti Optoelektronika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD606352</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 09:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Hendy Prasetyo, S.S.T., M.T.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead606552 antena dan propagasi a zainudin bonok, st.,mt. ifan wiranto, st., mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="zainudin bonok, st.,mt.|ifan wiranto, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Antena dan Propagasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD606552</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 14:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Zainudin Bonok, ST.,MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602533 sirkuit &amp; elektronika c iskandar z. nasibu, s.pd, m.eng. wildan, s.pd. m.pd teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="iskandar z. nasibu, s.pd, m.eng.|wildan, s.pd. m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Sirkuit &amp; Elektronika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602533</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Wildan, S.Pd. M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">Lab Instalasi</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 10 · 2 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="Lab Instalasi">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ung0513601012 dasar-dasar kependidikan a ir. rahmad hidayat dongka, s.pd., m.pd. andi sitti dwi auliyani, s.pd., m.pd pendidikan vokasional rekayasa elektro 1" data-prodi="Pendidikan Vokasional Rekayasa Elektro" data-dosen="ir. rahmad hidayat dongka, s.pd., m.pd.|andi sitti dwi auliyani, s.pd., m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Dasar-Dasar Kependidikan</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">UNG0513601012</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Pendidikan Vokasional Rekayasa Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Andi Sitti Dwi Auliyani, S.Pd., M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ean601812 menggambar teknik a ir. rahmad hidayat dongka, s.pd., m.pd. dr. lanto m. kamil amali, st., mt. pendidikan vokasional rekayasa elektro 1" data-prodi="Pendidikan Vokasional Rekayasa Elektro" data-dosen="ir. rahmad hidayat dongka, s.pd., m.pd.|dr. lanto m. kamil amali, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Menggambar Teknik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAN601812</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Pendidikan Vokasional Rekayasa Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 17:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
</section>

<!-- PANEL SELASA -->
<section class="tab-panel" id="panel-SELASA" data-tabbody>
  <div class="mb-3 flex items-center justify-between"><h2 class="text-base font-semibold text-slate-800">Selasa <span class="text-sm font-normal text-slate-400">· 9 ruangan</span></h2></div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">LAB. UNPROTECT</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. UNPROTECT">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601413 teknologi informasi c dr. mohamad syafri tuloli, st, mt zainudin bonok, st.,mt. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="dr. mohamad syafri tuloli, st, mt|zainudin bonok, st.,mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Teknologi Informasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601413</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Mohamad Syafri Tuloli, ST, MT</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Zainudin Bonok, ST.,MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="nas0511600412 bahasa indonesia c dosen luar dosen luar teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="dosen luar|dosen luar">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Bahasa Indonesia</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">NAS0511600412</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-[10px] font-bold">Belum Ditentukan</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead600913 kalkulus 1 a taufiq ismail yusuf, st., m.si. ervan hasan harun, st., mt. teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="taufiq ismail yusuf, st., m.si.|ervan hasan harun, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Kalkulus 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD600913</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Taufiq Ismail Yusuf, ST., M.Si.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ervan Hasan Harun, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601413 teknologi informasi b dr. mohamad syafri tuloli, st, mt zainudin bonok, st.,mt. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="dr. mohamad syafri tuloli, st, mt|zainudin bonok, st.,mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Teknologi Informasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601413</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Mohamad Syafri Tuloli, ST, MT</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Zainudin Bonok, ST.,MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">R.K 2.11</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="R.K 2.11">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal600913 fisika 1 d taufiq ismail yusuf, st., m.si. ade irawaty tolago, st., mt. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="taufiq ismail yusuf, st., m.si.|ade irawaty tolago, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Fisika 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL600913</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">D</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Taufiq Ismail Yusuf, ST., M.Si.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="nas0503600412 bahasa indonesia b dosen luar dosen luar teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="dosen luar|dosen luar">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Bahasa Indonesia</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">NAS0503600412</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-[10px] font-bold">Belum Ditentukan</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601833 variabel kompleks a ifan wiranto, st., mt. ir. rahmad hidayat dongka, s.pd., m.pd. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="ifan wiranto, st., mt.|ir. rahmad hidayat dongka, s.pd., m.pd.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Variabel Kompleks</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601833</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601833 variabel kompleks c ifan wiranto, st., mt. ir. rahmad hidayat dongka, s.pd., m.pd. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="ifan wiranto, st., mt.|ir. rahmad hidayat dongka, s.pd., m.pd.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Variabel Kompleks</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601833</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#6d28d9 0%,#a78bfa 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f5f3ff 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#6d28d9"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#6d28d9">LAB. TEKNIK KENDALI</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 3 sesi · Kendali</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. TEKNIK KENDALI">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal603953 sistem tertanam &amp; mikroprosesor b syahrir abdussamad, st., mt. iskandar z. nasibu, s.pd, m.eng. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="syahrir abdussamad, st., mt.|iskandar z. nasibu, s.pd, m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Sistem Tertanam &amp; Mikroprosesor</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL603953</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ung0503600572 literasi digital a amirudin yunus dako, st., m.eng. prof. dr. ir. sardi salim, m. pd.,ipu., asean eng. teknik elektro 7" data-prodi="Teknik Elektro" data-dosen="amirudin yunus dako, st., m.eng.|prof. dr. ir. sardi salim, m. pd.,ipu., asean eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Literasi Digital</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">UNG0503600572</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 14:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602933 fisika 2 a andi sitti dwi auliyani, s.pd., m.pd yasin mohamad, st., mt. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="andi sitti dwi auliyani, s.pd., m.pd|yasin mohamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Fisika 2</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602933</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Andi Sitti Dwi Auliyani, S.Pd., M.Pd</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#0369a1 0%,#0ea5e9 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f0f9ff 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#0369a1"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#0369a1">LAB. KOMPUTER 1</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 20 · 4 sesi · Komputer</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. KOMPUTER 1">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601113 dasar-dasar pemrograman 1 a amirudin yunus dako, st., m.eng. ulfatun nadifa, s.pd, m.kom. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="amirudin yunus dako, st., m.eng.|ulfatun nadifa, s.pd, m.kom.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Dasar-Dasar Pemrograman 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601113</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604152 multimedia a amirudin yunus dako, st., m.eng. ikhsan hidayat, s.kom., mt. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="amirudin yunus dako, st., m.eng.|ikhsan hidayat, s.kom., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Multimedia</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604152</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ikhsan Hidayat, S.Kom., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601913 pemrograman dasar b prof. lanto ningrayati amali, s.kom., m.kom., ph.d abdul gani f. lihawa, s.kom., m.kom teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="prof. lanto ningrayati amali, s.kom., m.kom., ph.d|abdul gani f. lihawa, s.kom., m.kom">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pemrograman Dasar</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601913</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604353 jaringan komputasi a ikhsan hidayat, s.kom., mt. dr. bambang panji asmara, st., mt. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="ikhsan hidayat, s.kom., mt.|dr. bambang panji asmara, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Jaringan Komputasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604353</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ikhsan Hidayat, S.Kom., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">R.K 3.16</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="R.K 3.16">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601633 matematika diskrit b rahmat d.r. dako, st., m.eng. andi sitti dwi auliyani, s.pd., m.pd teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="rahmat d.r. dako, st., m.eng.|andi sitti dwi auliyani, s.pd., m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Matematika Diskrit</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601633</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Andi Sitti Dwi Auliyani, S.Pd., M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead607532 tata tulis laporan dan karya ilmiah c yasin mohamad, st., mt. ade irawaty tolago, st., mt. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="yasin mohamad, st., mt.|ade irawaty tolago, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Tata Tulis Laporan dan Karya Ilmiah</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD607532</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602933 fisika 2 c andi sitti dwi auliyani, s.pd., m.pd yasin mohamad, st., mt. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="andi sitti dwi auliyani, s.pd., m.pd|yasin mohamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Fisika 2</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602933</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Andi Sitti Dwi Auliyani, S.Pd., M.Pd</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601513 probabilitas dan statistika a ade irawaty tolago, st., mt. nurul, s.pd., m.pd teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="ade irawaty tolago, st., mt.|nurul, s.pd., m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Probabilitas dan Statistika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601513</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Nurul, S.Pd., M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#b45309 0%,#f59e0b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#fffbeb 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#b45309"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#b45309">LAB. TENAGA LISTRIK</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Tenaga Listrik</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. TENAGA LISTRIK">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604653 distribusi tenaga listrik a yasin mohamad, st., mt. ade irawaty tolago, st., mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="yasin mohamad, st., mt.|ade irawaty tolago, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Distribusi Tenaga Listrik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604653</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604152 teknik instalasi  b dr. lanto m. kamil amali, st., mt. wildan, s.pd. m.pd teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="dr. lanto m. kamil amali, st., mt.|wildan, s.pd. m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Teknik Instalasi </h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604152</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Wildan, S.Pd. M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604152 teknik instalasi b dr. lanto m. kamil amali, st., mt. wildan, s.pd. m.pd teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="dr. lanto m. kamil amali, st., mt.|wildan, s.pd. m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Teknik Instalasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604152</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Wildan, S.Pd. M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604453 pembangkit tenaga listrik a dr.ir. arifin matoka, mt. dr. lanto m. kamil amali, st., mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="dr.ir. arifin matoka, mt.|dr. lanto m. kamil amali, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pembangkit Tenaga Listrik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604453</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr.Ir. Arifin Matoka, MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#0369a1 0%,#0ea5e9 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f0f9ff 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#0369a1"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#0369a1">LAB. KOMPUTER 2</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 20 · 3 sesi · Komputer</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. KOMPUTER 2">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal606073 cloud computing a afifah farhanah akadji, s.si., m.si. salmawaty tansa, st., m.eng. teknik komputer 7" data-prodi="Teknik Komputer" data-dosen="afifah farhanah akadji, s.si., m.si.|salmawaty tansa, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Cloud computing</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL606073</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Afifah Farhanah Akadji, S.Si., M.Si.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604652 desain rekayasa komputasi 1 c rahmat d.r. dako, st., m.eng. abdul gani f. lihawa, s.kom., m.kom teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="rahmat d.r. dako, st., m.eng.|abdul gani f. lihawa, s.kom., m.kom">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Desain Rekayasa Komputasi 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604652</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead608173 pemrograman web a ulfatun nadifa, s.pd, m.kom. rahmat d.r. dako, st., m.eng. teknik elektro 7" data-prodi="Teknik Elektro" data-dosen="ulfatun nadifa, s.pd, m.kom.|rahmat d.r. dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pemrograman Web</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD608173</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">14:40 - 17:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#f97316 0%,#fb923c 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#fff7ed 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#f97316"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#f97316">LAB. ELEKTRONIKA DAN KOMUNIKASI</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 3 sesi · Elektronika</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. ELEKTRONIKA DAN KOMUNIKASI">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602432 elektronika dasar a salmawaty tansa, st., m.eng. iskandar z. nasibu, s.pd, m.eng. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="salmawaty tansa, st., m.eng.|iskandar z. nasibu, s.pd, m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Elektronika Dasar</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602432</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602432 elektronika dasar b iskandar z. nasibu, s.pd, m.eng. salmawaty tansa, st., m.eng. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="iskandar z. nasibu, s.pd, m.eng.|salmawaty tansa, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Elektronika Dasar</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602432</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 14:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602533 sirkuit &amp; elektronika b wildan, s.pd. m.pd iskandar z. nasibu, s.pd, m.eng. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="wildan, s.pd. m.pd|iskandar z. nasibu, s.pd, m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Sirkuit &amp; Elektronika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602533</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">14:40 - 17:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Wildan, S.Pd. M.Pd</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">Lab Instalasi</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 10 · 1 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="Lab Instalasi">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ean601912 ilmu bahan listrik a nurul, s.pd., m.pd jumiati ilham, st., mt. pendidikan vokasional rekayasa elektro 1" data-prodi="Pendidikan Vokasional Rekayasa Elektro" data-dosen="nurul, s.pd., m.pd|jumiati ilham, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Ilmu Bahan Listrik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAN601912</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Pendidikan Vokasional Rekayasa Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Nurul, S.Pd., M.Pd</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Jumiati Ilham, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
</section>

<!-- PANEL RABU -->
<section class="tab-panel" id="panel-RABU" data-tabbody>
  <div class="mb-3 flex items-center justify-between"><h2 class="text-base font-semibold text-slate-800">Rabu <span class="text-sm font-normal text-slate-400">· 9 ruangan</span></h2></div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">R.K 2.11</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="R.K 2.11">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604452 sistem operasi b ikhsan hidayat, s.kom., mt. dr. bambang panji asmara, st., mt. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="ikhsan hidayat, s.kom., mt.|dr. bambang panji asmara, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Sistem Operasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604452</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 09:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ikhsan Hidayat, S.Kom., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601413 teknologi informasi d zainudin bonok, st.,mt. dr. mohamad syafri tuloli, st, mt teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="zainudin bonok, st.,mt.|dr. mohamad syafri tuloli, st, mt">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Teknologi Informasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601413</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">D</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">09:40 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Zainudin Bonok, ST.,MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Mohamad Syafri Tuloli, ST, MT</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601113 fisika 1 c salmawaty tansa, st., m.eng. ade irawaty tolago, st., mt. teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="salmawaty tansa, st., m.eng.|ade irawaty tolago, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Fisika 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601113</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead600913 kalkulus 1 b ervan hasan harun, st., mt. taufiq ismail yusuf, st., m.si. teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="ervan hasan harun, st., mt.|taufiq ismail yusuf, st., m.si.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Kalkulus 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD600913</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ervan Hasan Harun, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Taufiq Ismail Yusuf, ST., M.Si.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">LAB. UNPROTECT</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. UNPROTECT">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601013 kalkulus 1 c afifah farhanah akadji, s.si., m.si. ifan wiranto, st., mt. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="afifah farhanah akadji, s.si., m.si.|ifan wiranto, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Kalkulus 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601013</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Afifah Farhanah Akadji, S.Si., M.Si.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604452 sistem operasi a ikhsan hidayat, s.kom., mt. dr. bambang panji asmara, st., mt. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="ikhsan hidayat, s.kom., mt.|dr. bambang panji asmara, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Sistem Operasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604452</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ikhsan Hidayat, S.Kom., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602533 sirkuit &amp; elektronika a iskandar z. nasibu, s.pd, m.eng. wildan, s.pd. m.pd teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="iskandar z. nasibu, s.pd, m.eng.|wildan, s.pd. m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Sirkuit &amp; Elektronika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602533</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Wildan, S.Pd. M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602933 fisika 2 b yasin mohamad, st., mt. andi sitti dwi auliyani, s.pd., m.pd teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="yasin mohamad, st., mt.|andi sitti dwi auliyani, s.pd., m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Fisika 2</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602933</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Andi Sitti Dwi Auliyani, S.Pd., M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#0369a1 0%,#0ea5e9 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f0f9ff 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#0369a1"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#0369a1">LAB. KOMPUTER 2</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 20 · 3 sesi · Komputer</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. KOMPUTER 2">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601113 dasar-dasar pemrograman 1 b ulfatun nadifa, s.pd, m.kom. amirudin yunus dako, st., m.eng. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="ulfatun nadifa, s.pd, m.kom.|amirudin yunus dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Dasar-Dasar Pemrograman 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601113</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601113 dasar-dasar pemrograman 1 d ulfatun nadifa, s.pd, m.kom. amirudin yunus dako, st., m.eng. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="ulfatun nadifa, s.pd, m.kom.|amirudin yunus dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Dasar-Dasar Pemrograman 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601113</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">D</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal603033 pemrograman client/server c abdul gani f. lihawa, s.kom., m.kom rahmat d.r. dako, st., m.eng. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="abdul gani f. lihawa, s.kom., m.kom|rahmat d.r. dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pemrograman Client/Server</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL603033</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#6d28d9 0%,#a78bfa 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f5f3ff 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#6d28d9"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#6d28d9">LAB. TEKNIK KENDALI</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Kendali</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. TEKNIK KENDALI">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead603933 metode numerik a syahrir abdussamad, st., mt. ervan hasan harun, st., mt. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="syahrir abdussamad, st., mt.|ervan hasan harun, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Metode Numerik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD603933</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ervan Hasan Harun, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602732 sinyal dan sistem b ifan wiranto, st., mt. salmawaty tansa, st., m.eng. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="ifan wiranto, st., mt.|salmawaty tansa, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Sinyal dan Sistem</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602732</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604553 sinyal &amp; sistem b ifan wiranto, st., mt. hendy prasetyo, s.s.t., m.t. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="ifan wiranto, st., mt.|hendy prasetyo, s.s.t., m.t.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Sinyal &amp; Sistem</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604553</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Hendy Prasetyo, S.S.T., M.T.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602732 desain digital &amp; logika a dr. bambang panji asmara, st., mt. salmawaty tansa, st., m.eng. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="dr. bambang panji asmara, st., mt.|salmawaty tansa, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Desain Digital &amp; Logika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602732</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 17:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#0369a1 0%,#0ea5e9 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f0f9ff 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#0369a1"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#0369a1">LAB. KOMPUTER 1</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 20 · 4 sesi · Komputer</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. KOMPUTER 1">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal603033 pemrograman client/server b rahmat d.r. dako, st., m.eng. abdul gani f. lihawa, s.kom., m.kom teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="rahmat d.r. dako, st., m.eng.|abdul gani f. lihawa, s.kom., m.kom">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pemrograman Client/Server</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL603033</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ean602012 algoritma dan pemrograman a afifah farhanah akadji, s.si., m.si. dr. bambang panji asmara, st., mt. pendidikan vokasional rekayasa elektro 1" data-prodi="Pendidikan Vokasional Rekayasa Elektro" data-dosen="afifah farhanah akadji, s.si., m.si.|dr. bambang panji asmara, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Algoritma dan Pemrograman</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAN602012</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Pendidikan Vokasional Rekayasa Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Afifah Farhanah Akadji, S.Si., M.Si.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601913 pemrograman dasar a prof. lanto ningrayati amali, s.kom., m.kom., ph.d abdul gani f. lihawa, s.kom., m.kom teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="prof. lanto ningrayati amali, s.kom., m.kom., ph.d|abdul gani f. lihawa, s.kom., m.kom">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pemrograman Dasar</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601913</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601113 dasar-dasar pemrograman 1 c amirudin yunus dako, st., m.eng. ulfatun nadifa, s.pd, m.kom. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="amirudin yunus dako, st., m.eng.|ulfatun nadifa, s.pd, m.kom.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Dasar-Dasar Pemrograman 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601113</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">R.K 3.16</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="R.K 3.16">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601113 fisika 1 a ade irawaty tolago, st., mt. salmawaty tansa, st., m.eng. teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="ade irawaty tolago, st., mt.|salmawaty tansa, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Fisika 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601113</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead607532 tata tulis laporan dan karya ilmiah a yasin mohamad, st., mt. ade irawaty tolago, st., mt. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="yasin mohamad, st., mt.|ade irawaty tolago, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Tata Tulis Laporan dan Karya Ilmiah</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD607532</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601633 matematika diskrit c andi sitti dwi auliyani, s.pd., m.pd rahmat d.r. dako, st., m.eng. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="andi sitti dwi auliyani, s.pd., m.pd|rahmat d.r. dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Matematika Diskrit</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601633</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Andi Sitti Dwi Auliyani, S.Pd., M.Pd</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601013 kalkulus 1 d ifan wiranto, st., mt. afifah farhanah akadji, s.si., m.si. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="ifan wiranto, st., mt.|afifah farhanah akadji, s.si., m.si.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Kalkulus 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601013</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">D</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Afifah Farhanah Akadji, S.Si., M.Si.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#b45309 0%,#f59e0b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#fffbeb 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#b45309"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#b45309">LAB. TENAGA LISTRIK</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Tenaga Listrik</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. TENAGA LISTRIK">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead608273 standarisasi a jumiati ilham, st., mt. dr. lanto m. kamil amali, st., mt. teknik elektro 7" data-prodi="Teknik Elektro" data-dosen="jumiati ilham, st., mt.|dr. lanto m. kamil amali, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Standarisasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD608273</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Jumiati Ilham, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead608073 aplikasi komputer dalam  sistem tenaga listrik a ir. rahmad hidayat dongka, s.pd., m.pd. dr.ir. arifin matoka, mt. teknik elektro 7" data-prodi="Teknik Elektro" data-dosen="ir. rahmad hidayat dongka, s.pd., m.pd.|dr.ir. arifin matoka, mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Aplikasi Komputer dalam  Sistem Tenaga Listrik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD608073</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr.Ir. Arifin Matoka, MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604752 energi baru terbarukan a jumiati ilham, st., mt. dr. lanto m. kamil amali, st., mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="jumiati ilham, st., mt.|dr. lanto m. kamil amali, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Energi Baru Terbarukan</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604752</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 17:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Jumiati Ilham, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604752 energi baru terbarukan a jumiati ilham, st., mt. dr. lanto m. kamil amali, st., mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="jumiati ilham, st., mt.|dr. lanto m. kamil amali, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Energi Baru Terbarukan</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604752</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 17:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Jumiati Ilham, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#f97316 0%,#fb923c 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#fff7ed 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#f97316"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#f97316">LAB. ELEKTRONIKA DAN KOMUNIKASI</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 1 sesi · Elektronika</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. ELEKTRONIKA DAN KOMUNIKASI">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead606852 jaringan telekomunikasi a dr. bambang panji asmara, st., mt. zainudin bonok, st.,mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="dr. bambang panji asmara, st., mt.|zainudin bonok, st.,mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Jaringan Telekomunikasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD606852</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 14:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Zainudin Bonok, ST.,MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">Lab Instalasi</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 10 · 1 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="Lab Instalasi">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ung0513600912 perkembangan peserta didik a ir. rahmad hidayat dongka, s.pd., m.pd. nurul, s.pd., m.pd pendidikan vokasional rekayasa elektro 1" data-prodi="Pendidikan Vokasional Rekayasa Elektro" data-dosen="ir. rahmad hidayat dongka, s.pd., m.pd.|nurul, s.pd., m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Perkembangan Peserta Didik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">UNG0513600912</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Pendidikan Vokasional Rekayasa Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 17:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Nurul, S.Pd., M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
</section>

<!-- PANEL KAMIS -->
<section class="tab-panel" id="panel-KAMIS" data-tabbody>
  <div class="mb-3 flex items-center justify-between"><h2 class="text-base font-semibold text-slate-800">Kamis <span class="text-sm font-normal text-slate-400">· 9 ruangan</span></h2></div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#6d28d9 0%,#a78bfa 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f5f3ff 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#6d28d9"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#6d28d9">LAB. TEKNIK KENDALI</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Kendali</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. TEKNIK KENDALI">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="nas0503600212 pancasila c dosen luar dosen luar teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="dosen luar|dosen luar">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pancasila</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">NAS0503600212</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 09:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-[10px] font-bold">Belum Ditentukan</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal603953 sistem tertanam &amp; mikroprosesor a iskandar z. nasibu, s.pd, m.eng. syahrir abdussamad, st., mt. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="iskandar z. nasibu, s.pd, m.eng.|syahrir abdussamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Sistem Tertanam &amp; Mikroprosesor</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL603953</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">09:40 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602332 tata tulis laporan dan karya ilmiah c amirudin yunus dako, st., m.eng. syahrir abdussamad, st., mt. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="amirudin yunus dako, st., m.eng.|syahrir abdussamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Tata Tulis Laporan dan Karya Ilmiah</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602332</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 14:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601313 pengantar statistik dan probabilitas a ervan hasan harun, st., mt. ade irawaty tolago, st., mt. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="ervan hasan harun, st., mt.|ade irawaty tolago, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pengantar Statistik dan Probabilitas</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601313</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">14:40 - 17:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ervan Hasan Harun, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">LAB. UNPROTECT</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. UNPROTECT">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601313 pengantar statistik dan probabilitas b ervan hasan harun, st., mt. ade irawaty tolago, st., mt. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="ervan hasan harun, st., mt.|ade irawaty tolago, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pengantar Statistik dan Probabilitas</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601313</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ervan Hasan Harun, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="nas0503600212 pancasila b dosen luar dosen luar teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="dosen luar|dosen luar">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pancasila</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">NAS0503600212</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-[10px] font-bold">Belum Ditentukan</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead605852 pengolahan sinyal digital a salmawaty tansa, st., m.eng. hendy prasetyo, s.s.t., m.t. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="salmawaty tansa, st., m.eng.|hendy prasetyo, s.s.t., m.t.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pengolahan Sinyal Digital</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD605852</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 14:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Hendy Prasetyo, S.S.T., M.T.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602433 persamaan diferensial a afifah farhanah akadji, s.si., m.si. ifan wiranto, st., mt. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="afifah farhanah akadji, s.si., m.si.|ifan wiranto, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Persamaan Diferensial</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602433</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Afifah Farhanah Akadji, S.Si., M.Si.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#0369a1 0%,#0ea5e9 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f0f9ff 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#0369a1"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#0369a1">LAB. KOMPUTER 1</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 20 · 4 sesi · Komputer</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. KOMPUTER 1">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604152 multimedia b ikhsan hidayat, s.kom., mt. amirudin yunus dako, st., m.eng. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="ikhsan hidayat, s.kom., mt.|amirudin yunus dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Multimedia</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604152</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 09:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ikhsan Hidayat, S.Kom., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604152 multimedia c amirudin yunus dako, st., m.eng. ikhsan hidayat, s.kom., mt. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="amirudin yunus dako, st., m.eng.|ikhsan hidayat, s.kom., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Multimedia</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604152</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">09:40 - 11:20</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ikhsan Hidayat, S.Kom., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal603033 pemrograman client/server a rahmat d.r. dako, st., m.eng. abdul gani f. lihawa, s.kom., m.kom teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="rahmat d.r. dako, st., m.eng.|abdul gani f. lihawa, s.kom., m.kom">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pemrograman Client/Server</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL603033</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal606273 pemrograman jaringan a dr. bambang panji asmara, st., mt. salmawaty tansa, st., m.eng. teknik komputer 7" data-prodi="Teknik Komputer" data-dosen="dr. bambang panji asmara, st., mt.|salmawaty tansa, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pemrograman Jaringan</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL606273</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#0369a1 0%,#0ea5e9 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f0f9ff 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#0369a1"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#0369a1">LAB. KOMPUTER 2</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 20 · 2 sesi · Komputer</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. KOMPUTER 2">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601913 pemrograman dasar d prof. lanto ningrayati amali, s.kom., m.kom., ph.d abdul gani f. lihawa, s.kom., m.kom teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="prof. lanto ningrayati amali, s.kom., m.kom., ph.d|abdul gani f. lihawa, s.kom., m.kom">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pemrograman Dasar</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601913</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">D</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604353 jaringan komputasi b dr. bambang panji asmara, st., mt. ikhsan hidayat, s.kom., mt. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="dr. bambang panji asmara, st., mt.|ikhsan hidayat, s.kom., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Jaringan Komputasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604353</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ikhsan Hidayat, S.Kom., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">R.K 3.16</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 5 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="R.K 3.16">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead607672 k3 dan etika profesi a jumiati ilham, st., mt. dr. lanto m. kamil amali, st., mt. teknik elektro 7" data-prodi="Teknik Elektro" data-dosen="jumiati ilham, st., mt.|dr. lanto m. kamil amali, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">K3 dan Etika Profesi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD607672</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 09:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Jumiati Ilham, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead608673 pengolahan citra a salmawaty tansa, st., m.eng. dr. bambang panji asmara, st., mt. teknik elektro 7" data-prodi="Teknik Elektro" data-dosen="salmawaty tansa, st., m.eng.|dr. bambang panji asmara, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pengolahan Citra</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD608673</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">09:40 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601413 kimia  b jumiati ilham, st., mt. ulfatun nadifa, s.pd, m.kom. teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="jumiati ilham, st., mt.|ulfatun nadifa, s.pd, m.kom.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Kimia </h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601413</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Jumiati Ilham, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead607152 metodologi riset a dr. lanto m. kamil amali, st., mt. ifan wiranto, st., mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="dr. lanto m. kamil amali, st., mt.|ifan wiranto, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Metodologi Riset</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD607152</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">16:20 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead607152 metodologi riset a dr. lanto m. kamil amali, st., mt. ifan wiranto, st., mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="dr. lanto m. kamil amali, st., mt.|ifan wiranto, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Metodologi Riset</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD607152</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">16:20 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">R.K 2.11</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="R.K 2.11">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601633 matematika diskrit a andi sitti dwi auliyani, s.pd., m.pd rahmat d.r. dako, st., m.eng. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="andi sitti dwi auliyani, s.pd., m.pd|rahmat d.r. dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Matematika Diskrit</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601633</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Andi Sitti Dwi Auliyani, S.Pd., M.Pd</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead607532 tata tulis laporan dan karya ilmiah b ade irawaty tolago, st., mt. yasin mohamad, st., mt. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="ade irawaty tolago, st., mt.|yasin mohamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Tata Tulis Laporan dan Karya Ilmiah</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD607532</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601833 variabel kompleks b ir. rahmad hidayat dongka, s.pd., m.pd. ifan wiranto, st., mt. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="ir. rahmad hidayat dongka, s.pd., m.pd.|ifan wiranto, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Variabel Kompleks</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601833</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602332 tata tulis laporan dan karya ilmiah b syahrir abdussamad, st., mt. amirudin yunus dako, st., m.eng. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="syahrir abdussamad, st., mt.|amirudin yunus dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Tata Tulis Laporan dan Karya Ilmiah</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602332</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 17:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#f97316 0%,#fb923c 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#fff7ed 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#f97316"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#f97316">LAB. ELEKTRONIKA DAN KOMUNIKASI</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 1 sesi · Elektronika</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. ELEKTRONIKA DAN KOMUNIKASI">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602432 elektronika dasar c salmawaty tansa, st., m.eng. iskandar z. nasibu, s.pd, m.eng. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="salmawaty tansa, st., m.eng.|iskandar z. nasibu, s.pd, m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Elektronika Dasar</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602432</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 09:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">Lab Instalasi</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 10 · 3 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="Lab Instalasi">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ean601512 pengantar elektro teknik a ir. rahmad hidayat dongka, s.pd., m.pd. nurul, s.pd., m.pd pendidikan vokasional rekayasa elektro 1" data-prodi="Pendidikan Vokasional Rekayasa Elektro" data-dosen="ir. rahmad hidayat dongka, s.pd., m.pd.|nurul, s.pd., m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pengantar Elektro Teknik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAN601512</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Pendidikan Vokasional Rekayasa Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 09:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Nurul, S.Pd., M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ean601612 matematika dasar a andi sitti dwi auliyani, s.pd., m.pd dr. lanto m. kamil amali, st., mt. pendidikan vokasional rekayasa elektro 1" data-prodi="Pendidikan Vokasional Rekayasa Elektro" data-dosen="andi sitti dwi auliyani, s.pd., m.pd|dr. lanto m. kamil amali, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Matematika dasar</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAN601612</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Pendidikan Vokasional Rekayasa Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">10:30 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Andi Sitti Dwi Auliyani, S.Pd., M.Pd</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ean601712 fisika a andi sitti dwi auliyani, s.pd., m.pd yasin mohamad, st., mt. pendidikan vokasional rekayasa elektro 1" data-prodi="Pendidikan Vokasional Rekayasa Elektro" data-dosen="andi sitti dwi auliyani, s.pd., m.pd|yasin mohamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Fisika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAN601712</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Pendidikan Vokasional Rekayasa Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 14:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Andi Sitti Dwi Auliyani, S.Pd., M.Pd</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#b45309 0%,#f59e0b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#fffbeb 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#b45309"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#b45309">LAB. TENAGA LISTRIK</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 1 sesi · Tenaga Listrik</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. TENAGA LISTRIK">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604952 mesin listrik a dr. lanto m. kamil amali, st., mt. dr.ir. arifin matoka, mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="dr. lanto m. kamil amali, st., mt.|dr.ir. arifin matoka, mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Mesin Listrik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604952</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">14:40 - 16:20</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr.Ir. Arifin Matoka, MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
</section>

<!-- PANEL JUMAT -->
<section class="tab-panel" id="panel-JUMAT" data-tabbody>
  <div class="mb-3 flex items-center justify-between"><h2 class="text-base font-semibold text-slate-800">Jumat <span class="text-sm font-normal text-slate-400">· 7 ruangan</span></h2></div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#6d28d9 0%,#a78bfa 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f5f3ff 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#6d28d9"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#6d28d9">LAB. TEKNIK KENDALI</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 3 sesi · Kendali</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. TEKNIK KENDALI">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal600913 fisika 1 c taufiq ismail yusuf, st., m.si. ade irawaty tolago, st., mt. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="taufiq ismail yusuf, st., m.si.|ade irawaty tolago, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Fisika 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL600913</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Taufiq Ismail Yusuf, ST., M.Si.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602433 persamaan diferensial b ifan wiranto, st., mt. afifah farhanah akadji, s.si., m.si. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="ifan wiranto, st., mt.|afifah farhanah akadji, s.si., m.si.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Persamaan Diferensial</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602433</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Afifah Farhanah Akadji, S.Si., M.Si.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602732 sinyal dan sistem a salmawaty tansa, st., m.eng. ifan wiranto, st., mt. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="salmawaty tansa, st., m.eng.|ifan wiranto, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Sinyal dan Sistem</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602732</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 17:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">R.K 2.11</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 3 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="R.K 2.11">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal606873 kecerdasan buatan a ulfatun nadifa, s.pd, m.kom. salmawaty tansa, st., m.eng. teknik komputer 7" data-prodi="Teknik Komputer" data-dosen="ulfatun nadifa, s.pd, m.kom.|salmawaty tansa, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Kecerdasan Buatan</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL606873</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601413 teknologi informasi a dr. mohamad syafri tuloli, st, mt zainudin bonok, st.,mt. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="dr. mohamad syafri tuloli, st, mt|zainudin bonok, st.,mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Teknologi Informasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601413</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Mohamad Syafri Tuloli, ST, MT</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Zainudin Bonok, ST.,MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604852 metode riset a amirudin yunus dako, st., m.eng. syahrir abdussamad, st., mt. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="amirudin yunus dako, st., m.eng.|syahrir abdussamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Metode Riset</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604852</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 17:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">R.K 3.16</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 4 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="R.K 3.16">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="nas0503600412 bahasa indonesia a dosen luar dosen luar teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="dosen luar|dosen luar">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Bahasa Indonesia</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">NAS0503600412</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 09:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-[10px] font-bold">Belum Ditentukan</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="nas0503600212 pancasila a dosen luar dosen luar teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="dosen luar|dosen luar">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pancasila</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">NAS0503600212</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">09:40 - 11:20</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-[10px] font-bold">Belum Ditentukan</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601513 probabilitas dan statistika b ade irawaty tolago, st., mt. nurul, s.pd., m.pd teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="ade irawaty tolago, st., mt.|nurul, s.pd., m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Probabilitas dan Statistika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601513</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Nurul, S.Pd., M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601313 pengantar statistik dan probabilitas c ervan hasan harun, st., mt. ade irawaty tolago, st., mt. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="ervan hasan harun, st., mt.|ade irawaty tolago, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pengantar Statistik dan Probabilitas</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601313</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ervan Hasan Harun, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#f97316 0%,#fb923c 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#fff7ed 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#f97316"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#f97316">LAB. ELEKTRONIKA DAN KOMUNIKASI</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 1 sesi · Elektronika</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. ELEKTRONIKA DAN KOMUNIKASI">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead606652 mikroprosesor dan mikrokontroler a iskandar z. nasibu, s.pd, m.eng. syahrir abdussamad, st., mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="iskandar z. nasibu, s.pd, m.eng.|syahrir abdussamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Mikroprosesor dan Mikrokontroler</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD606652</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 09:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">LAB. UNPROTECT</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 3 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. UNPROTECT">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602433 persamaan diferensial c afifah farhanah akadji, s.si., m.si. ifan wiranto, st., mt. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="afifah farhanah akadji, s.si., m.si.|ifan wiranto, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Persamaan Diferensial</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602433</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 10:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Afifah Farhanah Akadji, S.Si., M.Si.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead600913 kalkulus 1 c taufiq ismail yusuf, st., m.si. ervan hasan harun, st., mt. teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="taufiq ismail yusuf, st., m.si.|ervan hasan harun, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Kalkulus 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD600913</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Taufiq Ismail Yusuf, ST., M.Si.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ervan Hasan Harun, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead607973 internet of things a iskandar z. nasibu, s.pd, m.eng. hendy prasetyo, s.s.t., m.t. teknik elektro 7" data-prodi="Teknik Elektro" data-dosen="iskandar z. nasibu, s.pd, m.eng.|hendy prasetyo, s.s.t., m.t.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Internet of Things</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD607973</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Hendy Prasetyo, S.S.T., M.T.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#0369a1 0%,#0ea5e9 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f0f9ff 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#0369a1"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#0369a1">LAB. KOMPUTER 1</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 20 · 1 sesi · Komputer</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. KOMPUTER 1">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal606573 forensik digital a abdul gani f. lihawa, s.kom., m.kom syahrir abdussamad, st., mt. teknik komputer 7" data-prodi="Teknik Komputer" data-dosen="abdul gani f. lihawa, s.kom., m.kom|syahrir abdussamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Forensik Digital</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL606573</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">Lab Instalasi</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 10 · 1 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="Lab Instalasi">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="nas0513600212 pancasila a dosen luar dosen luar pendidikan vokasional rekayasa elektro 1" data-prodi="Pendidikan Vokasional Rekayasa Elektro" data-dosen="dosen luar|dosen luar">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pancasila</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">NAS0513600212</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Pendidikan Vokasional Rekayasa Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 17:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-[10px] font-bold">Belum Ditentukan</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
</section>

<!-- PANEL SABTU -->
<section class="tab-panel" id="panel-SABTU" data-tabbody>
  <div class="mb-3 flex items-center justify-between"><h2 class="text-base font-semibold text-slate-800">Sabtu <span class="text-sm font-normal text-slate-400">· 5 ruangan</span></h2></div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">R.K 2.11</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 3 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="R.K 2.11">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="nas0511600412 bahasa indonesia a dosen luar dosen luar teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="dosen luar|dosen luar">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Bahasa Indonesia</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">NAS0511600412</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 09:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-[10px] font-bold">Belum Ditentukan</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601313 pengantar statistik dan probabilitas d ade irawaty tolago, st., mt. ervan hasan harun, st., mt. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="ade irawaty tolago, st., mt.|ervan hasan harun, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Pengantar Statistik dan Probabilitas</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601313</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">D</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">09:40 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ervan Hasan Harun, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="nas0511600412 bahasa indonesia b dosen luar dosen luar teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="dosen luar|dosen luar">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Bahasa Indonesia</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">NAS0511600412</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 14:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-[10px] font-bold">Belum Ditentukan</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#b45309 0%,#f59e0b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#fffbeb 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#b45309"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#b45309">LAB. TENAGA LISTRIK</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 1 sesi · Tenaga Listrik</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. TENAGA LISTRIK">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604552 transmisi tenaga listrik a ervan hasan harun, st., mt. taufiq ismail yusuf, st., m.si. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="ervan hasan harun, st., mt.|taufiq ismail yusuf, st., m.si.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Transmisi Tenaga Listrik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604552</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 09:40</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ervan Hasan Harun, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Taufiq Ismail Yusuf, ST., M.Si.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">LAB. UNPROTECT</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 1 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="LAB. UNPROTECT">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead607374 perancangan teknik elektro a iskandar z. nasibu, s.pd, m.eng. dr. lanto m. kamil amali, st., mt. teknik elektro 7" data-prodi="Teknik Elektro" data-dosen="iskandar z. nasibu, s.pd, m.eng.|dr. lanto m. kamil amali, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Perancangan Teknik Elektro</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD607374</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">4</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">08:00 - 11:20</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">R.K 3.16</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 30 · 3 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="R.K 3.16">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604553 sinyal &amp; sistem a ifan wiranto, st., mt. hendy prasetyo, s.s.t., m.t. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="ifan wiranto, st., mt.|hendy prasetyo, s.s.t., m.t.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Sinyal &amp; Sistem</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604553</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">09:40 - 12:10</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Hendy Prasetyo, S.S.T., M.T.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601013 kalkulus 1 a ifan wiranto, st., mt. afifah farhanah akadji, s.si., m.si. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="ifan wiranto, st., mt.|afifah farhanah akadji, s.si., m.si.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Kalkulus 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601013</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">13:00 - 15:30</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Afifah Farhanah Akadji, S.Si., M.Si.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead601113 fisika 1 b ade irawaty tolago, st., mt. salmawaty tansa, st., m.eng. teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="ade irawaty tolago, st., mt.|salmawaty tansa, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Fisika 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD601113</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">15:30 - 18:00</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ade Irawaty Tolago, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
  <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div style="background:linear-gradient(90deg,#334155 0%,#64748b 100%);height:6px;"></div><div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">
      <div class="mb-1 flex items-center justify-center gap-2" style="color:#334155"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">Laboratorium</span></div>
      <h3 class="text-base font-bold text-slate-800"><span style="color:#334155">Lab Instalasi</span></h3>
      <p class="mt-0.5 text-[11px] text-slate-500">Kapasitas 10 · 1 sesi · Umum</p>
    </div>
    <div class="p-4">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="Lab Instalasi">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="nas0513600412 bahasa indonesia a dosen luar dosen luar pendidikan vokasional rekayasa elektro 1" data-prodi="Pendidikan Vokasional Rekayasa Elektro" data-dosen="dosen luar|dosen luar">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Bahasa Indonesia</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">NAS0513600412</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">2</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Pendidikan Vokasional Rekayasa Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">09:40 - 11:20</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-[10px] font-bold">Belum Ditentukan</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li></ul>
                            </div>
                        </div>
                    </article>
      </div>
    </div>
  </div>
</section>

<!-- PANEL UNDEFINED -->
<section class="tab-panel" id="panel-UNDEFINED" data-tabbody>
  <div class="mb-3 flex items-center justify-between"><h2 class="text-base font-semibold text-slate-800">Undefined <span class="text-sm font-normal text-slate-400">· 48 sesi belum memiliki hari</span></h2></div>
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="grid gap-4 p-4 md:grid-cols-2 xl:grid-cols-3" data-room="">
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602011 prak. pemrograman dasar a abdul gani f. lihawa, s.kom., m.kom rahmat d.r. dako, st., m.eng. teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="abdul gani f. lihawa, s.kom., m.kom|rahmat d.r. dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Pemrograman Dasar</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602011</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602011 prak. pemrograman dasar b prof. lanto ningrayati amali, s.kom., m.kom., ph.d abdul gani f. lihawa, s.kom., m.kom teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="prof. lanto ningrayati amali, s.kom., m.kom., ph.d|abdul gani f. lihawa, s.kom., m.kom">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Pemrograman Dasar</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602011</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602011 prak. pemrograman dasar c abdul gani f. lihawa, s.kom., m.kom dr. mohamad syafri tuloli, st, mt teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="abdul gani f. lihawa, s.kom., m.kom|dr. mohamad syafri tuloli, st, mt">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Pemrograman Dasar</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602011</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Mohamad Syafri Tuloli, ST, MT</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602011 prak. pemrograman dasar d prof. lanto ningrayati amali, s.kom., m.kom., ph.d abdul gani f. lihawa, s.kom., m.kom teknik elektro 1" data-prodi="Teknik Elektro" data-dosen="prof. lanto ningrayati amali, s.kom., m.kom., ph.d|abdul gani f. lihawa, s.kom., m.kom">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Pemrograman Dasar</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602011</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">D</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602331 prak. rangkaian listrik a prof. dr. ir. sardi salim, m. pd.,ipu., asean eng. yasin mohamad, st., mt. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="prof. dr. ir. sardi salim, m. pd.,ipu., asean eng.|yasin mohamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Rangkaian Listrik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602331</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602331 prak. rangkaian listrik b yasin mohamad, st., mt. prof. dr. ir. sardi salim, m. pd.,ipu., asean eng. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="yasin mohamad, st., mt.|prof. dr. ir. sardi salim, m. pd.,ipu., asean eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Rangkaian Listrik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602331</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602331 prak. rangkaian listrik c prof. dr. ir. sardi salim, m. pd.,ipu., asean eng. yasin mohamad, st., mt. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="prof. dr. ir. sardi salim, m. pd.,ipu., asean eng.|yasin mohamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Rangkaian Listrik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602331</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602531 prak. elektronika dasar a salmawaty tansa, st., m.eng. iskandar z. nasibu, s.pd, m.eng. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="salmawaty tansa, st., m.eng.|iskandar z. nasibu, s.pd, m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Elektronika Dasar</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602531</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602531 prak. elektronika dasar b iskandar z. nasibu, s.pd, m.eng. salmawaty tansa, st., m.eng. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="iskandar z. nasibu, s.pd, m.eng.|salmawaty tansa, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Elektronika Dasar</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602531</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602831 prak. sinyal dan sistem a salmawaty tansa, st., m.eng. ifan wiranto, st., mt. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="salmawaty tansa, st., m.eng.|ifan wiranto, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Sinyal dan Sistem</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602831</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead602831 prak. sinyal dan sistem b ifan wiranto, st., mt. salmawaty tansa, st., m.eng. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="ifan wiranto, st., mt.|salmawaty tansa, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Sinyal dan Sistem</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD602831</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604031 prak. metode numerik a syahrir abdussamad, st., mt. ervan hasan harun, st., mt. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="syahrir abdussamad, st., mt.|ervan hasan harun, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Metode Numerik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604031</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ervan Hasan Harun, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604031 prak. metode numerik b syahrir abdussamad, st., mt. ervan hasan harun, st., mt. teknik elektro 3" data-prodi="Teknik Elektro" data-dosen="syahrir abdussamad, st., mt.|ervan hasan harun, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Metode Numerik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604031</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ervan Hasan Harun, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604251 prak. teknik instalasi a dr. lanto m. kamil amali, st., mt. wildan, s.pd. m.pd teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="dr. lanto m. kamil amali, st., mt.|wildan, s.pd. m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Teknik Instalasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604251</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Wildan, S.Pd. M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604251 prak. teknik instalasi a dr. lanto m. kamil amali, st., mt. wildan, s.pd. m.pd teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="dr. lanto m. kamil amali, st., mt.|wildan, s.pd. m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Teknik Instalasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604251</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Wildan, S.Pd. M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604251 prak. teknik instalasi b dr. lanto m. kamil amali, st., mt. wildan, s.pd. m.pd teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="dr. lanto m. kamil amali, st., mt.|wildan, s.pd. m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Teknik Instalasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604251</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Wildan, S.Pd. M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604251 prak. teknik instalasi b dr. lanto m. kamil amali, st., mt. wildan, s.pd. m.pd teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="dr. lanto m. kamil amali, st., mt.|wildan, s.pd. m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Teknik Instalasi</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604251</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Wildan, S.Pd. M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604851 prak. energi baru terbarukan a jumiati ilham, st., mt. dr. lanto m. kamil amali, st., mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="jumiati ilham, st., mt.|dr. lanto m. kamil amali, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Energi Baru Terbarukan</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604851</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Jumiati Ilham, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead604851 prak. energi baru terbarukan a jumiati ilham, st., mt. dr. lanto m. kamil amali, st., mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="jumiati ilham, st., mt.|dr. lanto m. kamil amali, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Energi Baru Terbarukan</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD604851</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Jumiati Ilham, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead605051 prak. mesin listrik a dr. lanto m. kamil amali, st., mt. dr.ir. arifin matoka, mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="dr. lanto m. kamil amali, st., mt.|dr.ir. arifin matoka, mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Mesin Listrik</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD605051</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Lanto M. Kamil Amali, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr.Ir. Arifin Matoka, MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead606751 prak. mikroprosesor dan mikrokontroler a iskandar z. nasibu, s.pd, m.eng. syahrir abdussamad, st., mt. teknik elektro 5" data-prodi="Teknik Elektro" data-dosen="iskandar z. nasibu, s.pd, m.eng.|syahrir abdussamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Mikroprosesor dan Mikrokontroler</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD606751</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ead607274 magang a ifan wiranto, st., mt. dosen luar teknik elektro 7" data-prodi="Teknik Elektro" data-dosen="ifan wiranto, st., mt.|dosen luar">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Magang</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAD607274</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">4</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 text-blue-700 px-2 py-0.5 text-[10px] font-bold">Full Pengajar 1</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ifan Wiranto, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dosen Luar</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601211 prak. dasar-dasar pemrograman 1 a ulfatun nadifa, s.pd, m.kom. amirudin yunus dako, st., m.eng. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="ulfatun nadifa, s.pd, m.kom.|amirudin yunus dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Dasar-Dasar Pemrograman 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601211</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601211 prak. dasar-dasar pemrograman 1 b amirudin yunus dako, st., m.eng. ulfatun nadifa, s.pd, m.kom. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="amirudin yunus dako, st., m.eng.|ulfatun nadifa, s.pd, m.kom.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Dasar-Dasar Pemrograman 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601211</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601211 prak. dasar-dasar pemrograman 1 c amirudin yunus dako, st., m.eng. ulfatun nadifa, s.pd, m.kom. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="amirudin yunus dako, st., m.eng.|ulfatun nadifa, s.pd, m.kom.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Dasar-Dasar Pemrograman 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601211</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601211 prak. dasar-dasar pemrograman 1 d ulfatun nadifa, s.pd, m.kom. amirudin yunus dako, st., m.eng. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="ulfatun nadifa, s.pd, m.kom.|amirudin yunus dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Dasar-Dasar Pemrograman 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601211</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">D</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal601211 prak. dasar-dasar pemrograman 1 e ulfatun nadifa, s.pd, m.kom. amirudin yunus dako, st., m.eng. teknik komputer 1" data-prodi="Teknik Komputer" data-dosen="ulfatun nadifa, s.pd, m.kom.|amirudin yunus dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Dasar-Dasar Pemrograman 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL601211</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">E</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ulfatun Nadifa, S.Pd, M.Kom.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602631 prak. sirkuit &amp; elektronika a iskandar z. nasibu, s.pd, m.eng. wildan, s.pd. m.pd teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="iskandar z. nasibu, s.pd, m.eng.|wildan, s.pd. m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Sirkuit &amp; Elektronika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602631</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Wildan, S.Pd. M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602631 prak. sirkuit &amp; elektronika b wildan, s.pd. m.pd iskandar z. nasibu, s.pd, m.eng. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="wildan, s.pd. m.pd|iskandar z. nasibu, s.pd, m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Sirkuit &amp; Elektronika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602631</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Wildan, S.Pd. M.Pd</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602631 prak. sirkuit &amp; elektronika c iskandar z. nasibu, s.pd, m.eng. wildan, s.pd. m.pd teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="iskandar z. nasibu, s.pd, m.eng.|wildan, s.pd. m.pd">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Sirkuit &amp; Elektronika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602631</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Wildan, S.Pd. M.Pd</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602831 prak. desain digital &amp; logika a dr. bambang panji asmara, st., mt. salmawaty tansa, st., m.eng. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="dr. bambang panji asmara, st., mt.|salmawaty tansa, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Desain Digital &amp; Logika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602831</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602831 prak. desain digital &amp; logika b salmawaty tansa, st., m.eng. dr. bambang panji asmara, st., mt. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="salmawaty tansa, st., m.eng.|dr. bambang panji asmara, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Desain Digital &amp; Logika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602831</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal602831 prak. desain digital &amp; logika c dr. bambang panji asmara, st., mt. salmawaty tansa, st., m.eng. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="dr. bambang panji asmara, st., mt.|salmawaty tansa, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Desain Digital &amp; Logika</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL602831</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Dr. Bambang Panji Asmara, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Salmawaty Tansa, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal603131 prak. pemrograman client/server a abdul gani f. lihawa, s.kom., m.kom rahmat d.r. dako, st., m.eng. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="abdul gani f. lihawa, s.kom., m.kom|rahmat d.r. dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Pemrograman Client/Server</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL603131</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal603131 prak. pemrograman client/server b rahmat d.r. dako, st., m.eng. abdul gani f. lihawa, s.kom., m.kom teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="rahmat d.r. dako, st., m.eng.|abdul gani f. lihawa, s.kom., m.kom">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Pemrograman Client/Server</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL603131</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal603131 prak. pemrograman client/server c abdul gani f. lihawa, s.kom., m.kom rahmat d.r. dako, st., m.eng. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="abdul gani f. lihawa, s.kom., m.kom|rahmat d.r. dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Pemrograman Client/Server</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL603131</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal603131 prak. pemrograman client/server d abdul gani f. lihawa, s.kom., m.kom rahmat d.r. dako, st., m.eng. teknik komputer 3" data-prodi="Teknik Komputer" data-dosen="abdul gani f. lihawa, s.kom., m.kom|rahmat d.r. dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Pemrograman Client/Server</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL603131</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">D</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 3</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604051 prak. sistem tertanam &amp; mikroprosesor a iskandar z. nasibu, s.pd, m.eng. syahrir abdussamad, st., mt. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="iskandar z. nasibu, s.pd, m.eng.|syahrir abdussamad, st., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Sistem Tertanam &amp; Mikroprosesor</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604051</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604051 prak. sistem tertanam &amp; mikroprosesor b syahrir abdussamad, st., mt. iskandar z. nasibu, s.pd, m.eng. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="syahrir abdussamad, st., mt.|iskandar z. nasibu, s.pd, m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Sistem Tertanam &amp; Mikroprosesor</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604051</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Syahrir Abdussamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Iskandar Z. Nasibu, S.Pd, M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604251 prak. multimedia a amirudin yunus dako, st., m.eng. ikhsan hidayat, s.kom., mt. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="amirudin yunus dako, st., m.eng.|ikhsan hidayat, s.kom., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Multimedia</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604251</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ikhsan Hidayat, S.Kom., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604251 prak. multimedia b ikhsan hidayat, s.kom., mt. amirudin yunus dako, st., m.eng. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="ikhsan hidayat, s.kom., mt.|amirudin yunus dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Multimedia</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604251</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ikhsan Hidayat, S.Kom., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604251 prak. multimedia c amirudin yunus dako, st., m.eng. ikhsan hidayat, s.kom., mt. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="amirudin yunus dako, st., m.eng.|ikhsan hidayat, s.kom., mt.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Multimedia</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604251</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Ikhsan Hidayat, S.Kom., MT.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604751 prak. desain rekayasa komputasi 1 a abdul gani f. lihawa, s.kom., m.kom rahmat d.r. dako, st., m.eng. teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="abdul gani f. lihawa, s.kom., m.kom|rahmat d.r. dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Desain Rekayasa Komputasi 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604751</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604751 prak. desain rekayasa komputasi 1 b rahmat d.r. dako, st., m.eng. abdul gani f. lihawa, s.kom., m.kom teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="rahmat d.r. dako, st., m.eng.|abdul gani f. lihawa, s.kom., m.kom">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Desain Rekayasa Komputasi 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604751</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">B</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal604751 prak. desain rekayasa komputasi 1 c rahmat d.r. dako, st., m.eng. abdul gani f. lihawa, s.kom., m.kom teknik komputer 5" data-prodi="Teknik Komputer" data-dosen="rahmat d.r. dako, st., m.eng.|abdul gani f. lihawa, s.kom., m.kom">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Prak. Desain Rekayasa Komputasi 1</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL604751</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">C</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 5</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">1</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Rahmat D.R. Dako, ST., M.Eng.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Abdul Gani F. Lihawa, S.Kom., M.Kom</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="eal605674 magang a yasin mohamad, st., mt. amirudin yunus dako, st., m.eng. teknik komputer 7" data-prodi="Teknik Komputer" data-dosen="yasin mohamad, st., mt.|amirudin yunus dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Magang</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">EAL605674</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">4</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ung0503600874 kuliah kerja nyata a yasin mohamad, st., mt. amirudin yunus dako, st., m.eng. teknik elektro 7" data-prodi="Teknik Elektro" data-dosen="yasin mohamad, st., mt.|amirudin yunus dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">Kuliah Kerja Nyata</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">UNG0503600874</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">4</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Elektro</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
                    <article class="mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow"
                             data-cari="ung0511600874 kkn a yasin mohamad, st., mt. amirudin yunus dako, st., m.eng. teknik komputer 7" data-prodi="Teknik Komputer" data-dosen="yasin mohamad, st., mt.|amirudin yunus dako, st., m.eng.">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3">
                            <h4 class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800">KKN</h4>
                            <div class="flex shrink-0 items-center gap-1"><button type="button" class="mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100">Edit</button><button type="button" class="mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100">Hapus</button></div>
                        </div>
                        <div class="flex-1 px-4 py-3">
                            <dl class="space-y-1.5 text-[13px]">
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kode</dt><dd class="font-medium text-slate-700">UNG0511600874</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Kelas</dt><dd class="font-medium text-slate-700">A</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Semester</dt><dd class="font-medium text-slate-700">Semester 7</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">SKS</dt><dd class="font-medium text-slate-700">4</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Prodi</dt><dd class="font-medium text-slate-700">Teknik Komputer</dd></div>
                                <div class="flex justify-between gap-2"><dt class="text-slate-400">Waktu</dt><dd class="font-medium text-slate-700">Belum dijadwalkan</dd></div>
                            </dl>
                            <div class="mt-3 border-t border-slate-100 pt-2.5">
                                <div class="mb-1 flex items-center justify-between gap-2"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pengajar</p><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span></div>
                                <ul class="space-y-1 text-[12px] text-slate-600"><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Yasin Mohamad, ST., MT.</span></li><li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>Amirudin Yunus Dako, ST., M.Eng.</span></li></ul>
                            </div>
                        </div>
                    </article>
    </div>
  </div>
</section>

    </div><!-- /#view-jadwal -->

    <!-- ================= VIEW RUANGAN (CRUD) ================= -->
    <section id="view-ruangan" style="display:none">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-lg text-blue-600">
                    <i class="fas fa-door-open"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Kelola Ruangan</h1>
                    <p class="text-xs text-slate-500">Tambah, ubah, dan hapus ruangan laboratorium &amp; kelas.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="btnTambahRuang" class="inline-flex items-center gap-1.5 rounded-lg border border-blue-600 bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"><i class="fas fa-plus"></i> Tambah Ruangan</button>
                <button type="button" id="btnKembaliJadwal" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"><i class="fas fa-arrow-left"></i> Kembali ke Jadwal</button>
            </div>
        </div>

        <!-- statistik ruangan -->
        <section class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="tile-corak tile-sky rounded-xl p-4 text-white shadow-md">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Total Ruangan</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight" id="statJmlRuang">0</p>
                    </div>
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-door-open"></i></span>
                </div>
            </div>
            <div class="tile-corak tile-emerald rounded-xl p-4 text-white shadow-md">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Kapasitas Total</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight" id="statKapasitas">0</p>
                    </div>
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-user-group"></i></span>
                </div>
            </div>
            <div class="tile-corak tile-orange rounded-xl p-4 text-white shadow-md">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Laboratorium</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight" id="statLab">0</p>
                    </div>
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-flask"></i></span>
                </div>
            </div>
            <div class="tile-corak tile-violet rounded-xl p-4 text-white shadow-md">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-white/85">Ruang Kelas</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight" id="statKelas">0</p>
                    </div>
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-chalkboard"></i></span>
                </div>
            </div>
        </section>

        <!-- daftar ruangan -->
        <section class="mb-4">
            <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                <div class="relative flex-1 min-w-[180px]">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input type="search" id="fCariRuang" placeholder="Cari nama ruangan, tipe…"
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-blue-400 focus:bg-white">
                </div>
                <select id="fTipeRuang" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-blue-400">
                    <option value="">Semua Tipe</option>
                    <option value="Laboratorium">Laboratorium</option>
                    <option value="Ruang Kelas">Ruang Kelas</option>
                </select>
            </div>
        </section>
        <div id="gridRuang" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"></div>
        <p id="lblRuangKosong" class="mt-6 hidden text-center text-sm text-slate-400">Belum ada ruangan. Klik <b>Tambah Ruangan</b> untuk membuat ruangan baru.</p>
    </section>

    <!-- ===== Modal Ruangan (Tambah/Edit) ===== -->
    <div class="modal-overlay" id="ruangModal" role="dialog" aria-modal="true">
        <div class="flex max-h-[88vh] w-full max-w-md flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
                <h6 class="text-sm font-semibold text-slate-900"><i class="fas fa-door-open mr-1 text-blue-600"></i><span id="ruangModalTitle">Tambah Ruangan</span></h6>
                <button type="button" class="text-xl leading-none text-slate-400 transition hover:text-slate-700" data-ruang-close>&times;</button>
            </div>
            <div class="flex-1 overflow-y-auto p-5">
                <div class="form-field">
                    <label>Nama Ruangan</label>
                    <input type="text" id="rNama" placeholder="cth: LAB. KOMPUTER 3">
                </div>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <div class="form-field">
                        <label>Tipe</label>
                        <select id="rTipe">
                            <option value="Laboratorium">Laboratorium</option>
                            <option value="Ruang Kelas">Ruang Kelas</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Kapasitas</label>
                        <input type="number" id="rKapasitas" min="1" placeholder="cth: 30">
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <div class="form-field">
                        <label>Status</label>
                        <select id="rStatus">
                            <option value="Umum">Umum</option>
                            <option value="Komputer">Komputer</option>
                            <option value="Tenaga Listrik">Tenaga Listrik</option>
                            <option value="Kendali">Kendali</option>
                            <option value="Elektronika">Elektronika</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Warna</label>
                        <select id="rWarna">
                            <option value="#334155">Slate</option>
                            <option value="#0369a1">Sky</option>
                            <option value="#0ea5e9">Light Blue</option>
                            <option value="#047857">Emerald</option>
                            <option value="#6d28d9">Violet</option>
                            <option value="#f97316">Orange</option>
                            <option value="#b45309">Amber</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-slate-100 px-5 py-3">
                <button type="button" class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-200" data-ruang-close>Batal</button>
                <button type="button" id="btnSaveRuang" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"><i class="fas fa-check mr-1"></i>Simpan</button>
            </div>
        </div>
    </div>

</main>

<!-- ===== Modal Edit Jadwal ===== -->
<div class="modal-overlay" id="detailModal" role="dialog" aria-modal="true">
    <div class="flex max-h-[88vh] w-full max-w-lg flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
            <h6 class="text-sm font-semibold text-slate-900"><i class="fas fa-pen-to-square mr-1 text-blue-600"></i><span id="modalTitle">Edit Jadwal</span></h6>
            <button type="button" class="text-xl leading-none text-slate-400 transition hover:text-slate-700" data-modal-close>&times;</button>
        </div>
        <div class="flex-1 overflow-y-auto p-5">
            <!-- Info Mata Kuliah (read-only, diload dari kode) -->
            <div class="mb-4 rounded-xl border border-blue-100 bg-blue-50/60 p-4">
                <div class="mb-2 flex items-center gap-1.5 text-[10px] font-semibold uppercase tracking-wide text-blue-600">
                    <i class="fas fa-book-open"></i> Info Mata Kuliah
                </div>
                <div class="form-field">
                    <label>Mata Kuliah</label>
                    <select id="eMatkul"></select>
                </div>
                <div class="mt-2 grid grid-cols-2 gap-2">
                    <div class="info-field col-span-2"><label>Nama</label><p id="iNama">—</p></div>
                    <div class="info-field"><label>Kode</label><p id="iKode">—</p></div>
                    <div class="info-field"><label>Semester</label><p id="iSemester">—</p></div>
                    <div class="info-field"><label>SKS</label><p id="iSks">—</p></div>
                    <div class="info-field"><label>Prodi</label><p id="iProdi">—</p></div>
                </div>
            </div>
            <!-- Jadwal (editable) -->
            <div class="grid grid-cols-2 gap-3">
                <div class="form-field">
                    <label>Hari</label>
                    <select id="eHari"></select>
                </div>
                <div class="form-field">
                    <label>Kelas</label>
                    <select id="eKelas"></select>
                </div>
                <div class="form-field col-span-2">
                    <label>Ruangan</label>
                    <select id="eRuang"></select>
                </div>
                <div class="form-field">
                    <label>Jam Mulai</label>
                    <input type="time" id="eMasuk">
                </div>
                <div class="form-field">
                    <label>Jam Selesai</label>
                    <input type="text" id="iSelesai" readonly>
                </div>
                <p class="form-hint col-span-2">Jam selesai otomatis dihitung dari jam mulai + SKS × 50 menit.</p>
                <div class="form-field col-span-2">
                    <label>Pengajar 1</label>
                    <select id="eP1"></select>
                </div>
                <div class="form-field col-span-2">
                    <label>Pengajar 2</label>
                    <select id="eP2"></select>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t border-slate-100 px-5 py-3">
            <button type="button" class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-200" data-modal-close>Batal</button>
            <button type="button" id="btnSaveEdit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"><i class="fas fa-check mr-1"></i>Simpan</button>
        </div>
    </div>
</div>

<script>
(function () {
    var tabAktif = null;
    var btnTab = Array.prototype.slice.call(document.querySelectorAll('.tab-btn[data-tab]'));
    var originalCounts = {};
    btnTab.forEach(function (b) {
        originalCounts[b.getAttribute('data-tab')] = parseInt(b.querySelector('.tnum').textContent, 10) || 0;
    });

    function setTab(t, silent) {
        tabAktif = t;
        btnTab.forEach(function (b) { b.setAttribute('aria-selected', b.getAttribute('data-tab') === t ? 'true' : 'false'); });
        document.querySelectorAll('[data-tabbody]').forEach(function (p) {
            p.classList.toggle('active', p.id === 'panel-' + t);
        });
        if (!silent) updatePanel();
    }
    btnTab.forEach(function (b) { b.addEventListener('click', function () { setTab(b.getAttribute('data-tab')); }); });

    function nilaiCari() { return document.getElementById('fCari').value.trim().toLowerCase(); }
    function matcher(q) {
        var prodi = document.getElementById('fProdi').value;
        var dosen = document.getElementById('fDosen').value;
        return function (card) {
            if (q && (card.getAttribute('data-cari') || '').indexOf(q) === -1) return false;
            if (prodi && card.getAttribute('data-prodi') !== prodi) return false;
            if (dosen && (card.getAttribute('data-dosen') || '').indexOf(dosen.toLowerCase()) === -1) return false;
            return true;
        };
    }

    function updateBadges(q) {
        btnTab.forEach(function (b) {
            var tab = b.getAttribute('data-tab');
            var panel = document.getElementById('panel-' + tab);
            var tnum = b.querySelector('.tnum');
            if (!panel || !tnum) return;
            if (!q && !document.getElementById('fProdi').value && !document.getElementById('fDosen').value) {
                tnum.textContent = originalCounts[tab];
                tnum.classList.remove('hit');
                return;
            }
            var n = 0;
            panel.querySelectorAll('.mk-card').forEach(function (card) {
                if (matcher(q)(card)) n++;
            });
            tnum.textContent = n;
            tnum.classList.toggle('hit', n > 0);
        });
    }

    function updatePanel() {
        var q = nilaiCari();
        var adaFilter = q || document.getElementById('fProdi').value || document.getElementById('fDosen').value;
        var cocok = 0, total = 0;
        document.querySelectorAll('#panel-' + tabAktif + ' .mk-card').forEach(function (card) {
            var r = matcher(q)(card);
            card.classList.toggle('hide', !r);
            if (r) cocok++;
            total++;
        });
        var lbl = document.getElementById('lblHasil');
        if (lbl) lbl.textContent = adaFilter ? cocok + ' dari ' + total + ' kartu cocok' : '';
    }

    function terapkanCari() {
        var q = nilaiCari();
        updateBadges(q);
        updatePanel();
    }
    document.getElementById('fCari').addEventListener('input', terapkanCari);
    document.getElementById('fProdi').addEventListener('change', terapkanCari);
    document.getElementById('fDosen').addEventListener('change', terapkanCari);

    function resetAll() {
        document.getElementById('fCari').value = '';
        document.getElementById('fProdi').value = '';
        document.getElementById('fDosen').value = '';
        terapkanCari();
    }
    document.getElementById('btnReset').addEventListener('click', resetAll);
    document.getElementById('btnReset2').addEventListener('click', resetAll);

    /* [Edit] → modal (isi form dari kartu) */
    var overlay = document.getElementById('detailModal');
    var editCard = null;

    function escapeHtml(s) {
        return (s || '').replace(/[&<>"']/g, function (ch) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
        });
    }
    function badgeHtml(p1, p2) {
        var n = 0, v1 = (p1 || '').trim(), v2 = (p2 || '').trim();
        if (v1 && v1 !== 'Dosen Luar') n++;
        if (v2 && v2 !== 'Dosen Luar') n++;
        if (n >= 2) return '<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-[10px] font-bold">Bagi 2</span>';
        if (n === 1) return '<span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 text-blue-700 px-2 py-0.5 text-[10px] font-bold">Full Pengajar 1</span>';
        return '<span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-[10px] font-bold">Belum Ditentukan</span>';
    }
    function pengajarLis(p1, p2) {
        var arr = [p1, p2].filter(function (v) { return v && v.trim(); });
        if (!arr.length) return '<li class="text-slate-400">Belum ditentukan</li>';
        return arr.map(function (p) {
            return '<li class="flex items-start gap-1.5"><i class="fas fa-user-tie mt-0.5 text-[10px] text-slate-300"></i><span>' + escapeHtml(p) + '</span></li>';
        }).join('');
    }
    function recomputeCari(card) {
        var nama = card.querySelector('h4').textContent.trim();
        var dds = card.querySelectorAll('dl dd');
        var kode = dds[0].textContent.trim(), kelas = dds[1].textContent.trim(), prodi = dds[4].textContent.trim(), sem = dds[2].textContent.trim();
        var dosenAttr = card.getAttribute('data-dosen') || '';
        var pengajar = dosenAttr.split('|').filter(Boolean).join(' ');
        card.setAttribute('data-cari', (kode + ' ' + nama + ' ' + kelas + ' ' + pengajar + ' ' + prodi + ' ' + sem).toLowerCase());
    }

    /* kumpulkan opsi dropdown dari kartu yang ada */
    var matkulMap = {};   // kode -> {nama, sem, sks, prodi}
    var kelasSet = {};
    var dosenSet = {};

    document.querySelectorAll('.mk-card').forEach(function (card) {
        var h4 = card.querySelector('h4');
        var dds = card.querySelectorAll('dl dd');
        var kode = dds[0] ? dds[0].textContent.trim() : '';
        var nama = h4 ? h4.textContent.trim() : '';
        var kelas = dds[1] ? dds[1].textContent.trim() : '';
        var sem = dds[2] ? dds[2].textContent.trim() : '';
        var sks = dds[3] ? dds[3].textContent.trim() : '';
        var prodi = dds[4] ? dds[4].textContent.trim() : '';
        if (kode && !matkulMap[kode]) matkulMap[kode] = { nama: nama, sem: sem, sks: sks, prodi: prodi };
        if (kelas) kelasSet[kelas] = true;
        (card.getAttribute('data-dosen') || '').split('|').forEach(function (d) {
            if (d && d.trim()) dosenSet[d.trim()] = true;
        });
    });

    function setOptions(select, list, placeholder) {
        select.innerHTML = '';
        if (placeholder) {
            var ph = document.createElement('option');
            ph.value = '';
            ph.textContent = placeholder;
            select.appendChild(ph);
        }
        list.forEach(function (v) {
            var o = document.createElement('option');
            o.value = v;
            o.textContent = v;
            select.appendChild(o);
        });
    }

    function ensureOption(select, value) {
        if (!value) return;
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].value === value) return;
        }
        var o = document.createElement('option');
        o.value = value;
        o.textContent = value;
        select.appendChild(o);
    }

    function fmtWaktu(totalMenit) {
        totalMenit = Math.max(0, totalMenit) % 1440;
        var h = Math.floor(totalMenit / 60), m = totalMenit % 60;
        return (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
    }

    function hitungSelesai() {
        var t = document.getElementById('eMasuk').value;
        var sks = parseInt(document.getElementById('iSks').textContent, 10) || 0;
        var out = document.getElementById('iSelesai');
        if (!t || !sks) { out.value = '—'; return ''; }
        var p = t.split(':');
        var res = fmtWaktu((+p[0] * 60 + +p[1]) + sks * 50);
        out.value = res;
        return res;
    }

    function infoFromKode() {
        var k = eMatkul.value;
        var info = matkulMap[k] || { nama: k, sem: '', sks: '', prodi: '' };
        document.getElementById('iNama').textContent = info.nama || '—';
        document.getElementById('iKode').textContent = k;
        document.getElementById('iSemester').textContent = (info.sem + '').replace(/^Semester\s*/i, '') || '—';
        document.getElementById('iSks').textContent = info.sks || '—';
        document.getElementById('iProdi').textContent = info.prodi || '—';
        hitungSelesai();
    }

    function isiRuang() {
        var day = eHari.value;
        var panel = document.getElementById('panel-' + day);
        var rooms = [];
        if (panel) {
            panel.querySelectorAll('.grid[data-room]').forEach(function (g) {
                var r = g.getAttribute('data-room');
                if (r && rooms.indexOf(r) === -1) rooms.push(r);
            });
        }
        setOptions(document.getElementById('eRuang'), rooms.slice().sort(), day === 'UNDEFINED' ? '— Belum Ditentukan —' : '— Pilih Ruangan —');
    }

    var DAYS = [
        { id: 'SENIN', label: 'Senin' },
        { id: 'SELASA', label: 'Selasa' },
        { id: 'RABU', label: 'Rabu' },
        { id: 'KAMIS', label: 'Kamis' },
        { id: 'JUMAT', label: 'Jumat' },
        { id: 'SABTU', label: 'Sabtu' },
        { id: 'UNDEFINED', label: 'Undefined' }
    ];

    /* isi dropdown sekali */
    var eMatkul = document.getElementById('eMatkul');
    eMatkul.innerHTML = '';
    Object.keys(matkulMap).sort().forEach(function (k) {
        var o = document.createElement('option');
        o.value = k;
        o.textContent = matkulMap[k].nama + ' (' + k + ')';
        eMatkul.appendChild(o);
    });
    eMatkul.addEventListener('change', infoFromKode);
    eMatkul.disabled = true;   // mode edit: info MK read-only

    var eHari = document.getElementById('eHari');
    eHari.innerHTML = '';
    DAYS.forEach(function (d) {
        var o = document.createElement('option');
        o.value = d.id;
        o.textContent = d.label;
        eHari.appendChild(o);
    });
    eHari.addEventListener('change', isiRuang);
    setOptions(document.getElementById('eKelas'), Object.keys(kelasSet).sort(), '— Pilih Kelas —');
    var dosenList = Object.keys(dosenSet).sort();
    if (!dosenSet['Dosen Luar']) dosenList.push('Dosen Luar');
    setOptions(document.getElementById('eP1'), dosenList, '— Pilih Pengajar —');
    setOptions(document.getElementById('eP2'), dosenList, '— Pilih Pengajar —');
    document.getElementById('eMasuk').addEventListener('change', hitungSelesai);

    function bukaForm(mode) {
        /* mode: 'edit' | 'tambah' */
        document.getElementById('modalTitle').textContent = (mode === 'tambah') ? 'Tambah Jadwal' : 'Edit Jadwal';
        eMatkul.disabled = (mode === 'edit');
        if (mode === 'tambah') {
            eMatkul.value = '';
            document.getElementById('iNama').textContent = '—';
            document.getElementById('iKode').textContent = '—';
            document.getElementById('iSemester').textContent = '—';
            document.getElementById('iSks').textContent = '—';
            document.getElementById('iProdi').textContent = '—';
            document.getElementById('iSelesai').value = '—';
            eHari.value = tabAktif;
            isiRuang();
            document.getElementById('eRuang').value = '';
            var kelasSel = document.getElementById('eKelas');
            kelasSel.value = '';
            document.getElementById('eMasuk').value = '';
            var pSel = document.getElementById('eP1');
            pSel.value = '';
            pSel = document.getElementById('eP2');
            pSel.value = '';
        }
        overlay.classList.add('show');
    }

    /* tombol [Edit] di kartu */
    function pasangEditListener() {
        document.querySelectorAll('.mk-edit').forEach(function (a) {
            if (a.getAttribute('data-bound')) return;
            a.setAttribute('data-bound', '1');
            a.addEventListener('click', function (e) {
                e.preventDefault();
                bukaForm('edit');
                editCard = a.closest('.mk-card');
                var panel = editCard.closest('[data-tabbody]');
                var dds = editCard.querySelectorAll('dl dd');

                var day = panel.id.replace('panel-', '');
                eHari.value = day;
                isiRuang();

                var cg = editCard.closest('.grid[data-room]');
                var currentRoom = cg ? cg.getAttribute('data-room') : '';
                ensureOption(document.getElementById('eRuang'), currentRoom);
                document.getElementById('eRuang').value = currentRoom;

                var kode = dds[0].textContent.trim();
                eMatkul.value = kode;
                infoFromKode();

                ensureOption(document.getElementById('eKelas'), dds[1].textContent.trim());
                document.getElementById('eKelas').value = dds[1].textContent.trim();

                var wtc = dds[5].textContent.trim();
                var wm = wtc.match(/(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})/);
                document.getElementById('eMasuk').value = wm ? wm[1] : '';
                hitungSelesai();

                var peng = editCard.querySelectorAll('ul li span');
                var p1 = peng[0] ? peng[0].textContent.trim() : '';
                var p2 = peng[1] ? peng[1].textContent.trim() : '';
                ensureOption(document.getElementById('eP1'), p1);
                ensureOption(document.getElementById('eP2'), p2);
                document.getElementById('eP1').value = p1;
                document.getElementById('eP2').value = p2;
            });
        });
        document.querySelectorAll('.mk-hapus').forEach(function (b) {
            if (b.getAttribute('data-bound')) return;
            b.setAttribute('data-bound', '1');
            b.addEventListener('click', function (e) {
                e.preventDefault();
                var card = b.closest('.mk-card');
                var nama = card.querySelector('h4').textContent.trim();
                if (!confirm('Hapus jadwal "' + nama + '"?')) return;
                card.remove();
                syncBadges();
                terapkanCari();
            });
        });
    }
    pasangEditListener();

    /* tombol Tambah Jadwal */
    document.getElementById('btnTambah').addEventListener('click', function () {
        editCard = null;
        bukaForm('tambah');
    });

    function buatKartu(o) {
        var art = document.createElement('article');
        art.className = 'mk-card flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow';
        art.setAttribute('data-prodi', o.prodi);
        art.setAttribute('data-dosen', (o.p1 + '|' + o.p2).split('|').filter(Boolean).join('|').toLowerCase());

        var head = document.createElement('div');
        head.className = 'flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3';
        var h4 = document.createElement('h4');
        h4.className = 'min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800';
        h4.textContent = o.nama;
        var btns = document.createElement('div');
        btns.className = 'flex shrink-0 items-center gap-1';
        var btnEdit = document.createElement('button');
        btnEdit.type = 'button';
        btnEdit.className = 'mk-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100';
        btnEdit.textContent = 'Edit';
        var btnHapus2 = document.createElement('button');
        btnHapus2.type = 'button';
        btnHapus2.className = 'mk-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100';
        btnHapus2.textContent = 'Hapus';
        btns.appendChild(btnEdit);
        btns.appendChild(btnHapus2);
        head.appendChild(h4);
        head.appendChild(btns);
        art.appendChild(head);

        var body = document.createElement('div');
        body.className = 'flex-1 px-4 py-3';
        var dl = document.createElement('dl');
        dl.className = 'space-y-1.5 text-[13px]';
        var dat = [
            ['Kode', o.kode],
            ['Kelas', o.kelas || '—'],
            ['Semester', o.sem || '—'],
            ['SKS', o.sks || '—'],
            ['Prodi', o.prodi],
            ['Waktu', (o.masuk && o.keluar) ? (o.masuk + ' - ' + o.keluar) : '—']
        ];
        dat.forEach(function (d) {
            var r = document.createElement('div');
            r.className = 'flex justify-between gap-2';
            var dt = document.createElement('dt');
            dt.className = 'text-slate-400';
            dt.textContent = d[0];
            var dd = document.createElement('dd');
            dd.className = 'font-medium text-slate-700';
            dd.textContent = d[1];
            r.appendChild(dt);
            r.appendChild(dd);
            dl.appendChild(r);
        });
        body.appendChild(dl);

        var ph = document.createElement('div');
        ph.className = 'mt-3 border-t border-slate-100 pt-2.5';
        var row = document.createElement('div');
        row.className = 'mb-1 flex items-center justify-between gap-2';
        var p = document.createElement('p');
        p.className = 'text-[10px] font-semibold uppercase tracking-wide text-slate-400';
        p.textContent = 'Pengajar';
        row.appendChild(p);
        row.insertAdjacentHTML('beforeend', badgeHtml(o.p1, o.p2));
        var ul = document.createElement('ul');
        ul.className = 'space-y-1 text-[12px] text-slate-600';
        ul.innerHTML = pengajarLis(o.p1, o.p2);
        ph.appendChild(row);
        ph.appendChild(ul);
        body.appendChild(ph);
        art.appendChild(body);

        art.setAttribute('data-cari', (o.kode + ' ' + o.nama + ' ' + (o.kelas || '') + ' ' + (o.p1 + ' ' + o.p2)
            + ' ' + o.prodi + ' ' + (o.sem || '')).toLowerCase());
        return art;
    }

    function syncBadges() {
        btnTab.forEach(function (b) {
            var t = b.getAttribute('data-tab');
            var panel = document.getElementById('panel-' + t);
            if (panel) b.querySelector('.tnum').textContent = panel.querySelectorAll('.mk-card').length;
        });
        originalCounts = {};
        btnTab.forEach(function (b) {
            originalCounts[b.getAttribute('data-tab')] = parseInt(b.querySelector('.tnum').textContent, 10) || 0;
        });
    }

    function cariGridTujuan(hari, ruang) {
        var panelTujuan = document.getElementById('panel-' + hari) || document.getElementById('panel-UNDEFINED');
        if (!panelTujuan) return null;
        var target = null;
        panelTujuan.querySelectorAll('.grid[data-room]').forEach(function (g) {
            if (g.getAttribute('data-room') === ruang) target = g;
        });
        if (!target) target = panelTujuan.querySelector('.grid[data-room]');
        return target;
    }

    document.getElementById('btnSaveEdit').addEventListener('click', function () {
        var kode = eMatkul.value;
        if (!kode) return;
        var info = matkulMap[kode] || { nama: kode, sks: '' };
        var nama = info.nama || kode;
        var sem = info.sem;
        var sks = info.sks;
        var prodi = info.prodi;
        var kelas = document.getElementById('eKelas').value;
        var p1 = document.getElementById('eP1').value;
        var p2 = document.getElementById('eP2').value;
        var masuk = document.getElementById('eMasuk').value;
        var keluar = hitungSelesai();
        var ruangBaru = document.getElementById('eRuang').value;
        var hariBaru = eHari.value;

        if (!editCard) {
            /* mode tambah: buat kartu baru */
            editCard = buatKartu({
                kode: kode, nama: nama, sem: sem, sks: sks, prodi: prodi,
                kelas: kelas, masuk: masuk, keluar: keluar, p1: p1, p2: p2
            });
            var tgrid = cariGridTujuan(hariBaru, ruangBaru);
            if (tgrid) tgrid.appendChild(editCard);
            pasangEditListener();
        } else {
            editCard.querySelector('h4').textContent = nama;
            var dds = editCard.querySelectorAll('dl dd');
            dds[0].textContent = kode;
            dds[1].textContent = kelas || '—';
            dds[2].textContent = sem || '—';
            dds[3].textContent = sks || '—';
            dds[4].textContent = prodi;
            dds[5].textContent = (masuk && keluar) ? (masuk + ' - ' + keluar) : '—';

            /* badge + pengajar */
            var ph = editCard.querySelector('.mt-3.border-t');
            ph.querySelector('.flex.items-center.justify-between span').outerHTML = badgeHtml(p1, p2);
            ph.querySelector('ul').innerHTML = pengajarLis(p1, p2);

            /* data attribute untuk filter */
            editCard.setAttribute('data-prodi', prodi);
            editCard.setAttribute('data-dosen', (p1 + '|' + p2).split('|').filter(Boolean).join('|').toLowerCase());
            recomputeCari(editCard);

            /* pindah kartu ke hari + ruangan tujuan */
            var panelLama = editCard.closest('[data-tabbody]');
            var panelTujuan = document.getElementById('panel-' + hariBaru) || panelLama;
            var target = null;
            panelTujuan.querySelectorAll('.grid[data-room]').forEach(function (g) {
                if (g.getAttribute('data-room') === ruangBaru) target = g;
            });
            if (!target) target = panelTujuan.querySelector('.grid[data-room]');
            if (target && target !== editCard.parentNode) target.appendChild(editCard);
        }

        overlay.classList.remove('show');
        editCard.classList.add('card-flash');
        setTimeout(function () { editCard.classList.remove('card-flash'); }, 1300);

        syncBadges();
        terapkanCari();
        editCard = null;
    });

    overlay.querySelectorAll('[data-modal-close]').forEach(function (b) {
        b.addEventListener('click', function () { overlay.classList.remove('show'); editCard = null; });
    });
    overlay.addEventListener('click', function (e) { if (e.target === overlay) { overlay.classList.remove('show'); editCard = null; } });

    /* badge awal = jumlah kartu per panel */
    btnTab.forEach(function (b) {
        var tab = b.getAttribute('data-tab');
        var panel = document.getElementById('panel-' + tab);
        if (panel) b.querySelector('.tnum').textContent = panel.querySelectorAll('.mk-card').length;
    });
    originalCounts = {};
    btnTab.forEach(function (b) {
        originalCounts[b.getAttribute('data-tab')] = parseInt(b.querySelector('.tnum').textContent, 10) || 0;
    });

    setTab('SENIN', true);
})();

/* ============ CRUD Ruangan (view switch dalam halaman yang sama) ============ */
(function () {
    var viewJadwal = document.getElementById('view-jadwal');
    var viewRuang = document.getElementById('view-ruangan');
    var ruangModal = document.getElementById('ruangModal');
    var ruangData = [];
    var editIndex = -1;

    function lighten(hex, amt) {
        var c = String(hex || '').replace('#', '');
        if (!c) return '#334155';
        if (c.length === 3) c = c.split('').map(function (x) { return x + x; }).join('');
        var num = parseInt(c, 16);
        var r = (num >> 16) & 255, g = (num >> 8) & 255, b = num & 255;
        r = Math.round(r + (255 - r) * amt);
        g = Math.round(g + (255 - g) * amt);
        b = Math.round(b + (255 - b) * amt);
        return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    }

    function escapeHtml2(s) {
        return (s || '').replace(/[&<>"']/g, function (ch) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
        });
    }

    function tipeRuang(nama) {
        return /^lab/i.test(nama) ? 'Laboratorium' : 'Ruang Kelas';
    }

    function kumpulkanRuang() {
        var map = {};
        document.querySelectorAll('[data-tabbody]').forEach(function (panel) {
            panel.querySelectorAll('.grid[data-room]').forEach(function (g) {
                var nama = g.getAttribute('data-room');
                if (!nama) return;
                var block = g.closest('.mb-5');
                if (!map[nama]) {
                    var warna = '#334155', kap = '', status = 'Umum';
                    if (block) {
                        var h3s = block.querySelector('h3 span');
                        if (h3s) {
                            var m = (h3s.getAttribute('style') || '').match(/color:\s*(#[0-9a-fA-F]{6})/);
                            if (m) warna = m[1];
                        }
                        var p = block.querySelector('p.mt-0\\.5');
                        if (p) {
                            var txt = p.textContent;
                            var km = txt.match(/Kapasitas\s*(\d+)/i);
                            if (km) kap = km[1];
                            var parts = txt.split('·');
                            if (parts.length >= 3) status = parts[2].trim();
                        }
                    }
                    map[nama] = { nama: nama, warna: warna, kapasitas: kap, status: status, tipe: tipeRuang(nama), sesi: 0 };
                }
                map[nama].sesi += g.querySelectorAll('.mk-card').length;
            });
        });
        var out = [];
        Object.keys(map).forEach(function (k) { out.push(map[k]); });
        return out;
    }

    function kartuRuang(r) {
        var art = document.createElement('article');
        art.className = 'flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-blue-300 hover:shadow';
        var head = document.createElement('div');
        head.className = 'flex items-start justify-between gap-2 border-b border-slate-100 px-4 py-3';
        var left = document.createElement('div');
        left.className = 'flex min-w-0 items-center gap-3';
        var ic = document.createElement('span');
        ic.className = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-white';
        ic.style.background = r.warna;
        ic.innerHTML = '<i class="fas fa-door-open text-sm"></i>';
        var tt = document.createElement('div');
        tt.className = 'min-w-0';
        var h3 = document.createElement('h3');
        h3.className = 'truncate text-sm font-bold text-slate-800';
        h3.textContent = r.nama;
        var sub = document.createElement('p');
        sub.className = 'text-[11px] text-slate-500';
        sub.textContent = r.tipe;
        tt.appendChild(h3); tt.appendChild(sub);
        left.appendChild(ic); left.appendChild(tt);
        var btns = document.createElement('div');
        btns.className = 'flex shrink-0 items-center gap-1';
        var bE = document.createElement('button');
        bE.type = 'button';
        bE.className = 'mk-ruang-edit rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-600 transition hover:bg-blue-100';
        bE.textContent = 'Edit';
        var bH = document.createElement('button');
        bH.type = 'button';
        bH.className = 'mk-ruang-hapus rounded-md border border-red-200 bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-100';
        bH.textContent = 'Hapus';
        btns.appendChild(bE); btns.appendChild(bH);
        head.appendChild(left); head.appendChild(btns);
        art.appendChild(head);
        var body = document.createElement('div');
        body.className = 'flex-1 px-4 py-3';
        var dl = document.createElement('dl');
        dl.className = 'space-y-1.5 text-[13px]';
        [['Kapasitas', r.kapasitas || '—'], ['Status', r.status], ['Sesi', r.sesi + ' sesi']].forEach(function (d) {
            var row = document.createElement('div');
            row.className = 'flex justify-between gap-2';
            var dt = document.createElement('dt');
            dt.className = 'text-slate-400';
            dt.textContent = d[0];
            var dd = document.createElement('dd');
            dd.className = 'font-medium text-slate-700';
            dd.textContent = d[1];
            row.appendChild(dt); row.appendChild(dd);
            dl.appendChild(row);
        });
        body.appendChild(dl);
        art.appendChild(body);
        bE.addEventListener('click', function () { bukaFormRuang(r); });
        bH.addEventListener('click', function () { hapusRuang(r); });
        return art;
    }

    function blokRuangHtml(r) {
        var c2 = lighten(r.warna, 0.3);
        var lbl = (r.tipe === 'Ruang Kelas') ? 'Ruang Kelas' : 'Laboratorium';
        return '<div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">'
            + '<div style="background:linear-gradient(90deg,' + r.warna + ' 0%,' + c2 + ' 100%);height:6px;"></div>'
            + '<div class="px-4 pb-4 pt-2 text-center" style="background:linear-gradient(180deg,#f1f5f9 0%,#ffffff 100%);">'
            + '<div class="mb-1 flex items-center justify-center gap-2" style="color:' + r.warna + '"><i class="fas fa-door-open text-xs"></i><span class="text-[10px] font-semibold uppercase tracking-[.15em]">' + lbl + '</span></div>'
            + '<h3 class="text-base font-bold text-slate-800"><span style="color:' + r.warna + '">' + escapeHtml2(r.nama) + '</span></h3>'
            + '<p class="mt-0.5 text-[11px] text-slate-500">Kapasitas ' + (r.kapasitas || '—') + ' · 0 sesi · ' + escapeHtml2(r.status) + '</p>'
            + '</div><div class="p-4"><div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-room="' + escapeHtml2(r.nama) + '"></div></div></div>';
    }

    function panels() {
        return Array.prototype.slice.call(document.querySelectorAll('[data-tabbody]'))
            .filter(function (p) { return p.id !== 'panel-UNDEFINED'; });
    }

    function perbaruiHitunganHari() {
        panels().forEach(function (p) {
            var n = p.querySelectorAll('.grid[data-room]').length;
            var span = p.querySelector('h2 span.text-sm');
            if (span) span.textContent = '· ' + n + ' ruangan';
        });
    }

    function renderRuang() {
        ruangData = kumpulkanRuang();
        var q = document.getElementById('fCariRuang').value.trim().toLowerCase();
        var tipe = document.getElementById('fTipeRuang').value;
        var grid = document.getElementById('gridRuang');
        grid.innerHTML = '';
        var list = ruangData.filter(function (r) {
            if (q && (r.nama + ' ' + r.tipe).toLowerCase().indexOf(q) === -1) return false;
            if (tipe && r.tipe !== tipe) return false;
            return true;
        });
        list.forEach(function (r) { grid.appendChild(kartuRuang(r)); });
        document.getElementById('lblRuangKosong').classList.toggle('hidden', list.length > 0);
        var kap = 0, nLab = 0, nKelas = 0;
        ruangData.forEach(function (r) {
            kap += parseInt(r.kapasitas, 10) || 0;
            if (r.tipe === 'Laboratorium') nLab++; else nKelas++;
        });
        document.getElementById('statJmlRuang').textContent = ruangData.length;
        document.getElementById('statKapasitas').textContent = kap.toLocaleString('id-ID');
        document.getElementById('statLab').textContent = nLab;
        document.getElementById('statKelas').textContent = nKelas;
        var labJ = document.getElementById('statLabJadwal');
        if (labJ) labJ.textContent = nLab;
    }

    function bukaFormRuang(r) {
        editIndex = r ? ruangData.indexOf(r) : -1;
        document.getElementById('ruangModalTitle').textContent = r ? 'Edit Ruangan' : 'Tambah Ruangan';
        document.getElementById('rNama').value = r ? r.nama : '';
        document.getElementById('rTipe').value = r ? r.tipe : 'Laboratorium';
        document.getElementById('rKapasitas').value = r ? r.kapasitas : '';
        document.getElementById('rStatus').value = r ? r.status : 'Umum';
        document.getElementById('rWarna').value = r ? (r.warna || '#334155') : '#334155';
        ruangModal.classList.add('show');
    }

    function simpanRuang() {
        var nama = document.getElementById('rNama').value.trim();
        if (!nama) { document.getElementById('rNama').focus(); return; }
        var tipe = document.getElementById('rTipe').value;
        var kap = document.getElementById('rKapasitas').value.trim();
        var status = document.getElementById('rStatus').value;
        var warna = document.getElementById('rWarna').value;
        var dup = ruangData.some(function (r, i) { return i !== editIndex && r.nama.toLowerCase() === nama.toLowerCase(); });
        if (dup) { alert('Nama ruangan "' + nama + '" sudah ada.'); return; }
        if (editIndex >= 0) {
            var old = ruangData[editIndex];
            var oldNama = old.nama;
            ruangData[editIndex] = { nama: nama, tipe: tipe, kapasitas: kap, status: status, warna: warna, sesi: old.sesi };
            document.querySelectorAll('[data-tabbody]').forEach(function (panel) {
                panel.querySelectorAll('.grid[data-room]').forEach(function (g) {
                    if (g.getAttribute('data-room') !== oldNama) return;
                    g.setAttribute('data-room', nama);
                    var block = g.closest('.mb-5');
                    if (!block) return;
                    var h3s = block.querySelector('h3 span');
                    if (h3s) { h3s.textContent = nama; h3s.setAttribute('style', 'color:' + warna); }
                    var strip = block.querySelector('div[style*="height:6px"]');
                    if (strip) strip.style.background = 'linear-gradient(90deg,' + warna + ' 0%,' + lighten(warna, 0.3) + ' 100%)';
                    var lbl = block.querySelector('[class="text-[10px]"]');
                    if (lbl) lbl.textContent = (tipe === 'Ruang Kelas') ? 'Ruang Kelas' : 'Laboratorium';
                    var p = block.querySelector('p.mt-0\\.5');
                    if (p) p.textContent = 'Kapasitas ' + (kap || '—') + ' · ' + g.querySelectorAll('.mk-card').length + ' sesi · ' + status;
                    var colorDiv = block.querySelector('.flex.items-center.justify-center.gap-2');
                    if (colorDiv) colorDiv.style.color = warna;
                });
            });
        } else {
            var nr = { nama: nama, tipe: tipe, kapasitas: kap, status: status, warna: warna, sesi: 0 };
            ruangData.push(nr);
            panels().forEach(function (p) { p.insertAdjacentHTML('beforeend', blokRuangHtml(nr)); });
        }
        perbaruiHitunganHari();
        renderRuang();
        ruangModal.classList.remove('show');
    }

    function hapusRuang(r) {
        var idx = ruangData.indexOf(r);
        if (r.sesi > 0) { alert('Ruangan "' + r.nama + '" masih memiliki ' + r.sesi + ' sesi terjadwal. Hapus/pindahkan jadwalnya dahulu.'); return; }
        if (!confirm('Hapus ruangan "' + r.nama + '"?')) return;
        document.querySelectorAll('[data-tabbody]').forEach(function (panel) {
            panel.querySelectorAll('.grid[data-room]').forEach(function (g) {
                if (g.getAttribute('data-room') !== r.nama) return;
                var block = g.closest('.mb-5');
                if (block) block.remove();
            });
        });
        if (idx >= 0) ruangData.splice(idx, 1);
        perbaruiHitunganHari();
        renderRuang();
    }

    document.getElementById('btnRuangan').addEventListener('click', function () {
        viewJadwal.style.display = 'none';
        viewRuang.style.display = '';
        renderRuang();
    });
    document.getElementById('btnKembaliJadwal').addEventListener('click', function () {
        viewRuang.style.display = 'none';
        viewJadwal.style.display = '';
    });
    document.getElementById('btnTambahRuang').addEventListener('click', function () { bukaFormRuang(null); });
    document.getElementById('btnSaveRuang').addEventListener('click', simpanRuang);
    document.getElementById('fCariRuang').addEventListener('input', renderRuang);
    document.getElementById('fTipeRuang').addEventListener('change', renderRuang);
    ruangModal.querySelectorAll('[data-ruang-close]').forEach(function (b) { b.addEventListener('click', function () { ruangModal.classList.remove('show'); }); });
    ruangModal.addEventListener('click', function (e) { if (e.target === ruangModal) ruangModal.classList.remove('show'); });
})();
</script>