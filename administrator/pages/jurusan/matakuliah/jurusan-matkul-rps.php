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

    /* ===== Loading spinner ===== */
    .is-loading { opacity: 0.7; cursor: not-allowed !important; pointer-events: none; }
    .spinner-slate { width: 1rem; height: 1rem; border: 2px solid rgba(15,23,42,0.2); border-top-color: #1a365d; border-radius: 50%; animation: spin 0.7s linear infinite; display: inline-block; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ===== Toast ===== */
    #toastContainer { position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: .5rem; pointer-events: none; }
    .toast-item { display: flex; align-items: center; gap: .6rem; min-width: 240px; background: #0f172a; color: #fff; padding: .6rem 1rem; border-radius: .6rem; font-size: .8rem; box-shadow: 0 6px 18px rgba(15,23,42,.35); transition: transform .3s ease, opacity .3s ease; pointer-events: auto; max-height: 120px; overflow: hidden; position: relative; animation: toastSlideIn .3s ease forwards; }
    .toast-item.toast-success { background: linear-gradient(135deg, #10b981, #059669); }
    .toast-item.toast-error { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .toast-item.toast-info { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .toast-close { position: absolute; top: .4rem; right: .4rem; background: rgba(255,255,255,0.2); border: none; color: #fff; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px; line-height: 1; }
    .toast-close:hover { background: rgba(255,255,255,0.3); }
    @keyframes toastSlideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    .toast-item.hide { animation: toastSlideOut .3s ease forwards; }
    @keyframes toastSlideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
</style>
<main class="content-area content-scroll">
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600"><i class="fas fa-book"></i></div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Matakuliah / RPS</h1>
                    <p class="text-xs text-slate-500" id="staticSubtitle">Memuat data...</p>
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
                    <h2 class="mt-3 text-xl lg:text-2xl font-bold tracking-tight" id="hdrKurikulumAktif">Memuat...</h2>
                    <p class="mt-1 max-w-lg text-sm text-slate-300">Jumlah RPS matakuliah pada kurikulum terbaru setiap program studi, dirinci per semester ganjil dan genap.</p>
                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-3" id="cardProdiContainer">
                    </div>
                </div>
                <div class="flex flex-col items-center justify-center lg:w-[230px]">
                    <p class="mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-300">Jumlah RPS per Prodi</p>
                    <div class="relative"><div style="width:140px;height:140px;"><canvas id="donutChart"></canvas></div>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none"><p class="text-xl font-extrabold" id="donutTotal">0</p><p class="text-[10px] text-slate-400">RPS Aktif</p></div>
                    </div>
                    <div class="mt-3 space-y-1.5" id="legendContainer">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]"><i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i><input type="search" id="fCari" placeholder="Cari kode, nama matakuliah..." class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white"></div>
            <select id="fProdi" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400"><option value="">Semua Prodi</option>
               
            </select>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400"><option value="">Semua Kurikulum</option>
             
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
    <!-- Accordion container - akan diisi dinamis dari database -->
    <div id="accordionContainer"></div>
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
                        <option value="">Pilih Prodi</option>
                    </select></div>
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kurikulum</label>
                    <select id="tpTahun" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400" disabled>
                        <option value="">Pilih prodi dulu</option>
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
                <button type="submit" id="btnSimpanTambah" class="px-4 py-2 text-xs rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium transition">Simpan</button>
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
                        <option value="">Pilih Prodi</option>
                    </select></div>
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kurikulum</label>
                    <select id="epTahun" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400" disabled>
                        <option value="">Pilih prodi dulu</option>
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
                <button type="submit" id="btnSimpanEdit" class="px-4 py-2 text-xs rounded-lg bg-sky-500 hover:bg-sky-600 text-white font-medium transition">Simpan Perubahan</button>
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
    var tpProdi = document.getElementById('tpProdi');
    var tpTahun = document.getElementById('tpTahun');
    var epProdi = document.getElementById('epProdi');
    var epTahun = document.getElementById('epTahun');
    var frmTambah = document.getElementById('frmTambah');
    var frmEdit = document.getElementById('frmEdit');
    var PROSES_URL = base_url() + '/administrator/pages/jurusan/matakuliah/proses.php';
    function base_url() {
        var root = window.location.pathname.split('/administrator/')[0];
        return root.replace(/\/$/, '');
    }

    // ===== LOAD FILTER DROPDOWN DARI DATABASE =====
    function loadFilterDropdown() {
        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { action: 'ambil-filter-data' },
            dataType: 'json',
            success: function(res) {
                if (!res.status || !res.filters) return;
                // Group by prodi, collect unique years per prodi
                var prodiYears = {};
                var prodiNameToCode = {};
                res.filters.forEach(function(f) {
                    if (!prodiNameToCode[f.prodi]) prodiNameToCode[f.prodi] = f.prodi_kode;
                    if (!prodiYears[f.prodi]) prodiYears[f.prodi] = [];
                    if (prodiYears[f.prodi].indexOf(f.tahun) === -1) {
                        prodiYears[f.prodi].push(f.tahun);
                    }
                });
                // Sort years descending
                Object.keys(prodiYears).forEach(function(p) {
                    prodiYears[p].sort(function(a, b) { return b - a; });
                });
                // isi fProdi — value = nama prodi (untuk matching accordion data-prodi)
                if (fProdi) {
                    fProdi.innerHTML = '<option value="">Semua Prodi</option>';
                    var prodis = Object.keys(prodiYears).sort();
                    prodis.forEach(function(p) {
                        var opt = document.createElement('option');
                        opt.value = p;
                        opt.textContent = p;
                        fProdi.appendChild(opt);
                    });
                }
                // Isi fTahun dari data filter (sudah include kurikulum kode/nama)
                // pakai prodi pertama untuk inisialisasi
                window._prodiNameToCode = prodiNameToCode;
                if (fTahun && prodis.length > 0) {
                    var firstProdi = prodis[0];
                    var firstProdiFilters = res.filters.filter(function(f) { return f.prodi === firstProdi; });
                    fTahun.innerHTML = '<option value="">Semua Kurikulum</option>';
                    firstProdiFilters.forEach(function(f) {
                        var opt = document.createElement('option');
                        opt.value = f.kurikulum_kode;
                        opt.textContent = f.kurikulum_nama ? (f.kurikulum_nama + ' - ' + f.tahun) : (f.kurikulum_kode + ' - ' + f.tahun);
                        fTahun.appendChild(opt);
                    });
                }
                // Load prodi list for modals
                loadProdiModal();
            },
            error: function() {
                toast('Gagal memuat filter dari database');
            }
        });
    }

    function loadProdiModal() {
        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { action: 'ambil-prodi-list' },
            dataType: 'json',
            success: function(res) {
                if (!res.status || !res.prodi) return;
                // isi tpProdi
                if (tpProdi) {
                    tpProdi.innerHTML = '<option value="">Pilih Prodi</option>';
                    res.prodi.forEach(function(p) {
                        var opt = document.createElement('option');
                        opt.value = p.nama;
                        opt.textContent = p.nama;
                        opt.setAttribute('data-kode', p.kode);
                        tpProdi.appendChild(opt);
                    });
                }
                // isi epProdi
                if (epProdi) {
                    epProdi.innerHTML = '<option value="">Pilih Prodi</option>';
                    res.prodi.forEach(function(p) {
                        var opt = document.createElement('option');
                        opt.value = p.nama;
                        opt.textContent = p.nama;
                        opt.setAttribute('data-kode', p.kode);
                        epProdi.appendChild(opt);
                    });
                }
            },
            error: function() {
                toast('Gagal memuat daftar prodi');
            }
        });
    }

    function populateKurikulum(prodiSelect, tahunSelect, done) {
        var prodiName = prodiSelect.value;
        if (!prodiName) {
            tahunSelect.innerHTML = '<option value="">Pilih prodi dulu</option>';
            tahunSelect.disabled = true;
            return;
        }
        var prodiCode = (window._prodiNameToCode && window._prodiNameToCode[prodiName]) || '';
        tahunSelect.innerHTML = '<option value="">Memuat...</option>';
        tahunSelect.disabled = true;
        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { action: 'ambil-kurikulum-by-prodi', prodi: prodiCode },
            dataType: 'json',
            success: function(res) {
                tahunSelect.innerHTML = '<option value="">Pilih Kurikulum</option>';
                if (res.status && res.kurikulum) {
                    res.kurikulum.forEach(function(k) {
                        var opt = document.createElement('option');
                        opt.value = k.kode;
                        opt.textContent = k.nama ? (k.nama + ' - ' + k.tahun) : (k.kode + ' - ' + k.tahun);
                        tahunSelect.appendChild(opt);
                    });
                    tahunSelect.disabled = false;
                    if (typeof done === 'function') done();
                }
            },
            error: function() {
                tahunSelect.innerHTML = '<option value="">Gagal memuat</option>';
            }
        });
    }

    if (tpProdi) tpProdi.addEventListener('change', function () { populateKurikulum(tpProdi, tpTahun); });
    if (epProdi) epProdi.addEventListener('change', function () { populateKurikulum(epProdi, epTahun); });

    // ===== LOAD DATA DARI DATABASE =====
    function loadData() {
        var container = document.getElementById('accordionContainer');
        if (container) container.innerHTML = '<div class="text-center py-12"><div class="spinner-slate mx-auto mb-3" style="width:2rem;height:2rem;border-width:3px;"></div><p class="text-sm text-slate-500">Memuat data matakuliah...</p></div>';
        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { action: 'ambil-data-matakuliah' },
            dataType: 'json',
            success: function(res) {
                if (!res.status) { toast('Gagal memuat data dari database'); return; }
                renderFromData(res.data);
            },
            error: function() {
                if (container) container.innerHTML = '<div class="text-center py-12 text-rose-500"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p class="text-sm">Terjadi kesalahan saat memuat data</p></div>';
                toast('Gagal memuat data dari database');
            }
        });
    }

    // Render accordion dinamis dari database
    function renderAccordion(data) {
        var container = document.getElementById('accordionContainer');
        if (!container) return;
        container.innerHTML = '';

        // Kelompokkan data per prodi
        var prodiGroups = {};
        var kurNamaMap = {}; // kurikulum_kode -> nama kurikulum
        data.forEach(function(mk) {
            if (!prodiGroups[mk.prodi]) prodiGroups[mk.prodi] = {};
            if (!prodiGroups[mk.prodi][mk.kurikulum]) prodiGroups[mk.prodi][mk.kurikulum] = [];
            kurNamaMap[mk.kurikulum] = mk.nama_kurikulum || mk.kurikulum;
            prodiGroups[mk.prodi][mk.kurikulum].push(mk);
        });

        // Urutkan prodi
        var prodiNames = Object.keys(prodiGroups).sort();

        prodiNames.forEach(function(prodi) {
            var kurikulums = Object.keys(prodiGroups[prodi]).sort(function(a, b) {
                // urutkan berdasarkan tahun kurikulum DESC
                var yearA = parseInt(prodiGroups[prodi][a][0] ? prodiGroups[prodi][a][0].tahun : 0) || 0;
                var yearB = parseInt(prodiGroups[prodi][b][0] ? prodiGroups[prodi][b][0].tahun : 0) || 0;
                return yearB - yearA;
            });
            var isVokasi = prodi.indexOf('Vokasi') !== -1 || prodi.indexOf('Pendidikan') !== -1;
            var isKomputer = prodi.indexOf('Komputer') !== -1;
            var icon = isVokasi ? 'fa-graduation-cap' : (isKomputer ? 'fa-microchip' : 'fa-bolt');
            var iconColor = isVokasi ? 'violet-100 text-violet-600' : (isKomputer ? 'sky-100 text-sky-600' : 'orange-100 text-orange-600');

            // Hitung total matakuliah dan SKS
            var totalMK = 0, totalSKS = 0;
            kurikulums.forEach(function(kk) {
                totalMK += prodiGroups[prodi][kk].length;
                prodiGroups[prodi][kk].forEach(function(m) { totalSKS += m.sks; });
            });

            // Buat section prodi
            var section = document.createElement('section');
            section.className = 'mb-6';
            section.setAttribute('data-prodi', prodi);
            section.innerHTML = '<div class="flex items-center gap-3 mb-3">' +
                '<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-' + iconColor + ' shadow-sm"><i class="fas ' + icon + ' text-sm"></i></div>' +
                '<div><h2 class="text-base font-bold text-slate-800">' + prodi + '</h2>' +
                '<p class="text-[11px] text-slate-500">' + kurikulums.length + ' kurikulum &middot; ' + totalMK + ' matakuliah &middot; ' + totalSKS + ' SKS</p></div></div>' +
                '<div class="space-y-4" id="acc-wrap-' + prodi.replace(/\s+/g, '-') + '"></div>';

            container.appendChild(section);
            var accWrap = section.querySelector('#acc-wrap-' + prodi.replace(/\s+/g, '-').replace(/[^a-zA-Z0-9-]/g, ''));
            // fallback jika id tidak ketemu (karakter aneh)
            if (!accWrap) accWrap = section.querySelector('.space-y-4');

            // Buat accordion per kurikulum (bukan per tahun)
            kurikulums.forEach(function(kk, idx) {
                var mkList = prodiGroups[prodi][kk];
                var tahunVal = mkList[0] ? mkList[0].tahun : '';
                var kurNama = kurNamaMap[kk] || kk;
                var isAktif = idx === 0; // kurikulum terbaru dianggap aktif
                var badge = isAktif ? '<span class="badge-aktif"><i class="fas fa-circle text-[5px]"></i>Aktif</span>' : '<span class="badge-jejak">Tidak Aktif</span>';
                var displayNama = kurNama + ' - ' + tahunVal;

                // Hitung per semester
                var semCounts = {};
                mkList.forEach(function(m) {
                    semCounts[m.semester] = (semCounts[m.semester] || 0) + 1;
                });

                var semPills = '';
                for (var s = 1; s <= 8; s++) {
                    semPills += '<span class="sem-pill" data-sem="' + s + '">Sem ' + s + ' (' + (semCounts[s] || 0) + ')</span>';
                }

                var accDiv = document.createElement('div');
                accDiv.className = 'acc-item acc-' + (isVokasi ? 'vok' : (isKomputer ? 'tk' : 'te')) + (isAktif ? ' is-active' : '') + ' reveal';
                accDiv.setAttribute('data-prodi', prodi);
                accDiv.setAttribute('data-tahun', tahunVal);
                accDiv.setAttribute('data-kurikulum', kk);
                accDiv.setAttribute('data-kurikulum-nama', kurNama);
                accDiv.innerHTML =
                    '<div class="acc-head" onclick="toggleAcc(this)">' +
                    '<span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-' + iconColor + ' text-lg"><i class="fas ' + icon + '"></i></span>' +
                    '<span class="min-w-0 flex-1"><span class="block text-sm font-bold text-slate-800">' + displayNama + ' ' + badge + '</span>' +
                    '<span class="block text-xs text-slate-500">Tahun ' + tahunVal + ' &middot; ' + mkList.length + ' matakuliah &middot; ' + mkList.reduce(function(a, b) { return a + b.sks; }, 0) + ' SKS</span></span>' +
                    '<span class="acc-badge">' + mkList.length + '</span>' +
                    '<span class="acc-chev"><i class="fas fa-chevron-down text-xs"></i></span></div>' +
                    '<div class="acc-body"><div class="acc-inner"><div class="sem-pills">' + semPills + '</div>' +
                    '<div class="border-t border-slate-100 bg-slate-50/60 p-4"><div class="overflow-hidden rounded-xl border border-slate-200 bg-white"><div class="tbl-wrap">' +
                    '<table class="tbl-modern"><thead><tr><th>Kode</th><th>Nama Matakuliah</th><th class="text-center">Sem</th><th class="text-center">SKS</th><th>Konsentrasi</th><th class="text-center">RPS</th><th class="text-right">Aksi</th></tr></thead>' +
                    '<tbody></tbody></table></div></div></div></div></div>';
                accWrap.appendChild(accDiv);
            });
        });

        // Render matakuliah ke tbody
        renderMatakuliah(data);
    }

    function renderMatakuliah(data) {
        // Render setiap matakuliah ke tbody yang sesuai
        data.forEach(function(mk) {
            var accItem = document.querySelector('.acc-item[data-prodi="' + mk.prodi + '"][data-kurikulum="' + mk.kurikulum + '"]');
            if (!accItem) return;
            var tb = accItem.querySelector('tbody');
            if (!tb) return;

            var sem = mk.semester;
            var semColor = ['bg-emerald-100 text-emerald-700','bg-sky-100 text-sky-700','bg-violet-100 text-violet-700','bg-amber-100 text-amber-700','bg-rose-100 text-rose-700','bg-teal-100 text-teal-700','bg-indigo-100 text-indigo-700','bg-pink-100 text-pink-700'];
            var konsBadge = mk.konsentrasi && mk.konsentrasi !== '-'
                ? '<span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">' + mk.konsentrasi + '</span>'
                : '<span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span>';
            var rpsStatus = mk.rps ? '1' : '0';
            var rpsLink = mk.rps
                ? '<a href="' + base_url() + '/file/' + mk.rps + '" target="_blank" class="rps-link mt-0.5 inline-flex items-center gap-1 text-[10px] font-semibold text-amber-600 hover:text-amber-700" data-rps-status="1"><i class="fas fa-file-pdf text-[9px]"></i>Lihat RPS</a>'
                : '<a href="javascript:void(0)" class="rps-link mt-0.5 inline-flex items-center gap-1 text-[10px] font-semibold text-slate-300 cursor-not-allowed" data-rps-status="0"><i class="fas fa-file text-[9px]"></i>RPS belum tersedia</a>';

            var cari = (mk.kode + ' ' + mk.nama + ' ' + (mk.konsentrasi || '')).toLowerCase();
            var tr = document.createElement('tr');
            tr.className = 'bg-white transition hover:bg-orange-50';
            tr.setAttribute('data-matkul-id', mk.id);
            tr.setAttribute('data-sem', sem);
            tr.setAttribute('data-cari', cari);
            tr.setAttribute('data-rps', rpsStatus);
            tr.setAttribute('data-kurikulum', mk.kurikulum);
            tr.innerHTML = '<td><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 font-mono">' + mk.kode + '</span></td>'
                + '<td><p class="font-medium text-slate-800 leading-snug">' + mk.nama + '</p></td>'
                + '<td class="text-center"><span class="inline-flex items-center rounded-full ' + semColor[(parseInt(sem) - 1) % 8] + ' px-2 py-0.5 text-xs font-bold">Sem ' + sem + '</span></td>'
                + '<td class="text-center"><span class="text-sm font-bold text-slate-700">' + mk.sks + '</span></td>'
                + '<td>' + konsBadge + '</td>'
                + '<td class="text-center">' + rpsLink + '</td>'
                + '<td class="text-right"><div class="flex items-center justify-end gap-1.5">'
                + '<button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>'
                + '<button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>'
                + '<button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>'
                + '</div></td>';
            tb.appendChild(tr);
        });
    }

    function renderFromData(data) {
        renderAccordion(data);
        updateHeaderCard(data);
        updateDonut(data);
        filterAll();
        document.querySelectorAll('.sem-pill').forEach(function(p) {
            if (p.getAttribute('data-sem') === '1') p.classList.add('active');
        });
        filterAll();
        
        // Update static subtitle
        var prodiSet = {};
        var kurSet = {};
        data.forEach(function(mk) {
            prodiSet[mk.prodi] = true;
            kurSet[mk.kurikulum] = true;
        });
        var sub = document.getElementById('staticSubtitle');
        if (sub) {
            sub.textContent = data.length + ' matakuliah \u00b7 ' + Object.keys(kurSet).length + ' kurikulum \u00b7 ' + Object.keys(prodiSet).length + ' program studi.';
        }
    }

    // Load data saat halaman siap
    $(document).ready(function() {
        loadFilterDropdown();
        loadData();
    });

    window.toggleAcc = function (head) { head.closest('.acc-item').classList.toggle('open'); };

    /* Donut chart " Chart.js dimuat setelah include halaman ini, jadi tunggu */
    var chartData = {
        labels: ["S1 Teknik Elektro","S1 Teknik Komputer","S1 Pendidikan Vokasi Rekayasa Elektro"],
        values: [0,0,0],
        colors: ["#f97316","#0ea5e9","#8b5cf6"]
    };
    function initDonut() {
        if (!window.Chart) { setTimeout(initDonut, 200); return; }
        var ctx = document.getElementById('donutChart'); if (!ctx) return;
        window._donutChart = new Chart(ctx, {
            type: 'doughnut',
            data: { labels: chartData.labels, datasets: [{ data: chartData.values, backgroundColor: chartData.colors, borderWidth: 3, borderColor: 'rgba(255,255,255,.15)', hoverOffset: 8 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%',
                plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 8, callbacks: { label: function (c) { return ' ' + c.label + ': ' + c.raw + ' RPS'; } } } } }
        });
    }
    initDonut();

    function updateDonut(data) {
        // Kelompokkan matakuliah per prodi + kurikulum, hanya ambil kurikulum terbaru (aktif)
        var prodiKur = {};
        var prodiList = {};
        data.forEach(function(mk) {
            if (!prodiKur[mk.prodi]) prodiKur[mk.prodi] = {};
            if (!prodiKur[mk.prodi][mk.kurikulum]) prodiKur[mk.prodi][mk.kurikulum] = [];
            prodiKur[mk.prodi][mk.kurikulum].push(mk);
            prodiList[mk.prodi] = true;
        });
        var counts = {};
        var totalRPS = 0;
        Object.keys(prodiKur).forEach(function(prodi) {
            var kurikulumArr = Object.keys(prodiKur[prodi]).sort(function(a, b) {
                var yA = parseInt(prodiKur[prodi][a][0] ? prodiKur[prodi][a][0].tahun : 0) || 0;
                var yB = parseInt(prodiKur[prodi][b][0] ? prodiKur[prodi][b][0].tahun : 0) || 0;
                return yB - yA;
            });
            var aktifKode = kurikulumArr[0];
            if (!aktifKode) return;
            prodiKur[prodi][aktifKode].forEach(function(mk) {
                if (!counts[prodi]) counts[prodi] = 0;
                var hasRps = mk.rps && mk.rps !== '' && mk.rps !== '0';
                if (hasRps) {
                    counts[prodi]++;
                    totalRPS++;
                }
            });
        });
        var prodiList = Object.keys(counts).sort();
        var colors = ["#f97316","#0ea5e9","#8b5cf6","#10b981","#ef4444","#f59e0b","#6366f1","#ec4899"];
        chartData.labels = prodiList;
        chartData.values = prodiList.map(function(p) { return counts[p]; });
        chartData.colors = prodiList.map(function(_, i) { return colors[i % colors.length]; });
        
        var totalEl = document.getElementById('donutTotal');
        if (totalEl) totalEl.textContent = totalRPS;
        
        var lc = document.getElementById('legendContainer');
        if (lc) {
            lc.innerHTML = '';
            prodiList.forEach(function(p, i) {
                var color = colors[i % colors.length];
                lc.innerHTML += '<div class="legend-item"><span class="legend-dot" style="background:' + color + '"></span><span class="truncate max-w-[130px]" title="' + p + '">' + p + '</span></div>';
            });
        }
        
        if (window._donutChart) {
            window._donutChart.data.labels = chartData.labels;
            window._donutChart.data.datasets[0].data = chartData.values;
            window._donutChart.data.datasets[0].backgroundColor = chartData.colors;
            window._donutChart.update();
        }
    }

    function updateHeaderCard(data) {
        var prodiGroups = {};
        var kurNamaMap = {};
        
        data.forEach(function(mk) {
            if (!prodiGroups[mk.prodi]) prodiGroups[mk.prodi] = {};
            if (!prodiGroups[mk.prodi][mk.kurikulum]) prodiGroups[mk.prodi][mk.kurikulum] = [];
            kurNamaMap[mk.kurikulum] = mk.nama_kurikulum || mk.kurikulum;
            prodiGroups[mk.prodi][mk.kurikulum].push(mk);
        });

        var prodiNames = Object.keys(prodiGroups).sort();
        var container = document.getElementById('cardProdiContainer');
        if (!container) return;
        container.innerHTML = '';

        var html = '';
        var colors = [
            { ic: 'fa-bolt', text: '#f97316' },
            { ic: 'fa-microchip', text: '#0ea5e9' },
            { ic: 'fa-graduation-cap', text: '#8b5cf6' },
            { ic: 'fa-cube', text: '#10b981' }
        ];

        var latestTahunGlobal = '';

        prodiNames.forEach(function(prodi, idx) {
            var c = colors[idx % colors.length];
            
            var kurikulums = Object.keys(prodiGroups[prodi]).sort(function(a, b) {
                var yearA = parseInt(prodiGroups[prodi][a][0] ? prodiGroups[prodi][a][0].tahun : 0) || 0;
                var yearB = parseInt(prodiGroups[prodi][b][0] ? prodiGroups[prodi][b][0].tahun : 0) || 0;
                return yearB - yearA;
            });
            
            var latestKurKode = kurikulums[0];
            if (!latestKurKode) return;
            
            var mkList = prodiGroups[prodi][latestKurKode];
            var tahunTerbaru = mkList[0] ? mkList[0].tahun : '';
            if (!latestTahunGlobal || tahunTerbaru > latestTahunGlobal) {
                latestTahunGlobal = tahunTerbaru;
            }
            
            var totalMK = mkList.length;
            var totalSKS = mkList.reduce(function(acc, m) { return acc + m.sks; }, 0);
            
            var totalRPS = 0, ganjilRPS = 0, genapRPS = 0, ganjilMK = 0, genapMK = 0;
            mkList.forEach(function(m) {
                var hasRps = m.rps && m.rps !== '' && m.rps !== '0';
                if (m.semester % 2 === 0) { genapMK++; if (hasRps) genapRPS++; }
                else { ganjilMK++; if (hasRps) ganjilRPS++; }
                if (hasRps) totalRPS++;
            });
            
            html += '<div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur-sm">' +
                    '<div class="flex items-center justify-between gap-2">' +
                        '<div class="flex items-center gap-2 min-w-0"><i class="fas ' + c.ic + ' text-sm shrink-0" style="color:' + c.text + '"></i><p class="truncate text-xs font-semibold text-slate-200" title="' + prodi + '">' + prodi + '</p></div>' +
                        '<span class="badge-aktif shrink-0">' + tahunTerbaru + '</span>' +
                    '</div>' +
                    '<div class="mt-3 flex items-end justify-between">' +
                        '<div><p class="text-2xl font-extrabold leading-none">' + totalRPS + '<span class="text-sm font-semibold text-slate-400 ml-1">/ ' + totalMK + '</span></p><p class="mt-1 text-[11px] text-slate-400">RPS / matakuliah</p></div>' +
                        '<p class="text-xs font-bold" style="color:' + c.text + '">' + totalSKS + ' SKS</p>' +
                    '</div>' +
                    '<div class="mt-3 grid grid-cols-2 gap-2">' +
                        '<div class="rounded-lg bg-white/5 px-2.5 py-1.5 ring-1 ring-white/10"><p class="text-[10px] text-slate-400"><i class="fas fa-sun mr-1"></i>Ganjil</p><p class="text-sm font-bold">' + ganjilRPS + '<span class="text-[10px] font-normal text-slate-400"> / ' + ganjilMK + ' MK</span></p></div>' +
                        '<div class="rounded-lg bg-white/5 px-2.5 py-1.5 ring-1 ring-white/10"><p class="text-[10px] text-slate-400"><i class="fas fa-moon mr-1"></i>Genap</p><p class="text-sm font-bold">' + genapRPS + '<span class="text-[10px] font-normal text-slate-400"> / ' + genapMK + ' MK</span></p></div>' +
                    '</div>' +
                '</div>';
        });
        
        container.innerHTML = html;
        var hdr = document.getElementById('hdrKurikulumAktif');
        if (hdr) hdr.textContent = 'Kurikulum Tahun ' + (latestTahunGlobal || 'Aktif');
    }

    /* Pill semester per accordion */
    document.addEventListener('click', function (e) {
        var pill = e.target.closest('.sem-pill');
        if (!pill) return;
        var wrap = pill.closest('.sem-pills');
        var accBody = wrap.closest('.acc-body');
        var semVal = pill.getAttribute('data-sem');
        wrap.querySelectorAll('.sem-pill').forEach(function (p) { p.classList.remove('active'); });
        pill.classList.add('active');
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
            var matchKur = !tahun || it.getAttribute('data-kurikulum') === tahun;
            var semVal = '';
            var activePill = it.querySelector('.sem-pill.active');
            if (activePill) semVal = activePill.getAttribute('data-sem');
            var trs = it.querySelectorAll('tbody tr');
            var visible = 0;
            for (var j = 0; j < trs.length; j++) {
                var tr = trs[j];
                if (tr.classList.contains('sem-row')) {
                    tr.style.display = 'none';
                    continue;
                }
                var matchKata = !kata || (tr.getAttribute('data-cari') || '').indexOf(kata) !== -1;
                var matchSem = !semVal || tr.getAttribute('data-sem') === semVal;
                var show = matchKata && matchSem;
                tr.style.display = show ? '' : 'none';
                if (show) visible++;
            }
            it.style.display = (!matchProdi || !matchKur) ? 'none' : '';
            if (matchProdi && matchKur) totalVisible += visible;
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
                var tahunLabel = tahun;
                if (tahun) {
                    var sel = fTahun.options[fTahun.selectedIndex];
                    if (sel) tahunLabel = sel.textContent;
                }
                if (kata) saran = 'Tidak ada matakuliah yang cocok dengan kata "' + kata + '".';
                else if (prodi && tahun) saran = tahunLabel + ' belum tersedia untuk ' + prodi + '.';
                else if (tahun) saran = 'Tidak ada kurikulum ' + tahunLabel + ' tersedia.';
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
        if (prodi) {
            var prodiCode = (window._prodiNameToCode && window._prodiNameToCode[prodi]) || prodi;
            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: { action: 'ambil-kurikulum-by-prodi', prodi: prodiCode },
                dataType: 'json',
                success: function(res) {
                    if (res.status && res.kurikulum) {
                    fTahun.innerHTML = '<option value="">Semua Kurikulum</option>';
                        res.kurikulum.forEach(function(k) {
                            var opt = document.createElement('option');
                            opt.value = k.kode;
                            opt.textContent = k.nama ? (k.nama + ' - ' + k.tahun) : (k.kode + ' - ' + k.tahun);
                            fTahun.appendChild(opt);
                        });
                        fTahun.value = '';
                    }
                }
            });
        } else {
            fTahun.innerHTML = '<option value="">Semua Kurikulum</option>';
        }
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
        document.querySelectorAll('.sem-pill').forEach(function (p) { p.classList.remove('active'); });
        document.querySelectorAll('.acc-item tbody tr').forEach(function (tr) { tr.style.display = ''; });
        filterAll();
        document.querySelectorAll('.acc-item.open').forEach(function (a) { a.classList.remove('open'); });
    }
    if (btnReset) btnReset.addEventListener('click', resetFilters);
    if (btnEmptyReset) btnEmptyReset.addEventListener('click', resetFilters);
    bindFilters();

    /* Toast Notification */
    var toastContainer = null;
    function toast(pesan, tipe) {
        tipe = tipe || 'info';
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            document.body.appendChild(toastContainer);
        }
        var item = document.createElement('div');
        item.className = 'toast-item toast-' + tipe;
        var ikon = tipe === 'success' ? 'fa-check-circle' : (tipe === 'error' ? 'fa-times-circle' : 'fa-info-circle');
        item.innerHTML = '<i class="fas ' + ikon + ' toast-icon"></i><span>' + pesan + '</span><button class="toast-close" onclick="this.parentElement.classList.add(\'hide\');setTimeout(function(){this.parentElement.remove()},350)">&times;</button>';
        toastContainer.appendChild(item);
        requestAnimationFrame(function(){ item.classList.add('show'); });
        setTimeout(function(){ item.classList.add('hide'); setTimeout(function(){ item.remove(); }, 350); }, 4000);
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
            var matkulId = tr.getAttribute('data-matkul-id');
            var namaMatkul = tr.querySelectorAll('td')[1] ? tr.querySelectorAll('td')[1].textContent.trim() : 'matakuliah ini';
            if (!matkulId) { toast('ID matakuliah tidak ditemukan', 'error'); return; }
            
            var doHapus = function() {
                $.ajax({
                    url: PROSES_URL,
                    method: 'POST',
                    data: { action: 'hapus-data-matakuliah', id: matkulId },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            tr.remove();
                            toast('Matakuliah "' + namaMatkul + '" berhasil dihapus', 'success');
                            loadData();
                        } else {
                            toast('Gagal menghapus: ' + (res.pesan || 'Unknown error'), 'error');
                        }
                    },
                    error: function() {
                        toast('Terjadi kesalahan saat menghapus', 'error');
                    }
                });
            };

            if (typeof konfirmasiHapus === 'function') {
                konfirmasiHapus(namaMatkul, doHapus);
            } else if (confirm('Hapus matakuliah "' + namaMatkul + '"?')) {
                doHapus();
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
        document.getElementById('dtTahun').textContent = accItem ? accItem.getAttribute('data-kurikulum-nama') || accItem.getAttribute('data-kurikulum') : '---';
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
            var kurikulumKode = editTarget.getAttribute('data-kurikulum');
            if (epProdi) {
                epProdi.value = prodi || '';
                populateKurikulum(epProdi, epTahun, function() {
                    var tahunSel = document.getElementById('epTahun');
                    for (var i = 0; i < tahunSel.options.length; i++) {
                        if (tahunSel.options[i].value === kurikulumKode) { tahunSel.selectedIndex = i; break; }
                    }
                });
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
    var btnSimpanTambah = document.getElementById('btnSimpanTambah');
    var frmEdit = document.getElementById('frmEdit');
    var btnSimpanEdit = document.getElementById('btnSimpanEdit');

    function setButtonLoading(btn, isLoading) {
        if (!btn) return;
        if (isLoading) {
            btn.classList.add('is-loading');
            btn.setAttribute('disabled', '');
            btn.setAttribute('data-text', btn.textContent);
            btn.innerHTML = '<span class="spinner-slate"></span> Memproses...';
        } else {
            btn.classList.remove('is-loading');
            btn.removeAttribute('disabled');
            btn.innerHTML = btn.getAttribute('data-text') || btn.textContent;
        }
    }

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
        setButtonLoading(btnSimpanTambah, true);

        // Buat FormData untuk upload file
        var formData = new FormData();
        formData.append('action', 'tambah-data-matakuliah');
        formData.append('kode', kode);
        formData.append('nama', nama);
        formData.append('semester', sem);
        formData.append('sks', sks);
        formData.append('kurikulum', tahun);
        formData.append('konsentrasi', kons);
        if (tpFile && tpFile.files.length) {
            formData.append('rps_file', tpFile.files[0]);
        }
        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    toast('Matakuliah "' + nama + '" berhasil ditambahkan', 'success');
                    tm.classList.remove('show');
                    frmTambah.reset();
                    document.getElementById('tpFileLabel').textContent = 'Klik untuk memilih file RPS';
                    loadData();
                } else {
                    toast('Gagal menambahkan: ' + (res.pesan || 'Unknown error'), 'error');
                }
            },
            error: function() {
                toast('Terjadi kesalahan saat menambahkan', 'error');
            },
            complete: function() {
                setButtonLoading(btnSimpanTambah, false);
            }
        });
    });

    if (frmEdit) frmEdit.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!editTarget) { em.classList.remove('show'); return; }

        setButtonLoading(btnSimpanEdit, true);

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
        formData.append('action', 'edit-data-matakuliah');
        formData.append('id', matkulId);
        formData.append('kode', kode);
        formData.append('nama', nama);
        formData.append('semester', sem);
        formData.append('sks', sks);
        formData.append('kurikulum', editTarget.getAttribute('data-kurikulum'));
        formData.append('konsentrasi', kons);
        if (epFile && epFile.files.length) {
            formData.append('rps_file', epFile.files[0]);
        }
        $.ajax({
            url: PROSES_URL,
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
                    tds[1].innerHTML = '<p class="font-medium text-slate-800 leading-snug">' + nama + '</p>';
                    var semColor = ['bg-emerald-100 text-emerald-700','bg-sky-100 text-sky-700','bg-violet-100 text-violet-700','bg-amber-100 text-amber-700','bg-rose-100 text-rose-700','bg-teal-100 text-teal-700','bg-indigo-100 text-indigo-700','bg-pink-100 text-pink-700'];
                    tds[2].innerHTML = '<span class="inline-flex items-center rounded-full ' + semColor[(parseInt(sem) - 1) % 8] + ' px-2 py-0.5 text-xs font-bold">Sem ' + sem + '</span>';
                    tds[3].innerHTML = '<span class="text-sm font-bold text-slate-700">' + sks + '</span>';
                    tds[4].innerHTML = kons
                        ? '<span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 px-2.5 py-1 text-[11px] font-bold">' + kons + '</span>'
                        : '<span class="inline-flex items-center rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-[11px] font-bold">&mdash;</span>';
                    tds[5].innerHTML = epFile && epFile.files.length
                        ? '<a href="' + base_url() + '/file/' + (res.rps || '') + '" target="_blank" class="rps-link inline-flex items-center gap-1 text-[10px] font-semibold text-amber-600 hover:text-amber-700" data-rps-status="1"><i class="fas fa-file-pdf text-[9px]"></i>Lihat RPS</a>'
                        : '<a href="javascript:void(0)" class="rps-link inline-flex items-center gap-1 text-[10px] font-semibold text-slate-300 cursor-not-allowed" data-rps-status="0"><i class="fas fa-file text-[9px]"></i>Belum ada</a>';
                    editTarget.setAttribute('data-sem', sem);
                    editTarget.setAttribute('data-cari', (kode + ' ' + nama + ' ' + kons).toLowerCase());

                    var uploadInfo = epFile && epFile.files.length ? ' dan RPS "' + epFile.files[0].name + '" ditautkan' : '';
                    toast('Matakuliah "' + nama + '" diperbarui' + uploadInfo, 'success');
                    editTarget = null;
                    em.classList.remove('show');
                } else {
                    toast('Gagal memperbarui: ' + (res.pesan || 'Unknown error'), 'error');
                }
            },
            error: function() {
                toast('Terjadi kesalahan saat memperbarui', 'error');
            },
            complete: function() {
                setButtonLoading(btnSimpanEdit, false);
            }
        });
    });
})();</script>

