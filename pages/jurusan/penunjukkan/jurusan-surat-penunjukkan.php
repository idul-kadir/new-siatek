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
    .btn-circle .tip { position: absolute; bottom: calc(100% + 6px); right: 0; background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    /* Tooltip kini muncul DI ATAS tombol & melebar ke KIRI (ke dalam tabel),
       supaya tidak terpotong tepi kotak scroll (overflow auto memotong 2 arah)
       dan tidak tertimpa baris setelahnya. Baris di-hover tetap diangkat. */
    #tbPn tbody tr { position: relative; }
    #tbPn tbody tr:hover { z-index: 30; }

    .pt-badge { display: inline-block; padding: .18rem .55rem; border-radius: .45rem; font-size: 10px; font-weight: 700; white-space: nowrap; }

    .pin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .pin-table thead th { background: #f8fafc; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; text-align: left; padding: .6rem .85rem; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
    .pin-table tbody td { padding: .7rem .85rem; font-size: .78rem; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .pin-table tbody tr:last-child td { border-bottom: none; }
    .pin-table tbody tr { transition: background .15s ease; }
    .pin-table tbody tr:hover { background: #f8fafc; }


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
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="Yudi Anas Sahri" data-nim="521419020" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521419020.pdf" data-nomor="627/UN47.B5.5/TD.06/2024" data-tanggal="26 Sep 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521419020" data-cari="yudi anas sahri 521419020 627/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">2</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">627/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Yudi Anas Sahri</p><p class="text-[10px] text-slate-400">NIM 521419020</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">26 Sep 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Aditya Hasin Mooduto" data-nim="521418026" data-file="Surat_Penunjukkan_Pembimbing_KP_521418026.pdf" data-nomor="624/UN47.B5.5/TD.06/2024" data-tanggal="25 Sep 2024" data-url="" data-cari="aditya hasin mooduto 521418026 624/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">3</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">624/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Aditya Hasin Mooduto</p><p class="text-[10px] text-slate-400">NIM 521418026</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">25 Sep 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="Moh. Ramdhan P" data-nim="521418057" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521418057.pdf" data-nomor="621/UN47.B5.5/TD.06/2024" data-tanggal="24 Sep 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521418057" data-cari="moh. ramdhan p 521418057 621/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">4</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">621/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Moh. Ramdhan P</p><p class="text-[10px] text-slate-400">NIM 521418057</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">24 Sep 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Yusran Moha" data-nim="521418039" data-file="Surat_Penunjukkan_Penguji_Hasil_521418039.pdf" data-nomor="612/UN47.B5.5/TD.06/2024" data-tanggal="19 Sep 2024" data-url="surat-penunjukkan-hasil/521418039" data-cari="yusran moha 521418039 612/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">5</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">612/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Yusran Moha</p><p class="text-[10px] text-slate-400">NIM 521418039</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">19 Sep 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="Sapril" data-nim="521420035" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521420035.pdf" data-nomor="610/UN47.B5.5/TD.06/2024" data-tanggal="18 Sep 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521420035" data-cari="sapril 521420035 610/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">6</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">610/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Sapril</p><p class="text-[10px] text-slate-400">NIM 521420035</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">18 Sep 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Nasrun Ishak" data-nim="521417004" data-file="Surat_Penunjukkan_Penguji_Tutup_521417004.pdf" data-nomor="407/UN47.B5.5/TD.06/2024" data-tanggal="5 Sep 2024" data-url="surat-penunjukkan-penguji/521417004" data-cari="nasrun ishak 521417004 407/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">7</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">407/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Nasrun Ishak</p><p class="text-[10px] text-slate-400">NIM 521417004</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">5 Sep 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="I Gusti Komang Apriadi" data-nim="521420010" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521420010.pdf" data-nomor="586/UN47.B5.5/TD.06/2024" data-tanggal="4 Sep 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521420010" data-cari="i gusti komang apriadi 521420010 586/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">8</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">586/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">I Gusti Komang Apriadi</p><p class="text-[10px] text-slate-400">NIM 521420010</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">4 Sep 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Nur Tahaja Ibrahim" data-nim="521420026" data-file="Surat_Penunjukkan_Penguji_KP_521420026.pdf" data-nomor="550/UN47.B5.5/TD.06/2024" data-tanggal="16 Agu 2024" data-url="" data-cari="nur tahaja ibrahim 521420026 550/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">9</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">550/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Nur Tahaja Ibrahim</p><p class="text-[10px] text-slate-400">NIM 521420026</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">16 Agu 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Nur Tahaja Ibrahim" data-nim="521420026" data-file="Surat_Penunjukkan_Pembimbing_KP_521420026.pdf" data-nomor="178/UN47.B5.5/TD.06/2024" data-tanggal="8 Agu 2024" data-url="" data-cari="nur tahaja ibrahim 521420026 178/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">10</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">178/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Nur Tahaja Ibrahim</p><p class="text-[10px] text-slate-400">NIM 521420026</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">8 Agu 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="Bagus Wijaya" data-nim="521420028" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521420028.pdf" data-nomor="526/UN47.B5.5/TD.06/2024" data-tanggal="6 Agu 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521420028" data-cari="bagus wijaya 521420028 526/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">11</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">526/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Bagus Wijaya</p><p class="text-[10px] text-slate-400">NIM 521420028</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">6 Agu 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="Kurniawan Ali Male" data-nim="521420036" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521420036.pdf" data-nomor="487/UN47.B5.5/TD.06/2024" data-tanggal="17 Jul 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521420036" data-cari="kurniawan ali male 521420036 487/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">12</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">487/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Kurniawan Ali Male</p><p class="text-[10px] text-slate-400">NIM 521420036</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">17 Jul 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pemskr" data-tahun="2024" data-nama="Moh. Rafli A. Bolilio " data-nim="521420003" data-file="Surat_Penunjukkan_Pembimbing_Skripsi_521420003.pdf" data-nomor="482/UN47.B5.5/TD.06/2024" data-tanggal="16 Jul 2024" data-url="surat-penunjukkan-pembimbing-skripsi/521420003" data-cari="moh. rafli a. bolilio  521420003 482/un47.b5.5/td.06/2024 pembimbing skripsi 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">13</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-sky-50 text-sky-700">Pembimbing Skripsi</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">482/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Moh. Rafli A. Bolilio </p><p class="text-[10px] text-slate-400">NIM 521420003</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">16 Jul 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Mauludin" data-nim="521418031" data-file="Surat_Penunjukkan_Penguji_Tutup_521418031.pdf" data-nomor="417/UN47.B5.5/TD.06/2024" data-tanggal="26 Jun 2024" data-url="surat-penunjukkan-penguji/521418031" data-cari="mauludin 521418031 417/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">14</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">417/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Mauludin</p><p class="text-[10px] text-slate-400">NIM 521418031</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">26 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Adi Purwanto Umar" data-nim="521418034" data-file="Surat_Penunjukkan_Penguji_Tutup_521418034.pdf" data-nomor="413/UN47.B5.5/TD.06/2024" data-tanggal="24 Jun 2024" data-url="surat-penunjukkan-penguji/521418034" data-cari="adi purwanto umar 521418034 413/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">15</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">413/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Adi Purwanto Umar</p><p class="text-[10px] text-slate-400">NIM 521418034</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">24 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Isnain Sarjan Mento " data-nim="521417055" data-file="Surat_Penunjukkan_Penguji_Tutup_521417055.pdf" data-nomor="387/UN47.B5.5/TD.06/2024" data-tanggal="21 Jun 2024" data-url="surat-penunjukkan-penguji/521417055" data-cari="isnain sarjan mento  521417055 387/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">16</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">387/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Isnain Sarjan Mento </p><p class="text-[10px] text-slate-400">NIM 521417055</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">21 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Nasrun Ishak" data-nim="521417004" data-file="Surat_Penunjukkan_Penguji_Tutup_521417004.pdf" data-nomor="407/UN47.B5.5/TD.06/2024" data-tanggal="20 Jun 2024" data-url="surat-penunjukkan-penguji/521417004" data-cari="nasrun ishak 521417004 407/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">17</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">407/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Nasrun Ishak</p><p class="text-[10px] text-slate-400">NIM 521417004</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">20 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Hendra Palakua" data-nim="521417016" data-file="Surat_Penunjukkan_Penguji_Tutup_521417016.pdf" data-nomor="384/UN47.B5.5/TD.06/2024" data-tanggal="14 Jun 2024" data-url="surat-penunjukkan-penguji/521417016" data-cari="hendra palakua 521417016 384/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">18</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">384/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Hendra Palakua</p><p class="text-[10px] text-slate-400">NIM 521417016</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">14 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Anggi" data-nim="521417022" data-file="Surat_Penunjukkan_Penguji_Tutup_521417022.pdf" data-nomor="410/UN47.B5.5/TD.06/2024" data-tanggal="13 Jun 2024" data-url="surat-penunjukkan-penguji/521417022" data-cari="anggi 521417022 410/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">19</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">410/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Anggi</p><p class="text-[10px] text-slate-400">NIM 521417022</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">13 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="tutup" data-tahun="2024" data-nama="Nasrun Ishak" data-nim="521417004" data-file="Surat_Penunjukkan_Penguji_Tutup_521417004.pdf" data-nomor="407/UN47.B5.5/TD.06/2024" data-tanggal="13 Jun 2024" data-url="surat-penunjukkan-penguji/521417004" data-cari="nasrun ishak 521417004 407/un47.b5.5/td.06/2024 penguji tutup 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">20</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-orange-50 text-orange-700">Penguji Tutup</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">407/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Nasrun Ishak</p><p class="text-[10px] text-slate-400">NIM 521417004</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">13 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Aditya Saputra Amin" data-nim="521417052" data-file="Surat_Penunjukkan_Penguji_Hasil_521417052.pdf" data-nomor="376/UN47.B5.5/TD.06/2024" data-tanggal="12 Jun 2024" data-url="surat-penunjukkan-hasil/521417052" data-cari="aditya saputra amin 521417052 376/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">21</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">376/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Aditya Saputra Amin</p><p class="text-[10px] text-slate-400">NIM 521417052</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">12 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Nasrun Ishak" data-nim="521417004" data-file="Surat_Penunjukkan_Penguji_Hasil_521417004.pdf" data-nomor="375/UN47.B5.5/TD.06/2024" data-tanggal="12 Jun 2024" data-url="surat-penunjukkan-hasil/521417004" data-cari="nasrun ishak 521417004 375/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">22</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">375/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Nasrun Ishak</p><p class="text-[10px] text-slate-400">NIM 521417004</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">12 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Anggi" data-nim="521417022" data-file="Surat_Penunjukkan_Penguji_Hasil_521417022.pdf" data-nomor="374/UN47.B5.5/TD.06/2024" data-tanggal="12 Jun 2024" data-url="surat-penunjukkan-hasil/521417022" data-cari="anggi 521417022 374/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">23</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">374/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Anggi</p><p class="text-[10px] text-slate-400">NIM 521417022</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">12 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Abd Hayan Arune" data-nim="521417031" data-file="Surat_Penunjukkan_Penguji_Hasil_521417031.pdf" data-nomor="347/UN47.B5.5/TD.06/2024" data-tanggal="9 Jun 2024" data-url="surat-penunjukkan-hasil/521417031" data-cari="abd hayan arune 521417031 347/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">24</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">347/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Abd Hayan Arune</p><p class="text-[10px] text-slate-400">NIM 521417031</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">9 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Muh Syukriadi Ansar" data-nim="521417034" data-file="Surat_Penunjukkan_Penguji_Hasil_521417034.pdf" data-nomor="344/UN47.B5.5/TD.06/2024" data-tanggal="8 Jun 2024" data-url="surat-penunjukkan-hasil/521417034" data-cari="muh syukriadi ansar 521417034 344/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">25</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">344/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Muh Syukriadi Ansar</p><p class="text-[10px] text-slate-400">NIM 521417034</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">8 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Yulanda S.ishak" data-nim="521420033" data-file="Surat_Penunjukkan_Penguji_KP_521420033.pdf" data-nomor="337/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="" data-cari="yulanda s.ishak 521420033 337/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">26</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">337/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Yulanda S.ishak</p><p class="text-[10px] text-slate-400">NIM 521420033</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Hijratun A. Samadi" data-nim="521420004" data-file="Surat_Penunjukkan_Penguji_KP_521420004.pdf" data-nomor="336/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="" data-cari="hijratun a. samadi 521420004 336/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">27</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">336/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Hijratun A. Samadi</p><p class="text-[10px] text-slate-400">NIM 521420004</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Sahrul Duhi" data-nim="521420032" data-file="Surat_Penunjukkan_Penguji_KP_521420032.pdf" data-nomor="335/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="" data-cari="sahrul duhi 521420032 335/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">28</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">335/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Sahrul Duhi</p><p class="text-[10px] text-slate-400">NIM 521420032</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Mohammad Rifki Iti" data-nim="521420030" data-file="Surat_Penunjukkan_Penguji_KP_521420030.pdf" data-nomor="334/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="" data-cari="mohammad rifki iti 521420030 334/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">29</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">334/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Mohammad Rifki Iti</p><p class="text-[10px] text-slate-400">NIM 521420030</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Ariyani Djafar" data-nim="521420014" data-file="Surat_Penunjukkan_Penguji_KP_521420014.pdf" data-nomor="333/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="" data-cari="ariyani djafar 521420014 333/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">30</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">333/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Ariyani Djafar</p><p class="text-[10px] text-slate-400">NIM 521420014</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Zul Fikrah Arifuddin" data-nim="521420027" data-file="Surat_Penunjukkan_Penguji_KP_521420027.pdf" data-nomor="332/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="" data-cari="zul fikrah arifuddin 521420027 332/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">31</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">332/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Zul Fikrah Arifuddin</p><p class="text-[10px] text-slate-400">NIM 521420027</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pengkp" data-tahun="2024" data-nama="Muh. Arya Sumarno" data-nim="521420016" data-file="Surat_Penunjukkan_Penguji_KP_521420016.pdf" data-nomor="331/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="" data-cari="muh. arya sumarno 521420016 331/un47.b5.5/td.06/2024 penguji kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">32</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-violet-50 text-violet-700">Penguji KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">331/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Muh. Arya Sumarno</p><p class="text-[10px] text-slate-400">NIM 521420016</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Akdar" data-nim="521418012" data-file="Surat_Penunjukkan_Penguji_Hasil_521418012.pdf" data-nomor="328/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="surat-penunjukkan-hasil/521418012" data-cari="akdar 521418012 328/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">33</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">328/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Akdar</p><p class="text-[10px] text-slate-400">NIM 521418012</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="hasil" data-tahun="2024" data-nama="Adi Purwanto Umar" data-nim="521418034" data-file="Surat_Penunjukkan_Penguji_Hasil_521418034.pdf" data-nomor="327/UN47.B5.5/TD.06/2024" data-tanggal="7 Jun 2024" data-url="surat-penunjukkan-hasil/521418034" data-cari="adi purwanto umar 521418034 327/un47.b5.5/td.06/2024 penguji hasil 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">34</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-amber-50 text-amber-700">Penguji Hasil</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">327/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Adi Purwanto Umar</p><p class="text-[10px] text-slate-400">NIM 521418034</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Jun 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Suhartin Rauf" data-nim="521420023" data-file="Surat_Penunjukkan_Pembimbing_KP_521420023.pdf" data-nomor="177/UN47.B5.5/TD.06/2024" data-tanggal="18 Mar 2024" data-url="" data-cari="suhartin rauf 521420023 177/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">35</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">177/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Suhartin Rauf</p><p class="text-[10px] text-slate-400">NIM 521420023</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">18 Mar 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Stella Maria Rontopaly" data-nim="521420017" data-file="Surat_Penunjukkan_Pembimbing_KP_521420017.pdf" data-nomor="176/UN47.B5.5/TD.06/2024" data-tanggal="18 Mar 2024" data-url="" data-cari="stella maria rontopaly 521420017 176/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">36</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">176/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Stella Maria Rontopaly</p><p class="text-[10px] text-slate-400">NIM 521420017</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">18 Mar 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Sahrul Duhi" data-nim="521420032" data-file="Surat_Penunjukkan_Pembimbing_KP_521420032.pdf" data-nomor="165/UN47.B5.5/TD.06/2024" data-tanggal="7 Mar 2024" data-url="" data-cari="sahrul duhi 521420032 165/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">37</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">165/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Sahrul Duhi</p><p class="text-[10px] text-slate-400">NIM 521420032</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Mar 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Yulanda S.ishak" data-nim="521420033" data-file="Surat_Penunjukkan_Pembimbing_KP_521420033.pdf" data-nomor="166/UN47.B5.5/TD.06/2024" data-tanggal="7 Mar 2024" data-url="" data-cari="yulanda s.ishak 521420033 166/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">38</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">166/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Yulanda S.ishak</p><p class="text-[10px] text-slate-400">NIM 521420033</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Mar 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-white transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Sahrul Duhi" data-nim="521420032" data-file="Surat_Penunjukkan_Pembimbing_KP_521420032.pdf" data-nomor="165/UN47.B5.5/TD.06/2024" data-tanggal="7 Mar 2024" data-url="" data-cari="sahrul duhi 521420032 165/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">39</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">165/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Sahrul Duhi</p><p class="text-[10px] text-slate-400">NIM 521420032</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Mar 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/60 transition hover:bg-orange-50 reveal" data-tab-jenis="pemkp" data-tahun="2024" data-nama="Mohammad Rifki Iti" data-nim="521420030" data-file="Surat_Penunjukkan_Pembimbing_KP_521420030.pdf" data-nomor="164/UN47.B5.5/TD.06/2024" data-tanggal="7 Mar 2024" data-url="" data-cari="mohammad rifki iti 521420030 164/un47.b5.5/td.06/2024 pembimbing kp 2024">
                            <td class="py-4 pl-5 pr-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">40</span></td>
                            <td class="px-4 py-4"><span class="pt-badge bg-cyan-50 text-cyan-700">Pembimbing KP</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-500">164/UN47.B5.5/TD.06/2024</td>
                            <td class="px-4 py-4"><div class="min-w-0"><p class="font-semibold leading-snug text-slate-800">Mohammad Rifki Iti</p><p class="text-[10px] text-slate-400">NIM 521420030</p></div></td>
                            <td class="px-4 py-4 whitespace-nowrap">7 Mar 2024</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-unduh btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh / Buka PDF</span></button>
                                    <button type="button" class="btn-edit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
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


<!-- ===== Modal Edit Surat Penunjukkan ===== -->
<div class="modal-overlay" id="editModal" role="dialog" aria-modal="true">
    <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3.5">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-100 text-sm text-sky-600"><i class="fas fa-pen"></i></span>
                <div>
                    <h6 class="text-sm font-semibold text-slate-900">Edit Surat Penunjukkan</h6>
                    <p class="text-[11px] text-slate-400">Perbarui data surat pada baris terpilih.</p>
                </div>
            </div>
            <button type="button" class="text-xl leading-none text-slate-400 hover:text-slate-700" data-edit-close>&times;</button>
        </div>
        <div class="space-y-3.5 p-5">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="efJenis" class="mb-1 block text-xs font-semibold text-slate-600">Jenis Surat</label>
                    <select id="efJenis" disabled class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500 outline-none">
                        <option value="pemskr">Pembimbing Skripsi</option>
                        <option value="pemkp">Pembimbing KP</option>
                        <option value="hasil">Penguji Hasil</option>
                        <option value="pengkp">Penguji KP</option>
                        <option value="tutup">Penguji Tutup</option>
                    </select>
                </div>
                <div>
                    <label for="efNama" class="mb-1 block text-xs font-semibold text-slate-600">Nama Mahasiswa</label>
                    <input type="text" id="efNama" readonly class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="efNomor" class="mb-1 block text-xs font-semibold text-slate-600">Nomor Surat <span class="text-rose-500">*</span></label>
                    <input type="text" id="efNomor" placeholder="636/UN47.B5.5/TD.06/2024" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                </div>
                <div>
                    <label for="efTanggal" class="mb-1 block text-xs font-semibold text-slate-600">Tanggal Surat</label>
                    <input type="text" id="efTanggal" placeholder="1 Okt 2024" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                </div>
            </div>
            <div id="efDosenWrap" class="grid grid-cols-2 gap-3"></div>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3">
            <button type="button" class="rounded-lg bg-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-300" data-edit-close>Batal</button>
            <button type="button" id="btnSimpanEdit" class="rounded-lg bg-orange-500 px-3 py-2 text-xs font-semibold text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-600"><i class="fas fa-check mr-1"></i>Simpan Perubahan</button>
        </div>
    </div>
</div>
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
                <select id="stTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold outline-none focus:border-orange-400">
                    <option value="2024" selected>2024</option>
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                    <option value="2021">2021</option>
                    <option value="2020">2020</option>
                </select>
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
                <tbody id="tbStat">
<tr class="bg-white" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">1</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Lanto M. Kamil Amali,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">10</td>
    <td class="text-center tabular-nums text-slate-700">19</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">37</td>
</tr>
<tr class="bg-slate-50/60" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">2</td>
    <td class="pr-5 font-medium text-slate-800">Jumiati Ilham,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">8</td>
    <td class="text-center tabular-nums text-slate-700">14</td>
    <td class="gsep text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">37</td>
</tr>
<tr class="bg-white" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">3</td>
    <td class="pr-5 font-medium text-slate-800">Yasin Mohamad,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">7</td>
    <td class="text-center tabular-nums text-slate-700">11</td>
    <td class="gsep text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">32</td>
</tr>
<tr class="bg-slate-50/60" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">4</td>
    <td class="pr-5 font-medium text-slate-800">Taufiq Ismail Yusuf,ST.,M.Si</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">8</td>
    <td class="text-center tabular-nums text-slate-700">13</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">29</td>
</tr>
<tr class="bg-white" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">5</td>
    <td class="pr-5 font-medium text-slate-800">Ade Irawaty Tolago,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">7</td>
    <td class="text-center tabular-nums text-slate-700">11</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">23</td>
</tr>
<tr class="bg-slate-50/60" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">6</td>
    <td class="pr-5 font-medium text-slate-800">Amirudin Yunus Dako,ST.,M.Eng</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">8</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">23</td>
</tr>
<tr class="bg-white" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">7</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">7</td>
    <td class="text-center tabular-nums text-slate-700">13</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">23</td>
</tr>
<tr class="bg-slate-50/60" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">8</td>
    <td class="pr-5 font-medium text-slate-800">Ervan Hasan Harun,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">22</td>
</tr>
<tr class="bg-white" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">9</td>
    <td class="pr-5 font-medium text-slate-800">Ifan Wiranto,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">19</td>
</tr>
<tr class="bg-slate-50/60" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">10</td>
    <td class="pr-5 font-medium text-slate-800">Salmawaty Tansa,ST.,M.Eng</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="gsep text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">19</td>
</tr>
<tr class="bg-white" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">11</td>
    <td class="pr-5 font-medium text-slate-800">Dr.Ir. Arifin Matoka,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="text-center tabular-nums text-slate-700">8</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">17</td>
</tr>
<tr class="bg-slate-50/60" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">12</td>
    <td class="pr-5 font-medium text-slate-800">Zainudin Bonok,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">16</td>
</tr>
<tr class="bg-white" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">13</td>
    <td class="pr-5 font-medium text-slate-800">Rahmat D.R. Dako, ST., M.Eng</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">15</td>
</tr>
<tr class="bg-slate-50/60" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">14</td>
    <td class="pr-5 font-medium text-slate-800">Ir. Wahab Musa,MT.,Ph.D</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">13</td>
</tr>
<tr class="bg-white" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">15</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Bambang Panji Asmara,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">10</td>
</tr>
<tr class="bg-slate-50/60" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">16</td>
    <td class="pr-5 font-medium text-slate-800">Iskandar Z. Nasibu, S.Pd, M.Eng</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">8</td>
</tr>
<tr class="bg-white" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">17</td>
    <td class="pr-5 font-medium text-slate-800">Syahrir Abdussamad,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">7</td>
</tr>
<tr class="bg-slate-50/60" data-tahun="2024">
    <td class="pl-5 pr-3 text-slate-400">18</td>
    <td class="pr-5 font-medium text-slate-800">Idham Halid Lahay, ST., M.Sc</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">2</td>
</tr>
<tr class="bg-slate-100/80 st-total" data-tahun="2024">
    <td class="pl-5 pr-3"></td>
    <td class="pr-5 font-bold uppercase tracking-wide text-slate-600">Total</td>
    <td class="text-center tabular-nums text-slate-700">25</td>
    <td class="text-center tabular-nums text-slate-700">25</td>
    <td class="text-center tabular-nums text-slate-700">95</td>
    <td class="text-center tabular-nums text-slate-700">135</td>
    <td class="gsep text-center tabular-nums text-slate-700">15</td>
    <td class="text-center tabular-nums text-slate-700">0</td>
    <td class="text-center tabular-nums text-slate-700">57</td>
    <td class="px-5 text-right font-extrabold tabular-nums text-slate-900">352</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">1</td>
    <td class="pr-5 font-medium text-slate-800">Ir. Wahab Musa,MT.,Ph.D</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">12</td>
    <td class="text-center tabular-nums text-slate-700">12</td>
    <td class="gsep text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">36</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">2</td>
    <td class="pr-5 font-medium text-slate-800">Jumiati Ilham,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">7</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="gsep text-center tabular-nums text-slate-700">6</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">10</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">36</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">3</td>
    <td class="pr-5 font-medium text-slate-800">Taufiq Ismail Yusuf,ST.,M.Si</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">34</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">4</td>
    <td class="pr-5 font-medium text-slate-800">Iskandar Z. Nasibu, S.Pd, M.Eng</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="text-center tabular-nums text-slate-700">11</td>
    <td class="text-center tabular-nums text-slate-700">11</td>
    <td class="gsep text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">33</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">5</td>
    <td class="pr-5 font-medium text-slate-800">Ade Irawaty Tolago,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-700">10</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">32</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">6</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Lanto M. Kamil Amali,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">7</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">11</td>
    <td class="text-center tabular-nums text-slate-700">8</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">30</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">7</td>
    <td class="pr-5 font-medium text-slate-800">Ervan Hasan Harun,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">7</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="gsep text-center tabular-nums text-slate-700">6</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">8</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">30</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">8</td>
    <td class="pr-5 font-medium text-slate-800">Ifan Wiranto,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="gsep text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">29</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">9</td>
    <td class="pr-5 font-medium text-slate-800">Syahrir Abdussamad,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">26</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">10</td>
    <td class="pr-5 font-medium text-slate-800">Amirudin Yunus Dako,ST.,M.Eng</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="gsep text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">13</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">25</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">11</td>
    <td class="pr-5 font-medium text-slate-800">Rahmat D.R. Dako, ST., M.Eng</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="gsep text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">8</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">25</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">12</td>
    <td class="pr-5 font-medium text-slate-800">Yasin Mohamad,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">7</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">24</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">13</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">20</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">14</td>
    <td class="pr-5 font-medium text-slate-800">Zainudin Bonok,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="gsep text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">20</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">15</td>
    <td class="pr-5 font-medium text-slate-800">Wrastawa Ridwan,ST.,MT, MCE, CDSEA</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="text-center tabular-nums text-slate-700">7</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">16</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">16</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Bambang Panji Asmara,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">7</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">14</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">17</td>
    <td class="pr-5 font-medium text-slate-800">Salmawaty Tansa,ST.,M.Eng</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">12</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">18</td>
    <td class="pr-5 font-medium text-slate-800">Dr.Ir. Arifin Matoka,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">9</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">19</td>
    <td class="pr-5 font-medium text-slate-800">Ikhsan Hidayat, S.Kom, M.T</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">9</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3 text-slate-400">20</td>
    <td class="pr-5 font-medium text-slate-800">Idham Halid Lahay, ST., M.Sc</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">1</td>
</tr>
<tr class="bg-slate-100/80 st-total" style="display:none" data-tahun="2023">
    <td class="pl-5 pr-3"></td>
    <td class="pr-5 font-bold uppercase tracking-wide text-slate-600">Total</td>
    <td class="text-center tabular-nums text-slate-700">36</td>
    <td class="text-center tabular-nums text-slate-700">36</td>
    <td class="text-center tabular-nums text-slate-700">135</td>
    <td class="text-center tabular-nums text-slate-700">120</td>
    <td class="gsep text-center tabular-nums text-slate-700">32</td>
    <td class="text-center tabular-nums text-slate-700">0</td>
    <td class="text-center tabular-nums text-slate-700">102</td>
    <td class="px-5 text-right font-extrabold tabular-nums text-slate-900">461</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">1</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Lanto M. Kamil Amali,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">15</td>
    <td class="text-center tabular-nums text-slate-700">13</td>
    <td class="gsep text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">43</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">2</td>
    <td class="pr-5 font-medium text-slate-800">Ade Irawaty Tolago,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-700">12</td>
    <td class="text-center tabular-nums text-slate-700">10</td>
    <td class="gsep text-center tabular-nums text-slate-700">4</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">8</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">37</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">3</td>
    <td class="pr-5 font-medium text-slate-800">Taufiq Ismail Yusuf,ST.,M.Si</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">11</td>
    <td class="text-center tabular-nums text-slate-700">10</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">8</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">32</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">4</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">13</td>
    <td class="text-center tabular-nums text-slate-700">12</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">29</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">5</td>
    <td class="pr-5 font-medium text-slate-800">Jumiati Ilham,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="text-center tabular-nums text-slate-700">7</td>
    <td class="gsep text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">29</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">6</td>
    <td class="pr-5 font-medium text-slate-800">Yasin Mohamad,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">11</td>
    <td class="text-center tabular-nums text-slate-700">10</td>
    <td class="gsep text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">29</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">7</td>
    <td class="pr-5 font-medium text-slate-800">Iskandar Z. Nasibu, S.Pd, M.Eng</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">7</td>
    <td class="text-center tabular-nums text-slate-700">7</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">21</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">8</td>
    <td class="pr-5 font-medium text-slate-800">Ir. Wahab Musa,MT.,Ph.D</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">20</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">9</td>
    <td class="pr-5 font-medium text-slate-800">Dr.Ir. Arifin Matoka,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">17</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">10</td>
    <td class="pr-5 font-medium text-slate-800">Ifan Wiranto,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="gsep text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">16</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">11</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Bambang Panji Asmara,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="gsep text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">15</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">12</td>
    <td class="pr-5 font-medium text-slate-800">Ervan Hasan Harun,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">15</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">13</td>
    <td class="pr-5 font-medium text-slate-800">Syahrir Abdussamad,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">15</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">14</td>
    <td class="pr-5 font-medium text-slate-800">Rahmat D.R. Dako, ST., M.Eng</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="gsep text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">14</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">15</td>
    <td class="pr-5 font-medium text-slate-800">Salmawaty Tansa,ST.,M.Eng</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">9</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">16</td>
    <td class="pr-5 font-medium text-slate-800">Zainudin Bonok,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">9</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">17</td>
    <td class="pr-5 font-medium text-slate-800">Wrastawa Ridwan,ST.,MT, MCE, CDSEA</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">8</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">18</td>
    <td class="pr-5 font-medium text-slate-800">Ikhsan Hidayat, S.Kom, M.T</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">7</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3 text-slate-400">19</td>
    <td class="pr-5 font-medium text-slate-800">Amirudin Yunus Dako,ST.,M.Eng</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">3</td>
</tr>
<tr class="bg-slate-100/80 st-total" style="display:none" data-tahun="2022">
    <td class="pl-5 pr-3"></td>
    <td class="pr-5 font-bold uppercase tracking-wide text-slate-600">Total</td>
    <td class="text-center tabular-nums text-slate-700">24</td>
    <td class="text-center tabular-nums text-slate-700">24</td>
    <td class="text-center tabular-nums text-slate-700">120</td>
    <td class="text-center tabular-nums text-slate-700">110</td>
    <td class="gsep text-center tabular-nums text-slate-700">24</td>
    <td class="text-center tabular-nums text-slate-700">0</td>
    <td class="text-center tabular-nums text-slate-700">66</td>
    <td class="px-5 text-right font-extrabold tabular-nums text-slate-900">368</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">1</td>
    <td class="pr-5 font-medium text-slate-800">Taufiq Ismail Yusuf,ST.,M.Si</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">12</td>
    <td class="text-center tabular-nums text-slate-700">12</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">31</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">2</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Lanto M. Kamil Amali,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">12</td>
    <td class="text-center tabular-nums text-slate-700">13</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">30</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">3</td>
    <td class="pr-5 font-medium text-slate-800">Yasin Mohamad,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="text-center tabular-nums text-slate-700">12</td>
    <td class="text-center tabular-nums text-slate-700">13</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">29</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">4</td>
    <td class="pr-5 font-medium text-slate-800">Ade Irawaty Tolago,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">10</td>
    <td class="text-center tabular-nums text-slate-700">11</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">25</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">5</td>
    <td class="pr-5 font-medium text-slate-800">Jumiati Ilham,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="text-center tabular-nums text-slate-700">10</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">25</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">6</td>
    <td class="pr-5 font-medium text-slate-800">Dr.Ir. Arifin Matoka,MT</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">10</td>
    <td class="text-center tabular-nums text-slate-700">11</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">23</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">7</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="text-center tabular-nums text-slate-700">11</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">22</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">8</td>
    <td class="pr-5 font-medium text-slate-800">Syahrir Abdussamad,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="text-center tabular-nums text-slate-700">6</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">12</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">9</td>
    <td class="pr-5 font-medium text-slate-800">Ir. Wahab Musa,MT.,Ph.D</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="text-center tabular-nums text-slate-700">5</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">11</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">10</td>
    <td class="pr-5 font-medium text-slate-800">Ervan Hasan Harun,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">10</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">11</td>
    <td class="pr-5 font-medium text-slate-800">Iskandar Z. Nasibu, S.Pd, M.Eng</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">10</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">12</td>
    <td class="pr-5 font-medium text-slate-800">Rahmat D.R. Dako, ST., M.Eng</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">6</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">13</td>
    <td class="pr-5 font-medium text-slate-800">Sri Wahyuni Dali,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">5</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">14</td>
    <td class="pr-5 font-medium text-slate-800">Zainudin Bonok,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">4</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">15</td>
    <td class="pr-5 font-medium text-slate-800">Ifan Wiranto,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">3</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">16</td>
    <td class="pr-5 font-medium text-slate-800">Salmawaty Tansa,ST.,M.Eng</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">3</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">17</td>
    <td class="pr-5 font-medium text-slate-800">Wrastawa Ridwan,ST.,MT, MCE, CDSEA</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">3</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">18</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Bambang Panji Asmara,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">2</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">19</td>
    <td class="pr-5 font-medium text-slate-800">Jamal Darussalam Giu,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">2</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3 text-slate-400">20</td>
    <td class="pr-5 font-medium text-slate-800">Ikhsan Hidayat, S.Kom, M.T</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">1</td>
</tr>
<tr class="bg-slate-100/80 st-total" style="display:none" data-tahun="2021">
    <td class="pl-5 pr-3"></td>
    <td class="pr-5 font-bold uppercase tracking-wide text-slate-600">Total</td>
    <td class="text-center tabular-nums text-slate-700">15</td>
    <td class="text-center tabular-nums text-slate-700">15</td>
    <td class="text-center tabular-nums text-slate-700">105</td>
    <td class="text-center tabular-nums text-slate-700">110</td>
    <td class="gsep text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-700">0</td>
    <td class="text-center tabular-nums text-slate-700">9</td>
    <td class="px-5 text-right font-extrabold tabular-nums text-slate-900">257</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">1</td>
    <td class="pr-5 font-medium text-slate-800">Iskandar Z. Nasibu, S.Pd, M.Eng</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">4</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">6</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">2</td>
    <td class="pr-5 font-medium text-slate-800">Yasin Mohamad,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">6</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">3</td>
    <td class="pr-5 font-medium text-slate-800">Ade Irawaty Tolago,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">5</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">4</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">5</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">5</td>
    <td class="pr-5 font-medium text-slate-800">Jumiati Ilham,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">5</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">6</td>
    <td class="pr-5 font-medium text-slate-800">Taufiq Ismail Yusuf,ST.,M.Si</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">5</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">7</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Lanto M. Kamil Amali,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">4</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">8</td>
    <td class="pr-5 font-medium text-slate-800">Ervan Hasan Harun,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">4</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">9</td>
    <td class="pr-5 font-medium text-slate-800">Jamal Darussalam Giu,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">2</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">4</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">10</td>
    <td class="pr-5 font-medium text-slate-800">Wrastawa Ridwan,ST.,MT, MCE, CDSEA</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">4</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">11</td>
    <td class="pr-5 font-medium text-slate-800">Ir. Wahab Musa,MT.,Ph.D</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">3</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">12</td>
    <td class="pr-5 font-medium text-slate-800">Zainudin Bonok,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">3</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">13</td>
    <td class="pr-5 font-medium text-slate-800">Dr.Ir. Arifin Matoka,MT</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">2</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">14</td>
    <td class="pr-5 font-medium text-slate-800">Rahmat D.R. Dako, ST., M.Eng</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">2</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">15</td>
    <td class="pr-5 font-medium text-slate-800">Sri Wahyuni Dali,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">2</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">16</td>
    <td class="pr-5 font-medium text-slate-800">Syahrir Abdussamad,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">2</td>
</tr>
<tr class="bg-white" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">17</td>
    <td class="pr-5 font-medium text-slate-800">Dr. Bambang Panji Asmara,ST.,MT</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">1</td>
</tr>
<tr class="bg-slate-50/60" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3 text-slate-400">18</td>
    <td class="pr-5 font-medium text-slate-800">Salmawaty Tansa,ST.,M.Eng</td>
    <td class="text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="gsep text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="text-center tabular-nums text-slate-300">0</td>
    <td class="px-5 text-right font-bold tabular-nums text-slate-900">1</td>
</tr>
<tr class="bg-slate-100/80 st-total" style="display:none" data-tahun="2020">
    <td class="pl-5 pr-3"></td>
    <td class="pr-5 font-bold uppercase tracking-wide text-slate-600">Total</td>
    <td class="text-center tabular-nums text-slate-700">15</td>
    <td class="text-center tabular-nums text-slate-700">15</td>
    <td class="text-center tabular-nums text-slate-700">20</td>
    <td class="text-center tabular-nums text-slate-700">10</td>
    <td class="gsep text-center tabular-nums text-slate-700">1</td>
    <td class="text-center tabular-nums text-slate-700">0</td>
    <td class="text-center tabular-nums text-slate-700">3</td>
    <td class="px-5 text-right font-extrabold tabular-nums text-slate-900">64</td>
</tr>

</tbody>
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

    /* ===== Aksi baris: unduh/buka PDF & edit (dummy) ===== */
    function openSurat(tr) {
        var url = tr.getAttribute('data-url');
        if (!url) { showToast('Template surat kategori ini belum tersedia', true); return; }
        window.open('/redesain-siatek/dokumen/' + url, '_blank');
    }

    var DOSEN_LABELS = {
        pemskr: ['Pembimbing 1', 'Pembimbing 2'],
        pemkp:  ['Pembimbing 1', 'Pembimbing 2'],
        hasil:  ['Penguji 1', 'Penguji 2', 'Penguji 3', 'Penguji 4', 'Penguji 5'],
        pengkp: ['Penguji 1', 'Penguji 2', 'Penguji 3', 'Penguji 4', 'Penguji 5'],
        tutup:  ['Penguji 1', 'Penguji 2', 'Penguji 3', 'Penguji 4', 'Penguji 5']
    };
    var editModal = document.getElementById('editModal');
    var editRow = null;
    function esc(t) { return (t || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }

    /* Daftar nama dosen dibaca dari baris tabel statistik beban dosen (data tetap di HTML) */
    function dosenNames() {
        var seen = {}, out = [];
        Array.prototype.forEach.call(document.querySelectorAll('#tbStat tr:not(.st-total)'), function (tr) {
            var n = (tr.cells[1] ? tr.cells[1].textContent : '').trim();
            if (n && !seen[n]) { seen[n] = 1; out.push(n); }
        });
        return out.sort(function (a, b) { return a.localeCompare(b); });
    }
    function renderDosenFields(jenis, vals) {
        var wrap = document.getElementById('efDosenWrap');
        var labels = DOSEN_LABELS[jenis] || DOSEN_LABELS.pemskr;
        var names = dosenNames();
        var h = '';
        labels.forEach(function (lbl, i) {
            h += '<div><label class="mb-1 block text-xs font-semibold text-slate-600">' + lbl + '</label>'
                + '<select data-dosen-idx="' + i + '" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">'
                + '<option value="">— Pilih dosen —</option>';
            names.forEach(function (n) {
                h += '<option value="' + esc(n) + '"' + (vals[i] === n ? ' selected' : '') + '>' + esc(n) + '</option>';
            });
            h += '</select></div>';
        });
        wrap.innerHTML = h;
    }

    document.addEventListener('click', function (e) {
        var btnU = e.target.closest('.btn-unduh');
        if (btnU) { var tr = btnU.closest('tr'); if (tr) openSurat(tr); return; }
        var btnE = e.target.closest('.btn-edit');
        if (!btnE) return;
        editRow = btnE.closest('tr');
        if (!editRow) return;
        var jenis = editRow.getAttribute('data-tab-jenis') || 'pemskr';
        document.getElementById('efJenis').value = jenis;
        document.getElementById('efNama').value = editRow.getAttribute('data-nama') || '';
        document.getElementById('efNomor').value = editRow.getAttribute('data-nomor') || '';
        document.getElementById('efTanggal').value = editRow.getAttribute('data-tanggal') || '';
        renderDosenFields(jenis, (editRow.getAttribute('data-dosen') || '').split('|'));
        editModal.classList.add('show');
    });
    editModal.querySelectorAll('[data-edit-close]').forEach(function (b) {
        b.addEventListener('click', function () { editModal.classList.remove('show'); });
    });
    editModal.addEventListener('click', function (e) { if (e.target === editModal) editModal.classList.remove('show'); });

    document.getElementById('btnSimpanEdit').addEventListener('click', function () {
        if (!editRow) return;
        var nomor = document.getElementById('efNomor').value.trim();
        var tanggal = document.getElementById('efTanggal').value.trim();
        if (!nomor) { showToast('Nomor surat wajib diisi', true); return; }
        var dosenVals = Array.prototype.slice.call(document.querySelectorAll('#efDosenWrap select'))
            .map(function (s) { return s.value.trim(); }).filter(Boolean);
        editRow.setAttribute('data-nomor', nomor);
        editRow.setAttribute('data-tanggal', tanggal);
        editRow.setAttribute('data-dosen', dosenVals.join('|'));
        var nama = editRow.getAttribute('data-nama') || '';
        editRow.setAttribute('data-cari', (nama + ' ' + nomor + ' ' + tanggal + ' ' + dosenVals.join(' ')).toLowerCase());
        var tds = editRow.querySelectorAll('td');
        tds[2].textContent = nomor;
        tds[4].textContent = tanggal;
        editModal.classList.remove('show');
        render();
        showToast('Surat penunjukkan diperbarui (dummy)');
    });

    /* ===== Statistik Beban Dosen ===== */
    var stModal = document.getElementById('statModal');
    var stTahun = document.getElementById('stTahun');
    var tbStat = document.getElementById('tbStat');

    function renderStat() {
        var y = stTahun.value;
        var vis = 0, totT = 0;
        Array.prototype.forEach.call(tbStat.querySelectorAll('tr'), function (tr) {
            var show = tr.getAttribute('data-tahun') === y;
            tr.style.display = show ? '' : 'none';
            if (!show) return;
            if (tr.classList.contains('st-total')) totT = parseInt(tr.querySelector('td:last-child').textContent, 10) || 0;
            else vis++;
        });
        document.getElementById('stRingkas').textContent = vis + ' dosen \u00b7 ' + totT + ' penunjukan \u00b7 tahun ' + y;
    }

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