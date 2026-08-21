<?php
/**
 * Halaman: Surat Penunjukkan (Jurusan)
 * Daftar surat penunjukkan dosen (pembimbing & penguji) skripsi tingkat jurusan,
 * beserta tombol untuk melihat/mencetak surat resmi A4.
 * Tampilan statis (dummy) â€” JS untuk tab, cari, filter tahun, pagination,
 * dan modal detail/modal pembuat surat. Data hardcoded di halaman (HTML).
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
    .tab-btn:hover { border-color: #fdba74; color: #c2410c; }
    .tab-btn .tdot { width: 8px; height: 8px; border-radius: 9999px; }
    .tab-btn .tnum { min-width: 22px; text-align: center; border-radius: 9999px; padding: 1px 6px; font-size: .7rem; background: #f1f5f9; color: #64748b; }
    .tab-btn[aria-selected="true"] { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }
    .tab-btn[aria-selected="true"] .tnum { background: rgba(255,255,255,.18); color: #fff; }

    .pt-badge { display: inline-block; padding: .18rem .55rem; border-radius: .45rem; font-size: 10px; font-weight: 700; white-space: nowrap; }

    .pin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .pin-table thead th { background: #f8fafc; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; text-align: left; padding: .6rem .85rem; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
    .pin-table tbody td { padding: .7rem .85rem; font-size: .78rem; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .pin-table tbody tr:last-child td { border-bottom: none; }
    .pin-table tbody tr { transition: background .15s ease; }
    .pin-table tbody tr:hover { background: #f8fafc; }

    .dosen-chip { display: inline-flex; align-items: center; gap: .5rem; max-width: 250px; padding: .28rem .5rem; border-radius: .6rem; border: 1px solid #e2e8f0; background: #f8fafc; }
    .dosen-chip .d-ava { display: inline-flex; align-items: center; justify-content: center; width: 1.6rem; height: 1.6rem; border-radius: 9999px; font-size: .62rem; font-weight: 700; flex-shrink: 0; }
    .dosen-chip .d-meta { min-width: 0; }
    .dosen-chip .d-name { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 600; font-size: .72rem; color: #334155; }
    .dosen-chip .d-sub { display: block; font-size: .63rem; color: #94a3b8; }

    .file-chip { display: inline-flex; align-items: center; gap: .5rem; min-width: 0; max-width: 200px; padding: .32rem .55rem; border-radius: .6rem; border: 1px solid #e2e8f0; background: #f8fafc; }
    .file-chip .f-ico { display: inline-flex; align-items: center; justify-content: center; width: 1.6rem; height: 1.6rem; border-radius: .45rem; font-size: .72rem; flex-shrink: 0; }
    .f-pdf   { background: #fee2e2; color: #dc2626; }
    .file-chip .f-meta { min-width: 0; }
    .file-chip .f-name { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 600; font-size: .72rem; color: #334155; }
    .file-chip .f-sub { display: block; font-size: .63rem; color: #94a3b8; }
    .f-down { display: inline-flex; align-items: center; justify-content: center; width: 1.6rem; height: 1.6rem; border-radius: .4rem; background: #fff; border: 1px solid #e2e8f0; color: #64748b; cursor: pointer; flex-shrink: 0; transition: all .15s ease; }
    .f-down:hover { background: #c2410c; border-color: #c2410c; color: #fff; }

    .pg-btn { min-width: 2rem; height: 2rem; border-radius: .5rem; padding: 0 .5rem; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: .75rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .pg-btn:hover { border-color: #fdba74; color: #c2410c; }
    .pg-btn:disabled { opacity: .45; cursor: not-allowed; }
    .pg-btn.on { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }

    .modal-overlay { display: none; position: fixed; inset: 0; z-index: 90; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem; }
    .modal-overlay.show { display: flex; }

    /* ===== Lembar surat resmi (preview on-screen) ===== */
    .letter-sheet { font-family: Georgia, 'Times New Roman', serif; color: #1e293b; }
    .letter-sheet .sheet { background: #fff; }
    .letter-sheet .kop { text-align: center; border-bottom: 3px double #334155; padding-bottom: 10px; }
    .letter-sheet .kop .l1 { font-size: 9px; letter-spacing: .3px; color: #334155; line-height: 1.5; }
    .letter-sheet .kop h2 { font-size: 19px; font-weight: 800; letter-spacing: .5px; margin: 4px 0 2px; }
    .letter-sheet .kop .fak { font-size: 11px; font-weight: 700; }
    .letter-sheet .kop .al { font-size: 10px; line-height: 1.5; margin-top: 3px; }
    .letter-sheet .tgl { text-align: right; font-size: 11px; margin-top: 14px; }
    .letter-sheet .meta { text-align: right; font-size: 11px; line-height: 1.7; margin-top: 6px; }
    .letter-sheet .meta b { font-weight: 600; }
    .letter-sheet .sal { margin-top: 12px; font-size: 11px; line-height: 1.6; }
    .letter-sheet .sal .kpd { font-weight: 600; }
    .letter-sheet .pembuka { margin-top: 10px; font-size: 11px; line-height: 1.6; }
    .letter-sheet .table { width: 100%; border-collapse: collapse; margin-top: 14px; font-size: 11px; line-height: 1.7; }
    .letter-sheet .table td { padding: 2px 0; vertical-align: top; }
    .letter-sheet .table td.no { width: 30px; }
    .letter-sheet .table td.lbl { width: 130px; }
    .letter-sheet .table td.col { width: 18px; }
    .letter-sheet .judul { margin-top: 18px; }
    .letter-sheet .judul p { font-size: 13px; line-height: 1.4; text-align: center; font-weight: 700; text-decoration: underline; }
    .letter-sheet .nomor { margin-top: 8px; font-size: 11px; }
    .letter-sheet .larangan { margin-top: 18px; }
    .letter-sheet .larangan p { font-size: 11px; line-height: 1.6; text-align: justify; }
    .letter-sheet .penutup { margin-top: 12px; }
    .letter-sheet .penutup p { font-size: 11px; line-height: 1.6; text-align: justify; }
    .letter-sheet .ttd { margin-top: 26px; font-size: 11px; line-height: 1.6; }
    .letter-sheet .ttd .skr { margin-bottom: 55px; }
    .letter-sheet .ttd .nm { font-weight: 700; text-decoration: underline; }
    .letter-sheet .dok { font-size: 10px; line-height: 1.6; margin-top: 10px; }

    #toastPn { position: fixed; right: 20px; bottom: 20px; z-index: 2000; display: none; align-items: center; gap: .6rem; max-width: 360px; border-radius: .75rem; background: #0f172a; color: #fff; padding: .7rem 1rem; font-size: .82rem; box-shadow: 0 12px 24px rgba(15,23,42,.25); }
    #toastPn.show { display: flex; animation: riseIn .3s ease; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Surat Penunjukkan</h1>
                    <p class="text-xs text-slate-500">Daftar surat penunjukkan dosen pembimbing dan penguji skripsi tingkat jurusan, lengkap dengan akses cetak surat resmi A4.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="/redesain-siatek/dokumen/surat-penunjukkan-pembimbing-skripsi/521421011" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-4 py-2 text-xs font-semibold text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-600">
                    <i class="fas fa-file-pdf"></i>Contoh Surat
                </a>
                <button type="button" id="btnBuatPn" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-4 py-2 text-xs font-semibold text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-600">
                    <i class="fas fa-plus"></i>Buat Surat
                </button>
            </div>
        </div>
    </section>

    <!-- ===== Tab Jenis ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap gap-2">
            <button type="button" class="tab-btn" data-tab="semua" aria-selected="true"><span class="tdot bg-slate-500"></span>Semua<span class="tnum" id="cnt-semua">9</span></button>
            <button type="button" class="tab-btn" data-tab="pembimbing" aria-selected="false"><span class="tdot bg-sky-500"></span>Pembimbing Skripsi<span class="tnum" id="cnt-pembimbing">4</span></button>
            <button type="button" class="tab-btn" data-tab="penguji" aria-selected="false"><span class="tdot bg-amber-500"></span>Penguji Hasil<span class="tnum" id="cnt-penguji">3</span></button>
            <button type="button" class="tab-btn" data-tab="tutup" aria-selected="false"><span class="tdot bg-orange-500"></span>Penguji Tutup<span class="tnum" id="cnt-tutup">2</span></button>
        </div>
    </section>

    <!-- ===== Toolbar ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari mahasiswa, NIM, dosen, nomor suratâ€¦"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Tahun</option>
                <option value="2026">2026</option>
                <option value="2025">2025</option>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
        </div>
    </section>

    <!-- ===== Ringkasan ===== -->
    <section class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs text-slate-500" id="jmlInfo">Menampilkan <b class="text-slate-800">0</b> dari <b class="text-slate-800">0</b> surat</p>
        <p class="text-xs text-slate-400"><i class="fas fa-print mr-1 text-orange-400"></i>Klik ikon <i class="fas fa-file-pdf mx-1 text-rose-500"></i>untuk membuka/cetak surat resmi A4.</p>
    </section>

    <!-- ===== Tabel Surat Penunjukkan ===== -->
    <section>
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="max-h-[560px] overflow-y-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
                        <tr>
                            <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">No</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Jenis</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Nomor</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Mahasiswa</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Dosen Ditunjuk</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tanggal</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">File</th>
                            <th class="py-3.5 px-5 text-right font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbPn" class="divide-y divide-slate-100">

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pembimbing" data-tahun="2026" data-nama="Mohamad Ryan Noor Sahidu" data-nim="521421011" data-dosen="Amirudin Yunus Dako, ST., M.Eng." data-nip="197410032001121001" data-file="Surat_Penunjukkan_Pembimbing_521421011.pdf" data-nomor="300/UN47.B5.5/TD.06/2026" data-tanggal="09 Jun 2026" data-url="surat-penunjukkan-pembimbing-skripsi/521421011" data-cari="mohamad ryan noor sahidu 521421011 amirudin yunus dako pembimbing skripsi 300 un47 b5 5 td 06 2026 2026">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">1</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">300/UN47.B5.5/TD.06/2026</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Mohamad Ryan Noor Sahidu</p><p class="text-[10px] text-slate-400">NIM 521421011</p></div></td>
                            <td class="px-4 py-4"><span class="dosen-chip"><span class="d-ava bg-sky-100 text-sky-700">AY</span><span class="d-meta"><span class="d-name">Amirudin Yunus Dako, ST., M.Eng.</span><span class="d-sub">Pembimbing 1</span></span></span></td>
                            <td class="px-4 py-4 whitespace-nowrap">09 Jun 2026</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_521421011.pdf</span><span class="f-sub">2.4 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pembimbing" data-tahun="2026" data-nama="Alya Pratiwi Putri Junus" data-nim="521422062" data-dosen="Ir. Hendra Wijaya, M.Eng." data-nip="197302151999031002" data-file="Surat_Penunjukkan_Pembimbing_521422062.pdf" data-nomor="B/TE/2026/101" data-tanggal="03 Jun 2026" data-url="surat-penunjukkan-pembimbing-skripsi/521422062" data-cari="alya pratiwi putri junus 521422062 ir hendra wijaya m eng pembimbing skripsi b te 2026 101 2026">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">2</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">B/TE/2026/101</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Alya Pratiwi Putri Junus</p><p class="text-[10px] text-slate-400">NIM 521422062</p></div></td>
                            <td class="px-4 py-4"><span class="dosen-chip"><span class="d-ava bg-sky-100 text-sky-700">HW</span><span class="d-meta"><span class="d-name">Ir. Hendra Wijaya, M.Eng.</span><span class="d-sub">Pembimbing 1</span></span></span></td>
                            <td class="px-4 py-4 whitespace-nowrap">03 Jun 2026</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_521422062.pdf</span><span class="f-sub">2.1 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pembimbing" data-tahun="2025" data-nama="Salsabila Buka" data-nim="521420025" data-dosen="Dr. Ratna Dewi, M.Kom." data-nip="197608251999032003" data-file="Surat_Penunjukkan_Pembimbing_521420025.pdf" data-nomor="B/TE/2025/188" data-tanggal="18 Okt 2025" data-url="surat-penunjukkan-pembimbing-skripsi/521420025" data-cari="salsabila buka 521420025 dr ratna dewi m kom pembimbing skripsi b te 2025 188 2025">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">3</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">B/TE/2025/188</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Salsabila Buka</p><p class="text-[10px] text-slate-400">NIM 521420025</p></div></td>
                            <td class="px-4 py-4"><span class="dosen-chip"><span class="d-ava bg-sky-100 text-sky-700">RD</span><span class="d-meta"><span class="d-name">Dr. Ratna Dewi, M.Kom.</span><span class="d-sub">Pembimbing 2</span></span></span></td>
                            <td class="px-4 py-4 whitespace-nowrap">18 Okt 2025</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_521420025.pdf</span><span class="f-sub">2.0 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pembimbing" data-tahun="2025" data-nama="Nur Uyun I Yusuf" data-nim="521418018" data-dosen="Deni Kurniawan, M.Sc." data-nip="198611122011011003" data-file="Surat_Penunjukkan_Pembimbing_521418018.pdf" data-nomor="B/TE/2025/176" data-tanggal="09 Sep 2025" data-url="surat-penunjukkan-pembimbing-skripsi/521418018" data-cari="nur uyun i yusuf 521418018 deni kurniawan m sc pembimbing skripsi b te 2025 176 2025">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">4</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">B/TE/2025/176</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Nur Uyun I Yusuf</p><p class="text-[10px] text-slate-400">NIM 521418018</p></div></td>
                            <td class="px-4 py-4"><span class="dosen-chip"><span class="d-ava bg-sky-100 text-sky-700">DK</span><span class="d-meta"><span class="d-name">Deni Kurniawan, M.Sc.</span><span class="d-sub">Pembimbing 1</span></span></span></td>
                            <td class="px-4 py-4 whitespace-nowrap">09 Sep 2025</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_521418018.pdf</span><span class="f-sub">2.3 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="penguji" data-tahun="2026" data-nama="Alya Pratiwi Putri Junus" data-nim="521422062" data-dosen="Dr. Ratna Dewi, M.Kom." data-nip="197608251999032003" data-file="Surat_Penunjukkan_Penguji_521422062.pdf" data-nomor="141/UN47.B5.5/TD.06/2026" data-tanggal="02 Mar 2026" data-url="surat-penunjukkan-penguji/521422062" data-cari="alya pratiwi putri junus 521422062 dr ratna dewi m kom penguji hasil skripsi 141 un47 b5 5 td 06 2026 2026">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">5</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">141/UN47.B5.5/TD.06/2026</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Alya Pratiwi Putri Junus</p><p class="text-[10px] text-slate-400">NIM 521422062</p></div></td>
                            <td class="px-4 py-4"><span class="dosen-chip"><span class="d-ava bg-amber-100 text-amber-700">RD</span><span class="d-meta"><span class="d-name">Dr. Ratna Dewi, M.Kom.</span><span class="d-sub">Penguji 1</span></span></span></td>
                            <td class="px-4 py-4 whitespace-nowrap">02 Mar 2026</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_521422062.pdf</span><span class="f-sub">2.6 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="penguji" data-tahun="2026" data-nama="Mohamad Ryan Noor Sahidu" data-nim="521421011" data-dosen="Ir. Hendra Wijaya, M.Eng." data-nip="197302151999031002" data-file="Surat_Penunjukkan_Penguji_521421011.pdf" data-nomor="B/TE/2026/097" data-tanggal="28 Feb 2026" data-url="surat-penunjukkan-penguji/521421011" data-cari="mohamad ryan noor sahidu 521421011 ir hendra wijaya m eng penguji hasil skripsi b te 2026 097 2026">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">6</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">B/TE/2026/097</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Mohamad Ryan Noor Sahidu</p><p class="text-[10px] text-slate-400">NIM 521421011</p></div></td>
                            <td class="px-4 py-4"><span class="dosen-chip"><span class="d-ava bg-amber-100 text-amber-700">HW</span><span class="d-meta"><span class="d-name">Ir. Hendra Wijaya, M.Eng.</span><span class="d-sub">Penguji 2</span></span></span></td>
                            <td class="px-4 py-4 whitespace-nowrap">28 Feb 2026</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_521421011.pdf</span><span class="f-sub">2.2 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="penguji" data-tahun="2025" data-nama="Salsabila Buka" data-nim="521420025" data-dosen="Dr. Siti Aminah, M.M." data-nip="198009152006042002" data-file="Surat_Penunjukkan_Penguji_521420025.pdf" data-nomor="B/TE/2025/214" data-tanggal="12 Des 2025" data-url="surat-penunjukkan-penguji/521420025" data-cari="salsabila buka 521420025 dr siti aminah m m penguji hasil skripsi b te 2025 214 2025">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">7</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">B/TE/2025/214</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Salsabila Buka</p><p class="text-[10px] text-slate-400">NIM 521420025</p></div></td>
                            <td class="px-4 py-4"><span class="dosen-chip"><span class="d-ava bg-amber-100 text-amber-700">SA</span><span class="d-meta"><span class="d-name">Dr. Siti Aminah, M.M.</span><span class="d-sub">Penguji 3</span></span></span></td>
                            <td class="px-4 py-4 whitespace-nowrap">12 Des 2025</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_521420025.pdf</span><span class="f-sub">2.5 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2026" data-nama="Nur Uyun I Yusuf" data-nim="521418018" data-dosen="Prof. Dr. Ahmad Fauzi, M.T." data-nip="197205101997021002" data-file="Surat_Penunjukkan_Tutup_521418018.pdf" data-nomor="B/TE/2026/133" data-tanggal="09 Jun 2026" data-url="surat-penunjukkan-penguji/521418018" data-cari="nur uyun i yusuf 521418018 prof dr ahmad fauzi m t penguji tutup skripsi b te 2026 133 2026">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">8</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">B/TE/2026/133</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Nur Uyun I Yusuf</p><p class="text-[10px] text-slate-400">NIM 521418018</p></div></td>
                            <td class="px-4 py-4"><span class="dosen-chip"><span class="d-ava bg-orange-100 text-orange-700">AF</span><span class="d-meta"><span class="d-name">Prof. Dr. Ahmad Fauzi, M.T.</span><span class="d-sub">Ketua Penguji</span></span></span></td>
                            <td class="px-4 py-4 whitespace-nowrap">09 Jun 2026</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Tutup_521418018.pdf</span><span class="f-sub">2.8 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2025" data-nama="Alya Pratiwi Putri Junus" data-nim="521422062" data-dosen="Dr. Ratna Dewi, M.Kom." data-nip="197608251999032003" data-file="Surat_Penunjukkan_Tutup_521422062.pdf" data-nomor="B/TE/2025/251" data-tanggal="20 Des 2025" data-url="surat-penunjukkan-penguji/521422062" data-cari="alya pratiwi putri junus 521422062 dr ratna dewi m kom penguji tutup skripsi b te 2025 251 2025">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">9</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">B/TE/2025/251</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Alya Pratiwi Putri Junus</p><p class="text-[10px] text-slate-400">NIM 521422062</p></div></td>
                            <td class="px-4 py-4"><span class="dosen-chip"><span class="d-ava bg-orange-100 text-orange-700">RD</span><span class="d-meta"><span class="d-name">Dr. Ratna Dewi, M.Kom.</span><span class="d-sub">Penguji 1</span></span></span></td>
                            <td class="px-4 py-4 whitespace-nowrap">20 Des 2025</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Tutup_521422062.pdf</span><span class="f-sub">2.7 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
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

<!-- ===== Modal Buat Surat Penunjukkan ===== -->
<div class="modal-overlay" id="buatModal" role="dialog" aria-modal="true">
    <div class="flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3.5">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-100 text-sm text-orange-600"><i class="fas fa-file-signature"></i></span>
                <div>
                    <h6 class="text-sm font-semibold text-slate-900">Buat Surat Penunjukkan</h6>
                    <p class="text-[11px] text-slate-400">Isi data dosen &amp; mahasiswa â€” pratinjau langsung di sisi kanan.</p>
                </div>
            </div>
            <button type="button" class="text-xl leading-none text-slate-400 hover:text-slate-700" data-buat-close>&times;</button>
        </div>

        <div class="grid flex-1 gap-0 overflow-y-auto md:grid-cols-2">
            <!-- ===== FORM ===== -->
            <div class="space-y-3.5 border-b border-slate-200 p-5 md:border-b-0 md:border-r">
                <div>
                    <label for="bfJenis" class="mb-1 block text-xs font-semibold text-slate-600">Jenis Surat <span class="text-rose-500">*</span></label>
                    <select id="bfJenis" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                        <option value="pembimbing" selected>Penunjukkan Dosen Pembimbing Skripsi</option>
                        <option value="penguji">Penunjukkan Dosen Penguji Hasil</option>
                        <option value="tutup">Penunjukkan Dosen Penguji Tutup</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="bfNama" class="mb-1 block text-xs font-semibold text-slate-600">Nama Mahasiswa <span class="text-rose-500">*</span></label>
                        <input type="text" id="bfNama" placeholder="Nama lengkap mahasiswa" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                    </div>
                    <div>
                        <label for="bfNim" class="mb-1 block text-xs font-semibold text-slate-600">NIM <span class="text-rose-500">*</span></label>
                        <input type="text" id="bfNim" placeholder="521421011" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="bfDosen" class="mb-1 block text-xs font-semibold text-slate-600">Nama Dosen <span class="text-rose-500">*</span></label>
                        <input type="text" id="bfDosen" placeholder="Nama dosen yang ditunjuk" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                    </div>
                    <div>
                        <label for="bfSep" class="mb-1 block text-xs font-semibold text-slate-600">Sebagai</label>
                        <select id="bfSep" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                            <option value="Pembimbing 1">Pembimbing 1</option>
                            <option value="Pembimbing 2">Pembimbing 2</option>
                            <option value="Ketua Penguji">Ketua Penguji</option>
                            <option value="Penguji 1">Penguji 1</option>
                            <option value="Penguji 2">Penguji 2</option>
                            <option value="Penguji 3">Penguji 3</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="bfNip" class="mb-1 block text-xs font-semibold text-slate-600">NIP Dosen</label>
                        <input type="text" id="bfNip" placeholder="197410032001121001" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                    </div>
                    <div>
                        <label for="bfNo" class="mb-1 block text-xs font-semibold text-slate-600">Nomor Surat</label>
                        <input type="text" id="bfNo" placeholder="B/TE/2026/102" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                    </div>
                </div>
                <div>
                    <label for="bfTanggal" class="mb-1 block text-xs font-semibold text-slate-600">Tanggal Surat <span class="text-rose-500">*</span></label>
                    <input type="date" id="bfTanggal" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                </div>
                <div>
                    <div class="mb-1 flex items-center justify-between">
                        <label for="bfJudul" class="block text-xs font-semibold text-slate-600">Judul Skripsi <span class="text-rose-500">*</span></label>
                    </div>
                    <textarea id="bfJudul" rows="3" class="w-full resize-y rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm leading-relaxed outline-none focus:border-orange-400 focus:bg-white" placeholder="Judul proposal / skripsi mahasiswa"></textarea>
                </div>
                <div class="flex items-center gap-2 rounded-lg bg-slate-50 border border-slate-200 px-3 py-2.5 text-[11px] text-slate-500">
                    <i class="fas fa-info-circle text-orange-400"></i>
                    Menyimpan surat akan menambah baris ke daftar (dummy) &amp; membuka pratinjau cetak A4.
                </div>
            </div>

            <!-- ===== PREVIEW SURAT ===== -->
            <div class="overflow-y-auto bg-slate-100 p-5">
                <div class="mb-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                    <i class="fas fa-eye text-orange-500"></i> Pratinjau surat penunjukkan
                    <span class="ml-auto hidden items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 md:inline-flex">
                        <i class="fas fa-circle text-[6px]"></i> Update otomatis
                    </span>
                </div>
                <div class="letter-sheet rounded-lg bg-white p-6 shadow-sm md:p-7" id="pvSheet"></div>
            </div>
        </div>

        <div class="flex items-center justify-between gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3">
            <p class="text-[11px] text-slate-400"><i class="fas fa-print mr-1 text-orange-400"></i>Surat dicetak formal A4 oleh sistem print.</p>
            <div class="flex items-center gap-2">
                <button type="button" class="rounded-lg bg-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-300" data-buat-close>Batal</button>
                <button type="button" id="btnSimpanPn" class="rounded-lg bg-orange-500 px-3 py-2 text-xs font-semibold text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-600"><i class="fas fa-check mr-1"></i>Simpan ke Daftar</button>
            </div>
        </div>
    </div>
</div>

<!-- iframe cetak tersembunyi (print sistem) -->
<div hidden><iframe id="printFrame" title="Pratinjau Cetak Surat Penunjukkan"></iframe></div>

<!-- Toast -->
<div id="toastPn"></div>

<script>
(function () {
    var tb = document.getElementById('tbPn');
    var rows = Array.prototype.slice.call(document.querySelectorAll('#tbPn tr'));
    var fCari = document.getElementById('fCari');
    var fTahun = document.getElementById('fTahun');
    var activeTab = 'semua';
    var page = 1, PER = 6;

    var TIPE = {
        pembimbing: { badge: 'bg-sky-50 text-sky-700', txt: 'Pembimbing Skripsi' },
        penguji:    { badge: 'bg-amber-50 text-amber-700', txt: 'Penguji Hasil' },
        tutup:      { badge: 'bg-orange-50 text-orange-700', txt: 'Penguji Tutup' }
    };

    function esc(t) { return (t || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function cval(id) { var el = document.getElementById(id); return el ? el.value.trim() : ''; }
    var BULAN_P = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    function pad2(n) { return (n < 10 ? '0' : '') + n; }
    function isoToday() { var d = new Date(); return d.getFullYear() + '-' + pad2(d.getMonth()+1) + '-' + pad2(d.getDate()); }
    function fmtTglPenuh(v) { var p = (v||'').split('-'); if (p.length !== 3) return '-'; return (+p[2]) + ' ' + BULAN_P[+p[1]-1] + ' ' + p[0]; }

    var toast = document.getElementById('toastPn');
    function showToast(msg, isErr) {
        toast.textContent = msg;
        toast.className = 'show ' + (isErr ? 'bg-rose-600' : 'bg-slate-900');
        clearTimeout(showToast._t);
        showToast._t = setTimeout(function () { toast.className = ''; }, 2000);
    }

    function recount() {
        var c = { 'semua': rows.length, 'pembimbing': 0, 'penguji': 0, 'tutup': 0 };
        rows.forEach(function (tr) {
            var j = tr.getAttribute('data-tab-jenis');
            if (c[j] !== undefined) c[j]++;
        });
        ['semua','pembimbing','penguji','tutup'].forEach(function (k) {
            document.getElementById('cnt-' + k).textContent = c[k];
        });
    }

    function visible() {
        var kata = (fCari.value || '').toLowerCase().trim();
        var th = fTahun.value;
        return rows.filter(function (tr) {
            if (activeTab !== 'semua' && tr.getAttribute('data-tab-jenis') !== activeTab) return false;
            if (th && tr.getAttribute('data-tahun') !== th) return false;
            return !kata || tr.getAttribute('data-cari').indexOf(kata) !== -1;
        });
    }

    function render() {
        var v = visible();
        var pages = Math.max(1, Math.ceil(v.length / PER));
        if (page > pages) page = pages;
        var start = (page - 1) * PER;
        var slice = v.slice(start, start + PER);
        rows.forEach(function (tr) { tr.style.display = 'none'; });
        slice.forEach(function (tr) { tr.style.display = ''; });
        document.getElementById('jmlInfo').innerHTML = 'Menampilkan <b class="text-slate-800">' + slice.length + '</b> dari <b class="text-slate-800">' + v.length + '</b> surat';
        document.getElementById('pgInfo').textContent = 'Halaman ' + page + ' dari ' + pages;
        var wrap = document.getElementById('pgWrap');
        wrap.innerHTML = '';
        var prev = document.createElement('button');
        prev.className = 'pg-btn'; prev.innerHTML = '<i class="fas fa-chevron-left"></i>'; prev.disabled = page <= 1;
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
        next.className = 'pg-btn'; next.innerHTML = '<i class="fas fa-chevron-right"></i>'; next.disabled = page >= pages;
        next.addEventListener('click', function () { if (page < pages) { page++; render(); } });
        wrap.appendChild(next);
    }

    function chipDosen(nama, sub) {
        var ini = (nama || '?').split(/\s+/).filter(Boolean).slice(0,2).map(function (w) { return w[0]; }).join('').toUpperCase();
        return '<span class="dosen-chip"><span class="d-ava bg-orange-100 text-orange-700">' + ini + '</span>' +
            '<span class="d-meta"><span class="d-name">' + esc(nama) + '</span><span class="d-sub">' + esc(sub) + '</span></span></span>';
    }
    function chipFile(nama) {
        return '<span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span>' +
            '<span class="f-meta"><span class="f-name">' + esc(nama) + '</span><span class="f-sub">Dokumen PDF</span></span>' +
            '<button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span>';
    }

    /* ===== Tab ===== */
    document.querySelectorAll('.tab-btn').forEach(function (b) {
        b.addEventListener('click', function () {
            document.querySelectorAll('.tab-btn').forEach(function (x) { x.setAttribute('aria-selected', 'false'); });
            b.setAttribute('aria-selected', 'true');
            activeTab = b.getAttribute('data-tab');
            page = 1; render();
        });
    });
    fCari.addEventListener('input', function () { page = 1; render(); });
    fTahun.addEventListener('change', function () { page = 1; render(); });
    document.getElementById('btnReset').addEventListener('click', function () {
        fCari.value = ''; fTahun.value = ''; page = 1; render();
    });

    /* ===== Aksi baris: unduh/buka PDF & hapus (dummy) ===== */
    function openSurat(tr) {
        var url = tr.getAttribute('data-url');
        window.open('/redesain-siatek/dokumen/' + url, '_blank');
    }
    document.addEventListener('click', function (e) {
        var btnU = e.target.closest('.btn-unduh');
        if (btnU) { var tr = btnU.closest('tr'); if (tr) openSurat(tr); return; }
        var chip = e.target.closest('.pn-cetak');
        if (chip) { var tr2 = chip.closest('tr'); if (tr2) openSurat(tr2); return; }
        var btnH = e.target.closest('.btn-hapus');
        if (btnH) {
            var tr3 = btnH.closest('tr');
            var nm = tr3 ? tr3.getAttribute('data-nama') : 'surat';
            tr3.classList.remove('reveal');
            tr3.remove();
            rows = Array.prototype.slice.call(document.querySelectorAll('#tbPn tr'));
            page = 1; recount(); render();
            showToast('Surat penunjukkan ' + nm + ' dihapus (dummy)', true);
            return;
        }
    });

    /* ===== Buat Surat ===== */
    function buildBody() {
        var jenis = cval('bfJenis') || 'pembimbing';
        var nm = esc(cval('bfNama'));
        var nim = esc(cval('bfNim'));
        var dosen = esc(cval('bfDosen'));
        var sep = esc(cval('bfSep'));
        var nip = esc(cval('bfNip'));
        var no = esc(cval('bfNo'));
        var tgl = fmtTglPenuh(cval('bfTanggal'));
        var judul = esc(cval('bfJudul'));

        var jdlTxt = jenis === 'pembimbing'
            ? 'SURAT PENUNJUKAN DOSEN PEMBIMBING PROPOSAL SKRIPSI'
            : jenis === 'tutup'
                ? 'SURAT PENUNJUKAN DOSEN PENGUJI SIDANG TUTUP SKRIPSI'
                : 'SURAT PENUNJUKAN DOSEN PENGUJI SIDANG HASIL SKRIPSI';

        var labelDosen = jenis === 'pembimbing' ? 'Pembimbing' : 'Penguji';

        var h = '<div class="sheet">';
        h += '<div class="kop">'
            + '<p class="l1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI<br>UNIVERSITAS NEGERI GORONTALO â€” FAKULTAS TEKNIK</p>'
            + '<h2>JURUSAN TEKNIK ELEKTRO</h2>'
            + '<p class="fak">Jl. Prof. Dr. Ing. B.J. Habibie, Moutong, Tilongkabila, Kab. Bone Bolango 96554</p>'
            + '<p class="al">Telp. (0435) 821125 &bull; e-mail: jtek@ung.ac.id &bull; laman: elektro.ung.ac.id</p>'
            + '</div>';
        h += '<div class="judul"><p>' + jdlTxt + '</p></div>';
        h += '<p class="nomor">Nomor : ' + (no || '300/UN47.B5.5/TD.06/2026') + '</p>';
        h += '<div class="larangan"><p>Ketua Jurusan Teknik Elektro dan Komputer Fakultas Teknik Universitas Negeri Gorontalo dengan ini menunjuk ' + (dosen || 'Bapak/Ibu') + ' sebagai ' + (sep || labelDosen) + ' mahasiswa sebagai berikut :</p></div>';
        h += '<table class="table">'
            + '<tr><td class="lbl">' + (sep || labelDosen) + '</td><td class="col">:</td><td>' + (dosen || 'â€¦') + '</td></tr>'
            + '<tr><td class="lbl">NIP</td><td class="col">:</td><td>' + (nip || 'â€¦') + '</td></tr>'
            + '<tr><td colspan="3" style="height:14px"></td></tr>'
            + '<tr><td class="lbl">Nama Mahasiswa</td><td class="col">:</td><td>' + (nm || 'â€¦') + '</td></tr>'
            + '<tr><td class="lbl">NIM</td><td class="col">:</td><td>' + (nim || 'â€¦') + '</td></tr>'
            + '<tr><td class="lbl" style="vertical-align:top">Judul Skripsi</td><td class="col">:</td><td style="text-align:justify"><b>' + (judul || 'â€¦') + '</b></td></tr>'
            + '</table>';
        h += '<div class="penutup"><p>Kepada Bapak/Ibu yang namanya tercantum dalam Surat Penunjukan ini agar dapat melaksanakan tugas dengan penuh tanggung jawab.</p></div>';
        h += '<div class="ttd">'
            + '<p class="tgl">Gorontalo, ' + tgl + '</p>'
            + '<p class="skr">Ketua Jurusan</p>'
            + '<p style="height:34px"></p>'
            + '<p class="nm">Yasin Mohamad, ST., MT.</p>'
            + '<p><b>NIP. 197102222001121001</b></p>'
            + '</div>';
        h += '<div class="dok">'
            + '<p><b>Tembusan disampaikan kepada Yth.:</b></p>'
            + '<p>1. Dosen ' + (jenis === 'pembimbing' ? 'pembimbing I dan II' : 'penguji sidang') + '</p>'
            + '<p>2. Mahasiswa yang bersangkutan</p>'
            + '<p>3. Arsip</p>'
            + '</div>';
        h += '</div>';
        return h;
    }
    function renderPreview() { document.getElementById('pvSheet').innerHTML = buildBody(); }

    /* ===== Simpan (dummy) ===== */
    document.getElementById('btnSimpanPn').addEventListener('click', function () {
        var nm = cval('bfNama'); var nim = cval('bfNim'); var dosen = cval('bfDosen');
        if (!nm || !nim || !dosen) { showToast('Nama mahasiswa, NIM, dan dosen wajib diisi', true); return; }
        var jenis = cval('bfJenis') || 'pembimbing';
        var t = TIPE[jenis] || TIPE.pembimbing;
        var no = cval('bfNo') || ('B/TE/' + new Date().getFullYear() + '/102');
        var tglIso = cval('bfTanggal') || isoToday();
        var tglS = fmtTglPenuh(tglIso).replace(/Januari/,'Jan').replace(/Februari/,'Feb').replace(/Maret/,'Mar').replace(/April/,'Apr').replace(/Agustus/,'Agu').replace(/September/,'Sep').replace(/Oktober/,'Okt').replace(/November/,'Nov').replace(/Desember/,'Des').replace(/Juni/,'Jun').replace(/Juli/,'Jul');
        var eps = { 'Pembimbing 1':'Pembimbing 1','Pembimbing 2':'Pembimbing 2','Ketua Penguji':'Ketua Penguji','Penguji 1':'Penguji 1','Penguji 2':'Penguji 2','Penguji 3':'Penguji 3' };
        var sep = eps[cval('bfSep')] || (jenis === 'pembimbing' ? 'Pembimbing 1' : 'Penguji 1');
        var file = 'Surat_Penunjukkan_' + (jenis === 'tutup' ? 'Tutup' : jenis === 'penguji' ? 'Penguji' : 'Pembimbing') + '_' + nim + '.pdf';
        var tahun = tglIso.slice(0,4);
        var url = (jenis === 'pembimbing' ? 'surat-penunjukkan-pembimbing-skripsi' : 'surat-penunjukkan-penguji') + '/' + nim;
        var cari = (nm + ' ' + nim + ' ' + dosen + ' ' + t.txt + ' ' + no + ' ' + tahun).toLowerCase();

        var tr = document.createElement('tr');
        tr.setAttribute('data-tab-jenis', jenis);
        tr.setAttribute('data-tahun', tahun);
        tr.setAttribute('data-nama', nm);
        tr.setAttribute('data-nim', nim);
        tr.setAttribute('data-dosen', dosen);
        tr.setAttribute('data-nip', cval('bfNip'));
        tr.setAttribute('data-file', file);
        tr.setAttribute('data-nomor', no);
        tr.setAttribute('data-tanggal', tglS);
        tr.setAttribute('data-url', url);
        tr.setAttribute('data-cari', cari);
        var zebra = (rows.length % 2 === 0) ? 'bg-white' : 'bg-slate-50/60';
        tr.className = zebra + ' transition hover:bg-orange-50 reveal';
        tr.innerHTML = '<td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">' + (rows.length + 1) + '</span></td>'
            + '<td class="px-4 py-4"><span class="pt-badge ' + t.badge + '">' + t.txt + '</span></td>'
            + '<td class="px-4 py-4 whitespace-nowrap text-slate-500">' + esc(no) + '</td>'
            + '<td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">' + esc(nm) + '</p><p class="text-[10px] text-slate-400">NIM ' + esc(nim) + '</p></div></td>'
            + '<td class="px-4 py-4">' + chipDosen(dosen, sep) + '</td>'
            + '<td class="px-4 py-4 whitespace-nowrap">' + tglS + '</td>'
            + '<td class="px-4 py-4">' + chipFile(file) + '</td>'
            + '<td class="px-5 py-4"><div class="flex items-center justify-end gap-1.5">'
            + '<button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>'
            + '<button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>'
            + '</div></td>';
        tb.insertAdjacentElement('beforeend', tr);
        rows = Array.prototype.slice.call(document.querySelectorAll('#tbPn tr'));
        page = 1; recount(); render();
        document.getElementById('buatModal').classList.remove('show');
        showToast('Surat ' + no + ' disimpan ke daftar (dummy) âœ“');
    });

    /* ===== Init ===== */
    document.getElementById('btnBuatPn').addEventListener('click', function () {
        document.getElementById('bfTanggal').value = isoToday();
        renderPreview();
        document.getElementById('buatModal').classList.add('show');
    });
    var bm = document.getElementById('buatModal');
    bm.querySelectorAll('[data-buat-close]').forEach(function (b) {
        b.addEventListener('click', function () { bm.classList.remove('show'); });
    });
    bm.addEventListener('click', function (e) { if (e.target === bm) bm.classList.remove('show'); });
    ['bfJenis','bfTanggal','bfNama','bfNim','bfDosen','bfSep','bfNip','bfNo','bfJudul'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) { el.addEventListener('input', renderPreview); el.addEventListener('change', renderPreview); }
    });

    recount();
    render();
})();
</script>
