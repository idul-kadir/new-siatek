<?php
/**
 * Halaman: Surat Penunjukkan (Jurusan)
 * Daftar surat penunjukkan dosen (pembimbing/penguji) skripsi & KP tingkat jurusan.
 * Data diambil dari tabel surat_penunjukkan DB "siatek" (nama mahasiswa & dosen
 * dari tabel mahasiswa/dosen), lalu ditulis statis ke HTML — tanpa load PHP/JS.
 * Sampel: 8 surat terbaru per kategori dari total 461 data.
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

    .pt-badge { display: inline-block; padding: .18rem .55rem; border-radius: .45rem; font-size: 10px; font-weight: 700; white-space: nowrap; }

    .pin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .pin-table thead th { background: #f8fafc; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; text-align: left; padding: .6rem .85rem; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
    .pin-table tbody td { padding: .7rem .85rem; font-size: .78rem; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .pin-table tbody tr:last-child td { border-bottom: none; }
    .pin-table tbody tr { transition: background .15s ease; }
    .pin-table tbody tr:hover { background: #f8fafc; }

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

    .stat-table { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 720px; }
    .stat-table thead th { background: #0f172a; color: #cbd5e1; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; padding: .55rem .75rem; white-space: nowrap; }
    .stat-table thead th.grp { background: #0f172a; color: #fff; text-align: center; }
    .stat-table thead th.gsep, .stat-table td.gsep { border-left: 1px solid rgba(148,163,184,.25); }
    .stat-table tbody td { padding: .65rem .75rem; font-size: .78rem; color: #334155; border-bottom: 1px solid #f1f5f9; }
    .stat-table tbody tr:last-child td { border-bottom: none; }
    .stat-table tbody tr:hover td { background: #f8fafc; }

    .modal-overlay { display: none; position: fixed; inset: 0; z-index: 90; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem; }
    .modal-overlay.show { display: flex; }

    #toastPn { position: fixed; right: 20px; bottom: 20px; z-index: 2000; display: none; align-items: center; gap: .6rem; max-width: 360px; border-radius: .75rem; background: #0f172a; color: #fff; padding: .7rem 1rem; font-size: .82rem; box-shadow: 0 12px 24px rgba(15,23,42,.25); }
    #toastPn.show { display: flex; animation: riseIn .3s ease; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                <i class="fas fa-file-signature"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-800">Surat Penunjukkan</h1>
                <p class="text-xs text-slate-500">Daftar surat penunjukkan dosen pembimbing dan penguji skripsi tingkat jurusan, lengkap dengan akses cetak surat resmi A4.</p>
            </div>
        </div>
    </section>

    <!-- ===== Strip Statistik Beban Dosen ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-5 py-3.5 shadow-sm">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-sm text-slate-600"><i class="fas fa-chart-simple"></i></span>
                <div>
                    <h6 class="text-sm font-semibold text-slate-800">Statistik Beban Dosen</h6>
                    <p class="text-[11px] text-slate-400" id="stRingkas">—</p>
                </div>
            </div>
            <button type="button" id="btnStat" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-700">
                <i class="fas fa-table-cells"></i>Lihat Pembagian
            </button>
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
            <select id="fJenis" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Kategori</option>
                <option value="pemskr">Pembimbing Skripsi</option>
                <option value="pemkp">Pembimbing KP</option>
                <option value="hasil">Penguji Hasil</option>
                <option value="pengkp">Penguji KP</option>
                <option value="tutup">Penguji Tutup</option>
            </select>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Tahun</option>
                <option value="2024">2024</option>
                <option value="2023">2023</option>
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
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tanggal</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">File</th>
                            <th class="py-3.5 px-5 text-right font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbPn" class="divide-y divide-slate-100">

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="Reynaldi Polingala" data-nim="521420038" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521420038.pdf" data-nomor="636/UN47.B5.5/TD.06/2024" data-tanggal="1 Okt 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521420038" data-cari="reynaldi polingala 521420038 636/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">1</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">636/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Reynaldi Polingala</p><p class="text-[10px] text-slate-400">NIM 521420038</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">1 Okt 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_Skripsi_521420038.pdf</span><span class="f-sub">2.9 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="Yudi Anas Sahri" data-nim="521419020" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521419020.pdf" data-nomor="627/UN47.B5.5/TD.06/2024" data-tanggal="26 Sep 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521419020" data-cari="yudi anas sahri 521419020 627/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">2</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">627/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Yudi Anas Sahri</p><p class="text-[10px] text-slate-400">NIM 521419020</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">26 Sep 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_Skripsi_521419020.pdf</span><span class="f-sub">2.8 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Aditya Hasin Mooduto" data-nim="521418026" data-file="Surat_Penunjukkan_Pembimbing_KP_521418026.pdf" data-nomor="624/UN47.B5.5/TD.06/2024" data-tanggal="25 Sep 2024" data-url="" data-cari="aditya hasin mooduto 521418026 624/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">3</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">624/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Aditya Hasin Mooduto</p><p class="text-[10px] text-slate-400">NIM 521418026</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">25 Sep 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_KP_521418026.pdf</span><span class="f-sub">2.7 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="Moh. Ramdhan P" data-nim="521418057" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521418057.pdf" data-nomor="621/UN47.B5.5/TD.06/2024" data-tanggal="24 Sep 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521418057" data-cari="moh. ramdhan p 521418057 621/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">4</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">621/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Moh. Ramdhan P</p><p class="text-[10px] text-slate-400">NIM 521418057</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">24 Sep 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_Skripsi_521418057.pdf</span><span class="f-sub">1.7 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Yusran Moha" data-nim="521418039" data-file="Surat_Penunjukkan_Penguji_Hasil_521418039.pdf" data-nomor="612/UN47.B5.5/TD.06/2024" data-tanggal="19 Sep 2024" data-url="surat-penunjukkan-hasil/521418039" data-cari="yusran moha 521418039 612/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">5</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">612/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Yusran Moha</p><p class="text-[10px] text-slate-400">NIM 521418039</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">19 Sep 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Hasil_521418039.pdf</span><span class="f-sub">2.9 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="Sapril" data-nim="521420035" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521420035.pdf" data-nomor="610/UN47.B5.5/TD.06/2024" data-tanggal="18 Sep 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521420035" data-cari="sapril 521420035 610/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">6</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">610/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Sapril</p><p class="text-[10px] text-slate-400">NIM 521420035</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">18 Sep 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_Skripsi_521420035.pdf</span><span class="f-sub">1.8 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Nasrun Ishak" data-nim="521417004" data-file="Surat_Penunjukkan_Penguji_Tutup_521417004.pdf" data-nomor="407/UN47.B5.5/TD.06/2024" data-tanggal="5 Sep 2024" data-url="surat-penunjukkan-penguji/521417004" data-cari="nasrun ishak 521417004 407/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">7</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">407/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Nasrun Ishak</p><p class="text-[10px] text-slate-400">NIM 521417004</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">5 Sep 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Tutup_521417004.pdf</span><span class="f-sub">1.7 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="I Gusti Komang Apriadi" data-nim="521420010" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521420010.pdf" data-nomor="586/UN47.B5.5/TD.06/2024" data-tanggal="4 Sep 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521420010" data-cari="i gusti komang apriadi 521420010 586/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">8</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">586/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">I Gusti Komang Apriadi</p><p class="text-[10px] text-slate-400">NIM 521420010</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">4 Sep 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_Skripsi_521420010.pdf</span><span class="f-sub">2.2 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Nur Tahaja Ibrahim" data-nim="521420026" data-file="Surat_Penunjukkan_Penguji_KP_521420026.pdf" data-nomor="550/UN47.B5.5/TD.06/2024" data-tanggal="16 Agu 2024" data-url="" data-cari="nur tahaja ibrahim 521420026 550/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">9</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">550/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Nur Tahaja Ibrahim</p><p class="text-[10px] text-slate-400">NIM 521420026</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">16 Agu 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_KP_521420026.pdf</span><span class="f-sub">1.7 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Nur Tahaja Ibrahim" data-nim="521420026" data-file="Surat_Penunjukkan_Pembimbing_KP_521420026.pdf" data-nomor="178/UN47.B5.5/TD.06/2024" data-tanggal="8 Agu 2024" data-url="" data-cari="nur tahaja ibrahim 521420026 178/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">10</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">178/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Nur Tahaja Ibrahim</p><p class="text-[10px] text-slate-400">NIM 521420026</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">8 Agu 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_KP_521420026.pdf</span><span class="f-sub">2.2 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="Bagus Wijaya" data-nim="521420028" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521420028.pdf" data-nomor="526/UN47.B5.5/TD.06/2024" data-tanggal="6 Agu 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521420028" data-cari="bagus wijaya 521420028 526/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">11</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">526/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Bagus Wijaya</p><p class="text-[10px] text-slate-400">NIM 521420028</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">6 Agu 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_Skripsi_521420028.pdf</span><span class="f-sub">1.6 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="Kurniawan Ali Male" data-nim="521420036" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521420036.pdf" data-nomor="487/UN47.B5.5/TD.06/2024" data-tanggal="17 Jul 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521420036" data-cari="kurniawan ali male 521420036 487/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">12</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">487/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Kurniawan Ali Male</p><p class="text-[10px] text-slate-400">NIM 521420036</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">17 Jul 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_Skripsi_521420036.pdf</span><span class="f-sub">2.8 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="Moh. Rafli A. Bolilio " data-nim="521420003" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521420003.pdf" data-nomor="482/UN47.B5.5/TD.06/2024" data-tanggal="16 Jul 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521420003" data-cari="moh. rafli a. bolilio  521420003 482/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">13</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">482/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Moh. Rafli A. Bolilio </p><p class="text-[10px] text-slate-400">NIM 521420003</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">16 Jul 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_Skripsi_521420003.pdf</span><span class="f-sub">1.8 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Mauludin" data-nim="521418031" data-file="Surat_Penunjukkan_Penguji_Tutup_521418031.pdf" data-nomor="417/UN47.B5.5/TD.06/2024" data-tanggal="26 Jun 2024" data-url="surat-penunjukkan-penguji/521418031" data-cari="mauludin 521418031 417/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">14</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">417/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Mauludin</p><p class="text-[10px] text-slate-400">NIM 521418031</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">26 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Tutup_521418031.pdf</span><span class="f-sub">2.1 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Adi Purwanto Umar" data-nim="521418034" data-file="Surat_Penunjukkan_Penguji_Tutup_521418034.pdf" data-nomor="413/UN47.B5.5/TD.06/2024" data-tanggal="24 Jun 2024" data-url="surat-penunjukkan-penguji/521418034" data-cari="adi purwanto umar 521418034 413/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">15</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">413/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Adi Purwanto Umar</p><p class="text-[10px] text-slate-400">NIM 521418034</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">24 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Tutup_521418034.pdf</span><span class="f-sub">2.4 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Isnain Sarjan Mento " data-nim="521417055" data-file="Surat_Penunjukkan_Penguji_Tutup_521417055.pdf" data-nomor="387/UN47.B5.5/TD.06/2024" data-tanggal="21 Jun 2024" data-url="surat-penunjukkan-penguji/521417055" data-cari="isnain sarjan mento  521417055 387/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">16</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">387/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Isnain Sarjan Mento </p><p class="text-[10px] text-slate-400">NIM 521417055</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">21 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Tutup_521417055.pdf</span><span class="f-sub">2.6 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Nasrun Ishak" data-nim="521417004" data-file="Surat_Penunjukkan_Penguji_Tutup_521417004.pdf" data-nomor="407/UN47.B5.5/TD.06/2024" data-tanggal="20 Jun 2024" data-url="surat-penunjukkan-penguji/521417004" data-cari="nasrun ishak 521417004 407/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">17</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">407/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Nasrun Ishak</p><p class="text-[10px] text-slate-400">NIM 521417004</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">20 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Tutup_521417004.pdf</span><span class="f-sub">2.4 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Hendra Palakua" data-nim="521417016" data-file="Surat_Penunjukkan_Penguji_Tutup_521417016.pdf" data-nomor="384/UN47.B5.5/TD.06/2024" data-tanggal="14 Jun 2024" data-url="surat-penunjukkan-penguji/521417016" data-cari="hendra palakua 521417016 384/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">18</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">384/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Hendra Palakua</p><p class="text-[10px] text-slate-400">NIM 521417016</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">14 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Tutup_521417016.pdf</span><span class="f-sub">2.4 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Anggi" data-nim="521417022" data-file="Surat_Penunjukkan_Penguji_Tutup_521417022.pdf" data-nomor="410/UN47.B5.5/TD.06/2024" data-tanggal="13 Jun 2024" data-url="surat-penunjukkan-penguji/521417022" data-cari="anggi 521417022 410/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">19</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">410/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Anggi</p><p class="text-[10px] text-slate-400">NIM 521417022</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">13 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Tutup_521417022.pdf</span><span class="f-sub">2.6 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Nasrun Ishak" data-nim="521417004" data-file="Surat_Penunjukkan_Penguji_Tutup_521417004.pdf" data-nomor="407/UN47.B5.5/TD.06/2024" data-tanggal="13 Jun 2024" data-url="surat-penunjukkan-penguji/521417004" data-cari="nasrun ishak 521417004 407/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">20</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">407/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Nasrun Ishak</p><p class="text-[10px] text-slate-400">NIM 521417004</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">13 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Tutup_521417004.pdf</span><span class="f-sub">2.1 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Aditya Saputra Amin" data-nim="521417052" data-file="Surat_Penunjukkan_Penguji_Hasil_521417052.pdf" data-nomor="376/UN47.B5.5/TD.06/2024" data-tanggal="12 Jun 2024" data-url="surat-penunjukkan-hasil/521417052" data-cari="aditya saputra amin 521417052 376/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">21</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">376/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Aditya Saputra Amin</p><p class="text-[10px] text-slate-400">NIM 521417052</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">12 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Hasil_521417052.pdf</span><span class="f-sub">1.7 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Nasrun Ishak" data-nim="521417004" data-file="Surat_Penunjukkan_Penguji_Hasil_521417004.pdf" data-nomor="375/UN47.B5.5/TD.06/2024" data-tanggal="12 Jun 2024" data-url="surat-penunjukkan-hasil/521417004" data-cari="nasrun ishak 521417004 375/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">22</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">375/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Nasrun Ishak</p><p class="text-[10px] text-slate-400">NIM 521417004</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">12 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Hasil_521417004.pdf</span><span class="f-sub">1.9 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Anggi" data-nim="521417022" data-file="Surat_Penunjukkan_Penguji_Hasil_521417022.pdf" data-nomor="374/UN47.B5.5/TD.06/2024" data-tanggal="12 Jun 2024" data-url="surat-penunjukkan-hasil/521417022" data-cari="anggi 521417022 374/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">23</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">374/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Anggi</p><p class="text-[10px] text-slate-400">NIM 521417022</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">12 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Hasil_521417022.pdf</span><span class="f-sub">2.3 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Abd Hayan Arune" data-nim="521417031" data-file="Surat_Penunjukkan_Penguji_Hasil_521417031.pdf" data-nomor="347/UN47.B5.5/TD.06/2024" data-tanggal="9 Jun 2024" data-url="surat-penunjukkan-hasil/521417031" data-cari="abd hayan arune 521417031 347/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">24</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">347/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Abd Hayan Arune</p><p class="text-[10px] text-slate-400">NIM 521417031</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">9 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Hasil_521417031.pdf</span><span class="f-sub">2.1 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Muh Syukriadi Ansar" data-nim="521417034" data-file="Surat_Penunjukkan_Penguji_Hasil_521417034.pdf" data-nomor="344/UN47.B5.5/TD.06/2024" data-tanggal="8 Jun 2024" data-url="surat-penunjukkan-hasil/521417034" data-cari="muh syukriadi ansar 521417034 344/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">25</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">344/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Muh Syukriadi Ansar</p><p class="text-[10px] text-slate-400">NIM 521417034</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">8 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Hasil_521417034.pdf</span><span class="f-sub">2.2 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Yulanda S.ishak" data-nim="521420033" data-file="Surat_Penunjukkan_Penguji_KP_521420033.pdf" data-nomor="337/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="" data-cari="yulanda s.ishak 521420033 337/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">26</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">337/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Yulanda S.ishak</p><p class="text-[10px] text-slate-400">NIM 521420033</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_KP_521420033.pdf</span><span class="f-sub">2.1 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Hijratun A. Samadi" data-nim="521420004" data-file="Surat_Penunjukkan_Penguji_KP_521420004.pdf" data-nomor="336/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="" data-cari="hijratun a. samadi 521420004 336/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">27</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">336/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Hijratun A. Samadi</p><p class="text-[10px] text-slate-400">NIM 521420004</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_KP_521420004.pdf</span><span class="f-sub">2.5 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Sahrul Duhi" data-nim="521420032" data-file="Surat_Penunjukkan_Penguji_KP_521420032.pdf" data-nomor="335/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="" data-cari="sahrul duhi 521420032 335/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">28</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">335/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Sahrul Duhi</p><p class="text-[10px] text-slate-400">NIM 521420032</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_KP_521420032.pdf</span><span class="f-sub">2.9 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Mohammad Rifki Iti" data-nim="521420030" data-file="Surat_Penunjukkan_Penguji_KP_521420030.pdf" data-nomor="334/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="" data-cari="mohammad rifki iti 521420030 334/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">29</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">334/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Mohammad Rifki Iti</p><p class="text-[10px] text-slate-400">NIM 521420030</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_KP_521420030.pdf</span><span class="f-sub">2.7 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Ariyani Djafar" data-nim="521420014" data-file="Surat_Penunjukkan_Penguji_KP_521420014.pdf" data-nomor="333/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="" data-cari="ariyani djafar 521420014 333/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">30</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">333/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Ariyani Djafar</p><p class="text-[10px] text-slate-400">NIM 521420014</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_KP_521420014.pdf</span><span class="f-sub">1.7 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Zul Fikrah Arifuddin" data-nim="521420027" data-file="Surat_Penunjukkan_Penguji_KP_521420027.pdf" data-nomor="332/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="" data-cari="zul fikrah arifuddin 521420027 332/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">31</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">332/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Zul Fikrah Arifuddin</p><p class="text-[10px] text-slate-400">NIM 521420027</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_KP_521420027.pdf</span><span class="f-sub">2.0 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Muh. Arya Sumarno" data-nim="521420016" data-file="Surat_Penunjukkan_Penguji_KP_521420016.pdf" data-nomor="331/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="" data-cari="muh. arya sumarno 521420016 331/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">32</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">331/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Muh. Arya Sumarno</p><p class="text-[10px] text-slate-400">NIM 521420016</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_KP_521420016.pdf</span><span class="f-sub">1.8 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Akdar" data-nim="521418012" data-file="Surat_Penunjukkan_Penguji_Hasil_521418012.pdf" data-nomor="328/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="surat-penunjukkan-hasil/521418012" data-cari="akdar 521418012 328/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">33</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">328/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Akdar</p><p class="text-[10px] text-slate-400">NIM 521418012</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Hasil_521418012.pdf</span><span class="f-sub">2.2 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Adi Purwanto Umar" data-nim="521418034" data-file="Surat_Penunjukkan_Penguji_Hasil_521418034.pdf" data-nomor="327/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="surat-penunjukkan-hasil/521418034" data-cari="adi purwanto umar 521418034 327/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">34</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">327/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Adi Purwanto Umar</p><p class="text-[10px] text-slate-400">NIM 521418034</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Penguji_Hasil_521418034.pdf</span><span class="f-sub">1.7 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Suhartin Rauf" data-nim="521420023" data-file="Surat_Penunjukkan_Pembimbing_KP_521420023.pdf" data-nomor="177/UN47.B5.5/TD.06/2024" data-tanggal="18 Mar 2024" data-url="" data-cari="suhartin rauf 521420023 177/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">35</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">177/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Suhartin Rauf</p><p class="text-[10px] text-slate-400">NIM 521420023</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">18 Mar 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_KP_521420023.pdf</span><span class="f-sub">2.4 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Stella Maria Rontopaly" data-nim="521420017" data-file="Surat_Penunjukkan_Pembimbing_KP_521420017.pdf" data-nomor="176/UN47.B5.5/TD.06/2024" data-tanggal="18 Mar 2024" data-url="" data-cari="stella maria rontopaly 521420017 176/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">36</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">176/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Stella Maria Rontopaly</p><p class="text-[10px] text-slate-400">NIM 521420017</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">18 Mar 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_KP_521420017.pdf</span><span class="f-sub">1.6 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Sahrul Duhi" data-nim="521420032" data-file="Surat_Penunjukkan_Pembimbing_KP_521420032.pdf" data-nomor="165/UN47.B5.5/TD.06/2024" data-tanggal="7 Mar 2024" data-url="" data-cari="sahrul duhi 521420032 165/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">37</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">165/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Sahrul Duhi</p><p class="text-[10px] text-slate-400">NIM 521420032</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Mar 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_KP_521420032.pdf</span><span class="f-sub">2.4 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Yulanda S.ishak" data-nim="521420033" data-file="Surat_Penunjukkan_Pembimbing_KP_521420033.pdf" data-nomor="166/UN47.B5.5/TD.06/2024" data-tanggal="7 Mar 2024" data-url="" data-cari="yulanda s.ishak 521420033 166/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">38</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">166/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Yulanda S.ishak</p><p class="text-[10px] text-slate-400">NIM 521420033</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Mar 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_KP_521420033.pdf</span><span class="f-sub">1.8 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Sahrul Duhi" data-nim="521420032" data-file="Surat_Penunjukkan_Pembimbing_KP_521420032.pdf" data-nomor="165/UN47.B5.5/TD.06/2024" data-tanggal="7 Mar 2024" data-url="" data-cari="sahrul duhi 521420032 165/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">39</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">165/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Sahrul Duhi</p><p class="text-[10px] text-slate-400">NIM 521420032</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Mar 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_KP_521420032.pdf</span><span class="f-sub">2.8 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-hapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Mohammad Rifki Iti" data-nim="521420030" data-file="Surat_Penunjukkan_Pembimbing_KP_521420030.pdf" data-nomor="164/UN47.B5.5/TD.06/2024" data-tanggal="7 Mar 2024" data-url="" data-cari="mohammad rifki iti 521420030 164/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">40</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">164/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Mohammad Rifki Iti</p><p class="text-[10px] text-slate-400">NIM 521420030</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Mar 2024</td>
                            <td class="px-4 py-4"><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name">Surat_Penunjukkan_Pembimbing_KP_521420030.pdf</span><span class="f-sub">2.2 MB</span></span><button type="button" class="f-down pn-cetak" title="Buka PDF"><i class="fas fa-file-pdf"></i></button></span></td>
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

<!-- ===== Modal Pembagian Beban Dosen ===== -->
<div class="modal-overlay" id="statModal" role="dialog" aria-modal="true">
    <div class="flex max-h-[88vh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-5 py-3.5">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-sm text-slate-600"><i class="fas fa-chart-simple"></i></span>
                <div>
                    <h6 class="text-sm font-semibold text-slate-900">Pembagian Beban Dosen</h6>
                    <p class="text-[11px] text-slate-400">Jumlah penunjukan per dosen, skripsi &amp; kerja praktek.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <select id="stTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold outline-none focus:border-orange-400"></select>
                <button type="button" class="text-xl leading-none text-slate-400 hover:text-slate-700" data-stat-close>&times;</button>
            </div>
        </div>
        <div class="overflow-auto">
            <table class="stat-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-left">No</th>
                        <th rowspan="2" class="text-left">Dosen</th>
                        <th colspan="4" class="grp">Skripsi</th>
                        <th colspan="3" class="grp gsep">Kerja Praktek</th>
                        <th rowspan="2" class="text-right">Total</th>
                    </tr>
                    <tr>
                        <th class="text-center">Pemb 1</th>
                        <th class="text-center">Pemb 2</th>
                        <th class="text-center">Hasil</th>
                        <th class="text-center">Tutup</th>
                        <th class="gsep text-center">Pemb 1</th>
                        <th class="text-center">Pemb 2</th>
                        <th class="text-center">Penguji</th>
                    </tr>
                </thead>
                <tbody id="tbStat"></tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 bg-slate-50 px-5 py-2.5">
            <p class="text-[11px] text-slate-400"><i class="fas fa-circle-info mr-1 text-orange-400"></i>Dihitung dari seluruh surat penunjukkan pada tahun terpilih.</p>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toastPn"></div>

<script>
(function () {
    var rows = Array.prototype.slice.call(document.querySelectorAll('#tbPn tr'));
    var fCari = document.getElementById('fCari');
    var fTahun = document.getElementById('fTahun');
    var fJenis = document.getElementById('fJenis');
    var page = 1, PER = 6;

    var LABEL_JENIS = {
        pemskr: 'Pembimbing Skripsi',
        pemkp:  'Pembimbing KP',
        hasil:  'Penguji Hasil',
        pengkp: 'Penguji KP',
        tutup:  'Penguji Tutup'
    };

    var toast = document.getElementById('toastPn');
    function showToast(msg, isErr) {
        toast.textContent = msg;
        toast.className = 'show ' + (isErr ? 'bg-rose-600' : 'bg-slate-900');
        clearTimeout(showToast._t);
        showToast._t = setTimeout(function () { toast.className = ''; }, 2000);
    }

    function recount() {
        var c = {};
        rows.forEach(function (tr) {
            var j = tr.getAttribute('data-tab-jenis');
            c[j] = (c[j] || 0) + 1;
        });
        Array.prototype.forEach.call(fJenis.options, function (op) {
            if (!op.value || !LABEL_JENIS[op.value]) return;
            op.textContent = LABEL_JENIS[op.value] + ' (' + (c[op.value] || 0) + ')';
        });
    }

    function visible() {
        var kata = (fCari.value || '').toLowerCase().trim();
        var th = fTahun.value;
        var jn = fJenis.value;
        return rows.filter(function (tr) {
            if (jn && tr.getAttribute('data-tab-jenis') !== jn) return false;
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

    /* ===== Filter ===== */
    fCari.addEventListener('input', function () { page = 1; render(); });
    fTahun.addEventListener('change', function () { page = 1; render(); });
    fJenis.addEventListener('change', function () { page = 1; render(); });
    document.getElementById('btnReset').addEventListener('click', function () {
        fCari.value = ''; fTahun.value = ''; fJenis.value = ''; page = 1; render();
    });

    /* ===== Aksi baris: unduh/buka PDF & hapus (dummy) ===== */
    function openSurat(tr) {
        var url = tr.getAttribute('data-url');
        if (!url) { showToast('Template surat kategori ini belum tersedia', true); return; }
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

    /* ===== Statistik Beban Dosen ===== */
    var STAT_DOSEN = {"2024":[{"n":"Dr. Lanto M. Kamil Amali,ST.,MT","s1":5,"s2":2,"sh":10,"st":19,"k1":1,"k2":0,"kp":0,"t":37},{"n":"Jumiati Ilham,ST.,MT","s1":6,"s2":1,"sh":8,"st":14,"k1":2,"k2":0,"kp":6,"t":37},{"n":"Yasin Mohamad,ST.,MT","s1":1,"s2":5,"sh":7,"st":11,"k1":3,"k2":0,"kp":5,"t":32},{"n":"Taufiq Ismail Yusuf,ST.,M.Si","s1":2,"s2":2,"sh":8,"st":13,"k1":1,"k2":0,"kp":3,"t":29},{"n":"Ade Irawaty Tolago,ST.,MT","s1":0,"s2":1,"sh":7,"st":11,"k1":1,"k2":0,"kp":3,"t":23},{"n":"Amirudin Yunus Dako,ST.,M.Eng","s1":1,"s2":1,"sh":6,"st":6,"k1":1,"k2":0,"kp":8,"t":23},{"n":"Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.","s1":1,"s2":1,"sh":7,"st":13,"k1":0,"k2":0,"kp":1,"t":23},{"n":"Ervan Hasan Harun,ST.,MT","s1":1,"s2":3,"sh":5,"st":9,"k1":1,"k2":0,"kp":3,"t":22},{"n":"Ifan Wiranto,ST.,MT","s1":3,"s2":1,"sh":6,"st":6,"k1":0,"k2":0,"kp":3,"t":19},{"n":"Salmawaty Tansa,ST.,M.Eng","s1":2,"s2":1,"sh":4,"st":4,"k1":3,"k2":0,"kp":5,"t":19},{"n":"Dr.Ir. Arifin Matoka,MT","s1":0,"s2":2,"sh":6,"st":8,"k1":0,"k2":0,"kp":1,"t":17},{"n":"Zainudin Bonok,ST.,MT","s1":0,"s2":2,"sh":5,"st":5,"k1":0,"k2":0,"kp":4,"t":16},{"n":"Rahmat D.R. Dako, ST., M.Eng","s1":1,"s2":1,"sh":5,"st":5,"k1":0,"k2":0,"kp":3,"t":15},{"n":"Ir. Wahab Musa,MT.,Ph.D","s1":1,"s2":0,"sh":4,"st":4,"k1":1,"k2":0,"kp":3,"t":13},{"n":"Dr. Bambang Panji Asmara,ST.,MT","s1":0,"s2":1,"sh":2,"st":2,"k1":1,"k2":0,"kp":4,"t":10},{"n":"Iskandar Z. Nasibu, S.Pd, M.Eng","s1":1,"s2":1,"sh":2,"st":2,"k1":0,"k2":0,"kp":2,"t":8},{"n":"Syahrir Abdussamad,ST.,MT","s1":0,"s2":0,"sh":2,"st":2,"k1":0,"k2":0,"kp":3,"t":7},{"n":"Idham Halid Lahay, ST., M.Sc","s1":0,"s2":0,"sh":1,"st":1,"k1":0,"k2":0,"kp":0,"t":2}],"2023":[{"n":"Ir. Wahab Musa,MT.,Ph.D","s1":5,"s2":0,"sh":12,"st":12,"k1":2,"k2":0,"kp":5,"t":36},{"n":"Jumiati Ilham,ST.,MT","s1":6,"s2":2,"sh":7,"st":5,"k1":6,"k2":0,"kp":10,"t":36},{"n":"Taufiq Ismail Yusuf,ST.,M.Si","s1":5,"s2":1,"sh":9,"st":9,"k1":1,"k2":0,"kp":9,"t":34},{"n":"Iskandar Z. Nasibu, S.Pd, M.Eng","s1":1,"s2":4,"sh":11,"st":11,"k1":2,"k2":0,"kp":4,"t":33},{"n":"Ade Irawaty Tolago,ST.,MT","s1":0,"s2":3,"sh":10,"st":9,"k1":1,"k2":0,"kp":9,"t":32},{"n":"Dr. Lanto M. Kamil Amali,ST.,MT","s1":7,"s2":0,"sh":11,"st":8,"k1":1,"k2":0,"kp":3,"t":30},{"n":"Ervan Hasan Harun,ST.,MT","s1":1,"s2":7,"sh":5,"st":3,"k1":6,"k2":0,"kp":8,"t":30},{"n":"Ifan Wiranto,ST.,MT","s1":0,"s2":4,"sh":9,"st":9,"k1":2,"k2":0,"kp":5,"t":29},{"n":"Syahrir Abdussamad,ST.,MT","s1":0,"s2":1,"sh":9,"st":9,"k1":1,"k2":0,"kp":6,"t":26},{"n":"Amirudin Yunus Dako,ST.,M.Eng","s1":2,"s2":1,"sh":3,"st":4,"k1":2,"k2":0,"kp":13,"t":25},{"n":"Rahmat D.R. Dako, ST., M.Eng","s1":2,"s2":2,"sh":5,"st":5,"k1":3,"k2":0,"kp":8,"t":25},{"n":"Yasin Mohamad,ST.,MT","s1":2,"s2":5,"sh":5,"st":4,"k1":1,"k2":0,"kp":7,"t":24},{"n":"Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.","s1":1,"s2":1,"sh":9,"st":6,"k1":1,"k2":0,"kp":2,"t":20},{"n":"Zainudin Bonok,ST.,MT","s1":1,"s2":1,"sh":5,"st":5,"k1":2,"k2":0,"kp":6,"t":20},{"n":"Wrastawa Ridwan,ST.,MT, MCE, CDSEA","s1":1,"s2":0,"sh":6,"st":7,"k1":1,"k2":0,"kp":1,"t":16},{"n":"Dr. Bambang Panji Asmara,ST.,MT","s1":1,"s2":0,"sh":7,"st":5,"k1":0,"k2":0,"kp":1,"t":14},{"n":"Salmawaty Tansa,ST.,M.Eng","s1":1,"s2":0,"sh":5,"st":5,"k1":0,"k2":0,"kp":1,"t":12},{"n":"Dr.Ir. Arifin Matoka,MT","s1":0,"s2":2,"sh":4,"st":1,"k1":0,"k2":0,"kp":2,"t":9},{"n":"Ikhsan Hidayat, S.Kom, M.T","s1":0,"s2":1,"sh":3,"st":3,"k1":0,"k2":0,"kp":2,"t":9},{"n":"Idham Halid Lahay, ST., M.Sc","s1":0,"s2":1,"sh":0,"st":0,"k1":0,"k2":0,"kp":0,"t":1}],"2022":[{"n":"Dr. Lanto M. Kamil Amali,ST.,MT","s1":6,"s2":1,"sh":15,"st":13,"k1":3,"k2":0,"kp":5,"t":43},{"n":"Ade Irawaty Tolago,ST.,MT","s1":0,"s2":3,"sh":12,"st":10,"k1":4,"k2":0,"kp":8,"t":37},{"n":"Taufiq Ismail Yusuf,ST.,M.Si","s1":1,"s2":1,"sh":11,"st":10,"k1":1,"k2":0,"kp":8,"t":32},{"n":"Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.","s1":1,"s2":0,"sh":13,"st":12,"k1":1,"k2":0,"kp":2,"t":29},{"n":"Jumiati Ilham,ST.,MT","s1":5,"s2":3,"sh":9,"st":7,"k1":2,"k2":0,"kp":3,"t":29},{"n":"Yasin Mohamad,ST.,MT","s1":1,"s2":1,"sh":11,"st":10,"k1":2,"k2":0,"kp":4,"t":29},{"n":"Iskandar Z. Nasibu, S.Pd, M.Eng","s1":0,"s2":5,"sh":7,"st":7,"k1":0,"k2":0,"kp":2,"t":21},{"n":"Ir. Wahab Musa,MT.,Ph.D","s1":5,"s2":1,"sh":6,"st":5,"k1":0,"k2":0,"kp":3,"t":20},{"n":"Dr.Ir. Arifin Matoka,MT","s1":0,"s2":0,"sh":6,"st":6,"k1":1,"k2":0,"kp":4,"t":17},{"n":"Ifan Wiranto,ST.,MT","s1":0,"s2":1,"sh":5,"st":5,"k1":2,"k2":0,"kp":3,"t":16},{"n":"Dr. Bambang Panji Asmara,ST.,MT","s1":0,"s2":0,"sh":5,"st":5,"k1":2,"k2":0,"kp":3,"t":15},{"n":"Ervan Hasan Harun,ST.,MT","s1":0,"s2":3,"sh":4,"st":4,"k1":1,"k2":0,"kp":3,"t":15},{"n":"Syahrir Abdussamad,ST.,MT","s1":0,"s2":2,"sh":5,"st":5,"k1":0,"k2":0,"kp":3,"t":15},{"n":"Rahmat D.R. Dako, ST., M.Eng","s1":2,"s2":1,"sh":3,"st":2,"k1":2,"k2":0,"kp":4,"t":14},{"n":"Salmawaty Tansa,ST.,M.Eng","s1":2,"s2":0,"sh":3,"st":4,"k1":0,"k2":0,"kp":0,"t":9},{"n":"Zainudin Bonok,ST.,MT","s1":0,"s2":0,"sh":1,"st":1,"k1":2,"k2":0,"kp":5,"t":9},{"n":"Wrastawa Ridwan,ST.,MT, MCE, CDSEA","s1":1,"s2":2,"sh":2,"st":2,"k1":0,"k2":0,"kp":1,"t":8},{"n":"Ikhsan Hidayat, S.Kom, M.T","s1":0,"s2":0,"sh":2,"st":2,"k1":0,"k2":0,"kp":3,"t":7},{"n":"Amirudin Yunus Dako,ST.,M.Eng","s1":0,"s2":0,"sh":0,"st":0,"k1":1,"k2":0,"kp":2,"t":3}],"2021":[{"n":"Taufiq Ismail Yusuf,ST.,M.Si","s1":4,"s2":2,"sh":12,"st":12,"k1":0,"k2":0,"kp":1,"t":31},{"n":"Dr. Lanto M. Kamil Amali,ST.,MT","s1":3,"s2":2,"sh":12,"st":13,"k1":0,"k2":0,"kp":0,"t":30},{"n":"Yasin Mohamad,ST.,MT","s1":0,"s2":4,"sh":12,"st":13,"k1":0,"k2":0,"kp":0,"t":29},{"n":"Ade Irawaty Tolago,ST.,MT","s1":0,"s2":2,"sh":10,"st":11,"k1":1,"k2":0,"kp":1,"t":25},{"n":"Jumiati Ilham,ST.,MT","s1":4,"s2":0,"sh":9,"st":10,"k1":1,"k2":0,"kp":1,"t":25},{"n":"Dr.Ir. Arifin Matoka,MT","s1":1,"s2":0,"sh":10,"st":11,"k1":0,"k2":0,"kp":1,"t":23},{"n":"Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.","s1":1,"s2":0,"sh":9,"st":11,"k1":0,"k2":0,"kp":1,"t":22},{"n":"Syahrir Abdussamad,ST.,MT","s1":0,"s2":0,"sh":6,"st":6,"k1":0,"k2":0,"kp":0,"t":12},{"n":"Ir. Wahab Musa,MT.,Ph.D","s1":1,"s2":0,"sh":5,"st":5,"k1":0,"k2":0,"kp":0,"t":11},{"n":"Ervan Hasan Harun,ST.,MT","s1":0,"s2":2,"sh":3,"st":4,"k1":0,"k2":0,"kp":1,"t":10},{"n":"Iskandar Z. Nasibu, S.Pd, M.Eng","s1":0,"s2":3,"sh":4,"st":3,"k1":0,"k2":0,"kp":0,"t":10},{"n":"Rahmat D.R. Dako, ST., M.Eng","s1":0,"s2":0,"sh":2,"st":2,"k1":1,"k2":0,"kp":1,"t":6},{"n":"Sri Wahyuni Dali,ST.,MT","s1":0,"s2":0,"sh":2,"st":2,"k1":0,"k2":0,"kp":1,"t":5},{"n":"Zainudin Bonok,ST.,MT","s1":0,"s2":0,"sh":2,"st":2,"k1":0,"k2":0,"kp":0,"t":4},{"n":"Ifan Wiranto,ST.,MT","s1":0,"s2":0,"sh":2,"st":1,"k1":0,"k2":0,"kp":0,"t":3},{"n":"Salmawaty Tansa,ST.,M.Eng","s1":0,"s2":0,"sh":2,"st":1,"k1":0,"k2":0,"kp":0,"t":3},{"n":"Wrastawa Ridwan,ST.,MT, MCE, CDSEA","s1":1,"s2":0,"sh":1,"st":1,"k1":0,"k2":0,"kp":0,"t":3},{"n":"Dr. Bambang Panji Asmara,ST.,MT","s1":0,"s2":0,"sh":1,"st":1,"k1":0,"k2":0,"kp":0,"t":2},{"n":"Jamal Darussalam Giu,ST.,MT","s1":0,"s2":0,"sh":1,"st":1,"k1":0,"k2":0,"kp":0,"t":2},{"n":"Ikhsan Hidayat, S.Kom, M.T","s1":0,"s2":0,"sh":0,"st":0,"k1":0,"k2":0,"kp":1,"t":1}],"2020":[{"n":"Iskandar Z. Nasibu, S.Pd, M.Eng","s1":0,"s2":4,"sh":1,"st":1,"k1":0,"k2":0,"kp":0,"t":6},{"n":"Yasin Mohamad,ST.,MT","s1":3,"s2":0,"sh":2,"st":1,"k1":0,"k2":0,"kp":0,"t":6},{"n":"Ade Irawaty Tolago,ST.,MT","s1":0,"s2":2,"sh":2,"st":1,"k1":0,"k2":0,"kp":0,"t":5},{"n":"Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.","s1":1,"s2":2,"sh":2,"st":0,"k1":0,"k2":0,"kp":0,"t":5},{"n":"Jumiati Ilham,ST.,MT","s1":1,"s2":1,"sh":1,"st":0,"k1":1,"k2":0,"kp":1,"t":5},{"n":"Taufiq Ismail Yusuf,ST.,M.Si","s1":1,"s2":0,"sh":2,"st":1,"k1":0,"k2":0,"kp":1,"t":5},{"n":"Dr. Lanto M. Kamil Amali,ST.,MT","s1":0,"s2":1,"sh":2,"st":1,"k1":0,"k2":0,"kp":0,"t":4},{"n":"Ervan Hasan Harun,ST.,MT","s1":2,"s2":0,"sh":1,"st":0,"k1":0,"k2":0,"kp":1,"t":4},{"n":"Jamal Darussalam Giu,ST.,MT","s1":0,"s2":2,"sh":1,"st":1,"k1":0,"k2":0,"kp":0,"t":4},{"n":"Wrastawa Ridwan,ST.,MT, MCE, CDSEA","s1":1,"s2":1,"sh":1,"st":1,"k1":0,"k2":0,"kp":0,"t":4},{"n":"Ir. Wahab Musa,MT.,Ph.D","s1":1,"s2":0,"sh":1,"st":1,"k1":0,"k2":0,"kp":0,"t":3},{"n":"Zainudin Bonok,ST.,MT","s1":1,"s2":0,"sh":1,"st":1,"k1":0,"k2":0,"kp":0,"t":3},{"n":"Dr.Ir. Arifin Matoka,MT","s1":1,"s2":0,"sh":1,"st":0,"k1":0,"k2":0,"kp":0,"t":2},{"n":"Rahmat D.R. Dako, ST., M.Eng","s1":1,"s2":1,"sh":0,"st":0,"k1":0,"k2":0,"kp":0,"t":2},{"n":"Sri Wahyuni Dali,ST.,MT","s1":0,"s2":0,"sh":1,"st":1,"k1":0,"k2":0,"kp":0,"t":2},{"n":"Syahrir Abdussamad,ST.,MT","s1":0,"s2":1,"sh":1,"st":0,"k1":0,"k2":0,"kp":0,"t":2},{"n":"Dr. Bambang Panji Asmara,ST.,MT","s1":1,"s2":0,"sh":0,"st":0,"k1":0,"k2":0,"kp":0,"t":1},{"n":"Salmawaty Tansa,ST.,M.Eng","s1":1,"s2":0,"sh":0,"st":0,"k1":0,"k2":0,"kp":0,"t":1}]};
    var stModal = document.getElementById('statModal');
    var stTahun = document.getElementById('stTahun');
    var tbStat = document.getElementById('tbStat');

    function statCell(v, sep) {
        return '<td class="' + (sep ? 'gsep ' : '') + 'text-center tabular-nums ' + (v ? 'text-slate-700' : 'text-slate-300') + '">' + v + '</td>';
    }
    function renderStat() {
        var list = STAT_DOSEN[stTahun.value] || [];
        var tot = { s1:0,s2:0,sh:0,st:0,k1:0,k2:0,kp:0,t:0 };
        var h = '';
        list.forEach(function (d, i) {
            ['s1','s2','sh','st','k1','k2','kp','t'].forEach(function (k) { tot[k] += d[k]; });
            h += '<tr class="' + (i % 2 ? 'bg-slate-50/60' : 'bg-white') + '">'
                + '<td class="pl-5 pr-3 text-slate-400">' + (i + 1) + '</td>'
                + '<td class="pr-5 font-medium text-slate-800">' + d.n + '</td>'
                + statCell(d.s1) + statCell(d.s2) + statCell(d.sh) + statCell(d.st)
                + statCell(d.k1, true) + statCell(d.k2) + statCell(d.kp)
                + '<td class="px-5 text-right font-bold tabular-nums text-slate-900">' + d.t + '</td>'
                + '</tr>';
        });
        h += '<tr class="bg-slate-100/80">'
            + '<td class="pl-5 pr-3"></td>'
            + '<td class="pr-5 font-bold uppercase tracking-wide text-slate-600">Total</td>'
            + statCell(tot.s1) + statCell(tot.s2) + statCell(tot.sh) + statCell(tot.st)
            + statCell(tot.k1, true) + statCell(tot.k2) + statCell(tot.kp)
            + '<td class="px-5 text-right font-extrabold tabular-nums text-slate-900">' + tot.t + '</td>'
            + '</tr>';
        document.getElementById('stRingkas').textContent = list.length + ' dosen \u00b7 ' + tot.t + ' penunjukan \u00b7 tahun ' + (stTahun.value || '-');
        tbStat.innerHTML = h;
    }

    Object.keys(STAT_DOSEN).sort().reverse().forEach(function (y) {
        var op = document.createElement('option');
        op.value = y; op.textContent = y;
        stTahun.appendChild(op);
    });
    stTahun.addEventListener('change', renderStat);
    document.getElementById('btnStat').addEventListener('click', function () {
        stModal.classList.add('show');
    });
    stModal.querySelectorAll('[data-stat-close]').forEach(function (b) {
        b.addEventListener('click', function () { stModal.classList.remove('show'); });
    });
    stModal.addEventListener('click', function (e) { if (e.target === stModal) stModal.classList.remove('show'); });
    renderStat();

    recount();
    render();
})();
</script>
