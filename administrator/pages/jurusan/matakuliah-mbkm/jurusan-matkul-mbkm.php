<style>
.content-scroll { overflow-y: auto; min-height: 0; }

/* ===== Hover lift & reveal ===== */
.lift { transition: transform .2s ease, box-shadow .2s ease; }
.lift:hover { transform: translateY(-3px); box-shadow: 0 14px 28px -14px rgba(15,23,42,.22); }
@keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
.reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
@media (prefers-reduced-motion: reduce) { .reveal, .lift { animation: none; transition: none; } }

/* ===== Kartu matakuliah ===== */
.mk-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: .9rem; }
.mk-card { position: relative; overflow: hidden; border-radius: 14px; background: #fff; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(15,23,42,.05); }
.mk-card::before { content: ""; position: absolute; inset: 0; border-radius: 14px; box-shadow: inset 0 0 0 1px var(--ring); opacity: 0; transition: opacity .2s ease; pointer-events: none; }
.mk-card:hover::before { opacity: .7; }
.mk-bar { position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--bar), var(--bar)); }
.mk-ico { display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 10px; font-size: .85rem; flex-shrink: 0; }
.badge-sks { display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .55rem; border-radius: 9999px; font-size: 10px; font-weight: 700; line-height: 1; white-space: nowrap; }
.mk-act { opacity: 0; transform: translateY(4px); transition: opacity .15s ease, transform .15s ease; margin-top: .85rem; }
.mk-card:hover .mk-act, .mk-act:focus-within { opacity: 1; transform: none; }

/* ===== Chip filter SKS ===== */
.chip-sks { border-color: #e2e8f0; background: #fff; color: #64748b; }
.chip-sks:hover { border-color: #fdba74; color: #ea580c; }
.chip-sks.active { border-color: #f97316; background: #fff7ed; color: #ea580c; box-shadow: 0 0 0 1px #f97316; }

/* ===== Empty state ===== */
.empty-hidden { display: none; }
.empty-show { display: flex; }

/* ===== Toolbar ===== */
.toolbar { border-radius: 14px; border: 1px solid #e2e8f0; background: #fff; box-shadow: 0 1px 2px rgba(15,23,42,.05); }
</style>
<main class="content-area content-scroll">
  <section class="mb-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600"><i class="fas fa-user-graduate"></i></div>
        <div>
          <h1 class="text-xl font-bold tracking-tight text-slate-800">Matakuliah MBKM</h1>
          <p class="text-xs text-slate-500">Merdeka Belajar Kampus Merdeka &middot; matakuliah kesetaraan program MBKM.</p>
        </div>
      </div>
      <button type="button" id="btnTambah" class="rounded-lg bg-orange-500 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600"><i class="fas fa-plus mr-1"></i>Tambah Matakuliah</button>
    </div>
  </section>

  <section class="toolbar mb-4 p-3">
    <div class="flex flex-wrap items-center gap-2">
      <div class="relative min-w-[200px] flex-1"><i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i><input type="search" id="fCari" placeholder="Cari kode atau nama matakuliah..." class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none transition focus:border-orange-400 focus:bg-white"></div>
      <div class="flex flex-wrap items-center gap-1.5" id="chipContainer">
        <button type="button" class="chip-sks active rounded-full border px-3 py-1.5 text-xs font-semibold transition" data-sks="">Semua</button>
<button type="button" class="chip-sks rounded-full border px-3 py-1.5 text-xs font-semibold transition" data-sks="2">SKS 2</button>
  <button type="button" class="chip-sks rounded-full border px-3 py-1.5 text-xs font-semibold transition" data-sks="3">SKS 3</button>
  <button type="button" class="chip-sks rounded-full border px-3 py-1.5 text-xs font-semibold transition" data-sks="4">SKS 4</button>
  <button type="button" class="chip-sks rounded-full border px-3 py-1.5 text-xs font-semibold transition" data-sks="5">SKS 5</button>
        </div>
      <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
    </div>
  </section>

  <div id="emptyState" class="empty-hidden flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white py-14 text-center">
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400"><i class="fas fa-search text-lg"></i></div>
    <p class="mt-3 text-sm font-semibold text-slate-600">Tidak ada hasil</p>
    <p class="mt-1 text-xs text-slate-400" id="emptyStateDetail">Tidak ada matakuliah MBKM yang cocok dengan pencarian.</p>
    <button type="button" id="btnEmptyReset" class="mt-4 rounded-lg bg-slate-100 px-4 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset Filter</button>
  </div>

  <section class="mb-6">
    <div class="mb-3 flex items-center justify-between">
      <p class="text-xs font-medium text-slate-500"><i class="fas fa-list mr-1 text-orange-400"></i>Menampilkan <span id="countNow" class="font-bold text-slate-700">73</span> dari 73 matakuliah</p>
    </div>
    <div class="mk-grid" id="mkGrid">
<article class="mk-card lift reveal" data-sks="5" data-cari="eadkm60105 dasar pemrograman backend" style="--bar:#f59e0b;--ring:#fde68a">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM60105</code>
      <span class="badge-sks bg-amber-100 text-amber-700"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>5 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-amber-100 text-amber-500"><i class="fas fa-certificate"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Dasar Pemrograman Backend</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm60203 software engineering" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM60203</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Software Engineering</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm60303 sustainable start up" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM60303</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">sustainable Start Up</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm60403 sustainable tourism" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM60403</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Sustainable Tourism</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm60503 product management" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM60503</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Product Management</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm60603 integrasi membangun digital startup (ai creation dan fab creation)" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM60603</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Integrasi Membangun Digital Startup (AI Creation dan Fab Creation)</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="5" data-cari="eadkm60705 project capstone ai-infra" style="--bar:#f59e0b;--ring:#fde68a">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM60705</code>
      <span class="badge-sks bg-amber-100 text-amber-700"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>5 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-amber-100 text-amber-500"><i class="fas fa-certificate"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Project Capstone AI-Infra</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="4" data-cari="eadkm60804 capstone / final project" style="--bar:#8b5cf6;--ring:#ddd6fe">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM60804</code>
      <span class="badge-sks bg-violet-100 text-violet-700"><span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span>4 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-violet-100 text-violet-500"><i class="fas fa-brain"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Capstone / Final Project</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="4" data-cari="eadkm60904 soft skill &amp; career development" style="--bar:#8b5cf6;--ring:#ddd6fe">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM60904</code>
      <span class="badge-sks bg-violet-100 text-violet-700"><span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span>4 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-violet-100 text-violet-500"><i class="fas fa-brain"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Soft skill &amp; Career Development</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm61003 aplikasi back-end untuk pemula" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM61003</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Aplikasi Back-End untuk Pemula</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="4" data-cari="eadkm61104 basic frontend web development with html &amp; css" style="--bar:#8b5cf6;--ring:#ddd6fe">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM61104</code>
      <span class="badge-sks bg-violet-100 text-violet-700"><span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span>4 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-violet-100 text-violet-500"><i class="fas fa-brain"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Basic Frontend Web Development with HTML &amp; CSS</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm61202 database management &amp; technology" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM61202</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Database Management &amp; Technology</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm61303 profesionalisme insinyur" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM61303</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Profesionalisme Insinyur</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm61402 ekosistem industri elektronika" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM61402</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Ekosistem Industri Elektronika</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm61503 perancangan pcb" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM61503</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Perancangan PCB</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm61603 engineering design process" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM61603</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Engineering Design Process</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm61702 growth mindset overview" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM61702</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Growth Mindset Overview</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm61803 data collection device iot" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM61803</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Data Collection Device IoT</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm61902 teknik mikrokontroler wemos d1" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM61902</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Teknik Mikrokontroler Wemos D1</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm62002 teknik elektronika dan peralatan perbengkelan" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM62002</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Teknik Elektronika dan Peralatan Perbengkelan</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="4" data-cari="eadkm62104 proyek akhir iot smart device" style="--bar:#8b5cf6;--ring:#ddd6fe">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM62104</code>
      <span class="badge-sks bg-violet-100 text-violet-700"><span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span>4 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-violet-100 text-violet-500"><i class="fas fa-brain"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Proyek Akhir IoT Smart Device</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm62203 teknis kelistrikan" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM62203</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">teknis kelistrikan</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm62302 teknis kompresor dan pendingin" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM62302</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">teknis kompresor dan pendingin</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm62402 dasar teknik maintenance" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM62402</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Dasar Teknik Maintenance</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm62502 management waktu dan organisasi" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM62502</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Management Waktu dan Organisasi</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm62603 safety, health and enviroment" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM62603</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Safety, Health and Enviroment</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm62702 continuos improvement" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM62702</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Continuos Improvement</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm62802 corporate comprehension" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM62802</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Corporate Comprehension</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm62902 kemampuan teknis mill" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM62902</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Kemampuan Teknis Mill</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm63002 organization savvy" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM63002</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Organization Savvy</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm63103 microsoft office operating skill" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM63103</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Microsoft Office Operating Skill</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm63202 e-learning system abilty" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM63202</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">E-Learning System Abilty</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm63302 impact through imfluence" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM63302</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Impact Through Imfluence</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm63402 kemampuan kewirausahaan" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM63402</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Kemampuan Kewirausahaan</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm63502 drive for result" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM63502</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Drive For Result</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm63603 desain hld dan lld" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM63603</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Desain HLD dan LLD</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm63703 monitoring dan evaluasi i-hld" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM63703</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Monitoring dan Evaluasi I-HLD</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm63803 ekspor data design" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM63803</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Ekspor Data Design</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm63903 back-end development" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM63903</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Back-End Development</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm64003 dasar pemrograman javasript" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM64003</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Dasar Pemrograman  JavaSript</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm64102 pemrograman python" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM64102</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Pemrograman Python</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm64202 logika dan konsep teknologi ai" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM64202</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Logika dan Konsep Teknologi AI</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm64302 siklus proyek ai" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM64302</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Siklus Proyek AI</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm64402 chatgpt" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM64402</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">ChatGPT</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm64502 etika profesi &amp; keterampilan perusahaan" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM64502</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Etika Profesi &amp; Keterampilan Perusahaan</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm64603 proyek akhir" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM64603</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Proyek Akhir</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm64702 digital marketing" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM64702</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Digital Marketing</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm64802 inisiatif" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM64802</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">INISIATIF</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm64903 network architecture fundamentals" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM64903</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Network Architecture Fundamentals</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm65003 network problem management" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM65003</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Network Problem Management</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm65102 cybersecurity" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM65102</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Cybersecurity</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm65203 network operation and maintenance" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM65203</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Network Operation and Maintenance</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm65302 core network management" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM65302</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Core Network Management</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm65402 sikap" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM65402</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Sikap</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm65502 kemampuan komunikasi" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM65502</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Kemampuan Komunikasi</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm65602 orientasi hasil kerja" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM65602</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Orientasi Hasil Kerja</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm65702 berfikir analisis" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM65702</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Berfikir Analisis</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm65802 perencanaan dan pengorganisasian" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM65802</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Perencanaan dan Pengorganisasian</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm65903 monitoring dan mengevaluasi" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM65903</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Monitoring dan Mengevaluasi</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm66003 introduction to stress and strains" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM66003</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Introduction to Stress and Strains</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm66103 stress and local equilibrium" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM66103</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Stress and Local Equilibrium</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm66202 volumetric and deviatoric" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM66202</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Volumetric and Deviatoric</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm66302 linear and nonlinear analysis" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM66302</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Linear and Nonlinear Analysis</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm66402 time domain and frequency domain analysis" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM66402</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Time Domain and Frequency Domain Analysis</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm66502 thermal strain analysis" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM66502</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Thermal Strain Analysis</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm66603 renewable energy capstone project" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM66603</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Renewable Energy Capstone Project</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="4" data-cari="eadkm66704 konsep dasar dan solusi smart city" style="--bar:#8b5cf6;--ring:#ddd6fe">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM66704</code>
      <span class="badge-sks bg-violet-100 text-violet-700"><span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span>4 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-violet-100 text-violet-500"><i class="fas fa-brain"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Konsep Dasar dan Solusi Smart City</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="4" data-cari="eadkm66804 konsep pariwisata yang berkelanjutan" style="--bar:#8b5cf6;--ring:#ddd6fe">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM66804</code>
      <span class="badge-sks bg-violet-100 text-violet-700"><span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span>4 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-violet-100 text-violet-500"><i class="fas fa-brain"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Konsep Pariwisata yang Berkelanjutan</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm66903 ekosistem bisnis dan investasi" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM66903</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Ekosistem Bisnis dan Investasi</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm67003 strategi peningkatan kualitas branding kota" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM67003</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Strategi Peningkatan Kualitas Branding Kota</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="3" data-cari="eadkm67103 project management dan soft skill" style="--bar:#0ea5e9;--ring:#bae6fd">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM67103</code>
      <span class="badge-sks bg-sky-100 text-sky-700"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>3 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-sky-100 text-sky-500"><i class="fas fa-book-open"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Project Management dan Soft Skill</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm67202 kerja sama tim" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM67202</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Kerja Sama Tim</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
<article class="mk-card lift reveal" data-sks="2" data-cari="eadkm67302 radio access network management" style="--bar:#64748b;--ring:#cbd5e1">
  <div class="mk-bar"></div>
  <div class="p-4">
    <div class="flex items-start justify-between gap-2">
      <code class="font-mono text-[11px] font-bold tracking-wide text-slate-500">EADKM67302</code>
      <span class="badge-sks bg-slate-100 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>2 SKS</span>
    </div>
    <div class="mt-3 flex items-start gap-3">
      <span class="mk-ico bg-slate-100 text-slate-500"><i class="fas fa-layer-group"></i></span>
      <h3 class="text-[13px] font-semibold leading-snug text-slate-800">Radio Access Network Management</h3>
    </div>
    <div class="mk-act"><button type="button" class="btn-detail rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm transition hover:bg-slate-700"><i class="fas fa-eye mr-1 text-[10px]"></i>Detail</button></div>
  </div>
</article>
    </div>
    <p class="mt-3 text-[11px] text-slate-400"><i class="fas fa-lightbulb mr-1 text-amber-400"></i>Matakuliah kesetaraan program MBKM yang diakui dan dapat dikonversi SKS-nya.</p>
  </section>
</main>
<script>
(function () {
  function toast(msg) {
    var t = document.createElement("div");
    t.textContent = msg;
    t.style.cssText = "position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;background:#0f1f3d;color:#fff;padding:.6rem 1rem;border-radius:.6rem;font-size:.8rem;box-shadow:0 6px 18px rgba(15,23,42,.35);transition:opacity .3s ease;";
    document.body.appendChild(t);
    setTimeout(function () { t.style.opacity = "0"; setTimeout(function () { t.remove(); }, 300); }, 2400);
  }

  var fCari = document.getElementById("fCari");
  var chips = Array.prototype.slice.call(document.querySelectorAll(".chip-sks"));
  var cards = Array.prototype.slice.call(document.querySelectorAll(".mk-card"));
  var countNow = document.getElementById("countNow");
  var sksFilter = "";

  function filterAll() {
    var kata = (fCari && fCari.value || "").toLowerCase().trim();
    var totalVisible = 0;
    cards.forEach(function (c) {
      var mKata = !kata || c.getAttribute("data-cari").indexOf(kata) !== -1;
      var mSks = !sksFilter || c.getAttribute("data-sks") === sksFilter;
      c.style.display = (mKata && mSks) ? "" : "none";
      if (mKata && mSks) totalVisible++;
    });
    if (countNow) countNow.textContent = totalVisible;
    var emptyState = document.getElementById("emptyState");
    var detail = document.getElementById("emptyStateDetail");
    var hasFilter = !!kata || !!sksFilter;
    emptyState.classList.toggle("empty-show", totalVisible === 0 && hasFilter);
    emptyState.classList.toggle("empty-hidden", !(totalVisible === 0 && hasFilter));
    if (detail) {
      detail.textContent = sksFilter
        ? "Tidak ada matakuliah MBKM ber-SKS " + sksFilter + (kata ? " '" + kata + "'." : ".")
        : "Tidak ada matakuliah MBKM dengan kata '" + kata + "'. Coba kata lain atau reset filter.";
    }
  }

  if (fCari) fCari.addEventListener("input", filterAll);
  chips.forEach(function (ch) {
    ch.addEventListener("click", function () {
      chips.forEach(function (o) { o.classList.remove("active"); });
      ch.classList.add("active");
      sksFilter = ch.getAttribute("data-sks");
      filterAll();
    });
  });

  function resetFilters() {
    if (fCari) fCari.value = "";
    sksFilter = "";
    chips.forEach(function (o) { o.classList.remove("active"); });
    var x = document.querySelector(".chip-sks[data-sks=\"\"]");
    if (x) x.classList.add("active");
    filterAll();
  }
  var btnReset = document.getElementById("btnReset");
  var btnEmptyReset = document.getElementById("btnEmptyReset");
  if (btnReset) btnReset.addEventListener("click", resetFilters);
  if (btnEmptyReset) btnEmptyReset.addEventListener("click", resetFilters);

  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".btn-detail");
    if (!btn) return;
    var card = btn.closest(".mk-card");
    var kode = card.querySelector("code").textContent.trim();
    var nama = card.querySelector("h3").textContent.trim();
    var sks = card.getAttribute("data-sks");
    toast("Detail: " + kode + " - " + nama + " (" + sks + " SKS)");
  });

  var btnTambah = document.getElementById("btnTambah");
  if (btnTambah) btnTambah.addEventListener("click", function () { toast("Modul tambah matakuliah MBKM akan segera hadir"); });
})();
</script>