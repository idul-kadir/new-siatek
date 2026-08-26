<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
    @media (prefers-reduced-motion: reduce) { .reveal { animation: none; } }

    /* ===== Tombol bulat + tooltip ===== */
    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    /* ===== Baris jabatan (konsep daftar, responsif murni) ===== */
    .org-grid { display: grid; grid-template-columns: 1fr; gap: .8rem; }
    @media (min-width: 900px) { .org-grid { grid-template-columns: 1fr 1fr; } }
    .org-row { display: flex; align-items: center; gap: .85rem; border: 1px solid #e2e8f0; background: #fff; border-radius: 14px; padding: .85rem .95rem; box-shadow: 0 1px 2px rgba(15,23,42,.05); transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; cursor: pointer; }
    .org-row:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -12px rgba(15,23,42,.22); border-color: #c7d2fe; }
    .org-row--on { border-color: #818cf8; box-shadow: 0 0 0 3px rgba(129,140,248,.35); }
    .org-avatar { display: inline-flex; align-items: center; justify-content: center; width: 2.8rem; height: 2.8rem; border-radius: 14px; font-weight: 800; font-size: .85rem; flex-shrink: 0; box-shadow: inset 0 0 0 1px rgba(15,23,42,.06); }
    .org-jab { font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; display: flex; align-items: center; gap: .3rem; }
    .org-nama { font-size: .85rem; font-weight: 700; color: #1e293b; line-height: 1.3; }
    .org-nip { font-size: 11px; color: #94a3b8; margin-top: 2px; }

    /* ===== Kartu prodi ===== */
    .prodi-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
    .prodi-card { position: relative; overflow: hidden; border-radius: 16px; background: #fff; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(15,23,42,.05); transition: transform .2s ease, box-shadow .2s ease; }
    .prodi-card:hover { transform: translateY(-3px); box-shadow: 0 16px 32px -16px rgba(15,23,42,.25); }
    .prodi-card::after { content: ""; position: absolute; inset: 0; border-radius: 16px; box-shadow: inset 0 0 0 1px var(--pr); opacity: 0; transition: opacity .2s ease; pointer-events: none; }
    .prodi-card:hover::after { opacity: .6; }
    .prodi-banner { height: 74px; background: linear-gradient(135deg, var(--b1), var(--b2)); position: relative; }
    .prodi-banner .shape { position: absolute; border-radius: 9999px; background: rgba(255,255,255,.14); }
    .prodi-badge { position: absolute; top: .7rem; right: .7rem; display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .55rem; border-radius: 9999px; font-size: 10px; font-weight: 700; line-height: 1; }
    .prodi-avatar { position: absolute; left: 1.1rem; top: 3.1rem; display: inline-flex; align-items: center; justify-content: center; width: 2.9rem; height: 2.9rem; border-radius: 14px; font-weight: 800; font-size: .85rem; box-shadow: 0 4px 10px -2px rgba(15,23,42,.35); border: 3px solid #fff; }
    .badge-pro { display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .55rem; border-radius: 9999px; font-size: 10px; font-weight: 700; line-height: 1; white-space: nowrap; }

    /* ===== Modal & empty ===== */
    .modal-overlay { display: none; position: fixed; inset: 0; z-index: 90; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem; }
    .modal-overlay.show { display: flex; }
    .empty-hidden { display: none; }
    .empty-show { display: flex; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Organisasi</h1>
                    <p class="text-xs text-slate-500">Struktur kepemimpinan jurusan &amp; program studi — klik baris untuk mengganti pejabat.</p>
                </div>
            </div>
            <button type="button" id="btnTambah" class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600">
                <i class="fas fa-plus text-sm"></i>
                <span class="tip">Tambah Prodi</span>
            </button>
        </div>
    </section>

    <!-- ===== Pejabat Struktural ===== -->
    <section class="mb-6">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800"><i class="fas fa-user-tie mr-1.5 text-orange-500"></i>Pejabat Struktural</h2>
            <p class="text-xs font-medium text-slate-500"><i class="fas fa-id-badge mr-1 text-orange-400"></i><span class="font-bold text-slate-700">7</span> jabatan</p>
        </div>
        <div class="org-grid" id="orgList">
            <div class="org-row reveal" data-cari="ketua jurusan yasin mohamad 197102222001121001" data-jabatan="Ketua Jurusan" data-nama="Yasin Mohamad, ST., MT." data-nip="197102222001121001">
                <span class="org-avatar bg-gradient-to-br from-orange-500 to-amber-500 text-white">YM</span>
                <div class="min-w-0 flex-1">
                    <p class="org-jab text-orange-500"><i class="fas fa-crown"></i>Ketua Jurusan</p>
                    <p class="org-nama">Yasin Mohamad, ST., MT.</p>
                    <p class="org-nip">NIP 197102222001121001</p>
                </div>
                <span class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Ubah Pejabat</span></span>
            </div>
            <div class="org-row reveal" data-cari="sekretaris jurusan amirudin yunus dako 197410032001121001" data-jabatan="Sekretaris Jurusan" data-nama="Amirudin Yunus Dako, ST., M.Eng." data-nip="197410032001121001">
                <span class="org-avatar bg-sky-100 text-sky-600">AD</span>
                <div class="min-w-0 flex-1">
                    <p class="org-jab text-sky-500"><i class="fas fa-pen-nib"></i>Sekretaris Jurusan</p>
                    <p class="org-nama">Amirudin Yunus Dako, ST., M.Eng.</p>
                    <p class="org-nip">NIP 197410032001121001</p>
                </div>
                <span class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Ubah Pejabat</span></span>
            </div>
            <div class="org-row reveal" data-cari="kepala laboratorium iskandar z nasibu 197011052001121001" data-jabatan="Kepala Laboratorium" data-nama="Iskandar Z. Nasibu, S.Pd, M.Eng." data-nip="197011052001121001">
                <span class="org-avatar bg-violet-100 text-violet-600">IN</span>
                <div class="min-w-0 flex-1">
                    <p class="org-jab text-violet-500"><i class="fas fa-flask"></i>Kepala Laboratorium</p>
                    <p class="org-nama">Iskandar Z. Nasibu, S.Pd, M.Eng.</p>
                    <p class="org-nip">NIP 197011052001121001</p>
                </div>
                <span class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Ubah Pejabat</span></span>
            </div>
            <div class="org-row reveal" data-cari="penjamin mutu zainudin bonok 196704212003121001" data-jabatan="Penjamin Mutu" data-nama="Zainudin Bonok, ST., MT." data-nip="196704212003121001">
                <span class="org-avatar bg-emerald-100 text-emerald-600">ZB</span>
                <div class="min-w-0 flex-1">
                    <p class="org-jab text-emerald-500"><i class="fas fa-shield-halved"></i>Penjamin Mutu</p>
                    <p class="org-nama">Zainudin Bonok, ST., MT.</p>
                    <p class="org-nip">NIP 196704212003121001</p>
                </div>
                <span class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Ubah Pejabat</span></span>
            </div>
            <div class="org-row reveal" data-cari="ketua pengelola jurnal syahrir abdussamad 197506242005011003" data-jabatan="Ketua Pengelola Jurnal" data-nama="Syahrir Abdussamad, ST., MT." data-nip="197506242005011003">
                <span class="org-avatar bg-indigo-100 text-indigo-600">SA</span>
                <div class="min-w-0 flex-1">
                    <p class="org-jab text-indigo-500"><i class="fas fa-book-open"></i>Ketua Pengelola Jurnal</p>
                    <p class="org-nama">Syahrir Abdussamad, ST., MT.</p>
                    <p class="org-nip">NIP 197506242005011003</p>
                </div>
                <span class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Ubah Pejabat</span></span>
            </div>
            <div class="org-row reveal" data-cari="senat jurusan lanto m kamil amali 197704042001121001" data-jabatan="Senat Jurusan" data-nama="Dr. Lanto M. Kamil Amali, ST., MT." data-nip="197704042001121001">
                <span class="org-avatar bg-amber-100 text-amber-600">LA</span>
                <div class="min-w-0 flex-1">
                    <p class="org-jab text-amber-500"><i class="fas fa-landmark"></i>Senat Jurusan</p>
                    <p class="org-nama">Dr. Lanto M. Kamil Amali, ST., MT.</p>
                    <p class="org-nip">NIP 197704042001121001</p>
                </div>
                <span class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Ubah Pejabat</span></span>
            </div>
            <div class="org-row reveal" data-cari="pengelola jurnal pengabdian jumiati ilham 197510172005012001" data-jabatan="Pengelola Jurnal Pengabdian" data-nama="Jumiati Ilham, ST., MT." data-nip="197510172005012001">
                <span class="org-avatar bg-teal-100 text-teal-600">JI</span>
                <div class="min-w-0 flex-1">
                    <p class="org-jab text-teal-500"><i class="fas fa-hand-holding-heart"></i>Pengelola Jurnal Pengabdian</p>
                    <p class="org-nama">Jumiati Ilham, ST., MT.</p>
                    <p class="org-nip">NIP 197510172005012001</p>
                </div>
                <span class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Ubah Pejabat</span></span>
            </div>
        </div>
        <p class="mt-3 text-[11px] text-slate-400"><i class="fas fa-lightbulb mr-1 text-amber-400"></i>Klik baris jabatan atau ikon pensil untuk mengganti pejabat yang menjabat.</p>
    </section>

    <!-- ===== Program Studi ===== -->
    <section class="mb-6">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800"><i class="fas fa-building-columns mr-1.5 text-orange-500"></i>Program Studi</h2>
            <p class="text-xs font-medium text-slate-500"><i class="fas fa-graduation-cap mr-1 text-orange-400"></i><span class="font-bold text-slate-700">3</span> prodi aktif</p>
        </div>
        <div class="prodi-grid" id="prodiGrid">
            <article class="prodi-card reveal" data-cari="s1 pendidikan vokasional rekayasa elektro dr bambang panji asmara" style="--pr:#6366f1;--b1:#4f46e5;--b2:#818cf8">
                <div class="prodi-banner">
                    <span class="shape" style="width:70px;height:70px;top:-24px;right:-16px"></span>
                    <span class="shape" style="width:40px;height:40px;bottom:-14px;left:52%"></span>
                    <span class="badge-pro bg-white/20 text-white backdrop-blur-sm"><i class="fas fa-circle text-[6px]"></i>Aktif</span>
                </div>
                <span class="prodi-avatar bg-white text-indigo-600">VOK</span>
                <div class="px-4 pb-4 pt-6">
                    <h3 class="text-sm font-bold leading-snug text-slate-800">S1 Pendidikan Vokasional Rekayasa Elektro</h3>
                    <code class="mt-1 block text-[10px] text-slate-400">s1pendidikanvokasionalrekayasaelektro</code>
                    <div class="mt-3 flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                        <div class="min-w-0"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Kaprodi</p><p class="truncate text-xs font-semibold text-slate-700">Dr. Bambang Panji Asmara, ST., MT.</p></div>
                        <span class="badge-pro bg-sky-100 text-sky-700 shrink-0">S1</span>
                    </div>
                    <div class="mt-3 flex items-center justify-end gap-1.5">
                        <button type="button" class="btn-aksi btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                        <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                        <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                    </div>
                </div>
            </article>
            <article class="prodi-card reveal" data-cari="s1 teknik elektro ifan wiranto" style="--pr:#f43f5e;--b1:#e11d48;--b2:#fb7185">
                <div class="prodi-banner">
                    <span class="shape" style="width:70px;height:70px;top:-24px;right:-16px"></span>
                    <span class="shape" style="width:40px;height:40px;bottom:-14px;left:52%"></span>
                    <span class="badge-pro bg-white/20 text-white backdrop-blur-sm"><i class="fas fa-circle text-[6px]"></i>Aktif</span>
                </div>
                <span class="prodi-avatar bg-white text-rose-600">TE</span>
                <div class="px-4 pb-4 pt-6">
                    <h3 class="text-sm font-bold leading-snug text-slate-800">S1 Teknik Elektro</h3>
                    <code class="mt-1 block text-[10px] text-slate-400">s1teknikelektro</code>
                    <div class="mt-3 flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                        <div class="min-w-0"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Kaprodi</p><p class="truncate text-xs font-semibold text-slate-700">Ifan Wiranto, ST., MT.</p></div>
                        <span class="badge-pro bg-sky-100 text-sky-700 shrink-0">S1</span>
                    </div>
                    <div class="mt-3 flex items-center justify-end gap-1.5">
                        <button type="button" class="btn-aksi btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                        <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                        <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                    </div>
                </div>
            </article>
            <article class="prodi-card reveal" data-cari="s1 teknik komputer syahrir abdussamad" style="--pr:#14b8a6;--b1:#0d9488;--b2:#2dd4bf">
                <div class="prodi-banner">
                    <span class="shape" style="width:70px;height:70px;top:-24px;right:-16px"></span>
                    <span class="shape" style="width:40px;height:40px;bottom:-14px;left:52%"></span>
                    <span class="badge-pro bg-white/20 text-white backdrop-blur-sm"><i class="fas fa-circle text-[6px]"></i>Aktif</span>
                </div>
                <span class="prodi-avatar bg-white text-teal-600">TK</span>
                <div class="px-4 pb-4 pt-6">
                    <h3 class="text-sm font-bold leading-snug text-slate-800">S1 Teknik Komputer</h3>
                    <code class="mt-1 block text-[10px] text-slate-400">s1teknikkomputer</code>
                    <div class="mt-3 flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                        <div class="min-w-0"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Kaprodi</p><p class="truncate text-xs font-semibold text-slate-700">Syahrir Abdussamad, ST., MT.</p></div>
                        <span class="badge-pro bg-sky-100 text-sky-700 shrink-0">S1</span>
                    </div>
                    <div class="mt-3 flex items-center justify-end gap-1.5">
                        <button type="button" class="btn-aksi btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>
                        <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                        <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                    </div>
                </div>
            </article>
        </div>
        <p class="mt-3 text-[11px] text-slate-400"><i class="fas fa-lightbulb mr-1 text-amber-400"></i>Kaprodi setiap prodi dikelola lewat ikon pensil pada kartu.</p>
    </section>
</main>

<!-- ===== Modal Tambah Prodi ===== -->
<div class="modal-overlay" id="tambahModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-plus mr-1 text-orange-500"></i>Tambah Program Studi</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-tambah-close>&times;</button>
        </div>
        <form id="frmTambah" class="p-5 space-y-3">
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Nama Program Studi</label>
                <input type="text" id="tpNama" required placeholder="mis. S1 Teknik Informatika" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"></div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kode</label>
                <input type="text" id="tpKode" required placeholder="mis. s1teknikinformatika" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kaprodi</label>
                    <select id="tpKaprodi" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>Dr. Bambang Panji Asmara, ST., MT.</option>
                        <option>Ifan Wiranto, ST., MT.</option>
                        <option>Syahrir Abdussamad, ST., MT.</option>
                    </select></div>
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Jenjang</label>
                    <select id="tpJenjang" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>S1</option><option>D3</option><option>D4</option><option>Profesi</option>
                    </select></div>
            </div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Status</label>
                <select id="tpStatus" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select></div>
            <div class="flex justify-end gap-2 pt-1 border-t border-slate-100">
                <button type="button" data-tambah-close class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium transition"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Modal Edit Prodi ===== -->
<div class="modal-overlay" id="editModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-pen mr-1 text-sky-500"></i>Edit Program Studi</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-edit-close>&times;</button>
        </div>
        <form id="frmEdit" class="p-5 space-y-3">
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Nama Program Studi</label>
                <input type="text" id="epNama" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"></div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kode</label>
                <input type="text" id="epKode" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kaprodi</label>
                    <select id="epKaprodi" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>Dr. Bambang Panji Asmara, ST., MT.</option>
                        <option>Ifan Wiranto, ST., MT.</option>
                        <option>Syahrir Abdussamad, ST., MT.</option>
                    </select></div>
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Jenjang</label>
                    <select id="epJenjang" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>S1</option><option>D3</option><option>D4</option><option>Profesi</option>
                    </select></div>
            </div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Status</label>
                <select id="epStatus" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select></div>
            <div class="flex justify-end gap-2 pt-1 border-t border-slate-100">
                <button type="button" data-edit-close class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium transition"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Modal Ubah Pejabat ===== -->
<div class="modal-overlay" id="pengurusModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-user-tie mr-1 text-sky-500"></i>Ubah Pejabat <span id="pengJabatan" class="text-slate-400 font-normal"></span></h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-peng-close>&times;</button>
        </div>
        <form id="frmPengurus" class="p-5 space-y-3">
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Pilih Dosen</label>
                <select id="pengDosen" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                    <option>Yasin Mohamad, ST., MT. — NIP 197102222001121001</option>
                    <option>Amirudin Yunus Dako, ST., M.Eng. — NIP 197410032001121001</option>
                    <option>Iskandar Z. Nasibu, S.Pd, M.Eng. — NIP 197011052001121001</option>
                    <option>Zainudin Bonok, ST., MT. — NIP 196704212003121001</option>
                    <option>Dr. Bambang Panji Asmara, ST., MT. — NIP 197004052009121001</option>
                    <option>Ifan Wiranto, ST., MT. — NIP 197201282005011003</option>
                    <option>Syahrir Abdussamad, ST., MT. — NIP 197506242005011003</option>
                    <option>Dr. Lanto M. Kamil Amali, ST., MT. — NIP 197704042001121001</option>
                    <option>Jumiati Ilham, ST., MT. — NIP 197510172005012001</option>
                </select></div>
            <div class="flex justify-end gap-2 pt-1 border-t border-slate-100">
                <button type="button" data-peng-close class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium transition"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
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

    /* ===== Modal helpers ===== */
    function bindClose(modal, attr) {
        modal.querySelectorAll('[' + attr + ']').forEach(function (b) {
            b.addEventListener('click', function () { modal.classList.remove('show'); });
        });
        modal.addEventListener('click', function (e) { if (e.target === modal) modal.classList.remove('show'); });
    }
    var tm = document.getElementById('tambahModal');
    var em = document.getElementById('editModal');
    bindClose(tm, 'data-tambah-close');
    bindClose(em, 'data-edit-close');

    var btnTambah = document.getElementById('btnTambah');
    if (btnTambah) btnTambah.addEventListener('click', function () {
        document.getElementById('frmTambah').reset();
        tm.classList.add('show');
        document.getElementById('tpNama').focus();
    });

    /* ===== Baris jabatan: klik → ubah pejabat ===== */
    var pengTarget = null;
    var pm = document.getElementById('pengurusModal');

    function openPeng(row) {
        if (pengTarget) pengTarget.classList.remove('org-row--on');
        pengTarget = row;
        row.classList.add('org-row--on');
        var jabatan = row.getAttribute('data-jabatan');
        var nama = row.getAttribute('data-nama');
        document.getElementById('pengJabatan').textContent = '— ' + jabatan;
        var sel = document.getElementById('pengDosen');
        for (var i = 0; i < sel.options.length; i++) {
            if (sel.options[i].textContent.trim().indexOf(nama) !== -1) { sel.selectedIndex = i; break; }
        }
        pm.classList.add('show');
    }

    document.getElementById('orgList').addEventListener('click', function (e) {
        var row = e.target.closest('.org-row');
        if (!row) return;
        openPeng(row);
    });

    var frmPengurus = document.getElementById('frmPengurus');
    if (frmPengurus) frmPengurus.addEventListener('submit', function (e) {
        e.preventDefault();
        var val = document.getElementById('pengDosen').value;
        var nama = val.split(' — ')[0];
        var nip = val.split(' — ')[1] || '';
        var jabatan = '';
        if (pengTarget) {
            jabatan = pengTarget.getAttribute('data-jabatan');
            pengTarget.setAttribute('data-nama', nama);
            pengTarget.setAttribute('data-nip', nip);
            pengTarget.querySelector('.org-nama').textContent = nama;
            pengTarget.querySelector('.org-nip').textContent = 'NIP ' + nip;
            pengTarget.querySelector('.org-avatar').textContent = nama.split(' ').map(function (w) {
                return w.charAt(0);
            }).slice(0, 2).join('').toUpperCase();
            pengTarget.classList.remove('org-row--on');
        }
        pm.classList.remove('show');
        toast('Pejabat ' + jabatan + ' diubah menjadi ' + nama);
    });

    pm.querySelectorAll('[data-peng-close]').forEach(function (b) {
        b.addEventListener('click', function () { pm.classList.remove('show'); });
    });
    pm.addEventListener('click', function (e) { if (e.target === pm) pm.classList.remove('show'); });

    /* ===== Aksi kartu prodi ===== */
    var editTarget = null;
    document.getElementById('prodiGrid').addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-circle');
        if (!btn) return;
        var card = btn.closest('.prodi-card');
        var nama = card.querySelector('h3').textContent.trim();
        if (btn.classList.contains('btn-aksi')) {
            toast('Detail program studi "' + nama + '"');
        } else if (btn.classList.contains('btn-edit')) {
            editTarget = card;
            var kode = card.querySelector('code').textContent.trim();
            var kaprodi = card.querySelector('.truncate').textContent.trim();
            var jenjang = card.querySelector('.badge-pro.bg-sky-100').textContent.trim();
            var status = card.querySelector('.prodi-banner .badge-pro').textContent.trim();
            document.getElementById('epNama').value = nama;
            document.getElementById('epKode').value = kode;
            document.getElementById('epJenjang').value = jenjang;
            var kapSel = document.getElementById('epKaprodi');
            for (var i = 0; i < kapSel.options.length; i++) {
                if (kapSel.options[i].textContent.trim() === kaprodi) { kapSel.selectedIndex = i; break; }
            }
            var stSel = document.getElementById('epStatus');
            for (var j = 0; j < stSel.options.length; j++) {
                if (stSel.options[j].value.toLowerCase() === status.toLowerCase()) { stSel.selectedIndex = j; break; }
            }
            em.classList.add('show');
        } else if (btn.classList.contains('btn-hapus')) {
            if (confirm('Hapus program studi "' + nama + '"?')) {
                card.remove();
                toast('Program studi "' + nama + '" dihapus');
            }
        }
    });

    /* ===== Submit Tambah ===== */
    var frmTambah = document.getElementById('frmTambah');
    if (frmTambah) frmTambah.addEventListener('submit', function (e) {
        e.preventDefault();
        var nama = document.getElementById('tpNama').value.trim();
        var kode = document.getElementById('tpKode').value.trim();
        var kaprodi = document.getElementById('tpKaprodi').value;
        var jenjang = document.getElementById('tpJenjang').value;
        var status = document.getElementById('tpStatus').value;
        var gold = ['#eab308', '#f59e0b', '#f97316'];
        var g = gold[Math.floor(Math.random() * gold.length)];
        var art = document.createElement('article');
        art.className = 'prodi-card reveal';
        art.setAttribute('data-cari', (nama + ' ' + kaprodi).toLowerCase());
        art.style.cssText = '--pr:' + g + ';--b1:' + g + ';--b2:' + '#fbbf24';
        art.innerHTML = '<div class="prodi-banner">' +
            '<span class="shape" style="width:70px;height:70px;top:-24px;right:-16px"></span>' +
            '<span class="shape" style="width:40px;height:40px;bottom:-14px;left:52%"></span>' +
            '<span class="badge-pro bg-white/20 text-white backdrop-blur-sm"><i class="fas fa-circle text-[6px]"></i>' + status + '</span></div>' +
            '<span class="prodi-avatar bg-white text-slate-500">NEW</span>' +
            '<div class="px-4 pb-4 pt-6"><h3 class="text-sm font-bold leading-snug text-slate-800">' + nama + '</h3>' +
            '<code class="mt-1 block text-[10px] text-slate-400">' + kode + '</code>' +
            '<div class="mt-3 flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">' +
            '<div class="min-w-0"><p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Kaprodi</p><p class="truncate text-xs font-semibold text-slate-700">' + kaprodi + '</p></div>' +
            '<span class="badge-pro bg-sky-100 text-sky-700 shrink-0">' + jenjang + '</span></div>' +
            '<div class="mt-3 flex items-center justify-end gap-1.5">' +
            '<button type="button" class="btn-aksi btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>' +
            '<button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>' +
            '<button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button></div></div>';
        document.getElementById('prodiGrid').appendChild(art);
        tm.classList.remove('show');
        toast('Program studi "' + nama + '" ditambahkan');
        frmTambah.reset();
    });

    /* ===== Submit Edit ===== */
    var frmEdit = document.getElementById('frmEdit');
    if (frmEdit) frmEdit.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!editTarget) { em.classList.remove('show'); return; }
        var nama = document.getElementById('epNama').value.trim();
        var kode = document.getElementById('epKode').value.trim();
        var kaprodi = document.getElementById('epKaprodi').value;
        var jenjang = document.getElementById('epJenjang').value;
        var status = document.getElementById('epStatus').value;
        editTarget.setAttribute('data-cari', (nama + ' ' + kaprodi).toLowerCase());
        editTarget.querySelector('h3').textContent = nama;
        editTarget.querySelector('code').textContent = kode;
        editTarget.querySelector('.truncate').textContent = kaprodi;
        editTarget.querySelector('.badge-pro.bg-sky-100').textContent = jenjang;
        var st = editTarget.querySelector('.prodi-banner .badge-pro');
        st.innerHTML = '<i class="fas fa-circle text-[6px]"></i>' + status;
        em.classList.remove('show');
        toast('Program studi "' + nama + '" diperbarui');
    });
})();
</script>